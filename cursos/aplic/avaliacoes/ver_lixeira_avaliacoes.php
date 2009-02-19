<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/ver_lixeira_avaliacoes.php

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

    Nied - Núcleo de Informática Aplicada à Educação
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitária "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/avaliacoes/ver_lixeira_avaliacoes.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("avaliacoes.inc");

  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,22);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,22);

  /* Verifica se o usuario eh formador. */
  $usr_formador = EFormador($sock, $cod_curso, $cod_usuario);

  /* 128 - Ação exclusiva a formadores. */
  if (!$usr_formador) exit(RetornaFraseDaLista($lista_frases,128));

  echo("<html>\n");
  /* 1 - Avaliações  */
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
  echo("  <link rel=stylesheet TYPE=text/css href=avaliacoes.css>\n");

  echo("  <script language=JavaScript src=../bibliotecas/dhtmllib.js></script>\n");
  echo("  <script language=JavaScript>\n\n");

  echo("    var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
  echo("    var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");

  echo("    if (isNav)\n");
  echo("    {\n");
  echo("      document.captureEvents(Event.MOUSEMOVE);\n");
  echo("    }\n");
  echo("    document.onmousemove = TrataMouse;\n\n");

  echo("    function TrataMouse(e)\n");
  echo("    {\n");
  echo("      Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
  echo("      Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
  echo("    }\n\n");

  echo("    var selected_item;\n");

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

  echo("    function iniciar()\n");
  echo("    {\n");
  /* Obtém o layer */
  /* para acesso às opções (Ver,EditarAvaliacao e ExcluirAvaliacao   */
  echo("      lay_avaliacao = getLayer('layer_avaliacao');\n");
  echo("    }\n\n");

  echo("    function EscondeLayer(cod_layer)\n");
  echo("    {\n");
  echo("      hideLayer(cod_layer);\n");
  echo("    }\n\n");

  echo("    function EscondeLayers()\n");
  echo("    {\n");
  /* Se estiver visualizando as avaliacoes atuais então esconde os layers   */
  /* para acesso às opções (Ver, Editar, */
  /* Apagar, Ver Notas, Ver Atividades Entregues e Ver Atividades Pendentes).                                                     */
  echo("      hideLayer(lay_avaliacao);\n");
  echo("    }\n\n");

  echo("    function MostraLayer(cod_layer)\n");
  echo("    {\n");
  echo("      EscondeLayers();\n");
  echo("      moveLayerTo(cod_layer, Xpos, Ypos + AjustePosMenuIE());\n");
  echo("      showLayer(cod_layer);\n");
  echo("    }\n\n");

  /* Cria as funções JavaScript */
  /* Ver(id), Editar(), Apagar(id), Ver Notas, Ver Atividades Entregues e Histórico de Notas    */
  echo("    function Ver(id)\n");
  echo("    {\n");
  echo("        document.frmAvaliacao.cod_avaliacao.value = id;\n");
  echo("        document.frmAvaliacao.action = \"ver.php?".RetornaSessionID());
  echo("&cod_curso=".$cod_curso."\";\n");
  echo("        document.frmAvaliacao.submit();\n");
  echo("    }\n\n");

  echo("    function ExcluirAvaliacao(id)\n");
    echo("    {\n");
    /* 129 - Você tem certeza de que deseja excluir esta avaliação? */
    /* 130 - (a avaliação será excluída definitivamente) */
    echo("  if(confirm('".RetornaFraseDaLista($lista_frases,129).RetornaFraseDaLista($lista_frases,130)."'))");
    echo("      {\n");
    echo("        document.frmAvaliacao.cod_avaliacao.value = id;\n");
    echo("        document.frmAvaliacao.operacao.value='excluir';\n");
    echo("        document.frmAvaliacao.action = \"excluir_avaliacao.php\";\n");
    echo("        document.frmAvaliacao.submit();\n");
    echo("      }\n");
    echo("    }\n\n");

    echo("    function RecuperarAvaliacao(id)\n");
    echo("    {\n");
    /* 118 - Você tem certeza de que deseja recuperar esta avaliação? */
    /* 119 - (Se você também excluiu a atividade a que a avaliação se refere e quiser também recuperá-la é necessário fazê-lo na respectiva ferramenta) */
    echo("  if(confirm('".RetornaFraseDaLista($lista_frases,118).RetornaFraseDaLista($lista_frases,119)."'))");
    echo("      {\n");
    echo("        document.frmAvaliacao.cod_avaliacao.value = id;\n");
    echo("        document.frmAvaliacao.action = \"excluir_avaliacao.php\";\n");
    echo("        document.frmAvaliacao.operacao.value='recuperar';\n");
    echo("        document.frmAvaliacao.submit();\n");
    echo("      }\n");
    echo("    }\n\n");

  echo("  </script>\n");

  echo("  <body link=#0000ff vlink=#0000ff bgcolor=white");

  //if ($usr_formador)
  echo(" onLoad='iniciar();'>\n");
  //else
  //  echo(">\n");

  /* Página Principal */
  /* 1 - Avaliações*/
  $cabecalho ="<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>\n";
  /* 16 - Lixeira (gen)*/
  $cabecalho.="<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases_geral,16)."</b>\n";

  $cod_pagina=9;
  /* Cabecalho */
  echo(PreparaCabecalho($cod_curso,$cabecalho,22,$cod_pagina));
  echo("<p>\n");
  echo("<table border=0 width=100%>\n");
  echo("  <tr class=menu>\n");
  /* 131 - Voltar para Avaliações */
  echo("    <td align=center><a href=\"avaliacoes.php?".RetornaSessionID()."&cod_curso=".$cod_curso."\" class=menu><b>".RetornaFraseDaLista($lista_frases,131)."</b></a></td>\n");

  echo("  </tr>\n");
  echo("</table>\n");
  echo("<br>\n");

  echo("    <form name=frmAvaliacao method=post>\n");

  echo(RetornaSessionIDInput());
  echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");
  echo("<input type=hidden name=operacao value=''>\n");
  echo("<input type=hidden name=status value='D'>\n");

  /* Passa o cod_avaliacao para executar ações sobre ela.       */
  echo("      <input type=hidden name=cod_avaliacao value=-1>\n");
  echo("    </form>\n");

  echo("<table border=0 width=100% cellspacing=0>\n");
  echo("  <tr>\n");
  echo("    <td width=10 class=colorfield>&nbsp;</td>\n");
  /* 1 - Avaliações */
  echo("    <td class=colorfield>".RetornaFraseDaLista($lista_frases,1)."</td>\n");
  /* 16 - Data de Início*/
  echo("    <td class=colorfield align=center>".RetornaFraseDaLista($lista_frases,16)."</td>\n");
  /* 17 - Data de Término*/
  echo("    <td class=colorfield align=center>".RetornaFraseDaLista($lista_frases,17)."</td>\n");
  echo("  </tr>\n");
  echo("  <tr>\n");
  echo("    <td colspan=4 height=5></td>\n");
  echo("  </tr>\n");

  $lista_avaliacoes=RetornaAvaliacoesApagadas($sock,$usr_formador);
  if (count($lista_avaliacoes)>0)
  {
    foreach ($lista_avaliacoes as $cod => $linha)
    {
      $data_inicio="<font class=text>".UnixTime2Data($linha['Data_inicio'])."</font>";
      $data_termino="<font class=text>".UnixTime2Data($linha['Data_termino'])."</font>";
      if ($data_acesso<$linha_item['data_inicio'])
      {
        $marcaib="<b>";
        $marcafb="</b>";
      }
      else
      {
        $marcaib="";
        $marcafb="";
      }

      if (!strcmp($linha['Ferramenta'],'F'))
      /* 145 - Fórum de Discussão*/
        $ferramenta=RetornaFraseDaLista($lista_frases,145);
      elseif (!strcmp($linha['Ferramenta'],'B'))
      /* 146 - Sessão de Bate-Papo*/
        $ferramenta=RetornaFraseDaLista($lista_frases,146);
      else
      /* 14 - Atividade no Portfólio*/
        $ferramenta=RetornaFraseDaLista($lista_frases,14);

      $icone="<a href=\"ver.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&status=D&cod_avaliacao=".$linha['Cod_avaliacao']."\"><img src=../figuras/avaliacao.gif border=0></a>";
      echo("  <tr>\n");
      echo("    <td width=10 align=center>".$icone."</td>\n");

      echo("    <td>\n");
      echo("<a class=text href=# onMouseDown='selected_item=");
      echo($linha['Cod_avaliacao'].";MostraLayer(lay_avaliacao);");
      echo("return(false);'>".$linha['Titulo']."</a><font class=text> (".$ferramenta.")</font>");
      echo(" </td>\n");

      echo("    <td align=center>".$data_inicio."</td>\n");
      echo("    <td align=center>".$data_termino."</td>\n");
      echo("  </tr>\n");
      echo("  <tr>\n");
      echo("    <td colspan=5 height=1><hr size=1></td>\n");
      echo("  </tr>\n");
    }
    echo("</table>\n");


    /* layer_avaliacao */
    echo("  <div id=layer_avaliacao class=block visibility=hidden onContextMenu='return(false);'>\n");
    echo("    <table bgcolor=#ffffff cellpadding=1 cellspacing=1 border=2>\n");
    echo("      <tr class=bgcolor>\n");
    echo("        <td class=bgcolor align=right>\n");
    echo("          <a href=# onClick='EscondeLayer(lay_avaliacao);return(false);'>");
    echo("<img src=../figuras/x.gif border=0></a>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("      <tr class=wtfield>\n");
    echo("        <td class=wtfield>\n");

    /* 33 - Objetivos/Critérios */
    echo("          <a href=# class=text onClick='Ver(selected_item);return(false);'>".RetornaFraseDaLista($lista_frases,33)."</a><br>\n");
    /* 48 - Recuperar (ger) */
      echo("          <a href=# class=text onClick='RecuperarAvaliacao(selected_item);return(false);'>".RetornaFraseDaLista($lista_frases_geral, 48)."</a><br>\n");

    /* 12 - Excluir (ger)*/
    echo("          <a href=# class=text onClick='ExcluirAvaliacao(selected_item);return(false);'>".RetornaFraseDaLista($lista_frases_geral, 12)."</a><br>\n");

    echo("        </td>\n");
    echo("      </tr>\n");
    echo("    </table>\n");
    echo("  </div>\n\n");
  }
  else
  {
    /* 132 - Nenhuma avaliação apagada! */
    echo("</table>\n");
    echo("<font class=text>".RetornaFraseDaLista($lista_frases,132)."</font>\n");
  }


  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);
  exit;

?>
