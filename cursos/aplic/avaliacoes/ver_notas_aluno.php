<?php
/*

<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/ver_nota_aluno.php

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
  ARQUIVO : cursos/aplic/avaliacoes/ver_nota_aluno.php
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

  echo("<html>\n");
  /* 1 - Avalia��es  */
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");

  if (!$SalvarEmArquivo)
  {
    echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
    echo("  <link rel=stylesheet TYPE=text/css href=avaliacoes.css>\n");
  }
  else
  {
    echo("  <style>\n");
    include "../teleduc.css";
    include "avaliacoes.css";
    echo("  </style>\n");
  }


  $usr_formador=EFormador($sock,$cod_curso,$cod_usuario);

  if ($acao=="mudarcomp" && $usr_formador)
  {
     if ($portfolio_grupo)
     {
       $lista_integrantes=RetornaListaIntegrantes($sock,$cod_grupo);
       foreach ($lista_integrantes as $cod_aluno => $linha)
       {
         $cod_nota=RetornaCodNota($sock, $cod_aluno, $cod_avaliacao);
         MudarCompartilhamentoNota($sock, $cod_nota, $tipo_comp);
       }
     }
     else
       MudarCompartilhamento($sock, $cod_nota, $tipo_comp);
  }

  $lista=RetornaAssociacaoItemAvaliacao($sock,$cod_item);

  $cod_avaliacao=$lista['cod_avaliacao'];

  $dados=RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);
  /* Obt�m o nome e o status do f�rum                     */
  $atividade=RetornaAtividade($sock,$dados['Cod_atividade']);

  /* Fun��es JavaScript */
  echo("<script language=JavaScript src=../bibliotecas/dhtmllib.js></script>\n");
  echo("<script language=JavaScript>\n");
  echo("var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
  echo("var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");
  echo("var Xpos, Ypos;\n");
  echo("var js_cod_nota;\n");
  echo("var js_comp = new Array();\n");


  if ($usr_formador)
  {

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
  echo("  cod_comp = getLayer(\"comp\");\n");
  echo("}\n");
  echo("\n");

  echo("function EscondeLayers()\n");
  echo("{\n");
  echo("  hideLayer(cod_comp);\n");
  echo("}\n");


   echo("function AtualizaComp(js_tipo_comp)\n");
  echo("{\n");
  echo("  if (isNav) {\n");
  echo("    document.comp.document.form_comp.tipo_comp.value=js_tipo_comp;\n");
  echo("    document.comp.document.form_comp.cod_nota.value=js_cod_nota;\n");
  echo("    if (js_tipo_comp=='T') {\n");
  echo("      document.comp.document.form_comp.tipo_comp[0].checked=true;\n");
  echo("      document.comp.document.form_comp.tipo_comp[1].checked=false;\n");
  echo("      document.comp.document.form_comp.tipo_comp[2].checked=false;\n");
  echo("    } else if (js_tipo_comp=='F') {\n");
  echo("      document.comp.document.form_comp.tipo_comp[0].checked=false;\n");
  echo("      document.comp.document.form_comp.tipo_comp[1].checked=true;\n");
  echo("      document.comp.document.form_comp.tipo_comp[2].checked=false;\n");
  echo("    } else {\n");
  echo("      document.comp.document.form_comp.tipo_comp[0].checked=false;\n");
  echo("      document.comp.document.form_comp.tipo_comp[1].checked=false;\n");
  echo("      document.comp.document.form_comp.tipo_comp[2].checked=true;\n");
  echo("    }\n");
  echo("  } else {\n");
  echo("    if (isIE) {\n");
  echo("      document.form_comp.tipo_comp.value=js_tipo_comp;\n");
  echo("      document.form_comp.cod_nota.value=js_cod_nota;\n");
  echo("      if (js_tipo_comp=='T') {\n");
  echo("        document.form_comp.tipo_comp[0].checked=true;\n");
  echo("        document.form_comp.tipo_comp[1].checked=false;\n");
  echo("        document.form_comp.tipo_comp[2].checked=false;\n");
  echo("      } else if (js_tipo_comp=='F') {\n");
  echo("        document.form_comp.tipo_comp[0].checked=false;\n");
  echo("        document.form_comp.tipo_comp[1].checked=true;\n");
  echo("        document.form_comp.tipo_comp[2].checked=false;\n");
  echo("      } else {\n");
  echo("        document.form_comp.tipo_comp[0].checked=false;\n");
  echo("        document.form_comp.tipo_comp[1].checked=false;\n");
  echo("        document.form_comp.tipo_comp[2].checked=true;\n");
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

  echo("  function AvaliarAlunoPortfolio(funcao)\n");
  echo("  {\n");
  echo("    window.open(\"avaliar_atividade.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&portfolio_grupo=".$portfolio_grupo."&VeioPeloPortfolio=0&cod_avaliacao=".$cod_avaliacao."&cod_usuario_portfolio=\"+funcao,\"AvaliarParticipante\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("    return(false);\n");
  echo("  }\n");

  }

  echo("  function HistoricodoDesempenho(funcao)\n");
  echo("  {\n");
  echo("    window.open(\"historico_desempenho.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&VeiodePortfolio=0&ferramenta=P&portfolio_grupo=".$portfolio_grupo."&cod_avaliacao=".$cod_avaliacao."&cod_aluno=\"+funcao,\"HistoricoDesempenho\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("    return(false);\n");
  echo("  }\n");

  echo("  function HistoricodoDesempenhoGrupo(grupo)\n");
  echo("  {\n");
  echo("    window.open(\"historico_desempenho.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&VeiodePortfolio=0&ferramenta=P&portfolio_grupo=".$portfolio_grupo."&cod_avaliacao=".$cod_avaliacao."&cod_grupo=\"+grupo,\"HistoricoDesempenho\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("    return(false);\n");
  echo("  }\n");
  
  echo("  function ImprimirRelatorio()\n");
  echo("  {\n");
  echo("    if ((navigator.appName == 'Microsoft Internet Explorer' && navigator.appVersion.indexOf('5.')>=0) || navigator.appName == 'Netscape') \n");
  echo("    {\n");
  echo("      self.print();\n");
  echo("    }\n");
  echo("    else\n");
  echo("    {\n");
  /* 51 - Infelizmente n�o foi poss�vel imprimir automaticamente esse documento. Mantenha a tecla <Ctrl> pressionada enquanto pressiona a tecla <p> para imprimir. */
  echo("      alert('".RetornaFraseDaLista($lista_frases_geral,51)."');\n");
  echo("    }\n");
  echo("  }\n");

  /* Fun��o JvaScript para chamar p�gina para salvar em arquivo. */
  echo("      function SalvarVerNotas()\n");
  echo("      {\n");
  echo("        document.frmpart.action = \"salvar_ver_notas_aluno.php?".RetornaSessionID());
  echo("&cod_curso=".$cod_curso."\";\n");
  echo("        document.frmpart.submit();\n");
  echo("      }\n\n");

  echo("function AbrePerfil(cod_usuario)\n");
  echo("{\n");
  echo("  window.open('../perfil/exibir_perfis.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno[]='+cod_usuario,'PerfilDisplay','width=620,height=400,top=60,left=60,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
  echo("  return(false);\n");
  echo("}\n");

  echo("function AbreJanelaComponentes(cod_grupo)\n");
  echo("{\n");
  echo("  window.open('componentes.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_grupo='+cod_grupo,'Componentes','width=400,height=300,top=150,left=250,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
  echo("  return false;\n");
  echo("}\n");

  echo("</script>\n");

  echo("<body link=#0000ff vlink=#0000ff bgcolor=white");
  if ($usr_formador)
    echo(" onload=\"Iniciar();\"");
    echo(">\n");
  /* 1 - Avalia��es */
  $cabecalho ="<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>";
  /* 134 - Notas do Participante  */
  $cabecalho.="<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,134)."</b>";

  $cod_pagina=18;
  /* Cabecalho */
  echo(PreparaCabecalho($cod_curso,$cabecalho,22,$cod_pagina));

  echo("<br>\n");
  echo("<p>\n");

   /* 135 - Atividade */
  echo("    <font class=text>".RetornaFraseDaLista($lista_frases,135).":</font>\n");
  echo("    <font class=text> ".$atividade."<br>");/*Nome do F�rum */
  /* 20 - Tipo da Atividade */
  echo("    <font class=text>".RetornaFraseDaLista($lista_frases,20).":</font>\n");
  if (!strcmp($dados['Tipo'],'I'))
  /* 21 - Individual*/
    echo("    <font class=text> ".RetornaFraseDaLista($lista_frases,21)."</font><br>");
  else
  /* 22 - Em Grupo*/
    echo("    <font class=text> ".RetornaFraseDaLista($lista_frases,22)."</font><br>");

  /* 58 - Valor da Atividade */
  echo(RetornaFraseDaLista($lista_frases,58).": ".$dados['Valor']."<br><br>\n");

  if (!$SalvarEmArquivo)
  {
  /* 46 - Ver objetivos/crit�rios da avalia��o */
     if ($usr_formador)
    echo("        <a class=text href=# onClick=\"window.open('ver.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_item=".$cod_item."&VeioDePortfolio=1&VeioDaAtividade=1','VerAvaliacao','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');EscondeLayers();return(false);\">".RetornaFraseDaLista($lista_frases,46)."</a><br><br>\n");
    else
    echo("        <a class=text href=# onClick=\"window.open('ver.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_item=".$cod_item."&VeioDePortfolio=1&VeioDaAtividade=1','VerAvaliacao','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');return(false);\">".RetornaFraseDaLista($lista_frases,46)."</a><br><br>\n");
  }
  echo("<hr>\n");


  if ($portfolio_grupo)
  {
    $cod_grupo_portfolio=RetornaCodGrupo($sock,$cod_item);
    $cod_aluno=RetornaCodAlunoMaisNotasnoGrupo($sock,$cod_avaliacao,$cod_grupo_portfolio);
    $nome=NomeGrupo($sock,$cod_grupo_portfolio);
    if (!$SalvarEmArquivo)
    {
      echo("<p>\n");
      /* 136 - Para visualizar o hist�rico do desempenho do grupo, clique sobre a nota dele.*/
      echo(RetornaFraseDaLista($lista_frases,136)."<br>\n");
      echo("<br>\n");
    }
  }
  else
  {
    $cod_aluno=RetornaCodUsuarioPortfolio($sock,$cod_item);
    $nome=NomeUsuario($sock,$cod_aluno);
    if (!$SalvarEmArquivo)
    {
      echo("<p>\n");
      /* 137 - Para visualizar o hist�rico do desempenho do participante, clique sobre a nota dele.*/
      echo(RetornaFraseDaLista($lista_frases,136)."<br>\n");
      echo("<br>\n");
    }
  }

 //Tabela com o nome do aluno, com a nota atual se houver e op��o de avaliar
  echo("<table border=0 width=100%>\n");
  echo("  <tr class=menu>\n");
  if ($portfolio_grupo)
  /* 48 - Grupo */
  echo("    <td class=colorfield align=center  width=33%>".RetornaFraseDaLista($lista_frases,48)."</td>\n");
  else
  /* 47 - Participante */
    echo("    <td class=colorfield align=center width=33%>".RetornaFraseDaLista($lista_frases,47)."</td>\n");

  /* 60 - Nota */
  echo("    <td class=colorfield align=center width=33%>".RetornaFraseDaLista($lista_frases,60)."</td>\n");
  if ($usr_formador)
    /* 63 - Compartilhamento */
    echo("    <td class=colorfield align=center width=33%>".RetornaFraseDaLista($lista_frases,63)."</td>\n");
  echo("    </tr>\n");

  if ($i==0)
    $field="g1field";
  else
    $field="g2field";
  echo("    <tr class=".$field.">\n");

  $i = ($i + 1) % 2;

  echo("      <td class=text align=center>");

  if (!$portfolio_grupo)
  {
    if (!$SalvarEmArquivo)
      echo("<a class=text href=# onClick=return(AbrePerfil(".$cod_aluno.")); class=text>".$nome."</a></td>\n");
    else
      echo($nome."</td>\n");
  }
  else
  {
    if (!$SalvarEmArquivo)
      echo("<a class=text href=# onClick=return(AbreJanelaComponentes(".$cod_grupo_portfolio.")); class=text>".$nome."</a></td>\n");
    else
      echo($nome."</td>\n");
  }

  $cod= $cod_aluno;
  if (FoiAvaliado($sock,$cod_avaliacao,$cod))  //Ja existe uma nota atribuida
  {
    if ($portfolio_grupo)
       $dados_nota=RetornaDadosNotaGrupo($sock, $cod_grupo_portfolio, $cod_avaliacao,$cod_usuario,$usr_formador, $cod);
    else
       $dados_nota=RetornaDadosNota($sock, $cod, $cod_avaliacao,$cod_usuario,$usr_formador);
    //$tipo_compartilhamento=RetornaCompartilhamento($sock, $cod, $cod_avaliacao);
    //$cod_nota=RetornaCodNota($sock, $cod, $cod_avaliacao);
    $tipo_compartilhamento=$dados_nota['tipo_compartilhamento'];
    $cod_nota=$dados_nota['cod_nota'];
    $nota=$dados_nota['nota'];

    if ($usr_formador)
    {
      if (!strcmp($tipo_compartilhamento,'T'))
      /* 51 - Totalmente Compartilhado*/
        $compartilhamento=RetornaFraseDaLista($lista_frases,51);
      elseif (!strcmp($tipo_compartilhamento,'G'))
      /* 53 - Compartilhado com Formadores e com o Grupo*/
        $compartilhamento=RetornaFraseDaLista($lista_frases,53);
      elseif (!strcmp($tipo_compartilhamento,'A'))
      /*54 - Compartilhado com Formadores e com o Participante*/
        $compartilhamento=RetornaFraseDaLista($lista_frases,54);
      else
      /* 52 - Compartilhado com Formadores*/
        $compartilhamento=RetornaFraseDaLista($lista_frases,52);

      $marcaib="";
      $marcafb="";
      echo("      <td class=text align=center>");
      if (!$SalvarEmArquivo)
      {
      if($portfolio_grupo)
        echo("<a href=# onClick=return(HistoricodoDesempenhoGrupo(".$cod_grupo_portfolio."));>".$nota."</a></td>\n");
      else
         echo("<a href=# onClick=return(HistoricodoDesempenho(".$cod."));>".$nota."</a></td>\n");
      }
      else
        echo($nota."</td>\n");
      if (!$SalvarEmArquivo)
        $compartilhamento=$marcaib."<a class=text href=# onMouseDown=\"js_cod_nota=".$cod_nota.";AtualizaComp('".$tipo_compartilhamento."');MostraLayer(cod_comp,140);return(false);\">".$compartilhamento."</a>".$marcafb;
      echo("      <td class=text align=center>".$compartilhamento);
      echo("</td>\n");
    }
    else     //� aluno
    {
      if (!strcmp($tipo_compartilhamento,'T'))
      {
         echo("      <td class=text align=center>");
         if (!$SalvarEmArquivo)
         {
           if($portfolio_grupo)
        echo("<a href=# onClick=return(HistoricodoDesempenhoGrupo(".$cod_grupo_portfolio."));>".$nota."</a></td>\n");
      else
          echo("<a href=# onClick=return(HistoricodoDesempenho(".$cod."));>".$nota."</a></td>\n");
         }
         else
           echo($nota."</td>\n");
      }
      elseif ((!strcmp($tipo_compartilhamento,'G')) && ($portfolio_grupo))
      {
        $cod_grupo_usuario=RetornaCodGrupoPortfolio($sock,$cod_usuario);         //retorna o codigo do grupo do usuario que esta acessando
        echo("      <td class=text align=center>");
        if (!$SalvarEmArquivo)
        {
          if ($cod_grupo_usuario==$cod_grupo_portfolio)    //O usuario pertence ao grupo que foi avaliado
            echo("<a href=# onClick=return(HistoricodoDesempenhoGrupo(".$cod_grupo_portfolio."));>".$nota."</a></td>\n");
        }
        else
          echo($nota."</td>\n");
      }
      elseif ((!strcmp($tipo_compartilhamento,'A')) && ($cod_usuario==$cod))
      {
        echo("      <td class=text align=center>");
        if (!$SalvarEmArquivo)
        {
          if($portfolio_grupo)
            echo("<a href=# onClick=return(HistoricodoDesempenhoGrupo(".$cod_grupo_portfolio."));>".$nota."</a></td>\n");
           else
           echo("<a href=# onClick=return(HistoricodoDesempenho(".$cod."));>".$nota."</a></td>\n");
        }
        else
            echo($nota."</td>\n");
      }
      else //Est� compartilhada s� com formadores
        echo("      <td class=text align=center>&nbsp;</td>\n");
    }
  }
  else // nenhuma nota foi atribuida
  {
    if ($usr_formador)
    {
      echo("      <td class=text align=center>");
      if (!$SalvarEmArquivo)
      {
        echo("      <a href=# onClick=return(AvaliarAlunoPortfolio(".$cod."));>");
        echo("Avaliar</a></td>\n");
      }
      else
        echo("&nbsp;</td>\n");
      echo("      <td class=text align=center>");
      echo("&nbsp;</td>\n");
    }
    else
      echo("      <td class=text align=center>&nbsp;</td>\n");
   }
   echo("    </tr>\n");

  echo("</table>\n");

    if (($usr_formador) && (!$SalvarEmArquivo))
  {
    /* Mudar Compartilhamento */
    echo("<div id=comp class=block visibility=hidden onContextMenu=\"return(false);\">\n");
    echo("<form method=post name=form_comp action=ver_notas_aluno.php>\n");
    echo(RetornaSessionIDInput());
    echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");
    echo("  <input type=hidden name=cod_nota value=\"\">\n");
    echo("  <input type=hidden name=acao value=mudarcomp>\n");
    echo("  <input type=hidden name=cod_item value=".$cod_item.">\n");
    echo("  <input type=hidden name=portfolio_grupo value=".$portfolio_grupo.">\n");
    echo("  <input type=hidden name=cod_grupo value=".$cod_grupo_portfolio.">\n");
    echo("  <input type=hidden name=cod_avaliacao value=".$cod_avaliacao.">\n");
    echo("<table class=wtfield cellspacing=1 cellpadding=1 border=2>\n");
    echo("  <tr>\n");
    echo("    <td class=bgcolor align=right colspan=2><a href=# onClick=EscondeLayer(cod_comp);return(false);><img src=../figuras/x.gif border=0></a></td>\n");
    echo("  </tr>\n");
    echo("  <tr>\n");
    echo("    <td>\n");
    echo("      <table class=wtfield border=0>\n");
    echo("        <tr>\n");
    echo("          <td>\n");
    echo("            <input type=radio name=tipo_comp value=\"T\" class=wtfield onClick=\"submit();\">\n");
    echo("          </td>\n");
    echo("          <td>\n");
    /* 51 - Totalmente compartilhado */
    echo("            <font class=text><nobr>".RetornaFraseDaLista($lista_frases,51)."</nobr></font>\n");
    echo("          </td>\n");
    echo("        </tr>\n");
    echo("        <tr>\n");
    echo("          <td>\n");
    echo("            <input type=radio name=tipo_comp value=\"F\" class=wtfield onClick=\"submit();\">\n");
    echo("          </td>\n");
    echo("          <td>\n");
    /* 52 - Compartilhado com formadores */
    echo("            <font class=text><nobr>".RetornaFraseDaLista($lista_frases,52)."</nobr></font>\n");
    echo("          </td>\n");
    echo("        </tr>\n");
    echo("        <tr>\n");
    echo("          <td>\n");
    if ($portfolio_grupo)
    {
      echo("            <input type=radio name=tipo_comp value=\"G\" class=wtfield onClick=\"submit();\">\n");
      echo("          </td>\n");
      echo("          <td>\n");
      /* 53 - Compartilhado com Formadores e com o Grupo */
      echo("            <font class=text><nobr>".RetornaFraseDaLista($lista_frases,53)."</nobr></font>\n");
    }
    else
    {
      echo("            <input type=radio name=tipo_comp value=\"A\" class=wtfield onClick=\"submit();\">\n");
      echo("          </td>\n");
      echo("          <td>\n");
      /* 54 - Compartilhado com Formadores e com o Participante */
      echo("            <font class=text><nobr>".RetornaFraseDaLista($lista_frases,54)."</nobr></font>\n");
    }
    echo("          </td>\n");
    echo("        </tr>\n");
    echo("      </table>\n");
    echo("    </td>\n");
    echo("  </tr>\n");
    echo("</table>\n");
    echo("</form>\n");
    echo("</div>\n");
  }


  echo("    <form name=frmpart method=post>\n");
  echo("      <div align=right>\n");
  if (!$SalvarEmArquivo)
  {
    /* 50 - Salvar em Arquivo (geral) */
    echo("  <input class=text type=button value='".RetornaFraseDaLista($lista_frases_geral,50)."' onClick='SalvarVerNotas();'>\n");
    echo("      <input type=hidden name=cod_curso value=".$cod_curso.">\n");
    echo("      <input type=hidden name=cod_item value=".$cod_item.">\n");
    echo("  <input type=hidden name=portfolio_grupo value=".$portfolio_grupo.">\n");
  }
  /* 14 - Imprimir (ger) */
  echo("<input class=text type=button value='".RetornaFraseDaLista($lista_frases_geral,14)."' onClick=ImprimirRelatorio();>\n");

  /* 23 - Fechar (gen) */
  if (!$SalvarEmArquivo)
    echo("  <input class=text type=button value='".RetornaFraseDaLista($lista_frases_geral,13)."' onClick=self.close()>\n");
  echo("      </div>\n");
  echo("      <br>\n");

  echo("    </form>\n");

  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>
