if (typeof wlrt_jquery === "undefined") {
    wlrt_jquery = jQuery.noConflict();
}

(function ($) {
    $(document).ready(function () {
        let wlrt_toast_box = document.getElementById('wlr-toast-box');

        window.showToast = function (type, message) {
            if (!wlrt_toast_box) {
                console.error("Toast container with ID 'wlr-toast-box' not found.");
                return;
            }

            let wlrt_toast = document.createElement('div');
            wlrt_toast.classList.add('wlr-toast');

            let icon;
            switch (type) {
                case 'success':
                    icon = '<i class="wlrt-save-success"></i>';
                    wlrt_toast.classList.add('wlrt-success');
                    break;
                case 'error':
                    icon = '<i class="wlrt-save-error"></i>';
                    wlrt_toast.classList.add('wlrt-error');
                    break;
                case 'invalid':
                    icon = '<i class="wlrt-save-problem"></i>';
                    wlrt_toast.classList.add('wlrt-invalid');
                    break;
                default:
                    icon = '';
            }

            wlrt_toast.innerHTML = `${icon} <span class="wlrt-message">${message}</span> <span class="wlr-dismiss-btn">&times;</span>`;

            wlrt_toast_box.appendChild(wlrt_toast);

            wlrt_toast.querySelector('.wlr-dismiss-btn').onclick = () => {
                wlrt_toast.classList.add('hide');
                setTimeout(() => wlrt_toast.remove(), 500);
            };

            setTimeout(() => wlrt_toast.classList.add('hide'), 3500);
            setTimeout(() => wlrt_toast.remove(), 4000);
        };
    });
})(wlrt_jquery);
