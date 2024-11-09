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

$ciudadGuardadaExitosamente = false;
$ciudadModificadaExitosamente = false;

// Variables para el formulario
$nombreciudad = "";
$idciudad = "";
$iddepartamento = "";

// Consultar departamentos para el desplegable
$sqlDepartamentos = "SELECT * FROM departamento";
$resultDepartamentos = $conn->query($sqlDepartamentos);

// Comprobar si hay una ciudad que se desea editar
if (isset($_GET['editar'])) {
    $idciudad = $_GET['editar'];
    $sql = "SELECT * FROM ciudad WHERE idciudad = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idciudad);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $ciudad = $resultado->fetch_assoc();

    // Pre-rellenar el formulario con la ciudad seleccionada
    $nombreciudad = $ciudad['nombreciudad'];
    $iddepartamento = $ciudad['iddepartamento'];
}

// Guardar o actualizar una ciudad
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombreciudad = $_POST['nombreciudad'];
    $iddepartamento = $_POST['iddepartamento'];

    if (isset($_POST['idciudad']) && $_POST['idciudad'] != "") {
        // Actualizar ciudad
        $idciudad = $_POST['idciudad'];
        $sql = "UPDATE ciudad SET nombreciudad = ?, iddepartamento = ? WHERE idciudad = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $nombreciudad, $iddepartamento, $idciudad);
        if ($stmt->execute()) {
            $ciudadModificadaExitosamente = true;
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        // Insertar nueva ciudad
        $sql = "INSERT INTO ciudad (nombreciudad, iddepartamento) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nombreciudad, $iddepartamento);
        if ($stmt->execute()) {
            $ciudadGuardadaExitosamente = true;
            // Limpiar campos
            $nombreciudad = "";
            $iddepartamento = "";
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    // Limpiar $idciudad para evitar que quede en modo edición
    $idciudad = ""; 
    $stmt->close();
}

// Consultar las ciudades ya existentes en la tabla `ciudad`
$sql = "SELECT * FROM ciudad";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Ciudad</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h3><?php echo isset($idciudad) && $idciudad != "" ? "Modificar Ciudad" : "Registrar Nueva Ciudad"; ?></h3>
        <form action="" method="POST">
            <div class="form-group">
                <label for="nombreciudad">Nombre de la Ciudad</label>
                <input type="text" class="form-control" id="nombreciudad" name="nombreciudad" value="<?php echo $nombreciudad; ?>" placeholder="Ingrese el nombre de la ciudad" required>
                <a href="..\Vista\indexGerente.php" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Regresar</a>
            </div>
            <div class="form-group">
                <label for="iddepartamento">Departamento</label>
                <select class="form-control" id="iddepartamento" name="iddepartamento" required>
                    <option value="">Seleccione un departamento</option>
                    <?php
                    // Verificar si hay departamentos disponibles
                    if ($resultDepartamentos->num_rows > 0) {
                        while ($row = $resultDepartamentos->fetch_assoc()) {
                            $selected = ($row['iddepartamento'] == $iddepartamento) ? "selected" : "";
                            echo "<option value='" . $row['iddepartamento'] . "' $selected>" . $row['nombredepartamento'] . "</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No hay departamentos disponibles.</option>";
                    }
                    ?>
                </select>
            </div>
            <input type="hidden" name="idciudad" value="<?php echo $idciudad; ?>">
            <button type="submit" class="btn btn-primary"><?php echo isset($idciudad) && $idciudad != "" ? "Modificar Ciudad" : "Guardar Ciudad"; ?></button>
            <a href="" class="btn btn-secondary">Nueva Ciudad</a>
        </form>

        <!-- Mostrar las ciudades guardadas en una tabla -->
        <h3>Ciudades Guardadas</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Ciudad</th>
                    <th>Nombre de la Ciudad</th>
                    <th>Departamento</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Verificar si hay resultados
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Obtener el nombre del departamento
                        $idDepartamento = $row['iddepartamento'];
                        $sqlDept = "SELECT nombredepartamento FROM departamento WHERE iddepartamento = ?";
                        $stmt = $conn->prepare($sqlDept);
                        $stmt->bind_param("i", $idDepartamento);
                        $stmt->execute();
                        $resultadoDept = $stmt->get_result();
                        $departamento = $resultadoDept->fetch_assoc();
                        $nombreDepartamento = $departamento ? $departamento['nombredepartamento'] : 'Sin Departamento';

                        echo "<tr>
                                <td>" . $row['idciudad'] . "</td>
                                <td>" . $row['nombreciudad'] . "</td>
                                <td>" . $nombreDepartamento . "</td>
                                <td>
                                    <a href='?editar=" . $row['idciudad'] . "' class='btn btn-warning btn-sm'>Editar</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No hay ciudades guardadas.</td></tr>";
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
            <h5 class="modal-title" id="modalExitoLabel"><?php echo $ciudadModificadaExitosamente ? "Ciudad Modificada" : "Ciudad Guardada"; ?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            ¡<?php echo $ciudadModificadaExitosamente ? "La ciudad ha sido modificada exitosamente" : "Se ha guardado la ciudad exitosamente"; ?>!
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
        // Mostrar el modal si la ciudad fue guardada o modificada
        <?php if ($ciudadGuardadaExitosamente || $ciudadModificadaExitosamente): ?>
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
