<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/12/29
 * Time: 18:47
 */

namespace App\Console\Commands;

use App\Console\AAACommand;
use App\Models\User;
use Exception;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

class CreateTokenCommand extends AAACommand
{
    /**
     * @inheritdoc
     */
    protected $signature = 'token:create
                            {name : 名称 }
                            {--id= : ID }
                            {--plain_text_token= : token }
                            {--abilities=* : 范围 }
                            {--username= : 指定用户 }
    ';

    /**
     * @inheritdoc
     */
    protected $description = '创建用户访问令牌';


    /**
     * @param  Schedule  $schedule
     * @return void
     * @throws Exception
     */
    public function handle(Schedule $schedule)
    {
        $name = $this->argument('name');
        $id = $this->option('id') ?: null;
        $username = $this->getUsername() ?: null;
        $plainTextToken = $this->option('plain_text_token') ?: Str::random(40);
        $abilities = $this->option('abilities') ?: ['*'];

        $user = User::query()->where('name', $username)->first();
        if (!$user instanceof User) {
            throw new Exception(sprintf('User "%s" does not exist!', $username));
        }

        $this->setAccessTokenModelUnguard();
        $token = $user->tokens()->create([
            'id' => $id,
            'name' => $name,
            'token' => hash('sha256', $plainTextToken),
            'abilities' => $abilities,
        ]);
        if (!$token instanceof Model) {
            throw new Exception(sprintf('Failed to save user "%s" access token!', $user->name));
        }

        $accessToken = new NewAccessToken($token, $token->getKey().'|'.$plainTextToken);

        $this->info(sprintf('The AccessToken "%s" is successfully created.', $accessToken->plainTextToken));
    }

    protected function getToken(): string
    {
        return $this->option('token') ?: Str::random(40);
    }

    protected function getUsername(): string
    {
        $username = $this->option('username') ?: null;
        if (!empty($username)) {
            return $username;
        }

        $question = 'Select the user that needs to produce the token!';
        $choices = User::query()->get()->map(function (User $user) {
            return $user->name;
        });

        return $this->choice($question, $choices->toArray());
    }

    protected function setAccessTokenModelUnguard($state = true)
    {
        /** @var PersonalAccessToken $model */
        $model = Sanctum::$personalAccessTokenModel;
        $model::unguard($state);
    }
}
