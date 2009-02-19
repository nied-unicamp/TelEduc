<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/mural/editar_assunto2.php

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
  ARQUIVO : cursos/aplic/mural/editar_assunto2.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("perguntas.inc");

  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,6);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock, $cod_curso, $cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,6);

  echo("  <html>\n");
  /* 1 - Perguntas Freqüentes */
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("    <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
  echo("    <link rel=stylesheet TYPE=text/css href=perguntas.css>\n");
  echo("\n");

  echo("  <body link=#0000ff vlink=#0000ff bgcolor=white>\n");
  /* 1 - Perguntas Freqüentes */
  $cabecalho = "  <b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>";
  /* 25 - Editar Assunto */
  $cabecalho .= "  <b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,25)."</b>";
  echo(PreparaCabecalho($cod_curso, $cabecalho, 6,7));
  echo("  <br>\n");

  /* Se o assunto for editado exibe uma mensagem confirmando o sucesso */
  /* e um botao para voltar aa pagina principal.                        */
  if (EditaAssunto($sock, $cod_assunto, $nome, $descricao))
  {
    echo("  <form action=perguntas.php method=post>\n");

    echo(RetornaSessionIDInput());
    echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");

    echo("    <input type=hidden name=cod_assunto_pai value=".$cod_assunto_pai.">\n");

    /* 26 - Assunto editado com sucesso. */
    echo("    <font class=text>".RetornaFraseDaLista($lista_frases,26)."</font>\n");
    echo("    <p>\n");
    /* 23 - Voltar */
    echo("    <input type=submit value='".RetornaFraseDaLista($lista_frases_geral, 23)."'>\n");
    echo("  </form>\n");
  } /* Se o assunto NAO pode ser editado apresenta uma mensagem de erro */
    /* e oferece a possibilidade de edita-lo.                            */
  else
  {
    echo("  <form name=voltar action=editar_assunto.php method=post>\n");

    echo(RetornaSessionIDInput());
    echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");

    echo("   <input type=hidden name=cod_assunto_pai value=".$cod_assunto_pai.">\n");
    echo("   <input type=hidden name=cod_assunto value=".$cod_assunto.">\n");

    /* 27 - Erro na edição do assunto. */
    echo("    <font class=text>".RetornaFraseDaLista($lista_frases,27)."</font>\n");
    echo("    <p>\n");
    echo("    <input type=hidden name=nome value='".$nome."'>\n");
    echo("    <input type=hidden name=descricao value='".$descricao."'>\n");
    /* 23 - Voltar */
    echo("    <input type=submit value='".RetornaFraseDaLista($lista_frases_geral, 23)."'>\n");
    echo("  </form>\n");
  }

  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);
?>
