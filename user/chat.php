<?php
session_start();
include "../config/config.php";

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['id'];
$user_name = $_SESSION['name'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Chat - G-Grant</title>

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
    background:#0f172a;
    color:white;
    padding:20px;
    overflow-y:auto;
    z-index:100;
}

.sidebar h4{ font-size:20px; margin-bottom:20px; font-weight:800; }

.sidebar a{
    display:block;
    padding:10px;
    margin:6px 0;
    color:#cbd5e1;
    text-decoration:none;
    border-radius:8px;
    transition:0.3s;
}

.sidebar a:hover, .sidebar a.active{
    background:#22c55e;
    color:white;
}

.main-wrapper{
    margin-left:260px;
    padding:20px;
    min-height:100vh;
}

/* CHAT CONTAINER */
.chat-wrapper{
    display:grid;
    grid-template-columns:220px 1fr;
    gap:15px;
    height:70vh;
}

.contacts-list{
    background:white;
    border-radius:12px;
    overflow-y:auto;
    box-shadow:0 2px 8px rgba(0,0,0,0.08);
}

.contact-item{
    padding:10px;
    border-bottom:1px solid #e2e8f0;
    cursor:pointer;
    transition:0.3s;
}

.contact-item:hover{
    background:#f1f5f9;
}

.contact-item.active{
    background:#22c55e;
    color:white;
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

.msg-user{
    justify-content:flex-end;
}

.msg-admin{
    justify-content:flex-start;
}

.msg-content{
    max-width:70%;
    padding:10px 12px;
    border-radius:10px;
    word-wrap:break-word;
    font-size:14px;
}

.msg-user .msg-content{
    background:#22c55e;
    color:white;
}

.msg-admin .msg-content{
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

/* MOBILE: HIDE SIDEBAR, FULL WIDTH */
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
    
    .contacts-list{
        display:none;
    }
    
    .messages-area{
        height:60vh;
    }
    
    .msg-content{
        max-width:90%;
        font-size:13px;
    }
    
    .input-area{
        position:fixed;
        bottom:60px;
        left:0;
        right:0;
        gap:8px;
        padding:10px;
    }
    
    #messageInput{
        font-size:14px;
    }
}

/* MOBILE NAV */
.mobile-nav{
    display:none;
    position:fixed;
    bottom:0;
    left:0;
    right:0;
    background:#0f172a;
    padding:8px 0;
    justify-content:space-around;
    gap:5px;
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

.mobile-nav a:hover, .mobile-nav a.active{
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
    <h4>💬 Chat</h4>
    <a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a>
    <a href="#" class="active"><i class="fa fa-comments"></i> Messages</a>
    <a href="profile.php"><i class="fa fa-user"></i> Profile</a>
    <a href="withdraw.php"><i class="fa fa-wallet"></i> Withdraw</a>
    <a href="transactions.php"><i class="fa fa-exchange-alt"></i> Transactions</a>
    <hr style="border-color:rgba(255,255,255,0.1);">
    <a href="../auth/logout.php" style="color:#ef4444;"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="main-wrapper">

    <div class="chat-wrapper">
        
        <!-- CONTACTS LIST -->
        <div class="contacts-list">
            <div style="padding:10px; font-weight:600; font-size:12px; color:#64748b;">ADMINS</div>
            <div id="contactsList"></div>
        </div>

        <!-- MESSAGES AREA -->
        <div class="messages-area">
            <div class="messages-header" id="chatHeader">Select a contact to start chatting</div>
            <div class="messages-box" id="messagesBox">
                <div class="no-selection">
                    👋 Select an admin to begin
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
    <a href="dashboard.php"><i class="fa fa-home"></i><span>Home</span></a>
    <a href="#" class="active"><i class="fa fa-comments"></i><span>Chat</span></a>
    <a href="profile.php"><i class="fa fa-user"></i><span>Profile</span></a>
    <a href="withdraw.php"><i class="fa fa-wallet"></i><span>Withdraw</span></a>
    <a href="transactions.php"><i class="fa fa-exchange-alt"></i><span>History</span></a>
</div>

<script>

let currentContact = null;
let userId = <?= $user_id; ?>;
let userName = "<?= $user_name; ?>";
let refreshInterval = null;

// ===========================
// LOAD CONTACTS
// ===========================
function loadContacts() {
    fetch('../api/chat.php?action=list')
        .then(r => r.json())
        .then(data => {
            if (data.contacts && data.contacts.length > 0) {
                renderContacts(data.contacts);
            } else {
                document.getElementById('contactsList').innerHTML = '<div style="padding:15px; color:#94a3b8; font-size:13px;">No contacts yet</div>';
            }
        })
        .catch(err => console.error('Error loading contacts:', err));
}

// ===========================
// RENDER CONTACTS
// ===========================
function renderContacts(contacts) {
    let html = '';
    for (let contact of contacts) {
        html += `
            <div class="contact-item ${currentContact?.id === contact.id ? 'active' : ''}" 
                 onclick="selectContact(${contact.id}, '${contact.full_name.replace(/'/g, "\\'")}')">
                <div style="font-size:14px; font-weight:600;">${contact.full_name}</div>
                <div style="font-size:12px; color:#94a3b8; margin-top:3px;">
                    ${contact.last_message ? contact.last_message.substring(0, 30) + '...' : 'No messages yet'}
                </div>
            </div>
        `;
    }
    document.getElementById('contactsList').innerHTML = html;
}

// ===========================
// SELECT CONTACT
// ===========================
function selectContact(contactId, contactName) {
    currentContact = {id: contactId, name: contactName};
    
    document.querySelectorAll('.contact-item').forEach(el => el.classList.remove('active'));
    event.target.closest('.contact-item').classList.add('active');
    
    document.getElementById('chatHeader').textContent = '💬 ' + contactName;
    document.getElementById('inputArea').style.display = 'flex';
    
    loadMessages();
    
    // Auto-refresh messages
    clearInterval(refreshInterval);
    refreshInterval = setInterval(loadMessages, 2000);
}

// ===========================
// LOAD MESSAGES
// ===========================
function loadMessages() {
    if (!currentContact) return;
    
    fetch(`../api/chat.php?action=fetch&target_id=${currentContact.id}`)
        .then(r => r.json())
        .then(data => {
            renderMessages(data.messages || []);
        })
        .catch(err => console.error('Error loading messages:', err));
}

// ===========================
// RENDER MESSAGES
// ===========================
function renderMessages(messages) {
    let html = '';
    for (let msg of messages) {
        let isUser = msg.sender_id === userId;
        let time = new Date(msg.timestamp).toLocaleTimeString('en-US', {hour:'2-digit', minute:'2-digit'});
        
        html += `
            <div class="message ${isUser ? 'msg-user' : 'msg-admin'}">
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
    if (!currentContact) return;
    
    let input = document.getElementById('messageInput');
    let msg = input.value.trim();
    
    if (!msg) return;
    
    let formData = new FormData();
    formData.append('action', 'send');
    formData.append('target_id', currentContact.id);
    formData.append('message', msg);
    
    fetch('../api/chat.php?action=send', {method: 'POST', body: formData})
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                input.value = '';
                loadMessages();
            }
        })
        .catch(err => console.error('Error sending message:', err));
}

// ===========================
// SEND ON ENTER
// ===========================
document.getElementById('messageInput')?.addEventListener('keypress', e => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});

// ===========================
// SEND BUTTON CLICK
// ===========================
document.getElementById('sendBtn')?.addEventListener('click', sendMessage);

// ===========================
// LOAD INITIAL DATA
// ===========================
loadContacts();
setInterval(loadContacts, 5000);

</script>

</body>
</html>