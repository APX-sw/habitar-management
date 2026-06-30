# Especificación de Diseño: Tablero "Oficina"

## Descripción General
Este es el segundo sub-proyecto del módulo de Recursos Humanos de Habitar. El objetivo es proveer a la administración (Dueña/RRHH) de un panel de control rápido y visual para auditar diariamente el estado de presencias y ausencias de todo el equipo de empleados.

## Arquitectura y Lógica de Datos
Esta funcionalidad es de **solo lectura** y se apoya enteramente en las tablas existentes desarrolladas en la fase anterior: `employees`, `attendances` y `absence_reasons`.

**Flujo de Carga de Datos:**
Dado un día `D` (por defecto la fecha actual), se obtienen:
1. Todos los empleados activos (`employees`).
2. Todas las asistencias registradas para la fecha `D` (`attendances` donde `date = D`).

La lógica de clasificación es la siguiente:
- Si el empleado NO tiene un registro en `attendances` para `D` -> **Pendientes**
- Si el empleado tiene registro con `status = 'present'` y `check_out` es NULO -> **En la Oficina**
- Si el empleado tiene registro con `status = 'present'` y `check_out` tiene un valor -> **Ya se Retiraron**
- Si el empleado tiene registro con `status = 'absent'` -> **Ausentes**

## Interfaz de Usuario (UI)

### 1. Ubicación y Accesos
- Se agregará un sub-ítem en el menú lateral llamado "Oficina" dentro de la sección madre "Recursos Humanos".
- Esta vista estará protegida por el permiso existente de lectura (`rrhh.read`). No requiere la creación de un nuevo permiso en esta iteración.

### 2. Navegación Temporal
- En la parte superior de la vista existirá un header con un título descriptivo y la fecha que se está visualizando.
- Controles: Botón `[< Día Anterior]`, un `[Input Date Picker]` para ir a una fecha específica, y un botón `[Día Siguiente >]`.
- Al cambiar la fecha, la vista se recargará con los datos correspondientes.

### 3. El Tablero (Kanban)
La vista principal será una cuadrícula responsiva con 4 columnas, de izquierda a derecha:

**Columna A: Pendientes / Sin Registro**
- Color temático: Gris (`#edf2f7` / texto gris oscuro).
- Tarjetas: Nombre del empleado y leyenda "Aún no marcó".

**Columna B: En la Oficina**
- Color temático: Verde (`#c6f6d5` / texto verde oscuro).
- Tarjetas: Nombre del empleado y la "Hora de Ingreso" (`created_at` o fecha de registro de la asistencia).

**Columna C: Ya se retiraron**
- Color temático: Azul (`#bee3f8` / texto azul oscuro).
- Tarjetas: Nombre del empleado, "Hora de Ingreso" y "Hora de Salida" (`check_out`).

**Columna D: Ausentes**
- Color temático: Rojo (`#fed7d7` / texto rojo oscuro).
- Tarjetas: Nombre del empleado, el nombre del "Motivo" de la licencia/ausencia (`absence_reason.name`), y las notas/observaciones que haya dejado (en texto truncado si es muy largo).

## Reglas de Negocio / Restricciones
- El tablero de "Oficina" es **puramente consultivo y de solo lectura**. 
- No hay controles en esta pantalla para alterar el estado de un empleado (no se puede arrastrar y soltar las tarjetas, no se puede hacer clic para modificar la asistencia). Cualquier corrección de un olvido deberá realizarse mediante otras herramientas administrativas de la base de datos o solicitándole al empleado que complete la acción si aún es pertinente.

## Revisión de Especificación (Self-Review)
- **Placeholders:** No existen. La lógica de negocio está completamente cubierta.
- **Consistencia:** Utiliza los modelos definidos en la iteración anterior sin necesidad de migraciones extra.
- **Alcance:** La funcionalidad se reduce estrictamente a la lectura visual diaria, tal como se solicitó para ahorrar complejidad y tokens.
