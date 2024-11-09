<?php include 'session.php'; ?>
<?php
// Procesar los datos enviados desde el carrito
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['productos'])) {
    $productos = $_POST['productos'];
    $subtotal = $_POST['subtotal'];
    $descuentoTotal = $_POST['descuentoTotal'];
    $totalConDescuento = $_POST['totalConDescuento'];
}

if ($usuario_actual) {
    // Obtener datos del cliente
    $queryCliente = "SELECT * FROM cliente WHERE correo = ?";
    $stmtCliente = $conn->prepare($queryCliente);
    $stmtCliente->bind_param("s", $usuario_actual);
    $stmtCliente->execute();
    $resultCliente = $stmtCliente->get_result();
    $cliente = $resultCliente->fetch_assoc();

    if ($cliente) {
        $latCliente = $cliente['gps_latitud'];
        $lonCliente = $cliente['gps_longitud'];

        // Obtener la sucursal más cercana al cliente
        $querySucursal = "
            SELECT sucursal.idsucursal, sucursal.nombresucursal, sucursal.gps_latitud, sucursal.gps_longitud,
                   sucursal.idciudad, ciudad.nombreciudad, departamento.nombredepartamento,
                   (6371 * acos(cos(radians(?)) * cos(radians(sucursal.gps_latitud)) * 
                   cos(radians(sucursal.gps_longitud) - radians(?)) + 
                   sin(radians(?)) * sin(radians(sucursal.gps_latitud)))) AS distancia
            FROM sucursal
            LEFT JOIN ciudad ON sucursal.idciudad = ciudad.idciudad
            LEFT JOIN departamento ON ciudad.iddepartamento = departamento.iddepartamento
            ORDER BY distancia ASC
            LIMIT 1
        ";
        $stmtSucursal = $conn->prepare($querySucursal);
        $stmtSucursal->bind_param("ddd", $latCliente, $lonCliente, $latCliente);
        $stmtSucursal->execute();
        $resultSucursal = $stmtSucursal->get_result();
        $sucursalCercana = $resultSucursal->fetch_assoc();
    }
} else {
    $cliente = null;
    $sucursalCercana = null;
}

// Consulta para obtener los métodos de pago
$query = "SELECT idmetodopago, metodopago FROM metodopago";
$result = $conn->query($query);

// Consulta para obtener las sucursales
$query2 = "SELECT idsucursal, nombresucursal FROM sucursal";
$result2 = $conn->query($query2);

// Consulta para obtener los métodos de pago
$query = "SELECT idmetodopago, metodopago FROM metodopago";
$result = $conn->query($query);

// Consulta para obtener las sucursales
$query2 = "SELECT idsucursal, nombresucursal FROM sucursal";
$result2 = $conn->query($query2);

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
        <link href="..\img/favicon.ico" rel="icon">

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700&display=swap" rel="stylesheet">

        <!-- CSS Libraries -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <link href="lib/slick/slick.css" rel="stylesheet">
        <link href="lib/slick/slick-theme.css" rel="stylesheet">

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
                        <div class="search">
                            <input type="text" placeholder="Buscar">
                            <button><i class="fa fa-search"></i></button>
                        </div>
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
                    <li class="breadcrumb-item"><a href="#">PRODUCTOS</a></li>
                    <li class="breadcrumb-item active">Realizar Pago</li>
                </ul>
            </div>
        </div>
        <!-- Breadcrumb End -->

        <!-- Checkout Start -->
        <form action="../Procesos/procesar_pedido.php" method="POST">
        <div class="checkout">
            <div class="container"> 
                <div class="row">
                    <div class="col-md-7">
                        <div class="billing-address">
                            <h2>Datos Comprador</h2>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Nombre</label>
                                    <input class="form-control" type="text" name="nombre" value="<?php echo htmlspecialchars($cliente['nombre'] ?? ''); ?>" placeholder="Nombre">
                                </div>
                                <div class="col-md-6">
                                    <label>Apellido</label>
                                    <input class="form-control" type="text" name="apellido" placeholder="Apellido">
                                </div>
                                <div class="col-md-6">
                                    <label>E-mail</label>
                                    <input class="form-control" type="email" name="email" value="<?php echo htmlspecialchars($cliente['correo'] ?? ''); ?>" placeholder="E-mail">
                                </div>
                                <div class="col-md-6">
                                    <label>Numero de Telefono</label>
                                    <input class="form-control" type="text" name="telefono" placeholder="Telefono">
                                </div>
                                <div class="col-md-12">
                                    <label>Direccion</label>
                                    <input class="form-control" type="text" name="direccion" value="<?php echo htmlspecialchars($cliente['direccion'] ?? ''); ?>" placeholder="Direccion">
                                </div>
                                <div class="col-md-6">
                                    <label>Sucursal</label>
                                    <select class="custom-select" name="sucursal">
                                        <?php
                                        if ($sucursalCercana) {
                                            echo '<option value="' . htmlspecialchars($sucursalCercana['idsucursal']) . '">' . htmlspecialchars($sucursalCercana['nombresucursal']) . ' (Más cercana)</option>';
                                        } else {
                                            echo '<option selected>No hay sucursales disponibles</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Departamento</label>
                                    <input class="form-control" type="text" name="departamento" value="<?php echo htmlspecialchars($sucursalCercana['nombredepartamento'] ?? ''); ?>" placeholder="Departamento">
                                </div>
                                <div class="col-md-6">
                                    <label>Ciudad</label>
                                    <input class="form-control" type="text" name="ciudad" value="<?php echo htmlspecialchars($sucursalCercana['nombreciudad'] ?? ''); ?>" placeholder="Ciudad">
                                </div>
                            </div>
                        </div>
                        <!--relleno-->
                        <div class="shipping-address">
                            <h2>Dirección de Envío</h2>
                        </div>
                    </div>
                    <!--relleno-->
                    <div class="col-md-5">
                    <div class="checkout-summary">
                        <h2 class="text-white">Total</h2>
                        <div class="checkout-content">
                            <h3 class="text-white">Factura</h3>
                            <?php
                            if (isset($productos)) {
                                foreach ($productos as $producto) {
                                    echo '<div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="text-white">
                                                <strong>' . htmlspecialchars($producto['nombre']) . '</strong>
                                            </div>
                                            <div class="text-right text-white">
                                                <span class="badge badge-primary">Q.' . number_format($producto['precio'], 2) . '</span>
                                                <span class="ml-2">x ' . htmlspecialchars($producto['cantidad']) . '</span>
                                                <span class="ml-2">= Q.' . number_format($producto['total'], 2) . '</span>
                                            </div>
                                            <input type="hidden" name="productos[' . htmlspecialchars($producto['idproducto']) . '][idproducto]" value="' . htmlspecialchars($producto['idproducto']) . '">
                                            <input type="hidden" name="productos[' . htmlspecialchars($producto['idproducto']) . '][cantidad]" value="' . htmlspecialchars($producto['cantidad']) . '">
                                            <input type="hidden" name="productos[' . htmlspecialchars($producto['idproducto']) . '][precio]" value="' . htmlspecialchars($producto['precio']) . '">
                                        </div>';
                                }
                            }
                            ?>
                            <p class="sub-total text-white">Sub Total<span>Q.<?php echo number_format($subtotal, 2); ?></span></p>
                            <p class="ship-cost text-white">Descuento<span>Q.<?php echo number_format($descuentoTotal, 2); ?></span></p>
                            <h4 class="text-white">Total<span>Q.<?php echo number_format($totalConDescuento, 2); ?></span></h4>

                            <!-- Hidden fields for subtotal, discount, and total with discount -->
                            <input type="hidden" name="subtotal" value="<?php echo htmlspecialchars($subtotal); ?>">
                            <input type="hidden" name="descuentoTotal" value="<?php echo htmlspecialchars($descuentoTotal); ?>">
                            <input type="hidden" name="totalConDescuento" value="<?php echo htmlspecialchars($totalConDescuento); ?>">
                        </div>
                    </div>
                    
                    <div class="checkout-payment">
                        <h2>Métodos de Pago</h2>
                        <div class="payment-methods">
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $idmetodo = $row['idmetodopago'];
                                    $nombreMetodo = htmlspecialchars($row['metodopago']);
                                    
                                    echo '<div class="payment-method">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" id="payment-' . $idmetodo . '" name="payment" value="' . $idmetodo . '">
                                                <label class="custom-control-label" for="payment-' . $idmetodo . '">' . $nombreMetodo . '</label>
                                            </div>
                                        </div>';
                                }
                            } else {
                                echo '<p>No hay métodos de pago disponibles.</p>';
                            }
                            ?>
                        </div>
                        <div class="checkout-btn">
                            <button type="submit">Iniciar Pedido</button>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <!-- Checkout End -->
        
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
                                <li><a href="login.php">Iniciar o Registrar Sesion</a></li>
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

        <!-- Modal Structure -->
        <div class="modal fade" id="responseModal" tabindex="-1" role="dialog" aria-labelledby="responseModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="responseModalLabel">Resultado</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="modalMessage">
                        <!-- Message will be injected here -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
        document.querySelector("form").addEventListener("submit", function(event) {
            event.preventDefault(); 

            const formData = new FormData(this);
            fetch("../Procesos/procesar_pedido.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                let message = data.message;
                document.getElementById("modalMessage").innerText = message;
                $('#responseModal').modal('show'); 
            })
            .catch(error => {
                document.getElementById("modalMessage").innerText = "Ocurrió un error al procesar el pedido.";
                $('#responseModal').modal('show');
            });
        });
        </script>
        
        
        <!-- Back to Top -->
        <a href="#" class="back-to-top"><i class="fa fa-chevron-up"></i></a>

        
        <!-- JavaScript Libraries -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
        <script src="lib/easing/easing.min.js"></script>
        <script src="lib/slick/slick.min.js"></script>

        
        <!-- Template Javascript -->
        <script src="..\js/main.js"></script>
    </body>
</html>
