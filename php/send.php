<?php
header('Content-Type: text/plain');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$message = $_POST['message'] ?? '';
$username = $_POST['username'] ?? '游客' . mt_rand(10000, 99999);
$icon = $_POST['adrulim'] ?? 'a';


if (empty($message)) {
    echo '内容不能为空';
    exit;
}


if (strlen($message) > 5000) {
    echo '消息过长';
    exit;
}

// 过滤敏感词
$bannedWords = ['妈的', '狗', 'fuck', '鸡巴','乐子','艹'];
foreach ($bannedWords as $word) {
    if (stripos($message, $word) !== false) {
        echo '消息包含不允许的内容';
        exit;
    }
}

// 保存消息
$messageFile = __DIR__ . '/data/messages.txt';
$data = [
    'id' => uniqid(),
    'username' => $username,
    'message' => $message,
    'timestamp' => time(),
    'ip' => $_SERVER['REMOTE_ADDR'],
    'device' => $_SERVER['HTTP_USER_AGENT'] ?? '未知设备',
    'icon' => $icon
];

file_put_contents($messageFile, json_encode($data) . PHP_EOL, FILE_APPEND);
echo 'OK';
?>