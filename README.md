# SABIC v2 - Sistema de Asistencia y Gestión

Bienvenido al repositorio de SABIC v2. Este proyecto es una aplicación Laravel basada en **Filament PHP** para la gestión de asistencia, personal y roles de usuario.

## 🚀 Instalación y Configuración

Sigue estos pasos para configurar el proyecto en tu entorno local:

1.  **Clonar el repositorio:**
    ```bash
    git clone <url-del-repositorio>
    cd SABIC-v2
    ```

2.  **Instalar dependencias:**
    ```bash
    composer install
    npm install && npm run build
    ```

3.  **Configurar entorno:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    Configura tu base de datos en el archivo `.env`.

4.  **Migraciones y Seeds:**
    ```bash
    php artisan migrate --seed
    ```

5.  **Crear un usuario administrador (si no lo has hecho):**
    ```bash
    php artisan make:filament-user
    ```

## 🛡️ Gestión de Roles y Permisos (Filament Shield)

Este proyecto utiliza **Filament Shield** para la gestión dinámica de roles y permisos. A continuación, se detalla cómo administrar y personalizar la seguridad.

### 1. Generación de Permisos

Shield genera automáticamente permisos basados en tus Recursos, Páginas y Widgets. Si creas un nuevo recurso o widget, ejecuta el siguiente comando para registrar sus permisos:

```bash
php artisan shield:generate --all
```

Esto escaneará tu aplicación y creará permisos como `view_any_model`, `create_model`, `widget_AttendanceStats`, etc.

### 2. Creación y Edición de Roles

*   Accede al panel de administración (`/admin`).
*   Ve a la sección **Roles y Permisos** (Shield).
*   Desde aquí puedes crear roles (ej. `Super Admin`, `Gerente`, `Empleado`) y asignarles permisos específicos marcando las casillas correspondientes.

### 3. Personalización del Recurso `RoleResource`

El recurso de Roles ha sido publicado y personalizado en `app/Filament/Resources/RoleResource.php` para permitir:
*   Edición del nombre del rol y nombre del guard (`guard_name`).
*   Control total sobre la UI de gestión de roles.

### 4. Protección de Widgets

Para que un Widget respete los permisos de Shield, debe usar el trait `HasWidgetShield`.

**Ejemplo:**
```php
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class AttendanceStats extends BaseWidget
{
    use HasWidgetShield; // <--- Habilita la protección

    // ... lógica del widget
}
```
Si un usuario no tiene el permiso `widget_AttendanceStats`, este widget no se mostrará en su dashboard.

## 📅 Módulo de Asistencia (Attendance)

El sistema cuenta con un panel dedicado para el registro de asistencia (`/attendance`).

*   **Panel Provider:** `app/Providers/Filament/AttendancePanelProvider.php`
*   **Seguridad:** Aunque tenga su propio panel, respeta los roles y permisos definidos globalmente en el sistema.

### Widgets en el Dashboard de Asistencia
Los widgets como "Estadísticas de Asistencia" se muestran condicionalmente según los permisos del usuario logueado. Si un usuario reporta que no ve un widget:
1.  Verifica su Rol.
2.  Asegúrate de que ese Rol tenga el permiso del widget activado en el panel Admin.

## 🛠️ Comandos Útiles

*   `php artisan shield:generate --all`: Regenera todos los permisos y políticas.
*   `php artisan shield:super-admin`: Crea un usuario super admin rápidamente.
*   `php artisan optimize:clear`: Limpia caché (útil si los cambios de permisos no se reflejan inmediatamente).

## 📄 Licencia

Este software es propiedad privada y confidencial.
