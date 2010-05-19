<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/autenticacao/autenticacao.inc

    TelEduc - Ambiente de Ensino-Aprendizagem a Distï¿½ncia
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

    Nied - Nï¿½cleo de Informï¿½tica Aplicada ï¿½ Educaï¿½ï¿½o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitï¿½ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/autenticacao/autenticacao.inc
  ========================================================== */

/*******************************************************
  Funï¿½ao NomeDoUsuario - Retorna o nome do usuario
  Retorna uma string com o nome do usuario.
  Entradas: $sock - sock de conexao,
            $cod_usuario - codigo do usuario.
  Saida:   string com nome do usuario
******************************************************/
function NomeDoUsuario($sock, $cod_usuario)
{
  // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  if ($cod_usuario==-2)
  {
    $tuplas="Visitante";
  }
  else if ($cod_usuario==-1)
  {
    $tuplas="Administra&ccedil;&atilde;o do TelEduc";
  }
  else
  {
    $cod_usuario_global = RetornaCodigoUsuarioGlobal($sock, $cod_usuario, $_SESSION['cod_curso_s']);
    $query="select nome from ".$dbnamebase.".Usuario where cod_usuario = '".$cod_usuario_global."'";
    $res=Enviar($sock, $query);

    $linhas=RetornaLinha($res);
    $tuplas=$linhas['nome'];
  }
  return($tuplas);
}


/*******************************************************
  Funï¿½ao EliminaEspacos - Elimina espacos no comeï¿½o e no
    final da frase.
  Retorna a frase sem os espacos no comeï¿½o e no final dela.
  Entradas: $frase - frase
  Saida:   frase sem os espacos no comeï¿½o e no final dela.
************************************** Atribue a flag de X (Deletado) ao campo
    status da mensagem.****************/

function EliminaEspacos($frase)
{
  return(trim($frase));
}


/* **********************************************************************
 * BoolEhEmail - Verifica se o $login <parametro> é um email, apenas.
 * 
 * Entrada: $login - login/email do usuário
 * 
 * Saída: 1 se for email, 0 caso contrario;
**/
function BoolEhEmail($login)
{
	return (strchr($login,"@") ? 1 : 0);  
}
?>