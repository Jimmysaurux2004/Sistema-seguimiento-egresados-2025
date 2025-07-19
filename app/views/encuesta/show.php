<?php
$title = 'Responder Encuesta';
$pageTitle = 'Responder Encuesta';
$pageDescription = 'Completa la siguiente encuesta';
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3>Encuesta</h3>
            </div>
            <div class="card-body">
                <?php if ($has_responded): ?>
                    <div class="alert alert-info">
                        <h5>Ya has respondido esta encuesta</h5>
                        <p>Gracias por tu participación. Ya no puedes modificar tu respuesta.</p>
                        <a href="/encuestas" class="btn btn-primary">Volver a Encuestas</a>
                    </div>
                <?php else: ?>
                    <form method="POST" action="/encuestas/respond" data-validate>
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <input type="hidden" name="encuesta_id" value="<?= $encuesta['id'] ?>">
                        
                        <div class="form-group">
                            <label class="form-label">
                                <strong><?= htmlspecialchars($encuesta['pregunta']) ?></strong>
                            </label>
                            
                            <?php if ($encuesta['tipo_respuesta'] === 'texto'): ?>
                                <textarea name="respuesta" 
                                          class="form-control" 
                                          rows="4" 
                                          placeholder="Escribe tu respuesta aquí..."
                                          required></textarea>
                                          
                            <?php elseif ($encuesta['tipo_respuesta'] === 'opcion_multiple'): ?>
                                <?php $opciones = json_decode($encuesta['opciones'], true); ?>
                                <?php if ($opciones): ?>
                                    <?php foreach ($opciones as $opcion): ?>
                                        <div class="form-check">
                                            <input type="radio" 
                                                   id="opcion_<?= md5($opcion) ?>" 
                                                   name="respuesta" 
                                                   value="<?= htmlspecialchars($opcion) ?>" 
                                                   class="form-check-input" 
                                                   required>
                                            <label for="opcion_<?= md5($opcion) ?>" class="form-check-label">
                                                <?= htmlspecialchars($opcion) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                
                            <?php elseif ($encuesta['tipo_respuesta'] === 'escala'): ?>
                                <?php $opciones = json_decode($encuesta['opciones'], true); ?>
                                <div class="scale-container">
                                    <?php 
                                    $min = $opciones['min'] ?? 1;
                                    $max = $opciones['max'] ?? 5;
                                    $labels = $opciones['labels'] ?? [];
                                    ?>
                                    
                                    <div class="scale-labels d-flex justify-content-between mb-2">
                                        <?php for ($i = $min; $i <= $max; $i++): ?>
                                            <small class="text-muted">
                                                <?= isset($labels[$i-1]) ? htmlspecialchars($labels[$i-1]) : $i ?>
                                            </small>
                                        <?php endfor; ?>
                                    </div>
                                    
                                    <div class="scale-options d-flex justify-content-between">
                                        <?php for ($i = $min; $i <= $max; $i++): ?>
                                            <div class="form-check">
                                                <input type="radio" 
                                                       id="escala_<?= $i ?>" 
                                                       name="respuesta" 
                                                       value="<?= $i ?>" 
                                                       class="form-check-input" 
                                                       required>
                                                <label for="escala_<?= $i ?>" class="form-check-label">
                                                    <?= $i ?>
                                                </label>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                
                            <?php elseif ($encuesta['tipo_respuesta'] === 'si_no'): ?>
                                <div class="form-check">
                                    <input type="radio" 
                                           id="si" 
                                           name="respuesta" 
                                           value="Sí" 
                                           class="form-check-input" 
                                           required>
                                    <label for="si" class="form-check-label">Sí</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" 
                                           id="no" 
                                           name="respuesta" 
                                           value="No" 
                                           class="form-check-input" 
                                           required>
                                    <label for="no" class="form-check-label">No</label>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="/encuestas" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary" data-original-text="Enviar Respuesta">
                                Enviar Respuesta
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.scale-container {
    padding: var(--spacing-4);
    background-color: var(--gray-50);
    border-radius: var(--border-radius-md);
}

.scale-options {
    max-width: 400px;
    margin: 0 auto;
}

.scale-options .form-check {
    text-align: center;
}

.scale-options .form-check-input {
    margin: 0 auto;
}
</style>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>