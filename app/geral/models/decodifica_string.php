<?php

$texto = $_POST['conteudo'];

$string = html_entity_decode($texto);

echo json_encode($string);

?>