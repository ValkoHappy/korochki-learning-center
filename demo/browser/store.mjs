export class DemoStore {
    constructor(key, storage = globalThis.localStorage) {
        this.key = key;
        this.storage = storage;
    }

    read() {
        const raw = this.storage.getItem(this.key);
        if (!raw) return { requests: [], profile: null };
        try {
            const data = JSON.parse(raw);
            if (Array.isArray(data.requests)) return data;
        } catch {}
        return { requests: [], profile: null };
    }

    write(data) {
        this.storage.setItem(this.key, JSON.stringify(data));
    }

    saveProfile(input) {
        const data = this.read();
        data.profile = {
            name: String(input.fio || input.name || input.login || 'Посетитель'),
            email: String(input.email || 'visitor@example.test'),
            phone: String(input.phone || ''),
        };
        this.write(data);
        return data.profile;
    }

    list() {
        return this.read().requests;
    }

    create(input, status) {
        const data = this.read();
        const id = Math.max(0, ...data.requests.map(row => Number(row.id) || 0)) + 1;
        const row = { id, status, created_at: new Date().toLocaleString('ru-RU'),
            user_name: data.profile?.name || 'Посетитель', fio: data.profile?.name || 'Посетитель',
            email: data.profile?.email || 'visitor@example.test', phone: data.profile?.phone || '' };
        for (const key of ['course_name', 'start_date', 'payment_method', 'full_name', 'address',
            'description', 'visit_date', 'type', 'date', 'count', 'patient_name', 'symptoms']) {
            if (input[key] !== undefined) row[key] = String(input[key]).trim();
        }
        data.requests.unshift(row);
        this.write(data);
        return row;
    }

    update(id, status) {
        const data = this.read();
        const row = data.requests.find(item => item.id === Number(id));
        if (!row) throw new Error('Заявка не найдена.');
        row.status = status;
        this.write(data);
    }

    remove(id) {
        const data = this.read();
        data.requests = data.requests.filter(row => row.id !== Number(id));
        this.write(data);
    }
}
