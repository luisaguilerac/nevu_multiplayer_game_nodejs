<!doctype html> 
<html lang="en"> 
    <head> 
        <meta charset="UTF-8" />
        <title>Nevu - Multiplayer Game</title>
        
        <style type="text/css">
            body {
                margin: 0;
            }
        </style>

        <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
        <link href="style/bootstrap.min.css" rel="stylesheet">
        <script src="js/bootstrap.min.js"></script>
    </head>
    <body>
        <br><br>
    <center>
        <img style="width: 200px;" src="assets/nevu.png">
        <h4 style="color: #009999;"><B>SELECCIONA TU AVATAR</B></h4>
        <table style="width: 600px;" class="table table-bordered">
            <tr>
                <td colspan="4">
                    <input id="nombre" placeholder="Ingresa un nickname"type="text" class="form-control">
                </td>
            </tr>
            <tr>
                <td><img onclick="redireccionar(0)" src="assets/0.png"></td>
                <td><img onclick="redireccionar(1)" src="assets/1.png"></td>
                <td><img onclick="redireccionar(2)" src="assets/2.png"></td>
                <td><img onclick="redireccionar(3)" src="assets/3.png"></td>
            </tr>
            <tr>
                <td><img onclick="redireccionar(4)" src="assets/4.png"></td>
                <td><img onclick="redireccionar(5)" src="assets/5.png"></td>
                <td><img onclick="redireccionar(6)" src="assets/6.png"></td>
                <td><img onclick="redireccionar(7)" src="assets/7.png"></td>
            </tr>

        </table>
    </center>
    <script>
        function redireccionar(tipo) {
        if($("#nombre").val() != ""){
             window.location = "nevu.php?tipo="+tipo+"&nombre="+$("#nombre").val();
        }else{
            alert("Debes ingresar un nickname");
        }
        
        }
    </script>

</body>
</html>