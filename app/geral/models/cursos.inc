<?php
require_once 'acesso_sql.inc';
require_once 'conversor_texto.inc';
class Cursos{
	/* *********************************************************************
	 RetornaStatusCurso - Retorna se o curso j�est�em andamento, ou se j�
	se encerrou
	Entradas: $sock - Socket da base de dados (retorno do Conectar)
	BASE DO CURSO
	$cod_curso - Curso em que ele est�
	Saida: N - N� come�u ainda
	A - Em Andamento
	E - Encerrado
	I - Invalido
	*/
	static function RetornaStatusCurso($sock,$cod_curso)
	{
		$query="select curso_inicio,curso_fim from Cursos where cod_curso=".$cod_curso;
		$res=AcessoSQL::Enviar($sock,$query);
		if (AcessoSQL::RetornaNumLinhas($res)>0)
		{
			$linha=AcessoSQL::RetornaLinha($res);
			$hoje=time();
			$ontem=$hoje - 86400;
			if ($linha[0]>$hoje)
				return ("N");
			else if ($linha[1]>=$ontem)
				return ("A");
			else
				return ("E");
		}
		else
		{
			return ("I");
		}
	}
	
	/* *********************************************************************
	 NomeCurso - Retorna o nome do curso
	Entrada: $sock - SOCK Externo
	Saida: string contendo o nome do curso
	*/
	static function NomeCurso($sock, $cod_curso)
	{
		$query="select nome_curso from Cursos where cod_curso = ".$cod_curso;
		$res=AcessoSQL::Enviar($sock,$query);
		$linha=AcessoSQL::RetornaLinha($res);
		return $linha['nome_curso'];
	}
	
	static function PreparaAjuda($sock, $cod_curso,$cod_ferramenta,$cod_pagina=null, $cod_usuario) {
	
		//   global $cod_lingua_s,$sock,$cod_usuario,$bibliotecas;
		$bibliotecas="../views/";
		$ajuda_nao_encontrada = true;
	
		if (isset($cod_pagina))
		{
			$eformador=Usuarios::EFormador($sock,$cod_curso,$cod_usuario);
			$sock = AcessoSQL::MudarDB($sock,"");
			$query="select nome_pagina,texto from Ajuda where cod_ferramenta=".ConversorTexto::VerificaNumeroQuery($cod_ferramenta)." and cod_pagina=".ConversorTexto::VerificaNumeroQuery($cod_pagina)." and cod_lingua=".ConversorTexto::VerificaNumeroQuery($_SESSION['cod_lingua_s'])." and tipo_usuario='".($eformador ? 'F' : 'A' )."'";
			$res=AcessoSQL::Enviar($sock,$query);
			if (AcessoSQL::RetornaNumLinhas($res)>0)
			{
				$ajuda_nao_encontrada=false;
				$ajuda_link=$bibliotecas."../ajuda/ajuda.php?cod_curso=".$cod_curso."&amp;cod_ferramenta=".$cod_ferramenta."&amp;cod_pagina=".$cod_pagina."&amp;tipo_usuario=".($eformador ? 'F' : 'A' );
				$html_link="<li><img src=\"../../../web-content/imgs/icAjuda.gif\" border=\"0\" alt=\"Ajuda\" />&nbsp;</li>";
				$html_link.="<li><a href=\"".$ajuda_link."\" onclick=\"window.open('".$ajuda_link."','AjudaDisplay','width=600,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');return false;\" target=\"blank\">Ajuda</a></li>";
				$html_link.="<li>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;</li>";
			}
			$sock = AcessoSQL::MudarDB($sock,$cod_curso);
		}
	
		// Esse ficar�provis�iamente, at�que todas as p�inas estiverem listadas
		if ($ajuda_nao_encontrada)
		{
			//     // Pegando o subtitulo da ferramenta para usar para linkar a ajuda
			//     $tmp=explode("> - ",$cabecalho);
			//     $tmp1=explode("</b>",$tmp[1]);
			//     $aname=RetiraEspacoEAcentos($tmp1[0]);
	
			// Checando se existe o diret�io com a Ajuda da ferramenta
			if (Usuarios::EFormador($sock,$cod_curso,$cod_usuario))
				$arquivo=$cod_lingua_s."_".$cod_ferramenta."_f.html";
			else
				$arquivo=$cod_lingua_s."_".$cod_ferramenta."_a.html";
	
			if (file_exists($bibliotecas."../ajuda/".$arquivo))
			{
				$ajuda_link=$bibliotecas."../ajuda/".$arquivo;
				// ?? - Ajuda
				$html_link="<li><img src=\"../imgs/icAjuda.gif\" border=\"0\" alt=\"Ajuda\" />&nbsp;</li>";
				$html_link.="<li><a href=\"".$ajuda_link."\" onClick=\"window.open('".$ajuda_link."','AjudaDisplay','width=600,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');return false;\" target=\"blank\">Ajuda</a></li>";
				$html_link.="<li>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;</li>";
			}
		}
		return $html_link;
	}
	
	/* *********************************************************************
	 CompletarDadosCurso - Retorna True se faltam dados do curso para completar
	Entradas: $sock - Socket da base de dados (retorno do Conectar)
	$cod_curso - Curso em que ele est�
	Saida: true se precisa completar dados
	*/
	static function CompletarDadosCurso($sock,$cod_curso)
	{
		$query="select inscricao_inicio,inscricao_fim,curso_inicio,curso_fim,informacoes,publico_alvo,tipo_inscricao from Cursos where cod_curso=".$cod_curso;
		$res=AcessoSQL::Enviar($sock,$query);
		$linha=AcessoSQL::RetornaLinha($res);
		if ($linha['inscricao_inicio']=="" || $linha['inscricao_fim']=="" || $linha['curso_inicio']=="" || $linha['curso_fim']=="" || $linha['informacoes']=="" || $linha['publico_alvo']=="" || $linha['tipo_inscricao']=="")
			return true;
		return false;
	}
	
	
}