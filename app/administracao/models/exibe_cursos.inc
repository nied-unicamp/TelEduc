<?php
class ExibeCursos{
	
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