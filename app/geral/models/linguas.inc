<?php

class Linguas {
	
	static function RetornaFrase($sock, $cod_texto, $cod_ferramenta)
	{
		//   Global $cod_lingua_s,$lista_frases_s;
	
		// 2Session
		$dbnamebase = $_SESSION['dbnamebase'];
	
		$cod_lingua= Sessao::CodLinguaS();
	
		if (!isset($_SESSION['lista_frases_s'][$cod_ferramenta]))
		{
			$query  = "SELECT texto FROM ".$dbnamebase.".Lingua_textos WHERE ";
			$query .= "cod_texto=".ConversorTexto::VerificaNumeroQuery($cod_texto)." AND ";
			$query .= "cod_lingua=".ConversorTexto::VerificaNumeroQuery($cod_lingua)." AND ";
			$query .= "cod_ferramenta=".ConversorTexto::VerificaNumeroQuery($cod_ferramenta);
	
			if($id_query = AcessoSQL::Enviar($sock, $query))
			{
				$linha = AcessoSQL::RetornaLinha($id_query);
				$texto = $linha[0];
				if (! isset($texto))
					$texto = "(ERRO : Texto correspondente a essa l&iacute;ngua n&atilde;o cadastrado: ".$cod_texto.")";
			}
			else
			{
				// Erro na execu�o da cl�sula : terminar o programa
				$msg  = "</script></table></div></table></div></table></div>";
				$msg .= "<h3>ERRO : N&atilde;o foi poss&iacute;vel executar a cl&aacute;usula SQL</h3>";
				$msg .= "<br><strong>SQL : </strong><br>".$query;
				$msg .= "<br><strong>Mensagem de erro do MySql : </strong><br>".mysql_error();
				$msg .= "<br><a href=\"#\" onclick=\"history.go(-1);\">Voltar</a>";
				die($msg);
			}
		}
		else
		{
			$texto=$_SESSION['lista_frases_s'][$cod_ferramenta][$cod_texto];
		}
	
		return(ConversorTexto::ConverteAspas2Html($texto));
	}
	
	static function RetornaFraseDaLista($textos, $cod_texto)
	{
		if(ConversorTexto::ConverteAspas2Html($textos[$cod_texto]) != "")
			return(ConversorTexto::ConverteAspas2Html($textos[$cod_texto]));
		else{
			switch(Sessao::CodLinguaS()){
				case 1://Portugu�s
					return("Falta o texto");
				case 2://Espanhol
					return("Falta texto");
				case 3://Ingl�s
					return("Missing Text");
				case 4://Portugu�s PT
					return("Falta o texto");
			}
		}
	}
	
	static function RetornaListaDeFrases($sock, $cod_ferramenta)
	{
		//   Global $cod_lingua_s,$lista_frases_s;
	
		// 2Session
		$dbnamebase = $_SESSION['dbnamebase'];
	
		$cod_lingua=Sessao::CodLinguaS();
	
		/* Se ja estiver em memoria, vai direto... */
		//if (Linguas::ListaDeFrasesS($sock, $cod_ferramenta!="") && count(Linguas::ListaDeFrasesS($sock, $cod_ferramenta))>0)
			//return (Linguas::ListaDeFrasesS($sock, $cod_ferramenta));
	
		/* Evitar erro caso a variavel $cod_lingua esteja vazia. Valor default = 1 (pt)*/
		if (is_null($cod_lingua))
			$cod_lingua = 1;
		$query  = "SELECT cod_texto, texto FROM ".$dbnamebase.".Lingua_textos WHERE ";
		$query .= "cod_lingua = ".ConversorTexto::VerificaNumeroQuery($cod_lingua)." AND ";
		$query .= "cod_ferramenta = ".ConversorTexto::VerificaNumeroQuery($cod_ferramenta)." ";
		$query .= "ORDER BY cod_texto";
		$textos = "";
		
		if ($id_query = AcessoSQL::Enviar($sock, $query))
		{
			unset($textos);
	
			if (AcessoSQL::RetornaNumLinhas($id_query) > 0)
			while($linha = AcessoSQL::RetornaLinha($id_query))
				$textos[$linha["cod_texto"]] = $linha["texto"];
		}
		else
		{
			// Erro na execução da cláusula : terminar o programa
			$msg = "<h3>ERRO : N&atilde;o foi poss&iacute;vel executar a cl&aacute;usula SQL</h3>";
			$msg .= "<br><b>SQL : </b><br>".$query;
			$msg .= "<br><b>Mensagem de erro do MySql : </b><br>".mysql_error();
			$msg .= "<br><a href=\"#\" onclick=\"history.go(-1);\">Voltar</a>";
			die($msg);
		}
		//$_SESSION['lista_frases_s'][$cod_ferramenta]=$textos;
	
		return($textos);
	}
	
	static function MudancaDeLingua($sock, $cod_lingua)
	{
		//   Global $cod_lingua_s, $lista_frases_s;
	
		if ($_SESSION['cod_lingua_s'] != $cod_lingua)
		{
			$_SESSION['cod_lingua_s'] = $cod_lingua;
			foreach($_SESSION['lista_frases_s'] as $cod_fer => $lista)
			{
				MemorizaListaDeFrases($sock,$cod_fer);
			}
		}
	
	}	
	/* ************************************************************************
	 ListaLinguas - Retorna a lista de linguas cadastradas
	Entrada: $sock - BASE Externa
	Saida: array [$cod_lingua]=nome
	*/
	static function ListaLinguas($sock)
	{
		//   Global $cod_lingua_s;
	
		// 2Session
		$dbnamebase = $_SESSION['dbnamebase'];
		$_SESSION['cod_lingua_s'] = Sessao::CodLinguaS();
		$query="select cod_texto 'cod_lingua',texto 'lingua' from ".$dbnamebase.".Lingua_textos where cod_ferramenta=-6 and cod_lingua=".$_SESSION['cod_lingua_s']." order by cod_texto";
		$res=AcessoSQL::Enviar($sock,$query);
		$saida = "";
		unset ($saida);
		while ($linha=AcessoSQL::RetornaLinha($res))
		if($linha['cod_lingua']!=2){ //retirando temporariamente o espanhol, por falta de tradu��o
			$saida[$linha['cod_lingua']]=$linha['lingua'];
		}
		return $saida;
	}
	
	static function LinguaLocal($sock,$cod_lin){
		$dbnamebase = $_SESSION['dbnamebase'];
		$query = "select texto from ".$dbnamebase.".Lingua_textos where cod_ferramenta=-6 and cod_lingua=".$cod_lin." and cod_texto=".$cod_lin."";
		$res   = AcessoSQL::Enviar($sock,$query);
		$linha = AcessoSQL::RetornaLinha($res);
		$texto = $linha['texto'];
		return($texto);
	
	}
	
	
}
?>