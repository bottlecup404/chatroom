<?php
header('Content-Type: text/plain');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$message = $_POST['message'] ?? '';
$username = $_POST['username'] ?? '';

if (!preg_match('/ai/i', $message)) {
    exit; 
}

// 使用智谱清言API（修改部分）
$apiUrl = 'https://oiapi.net/API/BigModel?message=' . urlencode($message);

// 调用智谱清言API（GET请求）
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 简化SSL验证
curl_setopt($ch, CURLOPT_TIMEOUT, 15); // 设置超时时间

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// 处理API响应
$reply = '抱歉，我无法回答这个问题'; // 默认回复
if ($response !== false && $httpCode === 200) {
    $result = json_decode($response, true);
    if ($result && isset($result['code']) && $result['code'] == 1 && isset($result['message'])) {
        $reply = $result['message'];
    }
}

// 保存机器人消息
$messageFile = __DIR__ . '/data/messages.txt';
$data = [
    'id' => uniqid(),
    'username' => 'ChatGLM',
    'message' => $reply,
    'timestamp' => time(),
    'ip' => '127.0.0.1',
    'device' => 'AI服务器',
    'icon' => 'robot'
];

file_put_contents($messageFile, json_encode($data) . PHP_EOL, FILE_APPEND);
echo 'OK';
?>
