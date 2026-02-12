# SABIC v2 - Sistema de Asistencia y Gestión

Bienvenido al repositorio de SABIC v2. Este proyecto es una aplicación Laravel basado en **Filament PHP** (v3) para la gestión de asistencia, personal y roles de usuario, operando bajo un entorno **Docker/Podman**.

## 🚀 Instalación y Configuración (Docker)

Sigue estos pasos para levantar el proyecto:

1.  **Levantar Contenedores:**
    ```bash
    docker-compose up -d --build
    ```

2.  **Configurar aplicación (Primera vez):**
    ```bash
    # Instalar dependencias
    podman exec sabic_app composer install
    
    # Generar Key y Enlace Simbólico
    podman exec sabic_app php artisan key:generate
    podman exec sabic_app php artisan storage:link
    
    # Migraciones y Roles Iniciales
    podman exec sabic_app php artisan migrate:fresh --seed
    ```

3.  **Admin Default:**
    - **URL**: `/admin`
    - **User**: `admin@admin.com`
    - **Pass**: `password` (Se solicitará cambio obligatorio al entrar).

---

## 🛡️ Estructura de Seguridad (Shield & Roles)

Utilizamos **Filament Shield** para el Control de Acceso Basado en Roles (RBAC).

### Registro de Nuevos Permisos
Cada vez que crees un nuevo **Recurso, Página o Widget**, debes sincronizar Shield:
```bash
podman exec -it sabic_app php artisan shield:generate --all
```
*Nota: Responde "yes" para generar las Políticas (Policies) automáticas.*

### Configuración Crucial
- **Un Rol por Usuario**: El sistema está configurado para que un usuario solo tenga 1 rol activo (vía `UserResource`).
- **Super Admin**: El rol `admin` es el Super Admin global definido en `config/filament-shield.php`. Tiene acceso total ignorando políticas.

---

## 🗑️ Eliminación Lógica (Soft Deletes)

Todos los módulos críticos usan eliminación lógica.

### Para Implementar en Nuevos Modelos:
1.  **Migración**: Añade `$table->softDeletes();`.
2.  **Modelo**: Usa el trait `use Illuminate\Database\Eloquent\SoftDeletes;`.
3.  **Recurso de Filament (Table/Form)**:
    - Filtro: `Tables\Filters\TrashedFilter::make()`
    - Acciones: `RestoreAction`, `ForceDeleteAction`.
    - Query: Sobrescribe `getEloquentQuery()` con `->withoutGlobalScopes([SoftDeletingScope::class])`.

---

## ♻️ Papelera de Reciclaje (Recycle Bin)

Usamos `promethys/revive` pero **extendido para Shield**.

Si necesitas modificar la papelera o si Shield deja de verla:
- El archivo clave es `app/Filament/Pages/RecycleBin.php`.
- Esta clase extiende la original y añade `use HasPageShield`.
- Está registrada manualmente en el `AdminPanelProvider` dentro del plugin `FilamentRevive`.

---

## 🔑 Renovación de Contraseña

Gestionado por `yebor974/filament-renew-password`.

### Forzar Cambio:
Al crear/editar un usuario, activa **"Forzar cambio de contraseña"**. El usuario será bloqueado en su próximo login hasta que defina una nueva.

### Implementación en Modelos:
El modelo debe implementar `RenewPasswordContract` y usar el trait `RenewPassword`.

---

## 🔄 Flujo de Trabajo: Agregar Nuevo Módulo

Si necesitas agregar una nueva funcionalidad (ej. `Departamentos`):

1.  **Crear Modelo y Migración**: `php artisan make:model Departamento -m`. (No olvides SoftDeletes).
2.  **Crear Recurso Filament solo para ADMIN**: `php artisan make:filament-resource Departamento`.
3.  **Configurar el Recurso**: Añade columnas, formularios y el soporte para Soft Deletes descrito arriba.
4.  **Generar Permisos**: Ejecuta el comando `shield:generate --all`.
5.  **Asignar**: Ve al Panel Admin -> Roles y activa los permisos de `Departamento` para los roles deseados.

---

## 🛠️ Comandos Útiles de Mantenimiento

*   **Limpiar todo el caché**: `podman exec sabic_app php artisan optimize:clear`
*   **Resetear Permisos**: `podman exec sabic_app php artisan permission:cache-reset`
*   **Tinker**: `podman exec -it sabic_app php artisan tinker`

---

## 📄 Licencia

Este software es propiedad privada y confidencial.
