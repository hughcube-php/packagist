<?php

namespace App\Http\Api\Trigger;

use App\Http\Controller;
use App\Jobs\UpdateOneJob;
use App\Models\PersonalAccessToken;
use App\Models\User;
use HughCube\PUrl\Url;
use Illuminate\Contracts\Bus\Dispatcher;
use Symfony\Component\HttpFoundation\Response;

/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/7/6
 * Time: 10:24
 */
abstract class AAAController extends Controller
{
    public function action(): Response
    {
        if (!$this->checkToken($this->getToken())) {
            return response('Forbidden')->setStatusCode(403);
        }

        if (!Url::isUrlString($url = $this->getUrl())) {
            return response('The url is not correct!')->setStatusCode(400);
        }

        if (!in_array($type = $this->getType(), ['git', 'svn'])) {
            return response('Type are not supported!')->setStatusCode(400);
        }

        $id = app(Dispatcher::class)->dispatch(UpdateOneJob::new(['url' => $url, 'type' => $type]));
        return response($id);
    }

    /**
     * @param  null|string  $plainTextToken
     * @return bool
     */
    protected function checkToken(null|string $plainTextToken): bool
    {
        $token = PersonalAccessToken::findToken($plainTextToken);
        if (!$token instanceof PersonalAccessToken || !$token->can('codeup')) {
            return false;
        }

        $user = $token->tokenable;
        return $user instanceof User && $user->isAvailable();
    }

    abstract protected function getToken(): ?string;

    abstract protected function getUrl(): ?string;

    protected function getType(): ?string
    {
        return 'git';
    }
}
