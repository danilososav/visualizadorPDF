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

// Primero agregar columna ciudad si no existe
try {
    $pdo->exec("ALTER TABLE empresas ADD COLUMN IF NOT EXISTS ciudad varchar(255)");
} catch (Exception $e) {}

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
        $dDirEmi = (string)$de->gDatGralOpe->gEmis->dDirEmi;
        $dTelEmi = (string)$de->gDatGralOpe->gEmis->dTelEmi;
        $dEmailE = (string)$de->gDatGralOpe->gEmis->dEmailE;
        $cActEco = (string)$de->gDatGralOpe->gEmis->gActEco->dDesActEco;
        $dDesCiuEmi = (string)$de->gDatGralOpe->gEmis->dDesCiuEmi;

        $stmtUpdate = $pdo->prepare("
            UPDATE empresas SET
                numero_timbrado = ?,
                fecha_inicio_vigencia = ?,
                tipo_documento = ?,
                direccion = ?,
                telefono = ?,
                email = ?,
                actividad_economica = ?,
                ciudad = ?
            WHERE id = ?
        ");
        $stmtUpdate->execute([
            $dNumTim,
            $dFeIniT,
            $dDesTiDE,
            $dDirEmi,
            $dTelEmi,
            $dEmailE,
            $cActEco,
            $dDesCiuEmi,
            $row['empresa_id']
        ]);

        $procesadas++;
        if ($procesadas % 5 == 0) {
            echo "Procesadas: $procesadas\n";
        }
    } catch (Exception $e) {
        echo "Error en empresa {$row['empresa_id']}: " . $e->getMessage() . "\n";
    }
}

echo "\nTotal actualizado: $procesadas\n";
?>
