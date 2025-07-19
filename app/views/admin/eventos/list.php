<?php
$title = 'Gestión de Eventos';
$pageTitle = 'Gestión de Eventos';
$pageDescription = 'Administra todos los eventos y actividades institucionales';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Gestión de Eventos</h2>
        <p class="text-muted">Administra todos los eventos y actividades institucionales</p>
    </div>
    <div>
        <a href="/admin/eventos/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Evento
        </a>
        <a href="/admin/eventos/calendar" class="btn btn-outline-secondary">
            <i class="fas fa-calendar"></i> Vista Calendario
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stats-card bg-primary">
            <h3><?= $stats['total_eventos'] ?? count($events) ?></h3>
            <p>Total de Eventos</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card bg-success">
            <h3><?= $stats['eventos_proximos'] ?? 0 ?></h3>
            <p>Próximos Eventos</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card bg-warning">
            <h3><?= $stats['eventos_esta_semana'] ?? 0 ?></h3>
            <p>Esta Semana</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card bg-info">
            <h3><?= $stats['capacitaciones'] ?? 0 ?></h3>
            <p>Capacitaciones</p>
        </div>
    </div>
</div>

<!-- Search and Filters -->
<div class="card mb-4">
    <div class="card-body">
        <?php
        $hasFilters = !empty($_GET['search']) || !empty($_GET['tipo']) || !empty($_GET['estado']) ||
            !empty($_GET['fecha_desde']) || !empty($_GET['fecha_hasta']);
        ?>
        <?php if ($hasFilters): ?>
            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle"></i>
                <strong>Filtros activos:</strong>
                <?php
                $activeFilters = [];
                if (!empty($_GET['search'])) $activeFilters[] = "Búsqueda: " . htmlspecialchars($_GET['search']);
                if (!empty($_GET['tipo'])) $activeFilters[] = "Tipo: " . ucfirst($_GET['tipo']);
                if (!empty($_GET['estado'])) $activeFilters[] = "Estado: " . ucfirst($_GET['estado']);
                if (!empty($_GET['fecha_desde'])) $activeFilters[] = "Desde: " . $_GET['fecha_desde'];
                if (!empty($_GET['fecha_hasta'])) $activeFilters[] = "Hasta: " . $_GET['fecha_hasta'];
                echo implode(', ', $activeFilters);
                ?>
                <a href="/admin/eventos" class="btn btn-sm btn-outline-secondary ms-2">
                    <i class="fas fa-times"></i> Limpiar todos
                </a>
            </div>
        <?php endif; ?>
        <form method="GET" action="/admin/eventos" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Buscar</label>
                <input type="text"
                    id="search"
                    name="search"
                    class="form-control"
                    placeholder="Nombre del evento..."
                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label for="tipo" class="form-label">Tipo</label>
                <select id="tipo" name="tipo" class="form-control form-select">
                    <option value="">Todos los tipos</option>
                    <option value="capacitacion" <?= ($_GET['tipo'] ?? '') === 'capacitacion' ? 'selected' : '' ?>>Capacitación</option>
                    <option value="evento" <?= ($_GET['tipo'] ?? '') === 'evento' ? 'selected' : '' ?>>Evento</option>
                    <option value="charla" <?= ($_GET['tipo'] ?? '') === 'charla' ? 'selected' : '' ?>>Charla</option>
                    <option value="reunion" <?= ($_GET['tipo'] ?? '') === 'reunion' ? 'selected' : '' ?>>Reunión</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="estado" class="form-label">Estado</label>
                <select id="estado" name="estado" class="form-control form-select">
                    <option value="">Todos</option>
                    <option value="activo" <?= ($_GET['estado'] ?? '') === 'activo' ? 'selected' : '' ?>>Activo</option>
                    <option value="finalizado" <?= ($_GET['estado'] ?? '') === 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
                    <option value="cancelado" <?= ($_GET['estado'] ?? '') === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="fecha_desde" class="form-label">Desde</label>
                <input type="date"
                    id="fecha_desde"
                    name="fecha_desde"
                    class="form-control"
                    value="<?= htmlspecialchars($_GET['fecha_desde'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label for="fecha_hasta" class="form-label">Hasta</label>
                <input type="date"
                    id="fecha_hasta"
                    name="fecha_hasta"
                    class="form-control"
                    value="<?= htmlspecialchars($_GET['fecha_hasta'] ?? '') ?>">
            </div>
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </div>
            </div>
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <a href="/admin/eventos" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Events Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Lista de Eventos</h3>
        <div class="d-flex align-items-center">
            <span class="text-muted me-3"><?= $total_events ?? count($events) ?> resultados</span>
            <select id="per-page" class="form-select form-select-sm" style="width: auto;">
                <option value="10" <?= ($_GET['per_page'] ?? '10') === '10' ? 'selected' : '' ?>>10 por página</option>
                <option value="25" <?= ($_GET['per_page'] ?? '10') === '25' ? 'selected' : '' ?>>25 por página</option>
                <option value="50" <?= ($_GET['per_page'] ?? '10') === '50' ? 'selected' : '' ?>>50 por página</option>
            </select>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($events)): ?>
            <div class="text-center py-5">
                <h4>No se encontraron eventos</h4>
                <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Evento</th>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Lugar</th>
                            <th>Capacidad</th>
                            <th>Estado</th>
                            <th>Inscritos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $evento): ?>
                            <tr>
                                <td>
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($evento['nombre']) ?></div>
                                        <small class="text-muted">
                                            <?= htmlspecialchars(substr($evento['descripcion'], 0, 60)) ?>
                                            <?= strlen($evento['descripcion']) > 60 ? '...' : '' ?>
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-<?=
                                                                $evento['tipo'] === 'capacitacion' ? 'primary' : ($evento['tipo'] === 'charla' ? 'success' : ($evento['tipo'] === 'reunion' ? 'warning' : 'secondary'))
                                                                ?>">
                                        <?= ucfirst($evento['tipo']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-bold"><?= date('d/m/Y', strtotime($evento['fecha'])) ?></div>
                                        <?php if ($evento['hora']): ?>
                                            <small class="text-muted"><?= date('H:i', strtotime($evento['hora'])) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($evento['lugar']): ?>
                                        <span class="text-muted"><?= htmlspecialchars($evento['lugar']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($evento['capacidad_maxima'] > 0): ?>
                                        <span class="text-muted"><?= $evento['capacidad_maxima'] ?> personas</span>
                                    <?php else: ?>
                                        <span class="text-muted">Sin límite</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $eventDate = strtotime($evento['fecha']);
                                    $today = strtotime(date('Y-m-d'));
                                    $isPast = $eventDate < $today;
                                    $isToday = $eventDate == $today;
                                    ?>
                                    <?php if (!$evento['activo']): ?>
                                        <span class="badge badge-danger">Cancelado</span>
                                    <?php elseif ($isPast): ?>
                                        <span class="badge badge-secondary">Finalizado</span>
                                    <?php elseif ($isToday): ?>
                                        <span class="badge badge-success">Hoy</span>
                                    <?php else: ?>
                                        <span class="badge badge-primary">Activo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($evento['capacidad_maxima'] > 0): ?>
                                        <div class="d-flex align-items-center">
                                            <span class="me-2"><?= $evento['inscritos'] ?? 0 ?>/<?= $evento['capacidad_maxima'] ?></span>
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <?php $porcentaje = ($evento['inscritos'] ?? 0) / $evento['capacidad_maxima'] * 100; ?>
                                                <div class="progress-bar bg-<?= $porcentaje >= 90 ? 'danger' : ($porcentaje >= 70 ? 'warning' : 'success') ?>"
                                                    style="width: <?= $porcentaje ?>%"></div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted"><?= $evento['inscritos'] ?? 0 ?> inscritos</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="/admin/eventos/<?= $evento['id'] ?>"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Ver detalles">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                        <a href="/admin/eventos/edit/<?= $evento['id'] ?>"
                                            class="btn btn-sm btn-outline-secondary"
                                            title="Editar">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <a href="/admin/eventos/<?= $evento['id'] ?>/inscritos"
                                            class="btn btn-sm btn-outline-info"
                                            title="Ver inscritos">
                                            <i class="fas fa-users"></i> Inscritos
                                        </a>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Eliminar"
                                            onclick="confirmDelete(<?= $evento['id'] ?>, '<?= htmlspecialchars($evento['nombre']) ?>')">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (isset($total_pages) && $total_pages > 1): ?>
                <nav aria-label="Paginación de eventos">
                    <ul class="pagination justify-content-center">
                        <?php if (isset($current_page) && $current_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $current_page - 1])) ?>">
                                    Anterior
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (isset($current_page) && isset($total_pages)): ?>
                            <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                                <li class="page-item <?= $i === $current_page ? 'active' : '' ?>">
                                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($current_page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $current_page + 1])) ?>">
                                        Siguiente
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
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

    .stats-card.bg-success {
        background: linear-gradient(135deg, var(--success-500), var(--success-600));
    }

    .stats-card.bg-warning {
        background: linear-gradient(135deg, var(--warning-500), var(--warning-600));
    }

    .stats-card.bg-info {
        background: linear-gradient(135deg, var(--info-500), var(--info-600));
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

    .table th {
        border-top: none;
        font-weight: var(--font-weight-semibold);
        color: var(--gray-600);
    }

    .btn-group .btn {
        border-radius: var(--border-radius-sm);
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        white-space: nowrap;
    }

    .btn-group .btn:not(:last-child) {
        margin-right: 2px;
    }

    .btn-group .btn i {
        margin-right: 0.25rem;
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
    // Per page selector
    document.getElementById('per-page').addEventListener('change', function() {
        const url = new URL(window.location);
        url.searchParams.set('per_page', this.value);
        url.searchParams.delete('page'); // Reset to first page
        window.location.href = url.toString();
    });

    // Confirm delete function
    function confirmDelete(eventoId, eventoName) {
        if (confirm(`¿Estás seguro de que quieres eliminar el evento "${eventoName}"? Esta acción no se puede deshacer.`)) {
            fetch(`/admin/eventos/${eventoId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                })
                .then(response => {
                    if (response.ok) {
                        location.reload();
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