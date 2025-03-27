<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Services\PostmarkService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBulkEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $templateId;
    public $list;
    public $variables;
    public $userId;

    /**
     * Create a new job instance.
     */
    public function __construct($templateId, $list, $variables, $userId)
    {
        $this->templateId = $templateId;
        $this->list = $list;
        $this->variables = $variables;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(PostmarkService $postmark)
    {
        $template = $postmark->getTemplateById($this->templateId);
        $templateName = $template->getName();

        $messageStream = ($this->list === 'newsletter') ? 'newsletter' : 'bonus-event';
        $fromEmail = ($this->list === 'newsletter') ? 'newsletter@tdacvic.com' : 'events@tdacvic.com';

        $emails = Subscription::where('list_name', $this->list)
            ->where('subscribed', true)
            ->pluck('email')
            ->unique()
            ->values();

        foreach ($emails as $email) {
            try {
                $postmark->sendEmail(
                    $this->templateId,
                    $email,
                    $this->variables,
                    $fromEmail,
                    $templateName,
                    $messageStream
                );

                \Log::info("✅ Sent to {$email} [{$this->list}]");
            } catch (\Exception $e) {
                \Log::error("❌ Failed to send to {$email}: " . $e->getMessage());
            }
        }

        \Log::info("🎉 Bulk send complete for list [{$this->list}]");
    }
}