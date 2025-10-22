/**************************************************************
* Fancybox
**************************************************************/
function initFancybox() {
    try {
        // Проверяем, существует ли Fancybox
        if (typeof Fancybox === 'undefined') {
            return;
        }

        // Уничтожаем предыдущие экземпляры Fancybox
        Fancybox.destroy();

        // Инициализируем Fancybox заново
        Fancybox.bind("[data-fancybox]", {
            theme: 'light',
            hideScrollbar: false,
            closeButton: 'outside',
            dragToClose: false,
            animated: true,
            showClass: 'f-fadeIn',
            hideClass: 'f-fadeOut',
            on: {
                init: () => {
                }
            }
        });

    } catch (error) {
        console.error("Error in " + arguments.callee.name + ":", error);
    }
}


// Ждем загрузку DOM перед инициализацией скриптов
document.addEventListener('DOMContentLoaded', function() {
    initScript();
    requestAnimationFrame(() => {
        initImageScaleAnimation();
        initTextAnimation();
    });
});
// Инициализация Lenis для плавного скролла
let lenis;


function initLenis() {
    try {
        if (typeof Lenis === 'undefined') {
            return;
        }
        
        // Уничтожаем предыдущий экземпляр Lenis, если он существует
        if (lenis) {
            lenis.destroy();
            lenis = null;
        }
        
        // Создаем новый экземпляр Lenis
        lenis = new Lenis({
            duration: 1.2,
            easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
            direction: 'vertical',
            gestureDirection: 'vertical',
            smooth: true,
            mouseMultiplier: 1,
            smoothTouch: false,
            touchMultiplier: 2,
            infinite: false,
        });

        // Экспортируем в глобальную область для доступа из других скриптов
        window.lenis = lenis;

        // Привязываем Lenis к requestAnimationFrame для обновления
        function raf(time) {
            lenis.raf(time);
            requestAnimationFrame(raf);
        }

        requestAnimationFrame(raf);


    } catch (error) {
        console.error("Error in " + arguments.callee.name + ":", error);
        
    }
}


/**
 * Устанавливает CSS-переменную для мобильных устройств
 */
function initWindowInnerheight() {
    try {
        $(document).ready(() => {
            let vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', `${vh}px`);
        });
    } catch (error) {
        console.error("Error in " + arguments.callee.name + ":", error);
        
    }
}

/**
 * Инициализирует анимацию изображений при скролле с помощью GSAP (один раз)
 */
function initImageScaleAnimation() {
    try {
        if (typeof gsap === 'undefined') {
            
            return;
        }

        if (typeof IntersectionObserver === 'undefined') {
            
            return;
        }

        const images = document.querySelectorAll('.hero img, .service__img img, .certs__gallery img');
        
        if (images.length === 0) return;

        // Устанавливаем начальное состояние для всех изображений
        gsap.set(images, { scale: 1.4 });

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Анимируем к scale(1) только один раз
                    gsap.to(entry.target, {
                        scale: 1,
                        duration: 1.2,
                        ease: "power2.out"
                    });
                    
                    // Прекращаем наблюдение за этим элементом
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        images.forEach(img => {
            observer.observe(img);
        });
        
        
    } catch (error) {
        console.error("Error in " + arguments.callee.name + ":", error);
        
    }
}


/**
 * Инициализирует анимацию заголовков по словам снизу вверх
 */
function initTextAnimation() {
    try {
        if (typeof gsap === 'undefined') {
            
            return;
        }

        if (typeof IntersectionObserver === 'undefined') {
            
            return;
        }

        const headings = document.querySelectorAll('h1, h2');
        
        if (headings.length === 0) return;

        // Подготавливаем каждый заголовок для анимации
        headings.forEach(heading => {
            // Проверяем, не был ли уже обработан этот заголовок
            if (heading.hasAttribute('data-text-animated')) {
                // Если уже обработан, просто запускаем анимацию
                const wordSpans = heading.querySelectorAll('span span');
                if (wordSpans.length > 0) {
                    gsap.set(wordSpans, { y: '100%' });
                }
                return;
            }
            
            const text = heading.textContent;
            const words = text.trim().split(/\s+/);
            
            // Особая обработка для promo h2 - применяем text-indent только к первому слову
            const isPromoH2 = heading.closest('.promo') && heading.tagName === 'H2';
            
            heading.innerHTML = '';
            
            words.forEach((word, index) => {
                const wordWrapper = document.createElement('span');
                wordWrapper.style.cssText = 'display: inline-block; clip-path: inset(0 0 -0.2em 0); vertical-align: top;';
                
                // Для первого слова в promo h2 добавляем text-indent
                if (isPromoH2 && index === 0) {
                    wordWrapper.style.marginLeft = '5vw';
                }
                
                const wordSpan = document.createElement('span');
                wordSpan.style.cssText = 'display: inline-block;';
                wordSpan.textContent = word;
                
                wordWrapper.appendChild(wordSpan);
                heading.appendChild(wordWrapper);
                
                // Добавляем пробел после слова (кроме последнего)
                if (index < words.length - 1) {
                    heading.appendChild(document.createTextNode(' '));
                }
            });
            
            // Помечаем как обработанный
            heading.setAttribute('data-text-animated', 'true');
            
            // Устанавливаем начальное состояние для всех слов
            const wordSpans = heading.querySelectorAll('span span');
            gsap.set(wordSpans, { y: '100%' });
        });

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const wordSpans = entry.target.querySelectorAll('span span');
                    
                    if (wordSpans.length === 0) return;
                    
                    // Анимируем каждое слово с плавной задержкой
                    wordSpans.forEach((span, index) => {
                        gsap.to(span, {
                            y: '0%',
                            duration: 1.2,
                            ease: "power2.out",
                            delay: index * 0.15 // увеличенная задержка между словами для лучшей видимости
                        });
                    });
                    
                    // Прекращаем наблюдение за этим элементом
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -30px 0px'
        });

        // Наблюдаем за каждым заголовком отдельно
        headings.forEach(heading => {
            observer.observe(heading);
        });
        
        
    } catch (error) {
        console.error("Error in " + arguments.callee.name + ":", error);
        
    }
}


/**
 * Инициализирует sticky header с плавными анимациями при скролле
 */
function initStickyHeader() {
    try {
        const header = document.querySelector('.header');
        
        if (!header) return;

        let isScrolled = false;
        let lastScrollTop = 0;
        let ticking = false;

        function updateHeader() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const shouldBeScrolled = scrollTop > 50;

            // Добавляем/убираем класс scrolled только при необходимости
            if (shouldBeScrolled !== isScrolled) {
                isScrolled = shouldBeScrolled;
                
                if (isScrolled) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            }

            lastScrollTop = scrollTop;
            ticking = false;
        }

        function onScroll() {
            if (!ticking) {
                requestAnimationFrame(updateHeader);
                ticking = true;
            }
        }

        // Проверяем начальное состояние
        updateHeader();

        // Привязываем обработчик скролла
        window.addEventListener('scroll', onScroll, { passive: true });
        
        // Возвращаем функцию для очистки
        return function cleanup() {
            window.removeEventListener('scroll', onScroll);
        };
        
    } catch (error) {
        console.error("Error in " + arguments.callee.name + ":", error);
    }
}

/**
 * Инициализирует мобильное меню
 */
function initMobileMenu() {
    try {
        const burger = document.querySelector('.burger');
        const mobileMenu = document.querySelector('.mobile-menu');
        const body = document.body;

        if (!burger || !mobileMenu) return;

        let isMenuOpen = false;

        // Функция открытия меню с анимацией
        function openMenu() {
            if (isMenuOpen) return;
            
            isMenuOpen = true;
            body.classList.add('menu-open');

            // GSAP анимация бургера и меню
            if (typeof gsap !== 'undefined') {
                const navLines = mobileMenu.querySelectorAll('.nav-link-line');
                const navLinks = mobileMenu.querySelectorAll('.nav-link-wrap a');
                const menuAction = mobileMenu.querySelector('.mobile-menu__action');
                const burgerSpans = burger.querySelectorAll('span');

                // Анимируем бургер в крестик
                gsap.to(burgerSpans[0], {
                    rotation: 45,
                    y: 8,
                    duration: 0.4,
                    ease: 'power2.out'
                });
                gsap.to(burgerSpans[1], {
                    opacity: 0,
                    duration: 0.2,
                    ease: 'power2.out'
                });
                gsap.to(burgerSpans[2], {
                    rotation: -45,
                    y: -8,
                    duration: 0.4,
                    ease: 'power2.out'
                });

                // Устанавливаем начальные состояния для меню
                gsap.set(navLines, { 
                    scaleX: 0,
                    opacity: 0,
                    transformOrigin: 'left center',
                    force3D: true
                });
                gsap.set(navLinks, { 
                    opacity: 0, 
                    y: 50,
                    force3D: true
                });
                gsap.set(menuAction, { 
                    opacity: 0, 
                    y: 60,
                    force3D: true
                });

                // Анимируем появление фона меню
                gsap.to(mobileMenu, {
                    opacity: 1,
                    visibility: 'visible',
                    duration: 0.3,
                    ease: 'power2.out'
                });

                // Создаем временную шкалу для меню
                const tl = gsap.timeline({ force3D: true });

                // Анимируем линии
                tl.to(navLines, {
                    scaleX: 1,
                    opacity: 1,
                    duration: 0.6,
                    ease: 'power2.out',
                    stagger: 0.08,
                    force3D: true
                }, 0.3)
                
                // Затем анимируем ссылки плавно снизу вверх
                .to(navLinks, {
                    opacity: 1,
                    y: 0,
                    duration: 0.8,
                    ease: 'power2.out',
                    stagger: 0.1,
                    force3D: true
                }, 0.4)
                
                // В конце анимируем action блок
                .to(menuAction, {
                    opacity: 1,
                    y: 0,
                    duration: 0.8,
                    ease: 'power2.out',
                    force3D: true
                }, 0.8);
            } else {
                // Fallback без GSAP
                burger.classList.add('active');
            }
        }

        // Функция закрытия меню
        function closeMenu() {
            if (!isMenuOpen) return;
            
            isMenuOpen = false;
            body.classList.remove('menu-open');

            // GSAP анимация закрытия меню и возврата бургера
            if (typeof gsap !== 'undefined') {
                const burgerSpans = burger.querySelectorAll('span');

                // Анимируем исчезновение меню
                gsap.to(mobileMenu, {
                    opacity: 0,
                    visibility: 'hidden',
                    duration: 0.3,
                    ease: 'power2.out'
                });

                // Возвращаем бургер в исходное состояние
                gsap.to(burgerSpans[0], {
                    rotation: 0,
                    y: 0,
                    duration: 0.4,
                    ease: 'power2.out'
                });
                gsap.to(burgerSpans[1], {
                    opacity: 1,
                    duration: 0.3,
                    ease: 'power2.out',
                    delay: 0.1
                });
                gsap.to(burgerSpans[2], {
                    rotation: 0,
                    y: 0,
                    duration: 0.4,
                    ease: 'power2.out'
                });
            } else {
                // Fallback без GSAP
                burger.classList.remove('active');
                mobileMenu.classList.remove('active');
            }
        }

        // Обработчик клика по бургеру
        burger.addEventListener('click', function() {
            if (isMenuOpen) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        // Обработка ссылок в мобильном меню с приоритетом
        const menuLinks = mobileMenu.querySelectorAll('a');
        menuLinks.forEach(link => {
            // Добавляем специальный атрибут чтобы отличать от других
            link.setAttribute('data-mobile-menu-link', 'true');
            
            // Удаляем все предыдущие обработчики
            const newLink = link.cloneNode(true);
            link.parentNode.replaceChild(newLink, link);
            
            newLink.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                
                // Если это anchor ссылка
                if (href && href.startsWith('#') && href !== '#') {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const targetElement = document.querySelector(href);
                    
                    if (targetElement) {
                        // Временно отключаем Lenis если он есть
                        if (lenis) {
                            lenis.stop();
                        }
                        
                        // Убираем возможные блокировки скролла
                        document.body.style.overflow = 'auto';
                        document.documentElement.style.overflow = 'auto';
                        
                        const headerHeight = 90;
                        const elementPosition = targetElement.offsetTop - headerHeight;
                        
                        // Принудительный скролл через анимацию
                        const startPosition = window.pageYOffset;
                        const distance = elementPosition - startPosition;
                        const duration = 1000;
                        let startTime = null;
                        
                        function animation(currentTime) {
                            if (startTime === null) startTime = currentTime;
                            const timeElapsed = currentTime - startTime;
                            const progress = Math.min(timeElapsed / duration, 1);
                            
                            // Easing function
                            const ease = progress * (2 - progress);
                            const currentPos = startPosition + (distance * ease);
                            
                            window.scrollTo(0, currentPos);
                            
                            if (progress < 1) {
                                requestAnimationFrame(animation);
                            } else {
                                // Возобновляем Lenis если был
                                if (lenis) {
                                    lenis.start();
                                }
                            }
                        }
                        
                        requestAnimationFrame(animation);
                        
                        // Закрываем меню после скролла
                        setTimeout(() => {
                            closeMenu();
                        }, 500);
                    } else {
                        closeMenu();
                    }
                } else {
                    // Для обычных ссылок закрываем сразу
                    closeMenu();
                }
            }, true); // Используем capture phase
        });

        // Закрытие меню при клике на фон
        mobileMenu.addEventListener('click', function(e) {
            if (e.target === mobileMenu) {
                closeMenu();
            }
        });

        // Закрытие меню при нажатии Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isMenuOpen) {
                closeMenu();
            }
        });
        
    } catch (error) {
        console.error("Error in " + arguments.callee.name + ":", error);
    }
}


/**
 * Запускает все скрипты на новой странице
 */
function initScript() {
    try {
        initLenis();
        initWindowInnerheight();
        initStickyHeader();

        initFancybox();
        initMobileMenu();
    } catch (error) {
        console.error("Error in " + arguments.callee.name + ":", error);
    }
}