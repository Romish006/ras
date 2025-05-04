<?php
session_start();
require_once 'db.php';

function checkTelegramAuthorization($auth_data, $bot_token) {
    $check_hash = $auth_data['hash'];
    unset($auth_data['hash']);
    ksort($auth_data);

    $data_check_arr = [];
    foreach ($auth_data as $key => $value) {
        $data_check_arr[] = $key . '=' . $value;
    }
    $data_check_string = implode("\n", $data_check_arr);

    $secret_key = hash('sha256', $bot_token, true);
    $hash = hash_hmac('sha256', $data_check_string, $secret_key);

    return hash_equals($hash, $check_hash);
}

$bot_token = '7695098348:AAERpU8nwfdmsjpmyKSDAowcAwKVBCkErHA';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'No data received']);
    exit;
}

if (!checkTelegramAuthorization($data, $bot_token)) {
    http_response_code(403);
    echo json_encode(['error' => 'Data is NOT from Telegram']);
    exit;
}

// Check if user exists by telegramm_id
$telegram_id = $data['id'];
$username = $data['username'] ?? '';
$first_name = $data['first_name'] ?? '';
$last_name = $data['last_name'] ?? '';
$profile_photo = $data['photo_url'] ?? '';
$registration_time = date('Y-m-d H:i:s');

// Check if user exists
$stmt = $mysqli->prepare("SELECT id FROM `ras users` WHERE telegramm_id = ?");
$stmt->bind_param('i', $telegram_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    // Insert new user
    $insert_stmt = $mysqli->prepare("INSERT INTO `ras users` (telegramm_id, telegramm_username, first_name, last_name, profile_photo, registration_time) VALUES (?, ?, ?, ?, ?, ?)");
    $insert_stmt->bind_param('isssss', $telegram_id, $username, $first_name, $last_name, $profile_photo, $registration_time);
    $insert_stmt->execute();
    $insert_stmt->close();
}

$stmt->close();

// Set session user as telegram username or id
$_SESSION['user'] = $username ?: $telegram_id;

echo json_encode(['status' => 'ok', 'user' => $_SESSION['user']]);

// Redirect to main page after successful login
header('Location: /main/');
exit;
?>
