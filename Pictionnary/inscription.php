<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" media="screen" href="css/styles.css" >
    <meta charset=utf-8 />
    <title>Pictionnary - Inscription</title>
    <script>
        function validateMdp2() {
            var mdp1 = document.getElementById('mdp1');
            var mdp2 = document.getElementById('mdp2');
            if (mdp1.validity.valid && (mdp1.value == mdp2.value)) {
                // ici on supprime le message d'erreur personnalisé, et du coup mdp2 devient valide.
                document.getElementById('mdp2').setCustomValidity('');
            } else {
                // ici on ajoute un message d'erreur personnalisé, et du coup mdp2 devient invalide.
                document.getElementById('mdp2').setCustomValidity('Les mots de passes doivent être égaux.');
            }
        }

         function computeAge() {
            try{
                document.getElementById("age").value = Math.floor((Date.now() - Date.parse(document.getElementById("birthdate").valueAsDate)) / (365.25 * 24 * 60 * 60 * 1000));
            } catch(e) {
                document.getElementById("age").value = '';
            }
        }

        function loadProfilePic(e) {
            // on récupère le canvas où on affichera l'image
            var canvas = document.getElementById("preview");
            var ctx = canvas.getContext("2d");
            // on réinitialise le canvas: on l'efface, et déclare sa largeur et hauteur à 0
            ctx.setFillColor("white");
            ctx.fillRect(0,0,canvas.width,canvas.height);
            canvas.width=0;
            canvas.height=0;
            // on récupérer le fichier: le premier (et seul dans ce cas là) de la liste
            var file = document.getElementById("profilepicfile").files[0];
            // l'élément img va servir à stocker l'image temporairement
            var img = document.createElement("img");
            // l'objet de type FileReader nous permet de lire les données du fichier.
            var reader = new FileReader();
            // on prépare la fonction callback qui sera appelée lorsque l'image sera chargée
            reader.onload = function(e) {
                //on vérifie qu'on a bien téléchargé une image, grâce au mime type
                if (!file.type.match(/image.*/)) {
                    // le fichier choisi n'est pas une image: le champs profilepicfile est invalide, et on supprime sa valeur
                    document.getElementById("profilepicfile").setCustomValidity("Il faut télécharger une image.");
                    document.getElementById("profilepicfile").value = "";
                }
                else {
                    // le callback sera appelé par la méthode getAsDataURL, donc le paramètre de callback e est une url qui contient
                    // les données de l'image. On modifie donc la source de l'image pour qu'elle soit égale à cette url
                    // on aurait fait différemment si on appelait une autre méthode que getAsDataURL.
                    img.src = e.target.result;
                    // le champs profilepicfile est valide
                    document.getElementById("profilepicfile").setCustomValidity("");
                    var MAX_WIDTH = 96;
                    var MAX_HEIGHT = 96;
                    var width = img.width;
                    var height = img.height;

                    // A FAIRE: si on garde les deux lignes suivantes, on rétrécit l'image mais elle sera déformée
                    // Vous devez supprimer ces lignes, et modifier width et height pour:
                    //    - garder les proportions,
                    //    - et que le maximum de width et height soit égal à 96
                    var max = width > height ? width : height;
                    width = width * MAX_WIDTH / max;
                    height = height * MAX_HEIGHT / max;

                    canvas.width = width;
                    canvas.height = height;
                    // on dessine l'image dans le canvas à la position 0,0 (en haut à gauche)
                    // et avec une largeur de width et une hauteur de height
                    ctx.drawImage(img, 0, 0, width, height);
                    // on exporte le contenu du canvas (l'image redimensionnée) sous la forme d'une data url
                    var dataurl = canvas.toDataURL("image/png");
                    // on donne finalement cette dataurl comme valeur au champs profilepic
                    document.getElementById("profilepic").value = dataurl;
                };
            }
            // on charge l'image pour de vrai, lorsque ce sera terminé le callback loadProfilePic sera appelé.
            reader.readAsDataURL(file);
        }

        function afficherTaille() {
            var taille = document.getElementById("taille");
            var tailleSpan = document.getElementById("tailleSpan");

            tailleSpan.innerHTML = taille.value + " m";
        }

        window.fbAsyncInit =function(){
            FB.init({
                appId      :'376974609148332',
                status     :true,// vérifier le statut de connexion
                cookie     :true,// autoriser les cookies pour permettre au serveur (et le SDK PHP) d'accéder à la session
                xfbml      :true// analyser le XFBML (déprécié par Facebook, c.f., https://developers.facebook.com/blog/post/568/)
            });

            // Ici on s'abonne à l'évèement JavaScript auth.authResponseChange. Cet évènement est généré pour tout
            // changement dans l'authentification, comme la connexion, la déconnexion, ou le rafraîchissement de la session.
            // Donc lorsqu'un utilisateur déjà connecté tente se se connecter à nouveau, le cas correct ci-dessous sera géré
            FB.Event.subscribe('auth.authResponseChange',function(response){
                // Est-ce que l'utilisateur est connecté au moment où l'évènement est généré ?
                if(response.status ==='connected'){
                    console.log("Y'a quelqu'un");
                    // c.f. l'objet response passé en paramète du callback est un objet JSON décrit après ce code.
                    testAPI();
                } else if (response.status ==='not_authorized'){
                    console.log("Y'a quelqu'un, mais il n'est pas connecté à l'application");
                    // Dans ce cas, la personne est loguée Facebook, mais pas à l'application.
                    // Donc on appelle FB.login() pour afficher la boîte de dialogue de connexion à l'application.
                    // On ferait pas comme ça pour une vrai application, pour deux raisons:
                    // (1) Un popup créé automatiquement par JavaScript serait bloqué par la plupart des navigateurs
                    // (2) c'est pas cool de sauter au cou de l'utilisateur dès le chargement de la page comme ça.
                    FB.login();
                }else{
                    console.log("l'utilisateur n'est pas connecté à Facebook");
                    // Dans ce cas, la personne n'est pas connectée à Facebook. Donc on appelle la méthode login().
                    // A ce moment, on ne sait pas si l'utilisateur s'est déjà connecté à l'application.
                    // si ils ne se sont jamais connecté à l'application, ils verront la boîte de dialogue de connection
                    // à l'application juste après s'être connecté à Facebook.
                    FB.login();
                }
            });
        };

        // Charger le SDK de manière asynchrone (comme pour les boutons j'aime et partager)
        (function(d){
            var js, id ='facebook-jssdk', ref = d.getElementsByTagName('script')[0];
            if(d.getElementById(id)){return;}
            js = d.createElement('script'); js.id = id; js.async =true;
            js.src ="//connect.facebook.net/fr_FR/all/debug.js";
            ref.parentNode.insertBefore(js, ref);
        }(document));

        // Ici on fait un requête très simple à l'API Open Graph lorsque l'utilisateur est connecté
        function testAPI(){
            console.log('Bienvenue !  On récupère vos informations.... ');
            FB.api('/me',function(response){
                console.log('Bienvenue, '+ response.name +'.');
            });
        }

        function logout() {

            FB.logout();
        }

        function post() {

            FB.login(function(){
                FB.api('/me/feed', 'post', {message: 'Hello, world!'});
            }, {scope: 'publish_actions'});
        }
    </script>
</head>
<body>
<?php include("header.php"); ?>
<h2>Inscrivez-vous</h2>
<?php
    if (isset($_GET["erreur"])) {
        echo "<div><span>".$_GET["erreur"]."</span></div>";
    }
?>

<!--   Ci-dessous, le bouton de connexion classique c'est la meilleur méthode pour laisser l'utilisateur se connecter. Ce bouton actionne la fonction FB.login(). -->
<div class="fb-login-button" data-max-rows="1" data-show-faces="true"></div>

<div><a href="JavaScript:logout()">Logout</a></div>

<div><a href="JavaScript:post()">Post</a></div>

<form class="inscription" action="req_inscription.php" method="post" name="inscription">

    <!-- c'est quoi les attributs action et method ? -->
    <!-- action : Lors de la soumission du formulaire, les données seront envoyées au fichier associé -->
    <!-- method : Permet de définir le type d'envoi de données (GET ou POST) -->

    <!-- qu'y a-t-il d'autre comme possiblité que post pour l'attribut method ? -->
    <!-- Comme vu dans la définition de l'attribut method, il est possible d'envoyer les données du formulaire en GET -->

    <span class="required_notification">Les champs obligatoires sont indiqués par *</span>
    <ul>
        <li>
            <label for="email">E-mail :</label>
            <input type="email" name="email" id="email" autofocus required/>

            <!-- quelle est la différence entre les attributs name et id ? -->
            <!-- Il peut y avoir plusieurs input possédant le même attribut name -->
            <!-- L'attribut id quand à lui permet d'identifier l'input de manière unique dans le but de le modifier, personnaliser... -->

            <!-- c'est lequel qui doit être égal à l'attribut for du label ? -->
            <!-- Sachant que l'attribut id permet d'identifier l'input, il est normal que se soit lui qui doit être égal à l'attribut for du label -->

            <span class="form_hint">Format attendu "name@something.com"</span>
        </li>
        <li>
            <label for="mdp1">Mot de passe :</label>
            <input type="password" name="password" id="mdp1" pattern="\w{6,8}" onkeyup="validateMdp2()" title = "Le mot de passe doit contenir de 6 à 8 caractères alphanumériques." required placeholder="Mot de passe">
            <!-- quels sont les deux scénarios où l'attribut title sera affiché ? -->
            <!-- encore une fois, quelle est la différence entre name et id pour un input ? -->
            <span class="form_hint">De 6 à 8 caractères alphanumériques.</span>
        </li>
        <li>
            <label for="mdp2">Confirmez mot de passe :</label>
            <input type="password" id="mdp2" required onkeyup="validateMdp2()">
            <!-- ajouter à input l'attribut qui donne une indication grisée (placeholder) -->
            <!-- pourquoi est-ce qu'on a pas mis un attribut name ici ? -->
            <!-- quel scénario justifie qu'on ait ajouté l'écouter validateMdp2() à l'évènement onkeyup de l'input mdp1 ? -->
            <span class="form_hint">Les mots de passes doivent être égaux.</span>
        </li>
        <li>
            <label for="nom">Nom :</label>
            <input type="text" name="nom" id="nom" placeholder="Doe" value="<?php
            if (isset($_GET["nom"])) {
                echo $_GET["nom"];
            }
            ?>"/>
        </li>
        <li>
            <label for="prenom">Prénom :</label>
            <input type="text" name="prenom" id="prenom" value="<?php
            if (isset($_GET["prenom"])) {
                echo $_GET["prenom"];
            }
            ?>" required placeholder="John"/>
        </li>
        <li>
            <label for="telephone">Téléphone :</label>
            <input type="tel" name="telephone" id="telephone" value="<?php
            if (isset($_GET["telephone"])) {
                echo $_GET["telephone"];
            }
            ?>"/>
        </li>
        <li>
            <label for="siteWeb">Site web :</label>
            <input type="url" name="siteWeb" id="siteWeb" value="<?php
            if (isset($_GET["siteWeb"])) {
                echo $_GET["siteWeb"];
            }
            ?>"/>
        </li>
        <li>
            <label>Sexe :</label>
            <div>
                <input type="radio" name="sexe" id="sexeHomme" value="H" checked/>
                <span>Homme</span>
            </div>
            <div>
                <input type="radio" name="sexe" id="sexeFemme" value="F"/>
                <span>Femme</span>
            </div>
        </li>
        <li>
            <label for="birthdate">Date de naissance:</label>
            <input type="date" name="birthdate" id="birthdate" placeholder="JJ/MM/AAAA" value="<?php
            if (isset($_GET["birthdate"])) {
                echo $_GET["birthdate"];
            }
            ?>" required onchange="computeAge()"/>
            <span class="form_hint">Format attendu "JJ/MM/AAAA"</span>
        </li>
        <li>
            <label for="age">Age:</label>
            <input type="number" name="age" id="age" disabled/>

            <!-- à quoi sert l'attribut disabled ? -->
            <!-- Le champs n'est pas actif. Il n'est pas possible de le remplir -->

        </li>
        <li>
            <label for="ville">Ville :</label>
            <input type="text" name="ville" id="ville" value="<?php
            if (isset($_GET["ville"])) {
                echo $_GET["ville"];
            }
            ?>"/>
        </li>
        <li>
            <label for="taille">Taille :</label>
            <input type="range" name="taille" id="taille" min="0" max="2.50" step="0.01" oninput="afficherTaille() value="<?php
            if (isset($_GET["taille"])) {
                echo $_GET["taille"];
            }
            ?>""/>
            <span id="tailleSpan">1.25 m</span>
        </li>
        <li>
            <label for="couleur">Couleur préférée :</label>
            <input type="color" name="couleur" id="couleur" value="black" value="<?php
            if (isset($_GET["couleur"])) {
                echo $_GET["couleur"];
            }
            ?>"/>
        </li>
        <li>
            <label for="profilepicfile">Photo de profil:</label>
            <input type="file" id="profilepicfile" onchange="loadProfilePic()"/>
            <!-- l'input profilepic va contenir le chemin vers l'image sur l'ordinateur du client -->
            <!-- on ne veut pas envoyer cette info avec le formulaire, donc il n'y a pas d'attribut name -->
            <span class="form_hint">Choisissez une image.</span>
            <input type="hidden" name="profilepic" id="profilepic" value="<?php
            if (isset($_GET["profilepic"])) {
                echo $_GET["profilepic"];
            }
            ?>"/>
            <!-- l'input profilepic va contenir l'image redimensionnée sous forme d'une data url -->
            <!-- c'est cet input qui sera envoyé avec le formulaire, sous le nom profilepic -->
            <canvas id="preview" width="0" height="0"></canvas>
            <!-- le canvas (nouveauté html5), c'est ici qu'on affichera une visualisation de l'image. -->
            <!-- on pourrait afficher l'image dans un élément img, mais le canvas va nous permettre également
            de la redimensionner, et de l'enregistrer sous forme d'une data url-->
        </li>
        <li>
            <input type="submit" value="Soumettre Formulaire">
        </li>
    </ul>
</form>
</body>
</html>