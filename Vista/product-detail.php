<?php include 'session.php'; ?>
<?php

// Obtener el id del producto
$idproducto = $_GET['idproducto'];

// Consulta para obtener los detalles del producto
$query = "SELECT * FROM producto WHERE idproducto = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $idproducto);
$stmt->execute();
$result = $stmt->get_result();
$producto = $result->fetch_assoc();
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
                    <li class="breadcrumb-item active">Detalle Productos</li>
                </ul>
            </div>
        </div>
        <!-- Breadcrumb End -->
        
        <!-- Product Detail Start -->
        <div class="product-detail">
            <div class="container">
                <div class="row">
                    <div class="col-lg-9">
                    <div class="row align-items-center product-detail-top">
                    <div class="col-md-5">
                        <div class="product-slider-single">
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($producto['foto']); ?>" alt="Product Image">
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="product-content">
                            <div class="title">
                                <h2><?php echo htmlspecialchars($producto['nombre']); ?></h2>
                            </div>
                            <div class="ratting">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <div class="price">Q.<?php echo number_format($producto['precio'], 2); ?> </div>
                            <div class="details">
                                <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                            </div>
                            <div class="action">
                                <form action="cart.php" method="GET" id="formCarrito<?php echo $producto['idproducto']; ?>">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="idproducto" value="<?php echo $producto['idproducto']; ?>">
                                    <input type="hidden" id="cantidadInput<?php echo $producto['idproducto']; ?>" name="cantidad" value="1">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-cart-plus"></i> Añadir al carrito
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                        <div class="row product-detail-bottom">
                            <div class="col-lg-12">
                                <ul class="nav nav-pills nav-justified">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="pill" href="#description">Descripción</a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <div id="description" class="container tab-pane active"><br>
                                        <h4>Descripción Producto</h4>
                                        <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="container">
                            <div class="section-header">
                                <h3>Productos Relacionados</h3>
                                <p>
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec viverra at massa sit amet ultricies. Nullam consequat, mauris non interdum cursus.
                                </p>
                            </div>
                        </div>
                        
                        <div class="row align-items-center product-slider product-slider-3">
                            <?php
                            // Obtener la categoría del producto actual
                            $idcategoria = $producto['idcategoria'];

                            // Consulta para obtener productos de la misma categoría, excluyendo el producto actual
                            $relatedQuery = "SELECT * FROM producto WHERE idcategoria = ? AND idproducto != ?";
                            $relatedStmt = $conn->prepare($relatedQuery);
                            $relatedStmt->bind_param("ii", $idcategoria, $idproducto);
                            $relatedStmt->execute();
                            $relatedResult = $relatedStmt->get_result();

                            if ($relatedResult->num_rows > 0) {
                                while ($relatedRow = $relatedResult->fetch_assoc()) {
                                    echo '<div class="col-lg-3">
                                            <div class="product-item">
                                                <div class="product-image">
                                                    <a href="product-detail.php?idproducto=' . $relatedRow['idproducto'] . '">
                                                        <img src="data:image/jpeg;base64,' . base64_encode($relatedRow['foto']) . '" alt="Product Image">
                                                    </a>
                                                    <div class="product-action">
                                                        <a href="#"><i class="fa fa-cart-plus"></i></a>
                                                        <a href="#"><i class="fa fa-heart"></i></a>
                                                        <a href="#"><i class="fa fa-search"></i></a>
                                                    </div>
                                                </div>
                                                <div class="product-content">
                                                    <div class="title"><a href="product-detail.php?idproducto=' . $relatedRow['idproducto'] . '">' . htmlspecialchars($relatedRow['nombre']) . '</a></div>
                                                    <div class="ratting">
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                    </div>
                                                    <div class="price">Q.' . number_format($relatedRow['precio'], 2) . '</div>
                                                </div>
                                            </div>
                                        </div>';
                                }
                            } else {
                                echo '<p>No hay productos relacionados para mostrar.</p>';
                            }
                            ?>
                        </div>
                    </div>
                    
                    <div class="col-lg-3">
                        <div class="sidebar-widget category">
                            <h2 class="title">Categoría</h2>
                            <ul>
                                <li><a href="#">Lorem Ipsum</a><span>(83)</span></li>
                                <li><a href="#">Cras sagittis</a><span>(198)</span></li>
                                <li><a href="#">Vivamus</a><span>(95)</span></li>
                                <li><a href="#">Fusce vitae</a><span>(48)</span></li>
                                <li><a href="#">Vestibulum</a><span>(210)</span></li>
                                <li><a href="#">Proin phar</a><span>(78)</span></li>
                            </ul>
                        </div>
                        
                        <div class="sidebar-widget image">
                            <h2 class="title">Producto Estelar</h2>
                            <a href="#">
                                <img src="..\img/11.jpeg" alt="Image">
                            </a>
                        </div>
                        
                        <div class="sidebar-widget brands">
                            <h2 class="title">Nuestras Marcas</h2>
                            <ul>
                                <li><a href="#">Nulla</a><span>(45)</span></li>
                                <li><a href="#">Curabitur</a><span>(34)</span></li>
                                <li><a href="#">Nunc</a><span>(67)</span></li>
                                <li><a href="#">Ullamcorper</a><span>(74)</span></li>
                                <li><a href="#">Fusce</a><span>(89)</span></li>
                                <li><a href="#">Sagittis</a><span>(28)</span></li>
                            </ul>
                        </div>
                        
                        <div class="sidebar-widget tag">
                            <h2 class="title">Etiquetas</h2>
                            <a href="#">Lorem ipsum</a>
                            <a href="#">Vivamus</a>
                            <a href="#">Phasellus</a>
                            <a href="#">pulvinar</a>
                            <a href="#">Curabitur</a>
                            <a href="#">Fusce</a>
                            <a href="#">Sem quis</a>
                            <a href="#">Mollis metus</a>
                            <a href="#">Sit amet</a>
                            <a href="#">Vel posuere</a>
                            <a href="#">orci luctus</a>
                            <a href="#">Nam lorem</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Product Detail End -->

        
        
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

