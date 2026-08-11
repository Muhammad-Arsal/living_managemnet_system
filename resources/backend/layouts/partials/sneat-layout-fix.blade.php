<script>
    (function () {
        function normalizeLmsLayout() {
            var root = document.documentElement;
            var body = document.body;

            root.classList.remove('layout-navbar-fixed');
            root.classList.add('layout-menu-fixed');
            if (body) {
                body.classList.remove('layout-navbar-fixed');
                body.classList.add('layout-menu-fixed');
            }

            var page = document.querySelector('.layout-content-navbar .layout-page');
            if (page) {
                page.style.setProperty('padding-top', '0px', 'important');
                page.style.setProperty('padding-block-start', '0px', 'important');
            }

            var content = document.querySelector('.layout-content-navbar .content-wrapper');
            if (content) {
                content.style.setProperty('padding-top', '0px', 'important');
                content.style.setProperty('padding-block-start', '0px', 'important');
            }

            var navbar = document.getElementById('layout-navbar');
            if (navbar) {
                navbar.style.setProperty('position', 'relative', 'important');
                navbar.style.setProperty('top', 'auto', 'important');
                navbar.style.setProperty('inset', 'auto', 'important');
                navbar.style.removeProperty('left');
                navbar.style.removeProperty('right');
                navbar.style.removeProperty('width');
                navbar.style.removeProperty('inline-size');
            }

            try {
                Object.keys(localStorage).forEach(function (key) {
                    if (key.indexOf('templateCustomizer') === -1) {
                        return;
                    }

                    var raw = localStorage.getItem(key);
                    if (!raw) {
                        return;
                    }

                    try {
                        var data = JSON.parse(raw);
                        if (data && typeof data === 'object') {
                            data.navbarFixed = false;
                            data.menuFixed = true;
                            localStorage.setItem(key, JSON.stringify(data));
                        }
                    } catch (e) {}
                });
            } catch (e) {}
        }

        normalizeLmsLayout();

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', normalizeLmsLayout);
        }

        window.addEventListener('load', normalizeLmsLayout);
        setTimeout(normalizeLmsLayout, 0);
        setTimeout(normalizeLmsLayout, 150);
        setTimeout(normalizeLmsLayout, 500);
    })();
</script>
