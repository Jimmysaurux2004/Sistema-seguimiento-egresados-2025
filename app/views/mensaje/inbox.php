<?php
$title = 'Mensajes';
$pageTitle = 'Bandeja de Entrada';
$pageDescription = 'Gestiona tus mensajes y conversaciones';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Mensajes</h2>
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
                    <a href="/mensajes" class="nav-link active">
                        📥 Bandeja de Entrada
                    </a>
                    <a href="/mensajes/sent" class="nav-link">
                        📤 Enviados
                    </a>
                </nav>
            </div>
        </div>
        
        <!-- Recent Conversations -->
        <?php if (!empty($conversations)): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h6>Conversaciones Recientes</h6>
                </div>
                <div class="card-body p-0">
                    <?php foreach ($conversations as $conversation): ?>
                        <a href="/mensajes/conversation/<?= $conversation['other_user_id'] ?>" 
                           class="d-block p-3 border-bottom text-decoration-none">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= htmlspecialchars($conversation['other_user_email']) ?></strong>
                                    <br><small class="text-muted">
                                        <?= date('d/m/Y', strtotime($conversation['ultima_comunicacion'])) ?>
                                    </small>
                                </div>
                                <?php if ($conversation['mensajes_no_leidos'] > 0): ?>
                                    <span class="badge badge-primary">
                                        <?= $conversation['mensajes_no_leidos'] ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Messages List -->
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h3>Bandeja de Entrada</h3>
            </div>
            <div class="card-body">
                <?php if (empty($messages)): ?>
                    <div class="text-center p-6">
                        <h5>No tienes mensajes</h5>
                        <p class="text-muted">Tu bandeja de entrada está vacía</p>
                        <a href="/mensajes/compose" class="btn btn-primary">Enviar Primer Mensaje</a>
                    </div>
                <?php else: ?>
                    <div class="message-list">
                        <?php foreach ($messages as $message): ?>
                            <div class="message-item <?= !$message['leido'] ? 'unread' : '' ?> border-bottom py-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong><?= htmlspecialchars($message['emisor_email']) ?></strong>
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
                                            <a href="/mensajes/conversation/<?= $message['emisor_id'] ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                Responder
                                            </a>
                                            <?php if (!$message['leido']): ?>
                                                <button class="btn btn-sm btn-outline-secondary" 
                                                        data-action="mark-message-read" 
                                                        data-message-id="<?= $message['id'] ?>">
                                                    Marcar como leído
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <?php if (!$message['leido']): ?>
                                        <div class="unread-indicator">
                                            <span class="badge badge-primary">Nuevo</span>
                                        </div>
                                    <?php endif; ?>
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
.message-item.unread {
    background-color: var(--primary-50);
    border-left: 4px solid var(--primary-500);
    padding-left: calc(var(--spacing-3) - 4px);
}

.message-actions {
    display: flex;
    gap: var(--spacing-2);
}

.unread-indicator {
    margin-left: var(--spacing-3);
}

.nav-link.active {
    background-color: var(--primary-100);
    color: var(--primary-700);
    border-radius: var(--border-radius-md);
}
</style>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>