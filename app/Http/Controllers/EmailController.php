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
}
