<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/ajuda/ajuda.inc

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
  ARQUIVO : administracao/ajuda/ajuda.inc
  ========================================================== */

/* **********************************************************************
   PreparaCabecalhoAjuda - Monta o Cabecalho padrão da página da administracao/ajuda
   Entrada: Nenhuma
   Saida: Monta o cabeçalho no HTML
*/
function PreparaCabecalhoAjuda($lista_frases)
{
  echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
  echo("<html lang=\"pt\">\n");
  echo("<head>\n");
  /* 1- Administração */
  echo("<title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title>\n");
  echo("<meta name=\"robots\" content=\"follow,index\" />\n");
  echo("<meta name=\"description\" content=\"\" />\n");
  echo("<meta name=\"keywords\" content=\"\" />\n");
  echo("<meta name=\"owner\" content=\"\" />\n");
  echo("<meta name=\"copyright\" content=\"\" />\n");
  echo("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n");
  echo("<link href=\"../js-css/ambiente.css\" rel=\"stylesheet\" type=\"text/css\" />\n");
  echo("</head>\n");

  echo("<body text=#000000 bgcolor=#FFFFFF link=#0000FF vlink=#0000FF ALINK=#0000FF>\n");
  echo("<a name=\"topo\"></a>\n");
  echo("<h1><a href=\"index.htm\"><img src=\"../imgs/logo.gif\" border=\"0\" alt=\"TelEduc . Educa&ccedil;&atilde;o a Dist&acirc;ncia\" /></a></h1>\n");
  echo("<table cellpadding=\"0\" cellspacing=\"0\" id=\"container\">\n");
  echo("<tr>\n");
  echo("<td></td>\n");
  echo("<td valign=\"top\" id=\"topo\"><!--NAVEGACAO NIVEL 3-->\n");
  echo("<h3>".RetornaFraseDaLista($lista_frases,2)."</h3>\n");
  echo("</tr>\n");
  echo("<tr>\n");
  echo("<td valign=\"top\"><!--NAVEGACAO PRINCIPAL-->\n");
  echo("<ul id=\"nav\">\n");

  /* 3 - Criação de Curso */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,3),"../index_criar_curso.php","");

  /* 125 - Editar Categorias */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,125),"../editar_categoria.php","");

  /* 131 - Selecionar Categoria dos Cursos */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,131),"../selecionar_categoria.php","");

  /* 4 - Extração de Curso */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,4),"../extracao/extrair_curso.php","");

  /* 141 - Inserção de Cursos Extraídos */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,141),"../insercao/inserir_curso.php","");

  /* 245 - Reutilização de Cursos Encerrados */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,245),"../resetar_curso.php","");

  /* 293 - Reenvio de dados aos coordenadores*/
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,293),"../infocurso/reenvio.php","");

  /* 9 - Enviar e-mail para usuários */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,9),"../enviar_email.php","");

  /* 8 - Trocar login */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,8),"../trocar_login.php","");

  /* 5 - Consulta a Base de Dados */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,5),"../consultar_base.php","");

  /* 153 - Estatísticas do Ambiente */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,153),"../estatistica/index.php","");

  /* 183 - Configurar dados do ambiente */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,183), "../configurar.php", "");

  /* 11 - Cadastro de Línguas */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,11),"../cadastro_linguas.php","");

  /* 171 - Cadastro de texto da Ajuda */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,171),"../ajuda/index.php","");

  /* 13 - Contato - NIED - Unicamp */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,13),"mailto:curso@nied.unicamp.br","");

  echo("</ul>\n");
  echo("<p align=\"center\"><a href=\"#\"><img src=\"../imgs/logoNied.gif\" alt=\"Nied\" width=\"28\" height=\"38\" border=\"0\" style=\"margin-right:8px;\" /></a> <a href=\"#\"><img src=\"../imgs/logoInstComp.gif\" alt=\"Instituto de Computa&ccedil;&atilde;o\" width=\"44\" height=\"38\" border=\"0\" style=\"margin-right:6px;\" /></a> <a href=\"#\"><img src=\"../imgs/logoUnicamp.gif\" alt=\"UNICAMP\" width=\"35\" height=\"38\" border=\"0\" /></a></p>\n");
  echo("</td>\n");
  echo("<td width=\"100%\" valign=\"top\" style=\"padding-bottom:40px; padding-top:22px;\">\n");
}

/* **********************************************************************
   RetornaTextoDaAjuda - Retorna o texto referente aos parâmetros passados na Entrada.
   Entrada: $sock = Socket da conexão atual
            $cod_ferramenta = Código da ferramenta da qual se quer o texto da Ajuda
            $cod_lingua =  Código da língua na qual se quer o texto da Ajuda
            $cod_pagina =  Código da página da ferramenta da qual se quer o texto da Ajuda
            $tipo_usuario = Para saber se o texto será o exibido na visão do aluno ou formador.
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
   CriaCampoTexto - Cria na tabela Ajuda uma nova linha para inserção do texto da Ajuda
   Entrada: $sock = Socket da conexão atual
            $cod_ferramenta = Código da ferramenta da qual se quer o texto da Ajuda
            $cod_pagina = Código da página da Ajuda
            $cod_lingua =  Código da língua na qual se quer o texto da Ajuda
            $nome_pagina =  Nome da página da ferramenta da qual se quer o texto da Ajuda
            $tipo_usuario = Para saber se o texto será o exibido na visão do aluno ou formador.
   Saida: Cria a linha especificada
*/
function CriaCampoTexto($sock,$cod_ferramenta,$cod_pagina,$nome_pagina,$cod_lingua,$tipo_usuario)
{

  $query="insert INTO Ajuda (cod_ferramenta,cod_pagina,cod_lingua,tipo_usuario,texto,nome_pagina) VALUES(".$cod_ferramenta.",".$cod_pagina.",".$cod_lingua.",'".$tipo_usuario."','','".$nome_pagina."')\n";
  Enviar($sock,$query);
}

/* **********************************************************************
   AtualizaTexto - Atualiza o texto da Ajuda
   Entrada: $sock = Socket da conexão atual
            $cod_ferramenta = Código da ferramenta da qual se quer atualizar o texto da Ajuda
            $cod_pagina = Código da página da qual se quer atualizar o texto da Ajuda
            $cod_lingua =  Código da língua na qual se quer o texto da Ajuda
            $tipo_usuario = Para saber se o texto será o exibido na visão do aluno ou formador
            $texto = Texto novo
   Saida: Atualiza texto da Ajuda
*/
function AtualizaTexto($sock,$cod_ferramenta,$cod_pagina,$cod_lingua,$tipo_usuario,$texto)
{
  $query="update Ajuda SET texto='".$texto."' where cod_ferramenta=".$cod_ferramenta." and cod_pagina=".$cod_pagina." and cod_lingua=".$cod_lingua." and tipo_usuario='".$tipo_usuario."'\n";
  Enviar($sock,$query);
}

/* **********************************************************************
   AtualizaNomePagina - Atualiza o nome da página da Ajuda
   Entrada: $sock = Socket da conexão atual
            $cod_pagina = Código da página que se quer atualizar o nome
            $nome_pagina = Novo nome da página
            $cod_ferramenta = Código da ferramenta da qual se quer atualizar o nome da página
            $cod_lingua =  Código da língua na qual se quer atualizar o nome da página
            $tipo_usuario = Tipo do usuário que se quer atualizar o nome da página
   Saida: Atualiza nome da página da Ajuda
*/
function AtualizaNomePagina($sock,$cod_pagina,$nome_pagina,$cod_ferramenta,$cod_lingua,$tipo_usuario)
{
  $query="update Ajuda SET nome_pagina='".$nome_pagina."' where cod_pagina=".$cod_pagina." and cod_ferramenta=".$cod_ferramenta." and cod_lingua=".$cod_lingua." and tipo_usuario='".$tipo_usuario."'\n";
  Enviar($sock,$query);
}

/* **********************************************************************
   RetornaCodPagina - Retorna o código da página da Ajuda
   Entrada: $sock = Socket da conexão atual
            $nome_pagina = Nome da página que se quer o código
            $cod_ferramenta = Código da ferramenta cuja página se quer o código
            $cod_lingua =  Código da língua cuja página se quer o código
            $tipo_usuario = Tipo do usuário que se quer o código da página
   Saida: $resultado['cod_pagina'] = código da página de nome $nome_pagina
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
   RetornaNomePagina - Retorna o nome da página da Ajuda
   Entrada: $sock = Socket da conexão atual
            $cod_pagina = Nome da página que se quer o código
            $cod_ferramenta = Código da ferramenta cuja página se quer o código
            $cod_lingua =  Código da língua cuja página se quer o código
            $tipo_usuario = Tipo do usuário que se quer o código da página
   Saida: $resultado['nome_pagina'] = nome da página de nome $nome_pagina
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
   RetornaPaginasdaFerramenta - Retorna as páginas de uma determinada ferramenta
   Entrada: $sock = Socket da conexão atual
            $cod_ferramenta = Código da ferramenta da qual se quer as páginas
            $cod_lingua =  Código da língua na qual se quer as páginas
            $tipo_usuario = Tipo do usuário do qual se quer as páginas
   Saida: $lista = Lista com nomes das páginas
*/
function RetornaPaginasdaFerramenta($sock,$cod_ferramenta,$cod_lingua,$tipo_usuario)
{
  $query="select nome_pagina,cod_pagina from Ajuda where cod_ferramenta=".$cod_ferramenta." and cod_lingua=".$cod_lingua." and tipo_usuario='".$tipo_usuario."' order by cod_pagina\n";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}

/* **********************************************************************
   MaxCodPagina - Retorna o próximo código de página que virá a ser criado
   Entrada: $sock = Socket da conexão atual
            $cod_ferramenta = Código da ferramenta da qual se quer o código
            $cod_lingua =  Código da língua na qual se quer o código
            $tipo_usuario = Tipo do usuário do qual se quer o código
   Saida: $cod_pagina = O maior código de página existente + 1
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
   AtualizaCodPagina - Atualiza o código da página especificada
   Entrada: $sock = Socket da conexão atual
            $cod_ferramenta = Código da ferramenta da qual se quer atualizar o código de página
            $cod_pagina_atual =  Código atual da página
            $cod_pagina_novo =  Código novo da página
            $tipo_usuario = Tipo do usuário do qual se quer atualizar o código
   Saida: Atualiza o código de página especificado
*/
function AtualizaCodPagina($sock,$cod_ferramenta,$cod_pagina_atual,$cod_pagina_novo,$tipo_usuario)
{
  $query="update Ajuda set cod_pagina=".$cod_pagina_novo." where cod_ferramenta=".$cod_ferramenta." and cod_pagina=".$cod_pagina_atual." and tipo_usuario='".$tipo_usuario."'\n";
  Enviar($sock,$query);
}
?>