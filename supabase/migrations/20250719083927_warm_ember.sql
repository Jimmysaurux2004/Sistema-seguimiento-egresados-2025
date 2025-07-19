-- Graduate Management System Database Schema
-- This script initializes the complete database structure

-- Set charset and collation
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS graduate_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE graduate_system;

-- Table: usuarios
-- Stores user authentication data for both graduates and administrators
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('egresado', 'admin') NOT NULL DEFAULT 'egresado',
    activo BOOLEAN DEFAULT TRUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_rol (rol)
) ENGINE=InnoDB;

-- Table: egresados
-- Stores graduate information including academic and contact details
CREATE TABLE egresados (
    dni VARCHAR(20) PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    correo VARCHAR(255) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    carrera VARCHAR(100) NOT NULL,
    anio_egreso YEAR NOT NULL,
    situacion_laboral_actual ENUM('empleado', 'desempleado', 'estudiando', 'emprendedor') DEFAULT 'desempleado',
    empresa_actual VARCHAR(200),
    cargo_actual VARCHAR(100),
    usuario_id INT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_correo (correo),
    INDEX idx_carrera (carrera),
    INDEX idx_anio_egreso (anio_egreso),
    INDEX idx_situacion_laboral (situacion_laboral_actual)
) ENGINE=InnoDB;

-- Table: tutorias
-- Manages tutoring sessions between graduates and teachers
CREATE TABLE tutorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    egresado_dni VARCHAR(20) NOT NULL,
    docente VARCHAR(100) NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    estado ENUM('pendiente', 'confirmada', 'completada', 'cancelada') DEFAULT 'pendiente',
    motivo TEXT,
    notas TEXT,
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (egresado_dni) REFERENCES egresados(dni) ON DELETE CASCADE,
    INDEX idx_egresado_dni (egresado_dni),
    INDEX idx_fecha (fecha),
    INDEX idx_estado (estado),
    INDEX idx_docente (docente)
) ENGINE=InnoDB;

-- Table: eventos
-- Stores institutional events, training sessions, and announcements
CREATE TABLE eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    fecha DATE NOT NULL,
    hora TIME,
    lugar VARCHAR(200),
    tipo ENUM('capacitacion', 'evento', 'charla', 'reunion') DEFAULT 'evento',
    capacidad_maxima INT DEFAULT 0,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_fecha (fecha),
    INDEX idx_tipo (tipo),
    INDEX idx_activo (activo)
) ENGINE=InnoDB;

-- Table: encuestas
-- Defines survey questions for employability and feedback tracking
CREATE TABLE encuestas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pregunta TEXT NOT NULL,
    tipo_respuesta ENUM('texto', 'opcion_multiple', 'escala', 'si_no') NOT NULL,
    opciones JSON,
    estado ENUM('activa', 'inactiva') DEFAULT 'activa',
    orden_pregunta INT DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_estado (estado),
    INDEX idx_orden (orden_pregunta)
) ENGINE=InnoDB;

-- Table: respuestas_encuesta
-- Stores graduate responses to surveys
CREATE TABLE respuestas_encuesta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    egresado_dni VARCHAR(20) NOT NULL,
    encuesta_id INT NOT NULL,
    respuesta TEXT,
    fecha_respuesta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (egresado_dni) REFERENCES egresados(dni) ON DELETE CASCADE,
    FOREIGN KEY (encuesta_id) REFERENCES encuestas(id) ON DELETE CASCADE,
    UNIQUE KEY unique_response (egresado_dni, encuesta_id),
    INDEX idx_egresado_dni (egresado_dni),
    INDEX idx_encuesta_id (encuesta_id)
) ENGINE=InnoDB;

-- Table: mensajes
-- Internal messaging system between users
CREATE TABLE mensajes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emisor_id INT NOT NULL,
    receptor_id INT NOT NULL,
    asunto VARCHAR(200),
    mensaje TEXT NOT NULL,
    leido BOOLEAN DEFAULT FALSE,
    fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (emisor_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (receptor_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_emisor (emisor_id),
    INDEX idx_receptor (receptor_id),
    INDEX idx_leido (leido),
    INDEX idx_fecha_envio (fecha_envio)
) ENGINE=InnoDB;

-- Table: notificaciones
-- System notifications for events and reminders
CREATE TABLE notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    mensaje TEXT NOT NULL,
    tipo ENUM('evento', 'tutoria', 'encuesta', 'sistema') DEFAULT 'sistema',
    leida BOOLEAN DEFAULT FALSE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_id (usuario_id),
    INDEX idx_leida (leida),
    INDEX idx_tipo (tipo),
    INDEX idx_fecha_creacion (fecha_creacion)
) ENGINE=InnoDB;

-- Insert default admin user
-- Password: admin123 (hashed)
INSERT INTO usuarios (email, password_hash, rol) VALUES 
('admin@universidad.edu', '$2y$10$HHDO5qJzD7bRdoZyk.vUAe9.BT0.LJHVt/bSp1SE2eOL5sVoIYzZa', 'admin');

-- Insert sample surveys
INSERT INTO encuestas (pregunta, tipo_respuesta, opciones, orden_pregunta) VALUES 
('¿Cuál es su situación laboral actual?', 'opcion_multiple', '["Empleado tiempo completo", "Empleado medio tiempo", "Desempleado", "Estudiando posgrado", "Emprendedor"]', 1),
('¿Qué tan satisfecho está con su trabajo actual?', 'escala', '{"min": 1, "max": 5, "labels": ["Muy insatisfecho", "Insatisfecho", "Neutral", "Satisfecho", "Muy satisfecho"]}', 2),
('¿Recomendaría su carrera a otros estudiantes?', 'si_no', null, 3),
('Comentarios adicionales sobre su experiencia profesional:', 'texto', null, 4);

-- Insert sample events
INSERT INTO eventos (nombre, descripcion, fecha, hora, tipo) VALUES 
('Charla: Tendencias en Tecnología 2025', 'Conferencia sobre las últimas tendencias tecnológicas para egresados', '2025-02-15', '14:00:00', 'charla'),
('Taller de Empleabilidad', 'Taller práctico para mejorar las habilidades de búsqueda de empleo', '2025-02-20', '09:00:00', 'capacitacion'),
('Reunión de Egresados', 'Encuentro anual de egresados de todas las carreras', '2025-03-01', '18:00:00', 'reunion');

SET FOREIGN_KEY_CHECKS = 1;