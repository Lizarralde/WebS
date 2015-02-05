<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<html>
	<head>
		<title>
			Utilisation d'OpenSSL pour PHP
		</title>
		<!-- Include CSS File Here-->
		<link href="css/openssl.css" rel="stylesheet" />
	</head>
    <body>
        <form action="scripts/A-B.php" method="post">
            <h1>
                A-B. Généreration de certificats
            </h1>
            <h4>
                Veuillez remplir les champs suivants.
            </h4>
            <div>
                <label for="commonName">
                    Nom de l'organisation :
                </label>
            </div>
            <div>
                <input type="text" name="commonName" id="commonName" value="Université de Nice Sophia Antipolis" />
            </div>
            <div>
                <label for="organizationName">
                    Acronyme de l'organisation :
                </label>
            </div>
            <div>
                <input type="text" name="organizationName" id="organizationName" value="UNSA" />
            </div>
            <div>
                <label for="organizationalUnitName">
                    Nom de l'unité de l'organisation :
                </label>
            </div>
            <div>
                <input type="text" name="organizationalUnitName" id="organizationalUnitName" value="LPSIL" />
            </div>
            <div>
                <label for="emailAddress">
                    E-mail de l'organisation :
                </label>
            </div>
            <div>
                <input type="text" name="emailAddress" id="emailAddress" value="contact@unsa.fr" />
            </div>
            <div>
                <label for="localityName">
                    Ville de l'organisation :
                </label>
            </div>
            <div>
                <input type="text" name="localityName" id="localityName" value="Sophia Antipolis" />
            </div>
            <div>
                <label for="stateOrProvinceName">
                    Département de l'organisation :
                </label>
            </div>
            <div>
                <input type="text" name="stateOrProvinceName" id="stateOrProvinceName" value="Alpes-Maritimes" />
            </div>
            <div>
                <label for="countryName">
                    Pays de l'organisation :
                </label>
            </div>
            <div>
                <input type="text" name="countryName" id="countryName" value="FR" />
            </div>
            <div>
                <input type="submit" name="submitAutoSignedButton" value="Obtenir un certificat autosigné" />
            </div>
            <div>
                <input type="submit" name="submitServerSignedButton" value="Obtenir un certificat signé par le serveur" />
            </div>
        </form>
        <form enctype="multipart/form-data" action="scripts/C.php" method="post">
            <h1>
                C. Chiffrement/déchiffrement pour assurer la confidentialité
            </h1>
            <div>
                <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
            </div>
            <div>
                <label for="text">
                    Texte à chiffrer/déchiffrer
                </label>
            </div>
            <div>
                <input type="text" name="text" id="text" value="Texte" />
            </div>
            <div>
                <label for="certificateFile">
                    Choisissez un certificat signé
                </label>
            </div>
            <div>
                <input type="file" name="certificateFile" id="certificateFile" />
            </div>
            <div>
                <input type="submit" name="submitEncrypt" value="Crypter le text" />
            </div>
            <div>
                <label for="$textFile">
                    Choisissez fichier contenant du texte crypté
                </label>
            </div>
            <div>
                <input type="file" name="$textFile" id="$textFile" />
            </div>
            <div>
                <label for="privateKeyFile">
                    Choisissez une clé privée
                </label>
            </div>
            <div>
                <input type="file" name="privateKeyFile"  id="privateKeyFile" />
            </div>
            <div>
                <input type="submit" name="submitDecrypt" value="Décrypter le text" />
            </div>
        </form>
        <form enctype="multipart/form-data" action="scripts/D.php" method="post">
            <h1>
                D. Signature d'un document
            </h1>
            <div>
                <label for="document">
                    Choisissez un document à signer
                </label>
            </div>
            <div>
                <input type="file" name="document" id="document" />
            </div>
            <div>
                <input type="submit" name="submitSign" value="Signer le document" />
            </div>
        </form>
        <form enctype="multipart/form-data" action="scripts/E.php" method="post">
            <h1>
                E. Vérification de la signature d'un document
            </h1>
            <div>
                <label for="document">
                    Choisissez un document
                </label>
            </div>
            <div>
                <input type="file" name="document" id="document" />
            </div>
            <div>
                <label for="signature">
                    Choisissez la signature associée
                </label>
            </div>
            <div>
                <input type="file" name="signature" id="signature" />
            </div>
            <div>
                <input type="submit" name="submitCheck" value="Vérifier la signature" />
            </div>
        </form>
    </body>
</html>
