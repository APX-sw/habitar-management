# Diseño de Mejoras del Sistema Habitar

## 1. ICL (Índice de Contratos de Locación) Automatizado

### Descripción
El sistema obtendrá automáticamente el valor del ICL consultando la API pública del BCRA (v4), calculará el porcentaje de variación mensual y lo almacenará como un `IndexValue`, reutilizando el motor de indexación existente.

### Componentes
*   **Comando Artisan (`FetchIclValues`)**:
    *   Consulta la API: `GET https://api.bcra.gob.ar/estadisticas/v4.0/monetarias/{id_icl}/datos` con rango del mes actual y anterior.
    *   Calcula variación: `((icl_N / icl_N-1) - 1) * 100`.
    *   Guarda en `index_values`.
    *   Programado para ejecución diaria a las 08:00 AM.
*   **Configuración UI**:
    *   Nuevo botón en la vista de Índices: "Actualizar ICL desde BCRA".
    *   Ruta POST que ejecuta el comando de forma manual bajo demanda.

## 2. Edición de todos los montos en Borrador de Cobro

### Descripción
Permitir la edición de todos los conceptos generados en un borrador de cobro, incluido el alquiler calculado, dejando trazabilidad si el valor original fue modificado.

### Componentes
*   **Base de datos**: Añadir `original_amount` a `collection_details`.
*   **Frontend (`collections.show`)**:
    *   Habilitar inputs para todos los conceptos.
    *   Si se modifica el alquiler (`type='rent'`), mostrar advertencia: "Estás modificando el alquiler calculado automáticamente. Este cambio aplica solo a este período."
*   **Backend (`CollectionController@update`)**: Actualizar la iteración para aceptar modificaciones en cualquier tipo de detalle, no solo cargos fijos.

## 3. Arqueo de Caja

### Descripción
Funcionalidad de cierre de caja diario que compara el efectivo físico (contando billetes) contra el saldo teórico del sistema en las cajas tipo efectivo.

### Componentes
*   **Base de datos**:
    *   `cash_register_closures`: `id`, `closure_date`, `closed_by`, `notes`, `total_declared`, `total_theoretical`, `difference`.
    *   `cash_register_closure_bills`: `closure_id`, `denomination`, `quantity`, `subtotal`.
*   **Frontend / Flujo**:
    *   Botón "Cierre de Caja del Día".
    *   Formulario con denominaciones de billetes argentinos vigentes.
    *   Cálculo: `total_declarado` (suma billetes) vs `total_teórico` (suma saldos de cuentas `type='cash'`).
    *   Muestra diferencia (sobrante/faltante) y permite confirmar con nota.
    *   Vista de historial de cierres.

## 4. Conceptos Recurrentes como Catálogo

### Descripción
Transformar los "Gastos Recurrentes" de texto libre a un catálogo estandarizado.

### Componentes
*   **Base de datos**: Nueva tabla `recurrent_concepts` (`id`, `name`, `transaction_category_id`, `is_active`).
*   **Configuración**: CRUD para gestionar los conceptos.
*   **Contratos**: Sustituir el campo de texto libre por un `select` que cargue el catálogo.
*   **Resumen Mensual**:
    *   Nuevo reporte en el módulo de Cobros.
    *   Agrupa detalles del mes correspondientes a conceptos recurrentes cuyo `destination` es `agency`.
    *   Muestra Propiedad, Monto, Código de Pago (ver Sec. 6) e Inquilino.

## 5. Gastos y Aplicación a Rendición

### Descripción
Control explícito sobre si un gasto debe descontarse al propietario en su rendición y si se paga con fondos propios de Habitar.

### Componentes
*   **Base de datos**: Añadir `applies_to_settlement` y `is_habitar_fund` a `expenses`.
*   **Frontend**: 
    *   Checkboxes "Aplica a rendición" y "Fondos de Habitar".
    *   **Validación UI**: Mutuamente excluyentes. Si uno se activa, el otro se deshabilita.
*   **Backend (`SettlementController`)**: Sumar a la rendición *solo* si `applies_to_settlement == true`.

## 6. Gastos Recurrentes en Propiedad y Expensas

### Descripción
Añadir información complementaria e identificadores de pago a las propiedades.

### Componentes
*   **Base de datos**: 
    *   Campos en `properties`: `pays_expenses`, `expenses_address`, `expenses_phone`.
    *   Tabla `property_recurrent_concepts`: `property_id`, `recurrent_concept_id`, `payment_code` (alfanumérico).
*   **Frontend (`properties.form`)**:
    *   Sección informativa de expensas.
    *   Sección para vincular conceptos recurrentes (del catálogo) y asignarles el código de pago.

## 7. Caja Habitar

### Descripción
Cuenta de caja especial e inmutable que acumula informativamente todos los honorarios percibidos por la agencia.

### Componentes
*   **Base de datos / Semilla**: 
    *   Cuenta predeterminada tipo `habitar_fund`.
*   **Frontend**:
    *   Visible en Cajas con distintivo "Informativa".
    *   No suma al Total General de Caja.
    *   **Restricción Fuerte**: No permite ajustes manuales, transferencias manuales salientes ni eliminación. Es de sistema.
*   **Lógica Automática (Listeners / Observers)**:
    *   **Ingresos**: 
        *   Pagos de cobros (`collection_payments`) con detalles `destination='agency'`.
        *   Pagos de rendiciones (`settlements`) por comisión de agencia.
    *   **Egresos**: 
        *   Registro de gastos (`expenses`) con `is_habitar_fund == true`.
