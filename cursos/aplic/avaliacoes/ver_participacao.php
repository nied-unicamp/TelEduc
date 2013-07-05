<?php
/*

<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/ver_participacao.php

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
  ARQUIVO : cursos/aplic/avaliacoes/ver_participacao.php
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

  $usr_formador = EFormador($sock, $cod_curso, $cod_usuario);
  $dados=RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);

  if (!$SalvarEmArquivo)
  {
    echo("    <link rel=\"stylesheet\" type=\"text/css\" href=\"../teleduc.css\">\n");
    echo("    <link rel=\"stylesheet\" type=\"text/css\" href=\"avaliacoes.css\">\n");
    echo("\n");
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
    $cabecalho ="<b class=\"titulo\">".RetornaFraseDaLista($lista_frases,1)."</b>";
    /* 138 - Verifica��o das participa��es */
    $cabecalho.="<b class=\"subtitulo\"> - ".RetornaFraseDaLista($lista_frases,138)."</b>";

    $cod_pagina=21;
    /* Cabecalho */
    echo(PreparaCabecalho($cod_curso,$cabecalho,22,$cod_pagina));

  // A variavel tela_avaliacao indica quais avaliacoes devem ser listadas: 'P'assadas, 'A'tuais ou 'F'uturas
  if (!isset($tela_avaliacao) || !in_array($tela_avaliacao, array('P', 'A', 'F')))
  {
    $tela_avaliacao = 'A';
  }
  switch ($tela_avaliacao)
  {
    case 'P' :
      $lista_avaliacoes = RetornaAvaliacoesAnteriores($sock,$usr_formador);
      break;
    case 'A' :
      $lista_avaliacoes = RetornaAvaliacoesAtuais($sock,$usr_formador);
       break;
    case 'F' :
      $lista_avaliacoes = RetornaAvaliacoesFuturas($sock,$usr_formador);
      break;
  }

  echo("<script language=\"javascript\">\n");
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

  echo("    function Ver()\n");
  echo("    {\n");
  echo("        document.frmAvaliacao.cod_avaliacao.value = ".$cod_avaliacao.";\n");
  echo("        document.frmAvaliacao.action = 'ver.php'; \n");
  echo("        document.frmAvaliacao.submit();\n");
  echo("    }\n\n");

  echo("function VerObj()\n");
  echo("{\n");
  $param = "'width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes'";
  $nome_janela = "'AvaliacoesHistorico'";
  echo("  window.open('ver_popup.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."', ".$nome_janela.", ".$param.");\n");
  echo("  return false;");
  echo("}\n");

  echo("</script>\n");

  // Determinamos a cor de cada link (amarelo ou branco) no menu superior
  $cor_link1 = array('A' => "", 'F' => "", 'P' => "");
  $cor_link2 = array('A' => "", 'F' => "", 'P' => "");
  $cor_link1[$tela_avaliacao] = "<font color=yellow>";
  $cor_link2[$tela_avaliacao] = "</font>";

  echo("    <form name=\"frmAvaliacao\" method=\"post\">\n");
  echo(RetornaSessionIDInput());
  echo("      <input type=\"hidden\" name=\"cod_curso\"      value=\"".$cod_curso."\">\n");
  // Passa o cod_avaliacao para executar a��es sobre ela.
  echo("      <input type=\"hidden\" name=\"cod_avaliacao\"  value=\"-1\">\n");
  // tela_avaliacao eh a variavel que indica se esta tela deve mostrar avaliacoes 'P'assadas, 'A'tuais ou 'F'uturas
  echo("      <input type=\"hidden\" name=\"tela_avaliacao\" value=\"".$tela_avaliacao."\">\n");
  echo("    </form>\n");

  echo("<p>\n");
  // menu com links para as outras avaliacoes, notas e lixeira
  echo("  <table border=0 width=100%>\n");
  echo("    <tbody>\n");
  echo("      <tr class=\"menu\">\n");
  // 29 - Avalia��es Passadas
  echo("        <td align=center><a href=\"#\" onMouseDown=\"return(VerTelaAvaliacoes('P'));\" class=\"menu\">".$cor_link1['P']."<b>".RetornaFraseDaLista($lista_frases, 29)."</b>".$cor_link2['P']."</a></td>\n");
  // 32 - Avalia��es Atuais
  echo("        <td align=center><a href=\"#\" onMouseDown=\"return(VerTelaAvaliacoes('A'));\" class=\"menu\">".$cor_link1['A']."<b>".RetornaFraseDaLista($lista_frases, 32)."</b>".$cor_link2['A']."</a></td>\n");
  // 30 - Avalia��es Futuras
  echo("        <td align=center><a href=\"#\" onMouseDown=\"return(VerTelaAvaliacoes('F'));\" class=\"menu\">".$cor_link1['F']."<b>".RetornaFraseDaLista($lista_frases, 30)."</b>".$cor_link2['F']."</a></td>\n");
  // 31 - Notas dos Participantes
  echo("        <td align=center><a href=\"#\" onMouseDown=\"return(VerTelaNotas());\" class=\"menu\"><b>".RetornaFraseDaLista($lista_frases, 31)."</b></a></td>\n");
  // G 16 - Lixeira
  echo("        <td align=center><a href=\"#\" onMouseDown=\"return(VerTelaLixeira());\" class=\"menu\"><b>".RetornaFraseDaLista($lista_frases_geral, 16)."</b></a></td>\n");
  echo("      </tr>\n");
  echo("    </tbody>\n");
  echo("  </table>\n");
  echo("<table border=0 width=100%;>\n");
  echo("  <tbody>\n");
  echo("    <tr class=\"menu3\">\n");
  // 120 - Ver Avalia��o
  echo("      <td align=center><a href=\"#\" class=\"menu3\" onClick=\"Ver(); return false;\">".RetornaFraseDaLista($lista_frases, 120)."</a></td>\n");
  // 46 - Ver objetivos/crit�rios da avalia��o
  echo("      <td align=center><a href=\"#\" class=\"menu3\" onclick=\"VerObj();return false;\">".RetornaFraseDaLista($lista_frases, 46)."</a></td>\n");
  echo("    </tr>\n");
  echo("  </tbody>\n");
  echo("</table>\n");
  echo("  </p>\n");

  if ($dados['Ferramenta'] == 'P')
  {
    $titulo = RetornaAtividade($sock,$dados['Cod_atividade']);
    if ($dados['Tipo'] == 'I')
      /* 161 - Atividade individual no portfolio */
      $tipo = "[".RetornaFraseDaLista($lista_frases,161)."]";
    elseif ($dados['Tipo'] == 'G')
      /* 162 - Atividade em grupo no portfolio */
      $tipo = "[".RetornaFraseDaLista($lista_frases,162)."]";
  }
  else if ($dados['Ferramenta'] == 'F')
  {
    // 145 - F�rum de Discuss�o
    $tipo = RetornaFraseDaLista($lista_frases,145);
    $titulo = RetornaForum($sock,$dados['Cod_atividade']);
  }
  elseif ($dados['Ferramenta'] == 'B')
  {
    // 146 - Sess�o de Bate-Papo
    $tipo = RetornaFraseDaLista($lista_frases,146);
    $titulo = RetornaAssunto($sock,$dados['Cod_atividade']);
  }

  echo("<p>\n");
  echo("<table cellpadding='0' cellspacing='0' border='0' style='width: 100%; text-align: left;'>\n");
  echo("  <tbody>\n");
  echo("    <tr class=\"colorfield\">\n");
  // 123 - T�tulo
  echo("      <td style=\"vertical-align: top;\">&nbsp; ".RetornaFraseDaLista($lista_frases, 123)."</td>\n");
  // // ?? - [Ferramenta]
  // echo("      <td style=\"vertical-align: top;\">&nbsp; "."[Ferramenta]"."</td>\n");
  // ?? - [Tipo]
  echo("      <td style='vertical-align: top;'>&nbsp; [Tipo]</td>\n");
  // 19 - Valor
  echo("      <td style='vertical-align: top;'>&nbsp; ".RetornaFraseDaLista($lista_frases, 19)."</td>\n");
  echo("    </tr>\n");

  echo("    <tr class=\"text\">\n");
  echo("      <td style=\"vertical-align: top;\">&nbsp; ".$titulo."</td>\n");
  // echo("      <td style=\"vertical-align: top;\">&nbsp; ".$nome_ferramenta."</td>\n");
  echo("      <td style=\"vertical-align: top;\">&nbsp; ".$tipo."</td>\n");
  echo("      <td style=\"vertical-align: top;\">&nbsp; ".FormataNota($dados['Valor'])."</td>\n");
  echo("    </tr>\n");
  echo("  </tbody>\n");
  echo("</table>\n");
  echo("</p>\n");

  /*
    $titulo=RetornaTituloAvaliacao($sock,$dados['Ferramenta'],$dados['Cod_atividade']);
    if (!strcmp($dados['Ferramenta'],'F')) //Avaliacao no Forum
    {
      //$forum_dados = RetornaForum($sock,$dados['Cod_atividade']);
      // 12 - F�rum
      echo("    <font class=\"text\">".RetornaFraseDaLista($lista_frases,12).":</font>\n");
      echo("    <font class=\"text\"> ".$titulo."</font>");
    }
    elseif (!strcmp($dados['Ferramenta'],'B')) //Avaliacao no Bate-Papo
    {
      //$assunto_sessao = RetornaAssunto($sock,$dados['Cod_atividade']);
      // 13 - Assunto da Sess�o
      echo("    <font class=\"text\">".RetornaFraseDaLista($lista_frases,13).":</font>\n");
      echo("    <font class=\"text\"> ".$titulo."</font>");
    }
    else //Avaliacao no portfolio
    {
      //$atividade_dados = RetornaAtividade($sock,$dados['Cod_atividade']);
      // 14 - Atividade no Portf�lio
      echo("    <font class=\"text\">".RetornaFraseDaLista($lista_frases,14).":</font>\n");
      echo("    <font class=\"text\"> ".$titulo."</font><br>");
       // 20 - Tipo da Atividade
      echo("    <font class=\"text\">".RetornaFraseDaLista($lista_frases,20).":</font>\n");
      if (!strcmp($dados['Tipo'],'I'))
       // 21 - Individual
        echo("    <font class=\"text\"> ".RetornaFraseDaLista($lista_frases,21)."</font>");
      else
       // 22 - Em Grupo
        echo("    <font class=\"text\"> ".RetornaFraseDaLista($lista_frases,22)."</font>");
    }

    echo("<br>\n");
    // 58 - Valor da Atividade
    echo("<font class=\"text\">".RetornaFraseDaLista($lista_frases,58).": ".$dados['Valor']."</font><br>\n");

    echo("<p>\n");
     if (!$SalvarEmArquivo)
    // 46 - Ver objetivos/crit�rios da avalia��o
    echo("        <a class=\"text\" href=\"#\" onClick=\"window.open('ver.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&EhAtalho=1&cod_avaliacao=".$cod_avaliacao."','VerAvaliacao','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');return(false);\">".RetornaFraseDaLista($lista_frases,46)."</a><br><br>\n"); */

  if ((!strcmp($dados['Ferramenta'],'B')) && (!BatePapoExiste($sock,$dados['Cod_atividade'])))
  {
    /* 139 - Esta sess�o de bate-papo n�o foi realizada! */
    echo ("<font class=\"text\">".RetornaFraseDaLista($lista_frases,139)."</font><br>\n");
    // 13 - Fechar (gen)
    // 23 - Voltar (gen)
    /*
      echo("<form action=".$origem.".php?".RetornaSessionID()." method=post>\n");
      echo("      <input type=\"hidden\" name=\"cod_curso\"     value=\"".$cod_curso."\">\n");
      echo("      <input type=\"hidden\" name=\"cod_avaliacao\" value=\"".$cod_avaliacao."\">\n");
      if (($origem=="ver")&&($VeioDaAtividade))
        echo("<input class=\"text\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,13)."\"  onclick=\"self.close();\">\n");
      else
        echo("<input class=\"text\" type=\"submit\" value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\">\n");
      echo("</form>\n"); */
  }
  else
  {
    echo("<script language=\"javascript\">\n");
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

   /* Fun��o JavaScript para chamar p�gina para salvar em arquivo. */
    echo("      function SalvarParticipacao()\n");
    echo("      {\n");
    echo("        document.frmMsg.action = \"salvar_ver_participacao.php?".RetornaSessionID());
    echo("&cod_curso=".$cod_curso."\";\n");
    echo("        document.frmMsg.submit();\n");
    echo("      }\n\n");

    echo("function AbrePerfil(cod_usuario)\n");
    echo("{\n");
    echo("  window.open('../perfil/exibir_perfis.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno[]='+cod_usuario,'PerfilDisplay','width=620,height=400,top=60,left=60,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
    echo("  return(false);\n");
    echo("}\n");

    echo("  function RetornaMensagensAluno(funcao)\n");
    echo("  {\n");
    echo("    window.open(\"../forum/ver_mensagens_aluno.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&cod_forum=".$dados['Cod_atividade']."&cod_aluno=\"+funcao,\"MensagensParticipante\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
    echo("    return(false);\n");
    echo("  }\n");

    echo("  function RetornaFalasAluno(funcao)\n");
    echo("  {\n");
    echo("    window.open(\"../batepapo/ver_falas_aluno.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&cod_assunto=".$dados['Cod_atividade']."&cod_aluno=\"+funcao,\"FalasParticipante\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
    echo("    return(false);\n");
    echo("  }\n");

    echo("  function RetornaItensAluno(funcao)\n");
    echo("  {\n");
    echo("    window.open(\"../portfolio/ver_itens_aluno.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&cod_usuario_portfolio=\"+funcao,\"ItensPortfolioParticipante\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
    echo("    return(false);\n");
    echo("  }\n");

    echo("  function RetornaItensGrupo(funcao)\n");
    echo("  {\n");
    echo("    window.open(\"../portfolio/ver_itens_aluno.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&cod_grupo_portfolio=\"+funcao,\"ItensPortfolioParticipante\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
    echo("    return(false);\n");
    echo("  }\n");

    echo("function AbreJanelaComponentes(cod_grupo)\n");
    echo("{\n");
    echo("  window.open('componentes.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_grupo='+cod_grupo,'Componentes','width=400,height=300,top=150,left=250,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
    echo("  return false;\n");
    echo("}\n");

    echo("</script>\n");

    /*
      if (!$SalvarEmArquivo)
      {
        if (!strcmp($dados['Ferramenta'],'F'))
        {
        // 140 - Para visualizar as mensagens postadas por um participante, clique sobre o n�mero de participa��es
          echo("<font class=\"text\">".RetornaFraseDaLista($lista_frases,140)."</font><br>\n");
        }
        elseif (!strcmp($dados['Ferramenta'],'B'))
        {
        // 140 - Para visualizar as mensagens de um participante, clique sobre o n�mero de participa��es.
          echo("<font class=\"text\">".RetornaFraseDaLista($lista_frases,144)."</font><br>\n");
        }
        elseif (!strcmp($dados['Ferramenta'],'P'))
        {
          // 143 - Para visualizar os itens postados pelo participante, clique sobre o n�mero de participa��es..
          if (!strcmp($dados['Tipo'],'I'))
            echo("<font class=\"text\">".RetornaFraseDaLista($lista_frases,143)."</font><br>\n");
        }
      } */
    echo("<br>\n");

    $lista_usuarios=RetornaUsuarios($sock,$cod_curso);

    if (!strcmp($dados['Ferramenta'],'B'))
    {
      $lista_sessoes=RetornaCodSessao($sock,$dados['Cod_atividade']);
      foreach($lista_sessoes as $cod => $linha)
      {
        $msgs_qtde=RetornaQtdeMsgsUsuario($sock,$linha['Cod_sessao'],$lista_usuarios);
        //para cada aluno incrementar a quantidade de mensagens
        foreach($lista_usuarios as $cod => $nome)
        {
          $msgs_total[$cod]=$msgs_total[$cod]+$msgs_qtde[$cod];
        }
      }
    }

    if ((!strcmp($dados['Ferramenta'],'P')) && (!strcmp($dados['Tipo'],'G')))
    {
      $portfolio_grupo=1;
      $lista_grupos=RetornaListaGrupos($sock);
      $num_grupos=count($lista_grupos);
      if ($num_grupos > 0)
      {
        // // 141 - Para visualizar as atividades do grupo, clique sobre o n�mero de participa��es.
        // echo("<font class=\"text\">".RetornaFraseDaLista($lista_frases,141)."</font><br>\n"); //� atividade de portfolio em grupo
        // Tabela com a lista de grupos do curso, indicando quais participaram das atividades
        echo("<table border=0 width=100%>\n");
        echo("  <tr class=\"menu\">\n");
        /* 158 - Grupos */
        echo("    <td class=\"colorfield\" align=center>".RetornaFraseDaLista($lista_frases,158)."</td>\n");
        /* 49 - Participa��es */
        echo("    <td class=\"colorfield\" align=center width=50%>".RetornaFraseDaLista($lista_frases,49)."</td>\n");
        echo("    </tr>\n");

        foreach ($lista_grupos as $cod => $nome)
        {
          if ($i==0)
            $field="g1field";
          else
            $field="g2field";
          echo("    <tr class=".$field.">\n");

          $i = ($i + 1) % 2;

          echo("      <td class=\"text\">");
          if (!$SalvarEmArquivo)
            echo("<a class=\"text\" href=\"#\" onClick=\"return(AbreJanelaComponentes(".$cod."));\">".$nome."</a></td>");
          else
            echo($nome."</td>\n");

          $num_itens=RetornaNumItensPortfolioAvaliacao($sock,$cod,$cod_avaliacao,$portfolio_grupo,$cod_usuario,$usr_formador,"");
          if ($num_itens > 0)
          {
            echo("      <td class=\"text\" align=center>");
            if (!$SalvarEmArquivo)
              echo("      <a href=\"#\" onClick=\"return(RetornaItensGrupo(".$cod."));\">".$num_itens."</a></td>\n");
            else
              echo($num_itens."</td>\n");
          }
          else
          {
            // N�o Participou
            echo("      <td class=\"text\" align=center>0</td>\n");
          }
          echo("    </tr>\n");
        }
        echo("</table>\n");
      }
      else
        // 77 - N�o h� grupos criados
        echo(RetornaFraseDaLista($lista_frases,77)."<br>");
    }
    else
    {
      $portfolio_grupo=0;
      $lista_users=RetornaListaUsuariosAluno($sock);
      if (count($lista_users)>0)
      {
        // Tabela com a lista de alunos do curso, indicando quais entregaram as atividades
        echo("<table border=0 width=100%>\n");
        echo("  <tr class=\"menu\">\n");
        /* 64 - Alunos */
        echo("    <td class=\"colorfield\" align=center width=50%>".RetornaFraseDaLista($lista_frases,64)."</td>\n");
        /* 49 - Participa��es */
        echo("    <td class=\"colorfield\" align=center width=50%>".RetornaFraseDaLista($lista_frases,49)."</td>\n");
        echo("    </tr>\n");

        foreach($lista_users as $cod => $nome)
        {
          if ($i==0)
            $field="g1field";
          else
            $field="g2field";
          echo("    <tr class=".$field.">\n");

          $i = ($i + 1) % 2;

          echo("      <td class=\"text\">");
          if (!$SalvarEmArquivo)
            echo("<a class=\"text\" href=\"#\" onClick=\"return(AbrePerfil(".$cod."));\">".$nome."</a></td>\n");
          else
            echo($nome."</td>\n");

          if (!strcmp($dados['Ferramenta'],'B'))
          {
            if (ParticipouDaSessao($sock,$cod,$dados['Cod_atividade']))
            {
              if ((int)$msgs_total[$cod]==0)
                echo("      <td class=\"text\" align=center>0</td>\n");
              else
              {
                echo("      <td class=\"text\" align=center>");
                if (!$SalvarEmArquivo)
                  echo("      <a href=\"#\" onClick=\"return(RetornaFalasAluno(".$cod."));\">".(int)$msgs_total[$cod]."</a></td>\n");
                else
                  echo((int)$msgs_total[$cod]."</td>\n");
              }
            }
            else
            {
              // N�o Participou
              echo("      <td class=\"text\" align=center>0</td>\n");
            }
          }
          elseif (!strcmp($dados['Ferramenta'],'F'))
          {
            if (ParticipouDoForum($sock,$cod,$dados['Cod_atividade']))
            {
              $num_mensagens=RetornaNumMsgsParticipantesForum($sock,$dados['Cod_atividade'],$cod);
              echo("      <td class=\"text\" align=center>");
              if (!$SalvarEmArquivo)
                echo("      <a href=\"#\" onClick=\"return(RetornaMensagensAluno(".$cod."));\">".$num_mensagens."</a></td>\n");
              else
                echo($num_mensagens."</td>\n");
            }
            else
            {
              /*N�o Participou*/
              echo("      <td class=\"text\" align=center>0</td>\n");
            }
          }
          else
          {
            if (RealizouAtividadeNoPortfolio($sock,$cod_avaliacao, $cod,$portfolio_grupo))
            {
              $num_itens=RetornaNumItensPortfolioAvaliacao($sock,$cod,$cod_avaliacao,$portfolio_grupo,$cod_usuario,$usr_formador,$cod);
              echo("      <td class=\"text\" align=center>");
              if (!$SalvarEmArquivo)
                echo("      <a href=\"#\" onClick=\"return(RetornaItensAluno(".$cod."));\">".$num_itens."</a></td>\n");
              else
                echo($num_itens."</td>\n");
            }
            else
            {
              /*N�o Participou */
              echo("      <td class=\"text\" align=center>0</td>\n");
            }
          }
          echo("    </tr>\n");
        }
        echo("</table><br><br>\n");
      }

      $lista_users=RetornaListaUsuariosFormador($sock);
      if ((count($lista_users)>0) && ($usr_formador))
      {
        //Tabela com a lista de formadores do curso, indicando quais entregaram as atividades
        echo("<table border=0 width=100%>\n");
        echo("  <tr class=\"menu\">\n");
        /* 156 - Formadores*/
        echo("    <td class=\"colorfield\" align=center width=50%>".RetornaFraseDaLista($lista_frases,156)."</td>\n");
        /* 49 - Participa��es*/
        echo("    <td class=\"colorfield\" align=center width=50%>".RetornaFraseDaLista($lista_frases,49)."</td>\n");
        echo("    </tr>\n");

        foreach($lista_users as $cod => $nome)
        {
          if ($i==0)
            $field="g1field";
          else
            $field="g2field";
          echo("    <tr class=".$field.">\n");

          $i = ($i + 1) % 2;

          echo("      <td class=\"text\">");
          if (!$SalvarEmArquivo)
            echo("<a class=\"text\" href=\"#\" onClick=\"return(AbrePerfil(".$cod."));\">".$nome."</a></td>\n");
          else
            echo($nome."</td>\n");

          if (!strcmp($dados['Ferramenta'],'B'))
          {
            if (ParticipouDaSessao($sock,$cod,$dados['Cod_atividade']))
            {
              if ((int)$msgs_total[$cod]==0)
                echo("      <td class=\"text\" align=center>0</td>\n");
              else
              {
                echo("      <td class=\"text\" align=center>");
                if (!$SalvarEmArquivo)
                  echo("      <a href=\"#\" onClick=\"return(RetornaFalasAluno(".$cod."));\">".(int)$msgs_total[$cod]."</a></td>\n");
                else
                  echo((int)$msgs_total[$cod]."</td>\n");
              }
            }
            else
            {
              /*N�o Participou */
              echo("      <td class=\"text\" align=center>0</td>\n");
            }
          }
          elseif (!strcmp($dados['Ferramenta'],'F'))
          {
            $x=0;
            if (ParticipouDoForum($sock,$cod,$dados['Cod_atividade']))
            {
              $num_mensagens   =RetornaNumMsgsParticipantesForum($sock,$dados['Cod_atividade'],$cod);
              echo("      <td class=\"text\" align=center>");
              if (!$SalvarEmArquivo)
                echo("      <a href=\"#\" onClick=\"return(RetornaMensagensAluno(".$cod."));\">".$num_mensagens."</a></td>\n");
              else
                echo($num_mensagens."</td>\n");
            }
            else
            {
              /*N�o Participou */
              echo("      <td class=\"text\" align=center>0</td>\n");
            }
          }
          else
          {
            if (RealizouAtividadeNoPortfolio($sock,$cod_avaliacao,$cod,$portfolio_grupo))
            {
              $num_itens=RetornaNumItensPortfolioAvaliacao($sock,$cod,$cod_avaliacao,$portfolio_grupo,$cod_usuario,$usr_formador,$cod);
              echo("      <td class=\"text\" align=center>");
              if (!$SalvarEmArquivo)
                echo("      <a href=\"#\" onClick=\"return(RetornaItensAluno(".$cod."));\">".$num_itens."</a></td>\n");
              else
                echo($num_itens."</td>\n");
            }
            else
            {
              /*N�o Participou */
              echo("      <td class=\"text\" align=center>0</td>\n");
            }
          }
          echo("    </tr>\n");
        }
        echo("</table>\n");
      }
    }

    echo("    <form name=\"frmMsg\" method=\"post\">\n");

    echo("      <div align=right>\n");
    if (!$SalvarEmArquivo)
    {
      /* 50 - Salvar em Arquivo (geral) */
      echo("  <input class=\"text\" type=\"button\" value='".RetornaFraseDaLista($lista_frases_geral,50)."' onClick='SalvarParticipacao();'>\n");
      echo("  <input type=\"hidden\" name=\"cod_curso\"     value=\"".$cod_curso."\">\n");
      echo("  <input type=\"hidden\" name=\"cod_avaliacao\" value=\"".$cod_avaliacao."\">\n");
    }
    /* 14 - Imprimir */
    echo("<input class=\"text\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,14)."\" onClick=\"ImprimirRelatorio();\">\n");

    /*
      if (!$SalvarEmArquivo)
      {
        if ($VeioDaAtividade)
        // 13 - Fechar (ger)
          echo("  &nbsp;<input class=\"text\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,13)."\" onClick=\"self.close();\">\n");
        else
        // 23 - Voltar (gen)
          echo("<input class=\"text\" type=\"submit\" value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onClick=\"self.close();\">\n");
      }  */
    echo("      </div>\n");
    echo("      <br>\n");
    echo("    </form>\n");
  }
  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>
