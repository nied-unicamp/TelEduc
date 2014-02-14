<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/ajuda/ajuda.inc

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
  ARQUIVO : administracao/ajuda/ajuda.inc
  ========================================================== */

/* **********************************************************************
   RetornaTextoDaAjuda - Retorna o texto referente aos par�metros passados na Entrada.
   Entrada: $sock = Socket da conex�o atual
            $cod_ferramenta = C�digo da ferramenta da qual se quer o texto da Ajuda
            $cod_lingua =  C�digo da l�ngua na qual se quer o texto da Ajuda
            $cod_pagina =  C�digo da p�gina da ferramenta da qual se quer o texto da Ajuda
            $tipo_usuario = Para saber se o texto ser� o exibido na vis�o do aluno ou formador.
   Saida: $array['texto'] = O referido texto
*/
function RetornaTextoDaAjuda($sock,$cod_ferramenta,$cod_pagina,$cod_lingua,$tipo_usuario)
{
  $query="select texto from Ajuda where cod_ferramenta=".$cod_ferramenta." and cod_pagina=".$cod_pagina." and cod_lingua=".$cod_lingua." and tipo_usuario='".$tipo_usuario."'\n";
  $res=Enviar($sock,$query);
  $array=RetornaLinha($res);
  return($array['texto']);
}

/* **********************************************************************
   CriaCampoTexto - Cria na tabela Ajuda uma nova linha para inser��o do texto da Ajuda
   Entrada: $sock = Socket da conex�o atual
            $cod_ferramenta = C�digo da ferramenta da qual se quer o texto da Ajuda
            $cod_pagina = C�digo da p�gina da Ajuda
            $cod_lingua =  C�digo da l�ngua na qual se quer o texto da Ajuda
            $nome_pagina =  Nome da p�gina da ferramenta da qual se quer o texto da Ajuda
            $tipo_usuario = Para saber se o texto ser� o exibido na vis�o do aluno ou formador.
   Saida: Cria a linha especificada
*/
function CriaCampoTexto($sock,$cod_ferramenta,$cod_pagina,$nome_pagina,$cod_lingua,$tipo_usuario)
{

  $query="insert INTO Ajuda (cod_ferramenta,cod_pagina,cod_lingua,tipo_usuario,texto,nome_pagina) VALUES(".$cod_ferramenta.",".$cod_pagina.",".$cod_lingua.",'".$tipo_usuario."','','".$nome_pagina."')\n";
  Enviar($sock,$query);
}

/* **********************************************************************
   AtualizaTexto - Atualiza o texto da Ajuda
   Entrada: $sock = Socket da conex�o atual
            $cod_ferramenta = C�digo da ferramenta da qual se quer atualizar o texto da Ajuda
            $cod_pagina = C�digo da p�gina da qual se quer atualizar o texto da Ajuda
            $cod_lingua =  C�digo da l�ngua na qual se quer o texto da Ajuda
            $tipo_usuario = Para saber se o texto ser� o exibido na vis�o do aluno ou formador
            $texto = Texto novo
   Saida: Atualiza texto da Ajuda
*/
function AtualizaTexto($sock,$cod_ferramenta,$cod_pagina,$cod_lingua,$tipo_usuario,$texto)
{
  $query="update Ajuda SET texto='".$texto."' where cod_ferramenta=".$cod_ferramenta." and cod_pagina=".$cod_pagina." and cod_lingua=".$cod_lingua." and tipo_usuario='".$tipo_usuario."'\n";
  Enviar($sock,$query);
}

/* **********************************************************************
   AtualizaNomePagina - Atualiza o nome da p�gina da Ajuda
   Entrada: $sock = Socket da conex�o atual
            $cod_pagina = C�digo da p�gina que se quer atualizar o nome
            $nome_pagina = Novo nome da p�gina
            $cod_ferramenta = C�digo da ferramenta da qual se quer atualizar o nome da p�gina
            $cod_lingua =  C�digo da l�ngua na qual se quer atualizar o nome da p�gina
            $tipo_usuario = Tipo do usu�rio que se quer atualizar o nome da p�gina
   Saida: Atualiza nome da p�gina da Ajuda
*/
function AtualizaNomePagina($sock,$cod_pagina,$nome_pagina,$cod_ferramenta,$cod_lingua,$tipo_usuario)
{
  $query="update Ajuda SET nome_pagina='".$nome_pagina."' where cod_pagina=".$cod_pagina." and cod_ferramenta=".$cod_ferramenta." and cod_lingua=".$cod_lingua." and tipo_usuario='".$tipo_usuario."'\n";
  Enviar($sock,$query);
}

/* **********************************************************************
   RetornaCodPagina - Retorna o c�digo da p�gina da Ajuda
   Entrada: $sock = Socket da conex�o atual
            $nome_pagina = Nome da p�gina que se quer o c�digo
            $cod_ferramenta = C�digo da ferramenta cuja p�gina se quer o c�digo
            $cod_lingua =  C�digo da l�ngua cuja p�gina se quer o c�digo
            $tipo_usuario = Tipo do usu�rio que se quer o c�digo da p�gina
   Saida: $resultado['cod_pagina'] = c�digo da p�gina de nome $nome_pagina
*/
function RetornaCodPagina($sock,$nome_pagina,$cod_ferramenta,$cod_lingua,$tipo_usuario)
{
  $query="select cod_pagina from Ajuda where nome_pagina='".$nome_pagina."' and cod_ferramenta=".$cod_ferramenta." and cod_lingua=".$cod_lingua." and tipo_usuario='".$tipo_usuario."'\n";
  $res=Enviar($sock,$query);
  $resultado=RetornaLinha($res);
  $cod_pagina=$resultado['cod_pagina'];
  if ($cod_pagina=='')
    return (-1);
  else
    return($cod_pagina);
}

/* **********************************************************************
   RetornaNomePagina - Retorna o nome da p�gina da Ajuda
   Entrada: $sock = Socket da conex�o atual
            $cod_pagina = Nome da p�gina que se quer o c�digo
            $cod_ferramenta = C�digo da ferramenta cuja p�gina se quer o c�digo
            $cod_lingua =  C�digo da l�ngua cuja p�gina se quer o c�digo
            $tipo_usuario = Tipo do usu�rio que se quer o c�digo da p�gina
   Saida: $resultado['nome_pagina'] = nome da p�gina de nome $nome_pagina
*/
function RetornaNomePagina($sock,$cod_pagina,$cod_ferramenta,$cod_lingua,$tipo_usuario)
{
  $query="select nome_pagina from Ajuda where cod_pagina=".$cod_pagina." and cod_ferramenta=".$cod_ferramenta." and cod_lingua=".$cod_lingua." and tipo_usuario='".$tipo_usuario."'\n";
  $res=Enviar($sock,$query);
  $resultado=RetornaLinha($res);
  $nome_pagina=$resultado['nome_pagina'];
  return($nome_pagina);
}

/* **********************************************************************
   RetornaPaginasdaFerramenta - Retorna as p�ginas de uma determinada ferramenta
   Entrada: $sock = Socket da conex�o atual
            $cod_ferramenta = C�digo da ferramenta da qual se quer as p�ginas
            $cod_lingua =  C�digo da l�ngua na qual se quer as p�ginas
            $tipo_usuario = Tipo do usu�rio do qual se quer as p�ginas
   Saida: $lista = Lista com nomes das p�ginas
*/
function RetornaPaginasdaFerramenta($sock,$cod_ferramenta,$cod_lingua,$tipo_usuario)
{
  $query="select nome_pagina,cod_pagina from Ajuda where cod_ferramenta=".$cod_ferramenta." and cod_lingua=".$cod_lingua." and tipo_usuario='".$tipo_usuario."' order by cod_pagina\n";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}

/* **********************************************************************
   MaxCodPagina - Retorna o pr�ximo c�digo de p�gina que vir� a ser criado
   Entrada: $sock = Socket da conex�o atual
            $cod_ferramenta = C�digo da ferramenta da qual se quer o c�digo
            $cod_lingua =  C�digo da l�ngua na qual se quer o c�digo
            $tipo_usuario = Tipo do usu�rio do qual se quer o c�digo
   Saida: $cod_pagina = O maior c�digo de p�gina existente + 1
*/
function MaxCodPagina ($sock,$cod_ferramenta,$cod_lingua,$tipo_usuario)
{
  $query="select max(cod_pagina) from Ajuda where cod_ferramenta=".$cod_ferramenta." and cod_lingua=".$cod_lingua." and tipo_usuario='".$tipo_usuario."'\n";
  $res=Enviar($sock,$query);
  $resultado=RetornaLinha($res);
  $cod_pagina=$resultado['max(cod_pagina)'] + 1;
  return($cod_pagina);
}

/* **********************************************************************
   AtualizaCodPagina - Atualiza o c�digo da p�gina especificada
   Entrada: $sock = Socket da conex�o atual
            $cod_ferramenta = C�digo da ferramenta da qual se quer atualizar o c�digo de p�gina
            $cod_pagina_atual =  C�digo atual da p�gina
            $cod_pagina_novo =  C�digo novo da p�gina
            $tipo_usuario = Tipo do usu�rio do qual se quer atualizar o c�digo
   Saida: Atualiza o c�digo de p�gina especificado
*/
function AtualizaCodPagina($sock,$cod_ferramenta,$cod_pagina_atual,$cod_pagina_novo,$tipo_usuario)
{
  $query="update Ajuda set cod_pagina=".$cod_pagina_novo." where cod_ferramenta=".$cod_ferramenta." and cod_pagina=".$cod_pagina_atual." and tipo_usuario='".$tipo_usuario."'\n";
  Enviar($sock,$query);
}
?>