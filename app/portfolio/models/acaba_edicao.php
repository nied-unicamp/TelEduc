<?php

$ferramenta_geral = 'geral';

$model_geral = '../../'.$ferramenta_geral.'/models/';

require_once $model_geral.'geral.inc';
	
$cod_curso = $_POST['cod_curso'];
$cod_item = $_POST['cod_item'];
$cod_usuario = $_POST['cod_usuario'];
$acao = $_POST['acao'];

$sock=AcessoSQL::Conectar($cod_curso);

// como vou precisar atualizar campos de data, preciso saber a data em UnixTime
$data = "";
unset ($data);
$data = time();

//Correзгo feita para nгo acontecer de os horбrios ficarem iguais no banco e atrapalhar o status do item
$consulta="select * from Portfolio_itens_historicos where data=".$data." and cod_item=".$cod_item;
$res=AcessoSQL::Enviar($sock, $consulta);
$linha = AcessoSQL::RetornaLinha($res);
if ($linha[0]!=""){ $data++; }

$consulta="update Portfolio_itens set status='L', data=".$data." where cod_item=".$cod_item;
$res=AcessoSQL::Enviar($sock, $consulta);

if($acao){
	$consulta="insert into Portfolio_itens_historicos values ('".$cod_item."', '".$cod_usuario."', '".$data."', 'F')";
}else{
	$consulta="insert into Portfolio_itens_historicos values ('".$cod_item."', '".$cod_usuario."', '".$data."', 'D')";
}

$res=AcessoSQL::Enviar($sock, $consulta);

AcessoSQL::Desconectar($sock);

//Correзгo feita para nгo acontecer de os horбrios ficarem iguais no banco e atrapalhar o status do item
if ($linha[0]!=""){ sleep(1); }
?>