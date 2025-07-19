# Graduate Management System

Sistema profesional de gestión de egresados universitarios desarrollado con **PHP nativo** y arquitectura **MVC manual**. Sin frameworks de JavaScript - todo es vanilla PHP, HTML, CSS y JavaScript puro.

## 🚀 Inicio Rápido con Docker

```bash
# Clonar el repositorio
git clone <repository-url>
cd graduate-management-system

# Levantar el proyecto con Docker
docker-compose up -d

# Acceder al sistema
# Aplicación: http://localhost:8080
# Adminer (BD): http://localhost:8081
```

**Usuario administrador por defecto:**
- Email: admin@universidad.edu  
- Contraseña: admin123

## 🚀 Características Principales

### Para Egresados
- **Perfil profesional**: Actualización de información académica y laboral
- **Tutorías especializadas**: Solicitud y gestión de sesiones con docentes
- **Encuestas de empleabilidad**: Participación en estudios de seguimiento
- **Eventos institucionales**: Información sobre capacitaciones y eventos
- **Mensajería interna**: Comunicación con administradores y otros usuarios
- **Notificaciones en tiempo real**: Sistema de polling para actualizaciones

### Para Administradores
- **Gestión de egresados**: CRUD completo de perfiles de egresados
- **Administración de tutorías**: Aprobación y seguimiento de sesiones
- **Creación de encuestas**: Diseño y análisis de encuestas personalizadas
- **Gestión de eventos**: Programación y difusión de actividades
- **Estadísticas de empleabilidad**: Reportes y análisis de datos
- **Sistema de mensajería**: Comunicación masiva e individual

## 🛠️ Stack Tecnológico

- **Backend**: PHP 8.1+ (nativo, sin frameworks)
- **Frontend**: HTML5, CSS3, JavaScript vanilla (sin frameworks)
- **Arquitectura**: MVC implementado manualmente
- **Base de datos**: MySQL 8.0
- **Estilos**: CSS personalizado con sistema de design tokens
- **Contenedores**: Docker & Docker Compose
- **Servidor web**: Apache 2.4
- **Sin dependencias**: No usa React, Vue, Angular ni ningún framework JS

## 📁 Estructura del Proyecto

```
├── app/                          # Lógica de aplicación
│   ├── core/                     # Clases base del framework MVC
│   │   ├── Database.php          # Conexión y manejo de base de datos
│   │   ├── Router.php            # Sistema de enrutamiento
│   │   ├── Controller.php        # Controlador base
│   │   └── Model.php             # Modelo base
│   ├── controllers/              # Controladores de la aplicación
│   │   ├── AuthController.php    # Autenticación y registro
│   │   ├── HomeController.php    # Dashboard y páginas principales
│   │   ├── EgresadoController.php# Gestión de egresados
│   │   ├── TutoriaController.php # Sistema de tutorías
│   │   ├── EncuestaController.php# Encuestas y respuestas
│   │   ├── MensajeController.php # Mensajería interna
│   │   └── EventoController.php  # Eventos institucionales
│   ├── models/                   # Modelos de datos
│   │   ├── Usuario.php           # Usuarios del sistema
│   │   ├── Egresado.php          # Datos de egresados
│   │   ├── Tutoria.php           # Sesiones de tutoría
│   │   ├── Encuesta.php          # Encuestas y respuestas
│   │   ├── Mensaje.php           # Sistema de mensajes
│   │   ├── Evento.php            # Eventos institucionales
│   │   └── Notificacion.php      # Notificaciones del sistema
│   └── views/                    # Plantillas de vistas
│       ├── layouts/              # Layouts principales
│       ├── auth/                 # Páginas de autenticación
│       ├── home/                 # Dashboard y páginas principales
│       ├── egresado/             # Vistas de egresados
│       ├── tutoria/              # Gestión de tutorías
│       ├── encuesta/             # Sistema de encuestas
│       ├── mensaje/              # Mensajería
│       ├── evento/               # Eventos
│       ├── admin/                # Panel administrativo
│       └── errors/               # Páginas de error
├── public/                       # Archivos públicos
│   ├── index.php                 # Punto de entrada
│   ├── .htaccess                 # Configuración Apache
│   └── assets/                   # Recursos estáticos
│       ├── css/style.css         # Estilos principales
│       └── js/app.js             # JavaScript de aplicación
├── config/                       # Configuración
│   └── config.php                # Parámetros de aplicación
├── sql/                          # Scripts de base de datos
│   └── init.sql                  # Esquema inicial con datos de prueba
├── docker/                       # Configuración Docker
│   └── vhost.conf                # Configuración de Apache
├── docker-compose.yml            # Orquestación de contenedores
├── Dockerfile                    # Imagen de aplicación
├── .env                          # Variables de entorno
└── README.md                     # Documentación
```

## 🐳 Instalación con Docker

### Prerrequisitos
- Docker 20.10+
- Docker Compose 2.0+

### Pasos de instalación

1. **Clonar el repositorio**
```bash
git clone <repository-url>
cd graduate-management-system
```

2. **Levantar con Docker Compose**
```bash
docker-compose up -d
```

3. **Verificar que los contenedores estén corriendo**
```bash
docker-compose ps
```

4. **Acceder al sistema**
```bash
# Aplicación principal
open http://localhost:8080

# Administrador de base de datos
open http://localhost:8081
```

### Servicios disponibles

| Servicio | Puerto | Descripción |
|----------|--------|-------------|
| **Aplicación** | [8080](http://localhost:8080) | Sistema principal |
| **Base de datos** | 3306 | MySQL Server |
| **Adminer** | [8081](http://localhost:8081) | Administrador de BD |

### Flujo de acceso inicial
1. Al acceder a http://localhost:8080 serás redirigido automáticamente al login
2. Usa las credenciales del administrador o regístrate como nuevo egresado
3. Una vez autenticado, accederás al dashboard correspondiente a tu rol

### Acceso a Adminer
- **URL**: http://localhost:8081
- **Servidor**: `db`
- **Usuario**: `graduate_user`
- **Contraseña**: `graduate_pass`
- **Base de datos**: `graduate_system`

## 👤 Usuarios de Prueba

### Administrador
- **Email**: admin@universidad.edu
- **Contraseña**: admin123

### Egresados
Los egresados se registran a través del formulario de registro público en: http://localhost:8080/register

## 📊 Base de Datos

### Tablas principales

1. **usuarios** - Autenticación de usuarios
2. **egresados** - Información de egresados
3. **tutorias** - Sesiones de tutoría
4. **eventos** - Eventos institucionales
5. **encuestas** - Preguntas de encuestas
6. **respuestas_encuesta** - Respuestas de egresados
7. **mensajes** - Sistema de mensajería
8. **notificaciones** - Notificaciones del sistema

### Características de la BD
- **Integridad referencial** con claves foráneas
- **Seguridad RLS** (Row Level Security) para protección de datos
- **Índices optimizados** para consultas frecuentes
- **Datos de prueba** incluidos para desarrollo

## 🎨 Diseño y UX

### Sistema de Design Tokens
- **Colores**: 6 ramjas de color (primary, secondary, accent, success, warning, error)
- **Espaciado**: Sistema basado en 8px
- **Tipografía**: 3 pesos de fuente máximo
- **Componentes**: Cards, botones, formularios, tablas, modales

### Responsive Design
- **Mobile**: < 768px
- **Tablet**: 768px - 1024px  
- **Desktop**: > 1024px

### Características UX
- **Navegación intuitiva** con sidebar colapsible
- **Micro-interacciones** y estados hover
- **Sistema de notificaciones** con polling (sin websockets)
- **Validación de formularios** en tiempo real
- **Mensajes de confirmación** para acciones críticas
- **JavaScript vanilla** sin dependencias externas

## 🔧 Funcionalidades Técnicas

### Arquitectura MVC Manual
```php
// Ejemplo de flujo MVC
Router -> Controller -> Model -> View
```

### Sistema de Autenticación
- **Sesiones PHP** seguras
- **Hash de contraseñas** con password_hash()
- **Protección CSRF** en formularios
- **Control de roles** (egresado/admin)

### Sistema de Polling
- **Notificaciones**: Consulta cada 30 segundos
- **Mensajes**: Actualización automática de contadores
- **Sin WebSockets**: Usa polling tradicional para simplicidad

### Validaciones
- **Backend**: Validación en modelos y controladores
- **Frontend**: JavaScript vanilla para validación inmediata
- **Sanitización**: Protección contra XSS y SQLi

## 🔒 Seguridad

### Medidas implementadas
- **Validación de entrada** en todos los formularios
- **Protección CSRF** con tokens únicos
- **Prevención SQL Injection** con prepared statements
- **Protección XSS** con escape de HTML
- **Headers de seguridad** en .htaccess
- **Control de acceso** basado en roles

## 📝 API Endpoints

### Rutas públicas
- `GET /` - Página de inicio
- `GET /login` - Formulario de login
- `POST /login` - Procesar login
- `GET /register` - Formulario de registro
- `POST /register` - Procesar registro

### Rutas autenticadas
- `GET /dashboard` - Dashboard principal
- `GET /profile` - Perfil de usuario
- `GET /tutorias` - Lista de tutorías
- `GET /encuestas` - Encuestas disponibles
- `GET /mensajes` - Bandeja de mensajes
- `GET /eventos` - Lista de eventos

### Rutas de administración
- `GET /admin/egresados` - Gestión de egresados
- `GET /admin/eventos` - Gestión de eventos
- `GET /admin/encuestas` - Gestión de encuestas

### API AJAX
- `GET /api/notifications` - Obtener notificaciones
- `POST /api/notifications/mark-read` - Marcar como leída
- `GET /api/messages/unread-count` - Contador de mensajes

## 🧪 Testing y Desarrollo

### Desarrollo local
```bash
# Logs de aplicación
docker-compose logs -f app

# Logs de base de datos
docker-compose logs -f db

# Acceso al contenedor
docker-compose exec app bash

# Reiniciar servicios
docker-compose restart

# Parar servicios
docker-compose down
```

### Base de datos
```bash
# Backup
docker-compose exec db mysqldump -u graduate_user -p graduate_system > backup.sql

# Restore
docker-compose exec -T db mysql -u graduate_user -p graduate_system < backup.sql
```

### Comandos útiles Docker
```bash
# Ver estado de contenedores
docker-compose ps

# Ver logs en tiempo real
docker-compose logs -f

# Reconstruir contenedores
docker-compose up -d --build

# Limpiar volúmenes (CUIDADO: borra datos)
docker-compose down -v
```

## 🚀 Despliegue en Producción

### Configuraciones recomendadas

1. **Variables de entorno**
```bash
APP_ENV=production
DB_PASS=<contraseña-segura>
```

2. **Configuración Apache**
- Activar compresión gzip
- Headers de seguridad
- Cache de archivos estáticos

3. **Base de datos**
- Configurar backup automático
- Optimizar consultas
- Monitoreo de rendimiento

## 🤝 Contribución

### Metodología de desarrollo
Este proyecto sigue la metodología **XP (Extreme Programming)** con iteraciones de desarrollo incremental.

### Estándares de código
- **PSR-12** para PHP
- **Camel Case** para variables y métodos
- **Sin frameworks JS**: Solo JavaScript vanilla
- **Comentarios descriptivos** en español
- **Validación obligatoria** en formularios
- **Arquitectura MVC manual** sin librerías externas

### Estructura de archivos
- Cada archivo debe tener menos de 300 líneas
- Separación clara de responsabilidades
- Código limpio y bien documentado
- Sin dependencias innecesarias

## 📜 Licencia

Este proyecto está desarrollado como sistema académico para gestión universitaria.

## 📞 Soporte

### Solución de problemas comunes

**Error de conexión a base de datos:**
```bash
docker-compose logs db
docker-compose restart db
```

**Permisos de archivos:**
```bash
docker-compose exec app chown -R www-data:www-data /var/www/html
```

**Limpiar caché:**
```bash
docker-compose restart app
```

---

**Sistema desarrollado con PHP nativo y Docker - Sin frameworks JavaScript**

**Comando principal: `docker-compose up -d`**