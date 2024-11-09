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

// Variables para manejar edición y finalizar traslado
$editMode = false;
$idTrasladoEdit = 0;
$cantidadEdit = '';
$fechaEdit = '';
$idProductoEdit = '';
$idSucursalOrigenEdit = '';
$idSucursalDestinoEdit = '';
$showSuccessModal = false; // Control del modal de éxito
$showErrorModal = false;  // Variable para mostrar el modal de error
$errorMessage = "";       // Variable para el mensaje de error

// Finalizar traslado
if (isset($_GET['finalizar'])) {
    $idTrasladoFinalizar = $_GET['finalizar'];

    // Obtener detalles del traslado
    $sqlTraslado = "SELECT cantidad, idproducto, idsucursalorigen, idsucursaldestino FROM traslado WHERE idtraslado='$idTrasladoFinalizar'";
    $resultTraslado = $conn->query($sqlTraslado);

    if ($resultTraslado->num_rows > 0) {
        $row = $resultTraslado->fetch_assoc();
        $cantidadTraslado = $row['cantidad'];
        $idProducto = $row['idproducto'];
        $idSucursalOrigen = $row['idsucursalorigen'];
        $idSucursalDestino = $row['idsucursaldestino'];

        // Actualizar inventario de la sucursal destino sumando la cantidad trasladada
        $sqlUpdateInventarioDestino = "UPDATE inventario 
                                        SET cantidad = cantidad + $cantidadTraslado 
                                        WHERE idproducto = '$idProducto' AND idsucursal = '$idSucursalDestino'";
        $conn->query($sqlUpdateInventarioDestino);

        // Actualizar inventario de la sucursal origen restando la cantidad trasladada
        $sqlUpdateInventarioOrigen = "UPDATE inventario 
                                       SET cantidad = cantidad - $cantidadTraslado 
                                       WHERE idproducto = '$idProducto' AND idsucursal = '$idSucursalOrigen'";
        $conn->query($sqlUpdateInventarioOrigen);

        // Actualizar traslado para que su cantidad sea 0
        $sqlUpdateTraslado = "UPDATE traslado SET cantidad = 0 WHERE idtraslado = '$idTrasladoFinalizar'";
        $conn->query($sqlUpdateTraslado);

        $showSuccessModal = true; // Mostrar modal de éxito
    }
}

// Guardar o actualizar traslado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cantidad = $_POST['cantidad'];
    $fecha = $_POST['fecha'];
    $idProducto = $_POST['producto'];
    $idSucursalOrigen = $_POST['sucursal_origen'];
    $idSucursalDestino = $_POST['sucursal_destino'];

    // Verificar si la sucursal de origen tiene el producto en inventario
    $sqlVerificarInventario = "SELECT cantidad FROM inventario WHERE idproducto = '$idProducto' AND idsucursal = '$idSucursalOrigen'";
    $resultInventario = $conn->query($sqlVerificarInventario);

    if ($resultInventario->num_rows > 0) {
        $rowInventario = $resultInventario->fetch_assoc();
        $cantidadDisponible = $rowInventario['cantidad'];

        if ($cantidadDisponible >= $cantidad) {
            if (isset($_POST['idtraslado'])) {
                // Modo edición
                $idTrasladoEdit = $_POST['idtraslado'];

                // Actualizar registro existente
                $sqlUpdate = "UPDATE traslado 
                              SET cantidad='$cantidad', fecha='$fecha', idproducto='$idProducto', idsucursalorigen='$idSucursalOrigen', idsucursaldestino='$idSucursalDestino' 
                              WHERE idtraslado='$idTrasladoEdit'";
                if ($conn->query($sqlUpdate) === TRUE) {
                    $showSuccessModal = true; // Mostrar modal de éxito
                } else {
                    echo "Error actualizando traslado: " . $conn->error;
                }
            } else {
                // Insertar nuevo traslado
                $sqlInsert = "INSERT INTO traslado (cantidad, fecha, idproducto, idsucursalorigen, idsucursaldestino) 
                              VALUES ('$cantidad', '$fecha', '$idProducto', '$idSucursalOrigen', '$idSucursalDestino')";
                if ($conn->query($sqlInsert) === TRUE) {
                    $showSuccessModal = true; // Mostrar modal de éxito
                } else {
                    echo "Error: " . $sqlInsert . "<br>" . $conn->error;
                }
            }
        } else {
            echo "Error: La sucursal de origen no tiene suficiente cantidad del producto.";
        }
    } else {
        echo "Error: La sucursal de origen no tiene ese producto en inventario.";
    }
}

// Modo edición: seleccionar traslado para editar
if (isset($_GET['edit'])) {
    $idTrasladoEdit = $_GET['edit'];

    $sqlEdit = "SELECT * FROM traslado WHERE idtraslado='$idTrasladoEdit'";
    $resultEdit = $conn->query($sqlEdit);

    if ($resultEdit->num_rows > 0) {
        $row = $resultEdit->fetch_assoc();
        $cantidadEdit = $row['cantidad'];
        $fechaEdit = $row['fecha'];
        $idProductoEdit = $row['idproducto'];
        $idSucursalOrigenEdit = $row['idsucursalorigen'];
        $idSucursalDestinoEdit = $row['idsucursaldestino'];
        $editMode = true;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Traslados</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1>Gestión de Traslados</h1>

        <!-- Formulario de ingreso/modificación -->
        <form action="" method="POST" class="mb-5">
            <?php if ($editMode): ?>
                <input type="hidden" name="idtraslado" value="<?php echo $idTrasladoEdit; ?>">
            <?php endif; ?>
            <a href="..\Vista\indexAdministrador.php" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Regresar</a>

            <div class="form-group">
                <label for="cantidad">Cantidad:</label>
                <input type="number" class="form-control" id="cantidad" name="cantidad" value="<?php echo $cantidadEdit; ?>" required>
            </div>

            <div class="form-group">
                <label for="fecha">Fecha:</label>
                <input type="datetime-local" class="form-control" id="fecha" name="fecha" value="<?php echo $fechaEdit; ?>" required>
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
                <label for="sucursal_origen">Sucursal de Origen:</label>
                <select class="form-control" id="sucursal_origen" name="sucursal_origen" required>
                    <option value="">Seleccione una sucursal de origen</option>
                    <?php
                    $sqlSucursal = "SELECT idsucursal, nombresucursal FROM sucursal";
                    $resultSucursal = $conn->query($sqlSucursal);
                    while($row = $resultSucursal->fetch_assoc()) {
                        $selected = ($row['idsucursal'] == $idSucursalOrigenEdit) ? 'selected' : '';
                        echo "<option value='" . $row['idsucursal'] . "' $selected>" . $row['nombresucursal'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="sucursal_destino">Sucursal de Destino:</label>
                <select class="form-control" id="sucursal_destino" name="sucursal_destino" required>
                    <option value="">Seleccione una sucursal de destino</option>
                    <?php
                    $sqlSucursal = "SELECT idsucursal, nombresucursal FROM sucursal";
                    $resultSucursal = $conn->query($sqlSucursal);
                    while($row = $resultSucursal->fetch_assoc()) {
                        $selected = ($row['idsucursal'] == $idSucursalDestinoEdit) ? 'selected' : '';
                        echo "<option value='" . $row['idsucursal'] . "' $selected>" . $row['nombresucursal'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary"><?php echo $editMode ? 'Actualizar Traslado' : 'Crear Traslado'; ?></button>
        </form>

        <!-- Listado de traslados -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Cantidad</th>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th>Sucursal Origen</th>
                    <th>Sucursal Destino</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sqlTraslado = "SELECT t.idtraslado, t.cantidad, t.fecha, p.nombre AS producto, so.nombresucursal AS sucursal_origen, sd.nombresucursal AS sucursal_destino
                                FROM traslado t
                                JOIN producto p ON t.idproducto = p.idproducto
                                JOIN sucursal so ON t.idsucursalorigen = so.idsucursal
                                JOIN sucursal sd ON t.idsucursaldestino = sd.idsucursal";
                $resultTraslado = $conn->query($sqlTraslado);

                if ($resultTraslado->num_rows > 0) {
                    while ($row = $resultTraslado->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['cantidad'] . "</td>";
                        echo "<td>" . $row['fecha'] . "</td>";
                        echo "<td>" . $row['producto'] . "</td>";
                        echo "<td>" . $row['sucursal_origen'] . "</td>";
                        echo "<td>" . $row['sucursal_destino'] . "</td>";
                        echo "<td>
                                <a href='?finalizar=" . $row['idtraslado'] . "' class='btn btn-success'>Finalizar Traslado</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No se encontraron traslados</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal de éxito -->
    <?php if ($showSuccessModal): ?>
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
                    Operación realizada con éxito.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#successModal').modal('show');
        });
    </script>
    <?php endif; ?>

    <!-- Modal de error -->
    <?php if ($showErrorModal): ?>
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php echo $errorMessage; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#errorModal').modal('show');
        });
    </script>
    <?php endif; ?>

</body>
</html>
