<?php
session_start();    // INICIALISE LA SESSION
session_unset();    // DESACTIVE LA SESSION
session_destroy();  // DETRUIT LA SESSION
setcookie('auth', '', time()-1, '/', null, false, true);

header('location: index.php');
exit();

?>