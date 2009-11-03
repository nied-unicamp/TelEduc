<?php
/*
 <!--
 -------------------------------------------------------------------------------

 Arquivo : cursos/aplic/exercicios/ver_exercicios.php

 TelEduc - Ambiente de Ensino-Aprendizagem a Distï¿½cia
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

 Nied - Ncleo de Informï¿½ica Aplicada ï¿½Educaï¿½o
 Unicamp - Universidade Estadual de Campinas
 Cidade Universitï¿½ia "Zeferino Vaz"
 Bloco V da Reitoria - 2o. Piso
 CEP:13083-970 Campinas - SP - Brasil

 http://www.nied.unicamp.br
 nied@unicamp.br

 ------------------------------------------------------------------------------
 -->
 */

/*==========================================================
 ARQUIVO : cursos/aplic/exercicios/ver_exercicios.php
 ========================================================== */

$bibliotecas="../bibliotecas/";
include($bibliotecas."geral.inc");
include("exercicios.inc");

require_once("../xajax_0.2.4/xajax.inc.php");

//Estancia o objeto XAJAX
$objAjax = new xajax();
//Registre os nomes das funcoes em PHP que voce quer chamar atraves do xajax
$objAjax->registerFunction("MudarCompartilhamentoDinamic");
//Manda o xajax executar os pedidos acima.
$objAjax->processRequests();

$cod_ferramenta = 23;
$visualizar = $_GET['visualizar'];

include("../topo_tela.php");

// instanciar o objeto, passa a lista de frases por parametro
$feedbackObject =  new FeedbackObject($lista_frases);

//adicionar as acoes possiveis, 1o parametro 
$feedbackObject->addAction("entregarExercicio", 504, 0);

 
if($visualizar != "I" && $visualizar != "G")
	$visualizar = "I";


$eformador = EFormador($sock,$cod_curso,$cod_usuario);
$convidado = EConvidado($sock, $cod_usuario, $cod_curso);

if($visualizar == "I"){
	
	$cod_usuario_exercicio = $_GET['cod'];
	AplicaExerciciosAoUsuario($sock,$cod_curso,$cod_usuario_exercicio);
	$exercicios = RetornaExerciciosUsuario($sock,$cod_usuario,$cod_curso,$eformador,$cod_usuario_exercicio);
	
} else if($visualizar == "G"){
	//$cod_grupo_exercicio = RetornaCodGrupoUsuario($sock,$cod_usuario);
	if ($cod_grupo_exercicio != NULL){
		AplicaExerciciosAoUsuario($sock,$cod_curso,$cod_usuario);
		$exercicios = RetornaExerciciosGrupo($sock,$cod_usuario,$cod_curso,$eformador,$_GET['cod']);
	}
}
$data_acesso=PenultimoAcesso($sock,$cod_usuario,"");
// verificamos se a ferramenta de Avaliacoes estÃ¡ disponivel
$ferramenta_avaliacao = TestaAcessoAFerramenta($sock, $cod_curso, $cod_usuario, 22);

/*********************************************************/
/* inï¿½io - JavaScript */
echo("  <script  type=\"text/javascript\" language=\"JavaScript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
echo("  <script  type=\"text/javascript\" src=\"jscriptlib.js\"> </script>\n");
echo("  <script  type=\"text/javascript\" language=\"JavaScript\">\n\n");

echo("    var js_cod_item;\n");
echo("    var js_comp = new Array();\n");
echo("    var cod_comp;");

/* Iniciliza os layers. */
echo("    function Iniciar()\n");
echo("    {\n");
echo("      cod_comp = getLayer(\"comp\");\n");
echo("      startList();\n");
$feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
echo("    }\n\n");

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

echo("      function AbreJanelaComponentes(id)\n");
echo("      {\n");
echo("         window.open(\"../grupos/exibir_grupo.php?cod_curso=".$cod_curso."&cod_grupo=\"+id,\"GruposDisplay\",\"width=700,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
echo("        return(false);\n");
echo("      }\n");

echo("      function OpenWindowPerfil(id)\n");
echo("      {\n");
echo("         window.open(\"../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]=\"+id,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
echo("        return(false);\n");
echo("      }\n");

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

echo("  </script>\n\n");
/* fim - JavaScript */
/*********************************************************/

$objAjax->printJavascript("../xajax_0.2.4/");

include("../menu_principal.php");

echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

if($visualizar == "I")
{
	/* Frase #1 - Exercicios */
	/* Frase #109 - Exercicios Individuais */
	$frase = RetornaFraseDaLista($lista_frases, 1)." - ".RetornaFraseDaLista($lista_frases, 109);
}
else if($visualizar == "G")
{
	/* Frase #1 - Exercicios */
	/* Frase #110 - Exercicios em Grupo */
	$frase = RetornaFraseDaLista($lista_frases, 1)." - ".RetornaFraseDaLista($lista_frases, 110);
}

echo("          <h4>".$frase."</h4>\n");

echo("          <div id=\"mudarFonte\">\n");
echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
echo("          </div>\n");
 
/* Frase #5 - Voltar */
echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"".RetornaFraseDaLista($lista_frases, 5)."\" /></span>\n");
 
if($visualizar=="G")
{
	if ($cod_grupo_exercicio != NULL){
		$nome=NomeGrupo($sock,$cod_grupo_exercicio);

		//Figura de Grupo
		$fig_exercicio = "<img alt=\"\" src=\"../imgs/icGrupo.gif\" border=\"0\" />";

		echo("          ".$fig_exercicio." <span class=\"link\" onclick=\"AbreJanelaComponentes(".$cod_grupo_exercicio.");\">".$nome."</span>");
	}
}
else
{
	$nome=NomeUsuario($sock,$cod_usuario_exercicio,$cod_curso);

	// Selecionando qual a figura a ser exibida ao lado do nome
	$fig_exercicio = "<img alt=\"\" src=\"../imgs/icPerfil.gif\" border=\"0\" />";

	echo("          ".$fig_exercicio." <span class=\"link\" onclick=\"OpenWindowPerfil(".$cod_usuario_exercicio.");\" > ".$nome."</span>");
}
 
echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("            <tr>\n");
echo("              <td valign=\"top\">\n");

echo("                <ul class=\"btAuxTabs\">\n");
 
if($eformador)
{
		/* Frase #109 - Exercicios Individuais */
    echo("                  <li><a href='exercicio.php?cod_curso=".$cod_curso."&visualizar=I&agrupar=A'>".RetornaFraseDaLista($lista_frases, 109)."</a></li>\n");
    
    /* Frase #110 - Exercicios em Grupo */
    echo("                  <li><a href='exercicio.php?cod_curso=".$cod_curso."&visualizar=G&agrupar=G'>".RetornaFraseDaLista($lista_frases, 110)."</a></li>\n");

  	/* Frase #111 - Biblioteca de Exercicios */
    echo("                  <li><a href='exercicios.php?cod_curso=".$cod_curso."&visualizar=E'>".RetornaFraseDaLista($lista_frases, 111)."</a></li>\n");
  	
    /* Frase #112 - Biblioteca de Questoes */
    echo("                  <li><a href='questoes.php?cod_curso=".$cod_curso."&visualizar=Q'>".RetornaFraseDaLista($lista_frases, 112)."</a></li>\n");
	
}

/* ? - Ver resoluo */
//echo("                  <li><a href='ver_gabarito.php?cod_curso=".$cod_curso."&visualizar=I&cod=".$cod_usuario_exercicio."''>Ver resoluo</a></li>\n");

echo("                </ul>\n");
echo("              </td>\n");
echo("            </tr>\n");
echo("            <tr>\n");
echo("              <td valign=\"top\">\n");
echo("                <table border=0 width=\"100%\" cellspacing=0 id=\"tabelaInterna\" class=\"tabInterna\">\n");
echo("                  <tr class=\"head\">\n");
/* Frase #1 - Exercicios */
echo("                    <td class=\"alLeft\">".RetornaFraseDaLista($lista_frases, 1)."</td>\n");
/* Frase #86 - Limite de entrega */
echo("                    <td width=\"10%\" style=\"cursor:pointer\">".RetornaFraseDaLista($lista_frases, 86)."</td>\n");
/* Frase #57 - Compartilhamento */
echo("                    <td width=\"10%\" style=\"cursor:pointer\">".RetornaFraseDaLista($lista_frases, 57)."</td>\n");
/* Frase #169 - Comentarios */
echo("                    <td width=\"10%\">".RetornaFraseDaLista($lista_frases, 169)."</td>\n");
/* Frase #170 - Avaliacao */
echo("                    <td width=\"10%\">".RetornaFraseDaLista($lista_frases, 170)."</td>\n");
/* Frase #130 - Situacao */
echo("                    <td width=\"10%\" style=\"cursor:pointer\">".RetornaFraseDaLista($lista_frases, 130)."</td>\n");
echo("                  </tr>\n");

if(count($exercicios) > 0 && $exercicios != null)
{
	foreach ($exercicios as $cod => $linha_item)
	{
		if ($cod_usuario_exercicio == $cod_usuario) $varTmp="P";
		else if ($eformador) $varTmp="F";
		else $varTmp="T";

		$icone = "<img src=\"../imgs/arqp.gif\" alt=\"\" border=\"0\" /> ";
		$aplicado = RetornaDadosExercicioAplicado($sock,$linha_item['cod_exercicio']);

		/* Frase #6 - Compartilhado com formadores */
		if($linha_item['compartilhada'] == "F")
		$compartilhamento = RetornaFraseDaLista($lista_frases, 6);
		/* Frase #7 - Totalmente compartilhado */
		else if($linha_item['compartilhada'] == "T")
		$compartilhamento = RetornaFraseDaLista($lista_frases, 7);
		/* Frase #8 - Nao Compartilhado */
		else
		$compartilhamento = RetornaFraseDaLista($lista_frases, 8);

		if($eformador) $compartilhamento = "<span id=\"comp_".$linha_item['cod_resolucao']."\" class=\"link\" onclick=\"js_cod_item=".$linha_item['cod_resolucao'].";AtualizaComp('".$linha_item['compartilhada']."');MostraLayer(cod_comp,140,event);return(false);\">".$compartilhamento."</span>";


		$num_comentarios=RetornaNumComentariosExercicio($sock,$cod_usuario,$linha_item['cod_resolucao']);

		$comentarios = "";
		if ($num_comentarios['num_comentarios_alunos']>0)
		$comentarios .= "<span class=\"cAluno\">(c)</span>";
		if ($num_comentarios['num_comentarios_formadores']>0)
		$comentarios .= "<span class=\"cForm\">(c)</span>";
		if ($num_comentarios['num_comentarios_usuario']>0)
		$comentarios .= "<span class=\"cMim\">(c)</span>";
		if ($num_comentarios['data_comentarios']>$data_acesso)
		$comentarios .= "<span class=\"cNovo\">*</span>";

		if($aplicado['avaliacao'] > 0)
		/* Frase #76 - Sim */
		/* Frase #77 - Nao */
		$avaliacao = RetornaFraseDaLista($lista_frases, 76);
		else
		$avaliacao = RetornaFraseDaLista($lista_frases, 77);

		$situacao = "";
		if($linha_item['submetida'] == 'S')
		$situacao .= "<span class=\"\">(e)</span>";
		if($linha_item['corrigida'] == 'S')
		$situacao .= "<span class=\"avaliada\">(a)</span>";

		$cod_resolucao = $linha_item['cod_resolucao'];
		
		echo("                  <tr id=\"trResolucao_".$linha_item['cod_resolucao']."\">\n");
		
		
		/* O Redirecionamento:
		 * 
		 * Formador:
		 * Se o Exercicio já estiver entregue, manda para a pagina de correcao (corrigir_exercicio)
		 * 
		 * Aluno:
		 * Se o Exercicio ja estiver corrigido, manda para a pagina de gabarito/correcao (ver_gabarito)
		 * Se o Exercicio não estiver nem entregue nem corrigido, manda para a pagina de resolucao (resolver)
		 * 
		 */
		
		if($linha_item['corrigida'] == 'S'){
			echo("                    <td align=\"left\">".$icone."<a href='ver_gabarito.php?cod_curso=".$cod_curso."&visualizar=I&cod=".$cod_usuario_exercicio."&cod_resolucao=".$cod_resolucao."'>".$linha_item['titulo']."</td>\n");
		} else  if($linha_item['submetida'] == 'S' && $eformador){
			echo("                    <td align=\"left\">".$icone."<a href=\"corrigir_exercicio.php?cod_curso=".$cod_curso."&cod_resolucao=".$linha_item['cod_resolucao']."\">".$linha_item['titulo']."</a>");
		 } else {
			echo("                    <td align=\"left\">".$icone."<a href=\"resolver.php?cod_curso=".$cod_curso."&cod_resolucao=".$linha_item['cod_resolucao']."\">".$linha_item['titulo']."</a>");
		}
		
		echo("                    <td>".UnixTime2DataHora($aplicado['dt_limite_submissao'])."</td>\n");
		echo("                    <td>".$compartilhamento."</td>\n");
		echo("                    <td>".$comentarios."</td>\n");
		echo("                    <td>".$avaliacao."</td>\n");
		echo("                    <td>".$situacao."</td>\n");
		echo("                  </tr>\n");
	}
}
else
{
	echo("                  <tr>\n");
	/* Frase #118 - Nao ha nenhum exercicio */
	echo("                    <td colspan=\"6\">".RetornaFraseDaLista($lista_frases, 118)."</td>\n");
	echo("                  </tr>\n");
}

echo("                </table>\n");

/* ?? - Comentario de Aluno */
/* ?? - Comentario de Formador */
/* ?? - Comentario postados por mim */
echo("                <span class=\"cAluno\">(c)</span> Comentario de Aluno - \n");
echo("                <span class=\"cForm\">(c)</span> Comentario de Formador - \n");

if(!EVisitante($sock,$cod_curso,$cod_usuario))
echo("                <span class=\"cMim\">(c)</span> Comentario postados por mim&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n");

/* ?? - Corrigido */
echo("                <span class=\"avaliado\">(a)</span> Corrigido - \n");
/* ?? - Entregue */
echo("                <span class=\"entregue\">(e)</span> Entregue\n");

echo("              </td>\n");
echo("            </tr>\n");
echo("          </table>\n");
/* Frase #5 - Voltar */
echo("          <span class=\"btsNavBottom\"><a href=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"".RetornaFraseDaLista($lista_frases, 5)."\" /></a> <a href=\"#topo\"><img src=\"../imgs/btTopo.gif\" border=\"0\" alt=\"Topo\" /></a></span>\n");


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
	/* Frase #7 - Totalmente compartilhado */
	echo("              <span>".RetornaFraseDaLista($lista_frases, 7)."</span>\n");
	echo("            </li>\n");
	echo("            <li onClick=\"document.getElementById('tipo_comp').value='F'; xajax_MudarCompartilhamentoDinamic(xajax.getFormValues('form_comp'), 'Compartilhado com formadores', 'R'); EscondeLayers();\">\n");
	echo("              <span id=\"tipo_comp_F\" class=\"check\"></span>\n");
	/* Frase #6 - Compartilhado com formadores */
	echo("              <span>".RetornaFraseDaLista($lista_frases, 6)."</span>\n");
	echo("            </li>\n");
	echo("            <li onClick=\"document.getElementById('tipo_comp').value='N'; xajax_MudarCompartilhamentoDinamic(xajax.getFormValues('form_comp'), 'Nao Compartilhado', 'R'); EscondeLayers();\">\n");
	echo("              <span id=\"tipo_comp_N\" class=\"check\"></span>\n");
	/* Frase #8 - Nao Compartilhado */
	echo("              <span>".RetornaFraseDaLista($lista_frases, 8)."</span>\n");
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