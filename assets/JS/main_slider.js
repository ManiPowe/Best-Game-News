document.addEventListener('DOMContentLoaded', function() {

    const sliderContainer = document.querySelector('.slider-container');
    const slides = document.querySelectorAll('.slide');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    const indicators = document.querySelectorAll('.indicator');

    let currentIndex = 0;
    const totalSlides = slides.length;

    function updateSlider() {

        slides.forEach(slide => {
            slide.classList.remove('active');
        });

        if (slides[currentIndex]) {
            slides[currentIndex].classList.add('active');
        }


        indicators.forEach((indicator, index) => {
            indicator.classList.remove('active');
            if (index === currentIndex) {
                indicator.classList.add('active');
            }
        });
    }


    function goToNextSlide() {
        currentIndex++;
        if (currentIndex >= totalSlides) {
            currentIndex = 0;
        }
        updateSlider();
    }

    function goToPrevSlide() {
        currentIndex--;
        if (currentIndex < 0) {
            currentIndex = totalSlides - 1;
        }
        updateSlider();
    }

    function goToSlide(index) {
        currentIndex = index;
        updateSlider();
    }

    prevBtn.addEventListener('click', goToPrevSlide);
    nextBtn.addEventListener('click', goToNextSlide);

    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            goToSlide(index);
        });
    });

    let autoSlideInterval;
    const startAutoSlide = () => {
        autoSlideInterval = setInterval(goToNextSlide, 5000);
    };

    const stopAutoSlide = () => {
        clearInterval(autoSlideInterval);
    };

    startAutoSlide();

    sliderContainer.addEventListener('mouseenter', stopAutoSlide);
    sliderContainer.addEventListener('mouseleave', startAutoSlide);

    updateSlider();
});
document.addEventListener('DOMContentLoaded', function() {
    const sliderContainer = document.querySelector('.slider-container');
    const slides = document.querySelectorAll('.slide');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    const indicatorsContainer = document.querySelector('.slider-indicators');

    let currentIndex = 0;
    const totalSlides = slides.length;

    function createIndicators() {

        indicatorsContainer.innerHTML = '';

        slides.forEach((slide, index) => {

            const indicator = document.createElement('span');
            
            indicator.classList.add('indicator');
            
            if (index === 0) {
                indicator.classList.add('active');
            }

            indicator.addEventListener('click', () => {
                goToSlide(index);
            });

            indicatorsContainer.appendChild(indicator);
        });
    }

    function updateSlider() {
        slides.forEach(slide => slide.classList.remove('active'));
        slides[currentIndex].classList.add('active');

        const indicators = document.querySelectorAll('.indicator');
        indicators.forEach((indicator, index) => {
            indicator.classList.remove('active');
            if (index === currentIndex) {
                indicator.classList.add('active');
            }
        });
    }

    function goToNextSlide() {
        currentIndex = (currentIndex + 1) % totalSlides;
        updateSlider();
    }

    function goToPrevSlide() {
        currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
        updateSlider();
    }

    function goToSlide(index) {
        currentIndex = index;
        updateSlider();
    }

    prevBtn.addEventListener('click', goToPrevSlide);
    nextBtn.addEventListener('click', goToNextSlide);

    createIndicators();
    updateSlider();

    let autoSlideInterval = setInterval(goToNextSlide, 5000);
    sliderContainer.addEventListener('mouseenter', () => clearInterval(autoSlideInterval));
    sliderContainer.addEventListener('mouseleave', () => {
        autoSlideInterval = setInterval(goToNextSlide, 5000);
    });
});
document.addEventListener('DOMContentLoaded', function() {
    // 1. Находим элементы (используем ваш класс .marquee-content)
    const track = document.querySelector('.marquee-content');
    const upBtn = document.querySelector('.up-btn');
    const downBtn = document.querySelector('.down-btn');
    
    // Получаем все оригинальные элементы внутри трека
    const originalItems = Array.from(track.children);
    
    // 2. ТРЮК БЕСКОНЕЧНОСТИ: Клонируем элементы и добавляем их в конец
    originalItems.forEach(item => {
        const clone = item.cloneNode(true);
        track.appendChild(clone);
    });

    // 3. Переменные состояния
    let posY = 0;
    const speed = 0.5; // Скорость автопрокрутки (пикселей за кадр)
    let isPaused = false;
    let animationId;

    // Вычисляем высоту одного полного набора элементов
    const singleSetHeight = track.scrollHeight / 2;

    // 4. Функция анимации
    function animate() {
        if (!isPaused) {
            posY -= speed;
            
            if (Math.abs(posY) >= singleSetHeight) {
                posY = 0;
            }
            
            track.style.transform = `translateY(${posY}px)`;
        }
        animationId = requestAnimationFrame(animate);
    }

    // 5. Запускаем анимацию
    animate();

    // 6. Логика кнопок
    const step = 50; // На сколько пикселей сдвигать при клике

    function moveUp() {
        isPaused = true;
        track.style.transition = 'transform 0.3s ease';
        
        posY += step;
        
        if (posY > 0) {
            posY = -singleSetHeight + step;
        }
        
        track.style.transform = `translateY(${posY}px)`;
        
        setTimeout(() => {
            track.style.transition = 'none';
            isPaused = false;
        }, 300);
    }

    function moveDown() {
        isPaused = true;
        track.style.transition = 'transform 0.3s ease';
        
        posY -= step;
        
        track.style.transform = `translateY(${posY}px)`;
        
        setTimeout(() => {
            track.style.transition = 'none';
            isPaused = false;
        }, 300);
    }

    // Назначаем обработчики событий
    upBtn.addEventListener('click', moveUp);
    downBtn.addEventListener('click', moveDown);

    // 7. Остановка при наведении мыши
    const marqueeContainer = document.querySelector('.game-marquee');
    marqueeContainer.addEventListener('mouseenter', () => {
        isPaused = true;
    });
    marqueeContainer.addEventListener('mouseleave', () => {
        isPaused = false;
    });
});