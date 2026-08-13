<?php
declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: Inquiry.html');
    exit;
}

function returnToForm(string $status): void
{
    header('Location: Inquiry.html?status=' . rawurlencode($status));
    exit;
}

function cleanHeader(string $value): string
{
    return trim(str_replace(["\r", "\n"], '', $value));
}

$name = cleanHeader((string) ($_POST['name'] ?? ''));
$email = cleanHeader((string) ($_POST['email'] ?? ''));
$phone = cleanHeader((string) ($_POST['phone'] ?? ''));
$inquiryType = cleanHeader((string) ($_POST['inquiry_type'] ?? ''));
$subject = cleanHeader((string) ($_POST['subject'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));
$agreement = (string) ($_POST['agreement'] ?? '');
$allowedInquiryTypes = [
    'General Inquiry',
    'Donor Registration',
    'Donation Information',
    'Blood Availability',
    'Eligibility Question',
    'Hospital Information',
    'Emergency Request Information',
    'Other',
];

if (
    strlen($name) < 3 ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    ($phone !== '' && !preg_match('/^0[0-9]{9}$/', $phone)) ||
    !in_array($inquiryType, $allowedInquiryTypes, true) ||
    strlen($subject) < 5 ||
    strlen($subject) > 100 ||
    strlen($message) < 10 ||
    strlen($message) > 500 ||
    $agreement !== 'yes'
) {
    returnToForm('invalid');
}

$recipient = getenv('INQUIRY_RECIPIENT_EMAIL');

if (!$recipient || !filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
    error_log('INQUIRY_RECIPIENT_EMAIL is not configured.');
    returnToForm('configuration-error');
}

$safeSubject = 'BloodCare inquiry: ' . substr($subject, 0, 100);
$body = implode("\n", [
    'A new inquiry was submitted through BloodCare.',
    '',
    'Name: ' . $name,
    'Email: ' . $email,
    'Phone: ' . ($phone !== '' ? $phone : 'Not provided'),
    'Inquiry type: ' . $inquiryType,
    'Subject: ' . $subject,
    '',
    'Message:',
    $message,
]);

$sender = getenv('INQUIRY_SENDER_EMAIL');
if (!$sender || !filter_var($sender, FILTER_VALIDATE_EMAIL)) {
    $sender = 'no-reply@' . (getenv('INQUIRY_MAIL_DOMAIN') ?: 'example.com');
}
$headers = [
    'From: BloodCare Website <' . $sender . '>',
    'Reply-To: ' . $name . ' <' . $email . '>',
    'Content-Type: text/plain; charset=UTF-8',
    'X-Mailer: PHP/' . PHP_VERSION,
];

$sent = mail($recipient, $safeSubject, $body, implode("\r\n", $headers));
returnToForm($sent ? 'sent' : 'send-error');
