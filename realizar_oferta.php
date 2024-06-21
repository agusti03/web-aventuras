<?php
include("basedatos.php");
include("header.html");
include("header.php");
include("BarraNavegacion.php");

if (!isset($_SESSION["Id"])) {
    echo '<script>alert("Debes iniciar sesión para realizar una oferta"); window.location.href = "index.php";</script>';
    die();
}

$usuarioID = $_SESSION["Id"];
$publicacionID = $_GET['publicacionID'];

// Obtener las embarcaciones del usuario
$sql = "SELECT id, Nombre FROM embarcacion WHERE usuarioID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuarioID);
$stmt->execute();
$resultado = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Oferta</title>
</head>
<body>
    <div style="margin-left:15%; padding:20px">
    <h2>Realizar Oferta</h2>
    <form action="procesar_oferta.php" method="POST">
        <input type="hidden" name="publicacionID" value="<?php echo $publicacionID; ?>">
        <label for="embarcacion">Selecciona una embarcación:</label>
        <select name="embarcacionID" required>
            <?php while ($row = $resultado->fetch_assoc()) { ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['Nombre']; ?></option>
            <?php } ?>
        </select>
        <button type="submit">Enviar Oferta</button>
    </form>
            </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>