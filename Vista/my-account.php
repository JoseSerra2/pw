<?php include 'session.php'; ?>
<?php 
// Inicializar variables
$cotizacion = [];
$cotizacionesDisponibles = [];

// Verificar que el usuario esté autenticado y que el cliente esté establecido
if ($usuario_actual && $cliente) {
    // Obtener todas las cotizaciones disponibles para este cliente
    $queryCotizaciones = "
        SELECT idcotizacion, fecha, total 
        FROM cotizacion 
        WHERE idcliente = ?
        ORDER BY fecha DESC
    ";
    $stmtCotizaciones = $conn->prepare($queryCotizaciones);
    $stmtCotizaciones->bind_param("i", $cliente['idcliente']);
    $stmtCotizaciones->execute();
    $resultCotizaciones = $stmtCotizaciones->get_result();

    // Guardar todas las cotizaciones en un array para la lista de selección
    while ($cot = $resultCotizaciones->fetch_assoc()) {
        $cotizacionesDisponibles[] = $cot;
    }

    // Obtener el ID de cotización seleccionado por el usuario (si existe)
    $id_cotizacion_seleccionada = isset($_GET['idcotizacion']) ? intval($_GET['idcotizacion']) : null;

    // Si se selecciona una cotización específica, buscar sus productos
    if ($id_cotizacion_seleccionada) {
        $queryCotizacion = "
            SELECT idcotizacion, fecha, total 
            FROM cotizacion 
            WHERE idcotizacion = ? AND idcliente = ?
        ";
        $stmtCotizacion = $conn->prepare($queryCotizacion);
        $stmtCotizacion->bind_param("ii", $id_cotizacion_seleccionada, $cliente['idcliente']);
        $stmtCotizacion->execute();
        $resultCotizacion = $stmtCotizacion->get_result();

        // Si la cotización existe, obtener sus productos
        if ($cotizacionData = $resultCotizacion->fetch_assoc()) {
            $cotizacion = [
                'id_cotizacion' => $cotizacionData['idcotizacion'],
                'fecha' => $cotizacionData['fecha'],
                'total_cotizacion' => $cotizacionData['total'],
                'productos' => []
            ];

            // Obtener los productos de la cotización seleccionada
            $queryDetalle = "
                SELECT dc.cantidad, dc.preciounitario, p.descripcion AS producto
                FROM detallecotizacion AS dc
                LEFT JOIN producto AS p ON dc.idproducto = p.idproducto
                WHERE dc.idcotizacion = ?
            ";
            $stmtDetalle = $conn->prepare($queryDetalle);
            $stmtDetalle->bind_param("i", $cotizacion['id_cotizacion']);
            $stmtDetalle->execute();
            $resultDetalle = $stmtDetalle->get_result();

            while ($producto = $resultDetalle->fetch_assoc()) {
                $cotizacion['productos'][] = $producto;
            }
        }
    }
}

$userId = $_SESSION['usuario_id']; // Assuming you have the user ID stored in the session
$query = "SELECT h.fecha, h.accion, s.nombresucursal AS sucursal 
          FROM historial h 
          JOIN sucursal s ON h.idsucursal = s.idsucursal 
          WHERE h.idusuario = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$historial = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Store Online S.A.</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="Bootstrap Ecommerce Template" name="keywords">
        <meta content="Bootstrap Ecommerce Template Free Download" name="description">

        <!-- Favicon -->
        <link href="img/favicon.ico" rel="icon">

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700&display=swap" rel="stylesheet">

        <!-- CSS Libraries -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <link href="..\lib/slick/slick.css" rel="stylesheet">
        <link href="..\lib/slick/slick-theme.css" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="..\css/style.css" rel="stylesheet">
    </head>

    <body>
        <!-- Top Header Start -->
        <div class="top-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <div class="logo">
                            <a href="">
                                <img src="..\img/logo.png" alt="Logo">
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        
                    </div>
                    <div class="col-md-3">
                        <div class="user">
                        <div class="dropdown">
                                <a href="..\Vista\my-account.php" class="dropdown-toggle" data-toggle="dropdown">Mi Cuenta</a>
                                <div class="dropdown-menu">
                                <?php if ($usuario_actual): ?>
                                    <!-- Mostrar si el usuario está logueado -->
                                    <a href="#" class="dropdown-item">Bienvenido, <?php echo htmlspecialchars($usuario_actual); ?></a>
                                    <a href="?logout=true" class="dropdown-item">Cerrar Sesión</a>
                                <?php else: ?>
                                    <!-- Mostrar si el usuario no está logueado -->
                                    <a href="..\Vista\LoginU.php" class="dropdown-item">Inicio de Sesion</a>
                                    <a href="..\Vista\login.php" class="dropdown-item">Registrarme</a>
                                <?php endif; ?>
                            </div>
                            </div>
                            <div class="cart">
                                <i class="fa fa-cart-plus"></i>
                                <span>(0)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Top Header End -->
        
        
        <!-- Header Start -->
        <div class="header">
            <div class="container">
                <nav class="navbar navbar-expand-md bg-dark navbar-dark">
                    <a href="#" class="navbar-brand">MENU</a>
                    <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                        <div class="navbar-nav m-auto">
                            <!-- Enlace "INICIO" dinámico según el rol del usuario -->
                            <a href="<?php if ($rol_actual == 3) { echo '../index.php'; } elseif ($rol_actual == 1) { echo 'indexAdministrador.php'; } 
                            else { echo 'indexGerente.php'; } ?>" class="nav-item nav-link active">INICIO</a>
                            
                            <a href="product-list.php" class="nav-item nav-link">PRODUCTOS</a>
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">PAGINAS</a>
                                <div class="dropdown-menu">
                                    <a href="product-list.php" class="dropdown-item">Producto</a>
                                    <a href="cart.php" class="dropdown-item">Carrito</a>
                                    <a href="login.php" class="dropdown-item">Iniciar o Registrar Sesion</a>
                                    <a href="my-account.php" class="dropdown-item">Mi Cuenta</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
        <!-- Header End -->
        
        <!-- Breadcrumb Start -->
        <div class="breadcrumb-wrap">
            <div class="container">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">INICIO</a></li>
                    <li class="breadcrumb-item"><a href="#">Usuario</a></li>
                    <li class="breadcrumb-item active">Mi Cuenta</li>
                </ul>
            </div>
        </div>
        <!-- Breadcrumb End -->

        <!-- My Account Start -->
        <div class="my-account">
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <a class="nav-link active" id="dashboard-nav" data-toggle="pill" href="#dashboard-tab" role="tab" aria-controls="dashboard-tab" aria-selected="true">Datos del cliente</a>
                            <a class="nav-link" id="orders-nav" data-toggle="pill" href="#orders-tab" role="tab" aria-controls="orders-tab" aria-selected="false">Historial</a>
                            <a class="nav-link" id="payment-nav" data-toggle="pill" href="#payment-tab" role="tab" aria-controls="payment-tab" aria-selected="false">Cotizaciones</a>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="tab-content" id="v-pills-tabContent">
                            <!-- Datos del cliente -->
                            <div class="tab-pane fade show active" id="dashboard-tab" role="tabpanel" aria-labelledby="dashboard-nav">
                                <h4>Datos del cliente</h4>
                                <?php if ($cliente): ?>
                                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($cliente['nombre']); ?></p>
                                    <p><strong>Correo:</strong> <?php echo htmlspecialchars($cliente['correo']); ?></p>
                                    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($cliente['direccion']); ?></p>
                                <?php else: ?>
                                    <p>No se encontraron datos del cliente.</p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Historial -->
                            <div class="tab-pane fade" id="orders-tab" role="tabpanel" aria-labelledby="orders-nav">
                                <h4>Historial de Acciones</h4>
                                <?php if (!empty($historial)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Acción</th>
                                                    <th>Sucursal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($historial as $accion): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($accion['fecha']); ?></td>
                                                        <td><?php echo htmlspecialchars($accion['accion']); ?></td>
                                                        <td><?php echo htmlspecialchars($accion['sucursal']); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p>No hay historial disponible para este usuario.</p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Cotizaciones -->
                            <div class="tab-pane fade" id="payment-tab" role="tabpanel" aria-labelledby="payment-nav">
                                <h4>Cotizaciones</h4>
                                <form method="get" action="">
                                    <label for="idcotizacion">Selecciona una cotización:</label>
                                    <select name="idcotizacion" id="idcotizacion" onchange="this.form.submit()">
                                        <option value="">-- Seleccionar --</option>
                                        <?php foreach ($cotizacionesDisponibles as $cot): ?>
                                            <option value="<?php echo $cot['idcotizacion']; ?>" <?php echo $id_cotizacion_seleccionada == $cot['idcotizacion'] ? 'selected' : ''; ?>>
                                                <?php echo "ID " . htmlspecialchars($cot['idcotizacion']) . " - " . htmlspecialchars($cot['fecha']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>

                                <?php if (!empty($cotizacion)): ?>
                                    <p><strong>ID Cotización:</strong> <?php echo htmlspecialchars($cotizacion['id_cotizacion']); ?></p>
                                    <p><strong>Fecha:</strong> <?php echo htmlspecialchars($cotizacion['fecha']); ?></p>
                                    <p><strong>Total Cotización:</strong> <?php echo htmlspecialchars(number_format($cotizacion['total_cotizacion'], 2)); ?></p>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>Producto</th>
                                                    <th>Cantidad</th>
                                                    <th>Precio Unitario</th>
                                                    <th>Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($cotizacion['productos'] as $producto): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($producto['producto']); ?></td>
                                                        <td><?php echo htmlspecialchars($producto['cantidad']); ?></td>
                                                        <td><?php echo htmlspecialchars(number_format($producto['preciounitario'], 2)); ?></td>
                                                        <td><?php echo htmlspecialchars(number_format($producto['preciounitario'] * $producto['cantidad'], 2)); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p>Seleccione una cotización para ver los detalles.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- My Account End -->


        <!-- Footer Start -->
        <div class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-widget">
                            <h1>Store Online S.A.</h1>
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse sollicitudin rutrum massa. Suspendisse sollicitudin rutrum massa. Vestibulum porttitor, metus sed pretium elementum, nisi nibh sodales quam, non lobortis neque felis id mauris.
                            </p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="footer-widget">
                            <h3 class="title">Nuestras Opciones</h3>
                            <ul>
                                <li><a href="product.php">Producto</a></li>
                                <li><a href="cart.php">Carrito</a></li>
                                <li><a href="checkout.php">Compra</a></li>
                                <li><a href="login.php">Iniciar Sesion o Registrarse</a></li>
                                <li><a href="my-account.php">Mi Cuenta</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="footer-widget">
                            <h3 class="title">Encuentranos</h3>
                            <div class="contact-info">
                                <p><i class="fa fa-map-marker"></i>Pradera Chimaltenango, Guatemala</p>
                                <p><i class="fa fa-envelope"></i>email@example.com</p>
                                <p><i class="fa fa-phone"></i>+123-456-7890</p>
                                <p><i class="fa fa-map-marker"></i>Pradera Escuintla, Guatemala</p>
                                <p><i class="fa fa-envelope"></i>email@example.com</p>
                                <p><i class="fa fa-phone"></i>+123-456-7890</p>
                                <p><i class="fa fa-map-marker"></i>Las Américas Mazatenango, Guatemala</p>
                                <p><i class="fa fa-envelope"></i>email@example.com</p>
                                <p><i class="fa fa-phone"></i>+123-456-7890</p>
                                <p><i class="fa fa-map-marker"></i>La Trinidad Coatepeque, Guatemala</p>
                                <p><i class="fa fa-envelope"></i>email@example.com</p>
                                <p><i class="fa fa-phone"></i>+123-456-7890</p>
                                <p><i class="fa fa-map-marker"></i>Pradera Xela Quetzaltenango, Guatemala</p>
                                <p><i class="fa fa-envelope"></i>email@example.com</p>
                                <p><i class="fa fa-phone"></i>+123-456-7890</p>
                                <p><i class="fa fa-map-marker"></i>Miraflores Guatemala, Guatemala</p>
                                <p><i class="fa fa-envelope"></i>email@example.com</p>
                                <p><i class="fa fa-phone"></i>+123-456-7890</p>
                                <div class="social">
                                    <a href=""><i class="fa fa-twitter"></i></a>
                                    <a href=""><i class="fa fa-facebook"></i></a>
                                    <a href=""><i class="fa fa-linkedin"></i></a>
                                    <a href=""><i class="fa fa-instagram"></i></a>
                                    <a href=""><i class="fa fa-youtube"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row payment">
                    <div class="col-md-6">
                        <div class="payment-method">
                            <p>Aceptamos:</p>
                            <img src="..\img/payment-method.png" alt="Payment Method" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="payment-security">
                            <p>Servicios de Seguridad:</p>
                            <img src="..\img/godaddy.svg" alt="Payment Security" />
                            <img src="..\img/norton.svg" alt="Payment Security" />
                            <img src="..\img/ssl.svg" alt="Payment Security" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer End -->

        
        <!-- Footer Bottom Start -->
        <div class="footer-bottom">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 copyright">
                        <p>Copyright &copy; <a href="https://htmlcodex.com">HTML Codex</a>. All Rights Reserved</p>
                    </div>

                    <div class="col-md-6 template-by">
                        <p>Template By <a href="https://htmlcodex.com">HTML Codex</a></p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer Bottom End -->
        
        
        <!-- Back to Top -->
        <a href="#" class="back-to-top"><i class="fa fa-chevron-up"></i></a>

        
        <!-- JavaScript Libraries -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
        <script src="..\lib/easing/easing.min.js"></script>
        <script src="..\lib/slick/slick.min.js"></script>

        
        <!-- Template Javascript -->
        <script src="..\js/main.js"></script>
    </body>
</html>
