<?php
require_once $diretorio_models.'data.inc';
class Administracao{
	/* *********************************************************************
	 RetornaDadosCurso - Atualiza o cronograma do curso
	Entrada: $sock - BASE DO CURSO ou BASE EXTERNA
	$cod_curso= codigo do curso,
	Saida:   array ['cod_curso'] = codigo do curso
	['nome_curso'] = nome do curso
	['inscricao_inicio'] = timestamp do dia de inicio da inscricao
	['inscricao_fim'] = timestamp do dia de fim da inscricao
	['curso_inicio'] = timestamp do dia de inicio do curso
	['curso_fim'] = timestamp do dia de fim do curso
	['informacoes'] = informacoes sobre o curso
	['publico_alvo'] = Publico esperado
	['tipo_inscricao'] = tipo de inscricao
	['acesso_visitante'] = 'A' - Permite acesso de visitante
	'N' - N�o permite
	['cod_lingua'] = codigo da lingua padr�o do curso (atual)
	*/
	static function RetornaDadosCursoAdm($sock,$cod_curso)
	{
		$query ="select * from Cursos where cod_curso=".$cod_curso;
		$res=AcessoSQL::Enviar($sock,$query);
		return (AcessoSQL::RetornaLinha($res));
	}
	
	/* *********************************************************************
	 CadastraDadosCurso - Atualiza o cronograma do curso
	Entrada: $sock - BASE DO CURSO ou BASE EXTERNA
	$cod_curso= codigo do curso,
	$nome_curso = nome do curso
	$inscricao_inicio = timestamp do dia de inicio da inscricao
	$inscricao_fim = timestamp do dia de fim da inscricao
	$curso_inicio = timestamp do dia de inicio do curso
	$curso_fim = timestamp do dia de fim do curso
	$informacoes = informacoes sobre o curso
	$publico_alvo = Publico esperado
	$tipo_inscricao = tipo de inscricao
	$acesso_visitante = 'A' - Permite acesso de visitante
	'N' - N�o permite
	$cod_lingua - Codigo da lingua padr�o a ser utilizada no curso
	$horario - hora atual (em que se est� realizando a atualiza��o)
	Saida: true se tudo ok.
	*/
	static function CadastraDadosCurso($sock,$cod_curso,$nome_curso,$inscricao_inicio,$inscricao_fim,$curso_inicio,$curso_fim,$informacoes,$publico_alvo,$tipo_inscricao,$acesso_visitante,$cod_lingua,$horario)
	{
		if ($acesso_visitante!='A')
			$acesso_visitante='N';
	
		$nome_curso = ConversorTexto::EliminaScript($nome_curso);
		$informacoes=ConversorTexto::EliminaScript($informacoes);
		$publico_alvo=ConversorTexto::EliminaScript($publico_alvo);
		$tipo_inscricao=ConversorTexto::EliminaScript($tipo_inscricao);
	
		$query ="update Cursos set nome_curso='".$nome_curso."',";
		$query.=" inscricao_inicio=".Data::Data2Unixtime($inscricao_inicio).",";
		$query.=" inscricao_fim=".Data::Data2Unixtime($inscricao_fim).",";
		$query.=" curso_inicio=".Data::Data2Unixtime($curso_inicio).",";
		$query.=" curso_fim=".Data::Data2Unixtime($curso_fim).",";
		$query.=" informacoes='".$informacoes."',";
		$query.=" publico_alvo='".$publico_alvo."',";
		$query.=" tipo_inscricao='".$tipo_inscricao."',";
		$query.=" cod_lingua=".$cod_lingua.",";
		$query.=" acesso_visitante='".$acesso_visitante."',";
		$query.=" _timestamp=".$horario;
		$query.=" where cod_curso=".$cod_curso;
		
		$res=AcessoSQL::Enviar($sock,$query);
		return $res;
	}
	
}