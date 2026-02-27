<?php
/**
 * Script PHP para generar PDF de factura desde HTML
 * Uso: php generar_factura_pdf.php
 */

// Datos de ejemplo (estos vendrían de la BD en Laravel)
$datos = [
    'ruc_empresa' => '80114643-7',
    'num_timbrado' => '17593723',
    'num_factura' => '0000718',
    'fecha_emision' => '12-11-2025',
    'ruc_receptor' => '80022570-8',
    'codigo_cliente' => '000000000000669',
    'nombre_cliente' => 'BANCO FAMILIAR SAECA',
    'condicion_venta' => 'Crédito',
    'cuotas' => '1',
    'moneda' => 'US Dollar',
    'direccion' => 'CHILE 1080',
    'email' => 'arnaldo.castro@familiar.com.py',
    'tipo_cambio' => '7060.17',
    'items' => [
        [
            'codigo' => '1',
            'descripcion' => 'Plataforma Publicidad, Banco Familiar, TC Beneficios - Consumo Noviembre, Rich Media Plus, Inicio: 1/11/2025 al 30/11/2025,',
            'unidad' => 'UNI',
            'cantidad' => '1',
            'precio_unitario' => '2.640',
            'descuento' => '0',
            'exentas' => '0',
            'cinco_porciento' => '0',
            'diez_porciento' => '2.640,00'
        ]
    ],
    'subtotal' => '2.640',
    'total_operacion' => '2.640',
    'total_guaranies' => '18.638.849',
    'iva_cinco' => '0',
    'iva_diez' => '240',
    'total_iva' => '240'
];

/**
 * Función para generar el HTML con datos
 */
function generarHTML($datos) {
    // Generar filas de items
    $filas_items = '';
    foreach ($datos['items'] as $item) {
        $filas_items .= <<<HTML
        <tr>
          <td class="c-cod">{$item['codigo']}</td>
          <td class="c-desc">{$item['descripcion']}</td>
          <td class="c-uni">{$item['unidad']}</td>
          <td class="c-cant">{$item['cantidad']}</td>
          <td class="c-pu">{$item['precio_unitario']}</td>
          <td class="c-desc2">{$item['descuento']}</td>
          <td class="c-ex">{$item['exentas']}</td>
          <td class="c-5">{$item['cinco_porciento']}</td>
          <td class="c-10">{$item['diez_porciento']}</td>
        </tr>
HTML;
    }

    $html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: Arial, sans-serif; font-size: 11px; background:#fff; display:flex; justify-content:center; padding:20px; }
.page { width: 750px; }

/* ===== BLOQUE 1: HEADER ===== */
.bloque { border: 1px solid #555; margin-bottom: 6px; }

.header { display:flex; align-items:stretch; padding:12px; gap:10px; }
.header-logo { width:110px; flex-shrink:0; display:flex; align-items:center; justify-content:center; }
.header-logo img { width:90px; }
.logo-placeholder { width:90px; height:45px; border:2px solid #e05; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:bold; color:#e05; gap:4px; }
.header-center { flex:1; padding-left:10px; }
.header-center .doc-label { font-weight:bold; font-size:11px; }
.header-center .company { font-size:13px; font-weight:bold; margin:2px 0 6px; }
.header-center .info { font-size:10px; line-height:1.7; }
.header-right { width:220px; flex-shrink:0; font-size:10px; line-height:1.8; padding-left:14px; border-left:1px solid #ccc; }
.header-right .doc-tipo { font-weight:bold; font-size:11px; }
.header-right .doc-num { font-size:11px; }

/* ===== BLOQUE 2: RECEPTOR ===== */
.receptor { padding:10px 12px; font-size:10px; line-height:1.9; display:grid; grid-template-columns:1fr 1fr; gap:0 30px; }
.receptor .full { grid-column:1/-1; }
.receptor b { font-weight:bold; }

/* ===== BLOQUE 3: TABLA ===== */
.tabla-bloque { border:1px solid #555; margin-bottom:6px; }
table { width:100%; border-collapse:collapse; font-size:10px; }
thead tr:first-child th { background:#d9d9d9; font-weight:bold; text-align:center; border:1px solid #999; padding:4px 3px; }
thead tr:last-child th { background:#d9d9d9; font-weight:bold; text-align:center; border:1px solid #999; padding:3px; font-size:9px; }
tbody td { border:1px solid #bbb; padding:4px 3px; vertical-align:top; }
.tbody-area { min-height:280px; }

/* Columnas */
.c-cod   { width:42px; text-align:center; }
.c-desc  { text-align:left; }
.c-uni   { width:52px; text-align:center; }
.c-cant  { width:52px; text-align:center; }
.c-pu    { width:68px; text-align:right; }
.c-desc2 { width:40px; text-align:center; }
.c-ex    { width:60px; text-align:right; }
.c-5     { width:48px; text-align:right; }
.c-10    { width:60px; text-align:right; }

/* Subtotales al final de la tabla */
.subtotales { border-top:1px solid #555; }
.subtotales tr td { border:1px solid #bbb; padding:3px 4px; font-size:10px; }
.sub-label { font-weight:bold; }
.sub-right { text-align:right; }
.total-row td { background:#d9d9d9; font-weight:bold; }
.guaranies-row td { background:#555; color:#fff; font-weight:bold; }
.iva-row td { background:#fff; }

/* ===== BLOQUE 4: QR/CDC ===== */
.cdc-bloque { border:1px solid #555; padding:12px; display:flex; gap:14px; align-items:flex-start; }
.qr-box { width:110px; flex-shrink:0; }
.qr-box img { width:110px; }
.qr-placeholder { width:110px; height:110px; border:1px solid #999; display:flex; align-items:center; justify-content:center; font-size:10px; color:#aaa; background:#f9f9f9; }
.cdc-text { flex:1; font-size:10px; line-height:1.6; }
.cdc-text .url { color:#000; }
.cdc-text .cdc-num { font-size:14px; font-weight:bold; letter-spacing:1px; margin:3px 0; }
.cdc-text .rep { font-size:9px; color:#333; }
.cdc-text .id { font-size:9px; margin-top:4px; }
.page-num { text-align:right; font-size:9px; margin-top:4px; color:#555; }
</style>
</head>
<body>
<div class="page">

  <!-- BLOQUE 1: HEADER -->
  <div class="bloque">
    <div class="header">
      <div class="header-logo">
        <img src="data:image/png;base64,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" alt="Atomik logo" style="width:100px;">
      </div>
      <div class="header-center">
        <div class="doc-label">KuDE de Factura electrónica</div>
        <div class="company">WILPAR S.A.</div>
        <div class="info">
          Actividades publicitarias<br>
          AVDA BRASILIA CASI GAETANO MARTINO<br>
          <br>
          ASUNCION (DISTRITO)<br>
          GENESIS@ATOMIK.PRO &nbsp; 0991707465
        </div>
      </div>
      <div class="header-right" style="border-left:none;">
        <div><b>RUC:</b> {$datos['ruc_empresa']}</div>
        <div><b>Timbrado N°:</b> {$datos['num_timbrado']}</div>
        <div><b>Inicio de vigencia:</b> 30-10-2024</div>
        <div class="doc-tipo">Factura electrónica</div>
        <div class="doc-num"><b>N°:</b> 001-001-{$datos['num_factura']}</div>
      </div>
    </div>
  </div>

  <!-- BLOQUE 2: RECEPTOR -->
  <div class="bloque">
    <div class="receptor">
      <div><b>Fecha de emisión:</b> {$datos['fecha_emision']} &nbsp;00:00:00</div>
      <div><b>Condición de venta:</b> {$datos['condicion_venta']}</div>
      <div><b>RUC/documento de identidad:</b> {$datos['ruc_receptor']}</div>
      <div><b>Cuotas:</b> {$datos['cuotas']}</div>
      <div><b>Código Cliente:</b> {$datos['codigo_cliente']}</div>
      <div class="full" style="margin-top:2px;"><b>Moneda:</b> {$datos['moneda']}</div>
      <div class="full"><b>Nombre o razón social:</b> {$datos['nombre_cliente']}</div>
      <div style="margin-top:4px;"><b>Tipo de transacción:</b> Prestación de servicios</div>
      <div style="margin-top:4px;"><b>Dirección:</b> {$datos['direccion']}</div>
      <div></div>
      <div><b>Correo electrónico:</b> {$datos['email']}</div>
      <div></div>
      <div><b>Tipo de cambio:</b> {$datos['tipo_cambio']}</div>
    </div>
  </div>

  <!-- BLOQUE 3: TABLA -->
  <div class="tabla-bloque">
    <table>
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
      <tbody>
        {$filas_items}
        <!-- Filas vacías para simular el espacio en blanco del original -->
        <tr><td colspan="9" style="height:220px; border:none; border-left:1px solid #bbb; border-right:1px solid #bbb;"></td></tr>
      </tbody>
      <!-- Subtotales dentro de la tabla -->
      <tfoot>
        <tr>
          <td colspan="6" class="sub-label" style="border:1px solid #bbb; padding:3px 4px; background:#d9d9d9;">SUBTOTAL</td>
          <td class="sub-right" style="border:1px solid #bbb; padding:3px 4px; background:#d9d9d9;">0</td>
          <td class="sub-right" style="border:1px solid #bbb; padding:3px 4px; background:#d9d9d9;">0</td>
          <td class="sub-right" style="border:1px solid #bbb; padding:3px 4px; background:#d9d9d9;">{$datos['subtotal']}</td>
        </tr>
        <tr>
          <td colspan="8" class="sub-label" style="border:1px solid #bbb; padding:3px 4px; background:#d9d9d9;">TOTAL DE LA OPERACIÓN</td>
          <td class="sub-right" style="border:1px solid #bbb; padding:3px 4px; background:#d9d9d9;">{$datos['total_operacion']}</td>
        </tr>
        <tr>
          <td colspan="8" class="sub-label" style="border:1px solid #555; padding:3px 4px; background:#555; color:#fff;">TOTAL EN GUARANÍES</td>
          <td class="sub-right" style="border:1px solid #555; padding:3px 4px; background:#555; color:#fff;">{$datos['total_guaranies']}</td>
        </tr>
        <tr>
          <td colspan="3" style="border:1px solid #bbb; padding:3px 4px; font-weight:bold;">LIQUIDACIÓN IVA</td>
          <td colspan="2" style="border:1px solid #bbb; padding:3px 4px;">(5%) &nbsp; {$datos['iva_cinco']}</td>
          <td colspan="2" style="border:1px solid #bbb; padding:3px 4px;">(10%) &nbsp; {$datos['iva_diez']}</td>
          <td colspan="2" style="border:1px solid #bbb; padding:3px 4px; font-weight:bold;">TOTAL IVA: &nbsp; {$datos['total_iva']}</td>
        </tr>
        <tr>
          <td colspan="9" style="border:1px solid #bbb; padding:3px 4px;">Info fiscal</td>
        </tr>
      </tfoot>
    </table>
  </div>

</div>
</body>
</html>
HTML;

    return $html;
}

// Generar HTML
$html = generarHTML($datos);

// Guardar HTML temporal
$archivo_html = __DIR__ . '/factura_temp.html';
file_put_contents($archivo_html, $html);

echo "✓ HTML generado exitosamente\n";
echo "Archivo: $archivo_html\n\n";

// Mostrar datos reemplazados
echo "=== DATOS UTILIZADOS ===\n";
echo "RUC Empresa: {$datos['ruc_empresa']}\n";
echo "Factura: {$datos['num_factura']}\n";
echo "Cliente: {$datos['nombre_cliente']}\n";
echo "Total: {$datos['total_operacion']}\n";
echo "Total en Guaraníes: {$datos['total_guaranies']}\n\n";

echo "Para convertir a PDF con DomPDF:\n";
echo "1. Copiar el HTML a tu proyecto Laravel\n";
echo "2. Usar: \\\PDF::loadHTML(\$html)->save('factura.pdf')\n";
?>
