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