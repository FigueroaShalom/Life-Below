</main><!-- /hy-main -->

<footer class="hy-footer">
    <div class="hy-footer-inner">

        <div class="hy-footer-brand">
            <p>Life Below</p>
            <p>Explorando y protegiendo<br>nuestros océanos.</p>
            <div class="hy-footer-social">
                <a href="#" title="Instagram">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.17.054 1.97.24 2.43.403a4.92 4.92 0 011.772 1.153 4.92 4.92 0 011.153 1.772c.163.46.35 1.26.403 2.43.058 1.265.07 1.645.07 4.85s-.012 3.584-.07 4.85c-.054 1.17-.24 1.97-.403 2.43a4.92 4.92 0 01-1.153 1.772 4.92 4.92 0 01-1.772 1.153c-.46.163-1.26.35-2.43.403-1.265.058-1.645.07-4.85.07s-3.584-.012-4.85-.07c-1.17-.054-1.97-.24-2.43-.403a4.92 4.92 0 01-1.772-1.153 4.92 4.92 0 01-1.153-1.772c-.163-.46-.35-1.26-.403-2.43C2.175 15.584 2.163 15.204 2.163 12s.012-3.584.07-4.85c.054-1.17.24-1.97.403-2.43A4.92 4.92 0 013.79 2.948a4.92 4.92 0 011.772-1.153c.46-.163 1.26-.35 2.43-.403C9.257 1.334 9.637 1.322 12 1.322M12 0C8.741 0 8.333.014 7.053.072 5.775.131 4.905.333 4.14.63a6.09 6.09 0 00-2.198 1.432A6.09 6.09 0 00.63 4.14C.333 4.905.131 5.775.072 7.053.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.059 1.277.261 2.148.558 2.913a6.09 6.09 0 001.432 2.198 6.09 6.09 0 002.198 1.432c.765.297 1.636.499 2.913.558C8.333 23.986 8.741 24 12 24s3.668-.014 4.948-.072c1.277-.059 2.148-.261 2.913-.558a6.09 6.09 0 002.198-1.432 6.09 6.09 0 001.432-2.198c.297-.765.499-1.636.558-2.913C23.986 15.668 24 15.259 24 12s-.014-3.668-.072-4.948c-.059-1.277-.261-2.148-.558-2.913a6.09 6.09 0 00-1.432-2.198A6.09 6.09 0 0019.86.63C19.095.333 18.224.131 16.947.072 15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.88 1.44 1.44 0 000-2.88z"/>
                    </svg>
                </a>
                <a href="#" title="YouTube">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M23.495 6.205a3.007 3.007 0 00-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 00.527 6.205a31.247 31.247 0 00-.522 5.805 31.247 31.247 0 00.522 5.783 3.007 3.007 0 002.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 002.088-2.088 31.247 31.247 0 00.5-5.783 31.247 31.247 0 00-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/>
                    </svg>
                </a>
            </div>
        </div>

        <div class="hy-footer-col">
            <h4>Explorar</h4>
            <a href="index.php?section=articulos">Artículos</a>
            <a href="index.php?section=watch">Videos</a>
            <a href="index.php?section=galeria">Galería</a>
            <a href="index.php?section=noticias">Noticias</a>
        </div>

        <div class="hy-footer-col">
            <h4>Comunidad</h4>
            <a href="index.php?section=registro">Únete</a>
            <a href="index.php?section=login">Iniciar sesión</a>
            <a href="index.php?section=dashboard">Dashboard</a>
        </div>

        <div class="hy-footer-col">
            <h4>ODS 14</h4>
            <p style="font-size:.85rem;color:rgba(255,255,255,0.55);line-height:1.6;">
                Life Below contribuye al Objetivo de Desarrollo Sostenible 14: Vida Submarina.
            </p>
            <span class="hy-ods-badge">🌊 Vida Submarina</span>
        </div>

    </div>

    <div class="hy-footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?> — Todos los derechos reservados</p>
        <p>Hecho con 🌊 para el océano</p>
    </div>
</footer>

<script>
// Hamburger menu
const btn = document.getElementById('hyHamburger');
const menu = document.getElementById('hyMobileMenu');
if (btn && menu) {
    btn.addEventListener('click', () => {
        menu.classList.toggle('open');
        btn.classList.toggle('open');
    });
}
// Header scroll effect
window.addEventListener('scroll', () => {
    const header = document.querySelector('.hy-header');
    if (header) header.classList.toggle('scrolled', window.scrollY > 20);
});
</script>
</body>
</html>