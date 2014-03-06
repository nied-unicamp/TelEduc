<?php
/*
 <!--
 -------------------------------------------------------------------------------

 Arquivo : cursos/aplic/exercicios/questoes.php

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
 ARQUIVO : cursos/aplic/exercicios/questoes.php
 ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("exercicios.inc");

  require_once("../xajax_0.5/xajax_core/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  //Registre os nomes das funcoes em PHP que voce quer chamar atraves do xajax
  $objAjax->register(XAJAX_FUNCTION,"AlteraStatusQuestaoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"MudarCompartilhamentoDinamic");
  // Registra fun��es para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  $cod_ferramenta = 23;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=4;

  $visualizar = $_GET['visualizar'];
  $cod_exercicio = $_GET['cod_exercicio'];

  include("../topo_tela.php");

  if($visualizar == "Q")
  $lista_questoes = RetornaQuestoes($sock);
  else if($visualizar == "L")
  $lista_questoes = RetornaQuestoesLixeira($sock);

  $lista_exercicios = RetornaExercicios($sock);

  if($lista_questoes != "")
  $totalQuestoes = count($lista_questoes);
  else
  $totalQuestoes = 0;

  /* Nmero de questoes exibidas por p�gina.             */
  if (!isset($questoesPorPag)) $questoesPorPag = 10;

  /* Se o nmero total de questoes for superior que o nmero de questoes por  */
  /* p�gina ent� calcula o total de p�ginas. Do contr�io, define o nmero de     */
  /* p�ginas para 1.                                                           */

  /* Calcula o nmero de p�ginas geradas.    */
  if($totalQuestoes > $questoesPorPag)
  $totalPag = ceil($totalQuestoes / $questoesPorPag);
  else
  $totalPag = 1;

  /* Se a p�gina atual n� estiver setada ent�, por padr�o, atribui-lhe o valor 1. */
  /* Se estiver setada, verifica se a p�gina �maior que o total de p�ginas, se for */
  /* atribui o valor de $total_pag �$pagAtual.                                    */
  if ((!isset($pagAtual))or($pagAtual=='')or($pagAtual==0))
  $pagAtual =  1;
  else $pagAtual = min($pagAtual, $totalPag);



  /*********************************************************/
  /* in�cio - JavaScript */
  if($totalQuestoes){
    echo("  <script type=\"text/javascript\" src=\"../js-css/sorttablePaginado.js\"></script>\n");
  }
  echo("  <script  type=\"text/javascript\" language=\"javascript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
  echo("  <script  type=\"text/javascript\" language=\"javascript\">\n\n");

  echo("    var js_cod_item;\n");
  echo("    var js_comp = new Array();\n");
  echo("    var pagAtual = ".$pagAtual.";\n");
  echo("    var totalQuestoes = ".$totalQuestoes.";\n");
  echo("    var totalPag = ".$totalPag.";\n");
  echo("    var questoesPorPag = ".$questoesPorPag.";\n");
  echo("    var topico = 'T';\n");
  echo("    var tp_questao = 'T';\n");
  echo("    var dificuldade = 'T';\n");
  echo("    var window_handle;\n");
  echo("    var t;\n");
  echo("    this.name = 'principal';\n\n");

  echo("    if (document.addEventListener) {\n");/* Caso do FireFox */
  echo("      document.addEventListener('mousemove', TrataMouse, false);\n");
  echo("    } else if (document.attachEvent){\n");/* Caso do IE */
  echo("      document.attachEvent('onmousemove', TrataMouse);\n");
  echo("    }\n");

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
  echo("        return(document.documentElement.scrollTop);\n");
  echo("    }\n\n");

  echo("    function AjustePosMenuIE()\n");
  echo("    {\n");
  echo("      if (isIE)\n");
  echo("        return(getPageScrollY());\n");
  echo("      else\n");
  echo("        return(0);\n");
  echo("    }\n\n");


  /* Mostra perfil de um usuario. */
  echo("    function OpenWindowPerfil(cod_curso,id)\n");
  echo("    {\n");
  echo("       window.open('../perfil/exibir_perfis.php?cod_curso='+cod_curso+'&cod_aluno[]='+id,'PerfilDisplay','width=700,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
  echo("      return(false);\n");
  echo("    }\n\n");

  /* Iniciliza os layers. */
  echo("    function Iniciar()\n");
  echo("    {\n");
  if($visualizar == "Q")
  {
    echo("      lay_nova_questao = getLayer('layer_nova_questao');\n");
    echo("      cod_comp = getLayer(\"comp\");\n");
    echo("      lay_exercicios = getLayer(\"layer_exercicios\");\n");
    echo("      lay_filtro = getLayer(\"layer_filtro\");\n");
  }
  echo("      startList();\n");
  echo("      ExibeMsgPagina(".$pagAtual.");\n");
  echo("    }\n\n");

  echo("      function ExibeMsgPagina(pagina){\n");
  echo("        var i = 0;\n");
  echo("        if (pagina < 1) return;\n");
  echo("        document.getElementById(\"checkMenu\").checked=false;\n");
  echo("        tabela = document.getElementById('tabelaQuestoes');\n");
  echo("        if(!tabela) return;\n");
  echo("        inicio = 1;\n");
  echo("        final = ((totalPag)*".$questoesPorPag.")+1;\n");
  echo("        for (i=inicio; i < final; i++){\n");
  echo("          if (!tabela.rows[i]) break;\n");
  echo("          tabela.rows[i].style.display=\"none\";\n");
  echo("        }\n");

  echo("        var browser=navigator.appName;\n\n");
  echo("        inicio = ((pagina-1)*".$questoesPorPag.")+1;\n");
  echo("        final = ((pagina)*".$questoesPorPag.");\n");
  echo("        for (i=inicio; i < final+1; i++){\n");
  echo("          if (!tabela.rows[i+1] || i > totalQuestoes){break;}\n");
  echo("          if (browser==\"Microsoft Internet Explorer\")\n");
  echo("            tabela.rows[i].style.display=\"block\";\n");
  echo("          else\n");
  echo("            tabela.rows[i].style.display=\"table-row\";\n");
  echo("          tabela.rows[i].className = 'altColor'+((i+1)%2);");
  echo("        }\n\n");
  echo("        document.getElementById('primQuestaoIndex').innerHTML=inicio;\n");
  echo("        document.getElementById('ultQuestaoIndex').innerHTML=(i-1);\n\n");

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
  echo("        ControlaSelecao();\n");
  echo("      }\n\n");

  echo("      function AuxiliaPaginacao(){\n");
  echo("        var topico = document.getElementById('topico').value;");
  echo("        var tp_questao = document.getElementById('tp_questao').value;");
  echo("        var dificuldade = document.getElementById('dificuldade').value;");
  echo("        var stringNiveisQuestoes = '';");
  echo("        AplicaFiltro(topico,tp_questao, dificuldade, stringNiveisQuestoes);\n");
  echo("        AtualizaEstadoPaginacao(pagAtual);\n");
  /* Frase #143 - Questoes ordenadas. */
  echo("        mostraFeedback(\"".RetornaFraseDaLista($lista_frases, 143)."\",true);\n");
  echo("      }\n\n");

  echo("      function MarcaOuDesmarcaTodos(pagAtual){\n");
  echo("        var e;\n");
  echo("        var i;\n");
  echo("        var inicio;\n");
  echo("        var final;\n");
  echo("        var elementos = document.getElementsByName('cod_questao[]')\n");
  echo("        inicio = ((pagAtual-1)*".$questoesPorPag.");\n");
  echo("        final = ((pagAtual)*".$questoesPorPag.");\n");
  echo("        controle = (pagAtual-1)*".$questoesPorPag.";\n");
  echo("        controle = elementos.length - controle;\n");
  echo("        if(controle < final) {final = inicio + controle;}\n");
  echo("        var CabMarcado = document.getElementById('checkMenu').checked;\n");
  echo("        for(i = inicio; i < final; i++){\n");
  echo("          e = document.getElementsByName('cod_questao[]')[i];\n");
  echo("          e.checked = CabMarcado;\n");
  echo("        }\n");
  echo("        ControlaSelecao();\n");
  echo("      }\n\n");

  echo("      function ControlaSelecao(){\n");
  echo("        var conteudo;\n");
  echo("        var controle=0;\n");
  echo("        var i=0;\n");
  echo("        var j=0;\n");
  echo("        var jPag=0;\n");
  if($visualizar == 'Q')
    echo("        EscondeLayers();\n");
  echo("        var cabecalho = document.getElementById('checkMenu');\n");
  echo("        var elementos = document.getElementsByName('cod_questao[]')\n");
  echo("        var inicio = ((pagAtual-1)*".$questoesPorPag.");\n");
  echo("        var final = ((pagAtual)*".$questoesPorPag.");\n");
  echo("        controle = (pagAtual-1)*".$questoesPorPag.";\n");
  echo("        controle = elementos.length - controle;\n");
  echo("        if(controle < final) {final = inicio + controle;}\n");
  echo("        for(i=0 ; i < elementos.length; i++){\n");
  echo("          if(elementos[i].checked){\n");
  echo("            j++;\n");
  echo("            if(i>=inicio && i<final)\n");
  echo("              jPag++;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        if((jPag == ".$questoesPorPag.") || (jPag == controle)){ cabecalho.checked = true;\n");
  echo("        }else{\n");
  echo("          cabecalho.checked = false");
  echo("        }\n");
  echo("        if(j > 0){\n");
  if($visualizar == 'Q'){
    echo("        document.getElementById('mExcluir_Selec').className=\"menuUp02\";\n");
    echo("        document.getElementById('mExcluir_Selec').onclick=function(){ TratarSelecionados('L'); };\n");
    echo("        document.getElementById('mIncluir_Selec').className=\"menuUp02\";\n");
    if($cod_exercicio == null)
      echo("        document.getElementById('mIncluir_Selec').onclick=function(){ MostraLayer(lay_exercicios, 0); };\n");
    else
      echo("        document.getElementById('mIncluir_Selec').onclick=function(){ document.getElementById('incluirQuestoes').submit() };\n");
  }
  else if($visualizar == 'L'){
    echo("        document.getElementById('mExcluir_Selec').className=\"menuUp02\";\n");
    echo("        document.getElementById('mExcluir_Selec').onclick=function(){ TratarSelecionados('X'); };\n");
    echo("        document.getElementById('mRecup_Selec').className=\"menuUp02\";\n");
    echo("        document.getElementById('mRecup_Selec').onclick=function(){ TratarSelecionados('V'); };\n");
  }
  echo("        }else{\n");
  if($visualizar == 'Q'){
    echo("        document.getElementById('mExcluir_Selec').className=\"menuUp\";\n");
    echo("        document.getElementById('mExcluir_Selec').onclick=function(){  };\n");
    echo("        document.getElementById('mIncluir_Selec').className=\"menuUp\";\n");
    echo("        document.getElementById('mIncluir_Selec').onclick=function(){ };\n");
  }else if($visualizar == 'L'){
    echo("        document.getElementById('mExcluir_Selec').className=\"menuUp\";\n");
    echo("        document.getElementById('mExcluir_Selec').onclick=function(){  };\n");
    echo("        document.getElementById('mRecup_Selec').className=\"menuUp\";\n");
    echo("        document.getElementById('mRecup_Selec').onclick=function(){  };\n");
  }
  echo("        }\n");
  echo("      }\n\n");

  echo("    function AtualizaEstadoPaginacao(pagina){\n");
  echo("      var trVazia;");
  echo("      totalPag = Math.ceil(totalQuestoes/".$questoesPorPag.");\n");
  echo("      document.getElementById(\"totalQuestoes\").innerHTML = totalQuestoes;\n");
  echo("      ExibeMsgPagina(pagina);\n");
  echo("      trVazia = document.getElementById(\"trVazia\");");
  echo("      if(totalQuestoes == 0)\n");
  echo("      {\n");
  echo("        if(!trVazia)");
  echo("          InsereLinhaVazia();\n");
  echo("        document.getElementById(\"trIndicaEstadoPag\").style.display = \"none\";\n");
  echo("        document.getElementById(\"trIndicePag\").style.display = \"none\";\n");
  echo("      }\n");
  echo("      else\n");
  echo("      {\n");
  echo("        if(trVazia)");
  echo("          trVazia.parentNode.removeChild(trVazia);\n");
  echo("        document.getElementById(\"trIndicaEstadoPag\").style.display = \"\";\n");
  echo("        document.getElementById(\"trIndicePag\").style.display = \"\";\n");
  echo("      }\n");
  echo("    }\n\n");

  if($visualizar == "Q")
  {
    echo("    function VerificaNovoTitulo(textbox, aspas) {\n");
    echo("      texto=textbox.value;\n");
    echo("      if (texto==''){\n");
    echo("        // se nome for vazio, nao pode\n");
    /* Frase #34 - O titulo nao pode ser vazio. */
    echo("        alert(\"".RetornaFraseDaLista($lista_frases,34)."\");\n");
    echo("        textbox.focus();\n");
    echo("        return false;\n");
    echo("      }\n");
    echo("      // se nome tiver aspas, <, >, nao pode - aspas pode ser 1,0\n");
    echo("      else if ((texto.indexOf(\"\\\\\")>=0 || texto.indexOf(\"\\\"\")>=0 || texto.indexOf(\"'\")>=0 || texto.indexOf(\">\")>=0 || texto.indexOf(\"<\")>=0)&&(!aspas)) {\n");
    /* Frase #120 - O titulo deve conter apenas numeros, letras e espacos. */
    echo("         alert(\"".ConverteAspas2BarraAspas(ConverteHtml2Aspas(RetornaFraseDaLista($lista_frases,120)))."\");\n");
    echo("        textbox.value='';\n");
    echo("        textbox.focus();\n");
    echo("        return false;\n");
    echo("      }\n");
    echo("      return true;\n");
    echo("    }\n\n");

    echo("    function EscondeLayers()\n");
    echo("    {\n");
    echo("      hideLayer(lay_nova_questao);\n");
    echo("      hideLayer(cod_comp);\n");
    echo("      hideLayer(lay_exercicios);\n");
    echo("      hideLayer(lay_filtro);\n");
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

    echo("    function NovaQuestao()\n");
    echo("    {\n");
    echo("      MostraLayer(lay_nova_questao, 0);\n");
    echo("      document.form_nova_questao.novo_titulo.value = '';\n");
    echo("      document.getElementById(\"nome\").focus();\n");
    echo("    }\n");

    echo("      function AtualizaComp(js_tipo_comp)\n");
    echo("      {\n");
    echo("        if ((isNav) && (!isMinNS6)) {\n");
    echo("          document.comp.document.form_comp.tipo_comp.value=js_tipo_comp;\n");
    echo("          document.comp.document.form_comp.cod_item.value=js_cod_item;\n");
    echo("          var tipo_comp = new Array(document.comp.document.getElementById('tipo_comp_F'), document.comp.document.getElementById('tipo_comp_N'));\n");
    echo("        } else {\n");
    echo("            document.form_comp.tipo_comp.value=js_tipo_comp;\n");
    echo("            document.form_comp.cod_item.value=js_cod_item;\n");
    echo("            var tipo_comp = new Array(document.getElementById('tipo_comp_F'), document.getElementById('tipo_comp_N'));\n");
    echo("        }\n");
    echo("        var imagem=\"<img src='../imgs/checkmark_blue.gif' />\"\n");
    echo("        if (js_tipo_comp=='F') {\n");
    echo("          tipo_comp[0].innerHTML=imagem;\n");
    echo("          tipo_comp[1].innerHTML=\"&nbsp;\";\n");
    echo("        } else{\n");
    echo("          tipo_comp[0].innerHTML=\"&nbsp;\";\n");
    echo("          tipo_comp[1].innerHTML=imagem;\n");
    echo("        }\n");
    echo("      }\n\n");

    /* *********************************************************************
     entraNoFiltro - Dado o id da questao analisada, verifica se ela entra ou nao no filtro. 
                     Para verificar os campos Topico da questao e Tipo da questao, verifica os elementos referentes a esses campos impressos na tela.
                     Para verificar o campo dificuldade/nivel da questao (nao visivel), verifica o nivel da questao analisada na stringNiveisQuestoes
     Entrada: topico = Topico escolhido no filtro ('T'caso seja selcionado Todos ou STRING do tipo de questao)
              tp_questao = Tipo de questao escolhida no filtro: 'T'(Todos), 'O'(Objetivas) ou 'D'(Dissertativas) 
              dificuldade = Dificuldade escolhida no filtro: 'T'(Todos), 'F'(Facil), 'M'(Medio), 'D'(Dificil)
              stringNiveisQuestoes = string com a dificuldade/nivel de todas as questoes
     Saida: true = caso a questao analisada entre no filtro (deva ser exibida)
            false = caso contrario
     */
    echo("    function entraNoFiltro(id,topico,tp_questao,dificuldade,stringNiveisQuestoes){\n");
    echo("      var id_topico,id_tp_questao,inicial_id_tp_questao,id_dificuldade;\n");
    echo("      id_dificuldade = stringNiveisQuestoes.substr(id-1,1);\n");
    echo("      id_topico = document.getElementById('topico_'+id);\n");
    echo("      id_tp_questao = document.getElementById('tipo_'+id);\n");
    echo("      inicial_id_tp_questao = (id_tp_questao.innerHTML).substr(0,1);\n");
    echo("      if ((topico != 'T') && (id_topico.innerHTML != topico))\n");
    echo("        return false;\n");
    echo("      if ((tp_questao != 'T') && (inicial_id_tp_questao != tp_questao))\n");
    echo("        return false;\n");
    echo("      if ((dificuldade != 'T') && (id_dificuldade != dificuldade))\n");
    echo("        return false;\n");
    echo("      return true;\n");
    echo("    }\n\n");

    /* *********************************************************************
     EscondeQuestoesExcluidas - Percorre o vetor com as questoes a serem excluidas e as remove da tela.
     Entrada: arrayExcluidas = array com o id das questoes a serem excluidas
              n = tamanho do array 
     */
    echo("    function EscondeQuestoesExcluidas(arrayExcluidas,n){\n");
    echo("      var i,tr,tabela;\n");
    echo("      for (i=0; i < n; i++){\n");
    echo("        tr = document.getElementById('trQuestao_'+arrayExcluidas[i]);\n");
    echo("        tabela = tr.parentNode;\n");
    echo("        tabela.removeChild(tr);\n");
    echo("        tr.style.display = \"none\";\n");
    echo("        tabela.appendChild(tr);\n");
    echo("      }\n");
    echo("    }\n\n");

    /* *********************************************************************
     AplicaFiltro - Cria um array com as questoes que nao entram no filtro e devem ser excluidas.
     Entrada: topico = Topico escolhido no filtro ('T'caso seja selcionado Todos ou STRING do tipo de questao)
              tp_questao = Tipo de questao escolhida no filtro: 'T'(Todos), 'O'(Objetivas) ou 'D'(Dissertativas) 
              dificuldade = Dificuldade escolhida no filtro: 'T'(Todos), 'F'(Facil), 'M'(Medio), 'D'(Dificil)
              stringNiveisQuestoes = string com a dificuldade/nivel de todas as questoes
     */
    echo("    function AplicaFiltro(topico,tp_questao, dificuldade, stringNiveisQuestoes){\n");
    echo("      var i,j,questoes,getNumber,arrayExcluidas;\n");
    echo("      j=0;\n");
    echo("      arrayExcluidas = new Array();\n");
    echo("      questoes = document.getElementsByName('cod_questao[]');\n"); // Verifica o numero de questoes que aparecem na tela 
    echo("      for (i=0; i < questoes.length; i++){\n");
    echo("        getNumber = questoes[i].id.split(\"_\");\n"); //getNumber = ["itm", "2"]
    echo("        if (!entraNoFiltro(getNumber[1],topico,tp_questao,dificuldade, stringNiveisQuestoes)){\n");
    echo("          arrayExcluidas[j++] = getNumber[1];\n"); //array excluidas recebe quem NAO entra no filtro
    echo("        }\n");
    echo("      }\n");
    echo("      EscondeQuestoesExcluidas(arrayExcluidas,j);\n");
    echo("      totalQuestoes = questoes.length - j;\n");
    echo("    }\n\n");

    /* *********************************************************************
     Filtrar - filtra as questoes de acordo com a escolha do ususario (topico da questao, tipo da questao e dificuldade)
     Entrada: 	topico = Topico escolhido no filtro ('T'caso seja selcionado Todos ou STRING do tipo de questao)
     		  	tp_questao = Tipo de questao escolhida no filtro: 'T'(Todos), 'O'(Objetivas), 'M'(Multipla Escolha) ou 'D'(Dissertativas) 
     			dificuldade = Dificuldade escolhida no filtro: 'T'(Todos), 'F'(Facil), 'M'(Medio), 'D'(Dificil)
     			stringNiveisQuestoes = string com a dificuldade/nivel de todas as questoes
     */
    echo("    function Filtrar(topico,tp_questao,dificuldade,stringNiveisQuestoes){\n");
    echo("      AplicaFiltro(topico,tp_questao,dificuldade,stringNiveisQuestoes);\n");
    echo("      AtualizaEstadoPaginacao(1);\n");
    /*216- Quest�es Filtradas*/
    echo("      mostraFeedback(\"".RetornaFraseDaLista($lista_frases, 216)."\",true);\n");
    echo("    }\n\n");
  }


  echo("    function DeletarLinhas(deleteArray,j){\n");
  echo("      var i,trQuestao;\n");
  echo("      for(i=0;i<j;i++)\n");
  echo("      {\n");
  echo("        trQuestao = document.getElementById('trQuestao_'+deleteArray[i]);\n");
  echo("        trQuestao.parentNode.removeChild(trQuestao);\n");
  echo("      }\n");
  echo("    }\n\n");

  echo("    function IntercalaCorLinha(){\n");
  echo("      var checks,i,trQuestao;\n");
  echo("      checks = document.getElementsByName('cod_questao[]');\n");
  echo("      corLinha = 0;\n");
  echo("      for (i=0; i<checks.length; i++){\n");
  echo("        getNumber=checks[i].id.split('_');\n");
  echo("        trQuestao = document.getElementById('trQuestao_'+getNumber[1]);\n");
  echo("        trQuestao.className = 'altColor'+(i%2);\n");
  echo("      }\n");
  echo("    }\n\n");

  echo("    function Confirma(op){\n");
  echo("        if(op == 'X')\n");
  /* Frase #144 - Tem certeza que deseja excluir definitivamente as questoes selecionadas? */
  echo("          return confirm('".RetornaFraseDaLista($lista_frases, 144)."');\n");
  echo("        else if(op == 'V')\n");
  /* Frase #145 - Tem certeza que deseja recuperar as questoes selecionadas? */
  echo("          return confirm('".RetornaFraseDaLista($lista_frases, 145)."');\n");
  echo("        else if(op == 'L')\n");
  /* Frase #146 - Tem certeza que deseja enviar para lixeira as questoes selecionadas? */
  echo("          return confirm('".RetornaFraseDaLista($lista_frases, 146)."');\n");
  echo("    }\n\n");

  echo("    function InsereLinhaVazia(){\n");
  echo("      var table,tr,td;");
  echo("      table = document.getElementById(\"tabelaQuestoes\");\n");
  echo("      tr = document.createElement(\"tr\");\n");
  echo("      tr.setAttribute(\"id\",\"trVazia\");\n");
  echo("      td = document.createElement(\"td\");\n");
  echo("      td.colSpan = \"6\";\n");
  /* Frase #35 - Nao ha nenhuma questao */
  echo("      td.appendChild(document.createTextNode('".RetornaFraseDaLista($lista_frases, 35)."'));\n");
  echo("      tr.appendChild(td);\n");
  echo("      table.appendChild(tr);\n");
  echo("    }\n\n");

  echo("    function RetornaTexto(op){\n");
  echo("        if(op == 'X')\n");
  /* Frase #147 - Questoes excluidas da lixeira. */
  echo("          return '".RetornaFraseDaLista($lista_frases, 147)."';\n");
  echo("        else if(op == 'V')\n");
  /* Frase #148 - Quetoes recuperadas. */
  echo("          return '".RetornaFraseDaLista($lista_frases, 148)."';\n");
  echo("        else if(op == 'L')\n");
  /* Frase #149 - Questoes enviadas para a lixeira. */
  echo("          return '".RetornaFraseDaLista($lista_frases, 149)."';\n");
  echo("    }\n\n");

  echo("    function VerificaPaginacao(){\n");
  echo("    if(Math.ceil(totalQuestoes/questoesPorPag) < pagAtual)\n");
  echo("      AtualizaEstadoPaginacao(pagAtual-1)\n");
  echo("    else\n");
  echo("      AtualizaEstadoPaginacao(pagAtual)\n");
  echo("}\n");

  echo("    function TratarSelecionados(op){\n");
  echo("      var checks,deleteArray,j;\n");
  echo("      checks = document.getElementsByName('cod_questao[]');\n");
  echo("      deletaArray = new Array();\n");
  echo("      j=0;\n");
  echo("      if(Confirma(op)){\n");
  //echo("      xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_raiz);\n");
  echo("        for (i=0; i<checks.length; i++){\n");
  echo("          if(checks[i].checked){\n");
  echo("            getNumber=checks[i].id.split(\"_\");\n");
  echo("            xajax_AlteraStatusQuestaoDinamic(".$cod_curso.",getNumber[1],op);\n");
  echo("            deletaArray[j++] = getNumber[1];\n");
  echo("            totalQuestoes--;");
  echo("          }\n");
  echo("        }\n");
  echo("        DeletarLinhas(deletaArray,j);\n");
  echo("        if(totalQuestoes > 0)\n");
  echo("          IntercalaCorLinha();\n");
  echo("        else\n");
  echo("          InsereLinhaVazia();\n");
  echo("        VerificaPaginacao();\n");
  echo("        ControlaSelecao();\n");
  echo("        mostraFeedback(RetornaTexto(op),true);\n");
  echo("      }\n");
  echo("    }\n\n");

  echo("\n</script>\n\n");

  $objAjax->printJavascript();

  echo("  <script  type=\"text/javascript\" src=\"jscriptlib.js\"> </script>\n");

  /* fim - JavaScript */
  /*********************************************************/

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  if ($tela_formador)
  {
    /* Frase #1 - Exercicios */
    /* Frase #112 - Biblioteca de Questoes */
    $frase = RetornaFraseDaLista($lista_frases, 1)." - ".RetornaFraseDaLista($lista_frases, 112);
    if($visualizar == "L")
    /* Frase #128 - Lixeira */
    $frase = $frase." - ".RetornaFraseDaLista($lista_frases, 128);

    echo("          <h4>".$frase."</h4>\n");

    /* Frase #5 - Voltar */
    /* 509 - Voltar */
    echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

    echo("          <div id=\"mudarFonte\">\n");
    echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
    echo("          </div>\n");

    echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
    echo("            <form id='incluirQuestoes' name='incluirQuestoes' method='POST' action='acoes.php'>");
    echo("            <tr>\n");
    echo("              <td valign=\"top\">\n");

    echo("                <ul class=\"btAuxTabs\">\n");

    /* Frase #109 - Exercicios Individuais */
    echo("                  <li><a href='exercicio.php?cod_curso=".$cod_curso."&visualizar=I&agrupar=A'>".RetornaFraseDaLista($lista_frases, 109)."</a></li>\n");

    /* Frase #110 - Exercicios em Grupo */
    echo("                  <li><a href='exercicio.php?cod_curso=".$cod_curso."&visualizar=G&agrupar=G'>".RetornaFraseDaLista($lista_frases, 110)."</a></li>\n");

    echo("                </ul>\n");
    echo("              </td>\n");
    echo("            </tr>\n");
    echo("            <tr>\n");
    echo("              <td>\n");
    echo("                <ul class=\"btAuxTabs03\">\n");
    if($visualizar == "Q")
    {
      if($cod_exercicio != null)
      /* Frase #150 - Voltar a edicao do exercicio */
      echo("                  <li><span onclick=\"document.location='editar_exercicio.php?cod_curso=".$cod_curso."&cod_exercicio=".$cod_exercicio."';\">".RetornaFraseDaLista($lista_frases, 150)."</span></li>\n");
      /* Frase #151 - Nova questao */
      echo("                  <li><span onclick=\"NovaQuestao();\">".RetornaFraseDaLista($lista_frases, 151)."</span></li>\n");
      /* Frase #152 - Filtrar */
      echo("                  <li><span onclick=\"MostraLayer(lay_filtro,0);\">".RetornaFraseDaLista($lista_frases, 152)."</span></li>\n");
      /* Frase #111 - Biblioteca de Exercicios */
      echo("                  <li><a href='exercicios.php?cod_curso=".$cod_curso."&visualizar=E'>".RetornaFraseDaLista($lista_frases, 111)."</a></li>\n");
      /* Frase #112 - Biblioteca de Questoes */
      echo("                  <li><a href='questoes.php?cod_curso=".$cod_curso."&visualizar=Q'>".RetornaFraseDaLista($lista_frases, 112)."</a></li>\n");
      /* Frase #128 - Lixeira */
      echo("                  <li><span onclick=\"document.location='questoes.php?cod_curso=".$cod_curso."&visualizar=L';\">".RetornaFraseDaLista($lista_frases, 128)."</span></li>\n");
    }
    echo("                </ul>\n");
    echo("              </td>\n");
    echo("            </tr>\n");
    echo("            <tr>\n");
    echo("              <td>\n");

    if($totalQuestoes > 0){
      // Calcula o índice da primeira mensagem.
      $primQuestaoIndex = (($pagAtual - 1) * $questoesPorPag) + 1;
      // Calcula o índice da última mensagem.
      $ultQuestaoIndex = $pagAtual * $questoesPorPag;

      // Se o índice da ultima mensagem for maior que o número de mensagens, então copia este
      // para o índice da última mensagem.
      if ($ultQuestaoIndex > ($totalQuestoes))
      $ultQuestaoIndex = ($totalQuestoes);
      echo("            <tr class=\"head01\" id=\"trIndicaEstadoPag\">\n");
      echo("              <td colspan=\"6\">\n");
      /* Frase #59 - Questoes */
      echo("                ".RetornaFraseDaLista($lista_frases, 59)." ");
      echo("(<span id=\"primQuestaoIndex\"></span>");
      /* Frase #221 - a             */
      echo(" ".RetornaFraseDaLista($lista_frases, 221)."&nbsp;");
      /* Frase #222 - de             */
      echo("<span id=\"ultQuestaoIndex\"></span> ".RetornaFraseDaLista($lista_frases, 222)." ");
      echo("<span id=\"totalQuestoes\">".($totalQuestoes)."</span>)\n");
      echo("              </td>\n");
      echo("            </tr>\n");
    }

    echo("            <tr>\n");
    echo("              <td>\n");

    if($totalQuestoes){
      echo("                <table border=\"0\" width=\"100%\" cellspacing=\"0\" style=\"cellpadding:0pt;\" class=\"sortable tabInterna\" id=\"tabelaQuestoes\">\n");
    }else{
      echo("                <table border=\"0\" width=\"100%\" cellspacing=\"0\" style=\"cellpadding:0pt;\" class=\"sortable tabInterna\">\n");
    }
    echo("                <thead>\n");

    echo("                  <tr class=\"head\">\n");
    if($totalQuestoes){
      echo("                    <td class=\"sorttable_nosort\" width=\"2%\"><input type=\"checkbox\" id=\"checkMenu\" onclick=\"MarcaOuDesmarcaTodos(pagAtual);\" /></td>\n");
    }else{
      echo("                    <td width=\"2%\"><input type=\"checkbox\" id=\"checkMenu\" onclick=\"MarcaOuDesmarcaTodos(pagAtual);\" /></td>\n");
    }
    /* Frase #13 - Titulo */
    echo("                    <td class=\"alLeft\" style=\"cursor:pointer\">".RetornaFraseDaLista($lista_frases, 13)."</td>\n");
    /* Frase #69 - Data */
    echo("                    <td width=\"10%\" style=\"cursor:pointer\">".RetornaFraseDaLista($lista_frases, 69)."</td>\n");
    /* Frase #61 - Topico */
    echo("                    <td width=\"15%\" style=\"cursor:pointer\">".RetornaFraseDaLista($lista_frases, 61)."</td>\n");
    if($visualizar == "Q")
    {
      /* Frase #60 - Tipo */
      echo("                    <td width=\"12%\" style=\"cursor:pointer\">".RetornaFraseDaLista($lista_frases, 60)."</td>\n");
      /* Frase #57 - Compartilhamento */
      echo("                    <td width=\"15%\" style=\"cursor:pointer\">".RetornaFraseDaLista($lista_frases, 57)."</td>\n");
    }
    echo("                  </tr>\n");
    echo("                </thead>\n");
    echo("                <tbody>\n");

    //numero de mensagens em uma determinada pagina
    $numQuestoesPag = 0;
    $numPagina = 1;

    if ((count($lista_questoes)>0)&&($lista_questoes != null))
    {
      $stringNiveisQuestoes = "";
      foreach ($lista_questoes as $cod => $linha_item)
      {
        /**
        * Cria um vetor com a dificuldade de cada questao para ser enviado ao se filtrar uma questao (alline)
        */
        $dadosQuestao = RetornaQuestao($sock,$linha_item['cod_questao']);
        $stringNiveisQuestoes =  $stringNiveisQuestoes.$dadosQuestao['nivel'];
        $aplicada = QuestaoAplicada($sock, $linha_item["cod_questao"]);  /* Verifica se a questao foi aplicada. */

        if($numQuestoesPag == $questoesPorPag){
          $numPagina++;
          $numQuestoesPag = 0;
        }
        if($numPagina == $pagAtual) $style = "";
        else $style = "display:none";

        $data = "<span id=\"data_".$linha_item['cod_questao']."\">".UnixTime2Data($linha_item['data'])."</span>";
        if($linha_item['tp_questao'] == 'O')
          $tipo = RetornaFraseDaLista($lista_frases, 159);
        elseif($linha_item['tp_questao'] == 'M')  //Adicionado tipo de questao Multipla Escolha
          $tipo = RetornaFraseDaLista($lista_frases, 212);
        elseif($linha_item['tp_questao'] == 'D')
          $tipo = RetornaFraseDaLista($lista_frases, 160);

        $titulo = $linha_item['titulo'];
        $topico = RetornaNomeTopico($sock,$linha_item['cod_topico']);
        $icone = "<img src=\"../imgs/arqp.gif\" alt=\"\" border=\"0\" /> ";

        /* Frase #6 - Compartilhado com Formadores */
        if($linha_item['tipo_compartilhamento'] == "F")
        $compartilhamento = RetornaFraseDaLista($lista_frases, 6);
        /* Frase #8 - Nao compartilhado */
        else
        $compartilhamento = RetornaFraseDaLista($lista_frases, 8);

        if($cod_usuario == $linha_item['cod_usuario'])
        $link_compartilhamento = "<span id=\"comp_".$linha_item['cod_questao']."\" class=\"link\" onclick=\"js_cod_item='".$linha_item['cod_questao']."';AtualizaComp('".$linha_item['tipo_compartilhamento']."');MostraLayer(cod_comp,140,event);return(false);\">".$compartilhamento."</span>";
        if($cod_usuario == $linha_item['cod_usuario'] || $linha_item['tipo_compartilhamento'] == "F"){
          echo("                  <tr class=\"altColor".($cod%2)."\" id=\"trQuestao_".$linha_item['cod_questao']."\" style=\"".$style."\">\n");
          echo("                    <td width=\"2\"><input type=\"checkbox\" name=\"cod_questao[]\" id=\"itm_".$linha_item['cod_questao']."\" onclick=\"ControlaSelecao();\" value=\"".$linha_item['cod_questao']."\" /></td>\n");

          if($visualizar == "Q")
            /* Se a visualizacao for de questoes, nao passa o parametro lixeira pelo post. */
            $lixeira = "";
          else
            /* Caso contrario, passa como parametro pelo post. */
            $lixeira = "&lixeira=ok";

          if($aplicada) {  //Se a questao foi aplicada, coloca aviso na frente do titulo e tira o link de compartilhamento
            $texto_aplicada=RetornaFraseDaLista($lista_frases, 228);  /* Frase #228 - (Questao aplicada) */
            $link_compartilhamento="<span id=\"comp_".$linha_item['cod_questao']."\">".$compartilhamento."</span>";
          } else {
            $texto_aplicada = "";
          }

          echo("                    <td align=left>".$icone."<a href=\"editar_questao.php?cod_curso=".$cod_curso."&cod_questao=".$linha_item['cod_questao']."&tp_questao=".$linha_item['tp_questao']."".$lixeira."\">".$titulo."&nbsp;".$texto_aplicada."</a></td>\n");
          echo("                    <td>".$data."</td>\n");
          echo("                    <td id=\"topico_".$linha_item['cod_questao']."\">".$topico."</td>\n");
          if($visualizar == "Q")
          {
            echo("                    <td id=\"tipo_".$linha_item['cod_questao']."\">".$tipo."</td>\n");
            echo("                    <td>".$link_compartilhamento."</td>\n");
          }
          echo("                  </tr>\n");
        }
        $numQuestoesPag++;
      }
    }
    else
    {
      echo("                  <tr>\n");
      /* Frase #35 - Nao ha nenhuma questao */
      echo("                    <td colspan=\"6\">".RetornaFraseDaLista($lista_frases, 35)."</td>\n");
      echo("                  </tr>\n");
    }

    $colspan = 3;
    if($visualizar == 'L')
    $colspan = 2;

    echo("                </tbody>\n");
    echo("                <tfoot>\n");
    echo("                  <tr id=\"trIndicePag\">\n");
    echo("                    <td colspan=\"".$colspan."\" align=\"left\" style=\"border-right:none\">\n");
    /* Frase #153 - clique no cabecalho para ordenar as questoes */
    if($totalQuestoes>1)
    echo("                      *".RetornaFraseDaLista($lista_frases, 153)."\n");
    echo("                    </td>\n");

    echo("                    <td colspan=\"".$colspan."\" align=\"right\">\n");
    echo("                    <span id=\"paginacao_first\"></span> <span id=\"paginacao_back\"></span>\n");
    $controle=1;
    while($controle<=5){
      echo("                      <span id=\"paginacao_".$controle."\"></span>\n");
      $controle++;
    }
    echo("                    <span id=\"paginacao_fwd\"></span> <span id=\"paginacao_last\"></span>\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
    echo("                </tfoot>\n");
    echo("                </table>\n");


    if($visualizar == "Q")
    {
      echo("                <ul>\n");
      /* Frase #64 - Apagar selecionadas */
      echo("                  <li id=\"mExcluir_Selec\" class=\"menuUp\"><span id=\"eapagarrSelec\">".RetornaFraseDaLista($lista_frases, 64)."</span></li>\n");
      if($cod_exercicio == null)
      {
        /* Frase #154 - Incluir selecionadas em um exercicio */
        echo("                  <li id=\"mIncluir_Selec\" class=\"menuUp\"><span id=\"eincluirSelec\">".RetornaFraseDaLista($lista_frases, 154)."</span></li>\n");
      }
      else
      {
        /* Frase #155 - Incluir selecionadas no exercicio */
        echo("                  <li id=\"mIncluir_Selec\" class=\"menuUp\"><span id=\"eincluirSelec\">".RetornaFraseDaLista($lista_frases, 155)."</span></li>\n");
      }
      echo("                </ul>\n");
    }
    else if($visualizar == "L")
    {
      echo("                <ul>\n");
      /* Frase #64 - Apagar selecionadas */
      echo("                  <li id=\"mExcluir_Selec\" class=\"menuUp\"><span id=\"eapagarrSelec\">".RetornaFraseDaLista($lista_frases, 64)."</span></li>\n");
      /* Frase #156 - Recuperar selecionadas */
      echo("                  <li id=\"mRecup_Selec\" class=\"menuUp\"><span id=\"recuperarSelec\">".RetornaFraseDaLista($lista_frases, 156)."</span></li>\n");
      echo("                </ul>\n");
    }

    echo("              </td>\n");
    echo("            </tr>\n");
    echo("          </table>\n");
    echo("          <br />\n");
    /* 509 - voltar, 510 - topo */
    echo("          <ul class=\"btsNavBottom\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span><span><a href=\"#topo\">&nbsp;".RetornaFraseDaLista($lista_frases_geral,510)."&nbsp;&#94;&nbsp;</a></span></li></ul>\n");
    /* Nao Formador */
  }
  else
  {
    /* Frase #1 - Exercicios */
    /* Frase #74 - Area restrita ao formador. */
    echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases,74)."</h4>\n");

    /* Frase #5 - Voltar */
    /* 509 - Voltar */
    echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

    echo("          <div id=\"mudarFonte\">\n");
    echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
    echo("          </div>\n");

    /* 23 - Voltar (gen) */
    echo("<input class=\"input\" type=button value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onclick=\"history.go(-1);\" />\n");
  }

  echo("        </td>\n");
  echo("      </tr>\n");

  include("../tela2.php");

  if($tela_formador && $visualizar == "Q")
  {
    /* Exercicios */
    echo("    <div id=\"layer_exercicios\" class=\"popup\">\n");
    echo("     <div class=\"posX\"><span onclick=\"EscondeLayer(lay_exercicios);\"><img src=\"../imgs/btClose.gif\" alt=\"".RetornaFraseDaLista($lista_frases_geral,13)."\" border=\"0\" /></span></div>\n");
    echo("      <div class=\"int_popup\">\n");
    echo("        <div class=\"ulPopup\">\n");

    echo("					<input type='hidden' name='acao' value='incluirQuestao'/>");
    echo("					<input type='hidden' name='cod_curso' value='".$cod_curso."'/>");

    /* Frase #157 - Escolha um exercicio: */
    echo("            ".RetornaFraseDaLista($lista_frases, 157)."<br />\n");
    echo("            <select name='cod_exercicio' class=\"input\" id=\"select_exercicio\">\n");

    if ((count($lista_exercicios)>0)&&($lista_exercicios != null))
    {
      foreach ($lista_exercicios as $cod => $linha_item)
      {
        if($linha_item['cod_exercicio'] == $cod_exercicio)
        $selected = "selected";
        else
        $selected = "";

        echo("              <option name='cod_exercicio' value=\"".$linha_item['cod_exercicio']."\" ".$selected.">".$linha_item['titulo']."</option>\n");
      }
    }

    echo("            </select><br /><br />\n");
    /* 18 - Ok (gen) */
    echo("            <input type=\"submit\" class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" />\n");
    /* 2 - Cancelar (gen) */
    echo("            &nbsp; &nbsp; <input type=\"button\" class=\"input\"  onClick=\"EscondeLayer(lay_exercicios);\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");
    echo("					</form>");
    echo("        </div>\n");
    echo("      </div>\n");
    echo("    </div>\n\n");

    /* Nova Questao */
    echo("    <div id=\"layer_nova_questao\" class=\"popup\">\n");
    echo("     <div class=\"posX\"><span onclick=\"EscondeLayer(lay_nova_questao);\"><img src=\"../imgs/btClose.gif\" alt=\"".RetornaFraseDaLista($lista_frases_geral,13)."\" border=\"0\" /></span></div>\n");
    echo("      <div class=\"int_popup\">\n");
    echo("        <form name=form_nova_questao method=post action=acoes.php onSubmit='return(VerificaNovoTitulo(document.form_nova_questao.novo_titulo, 1));'>\n");
    echo("          <div class=\"ulPopup\">\n");
    /* Frase #13 - Titulo */
    echo("            ".RetornaFraseDaLista($lista_frases, 13).":<br />\n");
    echo("            <input class=\"input\" type=\"text\" name=\"novo_titulo\" id=\"nome\" value=\"\" maxlength=150 /><br />\n");
    /* Frase #158 - Tipo da questao */
    echo("            ".RetornaFraseDaLista($lista_frases, 158).":<br />\n");
    echo("            <select class=\"input\" name=\"tp_questao\">");
    /* Frase #159 - Objetiva */
    echo("              <option value=\"O\" selected>".RetornaFraseDaLista($lista_frases, 159)."</option>");
    /* Frase #212 - Multipla escolha */
    echo("              <option value=\"M\">".RetornaFraseDaLista($lista_frases, 212)."</option>");

    /* Frase #160 - Dissertativa */
    echo("              <option value=\"D\">".RetornaFraseDaLista($lista_frases, 160)."</option>");
    echo("            </select><br /><br />");
    echo("            <input type=\"hidden\" name=cod_curso value=\"".$cod_curso."\" />\n");
    echo("            <input type=\"hidden\" name=acao value=criarQuestao />\n");
    echo("            <input type=\"hidden\" name=cod_usuario value=\"".$cod_usuario."\" />\n");
    echo("            <input type=\"hidden\" name=origem value=questoes />\n");
    /* 18 - Ok (gen) */
    echo("            <input type=\"submit\" id=\"ok_novaquestao\" class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" />\n");
    /* 2 - Cancelar (gen) */
    echo("            &nbsp; &nbsp; <input type=\"button\" class=\"input\"  onClick=\"EscondeLayer(lay_nova_questao);\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");
    echo("         </div>\n");
    echo("        </form>\n");
    echo("      </div>\n");
    echo("    </div>\n\n");


    /* Mudar Compartilhamento */
    echo("    <div class=\"popup\" id=\"comp\">\n");
    echo("      <div class=\"posX\"><span onclick=\"EscondeLayer(cod_comp);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"".RetornaFraseDaLista($lista_frases_geral,13)."\" border=\"0\" /></span></div>\n");
    echo("      <div class=\"int_popup\">\n");
    echo("        <form name=\"form_comp\" action=\"\" id=\"form_comp\">\n");
    echo("          <input type=\"hidden\" name=cod_curso value=\"".$cod_curso."\" />\n");
    echo("          <input type=\"hidden\" name=cod_usuario value=\"".$cod_usuario."\" />\n");
    echo("          <input type=\"hidden\" name=cod_item value=\"\" />\n");
    echo("          <input type=\"hidden\" name=tipo_comp id=tipo_comp value=\"\" />\n");
    echo("          <input type=\"hidden\" name=texto id=texto value=\"Texto\" />\n");
    echo("          <ul class=\"ulPopup\">\n");
    echo("            <li onClick=\"document.getElementById('tipo_comp').value='F'; xajax_MudarCompartilhamentoDinamic(xajax.getFormValues('form_comp'), 'Compartilhado com formadores','Q'); EscondeLayers();\">\n");
    echo("              <span id=\"tipo_comp_F\" class=\"check\"></span>\n");
    /* Frase #6 - Compartilhado com formadores */
    echo("              <span>".RetornaFraseDaLista($lista_frases, 6)."</span>\n");
    echo("            </li>\n");
    echo("            <li onClick=\"document.getElementById('tipo_comp').value='N'; xajax_MudarCompartilhamentoDinamic(xajax.getFormValues('form_comp'), 'Nao Compartilhado', 'Q'); EscondeLayers();\">\n");
    echo("              <span id=\"tipo_comp_N\" class=\"check\"></span>\n");
    /* Frase #8 - N�o Compartilhado */
    echo("              <span>".RetornaFraseDaLista($lista_frases, 8)."</span>\n");
    echo("            </li>\n");
    echo("          </ul>\n");
    echo("        </form>\n");
    echo("      </div>\n");
    echo("    </div>\n");

    /* Filtro */
    echo("    <div id=\"layer_filtro\" class=\"popup\">\n");
    echo("      <div class=\"posX\"><span onclick=\"EscondeLayer(lay_filtro);\"><img src=\"../imgs/btClose.gif\" alt=\"".RetornaFraseDaLista($lista_frases_geral,13)."\" border=\"0\" /></span></div>\n");
    echo("      <div class=\"int_popup\">\n");
    echo("        <div class=\"ulPopup\">\n");
    /* Frase #61 - Topico */
    echo("            ".RetornaFraseDaLista($lista_frases, 61).":<br />\n");
    echo("            <select class=\"input\" id=\"topico\">");
    /* Frase #161 - Todos */
    echo("              <option value=\"T\" selected>".RetornaFraseDaLista($lista_frases, 161)."</option>");

    $topicos = RetornaTopicos($sock);

    if ((count($topicos)>0)&&($topicos != null))
    {
      foreach ($topicos as $cod => $linha_item)
      {
        $topico = $linha_item['topico'];
        echo("              <option value=\"".$topico."\">".$topico."</option>\n");
      }
    }

    echo("            </select><br /><br />");
    /* Frase #158 - Tipo da questao */
    echo("            ".RetornaFraseDaLista($lista_frases, 158).":<br />\n");
    echo("            <select class=\"input\" id=\"tp_questao\">");
    /* Frase #162 - Todas */
    echo("              <option value=\"T\" selected>".RetornaFraseDaLista($lista_frases, 162)."</option>");
    /* Frase #159 - Objetiva */
    echo("              <option value=\"O\">".RetornaFraseDaLista($lista_frases, 159)."</option>");
    /* Frase #212 - Multipla escolha */
    echo("              <option value=\"M\">".RetornaFraseDaLista($lista_frases, 212)."</option>");
    /* Frase #160 - Dissertativa */
    echo("              <option value=\"D\">".RetornaFraseDaLista($lista_frases, 160)."</option>");
    echo("            </select><br /><br />");
    /* Frase #62 - Dificuldade */
    echo("            ".RetornaFraseDaLista($lista_frases, 62).":<br />\n");
    echo("            <select class=\"input\" id=\"dificuldade\">");
    /* Frase #162 - Todas */
    echo("              <option value=\"T\" selected>".RetornaFraseDaLista($lista_frases, 162)."</option>");
    /* Frase #102 - Facil */
    echo("              <option value=\"F\">".RetornaFraseDaLista($lista_frases, 102)."</option>");
    /* Frase #101 - Medio */
    echo("              <option value=\"M\">".RetornaFraseDaLista($lista_frases, 101)."</option>");
    /* Frase #100 - Dificil */
    echo("              <option value=\"D\">".RetornaFraseDaLista($lista_frases, 100)."</option>");
    echo("            </select><br /><br />");
    /* 18 - Ok (gen) */
    echo("            <input type=\"button\" id=\"ok_novaquestao\" onClick=\"Filtrar(document.getElementById('topico').value,document.getElementById('tp_questao').value,document.getElementById('dificuldade').value,'".$stringNiveisQuestoes."');EscondeLayer(lay_filtro);\" class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" />\n");
    /* 2 - Cancelar (gen) */
    echo("            &nbsp; &nbsp; <input type=\"button\" class=\"input\"  onClick=\"EscondeLayer(lay_filtro);\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");
    echo("        </div>\n");
    echo("      </div>\n");
    echo("    </div>\n\n");
  }

  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>