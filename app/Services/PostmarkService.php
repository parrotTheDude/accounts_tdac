<?php

namespace App\Services;

use Postmark\PostmarkClient;

class PostmarkService
{
    protected $client;

    public function __construct()
    {
        $this->client = new PostmarkClient(env('POSTMARK_API_TOKEN'));
    }

    public function getTemplates()
    {
        $response = $this->client->listTemplates();
        return $response['Templates'] ?? [];
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
}