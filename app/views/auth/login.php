<?php
$title = 'Iniciar Sesión';
ob_start();
?>

<div class="container" style="max-width: 400px; margin-top: 5rem;">
    <div class="card">
        <div class="card-header text-center">
            <h2><?= APP_NAME ?></h2>
            <p class="text-muted">Iniciar Sesión</p>
        </div>
        <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="/login" data-validate>
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control" 
                           placeholder="tu@email.com"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control" 
                           placeholder="Tu contraseña"
                           required>
                </div>
                
                <button type="submit" class="btn btn-primary w-100" data-original-text="Iniciar Sesión">
                    Iniciar Sesión
                </button>
            </form>
        </div>
        <div class="card-footer text-center">
            <p class="mb-0">
                ¿No tienes cuenta? 
                <a href="/register" class="text-primary">Regístrate aquí</a>
            </p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>