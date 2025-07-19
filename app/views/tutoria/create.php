<?php
$title = 'Solicitar Tutoría';
$pageTitle = 'Nueva Solicitud de Tutoría';
$pageDescription = 'Solicita una sesión de tutoría con un docente disponible';
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3>Solicitar Nueva Tutoría</h3>
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
                
                <form method="POST" action="/tutorias" data-validate>
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <div class="form-group">
                        <label for="docente" class="form-label">Docente</label>
                        <select id="docente" name="docente" class="form-control form-select" required>
                            <option value="">Selecciona un docente</option>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?= htmlspecialchars($teacher) ?>" 
                                        <?= isset($formData['docente']) && $formData['docente'] === $teacher ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($teacher) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha" class="form-label">Fecha</label>
                                <input type="date" 
                                       id="fecha" 
                                       name="fecha" 
                                       class="form-control" 
                                       min="<?= date('Y-m-d') ?>"
                                       value="<?= isset($formData['fecha']) ? htmlspecialchars($formData['fecha']) : '' ?>"
                                       required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hora" class="form-label">Hora</label>
                                <select id="hora" name="hora" class="form-control form-select" required>
                                    <option value="">Selecciona una hora</option>
                                    <option value="08:00" <?= isset($formData['hora']) && $formData['hora'] === '08:00' ? 'selected' : '' ?>>08:00 AM</option>
                                    <option value="09:00" <?= isset($formData['hora']) && $formData['hora'] === '09:00' ? 'selected' : '' ?>>09:00 AM</option>
                                    <option value="10:00" <?= isset($formData['hora']) && $formData['hora'] === '10:00' ? 'selected' : '' ?>>10:00 AM</option>
                                    <option value="11:00" <?= isset($formData['hora']) && $formData['hora'] === '11:00' ? 'selected' : '' ?>>11:00 AM</option>
                                    <option value="14:00" <?= isset($formData['hora']) && $formData['hora'] === '14:00' ? 'selected' : '' ?>>02:00 PM</option>
                                    <option value="15:00" <?= isset($formData['hora']) && $formData['hora'] === '15:00' ? 'selected' : '' ?>>03:00 PM</option>
                                    <option value="16:00" <?= isset($formData['hora']) && $formData['hora'] === '16:00' ? 'selected' : '' ?>>04:00 PM</option>
                                    <option value="17:00" <?= isset($formData['hora']) && $formData['hora'] === '17:00' ? 'selected' : '' ?>>05:00 PM</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="motivo" class="form-label">Motivo de la Tutoría</label>
                        <textarea id="motivo" 
                                  name="motivo" 
                                  class="form-control" 
                                  rows="4" 
                                  placeholder="Describe el tema o motivo de la tutoría..."
                                  required><?= isset($formData['motivo']) ? htmlspecialchars($formData['motivo']) : '' ?></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="/tutorias" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary" data-original-text="Solicitar Tutoría">
                            Solicitar Tutoría
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