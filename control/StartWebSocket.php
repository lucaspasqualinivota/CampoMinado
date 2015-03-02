<?php

require_once(dirname(__FILE__)."/processador.php");
require_once(dirname(__FILE__)."/Server.php");

$server    = new Server();

$webSocket = new Processador($server->getIp(), $server->getPorta());
$webSocket->run();