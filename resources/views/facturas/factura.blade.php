<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            background: #fff;
            display: flex;
            justify-content: center;
            padding: 20px;
        }

        .page {
            width: 750px;
        }

        /* ===== BLOQUE 1: HEADER ===== */
        .bloque {
            border: 1px solid #555;
            margin-bottom: 6px;
        }

        .header {
            display: flex;
            align-items: stretch;
            padding: 12px;
            gap: 10px;
        }

        .header-logo {
            width: 110px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header-logo img {
            width: 90px;
        }

        .header-center {
            flex: 1;
            padding-left: 10px;
        }

        .header-center .doc-label {
            font-weight: bold;
            font-size: 11px;
        }

        .header-center .company {
            font-size: 13px;
            font-weight: bold;
            margin: 2px 0 6px;
        }

        .header-center .info {
            font-size: 10px;
            line-height: 1.7;
        }

        .header-right {
            width: 220px;
            flex-shrink: 0;
            font-size: 10px;
            line-height: 1.8;
            padding-left: 14px;
        }

        .header-right .doc-tipo {
            font-weight: bold;
            font-size: 11px;
        }

        .header-right .doc-num {
            font-size: 11px;
        }

        /* ===== BLOQUE 2: RECEPTOR ===== */
        .receptor { padding:10px 12px; font-size:10px; line-height:1.9; display:grid; grid-template-columns:1fr 1fr; gap:0 30px; }
        .receptor .full { grid-column:1/-1; }
        .receptor b { font-weight:bold; }

        /* ===== BLOQUE 3: TABLA ===== */
        .tabla-bloque {
            border: 1px solid #555;
            margin-bottom: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        thead tr:first-child th {
            background: #d9d9d9;
            font-weight: bold;
            text-align: center;
            border: 1px solid #999;
            padding: 4px 3px;
        }

        thead tr:last-child th {
            background: #d9d9d9;
            font-weight: bold;
            text-align: center;
            border: 1px solid #999;
            padding: 3px;
            font-size: 9px;
        }

        tbody td {
            border: 1px solid #bbb;
            padding: 4px 3px;
            vertical-align: top;
        }

        /* Columnas */
        .c-cod {
            width: 42px;
            text-align: center;
        }

        .c-desc {
            text-align: left;
        }

        .c-uni {
            width: 52px;
            text-align: center;
        }

        .c-cant {
            width: 52px;
            text-align: center;
        }

        .c-pu {
            width: 68px;
            text-align: right;
        }

        .c-desc2 {
            width: 40px;
            text-align: center;
        }

        .c-ex {
            width: 60px;
            text-align: right;
        }

        .c-5 {
            width: 48px;
            text-align: right;
        }

        .c-10 {
            width: 60px;
            text-align: right;
        }

        .sub-label {
            font-weight: bold;
        }

        .sub-right {
            text-align: right;
        }
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
                <div><b>Fecha de emisión:</b> {{ $fecha_emision }} &nbsp;00:00:00</div>
                <div><b>Condición de venta:</b> {{ $condicion_venta }}</div>
                <div><b>RUC/documento de identidad:</b> {{ $ruc_receptor }}</div>
                <div><b>Cuotas:</b> {{ $cuotas }}</div>
                <div><b>Código Cliente:</b> {{ $codigo_cliente }}</div>
                <div class="full" style="margin-top:2px;"><b>Moneda:</b> {{ $moneda }}</div>
                <div class="full"><b>Nombre o razón social:</b> {{ $nombre_cliente }}</div>
                <div style="margin-top:4px;"><b>Tipo de transacción:</b> Prestación de servicios</div>
                <div style="margin-top:4px;"><b>Dirección:</b> {{ $direccion }}</div>
                <div></div>
                <div><b>Correo electrónico:</b> {{ $email }}</div>
                <div></div>
                <div><b>Tipo de cambio:</b> {{ $tipo_cambio }}</div>
            </div>
        </div>

        <!-- BLOQUE 3: TABLA -->
        <div class="tabla-bloque">
            <table>
                <thead>f
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
                    <tr>
                        <td colspan="9"
                            style="height:450px; border:none; border-left:1px solid #bbb; border-right:1px solid #bbb;">
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" class="sub-label"
                            style="border:1px solid #bbb; padding:3px 4px; background:#d9d9d9;">SUBTOTAL</td>
                        <td class="sub-right" style="border:1px solid #bbb; padding:3px 4px; background:#d9d9d9;">0</td>
                        <td class="sub-right" style="border:1px solid #bbb; padding:3px 4px; background:#d9d9d9;">0</td>
                        <td class="sub-right" style="border:1px solid #bbb; padding:3px 4px; background:#d9d9d9;">
                            {{ $subtotal }}</td>
                    </tr>
                    <tr>
                        <td colspan="8" class="sub-label"
                            style="border:1px solid #bbb; padding:3px 4px; background:#d9d9d9;">TOTAL DE LA OPERACIÓN
                        </td>
                        <td class="sub-right" style="border:1px solid #bbb; padding:3px 4px; background:#d9d9d9;">
                            {{ $total_operacion }}</td>
                    </tr>
                    <tr>
                        <td colspan="8" class="sub-label"
                            style="border:1px solid #555; padding:3px 4px; background:#555; color:#fff;">TOTAL EN
                            GUARANÍES</td>
                        <td class="sub-right"
                            style="border:1px solid #555; padding:3px 4px; background:#555; color:#fff;">
                            {{ $total_guaranies }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="border:1px solid #bbb; padding:3px 4px; font-weight:bold;">LIQUIDACIÓN
                            IVA</td>
                        <td colspan="2" style="border:1px solid #bbb; padding:3px 4px;">(5%) &nbsp;
                            {{ $iva_cinco }}</td>
                        <td colspan="2" style="border:1px solid #bbb; padding:3px 4px;">(10%) &nbsp;
                            {{ $iva_diez }}</td>
                        <td colspan="2" style="border:1px solid #bbb; padding:3px 4px; font-weight:bold;">TOTAL IVA:
                            &nbsp; {{ $total_iva }}</td>
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
