<?php
/**
 * Ember & Oak — reservation endpoint
 * ===================================
 * Plain, dependency-free PHP. Runs on any cheap shared host with PHP 7.4+.
 *
 * - Accepts a JSON POST (or a normal form POST) with: name, phone, date,
 *   time, party, notes (optional).
 * - Validates: name (>= 2 chars), phone (10-15 digits), date (today through
 *   60 days ahead, no past dates), time (one of the listed slots),
 *   party size (1-12).
 * - Appends the reservation to data/reservations.json and returns JSON:
 *   {"ok": true, "id": "..."} on success, {"ok": false, "errors": [...]}
 *   with HTTP 422 on validation failure.
 *
 * NOTE: This file does NOT run on GitHub Pages — Pages is static hosting
 * with no PHP. The front-end (script.js) detects that and shows a clearly
 * labelled demo success state instead of faking a submission. To use the
 * real backend, upload the whole project folder to a PHP host.
 */

header('Content-Type: application/json; charset=utf-8');

// Read the payload: JSON body first, fall back to a regular form POST.
$raw = file_get_contents('php://input');
$data = [];
if ($raw !== '' && $raw !== false) {
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        $data = $decoded;
    }
}
if (empty($data) && !empty($_POST)) {
    $data = $_POST;
}

$errors = [];

// ---- name ----
$name = isset($data['name']) ? trim((string) $data['name']) : '';
if (strlen($name) < 2) {
    $errors[] = 'Please enter your name.';
}

// ---- phone ----
$phone = isset($data['phone']) ? trim((string) $data['phone']) : '';
$digits = preg_replace('/\D/', '', $phone);
if (strlen($digits) < 10 || strlen($digits) > 15) {
    $errors[] = 'Please enter a valid phone number.';
}

// ---- date: today .. +60 days, no past dates ----
$date = isset($data['date']) ? trim((string) $data['date']) : '';
$today = new DateTimeImmutable('today');
$maxDate = $today->modify('+60 days');
$dateObj = DateTimeImmutable::createFromFormat('Y-m-d', $date);
if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
    $errors[] = 'Please choose a valid date.';
} elseif ($dateObj < $today) {
    $errors[] = "Reservations can't be in the past.";
} elseif ($dateObj > $maxDate) {
    $errors[] = 'Please book within the next 60 days.';
}

// ---- time slot ----
$allowedTimes = ['5:00 PM', '5:30 PM', '6:00 PM', '6:30 PM', '7:00 PM', '7:30 PM', '8:00 PM', '8:30 PM', '9:00 PM'];
$time = isset($data['time']) ? trim((string) $data['time']) : '';
if (!in_array($time, $allowedTimes, true)) {
    $errors[] = 'Please pick a valid time slot.';
}

// ---- party size 1-12 ----
$party = isset($data['party']) ? (int) $data['party'] : 0;
if ($party < 1 || $party > 12) {
    $errors[] = 'Party size must be between 1 and 12.';
}

// ---- optional notes ----
$notes = isset($data['notes']) ? trim((string) $data['notes']) : '';
if (strlen($notes) > 500) {
    $notes = substr($notes, 0, 500);
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'errors' => $errors]);
    exit;
}

// ---- store the reservation ----
$reservation = [
    'id' => 'res_' . bin2hex(random_bytes(6)),
    'name' => $name,
    'phone' => $digits,
    'date' => $date,
    'time' => $time,
    'party' => $party,
    'notes' => $notes,
    'created_at' => (new DateTimeImmutable())->format(DateTimeInterface::ATOM),
];

$dataDir = __DIR__ . '/data';
$file = $dataDir . '/reservations.json';

if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

$existing = [];
if (file_exists($file)) {
    $contents = file_get_contents($file);
    $decodedFile = $contents !== false ? json_decode($contents, true) : null;
    if (is_array($decodedFile)) {
        $existing = $decodedFile;
    }
}
$existing[] = $reservation;

// Lock while writing so concurrent bookings don't clobber each other.
$fp = fopen($file, 'c+');
if ($fp === false) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'errors' => ['Could not save the reservation. Please try again.']]);
    exit;
}
if (flock($fp, LOCK_EX)) {
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    fflush($fp);
    flock($fp, LOCK_UN);
}
fclose($fp);

echo json_encode(['ok' => true, 'id' => $reservation['id']]);
