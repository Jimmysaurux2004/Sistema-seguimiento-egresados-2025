<?php
// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

// If authenticated, redirect to dashboard
header('Location: /dashboard');
exit;
?>

<?php
$title = 'Bienvenido';
$pageTitle = 'Sistema de Gestión de Egresados';
$pageDescription = 'Accede a tu cuenta para continuar';
ob_start();
?>

<!-- Welcome Section for Public Access -->
<div class="container text-center" style="margin-top: 5rem;">
    <div class="card">
        <div class="card-body">
            <h1 class="mb-4">Sistema de Gestión de Egresados</h1>
            <p class="text-muted mb-4">
                Para acceder al sistema, por favor inicia sesión con tu cuenta.
            </p>
            <div class="d-flex justify-content-center" style="gap: 1rem;">
                <a href="/login" class="btn btn-primary btn-lg">Iniciar Sesión</a>
                <a href="/register" class="btn btn-outline-primary btn-lg">Registrarse</a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>