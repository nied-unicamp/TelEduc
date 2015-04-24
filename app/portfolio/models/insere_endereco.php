<?php

$ferramenta_geral = 'geral';
$ferramenta_portfolio = 'portfolio';

$model_portfolio = '../../'.$ferramenta_portfolio.'/models/';
$model_geral = '../../'.$ferramenta_geral.'/models/';

require_once $model_geral.'geral.inc';
require_once $model_portfolio.'portfolio.inc';

$nome = $_POST['nome'];
$endereco = $_POST['endereco'];
$cod_item = $_POST['cod_item'];
$cod_curso = $_POST['cod_curso'];
$cod_usuario = $_POST['cod_usuario'];

$sock=AcessoSQL::Conectar($cod_curso);

$consulta="select * from Portfolio_itens_enderecos where endereco='".ConversorTexto::LimpaTitulo($endereco)."' and nome='".ConversorTexto::LimpaTitulo($nome)."' and cod_item=".$cod_item;

$res=AcessoSQL::Enviar($sock, $consulta);

$linha=AcessoSQL::RetornaLinha($res);
if ($linha[0]!='') return false;

$num=AcessoSQL::RetornaNumLinhas($res);
if ($num>0)
{
	$linha=AcessoSQL::RetornaLinha($res);
	if ($linha['status']=="A")
	{
		$consulta="update Portfolio_itens_enderecos set status='F' where cod_endereco=".$linha['cod_endereco'];
		$res=AcessoSQL::Enviar($sock, $consulta);
	}
}
else
{
	$consulta="insert into Portfolio_itens_enderecos (cod_item, nome, endereco, status) values (".$cod_item.", '".$nome."', '".$endereco."', 'F')";
	$res=AcessoSQL::Enviar($sock, $consulta);
}

$consulta="select * from Portfolio_itens_enderecos where endereco='".ConversorTexto::LimpaTitulo($endereco)."' and nome='".ConversorTexto::LimpaTitulo($nome)."' and cod_item=".$cod_item;

$res=AcessoSQL::Enviar($sock, $consulta);

$linha=AcessoSQL::RetornaLinha($res);
$cod_endereco=$linha['cod_endereco'];


$flag = 1;
//$objResponse->create("listaEnderecos", "span", "end_".$cod_endereco);
//$objResponse->create("end_".$cod_endereco, "span", "link_".$cod_endereco);
//$objResponse->assign("link_".$cod_endereco, "className", "link");

if ($num==0){
	if ($nome!=''){
		$flag = 2;
		//$objResponse->assign("link_".$cod_endereco, "innerHTML", $nome);
		//$objResponse->insertAfter("link_".$cod_endereco, "span", "endEndereco_".$cod_endereco);
		//$objResponse->assign("endEndereco_".$cod_endereco, "innerHTML", "&nbsp;&nbsp;(".RetornaURLValida($endereco).") - \n");
	}else{
		$flag = 3;
		//$objResponse->assign("link_".$cod_endereco, "innerHTML", RetornaURLValida($endereco)." - ");
	}
}

$flag = 4;
//$objResponse->addEvent("link_".$cod_endereco, "onClick", "WindowOpenVerURL('".ConverteSpace2Mais(RetornaURLValida($endereco))."');return(false);");

//$objResponse->create("end_".$cod_endereco, "span", "endApagar_".$cod_endereco);
//$objResponse->assign("endApagar_".$cod_endereco, "className", 'link');
//$objResponse->assign("endApagar_".$cod_endereco, "innerHTML", 'Apagar<br>');
//$objResponse->addEvent("endApagar_".$cod_endereco, "onClick", "ApagarEndereco('".$cod_curso."', '".$cod_endereco."');");

AcessoSQL::Desconectar($sock);

Portfolio::AcabaEdicao($cod_curso, $cod_item, $cod_usuario, 1);

//$objResponse->call("mostraFeedback", $texto, 'true');

echo json_encode(array(num => $num, cod_endereco => $cod_endereco, url_valida => ConversorTexto::RetornaURLValida($endereco), url_valida_space => ConversorTexto::ConverteSpace2Mais(ConversorTexto::RetornaURLValida($endereco))));