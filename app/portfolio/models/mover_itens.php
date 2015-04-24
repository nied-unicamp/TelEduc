<?php

$ferramenta_geral = 'geral';
$ferramenta_portfolio = 'portfolio';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_portfolio = '../../'.$ferramenta_portfolio.'/models/';

require_once $model_geral.'geral.inc';
require_once $model_portfolio.'portfolio.inc';

$cod_curso = $_POST['cod_curso'];
$cod_usuario = $_POST['cod_usuario'];
$cod_topico_raiz = $_POST['cod_topico_raiz'];
$cod_topico_novo = $_POST['cod_topico_novo'];
$cod_topicos = $_POST['cod_topicos'];
$cod_itens = $_POST['cod_itens'];


$sock=AcessoSQL::Conectar($cod_curso);

//existem tópicos
if (count($cod_topicos))
{
	if (Portfolio::EPaiTopicos($sock, $cod_topico_novo, $cod_topicos))
	{
		//28 - Você não pode mover uma pasta para ela mesma ou para uma subpasta dela.
		//$objResponse->call("mostraFeedback", RetornaFraseDaLista($lista_frases,28), 'false');
		$flag = 1;
	}
	else
	{
		$existe = 0;
		for($i = 0; $i < count($cod_topicos); $i++)
		{
			if (!Portfolio::NaoExisteTop($sock, $cod_topico_novo, $cod_topicos[$i], $cod_usuario))
				$existe = 1;
		}

		if (!$existe)
		{
			// move todos os topicos selecionados
			for($i = 0; $i < count($cod_topicos); $i++)
			{
				Portfolio::MoverTopico($sock, $cod_topicos[$i], $cod_usuario, $cod_topico_novo);
			}
			// se tiver itens selecionados, move-os também
			if (strlen($cod_itens))
			{
				for($i = 0; $i < count($cod_itens); $i++)
				{
					Portfolio::MoverItem($sock, $cod_itens[$i], $cod_usuario, $cod_topico_novo);
				}
			}
			//$objResponse->script('Redirecionar('.$cod_topico_raiz.', "moverItens", "true");');
			$flag = 2;
		}
		//só existem itens
		else //existe topico com mesmo nome no diretorio destino
		{
			/* 71- Não foi possível mover a pasta, pois já existe uma pasta com mesmo nome no diretório destino. */
			//$objResponse->call("mostraFeedback", RetornaFraseDaLista($lista_frases,71), 'false');
			$flag = 3;
		}
	}
}
else
{
	if (count($cod_itens))
	{
		for($i = 0; $i < count($cod_itens); $i++)
		{
			Portfolio::MoverItem($sock, $cod_itens[$i], $cod_usuario, $cod_topico_novo);
		}
	}
	//$objResponse->script('Redirecionar('.$cod_topico_raiz.', "moverItens", "true");');
	$flag = 4;
}

Portfolio::ArrumaPosicoes($sock, $cod_topico_novo);
Portfolio::ArrumaPosicoes($sock, $cod_topico_raiz);

AcessoSQL::Desconectar($sock);

echo json_encode($flag);

?>