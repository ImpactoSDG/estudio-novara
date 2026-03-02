<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$host = '67.225.220.9';
$db_name = 'impactos_estudio_novara';
$username = 'impactos_gestion_interna_estudio';
$password = 'estudio_304';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db_name;charset=utf8mb4",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get years in business
    $yearsStmt = $pdo->query("SELECT TIMESTAMPDIFF(YEAR, '2007-01-01', CURDATE()) AS anos_asesorando");
    $yearsRow = $yearsStmt->fetch(PDO::FETCH_ASSOC);
    $anosAsesorando = $yearsRow ? (int)$yearsRow['anos_asesorando'] : 0;

    // Get total clients
    $clientsStmt = $pdo->query("SELECT total_clientes_activos FROM vista_WEB_nro_clientes LIMIT 1");
    $clientsRow = $clientsStmt->fetch(PDO::FETCH_ASSOC);
    $totalClientes = $clientsRow ? (int)$clientsRow['total_clientes_activos'] : 0;

    // Get rubros
    $rubrosStmt = $pdo->query("SELECT actividad_rubro_cliente FROM vista_WEB_rubros_clientes ORDER BY actividad_rubro_cliente ASC");
    $rubros = $rubrosStmt->fetchAll(PDO::FETCH_COLUMN);

    $normalizeText = static function ($value): string {
        $text = trim((string)$value);
        if ($text === '') {
            return '';
        }

        if (function_exists('mb_convert_encoding')) {
            if (strpos($text, 'Ã') !== false || strpos($text, 'Â') !== false) {
                $converted = @mb_convert_encoding($text, 'UTF-8', 'ISO-8859-1');
                if ($converted !== false) {
                    return $converted;
                }
            }
        }

        return $text;
    };

    // Filter and clean rubros
    $rubros = array_filter(array_map($normalizeText, $rubros));
    $rubros = array_values($rubros); // Re-index array

    // Count unique rubros
    $uniqueRubros = count(array_unique($rubros));

    http_response_code(200);
    echo json_encode([
        'anos_asesorando' => $anosAsesorando,
        'total_clientes_activos' => $totalClientes,
        'rubros_atendidos' => $uniqueRubros,
        'rubros_list' => $rubros
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    error_log("DB Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
