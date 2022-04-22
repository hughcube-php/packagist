<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2022/2/13
 * Time: 22:24
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

class AuthenticateWithBasicAuth
{
    /**
     * The guard factory instance.
     *
     * @var AuthFactory
     */
    protected AuthFactory $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  AuthFactory  $auth
     * @return void
     */
    public function __construct(AuthFactory $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param $request
     * @param  Closure  $next
     * @return mixed
     * @phpstan-ignore-next-line
     */
    public function handle($request, Closure $next): mixed
    {
        /** @phpstan-ignore-next-line  */
        $this->auth->guard()->basic('name');
        return $next($request);
    }
}
