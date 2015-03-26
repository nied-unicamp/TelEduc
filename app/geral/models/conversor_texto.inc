<?php

/**
 * Classe ConversorTexto
 * @author     TelEduc
 * @copyright  2014 TelEduc
 * @license    http://teleduc.org.br/
 */
class ConversorTexto {
    /*     * ********************************************************************
      Enter2BR - Converte Enter para <br>
      Entrada: $linha - string a ser corrigida
      Saida: string corrigida
     */

    /**
     * Converte Enter para <br>
     * 
     * @param String $linha String a ser alterada 
     * @return string Retorna uma string ja modificada.
     */
    static function Enter2BR($linha) {
        return(implode("<br>", explode("\n", $linha)));
    }

    /*     * ********************************************************************
      BR2Enter - Converte <br> para enter
      Entrada: $linha - string a ser corrigida
      Saida: string corrigida
     */

    /**
     * Converte <br> para enter
     * 
     * @param String $linha String a ser alterada 
     * @return string Retorna uma string ja modificada.
     */
    static function BR2Enter($linha) {
        return(implode("\n", explode("<br>", $linha)));
    }

    /*     * ********************************************************************
      Space2Nbsp - Converte " " para "&nbsp;"
      Entrada: $linha - string a ser corrigida
      Saida: string corrigida
     */

    /**
     * Converte " " para "&nbsp;"
     * 
     * @param String $linha String a ser alterada 
     * @return string Retorna uma string ja modificada.
     */
    static function Space2Nbsp($linha) {
        return(implode("&nbsp;", explode(" ", $linha)));
    }

    /*     * ********************************************************************
      AjustaParagrafo - Converte "  " para "&nbsp;&nbsp;"
      Entrada: $linha - string a ser corrigida
      Saida: string corrigida
     */

    /**
     * AjustaParagrafo - Converte "  " para "&nbsp;&nbsp;"
     * 
     * @param String $linha String a ser alterada 
     * @return string Retorna uma string ja modificada.
     */
    static function AjustaParagrafo($linha) {
        return(implode("&nbsp;&nbsp;", explode("  ", $linha)));
    }

    /*     * ********************************************************************
      Nbsp2Space - Converte "&nbsp;" para " "
      Entrada: $linha - string a ser corrigida
      Saida: string corrigida
     */

    /**
     * Nbsp2Space - Converte "&nbsp;" para " "
     * 
     * @param String $linha String a ser alterada 
     * @return string Retorna uma string ja modificada.
     */
    static function Nbsp2space($linha) {
        return(implode(" ", explode("&nbsp;", $linha)));
    }

    /*     * ********************************************************************
      EliminaScript - Elimina a tag <script>
      Entrada: $linha - string a ser corrigida
      Saida: string corrigida
     */

    /**
     *  EliminaScript - Elimina a tag <script>
     * 
     * @param String $linha String a ser alterada 
     * @return string Retorna uma string ja modificada.
     */
    static function EliminaScript($linha) {
        $linha = ConversorTexto::VerificaTagTable($linha);
        return eregi_replace("<script", "&lt;script", $linha);
    }

    /*     * ********************************************************************
      ConverteAspas2BarraAspas - troca ' e " por \' e \"
      Entrada: $linha - string a ser corrigida
      Saida: string corrigida
     */

    /**
     * ConverteAspas2BarraAspas - troca ' e " por \' e \"
     * 
     * @param String $linha String a ser alterada 
     * @return string Retorna uma string ja modificada.
     */
    static function ConverteAspas2BarraAspas($linha) {
        $linha1 = implode("\\'", explode("'", $linha));
        return implode('\\"', explode('"', $linha1));
    }

    /*     * ********************************************************************
      ConverteBarraAspas2Aspas - troca \' e \" por ' e "
      Entrada: $linha - string a ser corrigida
      Saida: string corrigida
     */

    /**
     * ConverteBarraAspas2Aspas - troca \' e \" por ' e "
     * 
     * @param String $linha String a ser alterada 
     * @return string Retorna uma string ja modificada.
     */
    static function ConverteBarraAspas2Aspas($linha) {
        $linha1 = implode("'", explode("\\'", $linha));
        return implode('"', explode('\\"', $linha1));
    }

    /*     * ********************************************************************
      ConverteAspas2Html - troca ' e " por &#39; e &#34;
      Entrada: $linha - string a ser corrigida
      Saida: string corrigida
     */

    /**
     * ConverteAspas2Html - troca ' e " por &#39; e &#34;
     * 
     * @param String $linha String a ser alterada 
     * @return string Retorna uma string ja modificada.
     */
    static function ConverteAspas2Html($linha) {
        $linha1 = eregi_replace("'", "&#39;", $linha);
        return eregi_replace('"', "&#34;", $linha1);
    }

    /*     * ********************************************************************
      ConverteHtml2Aspas- troca &#39 e &#34 por ' e ";
      Entrada: $linha - string a ser corrigida
      Saida: string corrigida
     */

    /**
     * ConverteHtml2Aspas- troca &#39 e &#34 por ' e ";
     * 
     * @param String $linha String a ser alterada 
     * @return string Retorna uma string ja modificada
     */
    static function ConverteHtml2Aspas($linha) {
        $linha1 = eregi_replace("&#39;", "'", $linha);
        return eregi_replace("&#34;", '"', $linha1);
    }

    /*     * ********************************************************************
      ConverteSpace2Mais- troca " " (espaÃ§o) por +
      Entrada: $linha - string a ser corrigida
      Saida: string corrigida
     */

    /**
     * ConverteSpace2Mais- troca " " (espaco) por +
     * 
     * @param String $linha String a ser alterada 
     * @return string Retorna uma string ja modificada
     */
    static function ConverteSpace2Mais($linha) {
        return eregi_replace(' ', '+', $linha);
    }

    /*     * ********************************************************************
      ConverteSpace2Html- troca " " (espaÃ§o) por %20
      Entrada: $linha - string a ser corrigida
      Saida: string corrigida
     */

    /**
     * ConverteSpace2Html- troca " " (espaco) por %20
     * 
     * @param String $linha String a ser alterada 
     * @return string Retorna uma string ja modificada
     */
    static function ConverteSpace2Html($linha) {
        return eregi_replace(' ', '%20', $linha);
    }

    /*     * ********************************************************************
      VerificaTagTable - Verifica se a um /table para cada table. Se nÃ£o tiver
      completa com o que faltar
      Entrada: $linha - string a ser corrigida
      Saida: string corrigida
     */

    /**
     * VerificaTagTable - Verifica se a um /table para cada table. Se nao tiver completa com o que faltar
     * 
     * @param String $linha String a ser alterada 
     * @return string Retorna uma string ja modificada
     */
    static function VerificaTagTable($texto) {
        $tbl1 = explode("<table", strtolower($texto));
        $tbl2 = explode("</table", strtolower($texto));
        $dif = count($tbl1) - count($tbl2);
        while ($dif > 0) {
            $dif--;
            $texto.="</table>";
        }
        return $texto;
    }

    /*     * ********************************************************************
      LimpaTitulo - Limpa todas as Tags indesejÃ¡veis do titulo
      Entrada: $linha - string a ser corrigida
      Saida: string corrigida
     */

    /**
     *  LimpaTitulo - Limpa todas as Tags indesejaveis do titulo
     * 
     * @param String $texto String a ser alterada 
     * @return string Retorna uma string ja modificada
     */
    static function LimpaTitulo($texto) {

        $texto = eregi_replace("<", "&lt;", $texto);
        $texto = eregi_replace("&lt;i>", "<i>", $texto);
        $texto = eregi_replace("&lt;b>", "<b>", $texto);
        $texto = eregi_replace("&lt;font", "<font", $texto);
        $texto = eregi_replace("&lt;s>", "<s>", $texto);
        $texto = eregi_replace("&lt;sub>", "<sub>", $texto);
        $texto = eregi_replace("&lt;sup>", "<sup>", $texto);
        $texto = eregi_replace("&lt;u>", "<u>", $texto);
        return $texto;
    }

    /*     * ********************************************************************
      LimpaConteudo - Limpa todas as Tags indesejÃ¡veis do conteudo
      Entrada: $linha - string a ser corrigida
      Saida: string corrigida
     */

    /**
     * LimpaConteudo - Limpa todas as Tags indeseja�veis do conteudo
     * 
     * @param String $texto String a ser alterada 
     * @return string Retorna uma string ja modificada
     */
    static function LimpaConteudo($texto) {
        $texto = eregi_replace("<script", "&lt;script", $texto);
        $texto = eregi_replace("<html", "&lt;html", $texto);
        $texto = eregi_replace("<head", "&lt;head", $texto);
        $texto = eregi_replace("<body", "&lt;body", $texto);
        $texto = eregi_replace("<title", "&lt;title", $texto);
        $texto = eregi_replace("<style", "&lt;style", $texto);
        $texto = eregi_replace("<frame", "&lt;frame", $texto);
        $texto = eregi_replace("<frameset", "&lt;frameset", $texto);
        $texto = eregi_replace("<form", "&lt;form", $texto);
        $texto = eregi_replace("<iframe", "&lt;iframe", $texto);

        return $texto;
    }

    /*     * ********************************************************************
      LimpaTag - Limpa todas as Tags
      Entrada: $linha - string a ser corrigida
      Saida: string corrigida
     * 
     */

    /**
     * LimpaTag - Limpa todas as Tags
     * 
     * @param String $texto String a ser alterada 
     * @return string Retorna uma string ja modificada
     */
    static function LimpaTags($texto) {
        $texto = eregi_replace("<", "&lt;", $texto);
        return $texto;
    }

    /*     * ********************************************************************
      TruncaString - Reduz o tamanho da string ao tamanho passado.
      Entrada: $texto- string a ser corrigida
      $limite - limite de caracteres
      Saida: string corrigida
     */

    /**
     * TruncaString - Reduz o tamanho da string ao tamanho passado.
     * 
     * @param String $texto String a ser alterada 
     * @param int $limite Limite de caracteres
     * @return string Retorna uma string ja modificada
     */
    static function TruncaString($texto, $limite) {
        if (strlen($texto) > $limite)
            $texto = substr($texto, 0, $limite - 3) . "...";
        return $texto;
    }

    /*     * ********************************************************************
      ConverteUrl2Html- troca " " (espaÃ§o) por %20, e acentos por especiais
      Entrada: $linha - string a ser corrigida
      Saida: string corrigida
     */

    /**
     * ConverteUrl2Html- e troca " " (espaco) por %20, e acentos por especiais
     * 
     * @param String $linha String a ser alterada 
     * @return string Retorna uma string ja modificada
     */
    static function ConverteUrl2Html($linha) {
        $substituir = " ������������������������������������������������";
        $saida = "";
        for ($i = 0; $i < strlen($substituir); $i++) {
            $array_sub[substr($substituir, $i, 1)] = "%" . dechex(ord(substr($substituir, $i, 1)));
        }
        for ($i = 0; $i < strlen($linha); $i++) {
            if (isset($array_sub[substr($linha, $i, 1)]))
                $saida.=$array_sub[substr($linha, $i, 1)];
            else
                $saida.=substr($linha, $i, 1);
        }
        return $saida;
    }

    /*     * ********************************************************************
      RetiraEspacoEAcentos - Ajusta a frase passada para se adequar as necessidades da Ajuda
      Entrada: $linha - string a ser corrigida
      Saida: string corrigida
     */

    /**
     *  RetiraEspacoEAcentos - Ajusta a frase passada para se adequar as necessidades da Ajuda
     * 
     * @param String $linha String a ser alterada 
     * @return string Retorna uma string ja modificada
     */
    static function RetiraEspacoEAcentos($linha) {
        $origem = "������������������������������������������������";
        $destin = "aeiouaeiouaoaeiouaeiouncAEIOUAEIOUAOAEIOUAEIOUNC";
        $saida = "";
        for ($i = 0; $i < strlen($origem); $i++) {
            $array_sub[substr($origem, $i, 1)] = substr($destin, $i, 1);
        }
        for ($i = 0; $i < strlen($linha); $i++) {
            $s = substr($linha, $i, 1);
            if (isset($array_sub[$s]))
                $saida.=$array_sub[$s];
            else if ($s != " " && $s != "-" && $s != "(" && $s != ")" && $s != "/")
                $saida.=$s;
        }
        return $saida;
    }

    /*     * *******************************************************************************************
      RetornaURLValida - Caso necessario, coloca http:// ou ftp:// na frente do link para exibicao
      Entrada: $endereco - endereco a ser verificado se necessita da modificacao
      Saida: endereco corrigido
     */
    
    /**
     * 
     * RetornaURLValida - Caso necessario, coloca http:// ou ftp:// na frente do link para exibicao
     * 
     * 
     * @param string $endereco endereco a ser verificado se necessita da modificacao
     * @return string Endereco corrigido
     */
    static function RetornaURLValida($endereco) {
        $partes = explode(":", $endereco);
        $partes2 = explode(".", $endereco);

        if ((strcmp($partes[0], "http") != 0) && (strcmp($partes[0], "ftp") != 0)) {
            if (strcmp($partes2[0], "www") == 0) {
                $endereco = "http://" . $endereco;
            } elseif (strcmp($partes2[0], "ftp") == 0) {
                $endereco = "ftp://" . $endereco;
            } else {
                $endereco = "http://" . $endereco;
            }
        }
        return $endereco;
    }

    /*     * ********************************************************************
      DecodificaString - decodifica a string passada por parametro utilizando 'html_entity_decode', dinamicamente
      Entrada: $id - id do elemento a ser retornado
      $texto - texto a ser decodificado
      $local - local a ser retornado (innerHTML, value, etc.)

      Saida: XML da função Ajax
     */
    
    /**
     * DecodificaString - decodifica a string passada por parametro utilizando 'html_entity_decode', dinamicamente
     * 
     * @param String $id id do elemento a ser retornado
     * @param String $texto texto a ser decodificado
     * @param String $local local a ser retornado (innerHTML, value, etc.)
     * @return \xajaxResponse XML da fun��o Ajax
     */
    static function DecodificaString($id, $texto, $local) {

        $objResponse = new xajaxResponse();

        $objResponse->assign($id, $local, html_entity_decode($texto));

        return $objResponse;
    }

    /*     * ********************************************************************
      VerificaStringQuery - Neutraliza qualquer tipo de SQL Injection em uma dada string
      Entrada: $string - string que será usada em uma query
      Saida: retorna a string tratada contra SQL Injection
     */
    
    /**
     * VerificaStringQuery - Neutraliza qualquer tipo de SQL Injection em uma dada string
     * 
     * @param string $string string que será usada em uma query
     * @return string retorna a string tratada contra SQL Injection
     */
    static function VerificaStringQuery($sock, $string) {
        if (get_magic_quotes_gpc()) {
            $string = stripslashes($string);
        }

        $string = mysqli_real_escape_string($sock, $string);

        return $string;
    }

    /*     * ********************************************************************
      VerificaN�meroQuery - Verifica se uma variavel contem mesmo um valor numerico
      Entrada: $num - variavel a ser verificada
      Saida: Retorna a propria variavel se a mesma contiver um valor numerico; "" caso contrario.
     */
    
    /**
     * VerificaN�meroQuery - Verifica se uma variavel contem mesmo um valor numerico
     * 
     * @param string $num variavel a ser verificada
     * @return string Retorna a propria variavel se a mesma contiver um valor numerico; "" caso contrario.
     */
    static function VerificaNumeroQuery($num) {
        if (is_numeric($num))
            return $num;
        else
            return "";
    }

    /*     * ********************************************************************
      str2num - Converte "valor_inteiro,valor_decimal" para "valor_inteiro.valor_decimal"
      Entrada: $str - string a ser corrigida.
      Saida: string corrigida
     */
    
    /**
     * str2num - Converte "valor_inteiro,valor_decimal" para "valor_inteiro.valor_decimal"
     * 
     * @param string $str string a ser corrigida.
     * @return string Retorna string corrigida
     */
    static function str2num($str) {
        if (strpos($str, '.') < strpos($str, ',')) {
            $str = str_replace('.', '', $str);
            $str = strtr($str, ',', '.');
        } else {
            $str = str_replace(',', '', $str);
        }
        return (float) $str;
    }

}

?>