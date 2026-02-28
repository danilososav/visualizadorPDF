<?php
$dbHost = 'localhost';
$dbPort = 5432;
$dbName = 'facturas_db';
$dbUser = 'postgres';
$dbPassword = '12345';

$pdo = new PDO("pgsql:host=$dbHost;port=$dbPort;dbname=$dbName", $dbUser, $dbPassword);

$stmt = $pdo->prepare("SELECT id, xml_contenido FROM facturas WHERE xml_contenido IS NOT NULL");
$stmt->execute();
$facturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Procesando " . count($facturas) . " facturas...\n";

$procesadas = 0;
foreach ($facturas as $factura) {
    try {
        $xml = simplexml_load_string($factura['xml_contenido']);
        $dDesTiDE = (string)$xml->DE->gTimb->dDesTiDE;

        $stmtUpdate = $pdo->prepare("UPDATE facturas SET tipo_documento = ? WHERE id = ?");
        $stmtUpdate->execute([$dDesTiDE, $factura['id']]);

        $procesadas++;
        if ($procesadas % 500 == 0) {
            echo "Procesadas: $procesadas\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

echo "Total: $procesadas\n";
?>
