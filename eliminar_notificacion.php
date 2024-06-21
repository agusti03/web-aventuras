<?php
session_start();
include("basedatos.php");

// Verificar si la solicitud es POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtener el ID de la notificación a eliminar del cuerpo de la solicitud JSON
    $data = json_decode(file_get_contents("php://input"), true);
    $idNotificacion = $data["id"];

    // Verificar si el usuario está autenticado y tiene permiso para eliminar la notificación
    if (isset($_SESSION["Id"])) {
        // Eliminar la notificación de la base de datos
        $sql = "DELETE FROM notificacion WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idNotificacion);
        if ($stmt->execute()) {
            // Si la eliminación fue exitosa, devolver una respuesta JSON con un mensaje de éxito
            echo json_encode(["success" => true, "message" => "Notificación eliminada correctamente"]);
        } else {
            // Si hubo un error al eliminar la notificación, devolver una respuesta JSON con un mensaje de error
            echo json_encode(["success" => false, "message" => "Error al eliminar la notificación"]);
        }
        $stmt->close();
    } else {
        // Si el usuario no está autenticado, devolver una respuesta JSON con un mensaje de error
        echo json_encode(["success" => false, "message" => "No estás autorizado para realizar esta acción"]);
    }
} else {
    // Si la solicitud no es POST, devolver una respuesta JSON con un mensaje de error
    echo json_encode(["success" => false, "message" => "Método no permitido"]);
}