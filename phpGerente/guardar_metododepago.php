<?php
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

$metodoGuardadoExitosamente = false;
$metodoModificadoExitosamente = false;

// Variables para el formulario
$trial_metodopago_2 = "";
$idmetodopago = "";

// Comprobar si hay un método de pago que se desea editar
if (isset($_GET['editar'])) {
    $idmetodopago = $_GET['editar'];
    $sql = "SELECT * FROM metodopago WHERE idmetodopago = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idmetodopago);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $metodo = $resultado->fetch_assoc();

    // Pre-rellenar el formulario con el método de pago seleccionado
    $trial_metodopago_2 = $metodo['metodopago'];
}

// Guardar o actualizar un método de pago
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $trial_metodopago_2 = $_POST['trial_metodopago_2'];

    if (isset($_POST['idmetodopago']) && $_POST['idmetodopago'] != "") {
        // Actualizar método de pago
        $idmetodopago = $_POST['idmetodopago'];
        $sql = "UPDATE metodopago SET metodopago = ? WHERE idmetodopago = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $trial_metodopago_2, $idmetodopago);
        if ($stmt->execute()) {
            $metodoModificadoExitosamente = true;
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        // Insertar nuevo método de pago
        $sql = "INSERT INTO metodopago (metodopago) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $trial_metodopago_2);
        if ($stmt->execute()) {
            $metodoGuardadoExitosamente = true;
            // Limpiar campo
            $trial_metodopago_2 = "";
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    // Limpiar $idmetodopago para evitar que quede en modo edición
    $idmetodopago = ""; 
    $stmt->close();
}

// Consultar los métodos de pago ya existentes en la tabla `metodopago`
$sql = "SELECT * FROM metodopago";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Método de Pago</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h3><?php echo isset($idmetodopago) && $idmetodopago != "" ? "Modificar Método de Pago" : "Registrar Nuevo Método de Pago"; ?></h3>
        <form action="" method="POST">
            <div class="form-group">
                <label for="trial_metodopago_2">Nombre del Método de Pago</label>
                <input type="text" class="form-control" id="trial_metodopago_2" name="trial_metodopago_2" value="<?php echo $trial_metodopago_2; ?>" placeholder="Ingrese el nombre del método de pago" required>
                <a href="..\Vista\indexGerente.php" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Regresar</a>
            </div>
            <input type="hidden" name="idmetodopago" value="<?php echo $idmetodopago; ?>">
            <button type="submit" class="btn btn-primary"><?php echo isset($idmetodopago) && $idmetodopago != "" ? "Modificar Método" : "Guardar Método"; ?></button>
            <a href="" class="btn btn-secondary">Nuevo Método</a>
        </form>

        <!-- Mostrar los métodos de pago guardados en una tabla -->
        <h3>Métodos de Pago Guardados</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Método de Pago</th>
                    <th>Nombre del Método de Pago</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Verificar si hay resultados
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row['idmetodopago'] . "</td>
                                <td>" . $row['metodopago'] . "</td>
                                <td>
                                    <a href='?editar=" . $row['idmetodopago'] . "' class='btn btn-warning btn-sm'>Editar</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No hay métodos de pago guardados.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal de éxito -->
    <div class="modal fade" id="modalExito" tabindex="-1" role="dialog" aria-labelledby="modalExitoLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalExitoLabel"><?php echo $metodoModificadoExitosamente ? "Método Modificado" : "Método Guardado"; ?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            ¡<?php echo $metodoModificadoExitosamente ? "El método de pago ha sido modificado exitosamente" : "Se ha guardado el método de pago exitosamente"; ?>!
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Agregar librerías JavaScript -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>

    <script>
    $(document).ready(function() {
        // Mostrar el modal si el método de pago fue guardado o modificado
        <?php if ($metodoGuardadoExitosamente || $metodoModificadoExitosamente): ?>
        $('#modalExito').modal('show');
        <?php endif; ?>
    });
    </script>
</body>
</html>

<?php
// Cerrar conexión
$conn->close();
?>
