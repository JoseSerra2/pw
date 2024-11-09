<?php
include '../Vista/session.php';  // Validar sesión

// Obtener las sucursales de la base de datos
$sucursales = [];
$sucursalQuery = "SELECT idsucursal, nombresucursal FROM sucursal";
$sucursalResult = $conn->query($sucursalQuery);
if ($sucursalResult->num_rows > 0) {
    while ($row = $sucursalResult->fetch_assoc()) {
        $sucursales[] = $row;
    }
}

// Procesar el formulario al enviar
$reporteGenerado = false;
// Valor predeterminado para tipoReporte
$tipoReporte = isset($_POST['tipoReporte']) ? $_POST['tipoReporte'] : 'general'; // Asignar un valor predeterminado

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sucursal = $_POST['sucursal'];

    // Base de la consulta SQL
    $sql = "
        SELECT 
            c.nombre AS cliente, 
            COUNT(h.idhistorial) AS compras 
        FROM 
            historial h
        JOIN cliente c ON h.idusuario = c.idcliente
    ";

    // Condiciones para filtrar según el tipo de reporte
    if ($tipoReporte === 'sucursal' && !empty($sucursal)) {
        $sql .= " WHERE h.idsucursal = '$sucursal'";
    }

    $sql .= " GROUP BY c.idcliente ORDER BY compras DESC LIMIT 100";

    // Ejecutar la consulta
    $result = $conn->query($sql);
    $reporteGenerado = $result->num_rows > 0 ? true : false;
}
?>