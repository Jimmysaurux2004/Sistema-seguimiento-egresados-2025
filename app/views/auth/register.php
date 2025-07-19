<?php
$title = 'Registrarse';
ob_start();
?>

<div class="container" style="max-width: 600px; margin-top: 3rem;">
    <div class="card">
        <div class="card-header text-center">
            <h2><?= APP_NAME ?></h2>
            <p class="text-muted">Registro de Egresado</p>
        </div>
        <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($errors) && !empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="/register" data-validate>
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="dni" class="form-label">DNI</label>
                            <input type="text" 
                                   id="dni" 
                                   name="dni" 
                                   class="form-control" 
                                   placeholder="12345678"
                                   value="<?= isset($formData['dni']) ? htmlspecialchars($formData['dni']) : '' ?>"
                                   required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   class="form-control" 
                                   placeholder="tu@email.com"
                                   value="<?= isset($formData['email']) ? htmlspecialchars($formData['email']) : '' ?>"
                                   required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombres" class="form-label">Nombres</label>
                            <input type="text" 
                                   id="nombres" 
                                   name="nombres" 
                                   class="form-control" 
                                   placeholder="Juan Carlos"
                                   value="<?= isset($formData['nombres']) ? htmlspecialchars($formData['nombres']) : '' ?>"
                                   required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="apellidos" class="form-label">Apellidos</label>
                            <input type="text" 
                                   id="apellidos" 
                                   name="apellidos" 
                                   class="form-control" 
                                   placeholder="Pérez González"
                                   value="<?= isset($formData['apellidos']) ? htmlspecialchars($formData['apellidos']) : '' ?>"
                                   required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" 
                                   id="telefono" 
                                   name="telefono" 
                                   class="form-control" 
                                   placeholder="+51 999 888 777"
                                   value="<?= isset($formData['telefono']) ? htmlspecialchars($formData['telefono']) : '' ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="carrera" class="form-label">Carrera</label>
                            <select id="carrera" name="carrera" class="form-control form-select" required>
                                <option value="">Selecciona tu carrera</option>
                                <option value="Ingeniería de Sistemas" <?= isset($formData['carrera']) && $formData['carrera'] === 'Ingeniería de Sistemas' ? 'selected' : '' ?>>Ingeniería de Sistemas</option>
                                <option value="Ingeniería Industrial" <?= isset($formData['carrera']) && $formData['carrera'] === 'Ingeniería Industrial' ? 'selected' : '' ?>>Ingeniería Industrial</option>
                                <option value="Administración" <?= isset($formData['carrera']) && $formData['carrera'] === 'Administración' ? 'selected' : '' ?>>Administración</option>
                                <option value="Contabilidad" <?= isset($formData['carrera']) && $formData['carrera'] === 'Contabilidad' ? 'selected' : '' ?>>Contabilidad</option>
                                <option value="Marketing" <?= isset($formData['carrera']) && $formData['carrera'] === 'Marketing' ? 'selected' : '' ?>>Marketing</option>
                                <option value="Psicología" <?= isset($formData['carrera']) && $formData['carrera'] === 'Psicología' ? 'selected' : '' ?>>Psicología</option>
                                <option value="Derecho" <?= isset($formData['carrera']) && $formData['carrera'] === 'Derecho' ? 'selected' : '' ?>>Derecho</option>
                                <option value="Otra" <?= isset($formData['carrera']) && $formData['carrera'] === 'Otra' ? 'selected' : '' ?>>Otra</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="anio_egreso" class="form-label">Año de Egreso</label>
                    <select id="anio_egreso" name="anio_egreso" class="form-control form-select" required>
                        <option value="">Selecciona tu año de egreso</option>
                        <?php for ($year = date('Y'); $year >= 1990; $year--): ?>
                            <option value="<?= $year ?>" <?= isset($formData['anio_egreso']) && $formData['anio_egreso'] == $year ? 'selected' : '' ?>>
                                <?= $year ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="form-control" 
                                   placeholder="Mínimo 6 caracteres"
                                   required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                            <input type="password" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   class="form-control" 
                                   placeholder="Repite tu contraseña"
                                   required>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100" data-original-text="Registrarse">
                    Registrarse
                </button>
            </form>
        </div>
        <div class="card-footer text-center">
            <p class="mb-0">
                ¿Ya tienes cuenta? 
                <a href="/login" class="text-primary">Inicia sesión aquí</a>
            </p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>