<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/ajuda/ajuda.inc

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
  ARQUIVO : cursos/aplic/ajuda/ajuda.inc
  ========================================================== */

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

/* *********************************************************************
   RetornaListaFerramentas - Retorna a lista de ferramentas
   Entrada: $sock - BASE EXTERNA
   Saida: array [$cod_ferramenta]['cod_texto_nome']
                [$cod_ferramenta]['cod_texto_descricao']
                [$cod_ferramenta]['diretorio']
*/
function RetornaListaFerramentas($sock)
{
  $query ="select * from Ferramentas order by cod_ferramenta";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  foreach($lista as $cod=>$linha)
  {
    $lista_retorno[$linha['cod_ferramenta']]['cod_texto_nome']=$linha['cod_texto_nome'];
    $lista_retorno[$linha['cod_ferramenta']]['cod_texto_descricao']=$linha['cod_texto_descricao'];
    $lista_retorno[$linha['cod_ferramenta']]['diretorio']=$linha['diretorio'];
  }
  return ($lista_retorno);
}

/* **********************************************************************
   RetornaFerramentasOrdemMenu - Retorna o codigo e nome das ferramentas
                                 na ordem do menu
   Entrada: $sock - BASE EXTERNA
   Saida: array[$cod_ferramenta]=nome
*/
function RetornaFerramentasOrdemMenu($sock)
{
//   global $cod_lingua_s;
  $query ="select F.cod_ferramenta 'cod_ferramenta', L.texto 'texto', M.posicao 'posicao'";
  $query.=" from Ferramentas F, Menu M, Lingua_textos L where L.cod_lingua=".$_SESSION['cod_lingua_s'];
  $query.=" and L.cod_ferramenta=-4 and L.cod_texto=F.cod_texto_nome and F.cod_ferramenta=M.cod_ferramenta";
  $query.=" order by M.posicao";
  $res=Enviar($sock,$query);

  while ($linha=RetornaLinha($res))
    $saida[$linha['cod_ferramenta']]=$linha['texto'];
  return $saida;
}

/* **********************************************************************
   RetornaRetornaPaginasFerramenta - Retorna um array com todos c�digos de p�ginas de uma ferramenta
   Entrada: $sock - BASE EXTERNA
            $cod_ferramenta - c�digo da ferramenta em que se quer as p�ginas
            $cod_lingua - c�gigo da l�ngua
            $tipo_usuario - tipo de usu�rio em que se quer as p�ginas
   Saida: $paginas = array com todos c�digos de p�ginas da ferramenta
*/
function RetornaPaginasFerramenta($sock,$cod_ferramenta,$cod_lingua,$tipo_usuario)
{
  $query="select cod_pagina from Ajuda where cod_ferramenta=".$cod_ferramenta." and cod_lingua=".$cod_lingua." and tipo_usuario='".$tipo_usuario."'";
  $res=Enviar($sock,$query);
  $count=1;
  while($linha=RetornaLinha($res))
  {
    $paginas[$count]=$linha['cod_pagina'];
    $count++;
  }
  return $paginas;
}
?>
