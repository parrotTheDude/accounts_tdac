<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PostmarkService;
use App\Models\Subscription;
use App\Jobs\SendBulkEmailJob;

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
            $postmark->sendEmail(
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
            'list'      => 'required|string',
            'variables' => 'nullable|array',
            'password'  => 'required|string',
        ]);

        if (!\Hash::check($validated['password'], auth()->user()->password)) {
            return back()->withErrors(['password' => 'Incorrect password. Bulk email not sent.']);
        }

        // ✅ All good — dispatch the job to send in the background
        dispatch(new \App\Jobs\SendBulkEmailJob(
            $templateId,
            $validated['list'],
            $validated['variables'] ?? [],
            auth()->user()->id
        ));

        return view('emails.bulk-in-progress', [
            'list' => $validated['list'],
        ]);
    }

    public function liveLog()
    {
        $logPath = storage_path('logs/laravel.log');

        if (!file_exists($logPath)) {
            return response('Log file not found.', 404);
        }

        $lines = array_slice(file($logPath), -100); // Last 100 lines
        return response(implode('', $lines), 200, ['Content-Type' => 'text/plain']);
    }
}
