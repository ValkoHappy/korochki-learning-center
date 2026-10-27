export class CourseDemoApi {
    static statuses = ['Новая', 'Идет обучение', 'Обучение завершено'];

    constructor(store) { this.store = store; }

    async request(path, options = {}) {
        const action = String(path).split('/').pop();
        const body = JSON.parse(options.body || '{}');
        let result;
        try {
            switch (action) {
                case 'get_applications.html':
                case 'get_applications.php':
                    result = { success: true, data: this.store.list() };
                    break;
                case 'create_application.html':
                case 'create_application.php':
                    if (!body.course_name || !body.start_date || !body.payment_method) throw new Error('Заполните все поля.');
                    this.store.create(body, 'Новая');
                    result = { success: true };
                    break;
                case 'update_application.html':
                case 'update_application.php':
                    if (!CourseDemoApi.statuses.includes(body.status)) throw new Error('Неизвестный статус.');
                    this.store.update(body.id, body.status);
                    result = { success: true };
                    break;
                case 'delete_application.html':
                case 'delete_application.php':
                    this.store.remove(body.id);
                    result = { success: true };
                    break;
                default: throw new Error('Операция недоступна в демонстрации.');
            }
        } catch (error) {
            result = { success: false, message: error.message };
        }
        return { status: 200, json: async () => result };
    }
}
