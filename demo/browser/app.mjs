import { DemoStore } from './store.mjs';
import { FormView } from './form-view.mjs';
import { AuthView } from './auth-view.mjs';
import { RequestTable } from './request-table.mjs';

export class DemoApp extends FormView {
    constructor(config, documentRoot = document) {
        super(documentRoot);
        this.config = config;
        this.base = new URL('../', import.meta.url);
        this.store = new DemoStore(`portfolio-demo:${config.slug}:v1`);
        this.course = config.kind === 'course';
        this.page = documentRoot.body.dataset.demoPage;
    }

    url(page) { return new URL(page, this.base).href; }
    go(page) { location.href = this.url(page); }

    init() {
        this.document.querySelectorAll('label').forEach((label, index) => {
            const control = label.nextElementSibling;
            if (!label.htmlFor && control?.matches('input, select, textarea')) {
                control.id ||= `demo-field-${index}`;
                label.htmlFor = control.id;
            }
        });
        const profileName = this.store.read().profile?.name;
        if (profileName && !this.course) this.document.querySelectorAll('p').forEach(element => {
            if (element.textContent.trim() === 'Посетитель' || element.textContent.trim() === 'Пользователь: Посетитель') {
                element.textContent = element.textContent.replace('Посетитель', profileName);
            }
        });
        this.document.querySelectorAll('form[action*="logout"]').forEach(form => {
            form.addEventListener('submit', event => {
                event.preventDefault();
                this.go('index.html');
            });
        });
        if (['login', 'register'].includes(this.page)) new AuthView({ document: this.document, store: this.store, course: this.course, go: page => this.go(page) }).bindAuth();
        if (this.page === 'request-create') this.bindRequest();
        if (['cabinet', 'admin'].includes(this.page)) {
            if (!this.course) {
                this.tableView = new RequestTable({ document: this.document, store: this.store, page: this.page });
                this.tableView.bindTable();
            }
            this.addNavigation();
        }
        if (this.course && ['cabinet', 'admin'].includes(this.page)) {
            this.showProfile();
            const form = this.document.getElementById('createAppForm');
            if (form) this.note(form, 'Учебная заявка хранится только в этом браузере и не отправляется в учебный центр.');
        }
        if (this.course && ['login', 'register'].includes(this.page)) {
            const burger = this.document.getElementById('burgerMenu');
            burger?.addEventListener('click', () => {
                burger.classList.toggle('active');
                this.document.querySelector('nav')?.classList.toggle('active');
            });
        }
        this.document.querySelectorAll('input[type="date"]').forEach(input => {
            const date = new Date();
            const today = [date.getFullYear(), String(date.getMonth()+1).padStart(2,'0'), String(date.getDate()).padStart(2,'0')].join('-');
            input.min = today;
        });
        addEventListener('storage', event => {
            if (event.key === this.store.key && this.tableView) this.tableView.renderRows();
        });
    }

    showProfile() {
        const name = this.store.read().profile?.name;
        const element = this.document.querySelector('.dashboard-welcome b');
        if (name && element) element.textContent = name;
    }

    addNavigation() {
        const target = this.course ? this.document.querySelector('.dashboard-header') : this.document.querySelector(this.config.kind === 'doctor' ? 'main.container' : '.container');
        if (!target) return;
        const nav = this.document.createElement('div');
        nav.setAttribute('role', 'navigation');
        nav.className = 'demo-links';
        nav.setAttribute('aria-label', 'Разделы демонстрации');
        for (const [label, path] of [['Главная', 'index.html'], ['Личный кабинет', this.course ? 'pages/dashboard.html' : 'cabinet.html'], ['Администратор', this.course ? 'pages/admin.html' : 'admin.html']]) {
            const link = this.document.createElement('a');
            link.href = this.url(path);
            link.textContent = label;
            nav.append(link);
        }
        target.prepend(nav);
    }

    bindRequest() {
        const form = this.document.querySelector('form:not([action*="logout"])');
        form.removeAttribute('novalidate');
        this.note(form, 'Учебная заявка сохранится только в этом браузере. Она не отправляется специалистам. Не вводите личные данные.');
        const name = form.querySelector('[name="full_name"]');
        if (name) name.value = this.store.read().profile?.name || '';
        form.addEventListener('submit', event => {
            event.preventDefault();
            if (!form.reportValidity()) return;
            try {
                this.store.create(Object.fromEntries(new FormData(form)), 'new');
                this.go('cabinet.html');
            } catch { this.message(form, 'Не удалось сохранить заявку. Разрешите хранение данных в браузере.'); }
        });
    }


}

const config = JSON.parse(document.getElementById('demo-config').textContent);
try { new DemoApp(config).init(); }
catch {
    const message = document.createElement('p');
    message.setAttribute('role', 'alert');
    message.textContent = 'Для работы демонстрации разрешите хранение данных в браузере.';
    document.querySelector('main, .container, body').prepend(message);
}
