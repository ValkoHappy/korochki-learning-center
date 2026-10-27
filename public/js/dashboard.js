document.addEventListener('DOMContentLoaded', () => {
    const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char]));
    const ROLE = document.body.dataset.role || 'user';

    const burger = document.getElementById('burgerMenu');
    const nav = document.getElementById('navMenu');
    if (burger) {
        burger.addEventListener('click', () => {
            burger.classList.toggle('active');
            nav.classList.toggle('active');
        });
    }

    const startDateInput = document.getElementById('startDateInput');
    if (startDateInput) {
        const today = new Date().toISOString().split('T')[0];
        startDateInput.setAttribute('min', today);
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}.${month}.${year}`;
    }

    function showTab(tab) {
        document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
        document.getElementById('tab-' + tab).style.display = 'block';

        if (ROLE !== 'admin') {
            document.querySelectorAll('#navMenu a').forEach(el => el.classList.remove('active'));
            const listLink = document.getElementById('nav-list');
            const createLink = document.getElementById('nav-create');
            if (tab === 'list' && listLink) listLink.classList.add('active');
            if (tab === 'create' && createLink) createLink.classList.add('active');
        }

        if (tab === 'list') loadApplications();
    }
    window.showTab = showTab;

    async function loadApplications() {
        const container = document.getElementById('applicationsList');
        try {
            const res = await fetch('../handlers/get_applications.php');
            if (res.status === 401) { window.location.href = 'login_page.php'; return; }
            const json = await res.json();

            if (json.success) {
                const apps = json.data;
                container.innerHTML = '';
                if (apps.length === 0) {
                    container.innerHTML = '<div class="empty-list">Список заявок пуст</div>';
                    return;
                }

                apps.forEach(app => {
                    const card = document.createElement('div');
                    card.className = 'app-card';
                    card.setAttribute('data-status', app.status);

                    let statusBadge = `<span class="status-badge status-new">Новая</span>`;
                    if (app.status === 'Идет обучение') statusBadge = `<span class="status-badge status-progress">Идет обучение</span>`;
                    if (app.status === 'Обучение завершено') statusBadge = `<span class="status-badge status-done">Завершено</span>`;

                    const date = formatDate(app.start_date);

                    let html = `
                        <div class="app-card-header">
                            <h3 class="app-card-title">${escapeHtml(app.course_name)}</h3>
                            <div class="delete-icon" onclick="deleteApp(${app.id})"></div>
                        </div>

                        ${ROLE === 'admin' ? '<div class="app-card-user">Пользователь: ' + escapeHtml(app.fio || 'ID: ' + app.user_id) + '</div>' : ''}

                        <div class="app-card-footer">
                            ${statusBadge}
                            <span class="app-card-date">${date}</span>
                        </div>
                    `;

                    if (ROLE === 'admin') {
                        html += `
                        <div class="admin-controls">
                            <label class="admin-label">Изменить статус</label>
                            <select class="admin-select" onchange="updateStatus(${app.id}, this.value)">
                                <option value="Новая" ${app.status === 'Новая' ? 'selected' : ''}>Новая</option>
                                <option value="Идет обучение" ${app.status === 'Идет обучение' ? 'selected' : ''}>Идет обучение</option>
                                <option value="Обучение завершено" ${app.status === 'Обучение завершено' ? 'selected' : ''}>Обучение завершено</option>
                            </select>
                        </div>
                        `;
                    } else {
                        if (app.status === 'Обучение завершено') {
                            html += `<button class="btn btn-secondary btn-review" data-review-id="${app.id}">Оставить отзыв</button>`;
                        }
                    }

                    card.innerHTML = html;
                    card.querySelector('[data-review-id]')?.addEventListener('click', () => openReviewModal(app.id, app.course_name));
                    container.appendChild(card);
                });

                setupFilters();
            }
        } catch (e) {
            console.error('Ошибка загрузки заявок:', e);
        }
    }
    window.loadApplications = loadApplications;

    function setupFilters() {
        const searchInput = document.getElementById('searchInput');
        const filterBtns = document.querySelectorAll('.filter-btn');
        let currentFilter = 'all';

        function applyFilters() {
            const term = searchInput.value.toLowerCase();
            document.querySelectorAll('.app-card').forEach(card => {
                const status = card.getAttribute('data-status');
                const text = card.innerText.toLowerCase();

                const matchesSearch = text.includes(term);
                const matchesStatus = (currentFilter === 'all' || status === currentFilter);

                card.style.display = (matchesSearch && matchesStatus) ? 'block' : 'none';
            });
        }

        searchInput.addEventListener('input', applyFilters);

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                currentFilter = btn.getAttribute('data-status');
                applyFilters();
            });
        });
    }

    async function updateStatus(id, newStatus) {
        try {
            const res = await fetch('../handlers/update_application.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ id, status: newStatus })
            });
            const json = await res.json();
            if (json.success) loadApplications();
            else alert(json.message);
        } catch (e) { }
    }
    window.updateStatus = updateStatus;

    async function deleteApp(id) {
        if (!confirm('Вы уверены?')) return;
        try {
            const res = await fetch('../handlers/delete_application.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ id })
            });
            const json = await res.json();
            if (json.success) loadApplications();
        } catch (e) { }
    }
    window.deleteApp = deleteApp;

    const createAppForm = document.getElementById('createAppForm');
    if (createAppForm) {
        createAppForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const msg = document.getElementById('createMessage');
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            try {
                const res = await fetch('../handlers/create_application.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify(data)
                });
                const json = await res.json();
                if (json.success) { e.target.reset(); showTab('list'); }
                else { msg.textContent = json.message; }
            } catch (e) { msg.textContent = 'Ошибка'; }
        });
    }

    const urlParams = new URLSearchParams(window.location.search);
    const createClaim = urlParams.get('create_claim');
    const courseId = urlParams.get('course');

    if (createClaim && ROLE !== 'admin') {
        showTab('create');

        if (courseId) {
            const courseSelect = document.querySelector('select[name="course_name"]');
            const courses = [
                '',
                'Основы алгоритмизации и программирования',
                'Основы веб-дизайна',
                'Основы проектирования баз данных'
            ];

            if (courseSelect && courses[courseId]) {
                courseSelect.value = courses[courseId];
            }
        }
    } else {
        loadApplications();
    }

    function openReviewModal(appId, courseName) {
        document.getElementById('reviewAppId').value = appId;
        document.getElementById('reviewCourseName').value = courseName;
        document.getElementById('reviewText').value = '';
        document.getElementById('reviewModal').style.display = 'flex';
    }
    window.openReviewModal = openReviewModal;

    function closeReviewModal() {
        document.getElementById('reviewModal').style.display = 'none';
    }
    window.closeReviewModal = closeReviewModal;

    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            alert('Спасибо за ваш отзыв!');
            closeReviewModal();
            loadApplications();
        });
    }
});
