<?php

require_once '../controller/AgendaController.php';

$agendaController = new AgendaController();

$resultado = $agendaController->apagaAgenda();

echo $resultado;

?>