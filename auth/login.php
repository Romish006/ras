<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'db.php';

$contentType = $_SERVER["CONTENT_TYPE"] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (strpos($contentType, 'application/json') !== false) {
        // Handle Google sign-in JSON data
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!$data || !isset($data['id']) || !isset($data['login'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid data received']);
            exit;
        }

        $google_id = $data['id'];
        $login = $data['login'];
        $first_name = $data['first_name'] ?? '';
        $last_name = $data['last_name'] ?? '';
        $profile_photo = $data['profile_photo'] ?? '';
        $registration_time = date('Y-m-d H:i:s');

        try {
            // Check if user exists by google_id or login (email)
            $stmt = $mysqli->prepare("SELECT id FROM users WHERE login = ? OR referal_code = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $mysqli->error);
            }
            $stmt->bind_param('ss', $login, $google_id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 0) {
                // Insert new user
                $insert_stmt = $mysqli->prepare("INSERT INTO users (login, referal_code, profile_photo, first_name, last_name, registration_time) VALUES (?, ?, ?, ?, ?, ?)");
                if (!$insert_stmt) {
                    throw new Exception("Prepare failed: " . $mysqli->error);
                }
                $insert_stmt->bind_param('ssssss', $login, $google_id, $profile_photo, $first_name, $last_name, $registration_time);
                if (!$insert_stmt->execute()) {
                    throw new Exception("Execute failed: " . $insert_stmt->error);
                }
                $user_id = $insert_stmt->insert_id;
            } else {
                // User exists, get user id
                $stmt->bind_result($user_id);
                $stmt->fetch();
            }

            // Set session user data
            $_SESSION['user'] = [
                'id' => $user_id,
                'login' => $login,
                'referal_code' => $google_id,
                'profile_photo' => $profile_photo,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'registration_time' => $registration_time
            ];

            echo json_encode(['success' => true]);
            exit;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    } else {
        // Handle normal login form
        $login = $_POST['login'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!$login || !$password) {
            echo 'Login and password are required.';
            exit;
        }

        // Prepare statement to prevent SQL injection
        $stmt = $mysqli->prepare("SELECT id, password, referal_code, profile_photo, pubg_uid, pubg_nickname, telegramm_id, first_name, last_name, telegramm_username, registration_time FROM `users` WHERE login = ?");
        $stmt->bind_param('s', $login);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            echo 'Invalid login or password.';
            exit;
        }

        $stmt->bind_result($user_id, $hashed_password, $referal_code, $profile_photo, $pubg_uid, $pubg_nickname, $telegramm_id, $first_name, $last_name, $telegramm_username, $registration_time);
        $stmt->fetch();
        if ($password === $hashed_password) {
            $_SESSION['user'] = [
                'id' => $user_id,
                'login' => $login,
                'referal_code' => $referal_code,
                'profile_photo' => $profile_photo,
                'pubg_uid' => $pubg_uid,
                'pubg_nickname' => $pubg_nickname,
                'telegramm_id' => $telegramm_id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'telegramm_username' => $telegramm_username,
                'registration_time' => $registration_time
            ];
            header('Location: /main/index.php');
            exit;
        } else {
            echo 'Invalid login or password.';
            exit;
        }
    }
} else {
    echo 'Invalid request method.';
}
?>
</create_file>
