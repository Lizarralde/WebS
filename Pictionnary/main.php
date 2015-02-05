<?php
if (isset($_GET["erreur"])) {
    echo "<div><span>".$_GET["erreur"]."</span></div>";
}
include("header.php");
echo "<a href='paint.php'>Dessiner</a>";

if (isset($_SESSION["email"])) {

	// Connect to server and select database.
	$dbh = new PDO('mysql:host=localhost;dbname=pictionnary', 'test', 'test');

	$email = $_SESSION["email"];

	$sql = $dbh->prepare("SELECT * FROM DRAWINGS WHERE EMAIL = '" . $email . "'");
	$sql->execute();

	echo "<div><table style=\"float:left\">";
	echo "<th>Vos dessins</th>";

	$i = 0;

	foreach ($sql as $row) {

		if ($i == 0) {

			echo "<tr>";
		}

		echo "<td>";
		echo "<img src=\"" . $row["dessin"] . "\" style=\"width:100px; height:100px;\" alt='Dessin' />";	
		echo "</td>";

		if ($i == 2) {

			echo "</tr>";
			$i = 0;
		} else {

			$i++;
		}
	}

	echo "</table></div>";
	
	$sql = $dbh->prepare("SELECT * FROM DRAWINGS WHERE DEST = '" . $email . "'");
	$sql->execute();

	echo "<div><table style=\"float:left\">";
	echo "<th>Vos demandes</th>";

	$i = 0;

	foreach ($sql as $row) {

		if ($i == 0) {

			echo "<tr>";
		}

		echo "<td>";
		echo "<a href=\"guess.php?id=" . $row["id"] . "\"><img src=\"" . $row["dessin"] . "\" style=\"width:100px; height:100px;\" alt='Dessin' /></a>";	
		echo "</td>";

		if ($i == 2) {

			echo "</tr>";
			$i = 0;
		} else {

			$i++;
		}
	}

	echo "</table></div>";
}
?>
