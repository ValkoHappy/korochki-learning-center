document.addEventListener('DOMContentLoaded', () => {
    const burger = document.getElementById('burgerMenu');
    const nav = document.getElementById('navMenu');

    if (burger && nav) {
        burger.addEventListener('click', () => {
            burger.classList.toggle('active');
            nav.classList.toggle('active');
        });

        document.querySelectorAll('nav a').forEach(link => {
            link.addEventListener('click', () => {
                burger.classList.remove('active');
                nav.classList.remove('active');
            });
        });
    }

    const slides = document.querySelectorAll('.slide-bg');
    const dots = document.querySelectorAll('.dot');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    if (slides.length > 0) {
        let currentSlide = 0;
        slides[0].classList.add('active');

        function showSlide(index) {
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));

            if (index >= slides.length) currentSlide = 0;
            else if (index < 0) currentSlide = slides.length - 1;
            else currentSlide = index;

            slides[currentSlide].classList.add('active');
            if (dots[currentSlide]) dots[currentSlide].classList.add('active');
        }

        if (nextBtn) nextBtn.addEventListener('click', () => showSlide(currentSlide + 1));
        if (prevBtn) prevBtn.addEventListener('click', () => showSlide(currentSlide - 1));

        dots.forEach((dot, idx) => {
            dot.addEventListener('click', () => showSlide(idx));
        });

        setInterval(() => showSlide(currentSlide + 1), 3000);
    }
});
