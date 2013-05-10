<?php

/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/dumpEmails/acoes.php

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

    Nied - Ncleo de Informática Aplicada à Educação
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
  ARQUIVO : administracao/exportar_resultados.php
  ========================================================== */

  session_start();

  header("Cache-Control: public");
  header("Content-Description: File Transfer");
  //header("Content-Length: ". filesize("$filename").";");
  header("Content-Disposition: attachment; filename=resultSet.csv");
  header("Content-Type: application/octet-stream; "); 
  header("Content-Transfer-Encoding: binary");

  $lista_s = $_SESSION['lista_s'];

  foreach($lista_s as $cod => $linha) {
    print $linha['NomeBase'] . "\n\n";
    print implode(",\t", $linha['Campos']);
    print "\n";
    foreach($linha['Res'] as $results) {
      print implode(",\t", $results);
      print "\n";
    }
  }

?>