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

$departamentoGuardadoExitosamente = false;
$departamentoModificadoExitosamente = false;

// Variables para el formulario
$nombredepartamento = "";
$iddepartamento = "";

// Comprobar si hay un departamento que se desea editar
if (isset($_GET['editar'])) {
    $iddepartamento = $_GET['editar'];
    $sql = "SELECT * FROM departamento WHERE iddepartamento = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $iddepartamento);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $departamento = $resultado->fetch_assoc();

    // Pre-rellenar el formulario con el departamento seleccionado
    $nombredepartamento = $departamento['nombredepartamento'];
}

// Guardar o actualizar un departamento
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombredepartamento = $_POST['nombredepartamento'];

    if (isset($_POST['iddepartamento']) && $_POST['iddepartamento'] != "") {
        // Actualizar departamento
        $iddepartamento = $_POST['iddepartamento'];
        $sql = "UPDATE departamento SET nombredepartamento = ? WHERE iddepartamento = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nombredepartamento, $iddepartamento);
        if ($stmt->execute()) {
            $departamentoModificadoExitosamente = true;
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        // Insertar nuevo departamento
        $sql = "INSERT INTO departamento (nombredepartamento) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nombredepartamento);
        if ($stmt->execute()) {
            $departamentoGuardadoExitosamente = true;
            // Limpiar campos
            $nombredepartamento = "";
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    // Limpiar $iddepartamento para evitar que quede en modo edición
    $iddepartamento = ""; 
    $stmt->close();
}

// Consultar los departamentos ya existentes en la tabla `departamento`
$sql = "SELECT * FROM departamento";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Departamento</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h3><?php echo isset($iddepartamento) && $iddepartamento != "" ? "Modificar Departamento" : "Registrar Nuevo Departamento"; ?></h3>
        <a href="..\Vista\indexGerente.php" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Regresar</a>
        <form action="" method="POST">
            <div class="form-group">
                <label for="nombredepartamento">Nombre del Departamento</label>
                <input type="text" class="form-control" id="nombredepartamento" name="nombredepartamento" value="<?php echo $nombredepartamento; ?>" placeholder="Ingrese el nombre del departamento" required>
            </div>
            <input type="hidden" name="iddepartamento" value="<?php echo $iddepartamento; ?>">
            <button type="submit" class="btn btn-primary"><?php echo isset($iddepartamento) && $iddepartamento != "" ? "Modificar Departamento" : "Guardar Departamento"; ?></button>
            <a href="" class="btn btn-secondary">Nueva Departamento</a>
        </form>

        <!-- Mostrar los departamentos guardados en una tabla -->
        <h3>Departamentos Guardados</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Departamento</th>
                    <th>Nombre del Departamento</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Verificar si hay resultados
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row['iddepartamento'] . "</td>
                                <td>" . $row['nombredepartamento'] . "</td>
                                <td>
                                    <a href='?editar=" . $row['iddepartamento'] . "' class='btn btn-warning btn-sm'>Editar</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No hay departamentos guardados.</td></tr>";
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
            <h5 class="modal-title" id="modalExitoLabel"><?php echo $departamentoModificadoExitosamente ? "Departamento Modificado" : "Departamento Guardado"; ?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            ¡<?php echo $departamentoModificadoExitosamente ? "El departamento ha sido modificado exitosamente" : "Se ha guardado el departamento exitosamente"; ?>!
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
        // Mostrar el modal si el departamento fue guardado o modificado
        <?php if ($departamentoGuardadoExitosamente || $departamentoModificadoExitosamente): ?>
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
