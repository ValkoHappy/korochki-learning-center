<?php
require_once dirname(__DIR__, 2) . '/src/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
session_destroy();
header('Location: ../index.php');
?>
