/* 聊天室核心代码 */
document.addEventListener('DOMContentLoaded', function () {
    const popupShownKey = "chatpopupShown",
        popupShownTimeKey = "chatpopupShownTime",
        popupDelay = 5000,
        popupExpiry = 10 * 60 * 60 * 1000,
        clickSound = new Audio('/images/audio/send.mp3');

    let msgTimestamps = [],
        isFirstLoad = true,
        oldmages = [];

function initializeUsername() {
    // 检查是否是管理员登录
    if (localStorage.getItem('is_admin') === 'true') {
        localStorage.setItem('username', 'admin');
        localStorage.setItem('selectedIconIndex', 'a'); // 管理员默认图标
        return 'admin';
    }
    
    // 原有游客逻辑
    let username = localStorage.getItem('username');
    if (!username) {
        username = "游客" + Math.floor(10000 + Math.random() * 90000);
        localStorage.setItem('username', username);
    }
    return username;
}
    const username = initializeUsername();
    window.username = username;

    function shouldShowPopup() {
        const popupShown = localStorage.getItem(popupShownKey) === "true";
        if (popupShown) {
            const popupShownTime = parseInt(localStorage.getItem(popupShownTimeKey), 10);
            return Date.now() - popupShownTime >= popupExpiry;
        }
        return true;
    }

    function showPopup() {
        window.FcuioShowPopup("欢迎来到聊天室");
        localStorage.setItem(popupShownKey, "true");
        localStorage.setItem(popupShownTimeKey, Date.now().toString());
    }

    if (shouldShowPopup()) setTimeout(showPopup, popupDelay);

    function updateTimestamps() {
        msgTimestamps.push(Date.now());
    }

    async function loadMessages() {
        try {
            const res = await fetch('/php/load.php');
            const data = await res.json();
            const chatBox = document.getElementById('chat-box');
            let messagesHtml = '';
            function updateChatTime() {
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    document.getElementById('chat-time').textContent = `${hours}:${minutes}`;
}

// 初始化时间并设置定时器
updateChatTime();
setInterval(updateChatTime, 60000); // 每分钟更新一次
            function isEqual(obj1, obj2) {
                return JSON.stringify(obj1) === JSON.stringify(obj2);
            }

            if (!isEqual(oldmages, data.messages) && oldmages.length > 0) window.FcuioShowPopup("发现了一条新消息，请下拉到底部查看！");
            if (isEqual(oldmages, data.messages)) return;
            if (data.messages) {
                const formattedMessages = data.messages.map(function (msg) {
                    const uname = localStorage.getItem('username');
                    msg = msg.replace(/\[img\](\S+)\[\/img\]/g, '<a href="$1"><img src="$1" alt="[图像加载失败]"></a>')
                            .replace(/\[audio\](\S+)\[\/audio\]/g, '<a href="$1"><audio controls src="$1" onerror="this.outerHTML=\'[音频加载失败]\'"></audio></a>')
                            .replace(/\[video\](\S+)\[\/video\]/g, '<a href="$1"><video src="$1" autoplay muted playsinline onerror="this.outerHTML=\'[视频加载失败]\'"></video></a>')
                            .replace(/\[h1\](.*?)\[\/h1\]/g, '<h1>$1</h1>')
                            .replace(/\[h2\](.*?)\[\/h2\]/g, '<h2>$1</h2>')
                            .replace(/\[h3\](.*?)\[\/h3\]/g, '<h3>$1</h3>')
                            .replace(/\[h4\](.*?)\[\/h4\]/g, '<h4>$1</h4>')
                            .replace(/\[h5\](.*?)\[\/h5\]/g, '<h5>$1</h5>')
                            .replace(/\[h6\](.*?)\[\/h6\]/g, '<h6>$1</h6>')
                            .replace(/\[strike\](.*?)\[\/strike\]/g, '<s>$1</s>')
                            .replace(/\[bold\](.*?)\[\/bold\]/g, '<b>$1</b>')
                            .replace(/\[link\](\S+)\[\/link\]/g, '<a href="$1">$1</a>')
                            .replace(/\[mark\](.*?)\[\/mark\]/g, '<mark>$1</mark>')
                            .replace(/\[u\](.*?)\[\/u\]/g, '<u>$1</u>')
                            .replace(/\[i\](.*?)\[\/i\]/g, '<i>$1</i>')
                            .replace(/\[marquee\](.*?)\[\/marquee\]/g, '<marquee>$1</marquee>')
                            .replace(/\[jump\](.*?)\[\/jump\]/g, '<span class="jump">$1</span>')
                            .replace(/\[shake\](.*?)\[\/shake\]/g, '<span class="shake">$1</span>')
                            .replace(new RegExp(`<-${username}->`, 'g'), 'cright cmsg')
                            .replace(/<-([^>]+)->/g, (_, p1) => p1 === username ? 'cright cmsg' : 'message cmsg');
                    let openTags = [];
                    msg = msg.replace(/§([0-9a-f])/g, function (_, p1) {
                        const colorMap = {
                            '0': '#000000', '1': '#0000AA', '2': '#00AA00', '3': '#00AAAA',
                            '4': '#AA0000', '5': '#AA00AA', '6': '#FFAA00', '7': '#AAAAAA',
                            '8': '#555555', '9': '#5555FF', 'a': '#55FF55', 'b': '#55FFFF',
                            'c': '#FF5555', 'd': '#FF55FF', 'e': '#FFFF55', 'f': '#FFFFFF'
                        };
                        let closeTag = openTags.length ? '</span>' : '';
                        openTags.push('<span style="color:' + colorMap[p1] + '">');
                        return closeTag + '<span style="color:' + colorMap[p1] + '">';
                    });
                    msg += openTags.reverse().join('') + '</span>';
                    return msg;
                });
                messagesHtml = formattedMessages.join('<br>') + `<div class="tips"><span class="tips-danger">--<b>非常重要的系统提示</b>--<br><b id="dumbers">dumbers</b><br>点击任何消息旁的头像可以弹出消息详情<br>点击任何消息的内容区域可以回复消息<br>点击页面上栏设置可以<b>设置昵称/图标/聊天背景等</b><br><b>发送含<font color="blue">ai</font>两字消息<font color="green">ChatGLM</font>为你解答</b></span></div>`;
            }
            if (!localStorage.getItem('UserSent')) {
                messagesHtml += `<br><div class="card-help" id="greetingCard">
                    <h2>需要和他人打个招呼吗？👋</h2>
                    <div class="buttons">
                        <button class="secondary-btn" onclick="handleDismiss()">不再显示</button>
                        <button class="primary-btn" onclick="handleGreet()">立即打招呼</button>
                    </div></div><br><br>`;
            }
            chatBox.innerHTML = messagesHtml;
            fetch('/php/data/NumberOfVisitors.txt').then(response => response.text()).then(text => document.getElementById('dumbers').innerHTML = text);
            oldmages = data.messages || [];
            if (isFirstLoad) {
                setTimeout(() => {chatBox.scrollTop = chatBox.scrollHeight}, 100);
                isFirstLoad = false;
            }
        } catch (err) {
            window.FcuioShowPopup("获取消息失败，请检查你的网络！");
            document.getElementById('chat-time').textContent = '连接失败';
        }
    }

    function handleDismiss() {
        localStorage.setItem('UserSent', 'true');
        const card = document.getElementById('greetingCard');
        if (card) card.remove();
    }

    function handleGreet() {
        localStorage.setItem('UserSent', 'true');
        document.getElementById('message').value = `大家好，我是 ${window.username} ，来这里和大家一起交流？`;
        document.getElementById('send').click();
        handleDismiss();
    }

    document.getElementById('toggle-theme').addEventListener('click', () => {
        document.body.classList.toggle('dark-theme');
    });

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        if (e.matches) {
           document.body.classList.add('dark-theme');
        } else {
           document.body.classList.remove('dark-theme');
        }
    });

    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.body.classList.add('dark-theme');
    } else {
        document.body.classList.remove('dark-theme');
    }

    document.getElementById('send').addEventListener('click', () => {
        const msgEl = document.getElementById('message');
        let msg = msgEl.value;
        let opreas = localStorage.getItem('selectedIconIndex') || 0;

        fetch('/php/send.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ message: msg, username: username, adrulim: opreas })
        }).then(res => res.text()).then(a => {
            if (a.toLowerCase() !== 'ok') {
                window.FcuioShowPopup(a);
                return;
            }
            oldmages = [];
            loadMessages();
            msgEl.value = '';
            isFirstLoad = true;
            updateTimestamps();
            autoResize(document.getElementById("message"));
            localStorage.setItem('UserSent', 'true');
            clickSound.play();

            return fetch('/php/robot.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ message: msg, username: username })
            });
        }).then(res => res?.text()).then(robotRes => {
            /* console.log("机器人回复数据:", robotRes); */
        });
    });

    window.belibe = function (promptText, messageID) {
        let replyContent = prompt(promptText);
        if (replyContent === null) return;
        fetch("/php/arera.php", {
            method: "POST",
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ username: username, messageID: messageID, replyContent: replyContent })
        }).then(r => r.text()).then(result => {
            if (result === "success") {
                window.FcuioShowPopup('非常恭喜，回复成功！');
                oldmages = [];
                loadMessages();
            } else {
                window.FcuioShowPopup(result);
            }
        }).catch(error => {
            console.error("请求失败", error);
        });
    };

    window.handleGreet = handleGreet;
    window.handleDismiss = handleDismiss;

    loadMessages();
    setInterval(loadMessages, 3000);
});

/* 后端辅助代码 */
const bodyElement = document.body;
const changeButton = document.getElementById('changeBackground');
const images = ['/images/bgrom/1.jpg', '/images/bgrom/2.jpg', '/images/bgrom/3.jpg', '/images/bgrom/4.jpg','/images/bgrom/5.jpg','/images/bgrom/6.jpg'];

let storedIndex = parseInt(localStorage.getItem('backgroundIndex'));
let currentIndex = isNaN(storedIndex) ? 0 : storedIndex;
let selectedIcon = null;
let isStyleApplied = false;

function chatips() {
    document.getElementById("chatPopup-com").classList.add("active");
}

function closePopup() {
    document.getElementById("chatPopup-com").classList.remove("active");
}

function injectCode(type) {
    const elements = {
        h1: 'headingText',
        link: 'linkUrl',
        bold: 'boldText',
        strike: 'strikeText',
        audio: 'audioUrl',
        video: 'videoUrl',
        img: 'imgUrl',
        mark: 'markText',
        u: 'underlineText',
        i: 'italicText',
        marquee: 'marqueeText',
        jump: 'jumpText',
        shake: 'shakeText'
    };
    const fieldId = elements[type];
    if (!fieldId) return false;
    const value = document.getElementById(fieldId).value.trim();
    const combinedValues = Object.values(elements).map(id => document.getElementById(id).value.trim()).join('');
    if (!combinedValues) {
        window.FcuioShowPopup?.('请输入内容！');
        return false;
    }
    if (!value) return false;
    const tag = `[${type}]${value}[/${type}]`;
    document.getElementById('message').value += tag;
    closePopup();
    return true;
}

function directSend(type) {
    if (injectCode(type)) {
        document.getElementById('send')?.click();
    }
}

function openModal() {
    const modal = document.getElementById('modal');
    modal.classList.add('show');
    const savedName = localStorage.getItem('username');
    const savedIconIndex = +localStorage.getItem('selectedIconIndex');
    if (savedName) document.getElementById('nameInput').value = savedName;
    if (!isNaN(savedIconIndex)) {
        const icons = document.querySelectorAll('.icon');
        if (icons[savedIconIndex]) {
            icons[savedIconIndex].classList.add('selected');
            selectedIcon = icons[savedIconIndex];
        }
    }
}

function closeModal() {
    const modal = document.getElementById('modal');
    modal.classList.remove('show');
}

function selectIcon(icon) {
    selectedIcon?.classList.remove('selected');
    icon.classList.add('selected');
    selectedIcon = icon;
}

function saveSettings() {
    const userName = document.getElementById('nameInput').value.trim();
    const icons = document.querySelectorAll('.icon');
    let selectedIconIndex = Array.from(icons).indexOf(selectedIcon);
    if (!/^[^\s]{1,12}$/.test(userName) || 
    userName.toLowerCase() === 'admin' || 
    userName.toLowerCase() === 'chatglm') {
    window.FcuioShowPopup('用户名需为1-12字符，且不能为“admin”或“ChatGLM”！');
    return;
}

    if (selectedIconIndex < 0 || selectedIconIndex >= icons.length) selectedIconIndex = 0;
    localStorage.setItem('username', userName);
    localStorage.setItem('selectedIconIndex', selectedIconIndex);
    closeModal();
    location.reload();
}

function updateBackground() {
    bodyElement.style.backgroundImage = currentIndex === -1 ? 'none' : `url('${images[currentIndex]}')`;
}

changeButton?.addEventListener('click', () => {
    currentIndex = (currentIndex + 1) % (images.length + 1);
    if (currentIndex === images.length) currentIndex = -1;
    updateBackground();
    localStorage.setItem('backgroundIndex', currentIndex);
});

updateBackground();

function toggleDivStyles() {
    document.querySelectorAll('div').forEach(div => {
        if (isStyleApplied) {
            div.style.color = '';
            div.style.mixBlendMode = '';
        } else {
            div.style.color = 'invert';
            div.style.mixBlendMode = 'difference';
        }
    });
    isStyleApplied = !isStyleApplied;
}

function autoResize(textarea) {
    textarea.style.height = 'auto';
    const style = window.getComputedStyle(textarea);
    const padding = parseFloat(style.paddingTop) + parseFloat(style.paddingBottom);
    textarea.style.height = (textarea.scrollHeight - padding) + 'px';
}
// 图片上传功能 ==============================================

// 本地图片上传处理
document.getElementById('local-image')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    // 验证文件类型
    const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg','image/webp'];
    if (!validTypes.includes(file.type)) {
        window.FcuioShowPopup('只支持JPG、PNG、GIF和WebP格式的图片');
        return;
    }

    // 验证文件大小 (限制2MB)
    const maxSize = 2 * 1024 * 1024;
    if (file.size > maxSize) {
        window.FcuioShowPopup('图片大小不能超过2MB');
        return;
    }

    // 显示进度条
    const progressBar = document.querySelector('.progress-bar');
    const progressText = document.querySelector('.progress-text');
    const uploadProgress = document.querySelector('.upload-progress');
    if (uploadProgress) uploadProgress.style.display = 'block';

    const formData = new FormData();
    formData.append('image', file);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/php/upload_image.php', true);

    // 上传进度处理
    xhr.upload.onprogress = function(e) {
        if (e.lengthComputable && progressBar && progressText) {
            const percent = Math.round((e.loaded / e.total) * 100);
            progressBar.style.width = percent + '%';
            progressText.textContent = percent + '%';
        }
    };

    xhr.onload = function() {
        if (uploadProgress) uploadProgress.style.display = 'none';
        
        try {
            const response = JSON.parse(xhr.responseText);
            if (response.url) {
                const imgUrlInput = document.getElementById('imgUrl');
                if (imgUrlInput) imgUrlInput.value = response.url;
                window.FcuioShowPopup('图片上传成功！');
            } else if (response.error) {
                window.FcuioShowPopup(response.error);
            }
        } catch (e) {
            window.FcuioShowPopup('上传处理失败');
        }
    };

    xhr.onerror = function() {
        if (uploadProgress) uploadProgress.style.display = 'none';
        window.FcuioShowPopup('上传失败，请重试');
    };

    xhr.send(formData);
});

// 确保DOM加载后执行
document.addEventListener('DOMContentLoaded', function() {
    // pass
});
// 图片点击放大功能
document.addEventListener('click', function(e) {
    // 点击缩略图
    if (e.target.closest('.image-thumbnail')) {
        e.preventDefault();
        showImageModal(e.target.closest('.image-thumbnail').getAttribute('data-full'));
    }
    
    // 点击模态框关闭按钮
    if (e.target.classList.contains('close-modal') || 
        e.target.classList.contains('image-modal')) {
        hideImageModal();
    }
});