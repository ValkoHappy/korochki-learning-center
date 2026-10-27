import { FormView } from './form-view.mjs';

export class RequestTable extends FormView {
    constructor({ document, store, page }) {
        super(document);
        this.store = store;
        this.page = page;
        this.statuses = { new: 'Новое', confirmed: 'Подтверждено', rejected: 'Отказано' };
    }

    bindTable() {
        const table = this.document.querySelector('table');
        if (!table) return;
        this.table = table;
        this.rowTemplate = table.querySelector('tr:has(td)')?.cloneNode(true);
        if (!this.rowTemplate) return;
        this.renderRows();
        table.addEventListener('submit', event => {
            event.preventDefault();
            const form = event.target;
            const input = Object.fromEntries(new FormData(form));
            if (!(input.status in this.statuses)) return;
            try { this.store.update(input.request_id, input.status); this.renderRows(); }
            catch (error) { this.message(form, error.message); }
        });
    }

    renderRows() {
        this.table.querySelectorAll('tr:has(td)').forEach(row => row.remove());
        const records = this.store.list();
        const tbody = this.table.tBodies[0] || this.table;
        for (const record of records) {
            const row = this.rowTemplate.cloneNode(true);
            const values = { ...record, status_name: this.statuses[record.status] || 'Новое' };
            const walker = this.document.createTreeWalker(row, NodeFilter.SHOW_TEXT);
            let node;
            while ((node = walker.nextNode())) node.textContent = node.textContent.replace(/__([a-z_]+)__/g, (_, key) => String(values[key] ?? ''));
            row.cells[0].textContent = String(record.id);
            const badge = row.querySelector('.badge');
            if (badge) {
                badge.className = 'badge text-bg-' + ({new:'warning',confirmed:'success',rejected:'danger'}[record.status] || 'secondary');
                badge.textContent = values.status_name;
            } else {
                row.cells[this.page === 'admin' ? 6 : 4].textContent = values.status_name;
            }
            const select = row.querySelector('select[name="status"]');
            if (select) select.value = record.status;
            const input = row.querySelector('input[name="request_id"]');
            if (input) input.value = String(record.id);
            tbody.append(row);
        }
        if (!records.length) {
            const row = tbody.insertRow();
            const cell = row.insertCell();
            cell.colSpan = this.rowTemplate.cells.length;
            cell.textContent = 'Заявок пока нет.';
        }
        this.table.style.visibility = 'visible';
    }
}
