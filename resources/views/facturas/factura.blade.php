<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
html, body { height:100%; }
body { font-family: Arial, sans-serif; font-size: 11px; background:#fff; display:flex; justify-content:center; padding:20px; min-height:100vh; }

.page {
  width: 750px;
  display: flex;
  flex-direction: column;
  min-height: calc(100vh - 40px);
}

/* ===== BLOQUES GENERALES ===== */
.bloque { border: 1px solid #555; margin-bottom: 6px; flex-shrink: 0; }

/* ===== HEADER ===== */
.header { display:flex; align-items:stretch; padding:12px; gap:10px; }
.header-logo { width:110px; flex-shrink:0; display:flex; align-items:center; justify-content:center; }
.header-logo img { width:90px; }
.header-center { flex:1; padding-left:10px; }
.header-center .doc-label { font-weight:bold; font-size:11px; }
.header-center .company { font-size:13px; font-weight:bold; margin:2px 0 6px; }
.header-center .info { font-size:10px; line-height:1.7; }
.header-right { width:220px; flex-shrink:0; font-size:10px; line-height:1.8; padding-left:14px; }
.header-right .doc-tipo { font-weight:bold; font-size:11px; }
.header-right .doc-num { font-size:11px; }

/* ===== RECEPTOR ===== */
.receptor {
  padding: 8px 12px;
  font-size: 10px;
  line-height: 1.85;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0;
}
.receptor .col-left  { grid-column: 1; }
.receptor .col-right { grid-column: 2; }
.receptor b { font-weight: bold; }

/* ===== TABLA: bloque flex column que crece ===== */
.tabla-bloque {
  border: 1px solid #555;
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

/* La table normal — NO flex — para que las columnas funcionen bien */
.tabla-bloque table {
  width: 100%;
  border-collapse: collapse;
  font-size: 10px;
  table-layout: fixed;
}

thead tr:first-child th { background:#d9d9d9; font-weight:bold; text-align:center; border:1px solid #999; padding:4px 3px; }
thead tr:last-child th  { background:#d9d9d9; font-weight:bold; text-align:center; border:1px solid #999; padding:3px; font-size:9px; }
tbody td { border:1px solid #bbb; padding:4px 3px; vertical-align:top; }

/* Área vacía entre datos y footer — crece para llenar espacio */
.area-vacia {
  flex: 1;
  border-left: 1px solid #bbb;
  border-right: 1px solid #bbb;
  min-height: 40px;
}

/* Footer de totales — tabla normal al fondo */
.tabla-footer {
  flex-shrink: 0;
}
.tabla-footer table {
  width: 100%;
  border-collapse: collapse;
  font-size: 10px;
  table-layout: fixed;
}
.tabla-footer td { border:1px solid #bbb; padding:3px 4px; }

/* Anchos de columna — deben coincidir en thead y footer */
.c-cod   { width:42px;  text-align:center; }
.c-desc  { text-align:left; }
.c-uni   { width:52px;  text-align:center; }
.c-cant  { width:52px;  text-align:center; }
.c-pu    { width:68px;  text-align:right; padding-right:4px; }
.c-desc2 { width:40px;  text-align:center; }
.c-ex    { width:60px;  text-align:right; padding-right:4px; }
.c-5     { width:48px;  text-align:right; padding-right:4px; }
.c-10    { width:62px;  text-align:right; padding-right:4px; }
</style>
</head>
<body>
<div class="page">

  <!-- BLOQUE 1: HEADER -->
  <div class="bloque">
    <div class="header">
      <div class="header-logo">
        <img src="{{ asset('atomik.png') }}" alt="Atomik logo" style="width:100px;">
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
      <div class="header-right">
        <div><b>RUC:</b> {{ $ruc_empresa }}</div>
        <div><b>Timbrado N°:</b> {{ $num_timbrado }}</div>
        <div><b>Inicio de vigencia:</b> 30-10-2024</div>
        <div class="doc-tipo">Factura electrónica</div>
        <div class="doc-num"><b>N°:</b> 001-001-{{ $num_factura }}</div>
      </div>
    </div>
  </div>

  <!-- BLOQUE 2: RECEPTOR -->
  <div class="bloque">
    <div class="receptor">
      <div class="col-left"><b>Fecha de emisión:</b> {{ $fecha_emision }} &nbsp;00:00:00</div>
      <div class="col-right"><b>Condición de venta:</b> {{ $condicion_venta }}</div>

      <div class="col-left"><b>RUC/documento de identidad:</b> {{ $ruc_receptor }}</div>
      <div class="col-right"><b>Cuotas:</b> {{ $cuotas }}</div>

      <div class="col-left"><b>Código Cliente:</b> {{ $codigo_cliente }}</div>
      <div class="col-right">&nbsp;</div>

      <div class="col-left"><b>Nombre o razón social:</b> {{ $nombre_cliente }}</div>
      <div class="col-right"><b>Moneda:</b> {{ $moneda }}</div>

      <div class="col-left" style="padding-top:4px;"><b>Tipo de transacción:</b> Prestación de servicios</div>
      <div class="col-right" style="padding-top:4px;"><b>Dirección:</b> {{ $direccion }}</div>

      <div class="col-left">&nbsp;</div>
      <div class="col-right"><b>Correo electrónico:</b> {{ $email }}</div>

      <div class="col-left">&nbsp;</div>
      <div class="col-right" style="padding-bottom:4px;"><b>Tipo de cambio:</b> {{ $tipo_cambio }}</div>
    </div>
  </div>

  <!-- BLOQUE 3: TABLA -->
  <div class="tabla-bloque">

    <!-- Cabecera + filas de datos: tabla normal -->
    <table>
      <colgroup>
        <col class="c-cod">
        <col class="c-desc">
        <col class="c-uni">
        <col class="c-cant">
        <col class="c-pu">
        <col class="c-desc2">
        <col class="c-ex">
        <col class="c-5">
        <col class="c-10">
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
      <tbody>
        @foreach($items as $item)
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
    </table>

    <!-- Espacio vacío que crece -->
    <div class="area-vacia"></div>

    <!-- Footer de totales: tabla separada con mismos anchos de col -->
    <div class="tabla-footer">
      <table>
        <colgroup>
          <col class="c-cod">
          <col class="c-desc">
          <col class="c-uni">
          <col class="c-cant">
          <col class="c-pu">
          <col class="c-desc2">
          <col class="c-ex">
          <col class="c-5">
          <col class="c-10">
        </colgroup>
        <tbody>
          <tr style="background:#d9d9d9;">
            <td colspan="6" style="font-weight:bold; border:1px solid #bbb; padding:3px 4px;">SUBTOTAL</td>
            <td style="text-align:right; border:1px solid #bbb; padding:3px 4px;">0</td>
            <td style="text-align:right; border:1px solid #bbb; padding:3px 4px;">0</td>
            <td style="text-align:right; border:1px solid #bbb; padding:3px 4px;">{{ $subtotal }}</td>
          </tr>
          <tr style="background:#d9d9d9;">
            <td colspan="8" style="font-weight:bold; border:1px solid #bbb; padding:3px 4px;">TOTAL DE LA OPERACIÓN</td>
            <td style="text-align:right; border:1px solid #bbb; padding:3px 4px;">{{ $total_operacion }}</td>
          </tr>
          <tr style="background:#555; color:#fff;">
            <td colspan="8" style="font-weight:bold; border:1px solid #555; padding:3px 4px;">TOTAL EN GUARANÍES</td>
            <td style="text-align:right; border:1px solid #555; padding:3px 4px;">{{ $total_guaranies }}</td>
          </tr>
          <tr>
            <td colspan="3" style="font-weight:bold; border:1px solid #bbb; padding:3px 4px;">LIQUIDACIÓN IVA</td>
            <td colspan="2" style="border:1px solid #bbb; padding:3px 4px;">(5%) &nbsp; {{ $iva_cinco }}</td>
            <td colspan="2" style="border:1px solid #bbb; padding:3px 4px;">(10%) &nbsp; {{ $iva_diez }}</td>
            <td colspan="2" style="border:1px solid #bbb; padding:3px 4px; font-weight:bold;">TOTAL IVA: &nbsp; {{ $total_iva }}</td>
          </tr>
          <tr>
            <td colspan="9" style="border:1px solid #bbb; padding:3px 4px;">Info fiscal</td>
          </tr>
        </tbody>
      </table>
    </div>

  </div><!-- fin tabla-bloque -->

</div>
</body>
</html>
