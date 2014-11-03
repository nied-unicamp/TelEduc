<?php
require_once "geral.inc";
class Usuarios{
	
	static function RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso)
	{
		// 2Session
		$dbnamebase = $_SESSION['dbnamebase'];
	
		$query = "SELECT cod_usuario FROM ".$dbnamebase.".Usuario_curso WHERE cod_usuario_global = '".ConversorTexto::VerificaStringQuery($cod_usuario_global)."' AND cod_curso = '".ConversorTexto::VerificaStringQuery($cod_curso)."'";
		$res=AcessoSQL::Enviar($sock,$query);
		$linha=AcessoSQL::RetornaLinha($res);
		$cod_usuario=$linha[0];
	
		return ($cod_usuario);
	}
	
	static function PreencheuDadosPessoais($sock)
	{
		$query  = "select cod_usuario from Usuario ";
		$query .= "where cod_usuario = ".$_SESSION['cod_usuario_global_s']." and ";
		$query .= "rg is not NULL and ";
		$query .= "data_nasc is not NULL and ";
		$query .= "endereco is not NULL and ";
		$query .= "cidade is not NULL and ";
		$query .= "estado is not NULL and ";
		$query .= "pais is not NULL";
	
		$res    = AcessoSQL::Enviar($sock,$query);
		$res    = AcessoSQL::RetornaLinha($res);
	
		if($res != "")
			return true;
		else
			return false;
	}
	
	static function RetornaLinguaUsuario($sock,$cod_usuario, $cod_curso)
	{
		//   global $cod_lingua_s;
		// 2Session
		$dbnamebase = $_SESSION['dbnamebase'];
	
		$query  = "select U.cod_lingua ";
		$query .= "from ".$dbnamebase.".Usuario_curso UC ";
		$query .= "inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global) ";
		$query .= "where UC.cod_usuario=".ConversorTexto::VerificaNumeroQuery($cod_usuario)." AND UC.cod_curso=".ConversorTexto::VerificaStringQuery($cod_curso);
	
		$res    = AcessoSQL::Enviar($sock,$query);
	
		if (AcessoSQL::RetornaNumLinhas($res)>0)
		{
			$linha=AcessoSQL::RetornaLinha($res);
			return ($linha[0]);
		}
		else
		{
			// Se n� tem registro (?!?) retorna a lingua atual
			return ($_SESSION['cod_lingua_s']);
		}
	}
	
	/* *********************************************************************
	 VerificaAcessoAoCurso- Barra usu�ios que ainda n� foram aceitos ou
	que foram rejeitados de acessar a p�ina
	Entradas: $sock - Socket da base de dados (retorno do Conectar)
	BASE EXTERNA
	$cod_curso - C�igo do curso
	$cod_usuario - C�igo do usu�io a buscar a informa�o
	Saida: Envia mensagem de acesso negado e interrompe a execu�o, caso
	aluno seja i ou r
	*/
	static function VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario)
	{
		if($cod_usuario != "")
			$status=Usuarios::RetornaStatusUsuario($sock,$cod_curso,$cod_usuario);

		else
			//usuario inexistente
			$status = "I";
		// Definida em 'cursos.inc'
		$status_curso=Cursos::RetornaStatusCurso($sock, $cod_curso);
		// os usu�ios com status nesse array n� devem entrar no curso
		$array_status = array('i', 'r', 'a', 'f', 'z', 'v', 'I');
		//se for administrador, possui acesso ao curso
		if($_SESSION['cod_usuario_global_s'] == -1)
			$adm = true;
		else
			$adm = false;
	
		if ((!$adm) && (($status_curso=="N" && $status!="F") || in_array($status, $array_status)))
		{
			/* 13 - Acesso negado */
			$mensagemHead = Linguas::RetornaFrase($sock,13,-2);
	
			$mensagem="";
	
	
			if ($status_curso=="N" && $status!="F")
				// 30 - O curso ainda n� foi iniciado.
				$mensagem.=Linguas::RetornaFrase($sock,30,-2);
			else if ($status == 'i')
				// 14 - Sua inscri�o ainda n� foi avaliada. Um e-mail lhe ser�enviado quando a avalia�o de sua inscri�o for feita.
				$mensagem.=Linguas::RetornaFrase($sock,14,-2);
			else if ($status == 'r' || $status == 'a' || $status == 'f' || $status == 'z' || $status == 'v')
				// 15 - Sua inscri�o n� foi aceita. Voc�n� tem acesso a esse curso.
				$mensagem.=Linguas::RetornaFrase($sock,15,-2);
			else if ($status=="I")
				// 16 - Usu�io inexistente ou inv�ido.
				$mensagem.=Linguas::RetornaFrase($sock,16,-2);
	
			echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
			echo("<html lang=\"pt\">\n");
			echo("  <head>\n");
			echo("    <title>TelEduc - Erro</title>\n");
			echo("    <link href=\"js-css/ambiente.css\" rel=\"stylesheet\" type=\"text/css\" />\n");
			echo("    <script type=\"text/javascript\">\n");
			echo("      function Iniciar(){\n");
			echo("        document.getElementById('detalhes').style.display='none';\n");
			echo("      }\n");
			echo("      var status='none';\n");
			echo("      function changeStatus(){\n");
			echo("        if(status=='') status = 'none';\n");
			echo("        else status = '';\n");
			echo("        document.getElementById('detalhes').style.display=status;\n");
			echo("      }\n");
			echo("    </script>\n");
			echo("  </head>\n");
			echo("  <body onload=\"Iniciar();\">\n");
			echo("    <br />\n");
			echo("    <table id=\"container\" style=\"width:700px;\">");
			echo("      <tr>\n");
			echo("        <td colspan=\"2\" id=\"conteudo\" valign=\"top\">\n");
			echo("          <h4>".$mensagemHead."</h4>\n");
			echo("          <br />\n");
			echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
			echo("            <tr>\n");
			echo("              <td>\n");
			echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
			echo("                  <tr class=\"head\">\n");
			echo("                    <td width=\"32%\" class=\"alLeft\">\n");
			echo("                      Ocorreu um Erro\n");
			echo("                    </td>\n");
			echo("                  </tr>\n");
			echo("                  <tr>\n");
			echo("                    <td>\n");
			echo("                      <p>".$mensagem."</p>\n");
			echo("                    </td>\n");
			echo("                  </tr>\n");
			echo("                  <tr>\n");
			echo("                    <td style=\"text-align:right;\">\n");
			echo("                      <span class=\"link\" onclick=\"changeStatus();\">+ Detalhes do Erro</span>\n");
			echo("                    </td>\n");
			echo("                  </tr>\n");
			echo("                  <tr id=\"detalhes\">\n");
			echo("                    <td class=\"alLeft\" style=\"padding: 10px 50px 10px 50px;\">\n");
			echo("                      &nbsp;\n");
			echo("                    </td>\n");
			echo("                  </tr>\n");
			echo("                </table>\n");
			echo("              </td>\n");
			echo("            </tr>\n");
			echo("          </table>\n");
			echo("          <span class=\"btsNavBottom\">\n");
			echo("            <a href=\"../../../pagina_inicial/exibe_cursos.php\"><img src=\"imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></a>\n");
			echo("          </span>\n");
			echo("        </td>\n");
			echo("      </tr>\n");
			echo("      <tr>\n");
			echo("        <td valign=\"bottom\" height=\"80\"><a href=\"http://www.nied.unicamp.br\"><img src=\"imgs/logoNied.gif\" alt=\"nied\" border=\"0\" style=\"margin-right:8px;\" /></a><a href=\"http://www.ic.unicamp.br\"><img src=\"imgs/logoInstComp.gif\" alt=\"Instituto de Computa&ccedil;&atilde;o\" border=\"0\" style=\"margin-right:6px;\" /></a><a href=\"http://www.unicamp.br\"><img src=\"imgs/logoUnicamp.gif\" alt=\"UNICAMP\" border=\"0\" /></a></td>\n");
			echo("        <td valign=\"bottom\" id=\"rodape\">2006  - TelEduc - Todos os direitos reservados. All rights reserved - NIED - UNICAMP</td>\n");
			echo("      </tr>\n");
			echo("    </table>\n");
			echo("  </body>");
			echo("</html>");
	
			exit();
		}
	}
	
	/* *********************************************************************
	 RetornaStatusUsuario - Retorna o status do Usu�io
	Entradas: $sock - Socket da base de dados (retorno do Conectar)
	BASE DO CURSO
	$cod_curso - C�igo do curso
	$cod_usuario - C�igo do usu�io a buscar a informa�o
	Saida: I - aluno inv�ido ou inexistente
	i - Aluno inscrito
	a - Aluno desligado
	r - Aluno rejeitado
	A - Aluno Aceito
	f - Formador desligado
	F - Formador
	v - Visitante desligado
	V - Visitante
	z - Colaborador desligado
	Z - Colaborador
	OBS.: Esta fun�o retorna o status real do usu�io.
	Checar se n� �um formador querendo ter a vis� do aluno com
	$_SESSION['visao_aluno_s']==true
	*/
	static function RetornaStatusUsuario($sock,$cod_curso,$cod_usuario)
	{
		// 2Session
		$dbnamebase = $_SESSION['dbnamebase'];
	
		/* Se administrador, retorna como Formador */
		if ($cod_usuario==-1)
			return ("F");
	
		$query="select tipo_usuario from ".$dbnamebase.".Usuario_curso where cod_usuario=".$cod_usuario." and cod_curso=".$cod_curso;
		
		$res=AcessoSQL::Enviar($sock,$query);
		
		if (AcessoSQL::RetornaNumLinhas($res)>0)
		{
			$linha=AcessoSQL::RetornaLinha($res);
			return ($linha[0]);
		}
		else
		{
			return ("I");
		}
	}
	
	/* *********************************************************************
	 EFormador - Retorna true se usu�io for formador ou se for administrador do ambiente
	Entradas: $sock - Socket da base de dados (retorno do Conectar)
	$cod_curso - C�igo do curso
	$cod_usuario - C�igo do usu�io a buscar a informa�o
	Saida: true se �formador e este n� esta com visao de aluno, false caso contr�io
	OBS.: O coordenador aparece tamb� como formador
	*/
	static function EFormador($sock,$cod_curso,$cod_usuario)
	{
		$sock =  AcessoSQL::Conectar($cod_curso);
	
		$estado=Usuarios::RetornaStatusUsuario($sock,$cod_curso,$cod_usuario);
		//se for administrador
	
		if(($_SESSION['cod_usuario_global_s'] == -1) && (!isset($_SESSION['visao_aluno_s']) || !($_SESSION['visao_aluno_s'])))
			return true;
		else if ($estado=="F")
		{
			if (isset($_SESSION['visao_aluno_s']) && $_SESSION['visao_aluno_s'])
				return false;
	
			return true;
		}
		else
			return false;
	}
	
	/* *********************************************************************
	 EFormadorMesmo - Retorna true se usu�io for formador ou se for administrador do ambiente
	Entradas: $sock - Socket da base de dados (retorno do Conectar)
	$cod_curso - C�igo do curso
	$cod_usuario - C�igo do usu�io a buscar a informa�o
	
	Saida: true se �formador, false caso contr�io
	OBS.: O coordenador aparece tamb� como formador
	*/
	static function EFormadorMesmo($sock,$cod_curso,$cod_usuario)
	{
		$estado=Usuarios::RetornaStatusUsuario($sock,$cod_curso,$cod_usuario);
	
		//se for administrador
		if($_SESSION['cod_usuario_global_s'] == -1)
			return true;
		else if ($estado=="F")
			return true;
		else
			return false;
	}
	
	/* *********************************************************************
	 EColaborador - Retorna se o usuario eh colaborador
	entrada: $sock - sock de conexao
	$cod_usuario - codigo do usuario a verificar
	saida: true - o usuario eh colaborador.
	false - o usuario nao eh colaborador.
	*/
	static function EColaborador($sock, $cod_curso, $cod_usuario)
	{
	
		// 2Session
		$dbnamebase = $_SESSION['dbnamebase'];
		$query = "select tipo_usuario from ".$dbnamebase.".Usuario_curso where cod_usuario=".ConversorTexto::VerificaNumeroQuery($cod_usuario)." and cod_curso=".ConversorTexto::VerificaStringQuery($cod_curso);
		$res   = AcessoSQL::Enviar ($sock, $query);
		$linha = AcessoSQL::RetornaLinha ($res);
		return ($linha ['tipo_usuario'] == 'Z');
	}
	
	/* *********************************************************************
	 EVisitante - Retorna true se usuario for visitante
	Entradas: $sock - Socket da base de dados (retorno do Conectar)
	$cod_curso - C�igo do curso
	$cod_usuario - C�igo do usu�io a buscar a informa�o
	Saida: true se �aluno, false caso contr�io
	*/
	static function EVisitante($sock,$cod_curso,$cod_usuario)
	{
		$estado=Usuarios::RetornaStatusUsuario($sock,$cod_curso,$cod_usuario);
		if ($estado=="V")
			return true;
		else
			return false;
	}
	
	/* *********************************************************************
	 VerificaAcessoAFerramenta - Barra usu�ios de entrar em ferramentas
	desabilitadas
	Entradas: $sock - Socket da base de dados (retorno do Conectar)
	BASE EXTERNA
	$cod_curso - C�igo do curso
	$cod_usuario - C�igo do usu�io a buscar a informa�o
	$cod_ferramenta - Codigo da ferramenta
	Saida: Envia mensagem de acesso negado e interrompe a execu�o, caso a ferramenta
	esteja desabilitada (checa status de aluno)
	*/
	static function VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,$cod_ferramenta)
	{
		if (!Usuarios::TestaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,$cod_ferramenta))
		{
			// 59 - Ferramenta desabilitada
			die("<b>".Linguas::RetornaFrase($sock,59,-1)."</b>");
		}
	}
	
	/* *********************************************************************
	 TestaAcessoAFerramenta - verifica se um usu�ios tem acesso a uma ferramenta
	Entradas: $sock - Socket da base de dados (retorno do Conectar)
	BASE EXTERNA
	$cod_curso - C�igo do curso
	$cod_usuario - C�igo do usu�io a buscar a informa�o
	$cod_ferramenta - Codigo da ferramenta
	Saida: true, se possui acesso, false do contr�io.
	*/
	static function TestaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,$cod_ferramenta)
	{
		$status_usuario = Usuarios::RetornaStatusUsuario($sock,$cod_curso,$cod_usuario);
	
		$query  = "select status from Curso_ferramentas ";
		$query .= "where cod_ferramenta=".ConversorTexto::VerificaNumeroQuery($cod_ferramenta);
	
		$res = AcessoSQL::Enviar($sock,$query);
	
		$linha = AcessoSQL::RetornaLinha($res);
	
		return (!(
				// Ferramenta n�o � visivel por ningu�m ou
				$linha['status'] == 'D' ||
				// Ferramenta � vis�vel apenas para formadores e
				// usu�rio atual n�o � formador
				($linha['status'] == 'F' && $status_usuario != 'F')
		)
		);
	}
	
	/* *********************************************************************
	 MarcaAcesso - Marca o acesso a determinada ferramenta pelo aluno
	Entradas: $sock - sock aberto para a base do curso (Conectar($cod_curso))
	$cod_usuario - C�igo do usu�io a buscar a informa�o
	$cod_ferramenta - C�igo da ferramenta. "" se Entrada no
	TelEduc
	Saida: nenhuma
	*/
	static function MarcaAcesso($sock,$cod_usuario,$cod_ferramenta)
	{
		if ($cod_usuario<0)
			return;
		$agora=time();
		if ($cod_ferramenta!="")
		{
			$query="select cod_ferramenta from Curso_acessos where cod_usuario=".ConversorTexto::VerificaNumeroQuery($cod_usuario)." order by data desc limit 1";
			$res=AcessoSQL::Enviar($sock,$query);
			$linha=AcessoSQL::RetornaLinha($res);
			if ($linha['cod_ferramenta']!=$cod_ferramenta)
			{
				$query="insert into Curso_acessos values (".ConversorTexto::VerificaNumeroQuery($cod_usuario).",".$agora.",".ConversorTexto::VerificaNumeroQuery($cod_ferramenta).")";
				AcessoSQL::Enviar($sock,$query);
			}
		}
		else
		{
			$query="select cod_ferramenta from Curso_acessos where cod_usuario=".ConversorTexto::VerificaNumeroQuery($cod_usuario)." order by data desc limit 1";
			$res=AcessoSQL::Enviar($sock,$query);
			$num=AcessoSQL::RetornaNumLinhas($res);
			$linha=AcessoSQL::RetornaLinha($res);
			if ($linha['cod_ferramenta']!="" || $num==0)
			{
				$query="insert into Curso_acessos (cod_usuario,data) values (".ConversorTexto::VerificaNumeroQuery($cod_usuario).",".$agora.")";
				AcessoSQL::Enviar($sock,$query);
			}
		}
	}
	
	/* *********************************************************************
	 ECoordenadorMesmo - Retorna true se usu�io for coordenador ou se for
	administrador do ambiente
	Entradas: $sock - Socket da base de dados (retorno do Conectar)
	$cod_curso - C�igo do curso
	$cod_usuario - C�igo do usu�io a buscar a informa�o
	Saida: true se �coordenador, false caso contr�io
	*/
	static function ECoordenadorMesmo($sock,$cod_curso,$cod_usuario)
	{
		$estado=Usuarios::RetornaStatusUsuario($sock,$cod_curso,$cod_usuario);
	
		//se for administrador
		if($cod_usuario == -1)
			return true;
		else if ($estado=="F")
		{
			$query="select cod_coordenador from Cursos where cod_curso="./*ConversorTexto::VerificaNumeroQuery*/($cod_curso); //TODO - Ver VerificaNumeroQuery, não está funcionando
			$res=AcessoSQL::Enviar($sock,$query);
			$linha=AcessoSQL::RetornaLinha($res);
			if ($linha[0]==$cod_usuario)
				return true;
		}
		return false;
	}
	
	/* *********************************************************************
	 PenultimoAcesso - Retorna o timestamp do penltimo acesso a determinada
	ferramenta
	Entradas: $sock - sock aberto para a base do curso (Conectar($cod_curso))
	$cod_usuario - C�igo do usu�io a buscar a informa�o
	$cod_ferramenta - C�igo da ferramenta. "" se Entrada no
	TelEduc
	Saida: timestamp do ltimo acesso
	*/
	static function PenultimoAcesso($sock,$cod_usuario,$cod_ferramenta)
	{
		if ($cod_usuario<0)
			return time();
		if ($cod_ferramenta!="")
			$query="select data from Curso_acessos where cod_usuario=".ConversorTexto::VerificaNumeroQuery($cod_usuario)." and cod_ferramenta=".ConversorTexto::VerificaNumeroQuery($cod_ferramenta)." order by data desc limit 2";
		else
			$query="select data from Curso_acessos where cod_usuario=".ConversorTexto::VerificaNumeroQuery($cod_usuario)." and cod_ferramenta is NULL order by data desc limit 2";
		$res=AcessoSQL::Enviar($sock,$query);
		if (AcessoSQL::RetornaNumLinhas($res)>1)
		{
			$linha=AcessoSQL::RetornaLinha($res);
			$linha=AcessoSQL::RetornaLinha($res);
			return $linha[0];
		}
		else
			return 0;
	}
	
	static function AtualizaVisita($sock, $cod_usuario) {
		$agora = time();
	
		// caso do admtele
		if($cod_usuario < 0) {
			return false;
		}
	
		// pega a data de ultimo acesso do usuario no curso
		$query = "select ultimo_acesso from Usuarios_online where cod_usuario = ".ConversorTexto::VerificaNumeroQuery($cod_usuario);
		$res=AcessoSQL::Enviar($sock,$query);
		$linha=AcessoSQL::RetornaLinha($res);
	
		// se o usuario jah estava inserido na tabela, faz um update, senao faz um insert
		if($linha["ultimo_acesso"]) {
			$query = "update Usuarios_online set ultimo_acesso = ".$agora.", logado = 1 where cod_usuario = ".ConversorTexto::VerificaNumeroQuery($cod_usuario);
		} else {
			$query = "insert into Usuarios_online values (".ConversorTexto::VerificaNumeroQuery($cod_usuario).", ".$agora.", 1)";
		}
		AcessoSQL::Enviar($sock,$query);
	}
	
	/* *********************************************************************
	 RetornaEmailUsuario - Retorna o email do usuario
	Entradas: $sock - sock da base externa
	$cod_usuario - o cod_usuario_global do usuario
	Saida: email
	*/
	static function RetornaEmailUsuario1($cod_usuario)
	{
		$sock = AcessoSQL::Conectar("");
		$query="select email from Usuario where cod_usuario=".$cod_usuario."";
		$res=AcessoSQL::Enviar($sock,$query);
		$linha=AcessoSQL::RetornaLinha($res);
		//Desconectar($sock);
		return ($linha['email']);
	}
	
	static function RetornaUsuariosOnline($sock, $time_out) {
		$agora=time();
	
		$query="select cod_usuario from Usuarios_online where (".$agora." - ultimo_acesso < ".$time_out.") and logado = 1";
		$res=AcessoSQL::Enviar($sock,$query);
		$linha=AcessoSQL::RetornaArrayLinhas($res);
	
		return $linha;
	}
	
	/* *********************************************************************
	 NomeUsuario - Retorna o nome do usuario
	Entradas: $sock - sock aberto para a base do curso (Conectar($cod_curso))
	$cod_usuario - C�igo do usu�io a buscar a informa�o
	$cod_curso - Codigo do curso
	Saida: nome
	*/
	static function NomeUsuario($sock, $cod_usuario, $cod_curso)
	{
		// 2Session
		$dbnamebase = $_SESSION['dbnamebase'];
	
		if ($cod_usuario == -2)
			return ("Visitante");
	
		$cod_usuario = $cod_usuario;
		$cod_curso   = $cod_curso;
		if ($cod_curso === '' || $cod_usuario === '') return '';
	
		$query  = "select U.nome ";
		$query .= "from ".$dbnamebase.".Usuario U ";
		$query .= "inner join ".$dbnamebase.".Usuario_curso UC ON (UC.cod_usuario_global=U.cod_usuario AND UC.cod_usuario = ".$cod_usuario." AND UC.cod_curso=".$cod_curso.")";
	
		$res=AcessoSQL::Enviar($sock,$query);
		$linha=AcessoSQL::RetornaLinha($res);
		$nome_usuario=$linha[0];
		if ($nome_usuario != Usuarios::AjustaNome($nome_usuario))
		{
			$cod_usuario_global = Usuarios::RetornaCodigoUsuarioGlobal($sock, $cod_usuario, $cod_curso);
			$nome_usuario=Usuarios::AjustaNome($nome_usuario);
			$query="update ".$dbnamebase.".Usuario set nome='".addslashes($nome_usuario)."' where cod_usuario=".$cod_usuario_global;
			AcessoSQL::Enviar($sock,$query);
		}
	
		return ($nome_usuario);
	}
	
	/* *********************************************************************
	 AjustaNome - Ajusta o nome do usuario em rela�o as maiusculas e minusculas
	Entradas: $nome_usuario
	Saida: nome com maiusculas e minsculas corrigidas
	*/
	static function AjustaNome($nome_usuario)
	{
		$tmp=explode(" ",$nome_usuario);
		if (count($tmp)>0)
		{
			foreach($tmp as $cod => $nome)
			{
				$tmp[$cod]=strtr($nome,"ABCDEFGHIJLKMNOPQRSTUVWXYZ������������","abcdefghijlkmnopqrstuvwxyz�����������");
				$primeira=substr($tmp[$cod],0,1);
				$resto=substr($tmp[$cod],1);
				$primeira=strtr($primeira,"abcdefghijlkmnopqrstuvwxyz�����������","ABCDEFGHIJLKMNOPQRSTUVWXYZ������������");
				$tmp[$cod]=$primeira.$resto;
			}
			$saida=implode(" ",$tmp);
		}
		return $saida;
	}
	
	/* *********************************************************************
	 RetornaCodigoUsuarioGlobal - Retorna codugo global do usuario
	Entradas: $sock - Sock aberto para a base
	$cod_usuario - Codigo do usuario no curso especificado
	$cod_curso - Codigo do curso
	Saida: Codigo do usuario no curso especificado
	*/
	static function RetornaCodigoUsuarioGlobal($sock, $cod_usuario, $cod_curso)
	{
		// 2Session
		$dbnamebase = $_SESSION['dbnamebase'];
	
		$query = "SELECT cod_usuario_global FROM ".$dbnamebase.".Usuario_curso WHERE cod_usuario = '".ConversorTexto::VerificaStringQuery($cod_usuario)."' AND cod_curso = '".ConversorTexto::VerificaStringQuery($cod_curso)."'";
		$res=AcessoSQL::Enviar($sock,$query);
		$linha=AcessoSQL::RetornaLinha($res);
		$cod_usuario_global=$linha[0];
	
		return ($cod_usuario_global);
	}
	
	/* *********************************************************************
	 ECoordenador - Retorna true se usu�io for coordenador ou se for administrador do ambiente
	Entradas: $sock - Socket da base de dados (retorno do Conectar)
	$cod_curso - C�igo do curso
	$cod_usuario - C�igo do usu�io a buscar a informa�o
	Saida: true se �coordenador e este n� esta com visao de aluno, false caso contr�io
	*/
	static function ECoordenador($sock,$cod_curso,$cod_usuario)
	{
		//   global $visao_aluno_s;
		$estado=Usuarios::RetornaStatusUsuario($sock,$cod_curso,$cod_usuario);
		//se for administrador
		if($cod_usuario == -1)
			return true;
		else if ($estado=="F")
		{
			$query="select cod_coordenador from Cursos where cod_curso=".ConversorTexto::VerificaNumeroQuery($cod_curso);
			$res=AcessoSQL::Enviar($sock,$query);
			$linha=AcessoSQL::RetornaLinha($res);
			if ($linha[0]==$cod_usuario)
			if ($_SESSION['visao_aluno_s']){
				return false;
			} else { return true; }
		}
		return false;
	}
	
	static function AtualizaFerramentasNova($sock,$cod_ferramenta,$tipo_usuario)
	{
		$query="replace Ferramentas_nova (cod_ferramenta, tipo_compartilhamento, data) values (".$cod_ferramenta." , '".ConversorTexto::VerificaStringQuery($tipo_usuario)."' , ".time().")" ;
		//if ($tipo_usuario=="F")
		//  $query.=" and tipo_compartilhamento='F'";
		AcessoSQL::Enviar($sock,$query);
	}

	
	
}
?>