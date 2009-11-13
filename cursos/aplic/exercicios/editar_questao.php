<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/exercicios/editar_questao.php

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
  ARQUIVO : cursos/aplic/exercicios/editar_questao.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("exercicios.inc");

  require_once("../xajax_0.2.4/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->registerFunction("DecodificaString");
  $objAjax->registerFunction("RetornaFraseDinamic");
  $objAjax->registerFunction("RetornaFraseGeralDinamic");
  $objAjax->registerFunction("EditarAlternativaObjDinamic");
  $objAjax->registerFunction("EditarAlternativaDissDinamic");
  $objAjax->registerFunction("CriarAlternativaDinamic");
  $objAjax->registerFunction("ApagarAlternativaDinamic");
  $objAjax->registerFunction("AtualizarNivelDinamic");
  $objAjax->registerFunction("AtualizarTopicoDinamic");
  $objAjax->registerFunction("CriaNovoTopicoDinamic");
  $objAjax->registerFunction("EditarTituloQuestaoDinamic");
  $objAjax->registerFunction("EditarEnunciadoDinamic");
  $objAjax->registerFunction("EditarGabaritoQuestaoDissDinamic");
  $objAjax->registerFunction("EditarGabaritoQuestaoObjDinamic");
  $objAjax->registerFunction("ExcluiArquivoDinamic");
  $objAjax->registerFunction("ExibeArquivoAnexadoDinamic");
  $objAjax->registerFunction("VerificaExistenciaArquivoDinamic");
  $objAjax->registerFunction("AtualizaPosicoesDasAlternativasDinamic");
  $objAjax->registerFunction("MudarCompartilhamentoDinamic");
  $objAjax->registerFunction("AtualizaIconesDinamic");
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
  $feedbackObject = new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
//  $feedbackObject->addAction("anexar", 62, sprintf(RetornaFraseDaLista($lista_frases, 189), ((int) ini_get('upload_max_filesize'))));
  $feedbackObject->addAction("anexar", 62, 61);
  
  $questao = RetornaQuestao($sock,$cod_questao);
  $questao_diss = RetornaQuestaoDiss($sock,$cod_questao);
  $alternativas = RetornaAlternativas($sock,$cod_questao);
  $topicos = RetornaTopicos($sock);
  $dir_questao_temp = CriaLinkVisualizar($sock, $cod_curso, $cod_usuario, $cod_questao, $diretorio_arquivos, $diretorio_temp, "questao");
  $tp_questao = $questao['tp_questao'];
  $lista_arq = RetornaArquivosQuestao($cod_curso, $dir_questao_temp['link']);
  $num_arq_vis = RetornaNumArquivosVisiveis($lista_arq);

  if($tp_questao == 'O')
  {
    $gabaritoObj = RetornaGabaritoQuestaoObj($sock, $cod_questao);
    $existeGabaritoDiss = 0;
  }
  else
  {
    $gabaritoObj = null;
    $existeGabaritoDiss = 1;
  }
    
    

  /*********************************************************/
  /* in�io - JavaScript */
  echo("    <script type=\"text/javascript\" language=\"JavaScript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
  echo("    <script type=\"text/javascript\" language=\"JavaScript\" src='../js-css/tablednd.js'></script>\n");
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

  if($tp_questao == 'O')
  {
  	echo("    var posiAlt = new Array();\n");
  	echo("    var gabarito = new Array();\n\n");
  }
  
  echo("    var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
  echo("    var isMinNS6 = ((navigator.userAgent.indexOf(\"Gecko\") != -1) && (isNav));\n");
  echo("    var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");
  echo("    var Xpos, Ypos;\n");
  echo("    var editaTexto = 0;\n");
  echo("    var editaTitulo = 0;\n");
  echo("    var input = 0;\n");
  echo("    var cancelarElemento = null;\n");
  echo("    var contaArq = ".count($lista_arq).";\n");
  echo("    var gabaritosVisiveis = 0;\n");
  echo("    var tBody;\n");
  echo("    var pastaRaiz = \"".$dir_questao_temp['link']."\";");
  echo("    var pastaAtual = \"Raiz/\";\n");
  echo("    var tableDnD;\n");
  echo("    var cod_comp;\n");
  echo("    var pastaRaiz = \"".$dir_questao_temp['link']."\";");
  echo("    var pastaAtual = \"Raiz/\";\n");
  echo("    var js_comp = new Array();\n");
  echo("    var cancelarTodos = 0;\n\n");

  if ($tp_questao == 'O' && (count($alternativas)>0) && ($alternativas != null))
  {
    $qtdAlternativas = 0;
    foreach ($alternativas as $cod => $linha_item)
    {
      echo("    posiAlt[".$qtdAlternativas."] = ".$linha_item['cod_alternativa'].";\n");
      $qtdAlternativas++;
    }
    echo("\n");
  }
  else
  {
    $qtdAlternativas = count($alternativas);
  }

  echo("    var qtdAlternativas = ".$qtdAlternativas.";\n\n");

  if ($gabaritoObj != null)
  {
    $aux = bindec($gabaritoObj);
    while($aux > 0 || $qtdAlternativas > 0)
    {
      echo("    gabarito[".--$qtdAlternativas."] = ". (int) $aux%2 .";\n");
      $aux = (int) $aux/2;
    }
    echo("\n");
  }
  
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

  $icone_correto = " <img src=\"../imgs/certo.png\" alt=\"resposta certa\" border=\"0\" /> ";
  $icone_errado = " <img src=\"../imgs/errado.png\" alt=\"resposta errada\" border=\"0\" /> ";

  echo("	function AtualizaIcones(alt,gabarito){\n
  			arrayAlt = alt.split('.');\n
  				for(i=0;i<gabarito.length;i++){\n
  					if(gabarito[i] == 0){\n
  						document.getElementById('div_'+arrayAlt[i]).innerHTML = '".$icone_errado."';\n
  					}\n
  					else\n
  						document.getElementById('div_'+arrayAlt[i]).innerHTML = '".$icone_correto."';\n
  				}\n
  			}\n\n
  ");
	
  /* Iniciliza os layers. */
  echo("    function Iniciar()\n");
  echo("    {\n");
  echo("      lay_novo_topico = getLayer('layer_novo_topico');\n");
  echo("      cod_comp = getLayer('comp');\n");
  echo("      tableDnD = new TableDnD();\n");
  echo("      tBody = document.getElementById('tBody');\n");
  echo("	  HabilitarMudancaPosicaoAlt();\n");
  echo("      startList();\n");
  echo("      xajax_AtualizaIconesDinamic('".$questao['cod_questao']."','".$cod_curso."');
  ");
  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("    }\n\n");

  echo("    function WindowOpenVer(id)\n");
  echo("    {\n");
  echo("      window.open(\"" . $dir_questao_temp['link'] . "\"+id,'Portfolio','top=50,left=100,width=600,height=400,menubar=yes,status=yes,toolbar=yes,scrollbars=yes,resizable=yes');\n");
  echo("    }\n\n");

  echo("    function EscondeLayers()\n");
  echo("    {\n");
  echo("      hideLayer(lay_novo_topico);\n");
  echo("      hideLayer(cod_comp);\n");
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
  echo("        xajax_EditarTituloQuestaoDinamic(".$cod_curso.", codigo, conteudo, ".$cod_usuario.", \"".RetornaFraseDaLista($lista_frases, 33)."\");\n");
  echo("      }else{\n");
  /* Frase #88 - O titulo nao pode ser vazio. */
  echo("      if ((valor=='ok')&&(document.getElementById(id+'_text').value==''))\n");
  echo("        alert('".RetornaFraseDaLista($lista_frases, 88)."');\n");
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
  echo("      document.getElementById('tit_'+id).className='';\n");
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
  echo("      createSpan.innerHTML='OK';\n");
  echo("      document.getElementById('tit_'+id).appendChild(createSpan);\n");
  //cria o elemento 'espaco' e adiciona na pagina
  echo("      espaco = document.createElement('span');\n");
  echo("      espaco.innerHTML='&nbsp;&nbsp;'\n");
  echo("      document.getElementById('tit_'+id).appendChild(espaco);\n");
  echo("      createSpan = document.createElement('span');\n");
  echo("      createSpan.className='link';\n");
  echo("      createSpan.onclick= function(){ EdicaoTitulo(id, 'tit_'+id, 'canc'); };\n");
  echo("      createSpan.setAttribute('id', 'CancelaEdita');\n");
  echo("      createSpan.innerHTML='Cancelar';\n");
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
  /* Frase #39 - Voce deseja limpar o texto? O conteudo sera perdido. */
  echo("      if(confirm(\"".RetornaFraseDaLista($lista_frases, 39)."\"))\n");
  echo("      {\n");
  //echo("        xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);\n");
  echo("        document.getElementById('text_'+id).innerHTML='';\n");
  echo("        if(id == ".$cod_questao.")\n");
  echo("          xajax_EditarEnunciadoDinamic(".$cod_curso.",".$cod_questao.",'',".$cod_usuario.", \"\");\n");
  echo("        else{\n");
  echo("          cod = RetornaCodAlternativa(id);");
  echo("          xajax_EditarGabaritoQuestaoDissDinamic(".$cod_curso.",".$cod_questao.",cod,'',".$cod_usuario.", \"\");\n");
  echo("        }\n");
  echo("      }\n");
  echo("    }\n\n");
  
  echo("    function LimparGabarito(id)\n");
  echo("    {\n");
  /* Frase #87 - Voce realmente deseja limpar o gabarito? O conteudo sera perdido. */
  echo("      if(confirm(\"".RetornaFraseDaLista($lista_frases, 87)."\"))\n");
  echo("      {\n");
  //echo("        xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);\n");
  echo("        document.getElementById('texto_'+id).innerHTML='';\n");
  echo("        xajax_EditarGabaritoQuestaoDissDinamic(".$cod_curso.", ".$cod_questao.", '');\n");
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
  
  echo("    function AlteraGabarito(id){\n");
  echo("      if (editaTexto==-1 || editaTexto != id){\n");
  echo("        CancelaTodos();\n");
  //echo("        xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);\n");
  echo("        conteudo = document.getElementById('texto_'+id).innerHTML;\n");
  echo("        writeRichTextOnJS_gabarito('texto_'+id+'_text', conteudo, 520, 200, true, false, id);\n");
  echo("        startList();\n");
  echo("        document.getElementById('texto_'+id+'_text').focus();\n");
  echo("        cancelarElemento=document.getElementById('CancelaEdita');\n");
  echo("        editaTexto = id;\n");
  echo("      }\n");
  echo("    }\n\n");

  echo("    function RetornaCodAlternativa(codigo)\n");
  echo("    {\n");
  echo("	  var cod_questao,cod;\n");
  echo("	  cod_questao = \"".$cod_questao."\";\n");
  echo("      codigo = codigo.toString();\n");
  echo("	  cod = codigo.substring(cod_questao.length);");
  echo("      return cod;\n");
  echo("    }\n\n");

  echo("    function EdicaoTexto(codigo, id, valor){\n");
  echo("      var cod;\n");
  echo("      if (valor=='ok'){\n");
  echo("        conteudo=document.getElementById(id+'_text').contentWindow.document.body.innerHTML;\n");
  echo("        if(codigo == ".$cod_questao.")\n");
  echo("          xajax_EditarEnunciadoDinamic(".$cod_curso.",".$cod_questao.",conteudo,".$cod_usuario.", \"\");\n");
  echo("        else{\n");
  echo("          cod = RetornaCodAlternativa(codigo);");
  echo("          xajax_EditarGabaritoQuestaoDissDinamic(".$cod_curso.",".$cod_questao.",cod,conteudo,".$cod_usuario.", \"\");\n");
  echo("        }\n");
  echo("      }\n");
  echo("      else{\n");
  // Cancela Edi�o
  //echo("        if (!cancelarTodos)\n");
  //echo("          xajax_AcabaEdicaoDinamic(cod_curso, cod_item, cod_usuario, 0);\n");
  echo("      }\n");
  echo("      document.getElementById(id).innerHTML=conteudo;\n");
  echo("      editaTexto=-1;\n");
  echo("      cancelarElemento=null;\n");
  echo("      HabilitarMudancaPosicaoAlt();\n");
  echo("    }\n\n");
  
  echo("    function EdicaoTexto_gabarito(codigo, id, valor){\n");
  echo("      var cod;\n");
  echo("      if (valor=='ok'){\n");
  echo("        conteudo=document.getElementById(id+'_text').contentWindow.document.body.innerHTML;\n");
  echo("          cod = RetornaCodAlternativa(codigo);");
  echo("          xajax_EditarGabaritoQuestaoDissDinamic(".$cod_curso.",".$cod_questao.",conteudo);\n");
  echo("      }\n");
  echo("      document.getElementById(id).innerHTML=conteudo;\n");
  echo("      editaTexto=-1;\n");
  echo("      cancelarElemento=null;\n");
  echo("      HabilitarMudancaPosicaoAlt();\n");
  echo("    }\n\n");

  echo("    function VerificaNovoTopico(textbox) {\n");
  echo("      var texto = textbox.value;\n");
  echo("      var select;\n");
  /* Frase #89 - Topico criado com sucesso */
  echo("      xajax_CriaNovoTopicoDinamic(".$cod_curso.",".$cod_questao.",texto,'".RetornaFraseDaLista($lista_frases, 89)."');\n");
  echo("      EscondeLayer(lay_novo_topico);\n");
  echo("    }\n\n");
  
  echo("    function AtualizaTopicoCriado(cod) {\n");
  echo("      select = document.getElementById('selectTopico');\n");
  echo("	  select.selectedIndex = cod; ");
  echo("    }\n\n");

  echo("    function AdicionaNovoTopico(cod,topico)\n");
  echo("    {\n");
  echo("      var select,opt;\n");
  echo("      select = document.getElementById('selectTopico');\n");
  echo("      opt = document.createElement(\"option\");\n");
  echo("      opt.setAttribute(\"value\",cod);\n");
  echo("      opt.innerHTML = topico;\n");
  echo("      select.appendChild(opt);");
  echo("    }\n");

  echo("    function VerificaChkBoxAlt(alpha){\n");
  echo("      checks = document.getElementsByName('chkAlt');\n");
  echo("      var i, j=0;\n");
  echo("      for (i=0; i<checks.length; i++){\n");
  echo("        if(checks[i].checked){\n");
  echo("          j++;\n");
  echo("        }\n");
  echo("      }\n");
  echo("      if (j==1){\n");
  echo("        document.getElementById('mAlt_apagar').className='menuUp02';\n");
  echo("        document.getElementById('sAlt_apagar').onclick= function(){ ApagarAlternativa(); };\n");
  echo("        document.getElementById('mAlt_editar').className='menuUp02';\n");
  echo("        document.getElementById('sAlt_editar').onclick= function(){ EditarAlternativa(); };\n");
  if($tp_questao == 'D')
  {
    echo("        document.getElementById('mAlt_gabarito').className='menuUp02';\n");
    echo("        document.getElementById('sAlt_gabarito').onclick= function(){ ExibirGabarito(); };\n");
  }
  echo("      }else if(j==0){\n");
  echo("        document.getElementById('mAlt_apagar').className='menuUp';\n");
  echo("        document.getElementById('mAlt_editar').className='menuUp';\n");
  echo("        document.getElementById('sAlt_apagar').onclick= function(){  };\n");
  echo("        document.getElementById('sAlt_editar').onclick= function(){  };\n");
  if($tp_questao == 'D')
  {
    echo("        document.getElementById('mAlt_gabarito').className='menuUp';\n");
    echo("        document.getElementById('sAlt_gabarito').onclick= function(){  };\n");
  }
  echo("      }else{\n");
  echo("        document.getElementById('mAlt_apagar').className='menuUp02';\n");
  echo("        document.getElementById('sAlt_apagar').onclick= function(){ ApagarAlternativa(); };\n");
  echo("        document.getElementById('mAlt_editar').className='menuUp';\n");
  echo("        document.getElementById('sAlt_editar').onclick= function(){ };\n");
  if($tp_questao == 'D')
  {
    echo("        document.getElementById('mAlt_gabarito').className='menuUp02';\n");
    echo("        document.getElementById('sAlt_gabarito').onclick= function(){ ExibirGabarito(); };\n");
  }
  echo("      }\n");
  //Nao foi chamado pela funcao CheckTodos
  echo("      if (alpha){\n");
  echo("        if (j==checks.length){ document.getElementById('checkMenuAlt').checked=true; }\n");
  echo("        else document.getElementById('checkMenuAlt').checked=false;\n");
  echo("      }\n");
  echo("    }\n\n");

  echo("    function CheckTodos(flag)\n");
  echo("    {\n");
  echo("      var e;\n");
  echo("      var i;\n");
  echo("      if(flag == 1)\n");
  echo("      {\n");
  echo("        var CabMarcado = document.getElementById('checkMenuArq').checked;\n");
  echo("        var checks=document.getElementsByName('chkArq');\n");
  echo("      }\n");
  echo("      else\n");
  echo("      {\n");
  echo("        var CabMarcado = document.getElementById('checkMenuAlt').checked;\n");
  echo("        var checks=document.getElementsByName('chkAlt');\n");
  echo("      }\n");
  echo("      for(i = 0; i < checks.length; i++)\n");
  echo("      {\n");
  echo("        e = checks[i];\n");
  echo("        e.checked = CabMarcado;\n");
  echo("      }\n");
  echo("      if(flag == 1)\n");
  echo("      {\n");
  echo("        VerificaChkBoxArq(0);\n");
  echo("      }\n");
  echo("      else\n");
  echo("      {\n");
  echo("        VerificaChkBoxAlt(0);\n");
  echo("      }\n");
  echo("    }\n\n");

  echo("    function RetornaValidadeQuestao(cod)\n");
  echo("    {\n");	
  echo("      var i;\n");
  echo("      for(i=0;i<qtdAlternativas;i++){\n");
  echo("        if(posiAlt[i] == cod)\n");
  echo("          return gabarito[i];\n");
  echo("      }\n");
  echo("    }\n\n");

  echo("    function CriaCheckBoxAlt(cod)\n");
  echo("    {\n");	
  echo("      var check = document.createElement(\"input\");\n");
  echo("      check.setAttribute(\"type\", \"checkbox\");\n");
  echo("      check.setAttribute(\"id\",'alt_'+cod);\n");
  echo("      check.setAttribute(\"name\", \"chkAlt\");\n");
  echo("      check.setAttribute(\"value\", cod);\n");
  echo("      check.onclick= function(){ VerificaChkBoxAlt(1); };\n");
  echo("      return check;\n");
  echo("    }\n\n");

  echo("    function CriaSelectAlt(cod)\n");
  echo("    {\n");	
  echo("      var select,opt1,opt2,txt;\n");
  echo("      select = document.createElement(\"select\");\n");
  echo("      select.setAttribute(\"id\",'select_'+cod);\n");
  echo("      select.setAttribute(\"class\",\"input\");\n");
  echo("      opt1 = document.createElement(\"option\");\n");
  echo("      opt1.setAttribute(\"value\",\"0\");\n");
  echo("      opt1.innerHTML = 'Errada';\n");
  echo("      opt2 = document.createElement(\"option\");\n");
  echo("      opt2.setAttribute(\"value\",\"1\");\n");
  echo("      opt2.innerHTML = 'Certa';\n");
  echo("      if(RetornaValidadeQuestao(cod) == 1)\n");
  echo("      {\n");
  echo("        opt2.setAttribute(\"selected\",\"selected\");\n");
  echo("      }\n");
  echo("      else\n");
  echo("      {\n");
  echo("        opt1.setAttribute(\"selected\",\"selected\");\n");
  echo("      }\n");
  echo("      select.appendChild(opt1);\n");
  echo("      select.appendChild(opt2);\n");
  echo("      return select;\n");
  echo("    }\n\n");

  echo("    function CriaSpanEspAlt(qtd)\n");
  echo("    {\n");
  echo("      var span,espaco,i;\n");
  echo("      span = document.createElement(\"span\");\n");
  echo("      espaco = '';\n");
  echo("      for(i=0;i<qtd;i++)\n");
  echo("      {\n");
  echo("        espaco = espaco+'&nbsp;';\n");
  echo("      }\n");
  echo("      span.innerHTML = espaco;\n");
  echo("      return span;\n");
  echo("    }\n\n");

  echo("    function CriaSpanAlt(cod)\n");
  echo("    {\n");	
  echo("      var span = document.createElement(\"span\");\n");
  echo("      span.setAttribute(\"id\",'span_'+cod);\n");
  echo("      return span;\n");
  echo("    }\n\n");
  
  echo("    function CriaSpanDiv(cod)\n");
  echo("    {\n");	
  echo("      var span = document.createElement(\"span\");\n");
  echo("      span.setAttribute(\"id\",'div_'+cod);\n");
  echo("      return span;\n");
  echo("    }\n\n");

  echo("    function CriaSpanOk(cod)\n");
  echo("    {\n");	
  echo("      var span = document.createElement(\"span\");\n");
  echo("      span.setAttribute(\"id\",'spanOk_'+cod);\n");
  echo("      span.setAttribute(\"class\",\"link\");\n");
  echo("      span.innerHTML = 'Ok';\n");
  echo("      span.onclick= function(){ ConfirmaEdicaoAlternativa(cod); };\n");
  echo("      return span;\n");
  echo("    }\n\n");

  echo("    function CriaSpanCanc(cod,conteudo)\n");
  echo("    {\n");	
  echo("      var span = document.createElement(\"span\");\n");
  echo("      span.setAttribute(\"id\",'spanCanc_'+cod);\n"); 
  echo("      span.setAttribute(\"class\",\"link\");\n");
  echo("      span.innerHTML = 'Cancelar';\n");
  echo("      span.onclick= function(){ CancelaEdicaoAlternativa(cod,conteudo); };\n");
  echo("      return span;\n");
  echo("    }\n\n");

  echo("    function CriaInputAlt(conteudo,cod)\n");
  echo("    {\n");
  echo("      var inputAlternativa = document.createElement(\"input\");\n");
  echo("      inputAlternativa.setAttribute(\"type\", \"text\");\n");
  echo("      inputAlternativa.setAttribute(\"value\",conteudo);\n");
  echo("      inputAlternativa.setAttribute(\"class\",\"input\");\n");
  echo("      inputAlternativa.setAttribute(\"id\",'textAlt_'+cod);\n");
  echo("      inputAlternativa.setAttribute(\"size\", \"46\");\n");
  echo("      inputAlternativa.setAttribute(\"maxlength\", \"255\");\n");
  echo("      return inputAlternativa;\n");
  echo("    }\n\n");

  echo("    function CriaCamposEdicao(conteudo,cod)\n");
  echo("    {\n");	
  echo("      var span;\n");
  echo("      span = document.getElementById('span_'+cod);\n");
  echo("      span.appendChild(CriaInputAlt(conteudo,cod));\n");
  if($tp_questao == 'O')
  {
    echo("      span.appendChild(document.createTextNode(' Validade:'));\n");
    echo("      span.appendChild(CriaSelectAlt(cod));\n");
  }
  echo("      span.appendChild(CriaSpanEspAlt(8));\n");
  echo("      span.appendChild(CriaSpanOk(cod));\n");
  echo("      span.appendChild(CriaSpanEspAlt(2));\n");
  echo("      span.appendChild(CriaSpanCanc(cod,conteudo));\n");
  echo("    }\n\n");

  echo("    function CriaSpanEditarGabarito(cod)\n");
  echo("    {\n");	
  echo("      var span = document.createElement(\"span\");\n"); 
  /*181 - Editar gabarito*/
  echo("      span.innerHTML = '".RetornaFraseDaLista($lista_frases, 181)."';\n");
  echo("      span.onclick= function(){ AlteraTexto(cod);DesabilitarMudancaPosicaoAlt(); };\n");
  echo("      return span;\n");
  echo("    }\n\n");

  echo("    function CriaSpanLimpaGabarito(cod)\n");
  echo("    {\n");	
  echo("      var span = document.createElement(\"span\");\n");
  /* 182 - Limpar gabarito */ 
  echo("      span.innerHTML = '".RetornaFraseDaLista($lista_frases, 182)."';\n");
  echo("      span.onclick= function(){ LimparTexto(cod); };\n");
  echo("      return span;\n");
  echo("    }\n\n");

  echo("    function CriaSpanEsconder(cod)\n");
  echo("    {\n");
  echo("      var span = document.createElement(\"span\");\n"); 
  /*183 - Esconder*/
  echo("      span.innerHTML = '".RetornaFraseDaLista($lista_frases, 183)."';\n");
  echo("      span.onclick= function(){ EsconderGabarito(cod); };\n");
  echo("      return span;\n");
  echo("    }\n\n");

  echo("    function CriaSpanGabarito(cod)\n");
  echo("    {\n");	
  echo("      var span = document.createElement(\"span\");\n");
  echo("      span.setAttribute(\"id\",'text_'+cod);\n");
  echo("      span.innerHTML = '';\n");
  echo("      span.onclick= function(){ EsconderGabarito(cod); };\n");
  echo("      return span;\n");
  echo("    }\n\n");

  echo("    function CriaOpcoes(cod)\n");
  echo("    {\n");	
  echo("      var ul,li;\n");
  echo("      ul = document.createElement(\"ul\");\n");
  echo("      li = document.createElement(\"li\");\n");
  echo("      li.appendChild(CriaSpanEditarGabarito(cod));\n");
  echo("      li.appendChild(CriaSpanLimpaGabarito(cod));\n");
  echo("      li.appendChild(CriaSpanEsconder(RetornaCodAlternativa(cod)));\n");
  echo("      ul.appendChild(li);\n");
  echo("      return ul;\n");
  echo("    }\n\n");

  echo("    function AdicionarAlternativa(cod)\n");
  echo("    {\n");	
  echo("      var tr,td,trGab,tdText,tdOp,codigo;\n");
  if($tp_questao == 'D')
  {
    echo("      codigo = cod;\n");
    echo("      cod = RetornaCodAlternativa(cod);\n");
  }
  echo("      tr = document.createElement(\"tr\");\n");
  echo("      tr.setAttribute(\"id\",'trAlt_'+cod);\n");
  echo("      td = document.createElement(\"td\");\n");
  echo("      td.className = 'itens';\n");
  echo("      td.setAttribute(\"colspan\",\"6\");\n");
  echo("      td.appendChild(CriaCheckBoxAlt(cod));\n");
  echo("      td.appendChild(CriaSpanEspAlt(5));\n");
  echo("      td.appendChild(CriaSpanAlt(cod));\n");
  echo("      td.appendChild(CriaSpanDiv(cod));\n");
  echo("      tr.appendChild(td);\n");
  echo("      tBody.appendChild(tr);\n");
  if($tp_questao == 'O'){
    echo("      AdicionaLinhaArrayGabEPosi(cod);\n");
    //echo("	    document.getElementById('textAlt_'+cod).focus();");
  }
  else if($tp_questao == 'D')
  {
    echo("      trGab = document.createElement(\"tr\");\n");
    echo("      trGab.setAttribute(\"id\",'trAltGab_'+cod);\n");
    echo("      tdText = document.createElement(\"td\");\n");
    echo("      tdText.className = 'itens';\n");
    echo("      tdText.setAttribute(\"colspan\",\"6\");\n");
    echo("      tdText.appendChild(CriaSpanGabarito(codigo));\n");
    echo("      tdOp = document.createElement(\"td\");\n");
    echo("      tdOp.setAttribute(\"valign\",\"top\");\n");
    echo("      tdOp.setAttribute(\"align\",\"left\");\n");
    echo("      tdOp.className = 'botao2';\n");
    echo("      tdOp.appendChild(CriaOpcoes(codigo));\n");
    echo("      trGab.appendChild(tdText);\n");
    echo("      trGab.appendChild(tdOp);\n");
    echo("      tbody.appendChild(trGab);\n");
  }
  echo("      CriaCamposEdicao('',cod);\n");
  echo("      DesabilitarMudancaPosicaoAlt();\n");
  //echo("      IntercalaCorLinhaAlt();\n");
  echo("      cancelarElemento=document.getElementById('spanCanc_'+cod);\n");
  if($tp_questao == 'O')
  	echo("	    document.getElementById('textAlt_'+cod).focus();");
  echo("      qtdAlternativas++;\n");
  echo("    }\n\n");
  
  echo("    function NovaAlternativa()\n");
  echo("    {\n");
  echo("      if(qtdAlternativas < 10)\n");
  echo("      {");
  echo("        CancelaTodos();\n");
  echo("        xajax_CriarAlternativaDinamic(".$cod_curso.",".$cod_usuario.",".$cod_questao.",'".$tp_questao."');\n");
  echo("	  }");
  echo("      else\n");
  //184 - Uma questao pode conter no maximo 10 alternativas.
  echo("        alert('".RetornaFraseDaLista($lista_frases, 183)."');\n");
  echo("    }\n\n");

  echo("    function IntercalaCorLinhaAlt(){\n");
  echo("      var checks,i,corLinha,trAlt;\n");
  echo("      checks = document.getElementsByName('chkAlt');\n");
  echo("      corLinha = 0;\n");
  echo("      for (i=0; i<checks.length; i++){\n");
  echo("        getNumber=checks[i].id.split('_');\n");
  echo("        trAlt = document.getElementById('trAlt_'+getNumber[1]);\n");
  echo("        if(trAlt.style.display != 'none'){\n");
  echo("          trAlt.className = 'altColor'+(corLinha%2);\n");
  echo("          corLinha++;\n");
  echo("        }\n");
  echo("      }\n");
  echo("    }\n\n");

  echo("    function ApagarAlternativa(){\n");
  echo("      var checks,i,j,deleteArray;\n");
  echo("	  j = 0;\n");
  echo("      checks = document.getElementsByName('chkAlt');\n");
  echo("	  deleteArray = new Array();\n");
  /* Frase #90 - Voce realmente deseja apagar o(s) item(s) selecionado(s)? */
  echo("      if (confirm('".RetornaFraseDaLista($lista_frases, 33)."')){\n");
  echo("        for (i=0; i<checks.length; i++){\n");
  echo("          if(checks[i].checked){\n");
  echo("            getNumber=checks[i].id.split('_');\n");
  echo("            deleteArray[j++] = getNumber[1];\n");
  if($tp_questao == 'O')
    echo("            AtualizaArrayGabEPosi(getNumber[1]);\n");
  echo("            xajax_ApagarAlternativaDinamic(".$cod_curso.",".$cod_usuario.",".$cod_questao.",getNumber[1],'".$tp_questao."');\n");
  echo("            qtdAlternativas--;\n");
  echo("          }\n");
  echo("        }\n");
  echo("		DeletarLinhasAlternativa(deleteArray,j);\n");
  echo("        xajax_EditarGabaritoQuestaoObjDinamic(".$cod_curso.",".$cod_questao.",FormaGabarito());\n");
  //echo("        IntercalaCorLinhaAlt();\n");
  echo("        VerificaChkBoxAlt(0);\n");
  echo("      }\n");
  echo("    }\n\n");

  echo("    function EditarAlternativa(){\n");
  echo("      var spanAlt,checks,conteudo;\n");
  echo("	  CancelaTodos();\n");
  echo("	  DesabilitarMudancaPosicaoAlt();\n");
  echo("      checks = document.getElementsByName('chkAlt');\n");
  echo("      for (i=0; i<checks.length; i++){\n");
  echo("        if(checks[i].checked){\n");
  echo("          getNumber=checks[i].id.split('_');\n");
  echo("          spanAlt = document.getElementById('span_'+getNumber[1]);\n");
  echo("          if(spanAlt.firstChild == null || spanAlt.firstChild.innerHTML != ''){\n");
  echo("            conteudo = spanAlt.innerHTML;\n");
  echo("            spanAlt.innerHTML = '';\n");
  echo("			document.getElementById('div_'+getNumber[1]).innerHTML = ' ' ;\n");
  echo("            CriaCamposEdicao(conteudo,getNumber[1]);\n");
  echo("            cancelarElemento=document.getElementById('spanCanc_'+getNumber[1]);\n");
  echo("          }\n");
  echo("        }\n");
  echo("      }\n");
  echo("    }\n\n");
  
  echo("    function DeletarLinhaGabarito(cod){\n");
  echo("      var trAltGab;\n");
  echo("      trAltGab = document.getElementById('trAltGab_'+cod);\n");
  echo("      trAltGab.parentNode.removeChild(trAltGab);\n");
  echo("    }\n\n");

  echo("    function DeletarLinhasAlternativa(deleteArray,j){\n");
  echo("      var i,trAlt;\n");
  echo("	  for(i=0;i<j;i++)\n");
  echo("      {\n");
  echo("        trAlt = document.getElementById('trAlt_'+deleteArray[i]);\n");
  echo("        trAlt.parentNode.removeChild(trAlt);\n");
  if($tp_questao == 'D')
  	echo("          DeletarLinhaGabarito(deleteArray[i]);\n");
  echo("	  }\n");
  echo("    }\n\n");

  echo("    function RetornaPosiAlternativa(cod){\n");
  echo("      var i;\n");
  echo("      for(i=0;i<qtdAlternativas;i++){\n");
  echo("        if(posiAlt[i] == cod)\n");
  echo("          return i;\n");
  echo("      }\n");
  echo("    }\n\n");
  
  echo("	function RetornaNovaPosiAlternativa(posiAltNova,cod)\n");
  echo("	{");
  echo("      var i;\n");
  echo("      for(i=0;i<qtdAlternativas;i++)\n");
  echo("      {\n");
  echo("        if(posiAltNova[i] == cod)\n");
  echo("		  return i;\n");
  echo("      }\n");
  echo("	}\n\n");

  echo("    function AdicionaLinhaArrayGabEPosi(cod){\n");
  echo("      var i;\n");
  echo("      posiAlt[qtdAlternativas] = cod;\n");
  echo("      gabarito[qtdAlternativas] = 0;\n");
  echo("    }\n\n");

  echo("    function AtualizaArrayGabEPosi(cod){\n");
  echo("      var i,j;\n");
  echo("      j = RetornaPosiAlternativa(cod);\n");
  echo("      for(i=j;i<qtdAlternativas-1;i++){\n");
  echo("        posiAlt[i] = posiAlt[i+1];\n");
  echo("        gabarito[i] = gabarito[i+1];\n");
  echo("      }\n");
  echo("    }\n\n");
    
  echo("	function AtualizaPosicoesGabEPosi(string)\n");
  echo("	{");
  echo("      var i,j,posiAltNova,gabaritoTemp;\n");
  echo("	  posiAltNova = string.split(\",\");\n");
  echo("      gabaritoTemp = new Array(qtdAlternativas);\n");
  echo("      for(i=0;i<qtdAlternativas;i++)\n");
  echo("	  {\n");
  echo("	    j = RetornaNovaPosiAlternativa(posiAltNova,posiAlt[i]);\n");
  echo("	    gabaritoTemp[j] = gabarito[i];\n");
  echo("	  }\n");
  echo("      for(i=0;i<qtdAlternativas;i++)\n");
  echo("	  {\n");
  echo("	    gabarito[i] = gabaritoTemp[i];\n");
  echo("	  }\n");
  echo("      for(i=0;i<qtdAlternativas;i++)\n");
  echo("	  {\n");
  echo("	    posiAlt[i] = posiAltNova[i];\n");
  echo("	  }\n");
  echo("	}\n\n");
  
  echo("    function AtualizarMudancaPosicoes(string){\n");
  echo("      var stringGabarito;\n");
  echo("	  AtualizaPosicoesGabEPosi(string);\n");
  echo("      stringGabarito = FormaGabarito();\n");
  echo("      xajax_EditarGabaritoQuestaoObjDinamic(".$cod_curso.",".$cod_questao.",stringGabarito);\n");
  echo("    }\n\n");

  echo("    function DeletaCamposEdicao(elemento){\n");
  echo("      while (elemento.firstChild) {\n");
  echo("        elemento.removeChild(elemento.firstChild);\n");
  echo("      }\n");
  echo("    }\n\n");

  echo("    function CancelaEdicaoAlternativa(cod,conteudo){\n");
  echo("      var span;\n");
  echo("      span = document.getElementById('span_'+cod);\n");
  echo("      DeletaCamposEdicao(span);\n");
  echo("      span.innerHTML = conteudo;\n");
  echo("	  HabilitarMudancaPosicaoAlt();\n");
  echo("    }\n\n");

  echo("    function FormaGabarito(){\n");
  echo("      var stringGabarito,i;\n");
  echo("      stringGabarito = '';\n");
  echo("      for(i=0;i<qtdAlternativas;i++){\n");
  echo("        stringGabarito = stringGabarito+gabarito[i];\n");
  echo("      }\n");
  echo("      return stringGabarito;\n");
  echo("    }\n\n");
  
  if($tp_questao == 'O')
  {
    echo("    function ConfirmaEdicaoAlternativa(cod){\n");
    echo("      var span,conteudo,posi,stringGabarito;\n");
    echo("      span = document.getElementById('span_'+cod);\n");
    echo("      conteudo = document.getElementById('textAlt_'+cod).value;\n");
    echo("      posi = RetornaPosiAlternativa(cod);\n");
    echo("      gabarito[posi] = document.getElementById('select_'+cod).value;\n");
    echo("      stringGabarito = FormaGabarito();\n");
    echo("      DeletaCamposEdicao(span);\n");
    echo("      span.innerHTML = conteudo;\n");
    echo("      xajax_EditarAlternativaObjDinamic(".$cod_curso.",".$cod_questao.",cod,conteudo,stringGabarito);\n");
    echo("      xajax_AtualizaIconesDinamic(".$cod_questao.",".$cod_curso.");\n");
    echo("		HabilitarMudancaPosicaoAlt();\n");
    echo("	    cancelarElemento = null;\n");
    echo("    }\n\n");
  }
  else
  {
    echo("    function ConfirmaEdicaoAlternativa(cod){\n");
    echo("      var span,conteudo;\n");
    echo("      span = document.getElementById('span_'+cod);\n");
    echo("      conteudo = document.getElementById('textAlt_'+cod).value;\n");
    echo("      DeletaCamposEdicao(span);\n");
    echo("      span.innerHTML = conteudo;\n");
    echo("      xajax_EditarAlternativaDissDinamic(".$cod_curso.",".$cod_questao.",cod,conteudo);\n");
    echo("		HabilitarMudancaPosicaoAlt();\n");
    echo("	    cancelarElemento = null;\n");
    echo("    }\n\n");
  }

  echo("    function AtualizaNivel(nivel)\n");
  echo("    {\n");
  /* Frase #91 - Dificuldade atualizada com sucesso. */
  echo("      xajax_AtualizarNivelDinamic(".$cod_curso.",".$cod_questao.",nivel,'".RetornaFraseDaLista($lista_frases, 91)."');\n");
  echo("    }\n\n");

  echo("    function AtualizaTopico(cod)\n");
  echo("    {\n");
  /* Frase #92 - Topico atualizado com sucesso. */
  echo("      xajax_AtualizarTopicoDinamic(".$cod_curso.",".$cod_questao.",cod,'".RetornaFraseDaLista($lista_frases, 92)."');\n");
  echo("    }\n\n");

  echo("    function NovoTopico(cod)\n");
  echo("    {\n");
  echo("        MostraLayer(lay_novo_topico, 100);\n");
  echo("        document.getElementById(\"nome\").value = '';\n");
  echo("        document.getElementById(\"nome\").focus();\n");
  echo("    }\n\n");

  echo("    function EsconderGabarito(cod)\n");
  echo("    {\n");
  echo("      var tr;\n");
  echo("      tr = document.getElementById('trAltGab_'+cod);\n");
  echo("	  if(tr.style.display == '')\n");
  echo("	  {\n");
  echo("        tr.style.display = 'none';\n");
  echo("	    gabaritosVisiveis--;");
  echo("        if(gabaritosVisiveis == 0)\n");
  echo("		  HabilitarMudancaPosicaoAlt();");
  echo("	  }");
  echo("    }\n\n");

  echo("    function MostrarGabarito(cod)\n");
  echo("    {\n");
  echo("      var tr;\n");
  echo("      tr = document.getElementById('trAltGab_'+cod);\n");
  echo("	  if(tr.style.display != '')\n");
  echo("	  {\n");
  echo("      	tr.style.display = '';\n");
  echo("		gabaritosVisiveis++;\n");
  echo("		if(gabaritosVisiveis == 1)\n");
  echo("		  DesabilitarMudancaPosicaoAlt();");
  echo("	  }\n");
  echo("    }\n\n");

  echo("    function ExibirGabarito(){\n");
  echo("      var checks,i;\n");
  echo("      checks = document.getElementsByName('chkAlt');\n");
  echo("      for (i=0; i<checks.length; i++){\n");
  echo("        if(checks[i].checked){\n");
  echo("          getNumber=checks[i].id.split('_');\n");
  echo("          MostrarGabarito(getNumber[1]);");
  echo("        }\n");
  echo("      }\n");
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
  echo("        xajax_VerificaExistenciaArquivoDinamic(".$cod_curso.",".$cod_questao.",".$cod_usuario.",pastaRaiz+subpasta,nomeArq);\n");
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

  echo("    function ApagarArq(){\n");
  echo("      var i,j,checks,getNumber,nomeArq,arrayIdArq,caminho;\n");
  echo("      checks = document.getElementsByName('chkArq');\n");
  echo("      arrayIdArq = new Array();\n");
  echo("      j = 0;\n");
  echo("      caminho = pastaRaiz + pastaAtual.split(\"Raiz/\")[1];\n");
  /* Frase #172 - Voce deseja excluir os arquivo(s) selecionado(s) ? */
  echo("      if (confirm('".RetornaFraseDaLista($lista_frases, 172)."')){\n");
  //echo("      xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_raiz);\n");
  echo("        for (i=0; i<checks.length; i++){\n");
  echo("          if(checks[i].checked){\n");
  echo("            getNumber=checks[i].id.split(\"_\");\n");
  echo("            nomeArq = document.getElementById(\"nomeArq_\"+getNumber[1]).innerHTML;\n");
  echo("            xajax_ExcluiArquivoDinamic(i,caminho+nomeArq,\"texto\", ".$cod_curso.",".$cod_usuario.",".$cod_questao.");\n");
  echo("            arrayIdArq[j++] = getNumber[1];\n");
  echo("          }\n");
  echo("        }\n");
  echo("        DeletaLinhaArq(arrayIdArq,j);\n");
  echo("        VerificaChkBoxArq();\n");
  echo("      }\n");
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
  echo("      }else{\n");
  echo("        document.getElementById('mArq_apagar').className=\"menuUp\";\n");
  echo("        document.getElementById('mArq_apagar').onclick=function(){ };\n");
  echo("        document.getElementById('mArq_ocultar').className=\"menuUp\";\n");
  echo("        document.getElementById('mArq_ocultar').onclick=function(){ };\n");
  echo("      }\n");
  echo("    }\n\n");
  
  
  echo("    function InsereDiretorioVazio(){\n");
  echo("	  var trRef,tr,td;");
  echo("	  tr = document.createElement(\"tr\");\n");
  echo("      tr.setAttribute(\"id\",\"diretorioVazio\");\n");
  echo("	  td = document.createElement(\"td\");\n");
  echo("	  td.colSpan = \"6\";\n");
  //?
  echo("	  td.appendChild(document.createTextNode('Diretorio esta vazio.'));\n");
  echo("	  tr.appendChild(td);\n");
  echo("      trRef = document.getElementById(\"optArq\");\n");
  echo("	  trRef.parentNode.insertBefore(tr,trRef);\n");
  echo("    }\n\n");
  
  echo("    function DeletaLinhaArq(arrayIdArq,n){\n");
  echo("      var i,tr;\n");
  echo("      for (i=0; i < n; i++){\n");
  echo("        tr = document.getElementById('arq_'+arrayIdArq[i]);\n");
  echo("        tr.parentNode.removeChild(tr);\n");
  echo("        contaArq--;\n");
  echo("      }\n");
  echo("    }\n\n");
  
    echo("    function criaTrArq(id){\n");
  echo("	  var tr;\n");
  echo("	  tr = document.createElement(\"tr\");\n");
  echo("	  tr.setAttribute(\"id\",\"arq_\"+id);\n");
  echo("	  tr.setAttribute(\"name\",pastaAtual);\n");
  echo("      return tr;\n");
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
  echo("      check.onclick = function(){ VerificaChkBoxArq(1); };\n");
  echo("      return check;\n");
  echo("    }\n\n");
    
  echo("    function CriaImgArq(nomeArq)\n");
  echo("    {\n");	
  echo("      var img = document.createElement(\"img\");\n");
  echo("      img.setAttribute(\"border\", \"0\");\n");
  echo("      img.setAttribute(\"src\",RetornaSrcImg(nomeArq));\n");
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
  
 
  echo("    function criaSpanArq(nomeArq,numArq,caminho){\n");
  echo("	  var span;\n");
//  echo("      span.setAttribute(\"numArq\",\"arq_\"+numArq);\n");
  echo("      span.appendChild(CriaCheckBoxArq(numArq));\n");
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
  echo("	  span.innerHTML = \" (oculto)\";\n");
  echo("      return span;\n");
  echo("    }\n\n");
  
  echo("    function CriaTexto(texto){\n");
  echo("	  texto = document.createTextNode(texto);\n");
  echo("      return texto;\n");
  echo("    }\n\n");
  
  echo("    function InsereLinhaArq(nomeArq,tamanho,numArq,caminho,data,tipo,status){\n");
  echo("      var novaTr,trRef,tdChk,spanNome;\n");
  echo("      if(tipo == \"arquivo\"){\n");
  echo("      	spanNome = document.createElement(\"span\");\n");
  echo("      	spanNome.setAttribute(\"id\",\"arq_\"+numArq);\n");
  echo("      	spanNome.appendChild(CriaCheckBoxArq(numArq));\n");
  echo("        spanNome.appendChild(CriaSpanEspAlt(5));\n");
  echo("	  	spanNome.appendChild(CriaImgArq(nomeArq));\n");
  echo("	  	spanNome.appendChild(CriaSpanVisualizarArq(nomeArq,numArq,caminho));\n");
  echo("        spanNome.appendChild(CriaTexto(\" - (\"+tamanho+\" Kb)\"));\n");
  echo("      }else if(status == '1')\n");
  echo("        spanNome.appendChild(criaSpanArqOculto());\n");
  echo("      tdRef = document.getElementById(\"listFiles\");\n");
  echo("      tdRef.appendChild(spanNome);\n");
  echo("      tdRef.appendChild(document.createElement(\"br\"));\n");
  echo("      contaArq++;\n");
  echo("    }\n\n");
  
  echo("    function EncontraArquivoEApaga(nomeArq)\n");
  echo("    {\n");
  echo("	  var i,span;\n");
  echo("      for(i = 0; i <= contaArq; i++)\n");
  echo("      {\n");
  echo("        span = document.getElementById(\"nomeArq_\"+i);\n");
  echo("        if(span != null)\n");
  echo("        {\n");
  echo("          if(span.innerHTML == nomeArq)\n");
  echo("          {\n"); 
  echo("            xajax_ExcluiArquivoDinamic(i,'".$dir_questao_temp['link']."'+nomeArq,".$cod_curso.",".$cod_questao.",".$cod_usuario.", \"texto\");\n");
  echo("          }\n");
  echo("        }\n");
  echo("      }\n");
  echo("    }\n\n");
  
  echo("    function VerificaUpload(nomeArq,flag)\n");
  echo("    {\n");
  echo("      subpasta = pastaAtual.split(\"Raiz/\")[1];\n");
  echo("      document.formFiles.subpasta.value = subpasta;\n");
  echo("	  if((flag == 0))");
  echo("        micoxUpload2('formFiles',0,'Anexando ',function(){},++contaArq,pastaRaiz+subpasta,nomeArq,".$cod_curso.",".$cod_questao.",".$cod_usuario.");\n");
  //185 -Arquivo anexado com sucesso.
  echo("        mostraFeedback('Arquivo anexado com sucesso.',true);\n");
  echo("	  if(flag == 1 && confirm('Arquivo '+nomeArq+' ja existe. Deseja sobrescreve-lo?'))");
  echo("      {");
  echo("		EncontraArquivoEApaga(nomeArq);\n");
  echo("        micoxUpload2('formFiles',0,'Anexando ',function(){},++contaArq,nomeArq,".$cod_curso.",".$cod_questao.",".$cod_usuario.");\n");
  echo("      }");
  echo("    }\n\n");
    
  echo("	function HabilitarMudancaPosicaoAlt()\n");
  echo("	{\n");
  echo("      if(tBody) tableDnD.init(null,tBody,".$existeGabaritoDiss.");\n");
  echo("	}\n\n");
  
  echo("	function DesabilitarMudancaPosicaoAlt()\n");
  echo("	{");
  echo("      if(tableDnD) tableDnD.term();\n");
  echo("	}\n\n");
  
  echo("	function SoltaMouse(ids)\n");
  echo("	{");
  //echo("      IntercalaCorLinhaAlt();\n");
  echo("      xajax_AtualizaPosicoesDasAlternativasDinamic(".$cod_curso.", ".$cod_usuario.", ids, \"".$tp_questao."\");\n");
  echo("	}\n\n");
    
  echo("    function Voltar()\n");
  echo("    {\n");
  echo("      window.location='questoes.php?cod_curso=".$cod_curso."&visualizar=Q';\n");
  echo("    }\n\n");
  
  echo("	function ApagarQuestao()\n");
  echo("	{\n");
  /* Frase #93 - Tem certeza que deseja excluir definitivamente as questoes? */
  echo("	  if(confirm('".RetornaFraseDaLista($lista_frases, 93)."'))\n");
  echo("	    document.location='acoes.php?cod_curso=".$cod_curso."&cod_questao=".$cod_questao."&acao=apagar&lixeira=".$lixeira."';\n");
  echo("	}\n\n");
  
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
  
  echo("    </script>\n\n");
  $objAjax->printJavascript("../xajax_0.2.4/");
  /* fim - JavaScript */
  /*********************************************************/

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  if ($tela_formador)
  {
    $titulo="<span id=\"tit_".$questao['cod_questao']."\">".$questao['titulo']."</span>";
    /* Frase #50 - Renomear */
    $renomear="<span onclick=\"AlteraTitulo('".$questao['cod_questao']."');\" id=\"renomear_".$questao['cod_questao']."\">".RetornaFraseDaLista($lista_frases, 50)."</span>";
		$enunciado="<span id=\"text_".$questao['cod_questao']."\">".$questao['enunciado']."</span>";
		$gabarito="<span id=\"texto_".$questao['cod_questao']."\">".$questao_diss."</span>";
    /* Frase #94 - Editar enunciado */
    $editar="<span onclick=\"AlteraTexto(".$questao['cod_questao'].");\">".RetornaFraseDaLista($lista_frases, 94)."</span>";
    /* Frase #95 - Novo topico */
    $novo_topico="<span onclick=\"NovoTopico(".$questao['cod_questao'].");\">".RetornaFraseDaLista($lista_frases, 95)."</span>";
    /* Frase #96 - Limpar enunciado */
    $limpar="<span onclick=\"LimparTexto(".$questao['cod_questao'].");\">".RetornaFraseDaLista($lista_frases, 96)."</span>";
    /* Frase #97 - Editar gabarito */
    $editar_gabarito="<span onclick=\"AlteraGabarito(".$questao['cod_questao'].");\">".RetornaFraseDaLista($lista_frases, 97)."</span>";
    /* Frase #98 - Limpar gabarito */
	$limpar_gabarito="<span onClick=\"LimparGabarito(".$questao['cod_questao'].");\">".RetornaFraseDaLista($lista_frases, 98)."</span>";

	/* Frase #1 - Exercicios */
	/* Frase #99 - Editar Questao */
	echo("          <h4>".RetornaFraseDaLista($lista_frases, 1)." - ".RetornaFraseDaLista($lista_frases, 99)."</h4>\n");
	
  	/* Frase #5 - Voltar */
  	echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"".RetornaFraseDaLista($lista_frases, 5)."\" /></span><br /><br />\n");

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
  	echo("                  <li><span onclick='Voltar();'>".RetornaFraseDaLista($lista_frases, 5)."</span></li>\n");

    	/* Frase #56 - Historico */
    	echo("              	<li><span onclick=\"window.open('historico_questao.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_questao=".$cod_questao."','Historico','width=600,height=400,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');\">".RetornaFraseDaLista($lista_frases, 56)."</span></li>\n");

  	echo("                </ul>\n");
  	echo("              </td>\n");
  	echo("            </tr>\n");
  	echo("            <tr>\n");
  	echo("              <td valign=\"top\">\n");
	echo("                  <table border=0 width=\"100%\" cellspacing=0 id=\"tabelaInterna\" class=\"tabInterna\">\n");
	echo("                    <tr class=\"head\">\n");
	/* Frase #13 - Titulo */
	echo("                      <td class=\"alLeft\">".RetornaFraseDaLista($lista_frases, 13)."</td>\n");
  /* Frase #61 - Topico */
	echo("                      <td width=\"15%\">".RetornaFraseDaLista($lista_frases, 61)."</td>\n");
  /* Frase #62 - Dificuldade */
	echo("                      <td width=\"15%\">".RetornaFraseDaLista($lista_frases, 62)."</td>\n");
  /* 70 - Opcoes (ger)*/
	echo("                      <td width=\"15%\">" . RetornaFraseDaLista($lista_frases_geral, 70) . "</td>\n");
	/* Frase #57 - Compartilhamento */
	echo("                      <td width=\"15%\">".RetornaFraseDaLista($lista_frases, 57)."</td>\n");
	echo("                    </tr>\n");
	echo("                    <tr id='tr_".$questao['cod_questao']."'>\n");
	echo("                      <td class=\"itens\">".$titulo."</td>\n");
        echo("                      <td>\n");
        echo("                        <select id=\"selectTopico\" class=\"input\" onChange=\"AtualizaTopico(this.value);\">");
        /* Frase #173 - Escolha um topico */
        echo("                          <option value=\"0\" ".$texto.">".RetornaFraseDaLista($lista_frases, 173)."</option>\n");
        if ((count($topicos)>0)&&($topicos != null))
        {
          foreach ($topicos as $cod => $linha_item)
          {
            $topico = $linha_item['topico'];
            $cod_topico = $linha_item['cod_topico'];
            if($cod_topico == $questao['cod_topico'])
              $texto = "selected";
            else
              $texto = "";
              
            echo("                          <option value=\"".$cod_topico."\" ".$texto.">".$topico."</option>\n");
          }
        }
        echo("                        </select>\n");
        echo("                      </td>\n");
        echo("                      <td>\n");
        if($questao['nivel'] == 'D') $dificil = "checked='true'";
        else $dificil = "";
        if($questao['nivel'] == 'M') $medio = "checked='true'";
        else $medio = "";
        if($questao['nivel'] == 'F') $facil = "checked='true'";
        else $facil = "";

        /* Frase #100 - Dificil */
        echo("                        <input type=\"radio\" name=\"nivel\" onClick=\"AtualizaNivel('D');\" ".$dificil." />".RetornaFraseDaLista($lista_frases, 100)."<br />\n");
        /* Frase #101 - Medio */
        echo("                        <input type=\"radio\" name=\"nivel\" onClick=\"AtualizaNivel('M');\" ".$medio." />".RetornaFraseDaLista($lista_frases, 101)."<br />\n");
        /* Frase #102 - Facil */
        echo("                        <input type=\"radio\" name=\"nivel\" onClick=\"AtualizaNivel('F');\" ".$facil." />".RetornaFraseDaLista($lista_frases, 102)."<br />\n");
        echo("                      </td>\n");
	echo("                      <td align=\"left\" valign=\"top\" class=\"botao2\">\n");
	echo("                        <ul>\n");
	echo("                          <li>".$renomear."</li>\n");
	echo("                          <li>".$editar."</li>\n");
	echo("                          <li>".$limpar."</li>\n");
	
	if($tp_questao == 'D')
    {
	  echo("                          <li>".$editar_gabarito."</li>\n");
	  echo("                          <li>".$limpar_gabarito."</li>\n");
    }
    echo("                          <li>".$novo_topico."</li>\n");
	// G 1 - Apagar
	echo("                          <li><span onclick=\"ApagarQuestao();\">" . RetornaFraseDaLista($lista_frases_geral, 1) . "</span></li>\n");
	echo("                        </ul>\n");
	echo("                      </td>\n");

		/* Frase #6 - Compartilhado com Formadores */
    if($questao['tipo_compartilhamento'] == "F")
      $compartilhamento = RetornaFraseDaLista($lista_frases, 6);
    /* Frase #8 - Nao compartilhado */
    else
      $compartilhamento = RetornaFraseDaLista($lista_frases, 8);
      
    if($cod_usuario == $questao['cod_usuario'])
      $compartilhamento = "<span id=\"comp_".$cod_questao."\" class=\"link\" onclick=\"js_cod_item='".$cod_questao."';AtualizaComp('".$questao['tipo_compartilhamento']."');MostraLayer(cod_comp,140,event);return(false);\">".$compartilhamento."</span>";
      
    echo("					    <td>".$compartilhamento."</td>");      
      
    echo("                    </tr>\n");
	
	echo("                    <tr class=\"head\">\n");
	/* Frase #17 - Enunciado */
	echo("                      <td class=\"center\" colspan=\"6\">".RetornaFraseDaLista($lista_frases, 17)."</td>\n");
	echo("                    </tr>\n");
	echo("                    <tr>\n");
	echo("                      <td class=\"itens\" colspan=\"6\">\n");
	echo("                        <div class=\"divRichText\">\n");
	echo ("                        ".$enunciado."\n");
	echo("                        </div>\n");
	echo("                      </td>\n");
	echo("                    </tr>\n");
	
	if($tp_questao == "D")
	{
	  /* Frase #103 - Gabarito */
	  echo("                    <tr class=\"head\">\n");
	  echo("                      <td class=\"center\" colspan=\"6\">".RetornaFraseDaLista($lista_frases, 103)."</td>\n");
	  echo("                    </tr>\n");
	  echo("                      <td class=\"itens\" colspan=\"6\">\n");
      echo("                        <div class=\"divRichText\">\n");
	  echo ("                        ".$gabarito."\n");
	  echo("                        </div>\n");
	  echo("                      </td>\n");
	  echo("                    </tr>\n");
	}

	if($tp_questao == "O")
	{
	  echo("                  <tr class=\"head\">\n");
	  /* Frase #18 - Alternativas */
	  echo("                    <td class=\"center\" colspan=\"6\">".RetornaFraseDaLista($lista_frases, 18)."</td>\n");
	  echo("                  </tr>\n");
	  echo("					<tBody id=\"tBody\">");

      if ((count($alternativas)>0)&&($alternativas != null))
      {
        foreach ($alternativas as $cod => $linha_item)
        {
          $texto = $linha_item['texto'];
          $cod_alternativa = $linha_item['cod_alternativa'];

          echo("                  <tr id=\"trAlt_".$linha_item['cod_alternativa']."\">\n");
          echo("                    <td class=\"itens\" colspan=\"6\"><input type=\"checkbox\" name=\"chkAlt\" id=\"alt_".$linha_item['cod_alternativa']."\" onclick=\"VerificaChkBoxAlt(1);\" value=\"".$linha_item['cod_alternativa']."\" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id=\"span_".$linha_item['cod_alternativa']."\">".$texto."</span><span id=\"div_".$linha_item['cod_alternativa']."\">&nbsp;</span></td>\n");
          echo("                  </tr>\n");

          if($tp_questao == 'D')
          {
  
            $gabarito = RetornaGabaritoQuestaoDiss($sock,$cod_questao,$cod_alternativa);

            echo("                  <tr id=\"trAltGab_".$cod_alternativa."\" style=\"display:none;\">\n");
            echo("                    <td class=\"itens\" valign=\"top\" colspan=\"6\"><span id=\"text_".$cod_questao.$cod_alternativa."\">".$gabarito."</span></td>\n");
            echo("                    <td align=\"left\" valign=\"top\" class=\"botao2\">\n");
	      	echo("                      <ul>\n");
	        echo("                        <li><span onclick=\"AlteraTexto(".$cod_questao.$cod_alternativa.");DesabilitarMudancaPosicaoAlt();\">Editar gabarito</span></li>\n");
	        echo("                        <li><span onclick=\"LimparTexto(".$cod_questao.$cod_alternativa.");\">Limpar gabarito</span></li>\n");
	        echo("                        <li><span onclick=\"EsconderGabarito(".$cod_alternativa.");\">Esconder</span></li>\n");
	        echo("                      </ul>\n");
	        echo("                    </td>\n");
            echo("                  </tr>\n");
          }
        }
      }
        
      echo("					</tBody>");
      
      echo("                  <tr id=\"optAlt\">\n");
	  echo("                    <td align=\"left\" colspan=\"6\">\n");
	  echo("                      <ul>\n");
	  echo("                        <li class=\"checkMenu\"><span><input type=\"checkbox\" id=\"checkMenuAlt\" onclick=\"CheckTodos(2);\" /></span></li>\n");
	  /* Frase #71 - Apagar */
	  echo("                        <li class=\"menuUp\" id=\"mAlt_apagar\"><span id=\"sAlt_apagar\">".RetornaFraseDaLista($lista_frases, 71)."</span></li>\n");
	  /* Frase #104 - Editar */
	  echo("                        <li class=\"menuUp\" id=\"mAlt_editar\"><span id=\"sAlt_editar\">".RetornaFraseDaLista($lista_frases, 104)."</span></li>\n");
      /*if($tp_questao == 'D')
        echo("                        <li class=\"menuUp\" id=\"mAlt_gabarito\"><span id=\"sAlt_gabarito\">Exibir gabarito</span></li>\n"); */
	  echo("                      </ul>\n");
	  echo("                    </td>\n");
	  echo("                  </tr>\n");
      echo("                  <tr id=\"trAddAlt\">\n");
  	  echo("                    <td align=\"left\" colspan=\"6\">\n");
    /* Frase #105 - Adicionar Alternativa */
	  echo("                      <div id=\"divAddAlt\"><span class=\"link\" id=\"insertAlt\" onclick=\"NovaAlternativa();\">(+) ".RetornaFraseDaLista($lista_frases, 105)."</span></div>\n");
      echo("                    </td>\n");
	  echo("                  </tr>\n");
	}
    echo("                  <tr class=\"head\">\n");
	/* Frase #12 - Arquivos */
	echo("                    <td colspan=\"6\">".RetornaFraseDaLista($lista_frases, 12)."</td>\n");
	echo("                  </tr>\n");
	echo ("                  <tr>\n");
	echo ("                    <td class=\"itens\" colspan=\"6\" id=\"listFiles\">\n");
	
	if (count($lista_arq) > 0 || $lista_arq != null) {

		$conta_arq = 0;
		// Procuramos na lista de arquivos se existe algum visivel
		$ha_visiveis = false;

		while ((list ($cod, $linha) = each($lista_arq)) && !$ha_visiveis) {
			if ($linha['Arquivo'] != "")
				$ha_visiveis = !($linha['Status']);
		}

		if ($ha_visiveis) {
			$nivel_anterior = 0;
			$nivel = -1;

			foreach ($lista_arq as $cod => $linha) {
				$linha['Arquivo'] = mb_convert_encoding($linha['Arquivo'], "ISO-8859-1", "UTF-8");
				if (!($linha['Arquivo'] == "" && $linha['Diretorio'] == ""))
					if (!$linha['Status']) {
						$nivel_anterior = $nivel;
						$espacos = "";
						$espacos2 = "";
						$temp = explode("/", $linha['Diretorio']);
						$nivel = count($temp) - 1;
						for ($c = 0; $c <= $nivel; $c++) {
							$espacos .= "&nbsp;&nbsp;&nbsp;&nbsp;";
							$espacos2 .= "  ";
						}

						$caminho_arquivo = $dir_questao_temp['link'] . ConverteUrl2Html($linha['Diretorio'] . "/" . $linha['Arquivo']);
						//converte o o caminho e o nome do arquivo que vêm do linux em UTF-8 para 
						//ISO-8859-1 para ser exibido corretamente na página.
						$caminho_arquivo = mb_convert_encoding($caminho_arquivo, "ISO-8859-1", "UTF-8");
						$linha['Arquivo'] = mb_convert_encoding($linha['Arquivo'], "ISO-8859-1", "UTF-8");
						if ($linha['Arquivo'] != "") {

							if ($linha['Diretorio'] != "") {
								$espacos .= "&nbsp;&nbsp;&nbsp;&nbsp;";
								$espacos2 .= "  ";
							}

							if ($linha['Status'])
								$arqOculto = "arqOculto='sim'";
							else
								$arqOculto = "arqOculto='nao'";

							if (eregi(".zip$", $linha['Arquivo'])) {
								// arquivo zip
								$imagem = "<img alt=\"\" src=\"../imgs/arqzip.gif\" border=\"0\" />";
								$tag_abre = "<span class=\"link\" id=\"nomeArq_" . $conta_arq . "\" onclick=\"WindowOpenVer('" . $caminho_arquivo . "');\" tipoArq=\"zip\" nomeArq=\"" . htmlentities($caminho_arquivo) . "\" arqZip=\"" . $linha['Arquivo'] . "\" " . $arqOculto . ">";
							} else {
								// arquivo comum
                                                                // imagem
								if((eregi(".jpg$",$linha['Arquivo'])) || eregi(".png$",$linha['Arquivo']) || eregi(".gif$",$linha['Arquivo']) || eregi(".jpeg$",$linha['Arquivo'])) {
                                                                  $imagem    = "<img alt=\"\" src=\"../imgs/arqimg.gif\" border=\"0\" />";
                                                                //doc
                                                                }else if(eregi(".doc$",$linha['Arquivo'])){
                                                                  $imagem    = "<img alt=\"\" src=\"../imgs/arqdoc.gif\" \"border=\"0\" />";
                                                                //pdf
                                                                }else if(eregi(".pdf$",$linha['Arquivo'])){
                                                                  $imagem    = "<img alt=\"\" src=\"../imgs/arqpdf.gif\" border=\"0\" />";
                                                                //html
                                                                }else if((eregi(".html$",$linha['Arquivo'])) || (eregi(".htm$",$linha['Arquivo']))){
                                                                  $imagem    = "<img alt=\"\" src=\"../imgs/arqhtml.gif\" border=\"0\" />";
                                                                }else if((eregi(".mp3$",$linha['Arquivo'])) || (eregi(".mid$",$linha['Arquivo']))) {
                                                                  $imagem    = "<img alt=\"\" src=\"../imgs/arqsnd.gif\" border=\"0\" />";
                                                                }else{
                                                                  $imagem    = "<img alt=\"\" src=\"../imgs/arqp.gif\" border=\"0\" />";
                                                                }

								$tag_abre = "<span class=\"link\" id=\"nomeArq_" . $conta_arq . "\" onclick=\"WindowOpenVer('" . $caminho_arquivo . "');\" tipoArq=\"comum\" nomeArq=\"" . htmlentities($caminho_arquivo) . "\" " . $arqOculto . ">";

							}

							$tag_fecha = "</span>";

							echo ("                        " . $espacos2 . "<span id=\"arq_" . $conta_arq . "\">\n");

							echo ("                          " . $espacos2 . "<input type=\"checkbox\" name=\"chkArq\" onclick=\"VerificaChkBoxArq(1);\" id=\"chkArq_" . $conta_arq . "\"/>\n");

							echo ("                          " . $espacos2 . $espacos . $imagem . $tag_abre . $linha['Arquivo'] . $tag_fecha . " - (" . round(($linha['Tamanho'] / 1024), 2) . "Kb)");

							echo ("<span id=\"local_oculto_" . $conta_arq . "\">");
							if ($linha['Status'])
								/* Frase #70 - Oculto */
								echo ("<span id=\"arq_oculto_" . $conta_arq . "\"> - <span style=\"color:red;\">".RetornaFraseDaLista($lista_frases, 70)."</span></span>");
							echo ("</span>\n");
							echo ("                          " . $espacos2 . "<br />\n");
							echo ("                        " . $espacos2 . "</span>\n");
						} else {
							if ($nivel_anterior >= $nivel) {
								$i = $nivel_anterior - $nivel;
								$j = $i;
								$espacos3 = "";
								do {
									$espacos3 .= "  ";
									$j--;
								} while ($j >= 0);
								do {
									echo ("                      " . $espacos3 . "</span>\n");
									$i--;
								} while ($i >= 0);
							}
							// pasta
							$imagem = "<img alt=\"\" src=\"../imgs/pasta.gif\" border=\"0\" />";
							echo ("                      " . $espacos2 . "<span id=\"arq_" . $conta_arq . "\">\n");
							echo ("                        " . $espacos2 . "<span class=\"link\" id=\"nomeArq_" . $conta_arq . "\" tipoArq=\"pasta\" nomeArq=\"" . htmlentities($caminho_arquivo) . "\"></span>\n");
							echo ("                        " . $espacos2 . "<input type=\"checkbox\" name=\"chkArq\" onclick=\"VerificaChkBoxArq(1);\" id=\"chkArq_" . $conta_arq . "\">\n");
							echo ("                        " . $espacos2 . $espacos . $imagem . $temp[$nivel] . "\n");
							echo ("                        " . $espacos2 . "<br />\n");
						}
					}
				$conta_arq++;
			}
			do {
				$j = $nivel;
				$espacos3 = "";
				do {
					$espacos3 .= "  ";
					$j--;
				} while ($j >= 0);
				$nivel--;
			}
			while ($nivel >= 0);
		}
	}
	echo ("                    </td>\n");
	echo ("                  </tr>\n");
  echo("                  <tr id=\"optArq\">\n");
	echo("                    <td align=\"left\" colspan=\"6\">\n");
	echo("                      <ul>\n");
	echo("                        <li class=\"checkMenu\"><span><input type=\"checkbox\" id=\"checkMenuArq\" onclick=\"CheckTodos(1);\" /></span></li>\n");
	/* Frase #71 - Apagar */
	echo("                        <li class=\"menuUp\" id=\"mArq_apagar\"><span id=\"sArq_apagar\">".RetornaFraseDaLista($lista_frases, 71)."</span></li>\n");
	/* Frase #73 - Ocultar */
  echo("                        <li class=\"menuUp\" id=\"mArq_ocultar\"><span id=\"sArq_ocultar\">".RetornaFraseDaLista($lista_frases, 73)."</span></li>\n");
	echo("                      </ul>\n");
	echo("                    </td>\n");
	echo("                  </tr>\n");
	echo("                  <tr>\n");
	echo("                    <td align=\"left\" colspan=\"6\">\n");
	echo("                      <form name=\"formFiles\" id=\"formFiles\" enctype=\"multipart/form-data\" method=\"post\" action=\"acoes.php\">\n");
	echo("                        <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
	echo("                        <input type=\"hidden\" name=\"cod_questao\" value=\"".$cod_questao."\" />\n");
	echo("                        <input type=\"hidden\" name=\"pasta\" value=\"questao\" />\n");
    echo("                        <input type=\"hidden\" name=\"acao\" value=\"anexar\" />\n");
    echo("                        <input type=\"hidden\" name=\"subpasta\" value=\"\" />\n");
    
	echo("                        <div id=\"divArquivoEdit\" class=\"divHidden\">\n");
	echo("                          <img alt=\"\" src=\"../imgs/paperclip.gif\" border=\"0\" />\n");
	echo("                          <span class=\"destaque\">" . RetornaFraseDaLista($lista_frases_geral, 26) . "</span>\n");
    // Adicionar Descricao
	echo("                          <span> - Adicionar Descricao</span>\n");
	echo("                          <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n");
//	echo("                          <input type=\"file\" id=\"input_files\" name=\"input_files\" class=\"input\">\n");
    echo("                          <input class=\"input\" type=\"file\" id=\"input_files\" name=\"input_files\" onchange=\"EdicaoArq(1);\" style=\"border:2px solid #9bc\" />\n");
//	echo("                          &nbsp;&nbsp;\n");
//	echo("                          <span onclick=\"EdicaoArq(1);\" id=\"OKFile\" class=\"link\">" . RetornaFraseDaLista($lista_frases_geral, 18) . "</span>\n");
//	echo("                          &nbsp;&nbsp;\n");
//	echo("                          <span onclick=\"EdicaoArq(0);\" id=\"cancFile\" class=\"link\">" . RetornaFraseDaLista($lista_frases_geral, 2) . "</span>\n");
	echo("                        </div>\n");
    echo("                        <div id=\"divAnexando\" class=\"divHidden\"></div>");
	/* 26 - Anexar arquivos (ger) */
	echo("                        <div id=\"divArquivo\"><img alt=\"\" src=\"../imgs/paperclip.gif\" border=\"0\" /> <span class=\"link\" id =\"insertFile\" onclick=\"AcrescentarBarraFile(1);\">" . RetornaFraseDaLista($lista_frases_geral, 26) . "</span></div>\n");
	echo("                      </form>\n");
	echo("                    </td>\n");
	echo("                  </tr>\n");
	echo("                </table>\n");
	echo("              </td>\n");
  	echo("            </tr>\n");
  	echo("          </table>\n");
        echo("          <span class=\"btsNavBottom\"><a href=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></a> <a href=\"#topo\"><img src=\"../imgs/btTopo.gif\" border=\"0\" alt=\"Topo\" /></a></span>\n");
  //*NAO �FORMADOR*/
  }
  else
  {
		/* Frase #1 - Exercicios */
  	/* Frase #74 - Area restrita ao formador. */
  	echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases,74)."</h4>\n");
	
        /* Frase #5 - Voltar */
        echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"".RetornaFraseDaLista($lista_frases, 5)."\" /></span><br /><br />\n");

        echo("          <div id=\"mudarFonte\">\n");
        echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
        echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
        echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
        echo("          </div>\n");

    	/* Frase #5 - Voltar */
    	echo("<form><input class=\"input\" type=button value=\"".RetornaFraseDaLista($lista_frases,5)."\" onclick=\"history.go(-1);\" /></form>\n");
  }

  echo("        </td>\n");
  echo("      </tr>\n"); 

  include("../tela2.php");

  /* Novo Topico */
  echo("    <div class=popup id=\"layer_novo_topico\">\n");
  echo("      <div class=\"posX\"><span onclick=\"EscondeLayer(lay_novo_topico);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("        <div class=int_popup>\n");
  echo("          <div class=ulPopup>\n");    
  /* Frase #106 - Nome do topico */
  echo("            ".RetornaFraseDaLista($lista_frases, 106).":<br />\n");
  echo("            <input class=\"input\" type=\"text\" name=\"novo_topico\" id=\"nome\" value=\"\" maxlength=150 /><br />\n");
  /* 18 - Ok (gen) */
  echo("            <input type=\"button\" id=\"ok_novotopico\" class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" onClick=\"VerificaNovoTopico(document.getElementById('nome'));\"/>\n");
  /* 2 - Cancelar (gen) */
  echo("            &nbsp; &nbsp; <input type=\"button\" class=\"input\"  onClick=\"EscondeLayer(lay_novo_topico);\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");
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
  echo("            <li onClick=\"document.getElementById('tipo_comp').value='F'; xajax_MudarCompartilhamentoDinamic(xajax.getFormValues('form_comp'), '".RetornaFraseDaLista($lista_frases, 6)."','Q'); EscondeLayers();\">\n");
  echo("              <span id=\"tipo_comp_F\" class=\"check\"></span>\n");
  /* Frase #6 - Compartilhado com formadores */
  echo("              <span>Compartilhado com formadores</span>\n");
  echo("            </li>\n");
  echo("            <li onClick=\"document.getElementById('tipo_comp').value='N'; xajax_MudarCompartilhamentoDinamic(xajax.getFormValues('form_comp'), '".RetornaFraseDaLista($lista_frases, 8)."', 'Q'); EscondeLayers();\">\n");
  echo("              <span id=\"tipo_comp_N\" class=\"check\"></span>\n");
  /* Frase #8 - Nao Compartilhado */
  echo("              <span>".RetornaFraseDaLista($lista_frases, 8)."</span>\n");
  echo("            </li>\n");
  echo("          </ul>\n");    
  echo("        </form>\n");
  echo("      </div>\n");
  echo("    </div>\n");
  
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);
?>