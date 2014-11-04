<?php

//require_once "acesso_sql.php";
/**
 * Classe Sessão do modulo geral
 * @author     TelEduc
 * @copyright  20014 TelEduc
 * @license    http://teleduc.org.br/
 */
class Sessao {
    /*     * ******************************************************************
      RetornaSessionID - Retorna o identificador de session usado pelo PHP
      Usar em toda chamada a arquivos (arquivo.php?".RetornaSessionID."&...)
      Saida: String com o Session (se necessï¿½io, ou vazio se Cookie ativo)
     */

    /**
     * Retorna o identificador de session usado pelo PHP
     * Usar em toda chamada a arquivos (arquivo.php?".RetornaSessionID."&...)
     * @return string  String com o Session (se necessï¿½io, ou vazio se Cookie ativo)
     */
    static function RetornaSessionID() {
        return SID;
    }

    /*     * ******************************************************************
      RetornaSessionIDInput - retorna a linha <input type=hidden name=PHPSESSID value=....>
      Saida: String com o Session (se necessï¿½io, ou vazio se Cookie ativo)
     */
 
    /**
     * Retorna a linha <input type=hidden name=PHPSESSID value=....>
     * 
     * @return string String com o Session (se necessï¿½io, ou vazio se Cookie ativo)
     */
    static function RetornaSessionIDInput() {
        return "<input type=\"hidden\" name=\"PHPSESSID\" value=\"" . session_id() . "\" />\n";
    }

    /* Se nenhuma lingua foi definida, portuguï¿½ ï¿½o padrï¿½ */
    
    /**
     * Se nenhuma lingua foi definida, português por padrão
     * 
     * @return int Codigo da lingua baseado na tabela Config
     */
    static function CodLinguaS() {

        $sock = AcessoSQL::Conectar("");
        $query = "select valor from Config where item='lingua'";
        $res = AcessoSQL::Enviar($sock, $query);
        /* AcessoSQL::Desconectar($sock); */
        $linha = AcessoSQL::RetornaLinha($res);
        if (isset($linha['valor']))
            $retorno = $linha['valor'];
        else
            $retorno = 1;

        if (isset($cod_ferramenta))
            $cod_ferramenta_s = $cod_ferramenta;
        return $retorno;
    }

}

?>