<?php

require_once 'adodb-time.inc.php';

$_SESSION['cod_lingua_s'] = 1;

if ($_SESSION['cod_lingua_s'] == 3)
    $data_invertida_g = true;
else
    $data_invertida_g = false;

/**
 * Classe Data do modulo geral
 * @author     TelEduc
 * @copyright  20014 TelEduc
 * @license    http://teleduc.org.br/
 */
class Data {
    /*     * ***********************************************************************
      Data2UnixTime - Converte string para UnixTime
      Entrada: $data - string no formato dd/mm/yyyy ou mm/dd/yyyy
      Saï¿½da: Unixtime da string
     */

    /**
     * Data2UnixTime - Converte string para UnixTime
     * 
     * @param String $data String no formato dd/mm/yyyy ou mm/dd/yyyy
     * @return String Unixtime da string
     */
    static function Data2UnixTime($data) {
        global $data_invertida_g;

        $data = explode("/", $data);
        if ($data_invertida_g) {
            return (adodb_mktime(0, 0, 0, $data[0], $data[1], $data[2]));
        } else {
            return (adodb_mktime(0, 0, 0, $data[1], $data[0], $data[2]));
        }
    }

    /**
     * Mascara para data no formato invertido, usado no mysql. 
     * 
     * @param string $data String no formato dd/mm/yyyy 
     * @return string Data no formato yyyy-mm-dd
     */
    static function Data2MySql($data) {

        $data = explode("/", $data);
        return $data[2] . "-" . $data[1] . "-" . $data[0];
    }

    /**
     * Converte inteiro UnixTime para data e hora
     * 
     * @param String $timestamp  Unixtime a ser convertido
     * @return string dd/mm/yyyy HH:MM:SS ou mm/dd/yyyy HH:MM:SS
     */
    static function UnixTime2DataHora($timestamp) {
        global $data_invertida_g;

        $temp = adodb_getdate($timestamp);
        $temp = Data::CorrigeZeros($temp);
        if ($data_invertida_g)
            $data = $temp['mon'] . "/" . $temp['mday'] . "/" . $temp['year'] . " " . $temp['hours'] . ":" . $temp['minutes'] . ":" . $temp['seconds'];
        else
            $data = $temp['mday'] . "/" . $temp['mon'] . "/" . $temp['year'] . " " . $temp['hours'] . ":" . $temp['minutes'] . ":" . $temp['seconds'];
        return ($data);
    }

    /**
     * Caso a data esteja com zeros faltantes, como por exemplo em 2/2/2002, serão adicionados 0 para ficar no formato 02/02/2002 
     * 
     * @param Array $data Datas separadas por indexadores, como em $data['dia']=5, $data['mes']=5 
     * @return array $data Datas separadas por indexadores, como em $data['dia']=5, $data['mes']=5 
     */
    static function CorrigeZeros($data) {
        foreach ($data as $cod => $temp)
            if (strlen($data[$cod]) < 2)
                $data[$cod] = "0" . $data[$cod];
        return ($data);
    }

    /*     * ***********************************************************************
      UnixTime2Data - Converte inteiro UnixTime para data
      Entrada: $timestamp - Unixtime a ser convertido
      Saï¿½da: string no formato: dd/mm/yyyy ou mm/dd/yyyy
     */

    /**
     * Converte inteiro UnixTime para data
     * 
     * @param String $timestamp Unixtime a ser convertido
     * @return type no formato: dd/mm/yyyy ou mm/dd/yyyy
     */
    static function UnixTime2Data($timestamp) {
        global $data_invertida_g;

        $temp = adodb_getdate($timestamp);
        $temp = Data::CorrigeZeros($temp);
        if ($data_invertida_g)
            $data = $temp['mon'] . "/" . $temp['mday'] . "/" . $temp['year'];
        else
            $data = $temp['mday'] . "/" . $temp['mon'] . "/" . $temp['year'];
        return ($data);
    }

    /*     * ***********************************************************************
      UnixTime2Hora - Converte inteiro UnixTime para hora
      Entrada: $timestamp - Unixtime a ser convertido
      Saida: string no formato: HH:MM:SS
     */
    
    /**
     * Converte inteiro UnixTime para hora
     * 
     * @param String $timestamp Unixtime a ser convertido
     * @return String No formato: HH:MM:SS
     */
    static function UnixTime2Hora($timestamp) {
        $temp = adodb_getdate($timestamp);
        $temp = Data::CorrigeZeros($temp);
        $data = $temp['hours'] . ":" . $temp['minutes'] . ":" . $temp['seconds'];
        return ($data);
    }

    /*     * ***********************************************************************
      UnixTime2DataMesAbreviado - Converte inteiro UnixTime para data
      Entrada: $timestamp - Unixtime a ser convertido
      Saida: string no formato: dd/mes abrev./yyyy ou mes abrev./dd/yyyy
     */
    
    /**
     * Converte inteiro UnixTime para data
     * 
     * @global int $data_invertida_g 
     * @param String $timestamp  Unixtime a ser convertido
     * @return String No formato: dd/mes abrev./yyyy ou mes abrev./dd/yyyy
     */
    static function UnixTime2DataMesAbreviado($timestamp) {
        global $data_invertida_g;

        $temp = adodb_getdate($timestamp);
        $temp = Data::CorrigeZeros($temp);
        if ($data_invertida_g)
            $data = Data::NomeMesAbreviado($temp['mon']) . "/" . $temp['mday'] . "/" . $temp['year'];
        else
            $data = $temp['mday'] . "/" . Data::NomeMesAbreviado($temp['mon']) . "/" . $temp['year'];
        return ($data);
    }

    /*     * ***********************************************************************
      NomeMesAbreviado - Retorna o nome do mes abreviado
      Entrada: $nummes - Numero do mes
      Saida: nome do mes abreviado
     */

    /**
     * Retorna o nome do mes abreviado
     * 
     * @param int $nummes Numero do mes
     * @return String  Nome do mes abreviado
     */
    static function NomeMesAbreviado($nummes) {
        /* 7 - Mes invalido,Jan,Fev,Mar,Abr,Mai,Jun,Jul,Ago,Set,Out,Nov,Dez */
        return Data::TratamentoArrayDataGenerico($nummes, _("INVALID_MONTH_-2"));
    }

    /*     * ***********************************************************************
      TratamentoArrayDataGenerico - Retorna o elemento $num do array $numarray
      do tabela -2 de Linguas, ou o elemento 0 se
      $num < 1 ou $num > $limite
      OBS.: ROTINA DE USO INTERNO DE DATA.INC
      Entrada: $num - Numero do elemento a ser retornado
      $numarray - Numero do array a ser usado
      Saida: elemento requerido, ou elemento 0 se fora do limite
     */

    /**
     * Retorna o elemento $num do array $numarray
     * do tabela -2 de Linguas, ou o elemento 0 se
     * $num < 1 ou $num > $limite
     * OBS.: ROTINA DE USO INTERNO DE DATA.INC
     * 
     * @param int $num Numero do elemento a ser retornado
     * @param int $numarray Numero do array a ser usado
     * @return String Elemento requerido, ou elemento 0 se fora do limite
     */
    static function TratamentoArrayDataGenerico($num, $numarray) {
        $Array = $numarray;
        
        $elems = explode(",", $Array);
        if ($num >= 0 && $num < count($elems))
            return $elems[$num + 0];
        else
            return $elems[0];
    }

}

?>
