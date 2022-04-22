<?php

namespace App\Http\Api\Trigger;

/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/7/6
 * Time: 10:24
 */
class CodeupWebhookController extends AAAController
{
    /**
     */
    protected function getToken(): ?string
    {
        return $this->getRequest()->headers->get('X-Codeup-Token');
    }

    /**
     * @return null|string
     */
    protected function getUrl(): ?string
    {
        return $this->getRequest()->json('repository.git_secondary_http_url');
    }
}
