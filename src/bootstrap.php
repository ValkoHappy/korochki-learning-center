<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Security.php';
Security::startSession();
Security::checkRequest();
