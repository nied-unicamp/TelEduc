<?php

class Importar {
	
	/* **************************************************************************
	 RetornaCursosAndamentoCompart - Retorna todos os cursos com
	compartilhamento e que estao em andamento
	(que compartilham a ferramenta com
			cod_ferramenta). O curso deve pertencer
	a categoria especificada por $categoria.
	O inicio do curso (Curso_Inicio) tambem
	deve estar dentro do periodo especificado
	
	Entrada: $sock - sock Base Externa
	$cod_ferramenta - codigo da ferramenta compartilhada
	$cod_categoria - codigo da categoria (pasta) a que pertence o curso.
	Se $cod_categoria = "NULL", entao codigo da pasta = NULL
	(curso estara na categoria "Cursos Gerais")
	$periodo_inicio - data inicial do periodo (em formato UnixTime)
	$periodo_fim - data final do periodo (em formato UnixTime)
	
	Saida: array com []['cod_curso']
	[]['nome_curso']
	[]['status'] - 'B' em base de dados, 'E' extraido
		************************************************************************** */
	static function RetornaCursosAndamentoCompart($sock, $cod_ferramenta, $cod_categoria, $periodo_inicio, $periodo_fim)
	{
		$horario = time();
		$query  = "select C.cod_curso, C.nome_curso, 'B' as 'status'
               from Cursos as C,
                    Cursos_compart as Cc
               where C.cod_curso = Cc.cod_curso and
                     Cc.cod_ferramenta = ".ConversorTexto::VerificaNumeroQuery($cod_ferramenta)." and
                     C.curso_inicio <= ".$horario." and
                     C.curso_fim >=  ".$horario." and
                     C.cod_pasta ".((($cod_categoria == "NULL")||($cod_categoria=="")) ? ("is NULL") : ("= ".ConversorTexto::VerificaNumeroQuery($cod_categoria)))."
               order by C.nome_curso";
	
		$res   = AcessoSQL::Enviar ($sock, $query);
		$Lista_Cursos = AcessoSQL::RetornaArrayLinhas ($res);
		return $Lista_Cursos;
	}
	
	/* **************************************************************************
	 RetornaCursosInscrAbertasCompart - Retorna todos os cursos com
	compartilhamento (que compartilham
			a ferramenta com cod_ferramenta) e
	que estao com as inscricoes abertas
	O curso deve pertencer a categoria
	especificada por $categoria.
	O inicio do curso (curso_inicio)
	tambem deve estar dentro do periodo
	especificado.
	
	Entrada: $sock - sock Base Externa
	$cod_ferramenta - codigo da ferramenta compartilhada
	$cod_categoria - codigo da categoria (pasta) a que pertence o curso.
	Se $categoria = NULL, entao codigo da pasta = NULL
	(curso estara na categoria "Cursos Gerais")
	$periodo_inicio - data inicial do periodo (em formato UnixTime)
	$periodo_fim - data final do periodo (em formato UnixTime)
	
	Saida: array com []['cod_curso']
	[]['nome_curso']
	[]['status'] - 'B' em base de dados, 'E' extraido
		************************************************************************** */
	static function RetornaCursosInscrAbertasCompart ($sock, $cod_ferramenta, $cod_categoria, $periodo_inicio, $periodo_fim)
	{
		$hoje = time();
		$ontem = $hoje - 86400;
	
		$query  = "select C.cod_curso, C.nome_curso, 'B' as 'status'
               from Cursos as C,
                    Cursos_compart as Cc
               where C.cod_curso = Cc.cod_curso and
                     Cc.cod_ferramenta = ".ConversorTexto::VerificaNumeroQuery($cod_ferramenta)." and
                     C.inscricao_inicio <= ".$hoje." and
                     C.inscricao_fim >= ".$ontem." and
                     C.cod_pasta ".((($cod_categoria == "NULL")||($cod_categoria=="")) ? ("is NULL") : ("= ".ConversorTexto::VerificaNumeroQuery($cod_categoria)))."
               order by C.nome_curso";
	
		$res   = AcessoSQL::Enviar ($sock, $query);
		$Lista_Cursos = AcessoSQL::RetornaArrayLinhas ($res);
		return $Lista_Cursos;
	}
	
	/* **************************************************************************
	 RetornaCursosLatentesCompart - Retorna todos os cursos com
	compartilhamento (que compartilham a
			ferramenta com cod_ferramenta) e que sao
	latentes (periodo de realizacao ainda nao
			comecou e nao se encontram em periodo de
			inscricao)
	O curso deve pertencer a categoria
	especificada por $categoria.
	
	Entrada: $sock - sock Base Externa
	$cod_ferramenta - codigo da ferramenta compartilhada
	$cod_categoria - codigo da categoria (pasta) a que pertence o curso.
	Se $categoria = NULL, entao codigo da pasta = NULL
	(curso estara na categoria "Cursos Gerais")
	
	Saida: array com []['cod_curso']
	[]['nome_curso']
	[]['status'] - 'B' em base de dados, 'E' extraido
		************************************************************************** */
	static function RetornaCursosLatentesCompart ($sock, $cod_ferramenta, $cod_categoria)
	{
		$hoje = time();
	
		$query  = "select C.cod_curso, C.nome_curso, 'B' as 'status'
               from Cursos as C,
                    Cursos_compart as Cc
               where C.cod_curso = Cc.cod_curso and
                     Cc.cod_ferramenta = ".ConversorTexto::VerificaNumeroQuery($cod_ferramenta)." and
                     (((C.curso_inicio > ".$hoje.") and
                       ((C.inscricao_inicio > ".$hoje.") or
                        (C.inscricao_fim < ".$hoje."))
                      ) or
                      ((C.curso_inicio is NULL) and
                       (C.curso_fim is NULL) and
                       (C.inscricao_inicio is NULL) and
                       (C.inscricao_fim is NULL))
                     ) and
                     C.cod_pasta ".((($cod_categoria == "NULL")||($cod_categoria=="")) ? ("is NULL") : ("= ".ConversorTexto::VerificaNumeroQuery($cod_categoria)))."
               order by C.nome_curso";
	
		$res   = AcessoSQL::Enviar ($sock, $query);
		$Lista_Cursos = AcessoSQL::RetornaArrayLinhas ($res);
		return $Lista_Cursos;
	}
	
	/* **************************************************************************
	 RetornaCursosEncerradosCompart - Seleciona todos os cursos com
	compartilhamento (que compartilham a
			ferramenta com cod_ferramenta) e que
	foram encerrados.
	O curso deve pertencer a categoria
	especificada por $categoria.
	O inicio do curso (curso_inicio)
	tambem deve estar dentro do periodo
	especificado.
	
	Entrada: $sock - sock Base Externa
	$cod_ferramenta - codigo da ferramenta compartilhada
	$cod_categoria - codigo da categoria (pasta) a que pertence o curso.
	Se $categoria = NULL, entao codigo da pasta = NULL
	(curso estara na categoria "Cursos Gerais")
	$periodo_inicio - data inicial do periodo (em formato UnixTime)
	$periodo_fim - data final do periodo (em formato UnixTime)
	
	Saida: array com []['cod_curso']
	[]['nome_curso']
	[]['status] - 'B' em base de dados, 'E' extraido
		************************************************************************** */
	static function RetornaCursosEncerradosCompart ($sock, $cod_ferramenta, $cod_categoria, $periodo_inicio, $periodo_fim)
	{
		$query  = "select C.cod_curso, C.nome_curso, 'B' as 'status'
               from Cursos as C,
                    Cursos_compart as Cc
               where C.cod_curso = Cc.cod_curso and
                     Cc.cod_ferramenta = ".ConversorTexto::VerificaNumeroQuery($cod_ferramenta)." and
                     C.curso_fim < ".ConversorTexto::VerificaNumeroQuery($periodo_fim)." and
                     C.curso_inicio >= ".ConversorTexto::VerificaNumeroQuery($periodo_inicio)." and
                     C.curso_inicio <= ".ConversorTexto::VerificaNumeroQuery($periodo_fim)." and
                     C.cod_pasta ".((($cod_categoria == "NULL")||($cod_categoria=="")) ? ("is NULL") : ("= ".ConversorTexto::VerificaNumeroQuery($cod_categoria)))."
               order by C.nome_curso";
	
	
		$res   = AcessoSQL::Enviar ($sock, $query);
		$Lista_Cursos_Encerrados = AcessoSQL::RetornaArrayLinhas ($res);
	
		$Lista_Cursos="";
	
		// Se a categoria for a geral, listamos tambem os cursos extraidos.
		if ($cod_categoria == "NULL" && is_array($Lista_Cursos_Extraidos = Importar::RetornaCursosExtraidosCompart($sock, $cod_ferramenta, $periodo_inicio, $periodo_fim)) && is_array($Lista_Cursos_Encerrados))
		{
			$Lista_Cursos = array_merge($Lista_Cursos_Encerrados , $Lista_Cursos_Extraidos);
		}
		else if (is_array($Lista_Cursos_Encerrados))
			$Lista_Cursos = $Lista_Cursos_Encerrados;
		else if (is_array($Lista_Cursos_Extraidos))
			$Lista_Cursos = $Lista_Cursos_Extraidos;
	
		return $Lista_Cursos;
	}
	
	/* **************************************************************************
	 RetornaCursosExtraidosCompart - Seleciona todos os cursos extraidos com
	compartilhamento (que compartilham a
			ferramenta com cod_ferramenta).
	O inicio do curso (curso_inicio)
	tambem deve estar dentro do periodo
	especificado.
	
	Entrada: $sock - sock Base Externa
	$cod_ferramenta - codigo da ferramenta compartilhada
	$periodo_inicio - data inicial do periodo (em formato UnixTime)
	$periodo_fim - data final do periodo (em formato UnixTime)
	
	Saida: array com []['cod_curso']
	[]['nome_curso']
	[]['status] - 'B' em base de dados, 'E' extraido
		************************************************************************** */
	static function RetornaCursosExtraidosCompart($sock, $cod_ferramenta, $periodo_inicio, $periodo_fim)
	{
		// Para cursos extraidos ignoramos a categoria a qual pertencia,
		// pois ela pode nao mais existir.
		$query  = "select Ce.codigo as 'cod_curso', Ce.nome as 'nome_curso', 'E' as 'status'
               from Cursos_extraidos Ce
                 inner join Cursos_extraidos_compart Cec
                   on Ce.codigo = Cec.codigo and
                      Cec.cod_ferramenta = ".ConversorTexto::VerificaNumeroQuery($cod_ferramenta)."
               where Ce.curso_fim < ".ConversorTexto::VerificaNumeroQuery($periodo_fim)." and
                     Ce.curso_inicio >= ".ConversorTexto::VerificaNumeroQuery($periodo_inicio)." and
                     Ce.curso_inicio <= ".ConversorTexto::VerificaNumeroQuery($periodo_fim)."
               order by Ce.codigo";
		$res   = AcessoSQL::Enviar ($sock, $query);
		$Lista_Cursos = AcessoSQL::RetornaArrayLinhas ($res);
		return $Lista_Cursos;
	}
	
	/* **************************************************************************
	 RetornaCategoriasCursos - Retorna uma array com as categorias em que os
	cursos estao divididos
	(somente as categorias que contem algum curso)
	Entrada: $sock - sock Base Externa
	$tipo_curso - tipo do curso
	'A' - Em andamento
	'I' - Com inscricao aberta
	'E' - Encerrado
	'L' - Latente
	$cod_ferramenta - codigo da ferramenta
	Saida: array com []['cod_pasta']
	[]['pasta']
	[]['qtos_cursos']
	************************************************************************** */
	static function RetornaCategoriasCursos($sock, $tipo_curso, $cod_ferramenta)
	{
		$hoje = time();
		$ontem  = $hoje - 86400;
	
		if($tipo_curso=='') $tipo_curso='A';
	
		$where="";
	
		if ($tipo_curso == 'A')  // Cursos em andamento
			$where = " C.curso_inicio <= ".$hoje." and C.curso_fim >= ".$ontem;
		elseif ($tipo_curso == 'I') // Cursos com Inscricoes Abertas
		$where = " ((C.inscricao_inicio <= ".$hoje.") and
                (C.inscricao_fim >= ".$ontem.")) ";
		elseif ($tipo_curso == 'E') // Cursos Encerrados
		$where = " C.curso_fim < ".$ontem;
		elseif ($tipo_curso == 'L') // Cursos Latentes
		{
			$where = " (((C.curso_inicio > ".$hoje.") and
                 ((C.inscricao_inicio > ".$hoje.") or
                  (C.inscricao_fim < ".$hoje."))
                ) or
                ((C.curso_inicio is NULL) and
                 (C.curso_fim is NULL) and
                 (C.inscricao_inicio is NULL) and
                 (C.inscricao_fim is NULL))
               )";
		}
	
	
		$query  = "select CP.cod_pasta, CP.pasta, count(*) 'qtos_cursos'
               from Cursos_pastas as CP,
                    Cursos as C
               where ".$where." AND CP.cod_pasta = C.cod_pasta
               group by CP.cod_pasta
               order by CP.cod_pasta";
	
		$res   = AcessoSQL::Enviar ($sock, $query);
		$Lista_Categorias = AcessoSQL::RetornaArrayLinhas ($res);
		return $Lista_Categorias;
	}
	
	/* **************************************************************************
	 RetornaTodosCursos - Retorna uma array com todos os cursos, cuja data de
	inicio esteja no periodo determinado por
	$periodo_inicio e $periodo_fim
	
	Entrada: $sock - sock Base Externa
	$tipo_curso - tipo do curso: 'A' - em andamento
	'I' - com inscricoes abertas
	'L' - latentes
	'E' - encerrados
	$cod_categoria - codigo da categoria da qual serao listados os cursos
	$periodo_inicio - data inicial do periodo (em formato UnixTime)
	$periodo_fim - data final do periodo (em formato UnixTime)
	
	Saida: array com []['cod_curso']
	[]['nome_curso']
	[]['status'] - 'B' em base de dados, 'E' extraido
		************************************************************************** */
	static function RetornaTodosCursos ($sock, $tipo_curso, $cod_categoria, $periodo_inicio, $periodo_fim)
	{
	
	
		$hoje = time();
		$ontem = $hoje - 86400;
	
		if($tipo_curso=="") $tipo_curso='A';
	
		if ($tipo_curso == 'A')
			$where = " curso_inicio <= ".$hoje." and
               curso_fim >= ".$ontem;
		else if ($tipo_curso == 'I')
			$where = " inscricao_inicio <= ".$hoje." and
               inscricao_fim >= ".$ontem;
		else if ($tipo_curso == 'L')
			$where = "(((curso_inicio > ".$hoje.") and
                ((inscricao_inicio > ".$hoje.") or
                 (inscricao_fim < ".$hoje."))
               ) or
               ((curso_inicio is NULL) and
                (curso_fim is NULL) and
                (inscricao_inicio is NULL) and
                (inscricao_fim is NULL))
              )";
		else if ($tipo_curso == 'E')
		{
			if ($periodo_fim > $ontem)
				$periodo_fim = $ontem;
	
			$where = " curso_fim < ".ConversorTexto::VerificaNumeroQuery($periodo_fim)." and
               curso_inicio >= ".ConversorTexto::VerificaNumeroQuery($periodo_inicio);
		}
		if ($cod_categoria == "NULL" || $cod_categoria == "")
			$pasta = " is NULL";
		else if (is_int((int) $cod_categoria))
			$pasta = " = ".ConversorTexto::VerificaNumeroQuery($cod_categoria);
	
		$query = "select cod_curso, nome_curso, 'B' as 'status'
              from Cursos
              where ".$where." and
                    cod_pasta ".$pasta."
              order by nome_curso";
	
		$res   = AcessoSQL::Enviar ($sock, $query);
		$Lista_Cursos = AcessoSQL::RetornaArrayLinhas ($res);
	
		// Se estiver listando um curso Encerrado e a categoria for
		// Cursos gerais, listamos os cursos extraidos.
		if (($tipo_curso == 'E') &&
		(($cod_categoria == "NULL" || $cod_categoria == ""))
		)
		{
			$Lista_Cursos_Extraidos = Importar::RetornaTodosCursosExtraidos($sock, $periodo_inicio, $periodo_fim);
	
			if (is_array($Lista_Cursos) && is_array($Lista_Cursos_Extraidos))
				$lista = array_merge($Lista_Cursos, $Lista_Cursos_Extraidos);
			elseif (is_array($Lista_Cursos_Extraidos))
			$lista = $Lista_Cursos_Extraidos;
			elseif (is_array($Lista_Cursos))
			$lista = $Lista_Cursos;
			else
				$lista = NULL;
	
			$Lista_Cursos = $lista;
	
		}
	
		return $Lista_Cursos;
	}
	
	/* **************************************************************************
	 RetornaTodosCursosExtraidos - Retorna uma array com todos os cursos extraidos
	(tabela 'Cursos_extraidos'), cuja data de inicio esteja
	no periodo determinado por $periodo_inicio e $periodo_fim
	
	Entrada: $sock - sock Base Externa
	$periodo_inicio - data inicial do periodo (em formato UnixTime)
	$periodo_fim - data final do periodo (em formato UnixTime)
	
	Saida: array com []['cod_curso']
	[]['nome_curso']
	[]['status'] - 'B' em base de dados, 'E' extraido
		************************************************************************** */
	static function RetornaTodosCursosExtraidos($sock, $periodo_inicio, $periodo_fim)
	{
		$query  = "select codigo as 'cod_curso', nome as 'nome_curso', 'E' as 'status'
               from Cursos_extraidos
               where curso_fim < ".ConversorTexto::VerificaNumeroQuery($periodo_fim)." and
                     curso_inicio >= ".ConversorTexto::VerificaNumeroQuery($periodo_inicio)." and
                     curso_inicio <= ".ConversorTexto::VerificaNumeroQuery($periodo_fim)."
               order by codigo";
		$res   = AcessoSQL::Enviar ($sock, $query);
		$Lista_Cursos = AcessoSQL::RetornaArrayLinhas ($res);
	
		return $Lista_Cursos;
	}
	
	/* **************************************************************************
	 FerramentaEstaCompartilhada - Retorna todos os cursos com
	compartilhamento (que compartilham a
			ferramenta com cod_ferramenta) e que sao
	latentes (periodo de realizacao ainda nao
			comecou e nao se encontram em periodo de
			inscricao)
	O curso deve pertencer a categoria
	especificada por $categoria.
	
	Entrada: $sock - sock Base Externa
	$cod_ferramenta - codigo da ferramenta compartilhada
	$categoria - codigo da categoria (pasta) a que pertence o curso.
	Se $categoria = -1, entao codigo da pasta = NULL
	(curso estara na categoria "Cursos Gerais")
	
	Saida: array com []['cod_curso']
	[]['nome_curso']
	************************************************************************** */
	static function FerramentaEstaCompartilhada($sock, $cod_curso, $cod_ferramenta)
	{
		$query  = "select cod_curso
               from Cursos_compart
               where cod_curso = ".ConversorTexto::VerificaNumeroQuery($cod_curso)." and
                     cod_ferramenta = ".ConversorTexto::VerificaNumeroQuery($cod_ferramenta);
	
		$res   = AcessoSQL::Enviar ($sock, $query);
		$qtas_linhas = AcessoSQL::RetornaNumLinhas ($res);
	
		if ($qtas_linhas > 0)
			return true;
		else
			return false;
	}
	
	
	
	
	
	
	
	
}