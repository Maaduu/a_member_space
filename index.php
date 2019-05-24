<?php
// INITIALISE LA SESSION
session_start();

require('src/log.php');

// VERIF SI LES CHAMPS SONT REMPLIS
if (!empty($_POST['email']) && !empty($_POST['password'])) {

	// APPEL A BDD
	require('src/connection.php');

	// VARIABLES
	$email					= htmlspecialchars($_POST['email']);
	$password				= htmlspecialchars($_POST['password']);

	// ADRESSE EMAIL SYNTAX
	if (!filter_var($email, 	FILTER_VALIDATE_EMAIL)) {
		header('location: index.php?error=1&message=Votre adresse email est invalide');
		exit();
	}

	// ENCRYPTAGE DU PASSWORD
	$password = "aq1" . sha1($password . "123") . "25";

	// VERIF SI LE EMAIL EST DEJA UTILISE
	$req =  $db->prepare('SELECT count(*) as numberEmail FROM users WHERE email =?');
	$req->execute(array($email));

	while ($email_verif = $req->fetch()) {
		if ($email_verif['numberEmail'] != 1) {
			header('location: index.php?error=1&message=Impossible de vous authentifier.');
		}
	}

	// CONNEXION
	$req = $db->prepare('SELECT * FROM users WHERE email =?');
	$req->execute(array($email));

	while ($user = $req->fetch()) {

		if ($password == $user['motDePasse'] && $user['blocked'] == 0) {

			$_SESSION['connect'] = 1;
			$_SESSION['email'] = $user['email'];

			// CONNECTION AUTOMATIQUE COOKIE
			if (isset($_POST['auto'])) {
				setcookie('auth', $user['secreto'], time() + 364 * 24 * 3600, "/", null, false, true);
			}

			header('location: index.php?success=1');
			exit();
		}
		if ($email == $user['email'] && $user['blocked'] == 1) {

			header('location: index.php?error=1&blocked=Vous etes bloquée.');
		} else {
			header('location: index.php?error=1&message=Impossible de vous authentifier.');
			exit();
		}
	}
}

?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>host image</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
	<link rel="icon" type="image/pngn" href="img/imgres.jpg">
</head>

<body>

	<?php include('src/header.php'); ?>

	<section>
		<div id="login-body">

			<?php if (isset($_SESSION['connect'])) { ?>
				<h1>Bonjour</h1>
				<?php if (isset($_GET['success'])) { ?>
					<?php echo '<div class="alert success">Vous etes maintenat connecte</div>'; ?>
				<?php 	} ?>
				<small><a href="logout.php">Deconexion.</a></small>


			<?php } else { ?>

				<h1>S'identifier</h1>
				<?php
				if (isset($_GET['error'])) {
					if (isset($_GET['message'])) {
						echo '<div class="alert error">' . htmlspecialchars($_GET['message']) . '</div>';
					}
					if (isset($_GET['blocked'])) {
						echo '<div class="alert error">' . htmlspecialchars($_GET['blocked']) . '</div>';
					}
				}
				?>
				<form method="post" action="index.php">
					<input type="email" name="email" placeholder="Votre adresse email" required />
					<input type="password" name="password" placeholder="Mot de passe" required />
					<button type="submit">S'identifier</button>
					<label id="option"><input type="checkbox" name="auto" checked />Se souvenir de moi</label>
				</form>


				<p class="grey">Première visite sur Netflix ? <a href="inscription.php">Inscrivez-vous</a>.</p>
			<?php } ?>

		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>

</html>