<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/portfolio/ver.php

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
  ARQUIVO : cursos/aplic/portfolio/ver.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("avaliacoes_portfolio.inc");

   require_once("../xajax_0.2.4/xajax.inc.php");
       
  //Estancia o objeto XAJAX
   $objAjax = new xajax();
  //Registre os nomes das fun�es em PHP que voc�quer chamar atrav� do xajax
   $objAjax->registerFunction("MudarCompartilhamento");
   $objAjax->registerFunction("EditarTitulo");
   $objAjax->registerFunction("EditarTexto");

  //Manda o xajax executar os pedidos acima.
   $objAjax->processRequests();

  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,15);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  $diretorio_arquivos=RetornaDiretorio($sock,'Arquivos');
  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  if ($acao=='anexar'){
    if (!file_exists($diretorio_temp."/tmp/".$cod_curso."/"))
      CriaDiretorio($diretorio_temp."/tmp/".$cod_curso."/");
    if (!file_exists($diretorio_temp."/tmp/".$cod_curso."/portfolio/"))
      CriaDiretorio($diretorio_temp."/tmp/".$cod_curso."/portfolio/");
    if (!file_exists($diretorio_temp."/tmp/".$cod_curso."/portfolio/item"))
      CriaDiretorio($diretorio_temp."/tmp/".$cod_curso."/portfolio/item");
    if (!file_exists($diretorio_temp."/tmp/".$cod_curso."/portfolio/item/".$cod_item."/"))
      CriaDiretorio($diretorio_temp."/tmp/".$cod_curso."/portfolio/item/".$cod_item."/");

    $dir=$diretorio_temp."/tmp/".$cod_curso."/portfolio/item/".$cod_item."/".($diret!=''?$diret."/":"");

    $nome_arquivo = $_FILES[input_files][name];

    if (!RealizaUpload($input_files,$dir.$nome_arquivo))
    {
     
      /* 61 - Aten�o: o arquivo que voc�anexou n� existe ou tem mais de 2Mb. Se voc�digitou o nome do arquivo, procure certificar-se que ele esteja correto ou ent� selecione o arquivo a partir do bot� Procurar (ou Browse). */
  
    }

  }
  /* 1 - Portf�io */
  echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n"); echo("\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
  echo("<html lang=\"pt\">\n");
  echo("  <head>\n");
  echo("    <title>TelEduc . Ensino &agrave; Dist&acirc;ncia</title>\n");
  echo("    <meta name=\"robots\" content=\"follow,index\" />\n");
  echo("    <meta name=\"description\" content=\"\" />\n");
  echo("    <meta name=\"keywords\" content=\"\" />\n");
  echo("    <meta name=\"owner\" content=\"\" />\n");
  echo("    <meta name=\"copyright\" content=\"\" />\n");
  echo("    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n");
  echo("    <link href=\"../js-css/ambiente.css\" rel=\"stylesheet\" type=\"text/css\" />\n");
  echo "    <script language=\"JavaScript\" type=\"text/javascript\" src=\"../bibliotecas/rte/html2xhtml.js\"></script>\n";
  echo "    <script language=\"JavaScript\" type=\"text/javascript\" src=\"../bibliotecas/rte/richtext.js\"></script>\n";
  echo("    <script type='text/javascript' src='../js-css/bib_ajax.js'> </script>\n");
  echo("    <script type='text/javascript' src='../bibliotecas/dhtmllib.js'></script>\n");

  echo("<script language=JavaScript>\n");
  //Usage: initRTE(imagesPath, includesPath, cssFile, genXHTML)
  echo "initRTE(\"../bibliotecas/rte/images/\", \"../bibliotecas/rte/\", \"../bibliotecas/rte/\", true);\n";
  echo "//-->\n";
  echo "</script>\n";

  echo("    <script language='JavaScript'>\n");
  echo("    var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
  echo("    var isMinNS6 = ((navigator.userAgent.indexOf(\"Gecko\") != -1) && (isNav));\n");
  echo("    var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");
  echo("    var Xpos, Ypos;\n");
  echo("    var js_cod_item, js_cod_topico;\n");
  echo("    var js_nome_topico;\n");
  echo("    var js_tipo_item;\n");
  echo("    var editando=0;\n");
  echo("    var mostrando=0\n");
  echo("    var js_comp = new Array();\n");
  echo("    var editando=0;\n");
  echo("    var conteudo=\"\";\n");
  echo("    var input=0;\n");

  echo("    if (isNav)\n");
  echo("    {\n");
  echo("      document.captureEvents(Event.MOUSEMOVE);\n");
  echo("    }\n");
  echo("    document.onmousemove = TrataMouse;\n");

  echo("    function TrataMouse(e)\n");
  echo("    {\n");
  echo("      Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
  echo("      Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
  echo("    }\n");

  echo("    function getPageScrollY()\n");
  echo("    {\n");
  echo("      if (isNav)\n");
  echo("        return(window.pageYOffset);\n");
  echo("      if (isIE){\n");
  echo("        if(document.documentElement.scrollLeft>=0){\n");
  echo("          return document.documentElement.scrollTop;\n");
  echo("        }else if(document.body.scrollLeft>=0){\n");
  echo("          return document.body.scrollTop;\n");
  echo("        }else{\n");
  echo("          return window.pageYOffset;\n");
  echo("        }\n");
  echo("      }\n");
  echo("    }\n");

  echo("    function AjustePosMenuIE()\n");
  echo("    {\n");
  echo("      if (isIE)\n");
  echo("        return(getPageScrollY());\n");
  echo("      else\n");
  echo("        return(0);\n");
  echo("    }\n");

  echo("    function Iniciar()\n");
  echo("    {\n");
  //echo("      cod_menu = getLayer(\"menu\");\n");
  //echo("      cod_menu_avaliado = getLayer(\"menu_avaliado\");\n");
 echo("      cod_comp = getLayer(\"comp\");\n");
//     echo("  cod_menu_top = getLayer(\"menutop\");\n");
//     echo("  cod_ren_top = getLayer(\"renomeartop\");\n");
  //echo("      cod_novo_top = getLayer(\"novotop\");\n");
//  echo("      cod_mover = getLayer(\"mover\");\n");
  //echo("      cod_topicos = getLayer(\"topicos\");\n");
//     echo("  cod_mudar_pos = getLayer(\"mudarpos\");\n");
 echo("      EscondeLayers();\n");
  echo("    }\n");
  echo("\n");

  echo("    function EscondeLayers()\n");
  echo("    {\n");
  //echo("      hideLayer(cod_menu);\n");
  echo("      hideLayer(cod_comp);\n");
  //echo("  hideLayer(cod_menu_avaliacao);\n");
//     echo("  hideLayer(cod_menu_top);\n");
//     echo("  hideLayer(cod_ren_top);\n");
  //echo("      hideLayer(cod_novo_top);\n");
//   echo("      hideLayer(cod_mover);\n");
  //echo("      hideLayer(cod_topicos);\n");
//     echo("  hideLayer(cod_mudar_pos);\n");
  echo("    }\n");

/*    echo("  if (isIE) \n");
    echo("    document.form_renomear_top.novo_nome.focus();\n");
*/
  //echo("}\n");
  echo("    function MostraLayer(cod_layer, ajuste)\n");
  echo("    {\n");
  echo("      EscondeLayers();\n");
  echo("      moveLayerTo(cod_layer,Xpos-ajuste,Ypos+AjustePosMenuIE());\n");
  echo("      if (editando>0){\n");
  echo("          if (editando==2) editando=0;\n");
  echo("      return false;\n");
  echo("      }\n");
  echo("      mostrando=1;\n");
  echo("      showLayer(cod_layer);\n");
  echo("    }\n");

  echo("    function EscondeLayer(cod_layer)\n");
  echo("    {\n");
  echo("      hideLayer(cod_layer);\n");
  echo("      mostrando=0;\n");
  echo("    }\n");
  echo(" \n");


  echo("    function AtualizaComp(js_tipo_comp)\n");
  echo("    {\n");
  echo("      if ((isNav) && (!isMinNS6)) {\n");
  echo("        document.comp.document.form_comp.tipo_comp.value=js_tipo_comp;\n");
  echo("        document.comp.document.form_comp.cod_item.value=js_cod_item;\n");
  echo("        var tipo_comp = new Array(document.comp.document.getElementById('tipo_comp_T'), document.comp.document.getElementById('tipo_comp_F'), document.comp.document.getElementById('tipo_comp_P'));\n");
  echo("      } else {\n");
  echo("      if (isIE || ((isNav)&&(isMinNS6)) ){\n");
  echo("         document.form_comp.tipo_comp.value=js_tipo_comp;\n");
  echo("         document.form_comp.cod_item.value=js_cod_item;\n");
  echo("         var tipo_comp = new Array(document.getElementById('tipo_comp_T'), document.getElementById('tipo_comp_F'), document.getElementById('tipo_comp_P'));\n");
  echo("         }\n");
  echo("      }\n");
  echo("      var imagem=\"<img src='../imgs/portfolio/checkmark_blue.gif'>\"\n");
  echo("      if (js_tipo_comp=='T') {\n");
  echo("        tipo_comp[0].innerHTML=imagem;\n");
  echo("        tipo_comp[1].innerHTML=\"\";\n");
  echo("        tipo_comp[2].innerHTML=\"\";\n");
  echo("      } else if (js_tipo_comp=='F') {\n");
  echo("        tipo_comp[0].innerHTML=\"\";\n");
  echo("        tipo_comp[1].innerHTML=imagem;\n");
  echo("        tipo_comp[2].innerHTML=\"\";\n");
  echo("      } else{\n");
  echo("        tipo_comp[0].innerHTML=\"\";\n");
  echo("        tipo_comp[1].innerHTML=\"\";\n");
  echo("        tipo_comp[2].innerHTML=imagem;\n");
  echo("      }\n");
  echo("    }\n");

  echo("function Edicao(codigo, id, valor, opcao){\n");
  echo("  if (valor=='ok'){\n");
  echo("    if (opcao==1) {\n");
  echo("      conteudo = document.getElementById(id+'_text').value;\n");
  echo("      xajax_EditarTitulo('".$cod_curso."', codigo, conteudo);\n");
  echo("    }else if(opcao==2){\n");
  echo("      conteudo=document.getElementById(id+'_text').contentWindow.document.body.innerHTML\n");
  echo("      xajax_EditarTexto('".$cod_curso."', codigo, conteudo);\n");
  echo("    }\n");
  echo("  }\n");
  echo("  if (opcao==1) document.getElementById(id).className=\"linkTexto\";\n");
  echo("  document.getElementById(id).innerHTML=conteudo;\n");
  echo("  editando=0;\n");
  echo("}\n");

  echo("function AlteraTitulo(id){\n");
  echo("  if (editando==0){\n");
  echo("    conteudo = document.getElementById('tit_'+id).innerHTML;\n");
  echo("    document.getElementById('tit_'+id).className=\"\";\n");
  echo("    document.getElementById('tit_'+id).innerHTML=\"<input type=text style=\\\"border: 2px solid #9bc;\\\" id='\"+'tit_'+id+\"_text' value='\"+conteudo+\"'> <span class=\\\"link\\\" onClick=\\\"Edicao('\"+id+\"', '\"+'tit_'+id+\"', 'ok', 1);\\\" id='OkEdita'>OK</span> <span class=\\\"link\\\" onClick=\\\"Edicao('\"+id+\"', '\"+'tit_'+id+\"', 'canc', 1);\\\" id='CancelaEdita'>Cancela</span>\";\n");
  echo("    document.getElementById('tit_'+id+'_text').focus();\n");
  echo("    editando++;\n");
  echo("  }\n");
  echo("}\n");

  echo("function AlteraTexto(id){\n");
  echo("  if (editando==0){\n");
  echo("    conteudo = document.getElementById('text_'+id).innerHTML;\n");
  echo("    writeRichTextOnJS('text_'+id+'_text', conteudo, 520, 200, true, false, id);\n");
  echo("    document.getElementById('text_'+id+'_text').focus();\n");
  echo("    editando++;\n");
  echo("  }\n");
  echo("}\n");

  echo("function EdicaoArq(id, i){\n");
  echo("  if (i==1){ //OK\n");
  echo("    alert('ok');\n");
  echo("    document.formFiles.submit()\n");
  echo("  }\n");
  //echo("  else {\n");
  echo("    elemen=document.getElementById(id);\n");
  echo("    elemen.className=\"link\";\n");
  echo("    elemen.removeChild(elemen.firstChild);\n");
  echo("    elemen.innerHTML=\"".RetornaFraseDaLista($lista_frases_geral,26)."\";\n");
  //echo("  }\n");
  echo("    input=0;\n");
  echo("}\n");

  echo("function AcrescentarBarra(id){\n");
  echo("    if (input==1) return;\n");
  echo("    document.getElementById(id).innerHTML=\"\";\n");
  echo("    document.getElementById(id).className=\"\";\n");

  echo("    inputFile = document.createElement('input');\n");
  echo("    inputFile.setAttribute(\"type\", \"file\");\n");
  echo("    inputFile.setAttribute(\"name\", \"input_files\");\n"); 
  echo("    inputFile.setAttribute(\"id\", \"input_files\");\n"); 
  echo("    document.getElementById(id).appendChild(inputFile);\n");
  echo("    input++;\n");
  echo("    espaco=document.createElement('span');\n");
  echo("    espaco.innerHTML='&nbsp;&nbsp;'\n");
  echo("    document.getElementById(id).appendChild(espaco);\n");
  echo("    createSpan = document.createElement('span');\n");
  echo("    createSpan.setAttribute('class', 'link');\n");
  echo("    createSpan.setAttribute('onClick', 'EdicaoArq(\''+id+'\', 1);');\n");
  echo("    createSpan.setAttribute('id', 'OKFile');\n");
  echo("    createSpan.innerHTML='OK';\n");
  echo("    document.getElementById(id).appendChild(createSpan);\n");  
  echo("    espaco=document.createElement('span');\n");
  echo("    espaco.innerHTML='&nbsp;&nbsp;'\n");
  echo("    document.getElementById(id).appendChild(espaco);\n");
  echo("    createSpan = document.createElement('span');\n");
  echo("    createSpan.setAttribute('class', 'link');\n");
  echo("    createSpan.setAttribute('onClick', 'EdicaoArq(\''+id+'\', 0);');\n");
  echo("    createSpan.setAttribute('id', 'CancFile');\n");
  echo("    createSpan.innerHTML='Cancelar';\n");
  echo("    document.getElementById(id).appendChild(createSpan);\n"); 
  //echo("    document.getElementById('OKFile').innerHTML=\"OK\";\n");
  //echo("    document.getElementById(id).innerHTML=\"<input type=file name=arquivo />&nbsp;&nbsp;<span class=\\\"link\\\" onClick=\\\"EdicaoArq('\"+id+\"', 1);\\\">OK</span>&nbsp;&nbsp;<span class=\\\"link\\\" onClick=\\\"EdicaoArq('\"+id+\"', 2);\\\">Cancelar</span>\";\n");
  echo("}\n");

  echo("  </script>\n");

  $objAjax->printJavascript("../xajax_0.2.4/");

  echo("  </head>\n");
  echo("  <body onLoad=\"Iniciar();\">\n");
  echo("    <a name=\"topo\"></a>\n");
  echo("    <h1><a href=\"home.htm\"><img src=\"../imgs/logo.gif\" border=\"0\" alt=\"TelEduc . Educa&ccedil;&atilde;o &agrave; Dist&acirc;ncia\" /></a></h1>\n");
  echo("    <table cellpadding=\"0\" cellspacing=\"0\" id=\"container\">\n");
  echo("	<tr>\n");
  echo("		<td></td>\n");
  echo("		<td valign=\"top\" id=\"topo\">\n");
  echo("		<!-- Navegacao Nivel 3 -->\n");
  echo("                  <ul id=\"nav3nivel\">\n");
  echo("                    <li class=\"visoes\"><a href=\"#\">Vis� do Formador</a></li>\n");
  echo("                    <li class=\"visoes\"><a href=\"#\">Vis� do Aluno</a></li>\n");
  echo("                    <li><a href=\"#\">Configura&ccedil;&atilde;o</a>&nbsp;&nbsp;|&nbsp;&nbsp;</li>\n");
  echo("                    <li><a href=\"#\">Suporte</a>&nbsp;&nbsp;|&nbsp;&nbsp;</li>\n");
  echo("                    <li><a href=\"#\">Administra&ccedil;&atilde;o</a></li>\n");
  echo("                  </ul>\n");
  echo("		  <div id=\"btsNivel3\"><span class=\"ajuste1\"><img src=\"../imgs/icAjuda.gif\" border=\"0\" alt=\"Ajuda\" /></span>&nbsp;&nbsp;<a href=\"#\">ajuda</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href=\"#\">X sair</a></div>\n");
  echo("		  <h3>CURSO DE TESTE DO AMBIENTE</h3>\n");
  echo("	        </td>\n");
  echo("	</tr>\n");
  echo("	<tr>\n");
  echo("	  <td width=\"140\" valign=\"top\">\n");
  echo("	     <!-- Navegacao Principal -->\n");
  echo("	     <ul id=\"nav\">\n");
  echo("                <li class=\"topLine\"><a href=\"#\">Din�ica do Curso</a></li>\n");
  echo("                <li><a href=\"#\">Agenda</a></li>\n");
  echo("                <li class=\"endLine\"><a href=\"#\">Avalia�es</a></li>\n");
  echo("                <li><a href=\"#\">Atividades</a></li>\n");
  echo("                <li><a href=\"#\">Material de Apoio</a></li>\n");
  echo("                <li><a href=\"#\">Leituras</a></li>\n");
  echo("                <li><a href=\"#\">Perguntas Frequentes</a></li>\n");
  echo("                <li><a href=\"#\">Exerc�ios</a></li>\n");
  echo("                <li><a href=\"#\">Parada Obrigat�ia</a></li>\n");
  echo("                <li class=\"endLine\"><a href=\"#\">Mural</a></li>\n");
  echo("                <li><a href=\"#\">F�uns de Discuss�</a></li>\n");
  echo("                <li><a href=\"#\">Bate-Papo</a></li>\n");
  echo("                <li class=\"endLine\"><a href=\"#\">Correio</a></li>\n");
  echo("                <li><a href=\"#\">Grupos</a></li>\n");
  echo("                <li><a href=\"#\">Perfil</a></li>\n");
  echo("                <li><a href=\"#\">Di�io de Bordo</a></li>\n");
  echo("                <li class=\"endLine\"><span class=\"link\" onclick=\"window.location='portfolio.php?cod_curso=".$cod_curso."&amp;cod_ferramenta=".$cod_ferramenta."&amp;exibir=myp';\">Portf�io</span></li>\n");
  echo("                <li><a href=\"#\">Acessos</a></li>\n");
  echo("                <li class=\"endLine\"><a href=\"#\">Intermap</a></li>\n");
  echo("              </ul>\n");
  echo("	    </td>\n");
  echo("	    <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  /* Verifica�o se o item est�em Edi�o */
  /* Se estiver, voltar a tela anterior, e disparar a tela de Em Edi�o... */
  $linha=RetornaUltimaPosicaoHistorico ($sock, $cod_item);
  if ($linha['acao']=="E")
  {
    /* Est�em edi�o... */
    echo("<script language=javascript>\n");
    echo("  window.open('em_edicao.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=ver&cod_topico_raiz=".$cod_topico_raiz."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."','EmEdicao','width=300,height=220,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
    echo("  document.location='portfolio.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_item=".$linha_item['cod_item']."&origem=ver&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."&cod_topico_raiz=".$cod_topico_raiz."';\n");
    echo("</script>\n");
    exit();
  }

  $eformador=EFormador($sock,$cod_curso,$cod_usuario);

  $dir_item_temp=CriaLinkVisualizar($sock, $cod_curso, $cod_usuario, $cod_item, $diretorio_arquivos, $diretorio_temp);

  $status_portfolio = RetornaStatusPortfolio ($sock, $cod_curso, $cod_usuario, $cod_usuario_portfolio, $cod_grupo_portfolio);

  $dono_portfolio    = $status_portfolio ['dono_portfolio'];
  $portfolio_apagado = $status_portfolio ['portfolio_apagado'];
  $portfolio_grupo   = $status_portfolio ['portfolio_grupo'];

  if ($acao=="mudarcomp" && $dono_portfolio)
  {
    MudarCompartilhamento($sock, $cod_item, $tipo_comp);
  }
  /* P�ina Principal */
//   $ferramenta_avaliacao = TestaAcessoAFerramenta($sock, $cod_curso, $cod_usuario, COD_AVALIACAO)
  $ferramenta_avaliacao = false;

  if ($ferramenta_avaliacao)
  {
    if($acao_portfolio_s=="G")
    {
      // ajuda para portfolio de grupos, ferramenta avaliacao ativada
      $cod_pagina = 24;
    }
    else
    {
      // ajuda para portfolio individual, ferramenta avaliacao ativada
      $cod_pagina = 20;
    }
  }
  else
  {
    if($acao_portfolio_s=="G")
    {
      // ajuda para portfolio de grupos, sem ferramenta avaliacao
      $cod_pagina = 11;
    }
    else
    {
      // ajuda para portolio individual, sem ferramenta avaliacao
      $cod_pagina = 5;
    }
  }

  if ($ferramenta_avaliacao)
  {
    if ($ferramenta_grupos_s){
        //acao_portfolio_s pode ser G (grupo), F (encerrados), M (pessoal)

        // 3 - Portfolios de grupos
        $cod_frase  =  3;
  
      //meu portfolio individual
      if (('M'== $acao_portfolio_s)&& (!$cod_grupo_portfolio)){
        $cod_frase=2;
      }
    }
    else
    {
      // 2 - Portfolios individual
      $cod_frase  =  2;
    }
  }
  else
  {
    if ($ferramenta_grupos_s){
        //acao_portfolio_s pode ser G (grupo), F (encerrados), M (pessoal)

        // 3 - Portfolios de grupos
        $cod_frase  =  3;
  
      //meu portfolio individual
      if (('M'== $acao_portfolio_s)&& (!$cod_grupo_portfolio)){
        $cod_frase=2;
      }
    }
    else
    {
      // 2 - Portfolios individual
      $cod_frase  =  2;
    }
  }

// // // //   /* Cabecalho */
// // // //   echo(PreparaCabecalho($cod_curso,$cabecalho,15,$cod_pagina));

  echo("<h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases, $cod_frase)."</h4>\n");
  echo("<span class=\"btsNav\" onClick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span>\n");

  $lista_topicos_ancestrais=RetornaTopicosAncestrais($sock, $cod_topico_raiz);
  unset($path);
  foreach ($lista_topicos_ancestrais as $cod => $linha)
  {
    if ($cod_topico_raiz!=$linha['cod_topico'])
    {
      $path="<a class=text href=\"portfolio.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_topico_raiz=".$linha['cod_topico']."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."\">".$linha['topico']."</a> &gt;&gt; ".$path;
    }
    else
    {
      $path="<b class=text>".$linha['topico']."</b><br>\n";
    }
  }

  if ($portfolio_grupo)
  {
    $nome=NomeGrupo($sock,$cod_grupo_portfolio);

    //Selecionando qual a figura a ser exibida ao lado do nome
    $fig_portfolio = "<img src=../imgs/portfolio_g_".($dono_portfolio  ? "p" : ( $portfolio_apagado ? "x" : "n") ).".gif border=0>";

    /* 84 - Grupo Exclu�o */
    if ($grupo_apagado && $eformador) $complemento=" <span>(".RetornaFraseDaLista($lista_frases,84).")</span>\n";

    echo("<a class=text href=# onClick=return(AbreJanelaComponentes(".$cod_grupo_portfolio."));>".$fig_portfolio." ".$nome."</a>".$complemento." - ");
    echo("<a href=# onMouseDown=\"MostraLayer(cod_topicos,0);return(false);\"><img src=\"../imgs/estruturag.gif\" border=0></a>");
  }
  else
  {
    $nome=NomeUsuario($sock,$cod_usuario_portfolio, $cod_curso);

    // Selecionando qual a figura a ser exibida ao lado do nome
    $fig_portfolio = "<img src=../imgs/portfolio_i_".($dono_portfolio  ? "p" : ( $portfolio_apagado ? "x" : "n") ).".gif border=0>";

    echo("<a class=text href=# onClick=return(AbrePerfil(".$cod_usuario_portfolio.")); class=text>".$fig_portfolio." ".$nome."</a>".$complemento." - ");
    echo("<a href=# onMouseDown=\"MostraLayer(cod_topicos,0);return(false);\"><img src=\"../imgs/estrutura.gif\" border=0></a>");
  }

  echo($path);

  echo("<!----------------- Tabelao ----------------->\n");
  echo("<table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
  echo("  <tr>\n");
  echo("    <!----------------- Botoes de Acao ----------------->\n");
  echo("      <td valign=\"top\">\n");
  echo("        <ul class=\"btAuxTabs\">\n");

   //174 - Meus portfolios 
  echo("        <li><span onClick=\"window.location='ver_portfolio.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&exibir=myp';\">".RetornaFraseDaLista($lista_frases,174)."</span></li>\n");    
  // 74 - Portfolios Individuais
  echo("        <li><span onClick=\"window.location='ver_portfolio.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&exibir=ind';\">".RetornaFraseDaLista($lista_frases,74)."</span></li>\n"); 
  // 75 - Portfolios de Grupos
  if ($ferramenta_grupos_s) {
  echo("        <li><span onClick=\"window.location='ver_portfolio.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&exibir=grp';\">".RetornaFraseDaLista($lista_frases,75)."</span></li>\n"); 
    // 177 - Portfolios encerrados
  echo("        <li><span onClick=\"window.location='ver_portfolio.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&exibir=enc';\">".RetornaFraseDaLista($lista_frases,177)."</span></li>\n"); 
  }
  echo("      </ul>\n");
  echo("    </td>\n");
  echo("  </tr>\n");
  echo("  <tr>\n");
  echo("    <td >\n");
  echo("        <ul class=\"btAuxTabs02\">\n");

  $cod_topico_raiz_usuario=RetornaPastaRaizUsuario($sock,$cod_usuario,"");

  unset($array_params);
  $array_params['cod_topico_raiz']       = $cod_topico_raiz;
  $array_params['cod_item']              = $cod_item;
  $array_params['cod_usuario_portfolio'] = $cod_usuario_portfolio;
  $array_params['cod_grupo_portfolio']   = $cod_grupo_portfolio;

  $EhAvaliacao=RetornaAssociacaoItemAvaliacao($sock,$cod_item);

/**************************************************************
arrumar
***************************************************************/
  if (count($EhAvaliacao)>0)
  {
    $dados=RetornaDadosAvaliacao($sock,$EhAvaliacao['cod_avaliacao']);
    $atividade=RetornaTituloAtividade($sock,$dados['cod_atividade']);
    /* 149 - Item associado a atividade: */
    print("<br><font class=text>".RetornaFraseDaLista($lista_frases,149)." </font><a class=text href=# onClick=\"window.open('../avaliacoes/ver_popup.php?&cod_curso=".$cod_curso."&cod_avaliacao=".$EhAvaliacao['cod_avaliacao']."','VerAvaliacao','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');EscondeLayers();return(false);\">".$atividade."</a><br>\n");
  }

// // //  MontaMenu($cod_curso, $acao_portfolio_s);

  // o tamanho da coluna 'ver outros itens' eh igual a 4, se for dono, ou a 2 se nao for
//   $num_colunas = ($dono_portfolio ? 4 : 2);

//   echo("<table border=0 width=100%>\n");

//   echo("  <tr class=menu2>\n");
  /*
    69 - Atualizar
    echo("    <td align=center><a class=menu2 href=\"ver.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_topico_raiz=".$cod_topico_raiz."&cod_item=".$cod_item."&time=".time()."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."\"><b>".RetornaFraseDaLista($lista_frases,69)."</b></a></td>\n");
  */
  /* 70 - Ver Outros Itens */
  echo("    <li><span onClick=\"window.location='portfolio.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_topico_raiz=".$cod_topico_raiz."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."';\">".RetornaFraseDaLista($lista_frases,70)."</span></li>\n");

/*
  echo("  </tr>\n");

  echo("  <tr class=menu2>\n");
*/

  //72 - Historico
  echo("    <li><span onClick=\"window.open('historico.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_item=".$cod_item."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."','Historico','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');return(false);\">".RetornaFraseDaLista ($lista_frases, 72)."</span></li>\n");
  if ($dono_portfolio)
  {
    if (count($EhAvaliacao)>0)
    {
      $foiavaliado=ItemFoiAvaliado($sock,$EhAvaliacao['cod_avaliacao'],$cod_item);
      $estahemavaliacao=ItemEmAvaliacao($sock,$EhAvaliacao['cod_avaliacao'],$cod_usuario_portfolio);
      if ((!$foiavaliado) && (!$estahemavaliacao))
      {
        // G 9 - Editar
        //echo("    <li><span onClick=\"window.location='editar_portfolio.php?".RetornaSessionID()."&origem=ver&cod_curso=".$cod_curso."&cod_topico=".$cod_topico_raiz."&cod_item=".$cod_item."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."';\">".RetornaFraseDaLista ($lista_frases_geral, 9)."</span></li>\n");
        // G 1 - Apagar
        //echo("    <li><span onClick=\"window.location='portfolio.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&acao=apagaritem&cod_item=".$cod_item."&cod_topico_raiz=".$cod_topico_raiz."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."';return(TemCertezaApagar());\">".RetornaFraseDaLista ($lista_frases_geral, 1)."</span></li>\n");

      }
    }
    else
    {
      // G 9 - Editar
      //echo("    <li><span onClick=\"window.location='editar_portfolio.php?".RetornaSessionID()."&origem=ver&cod_curso=".$cod_curso."&cod_topico=".$cod_topico_raiz."&cod_item=".$cod_item."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."';\">".RetornaFraseDaLista ($lista_frases_geral, 9)."</span></li>\n");
      // G 1 - Apagar
      //echo("    <li><span onClick=\"window.location='portfolio.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&acao=apagaritem&cod_item=".$cod_item."&cod_topico_raiz=".$cod_topico_raiz."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."';return(TemCertezaApagar());\">".RetornaFraseDaLista ($lista_frases_geral, 1)."</span></li>\n");
    }
    $num_colunas = 4;
  }
  // G 3 - Comentar
  echo("    <li><span onClick=\"return(OpenWindowComentar());\">".RetornaFraseDaLista ($lista_frases_geral, 3)."</span></li>\n");
  echo("  </ul>\n");
  echo("  </td>\n");
  echo("  </tr>\n");
  //echo("</table>\n");
//   echo("<tr>\n");

//   echo("<table  cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("  <tr>\n");
  echo("  <td>\n");
  echo("      <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("      <tr class=\"head\">\n");
  /* 41 - T�ulo */
  echo("    <td>".RetornaFraseDaLista($lista_frases,41)."</td>\n");
  // ?? - Op�es
  echo("    <td width=\"14%\" align=center>".htmlentities("Op�es")."</td>\n");
  /* 119 - Compartilhar */
  echo("    <td width=\"10%\" align=center>".RetornaFraseDaLista($lista_frases,119)."</td>\n");

  // se a ferramenta Avaliacoes estiver ativada, a tabela com os itens e pastas do portfolio tem 6 colunas, senao sao 5
  if ($ferramenta_avaliacao)
  {
    /* 139 - Avalia�o */
    echo("    <td width=\"8%\" align=center>".RetornaFraseDaLista($lista_frases,139)."</td>\n");
  }

  echo("  </tr>\n");

/**************************************************************
arrumar
***************************************************************/
//   echo("<script language=javascript>\n");
//   /* Abre a janela para o formador avaliar o aluno ou o grupo */
//   echo("  function AvaliaAlunos(cod_avaliacao)\n");
//   echo("  {\n");
//   echo("    window.open('../avaliacoes/avaliar_atividade.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno=".$cod_usuario_portfolio."&cod_item=".$cod_item."&portfolio_grupo=".$portfolio_grupo."&cod_grupo_portfolio=".$cod_grupo_portfolio."&VeioPeloPortfolio=1&cod_avaliacao='+cod_avaliacao,'AvaliarParticipante','width=600,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
//   echo("    return false;\n");
//   echo("  }\n");
// 
//   echo("</script>\n");

  $linha_item=RetornaDadosDoItem($sock, $cod_item);

  $titulo=$linha_item['titulo'];

  $texto="<span id=\"text_".$linha_item['cod_item']."\">".AjustaParagrafo($linha_item['texto'])."</span>";

  // G 9 - Editar
  $editar=RetornaFraseDaLista ($lista_frases_geral, 9);

    /* 12 - Totalmente Compartilhado */
  if ($linha_item['tipo_compartilhamento']=="T"){
    $compartilhamento=RetornaFraseDaLista($lista_frases,12);
  }
  /* 13 - Compartilhado com Formadores */
  else if ($linha_item['tipo_compartilhamento']=="F"){
    $compartilhamento=RetornaFraseDaLista($lista_frases,13);
  }
  /* 14 - Compartilhado com o Grupo */
  else if (($portfolio_grupo)&&($linha_item['tipo_compartilhamento']=="P")){
    $compartilhamento=RetornaFraseDaLista($lista_frases,14);
  }
  /* 15 - N� compartilhado */
  else if (!$portfolio_grupo){
    $compartilhamento=RetornaFraseDaLista($lista_frases,15);
  }

  // Marca se a linha cont� um item 'novo'
  if ($data_acesso<$linha_item['data']) $marcatr=" class=\"novoitem\"";
  else $marcatr="";

  $figura = "arquivo_";
  $figura.= ( $portfolio_grupo ? "g_" : "i_" );
  if ($portfolio_apagado)
  {
    $figura .= "x.gif";
  }
  else
  {
    if ($dono_portfolio)
    {
      $figura .= "p.gif";
    }
    else
    {
      $figura .= "n.gif";
    }
  }

  if ($linha_item['status']=="E"){

    $linha_historico=RetornaUltimaPosicaoHistorico($sock, $linha_item['cod_item']);

    if ($linha_item['inicio_edicao']<(time()-1800) || $cod_usuario == $linha_historico['cod_usuario'])
    {
      CancelaEdicao($sock, $linha_item['cod_item'], $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp, false, false, false);
      if ($dono_portfolio)
      {
        //se existe uma avalia�o ligada ao item
        if (is_array($lista))
        {
          $foiavaliado=ItemFoiAvaliado($sock,$lista['cod_avaliacao'],$linha_item['cod_item']);
          //talvez arrumar a funcao ItemFoiAvaliado, pois da forma que ta se o item tiver sido avaliado, mas tiver compartilhado so com
          //formadores, o aluno nao sabe que foi avaliado, mas nao consegue editar o item, o que fazer?

          // se foi avaliado n� pode editar o material
          if (!$foiavaliado){
            $titulo="<span id=\"tit_".$linha_item['cod_item']."\" class=\"linkTexto\" onclick=\"AlteraTitulo('".$linha_item['cod_item']."');\">".$linha_item['titulo']."</span>";
            $compartilhamento="<span id=\"comp_".$linha_item['cod_item']."\" class=\"link\" onclick=\"js_cod_item='".$linha_item['cod_item']."';AtualizaComp('".$linha_item['tipo_compartilhamento']."');MostraLayer(cod_comp,140);return(false);\">".$compartilhamento."</span>";
            $editar="<a href=\"#\" onClick=AlteraTexto(".$linha_item['cod_item'].");>Editar</a>";
          }
        }
        //else = n� existe uma avalia�o
        else {
          $titulo="<span id=\"tit_".$linha_item['cod_item']."\" class=\"linkTexto\" onclick=\"AlteraTitulo('".$linha_item['cod_item']."');\">".$linha_item['titulo']."</span>";
          $compartilhamento="<span id=\"comp_".$linha_item['cod_item']."\" class=\"link\" onclick=\"js_cod_item='".$linha_item['cod_item']."';AtualizaComp('".$linha_item['tipo_compartilhamento']."');MostraLayer(cod_comp,140);return(false);\">".$compartilhamento."</span>";
          $editar="<a href=\"#\" onClick=AlteraTexto(".$linha_item['cod_item'].");>Editar</a>";
        }
      }
    }
  }
  //else = item n� est�sendo editado
  else if (!(($ferramenta_avaliacao && is_array($lista) && ItemEmAvaliacao($sock,$lista['cod_avaliacao'],$cod_usuario_portfolio) && $dono_portfolio)))
  {
    if ($linha_item['status'] != "C")
    {
      if ($dono_portfolio)
      {
        if (is_array($lista))
        {
          $foiavaliado = ItemFoiAvaliado($sock,$lista['cod_avaliacao'],$linha_item['cod_item']);
          if (!$foiavaliado)
          {
            $titulo="<span id=\"tit_".$linha_item['cod_item']."\" class=\"linkTexto\" onclick=\"AlteraTitulo('".$linha_item['cod_item']."');\">".$linha_item['titulo']."</span>";
            $compartilhamento="<span id=\"comp_".$linha_item['cod_item']."\" class=\"link\" onclick=\"js_cod_item='".$linha_item['cod_item']."';AtualizaComp('".$linha_item['tipo_compartilhamento']."');MostraLayer(cod_comp,140);return(false);\">".$compartilhamento."</span>";
            $texto="<span id=\"text_".$linha_item['cod_item']."\" class=\"linkTexto\" onclick=\"AlteraTexto('".$linha_item['cod_item']."');\">".$texto."</span>";
          }
        }
        else
        {
          $titulo="<span border=1 id=\"tit_".$linha_item['cod_item']."\" class=\"linkTexto\" onclick=\"AlteraTitulo('".$linha_item['cod_item']."');\">".$linha_item['titulo']."</span>";
          $compartilhamento="<span id=\"comp_".$linha_item['cod_item']."\" class=\"link\" onclick=\"js_cod_item='".$linha_item['cod_item']."';AtualizaComp('".$linha_item['tipo_compartilhamento']."');MostraLayer(cod_comp,140);return(false);\">".$compartilhamento."</span>";
          $editar="<a href=\"#\" onClick=AlteraTexto(".$linha_item['cod_item'].");>Editar</a>";
        }
      }
    }
  }

  echo("  <tr>\n");
  echo("    <td class=\"itens\"><img src=\"../imgs/".$figura."\" border=0>".$titulo."</td>\n");

  echo("<td align=\"left\" valign=\"top\" class=\"botao2\">\n");
  echo("  <ul>\n");
  echo("    <li>".$editar."</li>\n");
  echo("    <li><a href=\"#\">Mover</a></li>\n");
  echo("    <li><a href=\"#\">Mudar Posi&ccedil;&atilde;o</a></li>\n");
  // G 1 - Apagar
  echo("    <li><a href=\"#\">".RetornaFraseDaLista ($lista_frases_geral, 1)."</a></li>\n");
  echo("  </ul>\n");
  echo("</td>\n");

  echo("<td align=center>".$compartilhamento."</td>\n");

  $Sim = RetornaFraseDaLista($lista_frases_geral, 35);

  if ($ferramenta_avaliacao)
  {
    echo("        <td align=center><span>");
    if (is_array($lista))
    {
      $foiavaliado=ItemFoiAvaliado($sock,$lista['cod_avaliacao'],$linha_item['cod_item']);
      if ($foiavaliado){
        if ($eformador){
          echo($Sim."</span><span class=\"avaliado\"> (a)\n");
        }
        //else = n� �formador
        else{
          $compartilhado=NotaCompartilhadaAluno($sock,$linha_item['cod_item'],$lista['cod_avaliacao'],$cod_grupo_portfolio,$cod_usuario);
          if ($compartilhado){
            echo($Sim."</span><span class=\"avaliado\"> (a)\n");
          }
          //else = n� �compartilhado
          else{
            echo($Sim);
          }
        }
       } 
       else{
         echo($Sim);
       }
      }
    //else = n� tem avalia�o
    else{
      // G 36 - N�
      echo("        ".RetornaFraseDaLista($lista_frases_geral, 36)."\n");
    }
  }

  if ($linha_item['texto']!="")
  {
    echo("<tr class=\"head\">\n");
    /* 42 - Texto  */
    echo("<td colspan=\"4\">".RetornaFraseDaLista($lista_frases,42)."</td>\n");
    echo("</tr>\n");
    echo("<tr>\n");
    echo("<td class=\"itens\" colspan=\"4\">\n");
    echo($texto."\n");
    echo "<noscript><p><b>Javascript must be enabled to use this form.</b></p></noscript>\n";
    echo("    </td>\n");
    echo("  </tr>\n");
  }

  $lista_arq=RetornaArquivosMaterialVer($cod_curso, $dir_item_temp['diretorio']);

  echo("  <tr class=\"head\">\n");
  /* 71 - Arquivos */
  echo("    <td colspan=4>".RetornaFraseDaLista($lista_frases,71)."</td>\n");
  echo("  </tr>\n");
  echo("  <tr>\n");
  echo("    <td class=\"itens\" colspan=\"4\" id=\"listFiles\">\n");

  if (count($lista_arq)>0)
  {
    // Procuramos na lista de arquivos se existe algum visivel
    $ha_visiveis = false;

    while (( list($cod, $linha) = each($lista_arq) ) && !$ha_visiveis)
    {
      if ($linha[Arquivo] != "")
        $ha_visiveis = !($linha['Status']);
    }

    if ($ha_visiveis)
    {

      foreach($lista_arq as $cod => $linha)
      {
        if (!($linha['Arquivo']=="" && $linha['Diretorio']==""))
          if (!$linha['Status'])
          {
            // Vamos exibir todos os arquivos em um mesmo nivel, como se nao houvesse pastas
            if ($linha[Arquivo] != "")
            {
              $caminho_arquivo = $dir_item_temp['link'].ConverteUrl2Html($linha['Diretorio']."/".$linha['Arquivo']);
              $tag_abre  = "<a class=text href=".$caminho_arquivo." onclick=\"WindowOpenVer('".$caminho_arquivo."');return(false);\">";
              $tag_fecha = "</a>";
              if (eregi(".zip$",$linha['Arquivo']))
              {
                // arquivo zip
                $imagem    = "<img src=../figuras/arqzip.gif border=0>";
              }
              else
              {
                // arquivo comum
                $imagem    = "<img src=../figuras/arqp.gif border=0>";
              }

/*
              $tmp = $tag_abre.$imagem.$linha[Arquivo].$tag_fecha;
              $tmp = ereg_replace("", "", $tmp);
              $tmp = ereg_replace("", "", $tmp);
 */
              echo($tag_abre.$imagem.$linha[Arquivo].$tag_fecha);
              echo("<br>\n");
            }
          }
      }
    }
  }
  echo("    </td>\n");
  echo("  </tr>\n");
  echo("  <tr>\n");
  echo("    <td align=left colspan=4>\n");
  echo("      <form name=formFiles id=formFiles action='ver_teste.php' method='POST' enctype=\"multipart/form-data\">\n");
  echo(         RetornaSessionIDInput());
  echo("        <input type='hidden' name='cod_curso' value='".$cod_curso."'>\n");
  echo("        <input type='hidden' name='cod_item' value='".$cod_item."'>\n");
  echo("        <input type='hidden' name='cod_topico_raiz' value='".$cod_topico_raiz."'>\n");
  echo("        <input type='hidden' name='cod_usuario_portfolio' value='".$cod_usuario_portfolio."'>\n");
  echo("        <input type='hidden' name='acao' value='anexar'>\n");
  /* 26 - Anexar arquivos (ger) */
  echo("    <img src=\"../imgs/paperclip.gif\" border=0><span class=\"link\" id =\"insertFile\" onClick=\"AcrescentarBarra('insertFile');\">".RetornaFraseDaLista($lista_frases_geral,26)."</span>\n");
  echo("      </form>\n");
  echo("    </td>\n");
  echo("  </tr>\n");

  $lista_url=RetornaEnderecosMaterial($sock, $cod_item);

  if (count($lista_url)>0)
  {

    echo("  <tr class=\"head\">\n");
    /* 44 - Endere�s */
    echo("    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,44)."</td>\n");
    echo("  </tr>\n");
    echo("  <tr>\n");
    echo("    <td class=\"itens\" colspan=\"4\">\n");

    foreach ($lista_url as $cod => $linha)
    {

      $linha['endereco'] = RetornaURLValida($linha['endereco']);
       
      if ($linha['nome']!="")
      {
        echo("  <font class=text><a href=# onClick=\"WindowOpenVerURL('".ConverteSpace2Mais($linha['endereco'])."');return(false);\">".$linha['nome']."</a>&nbsp;&nbsp;(".$linha['endereco'].")<font><br>\n");
      }
      else
      {
        echo("  <font class=text><a href=# onClick=\"WindowOpenVerURL('".ConverteSpace2Mais($linha['endereco'])."');return(false);\">".$linha['endereco']."</a></font><br>\n");
      }
    }

    echo("    </td>\n");
    echo("  </tr>\n");
    echo("</table>\n");

  }

  $ultimo_acesso=PenultimoAcesso($sock,$cod_usuario,"");
  $lista_comentario=RetornaComentariosDoItem($sock, $cod_item);

  if (count($lista_comentario)>0)
  {
    echo("<br><table border=0 width=100% cellspacing=0>\n");
    echo("  <tr>\n");
    /* 105 - Coment�ios */
    echo("    <td class=colorfield>".RetornaFraseDaLista($lista_frases,105)."</td>\n");
    /* 109 - Emissor */
    echo("    <td class=colorfield>".RetornaFraseDaLista($lista_frases,109)."</td>\n");
    echo("  </tr>\n");

    foreach ($lista_comentario as $cod => $linha)
    {
      echo("  <tr>\n");
      $bstt="";
      $bend="";
      if ($linha['data']>$ultimo_acesso)
      {
        $bstt="<b>";
        $bend="</b>";
      }
      echo("    <td>".$bstt."<a class=text href=# onClick=\"OpenWindowComentario(".$linha['cod_comentario']."); return(false);\">".RetornaFraseDaLista($lista_frases,108)." ".UnixTime2DataHora($linha['data']).$bend."</td>\n");
      echo("    <td>".$bstt."<a class=text href=# onClick=\"AbrePerfil(".$linha['cod_comentarista'].");return(false);\">".NomeUsuario($sock,$linha['cod_comentarista']).$bend."</td>\n");
      echo("  </tr>\n");
    }

    echo("</table>\n");

  }
  echo("</td>\n");
  echo("</tr>\n");
  echo("</table>\n");

  echo("    </td>\n");
  echo("  </tr>\n");
  echo("</table>\n");
  echo("	  </tr>\n");
  echo("	  <tr>\n");
  echo("	     <td valign=\"bottom\" height=\"80\"><img src=\"../imgs/logoNied.gif\" alt=\"nied\" border=\"0\" style=\"margin-right:8px;\" /> <img src=\"../imgs/logoInstComp.gif\" alt=\"Instituto de Computa&ccedil;&atilde;o\" border=\"0\" style=\"margin-right:6px;\" /> <img src=\"../imgs/logoUnicamp.gif\" alt=\"UNICAMP\" border=\"0\" /></td>\n");
  echo("	     <td valign=\"bottom\" id=\"rodape\">2006  - TelEduc - Todos os direitos reservados. All rights reserved - NIED - UNICAMP</td>\n");
  echo("	   </tr>\n");

  echo("</table>\n");

  include("layer.php");

  echo("</tr>\n");
  echo("</table>\n");
  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>
