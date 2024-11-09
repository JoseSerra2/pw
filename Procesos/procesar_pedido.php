<?php
include '../Vista/session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and validate inputs
    $productos = $_POST['productos'] ?? [];
    $subtotal = $_POST['subtotal'] ?? 0;
    $descuentoTotal = $_POST['descuentoTotal'] ?? 0;
    $totalConDescuento = $_POST['totalConDescuento'] ?? 0;
    $idmetodopago = $_POST['payment'] ?? null;
    $idsucursal = $_POST['sucursal'] ?? null;

    $conn->begin_transaction();

    try {
        // Insert each product into `detallefactura`
        foreach ($productos as $producto) {
            $cantidad = $producto['cantidad'] ?? null;
            $precio = $producto['precio'] ?? 0;
            $idproducto = $producto['idproducto'] ?? null;

            // Validate product fields
            if (is_null($cantidad) || $cantidad <= 0 || is_null($idproducto)) {
                throw new Exception("La cantidad y el producto son requeridos y deben ser válidos.");
            }

            // Check and update inventory
            $queryInventario = "SELECT cantidad FROM inventario WHERE idproducto = ? AND idsucursal = ?";
            $stmtInventario = $conn->prepare($queryInventario);
            $stmtInventario->bind_param("ii", $idproducto, $idsucursal);
            $stmtInventario->execute();
            $resultInventario = $stmtInventario->get_result();

            if ($resultInventario->num_rows > 0) {
                $inventario = $resultInventario->fetch_assoc();
                if ($inventario['cantidad'] >= $cantidad) {
                    // Reduce inventory
                    $queryUpdateInventario = "UPDATE inventario SET cantidad = cantidad - ? WHERE idproducto = ? AND idsucursal = ?";
                    $stmtUpdateInventario = $conn->prepare($queryUpdateInventario);
                    $stmtUpdateInventario->bind_param("iii", $cantidad, $idproducto, $idsucursal);
                    $stmtUpdateInventario->execute();
                } else {
                    throw new Exception("Stock insuficiente para el producto con ID: $idproducto en la sucursal seleccionada.");
                }
            } else {
                throw new Exception("Producto no encontrado en el inventario para la sucursal seleccionada.");
            }

            // Insert product details into `detallefactura`
            $queryDetalle = "INSERT INTO detallefactura (cantidad, preciounitario, idproducto) VALUES (?, ?, ?)";
            $stmtDetalle = $conn->prepare($queryDetalle);
            $stmtDetalle->bind_param("idi", $cantidad, $precio, $idproducto);
            $stmtDetalle->execute();
        }

        // Retrieve id for "Activo" status
        $queryEstado = "SELECT idestadofactura FROM estadofactura WHERE estadofactura = 'Activo'";
        $idEstadoFactura = $conn->query($queryEstado)->fetch_assoc()['idestadofactura'];

        // Insert into `factura`
        $fechaActual = date("Y-m-d H:i:s");
        $queryFactura = "INSERT INTO factura (fecha, total, idmetodopago, iddetalleventa, idestadofactura) VALUES (?, ?, ?, LAST_INSERT_ID(), ?)";
        $stmtFactura = $conn->prepare($queryFactura);
        $stmtFactura->bind_param("sdii", $fechaActual, $totalConDescuento, $idmetodopago, $idEstadoFactura);
        $stmtFactura->execute();

        // Insert into `historial`
        $accion = 'Factura generada';
        $idusuario = $_SESSION['usuario_id'];
        $queryHistorial = "INSERT INTO historial (accion, fecha, idusuario, idsucursal) VALUES (?, ?, ?, ?)";
        $stmtHistorial = $conn->prepare($queryHistorial);
        $stmtHistorial->bind_param("ssii", $accion, $fechaActual, $idusuario, $idsucursal);
        $stmtHistorial->execute();

        $conn->commit();
        echo json_encode(["status" => "success", "message" => "Pedido iniciado con éxito."]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => "Error al iniciar el pedido: " . $e->getMessage()]);
    }
}
?>
