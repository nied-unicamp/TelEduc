<?php
class Agenda{
	/* *********************************************************************
	 RetornaDiretorio - Retorna o Diret�rio da tabela de diretorios
	Entrada: $sock - BASE EXTERNA
	$item - nome do item a ser buscado
	Saida: string com o diret�rio
	*/
	static function RetornaDiretorio($sock,$item)
	{
		$query="select diretorio from Diretorio where item='".ConversorTexto::VerificaStringQuery($item)."'";
		$res=AcessoSQL::Enviar($sock,$query);
		$linha=AcessoSQL::RetornaLinha($res);
		return ($linha[0]);
	}
	
	/* *********************************************************************
	 RetornaAgendaAtiva - Retorna a agenda ativa
	Entrada: $sock - BASE Interna
	Saida: array ['cod_item'] - codigo do agenda
	['titulo'] - Titulo da agenda
	['texto'] - Texto
	['situacao'] - situacao A,N,H
	*/
	static function RetornaAgendaAtiva($sock)
	{
		$query="select cod_item,titulo,texto,situacao from Agenda_itens where situacao='A'";
		$res=AcessoSQL::Enviar($sock,$query);
		$linha=AcessoSQL::RetornaLinha($res);
		return $linha;
	}
	
	/* ***********************************************************************
	 CriaLinkVisualizar - Cria link simb�lico para dinamica e retorna o link
	Entrada: $sock - BASE DO CURSO
	$dirname - nome do diretorio da ferramenta
	$cod_curso - codigo do curso
	$cod_usuario - codigo do usuario
	$cod_item - c�digo do item
	$diretorio_arquivos - diretorio dos arquivos do TelEduc
	$diretorio_temp - diretorio dos arquivos do TelEduc
	Saida: caminho relativo
	*/
	static function CriaLinkVisualizar($sock,$dirname,$cod_curso,$cod_usuario,$cod_item,$diretorio_arquivos,$diretorio_temp)
	{
		if(trim($diretorio_arquivos) == "" || trim($diretorio_temp) == "" || trim($dirname) == "" ||
		$cod_curso == "" || $cod_item =="")
			return false;
	
		/* Busca Arquivo a ser mostrado */
		$dir=$diretorio_arquivos."/".$cod_curso."/".$dirname."/".$cod_item."/";
	
		// Cria link simb�lico (apaga antigo, se houver)
		if ($cod_usuario < 0)
			$cod_usuario = 0;
		$dirlink = $diretorio_temp."/".$dirname."_".$cod_curso."_".$cod_usuario;
		$dirlinkpath="../../diretorio/".$dirname."_".$cod_curso."_".$cod_usuario;
	
		if (Arquivos::ExisteArquivo($dirlink))
		{
			Arquivos::RemoveArquivo($dirlink);
		}
		Arquivos::CriaLinkSimbolico($dir,$dirlink);
	
		$retorno['diretorio']=$dirlink."/";
		$retorno['link']=$dirlinkpath."/";
	
		return ($retorno);
	}
	
	/* *********************************************************************
	 RetornaArquivosAgendaVer - Retorna lista de arquivos da Agenda
	Entrada: $dirlink - nome do diret�rio da ferramenta
	$cod_curso - c�digo do item
	Saida: Array multidimensional com:
	$lista[<num>]['Caminho'] - caminho completo.
	$lista[<num>]['Diretorio'] - Diretorio do arquivo
	$lista[<num>]['Arquivo'] - Nome do arquivo
	$lista[<num>]['Status'] - Condi��o especial (true ou false);
	$lista[<num>]['Tamanho'] - tamanho do arquivo
	$lista[<num>]['Data'] - data da �ltima modifica��o
	*/
	static function RetornaArquivosAgendaVer ($cod_curso, $dirlink)
	{
		return (Arquivos::RetornaArrayDiretorio($dirlink));
	}
	
	/* *********************************************************************
	 IniciaCriacao - Inicia a cria��o de um novo item
	Entrada: $sock - BASE DO CURSO
	$cod_usuario - usuario atual
	$cod_curso - codigo do curso
	$diretorio_temp - nome do diret�rio temporario, usado durante a edi��o
	$titulo - titulo do item a ser criado
	Saida: codigo do item criado
	*/
	static function IniciaCriacao ($sock, $cod_usuario, $cod_curso, $diretorio_temp, $titulo, $texto)
	{
		if(trim($diretorio_temp) == "" || $cod_curso == "")
			return -1; //tah certo isso?!
	
	
		$cod_item=AcessoSQL::RetornaProximoCodigo($sock, "Agenda_itens");
	
		$consulta="insert into Agenda_itens (cod_item, cod_usuario, titulo, texto, situacao, data, status, inicio_edicao) values (".$cod_item.", ".$cod_usuario.", '".ConversorTexto::VerificaStringQuery(htmlentities($titulo))."','".ConversorTexto::VerificaStringQuery(htmlentities($texto))."', 'N', ".time().", 'L', ".time().")";
	
		$res=AcessoSQL::Enviar($sock, $consulta);
		$consulta="insert into Agenda_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".time().", 'C')";
	
		$res=AcessoSQL::Enviar($sock, $consulta);
	
		if (!file_exists($diretorio_temp."/tmp/".$cod_curso."/"))
			Arquivos::CriaDiretorio($diretorio_temp."/tmp/".$cod_curso."/");
		if (!file_exists($diretorio_temp."/tmp/".$cod_curso."/agenda/"))
			Arquivos::CriaDiretorio($diretorio_temp."/tmp/".$cod_curso."/agenda/");
	
		$dir=$diretorio_temp."/tmp/".$cod_curso."/agenda/".$cod_item."/";
		Arquivos::CriaDiretorio($dir);
	
	
		return($cod_item);
	}
	
	
	
	
}