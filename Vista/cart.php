<?php include 'session.php'; ?>
<?php include '../Procesos/Carrito.php'; ?>

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
                                <img src="../img/logo.png" alt="Logo">
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
                    <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="#">Productos</a></li>
                    <li class="breadcrumb-item active">Carrito</li>
                </ul>
            </div>
        </div>
        <!-- Breadcrumb End -->
        
        
        <!-- Cart Start -->
        <div class="cart-page">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Nombre</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Total</th>
                                        <th>Remover</th>
                                    </tr>
                                </thead>
                                <tbody class="align-middle">
                                    <?php
                                    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                                        foreach ($_SESSION['cart'] as $idproducto => $product) {
                                            $total = $product['precio'] * $product['cantidad'];
                                            echo '<tr>
                                                    <td><a href="#"><img src="data:image/jpeg;base64,' . base64_encode($product['foto']) . '" alt="Product Image"></a></td>
                                                    <td><a href="#">' . htmlspecialchars($product['nombre']) . '</a></td>
                                                    <td>Q.' . number_format($product['precio'], 2) . '</td>
                                                    <td>
                                                        <form method="POST" action="cart.php">
                                                            <div class="qty">
                                                                <input type="hidden" name="idproducto" value="' . $idproducto . '">
                                                                <button type="submit" name="action" value="decrease" class="btn-minus"><i class="fa fa-minus"></i></button>
                                                                <input type="text" name="cantidad" value="' . $product['cantidad'] . '" readonly>
                                                                <button type="submit" name="action" value="increase" class="btn-plus"><i class="fa fa-plus"></i></button>
                                                            </div>
                                                        </form>
                                                    </td>
                                                    <td>Q.' . number_format($total, 2) . '</td>
                                                    <td>
                                                        <form method="POST" action="cart.php">
                                                            <input type="hidden" name="idproducto" value="' . $idproducto . '">
                                                            <button type="submit" name="action" value="remove" class="btn-remove"><i class="fa fa-trash"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="6">No hay productos en el carrito.</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="cart-summary">
                            <div class="cart-content">
                                <h3>Cotizacion</h3>
                                <p>Sub Total<span>Q.<?php echo number_format($subtotal, 2); ?></span></p>
                                <p>Descuento<span>Q.<?php echo number_format($descuentoTotal, 2); ?></span></p>
                                <h4>Total<span>Q.<?php echo number_format($totalConDescuento, 2); ?></span></h4>
                            </div>
                            <div class="cart-btn">
                            <form action="checkout.php" method="POST">
                                <!-- Aquí enviarás los productos en formato oculto -->
                                <?php
                                if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                                    foreach ($_SESSION['cart'] as $idproducto => $product) {
                                        echo '<input type="hidden" name="productos[' . $idproducto . '][idproducto]" value="' . $idproducto . '">';
                                        echo '<input type="hidden" name="productos[' . $idproducto . '][nombre]" value="' . htmlspecialchars($product['nombre']) . '">';
                                        echo '<input type="hidden" name="productos[' . $idproducto . '][precio]" value="' . $product['precio'] . '">';
                                        echo '<input type="hidden" name="productos[' . $idproducto . '][cantidad]" value="' . $product['cantidad'] . '">'; // Cantidad
                                        echo '<input type="hidden" name="productos[' . $idproducto . '][total]" value="' . ($product['precio'] * $product['cantidad']) . '">';
                                    }
                                }
                                ?>
                                <input type="hidden" name="subtotal" value="<?php echo $subtotal; ?>">
                                <input type="hidden" name="descuentoTotal" value="<?php echo $descuentoTotal; ?>">
                                <input type="hidden" name="totalConDescuento" value="<?php echo $totalConDescuento; ?>">
                                <button type="submit">Generar Venta</button>
                            </form>
                            <form action="" method="POST" style="display: inline;">
                                <input type="hidden" name="generar_cotizacion" value="1">
                                <button type="submit" class="btn btn-primary">Generar Cotización</button>
                            </form>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Cart End -->
        
        <!-- Footer Start -->
        <div class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-widget">
                            <h1>Store Online S.A</h1>
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse sollicitudin rutrum massa. Suspendisse sollicitudin rutrum massa. Vestibulum porttitor, metus sed pretium elementum, nisi nibh sodales quam, non lobortis neque felis id mauris.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-widget">
                            <h3 class="title">Nuestras Opciones</h3>
                            <ul>
                                <li><a href="product-list.php">Producto</a></li>
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

        <!-- Modal -->
        <div class="modal fade" id="modalCotizacion" tabindex="-1" role="dialog" aria-labelledby="modalCotizacionLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCotizacionLabel">Resultado de la Cotización</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?php if (isset($mensaje_exito)): ?>
                            <p><?php echo $mensaje_exito; ?></p>
                        <?php else: ?>
                            <p>No se ha generado ninguna cotización.</p>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- CSS de Bootstrap -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">

        <!-- JavaScript de jQuery y Bootstrap -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
        <script>
            $(document).ready(function() {
                // Mostrar el modal si hay un mensaje de éxito
                <?php if (isset($mensaje_exito)): ?>
                    $('#modalCotizacion').modal('show');
                <?php endif; ?>
            });
        </script>

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
        
        <!-- Template Javascript -->
        <script src="js/main.js"></script>
    </body>
</html>
