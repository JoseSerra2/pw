<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "pw";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si el usuario ha iniciado sesión
if (isset($_SESSION['usuario'])) {
    $usuario_actual = $_SESSION['usuario']; // Usuario logueado
    $rol_actual = $_SESSION['rol'];         // Rol del usuario logueado

    // Obtener id del cliente (ajustado a 'idcliente' en lugar de 'idusuario')
    $queryUsuario = "SELECT idcliente FROM cliente WHERE correo = ?";
    $stmtUsuario = $conn->prepare($queryUsuario);
    $stmtUsuario->bind_param("s", $usuario_actual);
    $stmtUsuario->execute();
    $resultUsuario = $stmtUsuario->get_result();

    if ($resultUsuario->num_rows > 0) {
        $usuarioData = $resultUsuario->fetch_assoc();
        $_SESSION['usuario_id'] = $usuarioData['idcliente']; // Guardar idcliente en sesión
    } 
} else {
    $usuario_actual = null; // No hay usuario logueado
    $rol_actual = null;     // No hay rol asignado
}

// Obtener los datos del cliente
if ($usuario_actual) {
    $queryCliente = "SELECT * FROM cliente WHERE correo = ?";
    $stmtCliente = $conn->prepare($queryCliente);
    $stmtCliente->bind_param("s", $usuario_actual);
    $stmtCliente->execute();
    $resultCliente = $stmtCliente->get_result();

    if ($resultCliente->num_rows > 0) {
        $cliente = $resultCliente->fetch_assoc(); // Datos del cliente
    } else {
        $cliente = null; // No se encontró el cliente
    }
}

// Código para cerrar sesión
if (isset($_GET['logout'])) {
    session_unset(); // Destruir todas las variables de sesión
    session_destroy(); // Destruir la sesión
    header("Location: " . $_SERVER['PHP_SELF']); // Recargar la página actual
    exit();
}
?>
