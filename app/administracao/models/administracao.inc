<?php
$ferramenta_geral = 'geral';

$model_geral = '../../'.$ferramenta_geral.'/models/';

require_once $model_geral.'data.inc';

/**
 * Classe Model do modulo Administração
 * @author     TelEduc
 * @copyright  2014 TelEduc
 * @license    http://teleduc.org.br/
 */

class Administracao{
	/**
     * Este método estático faz uma consulta ao banco a tabela Cursos e retorna um array 
     * indexado pelos nomes das colunas.
     *
     * @param mysqli_connect $sock	Instância de conexão do mysql 
     * @param int $cod_curso Id com o código do curso a ser consultado 
     * @return array Retorna um array indexado pelos nomes das colunas da tabela Cursos
     * @access public
     */
	static function RetornaDadosCursoAdm($sock,$cod_curso)
	{
		$query ="select * from Cursos where cod_curso=".$cod_curso;
		$res=AcessoSQL::Enviar($sock,$query);
		return (AcessoSQL::RetornaLinha($res));
	}
	
	/**
     * Este método estático atualiza a tabela Cursos e 
     * retorna true se a operacao for bem sucedida 
     *
     * @param mysqli_connect sock Instância de conexão do mysql
     * @param int $cod_curso Id com o código do curso a ser consultado
     * @param string $nome_curso Nome do curso 
     * @param string $inscricao_inicio Data de início da inscrição no formato dd/mm/yyyy ou mm/dd/yyyy
     * @param string $curso_inicio Data do início do curso no formato dd/mm/yyyy ou mm/dd/yyyy 
     * @param string $curso_fim Data do fim do curso no formato dd/mm/yyyy ou mm/dd/yyyy 
     * @param string $informacoes Texto sobre informações adicionais
     * @param string $publico_alvo Texto sobre publico alvo
     * @param string $tipo_inscricao  Texto
     * @param string $acesso_visitante Texto 
     * @param int $cod_lingua Id relacionou da tabela de linguas
     * @param string $horario Timestamp, funcao time() do php 
     * @return boolean Se true operacao foi bem sucessidida
     * @access public
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
	
	/* *********************************************************************
	 HouveAlteracoes - Verifica se houve alteracoes nas bases de dados
	utilizada pela administracao.
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
	'N' - Nao permite
	*/
	static function HouveAlteracoes($sock,$cod_curso,$cod_usuario)
	{
		// 2Session
		$dbnamebase = $_SESSION['dbnamebase'];
		/* Cria um array com 4 elementos com valores iniciais iguais a zero. */
		/* O valor de cada elemento sera utilizado para adicionar a tag bold */
		/* nas ferramentas onde houve modificacoes.                          */
	
		$alt = array(0,  /* Modificacoes no cronograma do curso.         */
				0,  /* Modificacoes na escolha de ferramentas.      */
				0,  /* Modificacoes no destaque de ferramentas.     */
				0); /* Nova requisicao de inscricao.            */
	
		/* Obtem o horario do penltimo acesso do usuario e compara-o com */
		/* as datas das tabelas.                                          */
		$penultac = Usuarios::PenultimoAcesso($sock, $cod_usuario, "");
	
		/* Verifica se houve modificacao no cronograma do curso.          */
		$query = "select _timestamp 'data' from Cursos";
		$res = AcessoSQL::Enviar($sock, $query);
		$tupla = AcessoSQL::RetornaLinha($res);
		if ($tupla['data'] > $penultac)
			$alt[0] = 1;
	
		/* Verifica se houve modificacao na escolha das ferramentas.      */
		unset($tupla);
		$query = "select max(_timestamp) 'data' from Curso_ferramentas";
		$res = AcessoSQL::Enviar($sock, $query);
		$tupla = AcessoSQL::RetornaLinha($res);
		if ($tupla['data'] > $penultac)
			$alt[1] = 1;
	
		/* Verifica se houve modificacao no destaque das ferramentas.     */
		unset($tupla);
		$query = "select max(data) 'data' from Ferramentas_destaque";
		$res = AcessoSQL::Enviar($sock, $query);
		$tupla = AcessoSQL::RetornaLinha($res);
		if ($tupla['data'] > $penultac)
			$alt[2] = 1;
	
		/* Verifica se ha uma nova requisicao de inscricao.               */
		unset($tupla);
		$query = "select max(data_inscricao) 'data' from ".$dbnamebase.".Usuario_curso where binary tipo_usuario = 'a' and cod_curso='".$cod_curso."'";
		$res = AcessoSQL::Enviar($sock, $query);
		$tupla = AcessoSQL::RetornaLinha($res);
		if ($tupla['data'] > $penultac)
			$alt[3] = 1;
	
		return($alt);
	}
	
	static function RetornaExtrator($sock) {
		$query = "select valor from Config where item='extrator'";
		$res = AcessoSQL::Enviar($sock, $query);
		$linha = AcessoSQL::RetornaLinha($res);
		$extrator = $linha['valor'];
		
		return $extrator;
	}
}
?>
