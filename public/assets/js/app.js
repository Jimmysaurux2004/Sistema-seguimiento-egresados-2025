/**
 * Graduate Management System - Vanilla JavaScript
 * NO FRAMEWORKS - Pure JavaScript for UI interactions, form validation, and AJAX
 */

// Application namespace - Pure JavaScript, no frameworks
const App = {
    // Configuration
    config: {
        pollInterval: 30000, // 30 seconds
        ajaxTimeout: 10000,  // 10 seconds
        baseUrl: window.location.origin
    },
    
    // State management
    state: {
        isPolling: false,
        notifications: [],
        unreadMessages: 0
    },
    
    // Initialize application
    init() {
        console.log('Initializing Graduate Management System - Pure JavaScript');
        this.setupEventListeners();
        this.startPolling();
        this.initializeTooltips();
        this.initializeModals();
        console.log('System ready - No frameworks used');
    },
    
    // Setup global event listeners
    setupEventListeners() {
        // Form validation
        document.addEventListener('submit', this.handleFormSubmission.bind(this));
        
        // Navigation
        document.addEventListener('click', this.handleNavigation.bind(this));
        
        // Mobile sidebar toggle
        const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', this.toggleSidebar.bind(this));
        }
        
        // Search functionality
        const searchInput = document.querySelector('[data-search]');
        if (searchInput) {
            searchInput.addEventListener('input', this.debounce(this.handleSearch.bind(this), 300));
        }
        
        // Auto-dismiss alerts
        this.setupAlertDismissal();
    },
    
    // Handle form submissions with validation
    handleFormSubmission(event) {
        const form = event.target;
        
        if (!form.matches('form[data-validate]')) {
            return;
        }
        
        const isValid = this.validateForm(form);
        
        if (!isValid) {
            event.preventDefault();
            return false;
        }
        
        // Show loading state
        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            this.setButtonLoading(submitButton, true);
        }
    },
    
    // Form validation
    validateForm(form) {
        let isValid = true;
        const errors = [];
        
        // Clear previous errors
        this.clearFormErrors(form);
        
        // Validate required fields
        const requiredFields = form.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                this.addFieldError(field, 'Este campo es requerido');
                isValid = false;
            }
        });
        
        // Validate email fields
        const emailFields = form.querySelectorAll('input[type="email"]');
        emailFields.forEach(field => {
            if (field.value && !this.isValidEmail(field.value)) {
                this.addFieldError(field, 'Ingrese un email válido');
                isValid = false;
            }
        });
        
        // Validate password confirmation
        const passwordField = form.querySelector('input[name="password"]');
        const confirmPasswordField = form.querySelector('input[name="confirm_password"]');
        
        if (passwordField && confirmPasswordField && passwordField.value !== confirmPasswordField.value) {
            this.addFieldError(confirmPasswordField, 'Las contraseñas no coinciden');
            isValid = false;
        }
        
        // Validate phone numbers
        const phoneFields = form.querySelectorAll('input[type="tel"]');
        phoneFields.forEach(field => {
            if (field.value && !this.isValidPhone(field.value)) {
                this.addFieldError(field, 'Ingrese un teléfono válido');
                isValid = false;
            }
        });
        
        // Validate dates
        const dateFields = form.querySelectorAll('input[type="date"]');
        dateFields.forEach(field => {
            if (field.value && field.hasAttribute('data-min-date')) {
                const minDate = new Date(field.getAttribute('data-min-date'));
                const inputDate = new Date(field.value);
                
                if (inputDate < minDate) {
                    this.addFieldError(field, 'La fecha no puede ser anterior al mínimo permitido');
                    isValid = false;
                }
            }
        });
        
        return isValid;
    },
    
    // Email validation
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    },
    
    // Phone validation
    isValidPhone(phone) {
        const phoneRegex = /^[0-9+\-\s()]+$/;
        return phoneRegex.test(phone) && phone.replace(/[^0-9]/g, '').length >= 7;
    },
    
    // Add error to form field
    addFieldError(field, message) {
        field.classList.add('is-invalid');
        
        // Remove existing error
        const existingError = field.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
        
        // Add new error message
        const errorElement = document.createElement('div');
        errorElement.className = 'field-error text-danger mt-1';
        errorElement.style.fontSize = '0.875rem';
        errorElement.textContent = message;
        
        field.parentNode.appendChild(errorElement);
    },
    
    // Clear all form errors
    clearFormErrors(form) {
        const invalidFields = form.querySelectorAll('.is-invalid');
        invalidFields.forEach(field => {
            field.classList.remove('is-invalid');
        });
        
        const errorMessages = form.querySelectorAll('.field-error');
        errorMessages.forEach(error => {
            error.remove();
        });
    },
    
    // Handle navigation clicks
    handleNavigation(event) {
        const target = event.target.closest('[data-action]');
        if (!target) return;
        
        const action = target.getAttribute('data-action');
        
        switch (action) {
            case 'mark-notification-read':
                this.markNotificationAsRead(target);
                break;
            case 'mark-message-read':
                this.markMessageAsRead(target);
                break;
            case 'toggle-status':
                this.toggleStatus(target);
                break;
            case 'confirm-delete':
                this.confirmDelete(target);
                break;
        }
    },
    
    // Toggle sidebar (mobile)
    toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            sidebar.classList.toggle('show');
        }
    },
    
    // Handle search input
    handleSearch(event) {
        const query = event.target.value.trim();
        const searchUrl = event.target.getAttribute('data-search-url');
        
        if (!searchUrl) return;
        
        if (query.length >= 2) {
            window.location.href = `${searchUrl}?search=${encodeURIComponent(query)}`;
        }
    },
    
    // Start polling for notifications and messages
    startPolling() {
        if (this.state.isPolling) return;
        
        this.state.isPolling = true;
        this.pollNotifications();
        this.pollMessages();
        
        // Set up intervals
        setInterval(() => {
            this.pollNotifications();
        }, this.config.pollInterval);
        
        setInterval(() => {
            this.pollMessages();
        }, this.config.pollInterval);
    },
    
    // Poll for notifications
    async pollNotifications() {
        try {
            const response = await fetch('/api/notifications', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                },
                timeout: this.config.ajaxTimeout
            });
            
            if (response.ok) {
                const data = await response.json();
                this.updateNotifications(data.notifications, data.unread_count);
            }
        } catch (error) {
            console.warn('Failed to fetch notifications:', error);
        }
    },
    
    // Poll for messages
    async pollMessages() {
        try {
            const response = await fetch('/api/messages/unread-count', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                },
                timeout: this.config.ajaxTimeout
            });
            
            if (response.ok) {
                const data = await response.json();
                this.updateMessageCount(data.unread_count);
            }
        } catch (error) {
            console.warn('Failed to fetch message count:', error);
        }
    },
    
    // Update notifications UI
    updateNotifications(notifications, unreadCount) {
        this.state.notifications = notifications;
        
        // Update notification badge
        const notificationBadge = document.querySelector('[data-notification-count]');
        if (notificationBadge) {
            if (unreadCount > 0) {
                notificationBadge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                notificationBadge.style.display = 'flex';
            } else {
                notificationBadge.style.display = 'none';
            }
        }
        
        // Update notification dropdown
        const notificationDropdown = document.querySelector('[data-notification-list]');
        if (notificationDropdown) {
            this.renderNotifications(notificationDropdown, notifications);
        }
    },
    
    // Update message count
    updateMessageCount(unreadCount) {
        this.state.unreadMessages = unreadCount;
        
        const messageBadge = document.querySelector('[data-message-count]');
        if (messageBadge) {
            if (unreadCount > 0) {
                messageBadge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                messageBadge.style.display = 'flex';
            } else {
                messageBadge.style.display = 'none';
            }
        }
    },
    
    // Render notifications in dropdown
    renderNotifications(container, notifications) {
        if (notifications.length === 0) {
            container.innerHTML = '<div class="p-4 text-center text-muted">No hay notificaciones</div>';
            return;
        }
        
        const html = notifications.map(notification => `
            <div class="notification-item p-3 border-bottom ${notification.leida ? '' : 'bg-primary-50'}" 
                 data-notification-id="${notification.id}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${this.escapeHtml(notification.titulo)}</h6>
                        <p class="mb-1 text-muted small">${this.escapeHtml(notification.mensaje)}</p>
                        <small class="text-muted">${this.formatDate(notification.fecha_creacion)}</small>
                    </div>
                    ${!notification.leida ? `
                        <button class="btn btn-sm btn-outline-primary" 
                                data-action="mark-notification-read" 
                                data-notification-id="${notification.id}">
                            Marcar como leída
                        </button>
                    ` : ''}
                </div>
            </div>
        `).join('');
        
        container.innerHTML = html;
    },
    
    // Mark notification as read
    async markNotificationAsRead(element) {
        const notificationId = element.getAttribute('data-notification-id');
        
        try {
            const response = await fetch('/api/notifications/mark-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `notification_id=${notificationId}`
            });
            
            if (response.ok) {
                // Update UI
                const notificationItem = element.closest('[data-notification-id]');
                if (notificationItem) {
                    notificationItem.classList.remove('bg-primary-50');
                    element.remove();
                }
                
                // Refresh notifications
                this.pollNotifications();
            }
        } catch (error) {
            console.error('Failed to mark notification as read:', error);
            this.showAlert('Error al marcar notificación como leída', 'danger');
        }
    },
    
    // Mark message as read
    async markMessageAsRead(element) {
        const messageId = element.getAttribute('data-message-id');
        
        try {
            const response = await fetch('/mensajes/mark-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `message_id=${messageId}`
            });
            
            if (response.ok) {
                // Update UI
                const messageItem = element.closest('.message-item');
                if (messageItem) {
                    messageItem.classList.remove('unread');
                }
                
                // Refresh message count
                this.pollMessages();
            }
        } catch (error) {
            console.error('Failed to mark message as read:', error);
        }
    },
    
    // Toggle status (generic)
    async toggleStatus(element) {
        const confirmMessage = element.getAttribute('data-confirm') || '¿Está seguro?';
        
        if (!confirm(confirmMessage)) {
            return;
        }
        
        const url = element.getAttribute('data-url');
        const formData = new FormData();
        
        // Add any data attributes as form data
        Array.from(element.attributes).forEach(attr => {
            if (attr.name.startsWith('data-') && attr.name !== 'data-action' && attr.name !== 'data-url' && attr.name !== 'data-confirm') {
                const key = attr.name.replace('data-', '').replace(/-/g, '_');
                formData.append(key, attr.value);
            }
        });
        
        try {
            this.setButtonLoading(element, true);
            
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });
            
            if (response.ok) {
                window.location.reload();
            } else {
                this.showAlert('Error al actualizar el estado', 'danger');
            }
        } catch (error) {
            console.error('Failed to toggle status:', error);
            this.showAlert('Error de conexión', 'danger');
        } finally {
            this.setButtonLoading(element, false);
        }
    },
    
    // Confirm delete action
    confirmDelete(element) {
        const confirmMessage = element.getAttribute('data-confirm') || '¿Está seguro de que desea eliminar este elemento?';
        
        if (confirm(confirmMessage)) {
            const form = element.closest('form');
            if (form) {
                form.submit();
            } else {
                const url = element.getAttribute('href');
                if (url) {
                    window.location.href = url;
                }
            }
        }
    },
    
    // Set button loading state
    setButtonLoading(button, isLoading) {
        if (isLoading) {
            button.disabled = true;
            button.innerHTML = '<span class="spinner"></span> Cargando...';
        } else {
            button.disabled = false;
            const originalText = button.getAttribute('data-original-text') || 'Enviar';
            button.innerHTML = originalText;
        }
    },
    
    // Show alert message
    showAlert(message, type = 'info') {
        const alertContainer = document.getElementById('alert-container') || this.createAlertContainer();
        
        const alertElement = document.createElement('div');
        alertElement.className = `alert alert-${type} alert-dismissible`;
        alertElement.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-dismiss="alert"></button>
        `;
        
        alertContainer.appendChild(alertElement);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (alertElement.parentNode) {
                alertElement.remove();
            }
        }, 5000);
    },
    
    // Create alert container if it doesn't exist
    createAlertContainer() {
        const container = document.createElement('div');
        container.id = 'alert-container';
        container.style.position = 'fixed';
        container.style.top = '20px';
        container.style.right = '20px';
        container.style.zIndex = '9999';
        container.style.minWidth = '300px';
        
        document.body.appendChild(container);
        return container;
    },
    
    // Setup alert dismissal
    setupAlertDismissal() {
        document.addEventListener('click', (event) => {
            if (event.target.matches('[data-dismiss="alert"]')) {
                const alert = event.target.closest('.alert');
                if (alert) {
                    alert.remove();
                }
            }
        });
    },
    
    // Initialize tooltips
    initializeTooltips() {
        const tooltipElements = document.querySelectorAll('[data-tooltip]');
        
        tooltipElements.forEach(element => {
            element.addEventListener('mouseenter', (event) => {
                this.showTooltip(event.target);
            });
            
            element.addEventListener('mouseleave', (event) => {
                this.hideTooltip(event.target);
            });
        });
    },
    
    // Show tooltip
    showTooltip(element) {
        const text = element.getAttribute('data-tooltip');
        if (!text) return;
        
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip-popup';
        tooltip.textContent = text;
        tooltip.style.position = 'absolute';
        tooltip.style.background = 'rgba(0, 0, 0, 0.8)';
        tooltip.style.color = 'white';
        tooltip.style.padding = '8px 12px';
        tooltip.style.borderRadius = '4px';
        tooltip.style.fontSize = '14px';
        tooltip.style.zIndex = '9999';
        tooltip.style.pointerEvents = 'none';
        
        document.body.appendChild(tooltip);
        
        const rect = element.getBoundingClientRect();
        tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';
        
        element._tooltip = tooltip;
    },
    
    // Hide tooltip
    hideTooltip(element) {
        if (element._tooltip) {
            element._tooltip.remove();
            delete element._tooltip;
        }
    },
    
    // Initialize modals
    initializeModals() {
        document.addEventListener('click', (event) => {
            const trigger = event.target.closest('[data-modal-target]');
            if (trigger) {
                const targetId = trigger.getAttribute('data-modal-target');
                const modal = document.getElementById(targetId);
                if (modal) {
                    this.showModal(modal);
                }
            }
            
            const closeButton = event.target.closest('[data-modal-close]');
            if (closeButton) {
                const modal = closeButton.closest('.modal');
                if (modal) {
                    this.hideModal(modal);
                }
            }
            
            // Close modal when clicking outside
            if (event.target.classList.contains('modal')) {
                this.hideModal(event.target);
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                const openModal = document.querySelector('.modal.show');
                if (openModal) {
                    this.hideModal(openModal);
                }
            }
        });
    },
    
    // Show modal
    showModal(modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    },
    
    // Hide modal
    hideModal(modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
    },
    
    // Utility functions
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },
    
    formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffInMinutes = Math.floor((now - date) / (1000 * 60));
        
        if (diffInMinutes < 1) return 'Ahora';
        if (diffInMinutes < 60) return `Hace ${diffInMinutes} minutos`;
        if (diffInMinutes < 1440) return `Hace ${Math.floor(diffInMinutes / 60)} horas`;
        if (diffInMinutes < 10080) return `Hace ${Math.floor(diffInMinutes / 1440)} días`;
        
        return date.toLocaleDateString('es-ES');
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    App.init();
});

// Export for use in other scripts
window.App = App;