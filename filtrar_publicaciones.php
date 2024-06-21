<?php
include("basedatos.php");

$sql = "SELECT publicacion.*, embarcacion.valor AS precio, embarcacion.anio AS antiguedad, embarcacion.Tipo AS tipo, embarcacion.marca 
        FROM publicacion 
        JOIN embarcacion ON publicacion.embarcacionID = embarcacion.id";

// Filtrar publicaciones según los criterios seleccionados
if (isset($_GET['criterio'])) {
    $criterio = $_GET['criterio'];
    switch ($criterio) {
        case 'precio_asc':
            $sql .= " ORDER BY embarcacion.valor ASC";
            break;
        case 'precio_desc':
            $sql .= " ORDER BY embarcacion.valor DESC";
            break;
        case 'antiguedad_asc':
            $sql .= " ORDER BY embarcacion.anio ASC";
            break;
        case 'antiguedad_desc':
            $sql .= " ORDER BY embarcacion.anio DESC";
            break;
        case 'tipo':
            if (isset($_GET['tipo'])) {
                $tipo = $conn->real_escape_string($_GET['tipo']);
                $sql .= " WHERE embarcacion.Tipo = '$tipo'";
            }
            break;
        case 'marca':
            if (isset($_GET['marca'])) {
                $marca = $conn->real_escape_string($_GET['marca']);
                $sql .= " WHERE embarcacion.marca = '$marca'";
            }
            break;
    }
}

$resultado = mysqli_query($conn, $sql);


echo '<ul>';
if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        echo '<li>';
        echo '<h2>' . $row["Titulo"] . '</h2>';
        echo '<p>Precio: ' . $row["precio"] . '</p>';
        echo '<p>Antigüedad: ' . $row["antiguedad"] . '</p>';
        echo '<p>Marca: ' . $row["marca"] . '</p>';
        echo '</li>';
    }
} else {
    echo '<li>No se encontraron publicaciones.</li>';
}
echo '</ul>';

$conn->close();