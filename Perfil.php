<?php 
    include("header.php");
    include("BarraNavegacion.php");
    include("basedatos.php");
    include("ScriptsPhp\Perfil\modificarBarco.php");
    include("ScriptsPhp\Perfil\modificarPerfil.php");
    if(!empty($_SESSION["Id"])){
        $id=$_SESSION["Id"];
        if(!empty($_GET["idBuscar"])){
            $idajeno=$_GET["idBuscar"];
        }
        else{
            $idajeno=$id;
        }
        $sqlQuery="SELECT * FROM usuario WHERE id = '$idajeno'";
        $result= mysqli_query($conn ,$sqlQuery);
        if(mysqli_num_rows($result)>0){
            $row=mysqli_fetch_assoc($result);
            $nombreMostrar= $row["NombreUsuario"];
            
            if($row["DirFotoPerfil"]==null){
                $fotoPerfil="public/img/Default.jpg";
            }
            else{
                $fotoPerfil= $row["DirFotoPerfil"];
            }
            $valoracion=$row["Valoracion"];    
        }
    }
   else
   {
    header("Location: index.php");
   }
?>
<!DOCTYPE html>
<html lang="en">
<head>  
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="public/StylePerfil.css">
    <link rel="stylesheet" href="public/Style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo $nombreMostrar; ?></title>
</head>
<body>
   <div id="formAgregarBarco">
        <form action="Perfil.php" method="POST"  enctype="multipart/form-data">
            <label for="Patente">Patente</label> <br>
            <input type="text" name="Patente" id="" required> <br>

            <label for="Nombre">Nombre</label><br>
            <input type="text" name="Nombre" id="" required> <br>

            <label for="Tipo">Tipo</label><br>
            <select name="Tipo" id="">
                <option value="Velero">Velero</option>
                <option value="Pesca">Pesca</option>
                <option value="Lancha">Lancha</option>
                <option value="Yate">Yate</option>
                <option value="Otro">Otro</option>
            </select> <br>

            <label for="Marca">Marca</label> <br>
            <input type="text" name="Marca" id="" required> <br>

            <label for="Modelo">Modelo</label> <br>
            <input type="text" name="Modelo" id="" required> <br>
            <label for="Año">Año</label> <br>
            <input type="number" name="Año" id="" required> <br>
            <label for="Valor">Valor</label> <br>
            <input type="number" name="Valor" id="" required> <br>
            <label for="Motor">Motor</label> <br>
            <input type="text" name="Motor" id=""> <br>
            <label for="Foto">Foto:</label><br>
            <input type="file" name="Foto" accept="image/*"><br>
            <label for="pdf">Documentacion de la embarcacion (En PDF):</label><br> 
            <input type="file" name="documento" accept=".pdf" required><br>
            <div>
                <input type="submit" value="Subir" name="subirBarco" id="formBotAgregar">
                <button id="formBotCancelar" onclick="cerrarFormBarco()">Cancelar</button>
            </div>
        </form>
   </div>
   <section class="perfil" style="margin-left:15%"> 
   <div class="contenedor">
        <div class="elemento" id="divFotoPerfil" style="margin-right:50px;">
            <img src=<?php echo $fotoPerfil ?> alt="Foto perfil" id="FotoPerfil" style="width:250px; height:250px;">
        </div>
        <div id="botonPerfil" style=" position:absolute;display:none; top:11%; right:5%;">
            <button onclick="mostrarModPerfil()">Editar Perfil.</button>
        </div>
        <div class="divusuarioPuntaje">
            <div id="spanNombreUsuario" class="elemento">
                <h1 style="text-decoration: underline;"> Usuario: <?php echo $nombreMostrar ?> </h1>
            </div>
            <div id="valoración"style="top:50%;">
                <h3>Valoración: <?php echo $valoracion ?> / 5 </h3>
                <!--<img src="public\img\estrellas.png" alt="estrella" style="height:10px; position:absolute; top:11%; left:50%;">-->
                <div id="mostrarValoracion" style="visibility:hidden">
                <h3 class="elemento">Valorar Usuario</h3>
                <div class id="rating-stars" style="position:absolute; top:37%;">
                    <span class="star" data-rating="1">&#9733;</span>
                    <span class="star" data-rating="2">&#9733;</span>
                    <span class="star" data-rating="3">&#9733;</span>
                    <span class="star" data-rating="4">&#9733;</span>
                    <span class="star" data-rating="5">&#9733;</span>
                </div>
                <input type="hidden" name="rating" id="rating-value">
            <button onclick="submitRating(event) "style="position:absolute;top:44%;left:35%" >Enviar Valoración</button>
            </div>
        </div>
        </div>
        </div>
    </div>
    <div class= "contenedor" id="misBarcos" style="display:none; height:400px;">
        <h2>Mis embarcaciones: </h2>
        <br>
        <div id="divBarcos" style="border: black 3px solid;">
            <table id="tablaBarcos">
                <tr style="text-decoration: underline;">
                    <th><h3>Foto</h3></th>
                    <th><h3>Patente</h3></th>
                    <th><h3>Nombre</h3></th>
                    <th><h3>Tipo</h3></th>
                    <th><h3>Marca</h3></th>
                    <th><h3>Año</h3></th>
                    <th><h3>Valor</h3></th>
                    <th><h3>Editar</h3></th>
                    <th><h3>Borrar</h3></th>
                </tr>
                <?php 
                    if($id==$idajeno){
                        $SQL= mysqli_query($conn,"SELECT * FROM embarcacion WHERE usuarioID = $id ");
                        if(mysqli_num_rows($SQL)==0){
                            echo"<h3> No hay embarcaciones registradas.</h3>";
                        }
                        else{
                        while($next= mysqli_fetch_assoc($SQL)){
                            if($next["Foto"]==null){
                               $foto= "public\img\barcodefault.png";
                            }
                            else{
                                $foto=$next["Foto"];
                            }
                            echo "<tr>";
                            echo "<td><img src='$foto' alt='Foto del barco' style='max-width: 100px;'></td>";
                            echo "<td><h3>{$next['Patente']}</h3></td>";
                            echo "<td><h3>{$next['Nombre']}</h3></td>";
                            echo "<td><h3>{$next['Tipo']}</h3></td>";
                            echo "<td><h3>{$next['Marca']}</h3></td>";
                            echo "<td><h3>{$next['Anio']}</h3></td>";
                            echo "<td><h3>{$next['Valor']}</h3></td>";
                            echo "<td><button class='boton-editar' onclick='editar({$next['id']})'>Editar embarcación</button></td>";
                            echo "<td><button class='boton-borrar' onclick='borrar({$next['id']})'>Borrar embarcación</button></td>";
                            echo "</tr>";
                        }
                        }
                    }
                ?>
            </table>
        </div>
        <br>
        <br><button id="botonAñadirBarco" onclick="mostrarAgregarBarco()" style="margin-top: 10px;"> <i class="fas fa-plus"></i>  Agregar embarcación.</button>
     </div>
   </section>
</body>
</html>
<script>
    var formbarco= document.getElementById("formAgregarBarco");
    function mostrarAgregarBarco(){
       formbarco.style.display="block";
    }
    function cerrarFormBarco(){
        formbarco.style.display="none";
    }
    function mostrarMisBarcos(){
        document.getElementById("misBarcos").style.display="flex";
        document.getElementById("botonPerfil").style.display="block";
    }
</script>
<?php 
    if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST["subirBarco"])){
        $patenteBarco=$_POST["Patente"];
        $Query="SELECT * FROM embarcacion where Patente = '$patenteBarco' ";
        $resultado=mysqli_query($conn, $Query);
        if(mysqli_num_rows($resultado)>0){
            echo "<script> alert('ATENCIÓN! patente ya ingresada, verifica que la patente sea correcta, si es correcta, contacta con un administrador para solucionar el problema');</script>";
        }
        else{
            $nombreBarco=$_POST["Nombre"];
            $marcaBarco=$_POST["Marca"];
            $modeloBarco=$_POST["Modelo"];
            $anioBarco=$_POST["Año"];
            $tipoBarco=$_POST["Tipo"];
            $motorBarco=$_POST["Motor"];
            $valorBarco=$_POST["Valor"];
            if(isset($_FILES["Foto"])){
                $rutaFoto="public\img".$_FILES["Foto"]["name"];
                move_uploaded_file($_FILES["Foto"]["tmp_name"],$rutaFoto);
                $rutaFotoEscapada=mysqli_real_escape_string($conn,$rutaFoto);
            }
            else{
                $rutaFotoEscapada="public\img\barcodefault.png";
            }
            $temporal=$_FILES["documento"]["name"];
            $rutaPdf="public\Pdfs\'{$temporal}'";
            move_uploaded_file($_FILES["documento"]["tmp_name"],$rutaPdf);
            $rutaPdfEscapada=mysqli_real_escape_string($conn,$rutaPdf);
            $Query="INSERT INTO embarcacion (Nombre, Tipo, Marca, Modelo, Anio, Motor, Patente, Valor, Documentacion, Foto, usuarioID)
                    Values ('$nombreBarco', '$tipoBarco', '$marcaBarco', '$modeloBarco', '$anioBarco', '$motorBarco', '$patenteBarco', '$valorBarco', '$rutaPdfEscapada', '$rutaFotoEscapada', '$id')";
            if(mysqli_query($conn,$Query)){
                echo "<script> alert('Barco ingresado exitosamente!'); </script>";
                $conn->close();
                echo "<script>
                            if ( window.history.replaceState ) {
                                    window.history.replaceState( null, null, window.location.href );
                                }
                            window.location = window.location.href;
                        </script>";
                die();
            }
            else{
                echo "<script> alert('Se produjo un error, intenta ingresar nuevamente los datos'); </script>";
            }
        }
    }
?>
<?php 
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["rating"])) { 
        $rating=$_POST["rating"];
        echo "<script> alert('$idajeno');</script>";
        if(!empty($_POST["rating"])){
            echo "<script> alert('$idajeno');<(script>";
            $sql="SELECT * FROM valoracion where usValorandoId = '$id' and usValoradoId = '$idajeno'";
            $result1= mysqli_query($conn, $sql);
            if(!mysqli_num_rows($result1)==0)
            {
                $sql="UPDATE valoracion SET valor = '$rating' where usValorandoId = '$id' and usValoradoId = '$idajeno'";
            }
            else{
                $sql= "INSERT INTO valoracion (usValorandoId, usValoradoId, valor) VALUES ('$id', '$idajeno', '$rating')";
            }
            mysqli_query($conn,$sql);
            $sql="SELECT Valoracion from usuario where id= $idajeno";
            echo "<script> alert('$idajeno');</script>";
            $result2=mysqli_query($conn,$sql);
            $valoracion=promedio(mysqli_query($conn,"SELECT valor from valoracion where usValoradoId='$idajeno'"));
            $sql="UPDATE usuario SET Valoracion= $valoracion where id='$idajeno'";
            mysqli_query($conn,$sql);
            $conn->close();
        }
    }
    function promedio($valoraciones){
        $promedio=0;
        $numero=mysqli_num_rows($valoraciones);
        while($row=mysqli_fetch_assoc($valoraciones)){
            $promedio+=$row["valor"];
        }
        return $promedio / $numero;
    }
?>
<script>
function mostrarValorar(){
    document.getElementById("mostrarValoracion").style.visibility="visible";
}
document.addEventListener("DOMContentLoaded", function() {
    let stars = document.querySelectorAll('.star');
    stars.forEach(function(star) {
        star.addEventListener('click', function() {
            let rating = this.getAttribute('data-rating');
            document.getElementById('rating-value').value = rating;
            stars.forEach(function(innerStar) {
                if (innerStar.getAttribute('data-rating') <= rating) {
                    innerStar.classList.add('rated');
                } else {
                    innerStar.classList.remove('rated');
                }
            });
        });
    })
});
function submitRating() {
    let rating = document.getElementById('rating-value').value;
    let xhr = new XMLHttpRequest();
    xhr.open("POST",window.location.href, true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            console.log(xhr.responseText); // Imprime la respuesta del servidor en la consola
        }
    };
    xhr.send("rating=" + rating);
}
</script>
<?php 
    if($idajeno!=$_SESSION["Id"]){
        echo "<script> mostrarValorar(); </script>";
    }
    else{
        
        echo "<script> mostrarMisBarcos(); </script>";
    }
?>
<?php
    if(isset($_POST["idEditar"])){
        $nombreBarco=$_POST["Nombre"];
        $marcaBarco=$_POST["Marca"];
        $modeloBarco=$_POST["Modelo"];
        $anioBarco=$_POST["Año"];
        $tipoBarco=$_POST["Tipo"];
        $motorBarco=$_POST["Motor"];
        $valorBarco=$_POST["Valor"];
        $valorEditar=intval($_POST["idEditar"]);
        $patenteBarcoCambiar=$_POST["Patente"];
        $valorEditar=$_POST["idEditar"];
        $row=mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM embarcacion where id = $valorEditar"));
        $patenteVieja=$row["Patente"];
        if(isset($_FILES["Foto"])){
            $rutaFoto="public\img".$_FILES["Foto"]["name"];
            move_uploaded_file($_FILES["Foto"]["tmp_name"],$rutaFoto);
            $rutaFotoEscapada=mysqli_real_escape_string($conn,$rutaFoto);
        }
        else{
            $rutaFotoEscapada=mysqli_real_escape_string($conn,"public\img\barcodefault.png");
        }
        $temporal=$_FILES["documento"]["name"];
        $rutaPdf="public\Pdfs\'{$temporal}'";
        move_uploaded_file($_FILES["documento"]["tmp_name"],$rutaPdf);
        $rutaPdfEscapada=mysqli_real_escape_string($conn,$rutaPdf);
        $Query="SELECT * FROM embarcacion where Patente = '$patenteBarcoCambiar'";
        $resultado=mysqli_query($conn, $Query);
        if(mysqli_num_rows($resultado) > 0 && $patenteBarcoCambiar!=$patenteVieja){
            echo "<script> alert('ATENCIÓN! patente ya ingresada, verifica que la patente sea correcta, si es correcta, contacta con un administrador para solucionar el problema');</script>";
        }
        else{
            $resultado=mysqli_query($conn,"SELECT * FROM embarcacion WHERE id = '$valorEditar'");
            $row= mysqli_fetch_assoc($resultado);
            if($row["Ofertado"]==1){
               echo "<script>alert('Error, la embarcación está siendo ofertada/publicada, para modificarla elimine la oferta/publicación e intente de nuevo.');</script>";
            }
            else{
                $Query="UPDATE embarcacion SET Nombre='$nombreBarco', Marca='$marcaBarco', Modelo='$modeloBarco', Anio='$anioBarco', Tipo='$tipoBarco', Patente='$patenteBarcoCambiar', Motor='$motorBarco', Valor='$valorBarco', Foto='$rutaFotoEscapada', Documentacion='$rutaPdfEscapada' WHERE id = $valorEditar";
                mysqli_query($conn, $Query);
            }           
        }
        $conn->close();
        echo "<script>
                    if ( window.history.replaceState ) {
                            window.history.replaceState( null, null, window.location.href );
                        }
                    window.location = window.location.href;
                </script>";
        die();
    }
?>
<script>
    var divfor= document.getElementById("editarBarco");
    var ve=0;
    var form= document.getElementById('editarBarcoForm');
    function borrar(idBorrar){
        let xhr = new XMLHttpRequest();
            xhr.open('POST',window.location.href, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    console.log(xhr.responseText); // Imprime la respuesta del servidor en la consola
                }
            };
            xhr.send("idBorrar="+idBorrar);
    }
    function editar(idEditar){
        divfor.style.display="block";
        ve=idEditar;
    }
    function getEditar(){
        return ve;
    }
    function cerrarFor(){
        divfor.style.display="none";
    }
    function subir(){
            document.getElementById('escondido').value = getEditar();
            var formd= new FormData(form);
            let xhr = new XMLHttpRequest();
            xhr.open('POST',window.location.href, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    console.log(xhr.responseText); // Imprime la respuesta del servidor en la consola
                }
            };
            xhr.send(formd);
        }
</script>
<?php 
    if(isset($_POST["idBorrar"])){
        $idborrar=$_POST["idBorrar"];
        $query="DELETE FROM embarcacion WHERE id = '$idborrar'";
        mysqli_query($conn,$query);
        echo "<script>
        if ( window.history.replaceState ) {
                window.history.replaceState( null, null, window.location.href );
                    }
                window.location = window.location.href;
            </script>";
    }
?>
<?php 
    if(isset($_POST["cambiarNombre"])&&isset($_POST["nombrePerfil"])){
        $idPerfil=$_SESSION["Id"];
        $nombrePerfil=$_POST["nombrePerfil"];
        $resultado=mysqli_query($conn,"SELECT * FROM usuario WHERE NOT id='$idPerfil' AND NombreUsuario = '$nombrePerfil'");
        if(mysqli_num_rows($resultado)>0){
            echo "<script>alert('Error nombre de usuario repetido');</script>";
        }
        else{
            if(isset($_FILES["pfp"])&& $_FILES["pfp"]["name"]!=null){
                $rutaFoto="public\img" .$_FILES["pfp"]["name"];
                move_uploaded_file($_FILES["pfp"]["tmp_name"],$rutaFoto);
                $pfp=mysqli_real_escape_string($conn,$rutaFoto);
            }
            if(is_null($pfp)){
                $query="UPDATE usuario SET NombreUsuario = '$nombrePerfil' WHERE id = '$idPerfil'";
            }
            else{
                $query="UPDATE usuario SET NombreUsuario = '$nombrePerfil', DirFotoPerfil='$pfp' WHERE id = '$idPerfil'";
            }
            if(!mysqli_query($conn,$query)){
                echo "<script>alert('Error');</script>";
            }
            else{
                $_SESSION["username"]=$nombrePerfil;
            }
        }        
        echo "<script>
        if ( window.history.replaceState ) {
                window.history.replaceState( null, null, window.location.href );
                    }
                window.location = window.location.href;
            </script>";
        die();
    }
?>