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

    public function getTemplateById($templateId)
    {
        return $this->client->getTemplate($templateId);
    }

    public function getClient()
    {
        return $this->client;
    }

    public function sendEmail($templateId, $to, $variables = [], $from = null, $alias = null, $stream = null)
    {
        $template = $this->getTemplateById($templateId);

        return $this->client->sendEmailWithTemplate(
            $from ?? config('services.postmark.from_email'),
            $to,
            (int) $templateId,
            $variables,
            true,                             // Track opens
            $alias ?? $template->getName(),   // Alias
            true,                             // Inline CSS
            null, null, null, null, null,
            'None',
            null,
            $stream ?? config('services.postmark.message_stream', 'outbound')
        );
    }
}