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

$rolGuardadoExitosamente = false; // Bandera para controlar si se ha guardado el rol
$rolModificadoExitosamente = false; // Bandera para control de modificación

// Editar un rol
$nombrerol = "";
$idrol = "";

// Comprobar si hay un rol que se desea editar
if (isset($_GET['editar'])) {
    $idrol = $_GET['editar'];
    $sql = "SELECT * FROM rol WHERE idrol = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idrol);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $rol = $resultado->fetch_assoc();
    
    // Pre-rellenar el formulario con el rol seleccionado
    $nombrerol = $rol['nombrerol'];
}

// Guardar o actualizar un rol
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombrerol = $_POST['nombrerol'];

    if (isset($_POST['idrol']) && $_POST['idrol'] != "") {
        // Actualizar el rol
        $idrol = $_POST['idrol'];
        $sql = "UPDATE rol SET nombrerol = ? WHERE idrol = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nombrerol, $idrol);
        if ($stmt->execute()) {
            $rolModificadoExitosamente = true;
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        // Insertar un nuevo rol
        $sql = "INSERT INTO rol (nombrerol) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nombrerol);
        if ($stmt->execute()) {
            $rolGuardadoExitosamente = true;
            // Limpiar campos después de guardar el rol
            $nombrerol = "";  // Limpiar el nombre del rol
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    // Cerrar declaración
    $stmt->close();
    // Limpiar $idrol para evitar que quede en modo edición
    $idrol = ""; 
}

// Consultar los roles ya existentes en la tabla `rol`
$sql = "SELECT * FROM rol";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Rol</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h3><?php echo isset($idrol) && $idrol != "" ? "Modificar Rol" : "Registrar Nuevo Rol"; ?></h3>
        <a href="..\Vista\indexGerente.php" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Regresar</a>
        <form action="" method="POST">
            <div class="form-group">
                <label for="nombrerol">Nombre del Rol</label>
                <input type="text" class="form-control" id="nombrerol" name="nombrerol" value="<?php echo $nombrerol; ?>" placeholder="Ingrese el nombre del rol" required>
                <input type="hidden" name="idrol" value="<?php echo $idrol; ?>">
            </div>
            <button type="submit" class="btn btn-primary"><?php echo isset($idrol) && $idrol != "" ? "Modificar Rol" : "Guardar Rol"; ?></button>
            <a href="" class="btn btn-secondary">Nuevo Rol</a> <!-- Botón para Nuevo Rol -->
        </form>

        <!-- Mostrar los roles guardados en una tabla -->
        <h3>Roles Guardados</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Rol</th>
                    <th>Nombre del Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Verificar si hay resultados
                if ($result->num_rows > 0) {
                    // Mostrar cada fila de la tabla
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row['idrol'] . "</td>
                                <td>" . $row['nombrerol'] . "</td>
                                <td>
                                    <a href='?editar=" . $row['idrol'] . "' class='btn btn-warning btn-sm'>Editar</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No hay roles guardados.</td></tr>";
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
            <h5 class="modal-title" id="modalExitoLabel"><?php echo $rolModificadoExitosamente ? "Rol Modificado" : "Rol Guardado"; ?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            ¡<?php echo $rolModificadoExitosamente ? "El rol ha sido modificado exitosamente" : "Se ha guardado el rol exitosamente"; ?>!
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
        // Mostrar el modal si el rol fue guardado o modificado
        <?php if ($rolGuardadoExitosamente || $rolModificadoExitosamente): ?>
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
