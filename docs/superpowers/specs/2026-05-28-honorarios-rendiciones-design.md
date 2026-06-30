# Diseño: Honorarios Extra y Rendiciones Negativas

## 1. Contexto y Objetivos
Se necesita extender el sistema de rendiciones a propietarios (`Settlements`) para permitir:
1. **Cobro de Honorarios Extra:** Permitir registrar ítems adicionales de cobro (ej. "Honorario por gestión extra") que se deducen del neto de la rendición.
2. **Manejo de Rendiciones Negativas:** Cuando los gastos, comisiones y honorarios superan los ingresos, el neto queda en negativo. El sistema debe permitir dos acciones:
   - Registrar un **Cobro al Propietario** (ingresa dinero a una caja de la inmobiliaria).
   - **Arrastrar Deuda:** Dejar la rendición sin cobrar para que el saldo pendiente se descuente automáticamente en la rendición del mes siguiente.

## 2. Cambios en Base de Datos
* **Nueva Tabla `settlement_extra_fees`**:
    * `id` (PK)
    * `settlement_id` (FK a `settlements`)
    * `description` (string)
    * `amount` (decimal)
    * `timestamps`
* **Tabla `settlement_payments`**:
    * Se mantendrá igual, pero su interpretación dependerá del `net_amount` de la rendición. Si la rendición es negativa, el "payment" representa un ingreso (Cobro) a la cuenta destino.

## 3. Lógica de Negocio y Controladores
1. **Agregar/Eliminar Extra Fees**: 
   - Crear un endpoint `POST /settlements/{settlement}/extra-fees` y `DELETE /settlements/extra-fees/{extraFee}`.
   - Al modificar un Extra Fee, se recalcula el `net_amount` de la rendición.
2. **Generación de Rendición (Arrastre de Deuda)**:
   - Al generar la rendición, buscar si el mes anterior existe una rendición con saldo deudor pendiente.
   - Si existe, crear automáticamente un `SettlementExtraFee` en la nueva rendición con concepto "Deuda mes anterior" y el monto correspondiente.
3. **Pagos / Cobros (`SettlementController@pay`)**:
   - Si `net_amount > 0`: Registra un movimiento de egreso (`expense`) en la cuenta seleccionada.
   - Si `net_amount < 0`: Registra un movimiento de ingreso (`income`) en la cuenta seleccionada.

## 4. Cambios en la Interfaz (Vista `show`)
1. **Sección de Honorarios Extra**: Añadir una lista donde se puedan añadir conceptos manuales (descripción y monto).
2. **Botón de Acción**: 
   - Si el neto a pagar es positivo: "Registrar Pagos" y formulario de pago.
   - Si el neto a pagar es negativo: "Registrar Cobro al Propietario" y formulario de cobro.
3. **Visualización de la Rendición**: Asegurarse de que los extra fees sean claramente visibles como un descuento.
