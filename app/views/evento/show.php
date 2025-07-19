<?php
$title = htmlspecialchars($event['nombre']);
$pageTitle = htmlspecialchars($event['nombre']);
$pageDescription = 'Detalles del evento';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="/eventos" class="btn btn-outline-secondary">← Volver a Eventos</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <span class="badge badge-<?= 
                        $event['tipo'] === 'capacitacion' ? 'primary' : 
                        ($event['tipo'] === 'charla' ? 'success' : 
                        ($event['tipo'] === 'reunion' ? 'warning' : 'secondary')) 
                    ?> badge-lg">
                        <?= ucfirst($event['tipo']) ?>
                    </span>
                    
                    <?php 
                    $eventDate = strtotime($event['fecha']);
                    $today = strtotime(date('Y-m-d'));
                    $daysUntil = ceil(($eventDate - $today) / (60 * 60 * 24));
                    ?>
                    
                    <?php if ($daysUntil < 0): ?>
                        <span class="badge badge-secondary">Evento Finalizado</span>
                    <?php elseif ($daysUntil === 0): ?>
                        <span class="badge badge-success">¡Hoy!</span>
                    <?php elseif ($daysUntil === 1): ?>
                        <span class="badge badge-warning">Mañana</span>
                    <?php elseif ($daysUntil <= 7): ?>
                        <span class="badge badge-primary">En <?= $daysUntil ?> días</span>
                    <?php endif; ?>
                </div>
                
                <h1 class="mb-4"><?= htmlspecialchars($event['nombre']) ?></h1>
                
                <?php if ($event['descripcion']): ?>
                    <div class="mb-4">
                        <h5>Descripción</h5>
                        <p class="text-muted"><?= nl2br(htmlspecialchars($event['descripcion'])) ?></p>
                    </div>
                <?php endif; ?>
                
                <div class="event-info">
                    <h5>Información del Evento</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <div class="d-flex align-items-center">
                                    <span class="info-icon mr-3">📅</span>
                                    <div>
                                        <strong>Fecha</strong><br>
                                        <span class="text-muted">
                                            <?= date('l, d \d\e F \d\e Y', strtotime($event['fecha'])) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($event['hora']): ?>
                                <div class="info-item mb-3">
                                    <div class="d-flex align-items-center">
                                        <span class="info-icon mr-3">🕒</span>
                                        <div>
                                            <strong>Hora</strong><br>
                                            <span class="text-muted"><?= date('H:i', strtotime($event['hora'])) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6">
                            <?php if ($event['lugar']): ?>
                                <div class="info-item mb-3">
                                    <div class="d-flex align-items-center">
                                        <span class="info-icon mr-3">📍</span>
                                        <div>
                                            <strong>Lugar</strong><br>
                                            <span class="text-muted"><?= htmlspecialchars($event['lugar']) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($event['capacidad_maxima'] > 0): ?>
                                <div class="info-item mb-3">
                                    <div class="d-flex align-items-center">
                                        <span class="info-icon mr-3">👥</span>
                                        <div>
                                            <strong>Capacidad</strong><br>
                                            <span class="text-muted"><?= $event['capacidad_maxima'] ?> personas</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Event Actions -->
        <div class="card">
            <div class="card-header">
                <h5>Acciones</h5>
            </div>
            <div class="card-body">
                <?php if ($daysUntil >= 0): ?>
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" onclick="addToCalendar()">
                            📅 Agregar al Calendario
                        </button>
                        <button class="btn btn-outline-secondary" onclick="shareEvent()">
                            📤 Compartir Evento
                        </button>
                        <button class="btn btn-outline-info" onclick="setReminder()">
                            🔔 Recordatorio
                        </button>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <p class="mb-0">Este evento ya ha finalizado.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Event Details Summary -->
        <div class="card mt-3">
            <div class="card-header">
                <h6>Resumen</h6>
            </div>
            <div class="card-body">
                <div class="summary-item">
                    <strong>Tipo:</strong> <?= ucfirst($event['tipo']) ?>
                </div>
                <div class="summary-item">
                    <strong>Fecha de creación:</strong><br>
                    <small class="text-muted">
                        <?= date('d/m/Y', strtotime($event['fecha_creacion'])) ?>
                    </small>
                </div>
                <?php if ($daysUntil >= 0): ?>
                    <div class="summary-item">
                        <strong>Estado:</strong> 
                        <span class="badge badge-success">Activo</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Related Events -->
        <div class="card mt-3">
            <div class="card-header">
                <h6>Eventos Relacionados</h6>
            </div>
            <div class="card-body">
                <p class="text-muted text-center">
                    <small>Próximamente: eventos relacionados</small>
                </p>
            </div>
        </div>
    </div>
</div>

<style>
.badge-lg {
    padding: var(--spacing-2) var(--spacing-4);
    font-size: 0.875rem;
}

.info-icon {
    font-size: 1.5rem;
    width: 2rem;
    text-align: center;
}

.info-item {
    padding: var(--spacing-3);
    border-left: 3px solid var(--primary-200);
    background-color: var(--gray-50);
    border-radius: var(--border-radius-md);
}

.summary-item {
    padding: var(--spacing-2) 0;
    border-bottom: 1px solid var(--gray-200);
}

.summary-item:last-child {
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
function addToCalendar() {
    const event = <?= json_encode($event) ?>;
    const startDate = new Date(event.fecha + (event.hora ? 'T' + event.hora : 'T09:00:00'));
    const endDate = new Date(startDate.getTime() + (2 * 60 * 60 * 1000)); // 2 hours later
    
    const calendarUrl = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(event.nombre)}&dates=${startDate.toISOString().replace(/[-:]/g, '').split('.')[0]}Z/${endDate.toISOString().replace(/[-:]/g, '').split('.')[0]}Z&details=${encodeURIComponent(event.descripcion || '')}&location=${encodeURIComponent(event.lugar || '')}`;
    
    window.open(calendarUrl, '_blank');
}

function shareEvent() {
    if (navigator.share) {
        navigator.share({
            title: <?= json_encode($event['nombre']) ?>,
            text: <?= json_encode($event['descripcion']) ?>,
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Enlace copiado al portapapeles');
        });
    }
}

function setReminder() {
    alert('Función de recordatorio próximamente disponible');
}
</script>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>