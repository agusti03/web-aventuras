<?php
include("basedatos.php");
include("header.html");
include("header.php");
include("BarraNavegacion.php");
date_default_timezone_set('America/Argentina/Buenos_Aires');


if (!isset($_SESSION['Id'])) {
    echo '<script>alert("Debes iniciar sesión para realizar esta acción."); window.location.href = "index.php";</script>';
    die();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ofertaID = $_POST['ofertaID'];
    $accion = $_POST['accion'];

    if ($accion === 'cancelar') {
        // Consultar la fecha de creación de la oferta
        $sql_consultar_fecha = "SELECT fecha FROM oferta WHERE id = ?";
        $stmt_consultar_fecha = $conn->prepare($sql_consultar_fecha);
        $stmt_consultar_fecha->bind_param("i", $ofertaID);
        $stmt_consultar_fecha->execute();
        $resultado = $stmt_consultar_fecha->get_result();
        $oferta = $resultado->fetch_assoc();
        $stmt_consultar_fecha->close();

        if ($oferta) {
            $fecha_creacion = new DateTime($oferta['fecha']);
            $fecha_actual = new DateTime();
            $diferencia_horas = $fecha_actual->diff($fecha_creacion)->h + ($fecha_actual->diff($fecha_creacion)->days * 24);

            if ($diferencia_horas <= 72) {
                // Eliminar la oferta si no han pasado más de 72 horas
                mysqli_query($conn,"UPDATE embarcacion a INNER JOIN oferta b ON a.id=b.idEmbarcacion SET a.Ofertado=0 WHERE b.Id=$ofertaID");
                $sql_eliminar_oferta = "DELETE FROM oferta WHERE id = ?";
                $stmt_eliminar_oferta = $conn->prepare($sql_eliminar_oferta);
                $stmt_eliminar_oferta->bind_param("i", $ofertaID);
                $stmt_eliminar_oferta->execute();
                $stmt_eliminar_oferta->close();            
                
                echo '<script>alert("Oferta cancelada."); window.location.href = "Ofertas.php";</script>';
            } else {
                echo '<script>alert("No se puede cancelar la oferta pasadas 72 horas."); window.location.href = "Ofertas.php";</script>';
            }
        } else {
            echo '<script>alert("Oferta no encontrada."); window.location.href = "Ofertas.php";</script>';
        }
    } else {
        echo '<script>alert("Acción no válida."); window.location.href = "Ofertas.php";</script>';
    }
} else {
    echo '<script>alert("Método no permitido."); window.location.href = "Ofertas.php";</script>';
}