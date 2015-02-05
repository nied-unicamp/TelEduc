<?php

/**
 * Classe Linguas do modulo geral
 * @author     TelEduc
 * @copyright  20014 TelEduc
 * @license    http://teleduc.org.br/
 */
class Linguas {

    /**
     * Este metodo est·tico retorna a frase solicitada baseado nos parametros.
     * 
     * @param mysqli_connect $sock	inst‚ncia de conex„o do mysql
     * @param int $cod_texto N˙mero de identificaÁ„o primaria do texto da tabela Lingua_textos
     * @param int $cod_ferramenta N˙mero de identificaÁ„o primaria da ferramenta da tabela Ferramentas
     * @return string Frase solicitada.
     */
    /*static function RetornaFrase($sock, $cod_texto, $cod_ferramenta) {
        //   Global $cod_lingua_s,$lista_frases_s;
        // 2Session
        $dbnamebase = $_SESSION['dbnamebase'];

        $cod_lingua = Sessao::CodLinguaS();

        if (!isset($_SESSION['lista_frases_s'][$cod_ferramenta])) {
            $query = "SELECT texto FROM " . $dbnamebase . ".Lingua_textos WHERE ";
            $query .= "cod_texto=" . ConversorTexto::VerificaNumeroQuery($cod_texto) . " AND ";
            $query .= "cod_lingua=" . ConversorTexto::VerificaNumeroQuery($cod_lingua) . " AND ";
            $query .= "cod_ferramenta=" . ConversorTexto::VerificaNumeroQuery($cod_ferramenta);

            if ($id_query = AcessoSQL::Enviar($sock, $query)) {
                $linha = AcessoSQL::RetornaLinha($id_query);
                $texto = $linha[0];
                if (!isset($texto))
                    $texto = "(ERRO : Texto correspondente a essa l&iacute;ngua n&atilde;o cadastrado: " . $cod_texto . ")";
            }
            else {
                // Erro na execuÔøΩo da clÔøΩsula : terminar o programa
                $msg = "</script></table></div></table></div></table></div>";
                $msg .= "<h3>ERRO : N&atilde;o foi poss&iacute;vel executar a cl&aacute;usula SQL</h3>";
                $msg .= "<br><strong>SQL : </strong><br>" . $query;
                $msg .= "<br><strong>Mensagem de erro do MySql : </strong><br>" . mysql_error();
                $msg .= "<br><a href=\"#\" onclick=\"history.go(-1);\">Voltar</a>";
                die($msg);
            }
        } else {
            $texto = $_SESSION['lista_frases_s'][$cod_ferramenta][$cod_texto];
        }

        return(ConversorTexto::ConverteAspas2Html($texto));
    }
    */
    /**
     * Este metÛdo baseado no array de textos, e no cod_texto do testo que se quer obter
     * retorna uma string. Caso n„o haja texto relacionado a cod_texto no array textos, ele retorna Falta Texto 
     * 
     * @param array $textos Array unidimensional com diversas strings de texto
     * @param int $cod_texto N˙mero de identificaÁ„o primaria do texto da tabela Lingua_textos
     * @return string Retorna texto do array enviado j· em condiÁıes de uso em um HTML ou Falta texto.
     */
    /* static function RetornaFraseDaLista($textos, $cod_texto) {
        if (ConversorTexto::ConverteAspas2Html($textos[$cod_texto]) != "")
            return(ConversorTexto::ConverteAspas2Html($textos[$cod_texto]));
        else {
            switch (Sessao::CodLinguaS()) {
                case 1://PortuguÔøΩs
                    return("Falta o texto");
                case 2://Espanhol
                    return("Falta texto");
                case 3://InglÔøΩs
                    return("Missing Text");
                case 4://PortuguÔøΩs PT
                    return("Falta o texto");
            }
        }
    } */
    
    /**
     * Retorna uma lista de frases em para uma feramenta.
     * 
     * @param mysqli_connect $sock	Inst‚ncia de conex„o do mysql 
     * @param int $cod_ferramenta N˙mero de identificaÁ„o primaria da ferramenta da tabela Ferramentas
     * @return array Retorna um array unidemensional com informaÁıes 
     */
    /* static function RetornaListaDeFrases($sock, $cod_ferramenta) {
        //   Global $cod_lingua_s,$lista_frases_s;
        // 2Session
        $dbnamebase = $_SESSION['dbnamebase'];

        $cod_lingua = Sessao::CodLinguaS();

        /* Se ja estiver em memoria, vai direto... */
        //if (Linguas::ListaDeFrasesS($sock, $cod_ferramenta!="") && count(Linguas::ListaDeFrasesS($sock, $cod_ferramenta))>0)
        //return (Linguas::ListaDeFrasesS($sock, $cod_ferramenta));

        /* Evitar erro caso a variavel $cod_lingua esteja vazia. Valor default = 1 (pt)
        if (is_null($cod_lingua))
            $cod_lingua = 1;
        $query = "SELECT cod_texto, texto FROM " . $dbnamebase . ".Lingua_textos WHERE ";
        $query .= "cod_lingua = " . ConversorTexto::VerificaNumeroQuery($cod_lingua) . " AND ";
        $query .= "cod_ferramenta = " . ConversorTexto::VerificaNumeroQuery($cod_ferramenta) . " ";
        $query .= "ORDER BY cod_texto";
        $textos = "";

        if ($id_query = AcessoSQL::Enviar($sock, $query)) {
            unset($textos);

            if (AcessoSQL::RetornaNumLinhas($id_query) > 0)
                while ($linha = AcessoSQL::RetornaLinha($id_query))
                    $textos[$linha["cod_texto"]] = $linha["texto"];
        }
        else {
            // Erro na execu√ß√£o da cl√°usula : terminar o programa
            $msg = "<h3>ERRO : N&atilde;o foi poss&iacute;vel executar a cl&aacute;usula SQL</h3>";
            $msg .= "<br><b>SQL : </b><br>" . $query;
            $msg .= "<br><b>Mensagem de erro do MySql : </b><br>" . mysql_error();
            $msg .= "<br><a href=\"#\" onclick=\"history.go(-1);\">Voltar</a>";
            die($msg);
        }
        $_SESSION['lista_frases_s'][$cod_ferramenta]=$textos;

        return($textos);
    } */
    
    /**
     * Muda de lingua baseado nos parametros
     * 
     * @param mysqli_connect $sock	Inst‚ncia de conex„o do mysql
     * @param int $cod_lingua  CÛdigo da lingua j· definido e que est· na Session
     */
    /* static function MudancaDeLingua($sock, $cod_lingua) {
        //   Global $cod_lingua_s, $lista_frases_s;

        if ($_SESSION['cod_lingua_s'] != $cod_lingua) {
            $_SESSION['cod_lingua_s'] = $cod_lingua;
            foreach ($_SESSION['lista_frases_s'] as $cod_fer => $lista) {
                MemorizaListaDeFrases($sock, $cod_fer);
            }
        }
    } */

    /*     * ***********************************************************************
      ListaLinguas - Retorna a lista de linguas cadastradas
      Entrada: $sock - BASE Externa
      Saida: array [$cod_lingua]=nome
     */
    
    /**
     * Retorna a lista de linguas cadastradas
     * @param mysqli_connect $sock	Inst‚ncia de conex„o do mysql 
     * @return array Retorna array deste modo:  [$cod_lingua]=nome
     */
    static function ListaLinguas($sock) {
        //   Global $cod_lingua_s;
        // 2Session
        $dbnamebase = $_SESSION['dbnamebase'];
        $_SESSION['cod_lingua_s'] = Sessao::CodLinguaS();
        $query = "select cod_texto 'cod_lingua',texto 'lingua' from " . $dbnamebase . ".Lingua_textos where cod_ferramenta=-6 and cod_lingua=" . $_SESSION['cod_lingua_s'] . " order by cod_texto";
        $res = AcessoSQL::Enviar($sock, $query);
        $saida = "";
        unset($saida);
        while ($linha = AcessoSQL::RetornaLinha($res))
            if ($linha['cod_lingua'] != 2) { //retirando temporariamente o espanhol, por falta de traduÔøΩÔøΩo
                $saida[$linha['cod_lingua']] = $linha['lingua'];
            }
        return $saida;
    }
    
    
    /**
     * Baseado no codigo da linha, retorna o nome da lingua em string.
     * 
     * @param mysqli_connect $sock  Inst‚ncia de conex„o do mysql 
     * @param int  $cod_lin  N˙mero de identificaÁ„o primaria de uma lingua da tabela Linguas
     * @return string Texto com a lingua pedida.
     */
    static function LinguaLocal($sock, $cod_lin) {
        $dbnamebase = $_SESSION['dbnamebase'];
        $query = "select texto from " . $dbnamebase . ".Lingua_textos where cod_ferramenta=-6 and cod_lingua=" . $cod_lin . " and cod_texto=" . $cod_lin . "";
        $res = AcessoSQL::Enviar($sock, $query);
        $linha = AcessoSQL::RetornaLinha($res);
        $texto = $linha['texto'];
        return($texto);
    }
    
    /* ************************************************************************
     MudancaDeLingua - Atualiza todas as frases em memoria para a nova lingua selecionada
    Entrada: $sock - Base Externa
    $cod_lingua - codigo da lingua a ser atualizada
    Saida: nenhuma
    */
    static function MudancaDeLingua($sock, $cod_lingua)
    {
    	if ($_SESSION['cod_lingua_s'] != $cod_lingua)
    	{
    		$_SESSION['cod_lingua_s'] = $cod_lingua;
    		
    		if($cod_lingua == 1){
    			$locale = "pt_BR";
    		}
    		else if($cod_lingua == 3){
    			$locale = "en_US";
    		}
    		else if($cod_lingua == 4){
    			$locale = "pt_PT";
    		}
    	}
    
    }

    /* ************************************************************************
     AbreviaturaLingua - Dado o codigo da lingua, retorna a abreviatura da lingua, conforme abaixo (ISO693):
    1 = "pt"
    2 = "es"
    3 = "en"
    4 = "pt"
    Por padrao, o codigo da lingua eh "pt".
    Entrada: $cod_lingua - codigo da lingua a ser consultada
    Saida: string
    */
    static function AbreviaturaLingua($cod_lingua){
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
}

?>