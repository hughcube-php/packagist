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

class CreateUserCommand extends AAACommand
{
    /**
     * @inheritdoc
     */
    protected $signature = 'user:create
                            {name : 用户名 }
                            {--password= : 用户密码 }
    ';

    /**
     * @inheritdoc
     */
    protected $description = '创建用户';


    /**
     * @throws Exception
     */
    public function handle(Schedule $schedule)
    {
        $name = $this->argument('name');
        $user = User::query()->where('name', $name)->first();

        $user = $user ?? new User();
        $user->name = $name;
        $user->password = $this->option('password') ?: null;

        if (!$user->save()) {
            throw new Exception(sprintf('Failed to create user "%s"!', $name));
        }

        $this->info(sprintf('Description User "%s" is created successfully.', $name));
    }
}
