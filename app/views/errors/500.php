<?php
$title = 'Error interno';
ob_start();
?>

<div class="container text-center" style="margin-top: 5rem;">
    <div class="card">
        <div class="card-body p-6">
            <h1 class="display-1 text-danger">500</h1>
            <h2 class="mb-4">Error Interno del Servidor</h2>
            <p class="text-muted mb-4">
                Ha ocurrido un error interno. Por favor, inténtalo de nuevo más tarde.
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