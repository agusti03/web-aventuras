<?php
session_start();
include("basedatos.php");
?>
<head>
    <link rel="icon" href="public/img/favicon.png">
    <!-- Fuentes -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
</head>
<header class="titulo">
    <div id="placeholderInicioSesionYUsuario" style="position: absolute;">
        <button id="iniciarsesion" style="display:block;">
            <h3> Iniciar Sesión </h3>
        </button>
        <h3 id="Bienvenida" style="display:none; margin-left:30px; top:25px">Bienvenido <?php echo $_SESSION["username"]; ?></h3>
    </div>
    <div id="cerrarSesi" style="position: absolute; left:93%; top: 15px;display:none">
        <a href="cerrarSesion.php" style="text-decoration:underline"> Cerrar sesión.</a>
    </div>
    <section id="div-inisesion" style="z-index:999;">
        <div style="position:absolute; top:0; left:0;">
            <button id="cerrar" style="font-size: large;">X</button>
        </div>
        <div style="text-align: center;position:fixed; margin-left:80px">
            <form method="POST">
                <label for="Username" style="text-decoration: underline;"> Nombre de usuario </label> <br>
                <input type="text" name="nick" placeholder="juan.pepe"> <br> <br>
                <label for="Contraseña" style="text-decoration: underline;">Contraseña</label> <br>
                <input type="password" name="Contraseña"> <br> <br>
                <input type="submit" name="Iniciars" value="Iniciar Sesion"> </input> <br>
                <a href="registrar-usuario.php" style="text-decoration: underline;">¿No tenes una cuenta? Registrate acá </a>
            </form>
        </div>
    </section>
    <!-- Enlace para las notificaciones -->
    <div id="notif" style="display:none; position: absolute; right: 150px; top: 20px; background-color:lightblue">
        <?php 
            if(!empty($_SESSION["Id"])){
                $idusuario=$_SESSION["Id"];
                $sqlquery="SELECT * FROM notificacion WHERE usuarioID = '$idusuario'";
                $resultado=mysqli_query($conn,$sqlquery);
                $cant=0;
                while($row=mysqli_fetch_assoc($resultado)){
                    if($row["leido"]==0){
                        $cant++;
                    }
                }
                if($cant>=1){
                    echo "<button style='position:absolute; height:3px; width:1px; border-radius:50%; background-color:red'>$cant<button>";
                }
            }
        ?>
        <a href="notificaciones.php" style="text-decoration: none; color: black;">
            <i class="fas fa-bell"></i> Notificaciones
        </a>
    </div>
</header>
<script>
    const iniciosesion = document.getElementById("div-inisesion");
    const boton = document.getElementById("iniciarsesion");
    const botcerrar = document.getElementById("cerrar");
    boton.onclick = function cerrar() {
        if (iniciosesion.style.display == "block") {
            iniciosesion.style.display = "none";
        } else {
            iniciosesion.style.display = "block";
        }
    };
    botcerrar.onclick = function() {
        iniciosesion.style.display = "none";
    }

    function ocultarIniciarSesion() {
        boton.style.display = "none";
        document.getElementById("Bienvenida").style.display = "block";
        document.getElementById("cerrarSesi").style.display = "block";
        document.getElementById("notif").style.display="block";
    }
</script>
<?php
// Modulo para mostrar/ ocultar inicio de sesion
if (!empty($_SESSION["Id"])) {
    $id = $_SESSION["Id"];
    $result = mysqli_query($conn, "SELECT * FROM usuario WHERE id = '$id'");
    $row = mysqli_fetch_assoc($result);
    $nombre = $row["NombreUsuario"];
    echo "<script> ocultarIniciarSesion(); </script>";
}
//Modulo iniciar sesion
if (isset($_POST["Iniciars"])) {
    $contra = $_POST["Contraseña"];
    $nick = $_POST["nick"];
    $sqlQuery = "SELECT * FROM usuario WHERE NombreUsuario = '$nick'";
    $result = mysqli_query($conn, $sqlQuery);
    $row = mysqli_fetch_assoc($result);
    if (mysqli_num_rows($result) > 0) {
        if (!(password_verify($contra, $row["Contrasenia"]))) {
            echo '<script>alert("La contraseña o el nombre de usuario son incorrectos");</script>';
        } else {
            $_SESSION["Id"] = $row["id"];
            $_SESSION["username"] = $row["NombreUsuario"];
            header("Location: index.php");
        }
    } else {
        echo '<script>alert("La contraseña o el nombre de usuario son incorrectos");</script>';
    }
}
?>