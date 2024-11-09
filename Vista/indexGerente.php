<?php include 'session.php'; ?>
<?php include '../Procesos/Tablas.php'; ?>
<?php
// Función para realizar el backup de la base de datos y enviarlo como descarga
if (!function_exists('backupDatabase')) {
    function backupDatabase($servername, $username, $password, $dbname, $conn) {
        // Nombre del archivo de backup
        $backupFile = $dbname . '_' . date('Y-m-d_H-i-s') . '.sql';

        // Encabezados para forzar la descarga
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $backupFile . '"');
        header('Content-Transfer-Encoding: binary');

        // Abrir salida para escribir el archivo de backup
        $output = fopen('php://output', 'w');
        if (!$output) {
            die("No se pudo abrir la salida para escritura.");
        }

        // Escribir el encabezado SQL para crear la base de datos
        fwrite($output, "CREATE DATABASE IF NOT EXISTS `$dbname`;\nUSE `$dbname`;\n\n");

        // Obtener las tablas de la base de datos
        $tablesResult = $conn->query("SHOW TABLES");
        while ($table = $tablesResult->fetch_row()) {
            $tableName = $table[0];

            // Obtener la estructura de la tabla
            $createTableResult = $conn->query("SHOW CREATE TABLE `$tableName`");
            $createTableRow = $createTableResult->fetch_row();
            fwrite($output, $createTableRow[1] . ";\n\n");

            // Obtener los datos de la tabla
            $dataResult = $conn->query("SELECT * FROM `$tableName`");
            if ($dataResult->num_rows > 0) {
                while ($row = $dataResult->fetch_assoc()) {
                    $columns = array_keys($row);
                    $values = array_values($row);

                    // Escapar los valores para evitar problemas con comillas
                    $escapedValues = array_map(function($value) use ($conn) {
                        return "'" . $conn->real_escape_string($value) . "'";
                    }, $values);

                    // Escribir la inserción de datos en el archivo
                    $insertQuery = "INSERT INTO `$tableName` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $escapedValues) . ");\n";
                    fwrite($output, $insertQuery);
                }
            } else {
                fwrite($output, "-- No hay datos en la tabla $tableName.\n\n");
            }

            fwrite($output, "\n");
        }

        fclose($output);
    }
}

// Verificar si es el 15 del mes y realizar el backup si es así
if (date('d') == '15') {
    // Iniciar la función de backup
    backupDatabase($servername, $username, $password, $dbname, $conn);
}
?>



<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Modulo de Empleados</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="Bootstrap Ecommerce Template" name="keywords">
        <meta content="Bootstrap Ecommerce Template Free Download" name="description">

        <!-- Favicon -->
        <link href="..\img/favicon.ico" rel="icon">

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700&display=swap" rel="stylesheet">

        <!-- CSS Libraries -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <link href="..\lib/slick/slick.css" rel="stylesheet">
        <link href="..\lib/slick/slick-theme.css" rel="stylesheet">

        <!-- Biblioteca para Graficas -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- Template Stylesheet -->
        <link href="..\css/style.css" rel="stylesheet">
    </head>

    <body>
        <!-- Top Header Start -->
        <div class="top-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <div class="logo">
                            <a href="">
                                <img src="..\img/logo.png" alt="Logo">
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                    </div>
                    <div class="col-md-3">
                        <div class="user">
                            <div class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Opciones</a>
                                <div class="dropdown-menu">
                                    <a href="..\phpGerente/guardar_usuario.php" class="dropdown-item">Registrar Usuario</a>
                                    <a href="..\phpGerente/guardar_rol.php" class="dropdown-item">Registrar Rol</a>
                                    <a href="..\phpGerente/guardar_categoria.php" class="dropdown-item">Registrar Categoria</a>
                                    <a href="..\phpGerente/guardar_departamento.php" class="dropdown-item">Registrar Departamento</a>
                                    <a href="..\phpGerente/guardar_ciudad.php" class="dropdown-item">Registrar Ciudad</a>
                                    <a href="..\phpGerente/guardar_metododepago.php" class="dropdown-item">Registrar Metodo de pago</a>
                                    <a href="..\phpGerente/guardar_sucursal.php" class="dropdown-item">Registrar Sucursal</a>
                                    <a href="..\phpGerente/guardar_producto.php" class="dropdown-item">Registrar Producto</a>
                                    <a href="..\phpGerente/guardar_promocion.php" class="dropdown-item">Registrar Promocion</a>
                                    <a href="..\phpGerente/guardar_inventario.php" class="dropdown-item">Inventario</a>
                                    <a href="..\phpGerente/Traslado.php" class="dropdown-item">Traslado</a>
                                    <a href="..\phpGerente/Historial.php" class="dropdown-item">Historial</a>
                                </div>
                            </div>
                            <div class="cart">
                                <i class="fa fa-cart-plus"></i>
                                <span>(0)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Top Header End -->
        
        
        <!-- Header Start -->
        <div class="header">
            <div class="container">
                <nav class="navbar navbar-expand-md bg-dark navbar-dark">
                    <a href="#" class="navbar-brand">MENU</a>
                    <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                        <div class="navbar-nav m-auto">
                            <a href="..\index.php" class="nav-item nav-link active">INICIO</a>
                            <a href="product-list.php" class="nav-item nav-link">PRODUCTOS</a>
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">PAGINAS</a>
                                <div class="dropdown-menu">
                                    <a href="product-list.php" class="dropdown-item">Producto</a>
                                    <a href="cart.php" class="dropdown-item">Carrito</a>
                                    <a href="login.php" class="dropdown-item">Iniciar o Registrar Sesion</a>
                                    <a href="my-account.php" class="dropdown-item">Mi Cuenta</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
        <!-- Header End -->

        <div class="container mt-4">
            <div class="row">
                <!-- Gráfico de los productos más vendidos (General) -->
                <div class="col-md-6 mb-4">
                    <h2>Top 100 Productos Más Vendidos (General)</h2>
                    <canvas id="chartGeneral"></canvas>
                </div>

                <!-- Gráfico de los productos más vendidos por sucursal -->
                <div class="col-md-6 mb-4">
                    <h2>Top 100 Productos Más Vendidos por Sucursal</h2>
                    <canvas id="chartSucursal"></canvas>
                </div>
                <!-- Gráfico de los productos con existencia menor a 10 unidades -->
                <div class="col-md-6 mb-4">
                    <h2>Top 20 Productos con Existencia Menor a 10 Unidades</h2>
                    <canvas id="chartBajaExistencia"></canvas>
                </div>
                <div class="col-md-6 mb-4">
                    <h2>Productos Más Vendidos por Mes</h2>
                    <canvas id="chartMes"></canvas>
                </div>
            </div>
        </div>

        <?php include '../Procesos/Reporte1.php'; ?>

        <div class="container mt-4">
            <h2>Generar Reporte de Productos Más Vendidos</h2>
            <form id="reporteForm" action="" method="POST">
                <div class="form-group">
                    <label for="tipoReporte">Tipo de Reporte:</label>
                    <select class="form-control" id="tipoReporte" name="tipoReporte">
                        <option value="general" <?= $tipoReporte === 'general' ? 'selected' : '' ?>>General</option>
                        <option value="mes" <?= $tipoReporte === 'mes' ? 'selected' : '' ?>>Por Mes</option>
                        <option value="sucursal" <?= $tipoReporte === 'sucursal' ? 'selected' : '' ?>>Por Sucursal</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="mes">Mes:</label>
                    <input type="month" class="form-control" id="mes" name="mes" value="<?= isset($_POST['mes']) ? $_POST['mes'] : date('Y-m') ?>">
                </div>

                <div class="form-group">
                    <label for="sucursal">Sucursal:</label>
                    <select class="form-control" id="sucursal" name="sucursal">
                        <option value="">Todas las Sucursales</option>
                        <?php foreach ($sucursales as $sucursal): ?>
                            <option value="<?= $sucursal['idsucursal'] ?>" <?= isset($_POST['sucursal']) && $_POST['sucursal'] == $sucursal['idsucursal'] ? 'selected' : '' ?>>
                                <?= $sucursal['nombresucursal'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Generar Reporte</button>
            </form>

            <!-- Resultados del Reporte -->
            <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
                <div class="mt-4">
                    <h3>Resultados del Reporte</h3>
                    <?php if ($reporteGenerado): ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Año</th>
                                    <th>Mes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['producto'] ?></td>
                                        <td><?= $row['año'] ?></td>
                                        <td><?= $row['mes'] ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No se encontraron resultados.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php include '../Procesos/Reporte2.php'; ?>

        <div class="container mt-4">
            <h2>Generar Reporte de Clientes que Compran Seguido</h2>
            <form id="reporteForm" action="" method="POST">
                <div class="form-group">
                    <label for="tipoReporte">Tipo de Reporte:</label>
                    <select class="form-control" id="tipoReporte" name="tipoReporte"> <!-- Aseguramos que el nombre del campo coincida -->
                        <option value="general" <?= $tipoReporte === 'general' ? 'selected' : '' ?>>General</option>
                        <option value="sucursal" <?= $tipoReporte === 'sucursal' ? 'selected' : '' ?>>Por Sucursal</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="sucursal">Sucursal:</label>
                    <select class="form-control" id="sucursal" name="sucursal">
                        <option value="">Todas las Sucursales</option>
                        <?php foreach ($sucursales as $sucursal): ?>
                            <option value="<?= $sucursal['idsucursal'] ?>" <?= isset($_POST['sucursal']) && $_POST['sucursal'] == $sucursal['idsucursal'] ? 'selected' : '' ?>>
                                <?= $sucursal['nombresucursal'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Generar Reporte</button>
            </form>

            <!-- Resultados del Reporte -->
            <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
                <div class="mt-4">
                    <h3>Resultados del Reporte</h3>
                    <?php if ($reporteGenerado): ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Compras Realizadas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['cliente'] ?></td>
                                        <td><?= $row['compras'] ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No se encontraron resultados.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php include '../Procesos/Reporte3.php'; ?>

        <div class="container mt-4">
            <h2>Generar Reporte de Compras por Rango de Fecha</h2>
            <form id="reporteForm" action="" method="POST">
                <div class="form-group">
                    <label for="sucursal">Sucursal:</label>
                    <select class="form-control" id="sucursal" name="sucursal">
                        <option value="">Todas las Sucursales</option>
                        <?php foreach ($sucursales as $sucursal): ?>
                            <option value="<?= $sucursal['idsucursal'] ?>" <?= isset($_POST['sucursal']) && $_POST['sucursal'] == $sucursal['idsucursal'] ? 'selected' : '' ?>>
                                <?= $sucursal['nombresucursal'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="fechaInicio">Fecha de Inicio:</label>
                    <input type="date" class="form-control" id="fechaInicio" name="fechaInicio" value="<?= isset($_POST['fechaInicio']) ? $_POST['fechaInicio'] : $fechaHoy ?>">
                </div>

                <div class="form-group">
                    <label for="fechaFin">Fecha de Fin:</label>
                    <input type="date" class="form-control" id="fechaFin" name="fechaFin" value="<?= isset($_POST['fechaFin']) ? $_POST['fechaFin'] : $fechaHoy ?>">
                </div>

                <button type="submit" class="btn btn-primary">Generar Reporte</button>
            </form>

            <!-- Resultados del Reporte -->
            <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
                <div class="mt-4">
                    <h3>Resultados del Reporte</h3>
                    <?php if ($reporteGenerado): ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Acción</th>
                                    <th>Fecha</th>
                                    <th>Sucursal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['accion'] ?></td>
                                        <td><?= $row['fecha'] ?></td>
                                        <td><?= $row['nombresucursal'] ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No se encontraron resultados.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

    <script>
    // Gráfico de los productos más vendidos (general)
        const ctxGeneral = document.getElementById('chartGeneral').getContext('2d');
        new Chart(ctxGeneral, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($productos_general); ?>,
                datasets: [{
                    label: 'Total Vendido',
                    data: <?php echo json_encode($ventas_general); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,  // Esto asegura que el gráfico mantenga su relación de aspecto
                scales: {
                    x: { display: false }, // Oculta etiquetas largas para una mejor visualización
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 10 }
                    }
                }
            }
        });

        // Gráfico de los productos más vendidos por sucursal
        const ctxSucursal = document.getElementById('chartSucursal').getContext('2d');
        new Chart(ctxSucursal, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($productos_sucursal); ?>,
                datasets: [{
                    label: 'Total Vendido',
                    data: <?php echo json_encode($ventas_sucursal); ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,  // Mantener la relación de aspecto
                scales: {
                    x: { display: false }, // Oculta etiquetas largas para una mejor visualización
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 10 }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            // Personalizamos el contenido del tooltip
                            label: function(tooltipItem) {
                                const producto = tooltipItem.label;  // Nombre del producto
                                const cantidad = tooltipItem.raw;    // Total vendido
                                const sucursal = <?php echo json_encode($sucursales); ?>[tooltipItem.dataIndex]; // Nombre de la sucursal
                                return producto + ' - Sucursal: ' + sucursal + ' - Total Vendido: ' + cantidad;
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de los productos con existencia menor a 10 unidades
        const ctxBajaExistencia = document.getElementById('chartBajaExistencia').getContext('2d');
        new Chart(ctxBajaExistencia, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($productos_baja); ?>,
                datasets: [{
                    label: 'Cantidad en Inventario',
                    data: <?php echo json_encode($cantidades_baja); ?>,
                    backgroundColor: 'rgba(255, 159, 64, 0.6)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,  // Mantener la relación de aspecto
                scales: {
                    x: { display: true },  // Mostrar etiquetas de los productos
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });

        // Gráfico de los productos más vendidos por mes
        const ctxMes = document.getElementById('chartMes').getContext('2d');
        new Chart(ctxMes, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($productos_mes); ?>,  // Nombres de los productos
                datasets: [{
                    label: 'Total Vendido',
                    data: <?php echo json_encode($ventas_mes); ?>,     // Cantidad vendida
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,  // Mantener la relación de aspecto
                scales: {
                    x: { 
                        display: true,
                        title: { 
                            display: true,
                            text: 'Productos'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 10
                        },
                        title: {
                            display: true,
                            text: 'Cantidad Vendida'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            // Personalizamos el contenido del tooltip
                            label: function(tooltipItem) {
                                const producto = tooltipItem.label;  // Nombre del producto
                                const cantidad = tooltipItem.raw;    // Total vendido
                                const mes = <?php echo json_encode($meses); ?>[tooltipItem.dataIndex]; // Mes-Año
                                return producto + ' - Mes: ' + mes + ' - Total Vendido: ' + cantidad;
                            }
                        }
                    }
                }
            }
        });
    </script>

        <!-- Back to Top -->
        <a href="#" class="back-to-top"><i class="fa fa-chevron-up"></i></a>

        
        <!-- JavaScript Libraries -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
        <script src="..\lib/easing/easing.min.js"></script>
        <script src="..\lib/slick/slick.min.js"></script>

        
        <!-- Template Javascript -->
        <script src="..\js/main.js"></script>
    </body> 
</html>


