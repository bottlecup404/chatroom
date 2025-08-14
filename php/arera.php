<?php
header('Content-Type: text/plain');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$username = $_POST['username'] ?? '';
$messageID = $_POST['messageID'] ?? '';
$replyContent = $_POST['replyContent'] ?? '';

if (empty($replyContent)) {
    echo '回复内容不能为空';
    exit;
}
if (strlen($replyContent) > 5000) {
    echo '消息过长';
    exit;
}

// 过滤敏感词
$bannedWords = ['妈的', '狗', 'fuck', '鸡巴','乐子','艹'];
foreach ($bannedWords as $word) {
    if (stripos($replyContent, $word) !== false) {
        echo '消息包含不允许的内容';
        exit;
    }
}
// 保存回复消息
$messageFile = __DIR__ . '/data/messages.txt';
$data = [
    'id' => uniqid(),
    'username' => $username,
    'message' => $replyContent,
    'timestamp' => time(),
    'ip' => $_SERVER['REMOTE_ADDR'],
    'device' => $_SERVER['HTTP_USER_AGENT'] ?? '未知设备',
    'icon' => 'a',
    'reply_to' => $messageID
];

file_put_contents($messageFile, json_encode($data) . PHP_EOL, FILE_APPEND);
echo 'success';
?>