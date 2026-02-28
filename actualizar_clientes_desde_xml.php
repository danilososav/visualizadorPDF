<?php
/**
 * Script para extraer emails y códigos de cliente del XML guardado
 * Usa: php actualizar_clientes_desde_xml.php
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

// Obtener todas las facturas con XML
$stmt = $pdo->prepare("SELECT id, cliente_id, xml_contenido FROM facturas WHERE xml_contenido IS NOT NULL LIMIT 20000");
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
        
        // Extraer datos del receptor
        $de = $xml->DE;
        if (!$de) {
            $errores++;
            continue;
        }
        
        $dEmailRec = (string)$de->gDatGralOpe->gDatRec->dEmailRec;
        $dCodCliente = (string)$de->gDatGralOpe->gDatRec->dCodCliente;
        
        // Actualizar cliente
        if ($dEmailRec || $dCodCliente) {
            $sqlUpdate = "UPDATE clientes SET ";
            $params = [];
            
            if ($dEmailRec) {
                $sqlUpdate .= "email = ?, ";
                $params[] = $dEmailRec;
            }
            
            if ($dCodCliente) {
                $sqlUpdate .= "codigo_cliente = ?, ";
                $params[] = $dCodCliente;
            }
            
            // Eliminar última coma
            $sqlUpdate = rtrim($sqlUpdate, ', ');
            $sqlUpdate .= " WHERE id = ?";
            $params[] = $factura['cliente_id'];
            
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $stmtUpdate->execute($params);
            
            if ($stmtUpdate->rowCount() > 0) {
                $actualizadas++;
            }
        }
        
        $procesadas++;
        
        if ($procesadas % 100 == 0) {
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
echo "Clientes actualizados: $actualizadas\n";
echo "Errores: $errores\n";
echo "======================================\n";
?>
