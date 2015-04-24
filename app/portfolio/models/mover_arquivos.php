<?php

$ferramenta_geral = 'geral';
$ferramenta_portfolio = 'portfolio';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_portfolio = '../../'.$ferramenta_portfolio.'/models/';

require_once $model_geral.'geral.inc';
require_once $model_portfolio.'portfolio.inc';

$origem = $_POST['origem'];
$destino = $_POST['destino'];
$cod_curso = $_POST['cod_curso'];
$cod_item = $_POST['cod_item'];
$cod_usuario = $_POST['cod_usuario'];

// Se a origem ou destino forem vazios retorna false
if (($origem == "") || ($destino == ""))
	return false;

// Vari?el de retorno da fun?o
$flag = true;

// Resolve o caminho da origem ('realpath' testa tamb? se o arquivo
// existe).

//linha necessária para corrigir nome de pastas ou arquivos com espaço em branco
$origem = eregi_replace("%20", " ", $origem);
$origem = realpath($origem);
if ($origem == false){
	//$objResponse->script("EscondeLayers();");
	//$objResponse->call("mostraFeedback", $msg_erro, 'false');
	$codigo = 1;
}

// Se retornar mais que um elemento (ou seja, pelo menos
// um que n? seja o pr?rio arquivo ou pasta a ser movida)
// ent? os movemos.
$total = count($conteudo);

$nome_arquivo = basename($origem);

$dest = $destino.DIRECTORY_SEPARATOR.$nome_arquivo;

if (Arquivos::ExisteArquivo($dest)){
	Portfolio::AcabaEdicao($cod_curso, $cod_item, $cod_usuario, 1);
	//$objResponse->script("EscondeLayers();");
	//$objResponse->script("window.location='ver.php?cod_curso='+cod_curso+'&cod_item='+cod_item+'&cod_topico_raiz='+cod_topico_ant+'&cod_usuario_portfolio='+cod_usuario_portfolio+'&acao=moverarquivos&atualizacao=true';");
	$codigo = 2;
}

// O teste aqui tamb? ?necess?io para ver se 'RemoveArquivo'
// n? retornou false.
if ($flag)
{
	$stat = Arquivos::RetornaStatusArquivo($conteudo);
	if (copy($origem, $dest))
	{
		if (Arquivos::AlteraStatusArquivo($dest, $stat))
		{
			if (!Arquivos::RemoveArquivo($origem)){
				//$objResponse->script("EscondeLayers()");;
				//$objResponse->call("mostraFeedback", $msg_erro, 'false');
				$codigo = 1;
			}
		}
		else{
			//$objResponse->script("EscondeLayers();");
			//$objResponse->call("mostraFeedback", $msg_erro, 'false');
			$codigo = 1;
		}
	}
	else{
		//$objResponse->script("EscondeLayers();");
		//$objResponse->call("mostraFeedback", $msg_erro, 'false');
		$codigo = 1;
	}
}

clearstatcache();

if (is_dir($origem))
	$flag = Arquivos::RemoveDiretorio($origem);

//$objResponse->script("window.location='ver.php?cod_curso='+cod_curso+'&cod_item='+cod_item+'&cod_topico_raiz='+cod_topico_ant+'&cod_usuario_portfolio='+cod_usuario_portfolio+'&acao=moverarquivos&atualizacao=true';");

Portfolio::AcabaEdicao($cod_curso, $cod_item, $cod_usuario, 1);

echo json_encode($codigo);