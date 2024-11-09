<?php
session_start();
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

$promocionGuardadaExitosamente = false;
$promocionModificadaExitosamente = false;

// Variables para el formulario
$nombrePromocion = "";
$descripcion = "";
$fechainicio = "";
$fechafin = "";
$descuento = "";
$estado = "";
$idpromocion = "";

// Comprobar si hay una promoción que se desea editar
if (isset($_GET['editar'])) {
    $idpromocion = $_GET['editar'];
    $sql = "SELECT * FROM promocion WHERE idpromocion = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idpromocion);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $promocion = $resultado->fetch_assoc();

    // Pre-rellenar el formulario con la promoción seleccionada
    $nombrePromocion = $promocion['nombrepromocion'];
    $descripcion = $promocion['descripcion'];
    $fechainicio = $promocion['fechainicio'];
    $fechafin = $promocion['fechafin'];
    $descuento = $promocion['descuento'];
    $estado = $promocion['estado'];
}

// Guardar o actualizar una promoción
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombrePromocion = $_POST['trial_nombrepromocion_2'];
    $descripcion = $_POST['descripcion'];
    $fechainicio = $_POST['fechainicio'];
    $fechafin = $_POST['fechafin'];
    $descuento = $_POST['descuento'];
    $estado = $_POST['estado'];

    if (isset($_POST['idpromocion']) && $_POST['idpromocion'] != "") {
        // Actualizar promoción
        $idpromocion = $_POST['idpromocion'];
        $sql = "UPDATE promocion SET nombrepromocion = ?, descripcion = ?, fechainicio = ?, fechafin = ?, descuento = ?, estado = ? WHERE idpromocion = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssdis", $nombrePromocion, $descripcion, $fechainicio, $fechafin, $descuento, $estado, $idpromocion);
        if ($stmt->execute()) {
            $promocionModificadaExitosamente = true;
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        // Insertar nueva promoción
        $sql = "INSERT INTO promocion (nombrepromocion, descripcion, fechainicio, fechafin, descuento, estado) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssdi", $nombrePromocion, $descripcion, $fechainicio, $fechafin, $descuento, $estado);
        if ($stmt->execute()) {
            $promocionGuardadaExitosamente = true;
            // Limpiar campos
            $nombrePromocion = "";
            $descripcion = "";
            $fechainicio = "";
            $fechafin = "";
            $descuento = "";
            $estado = "";
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    // Limpiar $idpromocion para evitar que quede en modo edición
    $idpromocion = "";
    $stmt->close();
}

// Consultar las promociones ya existentes en la tabla `promocion`
$sql = "SELECT * FROM promocion";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Promoción</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h3><?php echo isset($idpromocion) && $idpromocion != "" ? "Modificar Promoción" : "Registrar Nueva Promoción"; ?></h3>
        <a href="..\Vista\indexGerente.php" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Regresar</a>
        <form action="" method="POST">
            <div class="form-group">
                <label for="nombrepromocion">Nombre de la Promoción</label>
                <input type="text" class="form-control" id="nombrepromocion" name="trial_nombrepromocion_2" value="<?php echo $nombrePromocion; ?>" required>
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required><?php echo $descripcion; ?></textarea>
            </div>

            <div class="form-group">
                <label for="fechainicio">Fecha de Inicio</label>
                <input type="datetime-local" class="form-control" id="fechainicio" name="fechainicio" value="<?php echo $fechainicio; ?>" required>
            </div>

            <div class="form-group">
                <label for="fechafin">Fecha de Fin</label>
                <input type="datetime-local" class="form-control" id="fechafin" name="fechafin" value="<?php echo $fechafin; ?>" required>
            </div>

            <div class="form-group">
                <label for="descuento">Descuento</label>
                <input type="number" class="form-control" step="0.00000001" id="descuento" name="descuento" value="<?php echo $descuento; ?>" required>
            </div>

            <div class="form-group">
                <label for="estado">Estado</label>
                <select class="form-control" id="estado" name="estado" required>
                    <option value="1" <?php echo $estado == '1' ? 'selected' : ''; ?>>Activo</option>
                    <option value="0" <?php echo $estado == '0' ? 'selected' : ''; ?>>Inactivo</option>
                </select>
            </div>

            <input type="hidden" name="idpromocion" value="<?php echo $idpromocion; ?>">
            <button type="submit" class="btn btn-primary"><?php echo isset($idpromocion) && $idpromocion != "" ? "Modificar Promoción" : "Guardar Promoción"; ?></button>
            <a href="" class="btn btn-secondary">Nueva Promoción</a>
        </form>

        <!-- Mostrar las promociones guardadas en una tabla -->
        <h3>Promociones Guardadas</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Promoción</th>
                    <th>Nombre de la Promoción</th>
                    <th>Descripción</th>
                    <th>Fecha de Inicio</th>
                    <th>Fecha de Fin</th>
                    <th>Descuento</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Verificar si hay resultados
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row['idpromocion'] . "</td>
                                <td>" . $row['nombrepromocion'] . "</td>
                                <td>" . $row['descripcion'] . "</td>
                                <td>" . $row['fechainicio'] . "</td>
                                <td>" . $row['fechafin'] . "</td>
                                <td>" . $row['descuento'] . "</td>
                                <td>" . ($row['estado'] ? 'Activo' : 'Inactivo') . "</td>
                                <td>
                                    <a href='?editar=" . $row['idpromocion'] . "' class='btn btn-warning btn-sm'>Editar</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No hay promociones guardadas.</td></tr>";
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
            <h5 class="modal-title" id="modalExitoLabel"><?php echo $promocionModificadaExitosamente ? "Promoción Modificada" : "Promoción Guardada"; ?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            ¡<?php echo $promocionModificadaExitosamente ? "La promoción ha sido modificada exitosamente" : "Se ha guardado la promoción exitosamente"; ?>!
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
        // Mostrar el modal si la promoción fue guardada o modificada
        <?php if ($promocionGuardadaExitosamente || $promocionModificadaExitosamente): ?>
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
