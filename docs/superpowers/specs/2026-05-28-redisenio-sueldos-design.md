# Rediseño de Gestión de Sueldos y Pagos

Este documento detalla la estructura y flujos rediseñados para la vista resumen de sueldos (`salaries.period`) basándose en los requerimientos del usuario.

## 1. Tabla Principal (`resources/views/salaries/period.blade.php`)

### 1.1. Columnas y Presentación de Datos
- **Empleado**: Nombre y apellido.
- **Sueldo Base/Actual**: Mostrar el sueldo a liquidar en este periodo.
- **Adelantos**: Total acumulado de adelantos del mes. 
  - *Interacción*: Incorporar un ícono `!` (info/popover) que despliegue la lista de adelantos específicos (fecha y monto) sin salir de la tabla.
- **Bonos**: Total acumulado de bonos registrados para este periodo.
  - *Interacción*: Ícono `!` para ver descripción y monto de cada bono.
- **Neto a Pagar**: Cálculo de (Sueldo - Adelantos + Bonos).
- **Pagado**: Suma de todos los pagos registrados (EmployeeSalaryPayment).
- **Estado**: Etiqueta (badge) visual (Borrador, Listo, Parcial, Pagado).
- **Acciones**:
  - Botón **⚙️ Gestionar**: Abre el modal "Gestionar Sueldo".
  - Botón **💸 Pagos**: Abre el modal "Pagos" (Habilitado solo si el estado es Listo o Parcial).

## 2. Modal "Gestionar Sueldo"

**Objetivo**: Auditoría, carga de novedades (bonos) y confirmación del borrador a estado "Listo". *Este modal no maneja pagos de caja.*

### 2.1. Nueva Cabecera (Contexto del Empleado)
Dividir el header en dos columnas:
- **Izquierda (Datos del Empleado)**:
  - Nombre
  - Banco y CBU/Alias
  - Método de actualización (ej. Paritaria/Fijo)
- **Derecha (Historial de Bonos)**:
  - Mini listado mostrando los últimos 3 bonos otorgados al empleado en periodos anteriores como referencia rápida para el administrador.

### 2.2. Cuerpo del Modal
- **Adelantos Discriminados**: En lugar del total, una lista compacta con fecha, motivo (opcional) y monto de cada adelanto del mes actual.
- **Área de Bonos Optimizada**: El formulario para agregar un bono (Descripción y Monto) se colocará en una sola línea horizontal (inline form) para minimizar el espacio vertical.
- **Totales**: Muestra el Neto a Pagar calculado.
- **Botón de Acción**: "Guardar como Listo" (o Cancelar).

## 3. Modal "Registrar Pagos"

**Objetivo**: Exclusivo para operaciones de tesorería y emisión de comprobantes.

### 3.1. Formularios de Pago
- **Resumen Financiero**: Saldo Pendiente Actual vs Nuevo Saldo Restante dinámico.
- **Formulario Múltiple**: El sistema de filas (Monto, Cuenta de Egreso, Fecha) implementado previamente, permitiendo dividir un sueldo en distintas cajas.
- **Botón**: "Registrar Todos los Pagos".

### 3.2. Historial de Pagos y Comprobantes
- Listado inferior de todos los pagos efectuados contra esta liquidación.
- Al lado de cada fila de pago, un botón prominente **"RECIBO"** que abre el comprobante en una nueva pestaña.

## Consideraciones Técnicas
- **Controlador (`EmployeeSalarySettlementController`)**: Asegurar que al cargar la vista principal, se realice la carga diligente (eager loading) de:
  - `advances`
  - `bonuses`
  - `payments` y `payments.account`
  - `employee` (para banco, cbu, etc.)
  - Bonos históricos (últimos 3 de liquidaciones pasadas del mismo empleado).
- **JS/Blade**: Separar la lógica actual de modals en dos flujos JS distintos (`openManageModal` y `openPaymentsModal`).
