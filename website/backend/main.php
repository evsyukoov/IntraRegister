
<?php
require_once( "IntraAPI.php" );
require_once("DAO.php");
    $login = $_GET['login'];
    $storage = new DAO($login);
    $storage->addUser();

?>
