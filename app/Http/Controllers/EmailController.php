<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PostmarkService;
use App\Models\Subscription;

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

        try {
            $postmark->sendTestEmail(
                $templateId,
                $validated['to'],
                $validated['variables'] ?? []
            );

            return back()->with('success', "Test email sent to {$validated['to']}");

        } catch (\Exception $e) {
            \Log::error('Test email failed', [
                'to' => $validated['to'],
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['error' => 'Failed to send: ' . $e->getMessage()]);
        }
    }

    public function sendForm($templateId, PostmarkService $postmark)
    {
        $template = $postmark->getTemplateById($templateId);

        // Get all unique subscription lists from DB
        $lists = \App\Models\Subscription::select('list_name')
            ->where('subscribed', true)
            ->distinct()
            ->pluck('list_name');

        // Extract variables like before
        preg_match_all('/{{\s*(.*?)\s*}}/', $template->getHtmlBody(), $matches);
        $variables = collect($matches[1])
            ->unique()
            ->filter(fn($var) => !str_contains(strtolower($var), 'unsubscribe'))
            ->values();

        return view('emails.send-form', [
            'template' => $template,
            'lists' => $lists,
            'variables' => $variables,
        ]);
    }

    public function sendBulk(Request $request, $templateId, PostmarkService $postmark)
    {
        $validated = $request->validate([
            'list'       => 'required|string',
            'variables'  => 'nullable|array',
            'password'   => 'required|string',
        ]);

        // ✅ Confirm password
        if (!\Hash::check($validated['password'], auth()->user()->password)) {
            return back()->withErrors(['password' => 'Incorrect password. Bulk email not sent.']);
        }

        $recipientList = $validated['list'];
        $variables     = $validated['variables'] ?? [];

        // ✅ Determine sender + stream based on list
        $messageStream = ($recipientList === 'newsletter') ? 'newsletter' : 'bonus-event';
        $fromEmail     = ($recipientList === 'newsletter') ? 'newsletter@tdacvic.com' : 'events@tdacvic.com';

        // ✅ Fetch recipients (based on your DB structure)
        $emails = Subscription::where('list_name', $recipientList)
            ->where('subscribed', true)
            ->with('user')
            ->get()
            ->pluck('user.email')
            ->unique()
            ->values();

        if ($emails->isEmpty()) {
            \Log::warning('No recipients found for list', ['list_name' => $recipientList]);
            return back()->withErrors(['error' => 'No recipients found for this list.']);
        }

        // ✅ Fetch template name from Postmark
        $template = $postmark->getTemplateById($templateId);
        $templateName = $template->getName();

        $totalSent = 0;
        $batchSize = 500;

        foreach ($emails->chunk($batchSize) as $batch) {
            foreach ($batch as $email) {
                try {
                    $postmark->sendTestEmail(
                        $templateId,
                        $email,
                        $variables,
                        $fromEmail,
                        $templateName,
                        $messageStream
                    );

                    $totalSent++;
                } catch (\Exception $e) {
                    \Log::error("Bulk email failed for {$email}: {$e->getMessage()}");
                }
            }
        }

        return redirect()->route('emails.index')->with('success', "Bulk email sent to {$totalSent} recipients.");
    }
}
