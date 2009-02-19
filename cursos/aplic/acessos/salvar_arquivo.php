<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/acessos/salvar_arquivo.php

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
  ARQUIVO : cursos/aplic/acessos/salvar_arquivo.php
  ========================================================== */

  header("Content-type: application/octet-stream");
  header("Content-Disposition: attachment; filename=".$nome_arquivo);
  
  
  $SalvarEmArquivo=1;

  if ($origem == "acessos")
    include "relatorio_acessos.php";
  else if ($origem == "freq")
    include "relatorio_frequencia.php";
  else if ($origem == "freq2")
    include "relatorio_frequencia2.php";
  else if ($origem == "ferr_usr")
    include "relatorio_ferramentas_usr.php";
  else if ($origem == "ferr_grp")
    include "relatorio_ferramentas_grp.php";


?>
