<?php
$title = 'Dashboard';
$pageTitle = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin' ? 'Panel de Administración' : 'Mi Dashboard';
$pageDescription = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin' ? 'Gestión del sistema de egresados' : 'Bienvenido de nuevo';
ob_start();
?>

<?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
    <!-- Admin Dashboard -->
    
<?php else: ?>
    <!-- Graduate Dashboard -->
    <div class="row">
        <!-- Profile Summary -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3>Mi Perfil</h3>
                </div>
                <div class="card-body">
                    <?php if ($egresado): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <h5><?= htmlspecialchars($egresado['nombres'] . ' ' . $egresado['apellidos']) ?></h5>
                                <p class="text-muted"><?= htmlspecialchars($egresado['carrera']) ?></p>
                                <p><strong>Año de egreso:</strong> <?= $egresado['anio_egreso'] ?></p>
                                <p><strong>Situación laboral:</strong> 
                                    <span class="badge badge-<?= $egresado['situacion_laboral_actual'] === 'empleado' ? 'success' : 'warning' ?>">
                                        <?= ucfirst(str_replace('_', ' ', $egresado['situacion_laboral_actual'])) ?>
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <?php if ($egresado['empresa_actual']): ?>
                                    <p><strong>Empresa:</strong> <?= htmlspecialchars($egresado['empresa_actual']) ?></p>
                                <?php endif; ?>
                                <?php if ($egresado['cargo_actual']): ?>
                                    <p><strong>Cargo:</strong> <?= htmlspecialchars($egresado['cargo_actual']) ?></p>
                                <?php endif; ?>
                                <p><strong>Email:</strong> <?= htmlspecialchars($egresado['correo']) ?></p>
                                <?php if ($egresado['telefono']): ?>
                                    <p><strong>Teléfono:</strong> <?= htmlspecialchars($egresado['telefono']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="/profile" class="btn btn-primary">Actualizar Perfil</a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <p>Tu perfil de egresado no está completo. Por favor, contacta al administrador.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3>Acciones Rápidas</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-2">
                        <a href="/tutorias/create" class="btn btn-outline-primary">
                            📚 Solicitar Tutoría
                        </a>
                        <a href="/encuestas" class="btn btn-outline-secondary">
                            📊 Responder Encuestas
                        </a>
                        <a href="/mensajes/compose" class="btn btn-outline-success">
                            ✉️ Enviar Mensaje
                        </a>
                        <a href="/eventos" class="btn btn-outline-warning">
                            🎉 Ver Eventos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- My Tutoring Sessions -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Mis Tutorías</h3>
                    <a href="/tutorias" class="btn btn-sm btn-outline-primary">Ver Todas</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($tutorias)): ?>
                        <?php foreach (array_slice($tutorias, 0, 3) as $tutoria): ?>
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <div>
                                    <strong><?= htmlspecialchars($tutoria['docente']) ?></strong><br>
                                    <small class="text-muted">
                                        <?= date('d/m/Y', strtotime($tutoria['fecha'])) ?> a las <?= date('H:i', strtotime($tutoria['hora'])) ?>
                                    </small>
                                </div>
                                <span class="badge badge-<?= $tutoria['estado'] === 'confirmada' ? 'success' : ($tutoria['estado'] === 'pendiente' ? 'warning' : 'secondary') ?>">
                                    <?= ucfirst($tutoria['estado']) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center">No tienes tutorías programadas</p>
                        <div class="text-center">
                            <a href="/tutorias/create" class="btn btn-primary">Solicitar Primera Tutoría</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Recent Notifications -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3>Notificaciones Recientes</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($notifications)): ?>
                        <?php foreach ($notifications as $notification): ?>
                            <div class="d-flex justify-content-between align-items-start border-bottom py-2 <?= !$notification['leida'] ? 'bg-primary-50' : '' ?>">
                                <div>
                                    <strong><?= htmlspecialchars($notification['titulo']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($notification['mensaje']) ?></small><br>
                                    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($notification['fecha_creacion'])) ?></small>
                                </div>
                                <?php if (!$notification['leida']): ?>
                                    <span class="badge badge-primary">Nuevo</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center">No hay notificaciones recientes</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Upcoming Events -->
<?php if (!empty($events)): ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Próximos Eventos</h3>
        <a href="/eventos" class="btn btn-sm btn-outline-primary">Ver Todos</a>
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

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>