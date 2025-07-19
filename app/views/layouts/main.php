<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - ' . APP_NAME : APP_NAME ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/style.css?v=1.0">
    
    <!-- Simple favicon -->
    <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64,AAABAAEAEBAQAAEABAAoAQAAFgAAACgAAAAQAAAAIAAAAAEABAAAAAAAgAAAAAAAAAAAAAAAEAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA">
    
    <!-- Meta tags -->
    <meta name="description" content="Sistema de gestión de egresados universitarios">
    <meta name="author" content="Universidad">
    
    <!-- Security headers -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">
</head>
<body>
    <!-- Navigation -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <nav class="navbar">
            <div class="container-fluid d-flex justify-content-between align-items-center p-4">
                <div class="d-flex align-items-center">
                    <!-- Mobile sidebar toggle -->
                    <button class="btn btn-outline-primary d-md-none mr-3" data-sidebar-toggle>
                        ☰
                    </button>
                    
                    <a href="/dashboard" class="navbar-brand">
                        <?= APP_NAME ?>
                    </a>
                </div>
                
                <ul class="navbar-nav d-flex flex-row align-items-center">
                    <!-- Notifications -->
                    <li class="nav-item position-relative mr-4">
                        <a href="#" class="nav-link position-relative" data-modal-target="notifications-modal">
                            🔔
                            <span class="notification-badge" data-notification-count style="display: none;">0</span>
                        </a>
                    </li>
                    
                    <!-- Messages -->
                    <li class="nav-item position-relative mr-4">
                        <a href="/mensajes" class="nav-link position-relative">
                            ✉️
                            <span class="notification-badge" data-message-count style="display: none;">0</span>
                        </a>
                    </li>
                    
                    <!-- User menu -->
                    <li class="nav-item">
                        <div class="d-flex align-items-center">
                            <span class="mr-3">Hola, <?= htmlspecialchars($_SESSION['user_email']) ?></span>
                            <a href="/logout" class="btn btn-outline-primary btn-sm">Cerrar Sesión</a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    <?php endif; ?>
    
    <!-- Main content wrapper -->
    <div class="d-flex">
        <!-- Sidebar -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <aside class="sidebar">
                <div class="sidebar-nav">
                    <ul>
                        <li>
                            <a href="/dashboard" class="<?= ($_SERVER['REQUEST_URI'] == '/dashboard') ? 'active' : '' ?>">
                                🏠 Dashboard
                            </a>
                        </li>
                        
                        <?php if ($_SESSION['user_role'] === 'egresado'): ?>
                            <li>
                                <a href="/profile" class="<?= ($_SERVER['REQUEST_URI'] == '/profile') ? 'active' : '' ?>">
                                    👤 Mi Perfil
                                </a>
                            </li>
                            <li>
                                <a href="/tutorias" class="<?= (strpos($_SERVER['REQUEST_URI'], '/tutorias') === 0) ? 'active' : '' ?>">
                                    📚 Tutorías
                                </a>
                            </li>
                            <li>
                                <a href="/encuestas" class="<?= (strpos($_SERVER['REQUEST_URI'], '/encuestas') === 0) ? 'active' : '' ?>">
                                    📊 Encuestas
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php if ($_SESSION['user_role'] === 'admin'): ?>
                            <li>
                                <a href="/admin/egresados" class="<?= (strpos($_SERVER['REQUEST_URI'], '/admin/egresados') === 0) ? 'active' : '' ?>">
                                    👥 Egresados
                                </a>
                            </li>
                            <li>
                                <a href="/admin/eventos" class="<?= (strpos($_SERVER['REQUEST_URI'], '/admin/eventos') === 0) ? 'active' : '' ?>">
                                    📅 Eventos
                                </a>
                            </li>
                            <li>
                                <a href="/admin/encuestas" class="<?= (strpos($_SERVER['REQUEST_URI'], '/admin/encuestas') === 0) ? 'active' : '' ?>">
                                    📋 Encuestas
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <li>
                            <a href="/mensajes" class="<?= (strpos($_SERVER['REQUEST_URI'], '/mensajes') === 0) ? 'active' : '' ?>">
                                💬 Mensajes
                            </a>
                        </li>
                        <li>
                            <a href="/eventos" class="<?= (strpos($_SERVER['REQUEST_URI'], '/eventos') === 0) ? 'active' : '' ?>">
                                🎉 Eventos
                            </a>
                        </li>
                    </ul>
                </div>
            </aside>
        <?php endif; ?>
        
        <!-- Main content -->
        <main class="<?= isset($_SESSION['user_id']) ? 'main-content' : '' ?> flex-grow-1">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="content-header">
                    <div class="container-fluid">
                        <h1><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Dashboard' ?></h1>
                        <?php if (isset($pageDescription)): ?>
                            <p class="text-muted"><?= htmlspecialchars($pageDescription) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="<?= isset($_SESSION['user_id']) ? 'content-body' : '' ?>">
                <div class="<?= isset($_SESSION['user_id']) ? 'container-fluid' : 'container' ?>">
                    <!-- Flash messages -->
                    <?php if (isset($_SESSION['flash_message'])): ?>
                        <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible">
                            <?= htmlspecialchars($_SESSION['flash_message']) ?>
                            <button type="button" class="btn-close" data-dismiss="alert">×</button>
                        </div>
                        <?php 
                        unset($_SESSION['flash_message']); 
                        unset($_SESSION['flash_type']); 
                        ?>
                    <?php endif; ?>
                    
                    <!-- Page content -->
                    <?= $content ?? '' ?>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Notifications Modal -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <div id="notifications-modal" class="modal">
            <div class="modal-dialog">
                <div class="modal-header">
                    <h3 class="modal-title">Notificaciones</h3>
                    <button type="button" class="modal-close" data-modal-close>×</button>
                </div>
                <div class="modal-body">
                    <div data-notification-list>
                        <div class="p-4 text-center text-muted">Cargando notificaciones...</div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- JavaScript -->
    <script src="/assets/js/app.js?v=1.0"></script>
    
    <!-- Page-specific JavaScript -->
    <?php if (isset($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Inline JavaScript -->
    <?php if (isset($inlineScript)): ?>
        <script><?= $inlineScript ?></script>
    <?php endif; ?>
</body>
</html>