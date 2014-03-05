<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/portfolio/portfolio.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Distância
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

    Nied - Ncleo de Informática Aplicada à Educação
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit?ia "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/portfolio/portfolio.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("portfolio.inc");
  include("avaliacoes_portfolio.inc");
  require_once("../xajax_0.5/xajax_core/xajax.inc.php");
  
  // Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  // Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do x
  $objAjax->register(XAJAX_FUNCTION,"MudarCompartilhamentoEAtualiza");
  $objAjax->register(XAJAX_FUNCTION,"MoverItensDinamic");
  $objAjax->register(XAJAX_FUNCTION,"AcabaEdicaoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"AtualizaPosicoes");
  $objAjax->register(XAJAX_FUNCTION,"DecodificaString");
  $objAjax->register(XAJAX_FUNCTION,"CriaTopicoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"RenomearTopicoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"CriaZipDinamic");
  // Registra fun��es para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();
  
  /* Necess?io para a lixeira. */
  session_register("cod_topico_s");
  unset($cod_topico_s);

  $cod_ferramenta = 15;
  $cod_ferramenta_ajuda = 15;
  $cod_pagina_ajuda = 2;
  
  // diretorios para a geracao dinamica de zip
  $sock1 = Conectar("");
  $diretorio_arquivos_dinamic=RetornaDiretorio($sock1,'Arquivos');
  $diretorio_temp_dinamic=RetornaDiretorio($sock1,'ArquivosWeb');
  Desconectar($sock1);
  
  include("../topo_tela.php");
 
  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
  $feedbackObject->addAction("apagarSelecionados", 194, 0);
  $feedbackObject->addAction("apagarItem", 194, 0);
  $feedbackObject->addAction("moverItens", 202, 0);
  $feedbackObject->addAction("criarTopico", 206, 0);

  $eformador = EFormador($sock,$cod_curso,$cod_usuario);
  $visitante = EVisitante($sock, $cod_curso, $cod_usuario);
  
  // cria o diretorio temporario da ferramenta
  $dir_tmp_ferramenta = $diretorio_arquivos_dinamic.'/'.$cod_curso.'/portfolio/tmp';
  if (!file_exists($dir_tmp_ferramenta)) mkdir($dir_tmp_ferramenta);
  $tabela_dinamic="Portfolio";
  $nome_ferramenta_dinamic="Portfolio";
  
  // verificamos se a ferramenta de Avaliacoes está disponivel
  $ferramenta_avaliacao = TestaAcessoAFerramenta($sock, $cod_curso, $cod_usuario, 22);
  /* Apaga links simbolicos que por acaso tenham sobrado daquele usuario */
  system ("rm ../../diretorio/portfolio_".$cod_curso."_*_".$cod_usuario);

  $var = $diretorio_temp."/portfolio_".$cod_curso."_*_".$cod_usuario;

  foreach (glob($var) as $filename)
  {
    if(ExisteArquivo($filename))
      (RemoveArquivo($filename));
  }

  $data_acesso=PenultimoAcesso($sock,$cod_usuario,"");

  $cod_topico_raiz_usuario=RetornaPastaRaizUsuario($sock,$cod_usuario,"");


  if (!isset($cod_topico_raiz))
  {
    if ($cod_grupo_portfolio!="" && $cod_grupo_portfolio!="NULL")
      $cod_topico_raiz=RetornaPastaRaizUsuario($sock,$cod_usuario,$cod_grupo_portfolio);
    else if ($cod_usuario_portfolio!="")
      $cod_topico_raiz=RetornaPastaRaizUsuario($sock,$cod_usuario_portfolio,"");
    else
    {

      $cod_topico_raiz=$cod_topico_raiz_usuario;
      $cod_usuario_portfolio=$cod_usuario;

      /* Checagem da existência das pastas dos grupos a que o usuário pertence */
      VerificaPortfolioGrupos($sock,$cod_usuario);

      $cod_topico_raiz_usuario=RetornaPastaRaizUsuario($sock,$cod_usuario,"");

    }

    $cod_topico_raiz=$cod_topico_raiz_usuario;
    $cod_usuario_portfolio=$cod_usuario;

    /* Checagem da existência das pastas dos grupos a que o usuário pertence */
    VerificaPortfolioGrupos($sock,$cod_usuario);


  }

  if ($cod_topico_raiz=="NULL")
    // nao ha um topico selecionado: redirecionamos o usuario para exibir os portfolios do curso
  {
    Desconectar($sock);
    header("Location:ver_portfolio.php?cod_curso=".$cod_curso);
    exit;
  }

  $status_portfolio = RetornaStatusPortfolio ($sock, $cod_curso, $cod_usuario, $cod_usuario_portfolio, $cod_grupo_portfolio);

  $dono_portfolio    = $status_portfolio ['dono_portfolio'];
  $portfolio_apagado = $status_portfolio ['portfolio_apagado'];
  $portfolio_grupo   = $status_portfolio ['portfolio_grupo'];
  

  session_register ("ferramenta_grupos_s");
  $ferramenta_grupos_s = StatusFerramentaGrupos ($sock);
  
  if ($eformador){
    echo("    <script type=\"text/javascript\" language=\"javascript\">\n");
    echo("      function redirecionaDownloadAnexos(url){\n");
    echo("        window.location=url;\n");
    echo("      }\n");
    echo("    </script>\n");
  }
  
  if (!$dono_portfolio){
    //JS utilizado para mover as colunas da tabela
    echo("    <script type='text/javascript'>\n");
    echo("      function OpenWindowPerfil(id)\n");
    echo("      {\n");
    echo("         window.open(\"../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]=\"+id,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
    echo("        return(false);\n");
    echo("      }\n");
  
    echo ("      function WindowOpenAvalia(id)\n");
    echo ("      {\n");
    echo ("         window.open('../avaliacoes/ver_popup.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&cod_avaliacao='+id,'VerAvaliacao','width=620,height=450,top=150,left=250,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
    echo ("        return(false);\n");
    echo ("      }\n");

  }else{
    
    echo("    <script type='text/javascript'>\n");
  
    echo("      function OpenWindowPerfil(id)\n");
    echo("      {\n");
    echo("         window.open(\"../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]=\"+id,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
    echo("        return(false);\n");
    echo("      }\n");

    echo("      var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
    echo("      var isMinNS6 = ((navigator.userAgent.indexOf(\"Gecko\") != -1) && (isNav));\n");
    echo("      var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");
    echo("      var Xpos, Ypos;\n");
    echo("      var js_cod_item, js_cod_topico;\n");
    echo("      var js_nome_topico;\n");
    echo("      var js_tipo_item;\n");
    echo("      var editando=0;\n");
    echo("      var mostrando=0\n");
    echo("      var js_comp = new Array();\n");
    echo("      var array_itens;\n");
    echo("      var array_topicos;\n");
    echo("      var nome_topico_atual\n");
    echo("      var table;\n");
    echo("      var tableDnD;\n\n");
  
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
    echo("      }\n\n");

    echo("      function SoltaMouse(ids){\n");
    echo("        xajax_AtualizaPosicoes('".$cod_curso."', '".$cod_usuario."', '".$cod_topico_raiz."', ids, '".RetornaFraseDaLista($lista_frases,191)."','".RetornaFraseDaLista($lista_frases,211)."');\n");
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
    echo ("             EdicaoNomePasta(id,'ok');\n");
    echo ("         }\n\n");
    echo ("         return true;\n");
    echo ("     }\n\n");

    echo ("      function WindowOpenAvalia(id)\n");
    echo ("      {\n");
    echo ("         window.open('../avaliacoes/ver_popup.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&cod_avaliacao='+id,'VerAvaliacao','width=620,height=450,top=150,left=250,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
    echo ("        return(false);\n");
    echo ("      }\n");
    
    echo("      function AlterarNomePasta(){\n");
    echo("        id = 'nome_topico_atual';\n");
    echo("        id_aux = id;");
    echo("        document.getElementById(id).onclick = function() { };\n");
    echo("        nome_topico_atual = document.getElementById(id).innerHTML;\n");
    echo("        createInput = document.createElement('input');\n");
    echo("        document.getElementById(id).innerHTML = '';\n");
    echo("        document.getElementById(id).style.fontWeight = '';\n");
    echo("        createInput.setAttribute('type', 'text');\n");
    echo("        createInput.setAttribute('style', 'border: 2px solid #9bc');\n");

    echo("        if (createInput.addEventListener){\n"); //not IE
    echo("          createInput.addEventListener('keypress', function (event) {EditaTituloEnter(this, event, id_aux);}, false);\n");
    echo("        } else if (createInput.attachEvent){\n"); //IE
    echo("          createInput.attachEvent('onkeypress', function (event) {EditaTituloEnter(this, event, id_aux);});\n");
    echo("        }\n");
    echo("        createInput.setAttribute('id', 'tit_'+id+'_text');\n\n");
  
    echo("        document.getElementById(id).appendChild(createInput);\n");
    echo("        xajax_DecodificaString('tit_'+id+'_text', nome_topico_atual, 'value');\n");
  
    echo("        //cria o elemento 'espaco' e adiciona na pagina\n");
    echo("        espaco = document.createElement('span');\n");
    echo("        espaco.innerHTML='&nbsp;&nbsp;'\n");
    echo("        document.getElementById(id).appendChild(espaco);\n\n");
  
    echo("        createSpan = document.createElement('span');\n");
    echo("        createSpan.className='link';\n");
    echo("        createSpan.onclick= function(){ EdicaoNomePasta(id, 'ok'); };\n");
    echo("        createSpan.setAttribute('id', 'OkEdita');\n");
    echo("        createSpan.innerHTML='".RetornaFraseDaLista($lista_frases_geral,18)."';\n");
    echo("        document.getElementById(id).appendChild(createSpan);\n\n");
  
    echo("        //cria o elemento 'espaco' e adiciona na pagina\n");
    echo("        espaco = document.createElement('span');\n");
    echo("        espaco.innerHTML='&nbsp;&nbsp;'\n");
    echo("        document.getElementById(id).appendChild(espaco);\n");
  
    echo("        createSpan = document.createElement('span');\n");
    echo("        createSpan.className='link';\n");
    echo("        createSpan.onclick= function(){ EdicaoNomePasta(id, 'canc'); };\n");
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
    echo("      }\n");
  
    echo("      function EdicaoNomePasta(id, valor){\n");
  
    echo("        if ((valor=='ok')&&(document.getElementById('tit_'+id+'_text').value!=\"\")){\n");
    echo("          novo_nome_topico = document.getElementById('tit_'+id+'_text').value;\n");
    echo("          if(novo_nome_topico == nome_topico_atual){\n");
    echo("            document.getElementById(id).innerHTML=nome_topico_atual;\n");
    echo("          }else{\n");
    echo("            xajax_RenomearTopicoDinamic('".$cod_curso."', '".$cod_usuario."','".$cod_topico_raiz."', novo_nome_topico, '".RetornaFraseDaLista($lista_frases,196)."', '".RetornaFraseDaLista($lista_frases,207)."');\n");
    echo("          }\n");
    echo("        }else{\n");
    /* 36 - O titulo nao pode ser vazio. */
    echo("          if ((valor=='ok')&&(document.getElementById('tit_'+id+'_text').value==\"\"))\n");
    echo("            alert('".RetornaFraseDaLista($lista_frases,36)."');\n");
  
    echo("          document.getElementById(id).innerHTML=nome_topico_atual;\n");
    echo("        }\n");
    echo("        document.getElementById(id).style.fontWeight='bold';\n");
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
    
    echo("      function VerificaNovoItemTopico(textbox) {\n");
    echo("        texto=textbox.value;\n");
    echo("        if (texto==''){\n");
    echo("          // se nome for vazio, nao pode\n");
                  /* 36 - O titulo nao pode ser vazio. */
    echo("          alert(\"".RetornaFraseDaLista($lista_frases,36)."\");\n");
    echo("          textbox.focus();\n");
    echo("          return false;\n");
    echo("        }\n");
    echo("        // se nome tiver aspas, <, >, nao pode - aspas pode ser 1,0\n");
    echo("        else if (texto.indexOf(\"\\\\\")>=0 || texto.indexOf(\"\\\"\")>=0 || texto.indexOf(\"'\")>=0 || texto.indexOf(\">\")>=0 || texto.indexOf(\"<\")>=0 || texto.indexOf(\"#\")>=0) {\n");
    echo("           alert(\"".ConverteAspas2BarraAspas(ConverteHtml2Aspas(RetornaFraseDaLista($lista_frases,36)))."\");\n");
    echo("          textbox.value='';\n");
    echo("          textbox.focus();\n");
    echo("          return false;\n");
    echo("        }\n");
    echo("        xajax_CriaTopicoDinamic('".$cod_curso."', '".$cod_usuario."' , '".$cod_grupo_portfolio."', '".$cod_usuario_portfolio."', '".$cod_topico_raiz."', '".$dirname."', texto, '".RetornaFraseDaLista($lista_frases_geral, 73)."');\n");
    echo("        return false;\n");
    echo("      }\n\n");
  
    echo("      function AtualizaComp(js_tipo_comp)\n");
    echo("      {\n");
    echo("        if ((isNav) && (!isMinNS6)) {\n");
    echo("          document.comp.document.form_comp.tipo_comp.value=js_tipo_comp;\n");
    echo("          document.comp.document.form_comp.cod_item.value=js_cod_item;\n");
    echo("          var tipo_comp = new Array(document.comp.document.getElementById('tipo_comp_T'), document.comp.document.getElementById('tipo_comp_F'), document.comp.document.getElementById('tipo_comp_P'));\n");
    echo("        } else {\n");
    echo("            document.form_comp.tipo_comp.value=js_tipo_comp;\n");
    echo("            document.form_comp.cod_item.value=js_cod_item;\n");
    echo("            var tipo_comp = new Array(document.getElementById('tipo_comp_T'), document.getElementById('tipo_comp_F'), document.getElementById('tipo_comp_P'));\n");
    echo("        }\n");
    echo("        var imagem=\"<img src='../imgs/checkmark_blue.gif' />\"\n");
    echo("        if (js_tipo_comp=='T') {\n");
    echo("          tipo_comp[0].innerHTML=imagem;\n");
    echo("          tipo_comp[1].innerHTML=\"&nbsp;\";\n");
    echo("          tipo_comp[2].innerHTML=\"&nbsp;\";\n");
    echo("        } else if (js_tipo_comp=='F') {\n");
    echo("          tipo_comp[0].innerHTML=\"&nbsp;\";\n");
    echo("          tipo_comp[1].innerHTML=imagem;\n");
    echo("          tipo_comp[2].innerHTML=\"&nbsp;\";\n");
    echo("        } else{\n");
    echo("          tipo_comp[0].innerHTML=\"&nbsp;\";\n");
    echo("          tipo_comp[1].innerHTML=\"&nbsp;\";\n");
    echo("          tipo_comp[2].innerHTML=imagem;\n");
    echo("        }\n");
    echo("      }\n\n");
  
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
  
    echo("      function EscondeLayer(cod_layer)\n");
    echo("      {\n");
    echo("        hideLayer(cod_layer);\n");
    echo("        mostrando=0;\n");
    echo("      }\n\n");
  
    echo("      function VerificaCheck(){\n");
    echo("        var i;\n");
    echo("        var j=0;\n");
    echo("        var k=0;\n");
    echo("        var cod_itens=document.getElementsByName('chkItem');\n");
    echo("        var cod_topicos=document.getElementsByName('chkTopico');\n");
    echo("        var Cabecalho = document.getElementById('checkMenu');\n");
    echo("        array_itens = new Array();\n");
    echo("        array_topicos = new Array();\n");
    echo("        for (i=0; i < cod_itens.length; i++){\n");
    echo("          if (cod_itens[i].checked){\n");
    echo("            var item = cod_itens[i].id.split('_');\n");
    echo("            array_itens[j]=item[1];\n");
    echo("            j++;\n");
    echo("          }\n");
    echo("        }\n");
    echo("        for (i=0; i < cod_topicos.length; i++){\n");
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
    /* 18 - Você tem certeza de que deseja apagar este item? */
    /* 179 - (Os itens serão movidos para a lixeira) */
    echo("        if (confirm('".RetornaFraseDaLista($lista_frases,18)."\\n".RetornaFraseDaLista($lista_frases,179)."')){\n");
    echo("          document.getElementById('cod_topicos_form').value=array_topicos;\n");
    echo("          document.getElementById('cod_itens_form').value=array_itens;\n");
    echo("          document.form_dados.action='acoes.php';\n");
    echo("          document.form_dados.method='POST';\n");
    echo("          document.getElementById('acao_form').value='apagarSelecionados';\n");
    echo("          document.form_dados.submit();\n");
    echo("        }\n");
    echo("      }\n\n");

    echo("      function MoverSelecionados(topico_destino){\n");
    echo("        xajax_MoverItensDinamic('".$cod_curso."', '".$cod_usuario."', '".$cod_topico_raiz."', topico_destino, array_topicos, array_itens);\n");
    echo("      }\n\n");

    echo("      function Redirecionar(cod_topico_raiz, acao, atualizacao){\n");
    echo("         window.location='portfolio.php?cod_curso=".$cod_curso."&cod_topico_raiz='+cod_topico_raiz+'&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."&acao='+acao+'&atualizacao='+atualizacao;\n");
    echo("      }\n\n");

    echo("      function EscondeLayers()\n");
    echo("      {\n");
    echo("        hideLayer(cod_comp);\n");
    echo("        hideLayer(cod_mover);\n");
    echo("        hideLayer(cod_mover_selec);\n");
    echo("        hideLayer(cod_novoitem);\n");
    echo("        hideLayer(cod_novapasta);\n");
    echo("      }\n\n");
  
  }
  echo("      function Iniciar()\n");
  echo("      {\n");
  if($dono_portfolio){
    echo("        cod_comp = getLayer(\"comp\");\n");
    echo("        cod_mover = getLayer(\"mover\");\n");
    echo("        cod_mover_selec = getLayer(\"mover_selec\");\n");
    echo("        cod_novoitem = getLayer(\"novoitem\");\n");
    echo("        cod_novapasta = getLayer(\"novapasta\");\n");
    echo("        cod_topicos = getLayer(\"topicos\");\n");
    echo("        EscondeLayers();\n");
    echo("        tableDnD = new TableDnD();\n");
    echo("        table = document.getElementById('tab_interna');\n");
    echo("        if(table) tableDnD.init(table);\n");
  }
  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("        startList();\n");
  echo("      }\n\n");

  echo("      function AbreJanelaComponentes(id)\n");
  echo("      {\n");
  echo("         window.open(\"../grupos/exibir_grupo.php?cod_curso=".$cod_curso."&cod_grupo=\"+id,\"GruposDisplay\",\"width=700,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("        return(false);\n");
  echo("      }\n");

  echo("    </script>\n");
  echo("    <script type=\"text/javascript\" src=\"../js-css/tablednd.js\"></script>\n");

  $objAjax->printJavascript();

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  ExpulsaVisitante($sock, $cod_curso, $cod_usuario);

  /* P?ina Principal */

  if ($ferramenta_avaliacao)
  {
    if ($ferramenta_grupos_s && $cod_grupo_portfolio != '')
    {
      // 3 - Portfolios de grupos
      $cod_frase  =  3;
      $cod_pagina = 23;
    }
    else
    {
      // 2 - Portfolios individual
      $cod_frase  =  2;
      $cod_pagina = 18;
    }
  }
  else
  {
    if ($ferramenta_grupos_s && $cod_grupo_portfolio != '')
    {
      // 3 - Portfolios de grupos
      $cod_frase = 3;
      $cod_pagina=10;
    }
    else
    {
      // 2 - Portfolios individual
      $cod_frase = 2;
      $cod_pagina=3;
    }
  }


  echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases, $cod_frase)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  $lista_topicos_ancestrais=RetornaTopicosAncestrais($sock, $cod_topico_raiz);
  unset($path);

  foreach ($lista_topicos_ancestrais as $cod => $linha)
  {
    if ($cod_topico_raiz!=$linha['cod_topico'])
    {
      $path="<a class=\"text\" href=\"portfolio.php?cod_curso=".$cod_curso."&amp;cod_topico_raiz=".$linha['cod_topico']."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."\">".$linha['topico']."</a> &gt;&gt; ".$path;
    }
    else
    {
      $path="<span style=\"font-weight:bold;\" id=\"nome_topico_atual\">".$linha['topico']."</span><br />\n";
    }
  }

  if ($portfolio_grupo)
  {
    $nome=NomeGrupo($sock,$cod_grupo_portfolio);

    //Figura de Grupo
    $fig_portfolio = "<img alt=\"\" src=\"../imgs/icGrupo.gif\" border=\"0\" />";

    /* 84 - Grupo Excluído */
    if ($grupo_apagado && $eformador) $complemento=" <span>(".RetornaFraseDaLista($lista_frases,84).")</span>\n";


    echo("          ".$fig_portfolio." <span class=\"link\" onclick=\"AbreJanelaComponentes(".$cod_grupo_portfolio.");\">".$nome."</span>".$complemento." - ");
    echo("          <span class=\"link\" onclick=\"MostraLayer(cod_topicos,0,event);\"><img src=\"../imgs/estrutura.gif\" border=\"0\" alt=\"estrutura.gif\"/></span>");
  }
  else
  {
    $nome=NomeUsuario($sock,$cod_usuario_portfolio, $cod_curso);

    // Selecionando qual a figura a ser exibida ao lado do nome
    $fig_portfolio = "<img alt=\"\" src=\"../imgs/icPerfil.gif\" border=\"0\" />";

    /* 85 - Aluno Rejeitado */
    if (RetornaStatusUsuario($sock,$cod_curso,$cod_usuario_portfolio)=="r" && $eformador) $complemento=" <font class=\"textsmall\">(".RetornaFraseDaLista($lista_frases,85).")</font>\n";

    echo("          ".$fig_portfolio." <span class=\"link\" onclick=\"OpenWindowPerfil(".$cod_usuario_portfolio.");\" > ".$nome."</span>".$complemento." - ");
    echo("<a href=\"#\" onmousedown=\"js_cod_item='".$cod_item."'; MostraLayer(cod_topicos,0,event);return(false);\"><img alt=\"\" src=\"../imgs/estrutura.gif\" border=\"0\" /></a>");
  }

  echo ($path);

  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <!-- Botoes de Acao -->\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");

   //174 - Meus portfolios 
  echo("                  <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&amp;exibir=myp\">".RetornaFraseDaLista($lista_frases,174)."</a></li>\n");
  // 74 - Portfolios Individuais
  echo("                  <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&amp;exibir=ind\">".RetornaFraseDaLista($lista_frases,74)."</a></li>\n");

  // 75 - Portfolios de Grupos
  if ((isset($ferramenta_grupos_s)) && ($ferramenta_grupos_s)) {
    echo("                  <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&amp;exibir=grp\">".RetornaFraseDaLista($lista_frases,75)."</a></li>\n");
    // 177 - Portfolios encerrados
    echo("                  <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&amp;exibir=enc\">".RetornaFraseDaLista($lista_frases,177)."</a></li>\n");
  }

  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs03\">\n");

  // 69 - Atualizar
  echo("                  <li> <span onclick=\"window.location.reload();\">".RetornaFraseDaLista($lista_frases,69)."</span></li>\n");
  
  // download de todos os anexos do portfolio de um aluno
  // TODO: falta fazer a funcao dinamica para pegar os anexos e montar o zip
  //var_dump($cod_topico_raiz);
	//if ($eformador)
    	//echo("                    <li><span onclick=\"xajax_CriaZipDinamic('".$sock."','".$cod_topico_raiz."','".$dir_tmp_ferramenta."',".$cod_curso.",".$cod_ferramenta.",'".$diretorio_arquivos_dinamic."','".$tabela_dinamic."','".$bibliotecas."','".$nome_ferramenta_dinamic."','".$diretorio_temp_dinamic."');\">Baixar todos os anexos</span></li>\n");
		// FIXME
    	//CriaZipDinamic($cod_topico_raiz, $dir_tmp_ferramenta, $cod_curso, $cod_ferramenta, $diretorio_arquivos_dinamic, $tabela_dinamic, $bibliotecas, $nome_ferramenta_dinamic, $diretorio_temp_dinamic);
    	//$sock1 = Conectar($cod_curso);
    	//CriaArvorePastasTopico($sock1, $cod_topico_raiz, $dir_tmp_ferramenta, $cod_curso, $cod_ferramenta, $diretorio_arquivos_dinamic);
  		//Desconectar($sock1);
  if ($dono_portfolio)
  {
    // 4 - Incluir Novo Item
    echo("                  <li><span onclick=\"MostraLayer(cod_novoitem, 140,event);document.getElementById('titulo').focus();document.getElementById('titulo').value=''\">".RetornaFraseDaLista($lista_frases,4)."</span></li>\n");
    // 5 - Nova Pasta
    echo("                  <li><span onclick=\"MostraLayer(cod_novapasta, 140,event);document.getElementById('titulopasta').value=''; document.getElementById('titulopasta').focus();\">".RetornaFraseDaLista($lista_frases,5)."</span></li>\n");

    if($cod_topico_raiz != $cod_topico_raiz_usuario){
      // 183 - Renomear Pasta
      echo("                  <li><span onclick=\"AlterarNomePasta();\" >".RetornaFraseDaLista($lista_frases,183)."</span></li>\n");
    }

    // 7 - Lixeira
    echo("                  <li><span onclick=\"window.location='portfolio_lixeira.php?cod_curso=".$cod_curso."&amp;cod_topico=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."';\" >".RetornaFraseDaLista($lista_frases,7)."</span></li>\n");
  }

  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");

  if($dono_portfolio){
    echo("                    <td width=\"2\"><input type=\"checkbox\" id=\"checkMenu\" onclick=\"CheckTodos();\" /></td>\n");
  }

  /* 82 - Itens */
  echo("                    <td class=\"alLeft\">".RetornaFraseDaLista($lista_frases,82)."</td>\n");
  
  // se a ferramenta Avaliacoes estiver ativada, a tabela com os itens e pastas do portfolio tem 6 colunas, senao sao 5
  if ($ferramenta_avaliacao)
  {
    /* 139 - Avalia?o */
    echo("                    <td width=\"110\" align=\"center\">".RetornaFraseDaLista($lista_frases,139)."</td>\n");
  }
  
  /* 112 - Coment?ios */
  echo("                    <td width=\"110\" align=\"center\">".RetornaFraseDaLista($lista_frases,112)."</td>\n");
  
   /* 9 - Data */
  echo("                    <td width=\"70\" align=\"center\">".RetornaFraseDaLista($lista_frases,9)."</td>\n");
  
  /* 119 - Compartilharmento */
  echo("                    <td width=\"110\" align=\"center\">".RetornaFraseDaLista($lista_frases,119)."</td>\n");




  echo("                  </tr>\n");

  $lista_topicos=RetornaTopicosDoTopico($sock, $cod_curso, $cod_topico_raiz,$cod_usuario,$eformador,$cod_usuario_portfolio,$cod_grupo_portfolio);
  $lista_itens=RetornaItensDoTopico($sock, $cod_curso, $cod_topico_raiz,$cod_usuario,$eformador,$cod_usuario_portfolio,$cod_grupo_portfolio);

  if (((count($lista_topicos)<1)||($lista_topicos=="")) && ((count($lista_itens)<1)||($lista_itens=="")))
  {
    echo("                  <tr>\n");
    /* 11 - Não há nenhum item neste portfólio */
    echo("                    <td colspan=\"6\">".RetornaFraseDaLista($lista_frases,11)."</td>\n");
    echo("                  </tr>\n");
    echo("                </table>\n");
  }
  //else = existe um topico ou item no portfolio
  else
  {
    echo("                </table>\n");
    echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\" id=\"tab_interna\">");
    // definindo qual figura para representar pastas ou arquivos (itens)
    $pasta   = "pasta_";
    $arquivo = "arquivo_";

    // aqui, escolho entre a figura para grupo ou individual
    if ($portfolio_grupo) $gi="g_";
    else $gi="i_";
    $pasta  .= $gi;
    $arquivo.= $gi;

    // aqui, escolho entre pessoal, nao-pessoal ou apagado
    if ($dono_portfolio) $pnx="p.gif";
    else if ($portfolio_apagado) $pnx="x.gif";
    else $pnx="n.gif";
    $pasta  .= $pnx;
    $arquivo.= $pnx;


    $top_index = 0;
    $itens_index = 0;
    for($i=0; $i < ((count($lista_topicos))+(count($lista_itens))); $i++){
      if((!isset($lista_topicos[$top_index]['posicao_topico'])) || (isset($lista_itens[$itens_index]['posicao_item']) &&($lista_topicos[$top_index]['posicao_topico'] > $lista_itens[$itens_index]['posicao_item']))) {
        $lista_unificada[$i] = $lista_itens[$itens_index];
        $itens_index++;
      }else{
        //este if é para não alterar a estrutura dos portfólios antigos
        if((isset($lista_itens[$top_index]['posicao_item'])) && ($lista_topicos[$top_index]['posicao_topico'] == $lista_itens[$itens_index]['posicao_item'])) {
          $lista_itens[$itens_index]['posicao_item']++;
        }
        $lista_unificada[$i] = $lista_topicos[$top_index];
        $top_index++;
      }
    }

    foreach($lista_unificada as $cod => $linha){
      //se é tópico...
      if(isset($linha['posicao_topico']))
      {
        $data=UnixTime2Data($linha['data']);

        if ($dono_portfolio) $varTmp="P";
        else if ($eformador) $varTmp="F";
        else $varTmp="T";

        $max_data=RetornaMaiorData($sock,$linha['cod_topico'],$varTmp,$linha['data']);
        $num_comentarios=RetornaNumComentariosTopico($sock,$cod_usuario,$linha['cod_topico'],$varTmp,$linha['data'], $cod_curso);
        if ($data_acesso<$max_data) $marcatr=" class=\"novoitem\"";
        else $marcatr="";

        echo("<tr ".$marcatr." id=\"tr_top_".$linha['cod_topico']."\">");

        if($dono_portfolio) {
          echo("<td width=\"5\"><input type=\"checkbox\" id=\"chktop_".$linha['cod_topico']."\" name=\"chkTopico\" onclick=\"VerificaCheck()\" value=\"".$linha['cod_topico']."\" /></td>");
        }

        echo("<td class=\"itens\"><img alt=\"\" src=\"../imgs/".$pasta."\" border=\"0\" /> ");

        if ($dono_portfolio){
          $titulo_topico  = "<span class=\"link\" onclick=\"window.location='portfolio.php?cod_curso=".$cod_curso."&amp;cod_topico_raiz=".$linha['cod_topico']."&amp;time=".time()."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."';\">".$linha['topico']."</span>";
        }else{
          $titulo_topico  = "<span class=\"link\" onclick=\"window.location='portfolio.php?cod_curso=".$cod_curso."&amp;cod_topico_raiz=".$linha['cod_topico']."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."';\">".$linha['topico']."</span>";
        }

//         $num_itens = RetornaNumItensTopicoRec($sock, $linha ['cod_topico'], $dono_portfolio, $eformador);

        // 125 - Item
        if ($num_itens==1) $frase=125;
        // 82 - Itens
        else $frase=82;

        $complemento  = " (".$num_itens." ".RetornaFraseDaLista ($lista_frases, $frase).")";

        if ($num_itens>0) $itens=$complemento;
        else $itens="";

        echo($titulo_topico.$itens."</td>");

        // Esta eh a coluna de avaliacoes
        if ($ferramenta_avaliacao){
          echo("<td width=\"110\">&nbsp;</td>");
        }
        echo("<td width=\"110\" align=\"center\">&nbsp;");
          if ($num_comentarios['num_comentarios_alunos']>0)
            echo("<span class=\"cAluno\">(c)</span>");
          if ($num_comentarios['num_comentarios_formadores']>0)
            echo("<span class=\"cForm\">(c)</span>");
          if ($num_comentarios['num_comentarios_usuario']>0)
            echo("<span class=\"cMim\">(c)</span>");
          if ($num_comentarios['data_comentarios']>$data_acesso)
            echo("<span class=\"cNovo\">*</span>");
        echo("</td>");
                echo("<td width=\"70\" align=\"center\"><span>".$data."</span></td>");
        echo("<td width=\"110\">&nbsp;</td>");

        echo("</tr>");
      }
      // é item...
      else if(isset($linha['cod_item'])){

        $data=UnixTime2Data($linha['data']);
         /* 12 - Totalmente Compartilhado */
        if ($linha['tipo_compartilhamento']=="T"){
          $compartilhamento=RetornaFraseDaLista($lista_frases,12);
        }
        /* 13 - Compartilhado com Formadores */
        else if ($linha['tipo_compartilhamento']=="F"){
          $compartilhamento=RetornaFraseDaLista($lista_frases,13);
        }
        /* 14 - Compartilhado com o Grupo */
        else if (($portfolio_grupo)&&($linha['tipo_compartilhamento']=="P")){
          $compartilhamento=RetornaFraseDaLista($lista_frases,14);
        }
        /* 15 - Não compartilhado */
        else if (!$portfolio_grupo){
          $compartilhamento=RetornaFraseDaLista($lista_frases,15);
        }

        // Marca se a linha cont? um item 'novo'
        if ($data_acesso<$linha['data']) $marcatr=" class=\"novoitem\"";
        else $marcatr="";

        // se a ferramenta Avaliacoes estiver ativa, descobrimos quais avaliacoes estao presas a cada item
        if ($ferramenta_avaliacao) $lista = RetornaAssociacaoItemAvaliacao($sock,$linha['cod_item']);
        // senao, passamos uma variavel fake para enganar o codigo abaixo
        else $lista = NULL;

        if ($linha['status']=="E"){

          $linha_historico=RetornaUltimaPosicaoHistorico($sock, $linha['cod_item']);

          if ($linha['inicio_edicao']<(time()-1800) || $cod_usuario == $linha_historico['cod_usuario'])
          {
            CancelaEdicao($sock, $linha['cod_item'], $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp, false, false, false);
            if ($dono_portfolio)
            {
              //se existe uma avalia?o ligada ao item
              if (is_array($lista))
              {
                $foiavaliado=ItemFoiAvaliado($sock,$lista['cod_avaliacao'],$linha['cod_item']);
                //talvez arrumar a funcao ItemFoiAvaliado, pois da forma que ta se o item tiver sido avaliado, mas tiver compartilhado so com
                //formadores, o aluno nao sabe que foi avaliado, mas nao consegue editar o item, o que fazer?

                // se foi avaliado n? pode editar o material
                if ($foiavaliado){
                  $titulo="<span id=\"tit_".$linha['cod_item']."\" id=\"titulo_".$linha['cod_item']."\" class=\"link\" onclick=\"window.location='ver.php?cod_curso=".$cod_curso."&amp;cod_item=".$linha['cod_item']."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."'\">".$linha['titulo']."</span>";
                }
                //else = n? foi avaliado
                else {
                  $titulo="<span id=\"tit_".$linha['cod_item']."\" id=\"titulo_".$linha['cod_item']."\" class=\"link\" onclick=\"window.location='ver.php?cod_curso=".$cod_curso."&amp;cod_item=".$linha['cod_item']."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."'\">".$linha['titulo']."</span>";
                  $compartilhamento="<span id=\"comp_".$linha['cod_item']."\" class=\"link\" onclick=\"js_cod_item='".$linha['cod_item']."';AtualizaComp('".$linha['tipo_compartilhamento']."');MostraLayer(cod_comp,140,event);return(false);\">".$compartilhamento."</span>";
                }
              }
              //else = não existe uma avaliação
              else {
                $titulo="<span id=\"tit_".$linha['cod_item']."\" id=\"titulo_".$linha['cod_item']."\" class=\"link\" onclick=\"window.location='ver.php?cod_curso=".$cod_curso."&amp;cod_item=".$linha['cod_item']."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."'\">".$linha['titulo']."</span>";
                $compartilhamento="<span id=\"comp_".$linha['cod_item']."\" class=\"link\" onclick=\"js_cod_item='".$linha['cod_item']."';AtualizaComp('".$linha['tipo_compartilhamento']."');MostraLayer(cod_comp,140,event);return(false);\">".$compartilhamento."</span>";
              }
            }
            //else = não é dono do portfolio
            else
              $titulo="<span id=\"tit_".$linha['cod_item']."\" id=\"titulo_".$linha['cod_item']."\" class=\"link\" onclick=\"window.location='ver.php?cod_curso=".$cod_curso."&amp;cod_item=".$linha['cod_item']."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."'\">".$linha['titulo']."</span>";
          }
          //else = item está sendo editado
          else
          {
            /* 54 - Em Edição */
            $data="<a href=\"#\" class=\"text\" onclick=\"window.open('em_edicao.php?cod_curso=".$cod_curso."&amp;cod_item=".$linha['cod_item']."&amp;origem=portfolio&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."','EmEdicao','width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\">".RetornaFraseDaLista($lista_frases_geral,54)."</a>";
            $titulo=$linha['titulo'];
            $marcatr="";
          }
        }
        //else = item est?em avalia?o
        else if ($ferramenta_avaliacao && is_array($lista) && ItemEmAvaliacao($sock,$lista['cod_avaliacao'],$cod_usuario_portfolio) && $dono_portfolio)
        {
          /* 140 - Em Avalia?o */
          $data=RetornaFraseDaLista($lista_frases, 140);
          $titulo = "<span class=\"link\" onclick=\"js_cod_item=".$linha['cod_item'].";MostraLayer(cod_menu_item_em_avaliacao,0,event);return(false);\">".$linha['titulo']."</span>";
          $titulo = $titulo;
        }
        else
        {
          if ($linha['status'] != "C")
          {
            if ($dono_portfolio)
            {
              if (is_array($lista))
              {
                $foiavaliado = ItemFoiAvaliado($sock,$lista['cod_avaliacao'],$linha['cod_item']);
                if ($foiavaliado)
                {
                  $titulo="<span id=\"tit_".$linha['cod_item']."\" class=\"link\" onclick=\"window.location='ver.php?cod_curso=".$cod_curso."&amp;cod_item=".$linha['cod_item']."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."'\">".$linha['titulo']."</span>";
                }
                else
                {
                  $titulo="<span id=\"tit_".$linha['cod_item']."\" id=\"titulo_".$linha['cod_item']."\" class=\"link\" onclick=\"window.location='ver.php?cod_curso=".$cod_curso."&amp;cod_item=".$linha['cod_item']."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."'\">".$linha['titulo']."</span>";
                  $compartilhamento="<span id=\"comp_".$linha['cod_item']."\" class=\"link\" onclick=\"js_cod_item='".$linha['cod_item']."';AtualizaComp('".$linha['tipo_compartilhamento']."');MostraLayer(cod_comp,140,event);return(false);\">".$compartilhamento."</span>";
                }
              }
              else
              {
                $titulo="<span id=\"tit_".$linha['cod_item']."\" class=\"link\" onclick=\"window.location='ver.php?cod_curso=".$cod_curso."&amp;cod_item=".$linha['cod_item']."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."'\" >".$linha['titulo']."</span>";
                $compartilhamento="<span id=\"comp_".$linha['cod_item']."\" class=\"link\" onclick=\"js_cod_item='".$linha['cod_item']."'; AtualizaComp('".$linha['tipo_compartilhamento']."'); MostraLayer(cod_comp,140,event);\">".$compartilhamento."</span>";
              }
            }
            else
            {
              $titulo="<span id=\"tit_".$linha['cod_item']."\" class=\"link\" onclick=\"window.location='ver.php?cod_curso=".$cod_curso."&amp;cod_item=".$linha['cod_item']."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."'\">".$linha['titulo']."</span>";
            }
          }
        }

        if ($linha['status']=="C")
        {
          if ($linha['inicio_edicao']<(time()-1800) || $cod_usuario==$linha['cod_usuario'])
          {
            CancelaEdicao($sock, $linha['cod_item'], $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp, false, false, false);
          }
        }
        else
        {
          echo("<tr".$marcatr." id=\"tr_".$linha['cod_item']."\">");

          if($dono_portfolio) {
            echo("<td width=\"2\"><input type=\"checkbox\" id=\"chkitm_".$linha['cod_item']."\" name=\"chkItem\" value=\"".$linha['cod_item']."\" onclick=\"VerificaCheck()\" /></td>");
          }

          $icone = "<img alt=\"\" src=\"../imgs/".$arquivo."\" border=\"0\" />";

          echo("<td class=\"itens\">".$icone." ".$titulo."</td>");
          
          $tituloAvaliacao = RetornaTituloAvaliacaoDoItem($sock, $linha['cod_item']);
          
          if($tituloAvaliacao!= ""){
            $tituloavalia = "<span id=\"estadoAvaliacao\" class=\"link\" onclick=\"WindowOpenAvalia(".$lista['cod_avaliacao']."); return false;\" >" . $tituloAvaliacao . "</span>";
          }
          else{
            //36 - Nao
            $tituloavalia = "<span id=\"estadoAvaliacao\">" . RetornaFraseDaLista($lista_frases_geral, 36) . "</span>";
          }
          if ($ferramenta_avaliacao)
          {
            echo("<td width=\"110\" align=\"center\"><span>");
            if (is_array($lista))
            {
              //$foiavaliado=ItemFoiAvaliado($sock,$lista['cod_avaliacao'],$linha['cod_item']);
              $foiavaliado=FoiAvaliado($sock,$lista['cod_avaliacao'],$linha['cod_usuario']);
              if ($foiavaliado){
                if ($eformador){
                  echo($tituloavalia."</span><span class=\"avaliado\"> (a)");
                }
                //else = não é formador
                else{
                  $compartilhado=NotaCompartilhadaAluno($sock,$linha['cod_item'],$lista['cod_avaliacao'],$cod_grupo_portfolio,$cod_usuario);
                  if ($compartilhado){
                    echo($tituloavalia."</span><span class=\"avaliado\"> (a)");
                  }
                  //else = n? ?compartilhado
                  else{
                    echo($tituloavalia);
                  }
                }
              }
              else{
                echo($tituloavalia);
              }
            }
            //else = n? tem avalia?o
            else{
              // G 36 - N?
              echo($tituloavalia);
            }
            echo("</span>");
            echo("</td>");
          }
        }
        
        echo("<td width=\"110\">&nbsp;");

        if ($linha['num_comentarios_alunos']>0){
          echo("<span class=\"cAluno\">(c)</span>");
        }
        if ($linha['num_comentarios_formadores']>0){
          echo("<span class=\"cForm\">(c)</span>");
        }
        if ($linha['num_comentarios_usuario']>0){
          echo("<span class=\"cMim\">(c)</span>");
        }
        if ($linha['data_comentarios']>$data_acesso){
          echo("<span class=\"cNovo\">*</span>");
        }   
        echo("</td>");
        echo("<td width=\"70\"><span id=\"data_".$linha['cod_item']."\">".$data."</span></td>");
        echo("<td width=\"110\"><span>".$compartilhamento."</span></td>");
        echo("</tr>");
      } //fecha foreach
    }
    echo("</table>\n");
  } //fecha else = existem topicos ou pastas


  /* 113 - Coment?io de Aluno */
  /* 114 - Coment?io de Formador */
  /* 115 - Coment?io postados por mim */
  /* 141 - Item Avaliado */
  echo("                <span class=\"cAluno\">(c)</span> ".RetornaFraseDaLista($lista_frases,113)." - \n");
  echo("                <span class=\"cForm\">(c)</span> ".RetornaFraseDaLista($lista_frases,114)." - \n");

  if (!EVisitante($sock,$cod_curso,$cod_usuario))
    echo("                <span class=\"cMim\">(c)</span> ".RetornaFraseDaLista($lista_frases,115)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n");

  if ($ferramenta_avaliacao)
    echo("                <span class=\"avaliado\">(a)</span> ".RetornaFraseDaLista($lista_frases,141)."\n");

  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  if($dono_portfolio){
    echo("          <ul>\n");
    echo("            <li id=\"mExcluir_Selec\" class=\"menuUp\"><span id=\"excluirSelec\">".RetornaFraseDaLista($lista_frases_geral,68)."</span></li>\n");
    echo("            <li id=\"mMover_Selec\" class=\"menuUp\"><span id=\"moverSelec\">".RetornaFraseDaLista($lista_frases_geral,69)."</span></li>\n");
    echo("          </ul>\n");
  }
  echo("        </td>\n");
  echo("      </tr>\n");

  include("../tela2.php");
  
  if($dono_portfolio){
    include("layer.php");
  }

  echo("    <form name=\"form_dados\" action=\"\" id=\"form_dados\">\n");

  echo("      <input type=\"hidden\" name=\"cod_curso\" id=\"cod_curso\" value=\"".$cod_curso."\" />\n");
  echo("      <input type=\"hidden\" name=\"cod_topico_raiz\" value=\"".$cod_topico_raiz."\" />\n");
  echo("      <input type=\"hidden\" name=\"cod_item\" id=\"cod_item\" value=\"\" />\n");
  echo("      <input type=\"hidden\" name=\"acao\" id=\"acao_form\" value=\"\" />\n");
  echo("      <input type=\"hidden\" name=\"cod_topico\" value=\"\" />\n");
  echo("      <input type=\"hidden\" name=\"cod_usuario_portfolio\" value=\"".$cod_usuario_portfolio."\" />\n");
  echo("      <input type=\"hidden\" name=\"cod_grupo_portfolio\" value=\"".$cod_grupo_portfolio."\" />\n");
  echo("      <input type=\"hidden\" name=\"cod_topicos\" id=\"cod_topicos_form\" value=\"\" />\n");
  echo("      <input type=\"hidden\" name=\"cod_itens\" id=\"cod_itens_form\" value=\"\" />\n");
  echo("    </form>\n");
  echo("  </body>\n");
  echo("</html>\n");
  Desconectar($sock);
?>