<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/12/29
 * Time: 17:03
 */

namespace App\Http\Composer;

use App\Http\Controller;
use App\Models\PersonalAccessToken;
use App\Models\User;
use App\Services\Satis\Satis;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class CatchAllController extends Controller
{
    /**
     * @return Response
     */
    public function action(): Response
    {
        $token = PersonalAccessToken::findToken($this->getRequest()->getPassword());
        if (!$token instanceof PersonalAccessToken || !$token->can('composer')) {
            return $this->failedBasicResponse();
        }

        $user = $token->tokenable;
        if (!$user instanceof User || !$user->isAvailable() || $user->name !== $this->getRequest()->getUser()) {
            return $this->failedBasicResponse();
        }

        try {
            return $this->sendFile();
        } catch (FileException) {
            return response('Not Found')->setStatusCode(404);
        }
    }

    /**
     * @return BinaryFileResponse
     */
    protected function sendFile(): Response
    {
        $file = new File($this->getFile(), false);

        $response = new BinaryFileResponse($file);
        $response->headers->set('Content-Disposition', 'inline');
        if (!$this->isEnableHttpCachePath($this->getPath())) {
            $response->setLastModified();
        }

        return $response;
    }

    /**
     * @return string
     */
    protected function getFile(): string
    {
        return sprintf("%s%s", Satis::getBuildDir(), $this->getPath());
    }

    /**
     * @return string
     */
    protected function getPath(): string
    {
        $path = trim(Str::afterLast(trim($this->getRequest()->path(), '/'), 'composer'), '/');

        return '/'.ltrim(($path ?: 'index.html'), '/');
    }

    protected function isEnableHttpCachePath($path): bool
    {
        return !in_array($path, ['/packages.json', '/index.html']);
    }

    /**
     * @return Response
     */
    protected function failedBasicResponse(): Response
    {
        throw new UnauthorizedHttpException('Basic', 'Invalid credentials.');
    }
}
