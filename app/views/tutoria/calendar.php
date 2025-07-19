<?php
$title = 'Calendario de Tutorías';
$pageTitle = 'Calendario de Tutorías';
$pageDescription = 'Vista de calendario con todas las tutorías programadas';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Calendario de Tutorías</h2>
    </div>
    <div>
        <a href="/tutorias" class="btn btn-outline-primary">📋 Vista Lista</a>
        <?php if ($user_role !== 'admin'): ?>
            <a href="/tutorias/create" class="btn btn-primary">📚 Nueva Tutoría</a>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="calendar">
            <div class="calendar-header">
                <?= date('F Y') ?>
            </div>
            <div class="calendar-grid">
                <!-- Days of week header -->
                <div class="calendar-day-header">Dom</div>
                <div class="calendar-day-header">Lun</div>
                <div class="calendar-day-header">Mar</div>
                <div class="calendar-day-header">Mié</div>
                <div class="calendar-day-header">Jue</div>
                <div class="calendar-day-header">Vie</div>
                <div class="calendar-day-header">Sáb</div>
                
                <?php
                $firstDay = date('Y-m-01');
                $lastDay = date('Y-m-t');
                $startDay = date('w', strtotime($firstDay));
                $totalDays = date('t');
                
                // Empty cells for days before month starts
                for ($i = 0; $i < $startDay; $i++) {
                    echo '<div class="calendar-day empty"></div>';
                }
                
                // Days of the month
                for ($day = 1; $day <= $totalDays; $day++) {
                    $currentDate = date('Y-m-' . sprintf('%02d', $day));
                    $hasEvent = false;
                    $dayTutorias = [];
                    
                    foreach ($tutorias as $tutoria) {
                        if ($tutoria['fecha'] === $currentDate) {
                            $hasEvent = true;
                            $dayTutorias[] = $tutoria;
                        }
                    }
                    
                    $classes = 'calendar-day';
                    if ($hasEvent) $classes .= ' has-event';
                    if ($currentDate === date('Y-m-d')) $classes .= ' today';
                    
                    echo '<div class="' . $classes . '" data-date="' . $currentDate . '">';
                    echo '<span class="day-number">' . $day . '</span>';
                    
                    if (!empty($dayTutorias)) {
                        echo '<div class="day-events">';
                        foreach ($dayTutorias as $tutoria) {
                            $statusClass = $tutoria['estado'] === 'confirmada' ? 'success' : 
                                          ($tutoria['estado'] === 'pendiente' ? 'warning' : 'secondary');
                            echo '<div class="event-dot bg-' . $statusClass . '" title="' . 
                                 htmlspecialchars($tutoria['docente'] . ' - ' . date('H:i', strtotime($tutoria['hora']))) . '"></div>';
                        }
                        echo '</div>';
                    }
                    
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<!-- Event details modal -->
<div id="event-modal" class="modal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 class="modal-title">Tutorías del Día</h3>
            <button type="button" class="modal-close" data-modal-close>×</button>
        </div>
        <div class="modal-body" id="event-details">
            <!-- Event details will be loaded here -->
        </div>
    </div>
</div>

<style>
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
    background-color: var(--gray-200);
}

.calendar-day-header {
    background-color: var(--primary-600);
    color: white;
    padding: var(--spacing-3);
    text-align: center;
    font-weight: var(--font-weight-semibold);
}

.calendar-day {
    background-color: white;
    min-height: 80px;
    padding: var(--spacing-2);
    position: relative;
    cursor: pointer;
    transition: background-color var(--transition-fast);
}

.calendar-day:hover {
    background-color: var(--gray-50);
}

.calendar-day.empty {
    background-color: var(--gray-100);
    cursor: default;
}

.calendar-day.today {
    background-color: var(--primary-50);
    border: 2px solid var(--primary-500);
}

.calendar-day.has-event {
    background-color: var(--secondary-50);
}

.day-number {
    font-weight: var(--font-weight-semibold);
}

.day-events {
    position: absolute;
    bottom: var(--spacing-1);
    left: var(--spacing-1);
    right: var(--spacing-1);
    display: flex;
    gap: 2px;
    flex-wrap: wrap;
}

.event-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}
</style>

<script>
document.addEventListener('click', function(e) {
    const calendarDay = e.target.closest('.calendar-day[data-date]');
    if (calendarDay && !calendarDay.classList.contains('empty')) {
        const date = calendarDay.getAttribute('data-date');
        showDayEvents(date);
    }
});

function showDayEvents(date) {
    const tutorias = <?= json_encode($tutorias) ?>;
    const dayTutorias = tutorias.filter(t => t.fecha === date);
    
    if (dayTutorias.length === 0) {
        return;
    }
    
    let html = '<h4>Tutorías para ' + formatDate(date) + '</h4>';
    html += '<div class="mt-3">';
    
    dayTutorias.forEach(tutoria => {
        const statusClass = tutoria.estado === 'confirmada' ? 'success' : 
                           (tutoria.estado === 'pendiente' ? 'warning' : 'secondary');
        
        html += '<div class="border-bottom py-2">';
        html += '<div class="d-flex justify-content-between align-items-center">';
        html += '<div>';
        html += '<strong>' + tutoria.docente + '</strong><br>';
        html += '<small class="text-muted">' + tutoria.hora + '</small>';
        html += '</div>';
        html += '<span class="badge badge-' + statusClass + '">' + tutoria.estado + '</span>';
        html += '</div>';
        if (tutoria.motivo) {
            html += '<small class="text-muted">' + tutoria.motivo.substring(0, 100) + '...</small>';
        }
        html += '</div>';
    });
    
    html += '</div>';
    
    document.getElementById('event-details').innerHTML = html;
    document.getElementById('event-modal').classList.add('show');
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
}
</script>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>