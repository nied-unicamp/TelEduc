<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/alterar_nomenclatura2.php

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
  ARQUIVO : cursos/aplic/administracao/alterar_nomenclatura2.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");

  $cod_usuario = VerificaAutenticacao($cod_curso);

  $sock = Conectar("");

  $lista_frases = RetornaListaDeFrases($sock,0);
  $lista_frases_geral = RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock = Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  echo("<html>\n");
  /* 1 - Administra�o */
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("  <link rel=stylesheet TYPE=text/css href=\"../teleduc.css\">\n");
  echo("  <link rel=stylesheet TYPE=text/css href=\"administracao.css\">\n");

  if (!EFormador($sock,$cod_curso,$cod_usuario))
  {
    echo("<body link=#0000ff vlink=#0000ff bgcolor=white onLoad='document.frmErro.cmdVoltar.focus();'>\n");
    /* 1 - Administra�o */
    $cabecalho = "    <b class=titulo>".RetornaFraseDaLista($lista_frases, 1)."</b>";
    /* 28 - �ea restrita ao formador. */
    $cabecalho .= "    <b class=subtitulo> - ".RetornaFraseDaLista($lista_frases, 28)."</b>";
    echo($cabecalho);
    echo("    <br>\n");
    /* 23 - Voltar (gen) */
    echo("<form name=frmErro><input class=text type=button name=cmdVoltar value='".RetornaFraseDaLista($lista_frases_geral,23)."' onclick=history.go(-1);></form>\n");
    echo("</body></html>\n");
    Desconectar($sock);
    exit;
  }


  echo("  <body link=#0000ff vlink=#0000ff bgcolor=white onLoad='document.frmAlterar.cmdVoltar.focus();'>\n");

  /* 1 - Administra�o */
  $cabecalho = "    <b class=titulo>".RetornaFraseDaLista($lista_frases, 1)."</b>";
  /* 149 - Alterar nomenclatura do coordenador */
  $cabecalho .= "    <b class=subtitulo> - ".RetornaFraseDaLista($lista_frases, 149)."</b>";
  echo(PreparaCabecalho($sock,$cod_curso, $cabecalho, 0,12));
  echo("    <br>\n");

  if (AlteraCursoConfig($sock, 'status_coordenador', $status))
  {
    /* 23 - Nomenclatura do coordenador alterada com sucesso. */
    echo("    <font class=text>\n");
    echo("       ".RetornaFraseDaLista($lista_frases, 151)."<br>\n");

    /* Se optou por ser visto como formador refor� essa informa�o ao usu�io */
    if ($status == 'F')
    {
      /* 152 - O coordenador passar�a ser visto como um formador. */
      echo("       ".RetornaFraseDaLista($lista_frases, 152)."<br>\n");
    }
    echo("    </font>\n");

    echo("    <form name=frmAlterar method=post action=\"administracao.php?cod_curso=".$cod_curso."\">\n");
  }
  else
  {
    /* 153 - Erro ao alterar a nomenclatura. */
    echo("    <font class=text>".RetornaFraseDaLista($lista_frases, 153)."<font><br>\n");

    echo("    <form name=frmAlterar method=post action=\"alterar_nomenclatura.php?");
  }
  echo("      <p>\n");
  echo("      <input class=text type=submit name=cmdVoltar value=".RetornaFraseDaLista($lista_frases_geral, 23).">\n");

  echo("    </form>\n");

  echo("  </body>\n");
  echo("</html>\n");
  Desconectar($sock);
?>
