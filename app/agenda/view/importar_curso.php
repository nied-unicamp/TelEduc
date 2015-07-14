<?php

$dir_static = '../../../static_includes/';
$dir_lib = '../../../lib/';
$ctrl_agenda = '../controller/';
$ctrl_geral = '../../../app/geral/controller/';

include $ctrl_agenda.'AgendaController.php';
include $dir_lib.'FeedbackObject.inc.php';
include '../../../lib/DataJavaScript.php';

$cod_usuario = $_GET['cod_usuario'];

if (isset ($_GET['cod_curso']))
	$cod_curso = $_GET['cod_curso'];

//    codigo da categoria do curso
if (isset ($_GET['cod_categoria']))
	$cod_categoria = $_GET['cod_categoria'];
else
if (isset ($_POST['cod_categoria']))
	$cod_categoria = $_POST['cod_categoria'];
else
	$cod_categoria = "NULL";

if (isset ($_GET['tipo_curso']))
	$tipo_curso = $_GET['tipo_curso'];
else
if (isset ($_POST['tipo_curso']))
	$tipo_curso = $_POST['tipo_curso'];
else
	$tipo_curso = 'A';

if ($tipo_curso == 'E') {
	// Inicializa as datas para um periodo de um semestre anterior
	if (!isset ($data_inicio)) {
		// calcula, aproximadamente, 6 meses antes do dia de hoje (data_fim)
		$data_inicio = UnixTime2Data(time() - (60 * 60 * 24 * 30 * 6) - (60 * 60 * 24 * 2));
	} else
	if (isset ($_GET['data_inicio'])) {
		$data_inicio = $_GET['data_inicio'];
	}

	if (!isset ($data_fim)) {
		$data_fim = UnixTime2Data(time());
	} else
	if (isset ($_GET['data_fim'])) {
		$data_fim = $_GET['data_fim'];
	}
}
$cod_topico_raiz = $_GET['cod_topico_raiz'];

$cod_ferramenta = 1;
$cod_ferramenta_ajuda = $cod_ferramenta;
$cod_pagina_ajuda = 6;

include $dir_static.'topo_tela.php';

$feedbackObject =  new FeedbackObject();

//adicionar as acoes possiveis, 1o parametro é
$feedbackObject->addAction("validarImportacao", 0, 88);
$feedbackObject->addAction("ErroImportacao", 0, 112);

echo(" <script type=\"text/javascript\"\n");
echo(" var tipo_curso =".$tipo_curso."\n");
echo(" var hoje =".Data::UnixTime2Data(time())."\n");
echo(" var cod_curso = ".$cod_curso."\n");
echo(" var cod_topico_raiz = ".$cod_topico_raiz."\n");
echo(" var cod_ferramenta = ".$cod_ferramenta."\n");

echo ("      function Iniciar()\n");
echo ("      {\n");
echo ("        startList();\n");
$feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
echo ("      }\n\n");

echo("</script>");

DataJavaScript::GeraJSVerificacaoData();
DataJavaScript::GeraJSComparacaoDatas();

echo("	<script type=\"text/javascript\" src=\"../../../js/agenda.js\"></script>\n");
echo("	<script type=\"text/javascript\" src=\"../../../js/dhtmllib.js\"></script>\n");
echo("	<script type=\"text/javascript\" src=\"../../../js/jscript.js\"></script>\n");
echo("	<script type=\"text/javascript\" src=\"../../../js/javacrypt.js\"></script>\n");

include $dir_static.'menu_principal.php';

$controlerPermissao = new PermissaoController();

echo ("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
/* Impede o acesso a algumas secoes aos usuários que não são formadores. */
if (!$controlerPermissao->hasPermission($cod_usuario, $cod_ferramenta, 'Importar Agenda')){
	/* 1 - Agenda*/
	echo ("          <h4>Agenda");
	/* 73 - Acao exclusiva a formadores. */
	echo ("    - Acao exclusiva a formadores</h4>");

	/*Voltar*/
	/* 509 - Voltar */
	echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

	include $dir_static.'3as.php';

	echo ("        </td>\n");
	echo ("      </tr>\n");
	echo ("    </table>\n");
	echo ("  </body>\n");
	echo ("</html>\n");
	exit;
}

// 1 - "Agenda"
$cabecalho = ("         <h4>Agenda");
/*66 - Importando Agenda */
$cabecalho .= (" - Importando Agenda</h4>\n");
echo ($cabecalho);

// 3 A's - Muda o Tamanho da fonte
include $dir_static.'3as.php';

/*Voltar*/
/* 509 - Voltar */
echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;Voltar&nbsp;</span></li></ul>\n");

echo ("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo ("            <tr>\n");
echo ("              <td valign=\"top\" colspan=3>\n");
echo ("                <ul class=\"btAuxTabs\">\n");
/* 23(ger) - Voltar */
echo ("                  <li><a href=\"ver_editar.php?cod_curso=" . $cod_curso . "&amp;cod_usuario=" . $cod_usuario . "\">Voltar</a></li>\n");
/* 35(biblioteca) - Cursos Em Andamento */
echo ("                  <li><span onclick=\"ListarCursos('A');\">Cursos em Andamento</span></li>\n");
/* 36(biblioteca) - Cursos Com Inscrições Abertas  */
echo ("                  <li><span onclick=\"ListarCursos('I');\">Cursos Com Inscrições Abertas</span></li>\n");
/* 37(biblioteca) - Cursos Latentes */
echo ("                  <li><span onclick=\"ListarCursos('L');\">Cursos Latentes</span></li>\n");
/* 38(biblioteca) - Cursos Encerrados */
echo ("                  <li><span onclick=\"ListarCursos('E');\">Cursos Encerrados</span></li>\n");
echo ("                </ul>\n");
echo ("              </td>\n");
echo ("            </tr>\n");
echo ("            <tr>\n");
echo ("              <td>\n");

echo ("                  <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

echo ("                    <tr class=\"head\">\n");

if ('E' == $tipo_curso) {
	// 40(biblioteca) - Período:
	echo ("                      <td width=\"15%\">Período: </td>\n");
}
// 44(biblioteca) - Categorias
echo ("                      <td align=center width=\"15%\"><b>Categorias</b></td>\n");

switch ($tipo_curso) {
	// 62 - Cursos em andamento com agenda compartilhada
	case 'A' :
		$texto_categoria = 'Cursos em andamento com agenda compartilhada';
		$periodo_inicio = time();
		$periodo_fim = $periodo_inicio;
		break;
		// 63 - Cursos com inscricoes abertas com agenda compartilhada
	case 'I' :
		$texto_categoria = 'Cursos com inscricoes abertas com agenda compartilhada';
		$periodo_inicio = $periodo_fim = "";
		break;
		// 64 - Cursos em latentes com agenda compartilhada
	case 'L' :
		$texto_categoria = 'Cursos em latentes com agenda compartilhada';
		$periodo_inicio = $periodo_fim = "";
		break;
		// 65 - Cursos encerrados com agenda compartilhada
	case 'E' :
		$texto_categoria = 'Cursos encerrados com agenda compartilhada';
		// Converte datas do periodo para "UnixTime"
		$periodo_inicio = Data2UnixTime($data_inicio);
		$periodo_fim = Data2UnixTime($data_fim);
		break;
}

// Texto da categoria - ver acima
echo ("                      <td align=center width=\"30%\"><b>" . $texto_categoria . "</b></td>\n");
// 47(biblioteca) - Todos os Cursos
echo ("                      <td align=center><b>Todos os Cursos</b></td>\n");

/* switch ($tipo_curso) {
	case 'A' :
		$cursos_compart = RetornaCursosAndamentoCompart($sock, $cod_ferramenta, $cod_categoria, $periodo_inicio, $periodo_fim);
		break;
	case 'I' :
		$cursos_compart = RetornaCursosInscrAbertasCompart($sock, $cod_ferramenta, $cod_categoria, $periodo_inicio, $periodo_fim);
		break;
	case 'L' :
		$cursos_compart = RetornaCursosLatentesCompart($sock, $cod_ferramenta, $cod_categoria);
		break;
	case 'E' :
		$cursos_compart = RetornaCursosEncerradosCompart($sock, $cod_ferramenta, $cod_categoria, $periodo_inicio, $periodo_fim);
		break;
} */


echo ("                      <td width=\"10%\">&nbsp;\n");
echo ("                      </td>\n");
echo ("                    </tr>\n");
echo ("                    <tr>\n");

if ('E' == $tipo_curso) {

	echo ("                      <td>\n");
	echo ("                        <form name=\"frmAlteraPeriodo\" id=\"frmAlteraPeriodo\" method=\"post\" action=\"\" onsubmit=\"Valida(); return false;\">\n");
	// Passa o codigo do curso.
	echo ("                          <input type=\"hidden\" name=\"cod_curso\"      value='" . $cod_curso . "' />\n");
	// Passa o codigo da categoria dos cursos listados .
	echo ("                          <input type=\"hidden\" name=\"cod_categoria\"  value='" . $cod_categoria . "' />\n");
	// Passa o tipo do curso: E(xtraido) ou B(ase).
	echo ("                          <input type=\"hidden\" name=\"tipo_curso\"     value='" . $tipo_curso . "' />\n");
	// Passa o codigo da ferramenta.
	echo ("                          <input type=\"hidden\" name=\"cod_ferramenta\" value='" . $cod_ferramenta . "' />\n");
	// 46(biblioteca) - (extraido)
	echo ("                          <input type=\"hidden\" name=\"extraido\"       value='(extraido)' />\n");
	// 41(biblioteca) - De:
	echo ("                De: <input type=\"text\" id=\"data_inicio\" name=\"data_inicio\" size=\"10\" maxlength=\"10\" value='" . $data_inicio . "' class=\"input\" /><img src='../imgs/ico_calendario.gif' alt='' onclick=\"displayCalendar(document.getElementById ('data_inicio'),'dd/mm/yyyy',this);\" /><br />");
	// 42(biblioteca) - Ate:
	echo ("                Ate: \n");
	echo ("                          <input type=\"text\" id=\"data_fim\" name=\"data_fim\" size=\"10\" maxlength=\"10\" value='" . $data_fim . "' class='input' /><img src='../imgs/ico_calendario.gif' alt='' onclick=\"displayCalendar(document.getElementById ('data_fim'),'dd/mm/yyyy',this);\" />\n");
	echo ("                          <p style=\"text-align:center;\">\n");
	// 43(biblioteca) - Alterar Periodo
	echo ("                            <input type=\"submit\" class=\"input\" value=\"Alterar perodo/>\n");
	echo ("                          </p>\n");
	echo ("                        </form>");
	echo ("                      </td>\n");
}

echo ("                      <td>\n");
echo ("                <form name=\"frmImpMaterial\" method=\"get\" action=\"acoes_linha.php\">\n");

echo ("                  <input type=\"hidden\" name=\"cod_ferramenta\" value='" . $cod_ferramenta . "' />\n");
echo ("                  <input type=\"hidden\" name=\"cod_curso\" value='" . $cod_curso . "' />\n");
echo ("                  <input type=\"hidden\" name=\"acao\" value=\"validarImportacao\" />\n");
echo ("                  <input type=\"hidden\" id=\"getme\" name=\"cod_topico_raiz\" value='" . $cod_topico_raiz . "' />\n");
echo ("                  <input type=\"hidden\" name=\"tipo_curso\" value='" . $tipo_curso . "' />\n");

if ('E' == $tipo_curso) {
	echo ("                        <input type=\"hidden\" name=\"data_inicio\" value='' />\n");
	echo ("                        <input type=\"hidden\" name=\"data_fim\"    value='' />\n");
}







