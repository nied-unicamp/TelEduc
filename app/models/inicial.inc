<?php

require_once 'data.inc';
require_once 'email.inc';
require_once 'acesso_sql.inc';
require_once 'conversor_texto.inc';

$erro1 = 'Login repetido';
$erro2 = 'Email repetido';
$erro3 = 'Caracteres invalidos';
$texto = 'Sucesso';

/*if (isset($_POST['acao'])){
	$acao = $_POST['acao'];
	switch($acao){
		case 'CadastraDados':
			//Inicial::CadastraDadosUsuario($erro1, $erro2, $erro3, $texto);
			break;
	}
		
}*/

if (isset($_POST['login'])){ //TODO: ver porque não funciona o $_POST['acao'] ou $_POST['dados']
	Inicial::CadastraDadosUsuario($erro1, $erro2, $erro3, $texto);
}

class Inicial{
	static function CursoValido($sock, $cod_curso){
		$query = "select cod_curso from Cursos where cod_curso = ".ConversorTexto::VerificaNumeroQuery($cod_curso);
		$res = AcessoSQL::Enviar($sock,$query);
		$linha = AcessoSQL::RetornaLinha($res);
		$curso_valido = $linha[0];
		return($curso_valido);
	}
	
	/* **********************************************************************
	 RetornaListaDeCursosUsuario - Retorna os cursos do qual um usu�rio
	participa/participou baseado em seu e-mail ou login.
	
	Entrada: $email - e-mail do usu�rio,
	$login - login do usu�rio.
	Saida: array []['cod_curso']['nome_curso']
	[]['inscricao_inicio']
	[]['inscricao_fim']
	[]['acesso_visitante'] - 'A' se aceita, 'N' se n�o
	*/
	
	static function RetornaListaDeCursosUsuario($sock){
	
	  echo("              <li><a href=\"../../../pagina_inicial/exibe_cursos.php\">Meus Cursos<!--[if IE 7]><!--></a><!--<![endif]-->\n");
	  echo("                <!--[if lte IE 6]><table><tr><td><![endif]-->\n");
	  echo("                <ul>\n");
	
	  $cursos = Inicial::RetornaNomeCodCursoUsuario($sock);
	  foreach($cursos as $cod => $linha){
	    echo("                  <li><a href=\"../index.php?cod_curso=".$linha['cod_curso']."\" title=\"".$linha['nome_curso']."\">".$linha['nome_curso']."</a></li>\n");
	
	  }
	  echo("                </ul>\n");
	  echo("                <!--[if lte IE 6]></td></tr></table></a><![endif]-->\n");
	  echo("              </li>\n");
	}
	
	/********************************************************************
	 Fun�ao RetornaNomeCodCursoUsuario - Retorna os codigos e os nomes dos cursos dos quais o usuario tem acesso
	Entradas: $sock - sock de conexao a base geral
	Saida:    array contendo os codigos e os nomes dos cursos que o usuario tem acesso
	********************************************************************/
	static function RetornaNomeCodCursoUsuario($sock)
	{
		// 2Session
		$dbnamebase = $_SESSION['dbnamebase'];
	
		$query  = "SELECT CUR.cod_curso,CUR.nome_curso ";
		$query .= "FROM ".$dbnamebase.".Usuario USU ";
		$query .= "INNER JOIN ".$dbnamebase.".Usuario_curso USUC on (USUC.cod_usuario_global = USU.cod_usuario) ";
		$query .= "INNER JOIN ".$dbnamebase.".Cursos CUR on (CUR.cod_curso = USUC.cod_curso) ";
		$query .= "WHERE USU.cod_usuario = '".$_SESSION['cod_usuario_global_s']."'";
	
		$res = AcessoSQL::Enviar($sock, $query);
	
		return AcessoSQL::RetornaArrayLinhas($res);
	}
	
	/* *****************************************************************************
	 RetornaListaEscolaridade - Verifica se existe a orientacao na tabela Perfil_Orientacao
	Entrada: $sock - sock de conexao EXTERNA
	Saida:   array com
	[]['cod_escolaridade']
	[]['cod_texto_escolaridade']
	*/
	static function RetornaListaEscolaridade($sock)
	{
		$query="select * from Escolaridade order by cod_escolaridade";
		$res=AcessoSQL::Enviar($sock,$query);
		$lista=AcessoSQL::RetornaArrayLinhas($res);
		return($lista);
	}
	
	/********************************************************************
	 Fun�ao CadastraDadosUsuarioDinamic - Cadastra usuario, dinamicamente
	Entradas:  $dadosForm - dados do formulario de cadastro
	Saida:    reposta XML
	********************************************************************/
	static function CadastraDadosUsuarioDinamic($dadosForm,$erro1,$erro2,$erro3,$texto)
	{
	
		//$objResponse = new xajaxResponse();
	
		$dadosForm = $_POST['data'];
		$login            = $dadosForm['login'];
		$senha            = crypt($dadosForm['senha'],"AA");
		$nome_usuario     = $dadosForm['nome_usuario'];
		$endereco         = $dadosForm['endereco'];
		$cidade           = $dadosForm['cidade'];
		$estado           = $dadosForm['estado'];
		$pais             = $dadosForm['pais'];
		$email            = $dadosForm['email'];
		$rg               = $dadosForm['rg'];
		$telefone         = $dadosForm['telefone'];
		$sexo             = $dadosForm['sexo'];
		$local            = $dadosForm['local'];
		$profissao        = $dadosForm['profissao'];
		$informacoes      = $dadosForm['informacoes'];
		$cod_escolaridade = $dadosForm['cod_escolaridade'];
		$data_nascimento  = $dadosForm['data_nascimento'];
	
		$data=Data::Data2UnixTime($data_nascimento);
	
		$sock=AcessoSQL::Conectar("");
	
		$hash = false;
		$resultado = $dadosForm['resultado'];
		if($resultado  != $_SESSION['answer']){
			$hash = true;
		}
	
		$flag = 0;
		if(Inicial::LoginRepetido($sock,$login)){
			$flag=1;
			$objResponse->call("mostraFeedback", $erro1, 'false');
		}else if(Inicial::EmailRepetido($sock,$email)){
			$flag=2;
			$objResponse->call("mostraFeedback", $erro2, 'false');
		}else if($hash){
			$flag=3;
			$objResponse->call("mostraFeedback", $erro3, 'false');
		}else
		{
			$cod_usuario=AcessoSQL::RetornaProximoCodigo($sock,"Usuario");
	
			//Gera uma sequencia aletoria de 20 caracteres
			$sequencia = Inicial::GeraChave(20);
	
			$query="insert into Usuario (cod_usuario,login,senha,nome,rg,email,telefone,endereco,cidade,estado,pais,data_nasc,sexo,local_trab,profissao,cod_escolaridade,informacoes,data_inscricao,cod_lingua,confirmacao) values (".ConversorTexto::VerificaNumeroQuery($cod_usuario).",'".ConversorTexto::VerificaStringQuery($login)."','".ConversorTexto::VerificaStringQuery($senha)."','".ConversorTexto::VerificaStringQuery($nome_usuario)."','".ConversorTexto::VerificaStringQuery($rg)."','".ConversorTexto::VerificaStringQuery($email)."','".ConversorTexto::VerificaStringQuery($telefone)."','".ConversorTexto::VerificaStringQuery($endereco)."','".ConversorTexto::VerificaStringQuery($cidade)."','".ConversorTexto::VerificaStringQuery($estado)."','".ConversorTexto::VerificaStringQuery($pais)."','".ConversorTexto::VerificaStringQuery($data)."','".ConversorTexto::VerificaStringQuery($sexo)."','".ConversorTexto::VerificaStringQuery($local)."','".ConversorTexto::VerificaStringQuery($profissao)."','".ConversorTexto::VerificaStringQuery($cod_escolaridade)."','".ConversorTexto::VerificaStringQuery($informacoes)."','".time()."',1,'".ConversorTexto::VerificaStringQuery($sequencia)."')";
	
			$res=AcessoSQL::Enviar($sock,$query);
	
			/* Notificacao do Usuario via Email ************************************/
			$sock = AcessoSQL::Conectar('');
			$query="select diretorio from Diretorio where item='raiz_www'";
			$res=AcessoSQL::Enviar($sock,$query);
			$raiz_www_linha=AcessoSQL::RetornaLinha($res);
			$raiz_www = $raiz_www_linha[0];
			$host=Inicial::RetornaConfiguracao($sock,"host");
	
			$lista_frases_email = Linguas::RetornaListaDeFrases($sock,-2);
			$lista_frases_correio = Linguas::RetornaListaDeFrases($sock,11);
	
			$parametros_curso = "";
			if (isset($_GET["cod_curso"]) && !empty($_GET["cod_curso"])) {
				$parametros_curso .= "&c=".$_GET["cod_curso"];
				if (isset($_GET["tipo_curso"]) && !empty($_GET["tipo_curso"]))
					$parametros_curso .= "&t=".$_GET["tipo_curso"];
			}
	
			//87 - Confirmação de cadastro TelEduc
			$assunto = Linguas::RetornaFraseDaLista($lista_frases_email, 87);
			$mensagem = "<p>".Linguas::RetornaFraseDaLista($lista_frases_email, 88)."</p><p><a href='http://".$host.$raiz_www."/pagina_inicial/confirmacao.php?u=".$cod_usuario."&s=".$sequencia.$parametros_curso."'>http://".$host.$raiz_www."/pagina_inicial/confirmacao.php?u=".$cod_usuario."&s=".$sequencia.$parametros_curso."</a></p><p>".Linguas::RetornaFraseDaLista($lista_frases_email, 89)."</p><p>".Linguas::RetornaFraseDaLista($lista_frases_email, 78).",</p><p> ".Linguas::RetornaFraseDaLista($lista_frases_email, 3)."</p>";
			/*
			 // 74 - Bem-Vindo ao TelEduc!
			$assunto = RetornaFraseDaLista($lista_frases_email, 74);
	
			// 75 - Para acessar o ambiente, a sua Identificação é:
			// 76 - e a sua senha é:
			// 77 - O acesso deve ser feito a partir do endereco:
			// 78 - Atenciosamente
			// 3 - Ambiente de Administração do TelEduc
	
			$mensagem = "<p>".RetornaFraseDaLista($lista_frases_email, 75)." <big><em><strong>".$login."</strong></em></big> ".RetornaFraseDaLista($lista_frases_email, 76)."<big><em><strong> ".$dadosForm['senha']."</strong></em></big></p><p>".RetornaFraseDaLista($lista_frases_email, 77)." http://$host$raiz_www/</p><br /><p>".RetornaFraseDaLista($lista_frases_email, 78).",</p><p> ".RetornaFraseDaLista($lista_frases_email, 3)."</p>";
	
			*/
	
			// 115 - NAO_RESPONDA
			$remetente = Linguas::RetornaFraseDaLista($lista_frases_correio, 115)."@".$host;
			$mensagem_html = Email::MontaMsg($host, $raiz_www, '', $mensagem, $assunto, '', $nome_usuario);
			Email::MandaMsg($remetente,$email,$assunto,$mensagem_html, '');
	
			// Fim da Notificacao
			//$objResponse->call("mostraFeedback", $texto, 'true');
			echo $texto;
		}
		
	
		AcessoSQL::Desconectar($sock);
	
	
		$objResponse->script("trataEnvio('".$flag."');");
	
		return $objResponse;
	}
	
	/********************************************************************
	 Fun�ao CadastraDadosUsuario - Cadastra usuario, dinamicamente
	Entradas:  $dadosForm - dados do formulario de cadastro
	Saida:    reposta XML
	********************************************************************/
	static function CadastraDadosUsuario($erro1,$erro2,$erro3,$texto)
	{
		
		/* $dadosForm = $_POST['dados'];
		$login            = $dadosForm['login'];
		$senha            = crypt($dadosForm['senha'],"AA");
		$nome_usuario     = $dadosForm['nome_usuario'];
		$endereco         = $dadosForm['endereco'];
		$cidade           = $dadosForm['cidade'];
		$estado           = $dadosForm['estado'];
		$pais             = $dadosForm['pais'];
		$email            = $dadosForm['email'];
		$rg               = $dadosForm['rg'];
		$telefone         = $dadosForm['telefone'];
		$sexo             = $dadosForm['sexo'];
		$local            = $dadosForm['local'];
		$profissao        = $dadosForm['profissao'];
		$informacoes      = $dadosForm['informacoes'];
		$cod_escolaridade = $dadosForm['cod_escolaridade'];
		$data_nascimento  = $dadosForm['data_nascimento']; */
		
		$login            = $_POST['login'];
		$senha            = crypt($_POST['senha'],"AA");
		$nome_usuario     = $_POST['nome_usuario'];
		$endereco         = $_POST['endereco'];
		$cidade           = $_POST['cidade'];
		$estado           = $_POST['estado'];
		$pais             = $_POST['pais'];
		$email            = $_POST['email'];
		$rg               = $_POST['rg'];
		$telefone         = $_POST['telefone'];
		$sexo             = $_POST['sexo'];
		$local            = $_POST['local'];
		$profissao        = $_POST['profissao'];
		$informacoes      = $_POST['informacoes'];
		$cod_escolaridade = $_POST['cod_escolaridade'];
		$data_nascimento  = $_POST['data_nascimento'];

		$data=Data::Data2UnixTime($data_nascimento);/*Data2MySql($data_nascimento);*/
		$sock=AcessoSQL::Conectar("");
	
		$hash = false;
		$resultado = $_POST['resultado'];
		if($resultado  != $_SESSION['answer']){
			$hash = true;
		}
	
		$flag = 0;
		if(Inicial::LoginRepetido($sock,$login)){
			$flag=1;
		}else if(Inicial::EmailRepetido($sock,$email)){
			$flag=2;
		}else if($hash){
			$flag=3;
		}else
		{
			$cod_usuario=AcessoSQL::RetornaProximoCodigo($sock,"Usuario");
	
			//Gera uma sequencia aletoria de 20 caracteres
			$sequencia = Inicial::GeraChave(20);
	
			$query="insert into Usuario (cod_usuario, login,senha,nome,rg,email,telefone,endereco,cidade,estado,pais,data_nasc,sexo,local_trab,profissao,cod_escolaridade,informacoes,data_inscricao,cod_lingua,confirmacao) values (".ConversorTexto::VerificaNumeroQuery($cod_usuario).", '".ConversorTexto::VerificaStringQuery($login)."','".ConversorTexto::VerificaStringQuery($senha)."','".ConversorTexto::VerificaStringQuery($nome_usuario)."','".ConversorTexto::VerificaStringQuery($rg)."','".ConversorTexto::VerificaStringQuery($email)."','".ConversorTexto::VerificaStringQuery($telefone)."','".ConversorTexto::VerificaStringQuery($endereco)."','".ConversorTexto::VerificaStringQuery($cidade)."','".ConversorTexto::VerificaStringQuery($estado)."','".ConversorTexto::VerificaStringQuery($pais)."','".ConversorTexto::VerificaStringQuery($data)."','".ConversorTexto::VerificaStringQuery($sexo)."','".ConversorTexto::VerificaStringQuery($local)."','".ConversorTexto::VerificaStringQuery($profissao)."','".ConversorTexto::VerificaStringQuery($cod_escolaridade)."','".ConversorTexto::VerificaStringQuery($informacoes)."','".time()."',1,'".ConversorTexto::VerificaStringQuery($sequencia)."')";
	
			$res=AcessoSQL::Enviar($sock,$query);
		
			/* Notificacao do Usuario via Email ************************************/
			$sock = AcessoSQL::Conectar('');
			$query="select diretorio from Diretorio where item='raiz_www'";
			$res=AcessoSQL::Enviar($sock,$query);
			$raiz_www_linha=AcessoSQL::RetornaLinha($res);
			$raiz_www = $raiz_www_linha[0];
			$host=Inicial::RetornaConfiguracao($sock,"host"); 
			$lista_frases_email = Linguas::RetornaListaDeFrases($sock,-2);
			$lista_frases_correio = Linguas::RetornaListaDeFrases($sock,11);
			
			$parametros_curso = "";
			if (isset($_GET["cod_curso"]) && !empty($_GET["cod_curso"])) {
				$parametros_curso .= "&c=".$_GET["cod_curso"];
				if (isset($_GET["tipo_curso"]) && !empty($_GET["tipo_curso"]))
					$parametros_curso .= "&t=".$_GET["tipo_curso"];
			}
	
			//87 - Confirmação de cadastro TelEduc
			$assunto = Linguas::RetornaFraseDaLista($lista_frases_email, 87);
			$mensagem = "<p>".Linguas::RetornaFraseDaLista($lista_frases_email, 88)."</p><p><a href='http://".$host.$raiz_www."/pagina_inicial/confirmacao.php?u=".$cod_usuario."&s=".$sequencia.$parametros_curso."'>http://".$host.$raiz_www."/pagina_inicial/confirmacao.php?u=".$cod_usuario."&s=".$sequencia.$parametros_curso."</a></p><p>".Linguas::RetornaFraseDaLista($lista_frases_email, 89)."</p><p>".Linguas::RetornaFraseDaLista($lista_frases_email, 78).",</p><p> ".Linguas::RetornaFraseDaLista($lista_frases_email, 3)."</p>";
			/*
			 // 74 - Bem-Vindo ao TelEduc!
			$assunto = RetornaFraseDaLista($lista_frases_email, 74);
	
			// 75 - Para acessar o ambiente, a sua Identificação é:
			// 76 - e a sua senha é:
			// 77 - O acesso deve ser feito a partir do endereco:
			// 78 - Atenciosamente
			// 3 - Ambiente de Administração do TelEduc
	
			$mensagem = "<p>".RetornaFraseDaLista($lista_frases_email, 75)." <big><em><strong>".$login."</strong></em></big> ".RetornaFraseDaLista($lista_frases_email, 76)."<big><em><strong> ".$dadosForm['senha']."</strong></em></big></p><p>".RetornaFraseDaLista($lista_frases_email, 77)." http://$host$raiz_www/</p><br /><p>".RetornaFraseDaLista($lista_frases_email, 78).",</p><p> ".RetornaFraseDaLista($lista_frases_email, 3)."</p>";
	
			*/
	
			// 115 - NAO_RESPONDA
			$remetente = Linguas::RetornaFraseDaLista($lista_frases_correio, 115)."@".$host;
			$mensagem_html = Email::MontaMsg($host, $raiz_www, '', $mensagem, $assunto, '', $nome_usuario);
			Email::MandaMsg($remetente,$email,$assunto,$mensagem_html, '');
			// Fim da Notificacao
			//echo $texto;
		}
		
		echo $flag;
		
		AcessoSQL::Desconectar($sock);
	
	
	}
	
	
	/* *****************************************************************************
	 LoginRepetido - Verifica se o login escolhido pelo usuario ja existe
	Entrada: $sock - sock de conexao
	$novo_login - login que o usuario escolheu
	Saida:   true - se o login existe
	false - caso contrario
	*/
	static function LoginRepetido($sock, $novo_login)
	{
		$query="select login from Usuario where login='".ConversorTexto::VerificaStringQuery($novo_login)."'";
		$res=AcessoSQL::Enviar($sock, $query);
	
		if (AcessoSQL::RetornaNumLinhas($res) > 0)
			return true;
	
		return false;
	}
	
	/* *****************************************************************************
	 EmailRepetido - Verifica se o e-mail escolhido pelo usuario ja existe
	Entrada: $sock - sock de conexao
	$email - email que o usuario escolheu
	Saida:   true - se o email existe
	false - caso contrario
	*/
	static function EmailRepetido($sock, $email)
	{
		$query="select email from Usuario where email='".ConversorTexto::VerificaStringQuery($email)."'";
		$res=AcessoSQL::Enviar($sock, $query);
	
		if (AcessoSQL::RetornaNumLinhas($res) > 0)
			return true;
	
		return false;
	}
	
	/********************************************************************
	 Funcao GeraChave - Gera uma sequencia aleatoria de caracteres
	Entrada:  $digitos - tamanho da sequencia gerada
	Saida:    String com a sequencia gerada
	********************************************************************/
	static function GeraChave($digitos) {
		$chave = '';
		$caracteres = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		srand((double)microtime()*1000000);
		for($i=0; $i<$digitos; $i++) {
			$chave .= $caracteres[rand()%strlen($caracteres)];
		}
		return $chave;
	}
	
	/* **********************************************************************
	 RetornaConfiguracao - Retorna o conte�do de um item da configura��o
	Entrada: $sock - sock da base externa
	$item - item a ser buscado
	Saida: conte�do
		*/
	static function RetornaConfiguracao($sock,$item)
	{
		$query="select valor from Config where item='".ConversorTexto::VerificaStringQuery($item)."'";
		$res=AcessoSQL::Enviar($sock,$query);
		$linha=AcessoSQL::RetornaLinha($res);
		return ($linha['valor']);
	}
	
	static function CadastrarLogar($form,$cod_curso,$tipo_curso)
	{
		$objResponse = new xajaxResponse();
	
		$login=$form['login'];
		$senha=crypt($form['senha'],"AA");
	
		$sock=AcessoSQL::Conectar("");
	
		//Seleciona senha e código global do usuario
		$query="select senha, cod_usuario from Usuario where login='".ConversorTexto::VerificaStringQuery($login)."'";
	
	
		$res=AcessoSQL::Enviar($sock,$query);
		$num=AcessoSQL::RetornaNumLinhas($res);
		$linha=AcessoSQL::RetornaLinha($res);
	
		$cod_usuario = $linha['cod_usuario'];
	
		$_SESSION['cod_usuario_global_s'] = $cod_usuario;
		$_SESSION['cod_usuario_s'] = (!empty($cod_curso)) ? Usuarios::RetornaCodigoUsuarioCurso($sock, $_SESSION['cod_usuario_global_s'],$cod_curso) : "";
		$_SESSION['login_usuario_s'] = $login;
		//$_SESSION['tipo_usuario_s'] = "";
		$_SESSION['cod_lingua_s'] = $cod_lingua; //??
		//$_SESSION['visitante_s'] = $cod_visitante_s; //??
		//$_SESSION['visao_formador_s'] = 1;
	
		AcessoSQL::Desconectar($sock);
	
		$objResponse->redirect("inscricao.php?cod_curso=".$cod_curso."&tipo_curso=".$tipo_curso."");
	
		return $objResponse;
	}
	
	static function FuncaodeTeste(){
		
		$sock = AcessoSQL::Conectar("");
		
		$dados = $_POST['dados'];
		$nome = $dados['nome'];
		$query = "insert into Teste values (1, '".$nome."')";
		$res = AcessoSQL::Enviar($sock, $query);
	}
}
?>