<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: /auth/login/index.html');
    exit;
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>User Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #1e293b;
            color: #f1f5f9;
            padding: 20px;
        }
        .profile {
            max-width: 600px;
            margin: 0 auto;
            background: #334155;
            padding: 20px;
            border-radius: 8px;
        }
        .profile img {
            border-radius: 50%;
            max-width: 120px;
            margin-bottom: 20px;
        }
        .profile h1 {
            margin-bottom: 10px;
        }
        .profile table {
            width: 100%;
            border-collapse: collapse;
        }
        .profile table th, .profile table td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #475569;
        }
        .profile table th {
            width: 40%;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="profile">
        <h1>Welcome, <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h1>
        <?php if (!empty($user['profile_photo'])): ?>
            <img src="<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="Profile Photo" />
        <?php endif; ?>
        <table>
            <tr><th>ID</th><td><?php echo htmlspecialchars($user['id']); ?></td></tr>
            <tr><th>Login (Email)</th><td><?php echo htmlspecialchars($user['login']); ?></td></tr>
            <tr><th>Referral Code</th><td><?php echo htmlspecialchars($user['referal_code'] ?? ''); ?></td></tr>
            <tr><th>PUBG UID</th><td><?php echo htmlspecialchars($user['pubg_uid'] ?? ''); ?></td></tr>
            <tr><th>PUBG Nickname</th><td><?php echo htmlspecialchars($user['pubg_nickname'] ?? ''); ?></td></tr>
            <tr><th>Telegram ID</th><td><?php echo htmlspecialchars($user['telegramm_id'] ?? ''); ?></td></tr>
            <tr><th>Telegram Username</th><td><?php echo htmlspecialchars($user['telegramm_username'] ?? ''); ?></td></tr>
            <tr><th>Registration Time</th><td><?php echo htmlspecialchars($user['registration_time']); ?></td></tr>
        </table>
    </div>
</body>
</html>
