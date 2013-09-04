<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/exercicios/ver_gabarito.inc

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
  ARQUIVO : cursos/aplic/exercicios/ver_gabarito.inc
  ========================================================== */
/* **********************************************************************
   VerificaResolucaoO - Verifica se a quest�o ja possui uma resolucao, ou seja, foi corrigido pelo formador 
   Entrada: $cod_usuario - c�digo do usuario
            $cod_curso - c�digo do curso
            $cod_resolucao - c�digo da resolucao do exercicio
   Saida: a nota caso o exercicio tenha sido corrigido, "" caso contrario           
*/
function VerificaResolucaoO($cod_curso,$cod_questao,$cod_resolucao)
{
  $sock=Conectar($cod_curso);
  $consulta = "select nota from Exercicios_resolucao_questao_obj where cod_resolucao = ".$cod_resolucao." and cod_questao=".$cod_questao;
  $res = Enviar($sock, $consulta);
  $linha = RetornaLinha($res);
  //Desconectar($sock);

  if($linha != NULL)
    return $linha['nota'];
  else
    return "";
}

/* **********************************************************************
   VerificaResolucaoM - Verifica se a quest�o ja possui uma resolucao, ou seja, foi corrigido pelo formador 
   Entrada: $cod_usuario - c�digo do usuario
            $cod_curso - c�digo do curso
            $cod_resolucao - c�digo da resolucao do exercicio
   Saida: a nota caso o exercicio tenha sido corrigido, "" caso contrario           
*/
function VerificaResolucaoM($cod_curso,$cod_questao,$cod_resolucao)
{
  $sock=Conectar($cod_curso);
  $consulta = "select nota from Exercicios_resolucao_questao_mult where cod_resolucao = ".$cod_resolucao." and cod_questao=".$cod_questao;
  $res = Enviar($sock, $consulta);
  $linha = RetornaLinha($res);
  //Desconectar($sock);

  if($linha != NULL)
    return $linha['nota'];
  else
    return "";
}

/* **********************************************************************
   VerificaResolucaoD - Verifica se a quest�o ja possui uma resolucao, ou seja, foi corrigido pelo formador 
   Entrada: $cod_usuario - c�digo do usuario
            $cod_curso - c�digo do curso
            $cod_resolucao - c�digo da resolucao do exercicio
   Saida: a nota caso o exercicio tenha sido corrigido, "" caso contrario           
*/
function VerificaResolucaoD($cod_curso,$cod_questao,$cod_resolucao)
{
  $sock=Conectar($cod_curso);
  $consulta = "select nota from Exercicios_resolucao_questao_diss where cod_resolucao = ".$cod_resolucao." and cod_questao=".$cod_questao;
  $res = Enviar($sock, $consulta);
  $linha = RetornaLinha($res);
  //Desconectar($sock);

  if($linha != NULL)
    return $linha['nota'];
  else
    return "";

}

/* **********************************************************************
   VerificaComentario - Verifica se o exercicio dissertativo possui coment�rio
   Entrada: $cod_usuario - c�digo do usuario
            $cod_curso - c�digo do curso
            $cod_resolucao - c�digo da resolucao do exercicio
   Saida: string caso o exercicio tenha sido comentado, false caso contrario           
*/
function VerificaComentario($cod_curso,$cod_questao,$cod_resolucao)
{
  $sock=Conectar($cod_curso);
  $consulta = "select comentario from Exercicios_resolucao_questao_comentarios where cod_resolucao = ".$cod_resolucao." and cod_questao=".$cod_questao;
  $res = Enviar($sock, $consulta);
  $linha = RetornaLinha($res);
  //Desconectar($sock);

  if($linha != NULL)
    return $linha['comentario'];
  else
    return false;
}

/* **********************************************************************
   VerificaGabaritoD - Verifica se o exercicio dissertativo possui gabarito
   Entrada: $cod_questao - c�digo da quest�o
            $cod_resolucao - c�digo da resolucao do exercicio
            $cod_curso - c�digo do curso
   Saida: string caso o exercicio tenha gabarito, false caso contrario
*/
function VerificaGabaritoD($cod_curso,$cod_questao)
{
  $sock=Conectar($cod_curso);
  $consulta = "select gabarito from Exercicios_questao_diss where cod_questao=".$cod_questao;
  $res = Enviar($sock, $consulta);
  $linha = RetornaLinha($res);
  //Desconectar($sock);

  if($linha != NULL)
    return $linha['gabarito'];
  else
    return false;
}

/* **********************************************************************
   RespostaQuestao - Entrega a string de resposta da quest�o
   Entrada: $cod_questao - c�digo da quest�o
            $cod_curso - c�digo do curso
   Saida: string de resposta caso o exercicio tenha gabarito, false caso contrario           
*/
function RespostaQuestao($cod_curso,$cod_questao)
{
  $sock=Conectar($cod_curso);
  $consulta = "select gabarito from Exercicios_questao_obj where cod_questao=".$cod_questao;
  $res=Enviar($sock, $consulta);
  $gabarito=RetornaLinha($res);
  if($gabarito != NULL)
    return $gabarito['gabarito'];
  return false;
}


?>