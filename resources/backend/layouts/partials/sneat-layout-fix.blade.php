<script>
    (function () {
        function normalizeLmsLayout() {
            var root = document.documentElement;
            var body = document.body;

            // In-flow navbar + in-flow sidebar. Fixed-menu mode adds left padding that
            // overflows horizontally; 100vh sticky menus create phantom vertical scroll.
            root.classList.remove('layout-navbar-fixed', 'layout-menu-fixed', 'layout-menu-fixed-offcanvas');
            if (body) {
                body.classList.remove('layout-navbar-fixed', 'layout-menu-fixed', 'layout-menu-fixed-offcanvas');
            }

            var page = document.querySelector('.layout-content-navbar .layout-page');
            if (page) {
                page.style.setProperty('padding-top', '0px', 'important');
                page.style.setProperty('padding-block-start', '0px', 'important');
                page.style.setProperty('padding-inline-start', '0px', 'important');
                page.style.removeProperty('width');
                page.style.removeProperty('max-width');
                page.style.removeProperty('height');
                page.style.removeProperty('max-height');
            }

            var content = document.querySelector('.layout-content-navbar .content-wrapper');
            if (content) {
                content.style.setProperty('padding-top', '0px', 'important');
                content.style.setProperty('padding-block-start', '0px', 'important');
            }

            var menu = document.getElementById('layout-menu');
            if (menu) {
                menu.style.removeProperty('position');
                menu.style.removeProperty('inset');
                menu.style.removeProperty('inset-block');
                menu.style.removeProperty('inset-inline-start');
                menu.style.removeProperty('top');
                menu.style.removeProperty('left');
                menu.style.removeProperty('bottom');
                menu.style.removeProperty('height');
                menu.style.removeProperty('max-height');
                menu.style.removeProperty('overflow');
                menu.style.removeProperty('overflow-y');
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
                            data.menuFixed = false;
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
