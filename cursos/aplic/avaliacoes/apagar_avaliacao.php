<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/apagar_avaliacao.php

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
  ARQUIVO : cursos/aplic/avaliacoes/apagar_avaliacao.php
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

  echo("<html>\n");
  /* 1 - Avalia��es*/
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("    <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
  echo("    <link rel=stylesheet TYPE=text/css href=avaliacoes.css>\n");
  echo("\n");

  echo("\n\n");

  $cod_pagina=1;

  /* Verifica se o usuario eh formador. */
  $usr_formador = EFormador($sock, $cod_curso, $cod_usuario);

  /* 61 - A��o exclusiva a formadores. */
  if (!$usr_formador)
    exit(RetornaFraseDaLista($lista_frases, 61));

  echo("<body link=#0000ff vlink=#0000ff bgcolor=white onload=self.focus();>\n");

  // echo(__FILE__." ".__LINE__."<pre>\n"); var_dump($origem); var_dump($cod_avaliacao);

  // 1 - Avalia��es
  // 26 - Apagar Avalia��o
  $cabecalho ="<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b> - <b class=subtitulo>".RetornaFraseDaLista($lista_frases, 26)."</b>\n";
  $cod_pagina=1;
  /* Cabecalho */
  echo(PreparaCabecalho($cod_curso,$cabecalho,COD_AVALIACAO,$cod_pagina));


  if ($origem == "ver" || $origem == "avaliacoes")
  {
    // Avalia��o apagada com sucesso.
    echo("<form name=frmApagar action=avaliacoes.php> \n");
    echo(RetornaSessionIDInput());
    echo("  <input type=hidden name=cod_curso value=".$cod_curso." \n");
    echo("  <input type=hidden name=tela_avaliacao value=".$tela_avaliacao." \n");
    echo("</form> \n");
    if (ApagaAvaliacao($sock, $cod_avaliacao, $cod_usuario))
    {
      echo("<script language=JavaScript>document.frmApagar.submit();</script>\n");
    }
    else
    {
      // 28 - Erro ao apagar a avalia��o.
      echo("<script language=JavaScript> alert('".RetornaFraseDaLista($lista_frases, 28)."'); </script>\n");
    }
  }
  else
  {
    echo(__FILE__." ".__LINE__." origem inv�lida !<pre>\n"); var_dump($origem);
    exit;
  }

   echo("  </body>\n");
   echo("  </html>\n");
   Desconectar($sock);

?>
