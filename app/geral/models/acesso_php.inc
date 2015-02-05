<?php

$ferramenta_login = 'login';
$ferramenta_admin = 'admin';

$view_login = '../../'.$ferramenta_login.'/views/';
$view_admin = '../../'.$ferramenta_admin.'/views/';

/**
 * Classe AcessoPHP do modulo geral
 * @author     TelEduc
 * @copyright  2014 TelEduc
 * @license    http://teleduc.org.br/
 */
class AcessoPHP{
	
	/**
	 * Metódo estático para verificar se as credências do usuário estão corretas buscando essas informações na tabela Usuário
	 * Possue uma exceção para o login de usuário admtele ele é buscado na tabela Config
	 *
	 * @param string $login Login salvo na tabela Usuário ou Config
	 * @param string $senha Senha relacionada ao login salvo na tabela Usuário ou Config
	 * @return int Retorna o cod_usuario proveniente da tabela Usuário se o login e senha forem válidos,  0 se houver algum problema e -1 se login for administrador relacionado a tabela Config.
	 */
	static function VerificaLoginSenha ($login, $senha)
	{
		$senha_crypt = crypt($senha,"AA");
		$sock = AcessoSQL::Conectar("");
	
		if ($login == "admtele") {
			$query = "select valor from Config where item = 'admtele'";
		}
		else {
			$query = "select senha, cod_usuario, confirmacao from Usuario where login='".ConversorTexto::VerificaStringQuery($login)."' and senha='".ConversorTexto::VerificaStringQuery($senha_crypt)."'";
			//$query = "select senha, cod_usuario, confirmacao from Usuario where login='".VerificaStringQuery($login)."'";
		}
	
		$res = AcessoSQL::Enviar($sock,$query);
		$num = AcessoSQL::RetornaNumLinhas($res);
		$linha = AcessoSQL::RetornaLinha($res);
	
		if ($login == "admtele") {
			$cod_usuario = -1;
			$confirmacao = "";
			$senha_db = $linha['valor'];
		} else {
			$cod_usuario = $linha['cod_usuario'];
			$confirmacao = $linha['confirmacao'];
			$senha_db = $linha['senha'];
		}
	
		return (($num < 1) || ($senha_db != $senha_crypt) ? 0 : $cod_usuario);
	}
	
	/**
	 * Neste metódo estático é verificado se o Login da Tabela Usuário jah teve o email confirmado.
	 *
	 * @param string $login Login salvo na tabela Usuários
	 * @return boolean Se validado True, caso contrario False
	 */
	static function VerificaConfirmacaoEmail ($login)
	{
		$sock = AcessoSQL::Conectar("");
		$query = "select confirmacao from Usuario ";
		$query.= "where login='".$login."'";
		$res = AcessoSQL::Enviar($sock, $query);
		$linha = AcessoSQL::RetornaLinha($res);
		if($linha[0]=="" || $linha[0]=="NULL") {
			return true;
		}
		return false;
	}
	
	/**
	 * O metódo estático VerificaAutenticacaoAdministracao se basea na sessão de login.
	 * Caso a sessão nao exista o usuario será redirecionado para a tela de login.
	 * Caso a sessão exista, mas não seja do administrador ele é  enviado para a tela inicial.
	 * Caso a sessão exista e seja administrador nao acontecera nada.
	 */
	static function VerificaAutenticacaoAdministracao()
	{
		$sock=AcessoSQL::Conectar("");
	
		if (!isset($_SESSION['login_usuario_s']))
		{
			require_once $view_login.'autenticacao_cadastro.php';
			AcessoSQL::Desconectar($sock);
			exit;
		}
		else if($_SESSION['login_usuario_s'] != "admtele")
		{
			require_once '../views/topo_tela_inicial.php';
			/* Inicio do JavaScript */
			echo("    <script language=\"javascript\"  type=\"text/javascript\">\n");
	
			echo("      function Iniciar() {\n");
			echo("        startList();\n");
			echo("      }\n");
	
			echo("    </script>\n");
			/* Fim do JavaScript */
			require_once $view_admin.'menu_principal_tela_inicial.php';
			echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
			/* 4 - Autorizaï¿½o necessï¿½ia para entrar no Ambiente de Administraï¿½o do TelEduc. */
			/* 23 - Voltar */
			echo (_("msg4_-2")." <a href=\"#\" onclick=\"history.back(); return false;\">"._("msg23_-1")."</a>\n");
			echo("</td>\n");
			echo("</tr>\n");
			require_once $view_admin.'rodape_tela_inicial.php';
			echo("</body>\n");
			echo("</html>\n");
			AcessoSQL::Desconectar($sock);
			exit;
		}
	
		AcessoSQL::Desconectar($sock);
	}
	
	/**
	 * Metódo estático que verefica a autenticação do usuario baseando-se
	 * no cod_curso da tabela Cursos
	 *
	 * @param int $cod_curso Número de identificação primaria do usuario da tabela Cursos
	 * @return int -1 Administrador (Formador), -2 Visitante, -3 Acesso negado e caso seja maior que 0 é cod_usuario referente a tabela Usuario
	 */
	static function VerificaAutenticacao($cod_curso)
	{
	
		/* Informaï¿½es sobre alunos. cod_usuario>0 aluno normal.
		 cod_usuario = -1 Administrador (Formador).
		cod_usuario = -2 visitante. (Acesso Passivo).
		cod_usuario = -3 (Acesso negado ao curso).
		*/
	
		/* Adquire as variaveis de verificaï¿½o */
		//   global /*$_SESSION,$visitante_s*/,$cod_curso_s,/*$cod_lingua_s*/,$logout_flag_s;
	
		/* Obtï¿½ a raiz_www */
		$sock = AcessoSQL::Conectar("");
		$query = "select diretorio from Diretorio where item = 'raiz_www'";
		$res = AcessoSQL::Enviar($sock,$query);
		$linha = AcessoSQL::RetornaLinha($res);
		$raiz_www = $linha[0];
		AcessoSQL::Desconectar($sock);
	
		//$caminho = $raiz_www."/pagina_inicial";
		$caminho = $raiz_www."/app/login/views";
	
		/* Se quem esta acessando o ambiente eh um visitante */
		if ($_SESSION['visitante_s']=="sim")
		{
		
			$sock=AcessoSQL::Conectar("");
			$query="select acesso_visitante from Cursos where cod_curso=".ConversorTexto::VerificaNumeroQuery($cod_curso);
			$res=AcessoSQL::Enviar($sock,$query);
			$linha=AcessoSQL::RetornaLinha($res);
			if ($linha[0]!="A")
			{
				/* 12 - Este curso nï¿½ permite a entrada de visitantes. */
				/* 23 - Voltar. */
				echo _("msg12_-2").' <a href=# onclick=history.go(-1);>'._("msg23_-1").'</a>';
				AcessoSQL::Desconectar($sock);
				exit;
			}
			AcessoSQL::Desconectar($sock);
			return (-2);
		}
		else
		{
			
			/* Se quem esta acessando nao esta logado */
			if ((!isset($_SESSION['login_usuario_s'])))
			{
				$sock=AcessoSQL::Conectar("");
	
				/* Salva a URL de acesso, para o caso de precisar redirecionar */
				$_SESSION['url_de_acesso'] = $_SERVER['REQUEST_URI'];
	
		  /* Usuario Ã© direcionado para a pagina de autenticacao */
				header("Location:{$caminho}/autenticacao_cadastro.php?cod_curso=".$cod_curso);
	
				AcessoSQL::Desconectar($sock);
				exit;
			}
			else
			{
				/* Se quem esta acessando, esta logado e eh o admtele */
				if (isset($_SESSION['login_usuario_s']) && ($_SESSION['login_usuario_s']=="admtele"))
				{
					
					$sock=AcessoSQL::Conectar("");
	
					//Desconectar($sock);
					if (isset($cod_curso))
					{
						$_SESSION['cod_usuario_s']=Usuarios::RetornaCodigoUsuarioCurso($sock, $_SESSION['cod_usuario_global_s'],$cod_curso);
					}
	
					AcessoSQL::Desconectar($sock);
					return (-1);
				}
				else //VerificaAcessoCurso($_SESSION['cod_usuario_s'], $cod_curso)
				{
					// Erro de lï¿½gica, condiï¿½ï¿½o verificada anteriormente
					/*        if (!isset($_SESSION['login_usuario_s'])) //($num<1 || $linha[0]!=$senha)
					{
					//Desconectar($sock);
					$sock=Conectar("");
	
					// Salva a URL de acesso, para o caso de precisar redirecionar
					$_SESSION['url_de_acesso'] = $_SERVER['REQUEST_URI'];
					header("Location:{$caminho}/autenticacao_cadastro.php?cod_curso=".$cod_curso);
	
					Desconectar($sock);
					exit;
					} */
					/* Se o usuï¿½rio efetuou o logout, requisita novamente as informaï¿½es para acesso. */
					if (isset($_SESSION['logout_flag_s']) && $_SESSION['logout_flag_s'] == 1)
					{
					
					$_SESSION['logout_flag_s'] = 0;
	
					//Desconectar($sock);
					$sock=AcessoSQL::Conectar("");
	
					header("Location:{$caminho}/autenticacao_cadastro.php?cod_curso=".$cod_curso);
	
					AcessoSQL::Desconectar($sock);
					exit;
				}
					else
					{
						//Desconectar($sock);
					
						$sock=AcessoSQL::Conectar("");
	
						
						$query = "select U.senha, U.cod_usuario from Usuario U, Usuario_curso UC 
								where UC.cod_usuario_global = U.cod_usuario and 
								UC.cod_curso = ".$cod_curso." and login = '".$_SESSION['login_usuario_s']."'";
						
           
				$result = AcessoSQL::Enviar($sock, $query);
				$num = AcessoSQL::RetornaNumLinhas($result);
				$linha = AcessoSQL::RetornaLinha($result);
	    	if ($num > 0)
		    	{
		    			$_SESSION['cod_curso_s']=$cod_curso;
						$cod_usuario_s=Usuarios::RetornaCodigoUsuarioCurso($sock, $_SESSION['cod_usuario_global_s'], $cod_curso);
						$cod_lin=Usuarios::RetornaLinguaUsuario($sock,$linha[1], $cod_curso);
						//Desconectar($sock);
						// Retirado trecho do codigo abaixo pois estava mudando a lingua escolhida para a lingua no BD.
						//              if ($cod_lin!=$_SESSION['cod_lingua_s'])
							//              {
						//                $sock=Conectar("");
						//                MudancaDeLingua($sock, $cod_lin);
						//	        //desconectar aqui??
						//              }
	
						/*if(!Usuarios::PreencheuDadosPessoais($sock))
						{
						AcessoSQL::Desconectar($sock);
						header("Location:{$caminho}/preencher_dados.php?cod_curso=".$cod_curso."&acao=preencherDados&atualizacao=true");
						}*/
	
						AcessoSQL::Desconectar($sock);
						return ($linha[1]);
						}
						else
						{
						return (-3);
						}
						}
						}
						}
						}
	
						echo("Se vocï¿½estï¿½vendo essa mensagem, ocorreu um erro na codificaï¿½o dos IFs");
						exit;
						return -1;
	}
	
	/* *********************************************************************
	 GeraSenha - Gera uma senha aleatoria para o usuario
	Entrada: nenhuma
	Saida: senha gerada de 8 caracteres
	*/
	static function GeraSenha()
	{
		//$caracteres="ABCDEFGHJLKMNPQRSTUVWXYZabcdefghjmnpqrstuvwxyz23456789";
		$caracteres="abcdefghjkmnpqrstuvwxyz23456789";
		list($usec, $sec) = explode(' ', microtime());
		mt_srand((float) $sec + ((float) $usec * 100000));
	
		$senha="";
	
		for ($i=0;$i<8;$i++)
		{
		//$senha.=$caracteres[mt_rand(0,53)];
		$senha.=$caracteres[mt_rand(0,30)];
		}
		return $senha;
	}
}
   
?>