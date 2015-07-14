<?php

$dir_static = '../../../static_includes/';
$dir_lib = '../../../lib/';
$ctrl_agenda = '../controller/';
$ctrl_geral = '../../../app/geral/controller/';

include $ctrl_agenda.'AgendaController.php';
include $dir_lib.'FeedbackObject.inc.php';

//Adciona o topo tela que contém referencias aos css
include $dir_static.'topo_tela.php';

// instanciar o objeto, passa a lista de frases por parametro
$feedbackObject =  new FeedbackObject();
//adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
$feedbackObject->addAction("criarAgenda", 0, 97);
$feedbackObject->addAction("ativaragenda", "Agenda publicada com sucesso", 97);


echo("	<script type=\"text/javascript\">");
echo("		if (isNav)\n");
echo("		{\n");
echo("			document.captureEvents(Event.MOUSEMOVE);\n");
echo("      }\n");
echo("      document.onmousemove = TrataMouse;\n\n");

echo("      function Iniciar()\n");
echo("      {\n");
echo("        lay_nova_agenda = getLayer('layer_nova_agenda');\n");
$feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
echo("        startList();\n");
echo("      }\n\n");
echo("	</script>");

echo("	<script type=\"text/javascript\" src=\"../../../js/dhtmllib.js\"></script>\n");
echo("	<script type=\"text/javascript\" src=\"../../../js/jscript.js\"></script>\n");
echo("	<script type=\"text/javascript\" src=\"../../../js/agenda.js\"></script>\n");


include $dir_static.'menu_principal.php';

$usr_formador = true;
$cod_curso = $_GET['cod_curso'];

$controlerAgenda = new AgendaController();
$controlerPermissao = new PermissaoController();

echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
/* 1 - Agenda Atual*/
echo("          <h4>Agenda - Agenda Atual</h4>");

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


if ($controlerPermissao->hasPermission($cod_usuario, $cod_ferramenta, 'Criar Agenda')){
	/* 6 - Nova Agenda*/
	echo("                  <li><span OnClick='NovaAgenda();'>Nova Agenda</span></li>");
}
if ($controlerPermissao->hasPermission($cod_usuario, $cod_ferramenta, 'Visualizar Agendas Futuras')){
	/* 3 - Agendas Futuras*/
	echo("                  <li><a href=\"ver_editar.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."\">Agendas Futuras</a></li>\n");
}
if ($controlerPermissao->hasPermission($cod_usuario, $cod_ferramenta, 'Visualizar Agendas Anteriores')){
	/* 2- Agenda Anteriores*/
	echo("                  <li><a href=\"ver_anteriores.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_usuario=".$cod_usuario."\">Agendas Anteriores</a></li>\n");
}

echo("                </ul>\n");
echo("              </td>\n");
echo("            </tr>\n");
echo("            <tr>\n");
echo("              <td>\n");
/* Tabela Interna */
echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
echo("                  <tr class=\"head\">\n");
/*18 - Titulo */
echo("                    <td class=\"alLeft\">Titulo</td>\n");
echo("                  </tr>\n");
/* Conteudo */

$linha_item=$controlerAgenda->listaAgendasSituacao($cod_curso, 'A');

if (isset($linha_item['cod_item']))
{
	if ($controlerPermissao->hasPermission($cod_usuario, $cod_ferramenta, 'Editar'))
		$titulo="<a id=\"tit_".$linha_item['cod_item']."\" href=\"ver_linha.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$linha_item['cod_item']."&amp;origem=agenda\">".$linha_item['titulo']."</a>";
	else
		$titulo=$linha_item['titulo'];

	$icone="<img src=\"".$dir_img."arqp.gif\" alt=\"\" border=\"0\" /> ";

	if($linha_item['texto']!="")
		$conteudo = $linha_item['texto'];
	else
	{
		$arquivo_entrada="";
		$dir_name = "agenda";
		//$dir_item_temp = CriaLinkVisualizar($sock,$dir_name, $cod_curso, $cod_usuario, $linha_item['cod_item'], $diretorio_arquivos, $diretorio_temp);
		//$lista_arq=RetornaArquivosAgendaVer($cod_curso, $dir_item_temp['diretorio']);
		$lista_arq = null;
		if (count($lista_arq)>0)
		{
			foreach($lista_arq as $cod => $linha1)
			{
				if ($linha1['Status'] && $linha1['Arquivo']!="")
				{
					//if(preg_match('/\.php(\.)*/', $linha1['Arquivo'])){  //arquivos php.txt
						$arquivo_entrada = "agenda_entrada.php?cod_curso=".$cod_curso."&entrada=".ConverteUrl2Html($linha1['Arquivo']."&diretorio=".$dir_item_temp['link']);
					/* }else{
						$arquivo_entrada = ConverteUrl2Html($dir_item_temp['link'].$linha1['Diretorio']."/".$linha1['Arquivo']);
					}
					break; */
				}
			}
		}
		if ($arquivo_entrada!="")
		{
			$conteudo = "<iframe id=\"text_".$linha_item['cod_item']."\" height=\"400px\" name=\"iframe_ArqEntrada\" src=\"".$arquivo_entrada."\" frameBorder=\"0\" scrolling=\"auto\" marginwidth=\"0\" marginheight=\"0\" frameborder=\"0\" vspace=\"0\" hspace=\"0\" style=\"overflow:visible; width:100%; display:visible\"></iframe>";
		}
		else
		{
			$conteudo = "";
		}
	}

	echo("                  <tr>\n");
	echo("                    <td align=left>".$icone.$titulo."</td>\n");
	echo("                  </tr>\n");
	if (!empty($conteudo))
	{
		echo("                  <tr class=\"head\">\n");
		/* 94 - Conteudo */
		echo("                    <td class=\"alLeft\">Conteudo</td>\n");
		echo("                  </tr>\n");
		echo("                  <tr>\n");
		echo("                    <td align=left>\n");
		if ($arquivo_entrada!=""){
			echo($conteudo);
		} else {
			echo("                      <div class=\"divRichText\">".$conteudo."</div>\n");
		}
		echo("                    </td>\n");
		echo("                  </tr>\n");
	}
}
else
{
	/* 4 - Nenhuma agenda adicionada ainda! */
	echo("                  <tr>\n");
	echo("                    <td colspan=5>Nenhuma agenda adicionada ainda</td>\n");
	echo("                  </tr>\n");
}

/*Fim tabela interna*/
echo("                </table>\n");

/*Fim tabela externa*/
echo("              </td>\n");
echo("            </tr>\n");
echo("          </table>\n");
include $dir_static.'tela2.php';

/* Cria a funcao JavaScript que testa o nome da nova agenda e o layer  */
/* nova_agenda, se estiver visualizando as agendas disponieis.         */

/* Novo Item */
echo("    <div id=\"layer_nova_agenda\" class=\"popup\">\n");
echo("     <div class=\"posX\"><span onclick=\"EscondeLayer(lay_nova_agenda);\"><img src=\"../../../img/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
echo("      <div class=\"int_popup\">\n");
echo("        <form name=\"form_nova_agenda\" method=\"post\" action=\"acoes_linha.php\" onSubmit=\"return(VerificaNovoTitulo(document.form_nova_agenda.novo_titulo, 1));\">\n");
//echo("        ".RetornaSessionIDInput());
echo("          <div class=\"ulPopup\">\n");
/* 18 - Titulo: */
echo("            Titulo: <br />\n");
echo("            <input class=\"input\" type=\"text\" name=\"novo_titulo\" id=\"nome\" value=\"\" maxlength=\"150\" /><br />\n");
echo("            <input type=\"hidden\" name=\"cod_curso\"   value=\"".$cod_curso."\" />\n");
echo("            <input type=\"hidden\" name=\"acao\"        value=\"criarAgenda\" />\n");
echo("            <input type=\"hidden\" name=\"cod_usuario\" value=\"".$cod_usuario."\" />\n");
echo("            <input type=\"hidden\" name=\"origem\"      value=\"ver_editar\" />\n");
/* 18 - Ok (gen) */

echo("            <input type=\"submit\" id=\"ok_novoitem\" class=\"input\" value=\"Ok\"/>\n");

/* 2 - Cancelar (gen) */
echo("            &nbsp; &nbsp; <input type=\"button\" class=\"input\"  onClick=\"EscondeLayer(lay_nova_agenda);\" value=\"Cancelar\" />\n");
echo("         </div>\n");
echo("        </form>\n");
echo("      </div>\n");
echo("    </div>\n\n");
echo("  </body>\n");
echo("</html>\n");
//Desconectar($sock);

?>