<?php
header('Content-Type: application/json');

// 极验配置 - 必须修改以下两项
define('GEETEST_CAPTCHA_ID', '7a6a146faf12599b102fd945e9b4d63b');  //id
define('GEETEST_PRIVATE_KEY', '3c5ab36d1a9baa6f49c30b2b09c277b6'); //key


define('ADMIN_PASSWORD_HASH', 'aa027bf9f74d8fc6281da9e61853cf462e9105d3ddd522f4ebc7d3e4523c72e2');

/**
 * 验证极验验证码（适配最新版极验）
 */
function verifyGeetest($lot_number, $captcha_output, $pass_token, $gen_time) {
    $url = "https://gcaptcha4.geetest.com/validate";
    
    $sign_token = hash_hmac('sha256', $lot_number, GEETEST_PRIVATE_KEY);
    
    $data = [
        'lot_number' => $lot_number,
        'captcha_output' => $captcha_output,
        'pass_token' => $pass_token,
        'gen_time' => $gen_time,
        'sign_token' => $sign_token,
        'captcha_id' => GEETEST_CAPTCHA_ID
    ];
    
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-type: application/json',
            'content' => json_encode($data),
            'timeout' => 5 // 5秒超时
        ]
    ];
    
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    
    if ($response === false) {
        return false;
    }
    
    $result = json_decode($response, true);
    return isset($result['result']) && $result['result'] === 'success';
}

/**
 * 验证管理员密码
 */
function verifyAdminPassword($input) {
    // 修改为一次hash加密（SHA-256）
    $hash = hash('sha256', $input);
    
    return hash_equals(ADMIN_PASSWORD_HASH, $hash);
}

// 主处理逻辑
try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // // 验证极验参数
    // if (empty($input['result']) || empty($input['reason']) || 
        // empty($input['captcha_args'])) {
        // throw new Exception('验证码参数不完整');
    // }
    
    // // 执行极验验证
    // if (!verifyGeetest(
        // $input['result'],
        // $input['reason'],
        // $input['pass_token'],
        // $input['gen_time']
    // )) {
        // throw new Exception('验证码验证失败');
    // }
    
    // 验证密码
    if (!verifyAdminPassword($input['password'] ?? '')) {
        throw new Exception('管理员密码错误');
    }
    
    // 验证通过
    echo json_encode([
        'success' => true,
        'message' => '验证成功'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}