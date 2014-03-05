<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/forum/forum.php

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
  ARQUIVO : cursos/aplic/correio2/correio.php
  ========================================================== */

  $bibliotecas = "../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("correio.inc");
  
  require_once("../xajax_0.5/xajax_core/xajax.inc.php");
  
  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  //Registre os nomes das funções em PHP que você quer chamar através do xajax
  $objAjax->register(XAJAX_FUNCTION,"trocaEstadoMsg");
  $objAjax->register(XAJAX_FUNCTION,"VerificaMsgNova");
  $objAjax->register(XAJAX_FUNCTION,"RemoveLinkSimbolico");
  // Registra fun��es para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  $cod_ferramenta = 11;
  $cod_ferramenta_ajuda = 11;
  $cod_pagina_ajuda = 1;
  include("../topo_tela.php");

  $modoVisualizacao = $_GET['modoVisualizacao'];

  $feedbackObject = new FeedbackObject($lista_frases);

  if($_GET['rec'] == "rec")
    $feedbackObject->addAction("atualizarPag",RetornaFraseDalista($lista_frases,137)." ".RetornaFraseDalista($lista_frases,139),255);
  else if($modoVisualizacao == "L")
    $feedbackObject->addAction("atualizarPag", RetornaFraseDalista($lista_frases,137)." ".RetornaFraseDalista($lista_frases,138),255);
  else
    $feedbackObject->addAction("atualizarPag", RetornaFraseDalista($lista_frases,137)." ".RetornaFraseDalista($lista_frases,140),255);

  /* Desreferência as variáveis, senão ainda seria possível acessá-las */  
  unset($array_mensagens_s);
  unset($sin_pag_s);

  /* Remove o contedo da sessão. */
  session_unregister('array_mensagens_s');
  session_unregister('sin_pag_s');

  if ((!isset($todas_abertas)) || ($todas_abertas == '')) $todas_abertas = 0;
  /* Se o tipo de ordena�o n� foi especificada escolhe por padr� a            */
  /* ordena�o por data.                                                       */
  if ((!isset($ordem)) || ($ordem == "")) $ordem = 'data';
  
  $modoVisualizacao = $_GET['modoVisualizacao'];
  //$modoVisualizacao:
  //'R' - Ver Mensagens RECEBIDAS
  //'E' - Ver Mensagens ENVIADAS
  //'L' - Ver Mensagens da LIXEIRA
  if (($modoVisualizacao != 'R') && ($modoVisualizacao != 'E') && ($modoVisualizacao != 'L'))
    $modoVisualizacao = 'R';


  /* Verifica se o usuario eh formador. */
  $usr_colaborador = EColaborador($sock, $cod_curso, $cod_usuario);
  $usr_visitante   = EVisitante($sock, $cod_curso, $cod_usuario);
  $usr_formador    = EFormador($sock, $cod_curso, $cod_usuario);
  $usr_aluno       = EAluno($sock, $cod_curso, $cod_usuario);

   if($modoVisualizacao == 'R')
     $listaMsg = RetornaListaMensagensRecebidas2($sock,$cod_usuario,$ordem);
   else if($modoVisualizacao == 'E')
     $listaMsg = RetornaListaMensagensEnviadas2($sock,$cod_usuario,$ordem, $lista_frases, $cod_curso);
   else if($modoVisualizacao == 'L')
     $listaMsg = RetornaListaMensagensLixeira2($sock, $cod_usuario, $ordem, $lista_frases, $cod_curso);

  if($listaMsg != "")
    $totalMsg = count($listaMsg);
  else
    $totalMsg = 0;

  /* Nmero de mensagens exibidas por p�ina.             */
  if (!isset($msgPorPag)) $msgPorPag = 10;

  /* Se o nmero total de mensagens for superior que o nmero de mensagens por  */
  /* p�ina ent� calcula o total de p�inas. Do contr�io, define o nmero de     */
  /* p�inas para 1.                                                           */

  /* Calcula o nmero de p�inas geradas.    */
  if($totalMsg > $msgPorPag)
    $totalPag = ceil($totalMsg / $msgPorPag);
  else
    $totalPag = 1;

  /* Se a p�ina atual n� estiver setada ent�, por padr�, atribui-lhe o valor 1. */
  /* Se estiver setada, verifica se a p�ina �maior que o total de p�inas, se for */
  /* atribui o valor de $total_pag �$pagAtual.                                    */
  if ((!isset($pagAtual))or($pagAtual=='')or($pagAtual==0))
    $pagAtual =  1;
  else $pagAtual = min($pagAtual, $totalPag);

  if($totalMsg){
    echo("    <script type=\"text/javascript\" src=\"../js-css/sorttablePaginado.js\"></script>\n");
  }
  echo("    <script type=\"text/javascript\">\n\n");
  echo("      var pagAtual = ".$pagAtual.";\n");
  echo("      var totalMensagem = ".$totalMsg.";\n");
  echo("      var totalPag = ".$totalPag.";var teste;\n");
  echo("      var window_handle;\n\n");

  echo("      this.name = 'principal';\n\n");

  echo("      function Iniciar(event){\n");
  echo("        ExibeMsgPagina(".$pagAtual.");\n");
  echo("        var date = new Date();\n");
  echo("        clock = date.getTime();\n");
  echo("        setTimeout(\"VerificaMsgNova(clock)\", 60000);\n");
  echo("        startList();\n");

  echo("        var atualizacao = '".$_GET['atualizacao']."';\n");

  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);

  echo("      }\n\n");

  echo("      function OpenWindowPerfil(id){\n");
  echo("        window.open(\"../perfil/exibir_perfis.php?");
  echo("cod_curso=".$cod_curso."&cod_aluno[]=\" + id, \"PerfilDisplay\",\"width=600,height=400,");
  echo("top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("      }\n\n");

  echo("      function VerificaMsgNova(clock){\n");
  echo("        xajax_VerificaMsgNova(". $cod_curso.", ".$cod_usuario.", (clock - 60000));\n");
  echo("        t = setTimeout(\"VerificaMsgNova(clock)\", 60000);\n");
  echo("      }\n\n");

  echo("      function ExibeMsgPagina(pagina){\n");
  echo("        var i = 0;\n");
  echo("        if (pagina==-1) return;\n");
  echo("        document.frmSelecao.cabecalho.checked=false;\n");
  echo("        tabela = document.getElementById('tabelaMsgs');\n");
  echo("        if(!tabela) return;\n");
  echo("        if(pagAtual != pagina){\n");
  echo("          inicio = ((pagAtual-1)*".$msgPorPag.")+1;\n");
  echo("          final = ((pagAtual)*".$msgPorPag.")+1;\n");
  echo("          for (i=inicio; i < final; i++){\n");
  echo("            if (!tabela.rows[i]) break;\n");
  echo("            tabela.rows[i].style.display=\"none\";\n");
  echo("            e = document.frmSelecao.elements[i];\n");
  echo("            if(e){ e.checked = false;\n");
  echo("              ControlaSelecao(e);}\n");
  echo("            if((tabela.rows[i].id).split('_')[1] == 'msg'){\n");
  echo("              tableElement = tabela.rows[i].parentNode;\n");
  echo("              tableElement.removeChild(tabela.rows[i]);\n");
  echo("              i--\n");
  echo("            }\n");
  echo("          }\n");
  echo("        }\n\n");

  echo("        var browser=navigator.appName;\n\n");
  echo("        inicio = ((pagina-1)*".$msgPorPag.")+1;\n");
  echo("        final = ((pagina)*".$msgPorPag.");\n");
  echo("        contador=0;\n");
  echo("        for (i=inicio; i < final+1; i++){\n");
  echo("          if (!tabela.rows[i+1]){break;}\n");

  echo("          if (browser==\"Microsoft Internet Explorer\")\n");
  echo("            tabela.rows[i].style.display=\"block\";\n");
  echo("          else\n");
  echo("            tabela.rows[i].style.display=\"table-row\";\n");
  echo("          tabela.rows[i].className = 'altColor'+((i+1)%2);");
  echo("        }\n\n");
  echo("        document.getElementById('primMsgIndex').innerHTML=inicio;\n");
  echo("        document.getElementById('ultMsgIndex').innerHTML=(i-1);\n\n");

  echo("        if (browser==\"Microsoft Internet Explorer\")\n");
  echo("          tabela.rows[tabela.rows.length-1].style.display=\"block\";\n");
  echo("        else\n");
  echo("          tabela.rows[tabela.rows.length-1].style.display=\"table-row\";\n");

  echo("        pagAtual=pagina;\n\n");

  echo("        if (pagAtual != 1){\n");
  echo("          document.getElementById('paginacao_first').onclick = function(){ ExibeMsgPagina(1); };\n");
  echo("          document.getElementById('paginacao_first').className = \"link\";\n");
  echo("          document.getElementById('paginacao_back').onclick = function(){ ExibeMsgPagina(pagAtual-1); };\n");
  echo("          document.getElementById('paginacao_back').className = \"link\";\n");
  echo("        }else{\n");
  echo("          document.getElementById('paginacao_first').onclick = function(){};\n");
  echo("          document.getElementById('paginacao_first').className = \"\";\n");
  echo("          document.getElementById('paginacao_back').onclick = function(){};\n");
  echo("          document.getElementById('paginacao_back').className = \"\";\n");
  echo("        }\n");
  echo("        document.getElementById('paginacao_first').innerHTML = \"&lt;&lt;\";\n");
  echo("        document.getElementById('paginacao_back').innerHTML = \"&lt;\";\n");
  echo("        inicio = pagAtual-2;\n");
  echo("        if (inicio < 1) inicio=1;\n");
  echo("        fim = pagAtual+2;\n");
  echo("        if (fim > totalPag) fim=totalPag;\n");
  echo("        var controle=1;\n");
  echo("        var vetor= new Array();\n");
  echo("        for (j=inicio; j <= fim; j++){\n");
  echo("          // A página atual Não é exibida com link.\n");
  echo("          if (j == pagAtual){\n");
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
  echo("        if (pagAtual != totalPag){\n");
  echo("          document.getElementById('paginacao_fwd').onclick = function(){ ExibeMsgPagina(pagAtual+1); };\n");
  echo("          document.getElementById('paginacao_fwd').className = \"link\";\n");
  echo("          document.getElementById('paginacao_last').onclick = function(){ ExibeMsgPagina(totalPag); };\n");
  echo("          document.getElementById('paginacao_last').className = \"link\";\n");
  echo("        }\n");
  echo("        else{\n");
  echo("          document.getElementById('paginacao_fwd').onclick = function(){};\n");
  echo("          document.getElementById('paginacao_fwd').className = \"\";\n");
  echo("          document.getElementById('paginacao_last').onclick = function(){};\n");
  echo("          document.getElementById('paginacao_last').className = \"\";\n");
  echo("        }\n");
  echo("        document.getElementById('paginacao_fwd').innerHTML = \"&gt;\";\n");
  echo("        document.getElementById('paginacao_last').innerHTML = \"&gt;&gt;\";\n");
  echo("      }\n");

  echo("      function AuxiliaPaginacao(){\n");
  echo("        var i = 0;\n");
  echo("        document.frmSelecao.cabecalho.checked=false;\n");
  echo("        tabela = document.getElementById('tabelaMsgs');\n");
  echo("        if(!tabela) return;\n");
  echo("        inicio = 1;\n");
  echo("        final = ((totalPag)*".$msgPorPag.")+1;\n");
  echo("        for (i=inicio; i < final; i++){\n");
  echo("          if (!tabela.rows[i]) break;\n");
  echo("          tabela.rows[i].style.display=\"none\";\n");
  echo("          e = document.frmSelecao.elements[i];\n");
  echo("          if (e){ e.checked = false;\n");
  echo("            ControlaSelecao(e);}\n");
  echo("          if((tabela.rows[i].id).split('_')[1] == 'msg'){\n");
  echo("            tableElement = tabela.rows[i].parentNode;\n");
  echo("            tableElement.removeChild(tabela.rows[i]);\n");
  echo("            i--}\n");
  echo("        }\n\n");
  echo("        var browser=navigator.appName;\n\n");
  echo("        pagina = 1;\n");
  echo("        inicio = ((pagina-1)*".$msgPorPag.")+1;\n");
  echo("        final = ((pagina)*".$msgPorPag.");\n");
  echo("        contador=0;\n");
  echo("        for (i=inicio; i < final+1; i++){\n");
  echo("          if (!tabela.rows[i+1]){break;}\n");

  echo("          if (browser==\"Microsoft Internet Explorer\")\n");
  echo("            tabela.rows[i].style.display=\"block\";\n");
  echo("          else\n");
  echo("            tabela.rows[i].style.display=\"table-row\";\n");
  echo("          tabela.rows[i].className = 'altColor'+((i+1)%2);");
  echo("        }\n\n");
  echo("        document.getElementById('primMsgIndex').innerHTML=inicio;\n");

  echo("        document.getElementById('ultMsgIndex').innerHTML=(i-1);\n\n");

  echo("        if (browser==\"Microsoft Internet Explorer\")\n");
  echo("          tabela.rows[tabela.rows.length-1].style.display=\"block\";\n");
  echo("        else\n");
  echo("          tabela.rows[tabela.rows.length-1].style.display=\"table-row\";\n");

  echo("        if (browser==\"Microsoft Internet Explorer\")\n");
  echo("          tabela.rows[tabela.rows.length-1].style.display=\"block\";\n");
  echo("        else\n");
  echo("          tabela.rows[tabela.rows.length-1].style.display=\"table-row\";\n");

  echo("        pagAtual=pagina;\n\n");

  echo("        if (pagAtual != 1){\n");
  echo("          document.getElementById('paginacao_first').onclick = function(){ ExibeMsgPagina(1); };\n");
  echo("          document.getElementById('paginacao_first').className = \"link\";\n");
  echo("          document.getElementById('paginacao_back').onclick = function(){ ExibeMsgPagina(pagAtual-1); };\n");
  echo("          document.getElementById('paginacao_back').className = \"link\";\n");
  echo("        }else{\n");
  echo("          document.getElementById('paginacao_first').onclick = function(){};\n");
  echo("          document.getElementById('paginacao_first').className = \"\";\n");
  echo("          document.getElementById('paginacao_back').onclick = function(){};\n");
  echo("          document.getElementById('paginacao_back').className = \"\";\n");
  echo("        }\n");
  echo("        document.getElementById('paginacao_first').innerHTML = \"&lt;&lt;\";\n");
  echo("        document.getElementById('paginacao_back').innerHTML = \"&lt;\";\n");
  echo("        inicio = pagAtual-2;\n");
  echo("        if (inicio<1) inicio=1;\n");
  echo("        fim = pagAtual+2;\n");
  echo("        if (fim>totalPag) fim=totalPag;\n");
  echo("        var controle=1;\n");
  echo("        var vetor= new Array();\n");
  echo("        for (j=inicio; j <= fim; j++){\n");
  echo("          // A página atual Não é exibida com link.\n");
  echo("          if (j == pagAtual){\n");
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
  echo("        if (pagAtual != totalPag){\n");
  echo("          document.getElementById('paginacao_fwd').onclick = function(){ ExibeMsgPagina(pagAtual+1); };\n");
  echo("          document.getElementById('paginacao_fwd').className = \"link\";\n");
  echo("          document.getElementById('paginacao_last').onclick = function(){ ExibeMsgPagina(totalPag); };\n");
  echo("          document.getElementById('paginacao_last').className = \"link\";\n");
  echo("        }\n");
  echo("        else{\n");
  echo("          document.getElementById('paginacao_fwd').onclick = function(){};\n");
  echo("          document.getElementById('paginacao_fwd').className = \"\";\n");
  echo("          document.getElementById('paginacao_last').onclick = function(){};\n");
  echo("          document.getElementById('paginacao_last').className = \"\";\n");
  echo("        }\n");
  echo("        document.getElementById('paginacao_fwd').innerHTML = \"&gt;\";\n");
  echo("        document.getElementById('paginacao_last').innerHTML = \"&gt;&gt;\";\n");
  echo("      }\n");

  echo("      function MarcaOuDesmarcaTodos(pagAtual){\n");
  echo("        var e;\n");
  echo("        var i;\n");
  echo("        var inicio;\n");
  echo("        var final;\n");
  echo("        var elementos = document.getElementsByName('chk[]')\n");
  echo("        inicio = ((pagAtual-1)*".$msgPorPag.");\n");
  echo("        final = ((pagAtual)*".$msgPorPag.");\n");
  echo("        controle = (pagAtual-1)*".$msgPorPag.";\n");
  echo("        controle = elementos.length - controle;\n");
  echo("        if(controle < final) {final = inicio + controle;}\n");
  echo("        var CabMarcado = document.frmSelecao.cabecalho.checked;\n");
  echo("        for(i = inicio; i < final; i++){\n");
  echo("         e = document.getElementsByName('chk[]')[i];\n");
  echo("         e.checked = CabMarcado;\n");
  echo("         ControlaSelecao(e);\n");
  echo("        }\n");
  echo("      }\n");

  echo("      function ControlaSelecao(chkbox){\n");
  echo("        var conteudo;\n");
  echo("        var controle=0;\n");
  echo("        var i=0;\n");
  echo("        var j=0;\n");
  echo("        conteudo = document.frmSelecao.Selecionados.value;\n");
  echo("        var achou = conteudo.indexOf(chkbox.value+' ');");
  echo("        cabecalho = document.frmSelecao.cabecalho;\n");
  echo("        var elementos = document.getElementsByName('chk[]')\n");
//   echo("        if(chkbox.checked){\n");
  echo("          for(i=0 ; i < elementos.length; i++){\n");
  echo("            if(elementos[i].checked){\n");
  echo("              j++\n");
  echo("            }\n");
  echo("          }\n");
  echo("          controle = (pagAtual-1)*".$msgPorPag."\n");
  echo("          controle = elementos.length - controle\n");
  echo("          if((j == ".$msgPorPag.") || (j == controle)){ cabecalho.checked = true;\n");
  echo("        }else{\n");
  echo("          cabecalho.checked = false");
  echo("        }\n");
  echo("        if(j > 0){\n");
  if($modoVisualizacao != 'L'){
    echo("          document.getElementById('mApagar_Selec').className=\"menuUp02\";\n");
    echo("          document.getElementById('mApagar_Selec').onclick=function(){ ApagarMensagem('".$modoVisualizacao."'); };\n");
    echo("          document.getElementById('mExibir_Selec').className=\"menuUp02\";\n");
    echo("          document.getElementById('mExibir_Selec').onclick=function(){ ExibirMensagens(); };\n");
  }
  else{
    echo("          document.getElementById('mExcluir_Selec').className=\"menuUp02\";\n");
    echo("          document.getElementById('mExcluir_Selec').onclick=function(){ ApagarMensagem('".$modoVisualizacao."'); };\n");
    echo("          document.getElementById('mRecuperar_Selec').className=\"menuUp02\";\n");
    echo("          document.getElementById('mRecuperar_Selec').onclick=function(){};\n");
    echo("          document.getElementById('mRecuperar_Selec').onclick=function(){ ApagarMensagem('".$modoVisualizacao."','rec'); };\n");
  }
  echo("        }else{\n");
  if($modoVisualizacao != 'L'){
    echo("          document.getElementById('mApagar_Selec').className=\"menuUp\";\n");
    echo("          document.getElementById('mApagar_Selec').onclick=function(){  };\n");
    echo("          document.getElementById('mExibir_Selec').className=\"menuUp\";\n");
    echo("          document.getElementById('mExibir_Selec').onclick=function(){ };\n");

  }else{

    echo("          document.getElementById('mExcluir_Selec').className=\"menuUp\";\n");
    echo("          document.getElementById('mExcluir_Selec').onclick=function(){  };\n");
    echo("          document.getElementById('mRecuperar_Selec').className=\"menuUp\";\n");
    echo("          document.getElementById('mRecuperar_Selec').onclick=function(){ };\n");
  }
  echo("        }\n");
  echo("      }\n");

  echo("      function AbreMensagem(cod_msg, modoVisualizacao){\n");
  if($modoVisualizacao == 'R'){ //marcar como lida
    echo("        xajax_trocaEstadoMsg(".$cod_usuario.", ".$cod_curso.", 'R', cod_msg, 'L');\n");
  }
  echo("        window_handle = window.open('exibir_mensagem.php?&cod_msg='+cod_msg+'&cod_curso=".$cod_curso."&modoVisualizacao=".$modoVisualizacao."','mensagem','width=700,height=900,top=100,left=100,scrollbars=yes,status=no,toolbar=no,menubar=no,resizable=no');\n");
  echo("        window_handle.opener = window.self;\n");
  echo("        window_handle.focus();\n");
  echo("        return(false);\n");
  echo("      }\n");

  echo("      function NovaMensagem(){\n");
  echo("        window.open('compor.php?&cod_curso=" .$cod_curso . "', 'mensagem', 'top=50,left=100,scrollbars=yes, status=no,toolbar=no,menubar=no,resizable=yes');\n");
  echo("      }\n\n");

  echo("      function AtualizaPagina(){\n");
  echo("        window.location.reload();\n");
  echo("      }\n");

  echo("      function MostrarRecebidas(){\n");
  echo("        document.location = \"correio.php?cod_curso=".$cod_curso."&modoVisualizacao=R\";\n");
  echo("      }\n");

  echo("      function MostrarEnviadas(){\n");
  echo("        document.location = \"correio.php?cod_curso=".$cod_curso."&modoVisualizacao=E\";\n");
  echo("      }\n");

  echo("      function MostrarLixeira(){\n");
  echo("        document.location = \"correio.php?cod_curso=".$cod_curso."&modoVisualizacao=L\";\n");
  echo("      }\n");

  echo("      function ApagarMensagem(modoVisualizacao, recuperar){\n");
  echo("        if(typeof(recuperar) == \"undefined\") recuperar = \"\"; \n");

  echo("        var elementos = document.getElementsByName('chk[]')\n");
  echo("        tabela = document.getElementById('tabelaMsgs');\n");
  echo("        var msg = new Array();\n");
  echo("        var cont = 0;\n");
  echo("        var contCor = 0;\n");
  echo("        var altCor = 0;\n");
  echo("        var codMsgAtual;\n");
  echo("        var reload = 'no';\n");

  echo("        for(i=0 ; i < elementos.length; i++){\n");
  echo("          codMsgAtual = elementos[i].id.split('_');\n");
  echo("          if(elementos[i].checked){\n");
  echo("            cont++;\n");
  echo("          }\n");
  echo("        }\n");

  echo("        if(cont==0){ alert('".RetornaFraseDalista($lista_frases,57)."'); return} ;\n");

  if($modoVisualizacao == 'L'){
    echo("          if(recuperar == 'rec'){\n");
    /* 133 - Voce tem certeza de que deseja recuperar esta(s) mensagem(ns) ? */
    echo("            if(!confirm('".RetornaFraseDaLista($lista_frases,133)."')) return;\n");
    echo("          }else{\n");
    /* 134 - Voce tem certeza de que deseja excluir definitivamente esta(s) mensage(ns) ? */
    echo("            if(!confirm('".RetornaFraseDalista($lista_frases,134)."')) return;\n");
    echo("          }\n");
  }else{
    /* 135 - Voce tem certeza de que deseja mover esta(s) mensagem(ns) para a Lixeira? ? */
    echo("            if(!confirm('".RetornaFraseDalista($lista_frases,135)."')) return;\n");
  }

  echo("        cont=0;\n");
  echo("        for(i=0 ; i < elementos.length; i++){\n");
  echo("          codMsgAtual = elementos[i].id.split('_');\n");
  echo("          if(elementos[i].checked){\n");
  echo("            msg[cont] = elementos[i].value;\n");
  echo("            apagado = document.getElementById('tr_'+codMsgAtual[1]); \n");
  echo("            tabAux = apagado.parentNode;\n");
  echo("            tabAux.removeChild(apagado);\n");
  echo("            i--;cont++;\n");
  echo("            \n");
  echo("          }else{\n");
  echo("            altCor = (contCor%2);\n");
  echo("            contCor++;\n");
  echo("            document.getElementById('tr_'+codMsgAtual[1]).className=\"altColor\"+altCor;\n");
  echo("          }\n");
  echo("        }\n");
  /* 57 - Não há nenhuma mensagem selecionada !*/
  //   echo("        if(cont==0) alert('".RetornaFraseDalista($lista_frases,57)."');\n");

  echo("        if(totalMensagem == cont) reload = 'yes';\n");

  echo("        if(modoVisualizacao == 'L'){\n");
  echo("          if(recuperar == 'rec'){\n");
  echo("              xajax_trocaEstadoMsg(".$cod_usuario.", ".$cod_curso.", modoVisualizacao, msg, 'L', '".RetornaFraseDalista($lista_frases,137)." ".RetornaFraseDalista($lista_frases,139)."', reload, recuperar);\n");
  echo("          }else{\n");
  echo("              xajax_trocaEstadoMsg(".$cod_usuario.", ".$cod_curso.", modoVisualizacao, msg, 'X', '".RetornaFraseDalista($lista_frases,137)." ".RetornaFraseDalista($lista_frases,138)."', reload, recuperar);\n");
  echo("          }\n");
  echo("        }else{\n");
  echo("              xajax_trocaEstadoMsg(".$cod_usuario.", ".$cod_curso.", modoVisualizacao, msg, 'A', '".RetornaFraseDalista($lista_frases,137)." ".RetornaFraseDalista($lista_frases,140)."', reload);\n");

  echo("        }\n\n");

  echo("        if(((totalMensagem%".$msgPorPag.") - cont) < 0){\n");
  echo("          if(pagAtual == totalPag){\n");
  echo("            pagAtual--;\n");
  echo("          }\n");
  echo("          totalPag--;\n");
  echo("        }\n");
  echo("        totalMensagem -= cont;\n");
  echo("        document.getElementById('totalMsg').innerHTML=totalMensagem;\n");
  echo("        ExibeMsgPagina(pagAtual);\n");

  if($modoVisualizacao != 'L'){
    echo("          document.getElementById('mApagar_Selec').className=\"menuUp\";\n");
    echo("          document.getElementById('mApagar_Selec').onclick=function(){};\n");
    echo("          document.getElementById('mExibir_Selec').className=\"menuUp\";\n");
    echo("          document.getElementById('mExibir_Selec').onclick=function(){};\n");

  }else{

    echo("          document.getElementById('mExcluir_Selec').className=\"menuUp\";\n");
    echo("          document.getElementById('mExcluir_Selec').onclick=function(){};\n");
    echo("          document.getElementById('mRecuperar_Selec').className=\"menuUp\";\n");
    echo("          document.getElementById('mRecuperar_Selec').onclick=function(){};\n");
  }
  echo("      }\n\n");

  echo("      function ApagarOpener(cod_msg, modoVisualizacao, acao){\n");
  echo("        if(modoVisualizacao != '".$modoVisualizacao."'){\n");
  echo("          return;\n");
  echo("        }\n");
  echo("        var elementos = document.getElementsByName('chk[]')\n");
  echo("        tabela = document.getElementById('tabelaMsgs');\n");
  echo("        var msg = new Array();\n");
  echo("        var cont = 0;\n");
  echo("        var contCor = 0;\n");
  echo("        var altCor = 0;\n");
  echo("        var codMsgAtual;\n");
  echo("        apagado = document.getElementById('tr_'+cod_msg); \n");
  echo("        tabAux = apagado.parentNode;\n");
  echo("        tabAux.removeChild(apagado);\n");
  echo("        for(i=0 ; i < elementos.length; i++){\n");
  echo("          codMsgAtual = elementos[i].id.split('_');\n");
  echo("          altCor = (contCor%2);\n");
  echo("          contCor++;\n");
  echo("          document.getElementById('tr_'+codMsgAtual[1]).className=\"altColor\"+altCor;\n");
  echo("        }\n");

  echo("        if(((totalPag%".$msgPorPag.") - i) < 0);\n");
  echo("          totalPag--;\n");

  echo("        ExibeMsgPagina(pagAtual);\n");
  echo("        totalMensagem --;\n");
  echo("        document.getElementById('totalMsg').innerHTML=totalMensagem;\n");
  echo("        if(acao == 'apa'){\n");
  echo("          mostraFeedback('".RetornaFraseDalista($lista_frases,68)." ".RetornaFraseDalista($lista_frases,136)."', true)\n");
  echo("        }else if(acao == 'exc'){\n");
  echo("          mostraFeedback('".RetornaFraseDalista($lista_frases,68)." ".RetornaFraseDalista($lista_frases,70)."', true)\n");
  echo("        }else if(acao == 'rec'){\n");
  echo("          mostraFeedback('".RetornaFraseDalista($lista_frases,68)." ".RetornaFraseDalista($lista_frases,78)."', true)\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("      function ExibirMensagens(){\n");
  echo("        var cont = 0;\n");
  echo("        var elementos = document.getElementsByName('chk[]')\n");
  echo("        var arrayMsgs = new Array();\n");
  echo("        for(i=0 ; i < elementos.length; i++){\n");
  echo("          if(elementos[i].checked){\n");
  echo("            arrayMsgs[cont]= elementos[i].value;\n");
  echo("            cont++;\n");
  echo("          }\n");
  echo("        }\n");
  /* 57 - Não há nenhuma mensagem selecionada !*/
  echo("        if(cont==0) {alert('".RetornaFraseDalista($lista_frases,57)."'); return;}\n");
  echo("        window_handle = window.open('exibe_mensagem_selecionadas.php?&cod_curso=".$cod_curso."&modoVisualizacao=".$modoVisualizacao."&arrayMsgs='+arrayMsgs,'mensagem','width=1800,height=900,top=100,left=100,scrollbars=yes,status=no,toolbar=no,menubar=no,resizable=no');\n");

  echo("      }\n\n");

  echo("    </script>\n");

  $objAjax->printJavascript();

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  ExpulsaVisitante($sock, $cod_curso, $cod_usuario);

  if($modoVisualizacao == "R")       $nomeModo = RetornaFraseDaLista($lista_frases,4);
  else if ($modoVisualizacao == "E") $nomeModo = RetornaFraseDaLista($lista_frases,5);
  else if ($modoVisualizacao == "L") $nomeModo = RetornaFraseDaLista($lista_frases,6);
  /* 131 - Correio */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ". $nomeModo ."</h4>\n");
 
// 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
// <TABELA EXTERNA> --------------------------------------------------------------------------
  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 7 - Atualizar */
  echo("                  <li><span onclick=\"AtualizaPagina();\">".RetornaFraseDaLista($lista_frases, 7)."</span></li>\n");
  /* 128 - Nova Mensagem */
  echo("                  <li><span onclick=\"NovaMensagem();\">".RetornaFraseDaLista($lista_frases, 128)."</span></li>\n");
  /* 4 - Mensagens Recebidas */
  echo("                  <li><span onclick=\"MostrarRecebidas();\">".RetornaFraseDaLista($lista_frases, 4)."</span></li>\n");
  /* 5 - Mensagens Enviadas */
  echo("                  <li><span onclick=\"MostrarEnviadas();\">".RetornaFraseDaLista($lista_frases, 5)."</span></li>\n");

 /* 6 - Lixeira */
  echo("                  <li><span onclick=\"MostrarLixeira();\">".RetornaFraseDaLista($lista_frases, 6)."</span></li>\n");
  
  echo("                </ul>\n");

  echo("              </td>\n");
  echo("            </tr>\n");

  echo("            <tr>\n");
  echo("              <td>\n");

  if($totalMsg > 0){
    // Calcula o índice da primeira mensagem.
    $primMsgIndex = (($pagAtual - 1) * $msgPorPag) + 1;
    // Calcula o índice da última mensagem.
    $ultMsgIndex = $pagAtual * $msgPorPag;

    // Se o índice da ultima mensagem for maior que o número de mensagens, então copia este 
    // para o índice da última mensagem.
    if ($ultMsgIndex > ($totalMsg))
      $ultMsgIndex = ($totalMsg);
    echo("            <tr class=\"head01\">\n");
    echo("              <td colspan=\"4\">\n");
    /* 129 - Mensagens     */
    echo("                ".RetornaFraseDaLista($lista_frases, 129)." ");
    echo("(<span id=\"primMsgIndex\"></span>");
    /* 131 - a             */
    echo(" ".RetornaFraseDaLista($lista_frases, 131)."&nbsp;");
    /* 130 - de            */
    echo("<span id=\"ultMsgIndex\"></span> ".RetornaFraseDaLista($lista_frases, 130)." ");
    echo("<span id=\"totalMsg\">".($totalMsg)."</span>)\n");
    echo("              </td>\n");
    echo("            </tr>\n");
  }
  
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <form name=\"frmSelecao\" id=\"frmSelecao\" action=\"exibe_mensagem_selecionadas.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;modoVisualizacao=".$modoVisualizacao."\" target=\"_blank\" method=\"post\">\n");
  echo("                  <input type=\"hidden\" class=\"input\" name=\"Selecionados\" value=\"\" />\n");
  if($totalMsg){
    echo("                  <table border=\"0\" width=\"100%\" cellspacing=\"0\" style=\"cellpadding:0pt;\" class=\"sortable tabInterna\" id=\"tabelaMsgs\">\n");
  }else{
    echo("                  <table border=\"0\" width=\"100%\" cellspacing=\"0\" style=\"cellpadding:0pt;\" class=\"sortable tabInterna\">\n");
  }
  echo("                    <thead>\n");
  echo("                      <tr class=\"head\">\n");
  if($totalMsg){
  echo("                        <td class=\"sorttable_nosort\" width=\"2%\"><input type=\"checkbox\" name=\"cabecalho\" onclick=\"MarcaOuDesmarcaTodos(pagAtual);\" /></td>\n");
  }else{
  echo("                        <td width=\"2%\"><input type=\"checkbox\" name=\"cabecalho\" onclick=\"MarcaOuDesmarcaTodos(pagAtual);\" /></td>\n");
  }

  /* 20 - Assunto */
  echo("                        <td class=\"alLeft\" style=\"cursor:pointer\">".RetornaFraseDaLista($lista_frases, 20)."</td>\n");
  if($modoVisualizacao != 'E')
    /* 23 - Remetente */
    echo("                        <td width=\"20%\" style=\"cursor:pointer\">".RetornaFraseDaLista($lista_frases, 23)."</td>\n");
  /* 24 - Data */
  echo("                        <td width=\"12%\" style=\"cursor:pointer\">".RetornaFraseDaLista($lista_frases, 24)."</td>\n");
  echo("                      </tr>\n");
  echo("                    </thead>\n");
  echo("                    <tbody>\n");

  $num_msg = 0;
  //numero de mensagens em uma determinada pagina
  $numMsgPag = 0;
  if(!($totalMsg)){
    echo("                      <tr>\n");
    echo("                        <td colspan=\"4\">\n");
    /* 12 - N�o existem nenhuma mensagens. */
    echo("                          ".RetornaFraseDaLista($lista_frases, 12)."\n");
    echo("                        </td>\n");
    echo("                      </tr>\n");
  }else{
    $numPagina = 1;
    foreach($listaMsg as $campo => $dados){
      if($numMsgPag == $msgPorPag){
        $numPagina++;
        $numMsgPag = 0;
      }
      if($numPagina == $pagAtual) $style = "";
      else $style = "display:none";

      $cod_msg = $dados['cod_msg'];

      //$dados['estado'] contém o estado da mensagem na tabela Correio_destinos
      //N = Nova
      //L = Lida
      if($dados[9] == "N"){
        $icone = "<img src=\"../imgs/icNovaMensagem.gif\" border=\"0\" alt=\"\" id=\"img_".$cod_msg."\" />";
        $styleClass ="novo";
      }else{
        if($modoVisualizacao == 'E'){
          $icone = "<img src=\"../imgs/icMensagemEnviada.gif\" border=\"0\" alt=\"\" id=\"img_".$cod_msg."\" />";
        }else if($modoVisualizacao == 'L'){
          $icone = "<img src=\"../imgs/icMensagemApagada.gif\" border=\"0\" alt=\"\" id=\"img_".$cod_msg."\" />";
        }else{
          $icone = "<img src=\"../imgs/icMensagemLida.gif\" border=\"0\" alt=\"\" id=\"img_".$cod_msg."\" />";
        }
        $styleClass ="antigo";
      }

      //Não mostrar remetente nas mensagens enviadas;
      if($modoVisualizacao != 'E'){
        $remetente = RetornaNomeUsuarioDeCodigo($sock, $dados[1], $cod_curso);
        $remetente = "<span id=\"remetente_".$cod_msg."\" class=\"link ".$styleClass."\" onclick=\"OpenWindowPerfil(".$dados[1].");\">".$remetente."</span>";
      }

      //se a mensagem tiver arquivo anexo, coloca um icone ao lado do Assunto.
      Desconectar($sock);
      $sock = Conectar("");
      $diretorio_arq=RetornaDiretorio($sock,'Arquivos');
      $dir_arq=$diretorio_arq."/".$cod_curso."/correio/".$cod_msg;

      Desconectar($sock);
      $sock=Conectar($cod_curso);

      if(ExisteArquivo($dir_arq))
        $iconeAnexo = "<img src=\"../imgs/paperclip.gif\" border=\"0\" alt=\"\" id=\"img_".$cod_msg."_clip\" />";
      else
        $iconeAnexo = "";

      $assunto = LimpaTitulo($dados['assunto']);
      $assunto="<span id=\"assunto_".$cod_msg."\" class=\"link ".$styleClass."\" onclick=\"AbreMensagem(".$cod_msg .",'". $modoVisualizacao. "');\">".$assunto."</span>";

      $data = UnixTime2DataHora($dados['data']);
      $dataaux = explode(" ",$data);
      $data = $dataaux[0] . "<br />" . $dataaux[1];
      $data = "<span id=\"data_".$cod_msg."\" class=\"$styleClass\">".$data."</span>";

      echo("                      <tr id=\"tr_".$cod_msg."\" style=\"".$style."\" class=\"altColor".($num_msg%2)."\">\n");
      echo("                        <td width=\"2%\">\n");
      echo("                          <input type=\"checkbox\" name=\"chk[]\" id=\"chk_".$cod_msg."\" value=\"".$cod_msg."\" onclick=\"ControlaSelecao(this);\" />\n");
      echo("                        </td>\n");
      echo("                        <td class=\"alLeft\">\n");
      echo("                          ".$icone." ".$iconeAnexo." ".$assunto."\n");
      echo("                        </td>\n");
      if($modoVisualizacao != 'E'){
        echo("                        <td width=\"20%\">\n");
        echo("                          ".$remetente."\n");
        echo("                        </td>\n");
      }
      echo("                        <td width=\"10%\">\n");
      echo("                          ".$data."\n");
      echo("                        </td>\n");
      echo("                      </tr>\n");
      $numMsgPag++;
      $num_msg++;
      }
  }

  echo("                    </tbody>\n");
  echo("                    <tfoot>\n");
  echo("                      <tr>\n");
  echo("                        <td colspan=\"2\" align=\"left\" style=\"border-right:none\">\n");
  if($totalMsg>1)
    /* 118 - * clique no cabe�alho para ordenar as mensagens */
    echo("                          ".RetornaFraseDaLista($lista_frases, 118)."\n");
  echo("                        </td>\n");

  echo("                        <td colspan=\"2\" align=\"right\">\n");
  echo("                        <span id=\"paginacao_first\"></span> <span id=\"paginacao_back\"></span>\n");
  $controle=1;
  while($controle<=5){
    echo("                          <span id=\"paginacao_".$controle."\"></span>\n");
    $controle++;
  }
  echo("                        <span id=\"paginacao_fwd\"></span> <span id=\"paginacao_last\"></span>\n");
  echo("                        </td>\n");
  echo("                      </tr>\n");
  echo("                    </tfoot>\n");

  echo("                  </table>\n");/* fecha tabela tabelaMsgs */
  echo("                </form>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");

  echo("                <ul>\n");

  if($modoVisualizacao == 'L'){

    $status_curso=RetornaStatusCurso($sock,$cod_curso);
    if (!($status_curso=='E' && !EFormador($sock,$cod_curso,$cod_usuario))){
      /* 55 - Excluir definitivamente*/
      echo("  <li id=\"mExcluir_Selec\" class=\"menuUp\"><span onclick=\"function(){};\">".RetornaFraseDaLista($lista_frases, 55)."</span></li>\n");
      /* 79 - Recuperar mensagens */
      echo("  <li id=\"mRecuperar_Selec\" class=\"menuUp\"><span onclick=\"function(){};\">".RetornaFraseDaLista($lista_frases, 79)."</span></li>\n");
    }
  }else{

    /* 1 - Apagar */
    echo("                  <li id=\"mApagar_Selec\" class=\"menuUp\"><span id=\"apagarMsg\">". RetornaFraseDaLista($lista_frases_geral, 1). "</span></li>\n");
    /* 75 - Exibir mensagens selecionadas */
    echo("                  <li id=\"mExibir_Selec\" class=\"menuUp\"><span id=\"exibirMsg\" >".RetornaFraseDaLista($lista_frases, 75)."</span></li>\n");
  }
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");  // </TABELA EXTERNA>

  echo("        </td>\n");
  echo("      </tr>\n");
  include("../tela2.php");

  Desconectar($sock);
  echo("  </body>\n");
  echo("</html>");

?>
