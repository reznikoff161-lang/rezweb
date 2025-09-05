<?php
// index.php

// Запускаем сессию только если она еще не запущена
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Определяем базовые константы
define('BASE_DIR', __DIR__);
define('BASE_URL', '/crm');

// Явно подключаем основные классы ядра
require_once BASE_DIR . '/core/Database.php';
require_once BASE_DIR . '/core/Template.php';

// Получаем запрошенный URI
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Убираем базовый URL из запроса
$clean_path = str_replace(BASE_URL, '', $path);
$clean_path = ltrim($clean_path, '/');

// Убираем GET-параметры
if (($pos = strpos($clean_path, '?')) !== false) {
    $clean_path = substr($clean_path, 0, $pos);
}

// Обработка статических файлов
if (preg_match('#\.(css|js|png|jpg|jpeg|gif|ico|svg)$#i', $clean_path)) {
    $file_path = BASE_DIR . '/' . $clean_path;
    if (file_exists($file_path)) {
        $mime_types = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'ico' => 'image/x-icon',
            'svg' => 'image/svg+xml'
        ];
        
        $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        if (array_key_exists($extension, $mime_types)) {
            header('Content-Type: ' . $mime_types[$extension]);
            readfile($file_path);
            exit;
        }
    }
    http_response_code(404);
    exit;
}

// Задаем маршруты
$routes = [
    '' => 'HomeController',
    'login' => 'LoginController',
    'logout' => 'LogoutController',
// ... маршрут проектов
    'projects' => 'ProjectsController',
    'projects/create' => 'ProjectsCreateController',
    'projects/edit' => 'ProjectsEditController',
    'projects/delete' => 'ProjectsDeleteController',
    'projects/view' => 'ProjectsViewController',
// ... маршрут клиентов
    'clients' => 'ClientsController',
    'clients/create' => 'ClientsCreateController',
    'clients/view' => 'ClientsViewController',
    'clients/edit' => 'ClientsEditController',
    'clients/delete' => 'ClientsDeleteController',
    'clients/create' => 'ClientsCreateController',
// ... маршрут финансов
    'finance' => 'FinanceController',
    'finance/create' => 'FinanceCreateController',
    'finance/edit' => 'FinanceEditController',
    'finance/delete' => 'FinanceDeleteController',
    'finance/view' => 'FinanceViewController',
    'finance/export' => 'FinanceExportController',
// ... маршрут статистики
    'statistics' => 'StatisticsController',



    'services' => 'ServicesController'

  
];
// Находим контроллер
$controller_name = $routes[$clean_path] ?? null;

if ($controller_name === null) {
    http_response_code(404);
    echo "Страница не найдена: $clean_path";
    exit;
}

// Подключаем контроллер
$controller_file = BASE_DIR . '/controllers/' . $controller_name . '.php';
if (file_exists($controller_file)) {
    require_once $controller_file;
} else {
    http_response_code(404);
    echo "Контроллер не найден: $controller_file";
    exit;
}
?>