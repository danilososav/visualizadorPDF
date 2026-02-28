<?php
/**
 * Script para extraer cuotas del XML y actualizar BD
 * Usa: php actualizar_cuotas_desde_xml.php
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

// Primero, agregar columna si no existe
try {
    $pdo->exec("ALTER TABLE facturas ADD COLUMN IF NOT EXISTS cuotas int DEFAULT 1");
    echo "✓ Columna 'cuotas' verificada/creada\n\n";
} catch (Exception $e) {
    echo "⚠ Columna 'cuotas' ya existe\n\n";
}

// Obtener todas las facturas con XML
$stmt = $pdo->prepare("SELECT id, xml_contenido FROM facturas WHERE xml_contenido IS NOT NULL");
$stmt->execute();
$facturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Procesando " . count($facturas) . " facturas...\n\n";

$procesadas = 0;
$errores = 0;
$actualizadas = 0;

foreach ($facturas as $factura) {
    try {
        $xmlContent = $factura['xml_contenido'];
        $xml = simplexml_load_string($xmlContent);
        
        if (!$xml) {
            echo "✗ Error al parsear XML de factura ID: {$factura['id']}\n";
            $errores++;
            continue;
        }
        
        // Extraer cuotas del XML
        $de = $xml->DE;
        if (!$de) {
            $errores++;
            continue;
        }
        
        // Intentar obtener cuotas de diferentes rutas
        $cuotas = null;
        
        // Ruta 1: gCamCond->gPagCred->dCuotas (para facturas normales)
        if (isset($de->gDtipDE->gCamCond->gPagCred->dCuotas)) {
            $cuotas = (int)$de->gDtipDE->gCamCond->gPagCred->dCuotas;
        }
        // Ruta 2: Si no existe, intentar otra ruta
        else if (isset($de->gDatGralOpe->gOpeCom->dCondTiCam)) {
            $cuotas = 1; // Por defecto
        }
        
        // Si encontramos cuotas, actualizar
        if ($cuotas !== null && $cuotas > 0) {
            $stmtUpdate = $pdo->prepare("UPDATE facturas SET cuotas = ? WHERE id = ?");
            $stmtUpdate->execute([$cuotas, $factura['id']]);
            
            if ($stmtUpdate->rowCount() > 0) {
                $actualizadas++;
            }
        }
        
        $procesadas++;
        
        if ($procesadas % 500 == 0) {
            echo "Procesadas: $procesadas | Actualizadas: $actualizadas | Errores: $errores\n";
        }
        
    } catch (Exception $e) {
        echo "✗ Error en factura ID {$factura['id']}: " . $e->getMessage() . "\n";
        $errores++;
    }
}

echo "\n======================================\n";
echo "RESUMEN:\n";
echo "Total facturas: " . count($facturas) . "\n";
echo "Procesadas: $procesadas\n";
echo "Actualizadas: $actualizadas\n";
echo "Errores: $errores\n";
echo "======================================\n";
?>
