<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VerificationToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\PostmarkService;

class EmailVerificationController extends Controller
{
    public function send(Request $request, PostmarkService $postmark)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return back()->with('success', 'Email is already verified.');
        }

        // Clear old tokens
        VerificationToken::where('user_id', $user->id)->delete();

        $token = VerificationToken::create([
            'user_id' => $user->id,
            'token' => Str::random(6),
            'expires_at' => now()->addHour(),
        ]);

        $verificationUrl = route('verification.verify', $token->token);

        $postmark->sendEmail(
            templateId: 39165532,
            to: $user->email,
            variables: ['accountCreationUrl' => $verificationUrl],
            stream: 'admin'
        );

        return back()->with('success', 'Verification email sent!');
    }

    public function verify(string $token)
    {
        $tokenRecord = VerificationToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (! $tokenRecord) {
            return view('auth.email-invalid');
        }

        $user = $tokenRecord->user;

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified(); // Laravel's built-in method
        }

        $tokenRecord->delete();

        return view('auth.email-verified');
    }

    public function showVerificationForm(string $token)
    {
        return view('auth.verify', ['token' => $token]);
    }
}
