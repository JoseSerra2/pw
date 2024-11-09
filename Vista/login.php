<?php include 'session.php'; ?>
<?php
// Manejo del registro de cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'register') {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $apellido = $conn->real_escape_string($_POST['apellido']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $direccion = $conn->real_escape_string($_POST['direccion']);
    $latitud = $conn->real_escape_string($_POST['gps_latitud']);
    $longitud = $conn->real_escape_string($_POST['gps_longitud']);
    $contrasena = isset($_POST['contrasena']) ? $conn->real_escape_string($_POST['contrasena']) : ''; // Asegurar que la contraseña sea enviada

    // Obtener el ID del rol de cliente
    $rolQuery = "SELECT idrol FROM rol WHERE nombrerol = 'cliente'";
    $rolResult = $conn->query($rolQuery);
    $rolData = $rolResult->fetch_assoc();
    $idRolCliente = $rolData['idrol'];

    if ($latitud >= -90 && $latitud <= 90 && $longitud >= -180 && $longitud <= 180) {
        // Insertar el nuevo cliente
        $stmtCliente = $conn->prepare("INSERT INTO cliente (nombre, correo, direccion, gps_latitud, gps_longitud) VALUES (?, ?, ?, ?, ?)");
        $stmtCliente->bind_param("sssss", $nombre, $correo, $direccion, $latitud, $longitud);

        if ($stmtCliente->execute()) {
            // Obtener ID del nuevo cliente
            $clienteId = $stmtCliente->insert_id;

            // Insertar el nuevo usuario
            $contraseñaHashed = password_hash($contrasena, PASSWORD_DEFAULT); // Hasheando la contraseña
            $stmtUsuario = $conn->prepare("INSERT INTO usuario (nombreusuario, contraseñausuario, estado, idrol) VALUES (?, ?, 1, ?)");
            $stmtUsuario->bind_param("ssi", $correo, $contraseñaHashed, $idRolCliente);
            
            if ($stmtUsuario->execute()) {
                // Mensaje de éxito para el modal
                echo "<script>document.addEventListener('DOMContentLoaded', function() { 
                    $('#registroExitosoModal').modal('show'); 
                });</script>";
            } else {
                echo "Error al registrar usuario: " . $stmtUsuario->error;
            }

            // Cerrar la sentencia del usuario
            $stmtUsuario->close();
        } else {
            echo "Error al registrar cliente: " . $stmtCliente->error;
        }

        // Cerrar la sentencia del cliente
        $stmtCliente->close();
    } else {
        echo "Coordenadas inválidas: latitud = $latitud, longitud = $longitud";
    }
}

$conn->close();
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
        <link href="../lib/slick/slick.css" rel="stylesheet">
        <link href="../lib/slick/slick-theme.css" rel="stylesheet">
        <!-- Template Stylesheet -->
        <link href="../css/style.css" rel="stylesheet">
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
                    <li class="breadcrumb-item active">Iniciar Sesion</li>
                </ul>
            </div>
        </div>
        <!-- Breadcrumb End -->
        
        <!-- Login Start -->
        <div class="login">
            <div class="container">
                <div class="section-header">
                    <h3>Registrar Usuario y Iniciar Sesion</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec viverra at massa sit amet ultricies. Nullam consequat, mauris non interdum cursus</p>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="login-form">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>E-mail / Usuario</label>
                                    <input class="form-control" type="text" name="correo" placeholder="Usuario o E-mail" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de Registro -->
                    <div class="col-md-6">    
                        <div class="register-form">
                            <form id="registro-form" method="POST" action="login.php">
                                <input type="hidden" name="action" value="register">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Nombre</label>
                                        <input class="form-control" type="text" name="nombre" placeholder="Nombre" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Apellido</label>
                                        <input class="form-control" type="text" name="apellido" placeholder="Apellido" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>E-mail</label>
                                        <input class="form-control" type="email" name="correo" placeholder="E-mail" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Número de teléfono</label>
                                        <input class="form-control" type="text" name="telefono" placeholder="Número de teléfono" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Dirección</label>
                                        <input class="form-control" type="text" name="direccion" placeholder="Dirección" required id="direccion">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Contraseña</label>
                                        <input class="form-control" type="password" name="contrasena" placeholder="Contraseña" required>
                                    </div>
                                    <input type="hidden" name="gps_latitud" id="latitud" readonly>
                                    <input type="hidden" name="gps_longitud" id="longitud" readonly>
                                    <div class="col-md-12 mt-3">
                                        <button class="btn" type="submit">Registrar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Login End -->

        <!-- Modal de Registro Exitoso -->
        <div class="modal fade" id="registroExitosoModal" tabindex="-1" aria-labelledby="registroExitosoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registroExitosoModalLabel">Registro Exitoso</h5>
                        <!-- Reemplazado btn-close y data-bs-dismiss -->
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        ¡Tu registro ha sido exitoso!
                    </div>
                    <div class="modal-footer">
                        <!-- Reemplazado data-bs-dismiss -->
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.getElementById('registro-form').addEventListener('submit', function(event) {
                event.preventDefault(); // Evitar el envío del formulario

                var direccion = document.getElementById('direccion').value;
                if (direccion) {
                    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(direccion)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.length > 0) {
                                // Obtener la primera coincidencia
                                var latitud = data[0].lat;
                                var longitud = data[0].lon;
                                document.getElementById('latitud').value = latitud;
                                document.getElementById('longitud').value = longitud;

                                // Una vez que se han obtenido las coordenadas, enviar el formulario
                                this.submit();
                            } else {
                                alert('No se encontraron coordenadas para la dirección proporcionada.');
                            }
                        })
                        .catch(error => {
                            console.error('Error al obtener coordenadas:', error);
                            alert('Ocurrió un error al intentar obtener las coordenadas.');
                        });
                } else {
                    alert('Por favor, ingresa una dirección.');
                }
            });
        </script>


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
