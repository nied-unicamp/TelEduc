<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/ver_avaliacoes_anteriores.php

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
  ARQUIVO : cursos/aplic/avaliacoes/ver_avaliacoes_anteriores.php
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

  $data_acesso=PenultimoAcesso($sock,$cod_usuario,"");

  echo("<html>\n");
  /* 1 - Avalia��es  */
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("  <link rel=\"stylesheet\" type=\"text/css\" href=\"../teleduc.css\">\n");
  echo("  <link rel=\"stylesheet\" type=\"text/css\" href=\"avaliacoes.css\">\n");

  echo("  <script language=\"javascript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
  echo("  <script language=\"javascript\">\n\n");

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
  /* Obt�m o layer */
  /* para acesso �s op��es (Ver,EditarAvaliacao e apagarAvaliacao   */
  echo("      lay_avaliacao = getLayer('layer_avaliacao');\n");
  echo("    }\n\n");

  echo("    function EscondeLayer(cod_layer)\n");
  echo("    {\n");
  echo("      hideLayer(cod_layer);\n");
  echo("    }\n\n");

  echo("    function EscondeLayers()\n");
  echo("    {\n");
  /* Se estiver visualizando as avaliacoes passadas ent�o esconde os layers   */
  /* para acesso �s op��es (Ver, Editar, */
  /* Apagar, Ver Notas, Ver Atividades Entregues e Ver Atividades Pendentes).                                                     */
  echo("      hideLayer(lay_avaliacao);\n");
  echo("    }\n\n");

  echo("    function MostraLayer(cod_layer)\n");
  echo("    {\n");
  echo("      EscondeLayers();\n");
  echo("      moveLayerTo(cod_layer, Xpos, Ypos + AjustePosMenuIE());\n");
  echo("      showLayer(cod_layer);\n");
  echo("    }\n\n");

  /* Cria as fun��es JavaScript */
  /* Ver(id), Editar(), Apagar(id), Ver Notas, Ver Atividades Entregues e Hist�rico de Notas    */
  echo("    function Ver(id)\n");
  echo("    {\n");
  echo("        document.frmAvaliacao.cod_avaliacao.value = id;\n");
  echo("        document.frmAvaliacao.action = \"ver.php?".RetornaSessionID());
  echo("&voltar=ver_avaliacoes_anteriores\";\n");
  echo("        document.frmAvaliacao.submit();\n");
  echo("    }\n\n");

  echo("    function VerNotas(id)\n");
  echo("    {\n");
  echo("        document.frmAvaliacao.cod_avaliacao.value = id;\n");
  echo("        document.frmAvaliacao.action = \"ver_notas.php?".RetornaSessionID());
  echo("&origem=ver_avaliacoes_anteriores\";\n");
  echo("        document.frmAvaliacao.submit();\n");
  echo("    }\n\n");

  echo("    function HistoricodoDesempenho(id)\n");
  echo("    {\n");
  echo("      document.frmAvaliacao.cod_avaliacao.value = id;\n");
  echo("      document.frmAvaliacao.action = \"historico_desempenho_todos.php?".RetornaSessionID());
  echo("&origem=ver_avaliacoes_anteriores\";\n");
  echo("      document.frmAvaliacao.submit();\n");
  echo("    }\n\n");

  echo("    function VerificarParticipacao(id)\n");
  echo("    {\n");
  echo("      document.frmAvaliacao.cod_avaliacao.value = id;\n");
  echo("      document.frmAvaliacao.action = \"ver_participacao.php?".RetornaSessionID());
  echo("&origem=ver_avaliacoes_anteriores\";\n");
  echo("      document.frmAvaliacao.submit();\n");
  echo("    }\n\n");

  if ($usr_formador)
  {
/*    echo("    function ApagarAvaliacao(id)\n");
    echo("    {\n");
    /* XX - Deseja realmente apagar a avalia��o selecionada? (Esta atividade deixar� de ser avalia��o e a avalia��o ser� movida para a lixeira)*/
 /*   echo("      if(confirm('Deseja realmente apagar a avalia��o selecionada? (Esta atividade deixar� de ser avalia��o e a avalia��o ser� movida para a lixeira.)'))");
    echo("      {\n");
    echo("        document.frmAvaliacao.cod_avaliacao.value = id;\n");
    echo("        document.frmAvaliacao.action = \"apagar_avaliacao.php?".RetornaSessionID());
    echo("&origem=ver_avaliacoes_anteriores\";\n");
    echo("        document.frmAvaliacao.submit();\n");
    echo("      }\n");
    echo("    }\n\n");

    echo("    function AlterarAvaliacao(id)\n");
    echo("    {\n");
    echo("        document.frmAvaliacao.cod_avaliacao.value = id;\n");
    echo("        document.frmAvaliacao.action = \"alterar_avaliacao.php?".RetornaSessionID());
    echo("&origem=ver_avaliacoes_anteriores\";\n");
    echo("        document.frmAvaliacao.submit();\n");
    echo("    }\n\n");
   */

    /* Abre a janela com a lista de Participantes para ser avaliado */
    echo("  function AvaliarParticipantes(id)\n");
    echo("  {\n");
    echo("        document.frmAvaliacao.cod_avaliacao.value = id;\n");
    echo("        document.frmAvaliacao.action = \"avaliar_participantes.php?".RetornaSessionID());
    echo("&origem=ver_avaliacoes_anteriores\";\n");
    echo("        document.frmAvaliacao.submit();\n");
    echo("    }\n\n");

  }

  echo("  </script>\n");


   echo("  <body link=#0000ff vlink=#0000ff bgcolor=white");

  //if ($usr_formador)
  echo(" onLoad=\"iniciar();\">\n");
  //else
  //  echo(">\n");

  /* P�gina Principal */
  /* 29 - Avalia��es passadas */
  $cabecalho ="<b class=\"titulo\">".RetornaFraseDaLista($lista_frases,29)."</b>\n";

  $cod_pagina=2;
  /* Cabecalho */
  echo(PreparaCabecalho($cod_curso,$cabecalho,22,$cod_pagina));

  echo("<p>\n");
  echo("<table border=0 width=100%>\n");
  echo("  <tr class=\"menu\">\n");
  /* 110 - Ver Avalia��es Atuais */
  echo("    <td align=center><a href=\"avaliacoes.php?".RetornaSessionID()."&cod_curso=".$cod_curso."\" class=\"menu\"><b>".RetornaFraseDaLista($lista_frases,110)."</b></a></td>\n");
  /* 30 - Ver Avalia��es Futuras */
  echo("    <td align=center><a href=\"ver_avaliacoes_futuras.php?".RetornaSessionID()."&cod_curso=".$cod_curso."\" class=\"menu\"><b>".RetornaFraseDaLista($lista_frases,30)."</b></a></td>\n");
  /* 31 - Notas da Turma */
  echo("    <td align=center><a href=\"todas_as_notas.php?".RetornaSessionID()."&cod_curso=".$cod_curso."\" class=\"menu\"><b>".RetornaFraseDaLista($lista_frases,31)."</b></a></td>\n");
  if ($usr_formador)
  /* 16 - Lixeira (ger)*/
    echo("    <td align=center><a href=\"ver_lixeira_avaliacoes.php?".RetornaSessionID()."&cod_curso=".$cod_curso."\" class=menu><b>".RetornaFraseDaLista($lista_frases_geral,16)."</b></a></td>\n");

  echo("  </tr>\n");
  echo("</table>\n");
  echo("<br>\n");

  echo("    <form name=\"frmAvaliacao\" method=\"post\">\n");

  echo(RetornaSessionIDInput());
  echo("<input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\">\n");

  /* Passa o cod_avaliacao para executar a��es sobre ela.       */
  echo("      <input type=\"hidden\" name=\"cod_avaliacao\" value=\"-1\">\n");
  echo("    </form>\n");

  echo("<table border=0 width=100% cellspacing=0>\n");
  echo("  <tr>\n");
  echo("    <td width=10 class=\"colorfield\">&nbsp;</td>\n");
  /* 29 - Avalia��es passadas */
  echo("    <td class=\"colorfield\">".RetornaFraseDaLista($lista_frases,29)."</td>\n");
  /* 16 - Data de In�cio*/
  echo("    <td class=\"colorfield\" align=center>".RetornaFraseDaLista($lista_frases,16)."</td>\n");
  /* 17 - Data de T�rmino*/
  echo("    <td class=\"colorfield\" align=center>".RetornaFraseDaLista($lista_frases,17)."</td>\n");
  echo("  </tr>\n");
  echo("  <tr>\n");
  echo("    <td colspan=4 height=5></td>\n");
  echo("  </tr>\n");

  $lista_avaliacoes=RetornaAvaliacoesAnteriores($sock,$usr_formador);
  if (count($lista_avaliacoes)>0)
  {
    foreach ($lista_avaliacoes as $cod => $linha)
    {
      $data_inicio="<font class=\"text\">".UnixTime2Data($linha['Data_inicio'])."</font>";
      $data_termino="<font class=\"text\">".UnixTime2Data($linha['Data_termino'])."</font>";
      if ($data_acesso<$linha['Data'])
      {
        $marcaib="<b>";
        $marcafb="</b>";
        $marcatr=" bgcolor=#f0f0f0";
      }
      else
      {
        $marcaib="";
        $marcafb="";
        $marcatr="";
      }

      if (!strcmp($linha['Ferramenta'],'F'))
      /* 145 - F�rum de Discuss�o*/
        $ferramenta=RetornaFraseDaLista($lista_frases,145);
      elseif (!strcmp($linha['Ferramenta'],'B'))
      /* 146 - Sess�o de Bate-Papo*/
        $ferramenta=RetornaFraseDaLista($lista_frases,146);
      else
      /* 14 - Atividade no Portf�lio*/
        $ferramenta=RetornaFraseDaLista($lista_frases,14);

      //$titulo="<a href=\"agenda.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&visualizar=sim&origem=historico&cod_item=".$linha_item['cod_item']."\">".$linha['Titulo']."</a>";
      //$icone="<a href=\"agenda.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&visualizar=sim&origem=historico&cod_item=".$linha_item['cod_item']."\"><img src=\"../figuras/avaliacao.gif\" border=0></a>";

      $icone="<a href=\"ver.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$linha['Cod_avaliacao']."\"><img src=\"../figuras/avaliacao.gif\" border=0></a>";
      echo("  <tr".$marcatr.">\n");
      echo("    <td width=10 align=center>".$icone."</td>\n");

      echo("    <td>\n");
      echo($marcaib."<a class=\"text\" href=\"#\" ");
      echo("onMouseDown=\"selected_item=".$linha['Cod_avaliacao'].";MostraLayer(lay_avaliacao);return(false);\"");
      echo(">".$linha['Titulo']."</a><font class=\"text\"> (".$ferramenta.")</font>".$marcafb);
      echo(" </td>\n");

      echo($marcaib."    <td align=center>".$data_inicio."</td>\n".$marcafb);
      echo($marcaib."    <td align=center>".$data_termino."</td>\n".$marcafb);
      echo("  </tr>\n");
      echo("  <tr>\n");
      echo("    <td colspan=5 height=1><hr size=1></td>\n");
      echo("  </tr>\n");
    }
    echo("</table>\n");

    /* layer_avaliacao */
    echo("  <div id=\"layer_avaliacao\" class=\"block\" visibility=hidden onContextMenu=\"return(false);\">\n");
    echo("    <table bgcolor=#ffffff cellpadding=1 cellspacing=1 border=2>\n");
    echo("      <tr class=\"bgcolor\">\n");
    echo("        <td class=\"bgcolor\" align=right>\n");
    echo("          <a href=\"#\" onClick=\"EscondeLayer(lay_avaliacao);return(false);\">");
    echo("<img src=\"../figuras/x.gif\" border=0></a>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("      <tr class=\"wtfield\">\n");
    echo("        <td class=\"wtfield\">\n");

     /* 33 - Objetivos/Crit�rios */
    echo("          <a href=\"#\" class=\"text\" onClick=\"Ver(selected_item);return(false);\">".RetornaFraseDaLista($lista_frases,33)."</a><br>\n");
    if ($usr_formador)
    {
      /* XX - Editar Objetivos/Crit�rios*/
//      echo("          <a href=\"#\" class=\"text\" onClick=\"AlterarAvaliacao(selected_item);return(false);\">Editar Objetivos/Crit�rios</a><br>\n");
      /* XX - Apagar Objetivos/Crit�rios */
//      echo("          <a href=\"#\" class=\"text\" onClick=\"ApagarAvaliacao(selected_item);return(false);\">Apagar Objetivos/Crit�rios</a><br>\n");
      /* 34 - Avaliar Participantes */
      echo("          <a href=\"#\" class=\"text\" onClick=\"AvaliarParticipantes(selected_item);return(false);\">".RetornaFraseDaLista($lista_frases,34)."</a><br>\n");
    }

    /* 35 - Ver Participa��o */
    echo("          <a href=\"#\" class=\"text\" onClick=\"VerificarParticipacao(selected_item);return(false);\">".RetornaFraseDaLista($lista_frases,35)."</a><br>\n");
    /* 36 - Ver Notas */
    echo("          <a href=\"#\" class=\"text\" onClick=\"VerNotas(selected_item);return(false);\">".RetornaFraseDaLista($lista_frases,36)."</a><br>\n");
    /* 37 - Hist�rico do Desempenho */
    echo("          <a href=\"#\" class=\"text\" onClick=\"HistoricodoDesempenho(selected_item);return(false);\">".RetornaFraseDaLista($lista_frases,37)."</a><br>\n");

    echo("        </td>\n");
    echo("      </tr>\n");
    echo("    </table>\n");
    echo("  </div>\n\n");
  }
  else
  {
    /* 125 - N�o existem avalia��es anteriores! */
    echo("</table>\n");
    echo("<font class=\"text\">".RetornaFraseDaLista($lista_frases,125)."</font>\n");
  }


  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);
  exit;

?>
