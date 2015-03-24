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
	 * @param mysqli_connect $sock	instância de conexão do mysql
	 * @param int $cod_curso Número de identificação primaria do curso da tabela Cursos
	 * @return Array Um Array unidimensional com informações a respeito do curso.
	 */
	static function CursoValido($sock, $cod_curso){
		$query = "select cod_curso from Cursos where cod_curso = ".ConversorTexto::VerificaNumeroQuery($cod_curso);
		$res = AcessoSQL::Enviar($sock,$query);
		$linha = AcessoSQL::RetornaLinha($res);
		$curso_valido = $linha[0];
		return($curso_valido);
	}
	
	/* **********************************************************************
	 RetornaListaDeCursosUsuario - Retorna os cursos do qual um usuï¿½rio
	participa/participou baseado em seu e-mail ou login.
	
	Entrada: $email - e-mail do usuï¿½rio,
	$login - login do usuï¿½rio.
	Saida: array []['cod_curso']['nome_curso']
	[]['inscricao_inicio']
	[]['inscricao_fim']
	[]['acesso_visitante'] - 'A' se aceita, 'N' se nï¿½o
	*/
	
	/**
	 * Retorna os cursos do qual um usuário participa/participou baseado em seu e-mail ou login
	 *
	 * O retorno é um echo
	 *
	 * @param mysqli_connect $sock	instância de conexão do mysql
	 */
	static function RetornaListaDeCursosUsuario($sock){
	
	  $ferramenta_administracao = 'administracao';
	  $ferramenta_login = 'login';
	  
	  $view_administracao = '../../'.$ferramenta_administracao.'/views/';
	  $ctrl_login = '../../'.$ferramenta_login.'/controllers/';
	  
	  /* 132 - Meus cursos */
	  echo("              <li><a href=\"".$view_administracao."exibe_cursos.php\">"._("MY_COURSES_-3")."<!--[if IE 7]><!--></a><!--<![endif]-->\n");
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
     * @param mysqli_connect $sock	instância de conexão do mysql
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
	 *  @param mysqli_connect $sock	instância de conexão do mysql EXTERNA
	 *  @return array Retorna matriz bidimensional indexada por numeros no seu primeiro nivel.
	 */
	static function RetornaListaEscolaridade($sock)
	{
		$query="select * from Escolaridade order by cod_escolaridade";
		$res=AcessoSQL::Enviar($sock,$query);
		$lista=AcessoSQL::RetornaArrayLinhas($res);
		return($lista);
	}
	
	/* *****************************************************************************
	 LoginRepetido - Verifica se o login escolhido pelo usuario ja existe
	Entrada: $sock - sock de conexao
	$novo_login - login que o usuario escolheu
	Saida:   true - se o login existe
	false - caso contrario
	*/
	/**
	 * Verifica se o login escolhido pelo usuario já existe
	 *
	 * @param mysqli_connect $sock Instancia de conexão do mysql
	 * @param String $novo_login Uma string com um novo login
	 * @return boolean True se o login já existir e False se o login nao existir.
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
	 * Verifica se o e-mail escolhido pelo usuario já está na base de dados
	 *
	 * @param mysqli_connect $sock Instancia de conexão do mysql
	 * @param string $email email que o usuario escolheu
	 * @return boolean True se o email já estiver cadastro e False caso contrário.
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
	 RetornaConfiguracao - Retorna o conteï¿½do de um item da configuraï¿½ï¿½o
	Entrada: $sock - sock da base externa
	$item - item a ser buscado
	Saida: conteï¿½do
		*/
	/**
	 *
	 * @param @param mysqli_connect $sock Instancia de conexão do mysql
	 * @param string $item Item é o nome uma variavel salva no banco na Tabela Config.
	 * @return string Conteúdo da Variável
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
	 * @param @param int $cod_curso Numero de identificação primaria do curso da tabela Cursos
	 * @param type $tipo_curso Nao usado atualmente pelo metodo.
	 * @return xajaxResponse Xml para ser usado pelo Ajax
	 */
	static function CadastrarLogar($form,$cod_curso,$tipo_curso)
	{
		$objResponse = new xajaxResponse();
	
		$login=$form['login'];
		$senha=crypt($form['senha'],"AA");
	
		$sock=AcessoSQL::Conectar("");
	
		//Seleciona senha e cÃ³digo global do usuario
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
			$lista[$cont]['pasta']= _("GENERAL_COURSE_-3");
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
		$status_usuario = Inicial::RetornaTipoUsuario($sock, $cod_curso);
		if ($status_usuario == NULL)
			/* 232 - Não participa do curso*/
			return _("NOT_PART_OF_COURSE_-3");
	
		switch ($status_usuario['tipo_usuario']){
			case "i":
				/* 224 - Aluno Inscrito */
				return _("ENROLLED_STUDENT_-3");
			case "a":
				/* 234 - Aluno desligado */
				return _("UNENROLLED_STUDENT_-3");
			case "r":
				/* 225 - Aluno nao aceito */
				return _("UNACCEPTED_STUDENT_-3");
			case "A":
				/* 226 - Aluno aceito */
				return _("ACCEPTED_STUDENT_-3");
			case "f":
				/* 228 - Formador desligado */
				return _("UNENROLLED_INSTRUCTOR_-3");
			case "F":
				/* 58 - Formador */
				return _("INSTRUCTOR_-1");
			case "z":
				/* 229 - Colaborador desligado*/
				return _("UNENROLLED_COLLABORATOR_-3");
			case "Z":
				/* 231 - Colaborador */
				return _("COLLABORATOR_-3");
			case "v":
				/* 233 - Vistante desligado */
				return _("UNENROLLED_VISITOR_-3");
			case "V":
				/* 230 - Visitante */
				return _("VISITOR_-3");
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