export class FormView {
    constructor(documentRoot) { this.document = documentRoot; }

    note(form, text) {
        const note = this.document.createElement('p');
        note.className = 'demo-note';
        note.textContent = text;
        form.prepend(note);
    }

    message(form, text) {
        let message = form.querySelector('[role="alert"]');
        if (!message) {
            message = this.document.createElement('p');
            message.setAttribute('role', 'alert');
            form.prepend(message);
        }
        message.textContent = text;
    }

}
