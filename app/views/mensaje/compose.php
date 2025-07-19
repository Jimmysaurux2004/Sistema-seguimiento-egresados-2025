<?php
$title = 'Nuevo Mensaje';
$pageTitle = 'Redactar Mensaje';
$pageDescription = 'Envía un nuevo mensaje';
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3>Nuevo Mensaje</h3>
            </div>
            <div class="card-body">
                <?php if (isset($errors) && !empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="/mensajes/send" data-validate>
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <div class="form-group">
                        <label for="receptor_id" class="form-label">Destinatario</label>
                        <select id="receptor_id" name="receptor_id" class="form-control form-select" required>
                            <option value="">Selecciona un destinatario</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>" 
                                        <?= isset($formData['receptor_id']) && $formData['receptor_id'] == $user['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($user['email']) ?>
                                    <?php if (isset($user['nombres']) && $user['nombres']): ?>
                                        - <?= htmlspecialchars($user['nombres'] . ' ' . $user['apellidos']) ?>
                                    <?php endif; ?>
                                    (<?= ucfirst($user['rol']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="asunto" class="form-label">Asunto</label>
                        <input type="text" 
                               id="asunto" 
                               name="asunto" 
                               class="form-control" 
                               placeholder="Asunto del mensaje"
                               value="<?= isset($formData['asunto']) ? htmlspecialchars($formData['asunto']) : '' ?>"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="mensaje" class="form-label">Mensaje</label>
                        <textarea id="mensaje" 
                                  name="mensaje" 
                                  class="form-control" 
                                  rows="6" 
                                  placeholder="Escribe tu mensaje aquí..."
                                  required><?= isset($formData['mensaje']) ? htmlspecialchars($formData['mensaje']) : '' ?></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="/mensajes" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary" data-original-text="Enviar Mensaje">
                            Enviar Mensaje
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>