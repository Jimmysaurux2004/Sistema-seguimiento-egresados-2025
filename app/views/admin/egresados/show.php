<?php
$title = 'Detalles del Egresado';
$pageTitle = 'Detalles del Egresado';
$pageDescription = 'Información completa del egresado';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Detalles del Egresado</h2>
        <p class="text-muted">Información completa del egresado</p>
    </div>
    <div>
        <a href="/admin/egresados" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver a la Lista
        </a>
        <a href="/admin/egresados/<?= $egresado['dni'] ?>/edit" class="btn btn-primary">
            <i class="fas fa-edit"></i> Editar
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Personal Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Información Personal</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">DNI</label>
                            <p class="form-control-static"><?= htmlspecialchars($egresado['dni']) ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Email</label>
                            <p class="form-control-static"><?= htmlspecialchars($egresado['correo']) ?></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Nombres</label>
                            <p class="form-control-static"><?= htmlspecialchars($egresado['nombres']) ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Apellidos</label>
                            <p class="form-control-static"><?= htmlspecialchars($egresado['apellidos']) ?></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Teléfono</label>
                            <p class="form-control-static">
                                <?= !empty($egresado['telefono']) ? htmlspecialchars($egresado['telefono']) : '<span class="text-muted">No especificado</span>' ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Carrera</label>
                            <p class="form-control-static"><?= htmlspecialchars($egresado['carrera']) ?></p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Año de Egreso</label>
                    <p class="form-control-static"><?= $egresado['anio_egreso'] ?></p>
                </div>
            </div>
        </div>

        <!-- Employment Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Información Laboral</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Situación Laboral</label>
                            <p class="form-control-static">
                                <?php if (!empty($egresado['situacion_laboral_actual'])): ?>
                                    <span class="badge badge-<?=
                                                                $egresado['situacion_laboral_actual'] === 'empleado' ? 'success' : ($egresado['situacion_laboral_actual'] === 'desempleado' ? 'danger' : ($egresado['situacion_laboral_actual'] === 'estudiando' ? 'info' : 'warning'))
                                                                ?>">
                                        <?= ucfirst(str_replace('_', ' ', $egresado['situacion_laboral_actual'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">No especificado</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Empresa Actual</label>
                            <p class="form-control-static">
                                <?= !empty($egresado['empresa_actual']) ? htmlspecialchars($egresado['empresa_actual']) : '<span class="text-muted">No especificado</span>' ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Cargo Actual</label>
                    <p class="form-control-static">
                        <?= !empty($egresado['cargo_actual']) ? htmlspecialchars($egresado['cargo_actual']) : '<span class="text-muted">No especificado</span>' ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- System Information -->
        <div class="card">
            <div class="card-header">
                <h3>Información del Sistema</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Fecha de Registro</label>
                            <p class="form-control-static"><?= date('d/m/Y H:i', strtotime($egresado['fecha_registro'])) ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Última Actualización</label>
                            <p class="form-control-static">
                                <?= $egresado['fecha_actualizacion'] ? date('d/m/Y H:i', strtotime($egresado['fecha_actualizacion'])) : 'No actualizado' ?>
                            </p>
                        </div>
                    </div>
                </div>

                <?php if (isset($egresado['activo'])): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">Estado de la Cuenta</label>
                                <p class="form-control-static">
                                    <span class="badge badge-<?= $egresado['activo'] ? 'success' : 'danger' ?>">
                                        <?= $egresado['activo'] ? 'Activa' : 'Inactiva' ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">Último Acceso</label>
                                <p class="form-control-static">
                                    <?= $egresado['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($egresado['ultimo_acceso'])) : 'Nunca' ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Profile Summary -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Resumen del Perfil</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="profile-avatar-large">
                        <?= strtoupper(substr($egresado['nombres'], 0, 1) . substr($egresado['apellidos'], 0, 1)) ?>
                    </div>
                    <h5 class="mt-2"><?= htmlspecialchars($egresado['nombres'] . ' ' . $egresado['apellidos']) ?></h5>
                    <p class="text-muted"><?= htmlspecialchars($egresado['carrera']) ?></p>
                </div>

                <div class="profile-stats">
                    <div class="stat-item">
                        <strong>DNI:</strong><br>
                        <span class="text-muted"><?= htmlspecialchars($egresado['dni']) ?></span>
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
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h6>Acciones Rápidas</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="/admin/egresados/<?= $egresado['dni'] ?>/edit" class="btn btn-outline-primary">
                        <i class="fas fa-edit"></i> Editar Información
                    </a>
                    <button type="button" class="btn btn-outline-warning" onclick="resetPassword('<?= $egresado['dni'] ?>')">
                        <i class="fas fa-key"></i> Resetear Contraseña
                    </button>
                    <button type="button" class="btn btn-outline-danger" onclick="confirmDelete('<?= $egresado['dni'] ?>', '<?= htmlspecialchars($egresado['nombres'] . ' ' . $egresado['apellidos']) ?>')">
                        <i class="fas fa-trash"></i> Eliminar Egresado
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control-static {
        padding: 0.75rem;
        background-color: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: var(--border-radius-md);
        margin-bottom: 0;
        min-height: 48px;
        display: flex;
        align-items: center;
    }

    .profile-avatar-large {
        width: 80px;
        height: 80px;
        background-color: var(--primary-500);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: var(--font-weight-bold);
        font-size: 1.5rem;
        margin: 0 auto;
    }

    .profile-stats {
        border-top: 1px solid var(--gray-200);
        padding-top: var(--spacing-4);
    }

    .stat-item {
        margin-bottom: var(--spacing-3);
    }

    .stat-item:last-child {
        margin-bottom: 0;
    }

    .stat-item strong {
        color: var(--gray-700);
        font-size: 0.875rem;
    }

    .stat-item span {
        font-size: 0.875rem;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
    }
</style>

<script>
    function resetPassword(dni) {
        if (confirm('¿Estás seguro de que quieres resetear la contraseña? La nueva contraseña será el DNI del egresado.')) {
            fetch(`/admin/egresados/${dni}/reset-password`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                })
                .then(response => {
                    if (response.ok) {
                        alert('Contraseña reseteada correctamente. La nueva contraseña es el DNI del egresado.');
                    } else {
                        alert('Error al resetear la contraseña');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error de conexión');
                });
        }
    }

    function confirmDelete(dni, egresadoName) {
        if (confirm(`¿Estás seguro de que quieres eliminar al egresado "${egresadoName}"? Esta acción no se puede deshacer.`)) {
            fetch(`/admin/egresados/${dni}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                })
                .then(response => {
                    if (response.ok) {
                        window.location.href = '/admin/egresados';
                    } else {
                        alert('Error al eliminar el egresado');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error de conexión');
                });
        }
    }
</script>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>