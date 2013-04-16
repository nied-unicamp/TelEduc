<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/index_avaliacao.php

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
  ARQUIVO : cursos/aplic/avaliacoes/index_avaliacao.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("avaliacoes.inc");

  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases = RetornaListaDeFrases($sock, 22);
  $lista_frases_geral = RetornaListaDeFrases($sock, -1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);
    $tabela="Avaliacao";

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,22);

  if ($cancelar_edicao_avaliacao=="sim")
  {
     CancelaEdicaoAvaliacao($sock, $tabela, $cod_avaliacao, $cod_usuario);
  }

  if ($origem=="../exercicios/exercicios")
  {
    $query="update Exercicios_aplicado set avaliacao=0 where cod_modelo=$cod_modelo";
    Enviar($sock,$query);
  }
      

  if($origem=="../material/material")
  {
     echo("  <html>\n");
    /* 1 - Avalia��es */
    echo("    <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
    echo("    <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
    echo("  <link rel=stylesheet TYPE=text/css href=../avaliacoes/avaliacoes.css>\n");
    echo("\n\n");

    echo("<body link=#0000ff vlink=#0000ff bgcolor=white onload=self.focus();>\n");
    /* 1 - Avalia��es */
    echo("<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>\n");
    /* 9 - Cadastro de Avalia��o */
    echo("<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,9)." </b>\n");

    echo("<script language=JavaScript>\n");
    echo("self.close();");
    echo("</script>\n");

  }
  elseif ($origem=="alterar_avaliacao")               //neste caso veio da atividade (forum,batepapo ou atividade de portfolio)
  {
    echo("  <html>\n");
    /* 1 - Avalia��es */
    echo("    <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
    echo("    <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
    echo("  <link rel=stylesheet TYPE=text/css href=../avaliacoes/avaliacoes.css>\n");
    echo("\n\n");

    echo("<body link=#0000ff vlink=#0000ff bgcolor=white onload=self.focus();>\n");
    /* 1 - Avalia��es */
    echo("<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>\n");
    /* 9 - Cadastro de Avalia��o */
    echo("<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,9)." </b>\n");

    echo("<script language=JavaScript>\n");
    echo("self.close();");
    echo("</script>\n");
  }
  else              //neste caso veio de avalia�oes
  {
    echo("<html>\n");
    echo("<head>\n");
    echo("<title>TelEduc</title>\n");
    echo("</head>\n");

    echo("<Frameset cols='200,*' border=0 frameborder=0>\n");
    echo("<Frame src=../menu.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&time=".time()."&cod_ferramenta=22 name=menu>\n");
    echo("<Frame src=".$origem.".php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_topico_raiz=".$cod_topico."&cod_avaliacao=".$cod_avaliacao."&time=".time()." NORESIZE SCROLLING=auto name=direita>\n");
    echo("</Frameset>\n");
  }
  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);
?>
