<?php include 'session.php'; ?>
<?php include '../Procesos/Tablas.php'; ?>

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
                                    <a href="..\phpAdmin/guardar_categoria.php" class="dropdown-item">Registrar Categoria</a>
                                    <a href="..\phpAdmin/guardar_producto.php" class="dropdown-item">Registrar Producto</a>
                                    <a href="..\phpAdmin/guardar_promocion.php" class="dropdown-item">Registrar Promocion</a>
                                    <a href="..\phpAdmin/guardar_inventario.php" class="dropdown-item">Inventario</a>
                                    <a href="..\phpAdmin/Traslado.php" class="dropdown-item">Traslado</a>
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


