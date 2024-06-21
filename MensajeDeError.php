<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public\error.css">
    <link rel="stylesheet" href="public\Style.css">
</head>
<body>
    <div id="error-message" class="error-message" style="display:none">
    <div style="background-color:lavender ;position:absolute; height:30%; width:100%;text-align:center; top:30%;border: 2px;">
            <p id="error-text"></p>
            <button id="botonCerrarError" style="position:absolute; bottom:5%; width:50px; height:50px; background-color:lightblue; border-radius:5px;border:solid, 1px; border-color:black">Ok.</button>
    </div>
    </div>
</body>
</html>

<script>
    function showError(message) {
    var errorMessage = document.getElementById('error-message');
    var errorText = document.getElementById('error-text');
    errorText.textContent = message;
    errorMessage.style.display = 'block';
    }
    document.getElementById("botonCerrarError").onclick= function cerrarError(){
    var errorMessage = document.getElementById('error-message');
    errorMessage.style.display = 'none';
    }
</script>