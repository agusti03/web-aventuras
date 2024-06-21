<?php
    include("header.php");
    include("BarraNavegacion.php");
    include("basedatos.php");
    include("MensajeDeError.php");

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'phpmailer/src/Exception.php';
    require 'phpmailer/src/PHPMailer.php';
    require 'phpmailer/src/SMTP.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="public\img\favicon.png">
    <link rel="stylesheet" href="public/error.css">
    <link rel="stylesheet" href="public/Style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <title>Registrarse</title>
</head>
<body>
    <section id="div-inisesion" style="display: block; height:70%; top:15%; width:50%;left:25%;">
        <div style>
            <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
                 <label for="Apellido" style="text-decoration: underline;"> Apellido </label> <br> 
                 <input type="text" name="Apellido" placeholder="Mariano" required> <br> <br>
                 <label for="Nombre" style="text-decoration: underline;"> Nombre </label> <br> 
                 <input type="text" name="Nombre" placeholder="Julian" required> <br> <br>
                 <label for="Dni" style="text-decoration: underline;"> DNI </label> <br> 
                 <input type="number" name="DNI" placeholder="4342434" required> <br> <br>
                 <label for="Email"style="text-decoration: underline;" >Email</label> <br>
                 <input type="email" name="Email" required> <br> <br>
                 <label for="Fecha de nacimiento" style="text-decoration: underline;"> Fecha de nacimiento</label> <br>
                 <input type="date" name="Fecha_de_nacimiento" required> <br><br>
                 <input type="submit" name="Registrarme" value="Registrarme"> </input>
            </form>
        </div>
</section>
</body>
</html>
<?php 
    // Este bbl
    //El if este chequea que los datos enviados no sean maliciosos y despues chequeo que no esten vacios
    //Para subirlos a la base de datos.
    //
    if (isset($_POST["Registrarme"])) 
    {
        $nombre= filter_input(INPUT_POST,"Nombre", FILTER_SANITIZE_SPECIAL_CHARS);
        $apellido= filter_input(INPUT_POST,"Apellido", FILTER_SANITIZE_SPECIAL_CHARS);
        $dni=filter_input(INPUT_POST,"DNI", FILTER_SANITIZE_SPECIAL_CHARS);
        $email= filter_input(INPUT_POST,"Email", FILTER_SANITIZE_SPECIAL_CHARS);
        $fechadenacimiento= filter_input(INPUT_POST,"Fecha_de_nacimiento", FILTER_SANITIZE_SPECIAL_CHARS);
        if(validarEdad($fechadenacimiento)){
            try{
            if(validarDNI(mysqli_query($conn,"SELECT * FROM usuario"),$dni)){
                $contra= generarContrasenaUnica();
                $hash= password_hash($contra, PASSWORD_DEFAULT);
                $nombreDeUsuario= $apellido.".".$nombre;
                $sql= "INSERT INTO usuario (Nombre, Apellido, DNI, Correo, FechaNacimiento, NombreUsuario, Contrasenia)
                    VALUES ('$nombre', '$apellido', '$dni', '$email', '$fechadenacimiento', '$nombreDeUsuario', '$hash')";
                mysqli_query($conn,$sql);
            
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'WebAventuras01@gmail.com';
                $mail->Password = 'kgzqhwncbbbnptcv';
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;

                $mail->setFrom('WebAventuras01@gmail.com');

                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Credenciales de usuario - Web Aventuras (no responder)';
                $mensaje = "Su nombre de usuario es " . $nombreDeUsuario . " y su contraseña es " . $contra ;
                $mail->Body = $mensaje;
                $mail->send() ;

                echo '<script>alert("Cuenta creada exitosamente. Se han enviado las credenciales de ingreso a su casilla de correo"); window.location.href = "index.php";</script>';
            }
            else{
                error("¡El DNI ya está ingresado!");
            }
                }
                catch(mysqli_sql_exception){
                    error("¡No se pudo conectar a la base de datos!");
                }
        }
        else {
            error("¡Necesitas ser mayor de edad para acceder a Web Aventuras!");
        }
        mysqli_close($conn);
    }
    function error($mensaje){
        echo '<script>';
        echo 'showError("' . $mensaje . '");';
        echo '</script>';
    }
    function validarDNI($query,$Dni){
        while($row=mysqli_fetch_assoc($query)){
            if($row["DNI"]==$Dni){
                return false;
            }
        }
        return true;
    }
    function validarEdad($fecha){
        if(is_String($fecha)){
            $fecha = strtotime($fecha);
        }
        if(Time() - $fecha < 18 * 31536000){
            return false;
        }
        return true;
    }
    function generarContrasenaUnica() {
        $longitud = 10;
        // Caracteres que se pueden utilizar para generar la contraseña
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        // Obtener la longitud total de los caracteres
        $longitud_caracteres = strlen($caracteres);
        // Inicializar la contraseña como una cadena vacía
        $contrasena = '';
        // Generar la contraseña combinando caracteres aleatorios
        for ($i = 0; $i < $longitud; $i++) {
            // Obtener un carácter aleatorio del conjunto de caracteres
            $caracter_aleatorio = $caracteres[rand(0, $longitud_caracteres - 1)];
    
            // Agregar el carácter aleatorio a la contraseña
            $contrasena .= $caracter_aleatorio;
        }
        // Devolver la contraseña generada
        return $contrasena;
    }
    
?>