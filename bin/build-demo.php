<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$root = dirname(__DIR__);
require $root . '/demo/preview-context.php';
require $root . '/demo/TemplateExporter.php';
$config = json_decode(file_get_contents($root . '/demo/config.json'), true, 512, JSON_THROW_ON_ERROR);
(new TemplateExporter($root, $config))->build();
