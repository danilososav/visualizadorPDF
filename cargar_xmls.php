<?php
/**
 * Script para cargar XMLs y actualizar facturas en la BD
 * Uso: php cargar_xmls.php
 */

// Configuración BD
$dbHost = 'localhost';
$dbPort = 5432;
$dbName = 'facturas_db';
$dbUser = 'postgres';
$dbPassword = '12345';

// Conexión a BD
try {
    $pdo = new PDO("pgsql:host=$dbHost;port=$dbPort;dbname=$dbName", $dbUser, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Conectado a PostgreSQL\n\n";
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Ruta base de XMLs
$baseDir = 'C:\Users\Usuario\Desktop\visualizadorPDF\XML SICFE';

// Mapeo de carpetas a empresas
$empresasMap = [
    'wilpar' => 1,
    'brick' => 2,
    'ene' => 3,
    'fundacion' => 4,
    'mediabrand' => 5,
    'medialupe' => 6,
    'nasta' => 7,
    'project' => 8,
    'row' => 9,
    'texo' => 10,
    'vila' => 11
];

$totalArchivos = 0;
$archivosProcessados = 0;
$errores = 0;

// Procesar cada carpeta
foreach ($empresasMap as $carpeta => $empresaId) {
    $rutaCarpeta = $baseDir . '\\' . $carpeta . '\\Emitidos';

    if (!is_dir($rutaCarpeta)) {
        echo "⚠ Carpeta no encontrada: $rutaCarpeta\n";
        continue;
    }

    echo "=== Procesando: $carpeta ===\n";

    $archivos = glob($rutaCarpeta . '\*.xml');

    foreach ($archivos as $archivoXml) {
        $totalArchivos++;

        try {
            // Leer y parsear XML
            $xmlContent = file_get_contents($archivoXml);
            $xml = simplexml_load_string($xmlContent);

            if (!$xml) {
                echo "✗ Error al parsear: " . basename($archivoXml) . "\n";
                $errores++;
                continue;
            }

            // Registrarse en el namespace
            $namespaces = $xml->getNamespaces(true);
            $ns = isset($namespaces['']) ? $namespaces[''] : null;

            // Extraer datos del XML
            $de = $xml->DE;
            if (!$de) {
                echo "✗ No encontrado elemento DE en: " . basename($archivoXml) . "\n";
                $errores++;
                continue;
            }

            // Datos generales
            $dNumTim = (string)$de->gTimb->dNumTim;
            $dEst = (string)$de->gTimb->dEst;
            $dPunExp = (string)$de->gTimb->dPunExp;
            $dNumDoc = (string)$de->gTimb->dNumDoc;
            $numeroFactura = $dEst . '-' . $dPunExp . '-' . $dNumDoc;

            $dFeEmiDE = (string)$de->gDatGralOpe->dFeEmiDE;
            $cMoneOpe = (string)$de->gDatGralOpe->gOpeCom->cMoneOpe;
            $dTiCam = (float)$de->gDatGralOpe->gOpeCom->dTiCam;
            $iCondOpe = (int)$de->gDtipDE->gCamCond->iCondOpe;
            $dDCondOpe = (string)$de->gDtipDE->gCamCond->dDCondOpe;
            $dCuotas = (int)$de->gDtipDE->gCamCond->gPagCred->dCuotas;

            // Datos receptor (cliente)
            $dRucRec = (string)$de->gDatGralOpe->gDatRec->dRucRec;
            $dDVRec = (string)$de->gDatGralOpe->gDatRec->dDVRec;
            $dNomRec = (string)$de->gDatGralOpe->gDatRec->dNomRec;
            $dDirRec = (string)$de->gDatGralOpe->gDatRec->dDirRec;
            $dCodCliente = (string)$de->gDatGralOpe->gDatRec->dCodCliente;

            // Totales
            $dTotGralOpe = (float)$de->gTotSub->dTotGralOpe;
            $dTotIVA = (float)$de->gTotSub->dTotIVA;
            $dTotalGs = (float)$de->gTotSub->dTotalGs;

            // Buscar o crear cliente
            $stmtCliente = $pdo->prepare("
                SELECT id FROM clientes
                WHERE ruc = ? AND empresa_id = ?
            ");
            $stmtCliente->execute([$dRucRec, $empresaId]);
            $clienteResult = $stmtCliente->fetch(PDO::FETCH_ASSOC);

            if (!$clienteResult) {
                // Crear cliente
                $stmtInsertCliente = $pdo->prepare("
                    INSERT INTO clientes (empresa_id, ruc, dv, nombre, direccion, creado_en)
                    VALUES (?, ?, ?, ?, ?, NOW())
                    RETURNING id
                ");
                $stmtInsertCliente->execute([$empresaId, $dRucRec, $dDVRec, $dNomRec, $dDirRec]);
                $clienteId = $stmtInsertCliente->fetchColumn();
                echo "  + Cliente creado: $dNomRec (ID: $clienteId)\n";
            } else {
                $clienteId = $clienteResult['id'];
            }

            // Buscar o crear factura
            $stmtFactura = $pdo->prepare("
                SELECT id FROM facturas
                WHERE numero = ? AND empresa_id = ?
            ");
            $stmtFactura->execute([$numeroFactura, $empresaId]);
            $facturaResult = $stmtFactura->fetch(PDO::FETCH_ASSOC);

            if ($facturaResult) {
                // Actualizar factura
                $facturaId = $facturaResult['id'];
                $stmtUpdate = $pdo->prepare("
                    UPDATE facturas SET
                        cliente_id = ?,
                        fecha_emision = ?,
                        tipo_operacion = ?,
                        desc_tipo_operacion = ?,
                        moneda = ?,
                        tasa_cambio = ?,
                        subtotal = ?,
                        iva = ?,
                        total = ?,
                        numero_timbre = ?,
                        xml_contenido = ?,
                        xml_archivo = ?,
                        actualizada_en = NOW()
                    WHERE id = ?
                ");
                $stmtUpdate->execute([
                    $clienteId,
                    $dFeEmiDE,
                    $iCondOpe,
                    $dDCondOpe,
                    $cMoneOpe,
                    $dTiCam,
                    $dTotGralOpe,
                    $dTotIVA,
                    $dTotalGs,
                    $dNumTim,
                    $xmlContent,
                    basename($archivoXml),
                    $facturaId
                ]);
                echo "  ✓ Actualizada: $numeroFactura\n";
            } else {
                // Crear factura
                $stmtInsert = $pdo->prepare("
                    INSERT INTO facturas (
                        empresa_id, cliente_id, numero, fecha_emision, tipo_operacion,
                        desc_tipo_operacion, moneda, tasa_cambio, subtotal, iva, total,
                        numero_timbre, xml_contenido, xml_archivo, estado, creada_en, creada_por
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'CARGA_XML')
                ");
                $stmtInsert->execute([
                    $empresaId, $clienteId, $numeroFactura, $dFeEmiDE, $iCondOpe,
                    $dDCondOpe, $cMoneOpe, $dTiCam, $dTotGralOpe, $dTotIVA, $dTotalGs,
                    $dNumTim, $xmlContent, basename($archivoXml), 'VIGENTE'
                ]);
                echo "  ✓ Creada: $numeroFactura\n";
            }

            // Procesar items
            $items = $de->gDtipDE->gCamItem;
            foreach ($items as $item) {
                $dCodInt = (string)$item->dCodInt;
                $dDesProSer = (string)$item->dDesProSer;
                $cUniMed = (string)$item->cUniMed;
                $dDesUniMed = (string)$item->dDesUniMed;
                $dCantProSer = (float)$item->dCantProSer;
                $dPUniProSer = (float)$item->gValorItem->dPUniProSer;
                $dTotOpeItem = (float)$item->gValorItem->gValorRestaItem->dTotOpeItem;

                $stmtItemCheck = $pdo->prepare("
                    SELECT id FROM items_factura
                    WHERE factura_id = ? AND codigo = ?
                ");
                $stmtItemCheck->execute([$facturaId, $dCodInt]);

                if (!$stmtItemCheck->fetch()) {
                    $stmtItem = $pdo->prepare("
                        INSERT INTO items_factura (
                            factura_id, codigo, descripcion, cantidad, unidad_medida,
                            precio_unitario, total_item
                        ) VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmtItem->execute([
                        $facturaId, $dCodInt, $dDesProSer, $dCantProSer, $dDesUniMed,
                        $dPUniProSer, $dTotOpeItem
                    ]);
                }
            }

            $archivosProcessados++;

        } catch (Exception $e) {
            echo "✗ Error: " . basename($archivoXml) . " - " . $e->getMessage() . "\n";
            $errores++;
        }
    }

    echo "\n";
}

// Resumen
echo "======================================\n";
echo "RESUMEN DE CARGA:\n";
echo "Total archivos: $totalArchivos\n";
echo "Procesados: $archivosProcessados\n";
echo "Errores: $errores\n";
echo "======================================\n";
?>
