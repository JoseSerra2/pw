<?php 
// Manejar acciones del carrito
if (isset($_POST['action']) && isset($_POST['idproducto'])) {
    $idproducto = intval($_POST['idproducto']);

    if (isset($_SESSION['cart'][$idproducto])) {
        switch ($_POST['action']) {
            case 'increase':
                $_SESSION['cart'][$idproducto]['cantidad'] += 1;
                break;

            case 'decrease':
                if ($_SESSION['cart'][$idproducto]['cantidad'] > 1) {
                    $_SESSION['cart'][$idproducto]['cantidad'] -= 1;
                }
                break;

            case 'remove':
                unset($_SESSION['cart'][$idproducto]);
                break;
        }
    }

    // Redirigir de nuevo a la página de carrito para evitar reenvío de formulario al recargar
    header('Location: cart.php');
    exit;
}

// Si se ha realizado una acción para agregar un producto al carrito
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['idproducto'])) {
    $idproducto = intval($_GET['idproducto']);
    $cantidad = isset($_GET['cantidad']) ? intval($_GET['cantidad']) : 1; // Validar la cantidad seleccionada

    // Consulta para obtener los detalles del producto
    $query = "SELECT * FROM producto WHERE idproducto = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $idproducto);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        // Si el carrito no existe, lo creamos en la sesión
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Verificar si el producto ya está en el carrito
        if (isset($_SESSION['cart'][$idproducto])) {
            // Si ya está, incrementamos la cantidad con la seleccionada
            $_SESSION['cart'][$idproducto]['cantidad'] += $cantidad;
        } else {
            // Si no está, agregamos el producto con la cantidad seleccionada
            $_SESSION['cart'][$idproducto] = [
                'nombre' => $product['nombre'],
                'precio' => $product['precio'],
                'foto' => $product['foto'],
                'cantidad' => $cantidad // Cantidad seleccionada
            ];
        }
    }

    // Redirigir de nuevo a la página de carrito
    header('Location: cart.php');
    exit;
}

// Calcular subtotal y aplicar descuentos
$subtotal = 0;
$descuento = 0;

// Consultar las promociones activas
$fechaActual = date('Y-m-d H:i:s');
$sqlPromociones = "SELECT descuento FROM promocion WHERE estado = 1 AND fechainicio <= ? AND fechafin >= ?";
$stmtPromociones = $conn->prepare($sqlPromociones);
$stmtPromociones->bind_param("ss", $fechaActual, $fechaActual);
$stmtPromociones->execute();
$resultPromociones = $stmtPromociones->get_result();

// Si hay promociones activas, obtenemos el descuento
if ($resultPromociones->num_rows > 0) {
    while ($promo = $resultPromociones->fetch_assoc()) {
        $descuento += (float)$promo['descuento']; // Sumar todos los descuentos
    }
}

// Si existe un descuento adicional por la suscripción al newsletter
if (isset($_SESSION['descuento_adicional'])) {
    $descuento += $_SESSION['descuento_adicional']; // Sumar el 10% adicional
}

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $product) {
        $subtotal += $product['precio'] * $product['cantidad'];
    }
}

// Aplicar el descuento
$descuentoTotal = $subtotal * $descuento; 
$totalConDescuento = $subtotal - $descuentoTotal;

// Manejo de generación de la cotización
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generar_cotizacion'])) {
    if ($usuario_actual && isset($cliente)) {
        // Obtener el ID del cliente
        $id_cliente = $cliente['idcliente'];

        // Calcular el total de la cotización y preparar los productos
        $total_cotizacion = 0;
        $productos = [];

        foreach ($_SESSION['cart'] as $idproducto => $product) {
            $total = $product['precio'] * $product['cantidad'];
            $total_cotizacion += $total;

            $productos[] = [
                'idproducto' => $idproducto,
                'cantidad' => $product['cantidad'],
                'preciounitario' => $product['precio'],
                'total' => $total
            ];
        }

        // Insertar la cotización en la tabla `cotizacion`
        $queryCotizacion = "INSERT INTO cotizacion (fecha, total, idcliente) VALUES (NOW(), ?, ?)";
        $stmtCotizacion = $conn->prepare($queryCotizacion);
        $stmtCotizacion->bind_param("di", $total_cotizacion, $id_cliente);
        $stmtCotizacion->execute();

        // Obtener el último idcotizacion generado
        $id_cotizacion = $stmtCotizacion->insert_id;

        // Insertar los detalles de la cotización en la tabla `detallecotizacion`
        foreach ($productos as $producto) {
            $queryDetalle = "INSERT INTO detallecotizacion (cantidad, preciounitario, idproducto, idcotizacion) VALUES (?, ?, ?, ?)";
            $stmtDetalle = $conn->prepare($queryDetalle);
            $stmtDetalle->bind_param("idii", $producto['cantidad'], $producto['preciounitario'], $producto['idproducto'], $id_cotizacion);
            $stmtDetalle->execute();
        }

        // Mensaje de éxito para mostrar en el modal
        $mensaje_exito = "Cotización generada con éxito.";
    } else {
        $mensaje_exito = "No se pudo generar la cotización. Necesita tener una cuenta para realizar la cotización.";
    }
}

?>
