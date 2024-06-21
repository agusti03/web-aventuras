<?php
include("basedatos.php");  // Incluir tu script de conexión a la base de datos
include("BarraNavegacion.php");
include("header.html") ;
include("header.php") ;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Publicaciones de Embarcaciones</title>
</head>

<body>
    <div style="margin-left:15%; padding:20px">
        <h1>Publicaciones de Embarcaciones</h1>
        <ul>
            <?php
            $usuarioID= $_SESSION["Id"];
            if($usuarioID==null){   
                echo '<script>alert("Debes tener una sesion inicializada para poder ver tus publicaciones."); window.location.href = "index.php";</script>';
            }
            // Consulta SQL para seleccionar todas las publicaciones de embarcaciones
            $sql = "SELECT * FROM favorito WHERE usuarioID = $usuarioID";
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
</body>

</html>