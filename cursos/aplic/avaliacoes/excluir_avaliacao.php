<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/excluir_avaliacao.php

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
  ARQUIVO : cursos/aplic/avaliacoes/excluir_avaliacao.php
  ========================================================== */

  $bibliotecas = "../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("avaliacoes.inc");

  $cod_usuario = VerificaAutenticacao($cod_curso);

  $sock = Conectar("");

  $lista_frases = RetornaListaDeFrases($sock, 22);
  $lista_frases_geral = RetornaListaDeFrases($sock, -1);

  Desconectar($sock);

  $sock = Conectar($cod_curso);

  VerificaAcessoAoCurso($sock, $cod_curso, $cod_usuario);

  $dados=RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);

  echo("<html>\n");
  /* 1 - Avalia��es
  */
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("    <link rel=\"stylesheet\" type=\"text/css\" href=\"../teleduc.css\">\n");
  echo("    <link rel=\"stylesheet\" type=\"text/css\" href=\"avaliacoes.css\">\n");
  echo("\n");

  echo("\n\n");

  /* Verifica se o usuario eh formador. */
  $usr_formador = EFormador($sock, $cod_curso, $cod_usuario);

  /* 61 - A��o exclusiva a formadores. */
  if (!$usr_formador)
  {
    echo("<body link=#0000ff vlink=#0000ff bgcolor=white>\n");
    /* 1 - Avalia��es*/
    echo("<b class=\"titulo\">".RetornaFraseDaLista($lista_frases,1)."</b>\n");
    /* 8 - �rea restrita ao formador. */
    echo("<b class=\"subtitulo\"> - ".RetornaFraseDaLista($lista_frases,8)."</b><br>\n");
    /* 23 - Voltar (gen) */
    echo("<form><input type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onclick=\"history.go(-1);\"></form>\n");
    echo("</body></html>\n");
    Desconectar($sock);
    exit;
  }
  else
  {
    echo("<body link=#0000ff vlink=#0000ff bgcolor=white onload=self.focus();>\n");
    /* 1 - Avalia��es */
    $cabecalho ="<b class=\"titulo\">".RetornaFraseDaLista($lista_frases,1)."</b>";
    if ($operacao=="excluir")
    /* 80 - Excluir Avalia��o */
      $cabecalho.="<b class=\"subtitulo\"> - ".RetornaFraseDaLista($lista_frases,80)." </b>";

   /*COMO A LIXEIRA FOI EXCLUIDA DO AMBIENTE N�O HA MAIS NECESSIDADE DE TER COISAS REFERENTES A RECUPERAR NO CODIGO. POR ISSO FICARA TUDO COMENTADO AQUI.*/


    //elseif ($operacao=="recuperar")
    /* 81 - Recuperar Avalia��o */
      //$cabecalho.="<b class=\"subtitulo\"> - ".RetornaFraseDaLista($lista_frases,81)." </b>";

    $cod_pagina=10; //codigo da lixeira

    /* Cabecalho */
    echo(PreparaCabecalho($cod_curso,$cabecalho,22,$cod_pagina));

    echo("<br>\n");
    echo("<p>\n");

    // echo("    <form action=\"ver_lixeira_avaliacoes.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&".time()."\" method=post>\n");
    if ($tela_avaliacao == "P" || $tela_avaliacao == "A" || $tela_avaliacao == "F")
    {
      $form = "<form name=\"frmExcluir\" action=\"avaliacoes.php\">\n";
      $form.= RetornaSessionIDInput();
      $form.= "<input type=\"hidden\" name=\"cod_curso\"      value=\"".$cod_curso."\">\n";
      $form.= "<input type=\"hidden\" name=\"tela_avaliacao\" value=\"".$tela_avaliacao."\">\n";

      /* 23 - Voltar */
      $submit = "<input type=submit value='".RetornaFraseDaLista($lista_frases_geral, 23)."'>\n";
    }
    //else
    //{
      //echo(__FILE__." ".__LINE__." <pre>");
      //exit;
    //}

    echo($form);

    if ($operacao=="excluir")
    {
      if (ExcluiAvaliacao($sock, $cod_avaliacao,$cod_usuario))
      {
        /* 82 - Avalia��o exclu�da com sucesso. */
        echo("      <font class=\"text\">".RetornaFraseDaLista($lista_frases,82)."</font>\n");
      }
      else
      {
        /* 83 - Erro ao se excluir a avalia��o. */
        echo("      <font class=\"text\">".RetornaFraseDaLista($lista_frases,83)."</font>\n");
      }
    }

    /*NAO EXISTE MAIS "RECUPERAR"*/

    //else if ($operacao=="recuperar")
    //{
      //if (RecuperaAvaliacao($sock, $cod_avaliacao,$cod_usuario))
      //{
        /* 84 - Avalia��o recuperada com sucesso. */
        //echo("      <font class=\"text\">".RetornaFraseDaLista($lista_frases,84)."</font>\n");
      //}
      //else
      //{
        /* 85 - Erro ao se recuperar a avalia��o. */
        //echo("      <font class=\"text\">".RetornaFraseDaLista($lista_frases,85)."</font>\n");
      //}
    //}
    echo("      <p>\n");

    echo($submit);

    echo("    </form>\n");
    echo("  </body>\n");
    echo("  </html>\n");
    Desconectar($sock);
  }
?>
