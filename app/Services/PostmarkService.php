<?php

namespace App\Services;

use Postmark\PostmarkClient;

class PostmarkService
{
    protected $client;

    public function __construct()
    {
        $this->client = new PostmarkClient(config('services.postmark.token'));
    }

    public function getTemplates()
    {
        $response = $this->client->listTemplates();
        return $response->Templates ?? [];
    }

    public function sendTemplate($templateId, $to, $templateModel = [])
    {
        return $this->client->sendEmailWithTemplate(
            'your@from.email',
            $to,
            $templateId,
            $templateModel
        );
    }

    public function getTemplateById($templateId)
    {
        return $this->client->getTemplate($templateId);
    }
}