<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/grupos/grupos.php

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
  ARQUIVO : cursos/aplic/grupos/grupos.php
  ========================================================== */

/* C�igo principal */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("grupos.inc");

  require_once("../xajax_0.5/xajax_core/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->register(XAJAX_FUNCTION,"MostraGrupoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"EditarTituloDinamic");
  $objAjax->register(XAJAX_FUNCTION,"DecodificaString");
  $objAjax->register(XAJAX_FUNCTION,"MudarConfiguracaoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"ExcluirComponentesDinamic");
  $objAjax->register(XAJAX_FUNCTION,"VerificaNovoTituloDinamic");
  // Registra fun��es para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  $cod_ferramenta=12;
  $cod_ferramenta_ajuda=$cod_ferramenta;
  $cod_pagina_ajuda=1;

  include("../topo_tela.php");

  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
  $feedbackObject->addAction("apagar_grupo", 41, 0);
  $feedbackObject->addAction("incluir_no_grupo", 38, 0);
  $feedbackObject->addAction("criar_grupo", 20, 17);
  $feedbackObject->addAction("mudar_configuracao", 73, 0);

  /*Para evitar chamar a mesma função diversas vezes */
  $bool_grupos_fechados=GruposFechados($sock);

  /*
  ==================
  FUN�ES JAVASCRIPT
  ==================
  */

  echo("    <script type=\"text/javascript\">\n");
  
  echo("      var arrayMostraGrupo = new Array();\n");
  /* 18 - Aluno */
  echo("      arrayMostraGrupo[0]='".RetornaFraseDaLista($lista_frases,18)."';\n");
  /* 19 - Formador */
  echo("      arrayMostraGrupo[1]='".RetornaFraseDaLista($lista_frases,19)."';\n");
  /* 86 - Incluir Componentes */
  echo("      arrayMostraGrupo[2]='".RetornaFraseDaLista($lista_frases,86)."';\n");
  /* 87 - Excluir Selecionados */
  echo("      arrayMostraGrupo[3]='".RetornaFraseDaLista($lista_frases,87)."';\n");
  /* 31 - Não há integrantes neste grupo.*/
  echo("      arrayMostraGrupo[4]='".RetornaFraseDaLista($lista_frases,31)."';\n");
  /* 12 - Integrantes do grupo: */
  echo("      arrayMostraGrupo[5] = '".RetornaFraseDaLista($lista_frases, 12)."';\n");
  echo("      var janelaIncluir;\n");
  echo("      var novaJanela;\n");
  echo("      var conteudo;\n");
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

  echo("      function SalvaImprime()\n ");
  echo("      {\n");
  echo("       window.open(\"salva_imprime.php?cod_curso=".$cod_curso."&cod_grupo=+cod_grupo\",\"MostrarComponentes\",\"width=620,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no\");\n");
  echo("      }\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  if(!EVisitante($sock,$cod_curso,$cod_usuario))
  {
    if (EFormador($sock,$cod_curso,$cod_usuario))
    {
      echo("        lay_conf = getLayer('layer_conf');\n");
    }
    echo("        lay_novo = getLayer('layer_novo');\n");
  }
  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("        startList();\n");
  echo("      }\n\n");

  
    /* *********************************************************
  Funcao OpenWindowLink
    Abre nova janela com o perfil, se acessado atraves do link
    Entrada: funcao = $cod_curso - Codigo do curso
    Saida:   false - para nao dar reload na pagina. Conferir a
                     chamada da fun�o
  */
  echo("      function OpenWindowLink(funcao) \n");
  echo("      {\n");
  echo("         window.open(\"../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]=\"+funcao,\"PerfilDisplay\",\"width=600,height=400,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("        return(false);\n");
  echo("      }\n\n");
  
  echo("      function EscondeLayer(cod_layer)\n");
  echo("      {\n");
  echo("        hideLayer(cod_layer);\n");
  echo("      }\n\n");

  echo("      function EscondeLayers()\n");
  echo("      {\n");
  /* Se estiver visualizando os f�uns dispon�eis ent� esconde os layers   */
  /* para cria�o de um novo f�um, para acesso � op�es (Ver, Configurar, */
  /* Renomear e Apagar) de cada f�um e para acesso as op�es (Editar, */
  /* Apagar, Ver Notas, Ver Atividades Entregues e Ver Atividades Pendentes) de avalia�o.                                                     */
  if(!EVisitante($sock,$cod_curso,$cod_usuario))
  {
    if (EFormador($sock,$cod_curso,$cod_usuario))
    {
      echo("        hideLayer(lay_conf);\n");
      echo("        hideLayer(lay_novo);\n");
    }
  }
  echo("      }\n\n");

  echo("      function MostraLayer(cod_layer, ajuste, ev)\n");
  echo("        {\n");
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

  if(!EVisitante($sock,$cod_curso,$cod_usuario))
  {
    if (EFormador($sock,$cod_curso,$cod_usuario))
    {
      echo("      var statusConf =  \"".((GruposFechados($sock))?"T":"A")."\";\n");

      echo("      function Configurar(event)\n");
      echo("      {\n");
      echo("        document.getElementById('configuracao_A').innerHTML='&nbsp;';\n");
      echo("        document.getElementById('configuracao_T').innerHTML='&nbsp;';\n");
      echo("        var imagem=\"<img src='../imgs/checkmark_blue.gif' alt='blue.gif'/>\"\n");
      echo("        document.getElementById('configuracao_'+statusConf).innerHTML=imagem;\n");
      echo("        MostraLayer(lay_conf, 0, event);\n");
      echo("      }\n\n");
    }
  }
  
  echo("      function AbreListaGrupos()\n");
  echo("      {\n");
  echo("        spans = document.getElementsByTagName('span');\n");

  echo("        for (k=0; k < spans.length; k++){\n");
  echo("          if (spans[k].id.substr(0, 6).match(\"grupo_\")){\n");
  echo("		  {");
  // Verifica se o Grupo ja esta aberto
  echo("			var tmp_cod = spans[k].id.slice(6, spans[k].id.length);");
  echo("  			var aberto = document.getElementById('aberto_'+tmp_cod).value;\n");
  echo("			if (aberto == 0)");
  echo("		  	{");
  echo("            	spans[k].onclick();\n");
  echo("		  	}");
  echo("		  }");
  echo("          }\n");
  echo("        }\n");
  /* 66 - Ocultar Todos  */
  echo("        document.getElementById('componentes_grupos').innerHTML = '".RetornaFraseDaLista($lista_frases,66)."';\n");
  echo("        document.getElementById('componentes_grupos').onclick = function(){ FechaListaGrupos(); };\n");
  echo("        NGruposAbertos = NGrupos;\n");
  echo("      }\n\n");

  echo("      function FechaListaGrupos()\n");
  echo("      {\n");
  echo("        spans = document.getElementsByTagName('span');\n");
  echo("        for (k=0; k < spans.length; k++){\n");
  echo("          if (spans[k].id.substr(0, 7).match(\"fechar_\")){\n");
  echo("            spans[k].onclick();\n");
  echo("          }\n");
  echo("        }\n");
  echo("        document.getElementById('componentes_grupos').innerHTML = '".RetornaFraseDaLista($lista_frases,78)."';\n");
  echo("        document.getElementById('componentes_grupos').onclick = function(){ AbreListaGrupos(); };\n");
  echo("        NGruposAbertos = 0;\n");
  echo("      }\n\n");

  echo("      function IncluirComponentes(cod_grupo)\n");
  echo("      {\n");
  echo("        janelaIncluir = window.open('novo_comp.php?cod_curso=".$cod_curso."&cod_grupo='+cod_grupo,'MostrarComponentes','width=620,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n\n");
  echo("      }\n");

  echo("      function ExcluirComponentes(cod_grupo)\n");
  echo("      {\n");
  /* 64 - Deseja remover deste grupo os componentes selecionados? */
  echo("        if(!confirm('".RetornaFraseDaLista($lista_frases,64)."')) return;\n");
//   echo("        var array = new Array();\n");
  echo("        var array = document.getElementsByName('chk_grupo_'+cod_grupo);\n");
  echo("        var parent = array[0].parentNode;\n");
  echo("        var temp1='span2_grupo_'+cod_grupo+'_';\n");
  echo("        var temp2='';\n");
  echo("        var arraySelect = new Array();\n");
  echo("        j=0;\n");
  echo("        for (i=0; i < array.length; i++){\n");
  echo("          if (array[i].checked){\n");
  echo("            arraySelect[j]=array[i].id.split('_')[4];\n");
  echo("            temp2=temp1+arraySelect[j];\n");
  echo("            parent.removeChild(document.getElementById(temp2));\n");
  echo("            parent.removeChild(array[i]);\n");
  echo("            j++;\n");
  echo("            i--;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        if (!i){\n");
  echo("          document.getElementById('span_grupo_'+cod_grupo).innerHTML='".RetornaFraseDaLista($lista_frases, 31)."<br />';\n");
  echo("          document.getElementById('span_li2_grupo_'+cod_grupo).onclick = function(){ ExcluirComponentes(cod_grupo); };\n");
  echo("          document.getElementById('li2_grupo_'+cod_grupo).className='menuUp';\n");
  echo("        }\n");
  /* 74 - Componente(s) excluído(s) do grupo com sucesso. */
  echo("        if(j) xajax_ExcluirComponentesDinamic('".$cod_curso."', cod_grupo, arraySelect, '".RetornaFraseDaLista($lista_frases, 74)."');\n");
  echo("        document.getElementById('usuarios_grupo_'+cod_grupo).innerHTML=array.length;\n");
  echo("        RecarregarGrupos(-1);\n");
  echo("      }\n");
  
  echo("      function VerificarCheckBox(cod_grupo, cod_usuario){");
  echo("        var j=0;\n");
  echo("        if (document.getElementById('chk_grupo_'+cod_grupo+'_cmp_'+cod_usuario).checked){\n");
  echo("          j=1;\n");
  echo("        }else {\n");
  echo("          var arrayChk = new Array();\n");  
  echo("          arrayChk = document.getElementsByName('chk_grupo_'+cod_grupo);\n");
  echo("          for(i=0; i < arrayChk.length; i++){\n");
  echo("            if (arrayChk[i].checked){\n");
  echo("              j=1; break;\n");
  echo("            }\n");
  echo("          }\n");
  echo("        }\n");
  echo("        if(j){\n");
  echo("          document.getElementById('span_li2_grupo_'+cod_grupo).onclick = function(){ ExcluirComponentes(cod_grupo); };\n");
  echo("          document.getElementById('li2_grupo_'+cod_grupo).className='menuUp02';\n");
  echo("          startList();\n");
  echo("        }else{\n");
  echo("          document.getElementById('span_li2_grupo_'+cod_grupo).onclick = function(){  };\n");
  echo("          document.getElementById('li2_grupo_'+cod_grupo).className='menuUp';\n");  
  echo("        }\n");
  echo("      }\n");

  echo("      function MostrarComponentes(cod_grupo)\n");
  echo("      {\n");
  
  echo("        var trElementTmp = document.getElementById('tr_grupo_'+cod_grupo);\n");
  //TOZO
  echo("		document.getElementById('grupo_'+cod_grupo).onclick = function() { FechaGrupo(cod_grupo); FecharComponentes(cod_grupo); };");
  echo("        if((trElementTmp)&&(cod_grupo!=-1)){\n");
  echo("          if (navigator.appName==\"Microsoft Internet Explorer\"){\n");
  echo("            trElementTmp.style.display=\"block\";\n");
  echo("          }else{\n");
  echo("            trElementTmp.style.display=\"table-row\";\n");
  echo("          }\n");
  echo("		return");
  echo("        }\n");
  echo("        var trElement = document.getElementById('tr_'+cod_grupo);\n");
  echo("        var tableElement = trElement.parentNode;\n");
  echo("        trElement=trElement.nextSibling;\n");
  echo("        if(document.getElementById('td_grupo_'+cod_grupo))\n");
  echo("		{");
  echo("			var tSpan = document.getElementById('fechar_'+cod_grupo);");
  echo("			tSpan.onclick = function() { FechaGrupo(cod_grupo); FecharComponentes(cod_grupo); };");
  echo("			return;");
  echo("		}");
  echo("        newElement = document.createElement('tr');\n");
  echo("        newElement.setAttribute('id', 'tr_grupo_'+cod_grupo);\n");
  echo("        newElement.className='altColor2';\n");
  echo("        newTd = document.createElement('td');\n");
  echo("        newTd.setAttribute('id', 'td_grupo_'+cod_grupo);\n");
  if (!($bool_grupos_fechados))
    echo("        newTd.colSpan=2;\n");  

  echo("        newSpan = document.createElement('span');\n");
  echo("        newSpan.setAttribute('id', 'span_grupo_'+cod_grupo);\n");
  echo("        newSpan.style.fontWeight='bold';\n");
  echo("        newTd.appendChild(newSpan);\n");
  echo("        newSpan = document.createElement('span');\n");
  echo("        newSpan.setAttribute('id', 'span2_grupo_'+cod_grupo);\n");
  echo("        newTd.appendChild(newSpan);\n");
  echo("        newElement.appendChild(newTd);\n\n");
  echo("        newTd = document.createElement('td');\n");
  echo("        newTd.setAttribute('id', 'td_close_'+cod_grupo);\n\n");
  echo("        newSpan = document.createElement('span');\n");
  /* 13 (ger) - Fechar */
  echo("        newSpan.innerHTML='".RetornaFraseDaLista($lista_frases_geral, 13)."<br />';\n");
  echo("        newSpan.className='link';\n");
  echo("        newSpan.setAttribute('id', 'fechar_'+cod_grupo);\n");
  echo("        newSpan.onclick = function(){ FechaGrupo(cod_grupo); FecharComponentes(cod_grupo); };\n");
  echo("        newTd.appendChild(newSpan);\n");

  echo("        newElement.appendChild(newTd);\n");

  echo("        tableElement.insertBefore(newElement, trElement);\n");

  echo("        document.getElementById('td_grupo_'+cod_grupo).align='left';\n");
  echo("        xajax_MostraGrupoDinamic(".$cod_curso.", cod_grupo, arrayMostraGrupo);\n");
  echo("      }\n\n");

  echo("      function FecharComponentes(cod_grupo){\n");
  echo("          trElement = document.getElementById('tr_grupo_'+cod_grupo);\n");
    //TOZO
  echo("		document.getElementById('grupo_'+cod_grupo).onclick = function() { AbreGrupo(cod_grupo); MostrarComponentes(cod_grupo); };");
  echo("        if(cod_grupo==-1){\n");
  echo("          tableElement = trElement.parentNode;\n");
  echo("          tableElement.removeChild(trElement);\n");
  echo("        }else{\n");
  echo("          trElement.style.display=\"none\";\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("      function EdicaoTitulo(codigo, id, valor){\n");
  echo("        if ((valor=='ok')&&(document.getElementById(id+'_text').value!=\"\")){\n");
  echo("          conteudo = document.getElementById(id+'_text').value;\n");
  /* 75 - grupo renomeado com sucesso */
  echo("          xajax_EditarTituloDinamic('".$cod_curso."', codigo, conteudo, '".RetornaFraseDaLista($lista_frases, 75)."');\n");
  echo("        }else{\n");
          /* 15 - O titulo nao pode ser vazio. */
  echo("          if ((valor=='ok')&&(document.getElementById(id+'_text').value==\"\"))\n");
  echo("            alert('".RetornaFraseDaLista($lista_frases, 15)."');\n");
  echo("          document.getElementById(id).innerHTML=conteudo;\n");
  echo("          document.getElementById(id).className='link';\n");
  echo("          document.getElementById(id).onclick = function(){ MostrarComponentes(codigo); };\n");
  echo("        }\n");
  echo("        document.getElementById('grupo_'+codigo).style.textDecoration=\"none\";\n");
  echo("      }\n\n");

  // precisa sempre recarregar o grupo de "componentes sem grupo"
  echo("      function RecarregarGrupos(cod_grupo){\n");
  echo("        if (trElement = document.getElementById('tr_grupo_'+cod_grupo)){\n");
  echo("          var tableElement = trElement.parentNode;\n");
  echo("          tableElement.removeChild(trElement);\n");
  echo("        }\n");
  echo("        MostrarComponentes(cod_grupo);\n");
  echo("      }\n");
  
  echo("      function Renomear(id){\n");
  echo("        if (document.getElementById('CancelaEdita'))\n");
  echo("          document.getElementById('CancelaEdita').onclick();\n\n");

  echo("        id_aux = id;\n");
  echo("        conteudo = document.getElementById('grupo_'+id).innerHTML;\n");
  echo("        document.getElementById('grupo_'+id).className=\"\";\n");
  echo("        document.getElementById('grupo_'+id).style.fontWeight=\"\";\n");

  echo("        createInput = document.createElement('input');\n");
  echo("        document.getElementById('grupo_'+id).innerHTML='';\n");
  echo("        document.getElementById('grupo_'+id).onclick=function(){ };\n\n");

  echo("        createInput.setAttribute('type', 'text');\n");
  echo("        createInput.setAttribute('style', 'border: 2px solid #9bc');\n");
  echo("        createInput.setAttribute('id', 'grupo_'+id+'_text');\n\n");
  echo("        createInput.setAttribute('onkeypress', 'EditaTituloEnter(this, event, id_aux)');\n\n"); 

  echo("        document.getElementById('grupo_'+id).appendChild(createInput);\n");
  echo("        xajax_DecodificaString('grupo_'+id+'_text', conteudo, 'value');\n\n");

  echo("        //cria o elemento 'espaco' e adiciona na pagina\n");
  echo("        espaco = document.createElement('span');\n");
  echo("        espaco.innerHTML='&nbsp;&nbsp;'\n");
  echo("        document.getElementById('grupo_'+id).appendChild(espaco);\n\n");

  echo("        createSpan = document.createElement('span');\n");
  echo("        createSpan.className='link';\n");
  echo("        createSpan.onclick= function(){ EdicaoTitulo(id, 'grupo_'+id, 'ok'); };\n");
  echo("        createSpan.setAttribute('id', 'OkEdita');\n");
  echo("        createSpan.innerHTML='".RetornaFraseDaLista($lista_frases_geral, 18)."';\n");
  echo("        document.getElementById('grupo_'+id).appendChild(createSpan);\n\n");

  echo("        //cria o elemento 'espaco' e adiciona na pagina\n");
  echo("        espaco = document.createElement('span');\n");
  echo("        espaco.innerHTML='&nbsp;&nbsp;'\n");
  echo("        document.getElementById('grupo_'+id).appendChild(espaco);\n\n");

  echo("        createSpan = document.createElement('span');\n");
  echo("        createSpan.className='link';\n");
  echo("        createSpan.onclick= function(){ EdicaoTitulo(id, 'grupo_'+id, 'canc'); };\n");
  echo("        createSpan.setAttribute('id', 'CancelaEdita');\n");
  echo("        createSpan.innerHTML='".RetornaFraseDaLista($lista_frases_geral, 2)."';\n");
  echo("        document.getElementById('grupo_'+id).appendChild(createSpan);\n\n");

  echo("        //cria o elemento 'espaco' e adiciona na pagina\n");
  echo("        espaco = document.createElement('span');\n");
  echo("        espaco.innerHTML='&nbsp;&nbsp;'\n");
  echo("        document.getElementById('grupo_'+id).appendChild(espaco);\n\n");

  echo("        startList();\n");
  echo("        cancelarElemento=document.getElementById('CancelaEdita');\n");
  echo("        document.getElementById('grupo_'+id+'_text').select();\n");
//   echo("        document.getElementById('forumQtd_'+id).style.display=\"none\";\n");
  echo("      }\n\n");

  echo("      function ExcluirGrupo(cod_grupo)\n");
  echo("      {\n");
  echo("        nome_grupo = document.getElementById('grupo_'+cod_grupo).innerHTML;\n");
  echo("        /* 40 - Tem certeza de que deseja excluir o grupo */\n");
  echo("        if(confirm('".RetornaFraseDaLista($lista_frases,40)." '+nome_grupo+' ?')){\n");
  echo("          document.location='acoes.php?acao=apagar_grupo&cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_grupo='+cod_grupo;\n");
  echo("        }\n");
  echo("      }\n");

  echo("      function VerificaNovoTitulo(textbox, aspas) {\n");
  echo("        texto=textbox.value;\n");
  echo("        if (texto==''){\n");
  echo("          // se nome for vazio, nao pode\n");
                  /* 16 - O nome do seu grupo não pode ser vazio */
  echo("          alert(\"".RetornaFraseDaLista($lista_frases,16)."\");\n");
  echo("          textbox.focus();\n");
  echo("          return false;\n");
  echo("        }\n");
  /* 15 - Já existe um grupo com este nome */
  echo("        xajax_VerificaNovoTituloDinamic(".$cod_curso.",texto, '".RetornaFraseDaLista($lista_frases,15)."');\n");
  echo("        return false;\n");
  echo("      }\n\n");

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
  echo ("             EdicaoTitulo(id, 'grupo_'+id, 'ok');\n"); //A função e parâmetros são os mesmos utilizados na função de edição já utilizada.
  echo ("         }\n\n");
  echo ("         return true;\n");
  echo ("     }\n\n");

  
  $lista_grupos=RetornaListaGrupos($sock, $cod_curso);
  $num_grupos=count($lista_grupos);

  echo("var NGrupos = ".$num_grupos."+1;\n");
  echo("var NGruposAbertos = 0;\n");

  // AbreGrupo: Ajuda na contabilidade de quantos Grupos
  // encontram-se abertos ou fechados, afim de acertar o

  // bot�o Componentes/Ocultar
  echo("function AbreGrupo(cod_grupo){\n");
  echo("  var aberto=document.getElementById('aberto_'+cod_grupo).value;\n");
  echo("  document.getElementById('aberto_'+cod_grupo).value = 1;\n");
  echo("  if(aberto == 0) NGruposAbertos++;\n");

  echo("  if ((NGrupos - NGruposAbertos) == 0){\n");
  /* 66 - Ocultar Todos */
  echo("    document.getElementById('componentes_grupos').innerHTML = '".RetornaFraseDaLista($lista_frases,66)."';\n");
  echo("    document.getElementById('componentes_grupos').onclick = function(){ FechaListaGrupos(); };\n");
  echo("  }\n");
  echo("}\n");

  // FechaGrupo: Ajuda na contabilidade de quantos Grupos
  // encontram-se abertos ou fechados, afim de acertar o

  // bot�o Componentes/Ocultar
  echo("function FechaGrupo(cod_grupo){\n");
  echo("  var aberto=document.getElementById('aberto_'+cod_grupo).value;\n");
  /* 3 - Componentes dos Grupos */
  echo("  document.getElementById('componentes_grupos').innerHTML = '".RetornaFraseDaLista($lista_frases,3)."';\n");
  echo("  document.getElementById('componentes_grupos').onclick = function(){ AbreListaGrupos(); };\n");
  echo("  if (aberto == 1) NGruposAbertos--;\n"); 
  echo("  document.getElementById('aberto_'+cod_grupo).value = 0;\n");
  echo("}");

  echo("    </script>\n");

  $objAjax->printJavascript();

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  if (EVisitante($sock, $cod_curso, $cod_usuario))
  {
    /* 1 - Grupos */
    /*504 - �ea restrita a alunos e formadores */
    echo("          <h4>".RetornaFraseDaLista($lista_frases, 1)." - ".RetornaFraseDaLista($lista_frases_geral, 504)."</h4>\n");
    // 3 A's - Muda o Tamanho da fonte
    echo("          <div id=\"mudarFonte\">\n");
    echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
    echo("          </div>\n");
    
    /* 509 - Voltar */
    echo("          <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
    /* 23 - Voltar */
    echo("          <form>\n");
    echo("            <input class=\"input\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral, 23)."\" onclick=\"javascript:history.go(-1);\" />\n");
    echo("          </form>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("    </table>\n");
    echo("  </body>\n");
    echo("</html>\n");
    Desconectar($sock);
    exit();
  }

  /* 1 - Grupos */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,1));

  if ($bool_grupos_fechados)
  {
    /* 80 - Não permitir alteração */
    $tmp=RetornaFraseDaLista($lista_frases,80);
  }
  else
  {
    /* 79 - Permitir alteração */
    $tmp=RetornaFraseDaLista($lista_frases,79);
  }

  echo(" - ".$tmp."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("          <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <!-- Botoes de Acao -->\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");

  // Visitante n�o podem criar grupos:
  if(!EVisitante($sock,$cod_curso,$cod_usuario))
  {
    if (!$bool_grupos_fechados)
    {
      /* 2 - Novo Grupo */
      echo("                  <li><span 
  onclick=\"MostraLayer(lay_novo, 0 , event); 
  document.getElementById('novo_nome').focus(); 
  document.getElementById('novo_nome').value='';\">".RetornaFraseDaLista($lista_frases,2)."</span></li>\n");
    }
  }

  /* 4 - Grupos dos Componentes */
  echo("                  <li><a href=\"componentes.php?cod_curso=".$cod_curso."\" >".RetornaFraseDaLista($lista_frases,81)."</a></li>\n");

  if(!EVisitante($sock,$cod_curso,$cod_usuario))
  {
    if (EFormador($sock,$cod_curso,$cod_usuario))
    {

    echo("                <form action=\"salvar_arquivo.php\" method=\"get\" name=\"formSalvar\">\n");
    echo("                  <input type=\"hidden\" name=\"cod_curso\"    value=\"".$cod_curso."\" />\n");
    echo("                  <input type=\"hidden\" name=\"nome_arquivo\" value=\"grupos.html\" />\n");
    echo("                  <input type=\"hidden\" name=\"origem\"       value=\"grupos\" />\n");
    if (isset($check_ultimos))
      echo("                  <input type=\"hidden\" name=\"check_ultimos\" value=1 />\n");
    if (isset($check_qtde))
      echo("                  <input type=\"hidden\" name=\"check_qtde\"    value=1 />\n");
    if (isset($check_local))
      echo("                  <input type=\"hidden\" name=\"check_local\"   value=1 />\n");
    if (isset($check_cidade))
      echo("                  <input type=\"hidden\" name=\"check_cidade\"  value=1 />\n");
    if (isset($check_estado))
      echo("                  <input type=\"hidden\" name=\"check_estado\"  value=1 />\n");
    if (isset($radio_ord))
      echo("                  <input type=\"hidden\" name=\"radio_ord\"     value=".$radio_ord." />\n");
    echo("                </form>\n");

      /* usuario eh formador: permitir abertura e fechamento de grupos */
      /* 46 - Configurar Grupos */
      echo("                  <li><span onclick=\"Configurar(event);\">".RetornaFraseDaLista($lista_frases,46)."</span></li >");
      /*  Salvar/Imprimir */
     // echo("                  <li><span onClick=\"SalvaImprime();\">".RetornaFraseDaLista($lista_frases,72)."</span></li>\n");
      
    }
  }
  echo("                </ul>\n");
  echo("                <br /><br />\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">");
  /* 76 - Lista de Grupos */
  echo("                    <td>&nbsp;&nbsp;".RetornaFraseDaLista($lista_frases,76)."</td>\n");
  /* 77 - Número de Integrantes */
  echo("                    <td width=\"10%\">".RetornaFraseDaLista($lista_frases,77)."&nbsp;&nbsp;</td>\n");
  /* 70 (ger) - Opções */
  if (!$bool_grupos_fechados)
      echo("                    <td width=\"10%\">".RetornaFraseDaLista($lista_frases_geral,70)."</td>\n");
  echo("                  </tr>\n");

//   $lista_grupos=RetornaListaGrupos($sock);
// 
//   $num_grupos=count($lista_grupos);
  if ($num_grupos > 0){
    $icone = "<img src=\"../imgs/icGrupo.gif\" border=\"0\" alt=\"".RetornaFraseDaLista($lista_frases, 1)."\" />";
    $num_usuario=0;
    foreach ($lista_grupos as $cod => $linha)
    {

      echo("                  <tr id=\"tr_".$linha['cod_grupo']."\" class=\"altColor".($num_usuario%2)." alLeft\">\n");
      echo("                    <td>&nbsp;&nbsp;".$icone." <span id=\"grupo_".$linha['cod_grupo']."\" class=\"link\" onclick=\"AbreGrupo(".$linha['cod_grupo']."); MostrarComponentes(".$linha['cod_grupo'].");\">".$linha['nome_grupo']."</span><input type=\"hidden\" id=\"aberto_".$linha['cod_grupo']."\" value=0 /></td>\n");

      echo("                    <td align=\"center\" id=\"usuarios_grupo_".$linha['cod_grupo']."\">".$linha['num_usuarios']."</td>\n");
      if (!$bool_grupos_fechados){
        echo("                    <td width=\"10%\" align=\"center\" valign=\"top\" class=\"botao2\">\n");
        echo("                      <ul>\n");
        /* 19 (ger) - Renomear */
        echo("                        <li><span onclick='Renomear(\"".$linha['cod_grupo']."\");'>".RetornaFraseDaLista($lista_frases_geral, 19)."</span></li>\n");
        /* 12 (ger) - Excluir */
        echo("                        <li><span onclick='ExcluirGrupo(\"".$linha['cod_grupo']."\");'>".RetornaFraseDaLista($lista_frases_geral, 12)."</span></li>\n");
        echo("                      </ul>\n");
        echo("                    </td>\n");
      }
      echo("                  </tr>\n");
      $num_usuario++;
    }
  }

  $lista_sem=RetornaUsuariosSemGrupo($sock, $cod_curso);
  $num_sem=count($lista_sem);

  /* 9 - Participantes sem grupo */
  echo("                  <tr id=\"tr_-1\" class=\"head01 alLeft\">\n");
  echo("                     <td>&nbsp;&nbsp;<span id=\"grupo_-1\" class=\"link\" onclick=\"AbreGrupo(-1); MostrarComponentes(-1);\">".RetornaFraseDaLista($lista_frases,9)."</span><input type=\"hidden\" id=\"aberto_-1\" value=0></td>\n");
  echo("                    <td id=\"usuarios_grupo_-1\" align=\"center\">".$num_sem."</td>\n");
  if (!$bool_grupos_fechados){
    echo("                    <td>&nbsp;</td>\n");
  }
  echo("                  </tr>\n");
  echo("                </table>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 78 - Detalhar Todos */
  echo("                  <li><span id=\"componentes_grupos\" onclick=\"AbreListaGrupos();\" >".RetornaFraseDaLista($lista_frases,78)."</span></li>\n");
  /*  Salvar/Imprimir */
  echo("                  <li><span onClick=\"SalvaImprime();\">".RetornaFraseDaLista($lista_frases,72)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("      <tr>\n");
  echo("        <td>\n");
  //div do Layer de alteração da configuração
  echo("    <div id=\"layer_conf\" class=\"popup\">\n");
  echo("      <div class=\"posX\"><span onclick=\"EscondeLayer(lay_conf);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("      <div class=\"int_popup\">\n");
  echo("        <form method=\"post\" id=\"formConfiguracao\" name=\"formConfiguracao\" action=\"\">\n");
  echo("          <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
  echo("          <input type=\"hidden\" name=\"nova_configuracao\" id=\"nova_relevancia\" value=\"\" />\n");
  echo("        </form>\n");
  echo("        <ul class=\"ulPopup\">\n");
  /* 79 - Permitir Alteração */
  echo("          <li onclick=\"xajax_MudarConfiguracaoDinamic(xajax.getFormValues('formConfiguracao'), 'A'); EscondeLayers();\">\n");
  echo("            <span class=\"check\" id=\"configuracao_A\"></span>\n");
  echo("            <span>".RetornaFraseDaLista($lista_frases, 79)."</span>\n");
  echo("          </li>\n");
  /* 80 - Não Permitir Alteração */
  echo("          <li onclick=\"xajax_MudarConfiguracaoDinamic(xajax.getFormValues('formConfiguracao'), 'T'); EscondeLayers();\">\n");
  echo("            <span class=\"check\" id=\"configuracao_T\"></span>\n");
  echo("            <span>".RetornaFraseDaLista($lista_frases, 80)."</span>\n");
  echo("          </li>\n");
  echo("        </ul>\n");
  echo("      </div>\n");
  echo("    </div>\n\n");
  
  /* Novo Item */
  echo("    <div id=\"layer_novo\" class=\"popup\">\n");
  echo("     <div class=\"posX\"><span onclick=\"EscondeLayer(lay_novo);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("      <div class=\"int_popup\">\n");
  echo("        <form name=\"form_novo_grupo\" method=\"post\" action=\"acoes.php\" onsubmit=\"'return (VerificaNovoTitulo(document.form_novo_grupo.novo_nome, 1));'\">\n");

  echo("          <div class=\"ulPopup\">\n");
  /* 71 - Digite o título do grupo a ser criado aqui: */
  echo("            ".RetornaFraseDaLista($lista_frases,71)."<br />\n");
  echo("            <input class=\"input\" type=\"text\" name=\"novo_nome\" id=\"novo_nome\" value=\"\" maxlength=\"150\" /><br />\n");

  /* 18 - Ok (gen) */
  echo("            <input type=\"submit\" id=\"ok_novogrupo\" class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" />\n");
  /* 2 - Cancelar (gen) */
  echo("            &nbsp; &nbsp; <input type=\"button\" class=\"input\"  onclick=\"EscondeLayer(lay_novo);\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");
  echo("           </div>\n");
  echo("           <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
  
  echo("          <input type=\"hidden\" name=\"acao\" value=\"criar_grupo\" />\n");
  echo("        </form>\n");
  echo("      </div>\n");
  echo("    </div>\n");

  echo("        </td>\n");
  echo("      </tr>\n");

  include("../tela2.php");

  echo("  </body>\n");
  echo("</html>");
  Desconectar($sock);
?>
