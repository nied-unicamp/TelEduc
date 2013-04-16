<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/correio/remove_link_simbolico.php

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
  ARQUIVO : cursos/aplic/correio/remove_link_simbolico.php
  ========================================================== */

/* C�digo principal */
  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("correio.inc");


  $cod_usuario_global=VerificaAutenticacao($cod_curso);

  $sock=Conectar($cod_curso);

  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso); 

  Desconectar($sock);
  $sock = Conectar("");

  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');

  $cod_usuario_temp=$cod_usuario;
  if ($cod_usuario_temp<0)
    $cod_usuario_temp=0;

  $dir_temp=$diretorio_temp."/correio_".$cod_curso."_".$cod_usuario_temp;
  $link_temp="../../diretorio/correio_".$cod_curso."_".$cod_usuario_temp;

  /* se existe o link simbolico, apagamos. */
  if (ExisteArquivo($dir_temp))
    RemoveArquivo($dir_temp);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,11);

  ExpulsaConvidadoPassivo($sock, $cod_usuario, $cod_usuario, $lista_frases);

  echo("<html>\n");
  /* 1 - Correio */
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>");
  echo("<body link=#0000ff vlink=#0000ff bgcolor=#FFFFFF onLoad=self.close();>\n");
  Desconectar($sock);
  echo("</body>\n");
  echo("</html>");

?>