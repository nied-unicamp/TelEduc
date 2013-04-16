<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : bibliotecas/geral.inc

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
  ARQUIVO : bibliotecas/geral.inc
  ========================================================== */

/* Caso n�o esteja no nivel /cursos/aplic/ferramenta/, deve-se setar a
   vari�vel 
if (!isset($bibliotecas))
  $bibliotecas="../bibliotecas/";
*/


session_start();

/* De agora em diante, usaremos o teleduc.inc, 
 * e para n�o quebrar o ramo desenvolvimento,
 * vamos manter a compatibilidade reversa.
 */
include("teleduc.inc");

/* Fun��es de convers�o de texto */
include ("conversor_texto.inc");

/* Fun��es de acesso a base de dados */
include ("acesso_sql.inc");

/* Fun��es controle de Session do PHP */
/* Define as globais: cod_curso_s, cod_lingua_s, visitante_s, visao_aluno_s */
include ("sessao.inc");

/* Fun��es multilinguais */
include ("linguas.inc");

/* Fun��es de autentica��o */
include ("acesso_php.inc");

/* Fun��es de e-mail */
include ("email.inc");

/* Fun��es de data */
include ("data.inc");

include ("data_javascript.inc");

/* Fun��es de manipula��o de arquivos*/
include ("arquivos.inc");

/* Fun��es de controle de usu�rios*/
include ("usuarios.inc");

/* Fun��es de controle de cursos*/
include ("cursos.inc");

include ("FeedbackObject.inc.php");

?>