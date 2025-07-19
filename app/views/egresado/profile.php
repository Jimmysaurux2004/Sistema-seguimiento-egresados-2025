<?php
$title = 'Mi Perfil';
$pageTitle = 'Mi Perfil de Egresado';
$pageDescription = 'Actualiza tu información personal y profesional';
ob_start();
?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3>Información Personal</h3>
            </div>
            <div class="card-body">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($errors) && !empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <!-- Read-only information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">DNI</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($egresado['dni']) ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($egresado['correo']) ?>" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Nombres</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($egresado['nombres']) ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Apellidos</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($egresado['apellidos']) ?>" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Carrera</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($egresado['carrera']) ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Año de Egreso</label>
                            <input type="text" class="form-control" value="<?= $egresado['anio_egreso'] ?>" readonly>
                        </div>
                    </div>
                </div>
                
                <!-- Editable information -->
                <form method="POST" action="/profile" data-validate>
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <h5 class="mb-3">Información Actualizable</h5>
                    
                    <div class="form-group">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="tel" 
                               id="telefono" 
                               name="telefono" 
                               class="form-control" 
                               placeholder="+51 999 888 777"
                               value="<?= htmlspecialchars($egresado['telefono'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="situacion_laboral" class="form-label">Situación Laboral Actual</label>
                        <select id="situacion_laboral" name="situacion_laboral" class="form-control form-select" required>
                            <option value="empleado" <?= $egresado['situacion_laboral_actual'] === 'empleado' ? 'selected' : '' ?>>
                                Empleado
                            </option>
                            <option value="desempleado" <?= $egresado['situacion_laboral_actual'] === 'desempleado' ? 'selected' : '' ?>>
                                Desempleado
                            </option>
                            <option value="estudiando" <?= $egresado['situacion_laboral_actual'] === 'estudiando' ? 'selected' : '' ?>>
                                Estudiando
                            </option>
                            <option value="emprendedor" <?= $egresado['situacion_laboral_actual'] === 'emprendedor' ? 'selected' : '' ?>>
                                Emprendedor
                            </option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="empresa_actual" class="form-label">Empresa Actual</label>
                        <input type="text" 
                               id="empresa_actual" 
                               name="empresa_actual" 
                               class="form-control" 
                               placeholder="Nombre de la empresa donde trabajas"
                               value="<?= htmlspecialchars($egresado['empresa_actual'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="cargo_actual" class="form-label">Cargo Actual</label>
                        <input type="text" 
                               id="cargo_actual" 
                               name="cargo_actual" 
                               class="form-control" 
                               placeholder="Tu cargo o posición actual"
                               value="<?= htmlspecialchars($egresado['cargo_actual'] ?? '') ?>">
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary" data-original-text="Actualizar Perfil">
                            Actualizar Perfil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Profile Summary -->
        <div class="card">
            <div class="card-header">
                <h5>Resumen del Perfil</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="profile-avatar">
                        <?= strtoupper(substr($egresado['nombres'], 0, 1) . substr($egresado['apellidos'], 0, 1)) ?>
                    </div>
                    <h6 class="mt-2"><?= htmlspecialchars($egresado['nombres'] . ' ' . $egresado['apellidos']) ?></h6>
                    <p class="text-muted"><?= htmlspecialchars($egresado['carrera']) ?></p>
                </div>
                
                <div class="profile-stats">
                    <div class="stat-item">
                        <strong>Estado:</strong>
                        <span class="badge badge-<?= $egresado['situacion_laboral_actual'] === 'empleado' ? 'success' : 'warning' ?>">
                            <?= ucfirst(str_replace('_', ' ', $egresado['situacion_laboral_actual'])) ?>
                        </span>
                    </div>
                    
                    <div class="stat-item">
                        <strong>Egresado desde:</strong><br>
                        <span class="text-muted"><?= $egresado['anio_egreso'] ?></span>
                    </div>
                    
                    <div class="stat-item">
                        <strong>Miembro desde:</strong><br>
                        <span class="text-muted">
                            <?= date('d/m/Y', strtotime($egresado['fecha_registro'])) ?>
                        </span>
                    </div>
                    
                    <?php if ($egresado['fecha_actualizacion'] !== $egresado['fecha_registro']): ?>
                        <div class="stat-item">
                            <strong>Última actualización:</strong><br>
                            <span class="text-muted">
                                <?= date('d/m/Y', strtotime($egresado['fecha_actualizacion'])) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card mt-3">
            <div class="card-header">
                <h6>Acciones Rápidas</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="/tutorias/create" class="btn btn-outline-primary btn-sm">
                        📚 Solicitar Tutoría
                    </a>
                    <a href="/encuestas" class="btn btn-outline-secondary btn-sm">
                        📊 Ver Encuestas
                    </a>
                    <a href="/eventos" class="btn btn-outline-success btn-sm">
                        🎉 Ver Eventos
                    </a>
                    <a href="/mensajes/compose" class="btn btn-outline-warning btn-sm">
                        ✉️ Enviar Mensaje
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-avatar {
    width: 80px;
    height: 80px;
    background-color: var(--primary-500);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: var(--font-weight-bold);
    margin: 0 auto;
}

.profile-stats .stat-item {
    padding: var(--spacing-3) 0;
    border-bottom: 1px solid var(--gray-200);
}

.profile-stats .stat-item:last-child {
    border-bottom: none;
}

.d-grid {
    display: grid;
}

.gap-2 {
    gap: var(--spacing-2);
}
</style>

<script>
// Show/hide employment fields based on status
document.getElementById('situacion_laboral').addEventListener('change', function() {
    const empresaField = document.getElementById('empresa_actual');
    const cargoField = document.getElementById('cargo_actual');
    
    if (this.value === 'empleado' || this.value === 'emprendedor') {
        empresaField.parentNode.style.display = 'block';
        cargoField.parentNode.style.display = 'block';
    } else {
        empresaField.parentNode.style.display = 'none';
        cargoField.parentNode.style.display = 'none';
        empresaField.value = '';
        cargoField.value = '';
    }
});

// Trigger on page load
document.getElementById('situacion_laboral').dispatchEvent(new Event('change'));
</script>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>