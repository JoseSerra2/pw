<?php
session_start();

if (isset($_POST['email'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Suponemos que quieres aplicar un 10% adicional al descuento actual
        $additionalDiscount = 0.10;

        // Si la sesión del carrito ya tiene descuentos, los aplicamos
        if (isset($_SESSION['descuento_adicional'])) {
            $_SESSION['descuento_adicional'] += $additionalDiscount;
        } else {
            $_SESSION['descuento_adicional'] = $additionalDiscount;
        }

        // Respuesta exitosa
        echo "Descuento aplicado exitosamente";
    } else {
        // Error de formato de email
        http_response_code(400); // Bad Request
        echo "Email no válido";
    }
} else {
    http_response_code(400); // Bad Request
    echo "Email no proporcionado";
}
?>
