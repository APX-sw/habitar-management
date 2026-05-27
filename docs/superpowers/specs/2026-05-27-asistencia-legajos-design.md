# Especificación de Diseño: Asistencia y Legajos Básicos

## Descripción General
Este es el primer sub-proyecto del módulo de Recursos Humanos de Habitar. Se centra en proveer un sistema simple y no invasivo para registrar la asistencia diaria del equipo y mantener un legajo digital centralizado con información personal y documentación de cada empleado.

## Arquitectura de Datos

Se crearán las siguientes tablas en la base de datos (siguiendo estrictamente la convención de minúsculas y `snake_case`):

### `employees`
Almacena el perfil del empleado. Se relaciona con la tabla `users` uno a uno (opcionalmente) para los accesos.
- `id` (PK)
- `user_id` (FK a `users`, nullable)
- `first_name` (string)
- `last_name` (string)
- `document_number` (string)
- `phone` (string)
- `email` (string)
- `hire_date` (date)
- `job_title` (string)
- `emergency_contact_name` (string, nullable)
- `emergency_contact_phone` (string, nullable)
- `bank_name` (string, nullable)
- `cbu_alias` (string, nullable)
- `timestamps`

### `employee_documents`
Almacena referencias a los archivos adjuntos (ej: PDFs, imágenes de contratos, DNI).
- `id` (PK)
- `employee_id` (FK a `employees`)
- `document_type` (string, ej: 'dni', 'contract', 'other')
- `file_path` (string)
- `original_name` (string)
- `timestamps`

### `absence_reasons`
Catálogo administrable de motivos de ausencia.
- `id` (PK)
- `name` (string, ej: 'Enfermedad', 'Vacaciones')
- `description` (string, nullable)
- `is_active` (boolean, default true)
- `timestamps`

### `attendances`
Registro de asistencia y ausencias.
- `id` (PK)
- `employee_id` (FK a `employees`)
- `date` (date)
- `status` (enum: 'present', 'absent')
- `absence_reason_id` (FK a `absence_reasons`, nullable)
- `notes` (text, nullable)
- `timestamps`

*Nota: Una restricción única se aplicará sobre `employee_id` y `date` para evitar dobles registros por día.*

## Componentes y Flujos de Usuario

### 1. Panel de Autogestión (Empleados)
**Ubicación:** Dashboard principal al iniciar sesión (si el usuario es un empleado).
- **Marcar Ingreso:** Un botón destacado para registrar el "presente" del día. Al presionarlo, inserta un registro en `attendances` con `status = 'present'` y la fecha actual. No se requiere horario de salida.
- **Avisar Ausencia:** Un botón secundario que abre un modal modal para registrar una ausencia futura o del día. El empleado selecciona el motivo desde un desplegable (alimentado por `absence_reasons`) y opcionalmente deja una nota.

### 2. Panel de Administración (Dueña / RRHH)
**Ubicación:** Menú lateral bajo la sección "Recursos Humanos".

#### A. Gestión de Legajos (`/employees`)
- **Listado:** Tabla con todos los empleados y un resumen de sus datos básicos.
- **Creación/Edición:** Formulario completo para registrar todos los campos de la tabla `employees`.
- **Documentación:** Dentro del legajo de un empleado, una sección para subir y descargar archivos asociados a la tabla `employee_documents`.

#### B. Configuración de Motivos de Ausencia (`/absence-reasons`)
- ABM simple para gestionar las opciones que ven los empleados al reportar una ausencia.

#### C. Control de Asistencia y Reportes (`/attendances`)
- **Vista Diaria:** Un resumen rápido mostrando quiénes marcaron presente hoy y quiénes avisaron ausencia.
- **Historial y Filtros:** Una tabla donde se puede visualizar el historial. Incluye filtros por:
  - Rango de fechas.
  - Empleado específico.
  - Estado (Presente / Ausente).
  - Motivo de ausencia (para poder cuantificar, por ejemplo, los días tomados por vacaciones).

## Consideraciones de Seguridad y Acceso
- Solo los administradores o usuarios con permisos específicos de RRHH podrán acceder al Panel de Administración (ABM de Legajos, Motivos y Reportes).
- Los empleados regulares solo verán el panel de Autogestión y solo podrán registrar asistencia para su propio usuario (`employee_id` asociado a su `user_id`).

## Revisión de Especificación (Self-Review)
- **Placeholders/Ambigüedad:** No hay secciones "TODO". Se definieron explícitamente los campos de las tablas y el flujo.
- **Consistencia:** Las tablas soportan directamente las funcionalidades requeridas.
- **Alcance:** El documento está estrictamente acotado a la fase de Asistencia y Legajos Básicos, dejando de lado intencionalmente los sub-módulos de Sueldos y Tableros de Objetivos para futuras iteraciones.
