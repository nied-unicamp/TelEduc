<?php

/**
 * Classe Model do modulo Administra��o
 * @author     TelEduc
 * @copyright  2014 TelEduc
 * @license    http://teleduc.org.br/
 */
class ExibeCursos{
	/**
	 * Este met�do retorna os cursos que ainda n�o foram iniciados baseado no usu�rio.
	 *
	 * @param mysqli_connect $sock	Inst�ncia de conex�o do mysql
	 * @param int $cod_usuario Id com o c�digo do usu�rio a ser consultado
	 * @return Array Retorna array indexado por n�meros e com os cursos n�o iniciados.
	 */
	static function RetornaCursosNaoIniciados($sock,$cod_usuario)
	{
		$query  = "SELECT CUR.cod_curso, CUR.nome_curso, USUC.tipo_usuario ";
		$query .= "FROM Usuario USU ";
		$query .= "INNER JOIN Usuario_curso USUC on (USUC.cod_usuario_global = USU.cod_usuario) ";
		$query .= "INNER JOIN Cursos CUR on (CUR.cod_curso = USUC.cod_curso) ";
		$query .= "WHERE USU.cod_usuario = '".$_SESSION['cod_usuario_global_s']."' AND ";
		$query .= "      (CUR.curso_inicio > ".time()." OR CUR.curso_inicio is NULL) ";
		$query .= "ORDER BY CUR.nome_curso";
	
		$res = AcessoSQL::Enviar($sock, $query);
		$total = AcessoSQL::RetornaNumLinhas($res);
		$tuplas = AcessoSQL::RetornaArrayLinhas($res);
	
		$nCursos = is_array($tuplas) ? count($tuplas) : 0;
	
		return array ($tuplas, $nCursos);
	}
	
	/**
	 * Rertorna quais cursos o usu�rio tem em andamento baseado no seu id.
	 *
	 * @param mysqli_connect $sock	Inst�ncia de conex�o do mysql
	 * @param int $cod_usuario Id com o c�digo do usu�rio a ser consultado
	 * @return Array Retorna array indexado por n�meros e com os cursos  iniciados
	 */
	static function RetornaCursosEmAndamento($sock,$cod_usuario)
	{
		/*
		 I - aluno inv�ido ou inexistente
		i - Aluno inscrito
		a - Aluno desligado
		r - Aluno rejeitado
		f - Formador desligado
		v - Visitante desligado
		z - Colaborador desligado
		*/
	
		$query  = "SELECT CUR.cod_curso, CUR.nome_curso, USUC.tipo_usuario ";
		$query .= "FROM Usuario USU ";
		$query .= "INNER JOIN Usuario_curso USUC on (USUC.cod_usuario_global = USU.cod_usuario) ";
		$query .= "INNER JOIN Cursos CUR on (CUR.cod_curso = USUC.cod_curso) ";
		$query .= "WHERE USU.cod_usuario = '".$_SESSION['cod_usuario_global_s']."' AND ";
		$query .= "      CUR.curso_inicio <= '".time()."' AND ";
		$query .= "      CUR.curso_fim >= '".time()."' AND ";
		$query .= "      binary USUC.tipo_usuario not in ('I', 'i', 'a', 'r', 'f', 'v', 'z') ";
		$query .= "ORDER BY CUR.nome_curso";
	
		$res = AcessoSQL::Enviar($sock, $query);
		$total = AcessoSQL::RetornaNumLinhas($res);
		$tuplas = AcessoSQL::RetornaArrayLinhas($res);
	
		$nCursos = is_array($tuplas) ? count($tuplas) : 0;
	
		return array ($tuplas, $nCursos);
	}
	
	/**
	 * Rertorna quais cursos o usu�rio j� fez baseado no seu id.
	 *
	 * @param mysqli_connect $sock	Inst�ncia de conex�o do mysql
	 * @param int $cod_usuario Id com o c�digo do usu�rio a ser consultado
	 * @return Array Retorna array indexado por n�meros e com os cursos j� finalizados
	 */
	static function RetornaCursosPassados($sock,$cod_usuario)
	{
		$hoje=time();
		$ontem=$hoje - 86400;
	
		/*
		 I - aluno inv�ido ou inexistente
		i - Aluno inscrito
		a - Aluno desligado
		r - Aluno rejeitado
		f - Formador desligado
		v - Visitante desligado
		z - Colaborador desligado
		*/
	
		$query  = "SELECT CUR.cod_curso, CUR.nome_curso, USUC.tipo_usuario ";
		$query .= "FROM Usuario USU ";
		$query .= "INNER JOIN Usuario_curso USUC on (USUC.cod_usuario_global = USU.cod_usuario) ";
		$query .= "INNER JOIN Cursos CUR on (CUR.cod_curso = USUC.cod_curso) ";
		$query .= "WHERE USU.cod_usuario = '".$_SESSION['cod_usuario_global_s']."' AND ";
		$query .= "      CUR.curso_fim < '".$ontem."' AND ";
		$query .= "      binary USUC.tipo_usuario not in ('I', 'i', 'a', 'r', 'f', 'v', 'z') ";
		$query .= "ORDER BY CUR.nome_curso";
	
		$res = AcessoSQL::Enviar($sock, $query);
		$total = AcessoSQL::RetornaNumLinhas($res);
		$tuplas = AcessoSQL::RetornaArrayLinhas($res);
	
		$nCursos = is_array($tuplas) ? count($tuplas) : 0;
	
		return array ($tuplas, $nCursos);
	}
}
?>