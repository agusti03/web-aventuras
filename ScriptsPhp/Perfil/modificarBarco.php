<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/StylePerfil.css">
    <link rel="stylesheet" href="public/Style.css">
    <style>
        #editarBarco{
            top:2%;
            margin-left:30%;
            position:fixed;
            height:fit-content;
            width:500px;
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
<div id="editarBarco">
    <form id="editarBarcoForm" method="POST" enctype="multipart/form-data" onsubmit="subir()">
            <input type="hidden" name="idEditar" id="escondido" value="0"/>
            <label for="Patente">Patentes</label> <br>
            <input type="text" name="Patente" required> <br>
            <label for="Nombre">Nombre</label><br>
            <input type="text" name="Nombre" required> <br>
            <label for="Tipo">Tipo</label><br>
            <select name="Tipo" id="">
                <option value="Velero">Velero</option>
                <option value="Pesca">Pesca</option>
                <option value="Lancha">Lancha</option>
                <option value="Yate">Yate</option>
                <option value="Otro">Otro</option>
            </select> <br>
            <label for="Marca">Marca</label> <br>
            <input type="text" name="Marca" required> <br>
            <label for="Modelo">Modelo</label> <br>
            <input type="text" name="Modelo" required> <br>
            <label for="Año">Año</label> <br>
            <input type="number" name="Año" required> <br>
            <label for="Valor">Valor</label> <br>
            <input type="number" name="Valor" required> <br>
            <label for="Motor">Motor</label> <br>
            <input type="text" name="Motor" required> <br>
            <label for="Foto">Foto:</label><br>
            <input type="file" name="Foto" accept="image/*"><br>
            <label for="pdf">Documentacion de la embarcacion (En PDF):</label><br> 
            <input type="file" name="documento" accept=".pdf" required><br>
            <div>
                <input type="submit" value="Editar barco" name="editarBarco" id="formBotEditar">
                <button id="formBotCancelarEditar" onclick="cerrarFor()">Cancelar</button>
            </div>
        </form>
    </div>
</body>
</html>
