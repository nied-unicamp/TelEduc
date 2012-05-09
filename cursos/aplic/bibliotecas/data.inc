<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : bibliotecas/data.inc

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
  ARQUIVO : bibliotecas/data.inc
  ========================================================== */

/*
  biblioteca de fun��es de consers�o de data UnixTime para formatos em string.
  USA VARIAVEL GLOBAL $data_invertida_g
    TRUE - usar data no formato mm/dd/aaaa
    FALSE - usar data no formato dd/mm/aaaa
  Modificacoes por: Leonel Aguilar Gayard
*/

/*
 adodb-time.inc.php
Biblioteca com as funcoes mktime, date e outras relacionadas a data com suporte 
a anos com uma escala maior. Ela usa floats de 64-bits para o calculo dos anos.
*/
include("adodb-time.inc.php");

if ($_SESSION['cod_lingua_s']==3)
  $data_invertida_g=true;
else
  $data_invertida_g=false;

function CorrigeZeros($data)
{
  foreach ($data as $cod => $temp)
    if (strlen($data[$cod])<2)
      $data[$cod]="0".$data[$cod];
  return ($data);
}

/* ************************************************************************
   TratamentoArrayDataGenerico - Retorna o elemento $num do array $numarray
                                 do tabela -2 de Linguas, ou o elemento 0 se 
                                 $num < 1 ou $num > $limite
   OBS.: ROTINA DE USO INTERNO DE DATA.INC 
   Entrada: $num - N�mero do elemento a ser retornado
            $numarray - N�mero do array a ser usado
   Sa�da: elemento requerido, ou elemento 0 se fora do limite            
*/
function TratamentoArrayDataGenerico($num,$numarray)
{
  /* A lista estara em memoria, entao $sock nao eh necessario */
  $lista_frases=RetornaListaDeFrases(0,-2);

  $Array=RetornaFraseDaLista($lista_frases,$numarray);
  $elems=explode(",",$Array);
  if ($num>=0 && $num<count($elems))
    return $elems[$num + 0];
  else
    return $elems[0];  
}

/* ************************************************************************
   NomeMes - Retorna o nome do m�s por extenso
   Entrada: $nummes - N�mero do m�s
   Sa�da: nome do m�s por extenso
*/
function NomeMes($nummes)
{
  /* 6 - M�s inv�lido,Janeiro,Fevereiro,Mar�o,Abril,Maio,Junho,Julho,Agosto,Setembro,Outubro,Novembro,Dezembro */
  return TratamentoArrayDataGenerico($nummes,6);
}

/* ************************************************************************
   NomeMesAbreviado - Retorna o nome do m�s abreviado
   Entrada: $nummes - N�mero do m�s
   Sa�da: nome do m�s abreviado
*/
function NomeMesAbreviado($nummes)
{
  /* 7 - M�s inv�lido,Jan,Fev,Mar,Abr,Mai,Jun,Jul,Ago,Set,Out,Nov,Dez */
  return TratamentoArrayDataGenerico($nummes,7);
}

/* ************************************************************************
   NomeDiaSemana - Retorna o nome do dia da semanapor extenso
   Entrada: $numdiasemana - N�mero do dia
   Sa�da: nome do dia por extenso
*/
function NomeDiaSemana($numdiasemana)
{
  /* 8 - Dia inv�lido,Domingo,Segunda,Ter�a,Quarta,Quinta,Sexta,S�bado */
  return TratamentoArrayDataGenerico($numdiasemana,8);
}

/* ************************************************************************
   NomeDiaSemanaAbreviado - Retorna o nome do dia da semana abreviado
   Entrada: $numdiasemana - N�mero do dia
   Sa�da: nome do dia abreviado
*/
function NomeDiaSemanaAbreviado($numdiasemana)
{
  /* 9 - Dia inv�lido,Dom,Seg,Ter,Qua,Qui,Sex,S�b */
  return TratamentoArrayDataGenerico($numdiasemana,9);
}

/* ************************************************************************
   UnixTime2Data - Converte inteiro UnixTime para data
   Entrada: $timestamp - Unixtime a ser convertido
   Sa�da: string no formato: dd/mm/yyyy ou mm/dd/yyyy
*/
function UnixTime2Data($timestamp)
{
  global $data_invertida_g;
  
  $temp=adodb_getdate($timestamp);
  $temp=CorrigeZeros($temp);
  if ($data_invertida_g)
    $data=$temp['mon']."/".$temp['mday']."/".$temp['year'];
  else
    $data=$temp['mday']."/".$temp['mon']."/".$temp['year'];
  return ($data);
}

/* ************************************************************************
   UnixTime2DataMesExtenso - Converte inteiro UnixTime para data
   Entrada: $timestamp - Unixtime a ser convertido
   Sa�da: string no formato: dd/m�s por extenso/yyyy ou m�s por extenso/dd/yyyy
*/
function UnixTime2DataMesExtenso($timestamp)
{
  global $data_invertida_g;

  $temp=adodb_getdate($timestamp);
  $temp=CorrigeZeros($temp);
  if ($data_invertida_g)
    $data=NomeMes($temp['mon'])."/".$temp['mday']."/".$temp['year'];
  else
    $data=$temp['mday']."/".NomeMes($temp['mon'])."/".$temp['year'];
  return ($data);
}

/* ************************************************************************
   UnixTime2DataMesAbreviado - Converte inteiro UnixTime para data
   Entrada: $timestamp - Unixtime a ser convertido
   Sa�da: string no formato: dd/m�s abrev./yyyy ou m�s abrev./dd/yyyy
*/
function UnixTime2DataMesAbreviado($timestamp)
{
  global $data_invertida_g;

  $temp=adodb_getdate($timestamp);
  $temp=CorrigeZeros($temp);
  if ($data_invertida_g)
    $data=NomeMesAbreviado($temp['mon'])."/".$temp['mday']."/".$temp['year'];
  else
    $data=$temp['mday']."/".NomeMesAbreviado($temp['mon'])."/".$temp['year'];
  return ($data);
}

/* ************************************************************************
   UnixTime2DataHora - Converte inteiro UnixTime para data e hora
   Entrada: $timestamp - Unixtime a ser convertido
   Sa�da: string no formato: dd/mm/yyyy HH:MM:SS ou mm/dd/yyyy HH:MM:SS
*/
function UnixTime2DataHora($timestamp)
{
  global $data_invertida_g;

  $temp=adodb_getdate($timestamp);
  $temp=CorrigeZeros($temp);
  if ($data_invertida_g)
    $data=$temp['mon']."/".$temp['mday']."/".$temp['year']." ".$temp['hours'].":".$temp['minutes'].":".$temp['seconds'];
  else
    $data=$temp['mday']."/".$temp['mon']."/".$temp['year']." ".$temp['hours'].":".$temp['minutes'].":".$temp['seconds'];
  return ($data);
}

/* ************************************************************************
   UnixTime2DataHoraMesExtenso - Converte inteiro UnixTime para data hora
   Entrada: $timestamp - Unixtime a ser convertido
   Sa�da: string no formato: dd/m�s por extenso/yyyy HH:MM:SS ou M�s por extenso/dd/yyyy HH:MM:SS
*/
function UnixTime2DataHoraMesExtenso($timestamp)
{
  global $data_invertida_g;

  $temp=adodb_getdate($timestamp);
  $temp=CorrigeZeros($temp);
  if ($data_invertida_g)
    $data=NomeMes($temp['mon'])."/".$temp['mday']."/".$temp['year']." ".$temp['hours'].":".$temp['minutes'].":".$temp['seconds'];
  else
    $data=$temp['mday']."/".NomeMes($temp['mon'])."/".$temp['year']." ".$temp['hours'].":".$temp['minutes'].":".$temp['seconds'];
  return ($data);
}

/* ************************************************************************
   UnixTime2DataHoraMesAbreviado - Converte inteiro UnixTime para data hora
   Entrada: $timestamp - Unixtime a ser convertido
   Sa�da: string no formato: dd/m�s abrev./yyyy HH:MM:SS ou m�s/abrev./dd/yyyy HH:MM:SS
*/
function UnixTime2DataHoraMesAbreviado($timestamp)
{
  global $data_invertida_g;

  $temp=adodb_getdate($timestamp);
  $temp=CorrigeZeros($temp);
  if ($data_invertida_g)
    $data=NomeMesAbreviado($temp['mon'])."/".$temp['mday']."/".$temp['year']." ".$temp['hours'].":".$temp['minutes'].":".$temp['seconds'];
  else
    $data=$temp['mday']."/".NomeMesAbreviado($temp['mon'])."/".$temp['year']." ".$temp['hours'].":".$temp['minutes'].":".$temp['seconds'];
  return ($data);
}

/* ************************************************************************
   UnixTime2Hora - Converte inteiro UnixTime para hora
   Entrada: $timestamp - Unixtime a ser convertido
   Sa�da: string no formato: HH:MM:SS
*/
function UnixTime2Hora($timestamp)
{
  $temp=adodb_getdate($timestamp);
  $temp=CorrigeZeros($temp);
  $data=$temp['hours'].":".$temp['minutes'].":".$temp['seconds'];
  return ($data);
}

/* ************************************************************************
   DataHora2UnixTime - Converte string para UnixTime
   Entrada: $datahora - string no formato dd/mm/yyyy hh:mm ou mm/dd/yyyy hh:mm
   Sa�da: Unixtime da string
*/
function DataHora2UnixTime($datahora)
{
  global $data_invertida_g;

  $partes=explode(" ",$datahora);
  $data=explode("/",$partes[0]);
  $hora=explode(":",$partes[1]);
  if ($data_invertida_g)
    //                    Hora     Minuto   Segundo  Mes      Dia      Ano
    return(adodb_mktime($hora[0],$hora[1],$hora[2],$data[0],$data[1],$data[2]));
  else
    //                     Hora     Minuto   Segundo  Mes      Dia      Ano
    return(adodb_mktime($hora[0],$hora[1],$hora[2],$data[1],$data[0],$data[2]));
}

/* ************************************************************************
   Data2UnixTime - Converte string para UnixTime
   Entrada: $data - string no formato dd/mm/yyyy ou mm/dd/yyyy
   Sa�da: Unixtime da string
*/
function Data2UnixTime($data)
{
  global $data_invertida_g;

  $data=explode("/",$data);
  if ($data_invertida_g){
  	return (adodb_mktime(0,0,0,$data[0],$data[1],$data[2]));
   }
  else{
	return (adodb_mktime(0,0,0,$data[1],$data[0],$data[2]));
    }
}

/* ************************************************************************
   UnixTime2DiaSemana - Converte inteiro UnixTime para Dia da semana 
   Entrada: $timestamp - Unixtime a ser convertido
   Sa�da: valor de 1 a 7 (Domingo a sabado) 
*/
function UnixTime2DiaSemana($timestamp)
{
  $dia=adodb_date("w",$timestamp);
  return ($dia+1);
}

/* ************************************************************************
   DiaMesAno2UnixTime - Converte dia, mes e ano para UnixTime
   Entrada: $dia Dia
            $mes Mes
            $ano Ano
   Sa�da: Unixtime
*/
function DiaMesAno2UnixTime($dia,$mes,$ano)
{
   return(adodb_mktime(0,0,0,$mes,$dia,$ano));
}

/* ************************************************************************
   Unixtime2Dia - retorna o dia do unixtime 
   Entrada: $data - unixtime
   Sa�da: dia 
*/
function Unixtime2Dia($data)
{
  $temp=adodb_getdate($data);
  $temp=CorrigeZeros($temp);
  return $temp['mday'];
}

/* ************************************************************************
   Unixtime2Mes - retorna o mes do unixtime
   Entrada: $data - unixtime
   Sa�da: mes 
*/
function Unixtime2Mes($data)
{
  $temp=adodb_getdate($data);
  $temp=CorrigeZeros($temp);
  return $temp['mon'];
}

/* ************************************************************************
   Unixtime2Ano - retorna o dia do unixtime
   Entrada: $data - unixtime
   Sa�da: dia
*/
function Unixtime2Ano($data)
{
  $temp=adodb_getdate($data);
  $temp=CorrigeZeros($temp);
  return $temp['year'];
}


?>
