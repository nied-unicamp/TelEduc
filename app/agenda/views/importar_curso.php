<?php
$ferramenta_geral = 'geral';
$ferramenta_agenda = 'agenda';
$ferramenta_administracao = 'administracao';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_agenda = '../../'.$ferramenta_agenda.'/models/';
$ctrl_agenda = '../../'.$ferramenta_agenda.'/controllers/';
$view_agenda = '../../'.$ferramenta_agenda.'/views/';
$view_administracao = '../../'.$ferramenta_administracao.'/views/';
$diretorio_jscss = '../../../web-content/js-css/';
$diretorio_imgs = '../../../web-content/imgs/';

require_once $model_geral.'geral.inc';
require_once $model_agenda.'agenda.inc';
require_once $model_geral.'importar.inc';

//$objAjax->register(XAJAX_FUNCTION,"AlterarPeriodoDinamic");
// Registra funcoes para uso de menu_principal.php
//$objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");

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
		$data_inicio = Data::UnixTime2Data(time() - (60 * 60 * 24 * 30 * 6) - (60 * 60 * 24 * 2));
	} else
	if (isset ($_GET['data_inicio'])) {
		$data_inicio = $_GET['data_inicio'];
	}

	if (!isset ($data_fim)) {
		$data_fim = Data::UnixTime2Data(time());
	} else
	if (isset ($_GET['data_fim'])) {
		$data_fim = $_GET['data_fim'];
	}
}

$cod_topico_raiz = $_GET['cod_topico_raiz'];

$sock = AcessoSQL::Conectar("");
$lista_frases_biblioteca = Linguas::RetornaListaDeFrases($sock, -2);
$lista_frases_gerais = Linguas::RetornaListaDeFrases($sock, -1);
AcessoSQL::Desconectar($sock);

$cod_ferramenta = 1;
$cod_ferramenta_ajuda = $cod_ferramenta;
$cod_pagina_ajuda = 6;

require_once $view_administracao.'topo_tela.php';

$feedbackObject =  new FeedbackObject($lista_frases);

//adicionar as acoes possiveis, 1o parametro é
//$feedbackObject->addAction("validarImportacao", 0, 88);
$feedbackObject->addAction("validarImportacao", 0, 'Importar selecionados');
//$feedbackObject->addAction("ErroImportacao", 0, 112);
$feedbackObject->addAction("ErroImportacao", 0, 'Erro ao Importar, favor selecionar uma opção!');

echo ("    <script type=\"text/javascript\" src=\"".$diretorio_jscss."dhtmllib.js\"></script>\n");
echo ("    <script type=\"text/javascript\" language=javascript src=\"".$diretorio_jscss."javacrypt.js\"></script>\n");
echo("	   <script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js\"></script>");

DataJavaScript::GeraJSVerificacaoData();
DataJavaScript::GeraJSComparacaoDatas();

if ($tipo_curso == 'E') {
	echo ("    <script type=\"text/javascript\">\n\n");

	echo ("      function CopiaPeriodo()\n");
	echo ("      {\n");
	echo ("        document.frmImpMaterial.data_inicio.value = document.frmAlteraPeriodo.data_inicio.value;\n");
	echo ("        document.frmImpMaterial.data_fim.value = document.frmAlteraPeriodo.data_fim.value;\n");
	echo ("      }\n\n");

	// Se os cursos listados sao do tipo E(ncerrado) entao
	// cria funcao de validacao para periodo dos cursos.

	// Valida as datas (inicial e final) do periodo

	echo("	function Valida(){\n");
	echo ("        hoje = '" . Data::UnixTime2Data(time()) . "';\n\n");

	echo ("        if (ComparaData(document.getElementById('data_inicio'), document.getElementById('data_fim')) > 0)\n");
	echo ("        {\n");
	//31(biblioteca) - Período Inválido!
	echo ("          alert('" . Linguas::RetornaFraseDaLista($lista_frases_biblioteca, 31) . "!');\n");
	echo ("          document.frmAlteraPeriodo.data_inicio.value = document.frmAlteraPeriodo.data_fim.value;\n");
	echo ("          return false;\n");
	echo ("        }\n");
	echo ("        else if (AnoMesDia(document.getElementById('data_fim').value) > AnoMesDia(hoje))\n");
	echo ("        {\n");
	// 31(biblioteca) - Período Inválido!
	echo ("          alert('" . Linguas::RetornaFraseDaLista($lista_frases_biblioteca, 31) . "!');\n");
	echo ("          document.getElementById('data_fim').value = hoje;\n");
	echo ("          return false;\n");
	echo ("        }\n");
	echo ("        var select = document.getElementById('cod_curso_todos');\n");
	echo ("        while(select.length>0){\n");
	echo ("          select.removeChild(select.firstChild);\n");
	echo ("        }\n");
	echo ("        var select = document.getElementById('cod_curso_compart');\n");
	echo ("        while(select.length>0){\n");
	echo ("          select.removeChild(select.firstChild);\n");
	echo ("        }\n");
	echo("					$.ajax({\n");
	echo("					type: 'post',\n");
	echo("					url: '".$model_geral."alterar_periodo.php',\n");
	echo("					data: $('#frmAlteraPeriodo').serialize(),\n");
	echo("					success: function(data) {\n");
	echo("						var flag = $.parseJSON(data);\n");
	echo("							var y;\n");
	echo("							$.each(flag.cod_curso_todos, function(key, value){\n");
	echo("								var frase = document.frmAlteraPeriodo.extraido.value;\n");
	echo("								y= document.createElement('option');\n");
	echo("								y.innerHTML=value.nome_curso + (value.status=='E' ? frase:'');\n");
	echo("								y.value=value.status + ';' + value.cod_curso;\n");
	echo("								document.getElementById('cod_curso_todos').appendChild(y);");
	echo("							});\n");
	echo("							$.each(flag.cod_curso_todos, function(key, value){\n");
	echo("								var frase = document.frmAlteraPeriodo.extraido.value;\n");
	echo("								y= document.createElement('option');\n");
	echo("								y.innerHTML=value.nome_curso + (value.status=='E' ? frase:'');\n");
	echo("								y.value=value.status + ';' + value.cod_curso;\n");
	echo("								document.getElementById('cod_curso_compart').appendChild(y);");
	echo("							});\n");
	echo("					}\n");
	echo("					});\n");
	echo("	}\n");
} else {
	echo ("    <script type=\"text/javascript\">\n\n");
}

echo ("      function Iniciar()\n");
echo ("      {\n");
echo ("        startList();\n");
$feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
echo ("      }\n\n");

echo ("        function desmarcaSelect(selectObj)\n");
echo ("        {\n");
echo ("          eval(\"document.getElementById(selectObj).selectedIndex = -1;\");\n");
echo ("        }\n\n");

echo ("        function EnviaReq(){\n");
if ($tipo_curso == 'E')
	echo ("          CopiaPeriodo();\n");

echo ("          return(selecionouCurso());\n");
echo ("        }\n");

echo ("        function extracheck(obj)\n");
echo ("        {\n");
echo ("          return !obj.disabled;\n");
echo ("        }\n\n");

echo ("      function ListarCursos(tipo_curso)\n");
echo ("      {\n");
echo ("        document.frmImpMaterial.tipo_curso.value = tipo_curso;\n");
echo ("        document.frmImpMaterial.action = 'importar_curso.php?cod_curso=" . $cod_curso . "&cod_ferramenta=" . $cod_ferramenta . "&cod_topico_raiz=" . $cod_topico_raiz . "';\n");
echo ("        document.frmImpMaterial.submit();\n");
echo ("      }\n\n");

echo ("      function mudarCategoria()\n");
echo ("      {\n");

//echo("        x = document.getElementById('select_categorias');");
//echo("        cod_categoria = x.options[x.selectedIndex].value;");
//echo("        document.frmImpPergunta.cod_categoria;");
echo ("        document.frmImpMaterial.action = 'importar_curso.php';\n");
echo ("        document.frmImpMaterial.cod_curso.value = " . $cod_curso . ";\n");
echo ("        document.frmImpMaterial.tipo_curso.value = '" . $tipo_curso . "';\n");
echo ("        document.frmImpMaterial.submit();\n");
echo ("      }\n\n");

echo ("  function mudafonte(tipo) {\n");
echo ("    if ( tipo == 0 ) {");
echo ("          document.getElementById(\"tabelaInterna\").style.fontSize=\"1.0em\";\n");
echo ("          tipo=''; \n");
echo ("    } \n");
echo ("    if ( tipo == 1 ) {\n");
echo ("      document.getElementById(\"tabelaInterna\").style.fontSize=\"1.2em\";\n");
echo ("      tipo=''; \n");
echo ("    }\n");
echo ("    if ( tipo == 2 ) { \n");
echo ("      document.getElementById(\"tabelaInterna\").style.fontSize=\"1.4em\";\n");
echo ("      tipo=''; \n");
echo ("    }\n");
echo ("  }\n");

echo ("      function Voltar()\n");
echo ("      {\n");
echo ("        window.location='agenda.php?cod_curso=" . $cod_curso . "&cod_usuario=" . $cod_usuario . "';;\n");
echo ("      }\n\n");

echo ("      </script>\n\n");

require_once $view_administracao.'menu_principal.php';

echo ("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
/* Impede o acesso a algumas secoes aos usuários que não são formadores. */
if (!$tela_formador) {
	/* 1 - Agenda*/
	echo ("          <h4>" . Linguas::RetornaFraseDaLista($lista_frases, 1));
	/* 73 - Acao exclusiva a formadores. */
	echo ("    - " . Linguas::RetornaFraseDaLista($lista_frases, 73) . "</h4>");

	/*Voltar*/
	/* 509 - Voltar */
	echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".Linguas::RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

	echo ("          <div id=\"mudarFonte\">\n");
	echo ("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"".$diretorio_imgs."btFont1.gif\"/></a>\n");
	echo ("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"".$diretorio_imgs."btFont2.gif\"/></a>\n");
	echo ("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"".$diretorio_imgs."btFont3.gif\"/></a>\n");
	echo ("          </div>\n");

	echo ("        </td>\n");
	echo ("      </tr>\n");
	echo ("    </table>\n");
	echo ("  </body>\n");
	echo ("</html>\n");
	AcessoSQL::Desconectar($sock);
	exit;
}

// 1 - "Material"
$cabecalho = ("         <h4>" . Linguas::RetornaFraseDaLista($lista_frases, 1));
/*66 - Importando Agenda */
$cabecalho .= (" - " . Linguas::RetornaFraseDaLista($lista_frases, 66) . "</h4>\n");
echo ($cabecalho);

// 3 A's - Muda o Tamanho da fonte
echo ("          <div id=\"mudarFonte\">\n");
echo ("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"".$diretorio_imgs."btFont1.gif\"/></a>\n");
echo ("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"".$diretorio_imgs."btFont2.gif\"/></a>\n");
echo ("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"".$diretorio_imgs."btFont3.gif\"/></a>\n");
echo ("          </div>\n");

/*Voltar*/
/* 509 - Voltar */
echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".Linguas::RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

echo ("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo ("            <tr>\n");
echo ("              <td valign=\"top\" colspan=3>\n");
echo ("                <ul class=\"btAuxTabs\">\n");
/* 23(ger) - Voltar */
echo ("                  <li><a href=\"ver_editar.php?cod_curso=" . $cod_curso . "&amp;cod_usuario=" . $cod_usuario . "\">" . Linguas::RetornaFraseDaLista($lista_frases_geral, 23) . "</a></li>\n");
/* 35(biblioteca) - Cursos Em Andamento */
echo ("                  <li><span onclick=\"ListarCursos('A');\">" . Linguas::RetornaFraseDaLista($lista_frases_biblioteca, 35) . "</span></li>\n");
/* 36(biblioteca) - Cursos Com Inscrições Abertas  */
echo ("                  <li><span onclick=\"ListarCursos('I');\">" . Linguas::RetornaFraseDaLista($lista_frases_biblioteca, 36) . "</span></li>\n");
/* 37(biblioteca) - Cursos Latentes */
echo ("                  <li><span onclick=\"ListarCursos('L');\">" . Linguas::RetornaFraseDaLista($lista_frases_biblioteca, 37) . "</span></li>\n");
/* 38(biblioteca) - Cursos Encerrados */
echo ("                  <li><span onclick=\"ListarCursos('E');\">" . Linguas::RetornaFraseDaLista($lista_frases_biblioteca, 38) . "</span></li>\n");
echo ("                </ul>\n");
echo ("              </td>\n");
echo ("            </tr>\n");
echo ("            <tr>\n");
echo ("              <td>\n");

echo ("                  <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

echo ("                    <tr class=\"head\">\n");
if ('E' == $tipo_curso) {
	// 40(biblioteca) - Período:
	echo ("                      <td width=\"15%\">" . Linguas::RetornaFraseDaLista($lista_frases_biblioteca, 40) . "</td>\n");
}
// 44(biblioteca) - Categorias
echo ("                      <td align=center width=\"15%\"><b>" . Linguas::RetornaFraseDaLista($lista_frases_biblioteca, 44) . "</b></td>\n");

switch ($tipo_curso) {
	// 62 - Cursos em andamento com agenda compartilhada
	case 'A' :
		$texto_categoria = Linguas::RetornaFraseDaLista($lista_frases, 62);
		$periodo_inicio = time();
		$periodo_fim = $periodo_inicio;
		break;
		// 63 - Cursos com inscricoes abertas com agenda compartilhada
	case 'I' :
		$texto_categoria = Linguas::RetornaFraseDaLista($lista_frases, 63);
		$periodo_inicio = $periodo_fim = "";
		break;
		// 64 - Cursos em latentes com agenda compartilhada
	case 'L' :
		$texto_categoria = Linguas::RetornaFraseDaLista($lista_frases, 64);
		$periodo_inicio = $periodo_fim = "";
		break;
		// 65 - Cursos encerrados com agenda compartilhada
	case 'E' :
		$texto_categoria = Linguas::RetornaFraseDaLista($lista_frases, 65);
		// Converte datas do periodo para "UnixTime"
		$periodo_inicio = Data::Data2UnixTime($data_inicio);
		$periodo_fim = Data::Data2UnixTime($data_fim);
		break;
}

// Texto da categoria - ver acima
echo ("                      <td align=center width=\"30%\"><b>" . $texto_categoria . "</b></td>\n");
// 47(biblioteca) - Todos os Cursos
echo ("                      <td align=center><b>" . Linguas::RetornaFraseDaLista($lista_frases_biblioteca, 47) . "</b></td>\n");

$sock = AcessoSQL::MudarDB($sock, "");

switch ($tipo_curso) {
	case 'A' :
		$cursos_compart = Importar::RetornaCursosAndamentoCompart($sock, $cod_ferramenta, $cod_categoria, $periodo_inicio, $periodo_fim);
		break;
	case 'I' :
		$cursos_compart = Importar::RetornaCursosInscrAbertasCompart($sock, $cod_ferramenta, $cod_categoria, $periodo_inicio, $periodo_fim);
		break;
	case 'L' :
		$cursos_compart = Importar::RetornaCursosLatentesCompart($sock, $cod_ferramenta, $cod_categoria);
		break;
	case 'E' :
		$cursos_compart = Importar::RetornaCursosEncerradosCompart($sock, $cod_ferramenta, $cod_categoria, $periodo_inicio, $periodo_fim);
		break;
}

echo ("                      <td width=\"10%\">&nbsp;\n");
echo ("                      </td>\n");
echo ("                    </tr>\n");
echo ("                    <tr>\n");

if ('E' == $tipo_curso) {

	echo ("                      <td>\n");
	echo ("                        <form name=\"frmAlteraPeriodo\" id=\"frmAlteraPeriodo\" method=\"post\" action=\"\" onsubmit=\"Valida(); return false;\">\n");
	//echo ("                        <form name=\"frmAlteraPeriodo\" id=\"frmAlteraPeriodo\" method=\"post\" action=\"\">\n");
	// Passa o codigo do curso.
	echo ("                          <input type=\"hidden\" name=\"cod_curso\"      value='" . $cod_curso . "' />\n");
	// Passa o codigo da categoria dos cursos listados .
	echo ("                          <input type=\"hidden\" name=\"cod_categoria\"  value='" . $cod_categoria . "' />\n");
	// Passa o tipo do curso: E(xtraido) ou B(ase).
	echo ("                          <input type=\"hidden\" name=\"tipo_curso\"     value='" . $tipo_curso . "' />\n");
	// Passa o codigo da ferramenta.
	echo ("                          <input type=\"hidden\" name=\"cod_ferramenta\" value='" . $cod_ferramenta . "' />\n");
	// 46(biblioteca) - (extraido)
	echo ("                          <input type=\"hidden\" name=\"extraido\"       value='" . Linguas::RetornaFraseDaLista($lista_frases_biblioteca, 46) . "' />\n");
	// 41(biblioteca) - De:
	echo ("                " . Linguas::RetornaFraseDaLista($lista_frases_biblioteca, 41) . " <input type=\"text\" id=\"data_inicio\" name=\"data_inicio\" size=\"10\" maxlength=\"10\" value='" . $data_inicio . "' class=\"input\" /><img src='".$diretorio_imgs."ico_calendario.gif' alt='' onclick=\"displayCalendar(document.getElementById ('data_inicio'),'dd/mm/yyyy',this);\" /><br />");
	// 42(biblioteca) - Ate:
	echo ("                " . Linguas::RetornaFraseDaLista($lista_frases_biblioteca, 42) . "\n");
	echo ("                          <input type=\"text\" id=\"data_fim\" name=\"data_fim\" size=\"10\" maxlength=\"10\" value='" . $data_fim . "' class='input' /><img src='".$diretorio_imgs."ico_calendario.gif' alt='' onclick=\"displayCalendar(document.getElementById ('data_fim'),'dd/mm/yyyy',this);\" />\n");
	echo ("                          <p style=\"text-align:center;\">\n");
	// 43(biblioteca) - Alterar Periodo
	echo ("                            <input type=\"submit\" class=\"input\" value=\"" . Linguas::RetornaFraseDaLista($lista_frases_biblioteca, 43) . "\" />\n");
	echo ("                          </p>\n");
	echo ("                        </form>");
	echo ("                      </td>\n");
}

echo ("                      <td>\n");
echo ("                <form name=\"frmImpMaterial\" method=\"get\" action=\"".$ctrl_agenda."acoes_linha.php\">\n");

echo ("                  <input type=\"hidden\" name=\"cod_ferramenta\" value='" . $cod_ferramenta . "' />\n");
echo ("                  <input type=\"hidden\" name=\"cod_curso\" value='" . $cod_curso . "' />\n");
echo ("                  <input type=\"hidden\" name=\"acao\" value=\"validarImportacao\" />\n");
echo ("                  <input type=\"hidden\" id=\"getme\" name=\"cod_topico_raiz\" value='" . $cod_topico_raiz . "' />\n");
echo ("                  <input type=\"hidden\" name=\"tipo_curso\" value='" . $tipo_curso . "' />\n");

if ('E' == $tipo_curso) {
	echo ("                        <input type=\"hidden\" name=\"data_inicio\" value='' />\n");
	echo ("                        <input type=\"hidden\" name=\"data_fim\"    value='' />\n");
}

$categorias = Importar::RetornaCategoriasCursos($sock, $tipo_curso, $cod_ferramenta);

// Monta Select com as categorias de cursos
echo ("                        <select name=\"cod_categoria\" class=\"input\" onChange='");
echo ((($tipo_curso == 'E') ? "CopiaPeriodo();" : "") . "mudarCategoria();'>\n");

if (count($categorias) > 0) {
	foreach ($categorias as $idx => $cod) {
		echo ("                          <option value=" . $cod["cod_pasta"]);
		echo ((($cod["cod_pasta"] == $cod_categoria) ? " selected" : ""));
		echo (">" . $cod["pasta"] . "</option> \n");
	}
}

// 45(biblioteca) - Cursos Gerais
echo ("                          <option value='NULL'" . (("NULL" == $cod_categoria) ? " selected" : "") . ">" . Linguas::RetornaFraseDaLista($lista_frases_biblioteca, 45) . "</option> \n");
echo ("                        </select>\n");
echo ("                      </td>\n");
echo ("                      <td align=\"center\">\n");
// Monta select com os cursos com material compatilhado
echo ("                        <select class=\"input\" name=\"cod_curso_todos\" id=\"cod_curso_compart\" size=4 style=\"width:100%\" onFocus='desmarcaSelect(\"cod_curso_todos\");' onDblClick='if(this.value!=0){" . (('E' == $tipo_curso) ? "CopiaPeriodo();" : "") . "}'>\n");

if (count($cursos_compart) > 0) {
	foreach ($cursos_compart as $idx => $dados) {
		// 46(biblioteca) - (extraído)
		echo ("                          <option value='" . $dados['status'] . ";" . $dados["cod_curso"] . "'>" . $dados["nome_curso"] . (($dados['status'] == 'E') ? (" " . Linguas::RetornaFraseDaLista($lista_frases_biblioteca, 46)) : "") . "</option>\n");
	}
}
echo ("                        </select>\n");
echo ("                      </td>\n");
echo ("                      <td>\n");
$todos_cursos = Importar::RetornaTodosCursos($sock, $tipo_curso, $cod_categoria, $periodo_inicio, $periodo_fim);
//  $cursos_menos_compart =  Diferenca_Entre_Vetores ($Todos_Cursos, $Todos_Cursos_Compart);
AcessoSQL::Desconectar($sock);
// Monta select com os demais cursos (todos - compartilhados)
echo ("                        <select class=\"input\" name=\"cod_curso_todos\" id=\"cod_curso_todos\" size=4 style=\"width:100%\" onFocus='desmarcaSelect(\"cod_curso_compart\");' onDblClick='if(this.value!=0){" . (('E' == $tipo_curso) ? "CopiaPeriodo();" : "") . "}'>\n");

if (count($todos_cursos) > 0) {
	foreach ($todos_cursos as $idx => $dados) {
		// 46(biblioteca) - (extraido)
		echo ("                          <option value='" . $dados['status'] . ";" . $dados["cod_curso"] . "'>" . $dados["nome_curso"] . (($dados['status'] == 'E') ? (" " . Linguas::RetornaFraseDaLista($lista_frases_biblioteca, 46)) : "") . "</option>\n");
	}
}
echo ("                        </select>\n");
echo ("                      </td>\n");
echo ("                      <td>\n");
/* 75(ger) - Importar */
echo ("                        <input class=\"input\" type=\"submit\"  value=\"" . Linguas::RetornaFraseDaLista($lista_frases_geral, 75) . "\" />\n");
echo ("                      </td>\n");
echo ("                    </tr>\n");
echo ("                </table>\n");
echo ("              </td>\n");
echo ("            </tr>\n");
echo ("          </table>\n");
echo ("        </td>\n");
echo ("      </tr>\n");
require_once $view_administracao.'tela2.php';
echo ("  </body>\n");
echo ("</html>\n");


