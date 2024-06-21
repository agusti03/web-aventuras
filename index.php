<?php
include("header.php");
include("basedatos.php");
include("BarraNavegacion.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Web Aventuras</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/Style.css">
</head>

<body>
    <section class="publicaciones">
        <div>
            <ul>
                <?php
                    $sql = "SELECT publicacion.*, embarcacion.valor AS precio, embarcacion.anio AS antiguedad, embarcacion.Tipo AS tipo, embarcacion.marca 
                        FROM publicacion 
                        JOIN embarcacion ON publicacion.embarcacionID = embarcacion.id";

                    // Filtrar publicaciones según los criterios seleccionados
                    if (isset($_GET['criterio'])) {
                        $criterio = $_GET['criterio'];
                        switch ($criterio) {
                            case 'precio_asc':
                                $sql .= " ORDER BY embarcacion.valor ASC";
                                break;
                            case 'precio_desc':
                                $sql .= " ORDER BY embarcacion.valor DESC";
                                break;
                            case 'antiguedad_asc':
                                $sql .= " ORDER BY embarcacion.anio ASC";
                                break;
                            case 'antiguedad_desc':
                                $sql .= " ORDER BY embarcacion.anio DESC";
                                break;
                            case 'tipo':
                                if (isset($_GET['tipo'])) {
                                    $tipo = $conn->real_escape_string($_GET['tipo']);
                                    $sql .= " WHERE embarcacion.Tipo = '$tipo'";
                                }
                                break;
                            case 'marca':
                                if (isset($_GET['marca'])) {
                                    $marca = $conn->real_escape_string($_GET['marca']);
                                    $sql .= " WHERE embarcacion.marca = '$marca'";
                                }
                                break;
                        }
                    }

                    $resultado = mysqli_query($conn, $sql);

                    // Comprobar si hay resultados
                    if (mysqli_num_rows($resultado) > 0) {
                        // Recorrer los resultados y mostrar cada publicación
                        while ($row = mysqli_fetch_assoc($resultado)) {
                            echo '<li><a href="visualizar_publicacion.php?id=' . $row["id"] . '">' . $row["Titulo"] . '</a></li>';
                        }
                    } else {
                        echo '<li>No hay publicaciones disponibles.</li>';
                    }

                    // Liberar el resultado
                    mysqli_free_result($resultado);

                    // Cerrar la conexión a la base de datos
                    mysqli_close($conn);
                ?>
            </ul>
        </div>
    </section>
    <?php include("formulario_filtro.php"); ?>  <!-- Formulario de filtrado -->
    <footer>
    </footer>
</body>

</html>
