<?php
// Conexión a la base de datos
include('../Vista/Session.php');

// Verifica si se recibió el ID del historial
if (isset($_POST['idhistorial'])) {
    $idhistorial = $_POST['idhistorial'];
    $accion = $_POST['accion']; // Recibe la acción seleccionada

    // Actualiza la acción en el historial
    $query = "UPDATE historial SET accion = ? WHERE idhistorial = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $accion, $idhistorial);
    $stmt->execute();
    $success_message = "Acción actualizada con éxito.";
}

// Obtener datos para mostrar en el formulario
$query = "SELECT * FROM historial";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Acciones</title>

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
<a href="..\Vista\indexGerente.php" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Regresar</a>
<div class="container mt-4">
    <h2 class="mb-4">Historial de Acciones</h2>

    <!-- Tabla de historial -->
    <table class="table table-striped mt-4">
        <thead>
            <tr>
                <th>Acción</th>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Cliente</th>
                <th>Acciones</th> <!-- Columna para los botones -->
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) {
                // Obtener el cliente asociado a este historial (relacionando nombreusuario con correo)
                $usuarioId = $row['idusuario'];
                $clienteQuery = "SELECT * FROM cliente WHERE correo = (SELECT correo FROM usuario WHERE idusuario = ?)";
                $clienteStmt = $conn->prepare($clienteQuery);
                $clienteStmt->bind_param("i", $usuarioId);
                $clienteStmt->execute();
                $clienteResult = $clienteStmt->get_result();
                
                // Verificar si se obtuvo un cliente
                if ($clienteResult->num_rows > 0) {
                    $cliente = $clienteResult->fetch_assoc();
                } else {
                    // Si no se encuentra el cliente, asignar un valor predeterminado
                    $cliente = ['nombre' => 'Cliente no encontrado', 'correo' => 'N/A'];
                }
            ?>
                <tr>
                    <td><?php echo $row['accion']; ?></td>
                    <td><?php echo $row['fecha']; ?></td>
                    <td><?php echo $row['idusuario']; ?></td>
                    <td><?php echo $cliente['nombre']; ?> (<?php echo $cliente['correo']; ?>)</td>
                    <td>
                        <!-- Botón y select de actualización con estilo -->
                        <form method="POST" action="historial.php" style="display:inline-flex; align-items:center;">
                            <input type="hidden" name="idhistorial" value="<?php echo $row['idhistorial']; ?>" />
                            <select name="accion" class="form-control mr-2" style="width: auto;">
                                <option value="entregado" <?php echo ($row['accion'] == 'entregado') ? 'selected' : ''; ?>>Entregado</option>
                                <option value="pagado" <?php echo ($row['accion'] == 'pagado') ? 'selected' : ''; ?>>Pagado</option>
                                <option value="cancelado" <?php echo ($row['accion'] == 'cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                            </select>
                            <button type="submit" class="btn btn-warning btn-sm">Actualizar</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Modal de éxito -->
<?php if (isset($success_message)) { ?>
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Éxito</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php echo $success_message; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mostrar el modal de éxito cuando la acción se actualice
        $(document).ready(function() {
            $('#successModal').modal('show');
        });
    </script>
<?php } ?>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

</body>
</html>
