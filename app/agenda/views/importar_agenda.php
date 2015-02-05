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

$cod_curso_origem = $_GET['cod_curso_origem'];
$cod_curso = $_GET['cod_curso'];

if ($_SESSION['flag_curso_compartilhado'] == true){
	$curso_compartilhado = true;
}

require_once $model_geral.'geral.inc';
require_once $model_agenda.'agenda.inc';
require_once $model_geral.'importar.inc';

$cod_ferramenta = 1;
$cod_ferramenta_ajuda = $cod_ferramenta;
$cod_pagina_ajuda = 6;

require_once $view_administracao.'topo_tela.php';

$tabela = "Agenda";
$dir = "agenda";

if (Usuarios::EFormador($sock, $cod_curso, $cod_usuario))
	$usr_formador = true;
else
	$usr_formador = false;

$sock = AcessoSQL::MudarDB($sock, $cod_curso_origem);

echo ("    <script type=\"text/javascript\" src=\"".$diretorio_jscss."dhtmllib.js\"></script>\n");
echo ("    <script type=\"text/javascript\"src=\"".$diretorio_jscss."javacrypt.js\" defer></script>\n");
/* Funcoes JavaScript */
echo ("    <script type=\"text/javascript\" defer>\n\n");

echo ("      function Iniciar()\n");
echo ("      {\n");
echo ("        startList();\n");
echo ("      }\n\n");

echo ("      function ExibirItem(cod_item)\n");
echo ("       {\n");
echo ("         document.frmImportar.cod_item.value = cod_item;\n");
echo ("         document.frmImportar.action = 'importar_ver.php?cod_curso=" . $cod_curso . "&cod_usuario=" . $cod_usuario . "&cod_ferramenta=1';");
echo ("         document.frmImportar.submit();\n");
echo ("       }\n\n");

echo ("      function Validacheck(){\n");
echo ("        var i;\n");
echo ("        var j=0;\n");
echo ("        var cod_itens=document.getElementsByName('cod_itens_import[]');\n");
echo ("        var Cabecalho = document.getElementById('select_all');\n");
echo ("        for (i=0; i < cod_itens.length; i++){\n");
echo ("          if (cod_itens[i].checked){\n");
echo ("            j++;\n");
echo ("          }\n");
echo ("        }\n");
echo ("        if ((j)==(cod_itens.length)) Cabecalho.checked=true;\n");
echo ("        else Cabecalho.checked=false;\n");
echo ("        if((j)>0){\n");
echo ("          document.getElementById('Importar_Selec').className=\"menuUp02\";\n");
echo ("          document.getElementById('Importar_Selec').onclick=function(){ ImportarSelecionadas(); };\n");
echo ("           return true;\n");
echo ("        }else{\n");
echo ("          document.getElementById('Importar_Selec').className=\"menuUp\";\n");
echo ("          document.getElementById('Importar_Selec').onclick=function(){  };\n");
echo ("          return false;\n");
echo ("        }\n");
echo ("      }\n\n");

echo ("      function Importar()\n");
echo ("      {\n");
echo ("        if(Validacheck())\n");
echo ("        {\n");
echo ("          document.frmImportar.action = 'importar_agenda2.php?cod_curso=" . $cod_curso . "&cod_usuario=" . $cod_usuario . "&cod_ferramenta=1';");
echo ("          document.frmImportar.submit();\n");
echo ("        }\n");
echo ("      }\n\n");

echo ("      function CancelarImportacao()\n");
echo ("      {\n");
echo ("        document.frmImportar.action = 'importar_curso.php?cod_curso=" . $cod_curso . "&cod_usuario=" . $cod_usuario . "&cod_ferramenta=1';\n");
echo ("        document.frmImportar.submit();\n");
echo ("      }\n\n");

echo ("      function Cancelar()\n");
echo ("      {\n");
echo ("        document.location = 'importar_curso.php?cod_curso=" . $cod_curso . "&cod_usuario=" . $cod_usuario . "&cod_ferramenta=1';\n");
echo ("      }\n\n");

echo ("      function CheckAll(){\n");
echo ("        var e;\n");
echo ("        var i;\n");
echo ("        var CabMarcado = document.getElementById('select_all').checked;\n");
echo ("        var cod_itens=document.getElementsByName('cod_itens_import[]');\n");
echo ("        for(i = 0; i < cod_itens.length; i++){\n");
echo ("          e = cod_itens[i];\n");
echo ("          e.checked = CabMarcado;\n");
echo ("        }\n");
echo ("        Validacheck();\n");
echo ("      }\n\n");

echo("      function ImportarSelecionadas(){");
echo("        document.frmImportar.action ='".$ctrl_agenda."acoes_linha.php';\n");
echo("        document.frmImportar.acao.value = \"importarItem\";\n");
echo("        document.frmImportar.submit();\n");
echo("      }");

echo ("     </script>\n\n");

require_once $view_administracao.'menu_principal.php';

$sock = AcessoSQL::MudarDB($sock, $cod_curso_origem);

echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
// Pagina Principal
// 1 - Agenda
$cabecalho = ("          <h4>" . _("msg1_1"));
/*66 - Importando Agenda */
$cabecalho .= (" - " . _("msg66_1") . "</h4>\n");
echo ($cabecalho);

// 3 A's - Muda o Tamanho da fonte
echo ("<div id=\"mudarFonte\">\n");
echo ("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"".$diretorio_imgs."btFont1.gif\"/></a>\n");
echo ("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"".$diretorio_imgs."btFont2.gif\"/></a>\n");
echo ("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"".$diretorio_imgs."btFont3.gif\"/></a>\n");
echo ("          </div>\n");

/*Voltar*/
/* 509 - Voltar */
echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;"._("msg509_-1")."&nbsp;</span></li></ul>\n");

/* 1 - Agenda */
$cabecalho = "  <b class=\"titulo\">"._("msg1_1")."</b>";

echo ("          <form method=\"post\" name=\"frmImportar\">\n");
echo ("          <input type=\"hidden\" name=\"cod_curso\" value=\"" . $cod_curso . "\">\n");
echo ("          <input type=\"hidden\" name=\"cod_categoria\" value=NULL>\n");
echo ("          <input type=\"hidden\" name=\"cod_curso_import\" value=\"" . $cod_curso_origem . "\">\n");
echo ("          <input type=\"hidden\" name=\"cod_item\" value=''>\n");
echo ("          <input type=\"hidden\" name=\"curso_compartilhado\" value=\"" . $curso_compartilhado . "\">\n");
echo ("          <input type=\"hidden\" name=\"curso_extraido\" value=\"" . $curso_extraido . "\">\n");
echo ("          <input type=\"hidden\" name=\"tipo_curso\" value=\"" . $tipo_curso . "\">\n");
echo ("          <input type=\"hidden\" name=\"acao\" value=\"\">\n");
if ('E' == $tipo_curso) {
	echo ("          <input type=\"hidden\" name=\"data_inicio\" value='" . $data_inicio . "'>\n");
	echo ("          <input type=\"hidden\" name=\"data_fim\" value='" . $data_fim . "'>\n");
}

echo ("        <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo ("          <tr>\n");
echo ("            <td valign=\"top\">\n");
echo ("              <ul class=\"btAuxTabs\">\n");
/* 2 - Cancelar (geral) */
echo ("                  <li><span onClick=history.go(-1);>" . _("msg2_-1") . "</span></li>\n");
echo ("              </ul>\n");
echo ("            </td>\n");
echo ("          </tr>\n");
echo ("          <tr>\n");
echo ("            <td>\n");
echo ("              <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
echo ("                <tr class=\"head\">\n");
echo ("                  <td align=\"center\"><b><input type=\"checkbox\" name=\"select_all\" id=\"select_all\" onclick=\"CheckAll();\"></td>\n");
// 78 - Agendas do Curso:
echo ("                  <td align=\"left\"><b>" . _("msg78_1") . "&nbsp;" . $nome_curso_import . "</b></td>\n");
/* 7 - Data */
echo ("                  <td align=\"center\"><b>" . _("msg7_1") . "</b></td>\n");
echo ("                </tr>\n");

/*verificar status... confirmar.. e verificar c eh necessario cancelar a edicao!*/
$lista_itens = Agenda::RetornaAgendaCurso($sock);

/* Belissimo exemplo do legado do PHP, concebido como linguagem direcionada ao "templating"
 * de sites. Codigo do periodo jurassico a frente. Viva o CSS!
*/
if ((count($lista_itens) > 0) && ($lista_itens != "")) {
	foreach ($lista_itens as $cod => $linha_item) {
		/*verificar esses status*/
		if ($linha_item['status'] != 'E') /* nao esta em edicao!*/ {
			$data = Data::UnixTime2Data($linha_item['data']);
			if ($data_acesso < $linha_item['data']) {
				$marcaib = "<b>";
				$marcafb = "</b>";
				$marcatr = " bgcolor=#f0f0f0";
			} else {
				$marcaib = "";
				$marcafb = "";
				$marcatr = "";
			}

			$titulo = "<span id=\"tit_" . $linha_item['cod_item'] . "\" class=\"link\" onclick='ExibirItem(";
			$titulo .= $linha_item['cod_item'] . ");'>" . $linha_item['titulo'] . "</span>";

			$icone = "<img src=\"".$diretorio_imgs."arqp.gif\" border=\"0\" /> ";

			echo ("                <tr" . $marcatr . " class=\"altColor" . ($cod % 2) . "\">\n");
			echo ("                  <td width=\"1%\"><input type=\"checkbox\" name=\"cod_itens_import[]\" onclick=\"Validacheck();\" value=\"" . $linha_item['cod_item'] . "\"></td>\n");
			echo ("                  <td width=\"80%\" align=\"left\">" . $icone . $titulo . "</td>\n");
			echo ("                  <td align=\"center\">" . $marcaib . $data . $marcafb . "</td>\n");

			echo ("                </tr>\n");
		}
	} // de foreach
} else {
	echo ("                <tr " . $marcatr . ">\n");
	/* 4 - Nenhuma agenda adicionada ainda! */
	echo ("                  <td colspan=\"4\">" . _("msg4_1") . "</td>\n");
	echo ("                </tr>\n");
}
echo ("                </table>\n");
echo ("              </td>\n");
echo ("            </tr>\n");
if (count($lista_itens) > 0) {
	echo ("            <tr>\n");
	echo ("              <td valign=\"top\">\n");
	echo ("                <ul>\n");
	/* 88 - Importar Selecionados */
	echo ("                  <li id=\"Importar_Selec\" class=\"menuUp\"><span id=\"importar\">" . _("msg88_1") . "</span></li>\n");
	echo ("                </ul>\n");
	echo ("              </td>\n");
	echo ("            </tr>\n");
}

echo ("          </table>\n");
echo ("          </form>\n");
require_once $view_administracao.'tela2.php';
echo ("        </td>\n");
echo ("      </tr>\n");
echo ("    </table>\n");
echo ("  </body>\n");
echo ("</html>\n");

AcessoSQL::Desconectar($sock);
?>