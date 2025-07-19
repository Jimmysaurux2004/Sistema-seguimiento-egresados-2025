<?php
$title = 'Eventos';
$pageTitle = 'Eventos Institucionales';
$pageDescription = 'Mantente informado sobre eventos, capacitaciones y actividades';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Eventos y Actividades</h2>
    </div>
</div>

<?php if (empty($events)): ?>
    <div class="card">
        <div class="card-body text-center p-6">
            <h4>No hay eventos programados</h4>
            <p class="text-muted">Por el momento no hay eventos disponibles</p>
        </div>
    </div>
<?php else: ?>
    <!-- Filter by type -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-outline-primary active" data-filter="all">
                    Todos los Eventos
                </button>
                <button class="btn btn-outline-secondary" data-filter="capacitacion">
                    Capacitaciones
                </button>
                <button class="btn btn-outline-secondary" data-filter="evento">
                    Eventos
                </button>
                <button class="btn btn-outline-secondary" data-filter="charla">
                    Charlas
                </button>
                <button class="btn btn-outline-secondary" data-filter="reunion">
                    Reuniones
                </button>
            </div>
        </div>
    </div>
    
    <!-- Events Grid -->
    <div class="row" id="events-container">
        <?php foreach ($events as $event): ?>
            <div class="col-md-6 col-lg-4 mb-4 event-card" data-type="<?= $event['tipo'] ?>">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge badge-<?= 
                                $event['tipo'] === 'capacitacion' ? 'primary' : 
                                ($event['tipo'] === 'charla' ? 'success' : 
                                ($event['tipo'] === 'reunion' ? 'warning' : 'secondary')) 
                            ?>">
                                <?= ucfirst($event['tipo']) ?>
                            </span>
                            <small class="text-muted">
                                <?= date('d/m/Y', strtotime($event['fecha'])) ?>
                            </small>
                        </div>
                        
                        <h5 class="card-title"><?= htmlspecialchars($event['nombre']) ?></h5>
                        
                        <p class="card-text text-muted">
                            <?= htmlspecialchars(substr($event['descripcion'], 0, 120)) ?>
                            <?= strlen($event['descripcion']) > 120 ? '...' : '' ?>
                        </p>
                        
                        <div class="event-details mt-3">
                            <?php if ($event['hora']): ?>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="mr-2">🕒</span>
                                    <small><?= date('H:i', strtotime($event['hora'])) ?></small>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($event['lugar']): ?>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="mr-2">📍</span>
                                    <small><?= htmlspecialchars($event['lugar']) ?></small>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($event['capacidad_maxima'] > 0): ?>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="mr-2">👥</span>
                                    <small>Capacidad: <?= $event['capacidad_maxima'] ?> personas</small>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-3">
                            <a href="/eventos/<?= $event['id'] ?>" class="btn btn-primary btn-sm">
                                Ver Detalles
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <?php 
                        $eventDate = strtotime($event['fecha']);
                        $today = strtotime(date('Y-m-d'));
                        $daysUntil = ceil(($eventDate - $today) / (60 * 60 * 24));
                        ?>
                        
                        <?php if ($daysUntil < 0): ?>
                            <small class="text-muted">Evento finalizado</small>
                        <?php elseif ($daysUntil === 0): ?>
                            <small class="text-success"><strong>¡Hoy!</strong></small>
                        <?php elseif ($daysUntil === 1): ?>
                            <small class="text-warning"><strong>Mañana</strong></small>
                        <?php elseif ($daysUntil <= 7): ?>
                            <small class="text-primary">En <?= $daysUntil ?> días</small>
                        <?php else: ?>
                            <small class="text-muted">En <?= $daysUntil ?> días</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Upcoming Events Summary -->
<?php 
$upcomingEvents = array_filter($events, function($event) {
    return strtotime($event['fecha']) >= strtotime(date('Y-m-d'));
});
?>

<?php if (!empty($upcomingEvents)): ?>
<div class="card mt-4">
    <div class="card-header">
        <h3>Próximos Eventos</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 text-center">
                <h4 class="text-primary"><?= count($upcomingEvents) ?></h4>
                <p class="text-muted">Eventos Próximos</p>
            </div>
            <div class="col-md-4 text-center">
                <?php 
                $thisWeek = array_filter($upcomingEvents, function($event) {
                    $eventDate = strtotime($event['fecha']);
                    $weekEnd = strtotime('+7 days');
                    return $eventDate <= $weekEnd;
                });
                ?>
                <h4 class="text-success"><?= count($thisWeek) ?></h4>
                <p class="text-muted">Esta Semana</p>
            </div>
            <div class="col-md-4 text-center">
                <?php 
                $capacitaciones = array_filter($upcomingEvents, function($event) {
                    return $event['tipo'] === 'capacitacion';
                });
                ?>
                <h4 class="text-warning"><?= count($capacitaciones) ?></h4>
                <p class="text-muted">Capacitaciones</p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
.event-card {
    transition: transform var(--transition-fast);
}

.event-card:hover {
    transform: translateY(-2px);
}

.event-details {
    border-top: 1px solid var(--gray-200);
    padding-top: var(--spacing-3);
}

.btn[data-filter].active {
    background-color: var(--primary-600);
    color: white;
    border-color: var(--primary-600);
}

.event-card.hidden {
    display: none;
}
</style>

<script>
// Event filtering
document.addEventListener('click', function(e) {
    const filterBtn = e.target.closest('[data-filter]');
    if (!filterBtn) return;
    
    const filter = filterBtn.getAttribute('data-filter');
    
    // Update active button
    document.querySelectorAll('[data-filter]').forEach(btn => {
        btn.classList.remove('active');
        btn.classList.add('btn-outline-secondary');
        btn.classList.remove('btn-outline-primary');
    });
    
    filterBtn.classList.add('active');
    filterBtn.classList.remove('btn-outline-secondary');
    filterBtn.classList.add('btn-outline-primary');
    
    // Filter events
    document.querySelectorAll('.event-card').forEach(card => {
        if (filter === 'all' || card.getAttribute('data-type') === filter) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>