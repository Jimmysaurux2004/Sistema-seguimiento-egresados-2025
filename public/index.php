<?php

/**
 * Application entry point - Graduate Management System
 * Redirects to login if not authenticated, otherwise initializes routing
 */

// Include configuration
require_once '../config/config.php';

// Check if user is not logged in and redirect to login
if (!isset($_SESSION['user_id']) && $_SERVER['REQUEST_URI'] !== '/login' && $_SERVER['REQUEST_URI'] !== '/register') {
    // If accessing root, redirect to login
    if ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/index.php') {
        header('Location: /login');
        exit;
    }
}

// Include core classes
require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Router.php';
require_once APP_PATH . '/core/Controller.php';

// Include all controllers
require_once APP_PATH . '/controllers/AuthController.php';
require_once APP_PATH . '/controllers/HomeController.php';
require_once APP_PATH . '/controllers/EgresadoController.php';
require_once APP_PATH . '/controllers/TutoriaController.php';
require_once APP_PATH . '/controllers/EncuestaController.php';
require_once APP_PATH . '/controllers/MensajeController.php';
require_once APP_PATH . '/controllers/EventoController.php';

// Initialize router
$router = new Router();

// Public routes
$router->get('/', 'HomeController@index');
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');

// Protected routes
$router->get('/dashboard', 'HomeController@dashboard');

// Graduate routes
$router->get('/profile', 'EgresadoController@profile');
$router->post('/profile', 'EgresadoController@updateProfile');

// Tutoring routes
$router->get('/tutorias', 'TutoriaController@index');
$router->get('/tutorias/create', 'TutoriaController@create');
$router->post('/tutorias', 'TutoriaController@store');
$router->get('/tutorias/calendar', 'TutoriaController@calendar');
$router->post('/tutorias/update-status', 'TutoriaController@updateStatus');

// Survey routes
$router->get('/encuestas', 'EncuestaController@index');
$router->get('/encuestas/{id}', 'EncuestaController@show');
$router->post('/encuestas/respond', 'EncuestaController@respond');

// Message routes
$router->get('/mensajes', 'MensajeController@inbox');
$router->get('/mensajes/sent', 'MensajeController@sent');
$router->get('/mensajes/compose', 'MensajeController@compose');
$router->post('/mensajes/send', 'MensajeController@send');
$router->get('/mensajes/conversation/{userId}', 'MensajeController@conversation');
$router->post('/mensajes/reply', 'MensajeController@reply');
$router->post('/mensajes/mark-read', 'MensajeController@markAsRead');

// Event routes
$router->get('/eventos', 'EventoController@index');
$router->get('/eventos/{id}', 'EventoController@show');

// Admin routes
$router->get('/admin/dashboard', 'HomeController@dashboard');
$router->get('/admin/egresados', 'EgresadoController@list');
$router->get('/admin/egresados/{dni}', 'EgresadoController@show');

$router->get('/admin/eventos', 'EventoController@list');
$router->get('/admin/eventos/create', 'EventoController@create');
$router->post('/admin/eventos', 'EventoController@store');
$router->get('/admin/eventos/{id}', 'EventoController@show');
$router->get('/admin/eventos/edit/{id}', 'EventoController@edit');
$router->post('/admin/eventos/update/{id}', 'EventoController@update');
$router->post('/admin/eventos/toggle-status', 'EventoController@toggleStatus');
$router->delete('/admin/eventos/{id}', 'EventoController@delete');

$router->get('/admin/encuestas', 'EncuestaController@list');
$router->get('/admin/encuestas/create', 'EncuestaController@create');
$router->post('/admin/encuestas', 'EncuestaController@store');
$router->get('/admin/encuestas/statistics/{id}', 'EncuestaController@statistics');
$router->post('/admin/encuestas/toggle-status', 'EncuestaController@toggleStatus');

// API routes
$router->get('/api/notifications', 'HomeController@getNotifications');
$router->post('/api/notifications/mark-read', 'HomeController@markNotificationRead');
$router->get('/api/messages/unread-count', 'MensajeController@getUnreadCount');

// Dispatch the request
try {
    $router->dispatch();
} catch (Exception $e) {
    if (APP_ENV === 'development') {
        echo "<h1>Error</h1>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    } else {
        http_response_code(500);
        require VIEW_PATH . '/errors/500.php';
    }
}
