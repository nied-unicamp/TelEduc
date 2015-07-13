<?php 

require_once '../../../lib/Conexao.php';

$cod_item=$_GET['cod_item'];
$cod_curso=$_GET['cod_curso'];


    $sql1= "delete from Historico_Agenda_Item where  Agenda_item_cod_item=".$cod_item;
    $conexao = new Conexao();
    $conexao->Conectar();
    
    $res1 = $conexao->Enviar($sql1);
    
	
    $sql2 = "delete from Agenda_item  where cod_item=".$cod_item;

    $res2 = $conexao->Enviar($sql2);
	 	 
	$conexao->Desconectar();
	
	header("Location: ../../../app/agenda/view/agenda.php?cod_curso=$cod_curso");
	
?>php 
