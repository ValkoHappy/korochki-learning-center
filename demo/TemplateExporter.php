<?php
final class TemplateExporter
{
    public function __construct(private string $root, private array $config) {}

    public function build(): void
    {
        $output = $this->root . '/.pages';
        if (!is_dir($output)) mkdir($output, 0777, true);
        foreach (['css', 'js', 'img', 'media', 'assets'] as $directory) {
            $this->copyDirectory($this->root . '/public/' . $directory, $output . '/' . $directory);
        }
        $this->copyDirectory($this->root . '/demo/browser', $output . '/demo');
        copy($this->root . '/demo/demo.css', $output . '/demo/demo.css');
        if ($this->config['kind'] === 'course') {
            $pages = ['index.html'=>['public/index.php','home'], 'pages/login_page.html'=>['public/pages/login_page.php','login'],
                'pages/register_page.html'=>['public/pages/register_page.php','register'],
                'pages/dashboard.html'=>['public/pages/dashboard.php','cabinet'], 'pages/admin.html'=>['public/pages/dashboard.php','admin']];
        } elseif ($this->config['kind'] === 'doctor') {
            $pages = [];
            foreach (['index.html'=>'home','login.html'=>'login','register.html'=>'register','cabinet.html'=>'cabinet','admin.html'=>'admin','request-create.html'=>'request-create'] as $file=>$page) {
                $pages[$file] = ['templates/page.php', $page];
            }
        } else {
            $pages = [];
            foreach (['index'=>'home','login'=>'login','register'=>'register','cabinet'=>'cabinet','admin'=>'admin','request-create'=>'request-create'] as $file=>$page) {
                $pages[$file . '.html'] = ['public/' . $file . '.php', $page];
            }
        }
        foreach ($pages as $file => [$source, $page]) {
            $target = $output . '/' . $file;
            if (!is_dir(dirname($target))) mkdir(dirname($target), 0777, true);
            file_put_contents($target, $this->render($source, $page));
        }
        if ($this->config['kind'] === 'course') {
            $js = file_get_contents($output . '/js/dashboard.js');
            $imports = "import { DemoStore } from '../demo/store.mjs';\nimport { CourseDemoApi } from '../demo/course-api.mjs';\n";
            $imports .= 'const demoApi = new CourseDemoApi(new DemoStore(' . json_encode('portfolio-demo:' . $this->config['slug'] . ':v1') . "));\n";
            $js = str_replace(['await fetch(', "login_page.php"], ['await demoApi.request(', 'login_page.html'], $js);
            file_put_contents($output . '/js/dashboard.js', $imports . $js);
        }
        file_put_contents($output . '/.nojekyll', '');
        echo "Static demo built in .pages\n";
    }

    private function copyDirectory(string $source, string $destination): void
    {
        if (!is_dir($source)) return;
        if (!is_dir($destination)) mkdir($destination, 0777, true);
        foreach (new DirectoryIterator($source) as $file) {
            if ($file->isDot()) continue;
            $target = $destination . '/' . $file->getFilename();
            if ($file->isDir()) $this->copyDirectory($file->getPathname(), $target);
            elseif (!in_array($file->getExtension(), ['php', 'sql', 'log'], true)) copy($file->getPathname(), $target);
        }
    }

    private function render(string $source, string $page): string
    {
        $GLOBALS['demoAuthenticated'] = in_array($page, ['cabinet','admin','request-create'], true);
        $GLOBALS['demoAdmin'] = $page === 'admin';
        $_SESSION = ['csrf'=>'static-demo'];
        if ($GLOBALS['demoAuthenticated']) $_SESSION += ['user_id'=>1, 'role'=>$page === 'admin' ? 'admin':'user', 'fio'=>'Посетитель'];
        $role = $page === 'admin' ? 'admin' : 'user';
        $fio = 'Посетитель';
        $user = ['name'=>'Посетитель'];
        $errors = $flashes = [];
        $success = '';
        $pageTitle = $this->config['title'];
        $pageDescription = $pageTitle;
        $statusLabels = ['new'=>'Новое','confirmed'=>'Подтверждено','rejected'=>'Отказано'];
        $types = ['Торт','Пирожное','Зефир'];
        $request = ['id'=>1, 'status'=>'new', 'status_code'=>'new'];
        foreach (['full_name','address','description','visit_date','created_at','user_name','email','phone','type','date','count','patient_name','symptoms','status_name'] as $key) $request[$key] = '__' . $key . '__';
        $requests = $myRequests = $allRequests = [$request];
        $action = ['home'=>'home','login'=>'login','register'=>'register','cabinet'=>'cabinet','admin'=>'cabinet','request-create'=>'new_request'][$page];
        $template = file_get_contents($this->root . '/' . $source);
        if (str_starts_with($source, 'public/')) $template = preg_replace('/^\s*<\?php.*?\?>/s', '', $template, 1);
        $temporary = tempnam(sys_get_temp_dir(), 'demo-template-');
        file_put_contents($temporary, $template);
        ob_start();
        try { include $temporary; $html = ob_get_contents(); }
        finally { ob_end_clean(); unlink($temporary); }
        if ($this->config['kind'] === 'doctor') {
            foreach (['save_request'=>'request-create.html','new_request'=>'request-create.html','set_status'=>'admin.html','register'=>'register.html','login'=>'login.html','cabinet'=>'cabinet.html','logout'=>'logout.html'] as $actionName=>$file) {
                $html = str_replace('doctor.php?action=' . $actionName, $file, $html);
            }
            $html = str_replace('doctor.php', 'index.html', $html);
        }
        $html = str_replace('.php', '.html', $html);
        $prefix = str_starts_with($source, 'public/pages/') ? '../' : '';
        $html = preg_replace('/<body([^>]*)>/', '<body$1 data-demo-page="' . $page . '">', $html, 1);
        $html = str_replace('</head>', '<link rel="stylesheet" href="' . $prefix . 'demo/demo.css"></head>', $html);
        $html = preg_replace('/<script src="[^"\n]*auth\.js"><\/script>/', '', $html);
        $html = str_replace('<script src="../js/dashboard.js">', '<script type="module" src="../js/dashboard.js">', $html);
        $json = json_encode($this->config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
        $html = str_replace('</body>', '<script type="application/json" id="demo-config">' . $json . '</script><script type="module" src="' . $prefix . 'demo/app.mjs"></script></body>', $html);
        return $html;
    }
}
