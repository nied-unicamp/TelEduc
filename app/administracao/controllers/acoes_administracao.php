<?php
/**
* acoes_administacao.php
*
* Controller aчѕes administrativas do modulo administraчуo
* 
* Nestes blocos de cѓdigo sуo feitas as alteraчѕes de um
* dado curso que foi procurado a partir do cod_curso (id).
* Primeiro sуo buscadas informaчѕes adicionais do curso na tabela Cursos a partir do cod_curso.
* Em seguida щ criada uma sessуo com as frases relacionadas ao idioma escolhido e
* por њltimo os dados do curso sуo atualizados.
* 
*/


/**
 *  
 */
$diretorio_models = "../models/";
require_once $diretorio_models.'cursos.inc';
require_once $diretorio_models.'administracao.inc';
require_once $diretorio_models.'linguas.inc';

$action = $_POST['action'];
$cod_curso = $_POST['cod_curso'];
$nome_curso = $_POST['nome_curso'];
$inscricao_inicio = $_POST['inscricao_inicio'];
$inscricao_fim = $_POST['inscricao_fim'];
$curso_inicio = $_POST['curso_inicio'];
$curso_fim = $_POST['curso_fim'];
$informacoes = $_POST['informacoes'];
$publico_alvo = $_POST['publico_alvo'];
$tipo_inscricao = $_POST['tipo_inscricao'];
$cod_lin = $_POST['cod_lin'];

$sock = AcessoSQL::Conectar($cod_curso);


if($action == "alterarDadosCurso")
{
	$completar=Cursos::CompletarDadosCurso($sock,$cod_curso);
	$horario = time();
	$cadastro_dentro=Administracao::CadastraDadosCurso($sock,$cod_curso,$nome_curso,$inscricao_inicio,$inscricao_fim,$curso_inicio,$curso_fim,$informacoes,$publico_alvo,$tipo_inscricao,$acesso_visitante,$cod_lin,$horario);
	AcessoSQL::Desconectar($sock);
	$sock=AcessoSQL::Conectar("");
	Linguas::MudancaDeLingua($sock,$cod_lin);
	$cadastro_fora=Administracao::CadastraDadosCurso($sock,$cod_curso,$nome_curso,$inscricao_inicio,$inscricao_fim,$curso_inicio,$curso_fim,$informacoes,$publico_alvo,$tipo_inscricao,$acesso_visitante,$cod_lin,$horario);

	if (!($cadastro_fora && $cadastro_dentro))
	{
		$confirma='false'; //Erro
	}
	else
	{
		AcessoSQL::Desconectar($sock);
		$confirma='true';
	}
	header("Location:".$diretorio_views."administracao.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&acao=".$action."&atualizacao=".$confirma."");
}

?>