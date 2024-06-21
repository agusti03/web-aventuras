<?php
session_start();
include("basedatos.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones</title>
    <!-- Agrega tus estilos CSS aquí -->
    <style>
        .notificacion {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            position: relative;
            /* Añadido para posicionar correctamente el botón */
        }

        .borrar-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: #ff0000;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <h1>Notificaciones</h1>

    <div class="notificaciones">
        <?php
        // Lógica para recuperar y mostrar las notificaciones
        $usuarioID = $_SESSION["Id"];
        $sql_notificaciones = "SELECT * FROM notificacion WHERE usuarioID = ? ORDER BY fecha DESC";
        $stmt_notificaciones = $conn->prepare($sql_notificaciones);
        $stmt_notificaciones->bind_param("i", $usuarioID);
        $stmt_notificaciones->execute();
        $result_notificaciones = $stmt_notificaciones->get_result();

        if ($result_notificaciones->num_rows === 0) {
            echo "<p>No tienes nuevas notificaciones.</p>";
        } else {
            while ($row = $result_notificaciones->fetch_assoc()) {
                echo "<div class='notificacion'>";
                echo "<p>{$row['mensaje']}</p>";
                echo "<p>Fecha: {$row['fecha']}</p>";
                echo "<button class='borrar-btn' data-id='{$row['id']}'>Borrar</button>"; // Botón de borrar
                echo "</div>";
            }
        }

        $stmt_notificaciones->close();
        ?>
    </div>

    <!-- Agrega tus scripts JavaScript aquí -->
    <script>
        // Script para manejar el evento de clic en el botón de borrar
        document.querySelectorAll('.borrar-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const idNotificacion = btn.getAttribute('data-id');

                // Envía una solicitud AJAX para eliminar la notificación
                fetch('eliminar_notificacion.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            id: idNotificacion
                        }),
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error al borrar la notificación');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Elimina la notificación del DOM
                        btn.parentElement.remove();
                        alert(data.message); // Muestra un mensaje de éxito
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Hubo un problema al borrar la notificación');
                    });
            });
        });
    </script>
</body>

</html>