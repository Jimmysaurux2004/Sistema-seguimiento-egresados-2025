<?php
$title = 'Acceso denegado';
ob_start();
?>

<div class="container text-center" style="margin-top: 5rem;">
    <div class="card">
        <div class="card-body p-6">
            <h1 class="display-1 text-warning">403</h1>
            <h2 class="mb-4">Acceso Denegado</h2>
            <p class="text-muted mb-4">
                No tienes permisos para acceder a esta sección del sistema.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="/dashboard" class="btn btn-primary">Ir al Dashboard</a>
                <a href="javascript:history.back()" class="btn btn-outline-secondary">Volver Atrás</a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>