<?php
$title = 'Conversación';
$pageTitle = 'Conversación con ' . htmlspecialchars($other_user['email']);
$pageDescription = 'Historial de mensajes';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Conversación</h2>
        <p class="text-muted">Con: <?= htmlspecialchars($other_user['email']) ?></p>
    </div>
    <div>
        <a href="/mensajes" class="btn btn-outline-secondary">Volver a Mensajes</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Messages Thread -->
        <div class="card">
            <div class="card-header">
                <h4>Historial de Mensajes</h4>
            </div>
            <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                <?php if (empty($messages)): ?>
                    <div class="text-center p-4">
                        <p class="text-muted">No hay mensajes en esta conversación</p>
                    </div>
                <?php else: ?>
                    <div class="conversation-thread">
                        <?php foreach ($messages as $message): ?>
                            <?php 
                            $isFromCurrentUser = $message['emisor_id'] == $_SESSION['user_id'];
                            $messageClass = $isFromCurrentUser ? 'message-sent' : 'message-received';
                            ?>
                            <div class="message-bubble <?= $messageClass ?> mb-3">
                                <div class="message-header">
                                    <strong><?= htmlspecialchars($message['emisor_email']) ?></strong>
                                    <small class="text-muted ml-2">
                                        <?= date('d/m/Y H:i', strtotime($message['fecha_envio'])) ?>
                                    </small>
                                </div>
                                
                                <?php if ($message['asunto']): ?>
                                    <div class="message-subject">
                                        <strong><?= htmlspecialchars($message['asunto']) ?></strong>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="message-content">
                                    <?= nl2br(htmlspecialchars($message['mensaje'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Quick Reply -->
        <div class="card">
            <div class="card-header">
                <h5>Respuesta Rápida</h5>
            </div>
            <div class="card-body">
                <form id="quick-reply-form">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="receptor_id" value="<?= $other_user['id'] ?>">
                    
                    <div class="form-group">
                        <label for="quick-mensaje" class="form-label">Mensaje</label>
                        <textarea id="quick-mensaje" 
                                  name="mensaje" 
                                  class="form-control" 
                                  rows="4" 
                                  placeholder="Escribe tu respuesta..."
                                  required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        Enviar Respuesta
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Conversation Info -->
        <div class="card mt-3">
            <div class="card-header">
                <h6>Información</h6>
            </div>
            <div class="card-body">
                <p><strong>Participante:</strong><br><?= htmlspecialchars($other_user['email']) ?></p>
                <p><strong>Total de mensajes:</strong><br><?= count($messages) ?></p>
                <?php if (!empty($messages)): ?>
                    <p><strong>Último mensaje:</strong><br>
                        <?= date('d/m/Y H:i', strtotime(end($messages)['fecha_envio'])) ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.conversation-thread {
    display: flex;
    flex-direction: column;
}

.message-bubble {
    max-width: 80%;
    padding: var(--spacing-3);
    border-radius: var(--border-radius-lg);
    margin-bottom: var(--spacing-3);
}

.message-sent {
    align-self: flex-end;
    background-color: var(--primary-100);
    border: 1px solid var(--primary-200);
}

.message-received {
    align-self: flex-start;
    background-color: var(--gray-100);
    border: 1px solid var(--gray-200);
}

.message-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-2);
    font-size: 0.875rem;
}

.message-subject {
    margin-bottom: var(--spacing-2);
    font-size: 0.9rem;
    color: var(--gray-700);
}

.message-content {
    line-height: var(--line-height-relaxed);
}
</style>

<script>
document.getElementById('quick-reply-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const messageTextarea = this.querySelector('#quick-mensaje');
    
    try {
        submitButton.disabled = true;
        submitButton.textContent = 'Enviando...';
        
        const response = await fetch('/mensajes/reply', {
            method: 'POST',
            body: formData
        });
        
        if (response.ok) {
            messageTextarea.value = '';
            location.reload(); // Reload to show new message
        } else {
            alert('Error al enviar el mensaje');
        }
    } catch (error) {
        alert('Error de conexión');
    } finally {
        submitButton.disabled = false;
        submitButton.textContent = 'Enviar Respuesta';
    }
});
</script>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>