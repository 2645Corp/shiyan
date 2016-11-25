<?php
	require_once("srvclass.php");
	(new WebSocket("localhost","8888"))->run();
?>
