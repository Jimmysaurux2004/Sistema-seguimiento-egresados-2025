<?php

/**
 * Evento Controller
 * Handles events and institutional activities
 */

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/models/Evento.php';
require_once APP_PATH . '/models/Notificacion.php';

class EventoController extends Controller
{
    private $eventoModel;
    private $notificacionModel;

    public function __construct()
    {
        parent::__construct();
        $this->eventoModel = new Evento();
        $this->notificacionModel = new Notificacion();
    }

    /**
     * List active events
     */
    public function index()
    {
        $events = $this->eventoModel->getActive();

        $this->view('evento/index', [
            'events' => $events
        ]);
    }

    /**
     * Show event details (public)
     */
    public function showPublic($id)
    {
        $event = $this->eventoModel->find($id);

        if (!$event || !$event['activo']) {
            $this->redirect('/eventos');
        }

        $this->view('evento/show', [
            'event' => $event
        ]);
    }

    /**
     * List all events (admin only)
     */
    public function list()
    {
        $this->requireRole('admin');

        // Obtener parámetros de filtro
        $search = $_GET['search'] ?? '';
        $tipo = $_GET['tipo'] ?? '';
        $estado = $_GET['estado'] ?? '';
        $fecha_desde = $_GET['fecha_desde'] ?? '';
        $fecha_hasta = $_GET['fecha_hasta'] ?? '';
        $page = max(1, intval($_GET['page'] ?? 1));
        $per_page = max(10, min(50, intval($_GET['per_page'] ?? 10)));

        // Aplicar filtros
        $events = $this->eventoModel->getFilteredEvents([
            'search' => $search,
            'tipo' => $tipo,
            'estado' => $estado,
            'fecha_desde' => $fecha_desde,
            'fecha_hasta' => $fecha_hasta,
            'page' => $page,
            'per_page' => $per_page
        ]);

        $stats = $this->eventoModel->getStats();

        // Calcular paginación
        $total_events = $this->eventoModel->getFilteredCount([
            'search' => $search,
            'tipo' => $tipo,
            'estado' => $estado,
            'fecha_desde' => $fecha_desde,
            'fecha_hasta' => $fecha_hasta
        ]);

        $total_pages = ceil($total_events / $per_page);

        $this->view('admin/eventos/list', [
            'events' => $events,
            'stats' => $stats,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_events' => $total_events,
            'per_page' => $per_page
        ]);
    }

    /**
     * Show create form (admin only)
     */
    public function create()
    {
        $this->requireRole('admin');

        $this->view('admin/eventos/create', [
            'csrf_token' => $this->generateCsrf()
        ]);
    }

    /**
     * Store new event (admin only)
     */
    public function store()
    {
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/eventos/create');
        }

        $csrf_token = $_POST['csrf_token'] ?? '';

        // Validate CSRF token
        if (!$this->validateCsrf($csrf_token)) {
            $this->redirect('/admin/eventos/create');
        }

        $nombre = $_POST['nombre'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $fecha = $_POST['fecha'] ?? '';
        $hora = $_POST['hora'] ?? '';
        $lugar = $_POST['lugar'] ?? '';
        $tipo = $_POST['tipo'] ?? 'evento';
        $capacidad_maxima = $_POST['capacidad_maxima'] ?? 0;

        // Validate input
        $errors = [];

        if (empty($nombre)) {
            $errors[] = 'Nombre es requerido';
        }

        if (empty($fecha)) {
            $errors[] = 'Fecha es requerida';
        } elseif (strtotime($fecha) < strtotime(date('Y-m-d'))) {
            $errors[] = 'La fecha no puede ser anterior a hoy';
        }

        if (!in_array($tipo, ['capacitacion', 'evento', 'charla', 'reunion'])) {
            $errors[] = 'Tipo de evento inválido';
        }

        if (!empty($errors)) {
            $this->view('admin/eventos/create', [
                'errors' => $errors,
                'csrf_token' => $this->generateCsrf(),
                'formData' => $_POST
            ]);
            return;
        }

        try {
            $eventoData = [
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'fecha' => $fecha,
                'hora' => $hora,
                'lugar' => $lugar,
                'tipo' => $tipo,
                'capacidad_maxima' => $capacidad_maxima
            ];

            $eventoId = $this->eventoModel->create($eventoData);

            // Create notification for all users
            $this->notificacionModel->createForAllUsers(
                'Nuevo evento: ' . $nombre,
                "Se ha programado un nuevo evento para el $fecha. ¡No te lo pierdas!",
                'evento'
            );

            $this->redirect('/admin/eventos');
        } catch (Exception $e) {
            $this->view('admin/eventos/create', [
                'error' => 'Error al crear el evento',
                'csrf_token' => $this->generateCsrf(),
                'formData' => $_POST
            ]);
        }
    }

    /**
     * Show edit form (admin only)
     */
    public function edit($id)
    {
        $this->requireRole('admin');

        $event = $this->eventoModel->find($id);

        if (!$event) {
            $this->redirect('/admin/eventos');
        }

        $this->view('admin/eventos/edit', [
            'event' => $event,
            'csrf_token' => $this->generateCsrf()
        ]);
    }

    /**
     * Update event (admin only)
     */
    public function update($id)
    {
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/eventos');
        }

        $event = $this->eventoModel->find($id);

        if (!$event) {
            $this->redirect('/admin/eventos');
        }

        $csrf_token = $_POST['csrf_token'] ?? '';

        // Validate CSRF token
        if (!$this->validateCsrf($csrf_token)) {
            $this->redirect('/admin/eventos/edit/' . $id);
        }

        $nombre = $_POST['nombre'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $fecha = $_POST['fecha'] ?? '';
        $hora = $_POST['hora'] ?? '';
        $lugar = $_POST['lugar'] ?? '';
        $tipo = $_POST['tipo'] ?? 'evento';
        $capacidad_maxima = $_POST['capacidad_maxima'] ?? 0;

        // Validate input
        $errors = [];

        if (empty($nombre)) {
            $errors[] = 'Nombre es requerido';
        }

        if (empty($fecha)) {
            $errors[] = 'Fecha es requerida';
        }

        if (!in_array($tipo, ['capacitacion', 'evento', 'charla', 'reunion'])) {
            $errors[] = 'Tipo de evento inválido';
        }

        if (!empty($errors)) {
            $this->view('admin/eventos/edit', [
                'event' => $event,
                'errors' => $errors,
                'csrf_token' => $this->generateCsrf()
            ]);
            return;
        }

        try {
            $eventoData = [
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'fecha' => $fecha,
                'hora' => $hora,
                'lugar' => $lugar,
                'tipo' => $tipo,
                'capacidad_maxima' => $capacidad_maxima
            ];

            $this->eventoModel->update($id, $eventoData);

            $this->redirect('/admin/eventos');
        } catch (Exception $e) {
            $this->view('admin/eventos/edit', [
                'event' => $event,
                'error' => 'Error al actualizar el evento',
                'csrf_token' => $this->generateCsrf()
            ]);
        }
    }

    /**
     * Toggle event status (admin only)
     */
    public function toggleStatus()
    {
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
            return;
        }

        $eventoId = $_POST['evento_id'] ?? '';

        if (empty($eventoId)) {
            $this->json(['error' => 'ID de evento requerido'], 400);
            return;
        }

        try {
            $this->eventoModel->toggleStatus($eventoId);
            $this->json(['success' => true]);
        } catch (Exception $e) {
            $this->json(['error' => 'Error al actualizar'], 500);
        }
    }

    /**
     * Show event details (admin only)
     */
    public function show($id)
    {
        $this->requireRole('admin');

        $event = $this->eventoModel->getWithRegistrationCount($id);

        if (!$event) {
            $this->redirect('/admin/eventos');
        }

        $this->view('admin/eventos/show', [
            'event' => $event
        ]);
    }

    /**
     * Delete event (admin only)
     */
    public function delete($id)
    {
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->redirect('/admin/eventos');
        }

        try {
            $this->eventoModel->deleteEvent($id);
            http_response_code(200);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Error al eliminar el evento']);
        }
    }
}
