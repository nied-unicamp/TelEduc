<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/batepapo/sala_base.php

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
  ARQUIVO : cursos/aplic/batepapo/sala_base.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("batepapo.inc");

  $cod_curso = $_GET['cod_curso'];
  $cod_usuario_global=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,10);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,10);

  echo("<html>\n");
  /* 1 - Bate-Papo */
  echo("  <head>\n");
  echo("    <title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title>\n");
  echo("  </head>\n");

  echo("  <link rel=\"stylesheet\" type=\"text/css\" href=\"../teleduc.css\">\n");
  echo("  <link rel=\"stylesheet\" type=\"text/css\" href=\"batepapo.css\">\n");
  echo("  <font class=\"text\">Bem vindo(a) &agrave; sess&atilde;o de Batepapo!<br>Aguarde um instante...</font>\n");
  echo("</body>\n");
  echo("</html>\n");
  
?>
