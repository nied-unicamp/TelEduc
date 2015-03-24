<?php
$ferramenta_geral = 'geral';
$ferramenta_administacao = 'administracao';
$ferramenta_agenda = 'agenda';

$diretorio_jscss = "../../../web-content/js-css/";
$diretorio_imgs  = "../../../web-content/imgs/";
$view_administracao = '../../'.$ferramenta_administacao.'/views/';
$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_agenda = '../../'.$ferramenta_agenda.'/models/';
$view_agenda = '../../'.$ferramenta_agenda.'/views/';
$ctrl_agenda = '../../'.$ferramenta_agenda.'/controllers/';

require_once $model_geral.'geral.inc';
require_once $model_agenda.'agenda.inc';

$cod_ferramenta=1;
$cod_ferramenta_ajuda = $cod_ferramenta;
$cod_pagina_ajuda=5;

$cod_usuario = $_GET['cod_usuario'];

$cod_curso = $_GET['cod_curso'];

$eformador = Usuarios::EFormador($sock, $cod_curso, $cod_usuario);

require_once $view_administracao.'topo_tela.php';

$feedbackObject =  new FeedbackObject($lista_frases);
//adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"

/* 101 - Agenda(s) apagada(s) com sucesso. */
$feedbackObject->addAction("apagarSelecionados", _("AGENDA_DELETED_SUCCESS_1"), 0);
$feedbackObject->addAction("apagarItem", _("AGENDA_DELETED_SUCCESS_1"), 0);


$data_acesso=Usuarios::PenultimoAcesso($sock,$cod_usuario,"");

/* Funcoes JavaScript */
echo("    <script type=\"text/javascript\" src=\"../js-css/dhtmllib.js\"></script>\n");
echo("    <script type=\"text/javascript\">\n\n");

echo("      function VerificaCheck(){\n");
echo("        var i;\n");
echo("        var j=0;\n");
echo("        var cod_itens=document.getElementsByName('chkItem');\n");
echo("        var Cabecalho = document.getElementById('checkMenu');\n");
echo("        array_itens = new Array();\n");
echo("        for (i=0; i<cod_itens.length; i++){\n");
echo("          if (cod_itens[i].checked){\n");
echo("            var item = cod_itens[i].id.split('_');\n");
echo("            array_itens[j]=item[1];\n");
echo("            j++;\n");
echo("          }\n");
echo("        }\n");
echo("        if ((j)==(cod_itens.length)) Cabecalho.checked=true;\n");
echo("        else Cabecalho.checked=false;\n");
echo("        if((j)>0){\n");
echo("          document.getElementById('mExcluir_Selec').className=\"menuUp02\";\n");
echo("          document.getElementById('mExcluir_Selec').onclick=function(){ ExcluirSelecionados(); };\n");
echo("        }else{\n");
echo("          document.getElementById('mExcluir_Selec').className=\"menuUp\";\n");
echo("          document.getElementById('mExcluir_Selec').onclick=function(){  };\n");
echo("        }\n");
echo("      }\n\n");

echo("      function CheckTodos(){\n");
echo("        var e;\n");
echo("        var i;\n");
echo("        var CabMarcado = document.getElementById('checkMenu').checked;\n");
echo("        var cod_itens=document.getElementsByName('chkItem');\n");
echo("        if (cod_itens.length == 0){\n");
echo("          return;\n");
echo("        }\n");
echo("        for(i = 0; i < cod_itens.length; i++){\n");
echo("          e = cod_itens[i];\n");
echo("          e.checked = CabMarcado;\n");
echo("        }\n");
echo("        VerificaCheck();\n");
echo("      }\n\n");

echo("      function ExcluirSelecionados(){\n");
echo("        if (TemCertezaApagar()){\n");
echo("          document.getElementById('cod_itens_form').value=array_itens;\n");
echo("          document.form_dados.action='".$ctrl_agenda."acoes_linha.php';\n");
echo("          document.form_dados.method='POST';\n");
echo("          document.getElementById('acao_form').value='apagarSelecionados';\n");
echo("          document.form_dados.submit();\n");
echo("        }\n");
echo("      }\n\n");

echo("      function TemCertezaApagar()\n");
echo("      {\n");
/* 29 - Voce tem certeza de que deseja apagar esta agenda? */
/* 30 - (nao havera como recupera-la) */
echo("              return(confirm(\""._("SURE_TO_DELETE_AGENDA_1")."\\n"._("PERMANENTLY_DELETED_1")."\"));\n");
echo("      }\n");

echo("      function Iniciar()\n");
echo("      {\n");
$feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
echo("        startList();\n");
echo("      }\n\n");

echo("    </script>\n\n");

require_once $view_administracao.'menu_principal.php';

echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
/* 1 - Agenda 
 * 2 - Agendas Anteriores
 * */
echo("          <h4>"._("AGENDA_1"));
echo(" - "._("PAST_AGENDAS_1"));
echo("</h4>\n");

// 3 A's - Muda o Tamanho da fonte
echo("          <div id=\"mudarFonte\">\n");
echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"".$diretorio_imgs."btFont1.gif\"/></a>\n");
echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"".$diretorio_imgs."btFont2.gif\"/></a>\n");
echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"".$diretorio_imgs."btFont3.gif\"/></a>\n");
echo("          </div>\n");

/*Voltar*/
/* 509 - Voltar */
echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;"._("BACK_-1")."&nbsp;</span></li></ul>\n");

/* Tabela Externa */
echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("            <tr>\n");
echo("              <td valign=\"top\">\n");
echo("                <ul class=\"btAuxTabs\">\n");
/*8 - Ver Agenda Atual*/
echo("                      <li><a href=\"agenda.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=1'\">"._("VIEW_ACTIVE_AGENDA_1")."</a></li>\n");
echo("                </ul>\n");
echo("              </td>\n");
echo("            </tr>\n");
echo("            <tr>\n");
echo("              <td>\n");
/* Tabela Interna */
echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
echo("                  <tr class=\"head\">\n");

if ($eformador){
	echo("                    <td width=\"2\"><input type=\"checkbox\" id=\"checkMenu\" onClick=\"CheckTodos();\" /></td>\n");
}
/*1 - Agenda */
echo("                    <td class=\"alLeft\">"._("AGENDA_1")."</td>\n");
/*7 - Data */
echo("                    <td align=\"center\" width=\"15%\">"._("DATE_-1")."</td>\n");
echo("                  </tr>\n");
/* Conteudo */
 
$lista_agendas=Agenda::RetornaItensHistorico($sock);

if ((count($lista_agendas)>0)&&($lista_agendas != null))
{
	foreach ($lista_agendas as $cod => $linha_item)
	{
		$data=Data::UnixTime2Data($linha_item['data']);
		if ($data_acesso<$linha_item['data'])
		{
			$marcaib="<b>";
			$marcafb="</b>";
		}
		else
		{
			$marcaib="";
			$marcafb="";
		}
		if ($linha_item['status']=="E")
		{
			/* 43 - Em Edição */
			$data="<span class=\"link\" onclick=\"window.open('".$view_agenda."em_edicao.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$linha_item['cod_item']."&amp;origem=ver_editar','EmEdicao','width=600,height=280,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\">"._("IN_EDITION_-1")."</span>";
			$titulo=$linha_item['titulo'];
		}
		else
		{
			$titulo="<a id=\"tit_".$linha_item['cod_item']."\" href=\"ver_linha_agenda.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$linha_item['cod_item']."&amp;origem=ver_anteriores\">".$linha_item['titulo']."</a>";
		}

		$icone="<img src=\"".$diretorio_imgs."arqp.gif\" alt=\"\" border=\"0\" /> ";
		echo("                  <tr>\n");
		if ($eformador){
			echo("                    <td width=\"2\"><input type=\"checkbox\" name=\"chkItem\" id=\"itm_".$linha_item['cod_item']."\" onclick=\"VerificaCheck();\" value=\"".$linha_item['cod_item']."\" /></td>\n");
		}
		echo("                    <td align=\"left\">".$icone.$titulo."</td>\n");
		echo("                    <td align=\"center\">".$marcaib.$data.$marcafb."</td>\n");
		echo("                  </tr>\n");
	}
}
else
{
	/* 90 - Nao ha agendas anteriores. */
	echo("                  <tr>\n");
	echo("                    <td colspan=\"5\">"._("NO_PAST_AGENDAS_1")."</td>\n");
	echo("                  </tr>\n");
}
/*Fim tabela interna*/
echo("                </table>\n");

/* 68 - Excluir Selecionados (ger)*/
if ($eformador){
	echo("                <ul>\n");
	echo("                  <li id=\"mExcluir_Selec\" class=\"menuUp\"><span id=\"excluirSelec\">"._("DELETE_SELECTED_-1")."</span></li>\n");
	echo("                </ul>\n");
}

/*Fim tabela externa*/
echo("              </td>\n");
echo("            </tr>\n");
echo("          </table>\n");

include $view_administracao."tela2.php";

echo("    <form name=\"form_dados\" action=\"\" id=\"form_dados\">\n");
echo("      <input type=\"hidden\" name=\"cod_curso\" id=\"cod_curso\"      value=\"".$cod_curso."\" />\n");
echo("      <input type=\"hidden\" name=\"cod_item\"  id=\"cod_item\"       value=\"\" />\n");
echo("      <input type=\"hidden\" name=\"acao\"      id=\"acao_form\"      value=\"\" />\n");
echo("      <input type=\"hidden\" name=\"cod_itens\" id=\"cod_itens_form\" value=\"\" />\n");
echo("      <input type=\"hidden\" name=\"origem\"    value=\"ver_anteriores\"");
echo("    </form>\n");

echo("  </body>\n");
echo("</html>\n");
AcessoSQL::Desconectar($sock);

