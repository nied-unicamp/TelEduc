<?php

require_once 'data.inc';
require_once 'email.inc';
require_once 'acesso_sql.inc';
require_once 'conversor_texto.inc';

/**
 * Classe Inicial do modulo geral
 * @author     TelEduc
 * @copyright  2014 TelEduc
 * @license    http://teleduc.org.br/
 */
class Inicial{
	/**
	 *
	 * @param mysqli_connect $sock	inst‚ncia de conex„o do mysql
	 * @param int $cod_curso N˙mero de identificaÁ„o primaria do curso da tabela Cursos
	 * @return Array Um Array unidimensional com informaÁıes a respeito do curso.
	 */
	static function CursoValido($sock, $cod_curso){
		$query = "select cod_curso from Cursos where cod_curso = ".ConversorTexto::VerificaNumeroQuery($cod_curso);
		$res = AcessoSQL::Enviar($sock,$query);
		$linha = AcessoSQL::RetornaLinha($res);
		$curso_valido = $linha[0];
		return($curso_valido);
	}
	
	/* **********************************************************************
	 RetornaListaDeCursosUsuario - Retorna os cursos do qual um usuÔøΩrio
	participa/participou baseado em seu e-mail ou login.
	
	Entrada: $email - e-mail do usuÔøΩrio,
	$login - login do usuÔøΩrio.
	Saida: array []['cod_curso']['nome_curso']
	[]['inscricao_inicio']
	[]['inscricao_fim']
	[]['acesso_visitante'] - 'A' se aceita, 'N' se nÔøΩo
	*/
	
	/**
	 * Retorna os cursos do qual um usu·rio participa/participou baseado em seu e-mail ou login
	 *
	 * O retorno È um echo
	 *
	 * @param mysqli_connect $sock	inst‚ncia de conex„o do mysql
	 */
	static function RetornaListaDeCursosUsuario($sock){
	
	  $ferramenta_administracao = 'administracao';
	  $ferramenta_login = 'login';
	  
	  $view_administracao = '../../'.$ferramenta_administracao.'/views/';
	  $ctrl_login = '../../'.$ferramenta_login.'/controllers/';
	  
	  echo("              <li><a href=\"".$view_administracao."exibe_cursos.php\">Meus Cursos<!--[if IE 7]><!--></a><!--<![endif]-->\n");
	  echo("                <!--[if lte IE 6]><table><tr><td><![endif]-->\n");
	  echo("                <ul>\n");
	
	  $cursos = Inicial::RetornaNomeCodCursoUsuario($sock);
	  foreach($cursos as $cod => $linha){
	    echo("                  <li><a href=\"".$ctrl_login."index_curso.php?cod_curso=".$linha['cod_curso']."\" title=\"".$linha['nome_curso']."\">".$linha['nome_curso']."</a></li>\n");
	
	  }
	  echo("                </ul>\n");
	  echo("                <!--[if lte IE 6]></td></tr></table></a><![endif]-->\n");
	  echo("              </li>\n");
	}
	
	/**
     * Retorna os codigos e os nomes dos cursos dos quais o usuario tem acesso
     * 
     * @param mysqli_connect $sock	inst‚ncia de conex„o do mysql
     * @return array Retorna matriz bidimensional indexada por numeros no seu primeiro nivel.
     */
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
	/**
	 * Verifica se existe a orientacao na tabela Perfil_Orientacao
	 * Saida:   array com []['cod_escolaridade'][]['cod_texto_escolaridade']
	 *
	 *  @param mysqli_connect $sock	inst‚ncia de conex„o do mysql EXTERNA
	 *  @return array Retorna matriz bidimensional indexada por numeros no seu primeiro nivel.
	 */
	static function RetornaListaEscolaridade($sock)
	{
		$query="select * from Escolaridade order by cod_escolaridade";
		$res=AcessoSQL::Enviar($sock,$query);
		$lista=AcessoSQL::RetornaArrayLinhas($res);
		return($lista);
	}
	
	/********************************************************************
	 FunÔøΩao CadastraDadosUsuarioDinamic - Cadastra usuario, dinamicamente
	Entradas:  $dadosForm - dados do formulario de cadastro
	Saida:    reposta XML
	********************************************************************/
	/**
	 * Cadastra usuario, dinamicamente
	 *
	 * MetÛdo precisa ser reformulado.
	 *
	 * @param Array $dadosForm dados do formulario de cadastro um array (Nao utilizada efetivamente no metodo)
	 * @param string $erro1 Texto com um erro ocorrido 1 (Pode ser passada vazia)
	 * @param string $erro2 Texto com um erro ocorrido 1 (Pode ser passada vazia)
	 * @param string $erro3 Texto com um erro ocorrido 1 (Pode ser passada vazia)
	 * @param string $texto Ser· exibida como Echo no metodo, mais utilizado por funcoes ajax
	 * @return xml Reposta XML
	 */
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
	
			//87 - Confirma√ß√£o de cadastro TelEduc
			$assunto = Linguas::RetornaFraseDaLista($lista_frases_email, 87);
			$mensagem = "<p>".Linguas::RetornaFraseDaLista($lista_frases_email, 88)."</p><p><a href='http://".$host.$raiz_www."/pagina_inicial/confirmacao.php?u=".$cod_usuario."&s=".$sequencia.$parametros_curso."'>http://".$host.$raiz_www."/pagina_inicial/confirmacao.php?u=".$cod_usuario."&s=".$sequencia.$parametros_curso."</a></p><p>".Linguas::RetornaFraseDaLista($lista_frases_email, 89)."</p><p>".Linguas::RetornaFraseDaLista($lista_frases_email, 78).",</p><p> ".Linguas::RetornaFraseDaLista($lista_frases_email, 3)."</p>";
			/*
			 // 74 - Bem-Vindo ao TelEduc!
			$assunto = RetornaFraseDaLista($lista_frases_email, 74);
	
			// 75 - Para acessar o ambiente, a sua Identifica√ß√£o √©:
			// 76 - e a sua senha √©:
			// 77 - O acesso deve ser feito a partir do endereco:
			// 78 - Atenciosamente
			// 3 - Ambiente de Administra√ß√£o do TelEduc
	
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
	 FunÔøΩao CadastraDadosUsuario - Cadastra usuario, dinamicamente
	Entradas:  $dadosForm - dados do formulario de cadastro
	Saida:    reposta XML
	********************************************************************/
	/**
	 * Cadastra usuario, dinamicamente
	 *
	 * @param string $erro1 Texto com um erro ocorrido 1 (Nao utlizado ono metodo)
	 * @param string $erro2 Texto com um erro ocorrido 1 (Pode ser passada vazia)
	 * @param string $erro3 Texto com um erro ocorrido 1 (Pode ser passada vazia)
	 * @param string $texto Ser· exibida como Echo no metodo, mais utilizado por funcoes ajax
	 * @return xml Reposta XML
	
	 */
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
	
			//87 - Confirma√ß√£o de cadastro TelEduc
			$assunto = Linguas::RetornaFraseDaLista($lista_frases_email, 87);
			$mensagem = "<p>".Linguas::RetornaFraseDaLista($lista_frases_email, 88)."</p><p><a href='http://".$host.$raiz_www."/pagina_inicial/confirmacao.php?u=".$cod_usuario."&s=".$sequencia.$parametros_curso."'>http://".$host.$raiz_www."/pagina_inicial/confirmacao.php?u=".$cod_usuario."&s=".$sequencia.$parametros_curso."</a></p><p>".Linguas::RetornaFraseDaLista($lista_frases_email, 89)."</p><p>".Linguas::RetornaFraseDaLista($lista_frases_email, 78).",</p><p> ".Linguas::RetornaFraseDaLista($lista_frases_email, 3)."</p>";
			/*
			 // 74 - Bem-Vindo ao TelEduc!
			$assunto = RetornaFraseDaLista($lista_frases_email, 74);
	
			// 75 - Para acessar o ambiente, a sua Identifica√ß√£o √©:
			// 76 - e a sua senha √©:
			// 77 - O acesso deve ser feito a partir do endereco:
			// 78 - Atenciosamente
			// 3 - Ambiente de Administra√ß√£o do TelEduc
	
			$mensagem = "<p>".RetornaFraseDaLista($lista_frases_email, 75)." <big><em><strong>".$login."</strong></em></big> ".RetornaFraseDaLista($lista_frases_email, 76)."<big><em><strong> ".$dadosForm['senha']."</strong></em></big></p><p>".RetornaFraseDaLista($lista_frases_email, 77)." http://$host$raiz_www/</p><br /><p>".RetornaFraseDaLista($lista_frases_email, 78).",</p><p> ".RetornaFraseDaLista($lista_frases_email, 3)."</p>";
	
			*/
	
			// 115 - NAO_RESPONDA
			$remetente = Linguas::RetornaFraseDaLista($lista_frases_correio, 115)."@".$host;
			$mensagem_html = Email::MontaMsg($host, $raiz_www, '', $mensagem, $assunto, '', $nome_usuario);
			Email::MandaMsg($remetente,$email,$assunto,$mensagem_html, '');
			// Fim da Notificacao
			//echo $texto;
		}
		
		die("<Sucesso>");
		
		AcessoSQL::Desconectar($sock);
	
	
	}
	
	
	/* *****************************************************************************
	 LoginRepetido - Verifica se o login escolhido pelo usuario ja existe
	Entrada: $sock - sock de conexao
	$novo_login - login que o usuario escolheu
	Saida:   true - se o login existe
	false - caso contrario
	*/
	/**
	 * Verifica se o login escolhido pelo usuario j· existe
	 *
	 * @param mysqli_connect $sock Instancia de conex„o do mysql
	 * @param String $novo_login Uma string com um novo login
	 * @return boolean True se o login j· existir e False se o login nao existir.
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
	/**
	 * Verifica se o e-mail escolhido pelo usuario j· est· na base de dados
	 *
	 * @param mysqli_connect $sock Instancia de conex„o do mysql
	 * @param string $email email que o usuario escolheu
	 * @return boolean True se o email j· estiver cadastro e False caso contr·rio.
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
	/**
	 * Gera uma sequencia aleatoria de caracteres
	 *
	 * @param int  $digitos Tamanho da sequencia gerada
	 * @return string String com a sequencia gerada
	 */
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
	 RetornaConfiguracao - Retorna o conteÔøΩdo de um item da configuraÔøΩÔøΩo
	Entrada: $sock - sock da base externa
	$item - item a ser buscado
	Saida: conteÔøΩdo
		*/
	/**
	 *
	 * @param @param mysqli_connect $sock Instancia de conex„o do mysql
	 * @param string $item Item È o nome uma variavel salva no banco na Tabela Config.
	 * @return string Conte˙do da Vari·vel
	 */
	static function RetornaConfiguracao($sock,$item)
	{
		$query="select valor from Config where item='".ConversorTexto::VerificaStringQuery($item)."'";
		$res=AcessoSQL::Enviar($sock,$query);
		$linha=AcessoSQL::RetornaLinha($res);
		return ($linha['valor']);
	}
	
	/**
	 *
	 * @param Array $form Um array retornado por um formulario HTML por get ou post
	 * @param @param int $cod_curso Numero de identificaÁ„o primaria do curso da tabela Cursos
	 * @param type $tipo_curso Nao usado atualmente pelo metodo.
	 * @return xajaxResponse Xml para ser usado pelo Ajax
	 */
	static function CadastrarLogar($form,$cod_curso,$tipo_curso)
	{
		$objResponse = new xajaxResponse();
	
		$login=$form['login'];
		$senha=crypt($form['senha'],"AA");
	
		$sock=AcessoSQL::Conectar("");
	
		//Seleciona senha e c√≥digo global do usuario
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
	
	/* ****************************************************************************************
	 RetornaPastasDeCursos - Retorna uma array com as pastas em que os cursos estao divididos
	(somente as pastas que contem algum curso)
	Entrada: $sock - sock Base Externa
	$tipo_curso - tipo do curso
	'A' - Em andamento
	'I' - com inscricoes aberta
	'E' - Encerrados
	Saida: array com []['cod_pasta']
	[]['pasta']
	[]['num_cursos']
	*/
	static function RetornaPastasDeCursos($sock,$tipo_curso)
	{
		$lista_frases = Linguas::RetornaListaDeFrases($sock, -3);
	
		$hoje=time();
		$ontem=$hoje - 86400;
	
		if($tipo_curso=='N')
			$where="(C.curso_inicio > ".time()." or C.curso_inicio is NULL)";
		else if ($tipo_curso=='A')
			$where="C.curso_inicio<=".$hoje." and C.curso_fim>=".$ontem;
		else if ($tipo_curso=='I')
			$where="C.inscricao_inicio<=".$hoje." and C.inscricao_fim>=".$ontem;
		else
			$where="C.curso_fim<".$ontem;
	
		$query="select CP.cod_pasta,CP.pasta,count(*) 'num_cursos' from Cursos_pastas as CP, Cursos as C where CP.cod_pasta=C.cod_pasta and ".$where." group by CP.cod_pasta order by CP.pasta";
		$res=AcessoSQL::Enviar($sock,$query);
		$lista=AcessoSQL::RetornaArrayLinhas($res);
	
		$query="select count(*) from Cursos C where C.cod_pasta IS NULL and ".$where;
		$res=AcessoSQL::Enviar($sock,$query);
		$linha=AcessoSQL::RetornaLinha($res);
	
		if ($linha[0]>0)
		{
			$cont=count($lista);
			$lista[$cont]['cod_pasta']="NULL";
			/* 115 - Cursos gerais */
			$lista[$cont]['pasta']=Linguas::RetornaFraseDaLista($lista_frases,115);
			$lista[$cont]['num_cursos']=$linha[0];
		}
	
		return ($lista);
	}
	
	/* **********************************************************************
	 RetornaListaDeCursos - Retorna a lista de cursos dado o tipo
	Entrada: $sock - sock da base externa
	$tipo_curso - tipo do curso
	'N' - Nao iniciado
	'A' - Em andamento
	'I' - com inscricoes aberta
	'E' - Encerrados
	$cod_pasta - codigo da pasta a qual pertence os cursos a serem retornados
	Saida: array []['cod_curso']
	[]['nome_curso']
	[]['inscricao_inicio']
	[]['inscricao_fim']
	[]['acesso_visitante'] - 'A' se aceita, 'N' se nao
	*/
	static function RetornaListaDeCursos($sock,$tipo_curso,$cod_pasta)
	{
		$hoje=time();
		$ontem=$hoje - 86400;
	
		if($tipo_curso=='N')
			$where="(curso_inicio > ".time()." or curso_inicio is NULL)";
		else if ($tipo_curso=='A')
			$where="curso_inicio<=".$hoje." and curso_fim>=".$ontem;
		else if ($tipo_curso=='I')
			$where="inscricao_inicio<=".$hoje." and inscricao_fim>=".$ontem;
		else
			$where="curso_fim<".$ontem;
	
		if ($cod_pasta=="NULL" || $cod_pasta=="")
			$pasta="cod_pasta IS NULL";
		else
			$pasta="cod_pasta=".ConversorTexto::VerificaNumeroQuery($cod_pasta);
	
		$query="select cod_curso,nome_curso,inscricao_inicio,inscricao_fim,acesso_visitante from Cursos where ".$pasta." and ".$where." order by nome_curso";
	
		$res=AcessoSQL::Enviar($sock,$query);
		$lista=AcessoSQL::RetornaArrayLinhas($res);
	
		if (count($lista)>0 && $lista != "")
		{
			$c=0;
			foreach($lista as $cod => $linha)
				$lista_saida[$c++]=$linha;
		}
		else
			$lista_saida=$lista;
	
		return ($lista_saida);
	}
	
	/********************************************************************
	 Funcao ParticipaDoCurso - Verifica se o usuario participa do curso
	Entradas:  $cod_curso - codigo do curso
	Saida:    'true' se o usuario pertence ao curso; 'false', caso contrario.
	Caso o usuario nao esteja logado, retornamos 'null'.
	********************************************************************/
	
	static function ParticipaDoCurso($cod_curso)
	{
		if(!empty($_SESSION['cod_usuario_global_s']))
		{
			$sock = AcessoSQL::Conectar("");
			//caso o usuario esteja logado, ve quais cursos ele possui acesso
			$meus_cursos = Inicial::RetornaTodosCursosUsuario($sock);
			AcessoSQL::Desconectar($sock);
			if($meus_cursos=="") $meus_cursos=array();
			$tem_acesso_curso = false;
	
			//admtele tem acesso a todos os cursos
			if($_SESSION['cod_usuario_global_s'] == -1)
				$tem_acesso_curso = true;
			else
			{
				foreach ($meus_cursos as $meu_curso)
				{
					if($meu_curso['cod_curso'] == $cod_curso) {
						$tem_acesso_curso = true;
						break;
					}
				}
			}
	
			return $tem_acesso_curso;
		}
		else
			//caso contrario, retornamos null, indicando a impossibilidade de determinar se o usuario pertence ou nao ao curso
			return null;
	}
	
	/********************************************************************
	 Funcao RetornaTodosCursosUsuario - Retorna todos os codigos dos cursos que o usuario tem algum vinculo
	Entradas: $sock - sock de conexao a base geral
	Saida:    array contendo os codigos dos cursos que o usuario tem algum vinculo
	********************************************************************/
	static function RetornaTodosCursosUsuario($sock)
	{
		$query  = "SELECT CUR.cod_curso ";
		$query .= "FROM Usuario USU ";
		$query .= "INNER JOIN Usuario_curso USUC on (USUC.cod_usuario_global = USU.cod_usuario) ";
		$query .= "INNER JOIN Cursos CUR on (CUR.cod_curso = USUC.cod_curso) ";
		$query .= "WHERE USU.cod_usuario = '".$_SESSION['cod_usuario_global_s']."'";// and ";
		// Se o usuario tentou se incriver em um curso, mas sua inscricao foi cancelada, ele nao possui vinculo com aquele curso.
		//$query .= "      USUC.tipo_usuario <> 'r'";
	
		$res = AcessoSQL::Enviar($sock, $query);
	
		return AcessoSQL::RetornaArrayLinhas($res);
	}
	
	/********************************************************************
	 Funcao RejeitadoDoCurso - Verifica se o usuario foi rejeitado do curso
	Entradas:  $cod_curso - codigo do curso
	Saida:    'true' se o usuario foi rejeitado do curso; 'false', caso contrario.
	Caso o usuario nao esteja logado, retornamos 'null'.
	********************************************************************/
	
	static function RejeitadoDoCurso($cod_curso)
	{
		if(!empty($_SESSION['cod_usuario_global_s']))
		{
			$sock = AcessoSQL::Conectar("");
			//caso o usuario esteja logado, ve quais cursos ele possui acesso
			$meus_cursos = Inicial::RetornaTodosCursosETipoUsuarioDeUsuario($sock);
			AcessoSQL::Desconectar($sock);
			if($meus_cursos=="") $meus_cursos=array();
			$rejeitado_curso = false;
	
			//admtele tem acesso a todos os cursos
			//if($_SESSION['cod_usuario_global_s'] == -1)
			//  $tem_acesso_curso = true;
			//else
			//{
			foreach ($meus_cursos as $meu_curso)
			{
				if($meu_curso['cod_curso']    == $cod_curso &&
				$meu_curso['tipo_usuario'] == 'r') {
					$rejeitado_curso = true;
					break;
				}
	
			}
			//}
	
			return $rejeitado_curso;
			}
			else
				//caso contrario, retornamos null, indicando a impossibilidade de determinar se o usuario pertence ou nao ao curso
				return null;
	}
	
	/*xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
	 Funcao RetornaTodosCursosETipoUsuarioDeUsuario - Retorna todos os codigos dos cursos que o usuario tem algum vinculo e o tipo de usuario no curso
	Entradas: $sock - sock de conexao a base geral
	Saida:    array contendo os codigos dos cursos que o usuario tem algum vinculo e o tipo de usuario no curso
	xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx*/
	static function RetornaTodosCursosETipoUsuarioDeUsuario($sock)
	{
		$query  = "SELECT CUR.cod_curso, USUC.tipo_usuario ";
		$query .= "FROM Usuario USU ";
		$query .= "INNER JOIN Usuario_curso USUC on (USUC.cod_usuario_global = USU.cod_usuario) ";
		$query .= "INNER JOIN Cursos CUR on (CUR.cod_curso = USUC.cod_curso) ";
		$query .= "WHERE USU.cod_usuario = '".$_SESSION['cod_usuario_global_s']."'";
	
		$res = AcessoSQL::Enviar($sock, $query);
	
		return AcessoSQL::RetornaArrayLinhas($res);
	}
	
	/* **********************************************************************
	 Funcao StatusUsuarioNoCurso - Retorna a condicao do usuario no curso
	Entradas: $cod_curso - codigo do curso
	Saida: String com a condicao do usuario
	*************************************************************************/
	
	static function StatusUsuarioNoCurso($sock, $cod_curso){
		$lista_frases = Linguas::RetornaListaDeFrases($sock,-3);
		$status_usuario = Inicial::RetornaTipoUsuario($sock, $cod_curso);
		if ($status_usuario == NULL)
			return Linguas::RetornaFraseDaLista($lista_frases,232);
	
		switch ($status_usuario['tipo_usuario']){
			case "i":
				/* 224 - Aluno Inscrito */
				return Linguas::RetornaFraseDaLista($lista_frases,224);
			case "a":
				/* 234 - Aluno desligado */
				return Linguas::RetornaFraseDaLista($lista_frases,234);
			case "r":
				/* 225 - Aluno nao aceito */
				return Linguas::RetornaFraseDaLista($lista_frases,225);
			case "A":
				/* 226 - Aluno aceito */
				return Linguas::RetornaFraseDaLista($lista_frases,226);
			case "f":
				/* 228 - Formador desligado */
				return Linguas::RetornaFraseDaLista($lista_frases,228);
			case "F":
				/* 227 - Formador */
				return Linguas::RetornaFraseDaLista($lista_frases,227);
			case "z":
				/* 229 - Colaborador desligado*/
				return Linguas::RetornaFraseDaLista($lista_frases,229);
			case "Z":
				/* 231 - Colaborador */
				return Linguas::RetornaFraseDaLista($lista_frases,231);
			case "v":
				/* 233 - Vistante desligado */
				return Linguas::RetornaFraseDaLista($lista_frases,233);
			case "V":
				/* 230 - Visitante */
				return Linguas::RetornaFraseDaLista($lista_frases,230);
		}
	}
	
	/********************************************************************
	 Funcao RetornaTipoUsuario - Retorna o tipo_usuario da tabela Usuario_curso
	Entradas: $cod_curso - codigo do curso
	Saida: Flag de tipo_usuario
	*********************************************************************/
	
	static function RetornaTipoUsuario($sock, $cod_curso){
	
		$query  = "SELECT tipo_usuario ";
		$query .= "FROM Usuario_curso ";
		$query .= "WHERE cod_usuario_global = '".$_SESSION["cod_usuario_global_s"]."' AND ";
		$query .= "      cod_curso = '".$cod_curso."'";
	
		$res = AcessoSQL::Enviar($sock, $query);
	
		return AcessoSQL::RetornaLinha($res);
	}
	
	/* **********************************************************************
	 RetornaDadosMostraCurso - Retorna os dados do curso dado
	Entrada: $sock - base externo
	Saida: array com []['cod_curso']
	[]['nome_curso']
	[]['inscricao_inicio']
	[]['inscricao_fim']
	[]['curso_inicio']
	[]['curso_fim']
	[]['informacoes']
	[]['publico_alvo']
	[]['tipo_inscricao']
	[]['num_alunos']
	[]['cod_coordenador']
	[]['acesso_visitante']
	*/
	static function RetornaDadosMostraCurso($sock,$cod_curso)
	{
		$query="select * from Cursos where cod_curso=".ConversorTexto::VerificaNumeroQuery($cod_curso);
		$res=AcessoSQL::Enviar($sock,$query);
		$linha=AcessoSQL::RetornaLinha($res);
		return ($linha);
	}
}

?>