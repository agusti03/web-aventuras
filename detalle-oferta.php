<?php
    include("basedatos.php");
    include("header.php");
    include("BarraNavegacion.php");
    ob_start();
    if(!empty($_GET["idOferta"])){
        $idOf=$_GET["idOferta"];
        $sql="SELECT e1.Documentacion as docu1, e2.Documentacion as docu2 , i.id as idInt, u1.NombreUsuario as nom1, u2.NombreUsuario as nom2, e1.Patente as pat1, e2.Patente as pat2, e1.Foto as fot1, e2.Foto as fot2, i.fecha as fecha FROM intercambio i INNER JOIN usuario u1 ON i.fk_usuario1 = u1.id
        INNER JOIN usuario u2 ON i.fk_usuario2 = u2.id
        INNER JOIN embarcacion e1 ON i.fk_embarcacion1 = e1.id
        INNER JOIN embarcacion e2 ON i.fk_embarcacion2 = e2.id
        WHERE i.id=$idOf";
        $resultado=mysqli_query($conn,$sql);
        if(mysqli_num_rows($resultado)==0){
            echo "<script>alert('Id de oferta incorrecto.');</script>";
           echo '<script>window.location.href = "index.php";</script>';
        }
        $row=mysqli_fetch_assoc($resultado); 
        $dir1=htmlspecialchars($row["docu1"]);
        $dir2=htmlspecialchars($row["docu2"]);
        echo "<script>alert($dir1);</script>";
    }
    else{
        echo '<script>window.location.href = "index.php";</script>';
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de oferta</title>
    <style>
        #cuerpo{
            display: block;
            margin-left:15%;
            height: 100%;
            width: 85%;
            padding-inline-start: 10px;
            margin-right: 30px;
            padding:30px;
            background-color: white;
            overflow: scroll;
            overflow-x:hidden;
        }
    </style>
</head>
<body>
    <section id="cuerpo">
        <h2>Id de la oferta: <?php echo $idOf;?></h2>
        <h3>Intercambio efectuado en el día: <?php echo $row["fecha"]; ?></h3> <br>
        <div style="display:flex; flex-direction:row; width:fit-content; overflow-y:auto; height:auto">
            <div style="display:flex; height:fit-content; width:40%; flex-direction:column;" >
                <h4>Usuario: <?php echo $row["nom1"]; ?></h4> <br>
                <h4>Patente del barco intercambiado: <?php echo $row["pat1"]; ?></h4> <br>
                <h4>Imagen del barco: </h4> <br>
                <img src='<?php echo $row["fot1"];?>' alt="Foto del barco 1" style="heigth:200px;width:200px">
                <h4>Documentación:</h4>
                <a href='<?php echo $dir1; ?>' download="Documentacion">Descargar aquí.</a>
            </div>
            <div style="display:flex;  height:fit-content; width:40%;flex-direction:column; margin-left:100px">
                <h4>Usuario: <?php echo $row["nom2"]; ?></h4> <br>
                <h4>Patente del barco intercambiado: <?php echo $row["pat2"]; ?></h4> <br>
                <h4>Imagen del barco: </h4> <br>
                <img src='<?php echo $row["fot2"]; ?>' alt="Foto del barco 2" style="heigth:200px;width:200px">
                <h4>Documentación:</h4>
                <a href='<?php echo $dir2;?>' download="Documentacion">Descargar aquí.</a>
            </div>
        </div>
    </section>
</body>
</html>

<?php  echo "<script>alert('{$row['docu2']}');</script>"; ?>