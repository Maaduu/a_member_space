<?php
session_start();
require('src/log.php');

	// VERIF SI LES CHAMPS DE FORMULAIRE SONT REMPLIS
	if(!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password_two'])){
		
		require('src/connection.php');
		// VARIABLES
		$email						= htmlspecialchars($_POST['email']);
		$password					= htmlspecialchars($_POST['password']);
		$password_two				= htmlspecialchars($_POST['password_two']);

		// VERIF SI PASSWORD EST EGAL A PASSWORD_TWO
		if($password != $password_two){
			header('location: inscription.php?error=1&pass=Vos mots de passe ne sont pas identique');
			exit();
		}

		// VERIF SI L'ADRESS EMAIL EST VALID
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			header('location: inscription.php?error=1&invalidEmail=Cette email est invalide');
		}

		// VERIF SI L'ADRESSE EMAIL N'EST PAS UTILISE PAR UN OUTRE
		$req =  $db->prepare('SELECT count(*) as numberEmail FROM users WHERE email =?');
		$req->execute(array($email));

		while($email_verif = $req->fetch()){
			if($email_verif['numberEmail'] != 0 ){
				header('location: inscription.php?error=1&message=Cette email est déjà utilisée par quelqun');
			}
		}

		// HASH
		$secret = sha1($email).time();
		$secret = sha1($secret).time();

		// CHIFFRAGE DU MOT DE PASSE
		$password = "aq1".sha1($password."123")."25";

		// ENVOI DES DONNEES REMPLIR SUR FORMULAIRE A LA BDD
		$req = $db->prepare("INSERT INTO users(email, motDePasse, secreto) VALUES(?,?,?)");
		$req->execute(array($email, $password, $secret));
		
		header('location: inscription.php?success=1');
		exit();
	}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Netflix</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
	<link rel="icon" type="image/pngn" href="img/favicon.png">
</head>
<body>

	<?php include('src/header.php'); ?>
	
	<section>
		<div id="login-body">
				<?php if(isset($_SESSION['connect'])){?>
			<h1>Hello!</h1>
			<small><a href="logout.php">Deconexion.</a></small>
		
		<?php } else{ ?>
			<h1>S'inscrire</h1>
			<?php
				if(isset($_GET['error'])){
					if(isset($_GET['pass'])){
						echo '<div class="alert error">'.htmlspecialchars($_GET['pass']).'</div>';
					}
					if(isset($_GET['invalidEmail'])){
						echo '<div class="alert error">'.htmlspecialchars($_GET['invalidEmail']).'</div>';
					}
					if(isset($_GET['message'])){
						echo '<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';
					}
				} 
				else if(isset($_GET['success'])){
					echo '<div class="alert success"> Désormais vous etes inscrit. <a href="index.php">Connectez-vous.</a></div>';
				}
			?>
			<form method="post" action="inscription.php">
				<input type="email" name="email" placeholder="Votre adresse email" required />
				<input type="password" name="password" placeholder="Mot de passe" required />
				<input type="password" name="password_two" placeholder="Retapez votre mot de passe" required />
				<button type="submit">S'inscrire</button>
			</form>

			<p class="grey">Déjà sur Netflix ? <a href="index.php">Connectez-vous</a>.</p>
			<?php } ?>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>
</html>