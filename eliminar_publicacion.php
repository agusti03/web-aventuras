<?php
include("header.php");
include("basedatos.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $publicacionID = $_POST['publicacionID'];
    $usuarioID = $_SESSION["Id"];

    // Verificar si el usuario es el propietario de la publicación
    $sql = "SELECT * FROM publicacion WHERE id = ? AND usuarioID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $publicacionID, $usuarioID);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        echo '<script>alert("No tienes permisos para eliminar esta publicación."); window.location.href = "index.php";</script>';
    } else {
        
        // Obtener los ofertantes de la publicación
        $sql_ofertantes = "SELECT DISTINCT idUsuario FROM oferta WHERE idPublicacion = ?";
        $stmt_ofertantes = $conn->prepare($sql_ofertantes);
        $stmt_ofertantes->bind_param("i", $publicacionID);
        $stmt_ofertantes->execute();
        $result_ofertantes = $stmt_ofertantes->get_result();
        
        $sqlFlag= "SELECT * FROM oferta f INNER JOIN publicacion p ON f.idPublicacion=p.id INNER JOIN embarcacion e ON f.idEmbarcacion=e.id WHERE p.id='$publicacionID'";
        $resultado=mysqli_query($conn,$sqlFlag);
        while($row=mysqli_fetch_assoc($resultado)){
            $patMod=$row["Patente"];
            $query="UPDATE embarcacion SET Ofertado = 0 WHERE Patente = '$patMod'";
            mysqli_query($conn,$query);
        }

        // Eliminar las ofertas asociadas
        $sql_delete_ofertas = "DELETE FROM oferta WHERE idPublicacion = ?";
        $stmt_delete_ofertas = $conn->prepare($sql_delete_ofertas);
        $stmt_delete_ofertas->bind_param("i", $publicacionID);

        $sqlUpdate = "UPDATE embarcacion a INNER JOIN publicacion b ON a.id=b.embarcacionID SET Ofertado= 0 WHERE a.id= b.embarcacionID";
        mysqli_query($conn, $sqlUpdate);

        // Eliminar la publicación
        $sql_delete_publicacion = "DELETE FROM publicacion WHERE id = ? AND usuarioID = ?";
        $stmt_delete_publicacion = $conn->prepare($sql_delete_publicacion);
        $stmt_delete_publicacion->bind_param("ii", $publicacionID, $usuarioID);

        // Ejecutar las consultas
        if ($stmt_delete_ofertas->execute() && $stmt_delete_publicacion->execute()) {
            // Agregar notificaciones para los ofertantes
            while ($row_ofertante = $result_ofertantes->fetch_assoc()) {
                $ofertanteID = $row_ofertante['idUsuario'];
                $mensaje = "Se eliminó la publicación de ID $publicacionID por la cual habías ofertado.";
                $sql_insert_notificacion = "INSERT INTO notificacion (usuarioID, mensaje) VALUES (?, ?)";
                $stmt_insert_notificacion = $conn->prepare($sql_insert_notificacion);
                $stmt_insert_notificacion->bind_param("is", $ofertanteID, $mensaje);
                $stmt_insert_notificacion->execute();
            }

            echo '<script>alert("Publicación eliminada exitosamente."); window.location.href = "index.php";</script>';
        } else {
            echo '<script>alert("Error al eliminar la publicación."); window.location.href = "index.php";</script>';
        }
    }

    // Cerrar conexiones y liberar recursos
    $stmt->close();
    $stmt_delete_ofertas->close();
    $stmt_delete_publicacion->close();
    $stmt_insert_notificacion->close();
}

// Cerrar conexión
$conn->close();