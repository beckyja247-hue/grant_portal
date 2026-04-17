<?php
session_start();
include "../config/config.php";

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$admin_id = $_SESSION['id'];
$admin_name = $_SESSION['name'] ?? 'Admin';
?>

<!DOCTYPE html>
<html>
<head>
<title>Chat - Admin Panel</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
* { margin:0; padding:0; box-sizing:border-box; }

body{
    font-family:'Segoe UI', sans-serif;
    background:#f4f6fb;
}

/* DESKTOP: SIDEBAR + MAIN */
.sidebar{
    width:260px;
    height:100vh;
    position:fixed;
    left:0;
    top:0;
    background:linear-gradient(180deg,#0f172a,#020617);
    color:white;
    padding:20px;
    overflow-y:auto;
    z-index:100;
    border-right:1px solid rgba(255,255,255,0.05);
}

.sidebar h4{ font-size:20px; margin-bottom:20px; font-weight:800; }

.sidebar a{
    display:flex;
    align-items:center;
    gap:12px;
    padding:12px 14px;
    color:#94a3b8;
    text-decoration:none;
    border-radius:10px;
    margin-bottom:8px;
    transition:0.3s;
}

.sidebar a:hover, .sidebar a.active{
    background:#22c55e;
    color:white;
    transform:translateX(5px);
}

.main-wrapper{
    margin-left:260px;
    padding:20px;
    min-height:100vh;
}

/* CHAT CONTAINER */
.chat-wrapper{
    display:grid;
    grid-template-columns:240px 1fr;
    gap:15px;
    height:75vh;
}

.users-list{
    background:white;
    border-radius:12px;
    overflow-y:auto;
    box-shadow:0 2px 8px rgba(0,0,0,0.08);
}

.user-item{
    padding:12px;
    border-bottom:1px solid #e2e8f0;
    cursor:pointer;
    transition:0.3s;
}

.user-item:hover{
    background:#f1f5f9;
}

.user-item.active{
    background:#22c55e;
    color:white;
}

.user-name{
    font-weight:600;
    font-size:14px;
}

.user-email{
    font-size:12px;
    color:#94a3b8;
    margin-top:3px;
}

.messages-area{
    background:white;
    border-radius:12px;
    display:flex;
    flex-direction:column;
    box-shadow:0 2px 8px rgba(0,0,0,0.08);
    overflow:hidden;
}

.messages-header{
    padding:15px;
    background:#0f172a;
    color:white;
    font-weight:700;
    border-bottom:1px solid #e2e8f0;
}

.messages-box{
    flex:1;
    overflow-y:auto;
    padding:15px;
    background:#fafbfc;
}

.message{
    margin-bottom:12px;
    display:flex;
    animation:slideIn 0.3s ease;
}

@keyframes slideIn {
    from { opacity:0; transform:translateY(10px); }
    to { opacity:1; transform:translateY(0); }
}

.msg-admin{
    justify-content:flex-end;
}

.msg-user{
    justify-content:flex-start;
}

.msg-content{
    max-width:70%;
    padding:10px 12px;
    border-radius:10px;
    word-wrap:break-word;
    font-size:14px;
}

.msg-admin .msg-content{
    background:#22c55e;
    color:white;
}

.msg-user .msg-content{
    background:#e2e8f0;
    color:#0f172a;
}

.msg-time{
    font-size:11px;
    color:#94a3b8;
    margin-top:4px;
}

.input-area{
    padding:15px;
    background:white;
    border-top:1px solid #e2e8f0;
    display:flex;
    gap:10px;
}

#messageInput{
    flex:1;
    padding:10px;
    border:1px solid #e2e8f0;
    border-radius:8px;
    font-size:14px;
}

#sendBtn{
    background:#22c55e;
    color:white;
    border:none;
    padding:10px 20px;
    border-radius:8px;
    cursor:pointer;
    font-weight:600;
    transition:0.2s;
}

#sendBtn:hover{
    background:#16a34a;
}

/* MOBILE: ADJUSTED */
@media(max-width:768px){
    .sidebar{
        display:none !important;
    }
    
    .main-wrapper{
        margin-left:0 !important;
        padding:10px;
        padding-bottom:80px;
    }
    
    .chat-wrapper{
        grid-template-columns:1fr;
        height:auto;
    }
    
    .users-list{
        display:none;
    }
    
    .messages-area{
        height:60vh;
    }
    
    .msg-content{
        max-width:90%;
    }
    
    .input-area{
        position:fixed;
        bottom:60px;
        left:0;
        right:0;
        gap:8px;
        padding:10px;
    }
}

.mobile-nav{
    display:none;
    position:fixed;
    bottom:0;
    left:0;
    right:0;
    background:#0f172a;
    padding:8px 0;
    justify-content:space-around;
    z-index:999;
}

.mobile-nav a{
    color:#94a3b8;
    text-decoration:none;
    font-size:11px;
    display:flex;
    flex-direction:column;
    align-items:center;
    gap:3px;
    flex:1;
}

.mobile-nav a.active{
    color:#22c55e;
}

@media(max-width:768px){
    .mobile-nav{display:flex !important;}
}

.no-selection{
    display:flex;
    align-items:center;
    justify-content:center;
    height:100%;
    color:#94a3b8;
    text-align:center;
}

</style>
</head>

<body>

<!-- DESKTOP SIDEBAR -->
<div class="sidebar">
    <h4>💰 Admin Panel</h4>
    
    <a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a>
    <a href="users.php"><i class="fa fa-users"></i> Users</a>
    <a href="add_funds.php"><i class="fa fa-wallet"></i> Add Funds</a>
    <a href="applications.php"><i class="fa fa-file-alt"></i> Applications</a>
    <a href="#" class="active"><i class="fa fa-comments"></i> Chat</a>
    <a href="grants.php"><i class="fa fa-clock"></i> Grants</a>
    <a href="manage_grants.php"><i class="fa fa-plus-circle"></i> Manage Grants</a>
    <a href="transactions.php"><i class="fa fa-money-bill-wave"></i> Transactions</a>
    
    <hr style="border-color:rgba(255,255,255,0.1);">
    
    <a href="../auth/logout.php" style="color:#ef4444;"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="main-wrapper">

    <div class="chat-wrapper">
        
        <!-- USERS LIST -->
        <div class="users-list">
            <div style="padding:12px; font-weight:600; font-size:12px; color:#64748b;">USERS</div>
            <div id="usersList"></div>
        </div>

        <!-- MESSAGES AREA -->
        <div class="messages-area">
            <div class="messages-header" id="chatHeader">Select a user to start chatting</div>
            <div class="messages-box" id="messagesBox">
                <div class="no-selection">
                    👋 Select a user to begin
                </div>
            </div>
            <div class="input-area" id="inputArea" style="display:none;">
                <input type="text" id="messageInput" placeholder="Type your message...">
                <button id="sendBtn">Send</button>
            </div>
        </div>

    </div>

</div>

<!-- MOBILE NAV -->
<div class="mobile-nav">
    <a href="dashboard.php"><i class="fa fa-home"></i></a>
    <a href="users.php"><i class="fa fa-users"></i></a>
    <a href="#" class="active"><i class="fa fa-comments"></i></a>
    <a href="add_funds.php"><i class="fa fa-wallet"></i></a>
    <a href="../auth/logout.php"><i class="fa fa-sign-out-alt"></i></a>
</div>

<script>

let currentUser = null;
let adminId = <?= $admin_id; ?>;
let refreshInterval = null;

// ===========================
// LOAD USERS
// ===========================
function loadUsers() {
    fetch('../api/chat.php?action=list')
        .then(r => r.json())
        .then(data => {
            if (data.contacts && data.contacts.length > 0) {
                renderUsers(data.contacts);
            } else {
                document.getElementById('usersList').innerHTML = '<div style="padding:15px; color:#94a3b8; font-size:13px;">No contacts</div>';
            }
        })
        .catch(err => console.error('Error:', err));
}

// ===========================
// RENDER USERS
// ===========================
function renderUsers(users) {
    let html = '';
    for (let user of users) {
        let lastMsg = user.last_message ? user.last_message.substring(0, 35) + '...' : 'No messages yet';
        html += `
            <div class="user-item ${currentUser?.id === user.id ? 'active' : ''}" 
                 onclick="selectUser(${user.id}, '${user.full_name.replace(/'/g, "\\'")}', '${user.email}')">
                <div class="user-name">${user.full_name}</div>
                <div class="user-email">${user.email}</div>
                <div style="font-size:12px; color:#94a3b8; margin-top:4px;">${lastMsg}</div>
            </div>
        `;
    }
    document.getElementById('usersList').innerHTML = html;
}

// ===========================
// SELECT USER
// ===========================
function selectUser(userId, userName, email) {
    currentUser = {id: userId, name: userName};
    
    document.querySelectorAll('.user-item').forEach(el => el.classList.remove('active'));
    event.target.closest('.user-item').classList.add('active');
    
    document.getElementById('chatHeader').textContent = '💬 ' + userName;
    document.getElementById('inputArea').style.display = 'flex';
    
    loadMessages();
    
    clearInterval(refreshInterval);
    refreshInterval = setInterval(loadMessages, 2000);
}

// ===========================
// LOAD MESSAGES
// ===========================
function loadMessages() {
    if (!currentUser) return;
    
    fetch(`../api/chat.php?action=fetch&target_id=${currentUser.id}`)
        .then(r => r.json())
        .then(data => {
            renderMessages(data.messages || []);
        })
        .catch(err => console.error('Error:', err));
}

// ===========================
// RENDER MESSAGES
// ===========================
function renderMessages(messages) {
    let html = '';
    for (let msg of messages) {
        let isAdmin = msg.sender_id === adminId;
        let time = new Date(msg.timestamp).toLocaleTimeString('en-US', {hour:'2-digit', minute:'2-digit'});
        
        html += `
            <div class="message ${isAdmin ? 'msg-admin' : 'msg-user'}">
                <div>
                    <div class="msg-content">${msg.message}</div>
                    <div class="msg-time">${time}</div>
                </div>
            </div>
        `;
    }
    
    let msgBox = document.getElementById('messagesBox');
    msgBox.innerHTML = html;
    msgBox.scrollTop = msgBox.scrollHeight;
}

// ===========================
// SEND MESSAGE
// ===========================
function sendMessage() {
    if (!currentUser) return;
    
    let input = document.getElementById('messageInput');
    let msg = input.value.trim();
    
    if (!msg) return;
    
    let formData = new FormData();
    formData.append('action', 'send');
    formData.append('target_id', currentUser.id);
    formData.append('message', msg);
    
    fetch('../api/chat.php?action=send', {method: 'POST', body: formData})
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                input.value = '';
                loadMessages();
            }
        })
        .catch(err => console.error('Error:', err));
}

// ===========================
// EVENT LISTENERS
// ===========================
document.getElementById('messageInput')?.addEventListener('keypress', e => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});

document.getElementById('sendBtn')?.addEventListener('click', sendMessage);

// ===========================
// INIT
// ===========================
loadUsers();
setInterval(loadUsers, 5000);

</script>

</body>
</html>