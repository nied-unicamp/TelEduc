<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/intermap/exibir_perfil.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist�ncia
    Copyright (C) 2001-2004  NIED - Unicamp

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
  ARQUIVO : cursos/aplic/intermap/exibir_perfil.php
  ========================================================== */

  // Par�metros necess�rios
  // $cod_curso: c�digo do curso
  // $cod_usuario_perfil: c�digo do usu�rio cujo perfil ser� mostrado

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");

  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,13);

  Desconectar($sock);

  echo("<html>\n");

  echo("<script language=\"javascript\">\n");
  echo("  window.open(\"../perfil/exibir_perfis.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno[]=".$cod_usuario_perfil."\",\"PerfilDisplay\",\"width=600,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("</script>\n");

  echo("</html>\n");

?>
