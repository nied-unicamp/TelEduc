<?php 
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/material/material.php

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
  ARQUIVO : cursos/aplic/material/material.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("material.inc");

  /**************** ajax ****************/

  require_once("../xajax_0.5/xajax_core/xajax.inc.php");

  // Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  // Registre os nomes das funcoes em PHP que voce quer chamar atraves do xajax
  $objAjax->register(XAJAX_FUNCTION,"MudarCompartilhamento");
  $objAjax->register(XAJAX_FUNCTION,"AbreEdicao");
  $objAjax->register(XAJAX_FUNCTION,"DecodificaString");
  $objAjax->register(XAJAX_FUNCTION,"AcabaEdicaoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"MoverItensDinamic");
  $objAjax->register(XAJAX_FUNCTION,"RetornaFraseDinamic");
  $objAjax->register(XAJAX_FUNCTION,"AtualizaPosicoes");
  $objAjax->register(XAJAX_FUNCTION,"CriaTopicoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"RenomearTopicoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"CriaZipDinamic");
  // Registra fun��es para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  /**************** ajax ****************/

  $cod_ferramenta = $_GET['cod_ferramenta'];
  include("../topo_tela.php");

  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);

  //adicionar as acoes possiveis, 1o parametro é
  $feedbackObject->addAction("criarTopico", 127, 0);
  $feedbackObject->addAction("apagarItem", 128, 0);
  $feedbackObject->addAction("apagarSelecionados", 128, 0);
  $feedbackObject->addAction("moveritem", 130, 0);
  $feedbackObject->addAction("movertopico", 130, 0);
  $feedbackObject->addAction("importarItem", 148, 113);

  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda = 1;

  /* Registrando codigo da ferramenta nas variaveis de sessao
     necessario para saber qual ferramenta esta sendo
     utilizada, ja que este arquivo faz parte de quatro
     ferramentas quase distintas.
   */

  if (session_is_registered("cod_ferramenta_m") && (isset($cod_ferramenta)))
  {
    if ($cod_ferramenta_m != $cod_ferramenta) //mudou de ferramenta
    {
      $cod_ferramenta_m = $cod_ferramenta;

    }
  }
  else //primeira vez que acessa alguma ferramenta que utiliza esse arquivo (material.php)
  {
    if (isset($cod_ferramenta)) {

      session_register("cod_ferramenta_m");
      $cod_ferramenta_m  = $cod_ferramenta;
    }else
      $cod_ferramenta = $cod_ferramenta_m;
  }

  if ($cod_ferramenta==3){
    include("avaliacoes_material.inc");
  }

  /* Necess�rio para a lixeira. */
  session_register("cod_topico_s");
  unset($cod_topico_s);

  Desconectar($sock);
  $sock=Conectar("");

  $diretorio_arquivos=RetornaDiretorio($sock,'Arquivos');
  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');
  $diretorio_raiz=RetornaDiretorio($sock,'raiz_www');

  Desconectar($sock);

  $sock = Conectar($cod_curso);

  $AcessoAvaliacaoM = TestaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,22);

  switch ($cod_ferramenta) {
    case 3 :
      $tabela="Atividade";
      $dir="atividades";
      $nome_ferramenta="Atividades";
      break;
    case 4 :
      $tabela="Apoio";
      $dir="apoio";
      $nome_ferramenta="Material_de_apoio";
      break;
    case 5 :
      $tabela="Leitura";
      $dir="leituras";
      $nome_ferramenta="Leituras";
      break;
    case 7 :
      $tabela="Obrigatoria";
      $dir="obrigatoria";
      $nome_ferramenta="Parada_obrigatoria";
      break;
  }

  $data_acesso=PenultimoAcesso($sock,$cod_usuario,"");
  if (!isset($cod_topico_raiz))
    $cod_topico_raiz=1;

  $eformador = EFormador($sock,$cod_curso,$cod_usuario);

  $dir_tmp_ferramenta = $diretorio_arquivos.'/'.$cod_curso.'/'.$dir.'/tmp';
  
  if (!file_exists($dir_tmp_ferramenta)) mkdir($dir_tmp_ferramenta);
  
    /**************** ajax ****************/

  echo("    <script type=\"text/javascript\" language=\"javascript\">\n");
  echo("        function redirecionaDownloadAnexos(url){\n");
  echo("            window.location=url;\n");
  echo("        }\n");
  echo("    </script>\n");
  if($eformador){

    /**************** ajax ****************/

    /* Fun��es JavaScript */
    echo("    <script type=\"text/javascript\" src=\"../js-css/sorttable.js\"></script>\n");
    echo("    <script type=\"text/javascript\" language=\"javascript\" src='../bibliotecas/dhtmllib.js'></script>\n");
    echo("    <script type=\"text/javascript\" language=\"javascript\" src='../js-css/tablednd.js'></script>\n");
    echo("    <script type=\"text/javascript\" language=\"javascript\">\n");
    echo("      var qtosChecados = 0;\n");
    echo("      var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
    echo("      var isMinNS6 = ((navigator.userAgent.indexOf(\"Gecko\") != -1) && (isNav));\n");
    echo("      var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");
    echo("      var Xpos, Ypos;\n");
    echo("      var js_cod_item, js_cod_topico;\n");
    echo("      var js_nome_topico;\n");
    echo("      var js_tipo_item;\n");
    echo("      var js_comp = new Array();\n");
    echo("      var array_itens;\n");
    echo("      var array_topicos;\n");
    echo("      var table;\n");
    echo("      var tableDnD;\n\n");
    echo("      if (isNav){\n");
    echo("        document.captureEvents(Event.MOUSEMOVE);\n");
    echo("      }\n");
    echo("      function getPageScrollY()\n");
    echo("      {\n");
    echo("        if (isNav)\n");
    echo("          return(window.pageYOffset);\n");
    echo("        if (isIE){\n");
    echo("          if(document.documentElement.scrollLeft>=0){\n");
    echo("            return document.documentElement.scrollTop;\n");
    echo("          }else if(document.body.scrollLeft>=0){\n");
    echo("            return document.body.scrollTop;\n");
    echo("          }else{\n");
    echo("            return window.pageYOffset;\n");
    echo("          }\n");
    echo("        }\n");
    echo("      }\n");

    echo("      function AjustePosMenuIE()\n");
    echo("      {\n");
    echo("        if (isIE)\n");
    echo("          return(getPageScrollY());\n");
    echo("        else\n");
    echo("          return(0);\n");
    echo("      }\n");

    echo("      function Iniciar()\n");
    echo("      {\n"); 
    echo("        cod_comp = getLayer(\"comp\");\n");
    echo("        cod_novoitem = getLayer(\"novoitem\");\n");
    echo("        cod_novo_top = getLayer(\"novotop\");\n");
    echo("        cod_mover_selec = getLayer(\"mover_selec\");\n");
    echo("        lay_topicos = getLayer(\"topicos\");\n");
    echo("        EscondeLayers();\n");
    echo("        tableDnD = new TableDnD();\n");
    echo("        table = document.getElementById('tab_interna');\n");
    echo("        if(table) tableDnD.init(table);\n");
    echo("        startList();\n");
    echo("        var atualizacao = '".$_GET['atualizacao']."';\n");

    $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
    echo("      }\n\n");

    echo("      function EscondeLayers()\n");
    echo("      {\n");
    echo("        hideLayer(cod_comp);\n");
    echo("        hideLayer(cod_novoitem);\n"); 
    echo("        hideLayer(cod_novo_top);\n");
    echo("        hideLayer(lay_topicos);\n");
    echo("      }\n");

    echo("      function AtualizaComp(js_tipo_comp) {\n");
    echo("        var tipo_comp;\n");
    echo("        if ((isNav) && (!isMinNS6)) {\n");
    echo("          document.comp.document.form_comp.tipo_comp.value=js_tipo_comp;\n");
    echo("          document.comp.document.form_comp.cod_item.value=js_cod_item;\n");
    echo("          tipo_comp = new Array(document.comp.document.getElementById('tipo_comp_T'), document.comp.document.getElementById('tipo_comp_F'));\n");
    echo("        } else {\n");
    echo("          document.form_comp.tipo_comp.value=js_tipo_comp;\n");
    echo("          document.form_comp.cod_item.value=js_cod_item;\n");
    echo("          tipo_comp = new Array(document.getElementById('tipo_comp_T'), document.getElementById('tipo_comp_F'));\n");
    echo("         }\n");
    echo("        var imagem=\"<img src='../imgs/checkmark_blue.gif' />\"\n");
    echo("        if (js_tipo_comp=='T') {\n");
    echo("          tipo_comp[0].innerHTML=imagem;\n");
    echo("          tipo_comp[1].innerHTML=\"&nbsp;\";\n");
    echo("        } else if (js_tipo_comp=='F') {\n");
    echo("          tipo_comp[0].innerHTML=\"&nbsp;\";\n");
    echo("          tipo_comp[1].innerHTML=imagem;\n");
    echo("        }\n");
    echo("      }\n");

    echo("      function MostraLayer(cod_layer, ajuste, ev){\n");
    echo("        EscondeLayers();\n");
    echo("        ev = ev || window.event;\n");
    echo("        if(ev.pageX || ev.pageY){\n");
    echo("          Xpos = ev.pageX;\n");
    echo("          Ypos = ev.pageY;\n");
    echo("        }else{\n");
    echo("          Xpos = ev.clientX + document.body.scrollLeft - document.body.clientLeft;\n");
    echo("          Ypos = ev.clientY + getPageScrollY();\n");
    echo("        }\n");
    echo("        moveLayerTo(cod_layer,Xpos-100,Ypos);\n");
    echo("        showLayer(cod_layer);\n");
    echo("      }\n\n");

    echo("      function EscondeLayer(cod_layer) {\n");
    echo("        hideLayer(cod_layer);\n");
    echo("      }\n");

    echo("      function VerificaCheck(){\n");
    echo("        var i;\n");
    echo("        var j=0;\n");
    echo("        var k=0;\n");
    echo("        var cod_itens=document.getElementsByName('chkItem');\n");
    echo("        var cod_topicos=document.getElementsByName('chkTopico');\n");
    echo("        var Cabecalho = document.getElementById('checkMenu');\n");
    echo("        array_itens = new Array();\n");
    echo("        array_topicos = new Array();\n");
    echo("        for (i=0; i<cod_itens.length; i++){\n");
    echo("          if (cod_itens[i].checked){\n");
    echo("            var item = cod_itens[i].id.split('_');\n");
    echo("            array_itens[j]=item[1];\n");
    echo("            j++;\n");
    echo("          }\n");
    echo("        }\n");
    echo("        for (i=0; i<cod_topicos.length; i++){\n");
    echo("          if (cod_topicos[i].checked){\n");
    echo("            topico = cod_topicos[i].id.split('_');\n");
    echo("            array_topicos[k]=topico[1];\n");
    echo("            k++;\n");
    echo("          }\n");
    echo("        }\n");
    echo("        if ((k+j)==(cod_topicos.length+cod_itens.length)) Cabecalho.checked=true;\n");
    echo("        else Cabecalho.checked=false;\n");
    echo("        if((k+j)>0){\n");
    echo("          document.getElementById('mExcluir_Selec').className=\"menuUp02\";\n");
    echo("          document.getElementById('mMover_Selec').className=\"menuUp02\";\n");
    echo("          document.getElementById('mExcluir_Selec').onclick=function(){ ExcluirSelecionados(); };\n");
    echo("          document.getElementById('mMover_Selec').onclick = function(event) { MostraLayer(cod_mover_selec,0, event); };\n");
    echo("        }else{\n");
    echo("          document.getElementById('mExcluir_Selec').className=\"menuUp\";\n");
    echo("          document.getElementById('mMover_Selec').className=\"menuUp\";\n");
    echo("          document.getElementById('mExcluir_Selec').onclick=function(){  };\n");
    echo("          document.getElementById('mMover_Selec').onclick=function(){  };\n");
    echo("        }\n");
    echo("      }\n\n");

    echo("      function CheckTodos(){\n");
    echo("        var e;\n");
    echo("        var i;\n");
    echo("        var CabMarcado = document.getElementById('checkMenu').checked;\n");
    echo("        var cod_itens=document.getElementsByName('chkItem');\n");
    echo("        var cod_topicos=document.getElementsByName('chkTopico');\n");
    echo("        for(i = 0; i < cod_itens.length; i++){\n");
    echo("          e = cod_itens[i];\n");
    echo("          e.checked = CabMarcado;\n");
    echo("        }\n");
    echo("        for(i = 0; i < cod_topicos.length; i++){\n");
    echo("          e = cod_topicos[i];\n");
    echo("          e.checked = CabMarcado;\n");
    echo("        }\n");
    echo("        VerificaCheck();\n");
    echo("      }\n\n");

    echo("      function ExcluirSelecionados(){\n");

    echo("        document.getElementById('cod_topicos_form').value=array_topicos;\n");
    echo("        document.getElementById('cod_itens_form').value=array_itens;\n");
    echo("        if ((array_topicos.length + array_itens.length) > 1){\n");
    /* 115 - Voc� tem certeza de que deseja apagar as atividades selecionadas? */
    /* 117 - (as atividades ser?o movidas para a lixeira e se houver alguma avalia??o relacionada, as avalia??es tamb?m ser?o movidas para a lixeira DAS AVALIA??ES) */
    if ($cod_ferramenta==3){ 
      $numero = 117;
    }else{
      $numero = 7;
    }
    echo("          if (confirm(\"".RetornaFraseDaLista($lista_frases,115)."\\n".RetornaFraseDaLista($lista_frases,$numero)."\")){\n");
    echo("            document.form_dados.action='acoes.php';\n");
    echo("            document.form_dados.method='POST';\n");
    echo("            document.getElementById('acao_form').value='apagarSelecionados';\n");
    echo("            document.form_dados.submit();\n");
    echo("          }\n");
    echo("        }else{\n");
    /* 6 - Voce tem certeza de que deseja apagar esta atividade? */
    $msg_confirmacao = RetornaFraseDaLista($lista_frases,6);
    if ($cod_ferramenta==3){
      /* 101 - (a atividade sera movida para a lixeira e se houver alguma avaliacao relacionada, a avaliacao tambem sera movida para a lixeira DAS AVALIACOES) */
      $msg_confirmacao.= "\\n".RetornaFraseDaLista($lista_frases,101);
    }else{
      /* 7 (a 'ferramenta' sera movida para a lixeira) */
      $msg_confirmacao.= "\\n".RetornaFraseDaLista($lista_frases,7);
    }
    echo("          if (confirm(\"".$msg_confirmacao."\")){\n");
    echo("            document.form_dados.action='acoes.php';\n");
    echo("            document.form_dados.method='POST';\n");
    echo("            document.getElementById('acao_form').value='apagarSelecionados';\n");
    echo("            document.form_dados.submit();\n");
    echo("          }\n");
    echo("        }\n");

    echo("      }\n");

    echo("      function Redirecionar(cod_topico_raiz){\n");
    echo("         window.location='material.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&cod_topico_raiz='+cod_topico_raiz+'&cod_ferramenta=".$cod_ferramenta."&acao=movertopico&atualizacao=true';\n");
    echo("      }\n\n");

    echo("      function MoverSelecionados(topico_destino){\n");
    echo("        xajax_MoverItensDinamic('".$tabela."', '".$cod_curso."', '".$cod_ferramenta."', '".$cod_usuario."', '".$cod_topico_raiz."', topico_destino, array_topicos, array_itens, '".RetornaFraseDaLista($lista_frases, 2)."', '".RetornaFraseDaLista($lista_frases,56)."');\n");
    echo("      }\n\n");

    echo("      function SoltaMouse(ids){\n");
    echo("        xajax_AtualizaPosicoes('".$cod_curso."', '".$cod_usuario."', '".$cod_topico_raiz."', ids, '".$tabela."', '".RetornaFraseDaLista($lista_frases,145)."','".RetornaFraseDaLista($lista_frases,147)."');\n");
    echo("      }\n");

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
    echo ("             EdicaoNomePasta('ok');\n"); //A função e parâmetros são os mesmos utilizados na função de edição já utilizada.
    echo ("         }\n\n");
    echo ("         return true;\n");
    echo ("     }\n\n");


    echo("      function AlterarNomePasta(){\n");
    echo("        id = 'nome_topico_atual';\n");
    echo("        id_aux = id;\n");
    echo("        document.getElementById(id).onclick = function() { };\n");
    echo("        var nome_topico_atual = document.getElementById(id).innerHTML;\n");
    echo("        createInput = document.createElement('input');\n");
    echo("        document.getElementById(id).innerHTML = '';\n");
    echo("        document.getElementById(id).style.fontWeight = '';\n");
    echo("        createInput.setAttribute('type', 'text');\n");
    echo("        createInput.setAttribute('style', 'border: 2px solid #9bc');\n");
    echo("        createInput.setAttribute('id', 'tit_'+id+'_text');\n\n");
    echo("        createInput.onkeypress = function(event) {EditaTituloEnter(this, event, id_aux);}\n\n");

    echo("        document.getElementById(id).appendChild(createInput);\n");
    echo("        xajax_DecodificaString('tit_'+id+'_text', nome_topico_atual, 'value');\n");

    echo("        //cria o elemento 'espaco' e adiciona na pagina\n");
    echo("        espaco = document.createElement('span');\n");
    echo("        espaco.innerHTML='&nbsp;&nbsp;'\n");
    echo("        document.getElementById(id).appendChild(espaco);\n\n");

    echo("        createSpan = document.createElement('span');\n");
    echo("        createSpan.className='link';\n");
    echo("        createSpan.onclick= function(){ EdicaoNomePasta('ok', nome_topico_atual); };\n");
    echo("        createSpan.setAttribute('id', 'OkEdita');\n");
    echo("        createSpan.innerHTML='".RetornaFraseDaLista($lista_frases_geral,18)."';\n");
    echo("        document.getElementById(id).appendChild(createSpan);\n\n");

    echo("        //cria o elemento 'espaco' e adiciona na pagina\n");
    echo("        espaco = document.createElement('span');\n");
    echo("        espaco.innerHTML='&nbsp;&nbsp;'\n");
    echo("        document.getElementById(id).appendChild(espaco);\n");

    echo("        createSpan = document.createElement('span');\n");
    echo("        createSpan.className='link';\n");
    echo("        createSpan.onclick= function(){ EdicaoNomePasta('canc', nome_topico_atual); };\n");
    echo("        createSpan.setAttribute('id', 'CancelaEdita');\n");
    echo("        createSpan.innerHTML='".RetornaFraseDaLista($lista_frases_geral,2)."';\n");
    echo("        document.getElementById(id).appendChild(createSpan);\n\n");

    echo("        //cria o elemento 'espaco' e adiciona na pagina\n");
    echo("        espaco = document.createElement('span');\n");
    echo("        espaco.innerHTML='&nbsp;&nbsp;'\n");
    echo("        document.getElementById(id).appendChild(espaco);\n");

    echo("        startList();\n");
    echo("        cancelarElemento=document.getElementById('CancelaEdita');\n");
    echo("        document.getElementById('tit_'+id+'_text').select();\n");
    echo("      }\n\n");

    echo("      function EdicaoNomePasta(valor, nome_topico_atual){\n");
    echo("        var novoNome = document.getElementById('tit_nome_topico_atual_text').value\n");
    echo("        if ((valor=='ok')&&(document.getElementById('tit_nome_topico_atual_text').value!=\"\")){\n");
    echo("          if( !(novoNome.indexOf(\"\\\\\")>=0 || novoNome.indexOf(\"\\\"\")>=0 || novoNome.indexOf(\"'\")>=0 || novoNome.indexOf(\">\")>=0 || novoNome.indexOf(\"<\")>=0) ) {\n");
    echo("            xajax_RenomearTopicoDinamic('".$cod_curso."', '".$cod_topico_raiz."', '".$cod_usuario."','".$tabela."', document.getElementById('tit_nome_topico_atual_text').value, '".RetornaFraseDaLista($lista_frases_geral,74)."', id, '".RetornaFraseDaLista($lista_frases,139)."');\n");
    echo("          }else{\n");
    /* 77 - O titulo do item nao pode conter \\\", \\\', < ou >. */
    echo("            alert(\"".ConverteAspas2BarraAspas(ConverteHtml2Aspas(RetornaFraseDaLista($lista_frases,77)))."\");\n");
    echo("            document.getElementById('tit_nome_topico_atual_text').value = nome_topico_atual;\n");
    echo("            document.getElementById('tit_nome_topico_atual_text').focus();\n");
    echo("            return false\n");
    echo("          }\n");
    echo("        }else{\n");
    echo("          if (valor=='ok'){\n");
    /* 76 - O titulo nao pode ser vazio. */
    echo("              alert('".RetornaFraseDaLista($lista_frases,76)."');\n");
    echo("          }\n");
    echo("          document.getElementById(id).innerHTML=nome_topico_atual;\n");
    echo("          document.getElementById('nome_topico_atual').innerHTML=nome_topico_atual;\n");
    echo("        }\n");
    echo("        document.getElementById('nome_topico_atual').style.fontWeight='bold';\n");
    echo("      }\n");

    echo("      function VerificaNovoItemTitulo(textbox) {\n");
    echo("        texto=textbox.value;\n");
    echo("        if (texto==''){\n");
    echo("          // se nome for vazio, nao pode\n");
                    /* 76 - O titulo nao pode ser vazio. */
    echo("          alert(\"".RetornaFraseDaLista($lista_frases,76)."\");\n");
    echo("          textbox.focus();\n");
    echo("          return false;\n");
    echo("        }\n");
    echo("        // se nome tiver aspas, <, >, nao pode - aspas pode ser 1,0\n");
    echo("        else if (texto.indexOf(\"\\\\\")>=0 || texto.indexOf(\"\\\"\")>=0 || texto.indexOf(\"'\")>=0 || texto.indexOf(\">\")>=0 || texto.indexOf(\"<\")>=0) {\n");
                  /* 77 - O titulo do item nao pode conter \\\", \\\', < ou >. */
    echo("          alert(\"".ConverteAspas2BarraAspas(ConverteHtml2Aspas(RetornaFraseDaLista($lista_frases,77)))."\");\n");
    echo("          textbox.value='';\n");
    echo("          textbox.focus();\n");
    echo("          return false;\n");
    echo("        }\n");
    echo("        return true;\n");
    echo("      }\n\n");  

    echo("      function VerificaNovoItemTopico(textbox){\n");
    echo("        texto=textbox.value;\n");
    echo("        if (texto==''){\n");
    echo("          // se nome for vazio, nao pode\n");
                    /* 76 - O titulo nao pode ser vazio. */
    echo("          alert(\"".RetornaFraseDaLista($lista_frases,76)."\");\n");
    echo("          textbox.focus();\n");
    echo("          return false;\n");
    echo("        }\n");
    echo("        // se nome tiver aspas, <, >, nao pode - aspas pode ser 1,0\n");
    echo("        else if (texto.indexOf(\"\\\\\")>=0 || texto.indexOf(\"\\\"\")>=0 || texto.indexOf(\"'\")>=0 || texto.indexOf(\">\")>=0 || texto.indexOf(\"<\")>=0) {\n");
                  /* 77 - O titulo do item nao pode conter \\\", \\\', < ou >. */
    echo("          alert(\"".ConverteAspas2BarraAspas(ConverteHtml2Aspas(RetornaFraseDaLista($lista_frases,77)))."\");\n");
    echo("          textbox.value='';\n");
    echo("          textbox.focus();\n");
    echo("          return false;\n");
    echo("        }\n");
    echo("        xajax_CriaTopicoDinamic('".$cod_curso."', '".$cod_usuario."', '".$cod_ferramenta."', '".$tabela."', ".$cod_topico_raiz.", '".$dir."', texto, '".RetornaFraseDaLista($lista_frases_geral, 73)."');\n");
    echo("        return false;\n");
    echo("      }\n\n");

    if(($cod_ferramenta==3) && ($AcessoAvaliacaoM)) {
      echo("      function VerAvaliacao(id) {\n");
      echo("         window.open('../avaliacoes/ver_popup.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&VeioDaAtividade=1&origem=../material/material&cod_topico=".$cod_topico_raiz."&cod_avaliacao='+id,'VerAvaliacao','width=450,height=300,top=150,left=250,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
      echo("        return(false);\n");
      echo("      }\n");
    }
    echo("\n");
  }//if = eh formador
  else{//n�o � formador
    echo("  <script type=\"text/javascript\" src=\"../js-css/sorttable.js\"></script>\n");
    echo("    <script type=\"text/javascript\" language=\"javascript\">\n");
    echo("      function Iniciar(){\n");
    echo("        startList();\n");
    echo("      }\n");
  }

  echo("    </script>\n");

  $objAjax->printJavascript();

  include("../menu_principal.php");

  $cod_pagina=1;
  if(($cod_ferramenta==3)&&($AcessoAvaliacaoM)&&($eformador))/*verifi��o se aparecer� ajuda de avalia��es*/
    $cod_pagina=6;


  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

      /* Pagina Principal */
      /* 1 - 3: Atividades
            4: Material de Apoio
            5: Leituras
            7: Parada Obrigatoria
      */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,1)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  $lista_topicos_ancestrais=RetornaTopicosAncestrais($sock, $tabela, $cod_topico_raiz);
  unset($path);

  foreach ($lista_topicos_ancestrais as $cod => $linha){
      if ($cod_topico_raiz!=$linha['cod_topico'])
      {
        $path="<a class=\"text\" href=\"material.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;cod_topico_raiz=".$linha['cod_topico']."\">".$linha['topico']."</a> &gt;&gt; ".$path;
    }
    else{
      $path="<span style=\"font-weight:bold;\" id=\"nome_topico_atual\">".$linha['topico']."</span><br />\n";
    }
  }

  if($eformador){
    echo("          <span class=\"btsNav2\" onclick=\"MostraLayer(lay_topicos,0, event);return(false);\"><img src=\"../imgs/estrutura.gif\" border=\"0\" alt=\"\"/></span>");
    echo("          <span class=\"btsNav2\">".$path."</span>");
  }
  else{
    echo("      <span class=\"btsNav2\"><img src=\"../imgs/estrutura.gif\" border=\"0\" alt=\"\"/></span>");
    echo("      <span class=\"btsNav2\">".$path."</span>");
  }
  echo("          <form name=\"frmMaterial\" method=\"post\" action=\"\">\n");
  echo("            <input type=\"hidden\" name=\"ItensSelecionados\" value='' />\n");
  echo("            <input type=\"hidden\" name=\"TopSelecionados\" value='' />\n");
  echo("            <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");

  if($eformador){
    echo("              <tr>\n");
    echo("              <!-- Botoes de Acao -->\n");
    echo("                <td class=\"btAuxTabs\">\n");
    echo("                  <ul class=\"btAuxTabs\">\n");

      /* 8 - 3: Nova Atividade
              4: Novo Material de Apoio
              5: Nova Leitura
              7: Nova Parada Obrigat�ria
        */
    echo("                    <li><span onclick=\"MostraLayer(cod_novoitem, 150, event); document.getElementById('nome_novo_item').focus();document.getElementById('nome_novo_item').value='';\">".RetornaFraseDaLista($lista_frases,8)."</span></li>\n");
		/* 105 - 3: Importar Atividade
                4: Importar Material de Apoio
                5: Importar Leitura
                7: Importar Parada Obrigat�ria
        */
    if(($cod_topico_raiz < 2))
      echo("                    <li><a href=\"importar_curso.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_ferramenta=".$cod_ferramenta."\">".RetornaFraseDaLista($lista_frases,105)."</a></li>\n");

        /* 9 - Nova Pasta */
    echo("                    <li><span onclick=\"MostraLayer(cod_novo_top,150, event);document.getElementById('nome_novo_topico').focus();document.getElementById('nome_novo_topico').value='';\" title=\"".RetornaFraseDaLista($lista_frases,9)."\">".RetornaFraseDaLista($lista_frases,9)."</span></li>\n");

    if(($eformador) && ($cod_topico_raiz > 2)){
      // 118 - Renomear Pasta
      echo("                    <li><span onclick=\"AlterarNomePasta();\" >".RetornaFraseDaLista($lista_frases,118)."</span></li>\n");
    }

    /* 11 - Lixeira */
    echo("                    <li><a href=\"lixeira.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;cod_topico=".$cod_topico_raiz."\">".RetornaFraseDaLista($lista_frases,11)."</a></li>\n");
    echo("                  </ul>\n");
    echo("                </td>\n");
    echo("              </tr>\n");
  }

  echo("              <tr>\n");
  echo("                <td valign=\"top\">\n");
  echo("                  <table cellpadding=\"0\" cellspacing=\"0\" id=\"tab_interna\" class=\"sortable tabInterna\">\n");  

  $lista_topicos=RetornaTopicosDoTopico($sock, $tabela, $cod_topico_raiz);
  $lista_itens=RetornaItensDoTopico($sock, $tabela, $cod_topico_raiz);
  
  echo("                    <tr class=\"head\">\n");
  
  
  if($eformador){
    echo("                      <td width=\"2%\" class=\"sorttable_nosort\"><input type=\"checkbox\" id=\"checkMenu\" onclick=\"CheckTodos();\" /></td>\n");
  }
  /* 12 - 3: Atividade
            4: Material de Apoio
            5: Leitura
            7: Parada Obrigatoria
    */

  echo("                      <td class=\"alLeft\" style=\"cursor:pointer\">".RetornaFraseDaLista($lista_frases,12)."</td>\n");
  /* 13 - Data */
  echo("                      <td width=\"10%\" style=\"cursor:pointer\">".RetornaFraseDaLista($lista_frases,13)."</td>\n");

  if($eformador){
    /* 14 - Compartilhar */
    echo("                      <td width=\"20%\" style=\"cursor:pointer\">".RetornaFraseDaLista($lista_frases,14)."</td>\n");

    if(($cod_ferramenta==3)&&($AcessoAvaliacaoM)){
      /* 90 - Avaliacao */
      echo("                      <td width=\"10%\" style=\"cursor:pointer\">".RetornaFraseDaLista($lista_frases,90)."</td>\n");
    }

  }

  echo("                    </tr>\n");

 // echo("                  </table>\n");
 // echo("                  <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\" id=\"tab_interna\">\n");

  $query = "";

  if ((empty($lista_topicos)) && ((empty($lista_itens) || (!ExistemItensVisiveis($sock, $tabela, $cod_topico_raiz, $eformador)))))
  {
    echo("                    <tr>\n");
    /* 15 - 3: Nao ha nenhuma atividade
              4: Nao ha nenhum material de apoio
              5: Nao ha nenhuma leitura
              7: Nao ha nenhuma parada obrigat�ria
      */
    echo("                      <td colspan=\"5\" align=\"center\">".RetornaFraseDaLista($lista_frases,15)."</td>\n");
    echo("                    </tr>\n");
  }
  else
  {

    $top_index = 0;
    $itens_index = 0;
    for($i=0; $i<((count($lista_topicos))+(count($lista_itens))); $i++){
      if((!isset($lista_topicos[$top_index]['posicao_topico'])) || (isset($lista_itens[$itens_index]['posicao_item']) &&($lista_topicos[$top_index]['posicao_topico'] > $lista_itens[$itens_index]['posicao_item']))) {
        $lista_unificada[$i] = $lista_itens[$itens_index];
        $itens_index++;
      }else{
        //este if � para n�o alterar a estrutura dos portf�lios antigos
        if((isset($lista_itens[$top_index]['posicao_item'])) && ($lista_topicos[$top_index]['posicao_topico'] == $lista_itens[$itens_index]['posicao_item'])) {
          $lista_itens[$itens_index]['posicao_item']++;
        }
        $lista_unificada[$i] = $lista_topicos[$top_index];
        $top_index++;
      }
    }
    foreach($lista_unificada as $cod => $linha){
      //se � t�pico...
      if(isset($linha['posicao_topico'])){

        $data=UnixTime2Data($linha['data']);
        $max_data=RetornaMaiorData($sock,$tabela,$linha['cod_topico'],'F',$linha['data']);
        if ($data_acesso<$max_data)
        {
          $marcatr=" class=\"novoitem\"";
        }
        else
        {
          $marcatr="";
        }

        echo("                    <tr".$marcatr." id=\"tr_top_".$linha['cod_topico']."\">\n");
        if($eformador){
          echo("                      <td width=\"2%\">\n");
          echo("                        <input type=\"checkbox\" id=\"chktop_".$linha['cod_topico']."\" name=\"chkTopico\" value=\"".$linha['cod_topico']."\" onclick=\"VerificaCheck()\" />\n");
          echo("                      </td>\n");
          echo("                      <td class=\"alLeft\"><img src=\"../imgs/pasta.gif\" border=\"0\" alt=\"\" />&nbsp;&nbsp;<a href=\"material.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;cod_topico_raiz=".$linha['cod_topico']."\">".$linha['topico']."</a></td>\n");
          echo("                      <td width=\"10%\" align=\"center\">".$data."</td>\n"); //dani
          echo("                      <td width=\"20%\">&nbsp;</td>\n");
          if (($cod_ferramenta==3) && ($AcessoAvaliacaoM))
            echo("                      <td width=\"8%\">&nbsp;</td>\n");
        }else{

          echo("                      </td>\n");
          if($linha['tipo_compartilhamento']=="T"){
            echo("                      <td class=\"alLeft\"><img src=\"../imgs/pasta.gif\" border=0 alt=\"\"/>&nbsp;&nbsp;<a href=\"material.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;cod_topico_raiz=".$linha['cod_topico']."\">".$linha['topico']."</span></td>\n");
            echo("                      <td width=\"10%\" align=\"center\">".$data."</td>\n"); //dani
          }
        }
        echo("                    </tr>\n");
      } //� item

      else if( isset($linha['posicao_item'])){ 

        $data=UnixTime2Data($linha['data']);
          if ($linha['tipo_compartilhamento']=="T")
          {
            /* 16 - Totalmente Compartilhado */
            $compartilhamento=RetornaFraseDaLista($lista_frases,16);
          }
          else
          {
            /* 17 - Compartilhado com Formadores */
            $compartilhamento=RetornaFraseDaLista($lista_frases,17);
          }
          if ($data_acesso<$linha['data'])
          {
            $marcatr=" class=\"novoitem\"";
          }
          else
          {
            $marcatr="";
          }
          if($eformador){
              if ($linha['status']=="E") {
                $linha_historico=RetornaUltimaPosicaoHistorico($sock, $tabela, $linha['cod_item']);
                if ($linha['inicio_edicao']<(time()-1800) || $cod_usuario==$linha_historico['cod_usuario'])
                {
                  CancelaEdicao($sock, $tabela, $dir, $linha['cod_item'], $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp,$criacao_avaliacao);

                  $compartilhamento="<span id=\"comp_".$linha['cod_item']."\" class=\"link\" onclick=\"js_cod_item='".$linha['cod_item']."';AtualizaComp('".$linha['tipo_compartilhamento']."');MostraLayer(cod_comp,140, event);\">".$compartilhamento."</span>";

                  $titulo="<img src=\"../imgs/arqp.gif\" border=\"0\" alt=\"\" />&nbsp;&nbsp;<a href=\"ver.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;cod_item=".$linha['cod_item']."&amp;cod_topico_raiz=".$cod_topico_raiz."\">".$linha['titulo']."</a>";
                }
                else
                {
                  /* 18 - Em Edicao */
                  $data="<span class=\"link\" onclick=\"window.open('em_edicao.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;cod_item=".$linha['cod_item']."&amp;origem=material&amp;cod_ferramenta=".$cod_ferramenta."','EmEdicao','width=300,height=220,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\">".RetornaFraseDaLista($lista_frases,18)."</a>";
                  $compartilhamento=$compartilhamento;
                  $titulo="<img src=\"../imgs/arqp.gif\" border=\"0\" alt=\"\" />&nbsp;&nbsp;".$linha['titulo'];
                }
              }
              else
              {
                $compartilhamento="<span id=\"comp_".$linha['cod_item']."\" class=\"link\" onclick=\"js_cod_item='".$linha['cod_item']."';AtualizaComp('".$linha['tipo_compartilhamento']."');MostraLayer(cod_comp,140,event);\">".$compartilhamento."</span>";
                $titulo="<img src=\"../imgs/arqp.gif\" border=\"0\" alt=\"\" />&nbsp;&nbsp;<a href=\"ver.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;cod_item=".$linha['cod_item']."&amp;cod_topico_raiz=".$cod_topico_raiz."\">".$linha['titulo']."</a>";
              }

            echo("                    <tr".$marcatr." id=\"tr_".$linha['cod_item']."\">\n");
            echo("                      <td width=\"2%\"><input type=\"checkbox\" id=\"chkitm_".$linha['cod_item']."\" name=\"chkItem\" value=\"".$linha['cod_item']."\" onclick=\"VerificaCheck()\" /></td>\n");
            echo("                      <td class=\"alLeft\">".$titulo."</td>\n");
            echo("                      <td width=\"10%\" align=\"center\"><span id=\"data_".$linha['cod_item']."\">".$data."</span></td>\n");
            echo("                      <td width=\"20%\" align=\"center\">".$compartilhamento."</td>\n");

            if (($cod_ferramenta==3)&&($AcessoAvaliacaoM))
            {
              if (AtividadeEhAvaliacao($sock,$linha['cod_item']))
              {
                $cod_avaliacao=RetornaCodAvaliacao($sock,$linha['cod_item']);
                /* 35 - Sim (ger)*/
                echo("                      <td width=\"10%\" align=\"center\"><span class='link' onclick='VerAvaliacao(".$cod_avaliacao.");return(false);'>".RetornaFraseDaLista($lista_frases_geral,35)."</span>");
              }
              else
              /* 36 -  Nao (ger)*/
                echo("                      <td width=\"10%\" align=\"center\">".RetornaFraseDaLista($lista_frases_geral,36)."</td>\n");
            }
            echo("                    </tr>\n");
          }else if (($linha['status']!="E") && ($linha['tipo_compartilhamento']=="T")) {
            $titulo = "<img src=\"../imgs/arqp.gif\" border=\"0\" alt=\"\" />&nbsp;&nbsp;<a class=\"link\" href=\"ver.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;cod_item=".$linha['cod_item']."&amp;cod_topico_raiz=".$cod_topico_raiz."\">".$linha['titulo']."</a>";

            echo("                    <tr".$marcatr." id=\"tr_".$linha['cod_item']."\">\n");
            echo("                      <td class=\"alLeft\">".$titulo."</td>\n");
            echo("                      <td width=\"10%\" align=\"center\"><span id=\"data_".$linha['cod_item']."\">".$data."</span></td>\n");
            echo("                    </tr>\n");
          }
        } //else
      }
    } // else - count(lista_topicos), count(lista_itens)
    echo("                  </table>\n");   // table.class = tabInterna
    echo("                </td>\n");
    echo("              </tr>\n");
    echo("            </table>\n"); // table.class = tabExterna    
    echo("          </form>\n");
    if($eformador){
      echo("          <ul>\n");
      /* 68 - Excluir selecionados (gen) */
      echo("            <li id=\"mExcluir_Selec\" class=\"menuUp\"><span id=\"excluirSelec\">".RetornaFraseDaLista($lista_frases_geral,68)."</span></li>\n");
      /* 69 - Mover selecionados (gen) */
      echo("            <li id=\"mMover_Selec\" class=\"menuUp\"><span id=\"moverSelec\">".RetornaFraseDaLista($lista_frases_geral,69)."</span></li>\n");
      echo("          </ul>\n");
    }
    // testa se � raiz e se tem itens para habilitar o download de todos os anexos
    if(($cod_topico_raiz < 2) && !((empty($lista_topicos)) && ((empty($lista_itens) || (!ExistemItensVisiveis($sock, $tabela, $cod_topico_raiz, $eformador)))))){
      echo("                <div id=\"downloadAnexos\">");
      echo("                  <ul class=\"btAuxTabs\">\n");
      /* 151 - Baixar todos os anexos */
      echo("                    <li><span onclick=\"xajax_CriaZipDinamic('".$cod_topico_raiz."','".$dir_tmp_ferramenta."',".$cod_curso.",".$cod_ferramenta.",'".$diretorio_arquivos."','".$tabela."','".$bibliotecas."','".$nome_ferramenta."','".$diretorio_temp."');\">".RetornaFraseDaLista($lista_frases,151)."</span></li>\n");
      echo("                  </ul>\n");
      echo("                </div>");
    }
    echo("          <br />\n");
    /* 509 - voltar, 510 - topo */
    echo("          <ul class=\"btsNavBottom\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span><span><a href=\"#topo\">&nbsp;".RetornaFraseDaLista($lista_frases_geral,510)."&nbsp;&#94;&nbsp;</a></span></li></ul>\n");
        echo("        </td>\n");
    echo("      </tr>\n");

    include("../tela2.php");

    if($eformador){
      include("layer_material.php");
    }

    echo("    <form name=\"form_dados\" action=\"\" id=\"form_dados\">\n");
    echo("      <input type=\"hidden\" name=\"cod_curso\" id=\"cod_curso\" value=\"".$cod_curso."\" />\n");
    echo("      <input type=\"hidden\" name=\"cod_topico_raiz\" value=\"".$cod_topico_raiz."\" />\n");
    echo("      <input type=\"hidden\" name=\"acao\" id=\"acao_form\" value=\"\" />\n");
    echo("      <input type=\"hidden\" name=\"cod_usuario\" value=\"".$cod_usuario."\" />\n");
    echo("      <input type=\"hidden\" name=\"cod_topicos\" id=\"cod_topicos_form\" value=\"\" />\n");
    echo("      <input type=\"hidden\" name=\"cod_itens\" id=\"cod_itens_form\" value=\"\" />\n");
    echo("    </form>\n");
    echo("  </body>\n");
    echo("</html>\n");

    Desconectar($sock);
    exit;

?>