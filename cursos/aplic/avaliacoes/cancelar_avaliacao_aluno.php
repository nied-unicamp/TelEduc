<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/cancelar_avaliacao_aluno.php

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
  ARQUIVO : cursos/aplic/avaliacoes/cancelar_avaliacao_aluno.php
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
    $tabela="Avaliacao_notas";

  /*MODIFICA��ES AQUI PARA QUE NO CASO DE EXERCICIO EM GRUPO ELE CANCELE 
  CORRETAMENTE A AVALIA��O*/
  if ( (!strcmp($ferramenta,'P')) || (!strcmp($ferramenta,'E')) ) 
  {

    if ($portfolio_grupo || $exercicio_grupo)
    {
      $lista_integrantes=RetornaListaIntegrantes($sock,$cod_grupo);
      foreach ($lista_integrantes as $cod_aluno => $linha)
      {
        CancelaEdicaoAvaliacaoElementodoGrupo($sock, $tabela, $cod_aluno, $cod_avaliacao);
      }
    }
    else
      CancelaEdicaoAvaliacaoParticipante($sock, $tabela, $cod_nota);
  }
  else
    CancelaEdicaoAvaliacaoParticipante($sock, $tabela, $cod_nota);


  echo("<html>\n");
  /* 1 - Avalia��es */
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("  <link rel=\"stylesheet\" type=\"text/css\" href=\"../teleduc.css\">\n");
  echo("  <link rel=\"stylesheet\" type=\"text/css\" href=\"../avaliacoes/avaliacoes.css\">\n");

  echo("<body link=#0000ff vlink=#0000ff bgcolor=white onload=self.focus();>\n");
  echo("");
  echo("<script language=\"javascript\">\n");
  echo("self.close();");
  echo("</script>\n");

  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);
?>
