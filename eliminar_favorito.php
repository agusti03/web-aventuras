<?php
include ("header.php");
include ("basedatos.php");
include ("header.html");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuarioID = $_SESSION["Id"];
    $publicacionID = $_POST["publicacionID"];

    // Verificar si la publicación pertenece al usuario
    $sql = "SELECT * FROM favorito WHERE usuarioID = ? AND publicacionID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $usuarioID, $publicacionID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // La publicación está en los favoritos del usuario, entonces eliminarla
        $sql_delete = "DELETE FROM favorito WHERE usuarioID = ? AND publicacionID = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("ii", $usuarioID, $publicacionID);

        if ($stmt_delete->execute()) {
            echo '<script>alert("Publicación eliminada de favoritos exitosamente."); window.location.href = "index.php";</script>';
        } else {
            echo '<script>alert("Error al eliminar la publicación de favoritos."); window.location.href = "index.php";</script>';
        }

        $stmt_delete->close();
    } else {
        echo '<script>alert("Esta publicación no está en tu lista de favoritos."); window.location.href = "index.php";</script>';
    }

    $stmt->close();
    $conn->close();
}
?>

