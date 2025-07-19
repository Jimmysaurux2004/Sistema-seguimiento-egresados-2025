<?php
$title = 'Nuevo Evento';
$pageTitle = 'Crear Nuevo Evento';
$pageDescription = 'Registra un nuevo evento en el sistema';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Crear Nuevo Evento</h2>
        <p class="text-muted">Registra un nuevo evento en el sistema</p>
    </div>
    <div>
        <a href="/admin/eventos" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver a la Lista
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3>Información del Evento</h3>
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

                <form method="POST" action="/admin/eventos" data-validate>
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="nombre" class="form-label">Nombre del Evento *</label>
                                <input type="text"
                                    id="nombre"
                                    name="nombre"
                                    class="form-control"
                                    placeholder="Ej: Taller de Emprendimiento"
                                    value="<?= isset($formData['nombre']) ? htmlspecialchars($formData['nombre']) : '' ?>"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tipo" class="form-label">Tipo de Evento *</label>
                                <select id="tipo" name="tipo" class="form-control form-select" required>
                                    <option value="">Selecciona el tipo</option>
                                    <option value="capacitacion" <?= isset($formData['tipo']) && $formData['tipo'] === 'capacitacion' ? 'selected' : '' ?>>Capacitación</option>
                                    <option value="evento" <?= isset($formData['tipo']) && $formData['tipo'] === 'evento' ? 'selected' : '' ?>>Evento</option>
                                    <option value="charla" <?= isset($formData['tipo']) && $formData['tipo'] === 'charla' ? 'selected' : '' ?>>Charla</option>
                                    <option value="reunion" <?= isset($formData['tipo']) && $formData['tipo'] === 'reunion' ? 'selected' : '' ?>>Reunión</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcion" class="form-label">Descripción *</label>
                        <textarea id="descripcion"
                            name="descripcion"
                            class="form-control"
                            rows="4"
                            placeholder="Describe el evento, objetivos, contenido, etc."
                            required><?= isset($formData['descripcion']) ? htmlspecialchars($formData['descripcion']) : '' ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha" class="form-label">Fecha *</label>
                                <input type="date"
                                    id="fecha"
                                    name="fecha"
                                    class="form-control"
                                    value="<?= isset($formData['fecha']) ? $formData['fecha'] : date('Y-m-d') ?>"
                                    min="<?= date('Y-m-d') ?>"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hora" class="form-label">Hora</label>
                                <input type="time"
                                    id="hora"
                                    name="hora"
                                    class="form-control"
                                    value="<?= isset($formData['hora']) ? $formData['hora'] : '' ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="lugar" class="form-label">Lugar</label>
                                <input type="text"
                                    id="lugar"
                                    name="lugar"
                                    class="form-control"
                                    placeholder="Ej: Auditorio Principal, Sala de Conferencias"
                                    value="<?= isset($formData['lugar']) ? htmlspecialchars($formData['lugar']) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="capacidad_maxima" class="form-label">Capacidad Máxima</label>
                                <input type="number"
                                    id="capacidad_maxima"
                                    name="capacidad_maxima"
                                    class="form-control"
                                    placeholder="0 = Sin límite"
                                    min="0"
                                    value="<?= isset($formData['capacidad_maxima']) ? $formData['capacidad_maxima'] : '0' ?>">
                                <small class="text-muted">Deja en 0 para capacidad ilimitada</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="/admin/eventos" class="btn btn-secondary">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary" data-original-text="Crear Evento">
                            <i class="fas fa-save"></i> Crear Evento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Información Importante</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle"></i> Tipos de Eventos:</h6>
                    <ul class="mb-0">
                        <li><strong>Capacitación:</strong> Talleres, cursos, formación</li>
                        <li><strong>Evento:</strong> Celebraciones, ceremonias</li>
                        <li><strong>Charla:</strong> Conferencias, presentaciones</li>
                        <li><strong>Reunión:</strong> Juntas, asambleas</li>
                    </ul>
                </div>

                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle"></i> Notas:</h6>
                    <ul class="mb-0">
                        <li>Los campos marcados con * son obligatorios</li>
                        <li>La fecha no puede ser anterior a hoy</li>
                        <li>Se enviará notificación a todos los egresados</li>
                        <li>La capacidad máxima es opcional</li>
                    </ul>
                </div>

                <div class="alert alert-success">
                    <h6><i class="fas fa-check-circle"></i> Beneficios:</h6>
                    <ul class="mb-0">
                        <li>Notificación automática a egresados</li>
                        <li>Sistema de inscripciones integrado</li>
                        <li>Gestión de capacidad y cupos</li>
                        <li>Reportes y estadísticas</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: var(--font-weight-semibold);
        color: var(--gray-700);
        margin-bottom: 0.5rem;
    }

    .form-control,
    .form-select {
        border-radius: var(--border-radius-md);
        border: 1px solid var(--gray-300);
        padding: 0.75rem;
        transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-500);
        box-shadow: 0 0 0 0.2rem rgba(var(--primary-500-rgb), 0.25);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: var(--border-radius-md);
        font-weight: var(--font-weight-medium);
        transition: all var(--transition-fast);
    }

    .alert {
        border-radius: var(--border-radius-md);
        border: none;
        margin-bottom: 1rem;
    }

    .alert-info {
        background-color: rgba(var(--info-500-rgb), 0.1);
        color: var(--info-700);
    }

    .alert-warning {
        background-color: rgba(var(--warning-500-rgb), 0.1);
        color: var(--warning-700);
    }

    .alert-success {
        background-color: rgba(var(--success-500-rgb), 0.1);
        color: var(--success-700);
    }

    .alert h6 {
        margin-bottom: 0.5rem;
        font-weight: var(--font-weight-semibold);
    }

    .alert ul {
        margin-bottom: 0;
        padding-left: 1.25rem;
    }

    .alert li {
        margin-bottom: 0.25rem;
    }

    .alert li:last-child {
        margin-bottom: 0;
    }
</style>

<script>
    // Form validation
    document.querySelector('form[data-validate]').addEventListener('submit', function(e) {
        const nombre = document.getElementById('nombre').value;
        const descripcion = document.getElementById('descripcion').value;
        const fecha = document.getElementById('fecha').value;
        const tipo = document.getElementById('tipo').value;

        let isValid = true;
        let errorMessage = '';

        // Validate required fields
        if (!nombre.trim()) {
            errorMessage += 'El nombre del evento es obligatorio\n';
            isValid = false;
        }

        if (!descripcion.trim()) {
            errorMessage += 'La descripción es obligatoria\n';
            isValid = false;
        }

        if (!fecha) {
            errorMessage += 'La fecha es obligatoria\n';
            isValid = false;
        } else {
            const selectedDate = new Date(fecha);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (selectedDate < today) {
                errorMessage += 'La fecha no puede ser anterior a hoy\n';
                isValid = false;
            }
        }

        if (!tipo) {
            errorMessage += 'El tipo de evento es obligatorio\n';
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            alert('Por favor corrige los siguientes errores:\n\n' + errorMessage);
        }
    });

    // Auto-fill date with today if empty
    document.getElementById('fecha').addEventListener('focus', function() {
        if (!this.value) {
            this.value = new Date().toISOString().split('T')[0];
        }
    });

    // Update form based on event type
    document.getElementById('tipo').addEventListener('change', function() {
        const tipo = this.value;
        const nombreInput = document.getElementById('nombre');
        const descripcionInput = document.getElementById('descripcion');

        // Auto-suggest based on type
        if (tipo && !nombreInput.value) {
            const suggestions = {
                'capacitacion': 'Taller de ',
                'evento': 'Evento de ',
                'charla': 'Charla sobre ',
                'reunion': 'Reunión de '
            };

            nombreInput.placeholder = suggestions[tipo] + '...';
        }
    });
</script>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>