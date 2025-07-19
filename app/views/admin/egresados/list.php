<?php
$title = 'Gestión de Egresados';
$pageTitle = 'Gestión de Egresados';
$pageDescription = 'Administra la información de todos los egresados registrados';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Gestión de Egresados</h2>
        <p class="text-muted">Administra la información de todos los egresados registrados</p>
    </div>
    <div>
        <!-- Botón de crear egresado removido -->
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stats-card bg-primary">
            <h3><?= $stats['total_egresados'] ?? count($egresados) ?></h3>
            <p>Total de Egresados</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card bg-success">
            <h3><?= $stats['empleados'] ?? 0 ?></h3>
            <p>Empleados</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card bg-warning">
            <h3><?= $stats['desempleados'] ?? 0 ?></h3>
            <p>Desempleados</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card bg-info">
            <h3><?= $stats['activos_este_mes'] ?? 0 ?></h3>
            <p>Activos este Mes</p>
        </div>
    </div>
</div>

<!-- Search and Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="/admin/egresados" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Buscar</label>
                <input type="text"
                    id="search"
                    name="search"
                    class="form-control"
                    placeholder="Nombre, DNI, email..."
                    value="<?= htmlspecialchars($search ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label for="carrera" class="form-label">Carrera</label>
                <select id="carrera" name="carrera" class="form-control form-select">
                    <option value="">Todas las carreras</option>
                    <?php
                    $carreras = array_unique(array_column($egresados, 'carrera'));
                    foreach ($carreras as $carrera):
                    ?>
                        <option value="<?= $carrera ?>" <?= ($_GET['carrera'] ?? '') === $carrera ? 'selected' : '' ?>>
                            <?= htmlspecialchars($carrera) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="situacion_laboral" class="form-label">Situación Laboral</label>
                <select id="situacion_laboral" name="situacion_laboral" class="form-control form-select">
                    <option value="">Todas</option>
                    <option value="empleado" <?= ($_GET['situacion_laboral'] ?? '') === 'empleado' ? 'selected' : '' ?>>Empleado</option>
                    <option value="desempleado" <?= ($_GET['situacion_laboral'] ?? '') === 'desempleado' ? 'selected' : '' ?>>Desempleado</option>
                    <option value="estudiando" <?= ($_GET['situacion_laboral'] ?? '') === 'estudiando' ? 'selected' : '' ?>>Estudiando</option>
                    <option value="emprendedor" <?= ($_GET['situacion_laboral'] ?? '') === 'emprendedor' ? 'selected' : '' ?>>Emprendedor</option>
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

<!-- Egresados Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Lista de Egresados</h3>
        <div class="d-flex align-items-center">
            <span class="text-muted me-3"><?= count($egresados) ?> resultados</span>
            <select id="per-page" class="form-select form-select-sm" style="width: auto;">
                <option value="10" <?= ($_GET['per_page'] ?? '10') === '10' ? 'selected' : '' ?>>10 por página</option>
                <option value="25" <?= ($_GET['per_page'] ?? '10') === '25' ? 'selected' : '' ?>>25 por página</option>
                <option value="50" <?= ($_GET['per_page'] ?? '10') === '50' ? 'selected' : '' ?>>50 por página</option>
            </select>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($egresados)): ?>
            <div class="text-center py-5">
                <h4>No se encontraron egresados</h4>
                <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>DNI</th>
                            <th>Nombre Completo</th>
                            <th>Carrera</th>
                            <th>Año Egreso</th>
                            <th>Situación Laboral</th>
                            <th>Empresa</th>
                            <th>Última Actualización</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($egresados as $egresado): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($egresado['dni']) ?></strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            <?= strtoupper(substr($egresado['nombres'], 0, 1) . substr($egresado['apellidos'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars($egresado['nombres'] . ' ' . $egresado['apellidos']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($egresado['correo']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($egresado['carrera']) ?></td>
                                <td><?= $egresado['anio_egreso'] ?></td>
                                <td>
                                    <span class="badge badge-<?=
                                                                $egresado['situacion_laboral_actual'] === 'empleado' ? 'success' : ($egresado['situacion_laboral_actual'] === 'desempleado' ? 'danger' : ($egresado['situacion_laboral_actual'] === 'estudiando' ? 'info' : 'warning'))
                                                                ?>">
                                        <?= ucfirst(str_replace('_', ' ', $egresado['situacion_laboral_actual'] ?? 'No especificado')) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($egresado['empresa_actual'])): ?>
                                        <span class="text-muted"><?= htmlspecialchars($egresado['empresa_actual']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= date('d/m/Y', strtotime($egresado['fecha_actualizacion'] ?? $egresado['fecha_registro'])) ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="/admin/egresados/<?= $egresado['dni'] ?>"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Ver detalles">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                        <a href="/admin/egresados/<?= $egresado['dni'] ?>/edit"
                                            class="btn btn-sm btn-outline-secondary"
                                            title="Editar">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Eliminar"
                                            onclick="confirmDelete('<?= $egresado['dni'] ?>', '<?= htmlspecialchars($egresado['nombres'] . ' ' . $egresado['apellidos']) ?>')">
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
                <nav aria-label="Paginación de egresados">
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

    .avatar-sm {
        width: 32px;
        height: 32px;
        background-color: var(--primary-500);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: var(--font-weight-bold);
        font-size: 0.875rem;
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
    function confirmDelete(egresadoDni, egresadoName) {
        if (confirm(`¿Estás seguro de que quieres eliminar al egresado "${egresadoName}"? Esta acción no se puede deshacer.`)) {
            fetch(`/admin/egresados/${egresadoDni}`, {
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