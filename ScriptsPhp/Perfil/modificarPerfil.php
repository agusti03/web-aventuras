<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        #divEditarPerfil{
            top:30%;
            margin-left:40%;
            position:fixed;
            height:fit-content;
            width:fit-content;
            padding:20px;
            align-content: center;
            align-items: center;
            z-index: 10;
            background-color: white;
            border: 1px solid black;
            animation: fadeIn 0.5s ease;
            display: none;
            text-align: center;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div id="divEditarPerfil">
        <form action="Perfil.php" method="post" id="FormEditarPerfil" enctype="multipart/form-data">
            <label for="fotoPerfil">Nueva foto de perfil</label> <br>
                <input type="file" name="pfp" accept="image/*"/> <br><br>
            <label for="Nombre">Nuevo nombre de perfil</label><br>
                <input type="text" name="nombrePerfil"><br><br>
            <input type="submit" name="cambiarNombre" value="Aceptar"/>
            <button onclick="cerrarModPerfil()">Cancelar</button>
        </form>
    </div>
</body>
</html>
<script>
    var divModPer = document.getElementById('divEditarPerfil');
    function cerrarModPerfil(){
        divModPer.style.display="none";
    }
    function mostrarModPerfil(){
        
        divModPer.style.display="flex";
    }
</script>