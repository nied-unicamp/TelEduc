<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : bibliotecas/linguas.inc

    TelEduc - Ambiente de Ensino-Aprendizagem a Distï¿½cia
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

    Nied - Ncleo de Informï¿½ica Aplicada ï¿½Educaï¿½o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitï¿½ia "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : bibliotecas/linguas.inc
  ========================================================== */

/* ************************************************************************
   RetornaFrase - Retorna a frase pedida da lingua atual
   Entrada: $sock - Sock da base geral aberta
            $cod_texto - cod_texto a ser empregado.
            $cod_ferramenta - cod_ferramenta a ser empregada
   Saida: Texto na lingua pedida
*/
function RetornaFrase($sock, $cod_texto, $cod_ferramenta)
{
//   Global $cod_lingua_s,$lista_frases_s;

   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];
  
  $cod_lingua=$_SESSION['cod_lingua_s'];

  if (!isset($_SESSION['lista_frases_s'][$cod_ferramenta]))
  {
    $query  = "SELECT texto FROM ".$dbnamebase.".Lingua_textos WHERE ";
    $query .= "cod_texto=".VerificaNumeroQuery($cod_texto)." AND ";
    $query .= "cod_lingua=".VerificaNumeroQuery($cod_lingua)." AND ";
    $query .= "cod_ferramenta=".VerificaNumeroQuery($cod_ferramenta);

    if($id_query = Enviar($sock, $query))
    {
      $linha = RetornaLinha($id_query);
      $texto = $linha[0];
      if (! isset($texto))
        $texto = "(ERRO : Texto correspondente a essa l&iacute;ngua n&atilde;o cadastrado: ".$cod_texto.")";
    }
    else
    {
      // Erro na execuï¿½o da clï¿½sula : terminar o programa
      $msg  = "</script></table></div></table></div></table></div>";
      $msg .= "<h3>ERRO : N&atilde;o foi poss&iacute;vel executar a cl&aacute;usula SQL</h3>";
      $msg .= "<br><strong>SQL : </strong><br>".$query;
      $msg .= "<br><strong>Mensagem de erro do MySql : </strong><br>".mysql_error();
      $msg .= "<br><a href=\"#\" onclick=\"history.go(-1);\">Voltar</a>";
      die($msg);
    }
  }
  else
  {
    $texto=$_SESSION['lista_frases_s'][$cod_ferramenta][$cod_texto];
  }

  return(ConverteAspas2Html($texto));
}

/* ************************************************************************
   RetornaListaDeFrases - Retorna uma lista com todas as frases da 
                          ferramenta escolhida. usada em conjunto com
                          RetornaFraseDaLista
   Entrada: $sock - Sock da base geral aberta
            $cod_ferramenta - cod_ferramenta a ser empregada  
   Saida: Array com a lista de frases
*/
function RetornaListaDeFrases($sock, $cod_ferramenta)
{
//   Global $cod_lingua_s,$lista_frases_s;

   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $cod_lingua=$_SESSION['cod_lingua_s'];
  
  /* Se ja estiver em memoria, vai direto... */
  if (isset($_SESSION['lista_frases_s'][$cod_ferramenta]) && count($_SESSION['lista_frases_s'][$cod_ferramenta])>0)
    return ($_SESSION['lista_frases_s'][$cod_ferramenta]);
  
  /* Evitar erro caso a variavel $cod_lingua esteja vazia. Valor default = 1 (pt)*/
  if (is_null($cod_lingua))
    $cod_lingua = 1;
  $query  = "SELECT cod_texto, texto FROM ".$dbnamebase.".Lingua_textos WHERE ";
  $query .= "cod_lingua = ".VerificaNumeroQuery($cod_lingua)." AND ";
  $query .= "cod_ferramenta = ".VerificaNumeroQuery($cod_ferramenta)." ";
  $query .= "ORDER BY cod_texto";
  $textos = "";
  if ($id_query = Enviar($sock, $query))
  {
    unset($textos);

    if (RetornaNumLinhas($id_query) > 0)
      while($linha = RetornaLinha($id_query))
        $textos[$linha["cod_texto"]] = $linha["texto"];
  }
  else
  {
    // Erro na execuÃ§Ã£o da clÃ¡usula : terminar o programa
    $msg = "<h3>ERRO : N&atilde;o foi poss&iacute;vel executar a cl&aacute;usula SQL</h3>";
    $msg .= "<br><b>SQL : </b><br>".$query;
    $msg .= "<br><b>Mensagem de erro do MySql : </b><br>".mysql_error();
    $msg .= "<br><a href=\"#\" onclick=\"history.go(-1);\">Voltar</a>";
    die($msg);
  }
  $_SESSION['lista_frases_s'][$cod_ferramenta]=$textos;

  return($textos);
}

/* ************************************************************************
   RetornaFraseDaLista - Retorna a frase pedida da lingua atual
   Entrada: $textos - Array com lista de frases (RetornaListaDeFrases)
            $cod_texto - cod_texto a ser empregado.
   Saida: Texto na lingua pedida
*/
function RetornaFraseDaLista($textos, $cod_texto)
{
  if(ConverteAspas2Html($textos[$cod_texto]) != "")
    return(ConverteAspas2Html($textos[$cod_texto]));
  else{
    switch($_SESSION['cod_lingua_s']){
      case 1://Portuguï¿½s
        return("Falta o texto");
      case 2://Espanhol
        return("Falta texto");
      case 3://Inglï¿½s
        return("Missing Text");
      case 4://Portuguï¿½s PT
        return("Falta o texto");
    }
  }
}

/* ************************************************************************
   ListaDeFrasesEmMemoria - Retorna true se jï¿½estiver me memï¿½ia
   Entrada: $cod_ferramenta - cod_ferramenta a ser empregada  
   Saida: True se jï¿½estiver
*/
function ListaDeFrasesEmMemoria($cod_ferramenta)
{
  return (isset($_SESSION['lista_frases_s'][$cod_ferramenta]));
}

/* ************************************************************************
   ListaLinguas - Retorna a lista de linguas cadastradas
   Entrada: $sock - BASE Externa
   Saida: array [$cod_lingua]=nome
*/
function ListaLinguas($sock)
{
//   Global $cod_lingua_s;

   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query="select cod_texto 'cod_lingua',texto 'lingua' from ".$dbnamebase.".Lingua_textos where cod_ferramenta=-6 and cod_lingua=".$_SESSION['cod_lingua_s']." order by cod_texto";
  $res=Enviar($sock,$query);
  $saida = "";
  unset ($saida);
  while ($linha=RetornaLinha($res))
    if($linha['cod_lingua']!=2){ //retirando temporariamente o espanhol, por falta de tradução
      $saida[$linha['cod_lingua']]=$linha['lingua'];
    }
  return $saida;
}

function LinguaLocal($sock,$cod_lin){
  $dbnamebase = $_SESSION['dbnamebase'];
  $query="select texto from ".$dbnamebase.".Lingua_textos where cod_ferramenta=-6 and cod_lingua=".$cod_lin." and cod_texto=".$cod_lin."";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $texto=$linha['texto'];
  return($texto);

}

/* ************************************************************************
   MemorizaListaDeFrases - Memoriza uma lista com todas as frases da
                          ferramenta escolhida. A partir desse momento, menos 
                          selects necessitarao ser feitos a base
   Entrada: $sock - Sock da base geral aberta
            $cod_ferramenta - cod_ferramenta a ser empregada
   Saida: nenhuma 
*/
function MemorizaListaDeFrases($sock, $cod_ferramenta)
{

  $dbnamebase = $_SESSION['dbnamebase'];

  /* Evitar erro caso a variavel de sessão cod_lingua_s esteja vazia. Valor default = 1 (pt)*/
  if (isset($_SESSION['cod_lingua_s']) && !is_null($_SESSION['cod_lingua_s']))
    $cod_lingua = $_SESSION['cod_lingua_s'];
  else
    $cod_lingua = 1;

  $query  = "SELECT cod_texto, texto FROM ".$dbnamebase.".Lingua_textos WHERE ";
  $query .= "cod_lingua = ".VerificaNumeroQuery($cod_lingua)." AND ";
  $query .= "cod_ferramenta = ".VerificaNumeroQuery($cod_ferramenta)." ";
  $query .= "ORDER BY cod_texto";
  $textos = "";
  
  if ($id_query = Enviar($sock, $query))
  {
    unset($textos);

  if (RetornaNumLinhas($id_query) > 0)
    while($linha = RetornaLinha($id_query))
      $textos[$linha["cod_texto"]] = $linha["texto"];
  }
  else
  {
    // Erro na execuï¿½o da clï¿½sula : terminar o programa
    $msg = "<h3>ERRO : N&atilde;o foi poss&iacute;vel executar a cl&aacute;usula SQL</h3>";
    $msg .= "<br><b>SQL : </b><br>".$query;
    $msg .= "<br><b>Arquivo: </b><br>lingua.inc";
    $msg .= "<br><b>Mensagem de erro do MySql : </b><br>".mysql_error();
    $msg .= "<br><a href=\"#\" onclick=\"history.go(-1);\">Voltar</a>";
    die($msg);
  }
  $_SESSION['lista_frases_s'][$cod_ferramenta]=$textos;
}

/* ************************************************************************
   MudancaDeLingua - Atualiza todas as frases em memoria para a nova lingua selecionada 
   Entrada: $sock - Base Externa 
            $cod_lingua - codigo da lingua a ser atualizada
   Saida: nenhuma
*/
function MudancaDeLingua($sock, $cod_lingua)
{
//   Global $cod_lingua_s, $lista_frases_s;
 
  if ($_SESSION['cod_lingua_s'] != $cod_lingua)
  {
    $_SESSION['cod_lingua_s'] = $cod_lingua;
    foreach($_SESSION['lista_frases_s'] as $cod_fer => $lista)
    {
      MemorizaListaDeFrases($sock,$cod_fer);
    }
  }

}

/* ************************************************************************
   AbreviaturaLingua - Dado o codigo da lingua, retorna a abreviatura da lingua, conforme abaixo (ISO693):
      1 = "pt"
      2 = "es"
      3 = "en"
      4 = "pt"
   Por padrï¿½o, o codigo da lingua eh "pt".
   Entrada: $cod_lingua - codigo da lingua a ser consultada
   Saida: string
*/
function AbreviaturaLingua($cod_lingua){
  $abreviatura = "pt";
  switch ($cod_lingua){
    case '1':
      $abreviatura = "pt";
    break;
    case '2':
      $abreviatura = "es";
    break;
    case '3':
      $abreviatura = "en";
    break;
    case '4':
      $abreviatura = "pt";
    break;
  }
  return $abreviatura;
}


  if (!ListaDeFrasesEmMemoria(-4) || !ListaDeFrasesEmMemoria(-3) || !ListaDeFrasesEmMemoria(-2) || !ListaDeFrasesEmMemoria(-1)
 || !ListaDeFrasesEmMemoria(0))
  {
    $sock=Conectar("");

    if (!ListaDeFrasesEmMemoria(-4))
      MemorizaListaDeFrases($sock,-4);
    if (!ListaDeFrasesEmMemoria(-3))
      MemorizaListaDeFrases($sock,-3);
    if (!ListaDeFrasesEmMemoria(-2))
      MemorizaListaDeFrases($sock,-2);
    if (!ListaDeFrasesEmMemoria(-1))
      MemorizaListaDeFrases($sock,-1);
    if (!ListaDeFrasesEmMemoria(0))
      MemorizaListaDeFrases($sock,0);

    Desconectar($sock);
  }


?>
