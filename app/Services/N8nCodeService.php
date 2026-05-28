<?php

namespace App\Services;

class N8nCodeService
{
    /**
     * Get JS Code for Dossier / Report Email to Owner
     */
    public static function getDossierCode(): string
    {
        return <<<'JS'
// Obtener los datos de forma compatible con cualquier versión de n8n
const item = (typeof $input !== 'undefined') ? $input.all()[0] : items[0];
const body = item.json.body;

if (!body) {
  return { json: { error: 'No se encontraron datos en el webhook' } };
}

const owner = body.owner;
const totals = body.totals;
const period = body.period;
const properties = body.properties;
const monthlyIncome = body.monthly_income;
const publicUrl = body.public_url;

// Formateador de moneda argentina ($150.000,00)
const formatMoney = (val) => {
  return new Intl.NumberFormat('es-AR', {
    style: 'currency',
    currency: 'ARS',
    minimumFractionDigits: 2
  }).format(val);
};

// 1. Armar listado de propiedades
let propertiesHtml = '';
if (properties && properties.length > 0) {
  properties.forEach(p => {
    const tenantInfo = p.tenant_name 
      ? `<div style="margin-top: 6px; font-size: 13px; color: #4a5568;">Inquilino: <strong>${p.tenant_name}</strong></div>`
      : `<div style="margin-top: 6px; font-size: 13px; color: #e53e3e; font-weight: 600;">Actualmente Vacante</div>`;
    
    propertiesHtml += `
      <div style="border: 1px solid #edf2f7; border-radius: 8px; padding: 15px; margin-bottom: 12px; background-color: #ffffff;">
        <div style="font-weight: bold; color: #2d3748; font-size: 15px;">${p.location}</div>
        <div style="font-size: 12px; color: #718096; margin-top: 2px;">${p.city}, ${p.province} • ${p.rooms} amb.</div>
        ${tenantInfo}
      </div>
    `;
  });
} else {
  propertiesHtml = '<div style="color: #718096; text-align: center; padding: 15px;">No hay propiedades registradas.</div>';
}

// 2. Construir la plantilla HTML Premium del correo
const html = `
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Resumen Patrimonial - Habitar</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f7fafc; margin: 0; padding: 20px; color: #4a5568;">
  <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #edf2f7; overflow: hidden;">
    
    <!-- Encabezado corporativo -->
    <div style="background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%); padding: 35px 30px; color: #ffffff;">
      <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.15em; color: #38b2ac; font-weight: bold; margin-bottom: 6px;">INMOBILIARIA HABITAR</div>
      <div style="font-size: 24px; font-weight: 800; letter-spacing: -0.02em; margin-bottom: 4px;">Resumen Patrimonial</div>
      <div style="font-size: 14px; color: #cbd5e0;">Propietario: <strong>${owner.name}</strong></div>
      
      <div style="margin-top: 20px; display: inline-block; background-color: rgba(56,178,172,0.2); color: #38b2ac; border: 1px solid rgba(56,178,172,0.4); padding: 5px 14px; border-radius: 50px; font-size: 12px; font-weight: bold;">
        Período: ${period}
      </div>
    </div>

    <!-- Cuerpo del Mensaje -->
    <div style="padding: 30px;">
      <p style="margin-top: 0; color: #2d3748; font-size: 15px; line-height: 1.6;">
        Estimado/a <strong>${owner.name}</strong>,<br>
        Esperamos que se encuentre muy bien. A continuación le compartimos el balance de nuestra gestión sobre su patrimonio inmobiliario durante el período seleccionado:
      </p>

      <!-- Fichas de KPIs -->
      <div style="margin: 25px 0;">
        <!-- Neto Acreditado -->
        <div style="background-color: #f8fafc; border-left: 4px solid #38b2ac; padding: 15px; border-radius: 0 8px 8px 0; border: 1px solid #edf2f7; border-left-width: 4px; margin-bottom: 15px;">
          <div style="font-size: 10px; text-transform: uppercase; color: #718096; font-weight: bold; letter-spacing: 0.05em;">Rendimiento Neto Depositado</div>
          <div style="font-size: 26px; font-weight: 800; color: #319795; margin-top: 4px;">${formatMoney(totals.total_net_income)}</div>
          <div style="font-size: 12px; color: #718096; margin-top: 4px;">Monto total acreditado en sus cuentas libres de comisión (${totals.settlements_count} liquidaciones).</div>
        </div>
        
        <!-- Gastos Gestionados -->
        <div style="background-color: #f8fafc; border-left: 4px solid #4a5568; padding: 15px; border-radius: 0 8px 8px 0; border: 1px solid #edf2f7; border-left-width: 4px;">
          <div style="font-size: 10px; text-transform: uppercase; color: #718096; font-weight: bold; letter-spacing: 0.05em;">Gastos y Mantenimientos Gestionados</div>
          <div style="font-size: 26px; font-weight: 800; color: #2d3748; margin-top: 4px;">${formatMoney(totals.total_expenses_managed)}</div>
          <div style="font-size: 12px; color: #718096; margin-top: 4px;">Capital administrado por Habitar para el pago de impuestos, servicios y reparaciones técnicas.</div>
        </div>
      </div>

      <!-- Propiedades -->
      <div style="margin-top: 30px;">
        <h3 style="font-size: 16px; color: #1a202c; border-bottom: 2px solid #edf2f7; padding-bottom: 8px; margin-bottom: 15px; font-weight: 700;">Estado de sus Propiedades</h3>
        ${propertiesHtml}
      </div>

      <!-- Enlace Interactivo -->
      <div style="text-align: center; margin: 35px 0 10px 0;">
        <p style="font-size: 13px; color: #718096; margin-bottom: 12px;">Para ver las planillas de liquidación interactivas completas:</p>
        <a href="${publicUrl}" target="_blank" style="background: linear-gradient(135deg, #38b2ac 0%, #319795 100%); color: #ffffff; padding: 12px 28px; text-decoration: none; font-weight: bold; border-radius: 8px; font-size: 14px; display: inline-block; box-shadow: 0 4px 10px rgba(49,151,149,0.25);">
          Ver Dossier Web Interactivo
        </a>
      </div>
    </div>

    <!-- Pie de página -->
    <div style="background-color: #f8fafc; padding: 25px; text-align: center; font-size: 12px; color: #a0aec0; border-top: 1px solid #edf2f7;">
      Este informe es confidencial y de uso exclusivo para clientes de la Inmobiliaria Habitar.<br>
      Si tiene alguna duda contable o administrativa, por favor responda directamente a este correo.
    </div>

  </div>
</body>
</html>
`;

return { json: { html_body: html } };
JS;
    }

    /**
     * Get JS Code for Tenant Receipt
     */
    public static function getTenantReceiptCode(): string
    {
        return <<<'JS'
const items = $input.all();

for (let item of items) {
  // Soporta tanto si el webhook entrega el cuerpo dentro de 'body' (comportamiento por defecto de n8n) como si se lee directo
  const data = item.json.body || item.json;
  
  // Desestructuración de campos
  const tenantName = data.tenant?.name || 'Inquilino';
  const tenantEmail = data.tenant?.email || '';
  const receiptNumber = data.receipt_number || 'REC-XXXXXX';
  const paymentDate = data.payment_date || '';
  const period = data.period || '';
  const property = data.property || '';
  const amountPaid = parseFloat(data.amount_paid || 0).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  const paymentMethod = data.payment_method || 'N/A';
  const notes = data.notes || '';
  const agencyEmail = data.contact?.agency_email || 'contacto@habitar.com.ar';
  const agencyAddress = data.contact?.agency_address || 'Av. Belgrano (N) 450, Santiago del Estero';
  
  // Construcción dinámica de la tabla de detalles
  let detailsHtml = '';
  if (data.details && Array.isArray(data.details)) {
    data.details.forEach(detail => {
      const amt = parseFloat(detail.amount || 0).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      detailsHtml += `
        <tr style="border-bottom: 1px solid #edf2f7;">
          <td style="padding: 12px 8px; text-align: left; color: #2d3748; font-size: 14px; font-family: 'Outfit', sans-serif;">${detail.description}</td>
          <td style="padding: 12px 8px; text-align: right; color: #2d3748; font-weight: bold; font-size: 14px; font-family: 'Outfit', sans-serif;">$${amt}</td>
        </tr>
      `;
    });
  }

  // Plantilla HTML Premium de Habitar
  const html = `
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Pago - Habitar</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f7fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; -webkit-font-smoothing: antialiased;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f7fafc; padding: 40px 0;">
        <tr>
            <td align="center">
                <!-- Tarjeta Principal -->
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #edf2f7;">
                    <!-- Cabecera Premium -->
                    <tr>
                        <td align="center" style="background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%); padding: 40px 30px; position: relative;">
                            <h2 style="color: #38B2AC; margin: 0; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; font-family: 'Segoe UI', sans-serif;">Comprobante de Pago</h2>
                            <h1 style="color: #ffffff; margin: 10px 0 0 0; font-size: 28px; font-weight: 800; letter-spacing: -0.5px; font-family: 'Segoe UI', sans-serif;">HABITAR</h1>
                            <div style="margin-top: 15px; display: inline-block; background-color: rgba(56, 178, 172, 0.15); border: 1px solid #38B2AC; color: #38B2AC; padding: 6px 16px; border-radius: 50px; font-size: 12px; font-weight: 800; text-transform: uppercase; font-family: 'Segoe UI', sans-serif;">
                                ${receiptNumber}
                            </div>
                        </td>
                    </tr>
                    <!-- Contenido Principal -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="margin: 0 0 20px 0; color: #718096; font-size: 16px; line-height: 1.6;">
                                Hola <strong>${tenantName}</strong>, hemos recibido tu pago correctamente. A continuación, te adjuntamos el detalle del recibo digital.
                            </p>

                            <!-- Bloque de Monto Destacado -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f7fafc; border-radius: 12px; border: 1px solid #edf2f7; margin-bottom: 30px; padding: 20px;">
                                <tr>
                                    <td>
                                        <div style="font-size: 13px; color: #718096; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Monto Total Recibido</div>
                                        <div style="font-size: 32px; color: #2d3748; font-weight: 800; margin-top: 5px;">$${amountPaid}</div>
                                    </td>
                                    <td align="right" valign="bottom">
                                        <div style="font-size: 13px; color: #38B2AC; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">PAGO EXITOSO</div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Detalles de la Transacción -->
                            <h3 style="color: #1a202c; font-size: 15px; font-weight: 800; margin: 0 0 15px 0; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #edf2f7; padding-bottom: 8px;">Detalle de Liquidación</h3>
                            
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 30px; border-collapse: collapse;">
                                ${detailsHtml}
                            </table>

                            <!-- Ficha Técnica -->
                            <h3 style="color: #1a202c; font-size: 15px; font-weight: 800; margin: 0 0 15px 0; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #edf2f7; padding-bottom: 8px;">Información de Pago</h3>
                            
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border-radius: 12px; border: 1px solid #edf2f7; padding: 20px; font-size: 14px; line-height: 1.8; color: #4a5568;">
                                <tr>
                                    <td style="font-weight: 600; color: #718096;" width="140">Propiedad:</td>
                                    <td style="font-weight: 700; color: #2d3748;">${property}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600; color: #718096;">Período Liquidado:</td>
                                    <td style="font-weight: 700; color: #2d3748; text-transform: capitalize;">${period}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600; color: #718096;">Fecha de Pago:</td>
                                    <td style="font-weight: 700; color: #2d3748;">${paymentDate}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600; color: #718096;">Método de Pago:</td>
                                    <td style="font-weight: 700; color: #2d3748;">${paymentMethod}</td>
                                </tr>
                                ${notes ? `
                                <tr>
                                    <td style="font-weight: 600; color: #718096; vertical-align: top;">Notas:</td>
                                    <td style="font-weight: 500; color: #718096; font-style: italic;">"${notes}"</td>
                                </tr>` : ''}
                            </table>
                        </td>
                    </tr>
                    <!-- Pie de Página Premium / Soporte -->
                    <tr>
                        <td align="center" style="background-color: #f8fafc; padding: 30px; border-top: 1px solid #edf2f7;">
                            <p style="margin: 0 0 15px 0; color: #718096; font-size: 13px; line-height: 1.6; font-family: 'Segoe UI', sans-serif;">
                                Si tenés alguna duda sobre tu recibo, podés escribirnos a <a href="mailto:${agencyEmail}" style="color: #319795; text-decoration: underline; font-weight: 600;">${agencyEmail}</a> o visitarnos en ${agencyAddress}.
                            </p>
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="background: linear-gradient(135deg, #38B2AC 0%, #319795 100%); border-radius: 8px;">
                                        <a href="${data.contact?.whatsapp_url || '#'}" target="_blank" style="padding: 10px 24px; color: #ffffff; font-weight: 700; text-decoration: none; display: inline-block; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'Segoe UI', sans-serif;">
                                            💬 Contactar Administración
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin: 25px 0 0 0; color: #a0aec0; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">
                                HABITAR SA • Gestión de Alquileres Inteligente
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
  `;
  
  // Asignamos el HTML generado al objeto de salida del nodo
  item.json.html = html;
  // También aseguramos que el mail de destino esté disponible a primer nivel
  item.json.to_email = tenantEmail;
  item.json.subject = `Recibo de Pago ${receiptNumber} - Habitar`;
}

return items;
JS;
    }

    /**
     * Get JS Code for Settlement Mail to Owner
     */
    public static function getSettlementMailCode(): string
    {
        return <<<'JS'
// 1. Obtenemos los datos del webhook de Laravel
const body = $input.item.json.body;
const collections = body.details.collections || [];
const expenses = body.details.expenses || [];
const totals = body.totals;

// 2. Función para formatear como Pesos Argentinos ($)
const fmt = (val) => {
  const num = parseFloat(val) || 0;
  return new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(num);
};

// 3. Agrupación por Propiedad (Misma lógica contable del sistema)
const propertiesMap = {};

// A. Procesamos Cobros
collections.forEach(c => {
  const pName = c.property;
  if (!propertiesMap[pName]) {
    propertiesMap[pName] = {
      property: pName,
      tenant: c.tenant,
      items: [],
      expenses: [],
      incomeTotal: 0,
      expensesTotal: 0
    };
  }
  
  c.items.forEach(item => {
    const amount = parseFloat(item.amount) || 0;
    propertiesMap[pName].items.push(item);
    propertiesMap[pName].incomeTotal += amount;
    
    // Si es cobrado por la inmobiliaria, también suma al egreso para compensar
    if (item.destination === 'agency') {
      propertiesMap[pName].expensesTotal += amount;
    }
  });
});

// B. Procesamos Gastos Extraordinarios (Se incrustan en su propiedad correspondiente)
expenses.forEach(e => {
  const pName = e.property || 'Gral';
  if (!propertiesMap[pName]) {
    propertiesMap[pName] = {
      property: pName,
      tenant: 'N/A',
      items: [],
      expenses: [],
      incomeTotal: 0,
      expensesTotal: 0
    };
  }
  
  const amount = parseFloat(e.amount) || 0;
  propertiesMap[pName].expenses.push(e);
  propertiesMap[pName].expensesTotal += amount;
});

// 4. Construcción dinámica del HTML de cada Propiedad
let mainDetailsHtml = "";

Object.values(propertiesMap).forEach(prop => {
  let rowsHtml = "";
  
  // Renderizamos los cobros ordinarios e inmobiliarios
  prop.items.forEach(item => {
    const amount = parseFloat(item.amount) || 0;
    const isAgency = item.destination === 'agency';
    
    rowsHtml += `
      <tr style="border-bottom: 1px solid #edf2f7;">
        <td style="padding: 10px 15px; color: #2d3748; font-size: 0.9em; text-align: left;">
          <strong>${item.concept}</strong> <span style="font-size: 0.8em; color: #718096;">(Inq: ${prop.tenant})</span>
          ${isAgency ? '<span style="font-size: 0.75em; color: #4a5568; background: #e2e8f0; padding: 2px 6px; border-radius: 4px; font-weight: 600; margin-left: 5px; display: inline-block;">Cobrado Inq.</span>' : ''}
        </td>
        <td style="padding: 10px 15px; text-align: right; color: #38a169; font-weight: 700; font-size: 0.9em; width: 120px;">
          ${fmt(amount)}
        </td>
        <td style="padding: 10px 15px; text-align: right; color: #a0aec0; font-size: 0.9em; width: 120px;">
          -
        </td>
      </tr>
    `;
    
    if (isAgency) {
      rowsHtml += `
        <tr style="border-bottom: 1px solid #edf2f7; background-color: #faf5ff;">
          <td style="padding: 10px 15px; color: #6b46c1; font-style: italic; font-size: 0.85em; text-align: left; padding-left: 25px;">
            ↳ Retención: ${item.concept} (Pago a cargo de Inmobiliaria)
          </td>
          <td style="padding: 10px 15px; text-align: right; color: #a0aec0; font-size: 0.9em; width: 120px;">
            -
          </td>
          <td style="padding: 10px 15px; text-align: right; color: #e53e3e; font-weight: 700; font-size: 0.9em; width: 120px;">
            - ${fmt(amount)}
          </td>
        </tr>
      `;
    }
  });
  
  // Renderizamos los gastos extraordinarios de la propiedad
  prop.expenses.forEach(exp => {
    const amount = parseFloat(exp.amount) || 0;
    rowsHtml += `
      <tr style="border-bottom: 1px solid #edf2f7;">
        <td style="padding: 10px 15px; color: #2d3748; font-size: 0.9em; text-align: left;">
          <strong>${exp.description}</strong>
        </td>
        <td style="padding: 10px 15px; text-align: right; color: #a0aec0; font-size: 0.9em; width: 120px;">
          -
        </td>
        <td style="padding: 10px 15px; text-align: right; color: #e53e3e; font-weight: 700; font-size: 0.9em; width: 120px;">
          - ${fmt(amount)}
        </td>
      </tr>
    `;
  });
  
  const subtotalProp = prop.incomeTotal - prop.expensesTotal;
  
  mainDetailsHtml += `
    <div style="margin-bottom: 30px; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.02); background: white;">
      <!-- Cabecera de la Tarjeta de Propiedad -->
      <div style="background: #f8fafc; padding: 12px 20px; border-bottom: 1px solid #edf2f7; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-weight: 800; color: #1a202c; font-size: 0.95em;">${prop.property}</span>
      </div>
      
      <!-- Tabla de Movimientos de la Propiedad -->
      <table style="width: 100%; border-collapse: collapse; font-family: inherit;">
        <thead>
          <tr style="background: #ffffff; border-bottom: 1px solid #edf2f7; font-size: 0.75em; text-transform: uppercase; letter-spacing: 0.05em; color: #718096;">
            <th style="padding: 8px 15px; text-align: left; font-weight: 700;">Concepto</th>
            <th style="padding: 8px 15px; text-align: right; font-weight: 700; width: 120px;">Ingreso</th>
            <th style="padding: 8px 15px; text-align: right; font-weight: 700; width: 120px;">Egreso</th>
          </tr>
        </thead>
        <tbody>
          ${rowsHtml}
          <!-- Subtotal de la Propiedad -->
          <tr style="background: #f8fafc; font-weight: 800; border-top: 2px solid #e2e8f0;">
            <td style="padding: 12px 20px; text-align: right; color: #4a5568; font-size: 0.9em;">SUBTOTAL ${prop.property}:</td>
            <td colspan="2" style="padding: 12px 20px; text-align: right; font-weight: 900; color: #1a202c; font-size: 1.05em;">
              ${fmt(subtotalProp)}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  `;
});

// 5. Cálculo Dinámico de Honorarios Extra y Pagos Directos a Propietario
let extraFeesHtml = "";
const extraFees = body.details.extra_fees || [];
if (extraFees.length > 0) {
  extraFeesHtml += `
    <tr>
      <td colspan="2" style="padding: 15px 0 5px 0; font-weight: 800; color: #4a5568; font-size: 0.9em; border-bottom: 1px solid #edf2f7;">
        Honorarios Extra / Descuentos Adicionales
      </td>
    </tr>
  `;
  extraFees.forEach(ef => {
    extraFeesHtml += `
      <tr>
        <td style="padding: 8px 0; font-weight: 500; color: #718096; padding-left: 10px;">↳ ${ef.description}</td>
        <td style="text-align: right; font-weight: bold; color: #e53e3e;">- ${fmt(ef.amount)}</td>
      </tr>
    `;
  });
}

const directDiff = (parseFloat(totals.total_income) || 0) - (parseFloat(totals.total_expenses) || 0) - (parseFloat(totals.agency_commission) || 0) - (parseFloat(totals.extra_fees_total) || 0) - (parseFloat(totals.net_amount) || 0);
let directRowHtml = "";
if (directDiff > 0.01) {
  directRowHtml = `
    <tr>
      <td style="padding: 8px 0; font-weight: 600; color: #dd6b20;">Ya transferido directo:</td>
      <td style="text-align: right; font-weight: bold; color: #dd6b20;">- ${fmt(directDiff)}</td>
    </tr>
  `;
}

// 6. Estructura Global del Correo Electrónico (Maquetación Prémium)
const htmlContent = `
<div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 650px; margin: 0 auto; background-color: #ffffff; color: #2d3748;">
  
  <!-- Cabecera Premium -->
  <div style="padding: 30px; text-align: right; border-bottom: 3px solid #edf2f7;">
    <h1 style="color: #1a202c; margin: 0; font-size: 28px; letter-spacing: -0.5px;">RENDICIÓN MENSUAL</h1>
    <p style="color: #38B2AC; font-weight: bold; margin: 5px 0 0 0; font-size: 18px;">Período: ${body.period}</p>
    <p style="color: #a0aec0; margin: 5px 0 0 0; font-size: 12px;">Liquidación de Cuentas #${body.settlement_id}</p>
  </div>

  <div style="padding: 30px;">
    <!-- Saludo / Propietario -->
    <div style="background: #f8fafc; border-radius: 8px; padding: 20px; margin-bottom: 30px; border: 1px solid #edf2f7;">
      <p style="margin: 0; font-size: 12px; color: #a0aec0; font-weight: 800; text-transform: uppercase;">Destinatario / Propietario</p>
      <h3 style="margin: 5px 0 0 0; color: #1a202c; font-size: 18px;">${body.owner.name}</h3>
    </div>

    <h3 style="color: #1a202c; font-size: 16px; margin-bottom: 20px;">Detalle por Propiedad</h3>
    
    <!-- Inserción Dinámica de Tablas -->
    ${mainDetailsHtml}

    <!-- Resumen Final -->
    <div style="margin-top: 40px; border-top: 2px solid #edf2f7; padding-top: 20px; display: flex; justify-content: flex-end;">
      <table style="width: 350px; font-size: 15px; color: #4a5568; margin-left: auto;">
        <tr>
          <td style="padding: 8px 0; font-weight: 600;">Total Ingresos:</td>
          <td style="text-align: right; font-weight: bold; color: #38a169;">${fmt(totals.total_income)}</td>
        </tr>
        <tr>
          <td style="padding: 8px 0; font-weight: 600;">Total Gastos:</td>
          <td style="text-align: right; font-weight: bold; color: #e53e3e;">- ${fmt(totals.total_expenses)}</td>
        </tr>
        <tr>
          <td style="padding: 8px 0; font-weight: 600;">Honorarios Inmobiliaria:</td>
          <td style="text-align: right; font-weight: bold; color: #e53e3e;">- ${fmt(totals.agency_commission)}</td>
        </tr>
        ${extraFeesHtml}
        ${directRowHtml}
      </table>
    </div>

    <!-- Bloque Neto Final -->
    ${(parseFloat(totals.net_amount) || 0) < 0 ? `
      <div style="margin-top: 20px; background: #FFF5F5; border: 2px solid #FC8181; color: #9B2C2C; padding: 25px; border-radius: 12px; text-align: center;">
        <span style="font-size: 14px; font-weight: 800; display: block; margin-bottom: 5px; text-transform: uppercase;">SALDO A FAVOR DE HABITAR</span>
        <span style="font-size: 32px; font-weight: 900; color: #E53E3E;">${fmt(totals.net_amount)}</span>
        <p style="margin: 15px 0 0 0; font-size: 13px; font-weight: 600; color: #C53030; line-height: 1.5;">Por favor, recuerde transferir este monto a las cuentas bancarias de la Inmobiliaria para regularizar su cuenta corriente.</p>
      </div>
    ` : `
      <div style="margin-top: 20px; background: #1a202c; color: white; padding: 25px; border-radius: 12px; text-align: center;">
        <span style="font-size: 14px; font-weight: 800; opacity: 0.8; display: block; margin-bottom: 5px;">NETO FINAL A TRANSFERIR</span>
        <span style="font-size: 32px; font-weight: 900; color: #ffffff;">${fmt(totals.net_amount)}</span>
      </div>
    `}

  </div>
</div>
`;

// 7. Retorno de Variables
return {
  html_detalle: htmlContent,
  email: body.owner.email,
  nombre_propietario: body.owner.name,
  periodo: body.period
};
JS;
    }

    /**
     * Get JS Code for Settlement Payment Confirmation to Owner
     */
    public static function getSettlementPaymentConfirmCode(): string
    {
        return <<<'JS'
for (const item of $input.all()) {
  // 1. Obtenemos los datos del webhook
  const data = item.json.body || item.json;

  const collections = data?.details?.collections || [];
  const expenses = data?.details?.expenses || [];
  const payments = data?.payments || [];
  const totals = data?.totals || {};
  const owner = data?.owner || {};
  const period = data?.period || 'N/A';

  // Función para formatear a Pesos Argentinos ($)
  const fmt = (val) => {
    const num = parseFloat(val) || 0;
    return new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(num);
  };

  // 2. Agrupación por Propiedad (Misma lógica contable premium del sistema)
  const propertiesMap = {};

  // A. Procesamos Cobros
  collections.forEach(c => {
    const pName = c.property;
    if (!propertiesMap[pName]) {
      propertiesMap[pName] = {
        property: pName,
        tenant: c.tenant,
        items: [],
        expenses: [],
        incomeTotal: 0,
        expensesTotal: 0
      };
    }
    
    c.items.forEach(item => {
      const amount = parseFloat(item.amount) || 0;
      propertiesMap[pName].items.push(item);
      propertiesMap[pName].incomeTotal += amount;
      
      // Si es cobrado por la inmobiliaria, también suma al egreso para compensar
      if (item.destination === 'agency') {
        propertiesMap[pName].expensesTotal += amount;
      }
    });
  });

  // B. Procesamos Gastos Extraordinarios (Se incrustan en su propiedad correspondiente)
  expenses.forEach(e => {
    const pName = e.property || 'Gral';
    if (!propertiesMap[pName]) {
      propertiesMap[pName] = {
        property: pName,
        tenant: 'N/A',
        items: [],
        expenses: [],
        incomeTotal: 0,
        expensesTotal: 0
      };
    }
    
    const amount = parseFloat(e.amount) || 0;
    propertiesMap[pName].expenses.push(e);
    propertiesMap[pName].expensesTotal += amount;
  });

  // 3. Construcción dinámica del HTML de cada Propiedad
  let mainDetailsHtml = "";

  Object.values(propertiesMap).forEach(prop => {
    let rowsHtml = "";
    
    // Renderizamos los cobros ordinarios e inmobiliarios
    prop.items.forEach(item => {
      const amount = parseFloat(item.amount) || 0;
      const isAgency = item.destination === 'agency';
      
      rowsHtml += `
        <tr style="border-bottom: 1px solid #edf2f7;">
          <td style="padding: 10px 15px; color: #2d3748; font-size: 0.9em; text-align: left;">
            <strong>${item.concept}</strong> <span style="font-size: 0.8em; color: #718096;">(Inq: ${prop.tenant})</span>
            ${isAgency ? '<span style="font-size: 0.75em; color: #4a5568; background: #e2e8f0; padding: 2px 6px; border-radius: 4px; font-weight: 600; margin-left: 5px; display: inline-block;">Cobrado Inq.</span>' : ''}
          </td>
          <td style="padding: 10px 15px; text-align: right; color: #38a169; font-weight: 700; font-size: 0.9em; width: 120px;">
            ${fmt(amount)}
          </td>
          <td style="padding: 10px 15px; text-align: right; color: #a0aec0; font-size: 0.9em; width: 120px;">
            -
          </td>
        </tr>
      `;
      
      if (isAgency) {
        rowsHtml += `
          <tr style="border-bottom: 1px solid #edf2f7; background-color: #faf5ff;">
            <td style="padding: 10px 15px; color: #6b46c1; font-style: italic; font-size: 0.85em; text-align: left; padding-left: 25px;">
              ↳ Retención: ${item.concept} (Pago a cargo de Inmobiliaria)
            </td>
            <td style="padding: 10px 15px; text-align: right; color: #a0aec0; font-size: 0.9em; width: 120px;">
              -
            </td>
            <td style="padding: 10px 15px; text-align: right; color: #e53e3e; font-weight: 700; font-size: 0.9em; width: 120px;">
              - ${fmt(amount)}
            </td>
          </tr>
        `;
      }
    });
    
    // Renderizamos los gastos extraordinarios de la propiedad
    prop.expenses.forEach(exp => {
      const amount = parseFloat(exp.amount) || 0;
      rowsHtml += `
        <tr style="border-bottom: 1px solid #edf2f7;">
          <td style="padding: 10px 15px; color: #2d3748; font-size: 0.9em; text-align: left;">
            <strong>${exp.description}</strong>
          </td>
          <td style="padding: 10px 15px; text-align: right; color: #a0aec0; font-size: 0.9em; width: 120px;">
            -
          </td>
          <td style="padding: 10px 15px; text-align: right; color: #e53e3e; font-weight: 700; font-size: 0.9em; width: 120px;">
            - ${fmt(amount)}
          </td>
        </tr>
      `;
    });
    
    const subtotalProp = prop.incomeTotal - prop.expensesTotal;
    
    mainDetailsHtml += `
      <div style="margin-bottom: 30px; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.02); background: white;">
        <!-- Cabecera de la Tarjeta de Propiedad -->
        <div style="background: #f8fafc; padding: 12px 20px; border-bottom: 1px solid #edf2f7; display: flex; justify-content: space-between; align-items: center;">
          <span style="font-weight: 800; color: #1a202c; font-size: 0.95em;">${prop.property}</span>
        </div>
        
        <!-- Tabla de Movimientos de la Propiedad -->
        <table style="width: 100%; border-collapse: collapse; font-family: inherit;">
          <thead>
            <tr style="background: #ffffff; border-bottom: 1px solid #edf2f7; font-size: 0.75em; text-transform: uppercase; letter-spacing: 0.05em; color: #718096;">
              <th style="padding: 8px 15px; text-align: left; font-weight: 700;">Concepto</th>
              <th style="padding: 8px 15px; text-align: right; font-weight: 700; width: 120px;">Ingreso</th>
              <th style="padding: 8px 15px; text-align: right; font-weight: 700; width: 120px;">Egreso</th>
            </tr>
          </thead>
          <tbody>
            ${rowsHtml}
            <!-- Subtotal de la Propiedad -->
            <tr style="background: #f8fafc; font-weight: 800; border-top: 2px solid #e2e8f0;">
              <td style="padding: 12px 20px; text-align: right; color: #4a5568; font-size: 0.9em;">SUBTOTAL ${prop.property}:</td>
              <td colspan="2" style="padding: 12px 20px; text-align: right; font-weight: 900; color: #1a202c; font-size: 1.05em;">
                ${fmt(subtotalProp)}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    `;
  });

  // 4. Construcción Dinámica de Transferencias (Pagos con Titular)
  let paymentsHtml = "";
  if (payments.length > 0) {
    payments.forEach((p) => {
      // Formatear la fecha a dd/mm/yyyy
      let fecha = p.date;
      try {
        const parts = p.date.split('-');
        if(parts.length === 3) fecha = `${parts[2]}/${parts[1]}/${parts[0]}`;
      } catch(e) {}

      paymentsHtml += `
        <div style="background: #ffffff; border: 2px solid #C6F6D5; border-radius: 12px; padding: 20px; margin-bottom: 15px; position: relative;">
          <div style="font-size: 0.8rem; font-weight: 800; color: #2f855a; text-transform: uppercase; margin-bottom: 5px;">Monto Transferido</div>
          <div style="font-size: 24px; font-weight: 900; color: #22543D; margin-bottom: 15px;">${fmt(p.amount)}</div>
          
          <div style="border-top: 1px solid #e2e8f0; padding-top: 15px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px;">
              <span style="color: #718096;">Titular Cuenta:</span>
              <span style="font-weight: 700; color: #2d3748;">${p.holder || 'N/A'}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px;">
              <span style="color: #718096;">Alias / CBU:</span>
              <span style="font-weight: 700; color: #2d3748;">${p.bank}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 14px;">
              <span style="color: #718096;">Fecha de Pago:</span>
              <span style="font-weight: 700; color: #2d3748;">${fecha}</span>
            </div>
          </div>
        </div>
      `;
    });
  }

  // Obtenemos la fecha del primer pago para enviarla como variable global
  let fechaPagoPrincipal = new Date().toISOString().split('T')[0];
  if (payments.length > 0) {
    try {
      const parts = payments[0].date.split('-');
      fechaPagoPrincipal = `${parts[2]}/${parts[1]}/${parts[0]}`;
    } catch(e) {}
  }

  // 5. Cálculo Dinámico de Honorarios Extra y Pagos Directos a Propietario
  let extraFeesHtml = "";
  const extraFees = data?.details?.extra_fees || [];
  if (extraFees.length > 0) {
    extraFeesHtml += `
      <tr>
        <td colspan="2" style="padding: 15px 0 5px 0; font-weight: 800; color: #4a5568; font-size: 0.9em; border-bottom: 1px solid #edf2f7;">
          Honorarios Extra / Descuentos Adicionales
        </td>
      </tr>
    `;
    extraFees.forEach(ef => {
      extraFeesHtml += `
        <tr>
          <td style="padding: 8px 0; font-weight: 500; color: #718096; padding-left: 10px;">↳ ${ef.description}</td>
          <td style="text-align: right; font-weight: bold; color: #e53e3e;">- ${fmt(ef.amount)}</td>
        </tr>
      `;
    });
  }

  const directDiff = (parseFloat(totals.total_income) || 0) - (parseFloat(totals.total_expenses) || 0) - (parseFloat(totals.agency_commission) || 0) - (parseFloat(totals.extra_fees_total) || 0) - (parseFloat(totals.net_amount) || 0);
  let directRowHtml = "";
  if (directDiff > 0.01) {
    directRowHtml = `
      <tr>
        <td style="padding: 8px 0; font-weight: 600; color: #dd6b20;">Ya transferido directo:</td>
        <td style="text-align: right; font-weight: bold; color: #dd6b20;">- ${fmt(directDiff)}</td>
      </tr>
    `;
  }

  // 6. HTML Completo (Maquetación Premium)
  const htmlContent = `
  <div style="font-family: 'Inter', 'Segoe UI', sans-serif; max-width: 650px; margin: 0 auto; background-color: #ffffff; color: #2d3748; border: 1px solid #edf2f7; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);">
    
    <div style="padding: 30px; text-align: center; background-color: #C6F6D5; border-bottom: 3px solid #9AE6B4;">
      <h1 style="color: #22543D; margin: 0; font-size: 26px; font-weight: 900; letter-spacing: -0.5px;">COMPROBANTE DE PAGO</h1>
      <p style="color: #2f855a; font-weight: bold; margin: 5px 0 0 0; font-size: 16px;">Rendición Período: ${period}</p>
    </div>

    <div style="padding: 30px;">
      
      <p style="font-size: 16px; margin-top: 0; color: #4a5568;">Estimado/a <strong>${owner.name || 'Propietario'}</strong>,</p>
      <p style="font-size: 15px; color: #718096; line-height: 1.6; margin-bottom: 30px;">${(parseFloat(totals.net_amount) || 0) < 0 ? 'Hemos procesado exitosamente la recepción de tu pago para regularizar el saldo deudor de esta liquidación. A continuación le adjuntamos el recibo detallado.' : 'Te informamos que hemos procesado exitosamente la transferencia correspondiente a la liquidación de tus propiedades. A continuación, te adjuntamos los detalles de los depósitos realizados y el resumen de tu rendición.'}</p>

      <!-- Sección Dinámica de Transferencias -->
      <h3 style="color: #22543D; font-size: 18px; margin-bottom: 15px; display: flex; align-items: center;">
        <span style="background: #9AE6B4; width: 24px; height: 24px; border-radius: 50%; display: inline-block; margin-right: 10px; text-align: center; line-height: 24px; color: #22543D;">✓</span>
        Detalle de Transferencias
      </h3>
      ${paymentsHtml}

      <div style="border-top: 2px dashed #e2e8f0; margin: 35px 0;"></div>

      <!-- Detalle de la Rendición -->
      <h3 style="color: #1a202c; font-size: 18px; margin-bottom: 20px;">Resumen de Rendición</h3>
      ${mainDetailsHtml}

      <!-- Totales -->
      <div style="margin-top: 30px; background: #f8fafc; padding: 25px; border-radius: 12px; border: 1px solid #edf2f7;">
        <table style="width: 100%; font-size: 15px; color: #4a5568;">
          <tr>
            <td style="padding: 8px 0; font-weight: 600;">Total Ingresos:</td>
            <td style="text-align: right; font-weight: bold; color: #38a169;">${fmt(totals.total_income)}</td>
          </tr>
          <tr>
            <td style="padding: 8px 0; font-weight: 600;">Total Gastos:</td>
            <td style="text-align: right; font-weight: bold; color: #e53e3e;">- ${fmt(totals.total_expenses)}</td>
          </tr>
          <tr>
            <td style="padding: 8px 0; font-weight: 600; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px;">Honorarios Inmobiliaria:</td>
            <td style="text-align: right; font-weight: bold; color: #e53e3e; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px;">- ${fmt(totals.agency_commission)}</td>
          </tr>
          ${extraFeesHtml}
          ${directRowHtml}
          <tr>
            <td style="padding: 15px 0 0 0; font-weight: 800; font-size: 18px; color: #1a202c;">${(parseFloat(totals.net_amount) || 0) < 0 ? 'NETO COBRADO:' : 'NETO LIQUIDADO:'}</td>
            <td style="padding: 15px 0 0 0; text-align: right; font-weight: 900; font-size: 22px; color: ${(parseFloat(totals.net_amount) || 0) < 0 ? '#E53E3E' : '#2b6cb0'};">${fmt(totals.net_amount)}</td>
          </tr>
        </table>
      </div>

    </div>
  </div>
  `;

  // 5. Asignamos todo al item para poder usarlo en el nodo de Gmail
  item.json.html_detalle = htmlContent;
  item.json.email_propietario = owner.email || '';
  item.json.nombre_propietario = owner.name || '';
  item.json.periodo = period;
  item.json.fecha_pago = fechaPagoPrincipal;
}

return $input.all();
JS;
    }

    /**
     * Get JS Code for Tenant Collection Notification
     */
    public static function getTenantCollectionNotificationCode(): string
    {
        return <<<'JS'
for (const item of $input.all()) {
  // Manejo seguro por si los datos llegan envueltos en .body o directo en la raíz
  const data = item.json.body || item.json;
  const details = data.details || [];
  const bank = data.agency_bank_account;
  const contact = data.contact;
  
  // Función para formatear en Pesos Argentinos
  const fmt = (val) => {
    const num = parseFloat(val) || 0;
    return new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(num);
  };

  if (details && Array.isArray(details)) {
    
    let itemsHtml = "";
    
    // Generamos las filas de la tabla dinámicamente
    details.forEach(d => {
      itemsHtml += `
        <tr>
          <td style="padding: 12px 15px; color: #4a5568; font-size: 0.95em; border-bottom: 1px solid #edf2f7;">
            <strong>${d.description}</strong>
          </td>
          <td style="padding: 12px 15px; text-align: right; color: #2d3748; font-weight: bold; border-bottom: 1px solid #edf2f7;">
            ${fmt(d.amount)}
          </td>
        </tr>
      `;
    });

    // Bloque de Cuenta Bancaria (si existe)
    let bankHtml = "";
    if (bank) {
      bankHtml = `
        <div style="margin-top: 30px; padding: 20px; background-color: #f8fafc; border: 1px dashed #cbd5e0; border-radius: 10px;">
          <h4 style="margin: 0 0 10px 0; color: #2d3748; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Datos para Transferencia:</h4>
          <p style="margin: 4px 0; font-size: 14px; color: #4a5568;"><strong>Titular:</strong> ${bank.holder_name}</p>
          <p style="margin: 4px 0; font-size: 14px; color: #4a5568;"><strong>Banco:</strong> ${bank.bank_entity}</p>
          <p style="margin: 4px 0; font-size: 14px; color: #4a5568;"><strong>CBU:</strong> <span style="font-family: monospace;">${bank.cbu}</span></p>
          <p style="margin: 4px 0; font-size: 14px; color: #2b6cb0; font-weight: bold;"><strong>ALIAS:</strong> ${bank.alias}</p>
        </div>
      `;
    }

    // Bloque de WhatsApp (si existe)
    let whatsappHtml = "";
    if (contact && contact.whatsapp_url) {
      whatsappHtml = `
        <div style="margin-top: 25px; text-align: center;">
          <p style="margin-bottom: 12px; font-size: 14px; color: #718096;">Una vez realizado el pago, por favor enviá el comprobante:</p>
          <a href="${contact.whatsapp_url}" style="display: inline-block; background-color: #25D366; color: white; padding: 12px 25px; border-radius: 50px; text-decoration: none; font-weight: 700; font-size: 15px; box-shadow: 0 4px 10px rgba(37, 211, 102, 0.3);">
            <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" width="18" style="vertical-align: middle; margin-right: 8px; filter: brightness(0) invert(1);" />
            Enviar Comprobante
          </a>
        </div>
      `;
    }

    // Construimos la plantilla HTML Premium
    const htmlList = `
      <div style="font-family: 'Inter', 'Segoe UI', sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff; color: #2d3748; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);">
        
        <div style="background-color: #f8fafc; padding: 30px; border-bottom: 1px solid #e2e8f0; text-align: center;">
          <h2 style="margin: 0; color: #2b6cb0; font-size: 24px; font-weight: 800; letter-spacing: -0.5px;">AVISO DE COBRO</h2>
          <p style="margin: 8px 0 0 0; color: #718096; font-size: 15px;">Período: <strong style="color: #4a5568;">${data.period}</strong></p>
        </div>
        
        <div style="padding: 30px;">
          <p style="margin: 0 0 15px 0; font-size: 16px; color: #4a5568;">Hola <strong style="color: #2d3748;">${data.tenant ? data.tenant.name : 'Inquilino'}</strong>,</p>
          <p style="margin: 0 0 25px 0; color: #718096; line-height: 1.6;">El detalle de los conceptos a abonar correspondientes a la propiedad <strong style="color: #2d3748;">${data.property}</strong> ya se encuentra disponible para su cancelación.</p>
          
          <div style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; margin-bottom: 30px;">
            <table style="width: 100%; border-collapse: collapse; background: #ffffff;">
              ${itemsHtml}
            </table>
          </div>
          
          <div style="background: linear-gradient(135deg, #2b6cb0 0%, #2c5282 100%); color: white; padding: 25px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px rgba(43, 108, 176, 0.2);">
            <span style="font-size: 16px; font-weight: 600; opacity: 0.9;">TOTAL A ABONAR:</span>
            <span style="font-size: 28px; font-weight: 900; letter-spacing: -1px;">${fmt(data.total_amount)}</span>
          </div>

          ${bankHtml}
          ${whatsappHtml}
          
          <div style="margin-top: 40px; text-align: center; font-size: 12px; color: #a0aec0; border-top: 1px solid #edf2f7; padding-top: 20px;">
            <p style="margin: 0;">Este es un aviso automático de la administración. Por favor, no respondas a este correo.</p>
          </div>
        </div>
        
      </div>
    `;

    // Versión de texto plano simplificada
    let textList = details.map(d => `${d.description}: ${fmt(d.amount)}`).join('\n');
    textList += `\n\nTOTAL A ABONAR: ${fmt(data.total_amount)}`;
    if (bank) {
      textList += `\n\nDATOS DE TRANSFERENCIA:\nTitular: ${bank.holder_name}\nBanco: ${bank.bank_entity}\nCBU: ${bank.cbu}\nAlias: ${bank.alias}`;
    }
    if (contact && contact.whatsapp) {
      textList += `\n\nEnviar comprobante por WhatsApp al: ${contact.whatsapp}`;
    }

    // Guardamos las variables para usarlas en el nodo de Gmail
    item.json.detalles_formateados_texto = textList;
    item.json.detalles_formateados_html = htmlList;
    item.json.email_inquilino = data.tenant ? data.tenant.email : '';
  }
}

return $input.all();
JS;
    }
}
