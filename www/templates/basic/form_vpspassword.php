<?php

include('header.php');

if(!empty($_GET['vps']) && !empty($_GET['password'])) {
	$paquet = new Paquet();
	$paquet -> add_action('vpsPassword', array($_GET['vps'],$_GET['password']));
	$paquet -> send_actions();
}

?>