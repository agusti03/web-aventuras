<?php
session_start();
include("basedatos.php");

if (!isset($_SESSION["Id"])) {
    echo '<script>alert("Debes iniciar sesión para realizar una oferta"); window.location.href = "index.php";</script>';
    die();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtener los datos de la oferta desde el formulario
    $publicacionID = $_POST['publicacionID'];
    $embarcacionID = $_POST['embarcacionID'];
    $usuarioID = $_SESSION['Id'];

    // Verificar que la publicación no esté finalizada
    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM publicacion WHERE id=$publicacionID"));
    if (str_contains($row["Titulo"], " [Publicación finalizada]")) {
        echo '<script>alert("Error, la publicación de intercambio anterior ya finalizó, no se efectuó la oferta, volviendo a la pantalla anterior..."); window.location.href = "visualizar_publicacion.php?id=' . $publicacionID . '";</script>';
        die();
    }

    // Verificar que la embarcación existe y no está ofertada
    $sqlCon = "SELECT * FROM embarcacion WHERE id=$embarcacionID";
    $resultado = mysqli_query($conn, $sqlCon);
    if (mysqli_num_rows($resultado) == 0) {
        echo '<script>alert("No tienes ninguna embarcación. Volviendo a la publicación anterior."); window.location.href = "visualizar_publicacion.php?id=' . $publicacionID . '";</script>';
        die();
    }
    $row = mysqli_fetch_assoc($resultado);
    if ($row['Ofertado'] == 1) {
        echo '<script>alert("Error, no se puede ofrecer una embarcación que ya está involucrada en otra oferta o publicación"); window.location.href = "visualizar_publicacion.php?id=' . $publicacionID . '";</script>';
        die();
    }

    // Insertar la oferta en la base de datos
    $sql_insertar_oferta = "INSERT INTO oferta (idPublicacion, idUsuario, idEmbarcacion) VALUES (?, ?, ?)";
    $stmt_insertar_oferta = $conn->prepare($sql_insertar_oferta);
    $stmt_insertar_oferta->bind_param("iii", $publicacionID, $usuarioID, $embarcacionID);

    if ($stmt_insertar_oferta->execute()) {
        // Actualizar el estado de la embarcación a "Ofertado"
        $sql2 = "UPDATE embarcacion SET Ofertado=1 WHERE id=$embarcacionID";
        mysqli_query($conn, $sql2);

        // Enviar la notificación al propietario de la publicación
        $sql_publicacion_propietario = "SELECT usuarioID FROM publicacion WHERE id = ?";
        $stmt_publicacion_propietario = $conn->prepare($sql_publicacion_propietario);
        $stmt_publicacion_propietario->bind_param("i", $publicacionID);
        $stmt_publicacion_propietario->execute();
        $result_publicacion_propietario = $stmt_publicacion_propietario->get_result();

        if ($result_publicacion_propietario->num_rows === 1) {
            $row = $result_publicacion_propietario->fetch_assoc();
            $propietarioID = $row['usuarioID'];

            // Obtener el nombre del usuario que realiza la oferta
            $sql_nombre_usuario = "SELECT Nombre FROM usuario WHERE id = ?";
            $stmt_nombre_usuario = $conn->prepare($sql_nombre_usuario);
            $stmt_nombre_usuario->bind_param("i", $usuarioID);
            $stmt_nombre_usuario->execute();
            $result_nombre_usuario = $stmt_nombre_usuario->get_result();
            $row_nombre_usuario = $result_nombre_usuario->fetch_assoc();
            $nombre_usuario = $row_nombre_usuario['Nombre'];

            // Obtener el título de la publicación
            $sql_titulo_publicacion = "SELECT Titulo FROM publicacion WHERE id = ?";
            $stmt_titulo_publicacion = $conn->prepare($sql_titulo_publicacion);
            $stmt_titulo_publicacion->bind_param("i", $publicacionID);
            $stmt_titulo_publicacion->execute();
            $result_titulo_publicacion = $stmt_titulo_publicacion->get_result();
            $row_titulo_publicacion = $result_titulo_publicacion->fetch_assoc();
            $titulo_publicacion = $row_titulo_publicacion['Titulo'];

            // Crear el mensaje de notificación
            $mensaje_notificacion = "El usuario $nombre_usuario realizó una oferta en tu publicación '$titulo_publicacion'.";

            // Insertar la notificación en la base de datos
            $sql_insertar_notificacion = "INSERT INTO notificacion (usuarioID, mensaje) VALUES (?, ?)";
            $stmt_insertar_notificacion = $conn->prepare($sql_insertar_notificacion);
            $stmt_insertar_notificacion->bind_param("is", $propietarioID, $mensaje_notificacion);
            $stmt_insertar_notificacion->execute();
        }

        // Redirigir a la página de ofertas con un mensaje de éxito
        echo '<script>alert("Oferta realizada con éxito."); window.location.href = "Ofertas.php";</script>';
    } else {
        // Si hubo un error al insertar la oferta, mostrar un mensaje de error
        echo '<script>alert("Hubo un error al procesar la oferta. Por favor, inténtelo de nuevo."); window.location.href = "Ofertas.php";</script>';
    }

    $stmt_insertar_oferta->close();
    $stmt_publicacion_propietario->close();
    $stmt_nombre_usuario->close();
    $stmt_titulo_publicacion->close();
    $stmt_insertar_notificacion->close();
    $conn->close();
} else {
    // Si la solicitud no es POST, redirigir a la página de ofertas con un mensaje de error
    echo '<script>alert("Método no permitido."); window.location.href = "Ofertas.php";</script>';
}
?>
