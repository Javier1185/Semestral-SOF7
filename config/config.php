<?php

$projectFolder = basename(dirname(__DIR__));
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$uriPath = parse_url($requestUri, PHP_URL_PATH) ?: '/';

$baseUrl = '';

if ($uriPath === '/' . $projectFolder || strpos($uriPath, '/' . $projectFolder . '/') === 0) {
    $baseUrl = '/' . $projectFolder;
}

define('BASE_URL', $baseUrl);