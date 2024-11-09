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

$sucursalGuardadaExitosamente = false;
$sucursalModificadaExitosamente = false;

// Variables para el formulario
$nombresucursal = "";
$direccion = "";
$trial_gps_latitud_4 = "";
$gps_longitud = "";
$telefono = "";
$idciudad = "";
$idsucursal = "";

// Consultar ciudades para el desplegable
$sqlCiudades = "SELECT * FROM ciudad";
$resultCiudades = $conn->query($sqlCiudades);

// Comprobar si hay una sucursal que se desea editar
if (isset($_GET['editar'])) {
    $idsucursal = $_GET['editar'];
    $sql = "SELECT * FROM sucursal WHERE idsucursal = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idsucursal);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $sucursal = $resultado->fetch_assoc();

    // Pre-rellenar el formulario con la sucursal seleccionada
    $nombresucursal = $sucursal['nombresucursal'];
    $direccion = $sucursal['direccion'];
    $trial_gps_latitud_4 = $sucursal['trial_gps_latitud_4'];
    $gps_longitud = $sucursal['gps_longitud'];
    $telefono = $sucursal['telefono'];
    $idciudad = $sucursal['idciudad'];
}

// Guardar o actualizar una sucursal
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombresucursal = $_POST['nombresucursal'];
    $direccion = $_POST['direccion'];
    $trial_gps_latitud_4 = $_POST['trial_gps_latitud_4'];
    $gps_longitud = $_POST['gps_longitud'];
    $telefono = $_POST['telefono'];
    $idciudad = $_POST['idciudad'];

    if (isset($_POST['idsucursal']) && $_POST['idsucursal'] != "") {
        // Actualizar sucursal
        $idsucursal = $_POST['idsucursal'];
        $sql = "UPDATE sucursal SET nombresucursal = ?, direccion = ?, trial_gps_latitud_4 = ?, gps_longitud = ?, telefono = ?, idciudad = ? WHERE idsucursal = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssddssi", $nombresucursal, $direccion, $trial_gps_latitud_4, $gps_longitud, $telefono, $idciudad, $idsucursal);
        if ($stmt->execute()) {
            $sucursalModificadaExitosamente = true;
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        // Insertar nueva sucursal
        $sql = "INSERT INTO sucursal (nombresucursal, direccion, gps_latitud, gps_longitud, telefono, idciudad) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssddsi", $nombresucursal, $direccion, $trial_gps_latitud_4, $gps_longitud, $telefono, $idciudad);
        if ($stmt->execute()) {
            $sucursalGuardadaExitosamente = true;
            // Limpiar campos
            $nombresucursal = "";
            $direccion = "";
            $trial_gps_latitud_4 = "";
            $gps_longitud = "";
            $telefono = "";
            $idciudad = "";
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    // Limpiar $idsucursal para evitar que quede en modo edición
    $idsucursal = ""; 
    $stmt->close();
}

// Consultar las sucursales ya existentes en la tabla `sucursal`
$sql = "SELECT s.*, c.nombreciudad FROM sucursal s LEFT JOIN ciudad c ON s.idciudad = c.idciudad";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Sucursal</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 400px; /* Altura del mapa */
        }
    </style>
</head>
<body>
    <div class="container">
        <h3><?php echo isset($idsucursal) && $idsucursal != "" ? "Modificar Sucursal" : "Registrar Nueva Sucursal"; ?></h3>
        <a href="..\Vista\indexGerente.php" class="btn btn-primary" style="position: absolute; top: 10px; right: 10px;">Regresar</a>
        <form id="formSucursal" action="" method="POST">
            <div class="form-group">
                <label for="nombresucursal">Nombre de la Sucursal</label>
                <input type="text" class="form-control" id="nombresucursal" name="nombresucursal" value="<?php echo $nombresucursal; ?>" placeholder="Ingrese el nombre de la sucursal" required>
            </div>
            <div class="form-group">
                <label for="direccion">Dirección</label>
                <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo $direccion; ?>" placeholder="Ingrese la dirección" required>
                <button type="button" id="btnBuscar" class="btn btn-info mt-2">Buscar en el Mapa</button>
            </div>
            <div class="form-group">
                <label for="trial_gps_latitud_4">Latitud GPS</label>
                <input type="text" class="form-control" id="trial_gps_latitud_4" name="trial_gps_latitud_4" value="<?php echo $trial_gps_latitud_4; ?>" placeholder="Ingrese la latitud GPS" required readonly>
            </div>
            <div class="form-group">
                <label for="gps_longitud">Longitud GPS</label>
                <input type="text" class="form-control" id="gps_longitud" name="gps_longitud" value="<?php echo $gps_longitud; ?>" placeholder="Ingrese la longitud GPS" required readonly>
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $telefono; ?>" placeholder="Ingrese el teléfono" required>
            </div>
            <div class="form-group">
                <label for="idciudad">Ciudad</label>
                <select class="form-control" id="idciudad" name="idciudad" required>
                    <option value="">Seleccione una ciudad</option>
                    <?php
                    // Mostrar opciones de ciudades en el menú desplegable
                    if ($resultCiudades->num_rows > 0) {
                        while ($ciudad = $resultCiudades->fetch_assoc()) {
                            echo "<option value='" . $ciudad['idciudad'] . "'" . ($idciudad == $ciudad['idciudad'] ? " selected" : "") . ">" . $ciudad['nombreciudad'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <!-- Campos ocultos para latitud y longitud -->
            <input type="hidden" name="idsucursal" value="<?php echo $idsucursal; ?>">
            <button type="submit" class="btn btn-primary"><?php echo isset($idsucursal) && $idsucursal != "" ? "Modificar Sucursal" : "Guardar Sucursal"; ?></button>
            <a href="" class="btn btn-secondary">Nueva Sucursal</a>
        </form>

        <!-- Mapa para seleccionar latitud y longitud -->
        <div id="map"></div>

        <!-- Mostrar las sucursales guardadas en una tabla -->
        <h3>Sucursales Guardadas</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Sucursal</th>
                    <th>Nombre de la Sucursal</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Ciudad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Verificar si hay resultados
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row['idsucursal'] . "</td>
                                <td>" . $row['nombresucursal'] . "</td>
                                <td>" . $row['direccion'] . "</td>
                                <td>" . $row['telefono'] . "</td>
                                <td>" . $row['nombreciudad'] . "</td>
                                <td>
                                    <a href='?editar=" . $row['idsucursal'] . "' class='btn btn-warning btn-sm'>Editar</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No hay sucursales guardadas.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Inicializar el mapa
        var map = L.map('map').setView([0, 0], 2); // Vista inicial (global)

        // Añadir capa de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Marker para la sucursal
        var marker = L.marker([0, 0]).addTo(map);

        // Evento de clic en el mapa para obtener la latitud y longitud
        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;

            // Actualizar el marcador y los campos ocultos
            marker.setLatLng([lat, lng]);
            document.getElementById('trial_gps_latitud_4').value = lat;
            document.getElementById('gps_longitud').value = lng;
        });

        // Función para buscar la dirección
        document.getElementById('btnBuscar').onclick = function() {
            var direccion = document.getElementById('direccion').value;
            if (direccion) {
                // Usar la API de Nominatim para obtener las coordenadas
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(direccion)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            var lat = data[0].lat;
                            var lng = data[0].lon;

                            // Mover el mapa a las nuevas coordenadas
                            map.setView([lat, lng], 13);
                            marker.setLatLng([lat, lng]);
                            document.getElementById('trial_gps_latitud_4').value = lat;
                            document.getElementById('gps_longitud').value = lng;
                        } else {
                            alert('No se encontró la dirección. Inténtalo de nuevo.');
                        }
                    })
                    .catch(error => {
                        console.error('Error al buscar la dirección:', error);
                        alert('Error al buscar la dirección. Inténtalo de nuevo.');
                    });
            } else {
                alert('Por favor, ingresa una dirección.');
            }
        };
    </script>
</body>
</html>

<?php
// Cerrar conexión
$conn->close();
?>
