import { FormView } from './form-view.mjs';

export class AuthView extends FormView {
    constructor({ document, store, course, go }) {
        super(document);
        this.store = store;
        this.course = course;
        this.go = go;
    }

    bindAuth() {
        const form = this.document.querySelector('form:not([action*="logout"])');
        if (!form) return;
        form.removeAttribute('novalidate');
        this.note(form, 'Демонстрация. Данные остаются в этом браузере. Используйте вымышленные данные и пароль.');
        form.addEventListener('submit', event => {
            event.preventDefault();
            if (!form.reportValidity()) return;
            try {
                this.store.saveProfile(Object.fromEntries(new FormData(form)));
                this.go(this.course ? 'pages/dashboard.html' : 'cabinet.html');
            } catch { this.message(form, 'Не удалось сохранить данные. Разрешите хранение данных в браузере.'); }
        });
        const choices = this.document.createElement('div');
        choices.className = 'demo-actions';
        for (const [label, admin] of [['Войти в демо без регистрации', false], ['Посмотреть кабинет администратора', true]]) {
            const button = this.document.createElement('button');
            button.type = 'button';
            button.className = 'btn ' + (this.course ? 'btn-secondary' : 'btn-outline-secondary');
            button.textContent = label;
            button.addEventListener('click', () => this.go(this.course ? (admin ? 'pages/admin.html' : 'pages/dashboard.html') : (admin ? 'admin.html' : 'cabinet.html')));
            choices.append(button);
        }
        form.after(choices);
    }

}
