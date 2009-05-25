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

  require_once("../xajax_0.2.4/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  //Registre os nomes das funcoes em PHP que voce quer chamar atraves do xajax
  $objAjax->registerFunction("AlteraStatusQuestaoDinamic");
  $objAjax->registerFunction("MudarCompartilhamentoDinamic");
  $objAjax->registerFunction("AdicionaQuestaoAoExercicioDinamic");
  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequests();

  $cod_ferramenta=24;
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
        
  /* Nmero de questoes exibidas por p�ina.             */
  if (!isset($questoesPorPag)) $questoesPorPag = 10;
  
  /* Se o nmero total de questoes for superior que o nmero de questoes por  */
  /* p�ina ent� calcula o total de p�inas. Do contr�io, define o nmero de     */
  /* p�inas para 1.                                                           */

  /* Calcula o nmero de p�inas geradas.    */
  if($totalQuestoes > $questoesPorPag)
    $totalPag = ceil($totalQuestoes / $questoesPorPag);
  else
    $totalPag = 1;

  /* Se a p�ina atual n� estiver setada ent�, por padr�, atribui-lhe o valor 1. */
  /* Se estiver setada, verifica se a p�ina �maior que o total de p�inas, se for */
  /* atribui o valor de $total_pag �$pagAtual.                                    */
  if ((!isset($pagAtual))or($pagAtual=='')or($pagAtual==0))
    $pagAtual =  1;
  else $pagAtual = min($pagAtual, $totalPag);
  
  

  /*********************************************************/
  /* in�io - JavaScript */
  if($totalQuestoes){
    echo("  <script type=\"text/javascript\" src=\"../js-css/sorttable.js\"></script>\n");
  }
  echo("  <script  type=\"text/javascript\" language=\"JavaScript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
  echo("  <script  type=\"text/javascript\" src=\"jscriptlib.js\"> </script>\n");
  echo("  <script  type=\"text/javascript\" language=\"JavaScript\">\n\n");
  
  echo("    var js_cod_item;\n");
  echo("    var js_comp = new Array();\n");
  echo("    var pagAtual = ".$pagAtual.";\n");
  echo("    var totalQuestoes = ".$totalQuestoes.";\n");
  echo("    var totalPag = ".$totalPag.";\n");
  echo("    var topico = 'T';\n");
  echo("    var tp_questao = 'T';\n");
  echo("    var dificuldade = 'T';\n");
  echo("    var window_handle;\n");
  echo("    var t;\n");
  echo("    this.name = 'principal';\n\n");
  

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
  
  echo("      function ExibeMsgOrdenadas(){\n");
  echo("        var t;");
  echo("        AplicaFiltro();\n"); 
  echo("        AtualizaEstadoPaginacao(pagAtual);\n");
  //?
  echo("        mostraFeedback(\"Questoes ordenadas.\",true);\n");
  echo("      }\n\n");
    
  echo("      function MarcaOuDesmarcaTodos(pagAtual){\n");
  echo("        var e;\n");
  echo("        var i;\n");
  echo("        var inicio;\n");
  echo("        var final;\n");
  echo("        var elementos = document.getElementsByName('chk[]')\n");      
  echo("        inicio = ((pagAtual-1)*".$questoesPorPag.");\n");
  echo("        final = ((pagAtual)*".$questoesPorPag.");\n");
  echo("        controle = (pagAtual-1)*".$questoesPorPag.";\n");
  echo("        controle = elementos.length - controle;\n");
  echo("        if(controle < final) {final = inicio + controle;}\n");
  echo("        var CabMarcado = document.getElementById('checkMenu').checked;\n");
  echo("        for(i = inicio; i < final; i++){\n");
  echo("          e = document.getElementsByName('chk[]')[i];\n");
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
  echo("        var elementos = document.getElementsByName('chk[]')\n");
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
      echo("        document.getElementById('mIncluir_Selec').onclick=function(){ IncluirQuestoesNoExercicio(".$cod_exercicio."); };\n");
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
                  /* 15 - O titulo nao pode ser vazio. */
  	echo("        alert(\"".RetornaFraseDaLista($lista_frases,15)."\");\n");
  	echo("        textbox.focus();\n");
  	echo("        return false;\n");
  	echo("      }\n");
  	echo("      // se nome tiver aspas, <, >, nao pode - aspas pode ser 1,0\n");
  	echo("      else if ((texto.indexOf(\"\\\\\")>=0 || texto.indexOf(\"\\\"\")>=0 || texto.indexOf(\"'\")>=0 || texto.indexOf(\">\")>=0 || texto.indexOf(\"<\")>=0)&&(!aspas)) {\n");
                /* 16 - O t�tulo n�o pode conter \\. */
  	echo("         alert(\"".ConverteAspas2BarraAspas(ConverteHtml2Aspas(RetornaFraseDaLista($lista_frases,16)))."\");\n");
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
        
    echo("    function IncluirQuestoesNoExercicio(cod_exercicio){\n");
    echo("      var i,questoes,getNumber;\n");
    echo("      questoes = document.getElementsByName('chk[]');\n");
    echo("      for (i=0; i < questoes.length; i++){\n");
    echo("        if (questoes[i].checked){\n");
    echo("          getNumber = questoes[i].id.split(\"_\");\n");
    echo("          xajax_AdicionaQuestaoAoExercicioDinamic(cod_curso,cod_exercicio,getNumber[1]);\n");
    echo("        }\n");
    echo("      }\n");
    //?
    echo("      mostraFeedback(\"Questoes adiocionadas ao exercicio com sucesso.\",true);\n");
    echo("    }\n\n");
    
    echo("    function entraNoFiltro(id){\n");
    echo("      var tdTopico,tdTipo;\n");
    echo("      tdTopico = document.getElementById('topico_'+id);\n");
    echo("      tdTipo = document.getElementById('tipo_'+id);\n");
    echo("      if(topico != 'T' && tdTopico.innerHTML != topico)\n");
    echo("        return false;\n");
    echo("      if(tp_questao != 'T' && tdTipo.innerHTML != tp_questao)\n");
    echo("        return false;\n");
    echo("      return true;\n");
    echo("    }\n\n");
    
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
    
    echo("    function AplicaFiltro(){\n");
    echo("      var i,j,questoes,getNumber,arrayExcluidas;\n");
    echo("      j=0;\n");
    echo("      arrayExcluidas = new Array();\n");
    echo("      questoes = document.getElementsByName('chk[]');\n");
    echo("      for (i=0; i < questoes.length; i++){\n");
    echo("        getNumber = questoes[i].id.split(\"_\");\n");
    echo("        if (!entraNoFiltro(getNumber[1])){\n");
    echo("          arrayExcluidas[j++] = getNumber[1];\n");
    echo("        }\n");
    echo("      }\n");
    echo("      EscondeQuestoesExcluidas(arrayExcluidas,j);\n");
    echo("      totalQuestoes = questoes.length - j;\n");
    echo("    }\n\n");
    
    echo("    function Filtrar(topic,tipo){\n");
    echo("      topico = topic;\n");
    echo("      tp_questao = tipo;\n");
    echo("      AplicaFiltro();\n");
    echo("      AtualizaEstadoPaginacao(1);\n");
    //?
    echo("      mostraFeedback(\"Questoes filtradas.\",true);\n");
    echo("    }\n\n");
  }


  echo("    function DeletarLinhas(deleteArray,j){\n");
  echo("      var i,trQuestao;\n");
  echo("	  for(i=0;i<j;i++)\n");
  echo("      {\n");
  echo("        trQuestao = document.getElementById('trQuestao_'+deleteArray[i]);\n");
  echo("        trQuestao.parentNode.removeChild(trQuestao);\n");
  echo("	  }\n");
  echo("    }\n\n");
  amp;
  echo("    function IntercalaCorLinha(){\n");
  echo("      var checks,i,trQuestao;\n");
  echo("      checks = document.getElementsByName('chk[]');\n");
  echo("      corLinha = 0;\n");
  echo("      for (i=0; i<checks.length; i++){\n");
  echo("        getNumber=checks[i].id.split('_');\n");
  echo("        trQuestao = document.getElementById('trQuestao_'+getNumber[1]);\n");
  echo("        trQuestao.className = 'altColor'+(i%2);\n");
  echo("      }\n");
  echo("    }\n\n");
  
  echo("    function Confirma(op){\n");
  echo("        if(op == 'X')\n");
  echo("          return confirm('Tem certeza que deseja excluir definitivamente as questoes selecionadas?');\n");
  echo("        else if(op == 'V')\n");
  echo("          return confirm('Tem certeza que deseja recuperar os exercicios selecionadas?');\n");
  echo("        else if(op == 'L')\n");
  echo("          return confirm('Tem certeza que deseja enviar para lixeira as questoes selecionadas?');\n");
  echo("    }\n\n");
  
  echo("    function InsereLinhaVazia(){\n");
  echo("	  var table,tr,td;");
  echo("	  table = document.getElementById(\"tabelaQuestoes\");\n");
  echo("	  tr = document.createElement(\"tr\");\n");
  echo("      tr.setAttribute(\"id\",\"trVazia\");\n");
  echo("	  td = document.createElement(\"td\");\n");
  echo("	  td.colSpan = \"6\";\n");
  //?
  echo("	  td.appendChild(document.createTextNode('Nao ha nenhuma questao'));\n");
  echo("	  tr.appendChild(td);\n");
  echo("	  table.appendChild(tr);\n");
  echo("    }\n\n");
  
  echo("    function RetornaTexto(op){\n");
  echo("        if(op == 'X')\n");
  echo("          return 'Questao(oes) excluida(s) da lixeira.';\n");
  echo("        else if(op == 'V')\n");
  echo("          return 'Questao(oes) recuperada(s).';\n");
  echo("        else if(op == 'L')\n");
  echo("          return 'Questao(oes) enviada(s) para lixeira.';\n");
  echo("    }\n\n");
    
  echo("    function TratarSelecionados(op){\n");
  echo("	  var checks,deleteArray,j;\n");
  echo("      checks = document.getElementsByName('chk[]');\n");
  echo("	  deletaArray = new Array();\n");
  echo("      j=0;\n");
  echo("      if(Confirma(op)){\n");
  //echo("      xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_raiz);\n");
  echo("        for (i=0; i<checks.length; i++){\n");
  echo("        if(checks[i].checked){\n");
  echo("          getNumber=checks[i].id.split(\"_\");\n");
  echo("          xajax_AlteraStatusQuestaoDinamic(".$cod_curso.",getNumber[1],op);\n");
  echo("          deletaArray[j++] = getNumber[1];\n");
  echo("		  totalQuestoes--;");
  echo("          }\n");
  echo("        }\n");
  echo("		DeletarLinhas(deletaArray,j);\n");
  echo("		if(totalQuestoes > 0)\n");
  echo("          IntercalaCorLinha();\n");
  echo("		else\n");
  echo("          InsereLinhaVazia();\n");
  echo("        AtualizaEstadoPaginacao();\n");
  echo("        ControlaSelecao();\n");
  echo("		mostraFeedback(RetornaTexto(op),true);\n");
  echo("      }\n");
  echo("    }\n\n");
  
  echo("\n</script>\n\n");
  /* fim - JavaScript */
  /*********************************************************/

  $objAjax->printJavascript("../xajax_0.2.4/");

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  if ($tela_formador)
  {
	/* ? - Exercicios*/
        /* ? - Biblioteca de Questoes*/
  	$frase = "Exercicios - Biblioteca de Questoes";
  	if($visualizar == "L")
  		$frase = $frase." - Lixeira";
  
	echo("          <h4>".$frase."</h4>\n");
	
  	/*Voltar*/
  	echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

  	echo("          <div id=\"mudarFonte\">\n");
  	echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  	echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  	echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  	echo("          </div>\n");

	echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
	echo("            <tr>\n");
	echo("              <td valign=\"top\">\n");

  	echo("                <ul class=\"btAuxTabs\">\n");
  	
    /* ? - Exercicios Individuais */
    echo("                  <li><a href='exercicio.php?cod_curso=".$cod_curso."&visualizar=I'>Exercicios Individuais</a></li>\n");
    
    /* ? - Exercicios em Grupo */
    echo("                  <li><a href='exercicio.php?cod_curso=".$cod_curso."&visualizar=G'>Exercicios em Grupo</a></li>\n");

    /* ? - Biblioteca de Exercicios */
    echo("                  <li><a href='exercicios.php?cod_curso=".$cod_curso."&visualizar=E'>Biblioteca de Exercicios</a></li>\n");
    
    /* ? - Biblioteca de Questoes */
    echo("                  <li><a href='questoes.php?cod_curso=".$cod_curso."&visualizar=Q'>Biblioteca de Questoes</a></li>\n");
    
  	echo("                </ul>\n");
  	echo("              </td>\n");
  	echo("            </tr>\n");
    echo("            <tr>\n");
    echo("              <td>\n");
    echo("                <ul class=\"btAuxTabs03\">\n");
    if($visualizar == "Q")
    {
    
      if($cod_exercicio != null)
        /* ? - Exercicios */
        echo("                  <li><span onclick=\"document.location='editar_exercicio.php?cod_curso=".$cod_curso."&cod_exercicio=".$cod_exercicio."';\">Voltar a edicao do exercicio</span></li>\n");
      // ? - Nova questao
      echo("                  <li><span onclick=\"NovaQuestao();\">Nova questao</span></li>\n");
      // ? - Filtrar
      echo("                  <li><span onclick=\"MostraLayer(lay_filtro,0);\">Filtrar</span></li>\n");
      // ? - Lixeira
      echo("                  <li><span onclick=\"document.location='questoes.php?cod_curso=".$cod_curso."&visualizar=L';\">Lixeira</span></li>\n");
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
      /* ? - Questoes     */
      echo("                Questoes ");
      echo("(<span id=\"primQuestaoIndex\"></span>");
      /* ? - a             */
      echo(" a&nbsp;");
      /* ? - de            */
      echo("<span id=\"ultQuestaoIndex\"></span> de ");
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
	/* ? - T�ulo */
	echo("                    <td class=\"alLeft\" style=\"cursor:pointer\">Titulo</td>\n");
	/* ? - Data */
	echo("                    <td width=\"10%\" style=\"cursor:pointer\">Data</td>\n");
    /* ? - Topico */
	echo("                    <td width=\"15%\" style=\"cursor:pointer\">Topico</td>\n");
	if($visualizar == "Q")
    {
      /* ? - Tipo*/
	  echo("                    <td width=\"12%\" style=\"cursor:pointer\">Tipo</td>\n");
	  /* ? - Compartilhamento */
	  echo("                    <td width=\"15%\" style=\"cursor:pointer\">Compartilhamento</td>\n");
    }
	echo("                  </tr>\n");
	echo("                </thead>\n");
    echo("                <tbody>\n");
    
    //numero de mensagens em uma determinada pagina
    $numQuestoesPag = 0;
    $numPagina = 1;
    
   	if ((count($lista_questoes)>0)&&($lista_questoes != null))
    {
      foreach ($lista_questoes as $cod => $linha_item)
      {
        if($numQuestoesPag == $questoesPorPag){
          $numPagina++;
          $numQuestoesPag = 0;      
        }
        if($numPagina == $pagAtual) $style = "";
        else $style = "display:none";
      
        $data = "<span id=\"data_".$linha_item['cod_questao']."\">".UnixTime2Data($linha_item['data'])."</span>";
        $tipo = $linha_item['tp_questao'];
        $titulo = $linha_item['titulo'];
        $topico = RetornaNomeTopico($sock,$linha_item['cod_topico']);
        $icone = "<img src=\"../imgs/arqp.gif\" alt=\"\" border=\"0\" /> ";
        
        /* ?? - Compartilhado com Formadores */
        if($linha_item['tipo_compartilhamento'] == "F")
          $compartilhamento = "Compartilhado com Formadores";
        /* ?? - Nao compartilhado */
        else
          $compartilhamento = "Nao compartilhado";
        
        if($cod_usuario == $linha_item['cod_usuario'])
          $compartilhamento = "<span id=\"comp_".$linha_item['cod_questao']."\" class=\"link\" onclick=\"js_cod_item='".$linha_item['cod_questao']."';AtualizaComp('".$linha_item['tipo_compartilhamento']."');MostraLayer(cod_comp,140,event);return(false);\">".$compartilhamento."</span>";

        echo("                  <tr class=\"altColor".($cod%2)."\" id=\"trQuestao_".$linha_item['cod_questao']."\" style=\"".$style."\">\n");
        echo("                    <td width=\"2\"><input type=\"checkbox\" name=\"chk[]\" id=\"itm_".$linha_item['cod_questao']."\" onclick=\"ControlaSelecao();\" value=\"".$linha_item['cod_questao']."\" /></td>\n");
        echo("                    <td align=left>".$icone."<a href=\"editar_questao.php?cod_curso=".$cod_curso."&cod_questao=".$linha_item['cod_questao']."&tp_questao=".$linha_item['tp_questao']."\">".$titulo."</a></td>\n");
        echo("                    <td>".$data."</td>\n");
        echo("                    <td id=\"topico_".$linha_item['cod_questao']."\">".$topico."</td>\n");
        if($visualizar == "Q")
        {
          echo("                    <td id=\"tipo_".$linha_item['cod_questao']."\">".$tipo."</td>\n");
          echo("                    <td>".$compartilhamento."</td>\n");
        }
        echo("                  </tr>\n");
        $numQuestoesPag++;
      }
    }
    else
    {
      echo("                  <tr>\n");
      /* ? - Não há nenhuma questao */
      echo("                    <td colspan=\"6\">Nao ha nenhuma questao</td>\n");
      echo("                  </tr>\n");
    }
    
    $colspan = 3;
    if($visualizar == 'L')
      $colspan = 2;
    
    echo("                </tbody>\n");
    echo("                <tfoot>\n");
    echo("                  <tr id=\"trIndicePag\">\n");
    echo("                    <td colspan=\"".$colspan."\" align=\"left\" style=\"border-right:none\">\n");
    if($totalQuestoes>1)
      echo("                      *clique no cabe&ccedil;alho para ordenar as questoes\n");
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
      /* ? - Apagar selecionadas */
      echo("                  <li id=\"mExcluir_Selec\" class=\"menuUp\"><span id=\"eapagarrSelec\">Apagar selecionadas</span></li>\n");
      if($cod_exercicio == null)
      {
        /* ? - */
        echo("                  <li id=\"mIncluir_Selec\" class=\"menuUp\"><span id=\"eincluirSelec\">Incluir selecionadas em um exercicio</span></li>\n");
      }
      else
      {
        /* ? - */
        echo("                  <li id=\"mIncluir_Selec\" class=\"menuUp\"><span id=\"eincluirSelec\">Incluir selecionadas no exercicio</span></li>\n");
      }
      echo("                </ul>\n");
	}
	else if($visualizar == "L")
	{
	  echo("                <ul>\n");
      /* ? - Apagar selecionadas */
      echo("                  <li id=\"mExcluir_Selec\" class=\"menuUp\"><span id=\"eapagarrSelec\">Apagar Selecionadas</span></li>\n");
      /* ? - Recuperar selecionadas */
      echo(" 					<li id=\"mRecup_Selec\" class=\"menuUp\"><span id=\"recuperarSelec\">Recuperar Selecionadas</span></li>\n");
      echo("                </ul>\n");
	}
	
	echo("              </td>\n");
  	echo("            </tr>\n");
  	echo("          </table>\n");
    echo("          <span class=\"btsNavBottom\"><a href=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></a> <a href=\"#topo\"><img src=\"../imgs/btTopo.gif\" border=\"0\" alt=\"Topo\" /></a></span>\n");
  //*NAO �FORMADOR*/
  }
  else
  {
	/* 1 - Enquete */
  	/* 37 - Area restrita ao formador. */
  	echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases,37)."</h4>\n");
	
        /*Voltar*/
        echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

        echo("          <div id=\"mudarFonte\">\n");
        echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
        echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
        echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
        echo("          </div>\n");

    	/* 23 - Voltar (gen) */
    	echo("<form><input class=\"input\" type=button value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onclick=\"history.go(-1);\" /></form>\n");
  }

  echo("        </td>\n");
  echo("      </tr>\n"); 

  include("../tela2.php");

  if($tela_formador && $visualizar == "Q")
  {
  	/* Nova Questao */
  	echo("    <div id=\"layer_nova_questao\" class=popup>\n");
  	echo("     <div class=\"posX\"><span onclick=\"EscondeLayer(lay_nova_questao);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  	echo("      <div class=int_popup>\n");
  	echo("        <form name=form_nova_questao method=post action=acoes.php onSubmit='return(VerificaNovoTitulo(document.form_nova_questao.novo_titulo, 1));'>\n");
  	echo("          <div class=ulPopup>\n");    
  	/* ? - Titulo: */
  	echo("            Titulo:<br />\n");
  	echo("            <input class=\"input\" type=\"text\" name=\"novo_titulo\" id=\"nome\" value=\"\" maxlength=150 /><br />\n");
  	/* ? - Tipo da questao: */
  	echo("            Tipo da questao:<br />\n");
  	echo("            <select class=\"input\" name=\"tp_questao\">");
  	echo("              <option value=\"O\" selected>Objetiva</option>");
  	echo("              <option value=\"D\">Dissertativa</option>");
  	echo("            </select><br /><br />");
  	echo("            <input type=hidden name=cod_curso value=\"".$cod_curso."\" />\n");
  	echo("            <input type=hidden name=acao value=criarQuestao />\n");
  	echo("            <input type=hidden name=cod_usuario value=\"".$cod_usuario."\" />\n");
  	echo("            <input type=hidden name=origem value=questoes />\n");
  	/* 18 - Ok (gen) */
  	echo("            <input type=\"submit\" id=\"ok_novaquestao\" class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" />\n");
  	/* 2 - Cancelar (gen) */
  	echo("            &nbsp; &nbsp; <input type=\"button\" class=\"input\"  onClick=\"EscondeLayer(lay_nova_questao);\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");
  	echo("         </div>\n");
  	echo("        </form>\n");
  	echo("      </div>\n");
  	echo("    </div>\n\n");
  	
  	/* Exercicios */
  	echo("    <div id=\"layer_exercicios\" class=popup>\n");
  	echo("     <div class=\"posX\"><span onclick=\"EscondeLayer(lay_exercicios);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  	echo("      <div class=int_popup>\n");
  	echo("        <div class=ulPopup>\n");    
  	/* ? - Escolha um exercicio: */
  	echo("            Escolha um exercicio:<br />\n");
  	echo("            <select class=\"input\" id=\"select_exercicio\">\n");
  	
  	if ((count($lista_exercicios)>0)&&($lista_exercicios != null))
    {
      foreach ($lista_exercicios as $cod => $linha_item)
      {
        if($linha_item['titulo'] == $cod_exercicio)
          $selected = "selected";
        else
          $selected = ""; 
      
        echo("              <option value=\"".$linha_item['cod_exercicio']."\" ".$selected.">".$linha_item['titulo']."</option>\n");
      }
    }  
  	  
  	echo("            </select><br /><br />\n");
  	/* 18 - Ok (gen) */
  	echo("            <input type=\"button\" class=\"input\" onClick=\"IncluirQuestoesNoExercicio(document.getElementById('select_exercicio').value);EscondeLayer(lay_exercicios);\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" />\n");
  	/* 2 - Cancelar (gen) */
  	echo("            &nbsp; &nbsp; <input type=\"button\" class=\"input\"  onClick=\"EscondeLayer(lay_exercicios);\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");
  	echo("        </div>\n");
  	echo("      </div>\n");
  	echo("    </div>\n\n");
  	
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
  	echo("            <li onClick=\"document.getElementById('tipo_comp').value='F'; xajax_MudarCompartilhamentoDinamic(xajax.getFormValues('form_comp'), 'Compartilhado com formadores','Q'); EscondeLayers();\">\n");
  	echo("              <span id=\"tipo_comp_F\" class=\"check\"></span>\n");
  	/* ?? - Compartilhado com formadores */
  	echo("              <span>Compartilhado com formadores</span>\n");
  	echo("            </li>\n");
  	echo("            <li onClick=\"document.getElementById('tipo_comp').value='N'; xajax_MudarCompartilhamentoDinamic(xajax.getFormValues('form_comp'), 'Nao Compartilhado', 'Q'); EscondeLayers();\">\n");
  	echo("              <span id=\"tipo_comp_N\" class=\"check\"></span>\n");
  	/* ?? - Nao Compartilhado */
  	echo("              <span>Nao Compartilhado</span>\n");
  	echo("            </li>\n");
 	echo("          </ul>\n");    
  	echo("        </form>\n");
  	echo("      </div>\n");
  	echo("    </div>\n");
  	
  	/* Filtro */
  	echo("    <div id=\"layer_filtro\" class=popup>\n");
  	echo("      <div class=\"posX\"><span onclick=\"EscondeLayer(lay_filtro);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  	echo("      <div class=int_popup>\n");
  	echo("        <div class=ulPopup>\n");    
  	/* ? - Topico: */
  	echo("            Topico:<br />\n");
    echo("            <select class=\"input\" id=\"topico\">");
  	echo("              <option value=\"T\" selected>Todos</option>");
    
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
  	/* ? - Tipo da questao: */
  	echo("            Tipo da questao:<br />\n");
  	echo("            <select class=\"input\" id=\"tp_questao\">");
  	echo("              <option value=\"T\" selected>Todas</option>");
  	echo("              <option value=\"O\">Objetiva</option>");
  	echo("              <option value=\"D\">Dissertativa</option>");
  	echo("            </select><br /><br />");
  	/* ? - Dificuldade: */
  	echo("            Dificuldade:<br />\n");
  	echo("            <select class=\"input\" id=\"dificuldade\">");
  	echo("              <option value=\"T\" selected>Todas</option>");
  	echo("              <option value=\"F\">Facil</option>");
  	echo("              <option value=\"M\">Medio</option>");
  	echo("              <option value=\"D\">Dificil</option>");
  	echo("            </select><br /><br />");
  	/* 18 - Ok (gen) */
  	echo("            <input type=\"button\" id=\"ok_novaquestao\" onClick=\"Filtrar(document.getElementById('topico').value,document.getElementById('tp_questao').value);EscondeLayer(lay_filtro);\" class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" />\n");
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