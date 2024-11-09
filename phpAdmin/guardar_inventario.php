<?php
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "pw";

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Variables para manejar edición
$editMode = false;
$idInventarioEdit = 0;
$cantidadEdit = '';
$ubicacionEdit = '';
$idProductoEdit = '';
$idSucursalEdit = '';
$showSuccessModal = false; // Control del modal de éxito

// Guardar o actualizar inventario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar si se está editando
    if (isset($_POST['idinventario'])) {
        // Modo edición
        $idInventarioEdit = $_POST['idinventario'];
        $cantidad = $_POST['cantidad'];
        $ubicacion = $_POST['ubicacion'];
        $idProducto = $_POST['producto'];
        $idSucursal = $_POST['sucursal'];

        // Actualizar registro existente
        $sqlUpdate = "UPDATE inventario 
                      SET cantidad='$cantidad', ubicacion='$ubicacion', idproducto='$idProducto', idsucursal='$idSucursal' 
                      WHERE idinventario='$idInventarioEdit'";
        if ($conn->query($sqlUpdate) === TRUE) {
            $showSuccessModal = true; // Mostrar modal de éxito
        } else {
            echo "Error actualizando inventario: " . $conn->error;
        }
    } else {
        // Insertar nuevo inventario
        $cantidad = $_POST['cantidad'];
        $ubicacion = $_POST['ubicacion'];
        $idProducto = $_POST['producto'];
        $idSucursal = $_POST['sucursal'];

        $sqlInsert = "INSERT INTO inventario (cantidad, ubicacion, idproducto, idsucursal) 
                      VALUES ('$cantidad', '$ubicacion', '$idProducto', '$idSucursal')";

        if ($conn->query($sqlInsert) === TRUE) {
            $showSuccessModal = true; // Mostrar modal de éxito
        } else {
            echo "Error: " . $sqlInsert . "<br>" . $conn->error;
        }
    }
}

// Modo edición: seleccionar inventario para editar
if (isset($_GET['edit'])) {
    $idInventarioEdit = $_GET['edit'];

    $sqlEdit = "SELECT * FROM inventario WHERE idinventario='$idInventarioEdit'";
    $resultEdit = $conn->query($sqlEdit);

    if ($resultEdit->num_rows > 0) {
        $row = $resultEdit->fetch_assoc();
        $cantidadEdit = $row['cantidad'];
        $ubicacionEdit = $row['ubicacion'];
        $idProductoEdit = $row['idproducto'];
        $idSucursalEdit = $row['idsucursal'];
        $editMode = true;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Inventario</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1>Gestión de Inventario</h1>
        <a href="..\Vista\indexAdministrador.php" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Regresar</a>

        <!-- Formulario de ingreso/modificación -->
        <form action="" method="POST" class="mb-5">
            <?php if ($editMode): ?>
                <input type="hidden" name="idinventario" value="<?php echo $idInventarioEdit; ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="cantidad">Cantidad:</label>
                <input type="number" class="form-control" id="cantidad" name="cantidad" value="<?php echo $cantidadEdit; ?>" required>
            </div>

            <div class="form-group">
                <label for="ubicacion">Ubicación:</label>
                <input type="text" class="form-control" id="ubicacion" name="ubicacion" value="<?php echo $ubicacionEdit; ?>" maxlength="150" required>
            </div>

            <div class="form-group">
                <label for="producto">Producto:</label>
                <select class="form-control" id="producto" name="producto" required>
                    <option value="">Seleccione un producto</option>
                    <?php
                    $sqlProducto = "SELECT idproducto, nombre FROM producto";
                    $resultProducto = $conn->query($sqlProducto);
                    while($row = $resultProducto->fetch_assoc()) {
                        $selected = ($row['idproducto'] == $idProductoEdit) ? 'selected' : '';
                        echo "<option value='" . $row['idproducto'] . "' $selected>" . $row['nombre'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="sucursal">Sucursal:</label>
                <select class="form-control" id="sucursal" name="sucursal" required>
                    <option value="">Seleccione una sucursal</option>
                    <?php
                    $sqlSucursal = "SELECT idsucursal, nombresucursal FROM sucursal";
                    $resultSucursal = $conn->query($sqlSucursal);
                    while($row = $resultSucursal->fetch_assoc()) {
                        $selected = ($row['idsucursal'] == $idSucursalEdit) ? 'selected' : '';
                        echo "<option value='" . $row['idsucursal'] . "' $selected>" . $row['nombresucursal'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary"><?php echo $editMode ? 'Actualizar' : 'Guardar'; ?></button>
        </form>

        <!-- Tabla de inventario -->
        <h2>Inventario Actual</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cantidad</th>
                    <th>Ubicación</th>
                    <th>Producto</th>
                    <th>Sucursal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sqlInventario = "SELECT i.idinventario, i.cantidad, i.ubicacion, p.nombre as producto, s.nombresucursal as sucursal 
                                  FROM inventario i
                                  JOIN producto p ON i.idproducto = p.idproducto
                                  JOIN sucursal s ON i.idsucursal = s.idsucursal";
                $resultInventario = $conn->query($sqlInventario);

                if ($resultInventario->num_rows > 0) {
                    while($row = $resultInventario->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['idinventario'] . "</td>";
                        echo "<td>" . $row['cantidad'] . "</td>";
                        echo "<td>" . $row['ubicacion'] . "</td>";
                        echo "<td>" . $row['producto'] . "</td>";
                        echo "<td>" . $row['sucursal'] . "</td>";
                        echo "<td><a href='?edit=" . $row['idinventario'] . "' class='btn btn-warning'>Editar</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No hay inventario disponible.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal de éxito -->
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
            <?php echo $editMode ? 'Inventario actualizado correctamente.' : 'Nuevo inventario agregado correctamente.'; ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Mostrar modal si $showSuccessModal es verdadero -->
    <?php if ($showSuccessModal): ?>
    <script>
        $(document).ready(function() {
            $('#successModal').modal('show');
        });
    </script>
    <?php endif; ?>

</body>
</html>
