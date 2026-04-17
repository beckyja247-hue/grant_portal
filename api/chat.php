<?php
header('Content-Type: application/json');
session_start();
include "../config/config.php";

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? 'fetch';
$user_id = $_SESSION['id'];
$role = $_SESSION['role'];

// ===========================
// SEND MESSAGE
// ===========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'send') {
    
    $message = trim($_POST['message'] ?? '');
    $target_id = intval($_POST['target_id'] ?? 0);
    
    if (empty($message) || $target_id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid message or target']);
        exit;
    }
    
    // Determine sender and receiver based on role
    if ($role === 'admin') {
        $admin_id = $user_id;
        $receiver_user_id = $target_id;
    } else {
        // User sending to admin
        $admin_id = $target_id;
        $receiver_user_id = $user_id;
    }
    
    $stmt = $conn->prepare("
        INSERT INTO chat_messages (sender_id, admin_id, user_id, message, sender_role, timestamp)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->bind_param("iiiss", $user_id, $admin_id, $receiver_user_id, $message, $role);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Message sent']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
    exit;
}

// ===========================
// FETCH MESSAGES
// ===========================
if ($action === 'fetch') {
    
    $target_id = intval($_GET['target_id'] ?? 0);
    
    if ($target_id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid target']);
        exit;
    }
    
    if ($role === 'admin') {
        // Admin fetching user messages
        $query = "
            SELECT * FROM chat_messages 
            WHERE admin_id = ? AND user_id = ?
            ORDER BY timestamp ASC
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $target_id);
    } else {
        // User fetching admin messages
        $query = "
            SELECT * FROM chat_messages 
            WHERE user_id = ? AND admin_id = ?
            ORDER BY timestamp ASC
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $target_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = [
            'id' => $row['id'],
            'sender_id' => $row['sender_id'],
            'message' => htmlspecialchars($row['message']),
            'sender_role' => $row['sender_role'],
            'timestamp' => $row['timestamp']
        ];
    }
    
    echo json_encode(['messages' => $messages]);
    exit;
}

// ===========================
// GET CHAT LIST
// ===========================
if ($action === 'list') {
    
    if ($role === 'admin') {
        // Get ALL registered users so admin can chat with anyone
        $query = "
            SELECT 
                u.id,
                u.full_name,
                u.email,
                (SELECT message FROM chat_messages 
                 WHERE user_id = u.id AND admin_id = ? 
                 ORDER BY timestamp DESC LIMIT 1) as last_message,
                (SELECT timestamp FROM chat_messages 
                 WHERE user_id = u.id AND admin_id = ? 
                 ORDER BY timestamp DESC LIMIT 1) as last_timestamp
            FROM users u
            ORDER BY last_timestamp DESC, u.full_name ASC
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $user_id);
    } else {
        // Get ALL admin contacts for user - show all available admins
        $query = "
            SELECT 
                a.id,
                a.username as full_name,
                a.email,
                (SELECT message FROM chat_messages 
                 WHERE user_id = ? AND admin_id = a.id 
                 ORDER BY timestamp DESC LIMIT 1) as last_message,
                (SELECT timestamp FROM chat_messages 
                 WHERE user_id = ? AND admin_id = a.id 
                 ORDER BY timestamp DESC LIMIT 1) as last_timestamp
            FROM admins a
            ORDER BY last_timestamp DESC, a.username ASC
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $user_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $contacts = [];
    while ($row = $result->fetch_assoc()) {
        $contacts[] = $row;
    }
    
    echo json_encode(['contacts' => $contacts]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Invalid action']);
exit;
