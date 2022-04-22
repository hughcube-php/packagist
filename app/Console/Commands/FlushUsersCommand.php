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

class FlushUsersCommand extends AAACommand
{
    /**
     * @inheritdoc
     */
    protected $signature = 'user:flush';

    /**
     * @inheritdoc
     */
    protected $description = '清理用户';


    /**
     * @throws Exception
     */
    public function handle(Schedule $schedule)
    {
        User::query()->truncate();;
        $this->info('All users have been deleted!');
    }
}
