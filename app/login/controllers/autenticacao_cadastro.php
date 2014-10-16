<?php

/*class AutenticacaoCadastro extends Controller{
	
	static function invoke(){*/

	$ferramenta_geral = 'geral';
	$ferramenta_admin = 'admin';
	$ferramenta_login = 'login';
	$ferramenta_administacao = 'administracao';
	
	$model_geral = '../../'.$ferramenta_geral.'/models/';
	$view_admin = '../../'.$ferramenta_admin.'/views/';
	$view_login = '../../'.$ferramenta_login.'/views/';
	$view_administracao = '../../'.$ferramenta_administacao.'/views/';

	require_once $model_geral.'geral.inc';
	require_once $view_admin.'topo_tela_inicial.php';
	
	$fraseLoginPadrao = Linguas::RetornaFraseDaLista($lista_frases, 216);
	if(!isset($login)){
		$login = $fraseLoginPadrao;
	}

		$cod_ferramenta	=	8;
		$sock = AcessoSQL::Conectar("");
		
		//$lista_frases	=	Linguas::RetornaListaDeFrases($sock,9);
		
		//$raiz_www = RetornaRaizWWW($sock);
		//$caminho 	= $raiz_www."/pagina_inicial";
		
		/* if (isset($_GET['cod_curso']) && ($_GET['cod_curso'] != ""))
		{
			$cod_curso = $_GET['cod_curso'];
		}
		else if(isset($_POST['cod_curso']) && ($_POST['cod_curso'] != ""))
		{
			$cod_curso = $_POST['cod_curso'];
		} */
		
		$cod_curso = (isset($_GET['cod_curso'])) ? $_GET['cod_curso'] : $_POST['cod_curso'];
		
		
		$login = $_POST['login'];
		$senha = $_POST['senha'];
		
		$cod_usuario = AcessoPHP::VerificaLoginSenha($login, $senha);
		if ($cod_usuario == 0)
		{
			AcessoSQL::Desconectar($sock);
			header("Location:".$view_login."autenticacao_cadastro.php?cod_curso=".$cod_curso."&acao=erroAutenticacao&atualizacao=false");
			exit;
		}
		
		/* Verifica se o usuario jah confirmou o email */
		if(!AcessoPHP::VerificaConfirmacaoEmail($login)) {
			AcessoSQL::Desconectar($sock);
			header("Location:".$view_login."autenticacao_cadastro.php?cod_curso=".$cod_curso."&acao=erroConfirmacao&atualizacao=false");
			exit;
		}

		$_SESSION['cod_usuario_global_s'] = $cod_usuario;
		$_SESSION['cod_usuario_s'] 	 	= (!empty($cod_curso)) ? Usuarios::RetornaCodigoUsuarioCurso($sock, $_SESSION['cod_usuario_global_s'],$cod_curso) : "";
		//Email nao pode mais ser usado para login
		//$_SESSION['login_usuario_s']	= (BoolEhEmail($login) == 1) ? RetornaLoginUsuario($sock) : $login;
		$_SESSION['login_usuario_s']	= $login;
		
		//$_SESSION['cod_lingua_s'] 		= $cod_lingua;
		
		//$_SESSION['visitante_s'] 			= $cod_visitante_s;
		$_SESSION['visao_formador_s'] = 1;
		
		/* Se a autenticacao for para inscricao, manda para tela de inscricao */
		if($_POST['destino']=="inscricao") {
			AcessoSQL::Desconectar($sock);
			header("Location:".$view_administracao."inscricao.php?cod_curso=".$cod_curso."&amp;tipo_curso=".$tipo_curso);
			exit;
		}
		
		/* Verifica se o cod_curso corresponde a um curso valido e
		 * se foi fornecido algum cod_curso no ato de login
		*/
		$curso_valido = (!empty($cod_curso) ? Inicial::CursoValido($sock, $cod_curso) : "");
		if(!empty($curso_valido))
		{
			AcessoSQL::Desconectar($sock);
			 header("Location: ".$view_login."index_curso.php?cod_curso=".$cod_curso);
			exit;
		}
		else
		{
			/* Verifica se quem esta fazendo login eh o administrador do ambiente, admtele */
			if($_SESSION['cod_usuario_global_s'] == -1)
			{
				AcessoSQL::Desconectar($sock);
				header("Location: ".$view_admin."index_adm.php?acao=logar&atualizacao=true&cod_lingua=".$_SESSION['cod_lingua_s']);
				exit;
			}
			else
			{
				/* Usuario comum esta fazendo o login */
				AcessoSQL::Desconectar($sock);
			
				/* Se o usuario recebeu algum email com endereco especifico, mande ele para la */
				if (!empty($_SESSION['url_de_acesso']))
				{
					$urldeacesso = $_SESSION['url_de_acesso'];
					unset($_SESSION['url_de_acesso']);
					require_once $urldeacesso;
					exit;
				}
				/* Redireciona para a tela de Meus Cursos */
				header("Location: ".$view_administracao."exibe_cursos.php?acao=logar&atualizacao=true");
				exit;
			}
		}
			AcessoSQL::Desconectar($sock);
			exit;		
