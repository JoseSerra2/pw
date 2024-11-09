<?php include 'Vista/Session.php'; ?>
<?php
// Consulta para obtener los productos recientes (puedes ajustar el límite según cuántos productos quieras mostrar)
$sql = "SELECT idproducto, nombre, precio, foto FROM producto ORDER BY idproducto DESC LIMIT 6";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Store Online S.A</title>
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
        <link href="lib/slick/slick.css" rel="stylesheet">
        <link href="lib/slick/slick-theme.css" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="css/style.css" rel="stylesheet">
    </head>

    <body>
        <!-- Top Header Start -->
        <div class="top-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <div class="logo">
                            <a href="../phpGerente/guardar_usuario.php">
                                <img src="img/logo.png" alt="Logo">
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
                            <a href="..\index.php" class="nav-item nav-link active">INICIO</a>
                            <a href="..\Vista\product-list.php" class="nav-item nav-link">PRODUCTOS</a>
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">PAGINAS</a>
                                <div class="dropdown-menu">
                                    <a href="..\Vista\product-list.php" class="dropdown-item">Producto</a>
                                    <a href="..\Vista\cart.php" class="dropdown-item">Carrito</a>
                                    <a href="..\Vista\login.php" class="dropdown-item">Iniciar o Registrar Sesion</a>
                                    <a href="..\Vista\my-account.php" class="dropdown-item">Mi Cuenta</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
        <!-- Header End -->
        
        
        <!-- Main Slider Start -->
        <div class="home-slider">
            <div class="main-slider">
                <div class="main-slider-item"><img src="img/slider1.jpg" alt="Slider Image" /></div>
                <div class="main-slider-item"><img src="img/slider2.jpg" alt="Slider Image" /></div>
                <div class="main-slider-item"><img src="img/slider3.jpg" alt="Slider Image" /></div>
            </div>
        </div>
        <!-- Main Slider End -->
        
        
        <!-- Feature Start-->
        <div class="feature">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-3 col-md-6 feature-col">
                        <div class="feature-content">
                            <i class="fa fa-shield"></i>
                            <h2>Compras Seguras</h2>
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 feature-col">
                        <div class="feature-content">
                            <i class="fa fa-shopping-bag"></i>
                            <h2>Producto De gran calidad</h2>
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 feature-col">
                        <div class="feature-content">
                            <i class="fa fa-truck"></i>
                            <h2>Envios a travez de sucursales</h2>
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 feature-col">
                        <div class="feature-content">
                            <i class="fa fa-phone"></i>
                            <h2>Soporte a clientes</h2>
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Feature End-->
        
        
        <!-- Category Start-->
        <div class="category">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-4">
                        <div class="category-img">
                            <img src="img/category1.jpg" />
                            <a class="category-name" href="../Vista/product-list.php">
                                <h2>Computadoras</h2>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="category-img">
                            <img src="img/category2.jpg" />
                            <a class="category-name" href="../Vista/product-list.php">
                                <h2>Camaras</h2>
                            </a>
                        </div>
                        <div class="category-img">
                            <img src="img/category3.jpg" />
                            <a class="category-name" href="../Vista/product-list.php">
                                <h2>Telefonos</h2>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="category-img">
                            <img src="img/category4.jpg" />
                            <a class="category-name" href="../Vista/product-list.php">
                                <h2>Accesorios</h2>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Category End-->
        
        <!-- Newsletter Start -->
        <div class="newsletter">
            <div class="container">
                <div class="section-header">
                    <h3>Subscríbete para tener descuentos</h3>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec viverra at massa sit amet ultricies. Nullam consequat, mauris non interdum cursus
                    </p>
                </div>
                <div class="form">
                    <!-- Formulario para suscripción -->
                    <form id="newsletterForm">
                        <input type="email" id="email" placeholder="Ingrese su email" required>
                        <button type="submit">Submit</button>
                    </form>
                    <p id="error" style="color: black; display: none;">Por favor, ingrese una dirección de correo válida.</p>
                    <p id="success" style="color: black; display: none;">¡Gracias por suscribirte! Tu descuento del 10% ha sido aplicado.</p>
                </div>
            </div>
        </div>
        <!-- Newsletter End -->
        
        
       <!-- Recent Product Start -->
       <div class="recent-product">
            <div class="container">
                <div class="section-header">
                    <h3>Productos Recientes</h3>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec viverra at massa sit amet ultricies. Nullam consequat, mauris non interdum cursus.
                    </p>
                </div>
                <div class="row align-items-center -slider product-slider-4">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="col-lg-3">
                                    <div class="product-item">
                                        <div class="product-image">
                                            <a href="..\Vista\product-detail.php?idproducto=' . $row['idproducto'] . '">
                                                <img src="data:image/jpeg;base64,' . base64_encode($row['foto']) . '" alt="Product Image">
                                            </a>
                                            <div class="product-action">
                                                <a href="..\Vista\cart.php?action=add&idproducto=' . $row['idproducto'] . '"><i class="fa fa-cart-plus"></i></a>
                                            </div>
                                        </div>
                                        <div class="product-content">
                                            <div class="title"><a href="#">' . htmlspecialchars($row['nombre']) . '</a></div>
                                            <div class="ratting">
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                            </div>
                                            <div class="price">Q.' . number_format($row['precio'], 2) . '</div>
                                        </div>
                                    </div>
                                </div>';
                        }
                    } else {
                        echo '<p>No hay productos recientes para mostrar.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <!-- Recent Product End -->

        
        
        <!-- Brand Start -->
        <div class="brand">
            <div class="container">
                <div class="section-header">
                    <h3>Nuestros Patrocinadores</h3>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec viverra at massa sit amet ultricies. Nullam consequat, mauris non interdum cursus
                    </p>
                </div>
                <div class="brand-slider">
                    <div class="brand-item"><img src="img/brand-1.png" alt=""></div>
                    <div class="brand-item"><img src="img/brand-2.png" alt=""></div>
                    <div class="brand-item"><img src="img/brand-3.png" alt=""></div>
                    <div class="brand-item"><img src="img/brand-4.png" alt=""></div>
                    <div class="brand-item"><img src="img/brand-5.png" alt=""></div>
                    <div class="brand-item"><img src="img/brand-6.png" alt=""></div>
                </div>
            </div>
        </div>
        <!-- Brand End -->

        
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
                                <li><a href="..\Vista\product.php">Producto</a></li>
                                <li><a href="..\Vista\cart.php">Carrito</a></li>
                                <li><a href="..\Vista\checkout.php">Compra</a></li>
                                <li><a href="..\Vista\login.php">Iniciar Sesion o Registrarse</a></li>
                                <li><a href="..\Vista\my-account.php">Mi Cuenta</a></li>0
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
                            <img src="img/payment-method.png" alt="Payment Method" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="payment-security">
                            <p>Servicios de Seguridad:</p>
                            <img src="img/godaddy.svg" alt="Payment Security" />
                            <img src="img/norton.svg" alt="Payment Security" />
                            <img src="img/ssl.svg" alt="Payment Security" />
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
        <script src="lib/easing/easing.min.js"></script>
        <script src="lib/slick/slick.min.js"></script>

        
        <!-- Template Javascript -->
        <script src="js/main.js"></script>

        <script>
        document.getElementById("newsletterForm").addEventListener("submit", function(event) {
            event.preventDefault(); // Evitamos el envío del formulario tradicional
            var emailField = document.getElementById("email");
            var errorMessage = document.getElementById("error");
            var successMessage = document.getElementById("success");

            if (!emailField.checkValidity()) {
                errorMessage.style.display = "block";
            } else {
                errorMessage.style.display = "none";
                
                // Enviar solicitud AJAX al servidor
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "Procesos/apply_discount.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        successMessage.style.display = "block"; // Mostrar mensaje de éxito
                    } else {
                        alert("Hubo un error al aplicar el descuento.");
                    }
                };
                xhr.send("email=" + encodeURIComponent(emailField.value));
            }
        });
        </script>
    </body>
</html>
