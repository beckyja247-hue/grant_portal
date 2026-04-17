<?php
/**
 * DATABASE SETUP - Run this once to create/update tables
 * Access: http://localhost/grant_portal/setup.php
 */

include __DIR__ . "/config/config.php";

$output = [];

// ===========================
// CREATE USERS TABLE
// ===========================
$createUsers = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    balance DECIMAL(10,2) DEFAULT 0.00,
    login_attempts INT DEFAULT 0,
    last_attempt DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if ($conn->query($createUsers)) {
    $output[] = "✅ users table is ready";
} else {
    $output[] = "⚠️ users: " . $conn->error;
}

$alterUsers = "
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS login_attempts INT DEFAULT 0,
    ADD COLUMN IF NOT EXISTS last_attempt DATETIME DEFAULT NULL;
";

if ($conn->query($alterUsers)) {
    $output[] = "✅ users columns are current";
} else {
    $output[] = "⚠️ users columns: " . $conn->error;
}

// ===========================
// CREATE ADMINS TABLE
// ===========================
$createAdmins = "
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if ($conn->query($createAdmins)) {
    $output[] = "✅ admins table is ready";
} else {
    $output[] = "⚠️ admins: " . $conn->error;
}

$alterAdmins = "
ALTER TABLE admins
    ADD COLUMN IF NOT EXISTS username VARCHAR(255) NOT NULL AFTER id;
";

if ($conn->query($alterAdmins)) {
    $output[] = "✅ admins columns are current";
} else {
    $output[] = "⚠️ admins columns: " . $conn->error;
}

// ===========================
// CREATE GRANTS TABLE
// ===========================
$createGrants = "
CREATE TABLE IF NOT EXISTS grants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if ($conn->query($createGrants)) {
    $output[] = "✅ grants table is ready";
} else {
    $output[] = "⚠️ grants: " . $conn->error;
}

// ===========================
// CREATE APPLICATIONS TABLE
// ===========================
$createApplications = "
CREATE TABLE IF NOT EXISTS applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    grant_id INT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (grant_id) REFERENCES grants(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if ($conn->query($createApplications)) {
    $output[] = "✅ applications table is ready";
} else {
    $output[] = "⚠️ applications: " . $conn->error;
}

// ===========================
// CREATE TRANSACTIONS TABLE
// ===========================
$createTransactions = "
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    type ENUM('deposit', 'withdrawal', 'grant') NOT NULL,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if ($conn->query($createTransactions)) {
    $output[] = "✅ transactions table is ready";
} else {
    $output[] = "⚠️ transactions: " . $conn->error;
}

// ===========================
// CREATE WALLET TABLE
// ===========================
$createWallet = "
CREATE TABLE IF NOT EXISTS wallet (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    balance DECIMAL(10,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if ($conn->query($createWallet)) {
    $output[] = "✅ wallet table is ready";
} else {
    $output[] = "⚠️ wallet: " . $conn->error;
}

// ===========================
// CREATE CHAT_MESSAGES TABLE
// ===========================
$createChat = "
CREATE TABLE IF NOT EXISTS chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    admin_id INT DEFAULT NULL,
    user_id INT DEFAULT NULL,
    message TEXT NOT NULL,
    sender_role ENUM('admin', 'user') DEFAULT 'user',
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX (admin_id),
    INDEX (user_id),
    INDEX (timestamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if ($conn->query($createChat)) {
    $output[] = "✅ chat_messages table is ready";
} else {
    $output[] = "⚠️ chat_messages: " . $conn->error;
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Grant Portal - Database Setup</title>
<style>
body { font-family: Arial; background: #f4f6fb; padding: 20px; }
.box { background: white; padding: 20px; border-radius: 10px; max-width: 600px; margin: auto; }
h1 { color: #0f172a; }
li { padding: 5px 0; font-size: 14px; }
.success { color: #16a34a; }
.error { color: #dc2626; }
</style>
</head>
<body>

<div class="box">
<h1>🗄️ Database Setup</h1>

<?php if (!empty($output)): ?>
    <ul>
    <?php foreach ($output as $msg): ?>
        <li class="<?= strpos($msg, '✅') === 0 ? 'success' : 'error' ?>">
            <?= htmlspecialchars($msg) ?>
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<p><strong>✅ Database is ready!</strong></p>
<p>You can delete this file for security.</p>
<a href="admin/dashboard.php" style="display:inline-block; margin-top:20px; padding:10px 20px; background:#22c55e; color:white; text-decoration:none; border-radius:5px;">Go to Admin Dashboard</a>

</div>

</body>
</html>
