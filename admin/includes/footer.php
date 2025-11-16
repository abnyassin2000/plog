</div> <!-- row -->
</div> <!-- container-fluid -->
<!-- Footer -->
<footer class="footer text-center py-4 mt-5">
    <div class="container">
        <p class="mb-0">&copy; 2025 PHP Blog Dashboard. Crafted with care.</p>
    </div>
</footer>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleButton = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebarNav');
        const backdrop = document.getElementById('sidebarBackdrop');
        const pageBody = document.body;

        const closeSidebar = () => {
            if (!sidebar) {
                return;
            }
            sidebar.classList.remove('is-open');
            pageBody.classList.remove('sidebar-open');
            if (backdrop) {
                backdrop.classList.remove('is-visible');
            }
        };

        if (toggleButton && sidebar) {
            toggleButton.addEventListener('click', function (event) {
                event.stopPropagation();
                const isOpen = sidebar.classList.toggle('is-open');
                pageBody.classList.toggle('sidebar-open', isOpen);
                if (backdrop) {
                    backdrop.classList.toggle('is-visible', isOpen);
                }
            });
        }

        if (backdrop) {
            backdrop.addEventListener('click', closeSidebar);
        }

        document.addEventListener('click', function (event) {
            if (!sidebar || !sidebar.classList.contains('is-open')) {
                return;
            }

            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnToggle = toggleButton && toggleButton.contains(event.target);

            if (!isClickInsideSidebar && !isClickOnToggle) {
                closeSidebar();
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth >= 992) {
                closeSidebar();
            }
        });
    });
</script>
</body>

</html>