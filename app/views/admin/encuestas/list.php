<?php
$title = 'Gestión de Encuestas';
$pageTitle = 'Gestión de Encuestas';
$pageDescription = 'Administra todas las encuestas de empleabilidad y seguimiento';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Gestión de Encuestas</h2>
        <p class="text-muted">Administra todas las encuestas de empleabilidad y seguimiento</p>
    </div>
    <div>
        <a href="/admin/encuestas/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Encuesta
        </a>
        <a href="/admin/encuestas/report" class="btn btn-outline-secondary">
            <i class="fas fa-chart-bar"></i> Reportes
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stats-card bg-primary">
            <h3><?= $stats['total_encuestas'] ?? count($encuestas) ?></h3>
            <p>Total de Encuestas</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card bg-success">
            <h3><?= $stats['encuestas_activas'] ?? 0 ?></h3>
            <p>Encuestas Activas</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card bg-warning">
            <h3><?= $stats['respuestas_totales'] ?? 0 ?></h3>
            <p>Total Respuestas</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card bg-info">
            <h3><?= $stats['tasa_respuesta'] ?? 0 ?>%</h3>
            <p>Tasa de Respuesta</p>
        </div>
    </div>
</div>

<!-- Search and Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="/admin/encuestas" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Buscar</label>
                <input type="text"
                    id="search"
                    name="search"
                    class="form-control"
                    placeholder="Pregunta de la encuesta..."
                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label for="tipo_respuesta" class="form-label">Tipo de Respuesta</label>
                <select id="tipo_respuesta" name="tipo_respuesta" class="form-control form-select">
                    <option value="">Todos los tipos</option>
                    <option value="texto_libre" <?= ($_GET['tipo_respuesta'] ?? '') === 'texto_libre' ? 'selected' : '' ?>>Texto Libre</option>
                    <option value="opcion_multiple" <?= ($_GET['tipo_respuesta'] ?? '') === 'opcion_multiple' ? 'selected' : '' ?>>Opción Múltiple</option>
                    <option value="escala" <?= ($_GET['tipo_respuesta'] ?? '') === 'escala' ? 'selected' : '' ?>>Escala</option>
                    <option value="si_no" <?= ($_GET['tipo_respuesta'] ?? '') === 'si_no' ? 'selected' : '' ?>>Sí/No</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="estado" class="form-label">Estado</label>
                <select id="estado" name="estado" class="form-control form-select">
                    <option value="">Todos</option>
                    <option value="activa" <?= ($_GET['estado'] ?? '') === 'activa' ? 'selected' : '' ?>>Activa</option>
                    <option value="inactiva" <?= ($_GET['estado'] ?? '') === 'inactiva' ? 'selected' : '' ?>>Inactiva</option>
                    <option value="finalizada" <?= ($_GET['estado'] ?? '') === 'finalizada' ? 'selected' : '' ?>>Finalizada</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Encuestas Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Lista de Encuestas</h3>
        <div class="d-flex align-items-center">
            <span class="text-muted me-3"><?= count($encuestas) ?> resultados</span>
            <select id="per-page" class="form-select form-select-sm" style="width: auto;">
                <option value="10" <?= ($_GET['per_page'] ?? '10') === '10' ? 'selected' : '' ?>>10 por página</option>
                <option value="25" <?= ($_GET['per_page'] ?? '10') === '25' ? 'selected' : '' ?>>25 por página</option>
                <option value="50" <?= ($_GET['per_page'] ?? '10') === '50' ? 'selected' : '' ?>>50 por página</option>
            </select>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($encuestas)): ?>
            <div class="text-center py-5">
                <h4>No se encontraron encuestas</h4>
                <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Pregunta</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Respuestas</th>
                            <th>Tasa Respuesta</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($encuestas as $encuesta): ?>
                            <tr>
                                <td>
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($encuesta['pregunta']) ?></div>
                                        <?php if (!empty($encuesta['descripcion'])): ?>
                                            <small class="text-muted">
                                                <?= htmlspecialchars(substr($encuesta['descripcion'], 0, 60)) ?>
                                                <?= strlen($encuesta['descripcion']) > 60 ? '...' : '' ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-<?=
                                                                $encuesta['tipo_respuesta'] === 'texto_libre' ? 'primary' : ($encuesta['tipo_respuesta'] === 'opcion_multiple' ? 'success' : ($encuesta['tipo_respuesta'] === 'escala' ? 'warning' : 'info'))
                                                                ?>">
                                        <?= ucfirst(str_replace('_', ' ', $encuesta['tipo_respuesta'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?=
                                                                $encuesta['estado'] === 'activa' ? 'success' : ($encuesta['estado'] === 'inactiva' ? 'secondary' : 'warning')
                                                                ?>">
                                        <?= ucfirst($encuesta['estado']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="me-2"><?= $encuesta['total_respuestas'] ?? 0 ?></span>
                                        <?php if (isset($encuesta['total_egresados']) && $encuesta['total_egresados'] > 0): ?>
                                            <small class="text-muted">/ <?= $encuesta['total_egresados'] ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $tasa = isset($encuesta['total_egresados']) && $encuesta['total_egresados'] > 0 ?
                                        round(($encuesta['total_respuestas'] ?? 0) / $encuesta['total_egresados'] * 100) : 0;
                                    ?>
                                    <div class="d-flex align-items-center">
                                        <span class="me-2"><?= $tasa ?>%</span>
                                        <div class="progress flex-grow-1" style="height: 6px; width: 60px;">
                                            <div class="progress-bar bg-<?= $tasa >= 80 ? 'success' : ($tasa >= 50 ? 'warning' : 'danger') ?>"
                                                style="width: <?= $tasa ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= date('d/m/Y', strtotime($encuesta['fecha_creacion'])) ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="/admin/encuestas/<?= $encuesta['id'] ?>"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Ver detalles">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                        <a href="/admin/encuestas/<?= $encuesta['id'] ?>/edit"
                                            class="btn btn-sm btn-outline-secondary"
                                            title="Editar">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <a href="/admin/encuestas/<?= $encuesta['id'] ?>/respuestas"
                                            class="btn btn-sm btn-outline-info"
                                            title="Ver respuestas">
                                            <i class="fas fa-list"></i> Respuestas
                                        </a>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Eliminar"
                                            onclick="confirmDelete(<?= $encuesta['id'] ?>, '<?= htmlspecialchars($encuesta['pregunta']) ?>')">
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
                <nav aria-label="Paginación de encuestas">
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

<!-- Response Analysis -->
<?php if (!empty($encuestas)): ?>
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Análisis de Respuestas</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 text-center">
                            <h4 class="text-primary"><?= $stats['respuestas_este_mes'] ?? 0 ?></h4>
                            <p class="text-muted">Este Mes</p>
                        </div>
                        <div class="col-6 text-center">
                            <h4 class="text-success"><?= $stats['respuestas_semana'] ?? 0 ?></h4>
                            <p class="text-muted">Esta Semana</p>
                        </div>
                    </div>
                    <div class="progress mt-3">
                        <div class="progress-bar bg-success"
                            role="progressbar"
                            style="width: <?= $stats['tasa_respuesta'] ?? 0 ?>%"
                            aria-valuenow="<?= $stats['tasa_respuesta'] ?? 0 ?>"
                            aria-valuemin="0"
                            aria-valuemax="100">
                            <?= $stats['tasa_respuesta'] ?? 0 ?>%
                        </div>
                    </div>
                    <small class="text-muted">Tasa de respuesta general</small>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Tipos de Encuesta</h3>
                </div>
                <div class="card-body">
                    <?php
                    $tipos = [];
                    foreach ($encuestas as $encuesta) {
                        $tipo = $encuesta['tipo_respuesta'];
                        $tipos[$tipo] = ($tipos[$tipo] ?? 0) + 1;
                    }
                    ?>
                    <?php foreach ($tipos as $tipo => $cantidad): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted"><?= ucfirst(str_replace('_', ' ', $tipo)) ?></span>
                            <span class="badge badge-primary"><?= $cantidad ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
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
                        <a href="/admin/encuestas/create?tipo=texto_libre" class="btn btn-outline-primary w-100">
                            📝 Encuesta de Texto
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="/admin/encuestas/create?tipo=opcion_multiple" class="btn btn-outline-success w-100">
                            ☑️ Opción Múltiple
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="/admin/encuestas/create?tipo=escala" class="btn btn-outline-warning w-100">
                            📊 Escala de Valoración
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="/admin/encuestas/export" class="btn btn-outline-info w-100">
                            📥 Exportar Datos
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
    function confirmDelete(encuestaId, encuestaName) {
        if (confirm(`¿Estás seguro de que quieres eliminar la encuesta "${encuestaName}"? Esta acción no se puede deshacer.`)) {
            fetch(`/admin/encuestas/${encuestaId}`, {
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
                        alert('Error al eliminar la encuesta');
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