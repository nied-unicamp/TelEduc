<?php

$dir_static = '../../../static_includes/';
$ctrl_agenda = '../controller/';
$dir_img = '../../../img/';
$dir_lib = '../../../lib/';

include $ctrl_agenda.'AgendaController.php';
include $dir_lib.'FeedbackObject.inc.php';

$cod_ferramenta=1;

include $dir_static.'topo_tela.php';

$cod_curso = $_GET['cod_curso'];

$controlerAgenda = new AgendaController();
$controlerPermissao = new PermissaoController();

$feedbackObject =  new FeedbackObject();
//adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
$feedbackObject->addAction("apagarSelecionados", "Item(s) apagado(s) com sucesso", 0);
$feedbackObject->addAction("apagarItem", "Item apagado com sucesso", 0);

//$data_acesso=PenultimoAcesso($sock,$cod_usuario,"");

echo("	<script type=\"text/javascript\">\n\n");

echo("	function Iniciar()\n");
echo("	{\n"); 
$feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
echo("		startList();\n");
echo("	}\n\n"); 

echo("	</script>\n");

echo("	<script type=\"text/javascript\" src=\"../../../js/agenda.js\"></script>\n");
echo("	<script type=\"text/javascript\" src=\"../../../js/dhtmllib.js\"></script>\n");
echo("	<script type=\"text/javascript\" src=\"../../../js/jscript.js\"></script>\n");

include $dir_static.'menu_principal.php';

echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
/* 1 - Agenda */
echo("          <h4>Agenda");
echo(" - Agendas Anteriores");
echo("</h4>\n");

// 3 A's - Muda o Tamanho da fonte
include $dir_static.'3as.php';

/*Voltar*/
/* 509 - Voltar */
echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;Voltar&nbsp;</span></li></ul>\n");

/* Tabela Externa */
echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("            <tr>\n");
echo("              <td valign=\"top\">\n");
echo("                <ul class=\"btAuxTabs\">\n");
/*8 - Voltar para Agenda Atual*/
echo("                      <li><a href=\"agenda.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=1'\">Voltar para a Agenda Atual</a></li>\n");
echo("                </ul>\n");
echo("              </td>\n");
echo("            </tr>\n");
echo("            <tr>\n");
echo("              <td>\n");
/* Tabela Interna */
echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
echo("                  <tr class=\"head\">\n");
if ($controlerPermissao->hasPermission($cod_usuario, $cod_ferramenta, 'Editar'))
	echo("                    <td width=\"2\"><input type=\"checkbox\" id=\"checkMenu\" onClick=\"CheckTodos();\" /></td>\n");
/*1 - Agenda */
echo("                    <td class=\"alLeft\">Agenda</td>\n");
/*7 - Data */
echo("                    <td align=\"center\" width=\"15%\">Data</td>\n");
echo("                  </tr>\n");
/* Conteudo */

$lista_agendas = $controlerAgenda->listaAgendasSituacao($cod_curso, 'N');

if ((count($lista_agendas)>0)&&($lista_agendas != null))
{
	foreach ($lista_agendas as $cod => $linha_item)
	{
		$dataC = new Data();
		$data = $dataC->UnixTime2Data($linha_item['data_criacao']);
		/*if ($data_acesso<$linha_item['data'])
		{
			$marcaib="<b>";
			$marcafb="</b>";
		}
		else
		{ */
			$marcaib="";
			$marcafb="";
		//}
		if ($linha_item['status']=="E")
		{
			$data="<span class=\"link\" onclick=\"window.open('em_edicao.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$linha_item['cod_item']."&amp;origem=ver_editar','EmEdicao','width=600,height=280,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\">Em edicao</span>";
			$titulo=$linha_item['titulo'];
		}
		else
		{
			$titulo="<a id=\"tit_".$linha_item['cod_item']."\" href=\"ver_linha.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$linha_item['cod_item']."&amp;origem=ver_anteriores\">".$linha_item['titulo']."</a>";
		}

		$icone="<img src=\"".$dir_img."arqp.gif\" alt=\"\" border=\"0\" /> ";
		echo("                  <tr>\n");
		if ($controlerPermissao->hasPermission($cod_usuario, $cod_ferramenta, 'Editar'))
			echo("                    <td width=\"2\"><input type=\"checkbox\" name=\"chkItem\" id=\"itm_".$linha_item['cod_item']."\" onclick=\"VerificaCheck();\" value=\"".$linha_item['cod_item']."\" /></td>\n");
		echo("                    <td align=\"left\">".$icone.$titulo."</td>\n");
		echo("                    <td align=\"center\">".$marcaib.$data.$marcafb."</td>\n");
		echo("                  </tr>\n");
	}
}
else
{
	/* 90 - Nao ha agendas anteriores. */
	echo("                  <tr>\n");
	echo("                    <td colspan=\"5\">Nao ha agendas anteriores</td>\n");
	echo("                  </tr>\n");
}
/*Fim tabela interna*/
echo("                </table>\n");

if ($controlerPermissao->hasPermission($cod_usuario, $cod_ferramenta, 'Excluir Agenda')){
	/* 68 - Excluir Selecionados (ger)*/
	echo("                <ul>\n");
	echo("                  <li id=\"mExcluir_Selec\" class=\"menuUp\"><span id=\"excluirSelec\">Excluir Selecionados</span></li>\n");
	echo("                </ul>\n");
}

/*Fim tabela externa*/
echo("              </td>\n");
echo("            </tr>\n");
echo("          </table>\n");
include $dir_static.'tela2.php';

echo("    <form name=\"form_dados\" action=\"\" id=\"form_dados\">\n");
echo("      <input type=\"hidden\" name=\"cod_curso\" id=\"cod_curso\"      value=\"".$cod_curso."\" />\n");
echo("      <input type=\"hidden\" name=\"cod_item\"  id=\"cod_item\"       value=\"\" />\n");
echo("      <input type=\"hidden\" name=\"acao\"      id=\"acao_form\"      value=\"\" />\n");
echo("      <input type=\"hidden\" name=\"cod_itens\" id=\"cod_itens_form\" value=\"\" />\n");
echo("      <input type=\"hidden\" name=\"origem\"    value=\"ver_anteriores\" />\n");
echo("    </form>\n");

echo("  </body>\n");
echo("</html>\n");

?>