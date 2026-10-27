document.addEventListener('DOMContentLoaded', () => {
    const burger = document.getElementById('burgerMenu');
    const nav = document.querySelector('nav');

    if (burger && nav) {
        burger.addEventListener('click', () => {
            burger.classList.toggle('active');
            nav.classList.toggle('active');
        });
    }

    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const msg = document.getElementById('message');
            msg.textContent = 'Обработка...';
            msg.style.color = 'black';

            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            try {
                const res = await fetch('../handlers/login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify(data)
                });
                const result = await res.json();

                if (result.success) {
                    msg.style.color = 'green';
                    msg.textContent = 'Успешно! Перенаправление...';
                    setTimeout(() => window.location.href = 'dashboard.php', 1000);
                } else {
                    msg.style.color = 'var(--primary-color)';
                    msg.textContent = result.message;
                }
            } catch (err) {
                msg.textContent = 'Ошибка сервера';
            }
        });
    }

    const regForm = document.getElementById('regForm');
    if (regForm) {
        regForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            const msg = document.getElementById('message');

            try {
                const response = await fetch('../handlers/register.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json', 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    msg.className = 'error-message success';
                    msg.style.color = 'green';
                    msg.textContent = result.message + '. Перенаправление...';
                    setTimeout(() => window.location.href = 'login_page.php', 1000);
                } else {
                    msg.style.color = 'red';
                    msg.textContent = result.message;
                }
            } catch (error) {
                console.error('Error:', error);
                msg.textContent = 'Ошибка отправки формы';
            }
        });
    }

    const phoneInput = document.querySelector('input[name="phone"]');
    if (phoneInput) {
        phoneInput.addEventListener('input', function (e) {
            let input = e.target.value.replace(/\D/g, '');
            let formatted = '';

            if (!input) {
                e.target.value = '';
                return;
            }

            if (input.length > 0) {
                formatted += input.substring(0, 1);
            }
            if (input.length >= 2) {
                formatted += '(' + input.substring(1, 4);
            }
            if (input.length >= 5) {
                formatted += ')' + input.substring(4, 7);
            }
            if (input.length >= 8) {
                formatted += '-' + input.substring(7, 9);
            }
            if (input.length >= 10) {
                formatted += '-' + input.substring(9, 11);
            }

            e.target.value = formatted;
        });
    }
});
