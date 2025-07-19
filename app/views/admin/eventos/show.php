<?php
$title = 'Detalles del Evento';
$pageTitle = 'Detalles del Evento';
$pageDescription = 'Información completa del evento';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Detalles del Evento</h2>
        <p class="text-muted">Información completa del evento</p>
    </div>
    <div>
        <a href="/admin/eventos" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver a la Lista
        </a>
        <a href="/admin/eventos/edit/<?= $event['id'] ?>" class="btn btn-primary">
            <i class="fas fa-edit"></i> Editar
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Event Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Información del Evento</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Nombre</label>
                            <p class="form-control-static"><?= htmlspecialchars($event['nombre']) ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Tipo</label>
                            <p class="form-control-static">
                                <span class="badge badge-<?=
                                                            $event['tipo'] === 'capacitacion' ? 'primary' : ($event['tipo'] === 'evento' ? 'success' : ($event['tipo'] === 'charla' ? 'warning' : 'info'))
                                                            ?>">
                                    <?= ucfirst($event['tipo']) ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Descripción</label>
                    <p class="form-control-static"><?= nl2br(htmlspecialchars($event['descripcion'])) ?></p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Fecha</label>
                            <p class="form-control-static"><?= date('d/m/Y', strtotime($event['fecha'])) ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Hora</label>
                            <p class="form-control-static">
                                <?= !empty($event['hora']) ? $event['hora'] : 'No especificada' ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">Lugar</label>
                    <p class="form-control-static">
                        <?= !empty($event['lugar']) ? htmlspecialchars($event['lugar']) : 'No especificado' ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Registration Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Información de Inscripciones</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Capacidad Máxima</label>
                            <p class="form-control-static">
                                <?= $event['capacidad_maxima'] > 0 ? $event['capacidad_maxima'] : 'Sin límite' ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Inscritos</label>
                            <p class="form-control-static"><?= $event['inscritos'] ?? 0 ?></p>
                        </div>
                    </div>
                </div>

                <?php if ($event['capacidad_maxima'] > 0): ?>
                    <div class="form-group">
                        <label class="form-label fw-bold">Progreso de Inscripciones</label>
                        <div class="d-flex align-items-center">
                            <span class="me-3"><?= $event['inscritos'] ?? 0 ?>/<?= $event['capacidad_maxima'] ?></span>
                            <div class="progress flex-grow-1" style="height: 10px;">
                                <?php $porcentaje = ($event['inscritos'] ?? 0) / $event['capacidad_maxima'] * 100; ?>
                                <div class="progress-bar bg-<?= $porcentaje >= 90 ? 'danger' : ($porcentaje >= 70 ? 'warning' : 'success') ?>"
                                    style="width: <?= $porcentaje ?>%"></div>
                            </div>
                            <span class="ms-3"><?= round($porcentaje, 1) ?>%</span>
                        </div>
                    </div>
                <?php endif; ?>
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
                            <label class="form-label fw-bold">Estado</label>
                            <p class="form-control-static">
                                <?php
                                $today = date('Y-m-d');
                                $eventDate = $event['fecha'];
                                $isPast = $eventDate < $today;
                                $isToday = $eventDate == $today;
                                ?>
                                <?php if (!$event['activo']): ?>
                                    <span class="badge badge-danger">Cancelado</span>
                                <?php elseif ($isPast): ?>
                                    <span class="badge badge-secondary">Finalizado</span>
                                <?php elseif ($isToday): ?>
                                    <span class="badge badge-success">Hoy</span>
                                <?php else: ?>
                                    <span class="badge badge-primary">Activo</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Fecha de Creación</label>
                            <p class="form-control-static">
                                <?= isset($event['fecha_creacion']) ? date('d/m/Y H:i', strtotime($event['fecha_creacion'])) : 'No disponible' ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Event Summary -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Resumen del Evento</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="event-icon-large">
                        <?php
                        $icon = $event['tipo'] === 'capacitacion' ? '📚' : ($event['tipo'] === 'evento' ? '🎉' : ($event['tipo'] === 'charla' ? '🎤' : '📅'));
                        ?>
                        <span style="font-size: 3rem;"><?= $icon ?></span>
                    </div>
                    <h5 class="mt-2"><?= htmlspecialchars($event['nombre']) ?></h5>
                    <p class="text-muted"><?= ucfirst($event['tipo']) ?></p>
                </div>

                <div class="event-stats">
                    <div class="stat-item">
                        <strong>Fecha:</strong><br>
                        <span class="text-muted"><?= date('d/m/Y', strtotime($event['fecha'])) ?></span>
                    </div>

                    <div class="stat-item">
                        <strong>Inscritos:</strong><br>
                        <span class="text-muted"><?= $event['inscritos'] ?? 0 ?></span>
                    </div>

                    <?php if ($event['capacidad_maxima'] > 0): ?>
                        <div class="stat-item">
                            <strong>Disponibilidad:</strong><br>
                            <span class="text-muted"><?= $event['capacidad_maxima'] - ($event['inscritos'] ?? 0) ?> cupos</span>
                        </div>
                    <?php endif; ?>
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
                    <a href="/admin/eventos/edit/<?= $event['id'] ?>" class="btn btn-outline-primary">
                        <i class="fas fa-edit"></i> Editar Evento
                    </a>
                    <a href="/admin/eventos/<?= $event['id'] ?>/inscritos" class="btn btn-outline-info">
                        <i class="fas fa-users"></i> Ver Inscritos
                    </a>
                    <button type="button" class="btn btn-outline-warning" onclick="toggleStatus(<?= $event['id'] ?>)">
                        <i class="fas fa-toggle-on"></i>
                        <?= $event['activo'] ? 'Desactivar' : 'Activar' ?>
                    </button>
                    <button type="button" class="btn btn-outline-danger" onclick="confirmDelete(<?= $event['id'] ?>, '<?= htmlspecialchars($event['nombre']) ?>')">
                        <i class="fas fa-trash"></i> Eliminar Evento
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

    .event-icon-large {
        width: 80px;
        height: 80px;
        background-color: var(--primary-100);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    .event-stats {
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

    .progress {
        background-color: var(--gray-200);
        border-radius: var(--border-radius-lg);
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        transition: width var(--transition-normal);
    }
</style>

<script>
    function toggleStatus(eventId) {
        if (confirm('¿Estás seguro de que quieres cambiar el estado del evento?')) {
            fetch('/admin/eventos/toggle-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify({
                        evento_id: eventId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error al cambiar el estado del evento');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error de conexión');
                });
        }
    }

    function confirmDelete(eventId, eventName) {
        if (confirm(`¿Estás seguro de que quieres eliminar el evento "${eventName}"? Esta acción no se puede deshacer.`)) {
            fetch(`/admin/eventos/${eventId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = '/admin/eventos';
                    } else {
                        alert('Error al eliminar el evento');
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