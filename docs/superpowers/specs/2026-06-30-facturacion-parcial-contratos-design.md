# Facturación Parcial en Contratos (IVA Inmobiliario)

## Contexto

La inmobiliaria necesita registrar cuánto del alquiler mensual debe ser facturado oficialmente por el propietario a efectos impositivos (IVA / IB). Al dar de alta un contrato, el operador indica si el contrato requiere facturación y, en ese caso, qué porcentaje del alquiler vigente debe facturarse.

Este dato es **informativo para el propietario** — no afecta el cálculo del neto de la rendición. Su único fin es que el propietario sepa cuánto debe facturar cada mes y, por ende, cuánto IVA (21%) debe abonar.

## Regla de negocio

- Solo existe un contrato activo por propiedad a la vez.
- El monto a facturar se calcula sobre el **alquiler vigente del mes**, respetando cualquier actualización (por índice o porcentaje fijo). Si el alquiler de $500.000 sube a $550.000 en el mes 3 y el porcentaje es 50%, el monto a facturar ese mes es $275.000.
- El IVA informativo es siempre el **21%** del total a facturar de todas las propiedades del propietario en ese mes.
- Si ningún contrato del propietario tiene facturación activa, el bloque no se muestra (ni en la vista ni en el mail).

---

## Cambios propuestos

### 1. Base de datos — Migración en `leases`

**Archivo:** `[NUEVA] database/migrations/YYYY_MM_DD_HHMMSS_add_invoicing_fields_to_leases_table.php`

Agrega dos columnas a la tabla `leases`:

| Columna | Tipo | Default | Restricción |
|---|---|---|---|
| `invoicing_enabled` | `boolean` | `false` | NOT NULL |
| `invoicing_percentage` | `tinyInteger` | `null` | nullable, 1-99 |

---

### 2. Modelo `Lease`

**Archivo:** `[MODIFY] app/Models/Lease.php`

- Agregar `invoicing_enabled` e `invoicing_percentage` al array `$fillable`.
- Agregar método helper:

```php
/**
 * Calcula el monto a facturar para un mes/año dado.
 * Retorna null si el contrato no tiene facturación habilitada.
 */
public function getInvoiceAmountForDate($month, $year): ?float
{
    if (!$this->invoicing_enabled || !$this->invoicing_percentage) {
        return null;
    }
    $rent = $this->calculateRentForDate($month, $year);
    return round($rent * ($this->invoicing_percentage / 100), 2);
}
```

---

### 3. Formulario de contratos (create / edit)

**Archivo:** `[MODIFY] resources/views/leases/create.blade.php` (y `edit.blade.php` si existe por separado)

Se agrega un bloque al formulario:

- **Checkbox** "¿Requiere Facturación?" (`invoicing_enabled`) — desactivado por defecto.
- Al activarlo, aparece con animación un campo numérico **"Porcentaje a Facturar (%)"** (`invoicing_percentage`), con `min=1`, `max=99`, `step=1`.
- Al desactivarlo, el campo se oculta y se limpia el valor vía JS.

**Validación en `LeaseController`:**
- `invoicing_enabled`: `boolean`
- `invoicing_percentage`: `required_if:invoicing_enabled,1|integer|min:1|max:99|nullable`
- Si `invoicing_enabled` es `false`, guardar `invoicing_percentage` como `null`.

---

### 4. `SettlementController@show`

**Archivo:** `[MODIFY] app/Http/Controllers/SettlementController.php`

Después de cargar `$collections`, calcular `$invoicingData`:

```php
$invoicingData = null;
$invoicingItems = [];

foreach ($collections as $col) {
    $lease = $col->lease;
    $invoiceAmount = $lease->getInvoiceAmountForDate($settlement->month, $settlement->year);
    if ($invoiceAmount !== null) {
        $invoicingItems[] = [
            'property'   => $lease->property->location,
            'rent'       => $lease->calculateRentForDate($settlement->month, $settlement->year),
            'percentage' => $lease->invoicing_percentage,
            'amount'     => $invoiceAmount,
        ];
    }
}

if (count($invoicingItems) > 0) {
    $invoicingTotal = array_sum(array_column($invoicingItems, 'amount'));
    $invoicingData = [
        'items' => $invoicingItems,
        'total' => $invoicingTotal,
        'iva_21' => round($invoicingTotal * 0.21, 2),
    ];
}
```

Pasar `$invoicingData` a la vista.

---

### 5. Vista `settlements/show.blade.php`

**Archivo:** `[MODIFY] resources/views/settlements/show.blade.php`

Agregar un bloque **informativo** al final del resumen financiero, después del "NETO FINAL" y antes del footer, visible solo cuando `$invoicingData !== null`:

```
┌─────────────────────────────────────────────────────────────┐
│  📄 DATOS PARA FACTURACIÓN (Solo informativo — no afecta neto) │
│                                                             │
│  Propiedad Belgrano 450  — $550.000 × 50% = $275.000       │
│  Propiedad San Martín 12 — $400.000 × 60% = $240.000       │
│  ─────────────────────────────────────────────────          │
│  Total a Facturar:  $515.000                                │
│  IVA 21% estimado:  $108.150                                │
└─────────────────────────────────────────────────────────────┘
```

Diseño visual diferenciado: fondo azul muy suave (`#EBF8FF`) con borde izquierdo azul, para que sea claramente distinguible del resto de la liquidación e indicar su carácter informativo.

El bloque debe imprimirse también (no tiene clase `no-print`).

---

### 6. `SettlementController@sendToOwner`

**Archivo:** `[MODIFY] app/Http/Controllers/SettlementController.php`

Aplicar el mismo cálculo de `$invoicingData` y agregarlo al payload del webhook, **solo cuando `$type === 'settlement'`**:

```php
if ($type === 'settlement' && $invoicingData) {
    $payload['invoicing'] = $invoicingData;
}
```

Si no hay facturación, la clave `invoicing` se omite del payload.

---

### 7. `N8nCodeService::getSettlementMailCode()`

**Archivo:** `[MODIFY] app/Services/N8nCodeService.php`

En el código JS del nodo n8n, agregar después del bloque "NETO FINAL" un bloque HTML condicional:

```js
// Bloque de Facturación (solo si existe en el payload)
let invoicingHtml = '';
if (body.invoicing && body.invoicing.items && body.invoicing.items.length > 0) {
  let invoicingRowsHtml = '';
  body.invoicing.items.forEach(item => {
    invoicingRowsHtml += `
      <tr style="border-bottom: 1px solid #bee3f8;">
        <td style="padding: 8px 15px; color: #2b6cb0; font-size: 0.9em;">${item.property}</td>
        <td style="padding: 8px 15px; text-align: right; color: #4a5568; font-size: 0.85em;">
          ${fmt(item.rent)} × ${item.percentage}%
        </td>
        <td style="padding: 8px 15px; text-align: right; font-weight: 700; color: #2b6cb0; font-size: 0.9em;">
          ${fmt(item.amount)}
        </td>
      </tr>
    `;
  });

  invoicingHtml = `
    <div style="margin-top: 30px; background: #ebf8ff; border-left: 4px solid #3182ce;
                border-radius: 0 8px 8px 0; padding: 20px; border: 1px solid #bee3f8; border-left-width: 4px;">
      <div style="font-size: 11px; text-transform: uppercase; font-weight: 800;
                  color: #2b6cb0; letter-spacing: 0.08em; margin-bottom: 12px;">
        📄 Datos para Facturación — Solo Informativo
      </div>
      <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
        <thead>
          <tr style="font-size: 0.75em; color: #4a5568; border-bottom: 1px solid #bee3f8;">
            <th style="padding: 6px 15px; text-align: left;">Propiedad</th>
            <th style="padding: 6px 15px; text-align: right;">Cálculo</th>
            <th style="padding: 6px 15px; text-align: right;">A Facturar</th>
          </tr>
        </thead>
        <tbody>${invoicingRowsHtml}</tbody>
      </table>
      <div style="border-top: 1px solid #bee3f8; padding-top: 10px;">
        <div style="display: flex; justify-content: space-between; font-weight: 700; color: #2b6cb0; margin-bottom: 4px;">
          <span>Total a Facturar:</span>
          <span>${fmt(body.invoicing.total)}</span>
        </div>
        <div style="display: flex; justify-content: space-between; font-weight: 700; color: #744210;">
          <span>IVA 21% estimado:</span>
          <span>${fmt(body.invoicing.iva_21)}</span>
        </div>
      </div>
    </div>
  `;
}
```

Y referenciar `${invoicingHtml}` en el template HTML del mail, después del bloque NETO FINAL.

---

## Plan de verificación

### Automatizado
- `php artisan migrate` sin errores.
- `php artisan test` — no deben romperse tests existentes.

### Manual
1. Dar de alta un contrato con facturación habilitada (ej: 50%) y verificar que se guarda correctamente.
2. Generar una rendición para ese propietario y verificar que el bloque informativo aparece con los montos correctos.
3. Verificar que si el alquiler se actualiza, el monto a facturar refleja el valor actualizado.
4. Enviar la rendición por mail (webhook) y confirmar que el payload incluye la clave `invoicing`.
5. Verificar en n8n que el HTML del mail incluye el bloque de facturación.
6. Crear un contrato sin facturación y verificar que el bloque NO aparece en rendición ni en payload.
