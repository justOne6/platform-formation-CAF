<?php

session_start();

include_once('./require/constante.inc.php');

class Bdd
{
    // Si définition de constante dans le fichier : require/constante.inc.php utilisé les lignes ci-dessous. 
    // Pas actif de base, car un petit problème à été détecter avec la barre de recherche, de searchBar.php dans single-form-teach.php qui n'a pu être résolu.
    
    // private $host_name = HOST;
    // private $database = DATABASE;
    // private $user_name = USERNAME;
    // private $pass_word = PASSWORD;

    // Déclaration des paramètre de connexion pour la base de donnnées
    private $host_name = // nom de l'hôte de votre BDD
    private $database = // nom de la base de données utilisée
    private $user_name = // nom de l'utilisateur associé
    private $pass_word = // mot de passe lier à l'utilisateur

    // Déclaration des paramètres du serveur SMTP (envoi de mail)
    private $smtp_host = // hôte du serveur SMTP
    private $smtp_username = // nom utilisateur SMTP
    private $smtp_password = // mot de passe lier à l'utilisateur
    private $smtp_secure = 'tls'; // procédure sécurité du SMTP
    private $smtp_port = // port du SMTP

    // Db log 
    public function __construct()
    {
        try {
            $this->dbh = new PDO("mysql:dbname={$this->database};host={$this->host_name};charset=utf8", $this->user_name, $this->pass_word);
            // $this->dbh = new PDO("mysql:dbname=" . DATABASE . ";host=" . DATABASE . ";charset=utf8", USERNAME, PASSWORD); // en cas d'utilisation des constantes
        } catch (PDOException $e) {
            // Echec de la connexion
            echo 'Connexion échouée : ' . $e->getMessage();
            exit();
        }
    }

    // Data cleaner 
    public function dataCleaner($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Password generator
    public function pswd_generator()
    {
        $list = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890&@%-_';
        $pass = '';
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, strlen($list) - 1);
            $pass = $pass . $list[$n];
        }
        return $pass;
    }

    // Username generator 
    public function username_generator($lastname, $firstname)
    {
        // Generate username 
        $lettre = '';
        for ($i = 0; $i < strlen($lastname) / strlen($lastname) + 3; $i++) {
            $lettre = $lettre . $lastname[$i];
        }

        $username = $firstname[0] . $lettre;
        return $username;
    }

    // Test username 
    public function test_username($username)
    {
        $username = $username . '081';
        // Verifify if username is existing yet in db
        $res = $this->dbh->prepare('select * from utilisateurss where nom = ' . $username);
        $verify = $res->execute();
        $verify = $res->fetchAll();

        if (count($verify) != 0) {
            return False;
        } else {
            return $username;
        }
    }

    // Registration : 
    public function registration($lastname, $email, $firstname, $profil)
    {
        $succees = False;

        while ($succees === False) {
            // Generate username
            $username = $this->username_generator($lastname, $firstname);
            $username = $this->test_username($username);
            if ($username != false) {
                $succees = True;
                break;
            }
        }

        // Generate Password
        $pswd = $this->pswd_generator();

        // Clear data and hash pswd
        $username = $this->dataCleaner($username);
        $username = strtolower($username);
        $email = $this->dataCleaner($email);
        $email = strtolower($email);
        $pswd = $this->dataCleaner($pswd);
        echo $pswd;
        $pswd_hash = password_hash($pswd, PASSWORD_BCRYPT);
        $profil = strtolower($profil);
        $profil = $this->dataCleaner($profil);

        // verify if email is existing yet in db
        $res = $this->dbh->prepare('select count(*) from utilisateurs where mail = ' . $email);
        $verify = $res->execute();

        if ($verify != 0) {
            exit("L'adresse email est déjà utilisée.");
        } else {
            // Insert
            $res = $this->dbh->prepare('insert into utilisateurs(nom, empreinte, mail, profile) values (?,?,?,?)');

            $res->execute(array(
                $username,
                $pswd_hash,
                $email,
                $profil,
            ));

            $this->mailFirstLogin($email, $firstname, $pswd, $username);
        }
        echo '<meta http-equiv="refresh" content="1; url=' . $_SESSION['url'] . '/admin.php">';
    }

    // Login
    public function login($username, $pswd)
    {
        $username = $this->dataCleaner($username);
        $pswd = $this->dataCleaner($pswd);
        // Select
        $res = $this->dbh->prepare('select * from utilisateurs where nom = ?');
        $res->execute([$username]);
        $lignes = $res->fetch();

        if (count($lignes) > 0) { // If exist 1 row 
            if (password_verify($pswd, $lignes['empreinte'])) { // If the password correspond with the db hash 
                $_SESSION['user'] = array(
                    'id' => $lignes['id'],
                    'username' => $lignes['nom'],
                    'email' => $lignes['mail'],
                    'profile' => $lignes['profile'],
                    'state' => True
                );

                if ($_SESSION['user']['profile'] == 'admin') {
                    return 'admin';
                } elseif ($_SESSION['user']['profile'] == 'student') {
                    return 'student';
                } else {
                    return 'teacher';
                }
            } else {
                exit("<h2 style='color : red; text-align : center;'>Erreur avec les identifiants utilisées.");
            }
        } else {
            exit("<h2 style='color : red; text-align : center;'>Erreur avec les identifiants utilisées.");
        }
    }

    // Unlog
    public function unlog()
    {
        $_SESSION['user']['state'] = False;
        session_destroy();
        header('Location: ./index.php');
        die();
    }

    // Show
    public function show_users($arg)
    {
        $req = $this->dbh->prepare('select * from utilisateurs where profile = ?');
        $req->execute([$arg]);

        $lignes = $req->fetchAll();
        echo "<div>";
        echo "<table>";
        echo "<thead>";
        echo "<tr>";
        echo "<th></th>";
        echo "<th>Nom d'utilisateur</th>";
        echo "<th>Adresse e-mail</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        echo "<tr>";
        echo "<form action='' method='post'>";
        foreach ($lignes as $key => $value) {
            $id = $lignes[$key]['id'];
            echo "<th><input type=checkbox name=id[] class=utilisateur value='$id'></th>";
            echo "<th>" . $lignes[$key]['nom'] . "</th>";
            echo "<th>" . $lignes[$key]['mail'] . "</th>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        echo "<div class=button-users>";
        echo "<div id=users-supp>";
        echo "<input class=suppression type=submit name=suppression value=Supprimer>";
        echo "</div>";
        echo "<div id=users-updt>";
        echo "<input class=modifier type=submit name=modifier value=Modifier>";
        echo "</div>";
        echo "</form>";
        echo "</div>";
    }

    // Delete 
    public function delete($arg)
    {
        $req = 'delete FROM utilisateurs where id = :id';
        $res = $this->dbh->prepare($req);
        for ($i = 0; $i < count($arg); $i++) {

            $res->execute([":id" => $arg[$i]]);
        }
        echo '<meta http-equiv="refresh" content="1; url=' . $_SESSION['url'] . '/admin.php">';
    }

    // Update 
    public function update($arg)
    {
        $req = 'SELECT * FROM utilisateurs where id = :id';
        $res = $this->dbh->prepare($req);
        $res->execute([":id" => $arg]);
        $lignes = $res->fetch();

        $_SESSION['data'] = array(

            'id' => $lignes['id'],
            'email' => $lignes['mail'],
            'username' => $lignes['nom'],
            'pswd' => $lignes['empreinte'],

        );
        // $_SESSION['data'] = $data;


        // formulaire qui va entrer les nouvelles informations dans le tableau data
        echo "<div id=update>";
        echo "<form action='' method='post'>";
        echo $lignes['mail'] . ": <input class='inputenter' type='text' name='new_data[]' placeholder='Adresse e-mail'/><br>";
        echo $lignes['nom'] . ": <input class='inputenter' type='text' name='new_data[]' placeholder='identifiant'/><br>";
        echo "<input class='inputenter' type='password' name='new_data[]' placeholder='Mot de passe'/><br>";
        echo "<input class='modifierInfo' type='submit' name='modifierInfo' value='Modifier'>";
        echo "</form>";
        echo "</div>";
    }

    public function update_trt($data)
    {
        $req = "update utilisateurs set mail = :new_email, empreinte = :pswd, nom = :login where id = :id";
        $res = $this->dbh->prepare($req);
        $res->execute([':new_email' => $data['email'], ':pswd' => $data['pswd'], ':login' => $data['username'], ':id' => $data['id']]);
    }

    // Insert documentation
    public function new_formation($data, $link)
    {
        for ($i = 0; $i < count($data); $i++) {
            $data[$i] = $this->dataCleaner($data[$i]);
        }

        $req = "SELECT * from formations where title = :title and category = :category";
        $res = $this->dbh->prepare($req);

        $res->execute(array(
            'title' => $data[0],
            'category' => $data[2],
        ));

        $ligne = $res->fetch();

        if (isset($lignes)) {
            exit();
        } else {
            $req = "INSERT into formations(title, descript,category, subcategory) values (?,?,?,?)";
            $res = $this->dbh->prepare($req);

            $res->execute(array(
                $data[0],
                $data[1],
                $data[2],
                $data[3],
            ));

            if (isset($link)) {

                $req = "SELECT id_formation from formations where title = :title AND category = :category";
                $res = $this->dbh->prepare($req);
                $res->execute(array(
                    'title' => $data[0],
                    'category' => $data[2]
                ));
                $ligne = $res->fetch();

                if ($ligne) {
                    $id = $ligne['id_formation'];
                    // Insert all link 
                    for ($i = 0; $i < count($link); $i++) {
                        $url = $link[$i]['url'];
                        $type = $link[$i]['t_type'];
                        $req = "INSERT into tools_form(id_form, link, t_type) values (?,?,?)";
                        $res = $this->dbh->prepare($req);
                        $res->execute(array(
                            $id,
                            $url,
                            $type
                        ));
                    }
                    echo '<meta http-equiv="refresh" content="1; url=' . $_SESSION['url'] . 'teach-acceuil.php">';
                }
            }
        }
    }

    public function select_category()
    {
        try {
            $req = 'SELECT * from category c left join subcategory s on c.id_category = s.id_category_sub';
            $res = $this->dbh->prepare($req);
            $res->execute();
            $lignes = $res->fetchAll();

            foreach ($lignes as $key => $value) {
                if (isset($lignes[$key]['title-subcategory'])) {
                    echo "<option value=" . $lignes[$key]['id_category'] . '/' . $lignes[$key]['id_sub'] . ">" . ucfirst($lignes[$key]['title-category']) . " : " . ucfirst($lignes[$key]['title-subcategory']) . "</option>";
                } else {
                    echo "<option value=" . $lignes[$key]['id_category'] . '/' . $lignes[$key]['id_sub'] . "><bold>" . ucfirst($lignes[$key]['title-category']) . "</bold></option>";
                }
            }
        } catch (PDOException $e) {
            // Echec de la connexion
            echo 'Connexion échouée : ' . $e->getMessage();
        }
    }

    public function show_formation()
    {
        $req = "SELECT * from formations";
        $res = $this->dbh->prepare($req);

        $res->execute();
        $lignes = $res->fetchAll();

        for ($i = 0; $i < count($lignes); $i++) {
            $id = $lignes[$i]['id_formation'];
            echo "<li style='list-style-image : url(https://img.icons8.com/color/48/000000/books.png); display : list-item;border-bottom : 1px solid;border-color: rgba(0, 0, 0, 0.432); padding : 5px;'><a href=single-form-teach.php?id-form=$id>" . $lignes[$i]['title'] . "</a></li>";
        }
    }
    // Send mail to users with theses login 
    function mailFirstLogin($to, $name, $pswd, $username)
    {
        require './phpmail/PHPMailerAutoload.php';
        $mail = new PHPMailer;

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = $this->smtp_host;  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = $this->smtp_username;                // SMTP username
        $mail->Password = $this->smtp_password;                          // SMTP password
        $mail->SMTPSecure = $this->smtp_secure;                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = $this->smtp_port;                                    // TCP port to connect to

        $mail->setFrom($this->smtp_username, 'Administrateur système');
        $mail->addAddress($to);     // Add a recipient
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->CharSet = "UTF-8";

        $mail->Subject = 'Inscription effectuée';
        $mail->Body    = " <h5>Bonjour " . $name . " votre inscription sur la plateforme de formation de la Caf est finalisée</h5>
    <span>Veuillez trouvez ci-dessous vos identifiants pour votre première connexion :</span>
    <ul>
        <li>Nom d'utilisateur : " . $username . "</li>
        <li>Mot de passe : " . $pswd . "</li>
    </ul>
    <span>Pensez à modifier votre mot de passe lors de <a href=".$_SESSION['url']."new-password.php?action=0>votre première connexion</a></span>";
        if (!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        }
    }

    public function firstConnexion($username, $pswd)
    {
        $username = $this->dataCleaner($username);
        $pswd = $this->dataCleaner($pswd);
        // Select
        $res = $this->dbh->prepare('select * from utilisateurs where nom = ?');
        $res->execute([$username]);
        $lignes = $res->fetch();

        if ($lignes) { // If exist 1 row 
            if (password_verify($pswd, $lignes['empreinte'])) {
                $_SESSION['user'] = array(
                    'id' => $lignes['id'],
                    'username' => $lignes['nom'],
                    'email' => $lignes['mail'],
                    'profile' => $lignes['profile'],
                    'state' => True
                );
                echo "<div id=connexion>";
                echo "<form method=POST id=nouveau-form>";
                echo "<h2>Changer votre mot de passe</h2>";
                echo "<label class='label' for=''>Nom d'utilisateur</label>";
                echo "<input type=text name='username-2' value=" . $username . " readonly='readonly'>";
                echo "<label class='label' for=''>Nouveau mot de passe</label>";
                echo "<input type=password name=pswd>";
                echo "<label class='label' for=''>Confirmer mot de passe</label>";
                echo "<input type=password name=pswd-confirm>";
                echo "<input type=submit value=Valider name='new-pswd'>";
                echo "</form>";
                echo "</div>";
            } else {
                exit("<div id=connexion><h2 style='color : red; text-align : center;'>Erreurs avec l'identifant et le mot de passe.</h2>");
            }
        }
    }

    public function updatePswd($id, $pswd)
    {
        $pswd = $this->dataCleaner($pswd);
        $pswd = password_hash($pswd, PASSWORD_BCRYPT);
        $res = $this->dbh->prepare('update utilisateurs set empreinte = :pswd where id = :id');
        if ($res->execute([':pswd' => $pswd, ':id' => $id])) {
            echo '<meta http-equiv="refresh" content="1; url=' . $_SESSION['url'] . '">';
        } else {
            echo 'error update';
        }
    }

    public function get_formation_data($id)
    {
        $id = $this->dataCleaner($id);
        $res = $this->dbh->prepare('select * from formations f inner join category c on f.category = c.id_category INNER join subcategory s on f.category = s.id_sub where f.id_formation = ?');
        $res->execute([$id]);
        $lignes = $res->fetch();

        $data = array(
            'id' => $lignes['id_formation'],
            'title' => $lignes['title'],
            'description' => $lignes['descript'],
            'title-cat' => $lignes['title-category'],
            'title-sub' => $lignes['title-subcategory']
        );

        return $data;
    }

    public function get_formation_tools($id, $type)
    {
        $id = $this->dataCleaner($id);
        $res = $this->dbh->prepare('select * from formations f inner join tools_form t on f.id_formation = t.id_form where f.id_formation = ?');
        $res->execute([$id]);
        $lignes = $res->fetchAll();
        $content = array();

        for ($i = 0; $i < count($lignes); $i++) {
            if ($type === 'img') {
                if ($lignes[$i]['t_type'] === 'image/jpeg' || $lignes[$i]['t_type'] === 'image/png') {
                        $row = array(
                            'id' => $lignes[$i]['id_tools'],
                            'link' => $lignes[$i]['link']
                        );
                        array_push($content, $row);
                }
            } elseif ($type === 'pdf') {
                if ($lignes[$i]['t_type'] === 'application/pdf') {
                        $row = array(
                            'id' => $lignes[$i]['id_tools'],
                            'link' => $lignes[$i]['link']
                        );
                        array_push($content, $row);
                    }
                }
                // if($data['type'] === 'image/jpeg' || $data['type'] === 'image/png'){
                //     echo "<img src=".$data['link']." alt=''>";
                // }
            }

        if (isset($content)) {
            echo '<section id=' . $type . ' class="tab">';
            for ($i = 0; $i < count($content); $i++) {
                // $dl = $_SESSION['url'];
                // $dl = $dl / $content[$i];
                echo '<div class=card>';
                echo "<img src='".$content[$i]['link']."' alt=''>";
                echo "<a href='".$content[$i]['link']."' download>Télécharger le fichier</a>";
                echo '</div>';
            }
            echo '</section>';
        }
    }

    public function get_updatable_tools($id, $type)
    {
        $id = $this->dataCleaner($id);
        $res = $this->dbh->prepare('select * from formations f inner join tools_form t on f.id_formation = t.id_form where f.id_formation = ?');
        $res->execute([$id]);
        $lignes = $res->fetchAll();
        $content = array();

        for ($i = 0; $i < count($lignes); $i++) {
            if ($type === 'img') {
                if ($lignes[$i]['t_type'] === 'image/jpeg' || $lignes[$i]['t_type'] === 'image/png') {
                    $row = array(
                        'id' => $lignes[$i]['id_tools'],
                        'link' => $lignes[$i]['link']
                    );
                    array_push($content, $row);
                }
            } elseif ($type === 'pdf') {
                if ($lignes[$i]['t_type'] === 'application/pdf') {
                    $row = array(
                        'id' => $lignes[$i]['id_tools'],
                        'link' => $lignes[$i]['link']
                    );
                    array_push($content, $row);
                }
            }
        }

        if (isset($content)) {
            echo '<section id=' . $type . ' class="tab">';
            echo '<ul>';
            echo "<form id='list-tool' method='post'>";
            echo "<div id=users-btn>";
            echo "<input class=suppression type=submit name=suppression value=Supprimer>";
            echo "<button id=newFile>Nouveau fichier</button>";
            echo "</div>";
            for ($i = 0; $i < count($content); $i++) {
                echo "<li><input type='checkbox' name='id[]' value=" . $content[$i]['id'] . "><a href='#'> " . $content[$i]['link'] . "</a></li>";
            }
            echo "</form>";
            echo '</ul>';
            echo '</section>';
        }
    }

    public function supp_file($arg, $id_form)
    {
        for ($i = 0; $i < count($arg); $i++) {
            $id = $arg[$i];
            $res = $this->dbh->prepare('select link from tools_form where id_tools = ? ');
            $res->execute(array($id));
            $ligne = $res->fetch();
            unlink($ligne['link']);
            $res = $this->dbh->prepare('delete from tools_form where id_tools = ? ');
            $res->execute(array($id));
        }

        echo '<meta http-equiv="refresh" content="1; url=' . $_SESSION['url'] . '/single-form-teach.php?id-form=' . $id_form . '">';
    }

    public function upload_new_files($id_form, $link)
    {
        // Insert all link 
        for ($i = 0; $i < count($link); $i++) {
            $url = $link[$i]['url'];
            $type = $link[$i]['t_type'];
            $req = "INSERT into tools_form(id_form, link, t_type) values (?,?,?)";
            $res = $this->dbh->prepare($req);
            $res->execute(array(
                $id_form,
                $url,
                $type
            ));
        }
        echo '<meta http-equiv="refresh" content="1; url=' . $_SESSION['url'] . '/single-form-teach.php?id-form=' . $id_form . '">';
    }

    public function show_users_inscript($arg)
    {
        $req = $this->dbh->prepare("SELECT * from utilisateurs where id not in (SELECT id_student from is_sub where id_formation_sub = '" . $arg . "') and profile = 'student'");
        $req->execute([$arg]);

        $lignes = $req->fetchAll();

        foreach ($lignes as $key => $value) {
            $id = $lignes[$key]['id'];
            echo "<li><input type=checkbox name=id[] value='$id'><a href='' class='user-list-stud' data-username=" . $lignes[$key]['nom'] . "> " . $lignes[$key]['nom'] . "</a>" . "-" . " <a href='' class='user-list-stud' data-email=" . $lignes[$key]['mail'] . ">" . $lignes[$key]['mail'] . "</a></li>";
        }
    }

    public function filter($arg, $id)
    {
        $req = $this->dbh->prepare("SELECT * from utilisateurs where profile = 'student' and (nom like '" . $arg . "%' OR mail like '" . $arg . "%' or nom like '%" . $arg . "') and id not in (SELECT id_student from is_sub where id_formation_sub = '" . $id . "')");
        $req->execute();

        $lignes = $req->fetchAll();
        foreach ($lignes as $key => $value) {
            echo "<li>";
            $id = $lignes[$key]['id'];
            echo "<input type=checkbox name=id[] value='$id'>";
            echo "<a href='' id='user-list' data-username=" . $lignes[$key]['nom'] . "> " . $lignes[$key]['nom'] . ' - ' . $lignes[$key]['mail'] . "</a>";
            echo "</li>";
        }
    }

    public function update_info_form($data, $id_form)
    {
        for ($i = 0; $i < count($data); $i++) {
            $data[$i] = $this->dataCleaner($data[$i]);
        }

        $res = $this->dbh->prepare('update formations set title = :title, descript = :descript where id_formation = :id');
        $res->execute([':id' => $id_form, ':title' => $data[0], ':descript' => $data[1]]);
        echo '<meta http-equiv="refresh" content="1; url=' . $_SESSION['url'] . '/single-form-teach.php?id-form=' . $id_form . '">';
    }

    public function sub_users($data, $id_form)
    {
        for ($i = 0; $i < count($data); $i++) {
            $data[$i] = $this->dataCleaner($data[$i]);
        }
        $date_sub = date('Y-m-d');
        for ($i = 0; $i < count($data); $i++) {
            $req = $this->dbh->prepare("SELECT * from utilisateurs where id = '" . $data[$i] . "' IN (SELECT id_student from is_sub where id_formation_sub = '" . $id_form . "')");
            $req->execute();
            $ligne = $req->fetchAll();
            if (count($ligne) > 0) {
                echo '<meta http-equiv="refresh" content="1; url=' . $_SESSION['url'] . '/single-form-teach.php?id-form=' . $id_form . '">';
                exit();
            } else {
                $res = $this->dbh->prepare('INSERT into is_sub(id_student, id_formation_sub, state_sub, date_inscription) values (?,?,?,?)');
                $res->execute(array(
                    $data[$i],
                    $id_form,
                    'inscrit',
                    $date_sub,
                ));
            }
        }
        echo '<meta http-equiv="refresh" content="1; url=' . $_SESSION['url'] . '/single-form-teach.php?id-form=' . $id_form . '">';
    }

    public function getSubUser($arg)
    {
        $req = $this->dbh->prepare("SELECT id_sub, nom, mail from utilisateurs u inner join is_sub i on u.id = i.id_student join formations f on i.id_formation_sub = f.id_formation where f.id_formation = '" . $arg . "%'");
        $req->execute();

        $lignes = $req->fetchAll();
        foreach ($lignes as $key => $value) {
            echo "<li>";
            $id = $lignes[$key]['id_sub'];
            echo "<input type=checkbox name=id[] value='$id'>";
            echo ' ';
            echo "<a href='' class='user-list' data-username=" . $lignes[$key]['nom'] . "> " . $lignes[$key]['nom'] . "</a>";
            echo ' - ';
            echo "<a href='' class='user-list' data-email=" . $lignes[$key]['mail'] . ">" . $lignes[$key]['mail'] . "</a>";
            echo "</li>";
        }
    }

    public function unsub($id_form, $arg)
    {
        try {
            $req = 'DELETE FROM is_sub where id_sub = ?';
            $res = $this->dbh->prepare($req);
            for ($i = 0; $i < count($arg); $i++) {
                $res->execute(array(
                    $arg[$i]
                ));
            }
        } catch (PDOException $e) {
            // Echec de la connexion
            echo 'Connexion échouée : ' . $e->getMessage();
            exit();
        }
        echo '<meta http-equiv="refresh" content="1; url=' . $_SESSION['url'] . '/single-form-teach.php?id-form=' . $id_form . '">';
    }

    // Send mail to users with theses login 
    function forgotPassword($arg)
    {
        $arg = $this->dataCleaner($arg);
        $req = $this->dbh->prepare("SELECT id, mail, nom from utilisateurs where mail = '" . $arg . "' or nom = '" . $arg . "'");
        $req->execute();
        $ligne = $req->fetch();

        if (count($ligne) === 0) {
            die();
        } else {

            $data = array(
                $ligne['id'],
                $ligne['mail'],
                $ligne['nom']
            );
            $pswd = $this->pswd_generator();
            require './phpmail/PHPMailerAutoload.php';
            $mail = new PHPMailer;

            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = $this->smtp_host;  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = $this->smtp_username;                // SMTP username
            $mail->Password = $this->smtp_password;                          // SMTP password
            $mail->SMTPSecure = $this->smtp_secure;                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = $this->smtp_port;                                    // TCP port to connect to

        $mail->setFrom($this->smtp_username, 'Administrateur système');
            $mail->addAddress($data[1]);     // Add a recipient
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->CharSet = "UTF-8";

            $mail->Subject = 'Modification de mot de passe effectuée';
            $mail->Body    = " <h5>Chère, cher, " . $data[2] . "</h5>
    <span>Nous avons reçu une requête afin de réinitialiser votre mot de passe. Voici ci-dessous votre nouveau mot de passe : </span>
    <ul>
        <li>Nom d'utilisateur : " . $data[2] . "</li>
        <li>Nouveau mot de passe : " .  $pswd. "</li>
    </ul>
    <span>Vous devrez le modifier lors de <a href=".$_SESSION['url']."new-password.php?action=1>votre prochaine connexion</a>.</span>";
            if (!$mail->send()) {
                echo 'Message could not be sent.';
                echo 'Mailer Error: ' . $mail->ErrorInfo;
            }

            $this->updatePswd($ligne['id'], $pswd);
        }


    }

    public function user_formation($id_user)
    {
        $req = $this->dbh->prepare("SELECT * from formations f where f.id_formation not in (SELECT id_formation_sub from is_sub where id_student = '" . $id_user . "')");
        $req->execute();

        $lignes = $req->fetchAll();

        foreach ($lignes as $key => $value) {
            $id = $lignes[$key]['id_formation'];
            echo "<li style='list-style-image : url(https://img.icons8.com/color/48/000000/books.png); display : list-item; border-bottom : 1px solid;border-color: rgba(0, 0, 0, 0.432); padding : 15px; '><a href='single-form.php?id-form=$id' style='margin-bottom : 5px; class='user-list-stud' data-title=" . $lignes[$key]['title'] . "> " . $lignes[$key]['title'] . "</a></li>";
        }
    }

    public function user_sub_formation($id)
    {
        $req = $this->dbh->prepare("SELECT * from formations f where f.id_formation in (SELECT id_formation_sub from is_sub where id_student = '" . $id . "')");
        $req->execute();

        $lignes = $req->fetchAll();
       
        foreach ($lignes as $key => $value) {
            $id = $lignes[$key]['id_formation'];
            echo "<li style='list-style-image : url(https://img.icons8.com/color/48/000000/books.png); display : list-item; border-bottom : 1px solid;border-color: rgba(0, 0, 0, 0.432); padding : 15px; '><a href='single-form.php?id-form=$id' class='user-list-stud' data-title=" . $lignes[$key]['title'] . "> " . $lignes[$key]['title'] . "</a></li>";
        }
       
    }

    public function update_sub_state($id_user, $id_form, $new_state, $id_sub){
        $req = $this->dbh->prepare("update is_sub set state_sub = ? where id_student = ? and id_formation_sub = ?");
        $req->execute(array($new_state, $id_user, $id_form));

        if($new_state === 'finis'){
            $this->delete_sub($id_sub, $id_user);
        } else {
            echo '<meta http-equiv="refresh" content="1; url=' . $_SESSION['url'] . '/student-acceuil.php?id=' . $id_user . '">'; 
        }
    }

    public function get_sub_info($id_user, $id_form){
        $req = $this->dbh->prepare("SELECT * from is_sub where id_student = {$id_user} and id_formation_sub = {$id_form}");
        $req->execute();

        $ligne = $req->fetch();

        return $ligne;
    }

    public function delete_sub($id_sub, $id_user)
    {
        $req = $this->dbh->prepare("DELETE FROM is_sub where id_sub = ?");
        $req->execute(array($id_sub));
        echo '<meta http-equiv="refresh" content="1; url=' . $_SESSION['url'] . '/student-acceuil.php?id=' . $id_user . '">'; 
    }

    public function delete_formation($id_form)
    {
        $req = $this->dbh->prepare("DELETE FROM formations where id_formation = ?");
        $req->execute(array($id_form));
        echo '<meta http-equiv="refresh" content="1; url=' . $_SESSION['url'] . 'teach-accueil.php">'; 
    }
}
