/* 弹窗功能 */
(function(window) {
    var containerId = 'fcuio-popup-container';
    function getContainer() {
        var container = document.getElementById(containerId);
        if (!container) {
            container = document.createElement('div');
            container.id = containerId;
            container.style.position = 'fixed';
            container.style.top = '20px';
            container.style.left = '50%';
            container.style.transform = 'translateX(-50%)';
            container.style.zIndex = '10000';
            container.style.width = '100%';
            container.style.maxWidth = '600px';
            container.style.pointerEvents = 'none';
            document.body.appendChild(container);
        }
        return container;
    }

    function addStyles() {
        if (document.getElementById('fcuio-popup-styles')) return;
        var style = document.createElement('style');
        style.id = 'fcuio-popup-styles';
        style.innerHTML = `
            .fcuio-popup {
                background: rgba(255, 255, 255, 0.15);
                color: #fff;
                padding: 14px 24px;
                border-radius: 20px;
                font-size: 16px;
                margin-top: 12px;
                text-align: center;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.3);
                opacity: 0;
                transform: translateY(-20px);
                transition: opacity 0.4s ease-out, transform 0.4s ease-out;
                pointer-events: auto;
                font-family: 'Segoe UI', 'Helvetica Neue', Helvetica, Arial, sans-serif;
                word-wrap: break-word;
                background-clip: padding-box;
            }

            .fcuio-popup.show {
                opacity: 1;
                transform: translateY(0);
            }

            .fcuio-popup.hide {
                opacity: 0;
                transform: translateY(20px);
            }

            @media (max-width: 480px) {
                .fcuio-popup {
                    font-size: 14px;
                    padding: 12px 16px;
                }
            }
        `;
        document.head.appendChild(style);
    }

    window.FcuioShowPopup = function(message, duration) {
        duration = duration || 2500;
        try {
            addStyles();
            var container = getContainer();
            var popup = document.createElement('div');
            popup.className = 'fcuio-popup';
            popup.innerHTML = message;
            container.appendChild(popup);
            setTimeout(() => popup.classList.add('show'), 10);
            setTimeout(() => {
                popup.classList.remove('show');
                popup.classList.add('hide');
                popup.addEventListener('transitionend', function() {
                    if (popup.parentNode) container.removeChild(popup);
                });
            }, duration);
            return true;
        } catch (e) {
            console.error("弹窗创建失败:", e);
            alert(message);
            return false;
        }
    };
})(window);

/* 统计访问 */
(function() {
    const randomString = Math.random().toString(36).substring(2);
    const url = `/php/SiteMs.php?_=${randomString}`;
    fetch(url, { cache: 'no-store' })
    .then(response => response.text())
    .then(data => {
        if (data.trim() === 'OK') {
            console.log('[服务器与你的连接非常通畅]');
        } else {
            window.location.href = window.location.href + `/==R==/${randomString}`;
        }
    })
    .catch(error => {
        console.error('获取失败:', error);
        window.location.href = window.location.href + `/==R==/${randomString}`;
    });
})();

/* 防止缓存 */
(function() {
    const originalFetch = window.fetch;
    const originalXMLHttpRequestOpen = window.XMLHttpRequest.prototype.open;
    window.fetch = function(input, init = {}) {
        init = init || {};
        init.headers = init.headers || {};
        if (typeof init.headers.set === 'function') {
            init.headers.set('Cache-Control', 'no-cache, no-store, must-revalidate');
            init.headers.set('Pragma', 'no-cache');
            init.headers.set('Expires', '0');
        } else {
            init.headers['Cache-Control'] = 'no-cache, no-store, must-revalidate';
            init.headers['Pragma'] = 'no-cache';
            init.headers['Expires'] = '0';
        }
        if (typeof input === 'string') {
            const url = new URL(input, window.location.href);
            url.searchParams.append('_', Date.now());
            input = url.toString();
        }
        return originalFetch.call(this, input, init);
    };
    window.XMLHttpRequest.prototype.open = function(method, url, async, user, password) {
        const newUrl = new URL(url, window.location.href);
        newUrl.searchParams.append('_', Date.now());
        const xhr = originalXMLHttpRequestOpen.call(this, method, newUrl.toString(), async, user, password);
        this.setRequestHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
        this.setRequestHeader('Pragma', 'no-cache');
        this.setRequestHeader('Expires', '0');
        return xhr;
    };
})();
// // 字体被复制时的样式设置
    // var selectionStyle = document.createElement('style');
    // selectionStyle.innerHTML = `::selection{background-color:#000;color:#fff;text-shadow:0 0 10px rgba(255,255,255,0.8)}p::selection{background-color:rgba(255,255,0,0.5);color:#000;text-shadow:0 0 10px rgba(0,0,0,0.8)}`;
    // document.head.appendChild(selectionStyle);

// })();

// /* 绑架全局fetch函数强制防缓存 */
// (function() {
  // const originalFetch = window.fetch;
  // window.fetch = function(input, init = {}) {
    // let url;
    // if (typeof input === 'string') {
      // url = new URL(input, location.href);
    // } else if (input instanceof Request) {
      // url = new URL(input.url, location.href);
    // } else {
      // throw new Error('Unsupported fetch input type');
    // }
    // url.searchParams.set('_t', Date.now() + '_' + Math.random().toString(36).substr(2, 6));
    // if (typeof input === 'string') {
      // input = url.toString();
    // } else if (input instanceof Request) {
      // input = new Request(url.toString(), input);
    // }

    // init.cache = 'no-store';

    // return originalFetch(input, init);
  // };
// })();
/* 动态适配 */
document.documentElement.style.setProperty('--vh', `${window.innerHeight * 0.01}px`);
setInterval(() => document.documentElement.style.setProperty('--vh', `${window.innerHeight * 0.01}px`), 300);
