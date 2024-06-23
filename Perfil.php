<?php 
    include("header.php");
    include("BarraNavegacion.php");
    include("basedatos.php");
    include("ScriptsPhp\Perfil\modificarBarco.php");
    ob_start();
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
    
    echo '<script>window.location.href = "index.php";</script>';
    exit;
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
    <div id="borrar_barco" >
        <p>¿Está seguro de que quieres eliminar el barco seleccionado?</p>
        <p>Esta acción no se puede deshacer...</p>
        <form action="Perfil.php" method="POST">
            <input type="hidden" name="idBorrado" id="idBorrar" value="0" >
            <input type="submit" name="borrarBarco" value="Confirmar"style="position: absolute; left:35%;"/>
        </form>
        <button onclick="cancelarBorrado()" style="position: absolute; left:60%;">Cancelar</button>
    </div>
   <div id="formAgregarBarco" style="z-index:9999">
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

   <div class="contenedor" style="flex-direction: row;">
        <div class="elemento" id="divFotoPerfil" style="margin-right:50px; display:flex;">
            <img src=<?php echo $fotoPerfil ?> alt="Foto perfil" id="fotoPerfil" style="height:220px; width:220px; border:1px solid black">
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
        <div id="botonPerfil" style="display:none; flex-direction:row ;top:17%; right:17%;">
            <button onclick="mostrarModPerfil()"style="background-color:darkcyan;color:white;cursor:pointer; width:fit-content; height:fit-content;display:flex;">Editar Perfil</button><br>
            <button onclick="mostrarIntercambios()" style="background-color:darkcyan;color:white; cursor:pointer;width:fit-content; height:fit-content;display:flex;">Mostrar Historial</button>
        </div>
        </div>
    </div>
    <div id="intercambios">
        <button onclick="cerrarIntercambios()"style="background-color:red; color:white; border-radius: 10px; border:0px; cursor:pointer;">X</button>
        <?php 
            $sql="SELECT i.id as idInt, u1.NombreUsuario as nom1, u2.NombreUsuario as nom2, e1.Patente as pat1, e2.Patente as pat2, e1.Foto as fot1, e2.Foto as fot2, i.fecha as fecha FROM intercambio i INNER JOIN usuario u1 ON i.fk_usuario1 = u1.id
            INNER JOIN usuario u2 ON i.fk_usuario2 = u2.id
            INNER JOIN embarcacion e1 ON i.fk_embarcacion1 = e1.id
            INNER JOIN embarcacion e2 ON i.fk_embarcacion2 = e2.id
            WHERE i.fk_usuario1 = $idajeno OR i.fk_usuario2 = $idajeno";
            $result=mysqli_query($conn,$sql);
            while($row=mysqli_fetch_assoc($result)){
                $nom1=$row["nom1"];
                $nom2=$row["nom2"];
                $em1=$row["pat1"];
                $fot1=$row["fot1"]??"public\img\barcoDefault.png";
                $em2=$row["pat2"];
                $fot2=$row["fot2"]??"public\img\barcoDefault.png";
                $fecha=$row["fecha"];
                echo "<div class='intercambio' onclick='mostrarDetallesIntercambio({$row['idInt']})'>";
                echo "<div class='cont1'>";
                echo "<div class='info'>";
                echo "<p>Usuario: {$nom1}</p>";
                echo "</div>";
                echo "<img src='{$fot1}' alt='Foto Embarcación 1' style='max-width: 100px;border:1px solid black'>";
                echo "</div>";
                echo "<img src='public\img\Restock.jpg' alt='Cambio por:' style='width:80px; height:80px;'>";
                echo "<div class='cont1'>";
                echo "<div class='info'>";
                echo "<p>Usuario: {$nom2}</p>";
                echo "</div>";
                echo "<img src='{$fot2}' alt='Foto Embarcación 2' style='max-width: 100px;border:1px solid black'>";
                echo "</div>";
                echo "</div>";
            }
        ?>
    </div>
    <div class= "contenedor" id="misBarcos" style="display:none; height:400px;">
        <h2 style="text-decoration: underline;">Mis embarcaciones: </h2>
        <br>
        <div id="divBarcos" style="border: black 3px solid;">
                <?php 
                    if($id==$idajeno){
                        $SQLquery= mysqli_query($conn,"SELECT * FROM embarcacion WHERE usuarioID = $id ");
                        if(mysqli_num_rows($SQLquery)==0){
                            echo"<h3> No hay embarcaciones registradas.</h3>";
                        }
                        else{
                            echo "<table id='tablaBarcos'>
                                <tr style='text-decoration: underline;'>
                                    <th><h3>Foto</h3></th>
                                    <th><h3>Patente</h3></th>
                                    <th><h3>Nombre</h3></th>
                                    <th><h3>Tipo</h3></th>
                                    <th><h3>Marca</h3></th>
                                    <th><h3>Motor</h3></th>
                                    <th><h3>Año</h3></th>
                                    <th><h3>Valor</h3></th>
                                    <th><h3>Editar</h3></th>
                                    <th><h3>Borrar</h3></th>
                                </tr>";
                        while($next= mysqli_fetch_assoc($SQLquery)){
                            $foto=$next["Foto"];
                            echo "<tr>";
                            echo "<td><img src='$foto' alt='Foto del barco' style='max-width: 100px;'></td>";
                            echo "<td style='display:none'><h3>{$next['id']}</h3></td>";
                            echo "<td style='display:none'><h3>{$next['Documentacion']}</h3></td>";
                            echo "<td><h3>{$next['Patente']}</h3></td>";
                            echo "<td><h3>{$next['Nombre']}</h3></td>";
                            echo "<td><h3>{$next['Tipo']}</h3></td>";
                            echo "<td><h3>{$next['Marca']}</h3></td>";
                            echo "<td><h3>{$next['Motor']}</h3></td>";
                            echo "<td><h3>{$next['Anio']}</h3></td>";
                            echo "<td><h3>{$next['Valor']}</h3></td>";
                            echo "<td><button class='boton-editar' data-id='{$next['id']}' data-patente='{$next['Patente']}' data-nombre='{$next['Nombre']}' data-tipo='{$next['Tipo']}' data-marca='{$next['Marca']}' data-motor='{$next['Motor']}' data-modelo='{$next["Modelo"]}' data-anio='{$next['Anio']}' data-valor='{$next['Valor']}' data-foto='{$foto}' data-documentacion='{$next['Documentacion']}' onclick='editarEmbarcacion(this)'>Editar embarcación</button></td>";
                            echo "<td><button class='boton-borrar' onclick='borrar({$next['id']})'>Borrar embarcación</button></td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                        }
                    }
                ?>
        </div>
        <br>
        <br><button id="botonAñadirBarco" onclick="mostrarAgregarBarco()" style="margin-top: 10px;"> <i class="fas fa-plus"></i>  Agregar embarcación.</button>
     </div>
   </section>
</body>
</html>
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
            if(isset($_FILES["Foto"])&& $_FILES["Foto"]['error'] !== UPLOAD_ERR_NO_FILE){
                $rutaFoto="public\img".$_FILES["Foto"]["name"];
                move_uploaded_file($_FILES["Foto"]["tmp_name"],$rutaFoto);
                $rutaFotoEscapada=mysqli_real_escape_string($conn,$rutaFoto);
            }else{
                $rutaFotoEscapada=mysqli_real_escape_string($conn,"public\img\barcoDefault.png");
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
                exit;
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
            exit;
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
                 if(isset($_FILES["Foto"])&&$_FILES["Foto"]['error'] !== UPLOAD_ERR_NO_FILE){ 
                    $rutaFoto="public\img".$_FILES["Foto"]["name"];
                    move_uploaded_file($_FILES["Foto"]["tmp_name"],$rutaFoto);
                    $rutaFotoEscapada=mysqli_real_escape_string($conn,$rutaFoto);
                 }
                else{
                    $rutaFotoEscapada=mysqli_real_escape_string($conn,$row["Foto"]);
                }
                if(isset($_FILES["documento"])&&$_FILES["documento"]['error'] !== UPLOAD_ERR_NO_FILE){
                    $temporal=$_FILES["documento"]["name"];
                    $rutaPdf="public\Pdfs\'{$temporal}'";
                    move_uploaded_file($_FILES["documento"]["tmp_name"],$rutaPdf);
                    $rutaPdfEscapada=mysqli_real_escape_string($conn,$rutaPdf);
                }
                else{
                    $rutaPdfEscapada=mysqli_real_escape_string($conn,$row["Documentacion"]);
                }
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
        exit;
    }
?>
<script>
    var divfor= document.getElementById("editarBarco");
    var form= document.getElementById('editarBarcoForm');
    var formbarco= document.getElementById("formAgregarBarco");
    var interc=document.getElementById("intercambios");
    function mostrarDetallesIntercambio(id){
        var url="detalle-oferta.php?idOferta="+id;
        window.open(url);
    }
    function cerrarIntercambios(){
        interc.style.display="none";
    }
    function mostrarIntercambios(){
        cancelarBorrado();
        cerrarFormBarco();
        cerrarFor();
        interc.style.display="block";
    }
    function mostrarAgregarBarco(){
       formbarco.style.display="block";
       cerrarIntercambios();
       cancelarBorrado();
       cerrarFor();
    }
    function cerrarFormBarco(){
        formbarco.style.display="none";
    }
    function mostrarMisBarcos(){
        document.getElementById("misBarcos").style.display="flex";
        document.getElementById("botonPerfil").style.display="flex";
    }
    function borrar(idBorrar){
        document.getElementById("borrar_barco").style.display="block";
        document.getElementById("idBorrar").value=idBorrar;
        cerrarIntercambios();
        cerrarFormBarco();
        cerrarFor();
    }
    function cancelarBorrado(){
        document.getElementById("borrar_barco").style.display="none";
    }
    function editarEmbarcacion(button) {
        var idEditar = button.getAttribute('data-id');
        var patente = button.getAttribute('data-patente');
        var nombre = button.getAttribute('data-nombre');
        var tipo = button.getAttribute('data-tipo');
        var modelo= button.getAttribute('data-modelo');
        var marca = button.getAttribute('data-marca');
        var motor = button.getAttribute('data-motor');
        var anio = button.getAttribute('data-anio');
        var valor = button.getAttribute('data-valor');
        var foto = button.getAttribute('data-foto');
        var documentacion = button.getAttribute('data-documentacion'); 
        document.getElementById('escondido').value = idEditar;
        document.getElementById('pat').value = patente;
        document.getElementById('nom').value = nombre;
        document.getElementById('tip').value = tipo;
        document.getElementById('mar').value = marca;
        document.getElementById('mod').value = modelo;
        document.getElementById('mot').value = motor;
        document.getElementById('ani').value = anio;
        document.getElementById('val').value = valor;
        divfor.style.display="block";
        cancelarBorrado();
        cerrarIntercambios();
        cerrarFormBarco();
    }
    function cerrarFor(){
        divfor.style.display="none";
    }
    function subir(){
            var formd= new FormData(form);
            window.location.href = "Perfil.php";
            let xhr = new XMLHttpRequest();
            xhr.open('POST',window.location.href, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    console.log(xhr.responseText); // Imprime la respuesta del servidor en la consola
                }
            };
            xhr.send(formd);
            window.location=window.location.href;
        }
</script>
<?php 
    if(isset($_POST["borrarBarco"])){
        $idborrar=$_POST["idBorrado"];
        $query="DELETE FROM embarcacion WHERE id = '$idborrar'";
        mysqli_query($conn,$query); 
        echo "<script>
        if ( window.history.replaceState ) {
                window.history.replaceState( null, null, window.location.href );
                    }
                window.location = window.location.href;
            </script>";
        exit;
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
            if(isset($_FILES["pfp"]) && $_FILES["pfp"]['error'] !== UPLOAD_ERR_NO_FILE){
                $rutaFoto="public\img" .$_FILES["pfp"]["name"];
                move_uploaded_file($_FILES["pfp"]["tmp_name"],$rutaFoto);
                $pfp=mysqli_real_escape_string($conn,$rutaFoto);
                $query="UPDATE usuario SET NombreUsuario = '$nombrePerfil', DirFotoPerfil='$pfp' WHERE id = '$idPerfil'";
            }
            else{
                $query="UPDATE usuario SET NombreUsuario = '$nombrePerfil' WHERE id = '$idPerfil'";
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
        exit;
    }
?>

<?php 
    if($idajeno!=$_SESSION["Id"]){
        echo "<script> mostrarValorar(); </script>";
    }
    else{
        
        echo "<script> mostrarMisBarcos(); </script>";
    }
?>