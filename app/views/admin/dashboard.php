<?php
$title = 'Panel de Administración';
$pageTitle = 'Panel de Administración';
$pageDescription = 'Gestión del sistema de egresados';
ob_start();
?>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="stats-card bg-primary">
            <h3><?= $stats['total_egresados'] ?></h3>
            <p>Total de Egresados</p>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="stats-card bg-warning">
            <h3><?= $stats['tutorias_pendientes'] ?></h3>
            <p>Tutorías Pendientes</p>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="stats-card bg-success">
            <h3><?= $stats['eventos_proximos'] ?></h3>
            <p>Eventos Activos</p>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Tutoring Requests -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Solicitudes de Tutoría Pendientes</h3>
                <a href="/admin/tutorias" class="btn btn-sm btn-outline-primary">Ver Todas</a>
            </div>
            <div class="card-body">
                <?php if (!empty($recent_tutorias)): ?>
                    <?php foreach (array_slice($recent_tutorias, 0, 5) as $tutoria): ?>
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div>
                                <strong><?= htmlspecialchars($tutoria['nombres'] . ' ' . $tutoria['apellidos']) ?></strong><br>
                                <small class="text-muted">
                                    <?= htmlspecialchars($tutoria['docente']) ?> - 
                                    <?= date('d/m/Y', strtotime($tutoria['fecha'])) ?> a las <?= date('H:i', strtotime($tutoria['hora'])) ?>
                                </small>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-success" 
                                        onclick="updateTutoriaStatus(<?= $tutoria['id'] ?>, 'confirmada')">
                                    Aprobar
                                </button>
                                <button class="btn btn-sm btn-danger" 
                                        onclick="updateTutoriaStatus(<?= $tutoria['id'] ?>, 'cancelada')">
                                    Rechazar
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted text-center">No hay solicitudes pendientes</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Employment Statistics -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h3>Estadísticas de Empleabilidad</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($employment_stats)): ?>
                    <?php foreach ($employment_stats as $stat): ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <strong><?= ucfirst(str_replace('_', ' ', $stat['situacion_laboral_actual'])) ?></strong>
                                <br><small class="text-muted"><?= $stat['cantidad'] ?> egresados</small>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-<?= 
                                    $stat['situacion_laboral_actual'] === 'empleado' ? 'success' : 
                                    ($stat['situacion_laboral_actual'] === 'desempleado' ? 'danger' : 'warning') 
                                ?>">
                                    <?= $stat['porcentaje'] ?>%
                                </span>
                            </div>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-<?= 
                                $stat['situacion_laboral_actual'] === 'empleado' ? 'success' : 
                                ($stat['situacion_laboral_actual'] === 'desempleado' ? 'danger' : 'warning') 
                            ?>" 
                                 style="width: <?= $stat['porcentaje'] ?>%"></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted text-center">No hay datos de empleabilidad</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Events -->
<?php if (!empty($events)): ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Próximos Eventos</h3>
        <a href="/admin/eventos" class="btn btn-sm btn-outline-primary">Gestionar Eventos</a>
    </div>
    <div class="card-body">
        <div class="row">
            <?php foreach ($events as $event): ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge badge-primary"><?= ucfirst($event['tipo']) ?></span>
                                <small class="text-muted"><?= date('d/m/Y', strtotime($event['fecha'])) ?></small>
                            </div>
                            <h6 class="card-title"><?= htmlspecialchars($event['nombre']) ?></h6>
                            <p class="card-text text-muted">
                                <?= htmlspecialchars(substr($event['descripcion'], 0, 80)) ?>...
                            </p>
                            <?php if ($event['lugar']): ?>
                                <small class="text-muted">📍 <?= htmlspecialchars($event['lugar']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3>Acciones Rápidas</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="/admin/egresados/create" class="btn btn-outline-primary w-100">
                            👤 Nuevo Egresado
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="/admin/eventos/create" class="btn btn-outline-success w-100">
                            📅 Nuevo Evento
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="/admin/encuestas/create" class="btn btn-outline-warning w-100">
                            📊 Nueva Encuesta
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="/mensajes/compose" class="btn btn-outline-secondary w-100">
                            ✉️ Enviar Mensaje
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stats-card {
    background: linear-gradient(135deg, var(--primary-600), var(--primary-700));
    color: white;
    border-radius: var(--border-radius-lg);
    padding: var(--spacing-6);
    text-align: center;
}

.stats-card.bg-warning {
    background: linear-gradient(135deg, var(--warning-500), var(--warning-600));
}

.stats-card.bg-success {
    background: linear-gradient(135deg, var(--success-500), var(--success-600));
}

.stats-card h3 {
    font-size: 2rem;
    font-weight: var(--font-weight-bold);
    margin-bottom: var(--spacing-2);
    color: white;
}

.stats-card p {
    font-size: 0.875rem;
    opacity: 0.9;
    margin-bottom: 0;
}

.progress {
    height: 8px;
    background-color: var(--gray-200);
    border-radius: var(--border-radius-lg);
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    transition: width var(--transition-normal);
}

.progress-bar.bg-success {
    background-color: var(--success-500);
}

.progress-bar.bg-warning {
    background-color: var(--warning-500);
}

.progress-bar.bg-danger {
    background-color: var(--error-500);
}
</style>

<script>
async function updateTutoriaStatus(tutoriaId, status) {
    const notas = prompt('Notas adicionales (opcional):');
    
    try {
        const response = await fetch('/tutorias/update-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `tutoria_id=${tutoriaId}&status=${status}&notas=${encodeURIComponent(notas || '')}`
        });
        
        if (response.ok) {
            location.reload();
        } else {
            alert('Error al actualizar el estado');
        }
    } catch (error) {
        alert('Error de conexión');
    }
}
</script>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>