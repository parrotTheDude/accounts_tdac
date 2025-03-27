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

        $recipientList = $validated['list'];
        $variables     = $validated['variables'] ?? [];

        $messageStream = ($recipientList === 'newsletter') ? 'newsletter' : 'bonus-event';
        $fromEmail     = ($recipientList === 'newsletter') ? 'newsletter@tdacvic.com' : 'events@tdacvic.com';

        $emails = Subscription::where('list_name', $recipientList)
          ->where('subscribed', true)
          ->with('user')
          ->get()
          ->pluck('user.email')
          ->filter() // Remove nulls if any
          ->unique()
          ->values();

        if ($emails->isEmpty()) {
            \Log::warning('No recipients found for list', ['list_name' => $recipientList]);
            return back()->withErrors(['error' => 'No recipients found for this list.']);
        }

        $template = $postmark->getTemplateById($templateId);
        $templateName = $template->getName();

        $total = $emails->count();
        session(['bulk_sent' => 0, 'bulk_total' => $total]);

        $sent = 0;

        foreach ($emails as $email) {
            try {
                $postmark->sendEmail(
                    $templateId,
                    $email,
                    $variables,
                    $fromEmail,
                    $templateName,
                    $messageStream
                );

                $sent++;
                session(['bulk_sent' => $sent]);
            } catch (\Exception $e) {
                \Log::error("Bulk email failed for {$email}: " . $e->getMessage());
            }
        }

        // Reset progress session
        session(['bulk_sent' => $total]);

        return view('emails.bulk-in-progress', [
            'list' => $recipientList
        ]);
    }

    public function bulkProgress()
    {
        return response()->json([
            'sent' => session('bulk_sent', 0),
            'total' => session('bulk_total', 0),
        ]);
    }
}
