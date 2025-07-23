# API SystemTick LA

Sistema de gestión de tickets y colaboración para equipos y organizaciones, desarrollado en Laravel.

## Descripción

**SystemTick LA** es una API robusta para la gestión de tickets, usuarios, departamentos, workspaces, notificaciones y más. Permite la colaboración eficiente en equipos, seguimiento de incidencias y administración de permisos y roles.

## Características principales

- Gestión de tickets con asignación, comentarios, etiquetas y estados.
- Organización por departamentos y workspaces.
- Roles y permisos granulares para usuarios.
- Notificaciones y sistema de invitaciones.
- Configuración personalizada de vistas y preferencias de usuario.
- API RESTful documentada y segura (Laravel Sanctum).
- Arquitectura modular y escalable.

## Estructura de carpetas

```
app/
  Http/Controllers/         # Controladores agrupados por dominio (Users, Tickets, Workspaces, etc.)
  Models/                   # Modelos Eloquent agrupados por dominio
  ...
config/                     # Configuración de la aplicación
routes/                     # Definición de rutas (api.php)
database/                   # Migraciones, seeders y factories
lang/                       # Archivos de localización (es, en)
public/                     # Archivos públicos y documentación de la API
```

## Requisitos

- PHP >= 8.1
- Composer
- MySQL/MariaDB u otro motor compatible
- Node.js y npm (para assets opcionales)

## Instalación

1. Clona el repositorio:
   ```bash
   git clone <url-del-repo>
   cd api-systemtick-la
   ```
2. Instala dependencias PHP:
   ```bash
   composer install
   ```
3. Copia y configura el archivo `.env`:
   ```bash
   cp .env.example .env
   # Edita las variables de entorno según tu entorno local
   ```
4. Genera la clave de la aplicación:
   ```bash
   php artisan key:generate
   ```
5. Ejecuta las migraciones y seeders:
   ```bash
   php artisan migrate --seed
   ```
6. (Opcional) Instala dependencias frontend:
   ```bash
   npm install && npm run build
   ```

## Uso

- Levanta el servidor de desarrollo:
  ```bash
  php artisan serve
  ```
- La API estará disponible en `http://localhost:8000`
- La documentación de la API está disponible en `public/docs/index.html`

## Comandos útiles

- Ejecutar pruebas:
  ```bash
  php artisan test
  ```
- Generar documentación de la API:
  ```bash
  php artisan scribe:generate
  ```

## Estructura de la API

- Autenticación: Sanctum (token)
- Endpoints principales:
  - `/login`, `/logout`, `/users`, `/profile`
  - `/workspaces`, `/departments`, `/tickets`, `/comments`, `/tags`, `/notifications`
  - `/workspaces/{id}/permissions`, `/workspaces/{workspace}/users`, `/workspaces/{workspace}/invite`
  - `/workspaces/{workspace}/tickets`, `/tickets/{ticket}/tags`, etc.
- Consultar `routes/api.php` y la documentación generada para detalles de cada endpoint.

## Localización

- Mensajes y validaciones disponibles en español y en inglés (`lang/es`, `lang/en`).

## Contribución

¡Las contribuciones son bienvenidas! Por favor, abre un issue o pull request para sugerencias, mejoras o reportes de bugs.

## Licencia

Este proyecto está bajo la licencia MIT.

---

**Desarrollado con Laravel**
