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

    public function verify($token)
    {
        $record = \App\Models\VerificationToken::where('token', strtoupper($token))
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            $status = 'invalid';
            return view('auth.verification-result', compact('status'));
        }

        $user = $record->user;

        if (!$user) {
            $status = 'not_found';
            return view('auth.verification-result', compact('status'));
        }

        if ($user->hasVerifiedEmail()) {
            $status = 'already_verified';
        } else {
            $user->markEmailAsVerified();
            $status = 'success';
        }

        $record->delete();

        return view('auth.verification-result', compact('status'));
    }

    public function showVerificationForm(string $token)
    {
        return view('auth.verify', ['token' => $token]);
    }
}
