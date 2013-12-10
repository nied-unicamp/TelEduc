<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/grupos/novo_comp2.php

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
  ARQUIVO : cursos/aplic/grupos/novo_comp2.php
  ========================================================== */

/* C�digo principal */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("grupos.inc");

  $cod_ferramenta=12;
  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,12);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  if (EVisitante($sock, $cod_curso, $cod_usuario))
  {
    echo("<html>\n");
    /* 1 - Grupos */
    echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
    echo("  <link rel=\"stylesheet\" TYPE=\"text/css\" href=\"../teleduc.css\">\n");
    echo("  <link rel=\"stylesheet\" TYPE=\"text/css\" href=\"grupos.css\">\n");

    echo("  <body link=\"#0000ff\" vlink=\"#0000ff\" bgcolor=\"white\">\n");
    /* 1 - Grupos */
    $cabecalho = "  <b class=\"titulo\">".RetornaFraseDaLista($lista_frases, 1)."</b>";
    /* 504 - �rea restrita a alunos e formadores */
    $cabecalho .= "  <b class=\"subtitulo\"> - ".RetornaFraseDaLista($lista_frases_geral, 504)."</b>";
    echo($cabecalho);

    echo("    <br>\n");
    echo("    <p>\n");
    echo("  </body>\n");
    echo("  </html>\n");
    Desconectar($sock);
    exit();
  }
  
  echo("<html>\n");
  /* 1 - Grupos */
  echo("<head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("<link rel=\"stylesheet\" type=\"text/css\" href=\"../teleduc.css\">\n");
  echo("<link rel=\"stylesheet\" type=\"text/css\" href=\"grupos.css\">\n");
  echo("<body link=\"#0000ff\" vlink=\"#0000ff\" bgcolor=\"white\" onload=\"self.focus();\">\n");
  /* 1 - Grupos */
  $cabecalho ="<font class=\"titulo\"><b>".RetornaFraseDaLista($lista_frases,1)."</b></font>\n";
  /* 29 - Novo Componente */
  $cabecalho.="<font class=\"subtitulo\"><b> - ".RetornaFraseDaLista($lista_frases,29)."</b></font>";
  echo(PreparaCabecalho($sock,$cod_curso,$cabecalho,12,5));
  echo("<br>\n");
  echo("<P>\n");

  if (!GruposFechados($sock) || EFormador($sock,$cod_curso,$cod_usuario))
  {
    ExcluirTodosUsuariosDoGrupo($sock,$cod_grupo);
    if (count($select_destino)>0)
    {
      foreach ($select_destino as $cod => $linha)
      {
        InsereUsuarioNoGrupoGU($sock,$cod_grupo,$linha);
      }
    }

    echo("<script language=\"javascript\">\n");
    echo("  self.opener.location.reload();\n");
    echo("  self.location='componentes.php?cod_curso=".$cod_curso."&cod_grupo=".$cod_grupo."';\n");
    echo("</script>\n");
  }
  else
  {
    echo("<script language=\"javascript\">\n");
    echo("  self.opener.location.reload();\n");
    echo("</script>\n");

    echo("<P>\n");
    /* 52 - Os grupos j� est�o formados. N�o foi poss�vel inserir novos componentes. */
    echo("<font class=\"text\"><b>".RetornaFraseDaLista($lista_frases,52)."</b></font>\n");
    echo("<P>\n");
    /* G 23 - Voltar */
    echo("<form><input type=\"button\" value='".RetornaFraseDaLista($lista_frases_geral,23)."' onclick=\"document.location='componentes.php?cod_curso=".$cod_curso."&cod_grupo=".$cod_grupo."';\"></form>\n");
  }

  Desconectar($sock);

  echo("</body>\n");
  echo("</html>\n");

?>
