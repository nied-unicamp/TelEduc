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

require_once $model_geral.'geral.inc';
require_once $model_agenda.'agenda.inc';

$sock=AcessoSQL::Conectar("");
$diretorio_arquivos=Agenda::RetornaDiretorio($sock,'Arquivos');
$diretorio_temp=Agenda::RetornaDiretorio($sock,'ArquivosWeb');
AcessoSQL::Desconectar($sock);

$cod_ferramenta=1;
$cod_ferramenta_ajuda = $cod_ferramenta;
$cod_pagina_ajuda=1;
$cod_curso = $_GET['cod_curso'];

require_once $view_administracao.'topo_tela.php';

// instanciar o objeto, passa a lista de frases por parametro
$feedbackObject =  new FeedbackObject($lista_frases);
//adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
$feedbackObject->addAction("criarAgenda", 0, 97);

AcessoSQL::Desconectar($sock);
$sock = AcessoSQL::Conectar($cod_curso);
/* Verifica se o usuario � formador. */
$usr_formador = Usuarios::EFormador($sock, $cod_curso, $cod_usuario);

/* Fun��es JavaScript */
echo("    <script type=\"text/javascript\" src=\"".$diretorio_jscss."dhtmllib.js\"></script>\n");
//echo("    <script type=\"text/javascript\" src=\"jscriptlib.js\"></script>\n");
echo("    <script type=\"text/javascript\">\n\n");

echo("      var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
echo("      var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");
echo("      var Xpos, Ypos;\n");
echo("      var js_cod_item, js_cod_topico;\n");
echo("      var js_nome_topico;\n");
echo("      var js_tipo_item;\n");
echo("      var js_comp = new Array();\n\n");

echo("      if (isNav)\n");
echo("      {\n");
echo("        document.captureEvents(Event.MOUSEMOVE);\n");
echo("      }\n");
echo("      document.onmousemove = TrataMouse;\n\n");

echo("      function TrataMouse(e)\n");
echo("      {\n");
echo("        Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
echo("        Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
echo("      }\n");

echo("      function getPageScrollY()\n");
echo("      {\n");
echo("        if (isNav)\n");
echo("          return(window.pageYOffset);\n");
echo("        if (isIE){\n");
echo("          if(document.documentElement.scrollLeft>=0){\n");
echo("            return document.documentElement.scrollTop;\n");
echo("          }else if(document.body.scrollLeft>=0){\n");
echo("            return document.body.scrollTop;\n");
echo("          }else{\n");
echo("            return window.pageYOffset;\n");
echo("          }\n");
echo("        }\n");
echo("      }\n");

echo("      function AjustePosMenuIE()\n");
echo("      {\n");
echo("        if (isIE)\n");
echo("          return(getPageScrollY());\n");
echo("        else\n");
echo("          return(0);\n");
echo("      }\n");

echo("      function Iniciar()\n");
echo("      {\n");

echo("        lay_nova_agenda = getLayer('layer_nova_agenda');\n");
$feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
echo("        startList();\n");
echo("      }\n\n");

echo("      function EscondeLayers()\n");
echo("      {\n");
echo("        hideLayer(lay_nova_agenda);\n");
echo("      }\n");

echo("      function MostraLayer(cod_layer, ajuste)\n");
echo("      {\n");
echo("        EscondeLayers();\n");
echo("        moveLayerTo(cod_layer,Xpos-ajuste,Ypos+AjustePosMenuIE());\n");
echo("        showLayer(cod_layer);\n");
echo("      }\n");
echo("      function EscondeLayer(cod_layer)\n");
echo("      {\n");
echo("        hideLayer(cod_layer);\n");
echo("      }\n");

echo("      function NovaAgenda()\n");
echo("      {\n");
echo("        MostraLayer(lay_nova_agenda, 0);\n");
echo("        document.form_nova_agenda.novo_titulo.value = '';\n");
echo("        document.getElementById(\"nome\").focus();\n");
echo("      }\n");

echo("      function VerificaNovoTitulo(textbox, aspas) {\n");
echo("        var texto=textbox.value;\n");
echo("        if (texto==''){\n");
echo("          // se nome for vazio, nao pode\n");
/* 15 - O titulo nao pode ser vazio. */
echo("          alert(\"".Linguas::RetornaFraseDaLista($lista_frases,15)."\");\n");
echo("          textbox.focus();\n");
echo("          return false;\n");
echo("        }\n");
echo("        // se nome tiver aspas, <, >, nao pode - aspas pode ser 1,0\n");
echo("        else if ((texto.indexOf(\"\\\\\")>=0 || texto.indexOf(\"\\\"\")>=0 || texto.indexOf(\"'\")>=0 || texto.indexOf(\">\")>=0 || texto.indexOf(\"<\")>=0)&&(!aspas)) {\n");
/* 16 - O t�tulo n�o pode conter \\. */
echo("           alert(\"".ConversorTexto::ConverteAspas2BarraAspas(ConversorTexto::ConverteHtml2Aspas(Linguas::RetornaFraseDaLista($lista_frases,16)))."\");\n");
echo("          textbox.value='';\n");
echo("          textbox.focus();\n");
echo("          return false;\n");
echo("        }\n");
echo("        return true;\n");
echo("      }\n\n");
echo("    </script>\n\n");

require_once $view_administracao.'menu_principal.php';

echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
/* 1 - Agenda Atual*/
echo("          <h4>".Linguas::RetornaFraseDaLista($lista_frases, 1)." - ".Linguas::RetornaFraseDaLista($lista_frases, 23)."</h4>");

// 3 A's - Muda o Tamanho da fonte
echo("<div id=\"mudarFonte\">\n");
echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"".$diretorio_imgs."btFont1.gif\"/></a>\n");
echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"".$diretorio_imgs."btFont2.gif\"/></a>\n");
echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"".$diretorio_imgs."btFont3.gif\"/></a>\n");
echo("          </div>\n");

/*Voltar*/
/* 509 - Voltar */
echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".Linguas::RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

/* Tabela Externa */
echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("            <tr>\n");
echo("              <td valign=\"top\">\n");
echo("                <ul class=\"btAuxTabs\">\n");

if($usr_formador)
{
	/* 6 - Nova Agenda*/
	echo("                  <li><span OnClick='NovaAgenda();'>".Linguas::RetornaFraseDaLista($lista_frases, 6)."</span></li>");
	/* 3 - Editar Agenda*/
	echo("                  <li><a href=\"ver_editar.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."\">".Linguas::RetornaFraseDaLista($lista_frases, 3)."</a></li>\n");
}
/* 2- Agenda Anteriores*/
echo("                  <li><a href=\"ver_anteriores.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_usuario=".$cod_usuario."\">".Linguas::RetornaFraseDaLista($lista_frases, 2)."</a></li>\n");


echo("                </ul>\n");
echo("              </td>\n");
echo("            </tr>\n");
echo("            <tr>\n");
echo("              <td>\n");
/* Tabela Interna */
echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
echo("                  <tr class=\"head\">\n");
/*18 - Titulo */
echo("                    <td class=\"alLeft\">".Linguas::RetornaFraseDaLista($lista_frases,18)."</td>\n");
echo("                  </tr>\n");

/* Conteudo */

$linha_item=Agenda::RetornaAgendaAtiva($sock);

if (isset($linha_item['cod_item']))
{
	if($usr_formador)
		$titulo="<a id=\"tit_".$linha_item['cod_item']."\" href=\"".$view_agenda."ver_linha_agenda.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$linha_item['cod_item']."&amp;origem=agenda\">".$linha_item['titulo']."</a>";
	else
		$titulo=$linha_item['titulo'];

	$icone="<img src=\"".$diretorio_imgs."arqp.gif\" alt=\"\" border=\"0\" /> ";
	
	if($linha_item['texto']!="")
		$conteudo = $linha_item['texto'];
	else
	{
		$arquivo_entrada="";
		$dir_name = "agenda";
		$dir_item_temp = Agenda::CriaLinkVisualizar($sock,$dir_name, $cod_curso, $cod_usuario, $linha_item['cod_item'], $diretorio_arquivos, $diretorio_temp);
		$lista_arq=Agenda::RetornaArquivosAgendaVer($cod_curso, $dir_item_temp['diretorio']);
		
		if (count($lista_arq)>0)
		{
			foreach($lista_arq as $cod => $linha1)
			{
				if ($linha1['Status'] && $linha1['Arquivo']!="")
				{
					if(preg_match('/\.php(\.)*/', $linha1['Arquivo'])){  //arquivos php.txt
						$arquivo_entrada = "agenda_entrada.php?cod_curso=".$cod_curso."&entrada=".ConverteUrl2Html($linha1['Arquivo']."&diretorio=".$dir_item_temp['link']);
					}else{
						$arquivo_entrada = ConversorTexto::ConverteUrl2Html($dir_item_temp['link'].$linha1['Diretorio']."/".$linha1['Arquivo']);
					}
					break;
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
		echo("                    <td class=\"alLeft\">".Linguas::RetornaFraseDaLista($lista_frases,94)."</td>\n");
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
	echo("                    <td colspan=\"5\">".Linguas::RetornaFraseDaLista($lista_frases,4)."</td>\n");
	echo("                  </tr>\n");
}

/*Fim tabela interna*/
echo("                </table>\n");

/*Fim tabela externa*/
echo("              </td>\n");
echo("            </tr>\n");
echo("          </table>\n");
include $view_administracao."tela2.php";
//require_once $diretorio_views.'tela2.php';

/* Cria a funcao JavaScript que testa o nome da nova agenda e o layer  */
/* nova_agenda, se estiver visualizando as agendas disponieis.         */

/* Novo Item */
echo("    <div id=\"layer_nova_agenda\" class=\"popup\">\n");
echo("     <div class=\"posX\"><span onclick=\"EscondeLayer(lay_nova_agenda);\"><img src=\"".$diretorio_imgs."btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
echo("      <div class=\"int_popup\">\n");
echo("        <form name=\"form_nova_agenda\" method=\"post\" action=\"../controllers/acoes_linha.php?cod_curso=".$cod_curso."&acao=criarAgenda\" onSubmit=\"return(VerificaNovoTitulo(document.form_nova_agenda.novo_titulo, 1));\">\n");
//echo("        ".RetornaSessionIDInput());
echo("          <div class=\"ulPopup\">\n");
/* 18 - Titulo: */
echo("            ".Linguas::RetornaFraseDaLista($lista_frases,18)."<br />\n");
echo("            <input class=\"input\" type=\"text\" name=\"novo_titulo\" id=\"nome\" value=\"\" maxlength=\"150\" /><br />\n");
echo("            <input type=\"hidden\" name=\"cod_curso\"   value=\"".$cod_curso."\" />\n");
echo("            <input type=\"hidden\" name=\"acao\"        value=\"criarAgenda\" />\n");
echo("            <input type=\"hidden\" name=\"cod_usuario\" value=\"".$cod_usuario."\" />\n");
echo("            <input type=\"hidden\" name=\"origem\"      value=\"ver_editar\" />\n");
/* 18 - Ok (gen) */

echo("            <input type=\"submit\" id=\"ok_novoitem\" class=\"input\" value=\"".Linguas::RetornaFraseDaLista($lista_frases_geral,18)."\" />\n");

/* 2 - Cancelar (gen) */
echo("            &nbsp; &nbsp; <input type=\"button\" class=\"input\"  onClick=\"EscondeLayer(lay_nova_agenda);\" value=\"".Linguas::RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");
echo("         </div>\n");
echo("        </form>\n");
echo("      </div>\n");
echo("    </div>\n\n");

echo("  </body>\n");
echo("</html>\n");
AcessoSQL::Desconectar($sock);

?>