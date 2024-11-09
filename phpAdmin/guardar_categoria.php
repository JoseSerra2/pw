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

$categoriaGuardadaExitosamente = false;
$categoriaModificadaExitosamente = false;

// Variables para el formulario
$nombre = "";
$idcategoria = "";

// Comprobar si hay una categoría que se desea editar
if (isset($_GET['editar'])) {
    $idcategoria = $_GET['editar'];
    $sql = "SELECT * FROM categoria WHERE idcategoria = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idcategoria);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $categoria = $resultado->fetch_assoc();

    // Pre-rellenar el formulario con la categoría seleccionada
    $nombre = $categoria['nombre'];
}

// Guardar o actualizar una categoría
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];

    if (isset($_POST['idcategoria']) && $_POST['idcategoria'] != "") {
        // Actualizar categoría
        $idcategoria = $_POST['idcategoria'];
        $sql = "UPDATE categoria SET nombre = ? WHERE idcategoria = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nombre, $idcategoria);
        if ($stmt->execute()) {
            $categoriaModificadaExitosamente = true;
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        // Insertar nueva categoría
        $sql = "INSERT INTO categoria (nombre) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nombre);
        if ($stmt->execute()) {
            $categoriaGuardadaExitosamente = true;
            // Limpiar campos
            $nombre = "";
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    // Limpiar $idcategoria para evitar que quede en modo edición
    $idcategoria = ""; 
    $stmt->close();
}

// Consultar las categorías ya existentes en la tabla `categoria`
$sql = "SELECT * FROM categoria";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Categoría</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h3><?php echo isset($idcategoria) && $idcategoria != "" ? "Modificar Categoría" : "Registrar Nueva Categoría"; ?></h3>
        <form action="" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre de la Categoría</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $nombre; ?>" placeholder="Ingrese el nombre de la categoría" required>
                <a href="..\Vista\indexAdministrador.php" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Regresar</a>
            </div>
            <input type="hidden" name="idcategoria" value="<?php echo $idcategoria; ?>">
            <button type="submit" class="btn btn-primary"><?php echo isset($idcategoria) && $idcategoria != "" ? "Modificar Categoría" : "Guardar Categoría"; ?></button>
            <a href="" class="btn btn-secondary">Nueva Categoría</a>
        </form>

        <!-- Mostrar las categorías guardadas en una tabla -->
        <h3>Categorías Guardadas</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Categoría</th>
                    <th>Nombre de la Categoría</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Verificar si hay resultados
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row['idcategoria'] . "</td>
                                <td>" . $row['nombre'] . "</td>
                                <td>
                                    <a href='?editar=" . $row['idcategoria'] . "' class='btn btn-warning btn-sm'>Editar</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No hay categorías guardadas.</td></tr>";
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
            <h5 class="modal-title" id="modalExitoLabel"><?php echo $categoriaModificadaExitosamente ? "Categoría Modificada" : "Categoría Guardada"; ?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            ¡<?php echo $categoriaModificadaExitosamente ? "La categoría ha sido modificada exitosamente" : "Se ha guardado la categoría exitosamente"; ?>!
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
        // Mostrar el modal si la categoría fue guardada o modificada
        <?php if ($categoriaGuardadaExitosamente || $categoriaModificadaExitosamente): ?>
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
