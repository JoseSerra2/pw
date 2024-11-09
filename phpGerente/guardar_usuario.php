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

$usuarioGuardadoExitosamente = false;
$usuarioModificadoExitosamente = false;

// Variables para el formulario
$nombreusuario = "";
$contraseñausuario = "";
$estado = 1; // Por defecto activo
$idrol = null; // Rol seleccionado
$idusuario = "";

// Obtener roles para el dropdown
$sql_roles = "SELECT * FROM rol";
$result_roles = $conn->query($sql_roles);

// Comprobar si hay un usuario que se desea editar
if (isset($_GET['editar'])) {
    $idusuario = $_GET['editar'];
    $sql = "SELECT * FROM usuario WHERE idusuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idusuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();

    // Pre-rellenar el formulario con el usuario seleccionado
    $nombreusuario = $usuario['nombreusuario'];
    // Aquí no se pre-rellena la contraseña por seguridad
    $estado = $usuario['estado'];
    $idrol = $usuario['idrol'];
}

// Guardar o actualizar un usuario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombreusuario = $_POST['nombreusuario'];
    $contraseñausuario = $_POST['contraseñausuario'];
    $estado = isset($_POST['estado']) ? 1 : 0;
    $idrol = $_POST['idrol'];

    // Cifrar la contraseña antes de guardarla
    $contraseñausuario_cifrada = password_hash($contraseñausuario, PASSWORD_DEFAULT);

    if (isset($_POST['idusuario']) && $_POST['idusuario'] != "") {
        // Actualizar usuario
        $idusuario = $_POST['idusuario'];
        $sql = "UPDATE usuario SET nombreusuario = ?, contraseñausuario = ?, estado = ?, idrol = ? WHERE idusuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiii", $nombreusuario, $contraseñausuario_cifrada, $estado, $idrol, $idusuario);
        if ($stmt->execute()) {
            $usuarioModificadoExitosamente = true;
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        // Insertar nuevo usuario
        $sql = "INSERT INTO usuario (nombreusuario, contraseñausuario, estado, idrol) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $nombreusuario, $contraseñausuario_cifrada, $estado, $idrol);
        if ($stmt->execute()) {
            $usuarioGuardadoExitosamente = true;
            // Limpiar campos
            $nombreusuario = "";
            $contraseñausuario = "";
            $idrol = null;
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    // Limpiar $idusuario para evitar que quede en modo edición
    $idusuario = ""; 
    $stmt->close();
}

// Consultar los usuarios ya existentes en la tabla `usuario`, excluyendo el rol de cliente
$sql = "SELECT u.*, r.nombrerol FROM usuario u 
        LEFT JOIN rol r ON u.idrol = r.idrol 
        WHERE u.idrol != ?"; // Excluyendo el rol de cliente
$idRolCliente = 3; // Cambia este valor al ID del rol "cliente"
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idRolCliente);
$stmt->execute();
$result = $stmt->get_result();
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuario</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    
    <div class="container">
        <h3><?php echo isset($idusuario) && $idusuario != "" ? "Modificar Usuario" : "Registrar Nuevo Usuario"; ?></h3>
        <a href="..\Vista\indexGerente.php" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Regresar</a>
        <form action="" method="POST">
            <div class="form-group">
                <label for="nombreusuario">Nombre del Usuario</label>
                <input type="text" class="form-control" id="nombreusuario" name="nombreusuario" value="<?php echo $nombreusuario; ?>" placeholder="Ingrese el nombre de usuario" required>
            </div>
            <div class="form-group">
                <label for="contraseñausuario">Contraseña</label>
                <input type="password" class="form-control" id="contraseñausuario" name="contraseñausuario" value="<?php echo $contraseñausuario; ?>" placeholder="Ingrese la contraseña" required>
            </div>
            <div class="form-group">
                <label for="idrol">Rol</label>
                <select class="form-control" id="idrol" name="idrol" required>
                    <option value="">Seleccione un rol</option>
                    <?php
                    // Poblar el dropdown con los roles
                    while ($row = $result_roles->fetch_assoc()) {
                        $selected = $idrol == $row['idrol'] ? "selected" : "";
                        echo "<option value='" . $row['idrol'] . "' $selected>" . $row['nombrerol'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="estado" name="estado" <?php echo $estado ? "checked" : ""; ?>>
                <label class="form-check-label" for="estado">Activo</label>
            </div>
            <input type="hidden" name="idusuario" value="<?php echo $idusuario; ?>">
            <button type="submit" class="btn btn-primary"><?php echo isset($idusuario) && $idusuario != "" ? "Modificar Usuario" : "Guardar Usuario"; ?></button>
            <a href="" class="btn btn-secondary">Nuevo Usuario</a>
        </form>

        <!-- Mostrar los usuarios guardados en una tabla -->
        <h3>Usuarios Guardados</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Usuario</th>
                    <th>Nombre del Usuario</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Verificar si hay resultados
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $estadoUsuario = $row['estado'] ? "Activo" : "Inactivo";
                        echo "<tr>
                                <td>" . $row['idusuario'] . "</td>
                                <td>" . $row['nombreusuario'] . "</td>
                                <td>" . $row['nombrerol'] . "</td>
                                <td>" . $estadoUsuario . "</td>
                                <td>
                                    <a href='?editar=" . $row['idusuario'] . "' class='btn btn-warning btn-sm'>Editar</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No hay usuarios guardados.</td></tr>";
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
            <h5 class="modal-title" id="modalExitoLabel"><?php echo $usuarioModificadoExitosamente ? "Usuario Modificado" : "Usuario Guardado"; ?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            ¡<?php echo $usuarioModificadoExitosamente ? "El usuario ha sido modificado exitosamente" : "Se ha guardado el usuario exitosamente"; ?>!
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
        // Mostrar el modal si el usuario fue guardado o modificado
        <?php if ($usuarioGuardadoExitosamente || $usuarioModificadoExitosamente): ?>
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
