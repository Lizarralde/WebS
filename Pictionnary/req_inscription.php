<?php

// récupérer les éléments du formulaire  
// et se protéger contre l'injection MySQL (plus de détails ici: http://us.php.net/mysql_real_escape_string)  
$email=stripslashes($_POST['email']);
$password=stripslashes($_POST['password']);
$nom=stripslashes($_POST['nom']);
$prenom=stripslashes($_POST['prenom']);
$tel=stripslashes($_POST['tel']);
$website=stripslashes($_POST['website']);
$sexe='';
if (array_key_exists('sexe',$_POST)) {
    $sexe=stripslashes($_POST['sexe']);
}
$birthdate=stripslashes($_POST['birthdate']);
$ville=stripslashes($_POST['ville']);
$taille=stripslashes($_POST['taille']);
$couleur=stripslashes($_POST['couleur']);
$profilepic=stripslashes($_POST['profilepic']);

try {
    // Connect to server and select database.  
    $dbh = new PDO('mysql:host=localhost;dbname=pictionnary', 'test', 'test');

    // Vérifier si un utilisateur avec cette adresse email existe dans la table.  
    // En SQL: sélectionner tous les tuples de la table USERS tels que l'email est égal à $email.  
    $sql = $dbh->query("SELECT * FROM USERS U WHERE U.EMAIL = '" . $email . "'");
    if ($sql->rowCount() >= 1) {
        $temp = urlencode("un utilisateur avec cette adresse email existe déjà");
        foreach ($_POST as $key => $value) {
            $temp = $temp."&".$key."=".$value;
        }
        header("Location: inscription.php?erreur=".$temp);
    }
    else {
        // Tenter d'inscrire l'utilisateur dans la base  
        $sql = $dbh->prepare("INSERT INTO users (email, password, nom, prenom, tel, website, sexe, birthdate, ville, taille, couleur, profilepic) "
            . "VALUES (:email, :password, :nom, :prenom, :tel, :website, :sexe, :birthdate, :ville, :taille, :couleur, :profilepic)");
        $sql->bindValue(":email", $email, PDO::PARAM_STR);
        // de même, lier la valeur pour le mot de passe
        $sql->bindValue(":password", $password, PDO::PARAM_STR);
        // lier la valeur pour le nom, attention le nom peut être nul, il faut alors lier avec NULL, ou DEFAULT
        $sql->bindValue(":nom", $nom, PDO::PARAM_STR);
        // idem pour le prenom, tel, website, birthdate, ville, taille, profilepic
        // n.b., notez: birthdate est au bon format ici, ce serait pas le cas pour un SGBD Oracle par exemple
        $sql->bindValue(":prenom", $prenom, PDO::PARAM_STR);
        $sql->bindValue(":tel", $tel, PDO::PARAM_STR);
        $sql->bindValue(":website", $website, PDO::PARAM_STR);
        $sql->bindValue(":birthdate", $birthdate, PDO::PARAM_STR);
        $sql->bindValue(":ville", $ville, PDO::PARAM_STR);
        $sql->bindValue(":taille", $taille, PDO::PARAM_INT);
        $sql->bindValue(":profilepic", $profilepic, PDO::PARAM_LOB);
        // idem pour la couleur, attention au format ici (7 caractères, 6 caractères attendus seulement)
        // idem pour le prenom, tel, website
        $sql->bindValue(":couleur", $couleur, PDO::PARAM_STR);
        // idem pour le sexe, attention il faut être sûr que c'est bien 'H', 'F', ou ''
        $sql->bindValue(":sexe", $sexe, PDO::PARAM_STR);

        // on tente d'exécuter la requête SQL, si la méthode renvoie faux alors une erreur a été rencontrée.  
        if (!$sql->execute()) {
            echo "PDO::errorInfo():<br/>";
            $err = $sql->errorInfo();
            print_r($err);
        } else {

            session_start();

            // ensuite on requête à nouveau la base pour l'utilisateur qui vient d'être inscrit, et   
            $sql = $dbh->query("SELECT u.id, u.email, u.nom, u.prenom, u.couleur, u.profilepic FROM USERS u WHERE u.email='".$email."'");
            if ($sql->rowCount()<1) {
                header("Location: main.php?erreur=".urlencode("un problème est survenu"));
            }
            else {
                // on récupère la ligne qui nous intéresse avec $sql->fetch(),   
                // et on enregistre les données dans la session avec $_SESSION["..."]=...
                $result = $sql->fetch(PDO::FETCH_ASSOC);

                $_SESSION["email"] = $result["email"];
                $_SESSION["password"] = $result["password"];
                $_SESSION["nom"] = $result["nom"];
                $_SESSION["prenom"] = $result["prenom"];
                $_SESSION["tel"] = $result["tel"];
                $_SESSION["website"] = $result["website"];
                $_SESSION["sexe"] = $result["sexe"];
                $_SESSION["birthdate"] = $result["birthdate"];
                $_SESSION["ville"] = $result["ville"];
                $_SESSION["taille"] = $result["taille"];
                $_SESSION["couleur"] = $result["couleur"];
                $_SESSION["profilepic"] = $result["profilepic"];
            }

            header("Location: main.php");
        }
        $dbh = null;
    }  
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    $dbh = null;
    die();
}
?>