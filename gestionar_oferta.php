<?php
include("basedatos.php");
include("header.html");
include("header.php");
include("BarraNavegacion.php");

if (!isset($_SESSION['Id'])) {
    echo '<script>alert("Debes iniciar sesión para realizar esta acción."); window.location.href = "index.php";</script>';
    die();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ofertaID = $_POST['ofertaID'];
    $idEmbarcacionPublicada = $_POST['idEmbarcacionPublicada'];
    $accion = $_POST['accion'];

    // Obtener información de la oferta
    $sql_oferta = "SELECT idEmbarcacion, idUsuario, idPublicacion FROM oferta WHERE id = ?";
    $stmt_oferta = $conn->prepare($sql_oferta);
    $stmt_oferta->bind_param("i", $ofertaID);
    $stmt_oferta->execute();
    $resultado_oferta = $stmt_oferta->get_result();
    $oferta = $resultado_oferta->fetch_assoc();
    $idEmbarcacionOfrecida = $oferta['idEmbarcacion'];
    $idUsuarioOfertante = $oferta['idUsuario'];
    $idPublicacion = $oferta['idPublicacion'];
    $stmt_oferta->close();

    if ($accion === 'aceptar') {
        // Obtener los datos de la publicación desde la base de datos
        $sql = "SELECT * FROM publicacion WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idPublicacion);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 0) {
            // Manejar el caso en que no se encuentre la publicación
            echo '<script>alert("La publicación no existe."); window.location.href = "index.php";</script>';
            die();
        }

        $publicacion = $resultado->fetch_assoc();

        $stmt->close();

        // Intercambiar propiedad de las embarcaciones
        $conn->begin_transaction();

        try {
            //Quitar el ofertado a todos
            $sql="SELECT * FROM oferta f INNER JOIN embarcacion e ON e.id = f.idEmbarcacion";
            $result=mysqli_query($conn,$sql);
            while($row=mysqli_fetch_assoc($result)){
                $idBarcoCambiar=$row["id"];
                $sql="UPDATE embarcacion SET Ofertado = 0 WHERE id = $idBarcoCambiar ";
                mysqli_query($conn,$sql);
            }

            // Transferir la embarcación ofrecida al dueño de la publicación
            $sql_transferir_ofrecida = "UPDATE embarcacion SET usuarioID = ? WHERE id = ?";
            $stmt_transferir_ofrecida = $conn->prepare($sql_transferir_ofrecida);
            $stmt_transferir_ofrecida->bind_param("ii", $_SESSION['Id'], $idEmbarcacionOfrecida);
            $stmt_transferir_ofrecida->execute();
            $stmt_transferir_ofrecida->close();

            // Transferir la embarcación publicada al usuario ofertante
            $sql_transferir_publicada = "UPDATE embarcacion SET usuarioID = ?, Ofertado = 0 WHERE id = ?";
            $stmt_transferir_publicada = $conn->prepare($sql_transferir_publicada);
            $stmt_transferir_publicada->bind_param("ii", $idUsuarioOfertante, $idEmbarcacionPublicada);
            $stmt_transferir_publicada->execute();
            $stmt_transferir_publicada->close();

            // Eliminar todas las ofertas sobre la misma publicación
            $sql_eliminar_ofertas_publicacion = "DELETE FROM oferta WHERE idPublicacion = ?";
            $stmt_eliminar_ofertas_publicacion = $conn->prepare($sql_eliminar_ofertas_publicacion);
            $stmt_eliminar_ofertas_publicacion->bind_param("i", $idPublicacion);
            $stmt_eliminar_ofertas_publicacion->execute();
            $stmt_eliminar_ofertas_publicacion->close();

            // Actualizar el título de la publicación para indicar que está finalizada
            $sql_actualizar_publicacion = "UPDATE publicacion SET Titulo = CONCAT(Titulo, ' [Publicación finalizada]') WHERE id = ?";
            $stmt_actualizar_publicacion = $conn->prepare($sql_actualizar_publicacion);
            $stmt_actualizar_publicacion->bind_param("i", $idPublicacion);
            $stmt_actualizar_publicacion->execute();
            $stmt_actualizar_publicacion->close();

            // Enviar notificación al usuario ofertante sobre la oferta rechazada
            $mensaje_notificacion = "Tu oferta realizada en la publicación '" . $publicacion['Titulo'] . "' ha sido aceptada por el autor.";
            $sql_insertar_notificacion = "INSERT INTO notificacion (usuarioID, mensaje) VALUES (?, ?)";
            $stmt_insertar_notificacion = $conn->prepare($sql_insertar_notificacion);
            $stmt_insertar_notificacion->bind_param("is", $idUsuarioOfertante, $mensaje_notificacion);
            $stmt_insertar_notificacion->execute();
            $stmt_insertar_notificacion->close();

            echo '<script>alert("Oferta aceptada y embarcaciones intercambiadas. Se ha enviado una notificación al usuario ofertante."); window.location.href = "Ofertas.php";</script>';

            $conn->commit();
            echo '<script>alert("Oferta aceptada y embarcaciones intercambiadas."); window.location.href = "Ofertas.php";</script>';
        } catch (Exception $e) {
            $conn->rollback();
            echo '<script>alert("Hubo un error al aceptar la oferta. Por favor, inténtelo de nuevo."); window.location.href = "Ofertas.php";</script>';
        }
    } elseif ($accion === 'rechazar') {
        // Obtener los datos de la publicación desde la base de datos
        $sql = "SELECT * FROM publicacion WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idPublicacion);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 0) {
            // Manejar el caso en que no se encuentre la publicación
            echo '<script>alert("La publicación no existe."); window.location.href = "index.php";</script>';
            die();
        }

        $publicacion = $resultado->fetch_assoc();
        $stmt->close();

        // Eliminar la oferta
        $sql_editarof="UPDATE embarcacion SET Ofertado = 0 WHERE id= $idEmbarcacionOfrecida";
        mysqli_query($conn,$sql_editarof);
        $sql_eliminar_oferta = "DELETE FROM oferta WHERE id = ?";
        $stmt_eliminar_oferta = $conn->prepare($sql_eliminar_oferta);
        $stmt_eliminar_oferta->bind_param("i", $ofertaID);
        $stmt_eliminar_oferta->execute();
        $stmt_eliminar_oferta->close();

        // Enviar notificación al usuario ofertante sobre la oferta rechazada
        $mensaje_notificacion = "Tu oferta realizada en la publicación '" . $publicacion['Titulo'] . "' ha sido rechazada por el autor.";
        $sql_insertar_notificacion = "INSERT INTO notificacion (usuarioID, mensaje) VALUES (?, ?)";
        $stmt_insertar_notificacion = $conn->prepare($sql_insertar_notificacion);
        $stmt_insertar_notificacion->bind_param("is", $idUsuarioOfertante, $mensaje_notificacion);
        $stmt_insertar_notificacion->execute();
        $stmt_insertar_notificacion->close();

        echo '<script>alert("Oferta rechazada."); window.location.href = "Ofertas.php";</script>';
    } else {
        echo '<script>alert("Acción no válida."); window.location.href = "Ofertas.php";</script>';
    }
} else {
    echo '<script>alert("Método no permitido."); window.location.href = "Ofertas.php";</script>';
}
?>
