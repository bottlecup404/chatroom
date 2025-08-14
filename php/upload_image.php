<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// // 验证会话
// session_start();
// if (!isset($_SESSION['username'])) {
    // die(json_encode(['error' => '请先登录']));
// }

// 配置
$uploadDir = __DIR__.'/uploads/images/';
$allowedTypes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/jpg' => 'jpg',
    'image/webp' => 'webp'
];
$maxSize = 2 * 1024 * 1024; // 2MB

// 创建目录
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// 验证上传
if (!isset($_FILES['image'])) {
    die(json_encode(['error' => '没有文件上传']));
}

$file = $_FILES['image'];

// 验证错误代码
if ($file['error'] !== UPLOAD_ERR_OK) {
    die(json_encode(['error' => '上传错误: '.$file['error']]));
}

// 验证文件大小
if ($file['size'] > $maxSize) {
    die(json_encode(['error' => '文件大小超过2MB限制']));
}

// 验证文件类型
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);

if (!array_key_exists($mime, $allowedTypes)) {
    die(json_encode(['error' => '不支持的文件类型: '.$mime]));
}

// 生成安全文件名
$extension = $allowedTypes[$mime];
$filename = sprintf('%s.%s', sha1_file($file['tmp_name']), $extension);
$targetPath = $uploadDir.$filename;

// 移动文件
if (move_uploaded_file($file['tmp_name'], $targetPath)) {
    // 生成访问URL
    $imageUrl = 'php/uploads/images/thumbs/'.$filename;
    
    // 创建缩略图
    createThumbnail($targetPath, $uploadDir.'thumbs/'.$filename, 200, 200);
    
    echo json_encode(['url' => $imageUrl]);
} else {
    echo json_encode(['error' => '文件保存失败']);
}

// 创建缩略图函数
function createThumbnail($source, $dest, $width, $height) {
    if (!file_exists(dirname($dest))) {
        mkdir(dirname($dest), 0755, true);
    }
    
    $info = getimagesize($source);
    list($origWidth, $origHeight, $type) = $info;
    
    switch ($type) {
        case IMAGETYPE_JPEG: $img = imagecreatefromjpeg($source); break;
        case IMAGETYPE_PNG: $img = imagecreatefrompng($source); break;
        case IMAGETYPE_GIF: $img = imagecreatefromgif($source); break;
        case IMAGETYPE_WEBP: $img = imagecreatefromwebp($source); break;
        default: return false;
    }
    
    $ratio = min($width/$origWidth, $height/$origHeight);
    $newWidth = (int)($origWidth * $ratio);
    $newHeight = (int)($origHeight * $ratio);
    
    $thumb = imagecreatetruecolor($newWidth, $newHeight);
    
    // 处理透明背景
    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
        imagecolortransparent($thumb, imagecolorallocatealpha($thumb, 0, 0, 0, 127));
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
    }
    
    imagecopyresampled($thumb, $img, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
    
    switch ($type) {
        case IMAGETYPE_JPEG: imagejpeg($thumb, $dest, 90); break;
        case IMAGETYPE_PNG: imagepng($thumb, $dest); break;
        case IMAGETYPE_GIF: imagegif($thumb, $dest); break;
        case IMAGETYPE_WEBP: imagewebp($thumb, $dest); break;
    }
    
    imagedestroy($img);
    imagedestroy($thumb);
    
    return true;
}
?>