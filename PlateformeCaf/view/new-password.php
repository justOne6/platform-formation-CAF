<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./view/css/first-connexion.css">
    <title>Première connenxion</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<body>
  
    <div id="connexion" class="toDisable">
        <form action="" method="POST" id="form-1">
            <h2>Modification de mot de passe</h2>
        <label class='label' for="">Nom d'utilisateur</label>
            <input type="text" name='username'>
            <label for="">Mot de passe</label>
            <input type="password" name="pswd_1">
            <input type="submit" id='nouveau' name="connect" value="Connexion">
        </form>
    </div>
    <script>
        $('#nouveau').click(() => {
           $('.toDisable').css('display', 'none');
        }) ;

        $(document).ready(() => {
            if($('#nouveau-form').length > 0){
                $('.toDisable').css('display', 'none');
            }
        })
    </script>
</body>
</html>