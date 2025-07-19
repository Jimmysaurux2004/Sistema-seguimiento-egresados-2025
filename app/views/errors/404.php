<?php
$title = 'Página no encontrada';
ob_start();
?>

<div class="container text-center" style="margin-top: 5rem;">
    <div class="card">
        <div class="card-body p-6">
            <h1 class="display-1 text-primary">404</h1>
            <h2 class="mb-4">Página no encontrada</h2>
            <p class="text-muted mb-4">
                Lo sentimos, la página que estás buscando no existe o ha sido movida.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="/" class="btn btn-primary">Ir al Inicio</a>
                <a href="javascript:history.back()" class="btn btn-outline-secondary">Volver Atrás</a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>