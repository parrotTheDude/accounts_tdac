<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

file_put_contents(__DIR__ . '/hook-debug.log', "Webhook hit at " . date('c') . "\n", FILE_APPEND);

$secret = 'dawec0a46e6f89e71316504c968555cce7d3818c9cf';
$deployScript = __DIR__ . '/deploy.sh';

$payload = file_get_contents('php://input');
file_put_contents(__DIR__ . '/hook-debug.log', "Payload received\n", FILE_APPEND);

$headers = getallheaders();
$githubSig = $headers['X-Hub-Signature-256'] ?? '';

if (!$githubSig) {
    file_put_contents(__DIR__ . '/hook-debug.log', "❌ Missing signature\n", FILE_APPEND);
    http_response_code(403);
    exit('❌ Missing signature.');
}

$expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);
if (!hash_equals($expected, $githubSig)) {
    file_put_contents(__DIR__ . '/hook-debug.log', "❌ Invalid signature\n", FILE_APPEND);
    http_response_code(403);
    exit('❌ Invalid signature.');
}

$data = json_decode($payload, true);
file_put_contents(__DIR__ . '/hook-debug.log', "Decoded JSON: " . json_encode($data) . "\n", FILE_APPEND);

if (($data['ref'] ?? '') !== 'refs/heads/main') {
    file_put_contents(__DIR__ . '/hook-debug.log', "Skipped: Not main branch\n", FILE_APPEND);
    exit('✅ Skipped: Not main branch.');
}

file_put_contents(__DIR__ . '/hook-debug.log', "✅ Signature validated. Running deploy script...\n", FILE_APPEND);

$output = [];
$returnCode = 0;

exec("/bin/echo 'Deploy script ran!' >> /home/thatdisa/laravel-app/deploy-test.log 2>&1", $output, $returnCode);

file_put_contents(__DIR__ . '/hook-debug.log', "ECHO test done.\nReturn code: $returnCode\n", FILE_APPEND);

echo "✅ Deploy triggered.";