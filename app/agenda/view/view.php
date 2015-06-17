<?php

require_once '../controller/AgendaController.php';

$agendaController = new AgendaController();

$resultado = $agendaController->testaConexao();

echo $resultado;
