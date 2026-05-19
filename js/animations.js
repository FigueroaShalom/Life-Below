// ======================================================
// LIFE BELOW - ANIMACIONES PROFESIONALES
// GSAP + Swup + ScrollTrigger + Text Split
// ======================================================

// Esperar a que el DOM cargue
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. INICIALIZAR SWUP (transiciones entre páginas)
    const swup = new Swup({
        containers: ['#swup-content'],
        animateHistoryBrowsing: true,
        animationSelector: '[class*="transition-"]'
    });
    
    // 2. ANIMACIONES DE ENTRADA (más vistosas)
    function animateEntrada() {
        // Etiqueta de héroe
        gsap.from('.hy-hero-tag', {
            duration: 0.6,
            y: -20,
            opacity: 0,
            ease: 'power2.out'
        });
        
        // Títulos principales con efecto de rebote
        gsap.from('.hy-hero-title, .hy-section-title, .os-form-title', {
            duration: 1.1,
            y: 80,
            opacity: 0,
            scale: 0.85,
            ease: 'back.out(0.7)',
            stagger: 0.12,
            delay: 0.15
        });
        
        // Subtítulos y textos
        gsap.from('.hy-hero-sub, .hy-section-sub, .os-form-sub', {
            duration: 0.9,
            y: 40,
            opacity: 0,
            ease: 'power2.out',
            delay: 0.4,
            stagger: 0.08
        });
        
        // Botones y CTA con escala mejorada
        gsap.from('.hy-cta-primary, .hy-cta-secondary, .os-btn, .os-btn-google', {
            duration: 0.7,
            scale: 0.85,
            opacity: 0,
            ease: 'back.out(0.6)',
            delay: 0.6,
            stagger: 0.08
        });
        
        // Imágenes del héroe con efecto de slide
        gsap.from('.hy-hero-img-wrap img', {
            duration: 1.1,
            x: 120,
            opacity: 0,
            scale: 0.85,
            ease: 'power3.out',
            delay: 0.5
        });
    }
    
    // 3. ANIMACIÓN DE TARJETAS AL HACER SCROLL
    gsap.registerPlugin(ScrollTrigger);
    
    function initScrollAnimations() {
        // Tarjetas de exploración, posts, noticias con stagger mejorado
        gsap.utils.toArray('.hy-explore-card, .post-card, .hy-news-card, .hy-event-card, .gallery-item').forEach((card, index) => {
            gsap.from(card, {
                scrollTrigger: {
                    trigger: card,
                    start: 'top 85%',
                    end: 'bottom 60%',
                    toggleActions: 'play none none reverse',
                    scrub: false
                },
                duration: 0.9,
                y: 70,
                opacity: 0,
                scale: 0.88,
                rotationX: 8,
                ease: 'power3.out',
                delay: index * 0.08
            });
        });
        
        // Secciones de encabezados
        gsap.utils.toArray('.hy-section-header, .hy-gallery-controls-row, .hy-tabs-row').forEach(section => {
            gsap.from(section, {
                scrollTrigger: {
                    trigger: section,
                    start: 'top 80%',
                    toggleActions: 'play none none reverse'
                },
                duration: 0.7,
                y: 40,
                opacity: 0,
                ease: 'power2.out'
            });
        });
        
        // Elementos de contenido (párrafos)
        gsap.utils.toArray('.article-content p, .section-description').forEach(el => {
            gsap.from(el, {
                scrollTrigger: {
                    trigger: el,
                    start: 'top 90%',
                    toggleActions: 'play none none reverse'
                },
                duration: 0.8,
                y: 30,
                opacity: 0,
                ease: 'power2.out'
            });
        });
    }
    
    
    // 4. EFECTO DE BURBUJAS SUBMARINAS (solo registro/login)
    function createUnderwaterBubbles() {
        const container = document.querySelector('.os-wrapper');
        if (!container) return;
        
        setInterval(() => {
            const bubble = document.createElement('div');
            bubble.className = 'premium-bubble';
            const size = Math.random() * 40 + 10;
            bubble.style.width = size + 'px';
            bubble.style.height = size + 'px';
            bubble.style.left = Math.random() * 100 + '%';
            bubble.style.bottom = '-50px';
            bubble.style.position = 'fixed';
            bubble.style.background = 'radial-gradient(circle, rgba(0,212,232,0.4) 0%, rgba(0,212,232,0) 70%)';
            bubble.style.borderRadius = '50%';
            bubble.style.pointerEvents = 'none';
            bubble.style.zIndex = '999';
            bubble.style.filter = 'blur(0.5px)';
            document.body.appendChild(bubble);
            
            gsap.to(bubble, {
                duration: Math.random() * 4 + 3,
                y: -window.innerHeight - 100,
                opacity: 0,
                x: Math.sin(Math.random() * Math.PI * 2) * 40,
                ease: 'none',
                onComplete: () => bubble.remove()
            });
        }, 600);
    }
    
    // 5. HOVER EN BOTONES (GSAP) - MEJORADO
    function initButtonHoverAnimations() {
        document.querySelectorAll('.os-btn, .hy-btn-solid, .hy-btn-outline, .hy-cta-primary, .hy-cta-secondary, .read-more-btn').forEach(btn => {
            btn.addEventListener('mouseenter', () => {
                gsap.to(btn, { 
                    duration: 0.3, 
                    scale: 1.08, 
                    boxShadow: '0 12px 32px rgba(0,154,170,0.45)', 
                    y: -3,
                    ease: 'power2.out'
                });
            });
            btn.addEventListener('mouseleave', () => {
                gsap.to(btn, { 
                    duration: 0.25, 
                    scale: 1, 
                    boxShadow: '0 4px 12px rgba(0,119,190,0.2)', 
                    y: 0,
                    ease: 'power2.out'
                });
            });
        });
    }
    
    // 6. HOVER EN TARJETAS (GSAP) - MEJORADO CON EFECTO DE BRILLO
    function initCardHoverAnimations() {
        document.querySelectorAll('.post-card, .hy-explore-card, .hy-news-card, .hy-event-card, .gallery-item').forEach(card => {
            card.addEventListener('mouseenter', () => {
                gsap.to(card, { 
                    duration: 0.35, 
                    y: -12, 
                    boxShadow: '0 24px 52px rgba(0,154,170,0.25)',
                    scale: 1.02,
                    ease: 'power3.out'
                });
                // Efecto de brillo sutil en el borde
                gsap.to(card, {
                    duration: 0.35,
                    borderColor: 'rgba(0, 180, 220, 0.4)',
                    ease: 'power2.out'
                });
            });
            card.addEventListener('mouseleave', () => {
                gsap.to(card, { 
                    duration: 0.3, 
                    y: 0, 
                    scale: 1,
                    ease: 'power2.out'
                });
                gsap.to(card, {
                    duration: 0.3,
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    ease: 'power2.out'
                });
            });
        });
    }
    
    // 7. ANIMACIONES DE SWUP (transición entre páginas) - MEJORADA
    swup.hooks.on('willReplaceContent', () => {
        gsap.to('#swup-content', {
            duration: 0.35,
            opacity: 0,
            y: -30,
            ease: 'power2.in'
        });
    });
    
    swup.hooks.on('contentReplaced', () => {
        // Reset de ScrollTrigger
        ScrollTrigger.getAll().forEach(trigger => trigger.kill());
        
        // Fade in del contenido nuevo
        gsap.fromTo('#swup-content', 
            { opacity: 0, y: 30 },
            { duration: 0.5, opacity: 1, y: 0, ease: 'power2.out', delay: 0.15 }
        );
        
        // Reaplicar todas las animaciones al nuevo contenido
        animateEntrada();
        initScrollAnimations();
        initButtonHoverAnimations();
        initCardHoverAnimations();
        
        // Refrescar ScrollTrigger para nuevos elementos
        ScrollTrigger.refresh();
    });
    
    // 8. INICIALIZAR TODO
    animateEntrada();
    initScrollAnimations();
    initButtonHoverAnimations();
    initCardHoverAnimations();
    
    // Burbujas en login/registro
    if (window.location.href.includes('section=registro') || window.location.href.includes('section=login')) {
        createUnderwaterBubbles();
    }
    
    // 9. PARALLAX MEJORADO en el héroe
    window.addEventListener('scroll', () => {
        const scrolled = window.scrollY;
        const heroBg = document.querySelector('.hy-hero-bg');
        if (heroBg) {
            gsap.to(heroBg, { 
                duration: 0.15, 
                y: scrolled * 0.25,
                overwrite: 'auto'
            });
        }
    }, { passive: true });
    // Animación de las capas oceánicas (fade in al hacer scroll)
gsap.utils.toArray('.ocean-layer').forEach((layer, i) => {
    gsap.from(layer, {
        scrollTrigger: {
            trigger: layer,
            start: 'top 80%',
            toggleActions: 'play none none reverse'
        },
        duration: 1.2,
        opacity: 0,
        y: 50,
        ease: 'power3.out'
    });
    
    // Texto dentro de cada capa aparece con retraso
    gsap.from(layer.querySelector('.content'), {
        scrollTrigger: {
            trigger: layer,
            start: 'top 70%',
            toggleActions: 'play none none reverse'
        },
        duration: 0.8,
        scale: 0.9,
        opacity: 0,
        delay: 0.2,
        ease: 'back.out(0.5)'
    });
});
    // 10. ANIMACIÓN DE ENLACE SUAVE
    document.querySelectorAll('a[href*="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href.startsWith('#')) {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    gsap.to(window, {
                        duration: 1,
                        scrollTo: { y: target, offsetY: 80 },
                        ease: 'power3.inOut'
                    });
                }
            }
        });
    });
});