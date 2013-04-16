<?php
/*

<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/ver_participacao_aluno.php

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
  ARQUIVO : cursos/aplic/avaliacoes/ver_participacao_aluno.php
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

  echo("<body link=#0000ff vlink=#0000ff bgcolor=white onload=self.focus();>\n");
  /* 1 - Avalia��es */
  $cabecalho ="<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>";
  /* 138 - Verifica��o das participa��es */
  $cabecalho.="<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,138)."</b>";

   $cod_pagina=19;
  /* Cabecalho */
  echo(PreparaCabecalho($cod_curso,$cabecalho,22,$cod_pagina));

  $lista=RetornaAssociacaoItemAvaliacao($sock,$cod_item);

  $cod_avaliacao=$lista['cod_avaliacao'];

  $dados=RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);
  /* Obt�m o nome e o status do f�rum                     */
  $atividade=RetornaAtividade($sock,$dados['Cod_atividade']);

   echo("<br>\n");
  echo("<p>\n");

   /* 135 - Atividade */
  echo("    <font class=text>".RetornaFraseDaLista($lista_frases,135).":</font>\n");
  echo("    <font class=text> ".$atividade."</font><br>");/*Nome do F�rum */
  /* 20 - Tipo da Atividade */
  echo("    <font class=text>".RetornaFraseDaLista($lista_frases,20).":</font>\n");
  if (!strcmp($dados['Tipo'],'I'))
  /* 21 - Individual*/
    echo("    <font class=text> ".RetornaFraseDaLista($lista_frases,21)."</font><br>");
  else
  /* 22 - Em Grupo*/
    echo("    <font class=text> ".RetornaFraseDaLista($lista_frases,22)."</font><br>");

  /* 19 - Valor */
  echo("<font class=text>".RetornaFraseDaLista($lista_frases,19).": ".$dados['Valor']."</font><br><br>\n");

  if (!$SalvarEmArquivo)
  /* 46 - Ver objetivos/crit�rios da avalia��o */
    echo("        <a class=text href=# onClick=\"window.open('ver.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_item=".$cod_item."&VeioDePortfolio=1&VeioDaAtividade=1','VerAvaliacao','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');return(false);\">".RetornaFraseDaLista($lista_frases,46)."</a><br><br>\n");

  echo("<hr>\n");

  if ($portfolio_grupo)
  {
    $cod_grupo_portfolio=RetornaCodGrupo($sock,$cod_item);
    $nome=NomeGrupo($sock,$cod_grupo_portfolio);
    if (!$SalvarEmArquivo)
    {
      echo("<p>\n");
      /* 142 - Para visualizar os itens postados pelo grupo, clique sobre o n�mero de participa��es.*/
      echo("<font class=text>".RetornaFraseDaLista($lista_frases,142)."</font><br>\n");
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
      /* 143 - Para visualizar os itens postados pelo participante, clique sobre o n�mero de participa��es.*/
      echo("<font class=text>".RetornaFraseDaLista($lista_frases,143)."</font><br>\n");
      echo("<br>\n");
    }
  }

  echo("<script language=javascript>\n");
  echo("  function ImprimirRelatorio()\n");
  echo("  {\n");
  echo("    if ((navigator.appName == 'Microsoft Internet Explorer' && navigator.appVersion.indexOf('5.')>=0) || navigator.appName == 'Netscape') \n");
  echo("    {\n");
  echo("      self.print();\n");
  echo("    }\n");
  echo("    else\n");
  echo("    {\n");
  /* 51 (ger) - Infelizmente n�o foi poss�vel imprimir automaticamente esse documento. Mantenha a tecla <Ctrl> pressionada enquanto pressiona a tecla <p> para imprimir. */
  echo("      alert('".RetornaFraseDaLista($lista_frases,51)."');\n");
  echo("    }\n");
  echo("  }\n");

  /* Fun��o JvaScript para chamar p�gina para salvar em arquivo. */
  echo("      function SalvarVerParticipacao()\n");
  echo("      {\n");
  echo("        document.frmpart.action = \"salvar_ver_participacao_aluno.php?".RetornaSessionID());
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

  echo("  function RetornaItensAluno(funcao)\n");
  echo("  {\n");
  echo("    window.open(\"../portfolio/ver_itens_aluno.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&cod_usuario_portfolio=\"+funcao,\"ItensPortfolioAluno\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("    return(false);\n");
  echo("  }\n");

  echo("  function RetornaItensGrupo(funcao)\n");
  echo("  {\n");
  echo("    window.open(\"../portfolio/ver_itens_aluno.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&cod_grupo_portfolio=\"+funcao,\"ItensPortfolioAluno\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("    return(false);\n");
  echo("  }\n");

  echo("</script>\n");

 //Tabela com o nome do aluno, indicando se participou da atividade
  echo("<table border=0 width=100%>\n");
  echo("  <tr class=menu>\n");
  if ($portfolio_grupo)
  /* 48- Grupo */
  echo("    <td class=colorfield align=center>".RetornaFraseDaLista($lista_frases,48)."</td>\n");
  else
  /* 47- Participante */
    echo("    <td class=colorfield align=center>".RetornaFraseDaLista($lista_frases,47)."</td>\n");
  /* 49 - Participa��es */
  echo("    <td class=colorfield align=center>".RetornaFraseDaLista($lista_frases,49)."</td>\n");
  echo("    </tr>\n");

  if ($i==0)
    $field="g1field";
  else
    $field="g2field";
  echo("    <tr class=".$field.">\n");

  $i = ($i + 1) % 2;

  echo("      <td class=text>");

  if (!$portfolio_grupo)
  {
    if (!$SalvarEmArquivo)
      echo("<a class=text href=# onClick=return(AbrePerfil(".$cod_aluno.")); class=text>".$nome."</a></td>\n");
    else
      echo("<font class=text>".$nome."</font></td>\n");
  }
  else
  {
    if (!$SalvarEmArquivo)
      echo("<a class=text href=# onClick=return(AbreJanelaComponentes(".$cod_grupo_portfolio.")); class=text>".$nome."</a></td>\n");
    else
      echo("<font class=text>".$nome."</font></td>\n");
  }

  if ($portfolio_grupo)
  {
     $cod=$cod_grupo_portfolio;
     $cod_aluno="";
  }
  else
    $cod=$cod_aluno;

  $usr_formador = EFormador($sock, $cod_curso, $cod_usuario);

  if (RealizouAtividadeNoPortfolio($sock,$cod_avaliacao,$cod,$portfolio_grupo))
  {
     $num_itens=RetornaNumItensPortfolioAvaliacao($sock,$cod,$cod_avaliacao,$portfolio_grupo,$cod_usuario,$usr_formador,$cod_aluno);
     if (!$SalvarEmArquivo)
     {
       if($portfolio_grupo)
           echo("      <td class=text align=center><a href=# onClick=return(RetornaItensGrupo(".$cod_grupo_portfolio."));>".$num_itens."</a></td>\n");
       else
         echo("      <td class=text align=center><a href=# onClick=return(RetornaItensAluno(".$cod."));>".$num_itens."</a></td>\n");
     }
     else
       echo("      <td class=text align=center><font class=text>".$num_itens."</font></td>\n");
  }
  else
  {
    /*N�o Participou */
    echo("      <td class=text align=center><font class=text>0</font></td>\n");
  }

   echo("    </tr>\n");

   echo("</table>\n");

   echo("    <form name=frmpart method=post>\n");

    echo("      <div align=right>\n");
    if (!$SalvarEmArquivo)
    {
      /* 50 - Salvar em Arquivo (geral) */
      echo("  <input class=text type=button value='".RetornaFraseDaLista($lista_frases_geral,50)."' onClick='SalvarVerParticipacao();'>\n");
      echo("      <input type=hidden name=cod_curso value=".$cod_curso.">\n");
      echo("      <input type=hidden name=cod_item value=".$cod_item.">\n");
      echo("  <input type=hidden name=portfolio_grupo value=".$portfolio_grupo.">\n");
    }
    /* 14 - Imprimir */
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
