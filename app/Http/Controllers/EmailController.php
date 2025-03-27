<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PostmarkService;

class EmailController extends Controller
{
    public function index(PostmarkService $postmark)
    {
        $keywords = ['calendar', 'newsletter', 'teens', 'bonus', 'price'];

        $templates = collect($postmark->getTemplates())->filter(function ($template) use ($keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains(strtolower($template->getName()), $keyword)) {
                    return true;
                }
            }
            return false;
        });

        return view('emails.index', ['templates' => $templates]);
    }

    public function send($templateId, PostmarkService $postmark)
    {
        // Replace with logic to pull subscribers from DB
        $recipients = \App\Models\User::pluck('email');

        foreach ($recipients as $email) {
            $postmark->sendTemplate($templateId, $email, [
                'name' => 'Friend', // Add custom vars if needed
            ]);
        }

        return back()->with('success', 'Email sent!');
    }

    public function show($templateId, PostmarkService $postmark)
    {
        $template = $postmark->getTemplateById($templateId);

        preg_match_all('/{{\s*(.*?)\s*}}/', $template->getHtmlBody(), $matches);

        $variables = collect($matches[1])
            ->unique()
            ->filter(function ($var) {
                return !str_contains(strtolower($var), 'unsubscribe');
            })
            ->values(); // Reset array keys

        return view('emails.show', [
            'template' => $template,
            'variables' => $variables,
        ]);
    }

    public function sendTest(Request $request, $templateId, PostmarkService $postmark)
    {
        $validated = $request->validate([
            'to' => 'required|email',
            'variables' => 'nullable|array',
        ]);

        $to = $validated['to'];
        $variables = $validated['variables'] ?? [];

        try {
            // Get full template info
            $template = $postmark->getTemplateById($templateId);
            $templateName = $template->getName();

            // Send the test email using full parameter list
            $postmark->getClient()->sendEmailWithTemplate(
                config('services.postmark.from_email'),
                $to,
                (int) $templateId,
                $variables,
                true,                     // TrackOpens
                $templateName,           // TemplateAlias (optional)
                true,                     // InlineCss
                null, null, null, null, null,
                'None',                   // Tag
                null,
                config('services.postmark.message_stream', 'outbound')
            );


            return back()->with('success', 'Test email sent to ' . $to);
        } catch (\Exception $e) {
            \Log::error('Test email failed: ' . $e->getMessage(), ['email' => $to]);
            return back()->withErrors(['error' => 'Failed to send: ' . $e->getMessage()]);
        }
    }
}
