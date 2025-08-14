<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$messageFile = __DIR__ . '/data/messages.txt';
$messages = [];

if (file_exists($messageFile)) {
    $lines = file($messageFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $data = json_decode($line, true);
        if ($data) {
            $messages[] = formatMessage($data);
        }
    }
}

// 只保留最近100条消息
$messages = array_slice($messages, -100);

echo json_encode(['messages' => $messages]);

// 设备识别函数
function detectDeviceType($userAgent) {
    if (!$userAgent) return '未知设备';
    
    // 平板设备特征
    $tabletKeywords = ['iPad', 'Android(?!.*Mobile)', 'Tablet', 'Kindle', 'PlayBook', 
                      'Nexus 7', 'Nexus 9', 'Nexus 10', 'Surface', 'PlayStation Vita'];
    
    // 移动设备特征
    $mobileKeywords = ['Mobile', 'Android', 'iPhone', 'iPod', 'BlackBerry', 
                      'IEMobile', 'Windows Phone', 'SymbianOS', 'webOS', 'Opera Mini', 'Mobi'];
    
    // 检测平板设备
    foreach ($tabletKeywords as $keyword) {
        if (preg_match("/$keyword/i", $userAgent)) {
            return '平板设备';
        }
    }
    
    // 检测移动设备
    foreach ($mobileKeywords as $keyword) {
        if (preg_match("/$keyword/i", $userAgent)) {
            return '手机设备';
        }
    }
    
    // 默认识别为电脑
    return '电脑设备';
}

function formatMessage($data) {
    $username = htmlspecialchars($data['username'] ?? '');
    $message = $data['message'] ?? '';
    $timestamp = $data['timestamp'] ?? time();
    $ip = $data['ip'] ?? '';
    $device = $data['device'] ?? '';
    $icon = $data['icon'] ?? 'a';
    $messageId = $data['id'] ?? uniqid();
    
    $formattedTime = date('Y-m-d H:i:s', $timestamp);
    
    // 使用设备识别函数
    $deviceType = detectDeviceType($device);
    $timeInfo = "$formattedTime $ip $deviceType";
    
    // 格式化消息内容
    $formattedMsg = formatMessageContent($message);
    
    // 用户标识设置
    $userTitle = '用户';
    $usernameClass = '';
    $titleClass = 'user';
    
    // 管理员特殊样式
    if (strtolower($username) === 'admin') {
        $userTitle = '管理员';
        $usernameClass = 'admin-username';
        $titleClass = 'admin';
    }
    
    if ($username === 'ChatGLMl') {
        return <<<HTML
<div class='message cmsg'>
    <img class='headIcon radius' onclick='FcuioShowPopup("{$timeInfo}")' src='/images/logo/robot.jpg'>
    <div class='message-username'>{$username}<span class='htitle owner'>BOT</span></div>
    <div onclick='belibe("回复给 {$username} 的消息:","{$messageId}")' class='message-content'>{$formattedMsg}<div class='message-timestamp' id='{$messageId}'></div></div>
</div>
HTML;
    } else {
        return <<<HTML
<div class='<-{$username}->'>
    <img class='headIcon radius' onclick='FcuioShowPopup("{$timeInfo}")' src='/images/actor/{$icon}.png'>
    <span class='message-username {$usernameClass}'>{$username}<span class='htitle {$titleClass}'>{$userTitle}</span></span>
    <span onclick='belibe("回复给 {$username} 的消息:","{$messageId}")' class='message-content'>{$formattedMsg}<div class='message-timestamp' id='{$messageId}'></div></span>
</div>
HTML;
    }
}

function formatMessageContent($message) {
    // 处理BBCode标签
    $patterns = [
        '/\[img\](.*?)\[\/img\]/' => '<a href="$1"><img src="$1" alt="[图像加载失败]"></a>',
        '/\[audio\](.*?)\[\/audio\]/' => '<a href="$1"><audio controls src="$1" onerror="this.outerHTML=\'[音频加载失败]\'"></audio></a>',
        '/\[video\](.*?)\[\/video\]/' => '<a href="$1"><video src="$1" autoplay muted playsinline onerror="this.outerHTML=\'[视频加载失败]\'"></video></a>',
        '/\[h1\](.*?)\[\/h1\]/' => '<h1>$1</h1>',
        '/\[h2\](.*?)\[\/h2\]/' => '<h2>$1</h2>',
        '/\[h3\](.*?)\[\/h3\]/' => '<h3>$1</h3>',
        '/\[h4\](.*?)\[\/h4\]/' => '<h4>$1</h4>',
        '/\[h5\](.*?)\[\/h5\]/' => '<h5>$1</h5>',
        '/\[h6\](.*?)\[\/h6\]/' => '<h6>$1</h6>',
        '/\[strike\](.*?)\[\/strike\]/' => '<s>$1</s>',
        '/\[bold\](.*?)\[\/bold\]/' => '<b>$1</b>',
        '/\[link\](.*?)\[\/link\]/' => '<a href="$1">$1</a>',
        '/\[mark\](.*?)\[\/mark\]/' => '<mark>$1</mark>',
        '/\[u\](.*?)\[\/u\]/' => '<u>$1</u>',
        '/\[i\](.*?)\[\/i\]/' => '<i>$1</i>',
        '/\[marquee\](.*?)\[\/marquee\]/' => '<marquee>$1</marquee>',
        '/\[jump\](.*?)\[\/jump\]/' => '<span class="jump">$1</span>',
        '/\[shake\](.*?)\[\/shake\]/' => '<span class="shake">$1</span>'
    ];
    
    return preg_replace(array_keys($patterns), array_values($patterns), htmlspecialchars($message));
}
?>