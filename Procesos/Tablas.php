<?php
include '../Vista/session.php';  // Validar sesión
// Consulta para obtener los productos recientes (puedes ajustar el límite según cuántos productos quieras mostrar)
$sql = "SELECT idproducto, nombre, precio, foto FROM producto ORDER BY idproducto DESC LIMIT 6";
$result = $conn->query($sql);

// Verificar si el usuario está autenticado y si su rol no es cliente (rol = 1)
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] == 3) {
    header("Location: ../index.php"); // Redirigir al login si no tiene acceso
    exit();
}

// Consulta para el top 100 productos más vendidos (general)
$sql_general = "
    SELECT 
        p.nombre AS nombre_producto, 
        SUM(df.cantidad) AS total_vendido
    FROM 
        detallefactura df
        JOIN producto p ON df.idproducto = p.idproducto
    GROUP BY 
        p.idproducto
    ORDER BY 
        total_vendido DESC
    LIMIT 100
";
$result_general = $conn->query($sql_general);

// Consulta para el top 100 productos más vendidos por sucursal
$sql_sucursal = "
    SELECT 
        s.nombresucursal,
        p.nombre AS nombre_producto,
        SUM(df.cantidad) AS total_vendido
    FROM 
        detallefactura df
        JOIN producto p ON df.idproducto = p.idproducto
        JOIN inventario i ON p.idproducto = i.idproducto
        JOIN sucursal s ON i.idsucursal = s.idsucursal
    GROUP BY 
        s.idsucursal, p.idproducto
    ORDER BY 
        s.idsucursal, total_vendido DESC
    LIMIT 100
";
$result_sucursal = $conn->query($sql_sucursal);

// Consulta para los productos con existencia menor a 10 unidades
$sql_existencia_baja = "
    SELECT 
        p.nombre AS nombre_producto,
        i.cantidad AS cantidad_en_inventario
    FROM 
        inventario i
        JOIN producto p ON i.idproducto = p.idproducto
    WHERE 
        i.cantidad <= 10 
    ORDER BY 
        i.cantidad ASC
    LIMIT 20
";
$result_existencia_baja = $conn->query($sql_existencia_baja);

// Consulta para obtener los productos más vendidos por mes
$sql_mes = "
    SELECT 
        p.nombre AS nombre_producto, 
        YEAR(f.fecha) AS anio,
        MONTH(f.fecha) AS mes,
        SUM(df.cantidad) AS total_vendido
    FROM 
        detallefactura df
    JOIN 
        factura f ON df.iddetalleventa = f.idventa
    JOIN 
        producto p ON df.idproducto = p.idproducto
    GROUP BY 
        p.idproducto, anio, mes
    ORDER BY 
        anio DESC, mes DESC, total_vendido DESC
    LIMIT 100
";
$result_mes = $conn->query($sql_mes);

// Preparar datos para los gráficos
$productos_general = [];
$ventas_general = [];
while ($row = $result_general->fetch_assoc()) {
    $productos_general[] = $row['nombre_producto'];
    $ventas_general[] = $row['total_vendido'];
}

$sucursales = [];
$productos_sucursal = [];
$ventas_sucursal = [];
while ($row = $result_sucursal->fetch_assoc()) {
    $sucursales[] = $row['nombresucursal'];
    $productos_sucursal[] = $row['nombre_producto'];
    $ventas_sucursal[] = $row['total_vendido'];
}

// Preparar los datos para la gráfica de productos con baja existencia
$productos_baja = [];
$cantidades_baja = [];
while ($row = $result_existencia_baja->fetch_assoc()) {
    $productos_baja[] = $row['nombre_producto'];
    $cantidades_baja[] = $row['cantidad_en_inventario'];
}

// Preparar los datos para la gráfica
$productos_mes = [];
$ventas_mes = [];
$meses = [];
$anios = [];

while ($row = $result_mes->fetch_assoc()) {
    $productos_mes[] = $row['nombre_producto'];
    $ventas_mes[] = $row['total_vendido'];
    $meses[] = $row['mes'] . '-' . $row['anio'];  // Formato mes-año (por ejemplo, 11-2024)
    $anios[] = $row['anio'];
}
?>