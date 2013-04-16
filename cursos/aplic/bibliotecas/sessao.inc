<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : bibliotecas/sessao.inc

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
  ARQUIVO : bibliotecas/sessao.inc
  ========================================================== */

/* *******************************************************************
  RetornaSessionID - Retorna o identificador de session usado pelo PHP
                     Usar em toda chamada a arquivos (arquivo.php?".RetornaSessionID."&...)
  Saida: String com o Session (se necess�io, ou vazio se Cookie ativo)
*/
function RetornaSessionID()
{
  return SID;
}

/* *******************************************************************
  RetornaSessionIDInput - retorna a linha <input type=hidden name=PHPSESSID value=....> 
  Saida: String com o Session (se necess�io, ou vazio se Cookie ativo)
*/
function RetornaSessionIDInput()
{
  return "<input type=\"hidden\" name=\"PHPSESSID\" value=\"".session_id()."\" />\n";
}


/* Registra as variaveis globais do sistema
   IMPORTANTE: Somente cod_curso_s e cod_lingua_s s� globais.
               Todas as demais devem ser usadas por passagem de argumento
*/

/*if(strcmp(session_name(), "teleduc4.0") != 0){
  session_name("teleduc4.0");
}*/
 
// session_register("cod_ferramenta_s");


/* Se nenhuma lingua foi definida, portugu� �o padr� */
if (!isset($_SESSION['cod_lingua_s']))
{

  $sock=Conectar("");
  $query="select valor from Config where item='lingua'";
  $res=Enviar($sock,$query);
  Desconectar($sock);
  $linha=RetornaLinha($res);
  if (isset($linha['valor']))
    $_SESSION['cod_lingua_s']=$linha['valor']; 
  else
    $_SESSION['cod_lingua_s']=1;
}

if (isset($cod_ferramenta))
  $cod_ferramenta_s=$cod_ferramenta;

?>
