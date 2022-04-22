<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/2/23
 * Time: 10:58
 */

namespace App\Services\Satis;

use Illuminate\Support\Facades\Facade as IlluminateFacade;

/**
 * @method static Client mergeBuiltPackages()
 * @method static Client reset()
 * @method static string getBuildDir()
 *
 * Class Satis
 * @package App\Services\Satis
 * @mixin Client
 */
class Satis extends IlluminateFacade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'satis';
    }
}
