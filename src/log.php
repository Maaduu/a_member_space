<?php

if(isset($_COOKIE['auth']) && !isset($_SESSION['connect'])){
    // VARIABLE
    $secret = htmlspecialchars($_COOKIE['auth']);

    // VERIF
    require('src/connection.php');

    $req =  $db->prepare('SELECT count(*) as numberEmail FROM users WHERE secreto =?');
    $req->execute(array($secret));
    
    while($user = $req->fetch()){
    
        if($user['numberEmail'] == 1){
        
            $reqUser =$db->prepare('SELECT *FROM users WHERE secreto =?');
            $reqUser->execute(array($secret));

            while($userAccount  =$reqUser->fetch()){

                $_SESSION['connect'] = 1;
                $_SESSION['email'] = $userAccount['email'];
            }
		}
	}
}
// BLOCKED
/*if(isset($_SESSION['connect'])){

   	$reqUser =$db->prepare('SELECT *FROM users WHERE email =?');
   	$reqUser->execute(array($_SESSION['email']));

   	while($userAccount  =$reqUser->fetch()){
     	if($userAccount['blocked'] == 1){
           	header('location: logout.php?error=1&blocked=Vous etes bloquée.');
      	}
    
    }
}*/
?>