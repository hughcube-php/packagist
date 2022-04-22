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

class FlushTokenCommand extends AAACommand
{
    /**
     * @inheritdoc
     */
    protected $signature = 'token:flush';

    /**
     * @inheritdoc
     */
    protected $description = '清空用户访问令牌';


    /**
     * @param  Schedule  $schedule
     * @return void
     * @throws Exception
     */
    public function handle(Schedule $schedule)
    {
        /** @var PersonalAccessToken $model */
        $model = Sanctum::$personalAccessTokenModel;

        $model::query()->truncate();;
        $this->info('All tokens have been deleted!');
    }
}
