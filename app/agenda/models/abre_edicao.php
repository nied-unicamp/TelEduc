<?php
//header('Content-type: application/json');

$ferramenta_geral = 'geral';
$ferramenta_agenda = 'agenda';
$ferramenta_administracao = 'administracao';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_agenda = '../../'.$ferramenta_agenda.'/models/';

require_once $model_geral.'geral.inc';
require_once $model_agenda.'agenda.inc';

$cod_curso = $_POST['cod_curso'];
$cod_item = $_POST['cod_item'];
$cod_usuario = $_POST['cod_usuario'];
$origem = $_POST['origem'];

$data = time();

$sock=AcessoSQL::Conectar($cod_curso);
//Retorna os dados da agenda - Array: cod_item,cod_usuario,  titulo, texto, situacao,
//data, data_ativo, data_inativo, status, inicio_edicao.
$linha=Agenda::RetornaAgenda($sock, $cod_item);
//Retorna a ultima ocorrencia do historico de um item dado - cod_item, cod_usuario, data, acao
$linha_historico=Agenda::RetornaUltimaPosicaoHistorico($sock, $cod_item);

if (($linha['status']=="E")&&($cod_usuario !=$linha_historico['cod_usuario'])){
 window.open('em_edicao.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=ver','EmEdicao','width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');

//$objResponse->script("document.location='".$origem.".php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."cod_ferramenta=1'");
}else {
if($linha['status']=="E"){
	$consulta="insert into Agenda_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".time().", 'D')";
	$res=AcessoSQL::Enviar($sock,$consulta);
}
$consulta="update Agenda_itens set status='E', cod_usuario=".$cod_usuario.", inicio_edicao=".time()." where cod_item=".$cod_item;
$res=AcessoSQL::Enviar($sock,$consulta);
$consulta="insert into Agenda_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".time().", 'E')";
$res=AcessoSQL::Enviar($sock,$consulta);
	}

AcessoSQL::Desconectar($sock);

//Para evitar erros no histórico
sleep(1);

?>