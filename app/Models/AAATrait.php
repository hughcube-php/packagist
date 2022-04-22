<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2022/3/12
 * Time: 23:02
 */

namespace App\Models;

use HughCube\Laravel\Knight\Database\Eloquent\Traits\Model;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\CacheInterface;

trait AAATrait
{
    use Model;

    public function getCache(): ?CacheInterface
    {
        return Cache::store('octane');
    }
}
