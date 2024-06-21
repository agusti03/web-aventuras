<?php
include ("header.php");
include ("basedatos.php");
include("header.html");
include("BarraNavegacion.php");

// Verificar si se recibió un ID de publicación
if (!isset($_GET['id'])) {
    die('ID de publicación no especificado.');
}

$publicacionID = $_GET['id'];

// Obtener los datos de la publicación desde la base de datos
$sql = "SELECT * FROM publicacion WHERE id = ? AND usuarioID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $publicacionID, $_SESSION["Id"]);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo '<script>alert("No tienes permiso para modificar o eliminar esta publicacion."); window.location.href = "index.php";</script>';
    die();
}

$publicacion = $resultado->fetch_assoc();

$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Publicación</title>
    <style>
        /* Estilos para los campos de entrada */
        input[type="text"] {
            width: 300px;
            /* Ancho predeterminado para el campo de título */
            margin-bottom: 10px;
            /* Espacio entre los campos */
        }

        /* Estilo específico para el campo de descripción */
        #Descripcion {
            width: 400px;
            /* Ancho más amplio para el campo de descripción */
            height: 100px;
            /* Altura para permitir múltiples líneas de texto */
        }
    </style>
</head>

<body>
    <div style="margin-left:15%; padding:20px">
        <h1>Editar Publicación</h1>
        <form action="detalle_publicacion.php?id=<?php echo $publicacionID; ?>" method="POST">
            <label for="Titulo">Titulo:</label><br>
            <input type="text" id="Titulo" name="Titulo" value="<?php echo htmlspecialchars($publicacion['Titulo']); ?>"
                required> <br>
            <label for="Descripcion">Descripcion:</label><br>
            <textarea id="Descripcion" name="Descripcion"
                required><?php echo htmlspecialchars($publicacion['Descripcion']); ?></textarea> <br>
            <input type="submit" name="Actualizar" value="Actualizar"><br>
        </form>
        <form action="eliminar_publicacion.php" method="POST"
            onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta publicación?');">
            <input type="hidden" name="publicacionID" value="<?php echo $publicacionID; ?>">
            <input type="submit" value="Eliminar publicación">
        </form>
    </div>
</body>

</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Actualizar'])) {
    $titulo = filter_input(INPUT_POST, "Titulo", FILTER_SANITIZE_SPECIAL_CHARS);
    $descripcion = filter_input(INPUT_POST, "Descripcion", FILTER_SANITIZE_SPECIAL_CHARS);
    $usuarioID = $_SESSION["Id"];

    // Actualizar los datos de la publicación en la base de datos
    $sql = "UPDATE publicacion SET Titulo = ?, Descripcion = ? WHERE id = ? AND usuarioID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $titulo, $descripcion, $publicacionID, $usuarioID);

    if ($stmt->execute()) {
        echo '<script>alert("Publicación actualizada exitosamente."); window.location.href = "index.php";</script>';
    } else {
        echo '<script>alert("Error al actualizar la publicación.");</script>';
    }

    $stmt->close();
}

$conn->close();
?>

