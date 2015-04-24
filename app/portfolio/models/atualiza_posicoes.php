<?php
$ferramenta_geral = 'geral';

$model_geral = '../../'.$ferramenta_geral.'/models/';

require_once $model_geral.'geral.inc';

$cod_curso = $_POST['cod_curso'];
$cod_usuario = $_POST['cod_usuario'];
$cod_topico = $_POST['cod_topico'];
$tabela = $_POST['tabela'];

$sock=AcessoSQL::Conectar($cod_curso);

if(AcessoSQL::PegaSemaforo($sock, "Portfolio"))
{
	foreach($tabela as $cod => $linha){
		$vetor = split('_', $linha);
		if(!is_numeric($vetor[1])){
			//é um topico
			$query = "update Portfolio_topicos set posicao_topico = ".($cod+1)." where cod_topico=".$vetor[2];
		}else{
			$query = "update Portfolio_itens set posicao_item = ".($cod+1)." where cod_topico=".$cod_topico." and cod_item=".$vetor[1];
		}
	
		AcessoSQL::Enviar($sock, $query);
	}

	AcessoSQL::LiberaSemaforo($sock, "Portfolio");
	//$objResponse->call("mostraFeedback", $texto, 'true');
	$retorno = 'true';
}
else
{
	$retorno = 'false';
	//$objResponse->call("mostraFeedback", $msg_erro, 'false');
}

AcessoSQL::Desconectar($sock);

echo json_encode($retorno);