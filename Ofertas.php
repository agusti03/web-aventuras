<?php
include("basedatos.php");
include("header.html");
include("header.php");
include("BarraNavegacion.php");

if (!isset($_SESSION['Id'])) {
    echo '<script>alert("Debes iniciar sesión para ver la sección de ofertas."); window.location.href = "index.php";</script>';
    die();
}

$usuarioID = $_SESSION['Id'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Ofertas</title>
    <style>
        .container {
            display: flex;
            flex-wrap: wrap;
        }
        .column {
            flex: 50%;
            padding: 20px;
            box-sizing: border-box;
        }
        .column h3 {
            text-align: center;
        }
    </style>
</head>
<body>
    <div style="margin-left:15%; padding:20px; background-color:white; display:block; height:730px; overflow:scroll; overflow-x: hidden;">
        <div class="container">
            <div class="column">
            <?php
            // Obtener las ofertas recibidas por el usuario actual
            $sql_ofertas_recibidas = "SELECT o.id AS ofertaID, e.Nombre AS nombreEmbarcacionOfrecida, e.Tipo, e.Marca, e.Modelo, e.Anio, e.Motor, e.Patente, e.Valor, e.Documentacion, e.Foto, u.NombreUsuario AS nombreUsuarioOferente, ep.Nombre AS nombreEmbarcacionPublicada, ep.Tipo AS tipoEmbarcacionPublicada, ep.Marca AS marcaEmbarcacionPublicada, ep.Modelo AS modeloEmbarcacionPublicada, ep.Anio AS anioEmbarcacionPublicada, ep.Motor AS motorEmbarcacionPublicada, ep.Patente AS patenteEmbarcacionPublicada, ep.Valor AS valorEmbarcacionPublicada, ep.Foto AS fotoEmbarcacionPublicada, ep.id AS idEmbarcacionPublicada
                                      FROM oferta o
                                      JOIN embarcacion e ON o.idEmbarcacion = e.id
                                      JOIN usuario u ON o.idUsuario = u.id
                                      JOIN publicacion p ON o.idPublicacion = p.id
                                      JOIN embarcacion ep ON p.embarcacionID = ep.id
                                      WHERE p.usuarioID = ?";
            $stmt_ofertas_recibidas = $conn->prepare($sql_ofertas_recibidas);
            $stmt_ofertas_recibidas->bind_param("i", $usuarioID);
            $stmt_ofertas_recibidas->execute();
            $resultado_ofertas_recibidas = $stmt_ofertas_recibidas->get_result();

            echo '<h3>Ofertas recibidas:</h3>';
            if ($resultado_ofertas_recibidas->num_rows > 0) {
                while ($oferta = $resultado_ofertas_recibidas->fetch_assoc()) {
                    echo '<p>El usuario ' . htmlspecialchars($oferta['nombreUsuarioOferente']) . ' ha realizado una oferta sobre tu embarcación ' . htmlspecialchars($oferta['nombreEmbarcacionPublicada']) . '</p>';
                    
                    echo '<p>Detalles de la embarcación ofrecida:</p>';
                    echo '<ul>';
                    echo '<li>Nombre: ' . htmlspecialchars($oferta['nombreEmbarcacionOfrecida']) . '</li>';
                    echo '<li>Tipo: ' . htmlspecialchars($oferta['Tipo']) . '</li>';
                    echo '<li>Marca: ' . htmlspecialchars($oferta['Marca']) . '</li>';
                    echo '<li>Modelo: ' . htmlspecialchars($oferta['Modelo']) . '</li>';
                    echo '<li>Año: ' . htmlspecialchars($oferta['Anio']) . '</li>';
                    echo '<li>Motor: ' . htmlspecialchars($oferta['Motor']) . '</li>';
                    echo '<li>Patente: ' . htmlspecialchars($oferta['Patente']) . '</li>';
                    echo '<li>Valor: ' . htmlspecialchars($oferta['Valor']) . '</li>';
                    echo '<li>Foto: <img src="' . htmlspecialchars($oferta['Foto']) . '" alt="Foto de la embarcación" style="max-width: 200px;"></li>';
                    echo '</ul>';
                    
                    echo '<p>Detalles de tu embarcación publicada:</p>';
                    echo '<ul>';
                    echo '<li>Nombre: ' . htmlspecialchars($oferta['nombreEmbarcacionPublicada']) . '</li>';
                    echo '<li>Tipo: ' . htmlspecialchars($oferta['tipoEmbarcacionPublicada']) . '</li>';
                    echo '<li>Marca: ' . htmlspecialchars($oferta['marcaEmbarcacionPublicada']) . '</li>';
                    echo '<li>Modelo: ' . htmlspecialchars($oferta['modeloEmbarcacionPublicada']) . '</li>';
                    echo '<li>Año: ' . htmlspecialchars($oferta['anioEmbarcacionPublicada']) . '</li>';
                    echo '<li>Motor: ' . htmlspecialchars($oferta['motorEmbarcacionPublicada']) . '</li>';
                    echo '<li>Patente: ' . htmlspecialchars($oferta['patenteEmbarcacionPublicada']) . '</li>';
                    echo '<li>Valor: ' . htmlspecialchars($oferta['valorEmbarcacionPublicada']) . '</li>';
                    echo '<li>Foto: <img src="' . htmlspecialchars($oferta['fotoEmbarcacionPublicada']) . '" alt="Foto de la embarcación" style="max-width: 200px;"></li>';
                    echo '</ul>';
                    
                    echo '<form action="gestionar_oferta.php" method="POST">';
                    echo '<input type="hidden" name="ofertaID" value="' . htmlspecialchars($oferta['ofertaID']) . '">';
                    echo '<input type="hidden" name="idEmbarcacionPublicada" value="' . htmlspecialchars($oferta['idEmbarcacionPublicada']) . '">';
                    echo '<button type="submit" name="accion" value="aceptar">Aceptar</button>';
                    echo '<button type="submit" name="accion" value="rechazar">Rechazar</button>';
                    echo '</form>';
                }
            } else {
                echo '<p>No tienes ofertas.</p>';
            }
            $stmt_ofertas_recibidas->close();
            ?>
            </div>

            <div class="column">
                    <?php
                    // Obtener las ofertas realizadas por el usuario actual
                    $sql_ofertas_realizadas = "SELECT o.id AS ofertaID, ep.Nombre AS nombreEmbarcacionPublicada, ep.Tipo AS tipoEmbarcacionPublicada, ep.Marca AS marcaEmbarcacionPublicada, ep.Modelo AS modeloEmbarcacionPublicada, ep.Anio AS anioEmbarcacionPublicada, ep.Motor AS motorEmbarcacionPublicada, ep.Patente AS patenteEmbarcacionPublicada, ep.Valor AS valorEmbarcacionPublicada, ep.Foto AS fotoEmbarcacionPublicada, e.Nombre AS nombreEmbarcacionOfrecida, e.Tipo, e.Marca, e.Modelo, e.Anio, e.Motor, e.Patente, e.Valor, e.Foto, du.NombreUsuario AS nombreUsuarioPublicacion
                                            FROM oferta o
                                            JOIN embarcacion e ON o.idEmbarcacion = e.id
                                            JOIN publicacion p ON o.idPublicacion = p.id
                                            JOIN usuario du ON p.usuarioID = du.id
                                            JOIN embarcacion ep ON p.embarcacionID = ep.id
                                            WHERE o.idUsuario = ?";
                    $stmt_ofertas_realizadas = $conn->prepare($sql_ofertas_realizadas);
                    $stmt_ofertas_realizadas->bind_param("i", $usuarioID);
                    $stmt_ofertas_realizadas->execute();
                    $resultado_ofertas_realizadas = $stmt_ofertas_realizadas->get_result();

                    echo '<h3>Ofertas realizadas:</h3>';
                    if ($resultado_ofertas_realizadas->num_rows > 0) {
                        while ($oferta = $resultado_ofertas_realizadas->fetch_assoc()) {
                            echo '<p>Has realizado una oferta sobre la embarcación ' . htmlspecialchars($oferta['nombreEmbarcacionPublicada']) . ' del usuario ' . htmlspecialchars($oferta['nombreUsuarioPublicacion']) . '</p>';
                            echo '<p>Detalles de la embarcación ofertada:</p>';
                            echo '<ul style="list-style-type: none;">';
                            echo '<li>Nombre: ' . htmlspecialchars($oferta['nombreEmbarcacionPublicada']) . '</li>';
                            echo '<li>Tipo: ' . htmlspecialchars($oferta['tipoEmbarcacionPublicada']) . '</li>';
                            echo '<li>Marca: ' . htmlspecialchars($oferta['marcaEmbarcacionPublicada']) . '</li>';
                            echo '<li>Modelo: ' . htmlspecialchars($oferta['modeloEmbarcacionPublicada']) . '</li>';
                            echo '<li>Año: ' . htmlspecialchars($oferta['anioEmbarcacionPublicada']) . '</li>';
                            echo '<li>Motor: ' . htmlspecialchars($oferta['motorEmbarcacionPublicada']) . '</li>';
                            echo '<li>Patente: ' . htmlspecialchars($oferta['patenteEmbarcacionPublicada']) . '</li>';
                            echo '<li>Valor: ' . htmlspecialchars($oferta['valorEmbarcacionPublicada']) . '</li>';
                            echo '<li>Foto: </li>';
                            echo '<li> <img src="' . htmlspecialchars($oferta['fotoEmbarcacionPublicada']) . '" alt="Foto de la embarcación" style="max-width: 200px;"></li>';
                            echo '</ul>';
                            
                            echo '<p>Detalles de la embarcación ofrecida:</p>';
                            echo '<ul style="list-style-type: none;">';
                            echo '<li>Nombre: ' . htmlspecialchars($oferta['nombreEmbarcacionOfrecida']) . '</li>';
                            echo '<li>Tipo: ' . htmlspecialchars($oferta['Tipo']) . '</li>';
                            echo '<li>Marca: ' . htmlspecialchars($oferta['Marca']) . '</li>';
                            echo '<li>Modelo: ' . htmlspecialchars($oferta['Modelo']) . '</li>';
                            echo '<li>Año: ' . htmlspecialchars($oferta['Anio']) . '</li>';
                            echo '<li>Motor: ' . htmlspecialchars($oferta['Motor']) . '</li>';
                            echo '<li>Patente: ' . htmlspecialchars($oferta['Patente']) . '</li>';
                            echo '<li>Valor: ' . htmlspecialchars($oferta['Valor']) . '</li>';
                            echo '<li>Foto:</li> ';
                            echo '<li> <img src="' . htmlspecialchars($oferta['Foto']) . '" alt="Foto de la embarcación" style="max-width: 200px;"></li>';
                            echo '</ul>';
                            echo '<form action="cancelar_oferta.php" method="POST">';
                            echo '<input type="hidden" name="ofertaID" value="' . htmlspecialchars($oferta['ofertaID']) . '">';
                            echo '<button type="submit" name="accion" value="cancelar">Cancelar oferta realizada</button>';
                            echo '</form>';
                        }
                    } else {
                        echo '<p>No has realizado ninguna oferta.</p>';
                    }
                    $stmt_ofertas_realizadas->close();
                    ?>
            </div>
        </div>
    </div>
</body>
</html>
