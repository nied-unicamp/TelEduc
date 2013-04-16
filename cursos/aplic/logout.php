<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : logout.php


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
  ARQUIVO : logout.php
  ========================================================== */

session_start();

//$_SESSION['logout_flag_s'] = 1;

//unset($_SESSION['cod_lingua_s']);
//unset($_SESSION['visitantes_s']);
//unset($_SESSION['visao_aluno_s']);
//unset($_SESSION['lista_frases_s']);
//unset($_SESSION['login_usuario_s']);
//unset($_SESSION['cod_usuario_global_s']);

session_destroy();


//$_SESSION['logout_flag_s']=0;

header("Location: ../../pagina_inicial/autenticacao_cadastro.php?logout=1");
exit;

?>
