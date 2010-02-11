<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/exercicios/editar_exercicio.php

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
  ARQUIVO : cursos/aplic/exercicios/editar_exericio.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("exercicios.inc");

  require_once("../xajax_0.2.4/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  
  //$objAjax->debugOn();
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->registerFunction("DecodificaString");
  $objAjax->registerFunction("RetornaFraseDinamic");
  $objAjax->registerFunction("RetornaFraseGeralDinamic");
  $objAjax->registerFunction("EditarTituloExercicioDinamic");
  $objAjax->registerFunction("EditarTextoExercicioDinamic");
  $objAjax->registerFunction("ExcluiArquivoDinamic");
  $objAjax->registerFunction("ExibeArquivoAnexadoDinamic");
  $objAjax->registerFunction("VerificaExistenciaArquivoDinamic");
  $objAjax->registerFunction("MudarCompartilhamentoDinamic");
  $objAjax->registerFunction("AtribuiValorAQuestaoDinamic");
  $objAjax->registerFunction("ExluirQuestaoDoExercicioDinamic");
  $objAjax->registerFunction("AplicaExercicioDinamic");
  $objAjax->registerFunction("CancelaAplicacaoExercicioDinamic");
  $objAjax->registerFunction("RetornaArquivosDiretorioDinamic");
  $objAjax->registerFunction("DescompactarArquivoDinamic");
  $objAjax->registerFunction("MudaStatusArquivosDinamic");
  $objAjax->registerFunction("AlteraStatusExercicioInternoDinamic");
  $objAjax->registerFunction("VerificaNotas");
  
  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequests();
  
  $cod_ferramenta = 23;

  // Descobre os diretorios de arquivo, para os portfolios com anexo
  $sock = Conectar("");
  $diretorio_arquivos = RetornaDiretorio($sock, 'Arquivos');
  $diretorio_temp = RetornaDiretorio($sock, 'ArquivosWeb');
  Desconectar($sock);

  include("../topo_tela.php");
  
  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);

  /* Frase #29 - Aplicacao cancelada com sucesso! */
  $feedbackObject->addAction("cancelar", RetornaFraseDaLista($lista_frases, 29), 0);
  /* Frase #30 - Exercicio aplicado com sucesso */
  $feedbackObject->addAction("aplicar", RetornaFraseDaLista($lista_frases, 30), 0);
  /* Frase #31 - Exercicio reaplicado com sucesso */
  $feedbackObject->addAction("reaplicar", RetornaFraseDaLista($lista_frases, 31), 0);
  /* Frase #32 - Questoes incluidas com sucesso */
  $feedbackObject->addAction("incluirQuestao", RetornaFraseDaLista($lista_frases, 32), 0);
  
  $exercicio = RetornaExercicio($sock,$cod_exercicio);
  $lista_questoes = RetornaQuestoesExercicio($sock,$cod_exercicio);
  $totalValorQuestoes = RetornaSomaValorQuestoes($sock,$cod_exercicio);
  $dir_questao_temp = CriaLinkVisualizar($sock, $cod_curso, $cod_usuario, $cod_exercicio, $diretorio_arquivos, $diretorio_temp, "exercicio");
  $lista_arq = RetornaArquivosQuestao($cod_curso, $dir_questao_temp['link']);
  $num_arq_vis = RetornaNumArquivosVisiveis($lista_arq);
  $data = time();
  
  GeraJSComparacaoDatas();
  GeraJSVerificacaoData();

  /*********************************************************/
  /* in�io - JavaScript */
  echo("    <script type=\"text/javascript\" language=\"JavaScript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
  echo("    <script type=\"text/javascript\" language=\"JavaScript\" src=\"../bibliotecas/rte/html2xhtml.js\"></script>\n");
  echo("    <script type=\"text/javascript\" language=\"JavaScript\" src=\"../bibliotecas/rte/richtext.js\"></script>\n");
  echo("    <script type=\"text/javascript\" language=\"JavaScript\" src=\"micoxUpload2.js\"></script>\n");
  echo("    <script type=\"text/javascript\" language=\"JavaScript\">\n");
  echo("    <!--\n");
  //Usage: initRTE(imagesPath, includesPath, cssFile, genXHTML)
  echo("      initRTE(\"../bibliotecas/rte/images/\", \"../bibliotecas/rte/\", \"../bibliotecas/rte/\", true);\n");
  echo("    //-->\n");
  echo("    </script>\n");

  echo("    <script  type=\"text/javascript\" language=\"JavaScript\">\n\n");

  echo("    var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
  echo("    var isMinNS6 = ((navigator.userAgent.indexOf(\"Gecko\") != -1) && (isNav));\n");
  echo("    var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");
  echo("    var Xpos, Ypos;\n");
  echo("    var js_cod_item;\n");
  echo("    var js_comp = new Array();\n");
  echo("    var cod_comp;");
  echo("    var editaTexto = 0;\n");
  echo("    var editaTitulo = 0;\n");
  echo("    var input = 0;\n");
  echo("    var cancelarElemento = null;\n");
  echo("    var arqRecebidos = 0;\n");
  echo("    var contaArq = ".count($lista_arq).";\n");
  echo("    var indexArq = ".count($lista_arq).";\n");
  echo("    var numQuestoes = ".count($lista_questoes).";\n");
  echo("    var pastaRaiz = \"".$dir_questao_temp['link']."\";");
  echo("    var pastaAtual = \"Raiz/\";\n");
  echo("    var conteudoPasta = new Array();\n");
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
  echo("      cod_comp = getLayer(\"comp\");\n");
  echo("      lay_atribuir = getLayer(\"layer_atribuir\");\n");
  echo("      lay_aplicar = getLayer(\"layer_aplicar\");\n");
  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("      startList();\n");
  echo("    }\n\n");

  echo("    function WindowOpenVer(id)\n");
  echo("    {\n");
  echo("      window.open(\"" . $dir_questao_temp['link'] . "\"+id,'Portfolio','top=50,left=100,width=600,height=400,menubar=yes,status=yes,toolbar=yes,scrollbars=yes,resizable=yes');\n");
  echo("    }\n\n");

  echo("    function EscondeLayers()\n");
  echo("    {\n");
  echo("      hideLayer(cod_comp);\n");
  echo("      hideLayer(lay_atribuir);\n");
  echo("      hideLayer(lay_aplicar);\n");
  echo("    }\n\n");

  echo("    function MostraLayer(cod_layer, ajuste)\n");
  echo("    {\n\n");
  echo("      EscondeLayers();\n");
  echo("      moveLayerTo(cod_layer,Xpos-ajuste,Ypos+AjustePosMenuIE());\n");
  echo("      showLayer(cod_layer);\n");
  echo("    }\n\n");

  echo("    function EscondeLayer(cod_layer)\n");
  echo("    {\n");
  echo("      hideLayer(cod_layer);\n");
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

  echo("    function EdicaoTitulo(codigo, id, valor){\n");
  echo("      if ((valor=='ok')&&(document.getElementById(id+'_text').value!='')){\n");
  echo("        conteudo = document.getElementById(id+'_text').value;\n");
  /* Frase #33 - Titulo alterado com sucesso. */
  echo("        xajax_EditarTituloExercicioDinamic(".$cod_curso.", codigo, conteudo, ".$cod_usuario.", \"".RetornaFraseDaLista($lista_frases, 33)."\");\n");
  echo("      }else{\n");
  /* Frase #34 - O titulo nao pode ser vazio. */
  echo("      if ((valor=='ok')&&(document.getElementById(id+'_text').value==''))\n");
  echo("        alert('".RetornaFraseDaLista($lista_frases, 34)."');\n");
  echo("      document.getElementById(id).innerHTML=conteudo;\n");
  echo("      if(navigator.appName.match(\"Opera\")){\n");
  echo("        document.getElementById('renomear_'+codigo).onclick = AlteraTitulo(codigo);\n");
  echo("      }else{\n");
  echo("        document.getElementById('renomear_'+codigo).onclick = function(){ AlteraTitulo(codigo); };\n");
  echo("      }\n");
  //Cancela Edição
  //echo("      if (!cancelarTodos)\n");
  //echo("        xajax_AcabaEdicaoDinamic(cod_curso, cod_item, cod_usuario, 0);\n");
  echo("      }\n");
  echo("      editaTitulo=0;\n");
  echo("      cancelarElemento=null;\n");
  echo("    }\n\n");

  echo("    function EditaTituloEnter(campo, evento, id)\n");
  echo("    {\n");
  echo("      var tecla;\n");
  echo("      CheckTAB=true;\n");
  echo("      if(navigator.userAgent.indexOf(\"MSIE\")== -1)\n");
  echo("      {\n");
  echo("        tecla = evento.which;\n");
  echo("      }\n");
  echo("      else\n");
  echo("      {\n");
  echo("        tecla = evento.keyCode;\n");
  echo("      }\n");
  echo("      if ( tecla == 13 )\n");
  echo("      {\n");
  echo("        EdicaoTitulo(id,'tit_'+id,'ok');\n");
  echo("      }\n");
  echo("      return true;\n");
  echo("    }\n\n");

  echo("    function AlteraTitulo(id){\n");
  echo("    if (editaTitulo==0){\n");
  echo("      CancelaTodos();\n");
  echo("      id_aux = id;\n");
  //echo("      xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);\n");
  echo("      conteudo = document.getElementById('tit_'+id).innerHTML;\n");
  echo("      document.getElementById('tit_'+id).className='$cod_questao - codigo da questao';\n");
  echo("      document.getElementById('tr_'+id).className='';\n");
  echo("      createInput = document.createElement('input');\n");
  echo("      document.getElementById('tit_'+id).innerHTML='';\n");
  echo("      document.getElementById('tit_'+id).onclick=function(){ };\n");
  echo("      createInput.setAttribute('type', 'text');\n");
  echo("      createInput.setAttribute('style', 'border: 2px solid #9bc');\n");
  echo("      createInput.setAttribute('id', 'tit_'+id+'_text');\n");
  //echo("      createInput.onkeypress = function(event) {EditaTituloEnter(this, event, id_aux);}\n");
  echo("      if (createInput.addEventListener){; \n");
  echo("      createInput.addEventListener('keypress', function (event) {EditaTituloEnter(this, event, id_aux);}, false);\n");
  echo("      } else if (createInput.attachEvent){;\n"); 
  echo("      createInput.attachEvent('onkeypress', function (event) {EditaTituloEnter(this, event, id_aux);});\n");
  echo("      };\n");
  echo("      document.getElementById('tit_'+id).appendChild(createInput);\n");
  echo("      xajax_DecodificaString('tit_'+id+'_text', conteudo, 'value');\n");
  //cria o elemento 'espaco' e adiciona na pagina
  echo("      espaco = document.createElement('span');\n");
  echo("      espaco.innerHTML='&nbsp;&nbsp;'\n");
  echo("      document.getElementById('tit_'+id).appendChild(espaco);\n");
  echo("      createSpan = document.createElement('span');\n");
  echo("      createSpan.className='link';\n");
  echo("      createSpan.onclick= function(){ EdicaoTitulo(id, 'tit_'+id, 'ok'); };\n");
  echo("      createSpan.setAttribute('id', 'OkEdita');\n");
  // 21 - Ok
  echo("      createSpan.innerHTML='".RetornaFraseDaLista($lista_frases, 21)."';\n");
  echo("      document.getElementById('tit_'+id).appendChild(createSpan);\n");
  //cria o elemento 'espaco' e adiciona na pagina
  echo("      espaco = document.createElement('span');\n");
  echo("      espaco.innerHTML='&nbsp;&nbsp;'\n");
  echo("      document.getElementById('tit_'+id).appendChild(espaco);\n");
  echo("      createSpan = document.createElement('span');\n");
  echo("      createSpan.className='link';\n");
  echo("      createSpan.onclick= function(){ EdicaoTitulo(id, 'tit_'+id, 'canc'); };\n");
  echo("      createSpan.setAttribute('id', 'CancelaEdita');\n");
  //22 - Cancelar
  echo("      createSpan.innerHTML='".RetornaFraseDaLista($lista_frases, 22)."';\n");
  echo("      document.getElementById('tit_'+id).appendChild(createSpan);\n");
  //cria o elemento 'espaco' e adiciona na pagina
  echo("      espaco = document.createElement('span');\n");
  echo("      espaco.innerHTML='&nbsp;&nbsp;'\n");
  echo("      document.getElementById('tit_'+id).appendChild(espaco);\n");
  echo("      startList();\n");
  echo("      cancelarElemento=document.getElementById('CancelaEdita');\n");
  echo("      document.getElementById('tit_'+id+'_text').select();\n");
  echo("      editaTitulo++;\n");
  echo("    }\n");
  echo("    }\n\n");

  echo("    function LimparTexto(id)\n");
  echo("    {\n");
  /* Frase #33 - Voce deseja limpar o texto? O conteudo sera perdido. */
  echo("      if(confirm(\"".RetornaFraseDaLista($lista_frases, 33)."\"))\n");
  echo("      {\n");
  //echo("        xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);\n");
  echo("        document.getElementById('text_'+id).innerHTML='';\n");
  echo("        xajax_EditarTextoExercicioDinamic(".$cod_curso.",".$cod_exercicio.",'',".$cod_usuario.", \"\");\n");
  echo("      }\n");
  echo("    }\n\n");

  echo("    function AlteraTexto(id){\n");
  echo("      if (editaTexto==-1 || editaTexto != id){\n");
  echo("        CancelaTodos();\n");
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
  echo("        conteudo=document.getElementById(id+'_text').contentWindow.document.body.innerHTML;\n");
  echo("        xajax_EditarTextoExercicioDinamic(".$cod_curso.",".$cod_exercicio.",conteudo,".$cod_usuario.", \"".RetornaFraseDaLista($lista_frases, 175)."\");\n");
  echo("      }\n");
  echo("      else{\n");
  // Cancela Edi�o
  //echo("        if (!cancelarTodos)\n");
  //echo("          xajax_AcabaEdicaoDinamic(cod_curso, cod_item, cod_usuario, 0);\n");
  echo("      }\n");
  echo("      document.getElementById(id).innerHTML=conteudo;\n");
  echo("      editaTexto=-1;\n");
  echo("      cancelarElemento=null;\n");
  echo("    }\n\n");

  echo("    function AcrescentarBarraFile(apaga){\n");
  echo("      if (input==1) return;\n");
  echo("      CancelaTodos();\n");
  echo("      document.getElementById('input_files').style.visibility='visible';\n");
  echo("      document.getElementById('divArquivoEdit').className='';\n");
  echo("      document.getElementById('divArquivo').className='divHidden';\n");
  //echo("      xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);
  echo("      cancelarElemento=document.getElementById('cancFile');\n");
  echo("    }\n\n");
  
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
  
  echo("    function VerificaCheck(){\n");
  echo("      var i;\n");
  echo("      var j=0;\n");
  echo("      var cod_itens=document.getElementsByName('chkQuestao');\n");
  echo("      var Cabecalho = document.getElementById('checkMenu');\n");
  echo("      EscondeLayers();\n");
  echo("      for (i=0; i < cod_itens.length; i++){\n");
  echo("        if (cod_itens[i].checked){\n");
  echo("          j++;\n");
  echo("        }\n");
  echo("      }\n");
  echo("      if (j == (cod_itens.length)) Cabecalho.checked=true;\n");
  echo("      else Cabecalho.checked=false;\n");
  echo("      if(j > 0){\n");
  echo("        document.getElementById('mQuestao_apagar').className=\"menuUp02\";\n");
  echo("        document.getElementById('mQuestao_apagar').onclick=function(){ ApagarSelecionadas(); };\n");
  echo("        document.getElementById('mQuestao_valor').className=\"menuUp02\";\n");
  echo("        document.getElementById('mQuestao_valor').onclick=function(){ MostraLayer(lay_atribuir,140); };\n");
  echo("      }else{\n");
  echo("        document.getElementById('mQuestao_apagar').className=\"menuUp\";\n");
  echo("        document.getElementById('mQuestao_apagar').onclick=function(){  };\n");
  echo("        document.getElementById('mQuestao_valor').className=\"menuUp\";\n");
  echo("        document.getElementById('mQuestao_valor').onclick=function(){ };\n");
  echo("      }\n");
  echo("    }\n\n");
   
  echo("    function CheckTodos(){\n");
  echo("      var e;\n");
  echo("      var i;\n");
  echo("      var CabMarcado = document.getElementById('checkMenu').checked;\n");
  echo("      var cod_itens=document.getElementsByName('chkQuestao');\n");
  echo("      for(i = 0; i < cod_itens.length; i++){\n");
  echo("        e = cod_itens[i];\n");
  echo("        e.checked = CabMarcado;\n");
  echo("      }\n");
  echo("      VerificaCheck();\n");
  echo("    }\n\n");
  
  echo("    function InsereLinhaVazia(){\n");
  echo("	  var trTotal,tr,td;");
  echo("	  trTotal = document.getElementById(\"trTotal\");\n");
  echo("	  tr = document.createElement(\"tr\");\n");
  echo("	  td = document.createElement(\"td\");\n");
  echo("	  td.colSpan = \"6\";\n");
  /* Frase #35 - Nao ha nenhuma questao */
  echo("	  td.appendChild(document.createTextNode('".RetornaFraseDaLista($lista_frases, 35)."'));\n");
  echo("	  tr.appendChild(td);\n");
  echo("	  trTotal.parentNode.insertBefore(tr,trTotal);\n");
  echo("    }\n\n");
  
  echo("    function DeletaLinhaQuestoes(arrayIdQuestoes,n){\n");
  echo("      var i,tr;\n");
  echo("      for (i=0; i < n; i++){\n");
  echo("        tr = document.getElementById('trQuestao_'+arrayIdQuestoes[i]);\n");
  echo("        tr.parentNode.removeChild(tr);\n");
  echo("        numQuestoes--;\n");
  echo("      }\n");
  echo("    }\n\n");
  
  echo("    function ApagarSelecionadas(){\n");
  echo("      var i,j,questoes,getNumber,arrayIdQuestoes;\n");
  echo("      j=0;\n");
  echo("      arrayIdQuestoes = new Array();\n");
  echo("      questoes = document.getElementsByName('chkQuestao');\n");
  echo("      for (i=0; i < questoes.length; i++){\n");
  echo("        if (questoes[i].checked){\n");
  echo("          getNumber = questoes[i].id.split(\"_\");\n");
  echo("          xajax_ExluirQuestaoDoExercicioDinamic(".$cod_curso.",".$cod_exercicio.",getNumber[1]);\n");
  echo("          arrayIdQuestoes[j++] = getNumber[1];\n");
  echo("        }\n");
  echo("      }\n");
  echo("      DeletaLinhaQuestoes(arrayIdQuestoes,j)\n");
  echo("      if(numQuestoes == 0)\n");
  echo("      {\n");
  echo("        document.getElementById(\"trTotal\").style.display = \"none\";\n");
  echo("        InsereLinhaVazia();\n");
  echo("      }\n");
  echo("      else\n");
  echo("      {\n");
  echo("        xajax_AtualizaValorTotalExercicioDinamic(".$cod_curso.",".$cod_exercicio.");\n");
  echo("      }\n");
  /* Frase #36 - Questoes apagadas com sucesso. */
  echo("      mostraFeedback(\"".RetornaFraseDaLista($lista_frases, 36)."\",true);\n");
  echo("      VerificaCheck();\n");
  echo("    }\n\n");
  
  
  echo("    function AtribuiValor(valor){\n");
  echo("      var i,questoes,getNumber;\n");
  echo("      questoes = document.getElementsByName('chkQuestao');\n");
  echo("      for (i=0; i < questoes.length; i++){\n");
  echo("        if (questoes[i].checked){\n");
  echo("          getNumber = questoes[i].id.split(\"_\");\n");
  echo("          xajax_AtribuiValorAQuestaoDinamic(".$cod_curso.",".$cod_exercicio.",getNumber[1],valor);\n");
  echo("        }\n");
  echo("      }\n");
  /* Frase #37 - Valores atribuidos com sucesso */
  echo("      mostraFeedback(\"".RetornaFraseDaLista($lista_frases, 37)."\",true);\n");
  echo("    }\n\n");
  
  echo("    function isNumber(string)\n");
  echo("    {\n");
  echo("      var validChars,isNumber,c;\n");
  echo("      validChars = \"0123456789.\";\n");
  echo("      isNumber = true;\n");
  echo("      for (i = 0; i < string.length && isNumber == true; i++)\n");
  echo("      {\n"); 
  echo("        c = string.charAt(i);\n");
  echo("        if (validChars.indexOf(c) == -1)\n");
  echo("        {\n");
  echo("          isNumber = false;\n");
  echo("        }\n");
  echo("      }\n"); 
  echo("      return isNumber;\n");
  echo("    }\n\n");

  echo("    function VerificaValor(valor){\n");
  echo("      if(isNumber(valor) && valor != '')\n");
  echo("      {\n");
  echo("        AtribuiValor(valor);\n");
  echo("        EscondeLayer(lay_atribuir);\n");
  echo("        document.getElementById(\"valor\").value = \"\";");
  echo("      }\n");
  echo("      else\n");
  echo("      {\n");
  /* Frase #38 - O valor deve ser numerico! */
  echo("        alert(\"".RetornaFraseDaLista($lista_frases, 38)."\");\n");
  echo("        document.getElementById(\"valor\").value = \"\";");
  echo("        document.getElementById(\"valor\").focus();\n");
  echo("      }\n");
  echo("    }\n\n");
  
  /*************************************************************************************************************************************
    Funções para lidar com a seção "Arquivos"
  *************************************************************************************************************************************/
  
  echo("    function CheckTodosArq(){\n");
  echo("      var e;\n");
  echo("      var i;\n");
  echo("      var CabMarcado = document.getElementById('checkMenuArq').checked;\n");
  echo("      var cod_itens=document.getElementsByName('chkArq');\n");
  echo("      for(i = 0; i < cod_itens.length; i++){\n");
  echo("        e = cod_itens[i];\n");
  echo("        e.checked = CabMarcado;\n");
  echo("      }\n");
  echo("      VerificaChkBoxArq();\n");
  echo("    }\n\n");
  
  echo("    function VerificaChkBoxArq(){\n");
  echo("      var i,getNumber,nomeArq;\n");
  echo("      var j=0;\n");
  echo("      var flag=0;\n");
  echo("      var cod_itens=document.getElementsByName('chkArq');\n");
  echo("      var Cabecalho = document.getElementById('checkMenuArq');\n");
  echo("      EscondeLayers();\n");
  echo("      for (i=0; i < cod_itens.length; i++){\n");
  echo("        if (cod_itens[i].checked){\n");
  echo("          j++;\n");
  echo("          getNumber = cod_itens[i].id.split(\"_\");\n");
  echo("          nomeArq = document.getElementById('nomeArq_'+getNumber[1]).innerHTML;\n");
  echo("          if(RetornaTipoArq(nomeArq) != 'zip')\n");
  echo("            flag = 1;\n");
  echo("        }\n");
  echo("      }\n");
  echo("      if (j == (cod_itens.length) && i != 0) Cabecalho.checked=true;\n");
  echo("      else Cabecalho.checked=false;\n");
  echo("      if(j > 0){\n");
  echo("        document.getElementById('mArq_apagar').className=\"menuUp02\";\n");
  echo("        document.getElementById('mArq_apagar').onclick=function(){ ApagarArq(); };\n");
  echo("        document.getElementById('mArq_ocultar').className=\"menuUp02\";\n");
  echo("        document.getElementById('mArq_ocultar').onclick=function(){ Ocultar(); };\n");
  echo("        if(flag == 0)\n");
  echo("        {\n");
  echo("          document.getElementById('mArq_descomp').className=\"menuUp02\";\n");
  echo("          document.getElementById('mArq_descomp').onclick=function(){ Descompactar(); };\n");
  echo("        }\n");
  echo("        else\n");
  echo("        {\n");
  echo("          document.getElementById('mArq_descomp').className=\"menuUp\";\n");
  echo("          document.getElementById('mArq_descomp').onclick=function(){ };\n");
  echo("        }\n");
  echo("      }else{\n");
  echo("        document.getElementById('mArq_apagar').className=\"menuUp\";\n");
  echo("        document.getElementById('mArq_apagar').onclick=function(){ };\n");
  echo("        document.getElementById('mArq_descomp').className=\"menuUp\";\n");
  echo("        document.getElementById('mArq_descomp').onclick=function(){ };\n");
  echo("        document.getElementById('mArq_ocultar').className=\"menuUp\";\n");
  echo("        document.getElementById('mArq_ocultar').onclick=function(){ };\n");
  echo("      }\n");
  echo("    }\n\n");
  
  echo("    function ApagarArq(){\n");
  echo("      var i,j,checks,getNumber,nomeArq,arrayIdArq,caminho;\n");
  echo("      checks = document.getElementsByName('chkArq');\n");
  echo("      arrayIdArq = new Array();\n");
  echo("      j = 0;\n");
  echo("      caminho = pastaRaiz + pastaAtual.split(\"Raiz/\")[1];\n");
  /* Frase #39 - Voce realmente deseja apagar o arquivo? */
  echo("      if (confirm(\"".RetornaFraseDaLista($lista_frases, 39)."\")){\n");
  //echo("      xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_raiz);\n");
  echo("        for (i=0; i<checks.length; i++){\n");
  echo("          if(checks[i].checked){\n");
  echo("            getNumber=checks[i].id.split(\"_\");\n");
  echo("            nomeArq = document.getElementById(\"nomeArq_\"+getNumber[1]).innerHTML;\n");
  /* Frase #43 - Arquivo apagado com sucesso. */
  echo("            xajax_ExcluiArquivoDinamic(i,caminho+nomeArq,".$cod_curso.",".$cod_exercicio.",".$cod_usuario.", \"".RetornaFraseDaLista($lista_frases, 43)."\");\n");
  echo("            arrayIdArq[j++] = getNumber[1];\n");
  echo("          }\n");
  echo("        }\n");
  echo("        DeletaLinhaArq(arrayIdArq,j);\n");
  echo("        if(contaArq == 0)\n");
  echo("          InsereDiretorioVazio();\n");
  echo("        VerificaChkBoxArq();\n");
  echo("      }\n");
  echo("    }\n\n");
  
  echo("    function Descompactar(){\n");
  echo("      var arqZip,subpasta,flag;\n");
  echo("      flag = 0;\n");
  echo("      subpasta = pastaAtual.split(\"Raiz/\")[1];\n");
  echo("      checks = document.getElementsByName('chkArq');\n");
  echo("      for (i=0; i<checks.length; i++){\n");
  echo("        if(checks[i].checked){\n");
  echo("          getNumber=checks[i].id.split(\"_\");\n");
  echo("          arqZip=document.getElementById('nomeArq_'+getNumber[1]).innerHTML;\n");
	/* Frase #40 - Voce tem certeza que deseja descompactar este arquivo? */
  echo("          if (confirm(\"".RetornaFraseDaLista($lista_frases, 40)."\")){\n");
  //echo("            xajax_AbreEdicao('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", ".$cod_topico_raiz.");\n");
  echo("            xajax_DescompactarArquivoDinamic(pastaRaiz+subpasta,arqZip);\n");
  echo("            flag = 1;\n");
  echo("          }\n");
  echo("        } \n");
  echo("      }\n");
  echo("      if(flag == 1)\n");
  echo("        AbrePasta(pastaAtual);\n");
  echo("        VerificaChkBoxArq();\n");
  echo("    }\n");
  
  echo("    function Ocultar(){\n");
  echo("      var i,checks,getNumber,nomeArq,arrayArq,caminho;\n");
  echo("      arrayArq = new Array();\n");
  echo("      checks = document.getElementsByName('chkArq');\n");
  echo("      caminho = pastaRaiz + pastaAtual.split(\"Raiz/\")[1];\n");
  /* Frase #41 - Voce realmente deseja ocultar o arquivo? Ele nao sera visivel para alunos. */
  echo("      if (confirm(\"".RetornaFraseDaLista($lista_frases, 41)."\")){\n");
  //echo("      xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_raiz);\n");
  echo("        for (i=0; i<checks.length; i++){\n");
  echo("          if(checks[i].checked){\n");
  echo("            getNumber=checks[i].id.split(\"_\");\n");
  echo("            nomeArq = document.getElementById(\"nomeArq_\"+getNumber[1]).innerHTML;\n");
  echo("            arrayArq[i] = caminho+nomeArq;\n");
  echo("          }\n");
  echo("        }\n");
  /* Frase #42 - Arquivo(s) ocultado(s) com sucesso. */
  echo("        xajax_MudaStatusArquivosDinamic(arrayArq,\"".RetornaFraseDaLista($lista_frases, 42)."\");\n");
  echo("      }\n");
  echo("    }\n\n");
  
  echo("    function getfilename(path)\n");
  echo("    {\n");
  echo("      var pieces,n,file;");
  echo("      pieces=path.split('\'');\n");
  echo("      n=pieces.length;\n");
  echo("      file=pieces[n-1];\n");
  echo("      pieces=file.split('/');\n");
  echo("      n=pieces.length;\n");
  echo("      file=pieces[n-1];\n");
  echo("      return(file);\n");
  echo("    }\n\n");

  echo("    function ArquivoValido(file)\n");
  echo("    {\n");
  echo("      var n=file.length;\n");
  echo("      if (n==0)\n");
  echo("        return (false);\n");
  echo("      for(i=0; i<=n; i++) {\n");
  echo("        if ((file.charAt(i)==\"'\")||(file.charAt(i)==\"#\")||(file.charAt(i)==\"%\")||(file.charAt(i)==\"?\")|| (file.charAt(i)==\"/\")) {\n");
  echo("          return(false);\n");
  echo("        }\n");
  echo("      }\n");
  echo("      return(true);\n");
  echo("    }\n\n");

  echo("    function EdicaoArq(i, msg){\n");
  echo("      var nomeArq,td,subpasta;\n");
  echo("      nomeArq = getfilename(document.getElementById('input_files').value);\n");
  echo("      subpasta = pastaAtual.split(\"Raiz/\")[1];\n");
  echo("      if ((i==1)&&(ArquivoValido(nomeArq))){\n"); //OK
  echo("        xajax_VerificaExistenciaArquivoDinamic(".$cod_curso.",".$cod_exercicio.",".$cod_usuario.",pastaRaiz+subpasta,nomeArq);\n");
  echo("      }\n");
  echo("      else {\n");
  echo("        document.getElementById('input_files').style.visibility='hidden';\n");
  echo("        document.getElementById('input_files').value='';\n");
  echo("        document.getElementById('divArquivo').className='';\n");
  echo("        document.getElementById('divArquivoEdit').className='divHidden';\n");
  //Cancela Edição
  echo("        if (!cancelarTodos)\n");
  //echo("          xajax_AcabaEdicaoDinamic(cod_curso, cod_item, cod_usuario, 0);\n");
  echo("        input=0;\n");
  echo("        cancelarElemento=null;\n");
  echo("      }\n");
  echo("    }\n\n");
  
  echo("    function DeletaLinhaArq(arrayIdArq,n){\n");
  echo("      var i,tr;\n");
  echo("      for (i=0; i < n; i++){\n");
  echo("        tr = document.getElementById('trArq_'+arrayIdArq[i]);\n");
  echo("        tr.parentNode.removeChild(tr);\n");
  echo("        contaArq--;\n");
  echo("      }\n");
  echo("    }\n\n");
    
  echo("    function RetornaSrcImg(nomeArq)\n");
  echo("    {\n");	
  echo("      var array,formato,n;\n");
  echo("      array = nomeArq.split('.');\n");
  echo("	  n = array.length;\n");
  echo("	  formato = array[n-1];\n");
  echo("      if(formato == 'zip'){\n");
  echo("        return '../imgs/arqzip.gif';\n");
  echo("	  }\n");
  echo("      if(formato == 'jpg' || formato == 'jpeg' || formato == 'png' || formato == 'gif'){\n");
  echo("        return '../imgs/arqimg.gif';\n");
  echo("	  }\n");
  echo("      if(formato == 'doc'){\n");
  echo("        return '../imgs/arqdoc.gif';\n");
  echo("	  }\n");
  echo("      if(formato == 'pdf'){\n");
  echo("        return '../imgs/arqpdf.gif';\n");
  echo("	  }\n");
  echo("      if(formato == 'html'){\n");
  echo("        return '../imgs/arqhtml.gif';\n");
  echo("	  }\n");
  echo("      if(formato == 'mp3' || formato == 'midi'){\n");
  echo("        return '../imgs/arqsnd.gif';\n");
  echo("	  }\n");
  echo("      return '../imgs/arqp.gif';\n");
  echo("    }\n\n");
  
  echo("    function RetornaTipoArq(nomeArq)\n");
  echo("    {\n");	
  echo("      var array,formato,n;\n");
  echo("      array = nomeArq.split('.');\n");
  echo("	  n = array.length;\n");
  echo("	  formato = array[n-1];\n");
  echo("      if(formato == 'zip'){\n");
  echo("        return 'zip';\n");
  echo("	  }\n");
  echo("      else{\n");
  echo("        return 'comum';\n");
  echo("	  }\n");
  echo("    }\n\n");
  
  echo("    function CriaCheckBoxArq(cod)\n");
  echo("    {\n");	
  echo("      var check = document.createElement(\"input\");\n");
  echo("      check.setAttribute(\"type\", \"checkbox\");\n");
  echo("      check.setAttribute(\"id\",'chkArq_'+cod);\n");
  echo("      check.setAttribute(\"name\", \"chkArq\");\n");
  echo("      check.setAttribute(\"value\", cod);\n");
  echo("      check.onclick = function(){ VerificaChkBoxArq(); };\n");
  echo("      return check;\n");
  echo("    }\n\n");
    
  echo("    function CriaImgArq(nomeArq)\n");
  echo("    {\n");	
  echo("      var img = document.createElement(\"img\");\n");
  echo("      img.setAttribute(\"border\", \"0\");\n");
  echo("      img.setAttribute(\"src\",RetornaSrcImg(nomeArq));\n");
  echo("      return img;\n");
  echo("    }\n\n");
  
  echo("    function CriaImgPasta()\n");
  echo("    {\n");	
  echo("      var img = document.createElement(\"img\");\n");
  echo("      img.setAttribute(\"border\", \"0\");\n");
  echo("      img.setAttribute(\"src\",\"../imgs/pasta.gif\");\n");
  echo("      return img;\n");
  echo("    }\n\n");
  
  echo("    function CriaSpanVisualizarArq(nomeArq,cod,caminho)\n");
  echo("    {\n");	
  echo("      var span = document.createElement(\"span\");\n");
  echo("      span.setAttribute(\"class\", \"link\");\n");
  echo("      span.setAttribute(\"id\",\"nomeArq_\"+cod);\n");
  echo("      span.setAttribute(\"tipoArq\",RetornaTipoArq(nomeArq));\n");
  echo("      span.setAttribute(\"nomeArq\",caminho);\n");
  echo("	  if(RetornaTipoArq(nomeArq) == 'zip')\n");
  echo("	    span.setAttribute(\"arqZip\",nomeArq);\n");
  echo("      span.setAttribute(\"arqOculto\",\"nao\");\n");
  echo("      span.onclick = function(){ WindowOpenVer(caminho); }\n");
  echo("      span.innerHTML = nomeArq;\n");
  echo("      return span;\n");
  echo("    }\n\n");
  
  echo("    function CriaSpanAbrirPasta(nome,cod,caminho)\n");
  echo("    {\n");	
  echo("      var span = document.createElement(\"span\");\n");
  echo("      span.setAttribute(\"class\", \"link\");\n");
  echo("      span.setAttribute(\"id\",\"nomeArq_\"+cod);\n");
  echo("      span.onclick = function(){ AbrePasta(caminho); }\n");
  echo("      span.innerHTML = nome;\n");
  echo("      return span;\n");
  echo("    }\n\n");
  
  echo("    function criaTd(inner,colspan){\n");
  echo("	  var td;\n");
  echo("	  td = document.createElement(\"td\");\n");
  echo("	  td.innerHTML = inner;\n");
  echo("	  td.colSpan = colspan;\n");
  echo("      return td;\n");
  echo("    }\n\n");

  echo("    function criaTrArq(id){\n");
  echo("	  var tr;\n");
  echo("	  tr = document.createElement(\"tr\");\n");
  echo("	  tr.setAttribute(\"id\",\"trArq_\"+id);\n");
  echo("	  tr.setAttribute(\"name\",pastaAtual);\n");
  echo("      return tr;\n");
  echo("    }\n\n");
  
  echo("    function criaSpanArq(nomeArq,numArq,caminho){\n");
  echo("	  var span;\n");
  echo("	  span = document.createElement(\"span\");\n");
  echo("	  span.appendChild(CriaImgArq(nomeArq));\n");
  echo("	  span.appendChild(CriaSpanVisualizarArq(nomeArq,numArq,caminho));\n");
  echo("      return span;\n");
  echo("    }\n\n");
  
  echo("    function criaSpanPasta(nome,numArq,caminho){\n");
  echo("	  var span;\n");
  echo("	  span = document.createElement(\"span\");\n");
  echo("	  span.appendChild(CriaImgPasta());\n");
  echo("	  span.appendChild(CriaSpanAbrirPasta(nome,numArq,caminho));\n");
  echo("      return span;\n");
  echo("    }\n\n");
  
  echo("    function criaSpanArqOculto(){\n");
  echo("	  var span;\n");
  echo("	  span = document.createElement(\"span\");\n");
  echo("      span.style.color = \"red\";\n");
  //186 -(oculto)
  echo("	  span.innerHTML = \" ".RetornaFraseDaLista($lista_frases, 186)."\";\n");
  echo("      return span;\n");
  echo("    }\n\n");
  
  echo("    function InsereLinhaArq(nomeArq,tamanho,numArq,caminho,data,tipo,status){\n");
  echo("      var novaTr,trRef,tdChk,tdNome;\n");
  echo("      if(contaArq == 0)\n");
  echo("        RemoveDiretorioVazio();\n");
  echo("      novaTr = criaTrArq(numArq);\n");
  echo("      tdChk = document.createElement(\"td\");\n");
  echo("      tdChk.appendChild(CriaCheckBoxArq(numArq));\n");
  echo("      tdNome = document.createElement(\"td\");\n");
  echo("      tdNome.colSpan = \"3\";\n");
  echo("	  tdNome.className = \"alLeft\";\n");
  echo("      if(tipo == \"arquivo\")\n");
  echo("        tdNome.appendChild(criaSpanArq(nomeArq,numArq,caminho));\n");
  echo("      else if(tipo == \"pasta\")\n");
  echo("        tdNome.appendChild(criaSpanPasta(nomeArq,numArq,caminho));\n");
  echo("      if(status == '1')\n");
  echo("        tdNome.appendChild(criaSpanArqOculto());\n");
  echo("      novaTr.appendChild(tdChk);\n");
  echo("      novaTr.appendChild(tdNome);\n");
  echo("      novaTr.appendChild(criaTd(tamanho+\" Kb\",1));\n");
  echo("      novaTr.appendChild(criaTd(data,1));\n");
  echo("      trRef = document.getElementById(\"optArq\");\n");
  echo("      trRef.parentNode.insertBefore(novaTr,trRef);\n");
  echo("      contaArq++;\n");
  echo("    }\n\n");
  
  echo("    function EncontraArquivoEApaga(nomeArq)\n");
  echo("    {\n");
  echo("	  var i,span,tr,caminho;\n");
  echo("      caminho = pastaRaiz + pastaAtual.split(\"Raiz/\")[1];\n");
  echo("      for(i = 0; i <= contaArq; i++)\n");
  echo("      {\n");
  echo("        span = document.getElementById(\"nomeArq_\"+i);\n");
  echo("        if(span != null)\n");
  echo("        {\n");
  echo("          if(span.innerHTML == nomeArq)\n");
  echo("          {\n"); 
  /* Frase #43 - Arquivo apagado com sucesso. */
  echo("            xajax_ExcluiArquivoDinamic(i,caminho+nomeArq,".$cod_curso.",".$cod_exercicio.",".$cod_usuario.", \"".RetornaFraseDaLista($lista_frases, 43)."\");\n");
  echo("            tr = document.getElementById(\"trArq_\"+i);\n");
  echo("            tr.parentNode.removeChild(tr);\n");
  echo("          }\n");
  echo("        }\n");
  echo("      }\n");
  echo("    }\n\n");
  
  echo("    function VerificaUpload(nomeArq,flag)\n");
  echo("    {\n");
  echo("      subpasta = pastaAtual.split(\"Raiz/\")[1];\n");
  echo("      document.formFiles.subpasta.value = subpasta;\n");
  echo("	  if((flag == 0)){\n");
  echo("        micoxUpload2('formFiles',0,'Anexando ',function(){},++indexArq,pastaRaiz+subpasta,nomeArq,".$cod_curso.",".$cod_exercicio.",".$cod_usuario.");\n");
  /* Frase #44 - Arquivo anexado com sucesso */
  echo("        mostraFeedback('".RetornaFraseDaLista($lista_frases, 44)."',true);}\n");
  echo("	  if(flag == 1 && confirm('Arquivo '+nomeArq+' ja existe. Deseja sobrescreve-lo?'))\n");
  echo("      {\n");
  echo("		EncontraArquivoEApaga(nomeArq);\n");
  echo("        micoxUpload2('formFiles',0,'Anexando ',function(){},++indexArq,pastaRaiz+subpasta,nomeArq,".$cod_curso.",".$cod_exercicio.",".$cod_usuario.");\n");
  echo("      }\n");
  echo("    }\n\n");
  
  echo("    function FechaPastaAtual()\n");
  echo("    {\n");
  echo("      var tr,arrayIdArq;\n");
  echo("      arrayIdArq = new Array();\n");
  echo("      tr = document.getElementsByName(pastaAtual);\n");
  echo("      if(contaArq == 0 || tr.length == 0)\n");
  echo("        RemoveDiretorioVazio();\n");
  echo("      else\n");
  echo("      {\n");
  echo("        for(i=0;i<tr.length;i++)\n");
  echo("        {\n");
  echo("          arrayIdArq[i] = tr[i].id.split(\"_\")[1];\n");
  echo("        }\n");
  echo("        DeletaLinhaArq(arrayIdArq,i);\n");
  echo("      }\n");
  echo("    }\n\n");
  
  echo("    function InsereMsgLoadingPasta()\n");
  echo("    {\n");
  echo("      var trLoading,trRef;\n");
  echo("	  trLoading = document.createElement(\"tr\");\n");
  echo("	  trLoading.setAttribute(\"id\",\"trLoadingPasta\");\n");
  echo("      trLoading.appendChild(criaTd(\"Abrindo a pasta requisitada,aguarde...\",\"6\"));\n");
  echo("      trRef = document.getElementById(\"optArq\");\n");
  echo("      trRef.parentNode.insertBefore(trLoading,trRef);\n");
  echo("    }\n\n");
  
  echo("    function RemoveMsgLoadingPasta()\n");
  echo("    {\n");
  echo("      var trLoading;\n");
  echo("	  trLoading = document.getElementById(\"trLoadingPasta\");\n");
  echo("	  trLoading.parentNode.removeChild(trLoading);\n");
  echo("    }\n\n");
  
  echo("    function NovoArquivo(nome,tamanho,tipo,data,caminho,status,i)\n");
  echo("    {\n");
  echo("      if(tipo == \"pasta\")\n");
  echo("      {\n");
  echo("        caminho = pastaAtual+nome+'/';\n");
  echo("      }\n");
  echo("      conteudoPasta[i] = new Array();\n");
  echo("      conteudoPasta[i][0] = nome;\n");
  echo("      conteudoPasta[i][1] = tamanho;\n");
  echo("      conteudoPasta[i][2] = data;\n");
  echo("      conteudoPasta[i][3] = caminho;\n");
  echo("      conteudoPasta[i][4] = tipo;\n");
  echo("      conteudoPasta[i][5] = status;\n");
  echo("    }\n\n");
  
  echo("    function InsereDiretorioVazio(){\n");
  echo("	  var trRef,tr,td;");
  echo("	  tr = document.createElement(\"tr\");\n");
  echo("      tr.setAttribute(\"id\",\"diretorioVazio\");\n");
  echo("	  td = document.createElement(\"td\");\n");
  echo("	  td.colSpan = \"6\";\n");
  //187 - Diretorio esta vazio.
  echo("	  td.appendChild(document.createTextNode('".RetornaFraseDaLista($lista_frases, 187)."'));\n");
  echo("	  tr.appendChild(td);\n");
  echo("      trRef = document.getElementById(\"optArq\");\n");
  echo("	  trRef.parentNode.insertBefore(tr,trRef);\n");
  echo("    }\n\n");
  
  echo("    function RemoveDiretorioVazio(){\n");
  echo("	  var tr;\n");
  echo("      tr = document.getElementById(\"diretorioVazio\");\n");
  echo("      if(tr != null)\n");
  echo("	    tr.parentNode.removeChild(tr);\n");
  echo("    }\n\n");
  
  echo("    function RemoveCaminhoPastasAtual(){\n");
  echo("	  var td;");
  echo("	  td = document.getElementById(\"caminhoPastas\");\n");
  echo("      while(td.hasChildNodes())\n");
  echo("        td.removeChild(td.lastChild);\n");
  echo("    }\n\n");
  
  echo("    function AtualizaCaminhoPastas()\n");
  echo("    {\n");
  echo("      var i,j,pastas,pasta;\n");
  echo("      RemoveCaminhoPastasAtual();\n");
  echo("      td = document.getElementById(\"caminhoPastas\");\n");
  echo("      pastas = pastaAtual.split(\"/\");\n");
  echo("      for(i=0;i < pastas.length - 1;i++)\n");
  echo("      {\n");
  echo("        pasta = '';\n");
  echo("        for(j=0;j <= i;j++)\n");
  echo("        {\n");
  echo("          pasta += pastas[j] + '/';\n");
  echo("        }\n");
  echo("        if(i != 0)\n");
  echo("          td.appendChild(document.createTextNode(\" >> \"));\n");
  echo("        td.appendChild(criaSpanPasta(pastas[i],-1,pasta));\n");
  echo("      }\n");
  echo("    }\n\n");
  
  echo("    function ExibeDiretorio(n)\n");
  echo("    {\n");
  echo("      var i;\n");
  echo("      RemoveMsgLoadingPasta();\n");
  echo("      AtualizaCaminhoPastas();\n");
  echo("      for(i=0;i<n;i++)\n");
  echo("      {\n");
  echo("      	InsereLinhaArq(conteudoPasta[i][0],conteudoPasta[i][1],i,conteudoPasta[i][3],conteudoPasta[i][2],conteudoPasta[i][4],conteudoPasta[i][5]);\n");
  echo("      }\n");
  echo("      if(i == 0)\n");
  echo("      {\n");
  echo("      	InsereDiretorioVazio();\n");
  echo("      }\n");
  echo("    }\n\n");
  
  echo("    function AbrePasta(pasta)\n");
  echo("    {\n");
  echo("      var caminho;\n");
  echo("      caminho = pastaRaiz + pasta.split(\"Raiz/\")[1];\n");
  echo("      FechaPastaAtual();\n");
  echo("      VerificaChkBoxArq();\n");
  echo("      InsereMsgLoadingPasta();\n");
  echo("      pastaAtual = pasta;\n");
  echo("      xajax_RetornaArquivosDiretorioDinamic(".$cod_curso.",".$cod_usuario.",caminho);\n");
  echo("    }\n\n");
  
  echo("    function AplicarExercicio(cod)\n");
  echo("    {\n");
  echo("       MostraLayer(lay_aplicar,140,event);\n");
  echo("    }\n\n");
  
  echo("    function  ExibirAgendamento(value)\n");
  echo("    {\n");
  echo("       if(value == \"I\")\n");
  echo("         document.getElementById(\"div_disp\").style.display = \"none\";\n");
  echo("       if(value == \"A\")\n");
  echo("         document.getElementById(\"div_disp\").style.display = \"\";\n");
  echo("    }\n\n");
  
  echo("    function RetornaDataAtual()\n");
  echo("    {\n");
  echo("       var input;\n");
  echo("       input = document.createElement(\"input\");\n");
  echo("       input.setAttribute(\"value\",\"".UnixTime2Data($data)."\")\n");
  echo("       return input;\n");
  echo("    }\n\n");
  
  echo("    function RetornaHoraAtual()\n");
  echo("    {\n");
  echo("       var input;\n");
  echo("       input = document.createElement(\"input\");\n");
  echo("       input.setAttribute(\"value\",\"".UnixTime2Hora($data)."\")\n");
  echo("       return input;\n");
  echo("    }\n\n");
  
  echo("function RetornaHorarioEntrega()\n");
  echo("     {\n");
  echo("        hr_entrega = document.getElementById(\"hora_limite_entrega\").value;\n");
  echo("        min_entrega = document.getElementById(\"minuto_limite_entrega\").value;\n");
  echo("        horario_entrega = hr_entrega+':'+min_entrega;\n");
  echo("        var docHorario_entrega = document.createElement('input'); \n");
  echo("        docHorario_entrega.setAttribute('value',horario_entrega);\n");
  echo("		return(docHorario_entrega);\n");
  echo("     }\n");

  echo("function RetornaHorarioDisponibilizacao()\n");
  echo("     {\n");
  echo("        hr_disp = document.getElementById(\"hora_disponibilizacao\").value;\n");
  echo("        min_disp = document.getElementById(\"minuto_disponibilizacao\").value;\n");
  echo("        horario_disp = hr_disp+':'+min_disp;\n");
  echo("        var docHorario_disp = document.createElement('input'); \n");
  echo("        docHorario_disp.setAttribute('value',horario_disp);\n");
  echo("        return(docHorario_disp);\n");
  echo("     }\n");
    
  echo("      function verifica_intervalos()\n");
  echo("      {\n");
  echo("	    var dt_disponibilizacao,limite_entrega,hora_disponibilizacao,hora_limite_entrega,data_atual,hora_atual;\n");
  echo("        dt_disponibilizacao = document.getElementById(\"dt_disponibilizacao\");\n");
  echo("        limite_entrega = document.getElementById(\"limite_entrega\");\n");
  echo("        data_atual = RetornaDataAtual();\n");
  echo("		hora_limite_entrega = RetornaHorarioEntrega();\n");
  echo("		hora_disponibilizacao = RetornaHorarioDisponibilizacao();\n");
  echo("        hora_atual = RetornaHoraAtual();\n");
  echo("        if (!DataValidaAux(dt_disponibilizacao) || !DataValidaAux(limite_entrega))\n");
  echo("          return (false);\n");
  echo("        if (!hora_valida(hora_disponibilizacao))\n");
  echo("        {\n");
  /* Frase #45 - Hora de disponibilizacao invalida. Por favor volte e corrija. */
  echo("          alert('".RetornaFraseDaLista($lista_frases, 45)."');\n");
  echo("          return(false);\n");
  echo("        }\n");
  echo("        if (!hora_valida(hora_limite_entrega))\n");
  echo("        {\n");
 /* Frase #46 - Hora de limite de entrega invalida. Por favor volte e corrija. */
  echo("          alert('".RetornaFraseDaLista($lista_frases, 46)."');\n");
  echo("          return(false);\n");
  echo("        }\n");
  echo("        if (ComparaDataHora(data_atual,hora_atual,dt_disponibilizacao,hora_disponibilizacao) > 0 )\n");
  echo("        {\n");
  /* Frase #47 - A disponibilizacao do exercicio deve ser posterior a data atual. */
  echo("          alert('".RetornaFraseDaLista($lista_frases, 47)."');\n");
  echo("          return(false);\n");
  echo("        }\n");
  echo("        if (ComparaDataHora(dt_disponibilizacao,hora_disponibilizacao,limite_entrega,hora_limite_entrega) > 0 )\n");
  echo("        {\n");
  /* Frase #48 - O limite de entrega deve ser posterior a disponibilizacao do exercicio. */
  echo("          alert('".RetornaFraseDaLista($lista_frases, 48)."');\n");
  echo("          return(false);\n");
  echo("        }\n");
  echo("        return(true);\n");
  echo("      }\n");
  /* verifica_notas(flag)
   * flag = 0  -> Questoes sem nota ou nota = 0
   * flag = 1  -> Todas as questoes com nota, ok
   * flag = 2	 ->	Nao ha questoes */
  echo("    function verifica_notas(flag)\n");
  echo("    {\n");
  echo("      if(flag == '0'){\n");
  //188 - Existem quest�es com valores iguais a 0, Deseja continuar?
  echo("        if(confirm('".RetornaFraseDaLista($lista_frases, 188)."'))\n");
  echo("          AplicarExercicio();\n");
  echo("        else\n");
  echo("          EscondeLayer(lay_aplicar);\n");
  echo("      } else if (flag == '1'){\n");
  echo("        AplicarExercicio();\n");
  echo("			} else if (flag == '2'){\n ");
  /* Frase #193 - Nao e possivel aplicar um exercicio vazio. Adicione ao menos uma questao. */
  echo("        alert('".RetornaFraseDaLista($lista_frases, 193)."');");
  echo("      }\n");
  echo("    }\n");

  echo("    function AplicarExercicio()\n");
  echo("    {\n");
  echo("        if(document.getElementById(\"disponibilizacaoa\").checked)\n");
  echo("        {\n");
  echo("	      if(verifica_intervalos()){\n");
  echo("          	dt_disp = document.getElementById(\"dt_disponibilizacao\").value;\n");
  echo("          	hr_disp = document.getElementById(\"hora_disponibilizacao\").value;\n");
  echo("          	min_disp = document.getElementById(\"minuto_disponibilizacao\").value;\n");
  echo("          	horario_disp = hr_disp+':'+min_disp+':00';\n");
  echo("          }\n");
  echo("          else{\n");
  echo("            return 0;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        else\n");
  echo("        {\n");
  echo("            dt_disp = \"".UnixTime2Data($data)."\";\n");
  echo("            horario_disp = \"".UnixTime2Hora($data)."\";\n");
  echo("        }\n");
  echo("        limite_entrega = document.getElementById(\"limite_entrega\");\n");
  echo("        dt_disponibilizacao = document.getElementById(\"dt_disponibilizacao\");\n");
  echo("        dt_entrega = document.getElementById(\"limite_entrega\").value;\n");
  echo("        hr_entrega = document.getElementById(\"hora_limite_entrega\").value;\n");
  echo("        min_entrega = document.getElementById(\"minuto_limite_entrega\").value;\n");
  echo("        horario_entrega = hr_entrega+':'+min_entrega+':00';\n");
  echo("        tp_aplicacao = (document.getElementById(\"tp_aplicacaoi\").checked) ? 'I' : 'G';\n");
  echo("        disp_gabarito = (document.getElementById(\"disp_gabaritos\").checked) ? 'S' : 'N';\n");
  echo("        avaliacao = (document.getElementById(\"avaliacaos\").checked) ? 'S' : 'N';\n");
  echo("        if(document.getElementById(\"disponibilizacaoi\").checked)\n");
  echo("        {\n");
  echo("          if (ComparaDataHora(dt_disponibilizacao,RetornaHorarioDisponibilizacao(),limite_entrega,RetornaHorarioEntrega()) > 0 )\n");
  echo("          {\n");
  /* Frase #48 - O limite de entrega deve ser posterior a disponibilizacao do exercicio. */
  echo("            alert('".RetornaFraseDaLista($lista_frases, 48)."');\n");
  echo("            return(false);\n");
  echo("          }\n");
  echo("        }\n");
  if($exercicio['situacao'] != "C")
    echo("		  xajax_CancelaAplicacaoExercicioDinamic(".$cod_curso.",".$cod_usuario.",".$cod_exercicio.",0);\n");
  echo("		xajax_AplicaExercicioDinamic(".$cod_curso.",".$cod_exercicio.",".$cod_usuario.",dt_disp,horario_disp,dt_entrega,horario_entrega,tp_aplicacao,disp_gabarito,avaliacao);");
  echo("    }\n\n");
  
  echo("    function ExercicioAplicado(avaliacao,cod_avaliacao)\n");
  echo("    {\n");
  echo("      if(avaliacao == 'N')\n");
  echo("        window.location='editar_exercicio.php?cod_curso=".$cod_curso."&cod_exercicio=".$cod_exercicio."&acao=aplicar&atualizacao=true';\n");
  echo("      else\n");
  echo("        window.location='../avaliacoes/ver.php?cod_curso=".$cod_curso."&cod_avaliacao='+cod_avaliacao+'&origem=exercicios&operacao=null&acao=aplicar&atualizacao=true';\n");
  echo("    }\n\n");
  
  echo("    function Voltar()\n");
  echo("    {\n");
  echo("      window.location='exercicios.php?cod_curso=".$cod_curso."&visualizar=E';\n");
  echo("    }\n\n");
  
  echo("    function AplicacaoCancelada(flag)\n");
  echo("    {\n");
  echo("      if(flag)");
  echo("        window.location='editar_exercicio.php?cod_curso=".$cod_curso."&cod_exercicio=".$cod_exercicio."&acao=cancelar&atualizacao=true';\n");
  echo("    }\n\n");
  
  echo("	function VerValor(obj) {
				var newobj = obj.value.replace(',', '.');
				obj.value = newobj;
			} 
	");
  
  echo("	function ApagarExercicio(){
				xajax_AlteraStatusExercicioInternoDinamic(".$cod_curso.",".$cod_exercicio.",'L');
  			}	
  ");
	
  echo("    </script>\n\n");
  
  $objAjax->printJavascript("../xajax_0.2.4/");
  
  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  if($tela_formador)
  {
    $titulo="<span id=\"tit_".$exercicio['cod_exercicio']."\">".$exercicio['titulo']."</span>";
    /* Frase #50 - Renomear */
    $renomear="<span onclick=\"AlteraTitulo('".$exercicio['cod_exercicio']."');\" id=\"renomear_".$exercicio['cod_exercicio']."\">".RetornaFraseDaLista($lista_frases, 50)."</span>";
		$texto="<span id=\"text_".$exercicio['cod_exercicio']."\">".$exercicio['texto']."</span>";
    /* Frase #51 - Editar texto */
    $editar="<span onclick=\"AlteraTexto(".$exercicio['cod_exercicio'].");\">".RetornaFraseDaLista($lista_frases, 51)."</span>";
    /* Frase #52 - Limpar texto */
    $limpar="<span onclick=\"LimparTexto(".$exercicio['cod_exercicio'].");\">".RetornaFraseDaLista($lista_frases, 52)."</span>";
    /* Frase #53 - Aplicar */
    $aplicar="<span onclick=\"MostraLayer(lay_aplicar,140,event);\">".RetornaFraseDaLista($lista_frases, 53)."</span>";
    $apagar="<span onclick=\"ApagarExercicio();\">".RetornaFraseDaLista($lista_frases_geral, 1)."</span>";
    /* Frase #54 - Reaplicar */
    $reaplicar="<span onclick=\"MostraLayer(lay_aplicar,140,event);\">".RetornaFraseDaLista($lista_frases, 54)."</span>";
    /* Frase #55 - Cancelar aplicacao */
    $cancelar="<span onclick=\"xajax_CancelaAplicacaoExercicioDinamic(".$cod_curso.",".$cod_usuario.",".$cod_exercicio.",1);\">".RetornaFraseDaLista($lista_frases, 55)."</span>";

    /* Frase #6 - Compartilhado com Formadores */
    if($exercicio['tipo_compartilhamento'] == "F")
      $compartilhamento = RetornaFraseDaLista($lista_frases, 6);
    /* Frase #8 - Nao compartilhado */
    else
      $compartilhamento = RetornaFraseDaLista($lista_frases, 8);
        
    if($cod_usuario == $exercicio['cod_usuario'] && $exercicio['situacao'] == 'C')
      $compartilhamento = "<span id=\"comp_".$exercicio['cod_exercicio']."\" class=\"link\" onclick=\"js_cod_item='".$exercicio['cod_exercicio']."';AtualizaComp('".$exercicio['tipo_compartilhamento']."');MostraLayer(cod_comp,140,event);return(false);\">".$compartilhamento."</span>";

	/* Frase #1 - Exercicios */
	/* Frase #49 - Editar Exercicio */
	echo("          <h4>".RetornaFraseDaLista($lista_frases, 1)." - ".RetornaFraseDaLista($lista_frases, 49)."</h4>\n");
	
  	/* Frase #5 - Voltar */
  	echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"".RetornaFraseDaLista($lista_frases, 1)."\" /></span><br /><br />\n");

  	echo("          <div id=\"mudarFonte\">\n");
  	echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  	echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  	echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  	echo("          </div>\n");
	echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
	echo("            <tr>\n");
	echo("              <td valign=\"top\">\n");
  	echo("                <ul class=\"btAuxTabs\">\n");
  	/* Frase #5 - Voltar */
  	echo("                  <li><span onclick='Voltar();'>".RetornaFraseDaLista($lista_frases,5)."</span></li>\n");
    	/* Frase #56 - Historico */
    	echo("              	<li><span onclick=\"window.open('historico_exercicio.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_exercicio=".$cod_exercicio."','".RetornaFraseDaLista($lista_frases, 56)."','width=600,height=400,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');\">".RetornaFraseDaLista($lista_frases, 56)."</span></li>\n");
  	echo("                </ul>\n");
  	echo("              </td>\n");
  	echo("            </tr>\n");
  	echo("            <tr>\n");
  	echo("              <td valign=\"top\">\n");
	echo("                  <table border=0 width=\"100%\" cellspacing=0 id=\"tabelaInterna\" class=\"tabInterna\">\n");
	echo("                    <tr class=\"head\">\n");
	/* Frase #13 - Titulo */
	if($exercicio['situacao'] == 'C')
	  echo("                      <td class=\"alLeft\" colspan=\"3\">".RetornaFraseDaLista($lista_frases, 13)."</td>\n");
	else
	  echo("                      <td class=\"alLeft\" colspan=\"2\">Titulo</td>\n");
    /* 70 - Opcoes (ger)*/
	echo("                      <td width=\"20%\">".RetornaFraseDaLista($lista_frases_geral, 70)."</td>\n");
	/* Frase #57 - Compartilhamento */
	echo("                      <td width=\"20%\" colspan=\"2\">".RetornaFraseDaLista($lista_frases, 57)."</td>\n");
	echo("                    </tr>\n");
	echo("                    <tr id='tr_".$exercicio['cod_exercicio']."'>\n");
	if($exercicio['situacao'] == 'C')
	  echo("                      <td class=\"itens\" colspan=\"3\">".$titulo."</td>\n");
	else
	  echo("                      <td class=\"itens\" colspan=\"2\">".$titulo."</td>\n");
	echo("                      <td align=\"left\" valign=\"top\" class=\"botao2\">\n");
	echo("                        <ul>\n");
	if($exercicio['situacao'] == 'C')
	{
	  echo("                          <li>".$renomear."</li>\n");
	  echo("                          <li>".$limpar."</li>\n");
	  echo("                          <li>".$editar."</li>\n");
	  echo("                          <li>".$aplicar."</li>\n");
	}
	else
	{
      echo("                          <li>".$reaplicar."</li>\n");
	  echo("                          <li>".$cancelar."</li>\n");
	}
	// G 1 - Apagar
	echo("                          <li>".$apagar."</li>\n");
	echo("                        </ul>\n");
	echo("                      </td>\n");
	echo("                      <td colspan=\"2\">".$compartilhamento."</td>\n");
    echo("                    </tr>\n");
	echo("                    <tr class=\"head\">\n");
	/* Frase #58 - Texto */
	echo("                      <td class=\"center\" colspan=\"6\">".RetornaFraseDaLista($lista_frases, 58)."</td>\n");
	echo("                    </tr>\n");
	echo("                    <tr>\n");
	echo("                      <td class=\"itens\" colspan=\"6\">\n");
	echo("                        <div class=\"divRichText\">\n");
	echo ("                        ".$texto."\n");
	echo("                        </div>\n");
	echo("                      </td>\n");
	echo("                    </tr>\n");
    echo("                  <tr class=\"head\">\n");
	/* Frase #59 - Questoes */
	echo("                    <td class=\"center\" colspan=\"6\">".RetornaFraseDaLista($lista_frases, 59)."</td>\n");
	echo("                  </tr>\n");
	echo("                  <tr class=\"head01\">\n");
	if($exercicio['situacao'] == 'C')
      echo("                    <td width=\"2\"><input type=\"checkbox\" id=\"checkMenu\" onClick=\"CheckTodos();\" /></td>\n");
  /* Frase #13 - Titulo */
	echo("                    <td class=\"alLeft\">".RetornaFraseDaLista($lista_frases, 13)."</td>\n");
	/* Frase #60 - Tipo */
	echo("                    <td width=\"10%\">".RetornaFraseDaLista($lista_frases, 60)."</td>\n");
  /* Frase #61 - Topico */
	echo("                    <td width=\"20%\">".RetornaFraseDaLista($lista_frases, 61)."</td>\n");
  /* Frase #62 - Dificuldade */
	echo("                    <td width=\"10%\">".RetornaFraseDaLista($lista_frases, 62)."</td>\n");
	/* Frase #15 - Valor */
	echo("                    <td width=\"10%\">".RetornaFraseDaLista($lista_frases, 15)."</td>\n");
	echo("                  </tr>\n");
	
	
    if ((count($lista_questoes)>0)&&($lista_questoes != null))
    {
      foreach ($lista_questoes as $cod => $linha_item)
      {
        if($linha_item['tp_questao'] == 'O'){
        	$tipo = RetornaFraseDaLista($lista_frases, 159);
        } elseif($linha_item['tp_questao'] == 'D'){
        	$tipo = RetornaFraseDaLista($lista_frases, 160);
        }
        $titulo = $linha_item['titulo'];
        $topico = RetornaNomeTopico($sock,$linha_item['cod_topico']);
        $icone = "<img src=\"../imgs/arqp.gif\" alt=\"\" border=\"0\" /> ";
        $dificuldade = $linha_item['nivel'];
        $valor = "<span id=\"valorQuestao_".$linha_item['cod_questao']."\">".$linha_item['valor']."</span>";
        
        echo("                  <tr id=\"trQuestao_".$linha_item['cod_questao']."\">\n");
        if($exercicio['situacao'] == 'C')
          echo("                    <td width=\"2\"><input type=\"checkbox\" name=\"chkQuestao\" id=\"itm_".$linha_item['cod_questao']."\" onclick=\"VerificaCheck();\" value=\"".$linha_item['cod_questao']."\" /></td>\n");
        echo("                    <td align=left>".$icone."<a href=\"editar_questao.php?cod_curso=".$cod_curso."&cod_questao=".$linha_item['cod_questao']."\">".$titulo."</a></td>\n");
        echo("                    <td>".$tipo."</td>\n");
        echo("                    <td>".$topico."</td>\n");
        echo("                    <td>".$dificuldade."</td>\n");
        echo("                    <td>".$valor."</td>\n");
        echo("                  </tr>\n");
      }
      
      echo("                  <tr id=\"trTotal\">\n");
      /* Frase #63 - Total */
      if($exercicio['situacao'] == 'C')
        echo("                    <td colspan=\"5\" align=\"right\"><b>".RetornaFraseDaLista($lista_frases, 63).":</b></td>\n");
      else
        echo("                    <td colspan=\"4\" align=\"right\"><b>".RetornaFraseDaLista($lista_frases, 60).":</b></td>\n");
      echo("                    <td id=\"totalValorQuestoes\"><b>".$totalValorQuestoes."</b></td>\n");
      echo("                  </tr>\n");
    }
    else
    {
      echo("                  <tr>\n");
      /* Frase #35 - Nao ha nenhuma questao */
      echo("                    <td colspan=\"6\">".RetornaFraseDaLista($lista_frases, 35)."</td>\n");
      echo("                  </tr>\n");
      echo("                  <tr style=\"display:none;\">\n");
      /* Frase #63 - Total */
      echo("                    <td colspan=\"5\" align=\"right\">".RetornaFraseDaLista($lista_frases, 60).":</td>\n");
      echo("                    <td id=\"totalValorQuestoes\"></td>\n");
      echo("                  </tr>\n");
    }
    
    if($exercicio['situacao'] == C)
	{
	  echo("                  <tr id=\"optQuestoes\">\n");
	  echo("                    <td align=\"left\" colspan=\"6\">\n");
	  echo("                      <ul>\n");
	  /* Frase #64 - Apagar selecionadas */
	  echo("                        <li class=\"menuUp\" id=\"mQuestao_apagar\"><span id=\"sQuestao_apagar\">".RetornaFraseDaLista($lista_frases, 64)."</span></li>\n");
	  /* Frase #65 - Atribuir valor */
    echo("                        <li class=\"menuUp\" id=\"mQuestao_valor\"><span id=\"sQuestao_valor\">".RetornaFraseDaLista($lista_frases, 65)."</span></li>\n");
	  echo("                      </ul>\n");
	  echo("                    </td>\n");
	  echo("                  </tr>\n");
    echo("                  <tr id=\"addQuestoes\">\n");
    /* Frase #66 - Adicionar questoes */
	  echo("                    <td align=\"left\" colspan=\"6\"><span id=\"adicionarQuestao\" class=\"link\" onclick=\"window.location='questoes.php?cod_curso=".$cod_curso."&visualizar=Q&cod_exercicio=".$cod_exercicio."';\">(+) ".RetornaFraseDaLista($lista_frases, 66)."</span></td>\n");
	  echo("                  </tr>\n");
	}
	
    echo("                  <tr class=\"head\">\n");
	/* Frase #12 - Arquivos */
	echo("                    <td colspan=\"6\">".RetornaFraseDaLista($lista_frases, 12)."</td>\n");
	echo("                  </tr>\n");
	echo("                  <tr>\n");
	/* Frase #13 - Raiz */
	echo("                    <td colspan=\"6\" class=\"alLeft\" id=\"caminhoPastas\"><span class=\"link\" onclick=\"AbrePasta('Raiz/');\"><img src=\"../imgs/pasta.gif\">".RetornaFraseDaLista($lista_frases, 13)."</span></td>\n");
	echo("                  </tr>\n");
    echo("                  <tr class=\"head01\">\n");
	if($exercicio['situacao'] == 'C')
      echo("                    <td width=\"2\"><input type=\"checkbox\" id=\"checkMenuArq\" onclick=\"CheckTodosArq();\" /></td>\n");
  /* Frase #67 - Nome */
	echo("                    <td colspan=\"3\" class=\"alLeft\">".RetornaFraseDaLista($lista_frases, 67)."</td>\n");
	/* Frase #68 - Tamanho */
	echo("                    <td width=\"15%\">".RetornaFraseDaLista($lista_frases, 68)."</td>\n");
  /* Frase #69 - Data */
	echo("                    <td width=\"15%\">".RetornaFraseDaLista($lista_frases, 69)."</td>\n");
	echo("                  </tr>\n");

    if(count($lista_arq) > 0 || $lista_arq != null)
    {
      foreach ($lista_arq as $cod => $linha_arq)
      {
        $linha_arq['Arquivo'] = mb_convert_encoding($linha_arq['Arquivo'], "ISO-8859-1", "UTF-8");
        if(!($linha_arq['Arquivo'] == "") && $linha_arq['Diretorio'] == "")
        {
          $caminho_arquivo = $dir_exercicio_temp['link'] . ConverteUrl2Html($linha_arq['Diretorio'] . "/" . $linha_arq['Arquivo']);
	      //converte o o caminho e o nome do arquivo que vêm do linux em UTF-8 para 
          //ISO-8859-1 para ser exibido corretamente na página.
		  $caminho_arquivo = mb_convert_encoding($caminho_arquivo, "ISO-8859-1", "UTF-8");
		  $linha_arq['Arquivo'] = mb_convert_encoding($linha_arq['Arquivo'], "ISO-8859-1", "UTF-8");			
		  if(eregi(".zip$", $linha_arq['Arquivo'])) {
		  // arquivo zip
		    $imagem = "<img alt=\"\" src=\"../imgs/arqzip.gif\" border=\"0\" />";
	        $tag_abre = "<span class=\"link\" id=\"nomeArq_".($cod+1)."\" onclick=\"WindowOpenVer('".$caminho_arquivo ."');\">";
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

			  $tag_abre = "<span class=\"link\" id=\"nomeArq_".($cod+1)."\" onclick=\"WindowOpenVer('".$caminho_arquivo."');\">";
            }

	      $tag_fecha = "</span>";
        }
        else
        {
		  // pasta
		  $imagem = "<img alt=\"\" src=\"../imgs/pasta.gif\" border=\"0\" />";
		  $linha_arq['Arquivo'] = $linha_arq['Diretorio'];
		  $tag_abre = "<span class=\"link\" id=\"nomeArq_".($cod+1)."\" onclick=\"AbrePasta('Raiz/'+'".$linha_arq['Arquivo']."'+'/');\">\n";
		  $tag_fecha = "</span>";
        }
        
        /* Frase #70 - Oculto */
        if($linha_arq['Status'] == true)
        	$oculto = " <span style='color:red;'>(".RetornaFraseDaLista($lista_frases, 70).")</span>";
        else
        	$oculto = ""; 
        
	    echo("                        <tr name=\"Raiz/\" id=\"trArq_".($cod+1)."\">\n");
	    if($exercicio['situacao'] == 'C')
          echo("                          <td width=\"2\"><input type=\"checkbox\" name=\"chkArq\" id=\"chkArq_".($cod+1)."\" onclick=\"VerificaChkBoxArq();\" value=\"".$cod."\" /></td>\n");
        echo("                          <td colspan=\"3\" class=\"alLeft\">".$imagem.$tag_abre.$linha_arq['Arquivo'].$tag_fecha.$oculto."</td>\n");
        echo("                          <td>".round(($linha_arq['Tamanho']/1024),2)." Kb</td>\n");
        echo("                          <td>".unixTime2DataHora($linha_arq['Data'])."</td>\n");
        echo("                        </tr>\n");
      }
    }

	if($exercicio['situacao'] == 'C')
	{
      echo("                  <tr id=\"optArq\">\n");
	  echo("                    <td align=\"left\" colspan=\"6\">\n");
	  echo("                      <ul>\n");
	  /* Frase #71 - Apagar */
	  echo("                        <li class=\"menuUp\" id=\"mArq_apagar\"><span id=\"sArq_apagar\">".RetornaFraseDaLista($lista_frases, 71)."</span></li>\n");
    /* Frase #72 - Descompactar */
	  echo("                        <li class=\"menuUp\" id=\"mArq_descomp\"><span id=\"sArq_descomp\">".RetornaFraseDaLista($lista_frases, 72)."</span></li>\n");
    /* Frase #73 - Ocultar */
	  echo("                        <li class=\"menuUp\" id=\"mArq_ocultar\"><span id=\"sArq_ocultar\">".RetornaFraseDaLista($lista_frases, 73)."</span></li>\n");
	  echo("                      </ul>\n");
	  echo("                    </td>\n");
	  echo("                  </tr>\n");
	  echo("                  <tr>\n");
	  echo("                    <td align=\"left\" colspan=\"6\">\n");
	  echo("                      <form name=\"formFiles\" id=\"formFiles\" enctype=\"multipart/form-data\" method=\"post\" action=\"acoes.php\">\n");
	  echo("                        <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
	  echo("                        <input type=\"hidden\" name=\"cod_exercicio\" value=\"".$cod_exercicio."\" />\n");
      echo("                        <input type=\"hidden\" name=\"acao\" value=\"anexar\" />\n");
      echo("                        <input type=\"hidden\" name=\"pasta\" value=\"exercicio\" />\n");
      echo("                        <input type=\"hidden\" name=\"subpasta\" value=\"\" />\n");
	  echo("                        <div id=\"divArquivoEdit\" class=\"divHidden\">\n");
	  echo("                          <img alt=\"\" src=\"../imgs/paperclip.gif\" border=\"0\" />\n");
	  echo("                          <span class=\"destaque\">" . RetornaFraseDaLista($lista_frases_geral, 26) . "</span>\n");
	  // ?? - 
	  echo("                          <span> - Adicionar Descricao</span>\n");
	  echo("                          <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n");
	  echo("                          <input type=\"file\" id=\"input_files\" name=\"input_files\" class=\"input\" onchange=\"EdicaoArq(1)\">\n");
//	  echo("                          &nbsp;&nbsp;\n");
//	  echo("                          <span onclick=\"EdicaoArq(1);\" id=\"OKFile\" class=\"link\">" . RetornaFraseDaLista($lista_frases_geral, 18) . "</span>\n");
//	  echo("                          &nbsp;&nbsp;\n");
//	  echo("                          <span onclick=\"EdicaoArq(0);\" id=\"cancFile\" class=\"link\">" . RetornaFraseDaLista($lista_frases_geral, 2) . "</span>\n");
	  echo("                        </div>\n");
	  echo("                        <div id=\"divAnexando\" class=\"divHidden\"></div>");
	  /* 26 - Anexar arquivos (ger) */
	  echo("                        <div id=\"divArquivo\"><img alt=\"\" src=\"../imgs/paperclip.gif\" border=\"0\" /> <span class=\"link\" id =\"insertFile\" onclick=\"AcrescentarBarraFile(1);\">" . RetornaFraseDaLista($lista_frases_geral, 26) . "</span></div>\n");
	  echo("                      </form>\n");
	  echo("                    </td>\n");
	  echo("                  </tr>\n");
	}
	echo("                </table>\n");
	echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("          <span class=\"btsNavBottom\"><a href=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></a> <a href=\"#topo\"><img src=\"../imgs/btTopo.gif\" border=\"0\" alt=\"Topo\" /></a></span>\n");
  /* Nao formador: */
  }
  else
  {
		/* Frase #1 - Exercicios */
  	/* Frase #74 - Area restrita ao formador */
  	echo("          <h4>".RetornaFraseDaLista($lista_frases, 1)." - ".RetornaFraseDaLista($lista_frases, 74)."</h4>\n");
	
        /* Frase #5 - Voltar */
        echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"".RetornaFraseDaLista($lista_frases, 5)."\" /></span><br /><br />\n");

        echo("          <div id=\"mudarFonte\">\n");
        echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
        echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
        echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
        echo("          </div>\n");

    	/* Frase #5 - Voltar */
    	echo("<form><input class=\"input\" type=button value=\"".RetornaFraseDaLista($lista_frases, 5)."\" onclick=\"history.go(-1);\" /></form>\n");
  }

  echo("        </td>\n");
  echo("      </tr>\n"); 

  include("../tela2.php");
  
  /* Atribuir valor */
  echo("    <div id=\"layer_atribuir\" class=popup>\n");
  echo("     <div class=\"posX\"><span onclick=\"EscondeLayer(lay_atribuir);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("      <div class=int_popup>\n");
  echo("        <div class=ulPopup>\n");    
  /* Frase #15 - Valor */
  echo("            ".RetornaFraseDaLista($lista_frases, 15).": ");
  echo("            <input class=\"input\" type=\"text\" id=\"valor\" size=\"8\" value=\"\" maxlength=\"10\" onkeyup=\"javascript:VerValor(this);\"/><br /><br />\n");
  /* 18 - Ok (gen) */
  echo("            <input type=\"button\" class=\"input\" onClick=\"VerificaValor(document.getElementById('valor').value);\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" />\n");
  /* 2 - Cancelar (gen) */
  echo("            &nbsp; &nbsp; <input type=\"button\" class=\"input\" onClick=\"EscondeLayer(lay_atribuir);document.getElementById('valor').value='';\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");
  echo("        </div>\n");
  echo("      </div>\n");
  echo("    </div>\n\n");
  
  /* Aplicar */
  echo("    <div id=\"layer_aplicar\" class=popup>\n");
  echo("     <div class=\"posX\"><span onclick=\"EscondeLayer(lay_aplicar);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("      <div class=int_popup>\n");
  echo("        <div class=ulPopup>\n");    
  /* Frase #75 - Associar a avaliacao */
  echo("          ".RetornaFraseDaLista($lista_frases, 75).": <br />");
  /* Frase #76 - Sim */
  echo("		  <input type=\"radio\" name=\"avaliacao\" id=\"avaliacaos\" value=\"S\">".RetornaFraseDaLista($lista_frases, 76)."\n");
  /* Frase #77 - Nao */
  echo("		  <input type=\"radio\" name=\"avaliacao\" id=\"avaliacaon\" value=\"N\">".RetornaFraseDaLista($lista_frases, 77)."\n");
  echo("		  <br /><br />\n");
  /* Frase #78 - Disponibilizar gabarito com a correcao */
  echo("          ".RetornaFraseDaLista($lista_frases, 78).": <br />");
  echo("		    <input type=\"radio\" name=\"disp_gabarito\" id=\"disp_gabaritos\" value=\"S\">".RetornaFraseDaLista($lista_frases, 76)."\n");
  echo("		    <input type=\"radio\" name=\"disp_gabarito\" id=\"disp_gabariton\" value=\"N\">".RetornaFraseDaLista($lista_frases, 77)."\n");
  echo("		  <br /><br />\n");
  /* Frase #79 - Tipo de aplicacao */
  echo("          ".RetornaFraseDaLista($lista_frases, 79).": <br />");
  /* Frase #80 - Individual */
  echo("		    <input type=\"radio\" name=\"tp_aplicacao\" id=\"tp_aplicacaoi\" value=\"I\">".RetornaFraseDaLista($lista_frases, 80)."\n");
  /* Frase #81 - Em Grupo */
  echo("		    <input type=\"radio\" name=\"tp_aplicacao\" id=\"tp_aplicacaog\" value=\"G\">".RetornaFraseDaLista($lista_frases, 81)."\n");
  echo("		  <br /><br />\n");
  /* Frase #82 - Disponibilizacao */
  echo("          ".RetornaFraseDaLista($lista_frases, 82).": <br />");
  /* Frase #83 - Imediata */
  echo("		    <input type=\"radio\" onChange=\"ExibirAgendamento(this.value);\" name=\"disponibilizacao\" id=\"disponibilizacaoi\" value=\"I\">".RetornaFraseDaLista($lista_frases, 83)."\n");
  /* Frase #84 - Agendar */
  echo("		    <input type=\"radio\" onChange=\"ExibirAgendamento(this.value);\" name=\"disponibilizacao\" id=\"disponibilizacaoa\" value=\"A\">".RetornaFraseDaLista($lista_frases, 84)."\n");
  echo("		  <br /><br />\n");
  echo("          <div id=\"div_disp\" style=\"display:none;\">\n");
  /* Frase #69 - Data */
  echo("            ".RetornaFraseDaLista($lista_frases, 69).": <input class=\"input\" type=\"text\" size=\"10\" maxlength=\"10\" value=\"".UnixTime2Data($data)."\" id=\"dt_disponibilizacao\" name=\"dt_disponibilizacao\" />\n");
  echo("            <img src=\"../imgs/ico_calendario.gif\" alt=\"calendario\" onclick=\"displayCalendar(document.getElementById('dt_disponibilizacao'),'dd/mm/yyyy',this);\" />\n");
  /* Frase #85 - Horario */
  $horario = explode(":",UnixTime2Hora($data));
  echo("            <br /><br />".RetornaFraseDaLista($lista_frases, 85).": <select id=\"hora_disponibilizacao\" class=\"input\">\n");
  
  for($i=0;$i<24;$i++){
  	if($i<10) $i="0$i";
	if($i == $horario[0]) $selected = "selected=selected";
	else $selected = "";
  	echo("<option ".$selected." value=".$i.">".$i."</option>\n");
  }
  echo("</select><b> : </b><select id=\"minuto_disponibilizacao\" class=\"input\">\n");
  for($j=0;$j<60;$j++){
  	if($j<10) $j="0$j";
	if($j == $horario[1]) $selected = "selected=selected";
	else $selected = "";
  	echo("<option ".$selected." value=".$j.">".$j."</option>\n");
  }
  echo("</select>\n");
  echo("          </div><br />\n");
  /* Frase #86 - Limite de entrega: */
  echo("          ".RetornaFraseDaLista($lista_frases, 86).": <br /><br />");
  echo("          <div>\n");
  /* Frase #69 - Data */
  echo("            ".RetornaFraseDaLista($lista_frases, 69).": <input class=\"input\" type=\"text\" size=\"10\" maxlength=\"10\" value=\"".UnixTime2Data($data)."\" id=\"limite_entrega\" name=\"limite_entrega\" />\n");
  echo("            <img src=\"../imgs/ico_calendario.gif\" alt=\"calendario\" onclick=\"displayCalendar(document.getElementById('limite_entrega'),'dd/mm/yyyy',this);\" />\n");
  /* Frase #85 - Horario */
  echo("            <br /><br />".RetornaFraseDaLista($lista_frases, 85).": <select id=\"hora_limite_entrega\" class=\"input\">\n");
  for($i=0;$i<24;$i++){
  	if($i<10) $i="0$i";
  	echo("<option value=".$i.">".$i."</option>\n");
  }
  echo("</select><b> : </b><select id=\"minuto_limite_entrega\" class=\"input\">\n");
  for($j=0;$j<60;$j++){
  	if($j<10) $j="0$j";
  	echo("<option  value=".$j.">".$j."</option>\n");
  }
  echo("</select>\n");
  echo("          </div><br /><br />\n");
  /* 18 - Ok (gen) */
  echo("            <input type=\"button\" class=\"input\" onClick=\"xajax_VerificaNotas(".$cod_exercicio.",".$cod_curso.");\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" />\n");

  /* 2 - Cancelar (gen) */
  echo("            &nbsp; &nbsp; <input type=\"button\" class=\"input\" onClick=\"EscondeLayer(lay_aplicar);\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");
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
  /* Frase #192 - Compartilhamento alterado com sucesso. */
  echo("          <input type=hidden name=texto id=texto value=\"".RetornaFraseDaLista($lista_frases, 192)."\" />\n");
  echo("          <ul class=ulPopup>\n");
  echo("            <li onClick=\"document.getElementById('tipo_comp').value='F'; xajax_MudarCompartilhamentoDinamic(xajax.getFormValues('form_comp'), 'Compartilhado com formadores', 'E'); EscondeLayers();\">\n");
  echo("              <span id=\"tipo_comp_F\" class=\"check\"></span>\n");
  /* Frase #6 - Compartilhado com formadores */
  echo("              <span>".RetornaFraseDaLista($lista_frases, 6)."</span>\n");
  echo("            </li>\n");
  echo("            <li onClick=\"document.getElementById('tipo_comp').value='N'; xajax_MudarCompartilhamentoDinamic(xajax.getFormValues('form_comp'), 'Nao Compartilhado', 'E'); EscondeLayers();\">\n");
  echo("              <span id=\"tipo_comp_N\" class=\"check\"></span>\n");
  /* Frase #8 - Nao Compartilhado */
  echo("              <span>".RetornaFraseDaLista($lista_frases, 8)."</span>\n");
  echo("            </li>\n");
  echo("          </ul>\n");    
  echo("        </form>\n");
  echo("      </div>\n");
  echo("    </div>\n");

  Desconectar($sock);
?>