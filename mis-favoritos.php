<?php
include ("basedatos.php");  // Incluir tu script de conexión a la base de datos
include ("BarraNavegacion.php");
include ("header.html");
include ("header.php");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Publicaciones de Embarcaciones</title>
</head>

<body>
    <div style="margin-left:15%; padding:20px">
        <h1>Publicaciones de Embarcaciones Favoritas</h1>
        <ul>
            <?php
            $usuarioID = $_SESSION["Id"];
            if ($usuarioID == null) {
                echo '<script>alert("Debes tener una sesión iniciada para ver tus publicaciones favoritas."); window.location.href = "index.php";</script>';
            }

            // Consulta SQL para seleccionar los IDs de las publicaciones favoritas del usuario
            $sql = "SELECT publicacionID FROM favorito WHERE usuarioID = $usuarioID";
            $resultado = mysqli_query($conn, $sql);

            // Comprobar si hay resultados
            if (mysqli_num_rows($resultado) > 0) {
                // Recorrer los resultados y mostrar cada publicación favorita
                while ($row = mysqli_fetch_assoc($resultado)) {
                    $publicacionID = $row["publicacionID"];

                    // Consulta SQL para obtener los detalles de la publicación
                    $sql_detalle = "SELECT * FROM publicacion WHERE id = $publicacionID";
                    $resultado_detalle = mysqli_query($conn, $sql_detalle);

                    // Mostrar los detalles de la publicación favorita
                    if ($fila_detalle = mysqli_fetch_assoc($resultado_detalle)) {
                        echo '<li><a href="visualizar_publicacion.php?id=' . $fila_detalle["id"] . '">' . $fila_detalle["Titulo"] . '</a></li>';
                    }
                }
            } else {
                echo '<li>No tienes publicaciones favoritas.</li>';
            }

            // Liberar el resultado
            mysqli_free_result($resultado);

            // Cerrar la conexión a la base de datos
            mysqli_close($conn);
            ?>
        </ul>
    </div>
</body>

</html>