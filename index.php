<?php 
	require('anti_ddos/start.php');
?>


<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-Frame-Options" content="SAMEORIGIN"> 
    <!-- <meta http-equiv="X-XSS-Protection" content="1; mode=block"> -->
    <meta name="author" content="bottlecup">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>聊天交流系统</title>
    <script>
    document.addEventListener('visibilitychange', function () {
    if (document.visibilityState == 'hidden') {
    normal_title = document.title;
    document.title = '再看看嘛，这么快就走了┭┮﹏┭┮';
    } else document.title = normal_title;
    });
</script>
    <script src="/js/bootart.js"></script>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/css/style.css">
    
</head>

<body>
    <div class="chat-container">
        <div class="chat-header">
            <div class="chat-title">聊天系统</div>
            <div class="chat-time" id="chat-time"></div>
            <div class="theme-switch">
                <button onclick="openModal()" class="toggle-theme">聊天设置⚙️</button>
            </div>
        </div>
        <div class="chat-box" id="chat-box"><center>消息正在加载中...</center></div>
        <div class="chat-input">
            <textarea id="message" placeholder="请输入消息......." rows="1" oninput="autoResize(this)"></textarea>
            <button onclick="chatips()" id="sends">+</button>
            <button id="send">发送</button>
            <p class="error" id="error-message" style="display: none;">消息包含不允许的关键字或长度不符合要求。</p>
        </div>
    </div>

    <div id="chatPopup-com">
        <div class="popup-header-com">
            <h2>发送特殊内容[可用§标记颜色]</h2>
            <button onclick="closePopup()">×</button>
        </div>
        <div class="popup-header-coms">
        <div class="popup-section-com upload-section">
            <label class="upload-btn">
                <input type="file" id="local-image" accept="image/png, image/jpeg, image/gif, image/webp" style="display:none">
                <span>上传本地图片</span>
            </label>
            <div class="upload-progress" style="display:none">
                <div class="progress-bar"></div>
                <span class="progress-text">0%</span>
            </div>
        </div>
            <div class="popup-section-com">
                <input type="text" id="imgUrl" placeholder="图片链接...">
                <button onclick="injectCode('img')">注入</button>
                <button class="send" onclick="directSend('img')">发送</button>
            </div>

            <div class="popup-section-com">
                <input type="text" id="videoUrl" placeholder="视频链接...">
                <button onclick="injectCode('video')">注入</button>
                <button class="send" onclick="directSend('video')">发送</button>
            </div>

            <div class="popup-section-com">
                <input type="text" id="audioUrl" placeholder="音频链接...">
                <button onclick="injectCode('audio')">注入</button>
                <button class="send" onclick="directSend('audio')">发送</button>
            </div>

            <div class="popup-section-com">
                <input type="text" id="headingText" placeholder="输入标题文本...">
                <button onclick="injectCode('h1')">注入</button>
                <button class="send" onclick="directSend('h1')">发送</button>
            </div>

            <div class="popup-section-com">
                <input type="text" id="strikeText" placeholder="输入删除线文...">
                <button onclick="injectCode('strike')">注入</button>
                <button class="send" onclick="directSend('strike')">发送</button>
            </div>

            <div class="popup-section-com">
                <input type="text" id="boldText" placeholder="输入加粗文本...">
                <button onclick="injectCode('bold')">注入</button>
                <button class="send" onclick="directSend('bold')">发送</button>
            </div>

            <div class="popup-section-com">
                <input type="text" id="linkUrl" placeholder="输入网页链接...">
                <button onclick="injectCode('link')">注入</button>
                <button class="send" onclick="directSend('link')">发送</button>
            </div>
            <div class="popup-section-com">
                <input type="text" id="markText" placeholder="输入记号文本...">
                <button onclick="injectCode('mark')">注入</button>
                <button class="send" onclick="directSend('mark')">发送</button>
            </div>
            <div class="popup-section-com">
                <input type="text" id="underlineText" placeholder="输入下划线文本...">
                <button onclick="injectCode('u')">注入</button>
                <button class="send" onclick="directSend('u')">发送</button>
            </div>
            <div class="popup-section-com">
                <input type="text" id="italicText" placeholder="输入斜体文本...">
                <button onclick="injectCode('i')">注入</button>
                <button class="send" onclick="directSend('i')">发送</button>
            </div>
            <div class="popup-section-com">
                <input type="text" id="marqueeText" placeholder="输入走马灯文本...">
                <button onclick="injectCode('marquee')">注入</button>
                <button class="send" onclick="directSend('marquee')">发送</button>
            </div>
            <div class="popup-section-com">
                <input type="text" id="jumpText" placeholder="输入跳动文本...">
                <button onclick="injectCode('jump')">注入</button>
                <button class="send" onclick="directSend('jump')">发送</button>
            </div>
            <div class="popup-section-com">
                <input type="text" id="shakeText" placeholder="输入颤抖文本...">
                <button onclick="injectCode('shake')">注入</button>
                <button class="send" onclick="directSend('shake')">发送</button>
            </div>
        </div>
    </div>
    
    <div class="modal" id="modal">
        <div class="modal-content">
            <h2>聊天设置</h2>
            <div class="icon-container">
                <div style="background-image:url(/images/actor/0.png);background-size:100%;" class="icon" onclick="selectIcon(this)"></div>
                <div style="background-image:url(/images/actor/1.png);background-size:100%;" class="icon" onclick="selectIcon(this)"></div>
                <div style="background-image:url(/images/actor/2.png);background-size:100%;" class="icon" onclick="selectIcon(this)"></div>
                <div style="background-image:url(/images/actor/3.png);background-size:100%;" class="icon" onclick="selectIcon(this)"></div>
                <div style="background-image:url(/images/actor/4.png);background-size:100%;" class="icon" onclick="selectIcon(this)"></div>
                <div style="background-image:url(/images/actor/5.png);background-size:100%;" class="icon" onclick="selectIcon(this)"></div>
            </div>
            <div class="buttons">
                <button class="save-button" id="changeBackground">更换聊天背景</button>
                <button class="save-button" onclick="toggleDivStyles()">调节背景色差</button>
            </div>
            <input type="text" class="name-input" id="nameInput" placeholder="请输入您的名称...">
            <div class="buttons">
                <button class="save-button" onclick="saveSettings()">保存</button>
                <button class="save-button" id="toggle-theme">日/夜</button>
                <button class="cancel-button" onclick="closeModal()">返回</button>
            </div>
        </div>
    </div>

    <script src="/js/index.js"></script>
</body>

</html>