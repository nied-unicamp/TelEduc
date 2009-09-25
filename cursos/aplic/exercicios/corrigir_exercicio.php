<?php
/*
 <!--
 -------------------------------------------------------------------------------

 Arquivo : cursos/aplic/exercicios/resolver.php

 TelEduc - Ambiente de Ensino-Aprendizagem a Dist�cia
 Copyright (C) 2001  NIED - Unicamp

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License version 2 as
 published by the Free Software Foundation.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

 You could contact us through the following addresses:

 Nied - Ncleo de Inform�ica Aplicada �Educa�o
 Unicamp - Universidade Estadual de Campinas
 Cidade Universit�ia "Zeferino Vaz"
 Bloco V da Reitoria - 2o. Piso
 CEP:13083-970 Campinas - SP - Brasil

 http://www.nied.unicamp.br
 nied@unicamp.br

 ------------------------------------------------------------------------------
 -->
 */

/*==========================================================
 ARQUIVO : cursos/aplic/exercicios/resolver.php
 ========================================================== */

$bibliotecas="../bibliotecas/";
include($bibliotecas."geral.inc");
include("exercicios.inc");

require_once("../xajax_0.2.4/xajax.inc.php");

//Estancia o objeto XAJAX
$objAjax = new xajax();
//Registre os nomes das funcoes em PHP que voce quer chamar atraves do xajax
$objAjax->registerFunction("MudarCompartilhamentoDinamic");
$objAjax->registerFunction("AtualizaRespostaDoUsuarioDinamic");
$objAjax->registerFunction("EditarRespostaQuestaoDissDinamic");
$objAjax->registerFunction("EditarComentarioQuestaoDissDinamic");
$objAjax->registerFunction("AtualizaNotaDinamicDiss");
$objAjax->registerFunction("VerificaEntregaDinamic");

//Manda o xajax executar os pedidos acima.
$objAjax->processRequests();

// Descobre os diretorios de arquivo, para os portfolios com anexo
$sock = Conectar("");
$diretorio_arquivos = RetornaDiretorio($sock, 'Arquivos');
$diretorio_temp = RetornaDiretorio($sock, 'ArquivosWeb');
Desconectar($sock);

$cod_ferramenta = 23;
$cod_resolucao = $_GET['cod_resolucao'];

include("../topo_tela.php");

$resolucao = RetornaResolucao($sock,$cod_resolucao);
$exercicio = RetornaExercicio($sock,$resolucao['cod_exercicio']);
$questoes = RetornaQuestoesExercicio($sock,$resolucao['cod_exercicio']);
$aplicado = RetornaDadosExercicioAplicado($sock,$resolucao['cod_exercicio']);

/*********************************************************/
/* in�io - JavaScript */

echo("  <script  type=\"text/javascript\" language=\"JavaScript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
echo("  <script type=\"text/javascript\" language=\"JavaScript\" src=\"../bibliotecas/rte/html2xhtml.js\"></script>\n");
echo("  <script type=\"text/javascript\" language=\"JavaScript\" src=\"../bibliotecas/rte/richtext.js\"></script>\n");
echo("  <script type=\"text/javascript\" language=\"JavaScript\">\n");
//Usage: initRTE(imagesPath, includesPath, cssFile, genXHTML)
echo("      initRTE(\"../bibliotecas/rte/images/\", \"../bibliotecas/rte/\", \"../bibliotecas/rte/\", true);\n");
echo("  </script>\n");

echo("  <script  type=\"text/javascript\" language=\"JavaScript\">\n\n");

echo("    var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
echo("    var isMinNS6 = ((navigator.userAgent.indexOf(\"Gecko\") != -1) && (isNav));\n");
echo("    var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");
echo("    var Xpos, Ypos;\n");
echo("    var js_cod_item;\n");
echo("    var js_comp = new Array();\n");
echo("    var cod_comp;");
echo("    var editaTexto = 0;\n");
echo("    var cancelarElemento = null;\n");
echo("    var cancelarTodos = 0;\n\n");

echo("    if (isNav)\n");
echo("    {\n");
echo("      document.captureEvents(Event.MOUSEMOVE);\n");
echo("    }\n\n");

echo("    document.onmousemove = TrataMouse;\n\n");

echo("    function TrataMouse(e)\n");
echo("    {\n");
echo("      Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
echo("      Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
echo("    }\n\n");

echo("    function getPageScrollY()\n");
echo("    {\n");
echo("      if (isNav)\n");
echo("        return(window.pageYOffset);\n");
echo("      if (isIE)\n");
echo("        return(document.body.scrollTop);\n");
echo("    }\n\n");

echo("    function AjustePosMenuIE()\n");
echo("    {\n");
echo("      if (isIE)\n");
echo("        return(getPageScrollY());\n");
echo("      else\n");
echo("        return(0);\n");
echo("    }\n\n");

/* Iniciliza os layers. */
echo("    function Iniciar()\n");
echo("    {\n");
if ($tela_formador){
	echo("      cod_comp = getLayer(\"comp\");\n");
	echo("      startList();\n");
//	echo("		document.getElementById('editanota').style.visibility = \"hidden\";\n");
//	echo("		document.getElementById('editanota').style.display = \"none\";\n");
}
echo("    }\n\n");

echo("    function WindowOpenVer(id)\n");
echo("    {\n");
echo("      window.open(\"" . $dir_questao_temp['link'] . "\"+id,'Portfolio','top=50,left=100,width=600,height=400,menubar=yes,status=yes,toolbar=yes,scrollbars=yes,resizable=yes');\n");
echo("    }\n\n");

echo("    function OpenWindowPerfil(id)\n");
echo("    {\n");
echo("      window.open(\"../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]=\"+id,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
echo("      return(false);\n");
echo("    }\n");

if ($tela_formador){
	echo("    function EscondeLayers()\n");
	echo("    {\n");
	echo("      hideLayer(cod_comp);\n");
	echo("    }\n");

	echo("    function MostraLayer(cod_layer, ajuste)\n");
	echo("    {\n");
	echo("      EscondeLayers();\n");
	echo("      moveLayerTo(cod_layer,Xpos-ajuste,Ypos+AjustePosMenuIE());\n");
	echo("      showLayer(cod_layer);\n");
	echo("    }\n");

	echo("    function EscondeLayer(cod_layer)\n");
	echo("    {\n");
	echo("      hideLayer(cod_layer);\n");
	echo("    }\n");

	echo("      function AtualizaComp(js_tipo_comp)\n");
	echo("      {\n");
	echo("        if ((isNav) && (!isMinNS6)) {\n");
	echo("          document.comp.document.form_comp.tipo_comp.value=js_tipo_comp;\n");
	echo("          document.comp.document.form_comp.cod_item.value=js_cod_item;\n");
	echo("          var tipo_comp = new Array(document.comp.document.getElementById('tipo_comp_T'),document.comp.document.getElementById('tipo_comp_F'), document.comp.document.getElementById('tipo_comp_N'));\n");
	echo("        } else {\n");
	echo("            document.form_comp.tipo_comp.value=js_tipo_comp;\n");
	echo("            document.form_comp.cod_item.value=js_cod_item;\n");
	echo("            var tipo_comp = new Array(document.getElementById('tipo_comp_T'),document.getElementById('tipo_comp_F'), document.getElementById('tipo_comp_N'));\n");
	echo("        }\n");
	echo("        var imagem=\"<img src='../imgs/checkmark_blue.gif' />\"\n");
	echo("        if (js_tipo_comp=='T') {\n");
	echo("          tipo_comp[0].innerHTML=imagem;\n");
	echo("          tipo_comp[1].innerHTML=\"&nbsp;\";\n");
	echo("          tipo_comp[2].innerHTML=\"&nbsp;\";\n");
	echo("        }else if (js_tipo_comp=='F'){\n");
	echo("          tipo_comp[0].innerHTML=\"&nbsp;\";\n");
	echo("          tipo_comp[1].innerHTML=imagem;\n");
	echo("          tipo_comp[2].innerHTML=\"&nbsp;\";\n");
	echo("        }else{\n");
	echo("          tipo_comp[0].innerHTML=\"&nbsp;\";\n");
	echo("          tipo_comp[1].innerHTML=\"&nbsp;\";\n");
	echo("          tipo_comp[2].innerHTML=imagem;\n");
	echo("        }\n");
	echo("      }\n\n");
}
echo("    function AbreResposta(cod_questao)\n");
echo("    {\n");
echo("      document.getElementById(\"trResposta_\"+cod_questao).style.display = \"\";\n");
echo("    }\n");

echo("    function FechaResposta(cod_questao)\n");
echo("    {\n");
echo("      document.getElementById(\"trResposta_\"+cod_questao).style.display = \"none\";\n");
echo("    }\n");

echo("    function AlteraTexto(id){\n");
echo("      if (editaTexto==-1 || editaTexto != id){\n");
if ($tela_formador){
	echo("        CancelaTodos();\n");
}
//echo("        xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);\n");
echo("        conteudo = document.getElementById('text_'+id).innerHTML;\n");
echo("        writeRichTextOnJS('text_'+id+'_text', conteudo, 520, 200, true, false, id);\n");
echo("        startList();\n");
echo("        document.getElementById('text_'+id+'_text').focus();\n");
echo("        cancelarElemento=document.getElementById('CancelaEdita');\n");
echo("        editaTexto = id;\n");
echo("      }\n");
echo("    }\n\n");

echo("    function EdicaoTexto(codigo, id, valor){\n");
echo("      var cod;\n");
echo("      if (valor=='ok'){\n");
echo("        cod = codigo.split(\"_\");\n");
echo("        conteudo=document.getElementById(id+'_text').contentWindow.document.body.innerHTML;\n");
echo("        xajax_EditarComentarioQuestaoDissDinamic(".$cod_curso.",cod[0],cod[1],conteudo,".$cod_usuario.",\"Texto\");\n");
echo("      }\n");
echo("      else{\n");
// Cancela Edi�o
//echo("        if (!cancelarTodos)\n");
echo("      }\n");
echo("      document.getElementById(id).innerHTML=conteudo;\n");
echo("      document.getElementById('resp_'+codigo).style.display= '';\n");
echo("      editaTexto=-1;\n");
echo("      cancelarElemento=null;\n");
echo("    }\n\n");

echo("    function CancelaTodos(){\n");
echo("      EscondeLayers();\n");
echo("      cancelarTodos=1;\n");
echo("      if(cancelarElemento) {\n");
echo("        cancelarElemento.onclick();\n");
//echo("        xajax_AcabaEdicaoDinamic(cod_curso, cod_item, cod_usuario, 0);\n");
echo("      }\n");
echo("      cancelarTodos=0;\n");
echo("    }\n");

echo("    function Responder(id){\n");
echo("      document.getElementById(\"resp_\"+id).style.display=\"none\";\n");
echo("      AlteraTexto(id);\n");
echo("    }\n");

echo("	function EditarNota(nota,cod_questao){\n");
echo("		if(document.getElementById('editanota_'+cod_questao).style.display == 'none'){");
echo("			document.getElementById('editanota_'+cod_questao).style.visibility = \"visible\";\n");
echo("			document.getElementById('nota_'+cod_questao).style.visibility = \"hidden\";\n");
echo("			document.getElementById('editanota_'+cod_questao).style.display = \"block\";\n");
echo("			document.getElementById('nota_'+cod_questao).style.display = \"none\";\n");
echo("			document.getElementById('inputnota_'+cod_questao).value = nota;\n");
echo("			document.getElementById('inputnota_'+cod_questao).focus();\n");
echo("		}else{\n");
echo("			document.getElementById('editanota_'+cod_questao).style.visibility = \"hidden\";\n");
echo("			document.getElementById('nota_'+cod_questao).style.visibility = \"visible\";\n");
echo("			document.getElementById('editanota_'+cod_questao).style.display = \"none\";\n");
echo("			document.getElementById('nota_'+cod_questao).style.display = \"block\";\n");
echo("		}\n");
echo("	}\n");

echo("    function AtualizaNota(cod_questao, cod_curso, cod_resolucao, valor){\n");
echo("		  notainput = document.getElementById('inputnota_'+cod_questao).value;\n");
echo("        xajax_AtualizaNotaDinamicDiss(cod_questao, cod_curso, cod_resolucao,notainput, valor);\n");
echo("		  if(notainput <= valor) {");
echo("        	document.getElementById('nota_'+cod_questao).innerHTML = notainput;\n");
echo("        	document.getElementById('NotaDiss_'+cod_questao).innerHTML = notainput;\n");
echo("		  }");
echo("  	  document.getElementById('editanota_'+cod_questao).style.visibility = \"hidden\";\n");
echo("		  document.getElementById('nota_'+cod_questao).style.visibility = \"visible\";\n");
echo("		  document.getElementById('editanota_'+cod_questao).style.display = \"none\";\n");
echo("		  document.getElementById('nota_'+cod_questao).style.display = \"block\";\n");
echo("    }\n");

echo("	function VerificaEntrega(cod_curso,cod_resolucao,flag){ \n");

echo("  if(flag) {\n");
echo("  if(confirm('certeza?'))");
echo("     window.location.href = \"acoes.php?cod_resolucao=\" +cod_resolucao+ \"&cod_curso=\" +cod_curso+ \"&acao=entregarCorrecao\"\n");
echo("	}\n");
echo("  else\n");
echo("     mostraFeedback('erro', false)\n");
echo("	}\n");


echo("  </script>\n\n");
/* fim - JavaScript */
/*********************************************************/

$objAjax->printJavascript("../xajax_0.2.4/");


include("../menu_principal.php");

echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");


$frase = "Exercicios - Corrigir exercicio";
echo("          <h4>".$frase."</h4>\n");

if($resolucao['cod_grupo'] != null)
{
	$nome=NomeGrupo($sock,$resolucao['cod_grupo']);
	//Figura de Grupo
	$fig_exercicio = "<img alt=\"\" src=\"../imgs/icGrupo.gif\" border=\"0\" />";

	echo("          ".$fig_exercicio." <span class=\"link\" onclick=\"AbreJanelaComponentes(".$resolucao['cod_grupo'].");\">".$nome."</span>");
}
else
{
	$nome=NomeUsuario($sock,$resolucao['cod_usuario'],$cod_curso);

	// Selecionando qual a figura a ser exibida ao lado do nome
	$fig_exercicio = "<img alt=\"\" src=\"../imgs/icPerfil.gif\" border=\"0\" />";

	echo("          ".$fig_exercicio." <span class=\"link\" onclick=\"OpenWindowPerfil(".$resolucao['cod_usuario'].");\" > ".$nome."</span>");
}

/*Voltar*/
echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span>\n");

echo("          <div id=\"mudarFonte\">\n");
echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
echo("          </div>\n");

echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("            <tr>\n");
echo("              <td valign=\"top\">\n");
echo("                <ul class=\"btAuxTabs\">\n");
/* ? - Voltar */
echo("                  <li><a href='resolver.php?cod_curso=".$cod_curso."&cod_resolucao=".$cod_resolucao."'>Voltar</a></li>\n");
echo("                </ul>\n");
echo("              </td>\n");
echo("            </tr>\n");
echo("            <tr>\n");
echo("              <td valign=\"top\">\n");
echo("                <table border=0 width=\"100%\" cellspacing=0 id=\"tabelaInterna\" class=\"tabInterna\">\n");
echo("                  <tr class=\"head\">\n");
/* ? - Titulo */
echo("                    <td colspan=\"8\">".$exercicio['titulo']."</td>\n");
echo("                  </tr>\n");

/* ?? - Compartilhado com Formadores */
if($resolucao['compartilhada'] == "F")
$compartilhamento = "Compartilhado com Formadores";
/* ?? - Totalmente compartilhado */
else if($resolucao['compartilhada'] == "T")
$compartilhamento = "Totalmente compartilhado";
/* ?? - Nao compartilhado */
else
$compartilhamento = "Nao compartilhado";

if($cod_usuario == $resolucao['cod_usuario'] || RetornaCodGrupoUsuario($sock,$cod_usuario) == $resolucao['cod_grupo'])

$situacao = "";
if($resolucao['submetida'] == 'S')
$situacao .= "<span class=\"\">(e)</span>";
if($resolucao['corrigida'] == 'S')
$situacao .= "<span class=\"avaliada\">(a)</span>";

$texto = $exercicio['texto'];
echo("                  <tr>\n");
echo("                    <td colspan=\"8\" class=\"alLeft\">".$texto."</td>\n");
echo("                  </tr>\n");

$dir_exercicio_temp = CriaLinkVisualizar($sock, $cod_curso, $cod_usuario, $resolucao['cod_exercicio'], $diretorio_arquivos, $diretorio_temp,"exercicio");
$lista_arq = RetornaArquivosQuestao($cod_curso, $dir_exercicio_temp['link']);

if(count($lista_arq) > 0 || $lista_arq != null)
{
	echo("                  <tr class=\"head\">\n");
	/* ? - Arquivos */
	echo("                    <td colspan=\"6\">Arquivos</td>\n");
	echo("                  </tr>\n");
	echo("                  <tr>\n");
	echo("                    <td colspan=\"6\" class=\"alLeft\">\n");

	foreach ($lista_arq as $cod => $linha_arq)
	{
		$caminho_arquivo = $dir_exercicio_temp['link'] . ConverteUrl2Html($linha_arq['Diretorio'] . "/" . $linha_arq['Arquivo']);
		//converte o o caminho e o nome do arquivo que vêm do linux em UTF-8 para 
		//ISO-8859-1 para ser exibido corretamente na página.
		$caminho_arquivo = mb_convert_encoding($caminho_arquivo, "ISO-8859-1", "UTF-8");
			
		$linha_arq['Arquivo'] = mb_convert_encoding($linha_arq['Arquivo'], "ISO-8859-1", "UTF-8");
		if(eregi(".zip$", $linha_arq['Arquivo'])) {
			// arquivo zip
			$imagem = "<img alt=\"\" src=\"../imgs/arqzip.gif\" border=\"0\" />";
			$tag_abre = "<span class=\"link\" id=\"nomeArq_" . $conta_arq . "\" onclick=\"WindowOpenVer('" . $caminho_arquivo . "');\" tipoArq=\"zip\" nomeArq=\"" . htmlentities($caminho_arquivo) . "\" arqZip=\"" . $linha['Arquivo'] . "\">";
		}else{
			// arquivo comum
			// imagem
			if((eregi(".jpg$",$linha_arq['Arquivo'])) || eregi(".png$",$linha['Arquivo']) || eregi(".gif$",$linha['Arquivo']) || eregi(".jpeg$",$linha['Arquivo'])) {
				$imagem    = "<img alt=\"\" src=\"../imgs/arqimg.gif\" border=\"0\" />";
				//doc
			}else if(eregi(".doc$",$linha_arq['Arquivo'])){
				$imagem    = "<img alt=\"\" src=\"../imgs/arqdoc.gif\" \"border=\"0\" />";
				//pdf
			}else if(eregi(".pdf$",$linha_arq['Arquivo'])){
				$imagem    = "<img alt=\"\" src=\"../imgs/arqpdf.gif\" border=\"0\" />";
				//html
			}else if((eregi(".html$",$linha_arq['Arquivo'])) || (eregi(".htm$",$linha['Arquivo']))){
				$imagem    = "<img alt=\"\" src=\"../imgs/arqhtml.gif\" border=\"0\" />";
			}else if((eregi(".mp3$",$linha_arq['Arquivo'])) || (eregi(".mid$",$linha['Arquivo']))) {
				$imagem    = "<img alt=\"\" src=\"../imgs/arqsnd.gif\" border=\"0\" />";
			}else{
				$imagem    = "<img alt=\"\" src=\"../imgs/arqp.gif\" border=\"0\" />";
			}

			$tag_abre = "<span class=\"link\" id=\"nomeArq_" .$conta_arq ."\" onclick=\"WindowOpenVer('" . $caminho_arquivo . "');\" tipoArq=\"comum\" nomeArq=\"" . htmlentities($caminho_arquivo) . "\">";
		}

		$tag_fecha = "</span>";
		echo ("                      ".$imagem.$tag_abre.$linha_arq['Arquivo'].$tag_fecha."<br />");
	}
	echo("                    </td>\n");
	echo("                  </tr>\n");
}
echo("                  <tr class=\"head01\">\n");
/* ? - Titulo */
echo("                    <td class=\"alLeft\" colspan=\"5\">Titulo</td>\n");
/* ? - Nota */
echo("                    <td width=\"10%\">Nota do Aluno</td>\n");
/* ? - Valor */
echo("                    <td width=\"10%\">Valor da Questao</td>\n");
/* ? - Status */
echo("                    <td width=\"10%\">Status</td>\n");
echo("                  </tr>\n");

if ((count($questoes)>0)&&($questoes != null))
{
	foreach ($questoes as $cod => $linha_item)
	{
		$icone = "<img src=\"../imgs/arqp.gif\" alt=\"\" border=\"0\" /> ";
		$tipo = $linha_item['tp_questao'];
		$titulo = $linha_item['titulo'];
		$topico = RetornaNomeTopico($sock,$linha_item['cod_topico']);
		$valor = $linha_item['valor'];
		
		if($linha_item['tp_questao'] == 'O'){
			$alternativas = RetornaAlternativas($sock,$linha_item['cod_questao']);
			$status="corrigida";
			$notaObj=PegaNotaObjetiva($linha_item['cod_questao'], $cod_curso, $resolucao['cod_resolucao']);
		}
		else{
			$itens=VerificaQuestaoDissertativa($linha_item['cod_questao'], $cod_curso, $resolucao['cod_resolucao']);
			if($itens[0]==null){
				$status="nao corrigida";
				$notaDis="";
			}
			else{
				$status="corrigida";
				$notaDis=$itens[0];	
			}	
		}
		$comentario=PegaComentarioQuestao($cod_curso, $cod_resolucao,$linha_item['cod_questao'], $cod_usuario);	
		
		$resposta = RetornaRespostaQuestao($sock,$cod_resolucao,$linha_item['cod_questao'],$linha_item['tp_questao']);
		if(!$resposta)
			$notaDis = "0.00";
		$dir_questao_temp = CriaLinkVisualizar($sock, $cod_curso, $cod_usuario, $linha_item['cod_questao'], $diretorio_arquivos, $diretorio_temp, "questao");
		$lista_arq = RetornaArquivosQuestao($cod_curso, $dir_questao_temp['link']);

		echo("                  <tr id=\"trQuestao_".$linha_item['cod_questao']."\">\n");
		echo("                    <td align=left colspan=5>".$icone."<span class=\"link\" onclick=\"AbreResposta(".$linha_item['cod_questao'].");\">".$titulo."</span></td>\n");
		if($linha_item['tp_questao'] == 'O')
			echo("                    <td id=\"NotaObj_".$linha_item['cod_questao']."\">".$notaObj."</td>\n");
		else
			echo("                    <td id=\"NotaDiss_".$linha_item['cod_questao']."\">".$notaDis."</td>\n");
		echo("                    <td>".$linha_item['valor']."</td>\n");
		echo("                    <td>".$status."</td>\n");
		echo("                  </tr>\n");
		echo("                  <tr id=\"trResposta_".$linha_item['cod_questao']."\" style=\"display:none;\">\n");
		echo("                    <td style=\"width:50px\" colspan=\"7\" align=\"left\">\n");
		echo("                      <dl class=\"portlet\">\n");
		echo("                        <dt class=\"portletHeader\">Enunciado</dt>\n");
		echo("                          <dd class=\"portletItem\">".$linha_item['enunciado']."</dd>\n");

		if(count($lista_arq) > 0 || $lista_arq != null)
		{
			echo("                        <dt class=\"portletHeader\">Arquivos</dt>\n");
			echo("                          <dd class=\"portletItem\">\n");
			foreach ($lista_arq as $cod => $linha_arq)
			{
				$caminho_arquivo = $dir_questao_temp['link'] . ConverteUrl2Html($linha_arq['Diretorio'] . "/" . $linha_arq['Arquivo']);
				//converte o o caminho e o nome do arquivo que vêm do linux em UTF-8 para 
				//ISO-8859-1 para ser exibido corretamente na página.
				$caminho_arquivo = mb_convert_encoding($caminho_arquivo, "ISO-8859-1", "UTF-8");
				$linha_arq['Arquivo'] = mb_convert_encoding($linha_arq['Arquivo'], "ISO-8859-1", "UTF-8");
				if(eregi(".zip$", $linha_arq['Arquivo'])) {
			  // arquivo zip
			  $imagem = "<img alt=\"\" src=\"../imgs/arqzip.gif\" border=\"0\" />";
			  $tag_abre = "<span class=\"link\" id=\"nomeArq_" . $conta_arq . "\" onclick=\"WindowOpenVer('" . $caminho_arquivo . "');\" tipoArq=\"zip\" nomeArq=\"" . htmlentities($caminho_arquivo) . "\" arqZip=\"" . $linha['Arquivo'] . "\">";
				}else{
			  // arquivo comum
			  // imagem
			  if((eregi(".jpg$",$linha_arq['Arquivo'])) || eregi(".png$",$linha['Arquivo']) || eregi(".gif$",$linha['Arquivo']) || eregi(".jpeg$",$linha['Arquivo'])) {
			  	$imagem    = "<img alt=\"\" src=\"../imgs/arqimg.gif\" border=\"0\" />";
			  	//doc
			  }else if(eregi(".doc$",$linha_arq['Arquivo'])){
			  	$imagem    = "<img alt=\"\" src=\"../imgs/arqdoc.gif\" \"border=\"0\" />";
			  	//pdf
			  }else if(eregi(".pdf$",$linha_arq['Arquivo'])){
			  	$imagem    = "<img alt=\"\" src=\"../imgs/arqpdf.gif\" border=\"0\" />";
			  	//html
			  }else if((eregi(".html$",$linha_arq['Arquivo'])) || (eregi(".htm$",$linha['Arquivo']))){
			  	$imagem    = "<img alt=\"\" src=\"../imgs/arqhtml.gif\" border=\"0\" />";
			  }else if((eregi(".mp3$",$linha_arq['Arquivo'])) || (eregi(".mid$",$linha['Arquivo']))) {
			  	$imagem    = "<img alt=\"\" src=\"../imgs/arqsnd.gif\" border=\"0\" />";
			  }else{
			  	$imagem    = "<img alt=\"\" src=\"../imgs/arqp.gif\" border=\"0\" />";
			  }

			  $tag_abre = "<span class=\"link\" id=\"nomeArq_" .$conta_arq ."\" onclick=\"WindowOpenVer('" . $caminho_arquivo . "');\" tipoArq=\"comum\" nomeArq=\"" . htmlentities($caminho_arquivo) . "\">";
				}
				$tag_fecha = "</span>";
				echo ("                            ".$imagem.$tag_abre.$linha_arq['Arquivo'].$tag_fecha."<br />");
			}
			echo("                          </dd>\n");
		}

		if($linha_item['tp_questao'] == 'O')
		{
			/* Desabilita a radiobox, se ja foi entregue o ex. */
			$estado = "";
			if ($resolucao['submetida'] == 'S')
			$estado = "disabled";

			echo("                        <dt class=\"portletHeader\">Alternativas</dt>\n");
			echo("                          <dd class=\"portletItem\">\n");
			foreach ($alternativas as $cod => $linha_alt)
			{
				if($resposta != null && $resposta[$cod] == "1")
				$selected = "checked";
				else
				$selected = "";

				echo("                            <input  type=\"radio\" size=\"2\" name=\"resposta_".$linha_item['cod_questao']."\" ".$estado." onclick=\"SelecionaAlternativa(".$linha_item['cod_questao'].",".$cod.",".count($alternativas).");\" ".$selected.">&nbsp;&nbsp;&nbsp;".$linha_alt['texto']."\n");
				echo("                            <br />\n");
			}
			echo("                          </dd>\n");
		}
		else if($linha_item['tp_questao'] == 'D')
		{
			echo("                        <dt class=\"portletHeader\">Resposta</dt>\n");
			echo("                          <dd class=\"portletItem\">".$resposta."</dd>\n");
			echo("                        		<dt class=\"portletHeader\">Nota</dt>\n");
			echo("                          	  <div id=\"nota_".$linha_item['cod_questao']."\" class=\"portletItem\">".$notaDis."</div>\n");
  			echo("                          	  <div style=\"display:none;\" id=\"editanota_".$linha_item['cod_questao']."\" class=\"portletItem\"><input class=\"input\" id=\"inputnota_".$linha_item['cod_questao']."\" style=\"width:50px;\" type=\"text\" value=\"\"><span>  </span><span class=\"link\" onclick=\"AtualizaNota(".$linha_item['cod_questao'].",".$cod_curso.",".$resolucao['cod_resolucao'].", ".$linha_item['valor'].");\">Ok</span><span>  </span><span class=\"link\" onclick=\"EditarNota('".$notaDis."','".$linha_item['cod_questao']."');\">Cancel</span></div>\n");
			echo("                              <dt class=\"portletHeader\">Comentario do Avaliador</dt>\n");
			echo("                                <dd class=\"portletItem\">\n");
			echo("                                   <div class=\"divRichText\">\n");
			echo("                                     <span id=\"text_".$cod_resolucao."_".$linha_item['cod_questao']."\">");
			echo(										 $comentario);
			echo("                              	   </span>\n");
			echo("                            		 </div>\n");
			echo("                            	  </dd>\n");
					
			echo("                          	<dd class=\"portletFooter\"><span class=\"link\" onclick=\"EditarNota('".$notaDis."','".$linha_item['cod_questao']."');\">Editar nota</span></dd>\n");
			echo("                              <dd class=\"portletFooter\" id=\"resp_".$cod_resolucao."_".$linha_item['cod_questao']."\"><span class=\"link\" onclick=\"Responder('".$cod_resolucao."_".$linha_item['cod_questao']."');\">Editar comentario</span></dd>\n");
		}
		echo("                      </dl>\n");
		echo("                    </td>\n");
		echo("                    <td><span class=\"link\" onclick=\"FechaResposta(".$linha_item['cod_questao'].");\">Fechar</span></td>\n");
		echo("                  </tr>\n");

	}
}

echo("                </table>\n");
	/* ? - Entregar */				
//echo("								<form method='POST'  onsubmit='VerificaEntrega(".$cod_curso.", ".$cod_resolucao.",'')'>");
//echo("								<input type='hidden' name='acao' value='entregarCorrecao'/>");
//echo("								<input type='hidden' name='cod_resolucao' value='".$cod_resolucao."'/>");
//echo("								<input type='hidden' name='cod_curso' value='".$cod_curso."'/>");
echo("                <div align='right'><input type='button' onclick='xajax_VerificaEntregaDinamic(".$cod_curso.", ".$cod_resolucao.", ".$linha_item['cod_questao'].");'  class='input' value='Entregar Correcao'></div>\n");
//echo("								</form>");
echo("              </td>\n");
echo("            </tr>\n");
echo("          </table>\n");
echo("          <span class=\"btsNavBottom\"><a href=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></a> <a href=\"#topo\"><img src=\"../imgs/btTopo.gif\" border=\"0\" alt=\"Topo\" /></a></span>\n");



echo("        </td>\n");
echo("      </tr>\n");

include("../tela2.php");

if($tela_formador)
{
	/* Mudar Compartilhamento */
	echo("    <div class=popup id=\"comp\">\n");
	echo("      <div class=\"posX\"><span onclick=\"EscondeLayer(cod_comp);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
	echo("      <div class=int_popup>\n");
	echo("        <script type=\"text/javaScript\">\n");
	echo("        </script>\n");
	echo("        <form name=\"form_comp\" action=\"\" id=\"form_comp\">\n");
	echo("          <input type=hidden name=cod_curso value=\"".$cod_curso."\" />\n");
	echo("          <input type=hidden name=cod_usuario value=\"".$cod_usuario."\" />\n");
	echo("          <input type=hidden name=cod_item value=\"\" />\n");
	echo("          <input type=hidden name=tipo_comp id=tipo_comp value=\"\" />\n");
	echo("          <input type=hidden name=texto id=texto value=\"Texto\" />\n");
	echo("          <ul class=ulPopup>\n");
	echo("            <li onClick=\"document.getElementById('tipo_comp').value='T'; xajax_MudarCompartilhamentoDinamic(xajax.getFormValues('form_comp'), 'Totalmente Compartilhado', 'R'); EscondeLayers();\">\n");
	echo("              <span id=\"tipo_comp_T\" class=\"check\"></span>\n");
	/* ?? - Compartilhado com formadores */
	echo("              <span>Totalmente compartilhado</span>\n");
	echo("            </li>\n");
	echo("            <li onClick=\"document.getElementById('tipo_comp').value='F'; xajax_MudarCompartilhamentoDinamic(xajax.getFormValues('form_comp'), 'Compartilhado com formadores', 'R'); EscondeLayers();\">\n");
	echo("              <span id=\"tipo_comp_F\" class=\"check\"></span>\n");
	/* ?? - Compartilhado com formadores */
	echo("              <span>Compartilhado com formadores</span>\n");
	echo("            </li>\n");
	echo("            <li onClick=\"document.getElementById('tipo_comp').value='N'; xajax_MudarCompartilhamentoDinamic(xajax.getFormValues('form_comp'), 'Nao Compartilhado', 'R'); EscondeLayers();\">\n");
	echo("              <span id=\"tipo_comp_N\" class=\"check\"></span>\n");
	/* ?? - Nao Compartilhado */
	echo("              <span>Nao Compartilhado</span>\n");
	echo("            </li>\n");
	echo("          </ul>\n");
	echo("        </form>\n");
	echo("      </div>\n");
	echo("    </div>\n");
}

echo("  </body>\n");
echo("</html>\n");

Desconectar($sock);

?>