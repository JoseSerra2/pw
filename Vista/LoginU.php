<?php include 'session.php'; ?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $nombreusuario = $_POST['email'];
    $contraseña = $_POST['password']; // La contraseña ingresada por el usuario

    // Consulta SQL para verificar el usuario
    $sql = "SELECT nombreusuario, contraseñausuario, idrol FROM usuario WHERE nombreusuario = ? AND estado = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nombreusuario);
    $stmt->execute();
    $stmt->store_result();

    // Verificar si el usuario existe
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($db_nombreusuario, $db_contraseña, $rol);
        $stmt->fetch();

        // Verificar la contraseña usando password_verify
        if (password_verify($contraseña, $db_contraseña)) {
            // Iniciar sesión y guardar variables de sesión
            $_SESSION['usuario'] = $nombreusuario;
            $_SESSION['rol'] = $rol;

            // Redirigir según el rol
            if ($rol == 3) {
                // Si es cliente
                header("Location: ../index.php");
                exit();
            } elseif ($rol == 1 ) { 
                //si es administrador
                header("Location: indexAdministrador.php");
                exit();
            }
            else {
                // Si es empleado o tiene otro rol
                header("Location: indexGerente.php");
                exit();
            }
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "Usuario no encontrado o inactivo.";
    }

    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E Shop</title>

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700&display=swap" rel="stylesheet">

    <!-- CSS Libraries -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="lib/slick/slick.css" rel="stylesheet">
    <link href="lib/slick/slick-theme.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f5f5f5;
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f5f5;
        }

        .login-box {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-box h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
        }

        .form-control {
            border-radius: 25px;
            padding: 10px 20px;
        }

        .btn-primary {
            border-radius: 25px;
            padding: 10px 20px;
            width: 100%;
        }

        .login-box img {
            display: block;
            margin: 0 auto 20px;
            width: 100px;
        }

        .login-box .form-group {
            margin-bottom: 20px;
        }

        .forgot-password {
            text-align: right;
            display: block;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .text-center {
            text-align: center;
        }

        footer {
            text-align: center;
            padding: 10px 0;
            background-color: #007bff;
            color: white;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>

    <!-- Login Form -->
    <div class="login-container">
        <div class="login-box">
            <img src="../img/logo.png" alt="Logo">
            <h2>Iniciar Sesión</h2>

            <form action="LoginU.php" method="POST">
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Ingrese su correo" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Ingrese su contraseña" required>
                </div>
                <button type="submit" class="btn btn-primary">Ingresar</button>
            </form>
            <div class="text-center mt-4">
                <p>¿No tienes cuenta? <a href="..\Vista\login.php">Regístrate</a></p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>E Shop &copy; 2024</p>
    </footer>

    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="lib/slick/slick.min.js"></script>

</body>
</html>
