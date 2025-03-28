<?php

namespace App\Services;

use Postmark\PostmarkClient;

class PostmarkService
{
    protected $client;

    public function __construct()
    {
        $token = config('services.postmark.token');

        if (!$token) {
            throw new \RuntimeException('Postmark token not configured.');
        }

        $this->client = new PostmarkClient($token);
    }

    public function getTemplates()
    {
        return cache()->remember('postmark_templates', 60, function () {
            $response = $this->client->listTemplates();
            return $response->Templates ?? [];
        });
    }

    public function getTemplateById($templateId)
    {
        return $this->client->getTemplate((int) $templateId);
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

    public function sendVerificationEmail(string $to, string $verificationUrl)
    {
        return $this->client->sendEmailWithTemplate(
            config('services.postmark.from_email'),
            $to,
            39165532,                               // template_id
            ['accountCreationUrl' => $verificationUrl], // template variables
            true,                             // Track opens
            'Verification Email',             // Alias
            true,                             // Inline CSS
            null, null, null, null, null,
            'None',
            null,
            'admin'
        );
    }
}