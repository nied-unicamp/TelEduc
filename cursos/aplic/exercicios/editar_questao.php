<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/exercicios/editar_questao.php

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
  ARQUIVO : cursos/aplic/exercicios/editar_questao.php
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
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->register(XAJAX_FUNCTION,"DecodificaString");
  $objAjax->register(XAJAX_FUNCTION,"RetornaFraseDinamic");
  $objAjax->register(XAJAX_FUNCTION,"RetornaFraseGeralDinamic");
  $objAjax->register(XAJAX_FUNCTION,"EditarAlternativaObjDinamic");
  $objAjax->register(XAJAX_FUNCTION,"EditarAlternativaMultDinamic");
  $objAjax->register(XAJAX_FUNCTION,"EditarAlternativaDissDinamic");
  $objAjax->register(XAJAX_FUNCTION,"CriarAlternativaDinamic");
  $objAjax->register(XAJAX_FUNCTION,"ApagarAlternativaDinamic");
  $objAjax->register(XAJAX_FUNCTION,"AtualizarNivelDinamic");
  $objAjax->register(XAJAX_FUNCTION,"AtualizarTopicoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"CriaNovoTopicoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"EditarTituloQuestaoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"EditarEnunciadoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"EditarGabaritoQuestaoDissDinamic");
  $objAjax->register(XAJAX_FUNCTION,"EditarGabaritoQuestaoMultDinamic");
  $objAjax->register(XAJAX_FUNCTION,"EditarGabaritoQuestaoObjDinamic");
  $objAjax->register(XAJAX_FUNCTION,"ExcluiArquivoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"ExibeArquivoAnexadoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"VerificaExistenciaArquivoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"AtualizaPosicoesDasAlternativasDinamic");
  $objAjax->register(XAJAX_FUNCTION,"MudarCompartilhamentoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"AtualizaIconesDinamic");
  $objAjax->register(XAJAX_FUNCTION,"MudaTipoQuestaoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"OcultarArquivosDinamic");
  $objAjax->register(XAJAX_FUNCTION,"DesocultarArquivosDinamic");
  // Registra funções para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  $cod_ferramenta = 23;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=3;
  // Descobre os diretorios de arquivo, para os portfolios com anexo
  $sock = Conectar("");
  $diretorio_arquivos = RetornaDiretorio($sock, 'Arquivos');
  $diretorio_temp = RetornaDiretorio($sock, 'ArquivosWeb');
  Desconectar($sock);

  include("../topo_tela.php");

  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject = new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro Ã© a aÃ§Ã£o, o segundo Ã© o nÃºmero da frase para ser impressa se for "true", o terceiro caso "false"
  /*Frase #185 - Arquivo anexado com sucesso */
  $feedbackObject->addAction("anexar", RetornaFraseDaLista($lista_frases, 185), 0);
  /* Frase #210 - Arquivo descompactado com sucesso */
  $feedbackObject->addAction("descompactar", RetornaFraseDaLista($lista_frases, 210), 0);
  /*Frase #204: Questao criada com sucesso!*/
  $feedbackObject->addAction("criarQuestao", RetornaFraseDaLista($lista_frases, 204), 0);

  /* Se a questao esta em um exercicio que ja foi aplicado, volta para a pagina de questoes e exibe feedback. */
  $aplicada = QuestaoAplicada($sock, $cod_questao);

  $questao = RetornaQuestao($sock,$cod_questao);
  $questao_diss = RetornaQuestaoDiss($sock,$cod_questao);
  $alternativas = RetornaAlternativas($sock,$cod_questao);
  $topicos = RetornaTopicos($sock);
  $dir_questao_temp = CriaLinkVisualizar($sock, $cod_curso, $cod_usuario, $cod_questao, $diretorio_arquivos, $diretorio_temp, "questao");
  $tp_questao = $questao['tp_questao'];
  $lista_arq = RetornaArquivosQuestao($cod_curso, $dir_questao_temp['link']);
  $num_arq_vis = RetornaNumArquivosVisiveis($lista_arq);

  if($tp_questao == 'O') {
    $gabaritoObj = RetornaGabaritoQuestaoObj($sock, $cod_questao);
    $existeGabaritoDiss = 0;
  } elseif ($tp_questao == 'M') {
    $gabaritoObj = RetornaGabaritoQuestaoMult($sock, $cod_questao);
    $existeGabaritoDiss = 0;
  } else {
    $gabaritoObj = null;
    $existeGabaritoDiss = 1;
  }

  if($tp_questao == 'O')
    $tipo_tit = RetornaFraseDaLista($lista_frases, 159);
  elseif($tp_questao == 'D')
    $tipo_tit = RetornaFraseDaLista($lista_frases, 160);
  else
    $tipo_tit=RetornaFraseDaLista($lista_frases, 212);



  /*********************************************************/
  /* inicio - JavaScript */
  echo("  <script type=\"text/javascript\" language=\"javascript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
  echo("  <script type=\"text/javascript\" language=\"javascript\" src='../js-css/tablednd.js'></script>\n");
  echo("  <script type=\"text/javascript\" src=\"../bibliotecas/ckeditor/ckeditor.js\"></script>");
  echo("  <script type=\"text/javascript\" src=\"../bibliotecas/ckeditor/ckeditor_biblioteca.js\"></script>");
  echo("  <script type=\"text/javascript\" language=\"javascript\" src=\"micoxUpload2.js\"></script>\n");

  echo("  <script  type=\"text/javascript\" language=\"javascript\">\n\n");

  if($tp_questao == 'O' || $tp_questao == 'M'){
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
  echo("    var tBody_alternativas;\n");
  echo("    var pastaRaiz = \"".$dir_questao_temp['link']."\";");
  echo("    var pastaAtual = \"Raiz/\";\n");
  echo("    var tableDnD;\n");
  echo("    var cod_comp;\n");
  echo("    var pastaRaiz = \"".$dir_questao_temp['link']."\";");
  echo("    var pastaAtual = \"Raiz/\";\n");
  echo("    var js_comp = new Array();\n");
  echo("    var cancelarTodos = 0;\n");
  echo("    var lay_novo_topico;");
  echo("    var conteudo;\n\n");
  /* (ger) 18 - Ok */
  // Texto do botão Ok do ckEditor
  echo("    var textoOk = '".RetornaFraseDaLista($lista_frases_geral, 18)."';\n\n");
  /* (ger) 2 - Cancelar */
  // Texto do botão Cancelar do ckEditor
  echo("    var textoCancelar = '".RetornaFraseDaLista($lista_frases_geral, 2)."';\n\n");

  if ( ($tp_questao == 'O' || $tp_questao == 'M') && (count($alternativas)>0) && ($alternativas != null))
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

  //echo("    var num = 0;\n");
  echo("    var resposta=\"\";\n");

  $icone_correto = " <img src=\"../imgs/certo.png\" name=\"correta[ ]\" alt=\"resposta certa\" border=\"0\" /> ";
  $icone_errado = " <img  src=\"../imgs/errado.png\" alt=\"resposta errada\" border=\"0\" /> ";

  //funcao que carrega o numero de questÃµes corretas
  echo("    function CarregaNumCerta(){\n");
  echo("      var Alt=\"\";\n");
  echo("      var divCorreta=\"\";\n");
  echo("      var Resp=\"\";\n");
  echo("      Alt=document.getElementsByName('Alt[ ]');\n");
  echo("      for(var i=0;i<Alt.length;i++){\n");
  echo("        div=Alt[i].id.split('_');\n");
  echo("        divCorreta=document.getElementById('div_'+div[1]);\n");
  echo("        Resp=divCorreta.childNodes[1].alt;\n");
  echo("        if(Resp=='resposta correta')\n");
  echo("          num++;\n");
  echo("      }\n");
  echo("    }\n");

  echo("    function NumCerta(option_value){\n");
  echo("      if(option_value==1) {\n"); /* Se a opcao for outra alternativa correta */
  echo("        var num=0;\n");
  echo("        var numcorreta=document.getElementsByName('correta[ ]');\n");
  echo("        for(var i=1;i<=numcorreta.length;i++){\n");
  echo("          num++;");
  echo("        }\n");
  echo("        if(num>=1){\n");
  echo("          tr=document.getElementsByName('Alt[ ]');\n");
  //frase 207 Ja existe uma alternativa correta. Deseja continuar?
  echo("          if(!confirm('".RetornaFraseDaLista($lista_frases, 207)."')){\n");
  echo("            CancelaAlternativaNovaAlternativa(tr[tr.length -1].id.split('_')[1]);\n");
  echo("          }\n");
  echo("          else{\n");
  echo("            ConfirmaEdicaoAlternativa(tr[tr.length -1].id.split('_')[1]);\n");
  echo("          }\n");
  echo("        }\n");
  echo("      }\n");
  echo("     }\n");

  echo("    function MudaCabecalho(){\n");
  echo("      tituloPrinc=document.getElementById('tituloPrinc');\n");
  echo("      if(num<=1){\n");
  /* 99  - Editar Questao Objetiva */
  /* 159 - Objetiva */
  echo("        tituloPrinc.innerHTML = '".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases, 99)." ".RetornaFraseDaLista($lista_frases, 159)."';\n");
  echo("          xajax_MudaTipoQuestaoDinamic(".$cod_curso.",".$cod_usuario.",".$cod_questao.",'O');\n");
  echo("      }\n");
  echo("      else{\n");
  /* 99  - Editar Questao Objetiva */
  /* 212 - Multipla escolha */
  echo("        tituloPrinc.innerHTML = '".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases, 99)." ".RetornaFraseDaLista($lista_frases, 212)."';\n");
  echo("        xajax_MudaTipoQuestaoDinamic(".$cod_curso.",".$cod_usuario.",".$cod_questao.",'M');\n");
  echo("      }\n");
  echo("    }\n");

  echo("    function AtualizaIcones(alt,gabarito){\n");
  echo("      var arrayAlt = alt.split('.');\n");
  echo("      arrayGabarito = new Array();");
  echo("      arrayGabarito = gabarito.split('');");
  echo("      for(i=0;i<arrayGabarito.length;i++){\n");
  echo("        if(arrayGabarito[i] == 0) {\n");
  echo("          document.getElementById('div_'+arrayAlt[i]).innerHTML = '".$icone_errado."';\n");
  echo("        } else {\n");
  echo("          document.getElementById('div_'+arrayAlt[i]).innerHTML = '".$icone_correto."';\n");
  echo("        }\n");
  echo("      }\n");
  echo("    }\n\n");

  /* Iniciliza os layers. */
  echo("    function Iniciar()\n");
  echo("    {\n");
  echo("      lay_novo_topico = getLayer('layer_novo_topico');\n");
  echo("      cod_comp = getLayer('comp');\n");
  echo("      tableDnD = new TableDnD();\n");
  echo("      tBody_alternativas = document.getElementById('tBody_alternativas');\n");
  if(!$aplicada) { /* Se a questao foi aplicada, nao habilitar mudanca de posicao das alternativas. */
    echo("      HabilitarMudancaPosicaoAlt();\n");
  }
  echo("      startList();\n");
  if($tp_questao == 'O' || $tp_questao == 'M'){
    echo("     xajax_AtualizaIconesDinamic('".$questao['cod_questao']."','".$cod_curso."','".$tp_questao."');\n");
  }

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

  echo("    function MostraLayer(cod_layer,ajusteX,ajusteY)\n");
  echo("    {\n\n");
  echo("      EscondeLayers();\n");
  //echo("if(cod_layer==lay_novo_topico)")
  echo("      moveLayerTo(cod_layer,Xpos-ajusteX,Ypos+AjustePosMenuIE()-ajusteY);\n");
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
  //Cancela EdiÃ§Ã£o
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
  echo("      if (editaTitulo==0){\n");
  echo("        CancelaTodos();\n");
  echo("        id_aux = id;\n");
  //echo("        xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);\n");
  echo("        conteudo = document.getElementById('tit_'+id).innerHTML;\n");
  echo("        document.getElementById('tit_'+id).className='';\n");
  echo("        document.getElementById('tr_'+id).className='';\n");
  //cria o input
  echo("        createInput = document.createElement('input');\n");
  echo("        document.getElementById('tit_'+id).innerHTML='';\n");
  echo("        document.getElementById('tit_'+id).onclick=function(){ };\n");
  echo("        createInput.setAttribute('type', 'text');\n");
  echo("        createInput.setAttribute('style', 'border: 2px solid #9bc');\n");
  echo("        createInput.setAttribute('id', 'tit_'+id+'_text');\n");
  //echo("        createInput.onkeypress = function(event) {EditaTituloEnter(this, event, id_aux);}\n");
  echo("        if (createInput.addEventListener){\n");
  echo("          createInput.addEventListener('keypress', function (event) {EditaTituloEnter(this, event, id_aux);}, false);\n");
  echo("        } else if (createInput.attachEvent){\n");
  echo("          createInput.attachEvent('onkeypress', function (event) {EditaTituloEnter(this, event, id_aux);});\n");
  echo("        }\n");
  echo("        document.getElementById('tit_'+id).appendChild(createInput);\n");
  echo("        xajax_DecodificaString('tit_'+id+'_text', conteudo, 'value');\n");
  //cria o elemento 'espaco' e adiciona na pagina
  echo("        espaco = document.createElement('span');\n");
  echo("        espaco.innerHTML='&nbsp;&nbsp;'\n");
  echo("        document.getElementById('tit_'+id).appendChild(espaco);\n");
  echo("        createSpan = document.createElement('span');\n");
  echo("        createSpan.className='link';\n");
  echo("        createSpan.onclick= function(){ EdicaoTitulo(id, 'tit_'+id, 'ok'); };\n");
  echo("        createSpan.setAttribute('id', 'OkEdita');\n");
  echo("        createSpan.innerHTML='OK';\n");
  echo("        document.getElementById('tit_'+id).appendChild(createSpan);\n");
  //cria o elemento 'espaco' e adiciona na pagina
  echo("        espaco = document.createElement('span');\n");
  echo("        espaco.innerHTML='&nbsp;&nbsp;'\n");
  echo("        document.getElementById('tit_'+id).appendChild(espaco);\n");
  echo("        createSpan = document.createElement('span');\n");
  echo("        createSpan.className='link';\n");
  echo("        createSpan.onclick = function(){ \n");
  echo("          EdicaoTitulo(id, 'tit_'+id, 'canc');\n");
  echo("        };\n");
  echo("        createSpan.setAttribute('id', 'CancelaEdita');\n");
  echo("        createSpan.innerHTML='Cancelar';\n");
  echo("        document.getElementById('tit_'+id).appendChild(createSpan);\n");
  //cria o elemento 'espaco' e adiciona na pagina
  echo("        espaco = document.createElement('span');\n");
  echo("        espaco.innerHTML='&nbsp;&nbsp;'\n");
  echo("        document.getElementById('tit_'+id).appendChild(espaco);\n");
  echo("        startList();\n");
  echo("        cancelarElemento=document.getElementById('CancelaEdita');\n");
  echo("        document.getElementById('tit_'+id+'_text').select();\n");
  echo("        editaTitulo++;\n");
  echo("      }\n");
  echo("    }\n\n");

  echo("    function LimparTexto(id)\n");
  echo("    {\n");
  /* Frase #39 - Voce deseja limpar o texto? O conteudo sera perdido. */
  echo("      if(confirm(\"".RetornaFraseDaLista($lista_frases, 39)."\"))\n");
  echo("      {\n");
  //echo("        xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);\n");
  echo("        document.getElementById('text_'+id).innerHTML='';\n");
  echo("        if(id == ".$cod_questao.")\n");
  //Frase #205: Enunciado excluido com sucesso.
  echo("          xajax_EditarEnunciadoDinamic(".$cod_curso.",".$cod_questao.",'',".$cod_usuario.", \"".RetornaFraseDaLista($lista_frases, 205)."\");\n");
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
  //echo("      if (editaTexto==-1 || editaTexto != id){\n");
  echo("        CancelaTodos();\n");
  //echo("        xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);\n");
  echo("        conteudo = document.getElementById('text_'+id).innerHTML;\n");
  echo("        writeRichTextOnJS('text_'+id+'_text', conteudo, 520, 200, true, false, id);\n");
  echo("        startList();\n");
  //echo("        document.getElementById('text_'+id+'_text').focus();\n");
  echo("        cancelarElemento=document.getElementById('CancelaEdita');\n");
  echo("        editaTexto = id;\n");
  //echo("      }\n");
  echo("    }\n\n");

  echo("    function AlteraGabarito(id){\n");
  //echo("      if (editaTexto==-1 || editaTexto != id){\n");
  echo("        CancelaTodos();\n");
  //echo("        xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);\n");
  echo("        conteudo = document.getElementById('texto_'+id).innerHTML;\n");
  //echo("        eval('conteudo = CKEDITOR.instances.'+id+'_text'+'.getData();');");
  echo("        writeRichTextOnJS_gabarito('texto_'+id+'_text', conteudo, 520, 200, true, false, id);\n");
  echo("        startList();\n");
  //echo("        document.getElementById('texto_'+id+'_text').focus();\n");
  echo("        cancelarElemento=document.getElementById('CancelaEdita');\n");
  echo("        editaTexto = id;\n");
  //echo("      }\n");
  echo("    }\n\n");

  echo("    function RetornaCodAlternativa(codigo)\n");
  echo("    {\n");
  echo("      var cod_questao,cod;\n");
  echo("      cod_questao = \"".$cod_questao."\";\n");
  echo("      codigo = codigo.toString();\n");
  echo("      cod = codigo.substring(cod_questao.length);");
  echo("      return cod;\n");
  echo("    }\n\n");

  echo("    function EdicaoTexto(codigo, id, valor){\n");
  echo("      var cod;\n");
  echo("      if (valor=='ok'){\n");
  //echo("        conteudo=document.getElementById(id+'_text').contentWindow.document.body.innerHTML;\n");
  echo("        eval('conteudo = CKEDITOR.instances.'+id+'_text'+'.getData();');");
  echo("        if(codigo == ".$cod_questao.")\n");
  //frase #200: Enunciado editado com sucesso!
  echo("          xajax_EditarEnunciadoDinamic(".$cod_curso.",".$cod_questao.",conteudo,".$cod_usuario.", \"".RetornaFraseDaLista($lista_frases, 200)."\");\n");
  echo("        else{\n");
  echo("          cod = RetornaCodAlternativa(codigo);");
  echo("          xajax_EditarGabaritoQuestaoDissDinamic(".$cod_curso.",".$cod_questao.",cod,conteudo,".$cod_usuario.", \"\");\n");
  echo("        }\n");
  echo("      }\n");
  echo("      else{\n");
  // Cancela Ediï¿½o
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
  //echo("        conteudo=document.getElementById(id+'_text').contentWindow.document.body.innerHTML;\n");
  echo("        eval('conteudo = CKEDITOR.instances.'+id+'_text'+'.getData();');");
  echo("        cod = RetornaCodAlternativa(codigo);");
  echo("        xajax_EditarGabaritoQuestaoDissDinamic(".$cod_curso.",".$cod_questao.",conteudo);\n");
  echo("      }\n");
  echo("      document.getElementById(id).innerHTML=conteudo;\n");
  echo("      editaTexto=-1;\n");
  echo("      cancelarElemento=null;\n");
  echo("      HabilitarMudancaPosicaoAlt();\n");
  echo("    }\n\n");

  echo("    function VerificaNovoTopico(textbox){\n");
  echo("      var texto = textbox.value;\n");
  echo("      var select;\n");
  echo("      if(texto==''){\n");
  /* Frase #213 - Digite um nome para o tÃ³pico*/
  echo("        alert('".RetornaFraseDaLista($lista_frases, 213)."');\n");
  echo("        textbox.focus();\n");
  echo("        return false;\n");
  echo("      }\n");
  /* Frase #89 - Topico criado com sucesso */
  echo("      xajax_CriaNovoTopicoDinamic(".$cod_curso.",".$cod_questao.",texto,'".RetornaFraseDaLista($lista_frases, 89)."');\n");
  echo("      EscondeLayer(lay_novo_topico);\n");
  echo("    }\n\n");

  echo("    function AtualizaTopicoCriado(cod) {\n");
  echo("      select = document.getElementById('selectTopico');\n");
  echo("      select.selectedIndex = cod; ");
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
  echo("      var containerCheck = document.createElement(\"div\");\n");
  echo("      containerCheck.className = 'containerCheck';\n");
  echo("      containerCheck.appendChild(check);\n");
  echo("      return containerCheck;\n");
  echo("    }\n\n");

  echo("    function CriaTextArea(cod)\n");
  echo("    {\n");
  echo("      var textarea = document.createElement(\"textarea\");\n");
  echo("      textarea.setAttribute(\"id\",'textAlt_'+cod);\n");
  echo("      textarea.setAttribute(\"name\", \"textAlt\");\n");
  echo("      textarea.setAttribute(\"rows\", 3);\n");
  echo("      textarea.className = 'input';\n");
  echo("      var containerTextArea = document.createElement(\"div\");");
  echo("      containerTextArea.setAttribute(\"id\",'containerTextArea_'+cod);\n");
  echo("      containerTextArea.className = 'containerTextArea';\n");
  echo("      containerTextArea.appendChild(textarea);\n");
  echo("      return containerTextArea;\n");
  echo("    }\n\n");

  echo("    function CriaSelectAlt(cod)\n");
  echo("    {\n");
  echo("      var select,opt1,opt2,txt;\n");
  echo("      select = document.createElement(\"select\");\n");
  echo("      select.setAttribute(\"id\",'select_'+cod);\n");
  if($tp_questao=='O') {
    echo("      select.setAttribute(\"onchange\",\"NumCerta(this.value);\");\n");
  }
  //echo("      select.setAttribute(\"class\",\"input\");\n");
  echo("      select.className=\"input\";");
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

  echo("    function CriaDivAlt(cod)\n");
  echo("    {\n");
  echo("      var divAlt = document.createElement(\"div\");\n");
  echo("      divAlt.setAttribute(\"id\",'divAlt_'+cod);\n");
  echo("      divAlt.className = \"divAlt\";\n");
  echo("      return divAlt;\n");
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
  //echo("      span.setAttribute(\"class\",\"link\");\n");
  echo("      span.className=\"link\";");
  echo("      span.innerHTML = 'Ok';\n");
  echo("      span.onclick= function(){ ConfirmaEdicaoAlternativa(cod); };\n");
  echo("      return span;\n");
  echo("    }\n\n");

  echo("    function CriaSpanCanc(cod,conteudo)\n");
  echo("    {\n");
  echo("      var span = document.createElement(\"span\");\n");
  echo("      span.setAttribute(\"id\",'spanCanc_'+cod);\n");
  //echo("      span.setAttribute(\"class\",\"link\");\n");
  echo("      span.className=\"link\";");
  echo("      span.innerHTML = 'Cancelar';\n");
  echo("      span.onclick= function(){ CancelaEdicaoAlternativa(cod, conteudo); };\n");
  echo("      return span;\n");
  echo("    }\n\n");

  echo("    function CriaSpanCancNovaAlternativa(cod)\n");
  echo("    {\n");
  echo("      var span = document.createElement(\"span\");\n");
  echo("      span.setAttribute(\"id\",'spanCanc_'+cod);\n");
  //echo("      span.setAttribute(\"class\",\"link\");\n");
  echo("      span.className=\"link\";");
  echo("      span.innerHTML = 'Cancelar';\n");
  echo("      span.onclick= function(){ CancelaAlternativaNovaAlternativa(cod); };\n");
  echo("      return span;\n");
  echo("    }\n\n");

  echo("    function CriaInputAlt(conteudo,cod)\n");
  echo("    {\n");
  echo("      var inputAlternativa = document.createElement(\"input\");\n");
  echo("      inputAlternativa.setAttribute(\"type\", \"text\");\n");
  echo("      inputAlternativa.setAttribute(\"value\",conteudo);\n");
  //echo("      inputAlternativa.setAttribute(\"class\",\"input\");\n");
  echo("      inputAlternativa.className=\"input\";");
  echo("      inputAlternativa.setAttribute(\"id\",'textAlt_'+cod);\n");
  echo("      inputAlternativa.setAttribute(\"size\", \"46\");\n");
  echo("      inputAlternativa.setAttribute(\"maxlength\", \"255\");\n");
  echo("      return inputAlternativa;\n");
  echo("    }\n\n");

  //funcao que cria campo de edicao ao editar uma alternativa
  echo("    function CriaCamposEdicao(conteudo,cod)\n");
  echo("    {\n");
  echo("      var span;\n");
  echo("      span = document.getElementById('span_'+cod);\n");
  echo("      span.appendChild(CriaInputAlt(conteudo,cod));\n");
  if($tp_questao == 'O' || $tp_questao == 'M')
  {
    echo("      span.appendChild(document.createTextNode(' Validade:'));\n");
    echo("      span.appendChild(CriaSelectAlt(cod));\n");
  }
  echo("      span.appendChild(CriaSpanEspAlt(8));\n");
  echo("      span.appendChild(CriaSpanOk(cod));\n");
  echo("      span.appendChild(CriaSpanEspAlt(2));\n");
  echo("      span.appendChild(CriaSpanCanc(cod,conteudo));\n");
  echo("    }\n\n");


  //funÃ§Ã£o que cria o campo de edicao quando o usuario quer adicionar nova alternativa
  echo("    function CriaCamposEdicaoNovaAlternativa(conteudo,cod)\n");
  echo("    {\n");
  echo("      var divAlt;\n");
  echo("      divAlt = document.getElementById('divAlt_'+cod);\n");
  if($tp_questao == 'O' || $tp_questao == 'M')
  {
    echo("      var selectLabel = document.createElement(\"label\");\n");
    echo("      selectLabel.setAttribute(\"for\", \"select_\"+cod);\n");
    //TODO: Traduzir este label.
    echo("      selectLabel.innerHTML = \"Validade:\";\n");
    echo("      divAlt.appendChild(selectLabel);\n");
    echo("      divAlt.appendChild(CriaSelectAlt(cod));\n");
  }
  echo("      divAlt.appendChild(CriaSpanEspAlt(8));\n");
  echo("      divAlt.appendChild(CriaSpanOk(cod));\n");
  echo("      divAlt.appendChild(CriaSpanEspAlt(2));\n");
  echo("      divAlt.appendChild(CriaSpanCancNovaAlternativa(cod));\n");
  echo("    }\n\n");



  echo("    function CriaSpanEditarGabarito(cod)\n");
  echo("    {\n");
  echo("      var span = document.createElement(\"span\");\n");
  /* 97 - Editar gabarito*/
  echo("      span.innerHTML = '".RetornaFraseDaLista($lista_frases, 97)."';\n");
  echo("      span.onclick= function(){ AlteraTexto(cod);DesabilitarMudancaPosicaoAlt(); };\n");
  echo("      return span;\n");
  echo("    }\n\n");

  echo("    function CriaSpanLimpaGabarito(cod)\n");
  echo("    {\n");
  echo("      var span = document.createElement(\"span\");\n");
  /* 98 - Limpar gabarito */
  echo("      span.innerHTML = '".RetornaFraseDaLista($lista_frases, 98)."';\n");
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
//TODO:
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
  echo("      tr.setAttribute(\"name\",'Alt[ ]')\n");
  echo("      td = document.createElement(\"td\");\n");
  echo("      td.className = 'itens edicao';\n");
  echo("      td.setAttribute(\"colSpan\",\"6\");\n");
  echo("      td.appendChild(CriaCheckBoxAlt(cod));\n");
  echo("      td.appendChild(CriaTextArea(cod));\n");
  echo("      td.appendChild(CriaDivAlt(cod));\n");
  echo("      td.appendChild(CriaSpanDiv(cod));\n");
  echo("      tr.appendChild(td);\n");
  //echo("      tBody_alternativas.appendChild(tr);\n");
  if($tp_questao == 'O' || $tp_questao == 'M'){
    echo("      AdicionaLinhaArrayGabEPosi(cod);\n");
    echo("      tBody_alternativas.appendChild(tr);\n");
    //echo("      document.getElementById('textAlt_'+cod).focus();");
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
    echo("      tBody_alternativas.appendChild(trGab);\n");
  }
  echo("      CriaCamposEdicaoNovaAlternativa('',cod);\n");
  echo("      DesabilitarMudancaPosicaoAlt();\n");
  //echo("      IntercalaCorLinhaAlt();\n");
  echo("      cancelarElemento=document.getElementById('spanCanc_'+cod);\n");
  if($tp_questao == 'O' || $tp_questao == 'M')
    echo("      document.getElementById('textAlt_'+cod).focus();");
  echo("      qtdAlternativas++;\n");
  echo("    }\n\n");



  echo("    function NovaAlternativa()\n");
  echo("    {\n");
  echo("      if(qtdAlternativas < 10)\n");
  echo("      {");
  echo("        CancelaTodos();\n");
  echo("        xajax_CriarAlternativaDinamic(".$cod_curso.",".$cod_usuario.",".$cod_questao.",'".$tp_questao."');\n");
  //oculta o campo de Adicionar Nova Alternativa
  echo("        document.getElementById('trAddAlt').className = 'divHidden';");
  echo("      }");
  echo("      else\n");
  //184 - Uma questao pode conter no maximo 10 alternativas. TODO:

  echo("        alert('".RetornaFraseDaLista($lista_frases, 183)."');\n");
  echo("    }\n\n");

  echo("    function CancelaAlternativaNovaAlternativa(cod){\n");
  echo("      tr=document.getElementById('trAlt_'+cod);\n");
  echo("      if(tr!=null){\n");
  echo("        tr.parentNode.removeChild(tr);\n");
  echo("        xajax_ApagarAlternativaDinamic(".$cod_curso.",".$cod_usuario.",".$cod_questao.",cod,'".$tp_questao."');\n");
  echo("        HabilitarMudancaPosicaoAlt();");
  echo("        qtdAlternativas--;\n");
  echo("        document.getElementById('trAddAlt').className = '';");
  echo("      }\n");
  echo("    }\n");

  //echo("    function CancelaAlternativa(cod, conteudo){\n");
  //echo("      tr=document.getElementById('trAlt_'+cod);\n");
  //echo("      if(tr!=null){\n");
  //echo("        tr.parentNode.removeChild(tr);\n");
  //echo("        xajax_ApagarAlternativaDinamic(".$cod_curso.",".$cod_usuario.",".$cod_questao.",cod,'".$tp_questao."');\n");
  //echo("        HabilitarMudancaPosicaoAlt();");
  //echo("      }\n");
  //echo("    }\n");

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
  //echo("      var filhodiv = new Array();");
  echo("      j = 0;\n");
  echo("      checks = document.getElementsByName('chkAlt');\n");
  echo("      deleteArray = new Array();\n");
  /* Frase #90 - Voce realmente deseja apagar o(s) item(s) selecionado(s)? */
  echo("      if (confirm('".RetornaFraseDaLista($lista_frases, 90)."')){\n");
  echo("        for (i=0; i<checks.length; i++){\n");
  echo("          if(checks[i].checked){\n");
  echo("            getNumber=checks[i].id.split('_');\n");
  echo("            deleteArray[j++] = getNumber[1];\n");
  if($tp_questao == 'O' || $tp_questao == 'M')  
    echo("          AtualizaArrayGabEPosi(getNumber[1]);\n");
  echo("            xajax_ApagarAlternativaDinamic(".$cod_curso.",".$cod_usuario.",".$cod_questao.",getNumber[1],'".$tp_questao."');\n");
  echo("            qtdAlternativas--;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        DeletarLinhasAlternativa(deleteArray,j);\n");
  if($tp_questao == 'O') {
    echo("      xajax_EditarGabaritoQuestaoObjDinamic(".$cod_curso.",".$cod_questao.",FormaGabarito());\n");
  } elseif ($tp_questao == 'M') {
    echo("      xajax_EditarGabaritoQuestaoMultDinamic(".$cod_curso.",".$cod_questao.",FormaGabarito());\n");
  }
  echo("        VerificaChkBoxAlt(0);\n");
  echo("        document.getElementById('trAddAlt').className = '';");
  echo("      }\n");
  echo("    }\n\n");

  echo("    function EditarAlternativa(){\n");
  echo("      var spanAlt,checks,conteudo;\n");
  echo("      CancelaTodos();\n");
  echo("      DesabilitarMudancaPosicaoAlt();\n");
  echo("      checks = document.getElementsByName('chkAlt');\n");
  echo("      for (i=0; i<checks.length; i++){\n");
  echo("        if(checks[i].checked){\n");
  echo("          getNumber=checks[i].id.split('_');\n");
  echo("          spanAlt = document.getElementById('span_'+getNumber[1]);\n");
  //echo("          div=document.getElementById('div_'+getNumber[1]);\n");
  //if($tp_questao == 'O')
  //  echo("          resposta=div.childNodes[1].alt;\n");
  echo("          if(spanAlt.firstChild == null || spanAlt.firstChild.innerHTML != ''){\n");
  echo("            conteudo = spanAlt.innerHTML;\n");
  echo("            spanAlt.innerHTML = '';\n");
  echo("            document.getElementById('div_'+getNumber[1]).innerHTML = ' ' ;\n");
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
  echo("      for(i=0;i<j;i++)\n");
  echo("      {\n");
  echo("        trAlt = document.getElementById('trAlt_'+deleteArray[i]);\n");
  echo("        trAlt.parentNode.removeChild(trAlt);\n");
  if($tp_questao == 'D')
    echo("        DeletarLinhaGabarito(deleteArray[i]);\n");
  echo("      }\n");
  echo("    }\n\n");

  echo("    function RetornaPosiAlternativa(cod){\n");
  echo("      var i;\n");
  echo("      for(i=0;i<qtdAlternativas;i++){\n");
  echo("        if(posiAlt[i] == cod)\n");
  echo("          return i;\n");
  echo("      }\n");
  echo("    }\n\n");

  echo("    function RetornaNovaPosiAlternativa(posiAltNova,cod)\n");
  echo("    {");
  echo("      var i;\n");
  echo("      for(i=0;i<qtdAlternativas;i++)\n");
  echo("      {\n");
  echo("        if(posiAltNova[i] == cod)\n");
  echo("        return i;\n");
  echo("      }\n");
  echo("    }\n\n");

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

  echo("    function AtualizaPosicoesGabEPosi(string)\n");
  echo("    {");
  echo("      var i,j,posiAltNova,gabaritoTemp;\n");
  echo("      posiAltNova = string.split(\",\");\n");
  echo("      gabaritoTemp = new Array(qtdAlternativas);\n");
  echo("      for(i=0;i<qtdAlternativas;i++)\n");
  echo("      {\n");
  echo("        j = RetornaNovaPosiAlternativa(posiAltNova,posiAlt[i]);\n");
  echo("        gabaritoTemp[j] = gabarito[i];\n");
  echo("      }\n");
  echo("      for(i=0;i<qtdAlternativas;i++)\n");
  echo("      {\n");
  echo("        gabarito[i] = gabaritoTemp[i];\n");
  echo("      }\n");
  echo("      for(i=0;i<qtdAlternativas;i++)\n");
  echo("      {\n");
  echo("        posiAlt[i] = posiAltNova[i];\n");
  echo("      }\n");
  echo("    }\n\n");

  echo("    function AtualizarMudancaPosicoes(string){\n");
  echo("      var stringGabarito;\n");
  echo("      AtualizaPosicoesGabEPosi(string);\n");
  echo("      stringGabarito = FormaGabarito();\n");
  if($tp_questao=='O')
    echo("      xajax_EditarGabaritoQuestaoObjDinamic(".$cod_curso.",".$cod_questao.",stringGabarito);\n");
  elseif (tp_questao=='M')
    echo("      xajax_EditarGabaritoQuestaoMultDinamic(".$cod_curso.",".$cod_questao.",stringGabarito);\n");
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
  echo("      xajax_AtualizaIconesDinamic('".$questao['cod_questao']."','".$cod_curso."','".$tp_questao."');\n");
  echo("      HabilitarMudancaPosicaoAlt();\n");
  echo("      cancelarElemento = null;\n");
  echo("      document.getElementById('trAddAlt').className = '';");
  echo("    }\n\n");

  echo("    function FormaGabarito(){\n");
  echo("      var stringGabarito,i;\n");
  echo("      stringGabarito = '';\n");
  echo("      for(i=0;i<qtdAlternativas;i++){\n");
  echo("        stringGabarito = stringGabarito+gabarito[i];\n");
  echo("      }\n");
  echo("      return stringGabarito;\n");
  echo("    }\n\n");

  if($tp_questao == 'O' || $tp_questao == 'M')
  {
    echo("    function ConfirmaEdicaoAlternativa(cod){\n");
    echo("      var divAlt, containerTextArea, conteudo, posi, stringGabarito, td;\n");
    echo("      divAlt = document.getElementById('divAlt_'+cod);\n");
    echo("      containerTextArea = document.getElementById('containerTextArea_'+cod);\n");
    echo("      td = document.getElementById('trAlt_'+cod).firstElementChild;\n");
    echo("      td.className = \"itens\";\n");
    echo("      conteudo = document.getElementById('textAlt_'+cod).value;\n");
    echo("      posi = RetornaPosiAlternativa(cod);\n");
    echo("      gabarito[posi] = document.getElementById('select_'+cod).value;\n");
//    if($tp_questao == 'O'){
//      echo("      if(document.getElementById('select_'+cod).value=='0' && resposta=='resposta certa')\n");
//      echo("        num--;\n");
//      echo("      resposta=\"\";\n");
//    }
    echo("      stringGabarito = FormaGabarito();\n");
    echo("      DeletaCamposEdicao(divAlt);\n");
    echo("      containerTextArea.parentNode.removeChild(containerTextArea);");
    echo("      divAlt.innerHTML = conteudo;\n");
    if($tp_questao == 'O') {
      echo("      xajax_EditarAlternativaObjDinamic(".$cod_curso.",".$cod_questao.",cod,conteudo,stringGabarito);\n");
    } elseif ($tp_questao == 'M') {
      echo("      xajax_EditarAlternativaMultDinamic(".$cod_curso.",".$cod_questao.",cod,conteudo,stringGabarito);\n");
    }
    echo("      HabilitarMudancaPosicaoAlt();\n");
    echo("      cancelarElemento = null;\n");
    //Voltar aqui!!
    //echo("      if(qtdAlternativas == 1){\n");
    //echo("        xajax_CriarAlternativaDinamic(".$cod_curso.",".$cod_usuario.",$cod_questao,'O');\n");
    //echo("      }\n");
    echo("      xajax_AtualizaIconesDinamic(".$cod_questao.",".$cod_curso.",'".$tp_questao."');\n");
    echo("      document.getElementById('trAddAlt').className = '';");
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
    echo("      HabilitarMudancaPosicaoAlt();\n");
    echo("      cancelarElemento = null;\n");
    echo("    }\n\n");
  }

  echo("    function AtualizaNivel(nivel)\n");
  echo("    {\n");
  /* Frase #91 - Dificuldade atualizada com sucesso. */
  echo("      xajax_AtualizarNivelDinamic(".$cod_curso.",".$cod_questao.",nivel,'".RetornaFraseDaLista($lista_frases, 91)."');\n");
  echo("    }\n\n");

  echo("    function AtualizaTopico(cod)\n");
  echo("    {\n");
  echo("      if(cod==-1) {\n");
  echo("        NovoTopico(1);\n"); //No caso de selecionar "Novo Topico"
  echo("      }\n");
  /* Frase #92 - Topico atualizado com sucesso. */
  echo("      xajax_AtualizarTopicoDinamic(".$cod_curso.",".$cod_questao.",cod,'".RetornaFraseDaLista($lista_frases, 92)."');\n");
  echo("    }\n\n");

  echo("    function NovoTopico(cod)\n");
  echo("    {\n");
  echo("        MostraLayer(lay_novo_topico,30,10);\n");
  echo("        document.getElementById(\"nome\").value = '';\n");
  echo("        document.getElementById(\"nome\").focus();\n");
  echo("    }\n\n");

  echo("    function EsconderGabarito(cod)\n");
  echo("    {\n");
  echo("      var tr;\n");
  echo("      tr = document.getElementById('trAltGab_'+cod);\n");
  echo("      if(tr.style.display == '')\n");
  echo("      {\n");
  echo("        tr.style.display = 'none';\n");
  echo("        gabaritosVisiveis--;");
  echo("        if(gabaritosVisiveis == 0)\n");
  echo("        HabilitarMudancaPosicaoAlt();");
  echo("      }");
  echo("    }\n\n");

  echo("    function MostrarGabarito(cod)\n");
  echo("    {\n");
  echo("      var tr;\n");
  echo("      tr = document.getElementById('trAltGab_'+cod);\n");
  echo("      if(tr.style.display != '')\n");
  echo("      {\n");
  echo("        tr.style.display = '';\n");
  echo("        gabaritosVisiveis++;\n");
  echo("        if(gabaritosVisiveis == 1)\n");
  echo("        DesabilitarMudancaPosicaoAlt();");
  echo("      }\n");
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
  echo("      var pieces=path.split('\\\\');\n");
  echo("      n=pieces.length;\n");
  echo("      file=pieces[n-1];\n");
  echo("      pieces=file.split('/');\n");
  echo("      n=pieces.length;\n");
  echo("      file=pieces[n-1];\n");
  echo("      return(file);\n");
  echo("    }\n\n");

  echo("    function ArquivoValido(file)\n");
  echo("    {\n");
  // Usando expressÃ£o regular para identificar caracteres invÃ¡lidos
  echo("      var vet  = file.match(/^[A-Za-z0-9-\.\_\ ]+/);\n");
  echo("      if ((file.length == 0) || (vet == null) || (file.length != vet[0].length))\n");
  echo("        return false;\n");
  echo("      return true;\n");
  echo("    }\n");
  
  echo("    function CheckTodosArq(){\n");
  echo("      var e;\n");
  echo("      var i;\n");
  echo("      var CabMarcado = document.getElementById('checkMenuArq').checked;\n");
  echo("      var cod_itens=document.getElementsByName('chkArq');\n");
  //echo("      var cod_itens=getElementsByName_iefix('input', 'chkArq');\n");
  echo("      for(i = 0; i < cod_itens.length; i++){\n");
  echo("        e = cod_itens[i];\n");
  echo("        e.checked = CabMarcado;\n");
  echo("      }\n");
  echo("      VerificaChkBoxArq(0);\n");
  echo("    }\n\n");

  echo("      function EdicaoArq(i, msg){\n");
  echo("        var filename = document.getElementById('input_files').value;\n");
  echo("        filename = filename.replace(\"C:\\\\fakepath\\\\\", \"\");\n");
  echo("        if ((i==1) && ArquivoValido(filename)) { //OK\n");
  echo("          document.formFiles.submit();\n");
  echo("        }\n");
  echo("        else {\n");
  echo("          alert('".RetornaFraseDaLista($lista_frases, 206)."');\n");
  echo("          document.getElementById('input_files').style.visibility='hidden';\n");
  echo("          document.getElementById('input_files').value='';\n");
  echo("          document.getElementById('divArquivo').className='';\n");
  echo("          document.getElementById('divArquivoEdit').className='divHidden';\n");
  echo("          //Cancela EdiÃ§Ã£o\n");
  echo("          if (!cancelarTodos)\n");
  //echo("            xajax_AcabaEdicaoDinamic('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", 0);\n");
  echo("          input=0;\n");
  echo("          cancelarElemento=null;\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("      function ApagarArq(){\n");
  echo("        checks = document.getElementsByName('chkArq');\n");
  echo("        if (confirm('".RetornaFraseDaLista($lista_frases, 39)."')){\n");
  //echo("          xajax_AbreEdicao('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", ".$cod_topico_raiz.");\n");
  echo("          for (i=0; i<checks.length; i++){\n");
  echo("            if(checks[i].checked){\n");
  echo("              getNumber=checks[i].id.split('_');\n");
  echo("              nomeArq = document.getElementById(\"nomeArq_\"+getNumber[1]).getAttribute('nomeArq');\n");
  echo("            xajax_ExcluiArquivoDinamic(getNumber[1],nomeArq,".$cod_curso.",".$cod_questao.",".$cod_usuario.", \"".RetornaFraseDaLista($lista_frases, 43)."\");\n");
  //echo("              js_conta_arq--;\n");
  echo("            }\n");
  echo("          }\n");
  echo("          LimpaBarraArq();\n");
  echo("          VerificaChkBoxArq(0);\n");
  echo("        }\n");
  echo("      }\n\n");
  
  echo("      function Descompactar(){\n");
    echo("        checks = document.getElementsByName('chkArq');\n");
    echo("        for (i=0; i<checks.length; i++){\n");
    echo("          if(checks[i].checked){\n");
    echo("            getNumber=checks[i].id.split(\"_\");\n");
    echo("            arqZip=document.getElementById('nomeArq_'+getNumber[1]).getAttribute('arqZip');\n");
     /* Frase #40 - Voce tem certeza que deseja descompactar este arquivo? */
    echo("            if (confirm(\"".RetornaFraseDaLista($lista_frases, 40)."\")){\n");
   // echo("              xajax_AbreEdicao('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", ".$cod_topico_raiz.");\n");
    echo("              window.location='acoes.php?cod_curso=".$cod_curso."&cod_questao=".$cod_questao."&pasta=questao&acao=descompactar&arq='+arqZip;\n");
    echo("            }\n");
    echo("          } \n");
    echo("        }\n");
    echo("      }\n");

  echo("      function VerificaChkBoxArq(alpha){\n");
  echo("        CancelaTodos();\n");
  echo("        checks = document.getElementsByName('chkArq');\n");
  echo("        var i, j=0;\n");
  echo("        var arqComum=0;\n");
  echo("        var arqZip=0;\n");
  echo("        var arqOculto=0;\n");
  echo("        var pasta=0;\n\n");

  echo("        for (i=0; i<checks.length; i++){\n");
  echo("          if(checks[i].checked){\n");
  echo("            j++;\n");
  echo("            getNumber=checks[i].id.split(\"_\");\n");
  echo("            tipo = document.getElementById(\"nomeArq_\"+getNumber[1]).getAttribute('tipoArq');\n");
  echo("            switch (tipo){\n");
  echo("              case ('pasta'): pasta=1;break;\n");
  echo("              case ('comum'): arqComum++;break;\n");
  echo("              case ('zip'): arqZip++;break;\n");
  echo("            }\n\n");

  echo("            if (document.getElementById(\"nomeArq_\"+getNumber[1]).getAttribute('arqOculto')=='sim'){\n");
  echo("               arqOculto++;\n");
  echo("            }\n\n");

  echo("          }\n");
  echo("        }\n");

  echo("        if (pasta==1){\n");
  echo("          document.getElementById('mArq_apagar').className=\"menuUp02\";\n");
  echo("          document.getElementById('mArq_ocultar').className=\"menuUp\";\n");
  echo("          document.getElementById('mArq_descomp').className=\"menuUp\";\n");

  echo("          document.getElementById('sArq_apagar').onclick= function(){ ApagarArq(); };\n");
  echo("          document.getElementById('sArq_ocultar').onclick= function(){  };\n");
  echo("          document.getElementById('sArq_descomp').onclick= function(){  };\n\n");

  echo("        }else if((arqComum==1)||(arqZip>1)){\n");
  echo("          document.getElementById('mArq_apagar').className=\"menuUp02\";\n");
  echo("          document.getElementById('mArq_ocultar').className=\"menuUp02\";\n");
  echo("          document.getElementById('mArq_descomp').className=\"menuUp\";\n\n");

  echo("          document.getElementById('sArq_apagar').onclick= function(){ ApagarArq(); };\n");
  echo("          document.getElementById('sArq_ocultar').onclick= function(){ Ocultar(); };\n");
  echo("          document.getElementById('sArq_descomp').onclick= function(){  };\n\n");
  echo("        }else if(arqComum>1){\n");
  echo("          document.getElementById('mArq_apagar').className=\"menuUp02\";\n");
  echo("          document.getElementById('mArq_ocultar').className=\"menuUp02\";\n");
  echo("          document.getElementById('mArq_descomp').className=\"menuUp\";\n\n");

  echo("          document.getElementById('sArq_apagar').onclick= function(){ ApagarArq(); };\n");
  echo("          document.getElementById('sArq_ocultar').onclick= function(){ Ocultar(); };\n");
  echo("          document.getElementById('sArq_descomp').onclick= function(){  };\n\n");
  echo("        }else if(arqZip==1){\n");
  echo("          document.getElementById('mArq_apagar').className=\"menuUp02\";\n");
  echo("          document.getElementById('mArq_ocultar').className=\"menuUp02\";\n");
  echo("          document.getElementById('mArq_descomp').className=\"menuUp02\";\n\n");

  echo("          document.getElementById('sArq_apagar').onclick= function(){ ApagarArq(); };\n");
  echo("          document.getElementById('sArq_ocultar').onclick= function(){ Ocultar(); };\n");
  echo("          document.getElementById('sArq_descomp').onclick= function(){ Descompactar() };\n");
  echo("        }else{\n");
  echo("          document.getElementById('mArq_apagar').className=\"menuUp\";\n");
  echo("          document.getElementById('mArq_ocultar').className=\"menuUp\";\n");
  echo("          document.getElementById('mArq_descomp').className=\"menuUp\";\n\n");

  echo("          document.getElementById('sArq_apagar').onclick= function(){  };\n");
  echo("          document.getElementById('sArq_ocultar').onclick= function(){  };\n");
  echo("          document.getElementById('sArq_descomp').onclick= function(){  };\n");
  echo("        }\n\n");

  echo("        //todos arquivos selecionados sao ocultos\n");
  echo("        if ((j==arqOculto)&&(j!=0)) {\n");
  echo("            document.getElementById('sArq_ocultar').onclick= function(){ Desocultar(); };\n");
  echo("        }\n");

  echo("        //Nao foi chamado pela funcao CheckTodos\n");
  echo("        if (alpha){\n");
  echo("          if (j==checks.length){ document.getElementById('checkMenuArq').checked=true; }\n");
  echo("          else document.getElementById('checkMenuArq').checked=false;\n");
  echo("        }\n");
  echo("      }\n\n");
  
  echo("	function Ocultar(){\n");
  echo("		checks = document.getElementsByName('chkArq');\n");
  echo("		j=0;\n");
  echo("		var nomesArqs = new Array();\n");

  echo("		for (i=0; i<checks.length; i++){\n");
  echo("			if(checks[i].checked){\n");
  echo("				getNumber=checks[i].id.split(\"_\");\n");
  echo("				if ((document.getElementById(\"nomeArq_\"+getNumber[1]).getAttribute('arqOculto'))=='nao'){\n");
  echo("					nomesArqs[j] = new Array();\n");
  echo("					nomeArq = document.getElementById(\"nomeArq_\"+getNumber[1]).getAttribute('nomeArq');\n");
  echo("					nomesArqs[j][0]=nomeArq;\n");
  echo("					nomesArqs[j][1]=getNumber[1];\n");
  echo("					j++;\n");
  echo("				}\n");
  echo("			}\n");
  echo("		}\n");
  /* #70 - Oculto */
  /* #42 - Arquivo(s) ocultado(s) com sucesso. */
  echo("		xajax_OcultarArquivosDinamic(nomesArqs, '".RetornaFraseDaLista($lista_frases, 70)."', ".$cod_curso.", ".$cod_usuario.", '".RetornaFraseDaLista($lista_frases, 42)."');\n");
  echo("          LimpaBarraArq();\n");
  echo("          VerificaChkBoxArq(0);\n");
  echo("	}\n\n");

  echo("	function Desocultar(){\n");
  echo("		checks = document.getElementsByName('chkArq');\n");
  echo("		j=0;\n");
  echo("		var nomesArqs = new Array();\n");

  echo("		for (i=0; i<checks.length; i++){\n");
  echo("			if(checks[i].checked){\n");
  echo("				getNumber=checks[i].id.split(\"_\");\n");
  echo("				if ((document.getElementById(\"nomeArq_\"+getNumber[1]).getAttribute('arqOculto'))=='sim'){\n");
  echo("					nomesArqs[j] = new Array();\n");
  echo("					nomeArq = document.getElementById(\"nomeArq_\"+getNumber[1]).getAttribute('nomeArq');\n");
  echo("					nomesArqs[j][0]=nomeArq;\n");
  echo("					nomesArqs[j][1]=getNumber[1];\n");
  echo("					j++;\n");
  echo("				}\n");
  echo("			}\n");
  echo("		}\n");
  /* #234 - Arquivo(s) desocultado(s) com sucesso. */
  echo("		xajax_DesocultarArquivosDinamic(nomesArqs, ".$cod_curso.", ".$cod_usuario.", '".RetornaFraseDaLista($lista_frases, 234)."');\n");
  echo("          LimpaBarraArq();\n");
  echo("          VerificaChkBoxArq(0);\n");
  echo("}\n\n");
  
  echo("      function LimpaBarraArq(){\n");
  echo("        lista = document.getElementById('listFiles');\n");
  echo("        if (!js_conta_arq){\n");
  echo("          pai_lista=lista.parentNode;\n");
  echo("          pai_lista2=pai_lista.parentNode;\n");
  echo("          i=3;\n");
  echo("          do{\n");
  echo("            if (pai_lista.firstChild)\n");
  echo("              pai_lista.removeChild(pai_lista.firstChild);\n");
  echo("            i--;\n");
  echo("          }while(i>0);\n");
  echo("        }\n");
  echo("        document.getElementById('checkMenuArq').checked=false;\n");
  echo("        CheckTodosArq();\n");
  echo("      }\n");

  echo("    function InsereDiretorioVazio(){\n");
  echo("      var trRef,tr,td;");
  echo("      tr = document.createElement(\"tr\");\n");
  echo("      tr.setAttribute(\"id\",\"diretorioVazio\");\n");
  echo("      td = document.createElement(\"td\");\n");
  echo("      td.colSpan = \"6\";\n");
  /* Texto #187 - Diretorio esta vazio.*/
  echo("      td.appendChild(document.createTextNode(\"".RetornaFraseDaLista($lista_frases, 187)."\"));\n");
  echo("      tr.appendChild(td);\n");
  echo("      trRef = document.getElementById(\"optArq\");\n");
  echo("      trRef.parentNode.insertBefore(tr,trRef);\n");
  echo("    }\n\n");
  
  echo("    function HabilitarMudancaPosicaoAlt()\n");
  echo("    {\n");
  echo("      if(tBody_alternativas) tableDnD.init(null,tBody_alternativas,".$existeGabaritoDiss.");\n");
  echo("    }\n\n");

  echo("    function DesabilitarMudancaPosicaoAlt()\n");
  echo("    {");
  echo("      if(tableDnD) tableDnD.term();\n");
  echo("    }\n\n");

  echo("    function SoltaMouse(ids)\n");
  echo("    {");
  //echo("      IntercalaCorLinhaAlt();\n");
  echo("      xajax_AtualizaPosicoesDasAlternativasDinamic(".$cod_curso.", ".$cod_usuario.", ids, \"".$tp_questao."\");\n");
  echo("    }\n\n");

  echo("    function Voltar()\n");
  echo("    {\n");
  echo("      window.location='questoes.php?cod_curso=".$cod_curso."&visualizar=Q';\n");
  echo("    }\n\n");

  echo("    function ApagarQuestao()\n");
  echo("    {\n");
  /* Frase #93 - Tem certeza que deseja excluir definitivamente as questoes? */
  echo("      if(confirm('".RetornaFraseDaLista($lista_frases, 93)."'))\n");
  echo("        document.location='acoes.php?cod_curso=".$cod_curso."&cod_questao=".$cod_questao."&acao=apagar&lixeira=".$lixeira."';\n");
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
  echo("    </script>\n\n");

  $objAjax->printJavascript();

  /* fim - JavaScript */
  /*********************************************************/

  include("../menu_principal.php");

  if($aplicada)
    /* Frase #228 - (Questao aplicada) */
    $titulo_aplicada=RetornaFraseDaLista($lista_frases, 228);
  else
    $titulo_aplicada="";

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  if ($tela_formador)
  {
    $titulo="<span id=\"tit_".$questao['cod_questao']."\">".$questao['titulo']."&nbsp;".$titulo_aplicada."</span>";
    /* Frase #50 - Renomear */
    $renomear="<span onclick=\"AlteraTitulo('".$questao['cod_questao']."');\" id=\"renomear_".$questao['cod_questao']."\">".RetornaFraseDaLista($lista_frases, 50)."</span>";
    $enunciado="<span id=\"text_".$questao['cod_questao']."\">".$questao['enunciado']."</span>";
    $gabarito="<span id=\"texto_".$questao['cod_questao']."\">".$questao_diss."</span>";
    /* Frase #94 - Editar enunciado */
    $editar="<span onclick=\"AlteraTexto(".$questao['cod_questao'].");\">".RetornaFraseDaLista($lista_frases, 94)."</span>";
    /* Frase #95 - Novo topico */
    /*$novo_topico="<span onclick=\"NovoTopico(".$questao['cod_questao'].");\">".RetornaFraseDaLista($lista_frases, 95)."</span>";
    /* Frase #96 - Limpar enunciado */
    $limpar="<span onclick=\"LimparTexto(".$questao['cod_questao'].");\">".RetornaFraseDaLista($lista_frases, 96)."</span>";
    /* Frase #97 - Editar gabarito */
    $editar_gabarito="<span onclick=\"AlteraGabarito(".$questao['cod_questao'].");\">".RetornaFraseDaLista($lista_frases, 97)."</span>";
    /* Frase #98 - Limpar gabarito */
    $limpar_gabarito="<span onClick=\"LimparGabarito(".$questao['cod_questao'].");\">".RetornaFraseDaLista($lista_frases, 98)."</span>";


    if($aplicada)
      $cabecalho=RetornaFraseDaLista($lista_frases, 59);  /* Frase #59 - Questoes */
    elseif($tp_questao == 'O')
      $cabecalho=RetornaFraseDaLista($lista_frases, 99);  /* Frase #99 - Editar Questao Objetiva */
    elseif($tp_questao == 'M')
      $cabecalho=RetornaFraseDaLista($lista_frases, 214); /* Frase #214 - Editar Questao Multipla Escolha */
    elseif($tp_questao == 'D')
      $cabecalho=RetornaFraseDaLista($lista_frases, 229); /* Frase #229 - Editar Questao Dissertativa */

    echo("          <h4 id=\"tituloPrinc\">".RetornaFraseDaLista($lista_frases, 1)." - ".$cabecalho."</h4>\n");

    /* Frase #5 - Voltar */
    /* 509 - Voltar */
    echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

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
    echo("                  <li><span onclick=\"window.open('historico_questao.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_questao=".$cod_questao."','Historico','width=600,height=400,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');\">".RetornaFraseDaLista($lista_frases, 56)."</span></li>\n");

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
    if(!$aplicada) {
      /* 70 - Opcoes (ger)*/
      echo("                      <td width=\"15%\">" . RetornaFraseDaLista($lista_frases_geral, 70) . "</td>\n");
    }
    /* Frase #57 - Compartilhamento */
    echo("                      <td width=\"15%\">".RetornaFraseDaLista($lista_frases, 57)."</td>\n");
    echo("                    </tr>\n");

    /* Comeca a popular os dados na tabela. */
    echo("                    <tr id='tr_".$questao['cod_questao']."'>\n");
    echo("                      <td class=\"itens\">".$titulo."</td>\n");
    echo("                      <td>\n");
    if(!$aplicada) {
      echo("                      <select id=\"selectTopico\" class=\"input\" onChange=\"AtualizaTopico(this.value);\">");
      /* Frase #173 - Escolha um topico */
      echo("                        <option value=\"0\" ".$texto.">".RetornaFraseDaLista($lista_frases, 173)."</option>\n");
      /*Frase #106*/
      echo("                        <option value=\"-1\" >".RetornaFraseDaLista($lista_frases,95)."</option>\n");
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

          echo("                <option value=\"".$cod_topico."\" ".$texto.">".$topico."</option>\n");
        }
      }
      echo("                      </select>\n");
    } else {
      /* Pega o nome topico da questao */
      echo(RetornaTopicoQuestao($sock,$cod_questao));
    }
    echo("                      </td>\n");
    echo("                      <td>\n");

    if($questao['nivel'] == 'D') {
      $dificil = "checked='true'";
      /* Frase #100 - Dificil */
      $nivel_texto = RetornaFraseDaLista($lista_frases, 100);
    } else {
      $dificil = "";
    }
    if($questao['nivel'] == 'M') {
      $medio = "checked='true'";
      /* Frase #101 - Medio */
      $nivel_texto = RetornaFraseDaLista($lista_frases, 101);
    } else {
      $medio = "";
    }
    if($questao['nivel'] == 'F') {
      $facil = "checked='true'";
      /* Frase #102 - Facil */
      $nivel_texto = RetornaFraseDaLista($lista_frases, 102);
    } else {
      $facil = "";
    }

    if(!$aplicada) {
      /* Frase #100 - Dificil */
      echo("                        <input type=\"radio\" name=\"nivel\" onClick=\"AtualizaNivel('D');\" ".$dificil." />".RetornaFraseDaLista($lista_frases, 100)."<br />\n");
      /* Frase #101 - Medio */
      echo("                        <input type=\"radio\" name=\"nivel\" onClick=\"AtualizaNivel('M');\" ".$medio." />".RetornaFraseDaLista($lista_frases, 101)."<br />\n");
      /* Frase #102 - Facil */
      echo("                        <input type=\"radio\" name=\"nivel\" onClick=\"AtualizaNivel('F');\" ".$facil." />".RetornaFraseDaLista($lista_frases, 102)."<br />\n");
    } else {
      echo($nivel_texto); /* Nivel da questao */
    }
    echo("                      </td>\n");
    if(!$aplicada) {
      echo("                  <td align=\"left\" valign=\"top\" class=\"botao2\">\n");
      echo("                    <ul>\n");
      echo("                      <li>".$renomear."</li>\n");
      echo("                      <li>".$editar."</li>\n");
      echo("                      <li>".$limpar."</li>\n");

      if($tp_questao == 'D')
      {
        echo("                    <li>".$editar_gabarito."</li>\n");
        echo("                    <li>".$limpar_gabarito."</li>\n");
      }
      echo("                      <li>".$novo_topico."</li>\n");
      // G 1 - Apagar
      echo("                      <li><span onclick=\"ApagarQuestao();\">" . RetornaFraseDaLista($lista_frases_geral, 1) . "</span></li>\n");
      echo("                    </ul>\n");
      echo("                  </td>\n");
    }

    if($questao['tipo_compartilhamento'] == "F")
      /* Frase #6 - Compartilhado com Formadores */
      $compartilhamento = RetornaFraseDaLista($lista_frases, 6);
    else
      /* Frase #8 - Nao compartilhado */
      $compartilhamento = RetornaFraseDaLista($lista_frases, 8);

    if(!$aplicada) {
      if($cod_usuario == $questao['cod_usuario'])
        $compartilhamento = "<span id=\"comp_".$cod_questao."\" class=\"link\" onclick=\"js_cod_item='".$cod_questao."';AtualizaComp('".$questao['tipo_compartilhamento']."');MostraLayer(cod_comp,140,0);return(false);\">".$compartilhamento."</span>";
    }

    echo("                      <td>".$compartilhamento."</td>");

    echo("                    </tr>\n");

    echo("                    <tr class=\"head\">\n");
    /* Frase #17 - Enunciado */
    echo("                      <td class=\"center\" colspan=\"6\">".RetornaFraseDaLista($lista_frases, 17)."</td>\n");
    echo("                    </tr>\n");
    echo("                    <tr>\n");
    echo("                      <td class=\"itens\" colspan=\"6\">\n");
    echo("                        <div class=\"divRichText\">\n");
    echo("                        ".$enunciado."\n");
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


    if($tp_questao == "O" || $tp_questao == 'M')
    {
      echo("                  <tr class=\"head\">\n");
      /* Frase #18 - Alternativas */
      echo("                    <td class=\"center\" colspan=\"6\">".RetornaFraseDaLista($lista_frases, 18)."</td>\n");
      echo("                  </tr>\n");
      echo("                  <tBody id=\"tBody_alternativas\">");

      if ((count($alternativas)>0)&&($alternativas != null))
      {
        foreach ($alternativas as $cod => $linha_item)
        {
          $texto = $linha_item['texto'];
          $cod_alternativa = $linha_item['cod_alternativa'];
          echo("                  <tr name=\"Alt[ ]\" id=\"trAlt_".$linha_item['cod_alternativa']."\">\n");
          echo("                    <td class=\"itens\" colspan=\"6\">\n");
          if(!$aplicada) {
            echo("                     <div class=\"containerCheck\">\n");
            echo("                       <input type=\"checkbox\" name=\"chkAlt\" id=\"alt_".$linha_item['cod_alternativa']."\" onclick=\"VerificaChkBoxAlt(1);\" value=\"".$linha_item['cod_alternativa']."\" />\n");
            echo("                     </div>\n");
          }
          echo("                      <div id=\"divAlt_".$linha_item['cod_alternativa']."\" class=\"divAlt\">".$texto."</div>\n");
          echo("                      <span id=\"div_".$linha_item['cod_alternativa']."\">&nbsp;</span>\n");
          echo("                    </td>\n");
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

      echo("                  </tBody>");

      if(!$aplicada) {
        echo("                  <tr id=\"optAlt\">\n");
        echo("                    <td align=\"left\" colspan=\"6\">\n");
        echo("                      <ul>\n");

        echo("                        <li class=\"checkMenu\"><span><input type=\"checkbox\" id=\"checkMenuAlt\" onclick=\"CheckTodos(2);\" /></span></li>\n");
        // Frase #71 - Apagar 
        echo("                        <li class=\"menuUp\" id=\"mAlt_apagar\"><span id=\"sAlt_apagar\">".RetornaFraseDaLista($lista_frases, 71)."</span></li>\n");
        // Frase #104 - Editar 
        echo("                        <li class=\"menuUp\" id=\"mAlt_editar\"><span id=\"sAlt_editar\">".RetornaFraseDaLista($lista_frases, 104)."</span></li>\n");

        //if($tp_questao == 'D')
          //echo("                        <li class=\"menuUp\" id=\"mAlt_gabarito\"><span id=\"sAlt_gabarito\">Exibir gabarito</span></li>\n");
          echo("                      </ul>\n");
          echo("                    </td>\n");
          echo("                  </tr>\n");
          echo("                  <tr id=\"trAddAlt\">\n");
          echo("                    <td align=\"left\" colspan=\"6\">\n");
          /* Frase #105 - Adicionar Alternativa TODO:isj*/
          echo("                      <div id=\"divAddAlt\"><span class=\"link\" id=\"insertAlt\" onclick=\"NovaAlternativa();\">(+) ".RetornaFraseDaLista($lista_frases, 105)."</span></div>\n");
          echo("                    </td>\n");
          echo("                  </tr>\n");
      }
    }
    if (($num_arq_vis > 0) || (!$aplicada)) {
    echo("                  <tr class=\"head\">\n");
    /* 12 - Arquivos */
    echo("                    <td colspan=\"6\">".RetornaFraseDaLista($lista_frases,12)."</td>\n");
    echo("                  </tr>\n");
    
   if(count($lista_arq)==0){
      echo("                <tr>\n");
      /*187 - Diretório está vazio*/
      echo("                    <td colspan=\"6\">".RetornaFraseDaLista($lista_frases,187)."</td>\n");
      echo("				</tr>\n");
    }
  
    if (is_array($lista_arq) && count($lista_arq)>0){
    
      $conta_arq=0;

      echo("                  <tr>\n");
      echo("                    <td class=\"itens\" colspan=\"6\" id=\"listFiles\">\n");
      // Procuramos na lista de arquivos se existe algum visivel
      $ha_visiveis = $num_arq_vis > 0;


      if (($ha_visiveis) || (!$aplicada)){

        $nivel_anterior=0;
        $nivel=-1;

        foreach($lista_arq as $cod => $linha){
          $linha['Arquivo'] = mb_convert_encoding($linha['Arquivo'], "ISO-8859-1", "UTF-8");
          if (!($linha['Arquivo']=="" && $linha['Diretorio']=="")){
            if ((!$linha['Status']) || (!$aplicada)){
              $nivel_anterior=$nivel;
              $espacos="";
              $espacos2="";
              $temp=explode("/",$linha['Diretorio']);
              $nivel=count($temp)-1;
              for ($c=0;$c<=$nivel;$c++){
                if($exercicio['situacao']=='C'){
                  $espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
                  $espacos2.="  ";
                }
                else{
                  $espacos.="";
                  $espacos2.="";
                }
              }

              $caminho_arquivo = $dir_questao_temp['link'].$linha['Diretorio']."/".$linha['Arquivo'];
              $caminho_arquivo = preg_replace("/\/\//", "/", $caminho_arquivo);
// echo($caminho_arquivo);

              if ($linha['Arquivo'] != ""){
                if ($linha['Diretorio']!=""){
                    $espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
                    $espacos2.="  ";
                  }

                if ($linha['Status']) $arqOculto="arqOculto='sim'";
                else $arqOculto="arqOculto='nao'";

                if (eregi(".zip$",$linha['Arquivo'])){
                  // arquivo zip
                  $imagem    = "<img src=\"../imgs/arqzip.gif\" border=0 alt=\"\"/>";
                  $tag_abre  = "<a href=\"".ConverteUrl2Html($caminho_arquivo)."\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".ConverteUrl2Html($caminho_arquivo)."');return false;\" tipoArq=\"zip\" nomeArq=\"".ConverteUrl2Html($caminho_arquivo)."\" arqZip=\"".$linha['Arquivo']."\" ". $arqOculto.">";
                }
                else{
                  // arquivo comum
                  //imagem
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
                  $tag_abre  = "<a href=\"".ConverteUrl2Html($caminho_arquivo)."\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".ConverteUrl2Html($caminho_arquivo)."'); return false;\" tipoArq=\"comum\" nomeArq=\"".ConverteUrl2Html($caminho_arquivo)."\" ".$arqOculto.">";
                }

                $tag_fecha = "</a>";

                echo("                        ".$espacos2."<span id=\"arq_".$conta_arq."\">\n");

                if (!$aplicada){
                  echo("                          ".$espacos2."<input type=\"checkbox\" name=\"chkArq\" onclick=\"VerificaChkBoxArq(1);\" id=\"chkArq_".$conta_arq."\" />\n");
                }
				/* #235 - Última modificação em */
                echo("                          ".$espacos2.$espacos.$imagem." ".$tag_abre.$linha['Arquivo'].$tag_fecha." - (".round(($linha['Tamanho']/1024),2)."Kb) - ".RetornaFraseDaLista($lista_frases,235)." ".UnixTime2DataMesAbreviado($linha["Data"])." ".UnixTime2Hora($linha["Data"])."");

                echo("<span id=\"local_oculto_".$conta_arq."\">");
                if ($linha['Status']){
                  // 70 - Oculto
                    echo("<span id=\"arq_oculto_".$conta_arq."\"> - <span style='color:red;'>".RetornaFraseDaLista($lista_frases,70)."</span></span>");
                }
                echo("</span>\n");
                echo("                          ".$espacos2."<br />\n");
                echo("                        ".$espacos2."</span>\n");
              }

              else if ((!$aplicada) || (haArquivosVisiveisDir($linha['Diretorio'], $lista_arq))){
                if ($nivel_anterior>=$nivel){
                  $i=$nivel_anterior-$nivel;
                  $j=$i;
                  $espacos3="";
                  do{
                    $espacos3.="  ";
                    $j--;
                  }while($j>=0);

                  while($i>=0){
                    echo("                      ".$espacos3."</span>\n");
                    $i--;
                  }
                }
                // pasta
                $imagem    = "<img src=\"../imgs/pasta.gif\" border=0 alt=\"\" />";
                echo("                      ".$espacos2."<span id=\"arq_".$conta_arq."\">\n");
                echo("                        ".$espacos2."<span class=\"link\" id=\"nomeArq_".$conta_arq."\" tipoArq=\"pasta\" nomeArq=\"".htmlentities($caminho_arquivo)."\"></span>\n");
                if (!$aplicada){
                  echo("                        ".$espacos2."<input type=\"checkbox\" name=\"chkArq\" onclick=\"VerificaChkBoxArq(1);\" id=\"chkArq_".$conta_arq."\" />\n");
                }
                echo("                        ".$espacos2.$espacos.$imagem.$temp[$nivel]."\n");
                echo("                        ".$espacos2."<br />\n");
             }
            }
          }
          $conta_arq++;
        }
        do{
          $j=$nivel;
          $espacos3="";
          do{
            $espacos3.="  ";
            $j--;
          }while($j>=0);
          echo("                      ".$espacos3."</span>\n");
          $nivel--;
        }while($nivel>=0);
      }
      echo("                      <script type=\"text/javascript\" language=\"JavaScript\">js_conta_arq=".$conta_arq.";</script>\n");
      echo("                    </td>\n");
      echo("                  </tr>\n");
     
      
    }
    if(!$aplicada) {
      echo("                   <tr id=\"optArq\">\n");
      echo("                     <td align=\"left\" colspan=\"6\">\n");
       echo("                      <ul>\n");
      echo("                        <li class=\"checkMenu\"><span><input type=\"checkbox\" id=\"checkMenuArq\" onClick=\"CheckTodosArq();\" /></span></li>\n");
      echo("                        <li class=\"menuUp\" id=\"mArq_apagar\"><span id=\"sArq_apagar\">".RetornaFraseDaLista($lista_frases_geral, 1)."</span></li>\n");
      echo("                        <li class=\"menuUp\" id=\"mArq_descomp\"><span id=\"sArq_descomp\">".RetornaFraseDaLista($lista_frases_geral, 38)."</span></li>\n");
      echo("                        <li class=\"menuUp\" id=\"mArq_ocultar\"><span id=\"sArq_ocultar\">".RetornaFraseDaLista($lista_frases_geral, 511)."</span></li>\n");
      echo("                      </ul>\n");
      echo("                     </td>\n");
      echo("                   </tr>\n");
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
      echo(                          "<span>".RetornaFraseDaLista($lista_frases,208)."</span>\n");echo("                          <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n");
      //echo("                          <input type=\"file\" id=\"input_files\" name=\"input_files\" class=\"input\">\n");
      echo("                          <input class=\"input\" type=\"file\" id=\"input_files\" name=\"input_files\" onchange=\"EdicaoArq(1);\" style=\"border:2px solid #9bc\" />\n");
      //echo("                          &nbsp;&nbsp;\n");
      //echo("                          <span onclick=\"EdicaoArq(1);\" id=\"OKFile\" class=\"link\">" . RetornaFraseDaLista($lista_frases_geral, 18) . "</span>\n");
      //echo("                          &nbsp;&nbsp;\n");
      //echo("                          <span onclick=\"EdicaoArq(0);\" id=\"cancFile\" class=\"link\">" . RetornaFraseDaLista($lista_frases_geral, 2) . "</span>\n");
      echo("                        </div>\n");
      echo("                        <div id=\"divAnexando\" class=\"divHidden\"></div>");
      /* 26 - Anexar arquivos (ger) */
      echo("                        <div id=\"divArquivo\"><img alt=\"\" src=\"../imgs/paperclip.gif\" border=\"0\" /> <span class=\"link\" id =\"insertFile\" onclick=\"AcrescentarBarraFile(1);\">" . RetornaFraseDaLista($lista_frases_geral, 26) . "</span></div>\n");
      echo("                      </form>\n");
      echo("                    </td>\n");
      echo("                  </tr>\n");
    }
 }
    echo("                </table>\n");
    echo("              </td>\n");
    echo("            </tr>\n");
    echo("          </table>\n");
    echo("          <br />\n");
    /* 509 - voltar, 510 - topo */
    echo("          <ul class=\"btsNavBottom\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span><span><a href=\"#topo\">&nbsp;".RetornaFraseDaLista($lista_frases_geral,510)."&nbsp;&#94;&nbsp;</a></span></li></ul>\n");
    //*NAO ï¿½FORMADOR*/
  }
  else
  {
    /* Frase #1 - Exercicios */
    /* Frase #74 - Area restrita ao formador. */
    echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases,74)."</h4>\n");

    /* Frase #5 - Voltar */
    /* 509 - Voltar */
    echo("          <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

    echo("          <div id=\"mudarFonte\">\n");
    echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
    echo("          </div>\n");

    /* Frase #5 - Voltar */
    echo("<form><input class=\"input\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases,5)."\" onclick=\"history.go(-1);\" /></form>\n");
  }

  echo("        </td>\n");
  echo("      </tr>\n");

  include("../tela2.php");

  /* Novo Topico */
  echo("    <div class=\"popup\" id=\"layer_novo_topico\">\n");
  echo("      <div class=\"posX\"><span onclick=\"EscondeLayer(lay_novo_topico);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("        <div class=\"int_popup\">\n");
  echo("          <div class=\"ulPopup\">\n");
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
  echo("    <div class=\"popup\" id=\"comp\">\n");
  echo("      <div class=\"posX\"><span onclick=\"EscondeLayer(cod_comp);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("      <div class=\"int_popup\">\n");
  echo("        <form name=\"form_comp\" action=\"\" id=\"form_comp\">\n");
  echo("          <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
  echo("          <input type=\"hidden\" name=\"cod_usuario\" value=\"".$cod_usuario."\" />\n");
  echo("          <input type=\"hidden\" name=\"cod_item\" value=\"\" />\n");
  echo("          <input type=\"hidden\" name=\"tipo_comp\" id=\"tipo_comp\" value=\"\" />\n");
  /* Frase #192 - Compartilhamento alterado com sucesso. */
  echo("          <input type=\"hidden\" name=\"texto\" id=\"texto\" value=\"".RetornaFraseDaLista($lista_frases, 192)."\" />\n");
  echo("          <ul class=\"ulPopup\">\n");
  echo("            <li onClick=\"document.getElementById('tipo_comp').value='F'; xajax_MudarCompartilhamentoDinamic(xajax.getFormValues('form_comp'), '".RetornaFraseDaLista($lista_frases, 6)."','Q'); EscondeLayers();\">\n");
  echo("              <span id=\"tipo_comp_F\" class=\"check\"></span>\n");
  /* Frase #6 - Compartilhado com formadores */
  echo("              <span>".RetornaFraseDaLista($lista_frases, 6)."</span>\n");
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
