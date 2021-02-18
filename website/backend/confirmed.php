<?php
    require_once ("DAO.php");

    if (isset($_GET['hash']) && $_GET['hash']) {
        $hash = $_GET['hash'];
        $dao = new DAO(null);
        $user = $dao->findUserFromHash($hash);
        if ($user)
            $dao->updateUserStatus($user);
    }
    else
        echo 'not found';
?>