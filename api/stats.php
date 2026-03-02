<?php
header('Content-Type: application/json');
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

    // Get total clients
    $clientsStmt = $pdo->query("SELECT total_clientes_activos FROM vista_WEB_nro_clientes LIMIT 1");
    $clientsRow = $clientsStmt->fetch(PDO::FETCH_ASSOC);
    $totalClientes = $clientsRow ? (int)$clientsRow['total_clientes_activos'] : 120;

    // Get rubros
    $rubrosStmt = $pdo->query("SELECT actividad_rubro_cliente FROM vista_WEB_rubros_clientes ORDER BY actividad_rubro_cliente ASC");
    $rubros = $rubrosStmt->fetchAll(PDO::FETCH_COLUMN);

    // Filter and clean rubros
    $rubros = array_filter(array_map('trim', $rubros));
    $rubros = array_values($rubros); // Re-index array

    // Count unique rubros
    $uniqueRubros = count(array_unique($rubros));

    http_response_code(200);
    echo json_encode([
        'anos_asesorando' => 10,
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
