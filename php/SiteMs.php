<?php
// 定义存储访问次数的文件
$file = 'visit_count.txt';

// 检查文件是否存在，如果不存在则创建并初始化为0
if (!file_exists($file)) {
    file_put_contents($file, '0');
}

// 读取当前的访问次数
$count = (int)file_get_contents($file);

// 更新访问次数
$count++;

// 将新的访问次数写回文件
file_put_contents($file, $count);

// 返回结果
echo 'OK';
?>
