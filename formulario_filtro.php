<section class="busqueda">
    <h1>Filtrar Publicaciones</h1>
    <form action="index.php" method="GET">
        <label for="criterio">Filtrar por:</label>
        <select name="criterio" id="criterio" onchange="toggleInputFields()">
            <option value="precio_asc">Precio: Menor a Mayor</option>
            <option value="precio_desc">Precio: Mayor a Menor</option>
            <option value="antiguedad_asc">Antigüedad: Menor a Mayor</option>
            <option value="antiguedad_desc">Antigüedad: Mayor a Menor</option>
            <option value="tipo">Tipo de Barco</option>
            <option value="marca">Marca</option>
        </select>
        <div id="marcaField" style="display:none;">
            <label for="marca">Marca:</label>
            <input type="text" name="marca" id="marca">
        </div>
        <div id="tipoField" style="display:none;">
            <label for="tipo">Tipo de Barco:</label>
            <select name="tipo" id="tipo">
            <option value="Velero">Velero</option>
                <option value="Pesca">Pesca</option>
                <option value="Lancha">Lancha</option>
                <option value="Yate">Yate</option>
                <option value="Otro">Otro</option>
            </select>
        </div>
        <input type="submit" value="Filtrar">
    </form>
    <script>
        function toggleInputFields() {
            var criterio = document.getElementById("criterio").value;
            document.getElementById("marcaField").style.display = (criterio == 'marca') ? 'block' : 'none';
            document.getElementById("tipoField").style.display = (criterio == 'tipo') ? 'block' : 'none';
        }
    </script>
</section>