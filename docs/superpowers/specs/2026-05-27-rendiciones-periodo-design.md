# Rendiciones por Período

## Objetivo
Permitir al usuario seleccionar el mes específico para el cual desea generar las rendiciones masivas, manteniendo siempre el año corriente por defecto, mejorando así la flexibilidad del sistema sin complicar la interfaz.

## Experiencia de Usuario (UX)
- Al presionar el botón "➕ Nueva Rendición" en el listado de rendiciones, en lugar de navegar inmediatamente, se desplegará un **Modal**.
- El modal solicitará únicamente seleccionar el **Mes** (Enero a Diciembre), viniendo pre-seleccionado el mes actual.
- El **Año** se manejará de forma interna como el año en curso (corriente) y no requerirá interacción del usuario.
- Al confirmar el modal ("Continuar"), el sistema redirigirá al usuario a la vista de Borradores Masivos (`bulkPreview`), enviando el mes seleccionado por parámetro (ej. `?month=5&year=2026`).

## Cambios Técnicos Propuestos

### 1. Vista Index de Rendiciones (`resources/views/settlements/index.blade.php`)
- **[MODIFICAR]** El botón "Nueva Rendición" dejará de ser un enlace directo a `route('settlements.create')` y pasará a ser un botón que abra un modal (ej. `onclick="openPeriodModal()"`).
- **[NUEVO]** Agregar la estructura HTML del modal al final del archivo. Este modal contendrá:
  - Un formulario con método `GET` apuntando a `route('settlements.create')`.
  - Un `<select name="month">` con los 12 meses.
  - Un `<input type="hidden" name="year" value="{{ date('Y') }}">`.

### 2. Controlador de Rendiciones (`SettlementController.php`)
- **[VERIFICACIÓN]** El método `create()` ya soporta recibir `month` y `year` por parámetro (`$request->get('month', date('n'))`). No requiere cambios lógicos mayores, ya que al pasarle los parámetros desde el modal, automáticamente delegará al método `bulkPreview($month, $year)` con los valores correctos para filtrar cobros y gastos de ese período.

## Consideraciones
- **Consistencia:** El modal usará el mismo estilo que el resto de los modales de la aplicación (Tailwind/CSS nativo).
- **Año Fijo:** Si en el futuro se desea generar rendiciones de un año anterior, se requerirá habilitar el campo "Año" nuevamente, pero por los requerimientos actuales, se mantiene fijo por simplicidad.
