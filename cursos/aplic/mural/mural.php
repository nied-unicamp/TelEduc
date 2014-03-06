<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/mural/mural.php

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

/*==========================================================
  ARQUIVO : cursos/aplic/mural/mural.php
  ========================================================== */

  $bibliotecas = "../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("mural.inc");

  require_once("../xajax_0.5/xajax_core/xajax.inc.php");
  
  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  //Registre os nomes das funções em PHP que você quer chamar através do xajax
  $objAjax->register(XAJAX_FUNCTION,"MudarConfiguracaoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"MostraMensagemDinamicMural");
  $objAjax->register(XAJAX_FUNCTION,"EditarTituloDinamic");
  $objAjax->register(XAJAX_FUNCTION,"DecodificaString");
  // Registra fun��es para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  $cod_ferramenta=8;
  $cod_ferramenta_ajuda=$cod_ferramenta;
  $cod_pagina_ajuda=1;
  include("../topo_tela.php");

  /* Dereferência as variáveis, senão ainda seria possível acessá-las */
  
  unset($array_mensagens_s);
  unset($sin_pag_s);
  /* Remove o contedo da sessão. */
  
  session_unregister('array_mensagens_s');
  session_unregister('sin_pag_s');

  $feedbackObject =  new FeedbackObject($lista_frases);
  $feedbackObject->addAction("nova_msg", 10, 11);
  $feedbackObject->addAction("apagarMuralAtual", 12, 13);

  $AcessoAvaliacao = TestaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,22);


  if ((!isset($todas_abertas)) || ($todas_abertas == '')) $todas_abertas = 0;
  /* Se o tipo de ordena�o n� foi especificada escolhe por padr� a            */
  /* ordena�o por data.                                                       */
  if ((!isset($ordem)) || ($ordem == "")) $ordem = 'data';

/* Define se existe mensagem no mural ou ainda está vazio.*/
  $lista_mensagens=ListaMensagens($sock, $ordem, $cod_curso);
  
  $ultimo_acesso=PenultimoAcesso($sock,$cod_usuario,"");
  

  $existe_mensagem=!empty($lista_mensagens);
  $total_mensagem=count($lista_mensagens);

  /* Obtém os dados do curso */
  $status_curso=RetornaStatusCurso($sock,$cod_curso);

  /* Verifica se o usuario eh formador. */
  $usr_visitante = EVisitante($sock, $cod_curso, $cod_usuario);
  $usr_colaborador = EColaborador($sock, $cod_curso, $cod_usuario);
  $usr_formador = EFormador($sock, $cod_curso, $cod_usuario);
  $usr_aluno = EAluno($sock, $cod_curso, $cod_usuario);

  /* Nmero de mensagens exibidas por p�ina.             */
  if (!isset($msg_por_pag)) $msg_por_pag = 10;

  /* Se o nmero total de mensagens for superior que o nmero de mensagens por  */
  /* p�ina ent� calcula o total de p�inas. Do contr�io, define o nmero de     */
  /* p�inas para 1.                                                           */

  /* Calcula o nmero de p�inas geradas.                                       */
  $total_pag = ceil($total_mensagem / $msg_por_pag);


  /* Se a p�ina atual n� estiver setada ent�, por padr�, atribui-lhe o valor 1. */
  /* Se estiver setada, verifica se a p�ina �maior que o total de p�inas, se for */
  /* atribui o valor de $total_pag �$pag_atual.                                    */
   if ((!isset($pag_atual))or($pag_atual=='')or($pag_atual==0))
     $pag_atual =  1;
   else $pag_atual = min($pag_atual, $total_pag);
 

  /* Se o status das mensagens a serem visualizadas não foi setado então   */
  /* lhe atribui o valor 'A' por padrão. Com isso, apenas os fóruns ativos */
  /* serão visualizados, e conseqentemente as mensagens ativas.           */
  if ((!isset($status))||($status != 'D'))
  {
    /* Especifica o status das MENSAGENS a serem listadas: A - Ativo      */
    /*                                                     D - Deletado   */
    /*                                                     X - Excluído   */
    $status = 'A';
  }

  if (!$usr_visitante){
    echo("    <script type=\"text/javascript\" src=\"../bibliotecas/ckeditor/ckeditor.js\"></script>");
    echo("    <script type=\"text/javascript\" src=\"../bibliotecas/ckeditor/ckeditor_biblioteca.js\"></script>");
  }
  
  echo("    <script type=\"text/javascript\">\n\n");
  echo("      function OpenWindowLink(status) \n");
  echo("      {\n");
  echo("        if(status == 1) ");
  echo("          window.open(\"imprimir_mural.php?&cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&ordem=".$_SESSION['ordem']."\",\"ImprimirDisplay\",\"width=600,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("        else");
  echo("          window.open(\"imprimir_mural.php?&cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&ordem=".$_SESSION['ordem']."\",\"ImprimirDisplay\",\"width=600,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("      }\n\n");
  echo("    </script>\n\n");
  
  echo("    <script type=\"text/javascript\">\n\n");
  echo("      var pag_atual = ".$pag_atual.";\n\n");
  echo("      var total_pag = ".$total_pag.";\n\n");
  // var todas_abertas = variavel de controle para saber se todas mensagens estão abertas
  echo("      var todas_abertas = ".$todas_abertas.";\n\n");
  echo("      var conteudo=\"\";\n");
  echo("      somador=0;\n");
  echo("      var mensagens_abertas=0;\n");
  /* (ger) 18 - Ok */
  // Texto do bot�o Ok do ckEditor
  echo("      var textoOk = '".RetornaFraseDaLista($lista_frases_geral, 18)."';\n\n");
  /* (ger) 2 - Cancelar */
  // Texto do bot�o Cancelar do ckEditor
  echo("      var textoCancelar = '".RetornaFraseDaLista($lista_frases_geral, 2)."';\n\n");


  echo("      function OpenWindowPerfil(id){\n");
  echo("        window.open(\"../perfil/exibir_perfis.php?");
  echo("cod_curso=".$cod_curso."&cod_aluno[]=\" + id, \"PerfilDisplay\",\"width=600,height=400,");
  echo("top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("        return(false);\n");
  echo("      }\n\n");
  echo("      var selected_item, mensagens_abertas=0;\n");

  echo("      function MudaOrdenacao(){\n");
  echo("        elementos = document.getElementById('ordem_msg');\n");
  echo("        var ordem;\n");
  echo("        for (var i = 0; i < elementos.length; i++)\n");
  echo("        {\n");
  echo("          if (elementos.options[i].selected == true){\n");
  echo("            ordem = elementos.options[i].value;\n");
  echo("            break;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        document.location = 'mural.php?cod_curso=".$cod_curso."&ordem='+ordem+'&todas_abertas='+todas_abertas;\n");
  echo("      }\n\n");

  echo("      var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
  echo("      var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n\n");

  echo("      if (isNav)\n");
  echo("      {\n");
  echo("        document.captureEvents(Event.MOUSEMOVE);\n");
  echo("      }\n");
  echo("      document.onmousemove = TrataMouse;\n\n");

  echo("      function TrataMouse(e)\n");
  echo("      {\n");
  echo("        Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
  echo("        Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
  echo("      }\n\n");

  echo("      function getPageScrollY()\n");
  echo("      {\n");
  echo("        if (isNav)\n");
  echo("          return(window.pageYOffset);\n");
  echo("        if (isIE)\n");
  echo("          return(document.body.scrollTop);\n");
  echo("      }\n\n");

  echo("      function AjustePosMenuIE()\n");
  echo("      {\n");
  echo("        if (isIE)\n");
  echo("          return(getPageScrollY());\n");
  echo("        else\n");
  echo("          return(0);\n");
  echo("      }\n\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        relevIni= getLayer(\"relev\");\n");
    
  echo("        EscondeLayers();\n");

  if (!$usr_visitante){
    if($existe_mensagem){
      echo("        CancelarNovaMsg();\n");
      echo("        ExibeMsgPagina(".$pag_atual.");\n");
    }else{
      echo("        ComporMensagem();\n");
    }
  }

  if(($todas_abertas==1) && ($existe_mensagem)){
    echo("        ExibirTodasMsgs();\n");
  }

  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("        startList();\n");
  echo("      }\n\n");


  echo("      function EscondeLayer(cod_layer)\n");
  echo("      {\n");
  echo("        hideLayer(cod_layer);\n");
  echo("      }\n\n");

  echo("      function EscondeLayers()\n");
  echo("      {\n");

  echo("      }\n\n");
  
  echo("      function MostraLayer(cod_layer, ajuste){\n");
  echo("        EscondeLayers();\n");
  echo("        moveLayerTo(cod_layer,Xpos-ajuste,Ypos+AjustePosMenuIE());\n");
  echo("        showLayer(cod_layer);\n");
  echo("      }\n\n");

   if ($existe_mensagem){
    echo("      function ExibeMsgPagina(pagina){\n");
    echo("        if (pagina==-1) return;\n");
    if($usr_formador){
      echo("        document.frmSelecao.cabecalho.checked=false;\n");
    }
    echo("        tabela = document.getElementById('tabelaMsgs');\n");
    echo("        if(!tabela) return;\n");
    echo("        inicio = (((pag_atual-1)*".$msg_por_pag.")*2)+1;\n");
    echo("        final = (((pag_atual)*".$msg_por_pag.")*2)+1;\n");
    echo("        for (i=inicio; i <= final; i++){\n");
    echo("          if (!tabela.rows[i]) break;\n");
    echo("          tabela.rows[i].style.display=\"none\";\n");
    if($usr_formador){
      echo("          e = document.frmSelecao.elements[i];\n");
      echo("          if(e){\n");
      echo("            e.checked = false;\n");
      echo("          }\n");
      echo("        }\n");
      echo("        document.frmSelecao.cabecalho.checked=false;\n");
    }else{
      echo("        }\n");
    }

    echo("        var browser=navigator.appName;\n\n");
    
    echo("        inicio = (((pagina-1)*".$msg_por_pag.")*2)+1;\n");
    echo("        final = ((pagina)*".$msg_por_pag.")*2;\n");
    echo("        iTmp = 0; contador=0;\n");
    echo("        for (i=inicio; i < final+1; i++){\n");
    echo("          if (!tabela.rows[i]){ iTmp=1; break;}\n");
    echo("          if(i%2!=0){\n");
    echo("            if (browser==\"Microsoft Internet Explorer\")\n");
    echo("              tabela.rows[i].style.display=\"block\";\n");
    echo("            else\n");
    echo("              tabela.rows[i].style.display=\"table-row\";\n");
    echo("        mensagens_abertas++;\n");
    echo("          }\n");
    echo("        }\n\n");
    
    echo("        document.getElementById('prim_msg_index').innerHTML=(inicio-1)/2 + 1;\n");
    echo("        if (!iTmp) document.getElementById('ult_msg_index').innerHTML=final/2;\n");
    echo("        else document.getElementById('ult_msg_index').innerHTML=(i-2)/2;\n\n");
    
    echo("        if (browser==\"Microsoft Internet Explorer\")\n");
    echo("          tabela.rows[tabela.rows.length-1].style.display=\"block\";\n");
    echo("        else\n");
    echo("          tabela.rows[tabela.rows.length-1].style.display=\"table-row\";\n");
    
    echo("        pag_atual=pagina;\n\n");
    
    echo("        if (pag_atual != 1){\n");
    echo("          document.getElementById('paginacao_first').onclick = function(){ ExibeMsgPagina(1); };\n");
    echo("          document.getElementById('paginacao_first').className = \"link\";\n");
    echo("          document.getElementById('paginacao_back').onclick = function(){ ExibeMsgPagina(pag_atual-1); };\n");
    echo("          document.getElementById('paginacao_back').className = \"link\";\n");
    echo("        }else{\n");
    echo("         document.getElementById('paginacao_first').onclick = function(){};\n");
    echo("         document.getElementById('paginacao_first').className = \"\";\n");
    echo("         document.getElementById('paginacao_back').onclick = function(){};\n");
    echo("         document.getElementById('paginacao_back').className = \"\";\n");
    echo("        }\n");
    echo("        document.getElementById('paginacao_first').innerHTML = \"&lt;&lt;\";\n");
    echo("        document.getElementById('paginacao_back').innerHTML = \"&lt;\";\n");
    echo("        inicio = pag_atual-2;\n");
    echo("        if (inicio<1) inicio=1;\n");
    echo("        fim = pag_atual+2;\n");
    echo("        if (fim>total_pag) fim=total_pag;\n");
    echo("        var controle=1;\n");
    echo("        var vetor= new Array();\n");
    echo("        for (j=inicio; j <= fim; j++){\n");
    echo("          // A página atual Não é exibida com link.\n");
    echo("          if (j == pag_atual){\n");
    echo("             document.getElementById('paginacao_'+controle).innerHTML='<b>['+j+']<\/b>';\n");
    echo("             document.getElementById('paginacao_'+controle).className='';\n");
    echo("             vetor[controle] = -1;\n");
    echo("          }else{\n");
    echo("             document.getElementById('paginacao_'+controle).innerHTML=j;\n");
    echo("             document.getElementById('paginacao_'+controle).className='link';\n");
    echo("             vetor[controle]=j;\n");
    echo("          }\n");
    echo("          controle++;\n");
    echo("        }\n");
    echo("        while (controle<=5){\n");
    echo("          document.getElementById('paginacao_'+controle).innerHTML='';\n");
    echo("          document.getElementById('paginacao_'+controle).className='';\n");
    echo("          document.getElementById('paginacao_'+controle).onclick= function() { };\n");
    echo("          controle++;\n");
    echo("        }\n");
    echo("        document.getElementById('paginacao_1').onclick=function(){ ExibeMsgPagina(vetor[1]); };\n");
    echo("        document.getElementById('paginacao_2').onclick=function(){ ExibeMsgPagina(vetor[2]); };\n");
    echo("        document.getElementById('paginacao_3').onclick=function(){ ExibeMsgPagina(vetor[3]); };\n");
    echo("        document.getElementById('paginacao_4').onclick=function(){ ExibeMsgPagina(vetor[4]); };\n");
    echo("        document.getElementById('paginacao_5').onclick=function(){ ExibeMsgPagina(vetor[5]); };\n\n");
  
    echo("        /* Se a página atual Não for a última página então cria um   \n");
    echo("           link para a próxima página */\n");
    echo("        if (pag_atual != total_pag){\n");
    echo("         document.getElementById('paginacao_fwd').onclick = function(){ ExibeMsgPagina(pag_atual+1); };\n");
    echo("         document.getElementById('paginacao_fwd').className = \"link\";\n");
    echo("         document.getElementById('paginacao_last').onclick = function(){ ExibeMsgPagina(".$total_pag."); };\n");
    echo("         document.getElementById('paginacao_last').className = \"link\";\n");
    echo("        }\n");
    echo("        else{\n");
    echo("         document.getElementById('paginacao_fwd').onclick = function(){};\n");
    echo("         document.getElementById('paginacao_fwd').className = \"\";\n");
    echo("         document.getElementById('paginacao_last').onclick = function(){};\n");
    echo("         document.getElementById('paginacao_last').className = \"\";\n");
    echo("        }\n");
    echo("        document.getElementById('paginacao_fwd').innerHTML = \"&gt;\";\n");
    echo("        document.getElementById('paginacao_last').innerHTML = \"&gt;&gt;\";\n");
    echo("      }\n");

    echo("      function AlternaMensagem(cod_mural)\n");
    echo("      {\n");
    echo("        var browser=navigator.appName;\n\n");
    echo("        var totalMsgs=document.getElementsByName('tr_msg').length;\n");
    echo("        var sts = document.getElementById('tr_msg_'+cod_mural).style.display;\n");
    echo("        if ((sts == 'table-row') || (sts == 'block'))\n");
    echo("        {\n");
    echo("          FecharMsg(cod_mural);");
    echo("        }");
    echo("        else\n");
    echo("        {\n");
    echo("          if (browser==\"Microsoft Internet Explorer\")\n");
    echo("            document.getElementById('tr_msg_'+cod_mural).style.display=\"block\";\n");
    echo("          else\n");
    echo("            document.getElementById('tr_msg_'+cod_mural).style.display=\"table-row\";\n");
    echo("        mensagens_abertas++;\n");
    echo("        }");
    echo("        if(totalMsgs <= 10){\n");
    echo("          VerificaAbertas();\n");
    echo("        }\n");
    echo("      }\n\n");


    echo("      function VerificaAbertas(){\n");
    echo("        tabela = document.getElementById('tabelaMsgs');\n");
    echo("        var totalMsgs=document.getElementsByName('tr_msg').length;\n");

    echo("        final = tabela.rows.length-1;\n");
    echo("        var cont = 0\n");
    echo("        var browser=navigator.appName;\n\n");
    // i = 1, para evitar problema com a primeira linha da tabela, que eh o cabecalho
    echo("        for (i=1; i<final; i++){\n");
    echo("          if (!tabela.rows[i]) break;\n");
    echo("          if (browser==\"Microsoft Internet Explorer\"){\n");
    echo("            if (tabela.rows[i].style.display == \"block\"){\n");
    echo("              cont++;\n");
    echo("            }\n");
    echo("          }else{\n");
    echo("            if(tabela.rows[i].style.display == \"table-row\"){\n");
    echo("           cont++;\n");
    echo("             }\n");
    echo("          }\n");
    echo("        }\n");
    echo("        if(cont == (totalMsgs*2)) {\n");

    echo("        controle=1;\n");
    echo("        while (controle<=5){\n");
    echo("          document.getElementById('paginacao_'+controle).innerHTML='';\n");
    echo("          document.getElementById('paginacao_'+controle).className='';\n");
    echo("          document.getElementById('paginacao_'+controle).onclick= function() { };\n");
    echo("          controle++;\n");
    echo("        }\n");
    echo("        document.getElementById('paginacao_first').onclick = function(){};\n");
    echo("        document.getElementById('paginacao_first').className = \"\";\n");
    echo("        document.getElementById('paginacao_first').innerHTML = \"\";\n");
    echo("        document.getElementById('paginacao_back').onclick = function(){};\n");
    echo("        document.getElementById('paginacao_back').className = \"\";\n");
    echo("        document.getElementById('paginacao_back').innerHTML = \"\";\n");
    echo("        document.getElementById('paginacao_fwd').onclick = function(){};\n");
    echo("        document.getElementById('paginacao_fwd').className = \"\";\n");
    echo("        document.getElementById('paginacao_fwd').innerHTML = \"\";\n\n");
    echo("        document.getElementById('paginacao_last').onclick = function(){};\n");
    echo("        document.getElementById('paginacao_last').className = \"\";\n");
    echo("        document.getElementById('paginacao_last').innerHTML = \"\";\n\n");

    /* 27 - Exibir por página */
    echo("         document.getElementById('exibir_paginacao').innerHTML = \"".RetornaFraseDaLista($lista_frases,27)."\";\n");
    echo("         document.getElementById('exibir_paginacao').onclick = function(){ VoltarPaginacao(pag_atual); };\n");
    echo("         mensagens_abertas=contador-1;\n");
    echo("         }\n");
    echo("      }\n");



    echo("      function FecharMsg(cod_mural){\n");
    echo("        tdElement= document.getElementById('td_msg_'+cod_mural);\n");
    echo("        divElement = document.getElementById('divNovaMsg');\n");
    echo("        var totalMsgs=document.getElementsByName('tr_msg').length;\n");
    echo("        if (tdElement.lastChild == divElement){\n");
    echo("          document.getElementById('divNovaMsg').className=\"divHidden\";");
    echo("          tdElement2 = document.getElementById('tdNovaMsg');\n");
    echo("          tdElement2.appendChild(document.getElementById('divNovaMsg'));\n");
    echo("          respondendoMsg = -1;\n");
    echo("        }\n");

    echo("        document.getElementById('tr_msg_'+cod_mural).style.display=\"none\";\n");
    echo("        mensagens_abertas--;\n");
   
    echo("        if(mensagens_abertas<totalMsgs){\n");
    /* 26 - Exibir todas */
    echo("         document.getElementById('exibir_paginacao').innerHTML = \"".RetornaFraseDaLista($lista_frases,26)."\";\n");
    echo("        document.getElementById('exibir_paginacao').onclick = function(){ ExibirTodasMsgs(); };\n");
    echo("         }\n");

    echo("        if(mensagens_abertas==0 && total_pag==1) VoltarPaginacao(pag_atual);\n");
    echo("      }\n\n");
   
    echo("      function ExibirTodasMsgs(){\n");
    echo("        tabela = document.getElementById('tabelaMsgs');\n");
    echo("        final = tabela.rows.length-1;\n");
    echo("        todas_abertas = 1;\n");
    echo("        var browser=navigator.appName;\n\n");
    echo("        contador=0;\n");
    echo("        for (i=0; i < final; i++){\n");
    echo("          if (!tabela.rows[i]) break;\n");
    echo("          if (browser==\"Microsoft Internet Explorer\"){\n");
    echo("            tabela.rows[i].style.display=\"block\";\n");
    echo("          }else{\n");
    echo("            tabela.rows[i].style.display=\"table-row\";\n");
    echo("          }\n");
    echo("          idTemp = tabela.rows[i].id.split('_');\n");
    echo("          if (idTemp[1] !='msg') contador++;\n");
    echo("        }\n\n");
  
    echo("        document.getElementById('prim_msg_index').innerHTML=1;\n");
    echo("        document.getElementById('ult_msg_index').innerHTML=contador-1;\n\n");
  
    echo("        controle=1;\n");
    echo("        while (controle <= 5){\n");
    echo("          document.getElementById('paginacao_'+controle).innerHTML='';\n");
    echo("          document.getElementById('paginacao_'+controle).className='';\n");
    echo("          document.getElementById('paginacao_'+controle).onclick= function() { };\n");
    echo("          controle++;\n");
    echo("        }\n");
    echo("         document.getElementById('paginacao_first').onclick = function(){};\n");
    echo("         document.getElementById('paginacao_first').className = \"\";\n");
    echo("         document.getElementById('paginacao_first').innerHTML = \"\";\n");
    echo("         document.getElementById('paginacao_back').onclick = function(){};\n");
    echo("         document.getElementById('paginacao_back').className = \"\";\n");
    echo("         document.getElementById('paginacao_back').innerHTML = \"\";\n");
    echo("         document.getElementById('paginacao_fwd').onclick = function(){};\n");
    echo("         document.getElementById('paginacao_fwd').className = \"\";\n");
    echo("         document.getElementById('paginacao_fwd').innerHTML = \"\";\n\n");
    echo("         document.getElementById('paginacao_last').onclick = function(){};\n");
    echo("         document.getElementById('paginacao_last').className = \"\";\n");
    echo("         document.getElementById('paginacao_last').innerHTML = \"\";\n");

    if ($usr_formador){
      echo("        var CabMarcado = false;\n");
      echo("        inicio=0;");
      echo("        final=document.getElementsByName('chk').length;");
      echo("        for(i = inicio; i < final; i++){\n");
      echo("          e = document.getElementsByName('chk')[i];\n");
      echo("          e.checked = CabMarcado;\n");
      echo("        document.frmSelecao.cabecalho.checked=CabMarcado;\n");
      echo("        ");
      echo("        }\n\n");
    }
    /* 27 - Exibir por página */
    echo("         document.getElementById('exibir_paginacao').innerHTML = \"".RetornaFraseDaLista($lista_frases,27)."\";\n");
    echo("         document.getElementById('exibir_paginacao').onclick = function(){ VoltarPaginacao(pag_atual); };\n");
    echo("         mensagens_abertas=contador-1;\n");
    echo("      }\n");

  
    echo("      function VoltarPaginacao(pagina){\n");
    echo("         todas_abertas=0;\n");
    echo("         spans = document.getElementsByTagName('span');\n");
    echo("         for (i=0; i < spans.length; i++){\n");
    echo("           if (spans[i].id.substr(0, 7).match(\"fechar_\")){\n");
    echo("             spans[i].onclick();\n");
    echo("           }\n");
    echo("         }\n");
    echo("        tabela = document.getElementById('tabelaMsgs');\n");
    echo("        final = tabela.rows.length-1;\n");
    echo("        for (i=1; i < final; i++){\n");
    echo("          if (!tabela.rows[i]) break;\n");
    echo("          tabela.rows[i].style.display=\"none\";\n");
    echo("        }\n\n");

      /* 26 - Exibir todas */
    echo("        document.getElementById('exibir_paginacao').innerHTML = \"".RetornaFraseDaLista($lista_frases,26)."\";\n");
    echo("        document.getElementById('exibir_paginacao').onclick = function(){ ExibirTodasMsgs(); };\n");
    echo("        ExibeMsgPagina(pagina);\n");
    echo("        mensagens_abertas=0;\n");
    echo("      }\n");

    if ($usr_formador){
      if ($status == 'A'){
  
        echo("      function ControlaSelecao(chkbox){\n");
        echo("        var conteudo;\n");
        echo("        var controle=0;\n");
        echo("        var j=0;\n");
        echo("        cabecalho = document.frmSelecao.cabecalho;\n");
        echo("        var elementos = document.getElementsByName('chk')\n");
  
        echo("        if(chkbox.checked){\n");
        echo("          for(i=0 ; i < elementos.length; i++){\n");
        echo("            if(elementos[i].checked){\n");
        echo("              j++\n");
        echo("            }\n");
  
        echo("          }\n");
        echo("          controle = (pag_atual-1)*10\n");
        echo("          controle = elementos.length - controle\n");
        echo("          if((j == 10) || (j == controle)) cabecalho.checked = true;\n");

        echo("        }else{\n");
        echo("          for(i=0 ; i < elementos.length; i++){\n");
        echo("            if(elementos[i].checked){\n");
        echo("              j++\n");
        echo("              break;\n");
        echo("            }\n");
        echo("          }\n");
        echo("          cabecalho.checked = false");
        echo("        }\n");
        echo("        if(j > 0){\n");
        echo("          document.getElementById('apagar_msg').className=\"menuUp02\";\n");
        echo("          document.getElementById('apagar_msg').onclick=function(){ApagarMsgSelecionadas(); };\n");
        echo("        } else{\n");
        echo("          document.getElementById('apagar_msg').className=\"menuUp\";\n");
        echo("          document.getElementById('apagar_msg').onclick=function(){  };\n");
        echo("        }\n");
        echo("      }\n");
      
        echo("      function MarcaOuDesmarcaTodos(pag_atual){\n");
        echo("        var e;\n");
        echo("        var i;\n");
        echo("        var inicio;\n");
        echo("        var final;\n");
        echo("        var elementos = document.getElementsByName('chk')\n");
        echo("        inicio = ((pag_atual-1)*10);\n");
        echo("        final = ((pag_atual)*10);\n");
        echo("        if(todas_abertas==1){\n");
        echo("          inicio=0;\n");
        echo("        final=document.getElementsByName('chk').length;}\n");
        echo("        controle = (pag_atual-1)*10;\n");
        echo("        controle = elementos.length - controle;\n");
        echo("        if(controle < final) {final = inicio + controle;}\n");
        echo("        var CabMarcado = document.frmSelecao.cabecalho.checked;\n");
        echo("        for(i = inicio; i < final; i++){\n");
        echo("         e = document.getElementsByName('chk')[i];\n");
        echo("         e.checked = CabMarcado;\n");
        echo("        }\n");
        echo("        if(CabMarcado){\n");
        echo("          document.getElementById('apagar_msg').className=\"menuUp02\";\n");
        echo("          document.getElementById('apagar_msg').onclick=function(){ApagarMsgSelecionadas(); };\n");
        echo("        }else{\n");
        echo("          document.getElementById('apagar_msg').className=\"menuUp\";\n");
        echo("          document.getElementById('apagar_msg').onclick=function(){  };\n");
        echo("        }\n");
        echo("      }\n");
  
        echo("      function ApagarAtual(cod_mural){\n");
        echo("        document.location='acoes.php?cod_curso=".$cod_curso."&acao=apagarMuralAtual&cod_mural='+cod_mural+'&pag_atual='+pag_atual+'&ordem=".$ordem."&todas_abertas='+todas_abertas;\n");
        echo("      }\n");

        echo("      function ApagarMsgSelecionadas(){\n");
        echo("        var j=0;\n");
        echo("        var elementos = document.getElementsByName('chk')\n");
        echo("        elementosSelecionados = new Array();\n");
        echo("          for(i=0 ; i < elementos.length; i++){\n");
        echo("            if(elementos[i].checked){\n");
        echo("              elementosSelecionados[j] = elementos[i].value\n");
        echo("              j++\n");
        echo("            }\n");
        echo("          }\n");
        echo("        if(confirm('".RetornaFraseDaLista($lista_frases,19)."'))");
        echo("        {\n");
        echo("        document.location='acoes.php?cod_curso=".$cod_curso."&acao=apagarMural&elementos='+elementosSelecionados+'&pag_atual='+pag_atual+'&ordem=".$ordem."&todas_abertas='+todas_abertas;\n");
        echo("        }\n");
        echo("      }\n");
      }
    }
  }

  /* Se usu�rio n�o � visitante, ou o curso ainda est� ativo ou ele �      */
  /* formador, permite escrita de mensagem.                                */
  
  if (!$usr_visitante) {
    if (!($status_curso=='E' && !$usr_formador)){
      echo("      function ComporMensagem(){\n");
      echo("        if(!isIE){\n");
      echo("          document.getElementById('divNovaMsg').className=\"\";\n");
      echo("        }else{\n");
      echo("          document.getElementById('trNovaMsg').style.display=\"\"; \n");
      echo("        }\n");
      echo("        document.getElementById('tdNovaMsg').style.background=\"white\";\n");
      echo("        document.getElementById('divNovaMsg').className=\"\";\n");
      echo("        document.getElementById('acao').value='nova_msg';\n");
      //echo("        document.getElementById('tdNovaMsg').style.width=\"525px\";\n");
      echo("        document.formCompor.msg_titulo.focus();\n");
      echo("      }\n\n");
      
      echo("      function CancelarNovaMsg(){\n");
      echo("        if(!isIE){\n");
      echo("          document.getElementById('divNovaMsg').className=\"divHidden\";\n");
      echo("        }else{\n");
      echo("          document.getElementById('trNovaMsg').style.display=\"none\"; \n");
      echo("        }\n");
      echo("        document.getElementById('tdNovaMsg').style.background=\"#DCDCDC\";\n");

      echo("        document.formCompor.msg_titulo.value='';\n");
      echo("        clearRTE('msg_corpo');\n");
      echo("        if (document.getElementById('spanRespondeMsg')){\n");
      echo("          tdElement = document.getElementById('spanRespondeMsg').parentNode;\n");
      echo("          tdElement.removeChild(document.getElementById('spanRespondeMsg'));\n");
      echo("        }\n");
      echo("        respondendoMsg = -1;\n");
      echo("      }\n\n");
    
      echo("      function TestaNome(form){\n");
      echo("        updateRTE('msg_corpo');\n");
      /* Elimina os espaços para verificar se o titulo nao eh formado por apenas espaços */
      echo("        Msg_nome = form.msg_titulo.value;\n");
      echo("        Msg_corpo = form.cke_msg_corpo.value;\n");
      echo("        while (Msg_nome.search(\" \") != -1){\n");
      echo("          Msg_nome = Msg_nome.replace(/ /, \"\");\n");
      echo("        }\n");
      echo("        if (Msg_nome == ''){\n");
      /* 7 - A mensagem deve ter um título. */
      echo("          alert('".RetornaFraseDaLista($lista_frases, 7)."');\n");
      echo("          document.formCompor.msg_titulo.focus();\n");
      echo("          return(false);\n");
      echo("        }else {\n");
      echo("          while (Msg_corpo.search(\" \") != -1){\n");
      echo("            Msg_corpo = Msg_corpo.replace(/ /, \"\");\n");
      echo("          }\n");
      echo("          if (Msg_corpo == ''){\n");
      /* 8 - A mensagem deve ter um conteúdo. */
      echo("            alert('".RetornaFraseDaLista($lista_frases, 8)."');\n");
      echo("            return(false);\n");
      echo("          }\n");
      echo("        }\n");
      echo("        return(true);\n");
      echo("      }\n\n");
    }
  }

  echo("    </script>\n\n");

  $objAjax->printJavascript();

  include("../menu_principal.php");
  
  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 1 - Mural */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,1));


  echo("</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
    echo("          <table border=\"0\" width=\"100%\" cellspacing=\"2\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  /* Se o usuario FOR Formador entao exibe os controles. */
  if (!$usr_visitante) {
    if (!($status_curso=='E' && !$usr_formador)){
      echo("                <ul class=\"btAuxTabs\">\n");
      /* Se estiver visualizando os fóruns disponíveis então cria um link para o */
      /* layer novo_forum e outro para a função JavaScript VerLixeira().         */
      if ($status == 'A')
      {
        /* 2 - * Nova mensagem */
        echo("                  <li><span onclick='ComporMensagem();'>".RetornaFraseDaLista($lista_frases,2)."</span></li>\n");
      }
    }
    if(isset($status)){
      echo("                  <li><a onclick=\"OpenWindowLink(1);\" href=\"#\">".RetornaFraseDaLista($lista_frases, 28)."</a></li>");
    }
    else{
      echo("                  <li><a onclick=\"OpenWindowLink(0);\" href=\"#\">".RetornaFraseDaLista($lista_frases, 28)."</a></li>");
    }
  }
  echo("                </ul>\n");
  /* Repassa o status da mensagens que ser� selecionadas: A ou D (Lixeira)            */
  /* Se o status for A ent� ser� listadas os f�uns e mensagens ativos, do contr�io */
  /* ficar� vis�eis os f�uns deletados e ser� listadas as mensagens apagadas.      */

  echo("              </td>\n");
  echo("            </tr>\n");


  /* Se o curso estiver encerrado e for um aluno, n�o pode inserir mensagens */
  /* Visitantes n�o podem postar mensagens.                                  */
  if (!$usr_visitante) {

    if (!($status_curso=='E' && !$usr_formador)){
      echo("            <tr id=\"trNovaMsg\">\n");
      echo("              <td colspan=\"5\">\n");
      echo("                <table border=\"0\" width=\"100%\" cellspacing=\"0\" class=\"tabInterna\" style=\"border-collapse:collapse;\">\n");
      echo("                  <tr>   \n");
      if($existe_mensagem){
        echo("                    <td id=\"tdNovaMsg\" align=\"left\" style=\"padding: 0 5px 0 5px;background-color:#DCDCDC;\">\n");
      }else{
        echo("                    <td id=\"tdNovaMsg\" align=\"left\" style=\"padding: 0 5px 0 5px;\" style=\"padding:0;\">\n");
      }
      echo("                      <div id=\"divNovaMsg\"><br />\n");
      echo("                        <form id=\"formCompor\" name=\"formCompor\" action=\"acoes.php?cod_curso=".$cod_curso."&amp;ordem=".$ordem."&amp;todas_abertas=".$todas_abertas."\" onsubmit=\"return(TestaNome(document.formCompor));\" method=\"post\" >\n");
      /* 3 - Título */
      echo("                          <b>".RetornaFraseDaLista($lista_frases,3)."</b><br />\n");
      echo("                          <input type=\"text\" id=\"msg_titulo\" name=\"msg_titulo\" size=\"40\" maxlength=\"100\" value='".$msg_titulo."' style=\"border: 2px solid #9bc;\" /><br /><br />\n");
      /* 21 - Mensagem */
      echo("                          <b>".RetornaFraseDaLista($lista_frases,21)."</b><br />\n");
      echo("                          <textarea name=\"msg_corpo\" style=\"width:90%;height:100px;\"></textarea>");
      /*echo("                          <script type=\"text/javascript\">\n");
      echo("                            writeRichText('msg_corpo', '', 600, 200, true, false, 1);\n");
      echo("                          </script>\n");*/
      echo("                          <script type=\"text/javascript\">\n");
      echo("                            CKEDITOR.replace( 'msg_corpo',
										    {		    });");
      echo("                          </script>\n");
      echo("                          <br />\n");
      echo("                          <input type=\"hidden\" name=\"acao\" id=\"acao\" value=\"nova_msg\" />\n");
      echo("                          <input type=\"hidden\" name=\"codRespondeMensagem\" id=\"codRespondeMensagem\" value=\"\" />\n");
      echo("                          <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
      echo("                          <input type=\"hidden\" name=\"cod_forum\" value=\"".$cod_mural."\" />\n");
      /* 18 - Ok */
      echo("                          <input type=\"submit\" class=\"input\" id=\"OKComent\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" style=\"margin-bottom:5px;\" />\n");
      /* 2 - Cancelar */
      echo("                          <input type=\"button\" class=\"input\" id=\"cancComent\" onclick=\"CancelarNovaMsg();\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" style=\"margin-bottom:5px;\" />\n");
      echo("                        </form>\n");
      echo("                      </div>\n");
      echo("                    </td>\n");
      echo("                    <td style=\"background-color:#DCDCDC;\"></td>\n");
      echo("                  </tr>\n");
      echo("                </table>\n");
      echo("              </td>\n");
      echo("            </tr>\n");
    }
  }


  if($existe_mensagem){

    // Calcula o índice da primeira mensagem.
    $prim_msg_index = (($pag_atual - 1) * $msg_por_pag) + 1;
    // Calcula o índice da última mensagem.
    $ult_msg_index = $pag_atual * $msg_por_pag;

    // Se o índice da ultima mensagem for maior que o número de mensagens, então copia este 
    // para o índice da última mensagem.
    if ($ult_msg_index > ($total_mensagem))
      $ult_msg_index = ($total_mensagem);
    echo("            <tr class=\"head01\">\n");
    echo("              <td>\n");
    /* 22 - Mensagens     */
    echo("                ".RetornaFraseDaLista($lista_frases, 22)." ");
    echo("(<span id=\"prim_msg_index\"></span>");
    /* 23 - a             */
    echo(" ".RetornaFraseDaLista($lista_frases, 23)."&nbsp;");
    /* 24 - de            */
    echo("<span id=\"ult_msg_index\"></span> ".RetornaFraseDaLista($lista_frases, 24)." ");
    echo(($total_mensagem).")\n");
    echo("              </td>\n");

    echo("              <td width=\"40%\" align=\"right\">\n");
    /* 25 - Ordenar por:  */
    echo("                  <span>".RetornaFraseDaLista($lista_frases, 25)."</span>\n");
    
    $select[$ordem]='selected';
  
    echo("                <select class=\"input\" name=\"ordem\" id=\"ordem_msg\" onchange='MudaOrdenacao();'>\n");
    /* 5 - data */
    echo("                  <option value=\"data\" ".$select['data'].">".RetornaFraseDaLista($lista_frases, 5)."</option>\n");
    /* 3 - título */
    echo("                  <option value=\"titulo\" ".$select['titulo'].">".RetornaFraseDaLista($lista_frases, 3)."</option>\n");
    /* 4 - Emissor */
    echo("                  <option value=\"nome\" ".$select['nome'].">".RetornaFraseDaLista($lista_frases, 4)."</option>\n");
    echo("                </select>\n");
    echo("              </td>\n");
    echo("            </tr>\n");

  }
  echo("            <tr>\n");
  echo("              <td colspan=\"2\">\n");
  echo("                <form name=\"frmSelecao\" action=\"\">\n");
  echo("                  <input type=\"hidden\" name=\"Selecionados\" value=\"''\" />\n");
  echo("                  <table border=\"0\" width=\"100%\" cellspacing=\"0\" style=\"cellspadding:0pt;\" class=\"tabInterna\" id=\"tabelaMsgs\">\n");
  echo("                      <tr class=\"head\">\n");
  if($usr_formador)
     /* Checkbox que controla a selecao ou nao de todos os itens do mural */
    echo("                        <td width=\"9\"><input type=\"checkbox\" name=\"cabecalho\" onclick=\"MarcaOuDesmarcaTodos(pag_atual);\" /></td>\n");

  echo("                        <td width=\"2%\">#</td>\n");
  /* 3 - T�ulo */
  echo("                        <td class=\"alLeft\">".RetornaFraseDaLista($lista_frases, 3)."</td>\n");
  /* 4 - Emissor */
  echo("                        <td width=\"25%\">".RetornaFraseDaLista($lista_frases, 4)."</td>\n");
  /* 5 - Data */
  echo("                        <td width=\"15%\">".RetornaFraseDaLista($lista_frases, 5)."</td>\n");
  echo("                      </tr>\n");


 // $lista_mensagens=ListaMensagens($sock);
  $num_msg_pag=0;
  $num_msg = 0;
  if(!($existe_mensagem)){
    
    echo("                      <tr>\n");
    echo("                        <td colspan=\"5\">\n");
    /* 14 - N�o existem mensagens no mural. */
    echo("                          ".RetornaFraseDaLista($lista_frases, 14));
    echo("                        </td>\n");
    echo("                      </tr>\n");
  
  }else{
  
  
    $num_pagina = 1;

    foreach($lista_mensagens as $cod_msg => $dados){
      $cod_mural = $dados['cod_mural'];
      if($num_msg_pag == $msg_por_pag){
        $num_pagina++;
        $num_msg_pag =0;
      }

      if ($num_pagina == $pag_atual) $style = "";

      else $style = "display:none";

      $status = $dados['status'];
      
      /* status = A (Incluido) */
      /* status = X (Deletado) */
      /* Se a mensagem nao tiver sido deletada exibe-a */
      if ($status == "A"){
      /* Lembrar de por Perfil */
      /* Retorna o nome do usuario correspodente ao cod_usuario */
        $nome_usuario = $dados['nome'];
        
        if ($dados['cod_usuario']>0)
          $nome_usuario="<span id=\"emissor_".$cod_mural."\" class=\"link\" onclick='OpenWindowPerfil(".$dados['cod_usuario'].");'>". $nome_usuario."</span>";
      
        $titulo=LimpaTitulo($dados['titulo']);
        $titulo="<span id=\"titulo_".$cod_mural."\" class=\"link\" onclick='AlternaMensagem(".$cod_mural.");'>". $titulo."</span>";
        /* Retorna a data da mensagem */
        $data = UnixTime2DataHora($lista_mensagens[$num_msg]['data']);
        $dataaux = explode(" ",$data);
        $data = $dataaux[0] . "<br/>" . $dataaux[1];
              //echo(" data: ".$data[1] ."-". $data[0]." - ultimo: ".UnixTime2DataHora($ultimo_acesso)."<br>");
        
		$estilo = ( $lista_mensagens[$num_msg]['data'] > $ultimo_acesso ? "novo" : "antigo");
  
        /* Cria a linha da tabela */
        /* O uso do &nbsp; entre a tag <A></A> faz-se necessario para    */
        /* impedir que o usuario insira um titulo no formato <TAG></TAG> */
        /* o qual nao exibir� o t�tulo.                                  */
  
        echo("                      <tr id=\"tr_".$cod_mural."\" style=\"".$style."\" class=\"altColor".($num_msg%2)."\">\n");
        if($usr_formador){
          echo("                        <td width=\"1%\" class=\"wtfield\"><input type=\"checkbox\" name=\"chk\" value=\"".$cod_mural."\" onclick=\"ControlaSelecao(this);\" /></td>\n");

        }
        
        echo("                        <td width=\"2%\">". ($num_msg+1) .".</td>\n");
        echo("                        <td class=\"alLeft ".$estilo."\">".$titulo."</td>\n");
        echo("                        <td width=\"25%\" class=\"$estilo\">".$nome_usuario."</td>\n");
        echo("                        <td width=\"15%\" class=\"$estilo\">".$data."</td>\n");
        echo("                      </tr>\n");

        echo("                      <tr style=\"display:none;\" id=\"tr_msg_".$cod_mural."\" name=\"tr_msg\">");
        if($usr_formador){
          echo("                        <td colspan=\"2\"></td>\n");
        }else{
          echo("                        <td></td>\n");
        }
        echo("                        <td colspan=\"2\" id=\"td_msg_".$cod_mural."\" align=\"left\">\n");
        /* 21 - Mensagem */
        echo("                          <b>".RetornaFraseDaLista($lista_frases, 21).":</b><br /><br />\n");
        echo("                          <div class=\"divRichText\" style=\"overflow:auto;\">". PreparaExibicaoMensagem($dados['texto'])."</div>\n");
        echo("                        </td>\n");
        echo("                        <td id=\"td_close".$cod_mural."\">\n");
        /* 13 - Fechar */
        echo("                          <span class=\"link\" id=\"fechar_".$cod_mural."\" onclick=\"FecharMsg(".$cod_mural.");\">".RetornaFraseDaLista($lista_frases_geral, 13)."</span><br/>\n");
        /* 1 - Apagar */
        if($usr_formador){
          echo("                          <span class=\"link\" id=\"apagar_msg_".$cod_mural."\" onclick=\"ApagarAtual(".$cod_mural.");\">".RetornaFraseDaLista($lista_frases_geral, 1)."</span>\n");
        }
        echo("                        </td>\n");
        echo("                      </tr>\n");

        }
      $num_msg++;
      $num_msg_pag++;
    }
  }
  

  echo("                    <tr>\n");
  echo("                      <td colspan=\"5\" align=\"right\">\n");
  echo("                        <span id=\"paginacao_first\"></span> <span id=\"paginacao_back\"></span>\n");
  $controle=1;
  while ($controle<=5){
    echo("                        <span id=\"paginacao_".$controle."\"></span>\n");
    $controle++;
  }
  echo("                        <span id=\"paginacao_fwd\"></span> <span id=\"paginacao_last\"></span>\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");
  echo("                  </table>\n");
  echo("                </form>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul>\n");
  

     /* 1 - Apagar */
  if($usr_formador){
    echo("                  <li class=\"menuUp\" id=\"apagar_msg\" onclick=\"\"><span>".RetornaFraseDaLista($lista_frases_geral, 1)."</span></li>\n");
  }
  if ($existe_mensagem){
    // Se houver mensagens cria o botão para exibir todas as mensagens.
    /* 26 - Exibir todas */
    echo("                  <li class=\"menuUp02\" ><span id=\"exibir_paginacao\"  onclick=\"ExibirTodasMsgs();\">".RetornaFraseDaLista($lista_frases, 26)."</span></li>\n");
  }
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");

  echo("        </td>\n");
   
  echo("      </tr>\n");

  include("../tela2.php");

  echo("  </body>\n");
  echo("</html>");

  Desconectar($sock);


?>

