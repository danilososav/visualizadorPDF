<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
html, body { height:100%; }
body { font-family: Arial, sans-serif; font-size: 11px; background:#fff; display:flex; justify-content:center; padding:20px; min-height:100vh; }
.page { width: 750px; display: flex; flex-direction: column; min-height: calc(100vh - 40px); }
.bloque { border: 1px solid #555; margin-bottom: 6px; flex-shrink: 0; }
.header { display:flex; align-items:stretch; padding:12px; gap:10px; }
.header-logo { width:110px; flex-shrink:0; display:flex; align-items:center; justify-content:center; }
.header-logo img { width:90px; }
.header-center { flex:1; padding-left:10px; }
.header-center .doc-label { font-weight:bold; font-size:11px; }
.header-center .company { font-size:11px;  margin:2px 0 6px; }
.header-center .info { font-size:10px; line-height:1.7; }
.header-right { width:220px; flex-shrink:0; font-size:10px; line-height:1.8; padding-left:14px; }
.header-right .doc-tipo { font-weight:bold; font-size:11px; }
.header-right .doc-num { font-size:11px; }
.receptor { padding: 8px 12px; font-size: 10px; line-height: 1.85; display: grid; grid-template-columns: 1fr 1fr; gap: 0; }
.receptor .col-left { grid-column: 1; }
.receptor .col-right { grid-column: 2; }
.receptor b { font-weight: bold; }

/* ===== TABLA ===== */
.tabla-bloque {
    border: 1px solid #555;
    flex: 1;
    overflow: hidden;
}

.tabla-bloque table {
    width: 100%;
    border-collapse: collapse;
    font-size: 10px;
    table-layout: fixed;
    height: 100%;
}

/* Anchos de columna */
col.c-cod  { width: 42px; }
col.c-desc { width: auto; }
col.c-uni  { width: 52px; }
col.c-cant { width: 52px; }
col.c-pu   { width: 68px; }
col.c-desc2{ width: 40px; }
col.c-ex   { width: 60px; }
col.c-5    { width: 48px; }
col.c-10   { width: 62px; }

.c-cod  { text-align:center; }
.c-desc { text-align:left; }
.c-uni  { text-align:center; }
.c-cant { text-align:center; }
.c-pu   { text-align:right; padding-right:4px; }
.c-desc2{ text-align:center; }
.c-ex   { text-align:right; padding-right:4px; }
.c-5    { text-align:right; padding-right:4px; }
.c-10   { text-align:right; padding-right:4px; }

thead tr:first-child th {
    background:#d9d9d9; font-weight:bold; text-align:center;
    border:1px solid #999; padding:4px 3px;
}
thead tr:last-child th {
    background:#d9d9d9; font-weight:bold; text-align:center;
    border:1px solid #999; padding:3px; font-size:9px;
}

/* Filas de datos — sin border-bottom en la última fila */
tbody.datos td {
    border-right: 1px solid #bbb;
    border-bottom: none;
    padding: 4px 3px;
    vertical-align: top;
}
tbody.datos td:last-child { border-right: none; }
/* tbody.datos tr:last-child td { border-bottom: none; } */



/* Fila de relleno */
tbody.relleno { height: 100%; }
tbody.relleno tr { height: 100%; }
tbody.relleno td {
    border-right: 1px solid #bbb;
    border-bottom: none;
    height: 100%;
    padding: 0;
    vertical-align: top;
}
tbody.relleno td:last-child { border-right: none; }

/* Tfoot — sin border-top en primera fila para no duplicar línea */
tfoot td {
    border: 1px solid #bbb;
    padding: 3px 4px;
    font-size: 10px;
}
tfoot tr:first-child td { border-top: 1px solid #555; }
tfoot tr.sub  td { background: #d9d9d9; }
tfoot tr.guar td { background: #555; color: #fff; border-color: #555; }
tfoot tr.iva  td { background: #fff; }
tfoot tr.info td { background: #fff; }
tfoot .fw { font-weight: bold; }
tfoot .tr { text-align: right; }

@media print {
    a[href*="/descargar-pdf"] {
        display: none !important;
    }
}
</style>
</head>
<body>
<div class="page">

  <!-- BLOQUE 1: HEADER -->
<div class="bloque">
  <div class="header">
    <div class="header-logo">
      <img src="{{ $logo_base64 }}" alt="Logo" style="width:100px;">
    </div>
    <div class="header-center">
      <div class="doc-label">KuDE de Factura electrónica</div>
      <div class="company">{{ $nombre_empresa }}</div>
      <div class="info">
        {{ $actividad_empresa }}<br>
        {{ $direccion_empresa }}<br><br>
        {{ $ciudad_empresa }}<br>
        {{ $email_empresa }} &nbsp; {{ $telefono_empresa }}
      </div>
    </div>
    <div class="header-right">
      <div><b>RUC:</b> {{ $ruc_empresa }}</div>
      <div><b>Timbrado N°:</b> {{ $num_timbrado }}</div>
      <div><b>Inicio de vigencia:</b> {{ $fecha_inicio_vigencia }}</div>
      <div class="doc-tipo">{{ $tipo_documento }}</div>
      <div class="doc-num"><b>N°:</b> {{ $num_factura }}</div>
    </div>
  </div>
</div>

  <!-- BLOQUE 2: RECEPTOR -->
  <div class="bloque">
    <div class="receptor">
        <div class="col-left"><b>Fecha de emisión:</b> {{ $fecha_emision }}</div>
        @if($tipo_documento === 'Factura electrónica')
            <div class="col-right"><b>Condición de venta:</b> {{ $condicion_venta }}</div>
        @else
            <div class="col-right">&nbsp;</div>
        @endif

      <div class="col-left"><b>RUC/documento de identidad:</b> {{ $ruc_receptor }}</div>
      @if($tipo_documento === 'Factura electrónica')
        <div class="col-right"><b>Cuotas:</b> {{ $cuotas }}</div>
      @else
        <div class="col-right">&nbsp;</div>
     @endif

      <div class="col-left"><b>Código Cliente:</b> {{ $codigo_cliente }}</div>
      <div class="col-right">&nbsp;</div>

      <div class="col-left"><b>Nombre o razón social:</b> {{ $nombre_cliente }}</div>
      <div class="col-right"><b>Moneda:</b> {{ $moneda }}</div>

      <div class="col-left" style="padding-top:4px;"><b>Tipo de transacción:</b> Prestación de servicios</div>
      <div class="col-right" style="padding-top:4px;"><b>Dirección:</b> {{ $direccion }}</div>

      <div class="col-left">&nbsp;</div>
      <div class="col-right"><b>Correo electrónico:</b> {{ $email }}</div>

      <div class="col-left">&nbsp;</div>
      <div class="col-right" style="padding-bottom:4px;"><b>Tipo de cambio:</b> <span data-number="{{ $tipo_cambio }}">{{ $tipo_cambio }}</span></div>
    </div>
  </div>

  <!-- BLOQUE 3: TABLA — una sola table -->
  <div class="tabla-bloque">
    <table>
      <colgroup>
        <col class="c-cod"><col class="c-desc"><col class="c-uni"><col class="c-cant">
        <col class="c-pu"><col class="c-desc2"><col class="c-ex"><col class="c-5"><col class="c-10">
      </colgroup>

      <thead>
        <tr>
          <th class="c-cod" rowspan="2">Código</th>
          <th class="c-desc" rowspan="2">Descripción</th>
          <th class="c-uni" rowspan="2">Unidad</th>
          <th class="c-cant" rowspan="2">Cantidad</th>
          <th class="c-pu" rowspan="2">Precio<br>Unitario</th>
          <th class="c-desc2" rowspan="2">Desc.</th>
          <th colspan="3">Valor de Venta</th>
        </tr>
        <tr>
          <th class="c-ex">Exentas</th>
          <th class="c-5">5%</th>
          <th class="c-10">10%</th>
        </tr>
      </thead>

      <!-- Filas de datos -->
      <tbody class="datos">
        @foreach ($items as $item)
        <tr>
          <td class="c-cod">{{ $item['codigo'] }}</td>
          <td class="c-desc">{{ $item['descripcion'] }}</td>
          <td class="c-uni">{{ $item['unidad'] }}</td>
          <td class="c-cant">{{ $item['cantidad'] }}</td>
          <td class="c-pu">{{ $item['precio_unitario'] }}</td>
          <td class="c-desc2">{{ $item['descuento'] }}</td>
          <td class="c-ex">{{ $item['exentas'] }}</td>
          <td class="c-5">{{ $item['cinco_porciento'] }}</td>
          <td class="c-10">{{ $item['diez_porciento'] }}</td>
        </tr>
        @endforeach
      </tbody>

      <!-- Fila relleno que se estira -->
      <tbody class="relleno">
        <tr>
          <td class="c-cod"></td><td class="c-desc"></td><td class="c-uni"></td>
          <td class="c-cant"></td><td class="c-pu"></td><td class="c-desc2"></td>
          <td class="c-ex"></td><td class="c-5"></td><td class="c-10"></td>
        </tr>
      </tbody>

      <tfoot>
        <tr class="sub">
            <td colspan="6" class="fw">SUBTOTAL</td>
            <td class="tr">0</td>
            <td class="tr">0</td>
            <td class="tr" data-number="{{ $subtotal }}">{{ $subtotal }}</td>
        </tr>
        <tr class="sub">
            <td colspan="8" class="fw">TOTAL DE LA OPERACIÓN</td>
            <td class="tr" data-number="{{ $total_operacion }}">{{ $total_operacion }}</td>
        </tr>
        <tr class="guar">
            <td colspan="8" class="fw">TOTAL EN GUARANÍES</td>
            <td class="tr" data-number="{{ $total_guaranies }}">{{ $total_guaranies }}</td>
        </tr>
        <tr class="iva">
            <td colspan="3" class="fw">LIQUIDACIÓN IVA</td>
            <td colspan="2">(5%) &nbsp; <span data-number="{{ $iva_cinco }}">{{ $iva_cinco }}</span></td>
            <td colspan="2">(10%) &nbsp; <span data-number="{{ $iva_diez }}">{{ $iva_diez }}</span></td>
            <td colspan="2" class="fw">TOTAL IVA: &nbsp; <span data-number="{{ $total_iva }}">{{ $total_iva }}</span></td>
        </tr>
        <tr class="info">
            <td colspan="9">Info fiscal</td>
        </tr>
        </tfoot>

    <script>
function formatNumberPY(value) {
  if (!value || value == 0) return '0';

  const num = parseFloat(value);

  // Si es entero, sin decimales
  if (Number.isInteger(num)) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }

  // Si tiene decimales
  const parts = num.toFixed(2).split('.');
  const integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');

  // Si el segundo decimal es 0, mostrar solo 1 decimal
  if (parts[1] === '00') {
    return integerPart;
  }
  if (parts[1].endsWith('0') && parts[1] !== '00') {
    return integerPart + ',' + parts[1].substring(0, 1);
  }

  return integerPart + ',' + parts[1];
}

document.addEventListener('DOMContentLoaded', () => {
  // Formatear todos los números con data-number
  document.querySelectorAll('[data-number]').forEach(el => {
    el.textContent = formatNumberPY(el.getAttribute('data-number'));
  });
});

document.addEventListener('DOMContentLoaded', () => {
    const facturaParts = window.location.pathname.split('/');
    const facturaId = facturaParts[facturaParts.length - 2];

    const btn = document.createElement('a');
    btn.href = `/api/facturas/${facturaId}/descargar-pdf`;
    btn.textContent = '📥 Descargar PDF';
    btn.className = 'btn-descargar';
    btn.style.cssText = 'position: fixed; bottom: 20px; right: 20px; padding: 10px 15px; background: #007bff; color: white; border-radius: 5px; text-decoration: none; z-index: 1000;';
    document.body.appendChild(btn);
});

document.addEventListener('DOMContentLoaded', () => {
    // Solo mostrar botón si NO estamos descargando (si no está en iframe o es visualización)
    if (window.self === window.top) {
        const facturaId = {{ $factura_id ?? 0 }};

        const btn = document.createElement('a');
        btn.href = `/api/facturas/${facturaId}/descargar-pdf`;
        btn.textContent = '📥 Descargar PDF';
        btn.className = 'btn-descargar';
        btn.style.cssText = 'position: fixed; bottom: 20px; right: 20px; padding: 10px 15px; background: #007bff; color: white; border-radius: 5px; text-decoration: none; z-index: 1000; font-size: 14px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);';
        btn.target = '_self';
        document.body.appendChild(btn);
    }
});
</script>

    </table>
  </div>

</div>
</body>
</html>
