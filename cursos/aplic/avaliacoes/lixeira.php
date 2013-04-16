<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/lixeira.php

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
  ARQUIVO : cursos/aplic/avaliacoes/lixeira.php
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

  /* 128 - A��o exclusiva a formadores. */
  if (!$usr_formador) exit(RetornaFraseDaLista($lista_frases,128));

  echo("<html>\n");
  /* 1 - Avalia��es  */
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
  /* Obt�m o layer */
  /* para acesso �s op��es (Ver,EditarAvaliacao e ExcluirAvaliacao   */
  echo("      lay_avaliacao = getLayer('layer_avaliacao');\n");
  echo("    }\n\n");

  echo("    function EscondeLayer(cod_layer)\n");
  echo("    {\n");
  echo("      hideLayer(cod_layer);\n");
  echo("    }\n\n");

  echo("    function EscondeLayers()\n");
  echo("    {\n");
  /* Se estiver visualizando as avaliacoes atuais ent�o esconde os layers   */
  /* para acesso �s op��es (Ver, Editar, */
  /* Apagar, Ver Notas, Ver Atividades Entregues e Ver Atividades Pendentes). */
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
  echo("    function VerItemLixeira(id)\n");
  echo("    {\n");
  echo("        document.frmAvaliacao.cod_avaliacao.value = id;\n");
  echo("        document.frmAvaliacao.action = 'ver_lixeira.php'; \n");
  echo("        document.frmAvaliacao.submit();\n");
  echo("    }\n\n");

    echo("    function ExcluirAvaliacao(id)\n");
    echo("    {\n");
    /* 129 - Voc� tem certeza de que deseja excluir esta avalia��o? */
    /* 130 - (a avalia��o ser� exclu�da definitivamente) */
    echo("      if(confirm('".RetornaFraseDaLista($lista_frases,129).RetornaFraseDaLista($lista_frases,130)."'))");
    echo("      {\n");
    echo("        document.frmAvaliacao.cod_avaliacao.value = id; \n");
    echo("        document.frmAvaliacao.operacao.value='excluir'; \n");
    echo("        document.frmAvaliacao.action = 'excluir_avaliacao.php'; \n");
    echo("        document.frmAvaliacao.origem.value = 'lixeira'; \n");
    echo("        document.frmAvaliacao.submit();\n");
    echo("      }\n");
    echo("    }\n\n");

    echo("    function RecuperarAvaliacao(id)\n");
    echo("    {\n");
    /* 118 - Voc� tem certeza de que deseja recuperar esta avalia��o? */
    /* 119 - (Se voc� tamb�m excluiu a atividade a que a avalia��o se refere e quiser tamb�m recuper�-la � necess�rio faz�-lo na respectiva ferramenta) */
    echo("      if(confirm('".RetornaFraseDaLista($lista_frases,118).RetornaFraseDaLista($lista_frases,119)."'))");
    echo("      {\n");
    echo("        document.frmAvaliacao.cod_avaliacao.value = id;\n");
    echo("        document.frmAvaliacao.action = 'excluir_avaliacao.php'; \n");
    echo("        document.frmAvaliacao.operacao.value='recuperar'; \n");
    echo("        document.frmAvaliacao.origem.value = 'ver_lixeira'; \n");
    echo("        document.frmAvaliacao.submit();\n");
    echo("      }\n");
    echo("    }\n\n");
    echo("  </script>\n");

  echo("<script language=JavaScript>\n");
  // Esta funcao mostra a tela com a lista de avaliacoes Passadas, Atuais ou Futuras
  // tela = 'P', 'F' ou 'A'
  echo("    function VerTelaAvaliacoes(tela)\n");
  echo("    {\n");
  echo("      document.frmAvaliacao.action = 'avaliacoes.php';\n");
  echo("      document.frmAvaliacao.tela_avaliacao.value = tela;\n");
  echo("      document.frmAvaliacao.submit();\n");
  echo("      return false;\n");
  echo("    }\n");

  echo("    function VerTelaNotas()\n");
  echo("    {\n");
  echo("      document.frmAvaliacao.action = 'notas.php';\n");
  echo("      document.frmAvaliacao.submit();\n");
  echo("      return false;\n");
  echo("    }\n");

  echo("    function VerTelaLixeira()\n");
  echo("    {\n");
  echo("      document.frmAvaliacao.action = 'lixeira.php';\n");
  echo("      document.frmAvaliacao.submit();\n");
  echo("      return false;\n");
  echo("    }\n");
  echo("</script>\n");

  echo("  <body link=#0000ff vlink=#0000ff bgcolor=white");

  //if ($usr_formador)
  echo(" onLoad='iniciar();'>\n");
  //else
  //  echo(">\n");

  /* P�gina Principal */
  /* 1 - Avalia��es*/
  $cabecalho ="<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>\n";
  /* 16 - Lixeira (gen)*/
  $cabecalho.="<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases_geral,16)."</b>\n";

  $cod_pagina=9;
  /* Cabecalho */
  echo(PreparaCabecalho($cod_curso,$cabecalho,22,$cod_pagina));

  echo("<p>\n");
  // menu com links para as outras avaliacoes, notas e lixeira
  echo("  <table border=0 width=100%>\n");
  echo("    <tbody>\n");
  echo("      <tr class=menu>\n");
  // 29 - Avalia��es Passadas
  echo("        <td align=center><a href=# onClick=return(VerTelaAvaliacoes('P')) class=menu><b>".RetornaFraseDaLista($lista_frases, 29)."</b></a></td>\n");
  // 32 - Avalia��es Atuais
  echo("        <td align=center><a href=# onClick=return(VerTelaAvaliacoes('A')) class=menu><b>".RetornaFraseDaLista($lista_frases, 32)."</b></a></td>\n");
  // 30 - Avalia��es Futuras
  echo("        <td align=center><a href=# onClick=return(VerTelaAvaliacoes('F')) class=menu><b>".RetornaFraseDaLista($lista_frases, 30)."</b></a></td>\n");
  // 31 - Notas dos Participantes
  echo("        <td align=center><a href=# onClick=return(VerTelaNotas()) class=menu><b>".RetornaFraseDaLista($lista_frases, 31)."</b></a></td>\n");
  // G 16 - Lixeira
  echo("        <td align=center><a href=# onClick=return(VerTelaLixeira()) class=menu><font color=yellow><b>".RetornaFraseDaLista($lista_frases_geral, 16)."</b></font></a></td>\n");
  echo("      </tr>\n");
  echo("    </tbody>\n");
  echo("  </table>\n");
  echo("</p>\n");

  echo("    <form name=frmAvaliacao method=post>\n");

  echo(RetornaSessionIDInput());
  echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");
/*
  echo("<input type=hidden name=operacao value=''>\n");
  echo("<input type=hidden name=status value='D'>\n");
 */
  /* Passa o cod_avaliacao para executar a��es sobre ela.       */
  echo("      <input type=hidden name=cod_avaliacao value=-1> \n");
  echo("      <input type=hidden name=tela_avaliacao value=-1> \n");
  echo("      <input type=hidden name=operacao value=-1> \n");
  echo("      <input type=hidden name=origem value=-1> \n");
  echo("    </form>\n");

  echo("<table border=0 width=100% cellspacing=0>\n");
  echo("  <tr>\n");
  echo("    <td width=10 class=colorfield>&nbsp;</td>\n");
  // 1 - Avalia��es
  echo("    <td class=colorfield>".RetornaFraseDaLista($lista_frases,1)."</td>\n");
  /*
    // ?? - Ferramenta
    echo("    <td class=colorfield>"."[Ferramenta]"."</td>\n"); */
  // 113 - Tipo da avalia��o
  echo("    <td class=colorfield>".RetornaFraseDaLista($lista_frases, 113)."</td>\n");
  // 16 - Data de In�cio
  echo("    <td class=colorfield align=center>".RetornaFraseDaLista($lista_frases,16)."</td>\n");
  // 17 - Data de T�rmino
  echo("    <td class=colorfield align=center>".RetornaFraseDaLista($lista_frases,17)."</td>\n");
  echo("  </tr>\n");
  /*
    echo("  <tr>\n");
    echo("    <td colspan= height=5></td>\n");
    echo("  </tr>\n"); */
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
      {
        // 145 - F�rum de Discuss�o
        $ferramenta = RetornaFraseDaLista($lista_frases,145);
      }
      elseif (!strcmp($linha['Ferramenta'],'B'))
      {
        // 146 - Sess�o de Bate-Papo
        $ferramenta = RetornaFraseDaLista($lista_frases,146);
      }
      else if ($linha['Ferramenta'] == 'P')
      {
        if ($linha['Tipo'] == 'G')
          // 162 - Atividade em grupo no Portfolio
          $ferramenta = RetornaFraseDaLista($lista_frases, 162);
        elseif ($linha['Tipo'] == 'I')
          // 161 - Atividade individual no Portfolio
          $ferramenta = RetornaFraseDaLista($lista_frases, 161);
      }
      // coluna com icone
      // $icone="<a href=\"ver_lixeira.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$linha['Cod_avaliacao']."\"><img src=../figuras/avaliacao.gif border=0></a>";
      $icone="<a href=# onClick='selected_item=".$linha['Cod_avaliacao'].";MostraLayer(lay_avaliacao);'><img src=../figuras/avaliacao.gif border=0></a>";
      echo("  <tr>\n");
      echo("    <td width=10 align=center>".$icone."</td>\n");

      // coluna com titulo do item
      echo("    <td>\n");
      echo("<a class=text href=# onMouseDown='selected_item=".$linha['Cod_avaliacao'].";MostraLayer(lay_avaliacao);");
      echo("return(false);'>".$linha['Titulo']."</a>");
      echo(" </td>\n");

      // coluna com ferramenta
      echo("<td><font class=text>".$ferramenta."</font></td>\n");
      /*
        // coluna com Tipo da avaliacao : Individual ou em Grupo
        // coluna do tipo de avaliacao: Individual ou Em Grupo
        $frase_tipo = "";
        if (!isset($linha['tipo']))
          $frase_tipo = "";
        elseif ($linha['tipo'] == 'G')
          // 22 - Em Grupo
          $frase_tipo = RetornaFraseDaLista($lista_frases, 22);
        elseif ($linha['tipo'] == 'I')
          // 21 - Individual
          $frase_tipo = RetornaFraseDaLista($lista_frases, 21);
        echo("<td><font class=text>".$frase_tipo."</font></td>\n"); */

      echo("    <td align=center>".$data_inicio."</td>\n");
      echo("    <td align=center>".$data_termino."</td>\n");
      echo("  </tr>\n");
      echo("  <tr>\n");
      echo("    <td colspan=6 height=1><hr size=1></td>\n");
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

    /* G 21 - Ver */
    echo("          <a href=# class=text onClick='VerItemLixeira(selected_item);return(false);'>".RetornaFraseDaLista($lista_frases_geral,21)."</a><br>\n");
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
    /* 132 - Nenhuma avalia��o apagada! */
    echo("</table>\n");

    echo("<P> \n");
    echo("<font class=text>".RetornaFraseDaLista($lista_frases,132)."</font>\n");
    echo("</P> \n");
  }


  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);
  exit;

?>
