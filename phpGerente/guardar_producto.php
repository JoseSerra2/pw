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

$productoGuardadoExitosamente = false;
$productoModificadoExitosamente = false;

// Variables para el formulario
$nombre = "";
$descripcion = "";
$precio = "";
$foto = "";
$estado = "";
$idcategoria = "";
$idproducto = "";

// Consultar categorías para el desplegable
$sqlCategorias = "SELECT * FROM categoria";
$resultCategorias = $conn->query($sqlCategorias);

// Comprobar si hay un producto que se desea editar
if (isset($_GET['editar'])) {
    $idproducto = $_GET['editar'];
    $sql = "SELECT * FROM producto WHERE idproducto = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idproducto);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $producto = $resultado->fetch_assoc();

    // Pre-rellenar el formulario con el producto seleccionado
    $nombre = $producto['nombre'];
    $descripcion = $producto['descripcion'];
    $precio = $producto['precio'];
    $estado = $producto['estado'];
    $idcategoria = $producto['idcategoria'];
}

// Guardar o actualizar un producto
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    // Define estado como 1 si el checkbox está marcado; de lo contrario, 0
    $estado = isset($_POST['estado']) ? 1 : 0;
    $idcategoria = $_POST['idcategoria'];

    // Procesar la foto del producto si se ha subido
    $foto = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['tmp_name'] != "") {
        $foto = file_get_contents($_FILES['foto']['tmp_name']);
    }

    if (isset($_POST['idproducto']) && $_POST['idproducto'] != "") {
        // Actualizar producto
        $idproducto = $_POST['idproducto'];

        if ($foto !== null) {
            $sql = "UPDATE producto SET nombre = ?, descripcion = ?, precio = ?, foto = ?, estado = ?, idcategoria = ? WHERE idproducto = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdsiii", $nombre, $descripcion, $precio, $foto, $estado, $idcategoria, $idproducto);
        } else {
            // Si no se actualiza la foto, omitir ese campo
            $sql = "UPDATE producto SET nombre = ?, descripcion = ?, precio = ?, estado = ?, idcategoria = ? WHERE idproducto = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdiis", $nombre, $descripcion, $precio, $estado, $idcategoria, $idproducto);
        }

        if ($stmt->execute()) {
            $productoModificadoExitosamente = true;
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        // Insertar nuevo producto
        $sql = "INSERT INTO producto (nombre, descripcion, precio, foto, estado, idcategoria) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsii", $nombre, $descripcion, $precio, $foto, $estado, $idcategoria);

        if ($stmt->execute()) {
            $productoGuardadoExitosamente = true;
            // Limpiar campos
            $nombre = "";
            $descripcion = "";
            $precio = "";
            $estado = 0; // Resetear a 0
            $idcategoria = "";
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    // Limpiar $idproducto para evitar que quede en modo edición
    $idproducto = ""; 
    $stmt->close();
}
// Consultar los productos ya existentes en la tabla `producto`
$sql = "SELECT p.*, c.nombre AS nombrecategoria FROM producto p LEFT JOIN categoria c ON p.idcategoria = c.idcategoria";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Producto</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h3><?php echo isset($idproducto) && $idproducto != "" ? "Modificar Producto" : "Registrar Nuevo Producto"; ?></h3>
        <a href="..\Vista\indexGerente.php" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Regresar</a>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre">Nombre del Producto</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $nombre; ?>" placeholder="Ingrese el nombre del producto" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" placeholder="Ingrese la descripción del producto" required><?php echo $descripcion; ?></textarea>
            </div>
            <div class="form-group">
                <label for="precio">Precio</label>
                <input type="number" step="0.01" class="form-control" id="precio" name="precio" value="<?php echo $precio; ?>" placeholder="Ingrese el precio del producto" required>
            </div>
            <div class="form-group">
                <label for="foto">Foto del Producto</label>
                <input type="file" class="form-control" id="foto" name="foto" required>
            </div>
            <div class="form-group">
                <label for="estado">Estado</label>
                <input type="checkbox" id="estado" name="estado" <?php echo $estado ? "checked" : ""; ?>>
                <label for="estado">Activo</label>
            </div>
            <div class="form-group">
                <label for="idcategoria">Categoría</label>
                <select class="form-control" id="idcategoria" name="idcategoria" required>
                    <option value="">Seleccione una categoría</option>
                    <?php
                    // Mostrar opciones de categorías en el menú desplegable
                    if ($resultCategorias->num_rows > 0) {
                        while ($categoria = $resultCategorias->fetch_assoc()) {
                            echo "<option value='" . $categoria['idcategoria'] . "'" . ($idcategoria == $categoria['idcategoria'] ? " selected" : "") . ">" . $categoria['nombre'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <input type="hidden" name="idproducto" value="<?php echo $idproducto; ?>">
            <button type="submit" class="btn btn-primary"><?php echo isset($idproducto) && $idproducto != "" ? "Modificar Producto" : "Guardar Producto"; ?></button>
            <a href="" class="btn btn-secondary">Nuevo Producto</a>
        </form>

        <!-- Mostrar los productos guardados en una tabla -->
        <h3>Productos Guardados</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Producto</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Foto</th>
                    <th>Categoría</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Verificar si hay resultados
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row['idproducto'] . "</td>
                                <td>" . $row['nombre'] . "</td>
                                <td>" . $row['descripcion'] . "</td>
                                <td>" . $row['precio'] . "</td>
                                <td><img src='data:image/jpeg;base64," . base64_encode($row['foto']) . "' alt='Foto' style='width: 50px; height: 50px;'/></td>
                                <td>" . $row['nombrecategoria'] . "</td>
                                <td>
                                    <a href='?editar=" . $row['idproducto'] . "' class='btn btn-warning btn-sm'>Editar</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No hay productos guardados.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>


<?php
// Cerrar conexión
$conn->close();
?>
