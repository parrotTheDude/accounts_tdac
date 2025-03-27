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

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        $token = VerificationToken::where('user_id', $user->id)
        ->where('token', strtoupper($request->token)) // use $request->token instead of code
        ->where('expires_at', '>', now())
        ->first();

        if (!$token) {
            return back()->withErrors(['code' => 'Invalid or expired code.']);
        }

        $user->markEmailAsVerified(); // Custom method or Laravel built-in if supported
        $token->delete();

        return redirect()->route('dashboard')->with('success', 'Email verified!');
    }
}
