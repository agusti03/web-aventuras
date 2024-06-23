<?php
include("basedatos.php");
include("header.html");
include("header.php");
include("BarraNavegacion.php");
date_default_timezone_set('America/Argentina/Buenos_Aires');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver publicacion</title>
    <style>
        .comentario {
            margin-bottom: 20px;
        }

        .comentario form {
            margin-top: 10px;
        }

        .comentario .comentario {
            margin-left: 20px;
            /* Indentación para respuestas */
        }

        .comentario img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <div style="margin-left:15%; padding:20px; background-color:white; display:block; height:650px; overflow:scroll; overflow-x: hidden; ">
        <?php
        if (!isset($_GET['id'])) {
            die('ID de publicación no especificado.');
        }

        $publicacionID = $_GET['id'];

        // Obtener los datos de la publicación desde la base de datos
        $sql = "SELECT * FROM publicacion WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $publicacionID);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 0) {
            echo '<script>alert("Error"); window.location.href = "index.php";</script>';
            die();
        }

        $publicacion = $resultado->fetch_assoc();

        // Obtener los detalles del barco desde la base de datos
        $embarcacionID = $publicacion['embarcacionID'];
        $sql_embarcacion = "SELECT * FROM embarcacion WHERE id = ?";
        $stmt_embarcacion = $conn->prepare($sql_embarcacion);
        $stmt_embarcacion->bind_param("i", $embarcacionID);
        $stmt_embarcacion->execute();
        $resultado_embarcacion = $stmt_embarcacion->get_result();

        if ($resultado_embarcacion->num_rows === 0) {
            echo '<script>alert("Error al obtener los detalles del barco"); window.location.href = "index.php";</script>';
            die();
        }

        $embarcacion = $resultado_embarcacion->fetch_assoc();
        $stmt_embarcacion->close();


        $stmt->close();

        // Verificar si la publicación ya está en la lista de favoritos del usuario
        if (!isset($_SESSION["Id"])) {
            echo '<script>alert("Debes iniciar sesión para entrar en los detalles de la publicación"); window.location.href = "index.php";</script>';
            die();
        } else {
            $usuarioID = $_SESSION["Id"];
            $sql_favoritos = "SELECT * FROM favorito WHERE usuarioID = ? AND publicacionID = ?";
            $stmt_favoritos = $conn->prepare($sql_favoritos);
            $stmt_favoritos->bind_param("ii", $usuarioID, $publicacionID);
            $stmt_favoritos->execute();
            $resultado_favoritos = $stmt_favoritos->get_result();
            $ya_en_favoritos = $resultado_favoritos->num_rows > 0;
        }

        // Mostrar el botón correspondiente y muestra la publicacion
        echo '<div>';
        echo '<h2>' . $publicacion["Titulo"] . '</h2>';
        echo '<p>' . $publicacion["Descripcion"] . '</p>';
        echo '<div class="ficha-barco">';
        echo '<h3>Detalles del barco</h3>';
        echo '<p><strong>Nombre:</strong> ' . $embarcacion["Nombre"] . '</p>';
        echo '<p><strong>Marca:</strong> ' . $embarcacion["Marca"] . '</p>';
        echo '<p><strong>Tipo:</strong> ' . $embarcacion["Tipo"] . '</p>';
        echo '<p><strong>Modelo:</strong> ' . $embarcacion["Modelo"] . '</p>';
        echo '<p><strong>Año:</strong> ' . $embarcacion["Anio"] . '</p>';
        echo '<p><strong>Motor:</strong> ' . $embarcacion["Motor"] . '</p>';
        echo '<p><strong>Patente:</strong> ' . $embarcacion["Patente"] . '</p>';
        echo '<p><strong>Valor: $</strong> ' . $embarcacion["Valor"] . '</p>';
        echo '<p><img src="' . $embarcacion["Foto"] . '" alt="Foto del barco" style="height:200px; width:200px;"></p>';
        echo '</div>';

        if ($ya_en_favoritos) {
            echo '<form action="eliminar_favorito.php" method="POST">';
            echo '<input type="hidden" name="publicacionID" value="' . $publicacionID . '">';
            echo '<input type="submit" value="Eliminar de Favoritos">';
            echo '</form>';
        } else {
            echo '<form action="añadir_favorito.php" method="POST">';
            echo '<input type="hidden" name="publicacionID" value="' . $publicacionID . '">';
            echo '<input type="submit" value="Añadir a Favoritos">';
            echo '</form>';
        }
        echo '</div>';


        // Si el usuario es el autor de la publicacion, entonces no muestra la opcion de realizar oferta.
        if ($publicacion["usuarioID"] != $usuarioID) {
            echo '<form action="realizar_oferta.php" method="GET">';
            echo '<input type="hidden" name="publicacionID" value="' . $publicacionID . '">';
            echo '<input type="submit" value="Realizar oferta">';
            echo '</form>';
        }

        ob_start(); // Inicia el buffer de salida

        // Lógica de comentarios y respuestas
        $publicacionID = $_GET['id'];
        $usuarioID = $_SESSION['Id'];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['action'])) {
                $action = $_POST['action'];

                switch ($action) {
                    case 'eliminar':
                        if (isset($_POST['comentario_id'])) {
                            $comentarioID = $_POST['comentario_id'];
                            eliminarComentario($conn, $comentarioID, $usuarioID);
                        }
                        break;
                    case 'comentar':
                        if (isset($_POST['contenido'])) {
                            $contenido = $_POST['contenido'];
                            $parentID = isset($_POST['parent_id']) ? $_POST['parent_id'] : null;
                            comentar($conn, $contenido, $publicacionID, $usuarioID, $parentID);
                            echo "<meta http-equiv='refresh' content='0'>"; // Redirigir a la misma página después de 0 segundos
                            exit(); // Salir del script después de la redirección
                        }
                        break;
                    case 'editar':
                        if (isset($_POST['contenido']) && isset($_POST['comentario_id'])) {
                            $contenido = $_POST['contenido'];
                            $comentarioID = $_POST['comentario_id'];
                            editarComentario($conn, $contenido, $comentarioID, $usuarioID);
                            echo "<meta http-equiv='refresh' content='0'>"; // Redirigir a la misma página después de 0 segundos
                            exit(); // Salir del script después de la redirección
                        }
                        break;
                    default:
                        break;
                }

                echo '<script>alert("Cambios realizados, redirigiendo a la pagina principal"); window.location.href = "index.php";</script>';
            }
        }

        function eliminarComentario($conn, $comentarioID, $usuarioID)
        {
            // Validar tiempo transcurrido
            if (!validarTiempo($conn, $comentarioID, $usuarioID)) {
                echo '<script>alert("No puedes eliminar un comentario pasado los 15 minutos"); window.location.href = "index.php";</script>';
                exit();
            }

            $stmt = $conn->prepare("UPDATE comentario SET contenido = '[Este comentario ha sido eliminado por el autor]' WHERE id = ? AND usuarioID = ?");
            $stmt->bind_param("ii", $comentarioID, $usuarioID);
            $stmt->execute();
            $stmt->close();
        }

        function comentar($conn, $contenido, $publicacionID, $usuarioID, $parentID)
        {
            $stmt = $conn->prepare("INSERT INTO comentario (contenido, publicacionID, usuarioID, parent_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siii", $contenido, $publicacionID, $usuarioID, $parentID);
            $stmt->execute();
            $stmt->close();

            // Obtener el ID del propietario de la publicación
            $stmt_propietario = $conn->prepare("SELECT usuarioID FROM publicacion WHERE id = ?");
            $stmt_propietario->bind_param("i", $publicacionID);
            $stmt_propietario->execute();
            $stmt_propietario->bind_result($propietarioID);
            $stmt_propietario->fetch();
            $stmt_propietario->close();

            // Obtener el nombre del usuario que realizó el comentario
            $stmt_usuario = $conn->prepare("SELECT nombre FROM usuario WHERE id = ?");
            $stmt_usuario->bind_param("i", $usuarioID);
            $stmt_usuario->execute();
            $stmt_usuario->bind_result($nombreUsuario);
            $stmt_usuario->fetch();
            $stmt_usuario->close();

            // Crear el mensaje de notificación
            $mensaje = "El usuario {$nombreUsuario} ha comentado en tu publicación.";

            // Insertar la notificación para el propietario de la publicación
            $stmt_notificacion = $conn->prepare("INSERT INTO notificacion (usuarioID, mensaje) VALUES (?, ?)");
            $stmt_notificacion->bind_param("is", $propietarioID, $mensaje);
            $stmt_notificacion->execute();
            $stmt_notificacion->close();
        }


        function editarComentario($conn, $contenido, $comentarioID, $usuarioID)
        {
            // Validar tiempo transcurrido
            if (!validarTiempo($conn, $comentarioID, $usuarioID)) {
                echo '<script>alert("No puedes editar un comentario pasado los 15 minutos"); window.location.href = "index.php";</script>';
                exit();
            }

            // Obtener el contenido del comentario
            $stmt = $conn->prepare("SELECT contenido FROM comentario WHERE id = ? AND usuarioID = ?");
            $stmt->bind_param("ii", $comentarioID, $usuarioID);
            $stmt->execute();
            $stmt->bind_result($comentario);
            $stmt->fetch();
            $stmt->close();

            // Verificar si el comentario ha sido eliminado
            if ($comentario === '[Este comentario ha sido eliminado por el autor]') {
                echo '<script>alert("No puedes editar un comentario eliminado"); window.location.href = "index.php";</script>';
                exit(); // Salir del script
            }

            // Si el comentario no ha sido eliminado, proceder con la edición
            $stmt = $conn->prepare("UPDATE comentario SET contenido = CONCAT(?, ' (editado)') WHERE id = ? AND usuarioID = ?");
            $stmt->bind_param("sii", $contenido, $comentarioID, $usuarioID);
            $stmt->execute();
            $stmt->close();
        }
        function validarTiempo($conn, $comentarioID, $usuarioID)
        {
            $stmt = $conn->prepare("SELECT fecha FROM comentario WHERE id = ? AND usuarioID = ?");
            $stmt->bind_param("ii", $comentarioID, $usuarioID);
            $stmt->execute();
            $stmt->bind_result($fecha);
            $stmt->fetch();
            $stmt->close();

            $fechaComentario = strtotime($fecha);
            $fechaActual = time();

            echo '<script>';
            echo 'console.log("Fecha del comentario: ' . date("Y-m-d H:i:s", $fechaComentario) . '");';
            echo 'console.log("Fecha actual: ' . date("Y-m-d H:i:s", $fechaActual) . '");';
            echo 'console.log("Diferencia en segundos: ' . ($fechaActual - $fechaComentario) . '");';
            echo '</script>';

            return ($fechaActual - $fechaComentario) <= 15 * 60;
        }
        ?>

        <!-- Formulario para agregar un nuevo comentario -->
        <form method="post" action="">
            <textarea name="contenido" required></textarea>
            <button type="submit" name="action" value="comentar">Comentar</button>
        </form>

        <div id="editar-comentario" style="display: none;">
            <form id="formulario-edicion" method="post" action="">
                <textarea id="texto-editar" name="contenido" required></textarea>
                <button type="submit" name="action" value="editar">Guardar Cambios</button>
                <button type="button" id="cancelar-edicion">Cancelar</button>
            </form>
        </div>

        <?php
        // Función recursiva para mostrar comentarios y sus respuestas
        function mostrarComentarios($parentID, $conn, $publicacionID, $usuarioID)
        {
            if ($parentID === null) {
                $stmt = $conn->prepare("SELECT c.id, c.contenido, c.fecha, u.nombre AS autor, u.id AS usuarioID, u.DirFotoPerfil 
                                        FROM comentario c 
                                        JOIN usuario u ON c.usuarioID = u.id 
                                        WHERE c.publicacionID = ? AND c.parent_id IS NULL 
                                        ORDER BY c.fecha ASC");
                $stmt->bind_param("i", $publicacionID);
            } else {
                $stmt = $conn->prepare("SELECT c.id, c.contenido, c.fecha, u.nombre AS autor, u.id AS usuarioID, u.DirFotoPerfil 
                                        FROM comentario c 
                                        JOIN usuario u ON c.usuarioID = u.id 
                                        WHERE c.publicacionID = ? AND c.parent_id = ? 
                                        ORDER BY c.fecha ASC");
                $stmt->bind_param("ii", $publicacionID, $parentID);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                echo "<div class='comentario' style='margin-left: " . ($parentID ? "20px" : "0") . "; display: flex; align-items: flex-start;'>";
                echo "<img src='{$row['DirFotoPerfil']}' alt='Foto de perfil' style='width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;'>";
                echo "<div>";
                echo "<p><strong><a href='/mi-perfil?id={$row['usuarioID']}'>{$row['autor']}</a></strong> dijo:</p>";
                echo "<p>{$row['contenido']}</p>";
                echo "<p><em>{$row['fecha']}</em></p>";

                if ($row['usuarioID'] == $usuarioID) {
                    echo "<a href='#' class='editar-btn' data-id='{$row['id']}'>Editar</a> | ";
                    echo "<a href='#' class='eliminar-btn' data-id='{$row['id']}'>Eliminar  | </a>";
                }

                echo "<a href='#' class='responder-btn'>Responder</a>";
                echo "<div class='respuesta-form' style='display: none;'>";
                echo "<form method='post' action=''>";
                echo "<textarea name='contenido' required></textarea>";
                echo "<input type='hidden' name='parent_id' value='{$row['id']}'>";
                echo "<button type='submit' name='action' value='comentar'>Enviar</button>";
                echo "<button type='button'>Cancelar</button>";
                echo "</form>";
                echo "</div>";

                // Parte gráfica para la edición del comentario
                echo "<div class='editar-form' style='display: none;'>";
                echo "<form method='post' action=''>";
                echo "<textarea name='contenido' required>{$row['contenido']}</textarea>";
                echo "<input type='hidden' name='comentario_id' value='{$row['id']}'>";
                echo "<input type='hidden' name='action' value='editar'>";
                echo "<button type='submit'>Guardar Cambios</button>";
                echo "<button type='button' class='cancelar-btn'>Cancelar</button>";
                echo "</form>";
                echo "</div>";

                mostrarComentarios($row['id'], $conn, $publicacionID, $usuarioID);

                echo "</div>";
                echo "</div>";
            }

            $stmt->close();
        }

        // Mostrar comentarios principales (sin padre)
        mostrarComentarios(null, $conn, $publicacionID, $usuarioID);

        ob_end_flush(); // Envía el buffer de salida al navegador
        ?>
    </div>

    <script>
        document.querySelectorAll('.responder-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                // Encuentra el formulario de respuesta asociado al botón
                const respuestaForm = btn.nextElementSibling;

                // Cambia el estilo de visualización del formulario de respuesta
                if (respuestaForm.style.display === 'none' || respuestaForm.style.display === '') {
                    respuestaForm.style.display = 'block'; // Muestra el formulario
                } else {
                    respuestaForm.style.display = 'none'; // Oculta el formulario
                }
            });
        });

        // Función para ocultar el cuadro de comentario cuando se envía el formulario
        document.querySelectorAll('.respuesta-form form').forEach(form => {
            form.addEventListener('submit', () => {
                const respuestaForm = form.parentElement;
                respuestaForm.style.display = 'none'; // Oculta el formulario
            });
        });

        // Función para ocultar el cuadro de comentario cuando se hace clic en "Cancelar"
        document.querySelectorAll('.respuesta-form button[type="button"]').forEach(cancelButton => {
            cancelButton.addEventListener('click', () => {
                const respuestaForm = cancelButton.parentElement.parentElement;
                respuestaForm.style.display = 'none'; // Oculta el formulario
            });
        });

        // Función para manejar el clic en el botón "Editar"
        document.querySelectorAll('.editar-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                // Encuentra el formulario de edición asociado al botón
                const comentarioDiv = btn.closest('.comentario');
                const editarForm = comentarioDiv.querySelector('.editar-form');

                // Cambia el estilo de visualización del formulario de edición
                if (editarForm.style.display === 'none' || editarForm.style.display === '') {
                    editarForm.style.display = 'block'; // Muestra el formulario
                } else {
                    editarForm.style.display = 'none'; // Oculta el formulario
                }
            });
        });

        // Función para ocultar el formulario de edición cuando se hace clic en "Cancelar"
        document.querySelectorAll('.editar-form .cancelar-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const editarForm = btn.closest('.editar-form');
                editarForm.style.display = 'none'; // Oculta el formulario de edición
            });
        });

        document.querySelectorAll('.eliminar-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault(); // Prevenir el comportamiento predeterminado del enlace

                if (confirm('¿Estás seguro de que quieres eliminar este comentario?')) {
                    const comentarioID = btn.getAttribute('data-id');
                    const form = document.createElement('form');
                    form.method = 'post';
                    form.action = '';

                    const inputComentarioID = document.createElement('input');
                    inputComentarioID.type = 'hidden';
                    inputComentarioID.name = 'comentario_id';
                    inputComentarioID.value = comentarioID;

                    const inputAction = document.createElement('input');
                    inputAction.type = 'hidden';
                    inputAction.name = 'action';
                    inputAction.value = 'eliminar';

                    form.appendChild(inputComentarioID);
                    form.appendChild(inputAction);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    </script>


</body>

</html>