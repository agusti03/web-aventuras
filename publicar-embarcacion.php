<?php 
    include("header.php");
    include("header.html");
    include("barraNavegacion.php");
    include ("basedatos.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Publicar Embarcación</title>
    <style>
        /* Estilos para los campos de entrada */
        input[type="text"] {
            width: 300px; /* Ancho predeterminado para el campo de título */
            margin-bottom: 10px; /* Espacio entre los campos */
        }

        /* Estilo específico para el campo de descripción */
        #Descripcion {
            width: 400px; /* Ancho más amplio para el campo de descripción */
            height: 100px; /* Altura para permitir múltiples líneas de texto */
        }
    </style>
</head>
<body>
    <div style="margin-left:15%; padding:20px">
    <h1>Publicar Embarcación</h1>
    <form action="publicar-embarcacion.php" method="POST">
        <label for="Titulo" style="text-decoration: underline;">Titulo:</label><br>
        <input type="text" id="Titulo" name="Titulo" placeholder="Título de la publicación" required> <br>
        <label for="Descripcion" style="text-decoration: underline;">Descripcion:</label><br>
        <textarea id="Descripcion" name="Descripcion" placeholder="Descripcion de la publicación" required></textarea> <br>
        <label for="Embarcacion" style="text-decoration: underline;">Embarcacion:</label><br>
        <select id="Embarcacion" name="Embarcacion">
            <option value="">Selecciona una embarcacion</option>
            <?php
            // Consulta SQL
            // echo "<script> alert('se ejecuto el php, previo a consulta sql'); </script>"; //borrar
            $id = $_SESSION["Id"] ;
            // echo "<script> alert('usuario $id'); </script>"; // borrar
            $sql = "SELECT * FROM embarcacion where usuarioID = $id";
            $resultado = mysqli_query($conn, $sql); 
            // echo "<script> alert('se ejecuto el php 2'); </script>"; // borrar
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_assoc($resultado)) {
                    echo '<option value="' . $row["id"] . '">' . $row["Nombre"] . '</option>';
                }
            }
            ?>
        </select> <br>
        <input type="submit" name="Publicar" value="Publicar">
    </form>
    </div>
</body>
</html>
<?php
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $titulo= filter_input(INPUT_POST,"Titulo", FILTER_SANITIZE_SPECIAL_CHARS);
        $descripcion= filter_input(INPUT_POST,"Descripcion", FILTER_SANITIZE_SPECIAL_CHARS);
        $usuarioID= $_SESSION["Id"] ;
        $embarcacionID = $_POST["Embarcacion"] ;

        if (empty($embarcacionID)) {
            // Si no se selecciona ninguna embarcación, mostrar mensaje y no crear la publicación
            echo '<script>alert("Tienes que cargar al menos una embarcacion a tu perfil para poder realizar una publicacion.");</script>';
        } else {
            $sqlQuery="SELECT * FROM embarcacion where id=$embarcacionID";
            $resultado=mysqli_query($conn,$sqlQuery);
            $row=mysqli_fetch_assoc($resultado);
            if($row["Ofertado"]==1){
                echo '<script>alert("Error, no se puede volver a publicar una embarcacion que ya fue ofertada o publicada.");</script>';
                die();
            }
            // Crear la publicación solo si se ha seleccionado una embarcación
            $sql = "INSERT INTO publicacion (Titulo, Descripcion, embarcacionID, usuarioID)
                    VALUES ('$titulo', '$descripcion', '$embarcacionID', '$usuarioID')";
            if (mysqli_query($conn, $sql)) {
                mysqli_query($conn,"UPDATE embarcacion SET Ofertado=1 where id=$embarcacionID");
                echo '<script>alert("Publicación creada exitosamente.");</script>';
            } else {
                echo '<script>alert("Error al crear la publicación. Por favor, inténtalo de nuevo más tarde.");</script>';
            }}
    }
    mysqli_close($conn);

?>


