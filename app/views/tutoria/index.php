<?php
$title = 'Mis Tutorías';
$pageTitle = 'Gestión de Tutorías';
$pageDescription = 'Solicita y gestiona tus sesiones de tutoría con docentes';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Mis Tutorías</h2>
    </div>
    <div>
        <?php if ($user_role !== 'admin'): ?>
            <a href="/tutorias/create" class="btn btn-primary">
                📚 Solicitar Nueva Tutoría
            </a>
        <?php endif; ?>
        <a href="/tutorias/calendar" class="btn btn-outline-secondary">
            📅 Ver Calendario
        </a>
    </div>
</div>

<?php if (empty($tutorias)): ?>
    <div class="card">
        <div class="card-body text-center p-6">
            <h4>No tienes tutorías programadas</h4>
            <p class="text-muted mb-4">Solicita tu primera sesión de tutoría con un docente</p>
            <?php if ($user_role !== 'admin'): ?>
                <a href="/tutorias/create" class="btn btn-primary">Solicitar Tutoría</a>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-header">
            <h3>Lista de Tutorías</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Docente</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Estado</th>
                            <th>Motivo</th>
                            <?php if ($user_role === 'admin'): ?>
                                <th>Egresado</th>
                                <th>Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tutorias as $tutoria): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($tutoria['docente']) ?></strong>
                                </td>
                                <td>
                                    <?= date('d/m/Y', strtotime($tutoria['fecha'])) ?>
                                </td>
                                <td>
                                    <?= date('H:i', strtotime($tutoria['hora'])) ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?= 
                                        $tutoria['estado'] === 'confirmada' ? 'success' : 
                                        ($tutoria['estado'] === 'pendiente' ? 'warning' : 
                                        ($tutoria['estado'] === 'completada' ? 'primary' : 'danger')) 
                                    ?>">
                                        <?= ucfirst($tutoria['estado']) ?>
                                    </span>
                                </td>
                                <td>
                                    <small><?= htmlspecialchars(substr($tutoria['motivo'], 0, 50)) ?>...</small>
                                </td>
                                <?php if ($user_role === 'admin'): ?>
                                    <td>
                                        <?= isset($tutoria['nombres']) ? htmlspecialchars($tutoria['nombres'] . ' ' . $tutoria['apellidos']) : 'N/A' ?>
                                    </td>
                                    <td>
                                        <?php if ($tutoria['estado'] === 'pendiente'): ?>
                                            <button class="btn btn-sm btn-success" 
                                                    onclick="updateTutoriaStatus(<?= $tutoria['id'] ?>, 'confirmada')">
                                                Confirmar
                                            </button>
                                            <button class="btn btn-sm btn-danger" 
                                                    onclick="updateTutoriaStatus(<?= $tutoria['id'] ?>, 'cancelada')">
                                                Cancelar
                                            </button>
                                        <?php elseif ($tutoria['estado'] === 'confirmada'): ?>
                                            <button class="btn btn-sm btn-primary" 
                                                    onclick="updateTutoriaStatus(<?= $tutoria['id'] ?>, 'completada')">
                                                Completar
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if ($user_role === 'admin'): ?>
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
<?php endif; ?>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>