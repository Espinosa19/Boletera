<?php
session_start(); 
$_SESSION = [];

// Destruye la sesión
session_destroy();

header("Location: ../login.php");
exit;
