<?php
$title = 'Encuestas';
$pageTitle = 'Encuestas de Empleabilidad';
$pageDescription = 'Participa en las encuestas para mejorar el seguimiento de egresados';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Encuestas Disponibles</h2>
    </div>
</div>

<?php if (empty($encuestas)): ?>
    <div class="card">
        <div class="card-body text-center p-6">
            <h4>No hay encuestas disponibles</h4>
            <p class="text-muted">Por el momento no hay encuestas activas para responder</p>
        </div>
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($encuestas as $encuesta): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title"><?= htmlspecialchars($encuesta['pregunta']) ?></h5>
                            <?php if ($encuesta['respuesta']): ?>
                                <span class="badge badge-success">Respondida</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Pendiente</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <small class="text-muted">
                                Tipo: <?= ucfirst(str_replace('_', ' ', $encuesta['tipo_respuesta'])) ?>
                            </small>
                        </div>
                        
                        <?php if ($encuesta['respuesta']): ?>
                            <div class="alert alert-success">
                                <strong>Tu respuesta:</strong><br>
                                <?= htmlspecialchars($encuesta['respuesta']) ?>
                                <br><small class="text-muted">
                                    Respondida el <?= date('d/m/Y H:i', strtotime($encuesta['fecha_respuesta'])) ?>
                                </small>
                            </div>
                        <?php else: ?>
                            <div class="mt-3">
                                <a href="/encuestas/<?= $encuesta['id'] ?>" class="btn btn-primary">
                                    Responder Encuesta
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Statistics Section -->
<div class="card mt-4">
    <div class="card-header">
        <h3>Tu Participación</h3>
    </div>
    <div class="card-body">
        <?php 
        $totalEncuestas = count($encuestas);
        $respondidas = count(array_filter($encuestas, function($e) { return !empty($e['respuesta']); }));
        $porcentaje = $totalEncuestas > 0 ? round(($respondidas / $totalEncuestas) * 100) : 0;
        ?>
        
        <div class="row">
            <div class="col-md-4 text-center">
                <h4 class="text-primary"><?= $totalEncuestas ?></h4>
                <p class="text-muted">Total de Encuestas</p>
            </div>
            <div class="col-md-4 text-center">
                <h4 class="text-success"><?= $respondidas ?></h4>
                <p class="text-muted">Respondidas</p>
            </div>
            <div class="col-md-4 text-center">
                <h4 class="text-warning"><?= $porcentaje ?>%</h4>
                <p class="text-muted">Participación</p>
            </div>
        </div>
        
        <div class="progress mt-3">
            <div class="progress-bar bg-success" 
                 role="progressbar" 
                 style="width: <?= $porcentaje ?>%"
                 aria-valuenow="<?= $porcentaje ?>" 
                 aria-valuemin="0" 
                 aria-valuemax="100">
                <?= $porcentaje ?>%
            </div>
        </div>
    </div>
</div>

<style>
.progress {
    height: 20px;
    background-color: var(--gray-200);
    border-radius: var(--border-radius-lg);
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background-color: var(--success-500);
    transition: width var(--transition-normal);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: var(--font-weight-medium);
}
</style>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>