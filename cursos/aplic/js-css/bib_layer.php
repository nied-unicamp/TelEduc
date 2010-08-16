<?php  
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : bib_layer.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist�ncia
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

    Nied - N�cleo de Inform�tica Aplicada � Educa��o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit�ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : bib_layer.php
  ========================================================== */

    /* Fun�es JavaScript */
    //echo("<script language=JavaScript src=../bibliotecas/dhtmllib.js></script>\n");
    echo("<script language=JavaScript>\n");
    echo("var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
    echo("var isMinNS6 = ((navigator.userAgent.indexOf(\"Gecko\") != -1) && (isNav));\n");
    echo("var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");
    echo("var Xpos, Ypos;\n");
    echo("var js_cod_item, js_cod_topico;\n");
    echo("var js_nome_topico;\n");
    echo("var js_tipo_item;\n");
    echo("var js_comp = new Array();\n");

    echo("if (isNav)\n");
    echo("{\n");
    echo("  document.captureEvents(Event.MOUSEMOVE);\n");
    echo("}\n");
    echo("document.onmousemove = TrataMouse;\n");

    echo("function TrataMouse(e)\n");
    echo("{\n");
    echo("  Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
    echo("  Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
    echo("}\n");

    echo("function getPageScrollY()\n");
    echo("{\n");
    echo("  if (isNav)\n");
    echo("    return(window.pageYOffset);\n");
    echo("  if (isIE)\n");
    echo("    return(document.body.scrollTop);\n");
    echo("}\n");

    echo("function AjustePosMenuIE()\n");
    echo("{\n");
    echo("  if (isIE)\n");
    echo("    return(getPageScrollY());\n");
    echo("  else\n");
    echo("    return(0);\n");
    echo("}\n");

    echo("function Iniciar()\n");
    echo("{\n");
    echo(" alert(\"ok!\n");
//     echo("  cod_menu = getLayer(\"menu\");\n");
    echo("  cod_menu = getLayer(\"menu\");\n");
    echo("  cod_menu_avaliado = getLayer(\"menu_avaliado\");\n");
    echo("  cod_comp = getLayer(\"comp\");\n");
//     echo("  cod_menu_top = getLayer(\"menutop\");\n");
//     echo("  cod_ren_top = getLayer(\"renomeartop\");\n");
    echo("  cod_novo_top = getLayer(\"novotop\");\n");
    echo("  cod_mover = getLayer(\"mover\");\n");
    echo("  cod_topicos = getLayer(\"topicos\");\n");
//     echo("  cod_mudar_pos = getLayer(\"mudarpos\");\n");
    echo("  EscondeLayers();\n");
    echo("}\n");
    echo("\n");

    echo("function EscondeLayers()\n");
    echo("{\n");
//     echo("  hideLayer(cod_menu);\n");
    echo("  hideLayer(cod_comp);\n");
//     echo("  hideLayer(cod_menu_top);\n");
//     echo("  hideLayer(cod_ren_top);\n");
    echo("  hideLayer(cod_novo_top);\n");
    echo("  hideLayer(cod_mover);\n");
    echo("  hideLayer(cod_topicos);\n");
//     echo("  hideLayer(cod_mudar_pos);\n");
    echo("}\n");

/*    echo("  if (isIE) \n");
    echo("    document.form_renomear_top.novo_nome.focus();\n");
*/
    //echo("}\n");

    echo("function testa_titulo_criar(form)\n");
    echo("{\n");
    echo("  return (PossoRenomear(form));\n");
    echo("}\n");

    echo("function PossoRenomear(form) {\n");
    echo("  nome=form.novo_nome.value;\n");
    echo("  if (nome==js_nome_topico) {\n");
    /* 3 - Nenhuma alteracao foi efetuada. */
    echo("    alert(\"".RetornaFraseDaLista($lista_frases,3)."\");\n");
    echo("    hideLayer(cod_ren);\n");
    echo("    return(false);\n");
    echo("  } else {\n");
    echo("// se nome for vazio, nao pode\n");

    echo("      while (nome.search(\" \") != -1)\n");
    echo("      {\n");
    echo("        nome = nome.replace(/ /, \"\");\n");
    echo("      }\n");

    echo("    if (nome==\"\") {\n");
    /* 4 - O titulo do item a ser renomeado nao pode ser vazio. */
    echo("      alert(\"".RetornaFraseDaLista($lista_frases,4)."\");\n");
    echo("      return(false);\n");
    echo("    } else {\n");
    echo("// se nome tiver aspas, <, >, nao pode\n");
    echo("      if (nome.indexOf(\"\\\\\")>=0 || nome.indexOf(\"\\\"\")>=0 || nome.indexOf(\"'\")>=0 || nome.indexOf(\">\")>=0 || nome.indexOf(\"<\")>=0) {\n");
    /* 5 - O titulo do item a ser renomeado nao pode conter \\\", \\\', < ou >. */
    echo("        alert(\"".ConverteAspas2BarraAspas(ConverteHtml2Aspas(RetornaFraseDaLista($lista_frases,5)))."\");\n");
    echo("        return(false);\n");
    echo("      } else {\n");
    echo("        return(true);\n");
    echo("      }\n");
    echo("    }\n");
    echo("  }\n");
    echo("}\n");

    echo("function AtualizaComp(js_tipo_comp)\n");
    echo("{\n");
    echo("  if ((isNav) && (!isMinNS6)) {\n");
    echo("    document.comp.document.form_comp.tipo_comp.value=js_tipo_comp;\n");
    echo("    document.comp.document.form_comp.cod_item.value=js_cod_item;\n");
    echo("    if (js_tipo_comp=='F') {\n");
    echo("      document.comp.document.form_comp.tipo_comp[0].checked=true;\n");
    echo("      document.comp.document.form_comp.tipo_comp[1].checked=false;\n");
    echo("    } else {\n");
    echo("      document.comp.document.form_comp.tipo_comp[1].checked=true;\n");
    echo("      document.comp.document.form_comp.tipo_comp[0].checked=false;\n");
    echo("    }\n");
    echo("  } else {\n");
    echo("    if (isIE || ((isNav)&&(isMinNS6)) ){\n");
    echo("      document.form_comp.tipo_comp.value=js_tipo_comp;\n");
    echo("      document.form_comp.cod_item.value=js_cod_item;\n");
    echo("      if (js_tipo_comp=='F') {\n");
    echo("        document.form_comp.tipo_comp[0].checked=true;\n");
    echo("        document.form_comp.tipo_comp[1].checked=false;\n");
    echo("      } else {\n");
    echo("        document.form_comp.tipo_comp[1].checked=true;\n");
    echo("        document.form_comp.tipo_comp[0].checked=false;\n");
    echo("      }\n");
    echo("    }\n");
    echo("  }\n");
    echo("}\n");

    echo("function MostraLayer(cod_layer, ajuste)\n");
    echo("{\n");
    echo("  EscondeLayers();\n");
    echo("  moveLayerTo(cod_layer,Xpos-ajuste,Ypos+AjustePosMenuIE());\n");
    echo("  showLayer(cod_layer);\n");
    echo("}\n");

    echo("function EscondeLayer(cod_layer)\n");
    echo("{\n");
    echo("  hideLayer(cod_layer);\n");
    echo("}\n");

    echo("function MoverItem(link,cod_destino)\n");
    echo("{\n");
    echo("  if (js_tipo_item=='item')\n");
    echo("  {\n");
    echo("    link.search='?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_item='+js_cod_item+'&cod_topico_raiz='+cod_destino+'&cod_topico_ant=".$cod_topico_raiz."&acao=moveritem';\n");
    echo("    return true;\n");
    echo("  }\n");
    echo("  else\n");
    echo("  {\n");
    echo("    if (js_tipo_item=='topico')\n");
    echo("    {\n");
    echo("      link.search='?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_topico='+js_cod_topico+'&cod_topico_raiz='+cod_destino+'&cod_topico_ant=".$cod_topico_raiz."&acao=movertopico';\n");
    echo("      return true;\n");
    echo("    }\n");
    echo("    else\n");
    echo("    {\n");
    echo("	if (js_tipo_item=='selec')\n");
    echo("	{\n");
    echo("        link.search='?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_itens='+js_cod_itens+'&cod_topicos='+js_cod_topicos+'&cod_topico_raiz='+cod_destino+'&cod_topico_ant=".$cod_topico_raiz."&acao=moverselecionados';\n");
    echo("        return true;\n");
    echo("	}\n");
    echo("      return false;\n");
    echo("    }\n");
    echo("  }\n");
    echo("}\n");

    if(($cod_ferramenta==3) && ($AcessoAvaliacao))
    {
      echo("function VerAvaliacao(id)\n");
      echo("{\n");
      echo("  window.open('../avaliacoes/ver_popup.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&VeioDaAtividade=1&origem=../material/material&cod_topico=".$cod_topico_raiz."&cod_avaliacao='+id,'VerAvaliacao','width=450,height=300,top=150,left=250,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
      echo("  return(false);\n");
      echo("}\n");
    }

    echo("\n");

    echo("function ControlaSelecao(chkbox)\n");
    echo("{\n");
    echo("  var tipo = 'top';\n");
    echo("  var conteudo;\n");
    echo("  if (chkbox.name.indexOf('itm') != -1) //selecionou um item\n");
    echo("  {\n");
    echo("    tipo = 'itm';\n");    
    echo("    conteudo = document.frmMaterial.ItensSelecionados.value;\n");
    echo("  }\n");
    echo("  else //selecionou um topico\n");
    echo("    conteudo = document.frmMaterial.TopSelecionados.value;\n");
    echo("  var achou = conteudo.indexOf(chkbox.value+' ');");
    echo("  //Adiciona o valor\n");
    echo("  if(chkbox.checked)\n");
    echo("  {\n");
    echo("    if (achou == -1)\n");
    echo("    {\n");
    echo("      if (conteudo.length == 0)\n");
    echo("        conteudo = chkbox.value + ' ';\n");
    echo("      else\n");
    echo("        conteudo += ',' + chkbox.value + ' ';\n");
    echo("    }\n");
    echo("  }\n");
    echo("  //Remove o valor\n");
    echo("  else\n");
    echo("  {\n");
    echo("    if (achou == 0) //eh o primeiro item\n");
    echo("    {\n");
    echo("      if (conteudo.indexOf(',') != -1) //tem mais itens selecionados\n");
    echo("        conteudo = conteudo.replace(chkbox.value+' ,','');\n");
    echo("      else\n");
    echo("        conteudo = conteudo.replace(chkbox.value+' ','');\n");
    echo("    }\n");
    echo("    else\n");
    echo("      conteudo = conteudo.replace(','+chkbox.value+' ','');\n");
    echo("  }\n");
    echo("  if (tipo == 'itm')\n");
    echo("    document.frmMaterial.ItensSelecionados.value = conteudo;\n");
    echo("  else\n");
    echo("    document.frmMaterial.TopSelecionados.value = conteudo;\n");
    echo("}\n");

    echo("function MarcaOuDesmarcaTodos()\n");
    echo("{\n");
    echo("  var e;\n");
    echo("  var i;\n");
    echo("  var CabMarcado = document.forms[0].cabecalho.checked;\n");
    echo("  for(i = 0; i < document.forms[0].elements.length; i++)\n");
    echo("  {\n");
    echo("    e = document.forms[0].elements[i];\n");
    echo("    if ((e.name.indexOf(\"itm\") == 0) || (e.name.indexOf(\"top\") == 0))\n");
    echo("    {\n");
    echo("      e.checked = CabMarcado;\n");
    echo("      ControlaSelecao(e);\n");
    echo("    }\n");
    echo("  }\n");
    echo("}\n");
    echo("\n");
    
    echo("function moverSelecionados() {\n");
    echo("  if ((document.forms[0].ItensSelecionados.value.length > 0) || ");
    echo("     (document.forms[0].TopSelecionados.value.length > 0))\n");
    echo("  {\n");
    echo("    js_cod_itens=document.forms[0].ItensSelecionados.value;\n");
    echo("    js_cod_topicos=document.forms[0].TopSelecionados.value;\n");
    echo("    js_tipo_item='selec';\n");
    echo("    MostraLayer(cod_mover,0);\n");
    echo("    return(false);\n");
    echo("  }\n");
    echo("}\n");
    
    echo("function excluirSelecionados() {\n");
    echo("  if((document.forms[0].ItensSelecionados.value.length > 0) || ");
    echo("    (document.forms[0].TopSelecionados.value.length > 0))\n");
    echo("  {\n");
    if ($cod_ferramenta == 3)
      echo("  if (TemCertezaApagarAtividade(1))\n");
    else
      echo("    if (TemCertezaApagar(1))\n");
    echo("    {\n");
    echo("      js_cod_itens=document.forms[0].ItensSelecionados.value;\n");
    echo("      js_cod_topicos=document.forms[0].TopSelecionados.value;\n");
    echo("        document.location='material.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&acao=apagarselecionados&cod_itens='+js_cod_itens+'&cod_topicos='+js_cod_topicos;\n");
    echo("    }\n");
    echo("  }\n");
    echo("}\n");
    echo("\n");
    echo("</script>\n");
    echo("\n");
?>