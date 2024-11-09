<?php
include '../Vista/session.php';  // Validar sesión

// Obtener sucursales de la base de datos
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
$sucursal = isset($_POST['sucursal']) ? $_POST['sucursal'] : '';  // Asignar valor predeterminado vacío
$fechaInicio = isset($_POST['fechaInicio']) ? $_POST['fechaInicio'] : '';  // Asignar valor predeterminado vacío
$fechaFin = isset($_POST['fechaFin']) ? $_POST['fechaFin'] : '';  // Asignar valor predeterminado vacío

// Base de la consulta SQL
$sql = "
    SELECT 
        h.accion, 
        h.fecha, 
        s.nombresucursal 
    FROM 
        historial h
    JOIN sucursal s ON h.idsucursal = s.idsucursal
";

// Condiciones para filtrar por fechas
if (!empty($fechaInicio) && !empty($fechaFin)) {
    $sql .= " WHERE h.fecha BETWEEN '$fechaInicio' AND '$fechaFin'";
}

// Condición para filtrar por sucursal
if (!empty($sucursal)) {
    $sql .= " AND h.idsucursal = '$sucursal'";
}

// Ejecutar la consulta
$result = $conn->query($sql);
$reporteGenerado = $result->num_rows > 0 ? true : false;

// Establecer la fecha de hoy como valor predeterminado
$fechaHoy = date('Y-m-d');
?>