<?php
/*
 <!--
-------------------------------------------------------------------------------

Arquivo : cursos/aplic/agenda/ver_linha.php

TelEduc - Ambiente de Ensino-Aprendizagem a Distï¿½ncia
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

Nied - Nï¿½cleo de Informï¿½tica Aplicada ï¿½ Educaï¿½ï¿½o
Unicamp - Universidade Estadual de Campinas
Cidade Universitï¿½ria "Zeferino Vaz"
Bloco V da Reitoria - 2o. Piso
CEP:13083-970 Campinas - SP - Brasil

http://www.nied.unicamp.br
nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
 ARQUIVO : cursos/aplic/agenda/ver_linha.php
========================================================== */
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

/*$objAjax->register(XAJAX_FUNCTION,"EditarTitulo");
$objAjax->register(XAJAX_FUNCTION,"EditarTexto");
$objAjax->register(XAJAX_FUNCTION,"DecodificaString");
$objAjax->register(XAJAX_FUNCTION,"AbreEdicao");
$objAjax->register(XAJAX_FUNCTION,"AcabaEdicaoDinamic");
$objAjax->register(XAJAX_FUNCTION,"ExcluirArquivo");
$objAjax->register(XAJAX_FUNCTION,"SelecionarEntradaDinamic");
$objAjax->register(XAJAX_FUNCTION,"RetirarEntradaDinamic");
$objAjax->register(XAJAX_FUNCTION,"RetornaFraseDinamic");
$objAjax->register(XAJAX_FUNCTION,"RetornaFraseGeralDinamic");

$objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");*/


$sock=AcessoSQL::Conectar("");
$diretorio_arquivos=Agenda::RetornaDiretorio($sock,'Arquivos');

$diretorio_temp=Agenda::RetornaDiretorio($sock,'ArquivosWeb');
AcessoSQL::Desconectar($sock);

$cod_item = $_GET['cod_item'];
$origem = $_GET['origem'];

$cod_ferramenta=1;
$cod_ferramenta_ajuda = $cod_ferramenta;
$cod_pagina_ajuda=4;

require_once $view_administracao.'topo_tela.php';

// instanciar o objeto, passa a lista de frases por parametro
$feedbackObject =  new FeedbackObject($lista_frases);
//adicionar as acoes possiveis, 1o parametro eh a acao, o segundo eh o numero da frase para ser impressa se for "true", o terceiro caso "false"

/* 96 - Agenda criada com sucesso.
 * Arquivo anexado com sucesso.
 * Ocorreu um erro ao tentar anexar o arquivo.
 * Arquivo descompactado com sucesso.
 * Houve um erro ao descompactar o arquivo.
 * 105 - Arquivo de entrada selecionado com sucesso.
 * 55 - Selecione o arquivo que será a entrada da agenda
 * */
$feedbackObject->addAction("criarAgenda", _("AGENDA_CREATED_SUCCESS_1"), 0);
$feedbackObject->addAction("anexar", _("FILE_ATTACHED_SUCCESS_-1"), _("ERROR_ATTACHING_FILE_-1"));
$feedbackObject->addAction("descompactar", _("FILE_EXTRACTED_SUCCESS_-1"), _("ERROR_EXTRACTING_FILE_-1"));
$feedbackObject->addAction("selecionar_entrada", _("MAIN_PAGE_SELECTED_SUCCESS_1"), 0);
$feedbackObject->addAction("retirar_entrada", _("SELECT_MAIN_PAGE_FILE_1"), 0);

$dir_name = "agenda";
$dir_item_temp=Agenda::CriaLinkVisualizar($sock,$dir_name, $cod_curso, $cod_usuario, $cod_item, $diretorio_arquivos, $diretorio_temp);

/* Verifica se o usuario eh formador. */
$usr_formador = Usuarios::EFormador($sock, $cod_curso, $cod_usuario);
$linha_item = Agenda::RetornaAgenda($sock, $cod_item);

$id = $linha_item['cod_item'];

echo("	<script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js\"></script>");

echo("    <script type=\"text/javascript\" src=\"".$diretorio_jscss."ckeditor/ckeditor.js\"></script>");
echo("    <script type=\"text/javascript\" src=\"".$diretorio_jscss."ckeditor/ckeditor_biblioteca.js\"></script>");
echo("    <script type='text/javascript' src='".$diretorio_jscss."dhtmllib.js'></script>\n");

echo("    <script type=\"text/javascript\">\n\n");


echo("      var cod_item='".$cod_item."';\n");
echo("      var cod_curso='".$cod_curso."';\n");
echo("      var cod_usuario='".$cod_usuario."';\n");
echo("      var origem='".$origem."';\n");
echo("      var num_apagados = '0';\n");
/* (ger) 18 - Ok */
// Texto do botao Ok do ckEditor
echo("    var textoOk = '"._("OK_-1")."';\n\n");
/* (ger) 2 - Cancelar */
// Texto do botao Cancelar do ckEditor
echo("    var textoCancelar = '"._("CANCEL_-1")."';\n\n");

echo("      function TemCertezaAtivar()\n");
echo("      {\n");
/* 57 - Tem certeza que deseja ativar esta agenda? */
/* 58 - (Uma vez ativada, nao havera como desativa-la) */
echo("        return(confirm(\""._("SURE_TO_ACTIVATE_AGENDA_1")."\\n"._("NO_WAY_TO_DEACTIVATE_1")."\"));\n");
echo("      }\n");


echo("      function Ativar()\n");
echo("      {\n");
echo("        if(TemCertezaAtivar())\n");
echo("        {\n");
echo("          window.location='".$ctrl_agenda."acoes_linha.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=1&cod_item=".$cod_item."&acao=ativaragenda';\n");
echo("        }\n");
echo("        return false;\n");
echo("      }\n");

echo("      function WindowOpenVer(id)\n");
echo("      {\n");
echo("         window.open(id+'?".time()."','Agenda','top=50,left=100,width=600,height=400,menubar=yes,status=yes,toolbar=yes,scrollbars=yes,resizable=yes');\n");
echo("      }\n\n");

echo("      function Iniciar()\n");
echo("      {\n");
$feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
echo("        startList();\n");
echo("      }\n\n");

echo ("     function EditaTituloEnter(campo, evento, id)\n");
echo ("     {\n");
echo ("         var tecla;\n");
echo ("         CheckTAB=true;\n\n");
echo ("         if(navigator.userAgent.indexOf(\"MSIE\")== -1)\n");
echo ("         {\n");
echo ("             tecla = evento.which;\n");
echo ("         }\n");
echo ("         else\n");
echo ("         {\n");
echo ("             tecla = evento.keyCode;\n");
echo ("         }\n\n");
echo ("         if ( tecla == 13 )\n");
echo ("         {\n");
echo ("             EdicaoTitulo(id, 'tit_'+id, 'ok');\n");
echo ("         }\n\n");
echo ("         return true;\n");
echo ("     }\n\n");

//echo("      function AlteraTitulo(id){\n");
echo("		$(document).ready(function(){\n");
echo("			$('#renomear_".$id."').click(function(){\n");
echo("        	if (editaTitulo==0){\n");
echo("          	CancelaTodos();\n");
echo("        	var id_aux = id;\n");
echo("			var cod_curso = ".$cod_curso.";\n");
echo("			var cod_item = ".$cod_item.";\n");
echo("			var cod_usuario = ".$cod_usuario.";\n");
echo("			var id = ".$id.";\n");

echo("			$.post(\"".$model_agenda."abre_edicao.php\",{cod_curso: cod_curso, cod_item: cod_item, cod_usuario:cod_usuario, origem:origem, action:'abrirEdicao'}, \n");
echo("				function(data){\n");
echo("					var code = $.parseJSON(data);\n");
//echo("					alert(code);\n");
echo("			});\n");

//echo("          xajax_AbreEdicao(".$cod_curso.", ".$cod_item.", ".$cod_usuario.", origem);\n");

echo("          conteudo = document.getElementById('tit_'+id).innerHTML;\n");
echo("          document.getElementById('tr_'+id).className='';\n");
echo("          document.getElementById('tit_'+id).className='';\n");

echo("          createInput = document.createElement('input');\n");
echo("          document.getElementById('tit_'+id).innerHTML='';\n");
//echo("          document.getElementById('renomear_'+id).onclick=function(){ };\n\n");
//echo("          document.getElementById('renomear_'+id).setAttribute('onclick', '');\n");

echo("          createInput.setAttribute('type', 'text');\n");
echo("          createInput.setAttribute('style', 'border: 2px solid #9bc');\n");
echo("          createInput.setAttribute('id', 'tit_'+id+'_text');\n\n");
echo("          if (createInput.addEventListener){\n"); //not IE
echo("            createInput.addEventListener('keypress', function (event) {EditaTituloEnter(this, event, id_aux);}, false);\n");
echo("          } else if (createInput.attachEvent){\n"); //IE
echo("            createInput.attachEvent('onkeypress', function (event) {EditaTituloEnter(this, event, id_aux);});\n");
echo("          }\n");

echo("          document.getElementById('tit_'+id).appendChild(createInput);\n");

echo("			$.post(\"".$model_geral."decodifica_string.php\",{conteudo:conteudo}, \n");
echo("				function(data){\n");
echo("					var code = $.parseJSON(data);\n");
echo("					$('#tit_".$id."_text').val(code);\n");
echo("			});\n");
//echo("          xajax_DecodificaString('tit_'+id+'_text', conteudo, 'value');\n\n");

echo("          //cria o elemento 'espaco' e adiciona na pagina\n");
echo("          espaco = document.createElement('span');\n");
echo("          espaco.innerHTML='&nbsp;&nbsp;';\n");
echo("          document.getElementById('tit_'+id).appendChild(espaco);\n");

echo("          createSpan = document.createElement('span');\n");
echo("          createSpan.className='link';\n");
echo("          createSpan.onclick= function(){ EdicaoTitulo(id, 'tit_'+id, 'ok'); };\n");
echo("          createSpan.setAttribute('id', 'OkEdita');\n");
echo("          createSpan.innerHTML='"._("OK_-1")."';\n");
echo("          document.getElementById('tit_'+id).appendChild(createSpan);\n\n");

echo("          //cria o elemento 'espaco' e adiciona na pagina\n");
echo("          espaco = document.createElement('span');\n");
echo("          espaco.innerHTML='&nbsp;&nbsp;';\n");
echo("          document.getElementById('tit_'+id).appendChild(espaco);\n\n");

echo("          createSpan = document.createElement('span');\n");
echo("          createSpan.className='link';\n");
echo("          createSpan.onclick= function(){ EdicaoTitulo(id, 'tit_'+id, 'canc'); };\n");
echo("          createSpan.setAttribute('id', 'CancelaEdita');\n");
echo("          createSpan.innerHTML='"._("CANCEL_-1")."';\n");
echo("          document.getElementById('tit_'+id).appendChild(createSpan);\n\n");

echo("          //cria o elemento 'espaco' e adiciona na pagina\n");
echo("          espaco = document.createElement('span');\n");
echo("          espaco.innerHTML='&nbsp;&nbsp;';\n");
echo("          document.getElementById('tit_'+id).appendChild(espaco);\n\n");

echo("          startList();\n");
echo("          cancelarElemento=document.getElementById('CancelaEdita');\n");
echo("          document.getElementById('tit_'+id+'_text').select();\n");
echo("          editaTitulo++;\n");
echo("        }\n");
//echo("      }\n\n");
echo("			});\n");

echo("		function EdicaoTitulo(codigo, id, valor){\n");
echo("			//se o título não é vazio\n");
echo("			if ((valor=='ok')&&(document.getElementById(id+'_text').value != \"\")){\n");
echo("				novoconteudo = document.getElementById(id+'_text').value;\n");
echo("				//Edita o título do item dado, dinâmicamente\n");
echo("			$.post(\"".$model_agenda."editar_titulo.php\",{cod_curso:cod_curso, cod_item:codigo, novo_nome:novoconteudo, cod_usuario:cod_usuario}, \n");
echo("				function(data){\n");
echo("					$('#tr_".$id."').toggleClass('novoitem');\n");
echo("					$('#tit_".$id."').html(novoconteudo);\n");
/* 103 - Agenda renomeada com sucesso.*/
echo("					mostraFeedback('"._("AGENDA_RENAMED_SUCCESS_1")."', 'true');\n");
echo("			});\n");
echo("			//else - se o título for vazio.\n");
echo("			}else{\n");
echo("				/* 15 - O titulo nao pode ser vazio. */\n");
echo("				if ((valor=='ok')&&(document.getElementById(id+'_text').value == \"\"))\n");
/* 92 - O título não pode ser vazio.*/
echo("					alert('"._("TITLE_CANNOT_BE_EMPTY_-1")."');\n");
echo("					document.getElementById(id).innerHTML=conteudo;\n");
//echo("			if(navigator.appName.match(\"Opera\")){\n");
//echo("				document.getElementById('renomear_'+codigo).onclick = AlteraTitulo(codigo);\n");
//echo("			}else{\n");
//echo("				document.getElementById('renomear_'+codigo).onclick = function(){ AlteraTitulo(codigo); };\n");
//echo("			}\n");
echo("			//Cancela Edição\n");
echo("			if (!cancelarTodos)\n");
echo("			$.post(\"".$model_agenda."acaba_edicao.php\",{cod_curso: cod_curso, cod_item: cod_item, cod_usuario:cod_usuario, origem:origem, acao: 0}, \n");
echo("				function(data){\n");
echo("					var code = $.parseJSON(data);\n");
//echo("					alert(code);\n");
echo("			});\n");
//echo("				xajax_AcabaEdicaoDinamic(cod_curso, cod_item, cod_usuario, 0);\n");
echo("			}\n");
echo("		editaTitulo=0;\n");
echo("		cancelarElemento=null;\n");
echo("		}\n\n");
echo("	});");
//echo("		event.preventDefault();\n");


echo("    </script>\n\n");

echo("    <script type=\"text/javascript\" src=\"".$diretorio_jscss."jscriptlib_agenda.js\"> </script>\n");

require_once $view_administracao.'menu_principal.php';

echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
/* Verifica se o item esta em Edicao */
/* Se estiver, voltar a tela anterior, e disparar a tela de Em Edicao... */

$linha=Agenda::RetornaUltimaPosicaoHistorico($sock, $cod_item);

if ($linha['acao']=="E")
{
	if (($linha['data']<(time()-1800)) || ($cod_usuario == $linha['cod_usuario'])){
		Agenda::AcabaEdicao($sock, $cod_curso, $cod_item, $cod_usuario, 0);
	}else{
		/* Esta em edicao... */
		echo("          <script language=\"javascript\">\n");
		echo("            window.open('".$view_agenda."em_edicao.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=1&cod_item=".$cod_item."&origem=ver_linha','EmEdicao','width=400,height=250,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
		echo("            window.location='".$origem.".php?".RetornaSessionID()."&cod_curso=".$cod_curso."';\n");
		echo("          </script>\n");
		echo("        </td>\n");
		echo("      </tr>\n");
		echo("    </table>\n");
		echo("  </body>\n");
		echo("</html>\n");
		exit();
	}
}

/* Pagina Principal */

/* Se foi clicado no nome da agenda vindo da pagina de Agendas Anteriores, entao apenas mostra a agenda. Sendo assim ela nao eh editavel.
 * Assim, o titulo da pagina eh: "Agenda - Agendas Anteriores"
*
* Se nï¿½o, foi clicado em determinada agenda e ela aparece editavel. Neste caso, o titulo da pagina eh: "Agenda - Editar Agenda"
*/
if($origem == "ver_anteriores")
  {
    /* 1 - Agenda */
    /*2 - Agendas Anteriores*/
$cabecalho = "          <h4>"._("AGENDA_1")." - "._("PAST_AGENDAS_1")."</h4>";
} else {
    /* 1 - Agenda */
    /* 111 - Editar Agenda*/
     $cabecalho = "          <h4>"._("AGENDA_1")." - "._("EDIT_AGENDA_1")."</h4>";
}
echo($cabecalho);

// 3 A's - Muda o Tamanho da fonte
echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"".$diretorio_imgs."btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"".$diretorio_imgs."btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"".$diretorio_imgs."btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  /*Voltar*/
   /* 509 - Voltar */
echo("          <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;"._("BACK_-1")."&nbsp;</span></li></ul>\n");

  /* Tabela Externa */
  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");

  if($origem == "ver_anteriores")
  /*33 - Voltar para Agenda Anteriores*/
  $frase = _("BACK_TO_PAST_AGENDAS_1");
  else if($origem == "ver_editar")
  	/*3 - Agendas Futuras*/
  			$frase = _("FUTURE_AGENDAS_1");
  			else
  				/*8 - Voltar para Agenda Atual*/
  				$frase = _("BACK_TO_ACTUAL_AGENDA_1");


  				if($origem == "ver_editar")
  				$caminho="ver_editar.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario;
  else if($origem == "ver_anteriores")
  				$caminho="ver_anteriores.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario;
  else
  $caminho="agenda.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario;

  echo("                  <li><a href=\"".$caminho."\">".$frase."</a></li>\n");
  /*34 - Historico */
  			echo("                  <li><span onclick=\"window.open('historico_agenda.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$cod_item."','Historico','width=600,height=400,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');\">"._("RECORD_OF_CHANGES_-1")."</span></li>\n");
  			if($origem == "ver_editar"){
  			/*24 - Publicar */
  			echo("                  <li><span onClick=\"Ativar();\">"._("ACTIVATE_1")."</span></li>\n");
  }
  /* 1(ger) - Apagar */
  if($usr_formador){
  			echo("                  <li><span onClick=\"ApagarItem();\">"._("DELETE_-1")."</span></li>\n");
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
  			echo("                    <td class=\"alLeft\" align=\"left\">"._("TITLE_-1")."</td>\n");

  					/*Conteudo da Agenda*/
  			

  			if(($usr_formador) && ($linha_item['situacao'] != "H"))
  			{
  				/*70 (gn) - Opcoes */
  				echo("                  <td align=center width=\"15%\">"._("OPTIONS_0")."</td>\n");
  			}
  			echo("                  </tr>\n");

  			$titulo=$linha_item['titulo'];

  			/* (ger) 9 - Editar */
  			$editar=_("EDIT_-1");

  			if ($linha_item['status']=="E")
  			{

  				$linha_historico=Agenda::RetornaUltimaPosicaoHistorico($sock, $linha_item['cod_item']);

  				if ($linha['data']<(time()-1800) || $cod_usuario == $linha_historico['cod_usuario'])
  				{
  					Agenda::CancelaEdicao($sock, $linha_item['cod_item'], $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp);
  					if($usr_formador)
  					{
        $titulo="<span id=\"tit_".$linha_item['cod_item']."\">".$linha_item['titulo']."</span>";
        // 106 - Renomear Título
        $renomear="<span id=\"renomear_".$linha_item['cod_item']."\">"._("RENAME_TITLE_-1")."</span>";
        /* 91 - Editar texto */
        $editar="<span onclick=\"AlteraTexto(".$linha_item['cod_item'].");\">"._("EDIT_TEXT_-1")."</span>";
        /* 92 - Limpar texto */
        $limpar="<span onclick=\"LimpaTexto(".$linha_item['cod_item'].");\">"._("DELETE_TEXT_-1")."</span>";
  					}
  				}
  			}
  			//else = item nao esta sendo editado
  			else
  			{
  				if($usr_formador)
  				{
  					$titulo="<span id=\"tit_".$linha_item['cod_item']."\">".$linha_item['titulo']."</span>";
  					// 106 - Renomear Ti\ADtulo
  					$renomear="<span id=\"renomear_".$linha_item['cod_item']."\">"._("RENAME_TITLE_-1")."</span>";
  					/* 91 - Editar texto */
  					$editar="<span onclick=\"AlteraTexto(".$linha_item['cod_item'].");\">"._("EDIT_TEXT_-1")."</span>";
  					/* 92 - Limpar texto */
  					$limpar="<span onclick=\"LimpaTexto(".$linha_item['cod_item'].");\">"._("DELETE_TEXT_-1")."</span>";
  				}
  			}

  			echo("                  <tr id='tr_".$linha_item['cod_item']."'>\n");
  			echo("                    <td class=\"itens\">".$titulo."</td>\n");

  			if ($linha_item['situacao']!="H")
  			{
  				if($usr_formador)
  				{
  					echo("                    <td align=\"left\" valign=\"top\" class=\"botao2\">\n");
  					echo("                      <ul>\n");
  					echo("                        <li>".$renomear."</li>\n");
  					echo("                        <li>".$editar."</li>\n");
  					echo("                        <li>".$limpar."</li>\n");
  					/* So pode apagar ou ativar agendas que estao na sessao "Editar Agendas" ou que acabaram de ser criadas*/
  					/*if($origem == "ver_editar")
  					 {*/
  					/*24 - Ativar*/
  					/*echo("                        <li><span onClick=\"Ativar();\">".Linguas::RetornaFraseDaLista ($lista_frases, 24)."</span></li>\n");
        // G 1 - Apagar
  					echo("                        <li><span onClick=\"ApagarItem();\">".Linguas::RetornaFraseDaLista ($lista_frases_geral, 1)."</span></li>\n");
  					}*/
  					echo("                      </ul>\n");
  					echo("                    </td>\n");
  				}
  			}

  			echo("                  </tr>\n");

  			/*Verifica se ha arquivo de entrada*/
  			$arquivo_entrada="";
  			$lista_arq=Agenda::RetornaArquivosAgendaVer($cod_curso, $dir_item_temp['diretorio']);

  			if (count($lista_arq)>0)
  			foreach($lista_arq as $cod => $linha1)
  			if ($linha1['Status'] && $linha1['Arquivo']!=""){
  				if(preg_match('/\.php(\.)*/', $linha1['Arquivo'])){  //arquivos php.txt

  					$arquivo_entrada = "".$view_agenda."agenda_entrada.php?entrada=".ConversorTexto::ConverteUrl2Html($linha1['Arquivo']."&diretorio=".$dir_item_temp['link']);
  				}else{
  					$arquivo_entrada = ConversorTexto::ConverteUrl2Html($dir_item_temp['link'].$linha1['Diretorio']."/".$linha1['Arquivo']);
  				}
  				break;
  			}

  			/*Se houver, cria um iframe para exibi-lo*/
  			if(($linha_item['texto']=="")&&($arquivo_entrada!=""))
  				$conteudo="<span id=\"text_".$linha_item['cod_item']."\"><iframe id=\"iframe_ArqEntrada\" texto=\"ArqEntrada\" src=\"".$arquivo_entrada."\" width=\"100%\" height=\"400\" frameBorder=\"0\" scrolling=\"Auto\"></iframe></span>";
  			/*Senaum, exibe o texto da agenda*/
  			else
  			{
  				$texto = ConversorTexto::AjustaParagrafo($linha_item['texto']);

  				if(($texto == "<P>&nbsp;</P>") || ($texto == "<br />"))
  					$texto = "";

  				$conteudo="<span id=\"text_".$linha_item['cod_item']."\">".$texto."</span>";
  			}

  			echo("                  <tr class=\"head\">\n");
  			/* 94 - Conteudo  */
  			echo("                    <td colspan=\"4\">"._("CONTENT_-1")."</td>\n");
  			echo("                  </tr>\n");
  			echo("                  <tr>\n");
  			echo("                    <td class=\"itens\" colspan=\"4\">\n");
  			echo("                      <div class=\"divRichText\">\n");
  			echo("                        ".$conteudo."\n");
  			echo("                      </div>\n");
  			echo("                    </td>\n");
  			echo("                  </tr>\n");

  			if ($usr_formador){
  				echo("                  <tr class=\"head\">\n");
  				/* 57(biblioteca) - Arquivos */
  				echo("                    <td colspan=\"4\">"._("FILES_-2")."</td>\n");
  				echo("                  </tr>\n");


  				if (count($lista_arq)>0){
  					$conta_arq=0;

  					echo("                  <tr>\n");
  					echo("                    <td class=\"itens\" colspan=\"4\" id=\"listFiles\">\n");
  					// Procuramos na lista de arquivos se existe algum visivel
  					$ha_visiveis = true;

  					$nivel_anterior=0;
  					$nivel=-1;

  					foreach($lista_arq as $cod => $linha)
  					{
  						$linha['Arquivo'] = mb_convert_encoding($linha['Arquivo'], "ISO-8859-1", "UTF-8");
  						if (!($linha['Arquivo']=="" && $linha['Diretorio']==""))
  						{
  							$nivel_anterior=$nivel;
  							$espacos="";
  							$espacos2="";
  							$temp=explode("/",$linha['Diretorio']);
  							$nivel=count($temp)-1;			
  							for ($c=0;$c<=$nivel;$c++){
  								$espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
  								$espacos2.="  ";
  							}

  							$caminho_arquivo = $dir_item_temp['link'].$linha['Diretorio']."/".$linha['Arquivo'];
  							$caminho_arquivo = preg_replace("/\/\//", "/", $caminho_arquivo);
  							
  							
  							if ($linha['Arquivo'] != "")
  							{

  								if ($linha['Diretorio']!=""){
  									$espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
  									$espacos2.="  ";
  								}

  								if ($linha['Status']) $arqEntrada="arqEntrada='sim'";
  								else $arqEntrada="arqEntrada='nao'";

  								if (eregi(".zip$",$linha['Arquivo']))
  								{
  									// arquivo zip
  									$imagem    = "<img alt=\"\" src=".$diretorio_imgs."arqzip.gif border=0 />";
  									$tag_abre  = "<a href=\"".ConversorTexto::ConverteUrl2Html($caminho_arquivo)."\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".ConversorTexto::ConverteUrl2Html($caminho_arquivo)."'); return(false);\" tipoArq=\"zip\" nomeArq=\"".htmlentities($caminho_arquivo)."\" arqZip=\"".$linha['Arquivo']."\" ". $arqEntrada.">";
  								}
  								else
  								{
  									// arquivo comum
  									$imagem    = "<img alt=\"\" src=".$diretorio_imgs."arqp.gif border=0 />";
  									$tag_abre  = "<a href=\"".ConversorTexto::ConverteUrl2Html($caminho_arquivo)."\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".ConversorTexto::ConverteUrl2Html($caminho_arquivo)."'); return(false);\" tipoArq=\"comum\" nomeArq=\"".htmlentities($caminho_arquivo)."\" ".$arqEntrada.">";
  								}

  								$tag_fecha = "</a>";

  								echo("                        ".$espacos2."<span id=\"arq_".$conta_arq."\">\n");

  								if(($usr_formador) && ($linha_item['situacao'] != "H")){
  									echo("                          ".$espacos2."<input type=\"checkbox\" name=\"chkArq\" onClick=\"VerificaChkBox(1);\" id=\"chkArq_".$conta_arq."\">\n");
  								}
								
  								/* 107 - Última modificação em */
  								echo("                          ".$espacos2.$espacos.$imagem.$tag_abre.$linha['Arquivo'].$tag_fecha." - (".round(($linha['Tamanho']/1024),2)."Kb) - "._("LAST_MODIFICATION_IN_-1")." ".Data::UnixTime2Hora($linha["Data"])." ".Data::UnixTime2DataMesAbreviado($linha["Data"])."");

  								echo("<span id=\"local_entrada_".$conta_arq."\">");
  								if ($linha['Status'])
  									// 59 - entrada
  									echo("<span id=\"arq_entrada_".$conta_arq."\">- <span style='color:red;'>"._("MAIN_PAGE_1")."</span></span>");
  								echo("</span>\n");
  								echo("                          ".$espacos2."<br>\n");
  								echo("                        ".$espacos2."</span>\n");
  							}
  							else{
  								if ($nivel_anterior>=$nivel){
  									$i=$nivel_anterior-$nivel;
  									$j=$i;
  									$espacos3="";
  									do{
  										$espacos3.="  ";
  										$j--;
  									}while($j>=0);
  									do{
  										echo("                      ".$espacos3."</span>\n");
  										$i--;
  									}while($i>=0);
  								}
  								// pasta
  								$imagem    = "<img alt=\"\" src=".$diretorio_imgs."pasta.gif border=0 />";
  								echo("                      ".$espacos2."<span id=\"arq_".$conta_arq."\">\n");
  								echo("                        ".$espacos2."<span class=\"link\" id=\"nomeArq_".$conta_arq."\" tipoArq=\"pasta\" nomeArq=\"".htmlentities($caminho_arquivo)."\"></span>\n");
  								if(($usr_formador) && ($linha_item['situacao'] != "H")){
  									echo("                        ".$espacos2."<input type=\"checkbox\" name=\"chkArq\" onClick=\"VerificaChkBox(1);\" id=\"chkArq_".$conta_arq."\">\n");
  								}
  								echo("                        ".$espacos2.$espacos.$imagem.$temp[$nivel]."\n");
  								echo("                        ".$espacos2."<br>\n");
  							}

  						}
  						$conta_arq++;
  					}
  					do{
  						$j=$nivel;
  						$espacos3="";
  						while($j>0){
  							$espacos3.="  ";
  							$j--;
  						}
  						if($j!=$nivel){
  							echo("                      ".$espacos3."</span>\n");
  						}
  						$nivel--;
  					}while($nivel>=0);

  					echo("                      <script type=\"text/javascript\">js_conta_arq=".$conta_arq.";</script>\n");
  					echo("                    </td>\n");
  					echo("                  </tr>\n");

  				}
  			}

  			if(($usr_formador) && ($linha_item['situacao'] != "H"))
  			{

  				echo("                  <tr>\n");
  				echo("                    <td align=\"left\" colspan=\"4\">\n");
  				echo("                      <ul>\n");
  				echo("                        <li class=\"checkMenu\"><span><input type=\"checkbox\" id=\"checkMenu\" onClick=\"CheckTodos();\" /></span></li>\n");
  				/*1 - Apagar (ger) */
  				echo("                        <li class=\"menuUp\" id=\"mArq_apagar\"><span id=\"sArq_apagar\">"._("DELETE_-1")."</span></li>\n");
  				/*38 - Descompactar (ger)*/
  				echo("                        <li class=\"menuUp\" id=\"mArq_descomp\"><span id=\"sArq_descomp\">"._("EXTRACT_-1")."</span></li>\n");
  				/*60 - Selecionar Entrada */
  				echo("                        <li class=\"menuUp\" id=\"mArq_entrada\"><span id=\"sArq_entrada\">"._("SELECT_MAIN_PAGE_1")."</span></li>\n");
  				echo("                      </ul>\n");
  				echo("                    </td>\n");
  				echo("                  </tr>\n");
  				echo("                  <tr>\n");
  				echo("                    <td align=left colspan=4>\n");
  				echo("                      <form name=\"formFiles\" id=\"formFiles\" action='".$ctrl_agenda."acoes_linha.php' method='post' enctype=\"multipart/form-data\">\n");
  				echo("                        <input type='hidden' name='cod_curso' value='".$cod_curso."' />\n");
  				echo("                        <input type='hidden' name='cod_item' value='".$cod_item."' />\n");
  				echo("                        <input type='hidden' name='acao' value='anexar' />\n");
  				echo("                        <input type='hidden' name='origem' value='".$origem."' />\n");
  				echo("                        <div id=\"divArquivoEdit\" class=\"divHidden\">\n");
  				echo("                          <img alt=\"\" src=\"".$diretorio_imgs."paperclip.gif\" border=0 />\n");
  				/* 26 - Anexar Arquivo */
  				echo("                          <span class=\"destaque\">"._("ATTACH_FILE_-1")."</span>\n");
  				/* 48 - Pressione o botão abaixo para selecionar o arquivo a ser anexado  em seguida, pressione OK para prosseguir.
  				 * 49 - (arquivos .ZIP podem ser enviados e descompactados posteriormente)*/
  				echo("                          <span> - "._("PRESS_BUTTON_SELECT_ATTACH_FILE_-1")._("ZIP_FILES_CAN_BE_EXTRACTED_-1")."</span>\n");
  				echo("                          <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n");
  				echo("                          <input type=\"file\" id=\"input_files\" name=\"input_files\" onchange=\"EdicaoArq(1);\" style=\"border:2px solid #9bc\" />\n");
  				echo("                          &nbsp;&nbsp;\n");
  				//echo("                          <span onClick=\"EdicaoArq(1);\" id=\"OKFile\" class=\"link\">".Linguas::RetornaFraseDaLista ($lista_frases_geral, 18)."</span>\n");
  				//echo("                          &nbsp;&nbsp;\n");
  				//echo("                          <span onClick=\"EdicaoArq(0);\" id=\"cancFile\" class=\"link\">".Linguas::RetornaFraseDaLista ($lista_frases_geral, 2)."</span>\n");
  				echo("                        </div>\n");
  				/* 26 - Anexar arquivo (ger) */
  				echo("                        <div id=\"divArquivo\"><img alt=\"\" src=\"".$diretorio_imgs."paperclip.gif\" border=0 /> <span class=\"link\" id =\"insertFile\" onClick=\"AcrescentarBarraFile(1);\">"._("ATTACH_FILE_-1")."</span></div>\n");
  				echo("                      </form>\n");
  				echo("                    </td>\n");
  				echo("                  </tr>\n");
  			}

  			/*Fim tabela interna*/
  			echo("                </table>\n");

  			if($usr_formador)
  			{
  				echo("              </td>\n");
  				echo("            </tr>\n");
  				echo("            <tr>\n");
  				/* 59 - entrada. */
  				/* 20 - Este arquivo sera a entrada da agenda*/
  				echo("              <td align=\"left\">(<font color=red>"._("MAIN_PAGE_1")."</font>) - "._("FILE_AGENDA_MAIN_PAGE_1")."</td>\n");
  				echo("            </tr>\n");
  				echo("            <tr>\n");
  				/* 44 - Obs.: A agenda devera conter somente texto ou somente arquivos. */
  				echo("              <td align=\"left\">"._("CONTAIN_ONLY_TEXT_OR_ONLY_FILE_1")."\n");
  			}
  			/*Fim tabela externa*/
  			echo("              </td>\n");
  			echo("            </tr>\n");
  			echo("          </table>\n");
  			require_once $view_administracao.'tela2.php';
  			echo("  </body>\n");
  			echo("</html>\n");
  			AcessoSQL::Desconectar($sock);
?>