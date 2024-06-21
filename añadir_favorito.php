<?php

include ("header.php");
include ("basedatos.php");
include ("header.html");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuarioID = $_SESSION["Id"];
    $publicacionID = $_POST["publicacionID"];

    // Verificar si la publicación pertenece al usuario
    $sql = "SELECT usuarioID FROM publicacion WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $publicacionID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['usuarioID'] == $usuarioID) {
        echo '<script>alert("No puedes añadir tu propia publicación a favoritos."); window.location.href = "index.php";</script>';
    } else {
        // Verificar si la publicación ya está en favoritos
        $sql = "SELECT * FROM favorito WHERE usuarioID = ? AND publicacionID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $usuarioID, $publicacionID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo '<script>alert("Esta publicación ya está en tu lista de favoritos."); window.location.href = "index.php";</script>';
        } else {
            $sql = "INSERT INTO favorito (usuarioID, publicacionID) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $usuarioID, $publicacionID);

            if ($stmt->execute()) {
                echo '<script>alert("Publicación añadida a favoritos exitosamente."); window.location.href = "index.php";</script>';
            } else {
                echo '<script>alert("Error al añadir a favoritos la publicación.");</script>';
            }
        }
    }

    $stmt->close();
    $conn->close();
}
?>

