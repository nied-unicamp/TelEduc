<?php

$ferramenta_geral = 'geral';
$ferramenta_portfolio = 'portfolio';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_portfolio = '../../'.$ferramenta_portfolio.'/models/';
$view_portfolio = '../../'.$ferramenta_portfolio.'/views/';

require_once $model_geral.'geral.inc';
require_once $model_portfolio.'portfolio.inc';

$cod_curso = $_POST['cod_curso'];
$cod_item = $_POST['cod_item'];
$cod_usuario = $_POST['cod_usuario'];

// como vou precisar atualizar campos de data, preciso saber a data em UnixTime
$data ="";
unset ($data);
$data = time();

$sock=AcessoSQL::Conectar($cod_curso);

//Correção feita para não acontecer de os horários ficarem iguais no banco e atrapalhar o status do item
$consulta="select * from Portfolio_itens_historicos where data=".$data." and cod_item=".$cod_item;
$res=AcessoSQL::Enviar($sock, $consulta);
$linha = AcessoSQL::RetornaLinha($res);

if ($linha!=""){ 
	$data++; 
}

$linha=Portfolio::RetornaDadosDoItem($sock, $cod_item);

$linha_historico=Portfolio::RetornaUltimaPosicaoHistorico($sock, $cod_item);

if (($linha['status']=="E")&&($cod_usuario !=$linha_historico['cod_usuario'])){
	$flag = 1;
	//$objResponse->script("window.open('em_edicao.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=ver','EmEdicao','width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');");
	//$objResponse->script("document.location='portfolio.php?cod_usuario=".$cod_usuario."&cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=ver&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."&cod_topico_raiz=".$cod_topico_raiz."'");
}else if ($linha['status']!="E"){
	$consulta="update Portfolio_itens set status='E', cod_usuario=".$cod_usuario.", inicio_edicao=".$data." where cod_item=".$cod_item;
	$res=AcessoSQL::Enviar($sock,$consulta);
	$consulta="insert into Portfolio_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".$data.", 'E')";
	$res=AcessoSQL::Enviar($sock,$consulta);
}

AcessoSQL::Desconectar($sock);

echo json_encode($flag);