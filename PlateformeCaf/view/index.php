<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="./view/css/index.css">
</head>

<body>
    <div class="col-right">
        <div class="container-r">
            <img src="./data/Ardennes.jpg" alt="" id="logo-index">
            <hr>
            <p>
                Bienvenue sur la plateforme de formation de la C.A.F
            </p>
            <a href="https://www.caf.fr">Vous souhaitez vous diriger sur le site de la Caf.fr ?</a>
            <hr>
        </div>
    </div>
    <div class="col-left">
        <div class="form-connection">
            <form action="" method="post" class="form">
                <label class='label' for="">Nom d'utilisateur</label>
                <input type="text" name="username" id="" autocomplete="on" required>
                <label for="">Mot de passe</label>
                <input type="password" name="password" id="" autocomplete="on" required>
                <hr>
                <div class="button-conn">
                    <input type="submit" value="Connexion" name="submitform">
                </div>
            </form>
            <div class="link-conn">
                <a href="<?= $_SESSION['url'] ?>new-password.php?action=0">Première connexion ?</a>
                <hr>
                <a href="<?= $_SESSION['url'] ?>ask-pswd.php">Un problème avec votre identification ?</a>
            </div>
        </div>
    </div>
</body>

</html>