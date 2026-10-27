from pathlib import Path
from urllib.request import Request, build_opener, HTTPCookieProcessor, HTTPRedirectHandler
from urllib.error import HTTPError, URLError
from urllib.parse import urlencode
from http.cookiejar import CookieJar
from datetime import date, timedelta
import json, os, re, sys, time, uuid

APP = Path(__file__).resolve().parents[1].name
BASE = sys.argv[1].rstrip('/')
COUNT = 0

class NoRedirect(HTTPRedirectHandler):
    def redirect_request(self, req, fp, code, msg, headers, newurl):
        return None

class Client:
    def __init__(self):
        self.opener = build_opener(HTTPCookieProcessor(CookieJar()), NoRedirect())
        self.token = ''

    def request(self, path, data=None, json_body=False, token=True):
        headers = {}
        payload = None
        if data is not None:
            data = dict(data)
            if token:
                if json_body: headers['X-CSRF-Token'] = self.token
                else: data['_csrf'] = self.token
            headers['Content-Type'] = 'application/json' if json_body else 'application/x-www-form-urlencoded'
            payload = (json.dumps(data) if json_body else urlencode(data)).encode()
        try:
            response = self.opener.open(Request(BASE + path, data=payload, headers=headers), timeout=10)
        except HTTPError as error:
            response = error
        body = response.read().decode('utf-8')
        return response.code, body, response.headers

    def page(self, path='/'):
        status, text, _ = self.request(path)
        check(status == 200, f'GET {path}: {status}')
        match = re.search(r'name="_csrf" value="([a-f0-9]+)"|name="csrf-token" content="([a-f0-9]+)"', text)
        if match: self.token = next(value for value in match.groups() if value)
        check('Fatal error' not in text and 'Warning:' not in text, f'No PHP errors on {path}')
        return text

def check(condition, label):
    global COUNT
    if not condition: raise AssertionError(label)
    COUNT += 1

for attempt in range(30):
    try:
        Client().request('/')
        break
    except URLError:
        if attempt == 29: raise
        time.sleep(.2)

suffix = uuid.uuid4().hex[:10]
email = f'user{suffix}@example.test'
password = 'TestUserPassword2026!'
admin_email = os.environ['TEST_ADMIN_EMAIL']
admin_password = os.environ['TEST_ADMIN_PASSWORD']
future = (date.today() + timedelta(days=7)).isoformat()
user, other, admin = Client(), Client(), Client()

if APP == 'korochki-learning-center':
    user.page('/pages/register_page.php')
    check(user.request('/handlers/get_applications.php')[0] == 401, 'Guest cannot list applications')
    form = {'login': 'user' + suffix, 'fio': 'Тестовый Пользователь', 'phone': '8(000)000-00-00', 'email': email, 'password': password}
    check(user.request('/handlers/register.php', form, True, False)[0] == 403, 'Registration rejects missing CSRF')
    check(json.loads(user.request('/handlers/register.php', form, True)[1])['success'], 'Register user')
    user.page('/pages/login_page.php')
    check(user.request('/handlers/login.php', {'login':form['login'], 'password':'wrong'}, True)[0] == 401, 'Wrong password rejected')
    check(json.loads(user.request('/handlers/login.php', {'login':form['login'], 'password':password}, True)[1])['success'], 'Login user')
    dashboard = user.page('/pages/dashboard.php')
    check('Тестовый Пользователь' in dashboard, 'User name rendered')
    application = {'course_name': 'Основы веб-дизайна', 'start_date': future, 'payment_method':'Наличные'}
    check(user.request('/handlers/create_application.php', application, True, False)[0] == 403, 'Create rejects missing CSRF')
    check(user.request('/handlers/create_application.php', dict(application, course_name='<script>alert(1)</script>'), True)[0] == 422, 'Reject unknown course')
    check(json.loads(user.request('/handlers/create_application.php', application, True)[1])['success'], 'Create application')
    rows = json.loads(user.request('/handlers/get_applications.php')[1])['data']
    request_id = rows[0]['id']
    check(len(rows) == 1 and rows[0]['course_name'] == application['course_name'], 'List owned application')
    check(user.request('/handlers/update_application.php', {'id':request_id,'status':'Обучение завершено'},True)[0] == 403, 'User cannot change status')
    other.page('/pages/register_page.php')
    form2 = dict(form, login='other'+suffix, email='other'+email)
    other.request('/handlers/register.php',form2,True)
    other.request('/handlers/login.php', {'login':form2['login'],'password':password},True)
    other.page('/pages/dashboard.php')
    check(json.loads(other.request('/handlers/get_applications.php')[1])['data'] == [], 'Another user cannot see application')
    check(other.request('/handlers/delete_application.php', {'id':request_id},True)[0] == 404, 'Another user cannot delete application')
    admin.page('/pages/login_page.php')
    check(json.loads(admin.request('/handlers/login.php', {'login':'administrator','password':admin_password},True)[1])['success'], 'Admin login')
    admin.page('/pages/dashboard.php')
    check(admin.request('/handlers/update_application.php', {'id':request_id,'status':'invalid'},True)[0] == 422, 'Reject invalid status')
    check(json.loads(admin.request('/handlers/update_application.php', {'id':request_id,'status':'Обучение завершено'},True)[1])['success'], 'Admin changes status')
    rows = json.loads(user.request('/handlers/get_applications.php')[1])['data']
    check(rows[0]['status'] == 'Обучение завершено', 'Updated status visible to owner')
    check(json.loads(user.request('/handlers/delete_application.php', {'id':request_id},True)[1])['success'], 'Owner deletes application')
    check(user.request('/handlers/logout.php')[0] == 405, 'GET logout rejected')
    check(user.request('/handlers/logout.php', {})[0] == 302, 'POST logout')
    check(user.request('/pages/dashboard.php')[0] == 302, 'Cabinet protected after logout')
elif APP == 'doctor-home-visits':
    entry='/doctor.php'
    user.page(entry+'?action=register')
    check(user.request(entry+'?action=set_status', {'request_id':1,'status':'confirmed'},token=False)[0] == 403, 'Reject missing CSRF')
    form={'email':email,'name':'Тестовый Пользователь','phone':'+70000000000','password':password}
    status, text, _=user.request(entry+'?action=register',form)
    check(status in [200,302] and 'Fatal error' not in text, 'Register doctor user')
    user.page(entry+'?action=login')
    check(user.request(entry+'?action=login',{'email':email,'password':password})[0]==302,'Login doctor user')
    user.page(entry+'?action=new_request')
    form={'patient_name':'Учебный Пациент','address':'Тестовый адрес для проверки','symptoms':'Тестовая заявка '+suffix}
    status,text,_=user.request(entry+'?action=save_request',form)
    check(status==200 and 'Заявка добавлена' in text,'Create doctor request')
    check(user.request(entry+'?action=set_status',{'request_id':1,'status':'confirmed'})[0]==403,'User cannot change medical request status')
    other.page(entry+'?action=register')
    other.request(entry+'?action=register',{'email':'other'+email,'name':'Другой Пользователь','phone':'+70000000000','password':password})
    other.request(entry+'?action=login',{'email':'other'+email,'password':password})
    check(suffix not in other.page(entry+'?action=cabinet'),'Other user cannot see medical request')
    admin.page(entry+'?action=login')
    check(admin.request(entry+'?action=login',{'email':admin_email,'password':admin_password})[0]==302,'Admin login')
    text=admin.page(entry+'?action=cabinet')
    check(suffix in text,'Admin sees request')
    request_id=re.search(r'name="request_id" value="(\d+)"',text).group(1)
    status,text,_=admin.request(entry+'?action=set_status',{'request_id':request_id,'status':'confirmed'})
    check(status==200 and 'Статус обновлён' in text,'Admin changes medical status')
    check('<td>Подтверждено</td>' in user.page(entry+'?action=cabinet'),'Owner sees confirmed status')
    check(user.request(entry+'?action=logout')[0]==405,'GET logout rejected')
    check(user.request(entry+'?action=logout',{})[0]==302,'POST logout')
else:
    user.page('/register.php')
    form={'email':email,'name':'Тестовый Пользователь','phone':'+70000000000','password':password}
    check(user.request('/register.php',form,token=False)[0]==403,'Register rejects missing CSRF')
    check(user.request('/register.php',form)[0]==302,'Register user')
    user.page('/login.php')
    check('Неверный' in user.request('/login.php',{'email':email,'password':'wrong'})[1],'Wrong password rejected')
    check(user.request('/login.php',{'email':email,'password':password})[0]==302,'Login user')
    user.page('/request-create.php')
    if APP=='computer-repair-service':
        form={'full_name':'Учебный Клиент','address':'Тестовый адрес для проверки','description':'Тестовая заявка '+suffix,'visit_date':future}
    else:
        form={'type':'Торт','date':future,'count':2}
    check(user.request('/request-create.php',form,token=False)[0]==403,'Create rejects missing CSRF')
    response=user.request('/request-create.php',form)
    check(response[0]==302, 'Create request without headers-sent errors')
    text=user.page('/cabinet.php')
    check(('Тестовая заявка '+suffix if APP=='computer-repair-service' else 'Торт') in text,'Owner sees request')
    check(user.request('/admin.php')[0]==302,'Non-admin cannot open admin')
    admin.page('/login.php')
    check(admin.request('/login.php',{'email':admin_email,'password':admin_password})[0]==302,'Admin login')
    text=admin.page('/admin.php')
    request_id=re.search(r'name="request_id" value="(\d+)"',text).group(1)
    check(admin.request('/admin.php',{'request_id':request_id,'status':'confirmed'})[0]==302,'Admin changes status')
    check('bg-success' in user.page('/cabinet.php'),'Owner sees confirmed status')
    check(user.request('/logout.php')[0]==405,'GET logout rejected')
    check(user.request('/cabinet.php')[0]==200,'Rejected GET logout preserves session')
    check(user.request('/logout.php',{})[0]==302,'POST logout')
    check(user.request('/cabinet.php')[0]==302,'Cabinet protected after logout')

check(Client().request('/src/Database.php')[0]==404,'Source files outside document root')
check(Client().request('/config/local.php')[0]==404,'Local config outside document root')
print(f'{APP}: {COUNT} HTTP checks passed')
