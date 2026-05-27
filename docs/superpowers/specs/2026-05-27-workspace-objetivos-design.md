# Especificación de Diseño: Objetivos y Autogestión de Asistencias (Workspace)

## Descripción General
Este es el tercer sub-proyecto del módulo de Recursos Humanos de Habitar. Su propósito es unificar y centralizar las tareas cotidianas del empleado de RRHH en un **"Espacio de Trabajo" (Workspace)** personal, incorporando un sistema de metas y objetivos ágiles (diarios, semanales y mensuales) creados de manera colaborativa, a la vez que se remueven los controles de asistencia de la pantalla de inicio global para alojarlos de forma exclusiva y ordenada en este nuevo espacio.

---

## Arquitectura de Datos y Base de Datos

Se creará una nueva tabla en la base de datos siguiendo la nomenclatura obligatoria en minúsculas y `snake_case`:

### Tabla `objectives`
*   `id` (PK, BigIncrements)
*   `employee_id` (FK a `employees`, ON DELETE CASCADE): Relación con el empleado al cual pertenece el objetivo.
*   `creator_id` (FK a `users`, ON DELETE RESTRICT): ID del usuario de la plataforma que creó el objetivo (administrador o el propio empleado).
*   `title` (String, max 255): Título descriptivo de la meta.
*   `description` (Text): Descripción detallada del objetivo.
*   `due_date` (Date, nullable): Fecha límite opcional de finalización.
*   `period` (Enum: `'daily'`, `'weekly'`, `'monthly'`): Frecuencia o ciclo del objetivo.
*   `status` (Enum: `'pending'`, `'in_progress'`, `'completed'`, default `'pending'`): Estado de progreso del objetivo.
*   `employee_notes` (Text, nullable): Notas de avance, reportes o comentarios que redacta el empleado.
*   `admin_comment` (Text, nullable): Comentario único o feedback que introduce el administrador (Dueña/RRHH).
*   `timestamps`

---

## Reglas de Negocio y Permisos

1.  **Creación**:
    *   **Empleado**: Puede crearse objetivos asignados únicamente a sí mismo. Al guardarse, el backend asocia automáticamente su `employee_id` correspondiente al usuario logueado, y su `user_id` en el campo `creator_id`.
    *   **Admin**: Puede crear un objetivo para cualquier empleado seleccionándolo de un desplegable de empleados activos. Su `user_id` se guarda en `creator_id`.
2.  **Transición de Estados y Notas**:
    *   *Solo el empleado asignado* puede cambiar el estado de su objetivo (`'pending'`, `'in_progress'`, `'completed'`) y actualizar el campo `employee_notes`.
3.  **Retroalimentación Administrativa**:
    *   *Solo los administradores* (usuarios que posean el permiso general `rrhh.read`) pueden rellenar y actualizar el campo `admin_comment` en los objetivos de cualquier empleado. No tienen permisos para cambiar directamente el estado del objetivo ni editar las notas del empleado.

---

## Interfaz de Usuario (UI) y Vistas

### 1. Espacio de Trabajo Personal del Empleado (`/workspace`)
Una vista premium, moderna y responsive que unifica el control de asistencia diario y el panel Kanban de metas.

#### Sección superior: Consola de Asistencia (Real-time Clock-in)
Consolida los controles que antes residían en `welcome.blade.php`:
*   **Estado: Sin ingresar**: Muestra un botón verde destacado `"Marcar Ingreso"` y un botón secundario `"Avisar Ausencia"`.
*   **Estado: En servicio**: Muestra un mensaje verde con fondo suave indicando la hora exacta de ingreso y un botón rojo destacado `"Marcar Salida"`.
*   **Estado: Jornada Completa / Ausente**: Muestra un badge con el estado del día (ej. *"Salida Registrada"* o *"Ausente - Motivo: Vacaciones"*).

#### Sección inferior: Tablero de Objetivos (Kanban Grid)
*   Botón superior derecho: **"Nuevo Objetivo"** (abre un modal con título, descripción, período y fecha de vencimiento).
*   Tres columnas responsivas ordenadas de izquierda a derecha:
    1.  **Pendientes (Gris)**: Tarjetas con objetivos creados aún no iniciados.
    2.  **En Proceso (Azul/Celeste)**: Tarjetas con objetivos activos en los que se está trabajando.
    3.  **Completados (Verde)**: Tarjetas con objetivos finalizados con éxito.
*   **Tarjetas Kanban**:
    *   Muestran: Título, descripción truncada, badge de período (Diario, Semanal, Mensual) y badge de fecha de vencimiento (si aplica).
    *   Controles: Botón para cambiar de estado al siguiente nivel (ej: de Pendiente a En Proceso) y un botón para abrir un modal simple para editar las "Notas de Avance".
    *   Si el administrador ha dejado un `admin_comment`, se muestra en una sección interna destacada de la tarjeta con fondo amarillo claro tipo "nota adhesiva" premium, indicando que hay feedback disponible.

### 2. Panel de Administración de Objetivos (`/objectives`)
Un nuevo sub-ítem en el menú lateral bajo Recursos Humanos exclusivo para administradores (`rrhh.read`).
*   **Listado General**: Tabla completa con las metas vigentes e históricas de todos los empleados de Habitar.
*   **Filtros**: Permite realizar búsquedas por empleado, período, estado y creador del objetivo.
*   **Acciones**:
    *   Botón **"Asignar Objetivo"**: Abre un formulario/modal para dar de alta una meta seleccionando al empleado destinatario.
    *   Botón **"Dar Feedback"**: Permite a RRHH o a la dueña ingresar o actualizar el `admin_comment` para un objetivo.

### 3. Histórico de Objetivos en Ficha del Legajo (`/employees/{id}`)
*   Se añadirá una pestaña o sección dedicada llamada **"Objetivos"** dentro de la vista de visualización del legajo del empleado para ver la evolución e histórico de metas asignadas a lo largo de su carrera dentro de la empresa.

### 4. Limpieza de Pantalla de Inicio (`/welcome`)
*   Se eliminará el widget superior de marcado de asistencia de la pantalla de inicio general (`welcome.blade.php`).
*   Si el usuario logueado cuenta con legajo de empleado, se mostrará una elegante tarjeta destacada de bienvenida con un botón que le invite a navegar a su **"Espacio de Trabajo / Autogestión"** (`/workspace`).

---

## Plan de Verificación

### Pruebas de Flujo
1.  **Creación**: Probar que un empleado crea su meta y se autoasigna correctamente. Probar que un admin crea una meta para un empleado específico.
2.  **Modificación de Estado**: Verificar que el empleado puede mover objetivos entre columnas y dejar notas. Probar que si el admin intenta modificar el estado del objetivo, el backend arroje un error de autorización.
3.  **Comentarios**: Probar que el admin puede guardar y editar su comentario, y que se visualice correctamente en la tarjeta del empleado. Probar que un empleado no pueda modificar el comentario del administrador.
