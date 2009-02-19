<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/batepapo/sala_base.php

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
  ARQUIVO : cursos/aplic/batepapo/sala_base.php
  ========================================================== */
  
  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("batepapo.inc");
  
  $cod_usuario=VerificaAutenticacao($cod_curso);
  
  $sock=Conectar("");
  
  $lista_frases=RetornaListaDeFrases($sock,10);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);
  
  Desconectar($sock);
  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,10);
  echo("<html>");
  echo("<body>");

  while (true){
  }


  echo("</body>\n");
  echo("</html>\n");
  Desconectar($sock);

?>

