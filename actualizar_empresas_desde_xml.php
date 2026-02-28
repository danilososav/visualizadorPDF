<?php
$dbHost = 'localhost';
$dbPort = 5432;
$dbName = 'facturas_db';
$dbUser = 'postgres';
$dbPassword = '12345';

try {
    $pdo = new PDO("pgsql:host=$dbHost;port=$dbPort;dbname=$dbName", $dbUser, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

$stmt = $pdo->prepare("SELECT DISTINCT f.empresa_id, f.xml_contenido FROM facturas f WHERE f.xml_contenido IS NOT NULL");
$stmt->execute();
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Procesando " . count($resultados) . " empresas...\n";

$procesadas = 0;
foreach ($resultados as $row) {
    try {
        $xml = simplexml_load_string($row['xml_contenido']);
        $de = $xml->DE;

        $dNumTim = (string)$de->gTimb->dNumTim;
        $dFeIniT = (string)$de->gTimb->dFeIniT;
        $dDesTiDE = (string)$de->gTimb->dDesTiDE;

        $stmtUpdate = $pdo->prepare("
            UPDATE empresas SET
                numero_timbrado = ?,
                fecha_inicio_vigencia = ?,
                tipo_documento = ?
            WHERE id = ?
        ");
        $stmtUpdate->execute([$dNumTim, $dFeIniT, $dDesTiDE, $row['empresa_id']]);

        $procesadas++;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

echo "Total actualizado: $procesadas\n";
?>
