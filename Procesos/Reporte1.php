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

// Asignar valor predeterminado para tipoReporte
$tipoReporte = isset($_POST['tipoReporte']) ? $_POST['tipoReporte'] : 'general'; // Valor predeterminado

// Comprobar si se ha recibido un mes, si no usar el mes actual
$mes = isset($_POST['mes']) ? $_POST['mes'] : date('Y-m'); // Usa el mes y año actual por defecto
$sucursal = isset($_POST['sucursal']) ? $_POST['sucursal'] : ''; // Validar si se ha seleccionado sucursal

// Base de la consulta SQL
$sql = "
    SELECT 
        p.nombre AS producto, 
        YEAR(f.fecha) AS año,
        MONTH(f.fecha) AS mes, 
        SUM(df.cantidad) AS cantidad 
    FROM 
        detallefactura df
    JOIN factura f ON df.iddetalleventa = f.idventa
    JOIN producto p ON df.idproducto = p.idproducto
";

// Condiciones para filtrar según el tipo de reporte
if ($tipoReporte === 'mes' && !empty($mes)) {
    $year = date('Y', strtotime($mes));
    $month = date('m', strtotime($mes));
    $sql .= " WHERE YEAR(f.fecha) = '$year' AND MONTH(f.fecha) = '$month'";
} elseif ($tipoReporte === 'sucursal' && !empty($sucursal)) {
    $sql .= " JOIN inventario i ON p.idproducto = i.idproducto";
    $sql .= " WHERE i.idsucursal = '$sucursal'";
}

$sql .= " GROUP BY p.idproducto, año, mes ORDER BY cantidad DESC";

// Ejecutar la consulta
$result = $conn->query($sql);
$reporteGenerado = $result->num_rows > 0 ? true : false;
?>