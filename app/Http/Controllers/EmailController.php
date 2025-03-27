<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PostmarkService;

class EmailController extends Controller
{
    public function index(PostmarkService $postmark)
    {
        $templates = $postmark->getTemplates();
        return view('emails.index', compact('templates'));
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
