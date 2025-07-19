<?php
$title = 'Mensajes Enviados';
$pageTitle = 'Mensajes Enviados';
$pageDescription = 'Revisa los mensajes que has enviado';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Mensajes Enviados</h2>
    </div>
    <div>
        <a href="/mensajes/compose" class="btn btn-primary">
            ✉️ Nuevo Mensaje
        </a>
    </div>
</div>

<div class="row">
    <!-- Message Navigation -->
    <div class="col-md-3 mb-4">
        <div class="card">
            <div class="card-body">
                <nav class="nav flex-column">
                    <a href="/mensajes" class="nav-link">
                        📥 Bandeja de Entrada
                    </a>
                    <a href="/mensajes/sent" class="nav-link active">
                        📤 Enviados
                    </a>
                </nav>
            </div>
        </div>
    </div>
    
    <!-- Sent Messages List -->
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h3>Mensajes Enviados</h3>
            </div>
            <div class="card-body">
                <?php if (empty($messages)): ?>
                    <div class="text-center p-6">
                        <h5>No has enviado mensajes</h5>
                        <p class="text-muted">Aún no has enviado ningún mensaje</p>
                        <a href="/mensajes/compose" class="btn btn-primary">Enviar Primer Mensaje</a>
                    </div>
                <?php else: ?>
                    <div class="message-list">
                        <?php foreach ($messages as $message): ?>
                            <div class="message-item border-bottom py-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <strong>Para: <?= htmlspecialchars($message['receptor_email']) ?></strong>
                                            </div>
                                            <small class="text-muted">
                                                <?= date('d/m/Y H:i', strtotime($message['fecha_envio'])) ?>
                                            </small>
                                        </div>
                                        
                                        <?php if ($message['asunto']): ?>
                                            <h6 class="mb-2"><?= htmlspecialchars($message['asunto']) ?></h6>
                                        <?php endif; ?>
                                        
                                        <p class="mb-2 text-muted">
                                            <?= htmlspecialchars(substr($message['mensaje'], 0, 150)) ?>
                                            <?= strlen($message['mensaje']) > 150 ? '...' : '' ?>
                                        </p>
                                        
                                        <div class="message-actions">
                                            <a href="/mensajes/conversation/<?= $message['receptor_id'] ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                Ver Conversación
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.nav-link.active {
    background-color: var(--primary-100);
    color: var(--primary-700);
    border-radius: var(--border-radius-md);
}

.message-actions {
    display: flex;
    gap: var(--spacing-2);
}
</style>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>