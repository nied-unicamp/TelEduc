<?php
/**
 * 
 */
require_once 'acesso_sql.inc';
require_once 'conversor_texto.inc';
/**
 * Classe Cursos do modulo geral
 * @author     TelEduc
 * @copyright  20014 TelEduc
 * @license    http://teleduc.org.br/
 */
class Cursos {
    /*     * ********************************************************************
      RetornaStatusCurso - Retorna se o curso jï¿½estï¿½em andamento, ou se jï¿½
      se encerrou
      Entradas: $sock - Socket da base de dados (retorno do Conectar)
      BASE DO CURSO
      $cod_curso - Curso em que ele estï¿½
      Saida: N - Nï¿½ comeï¿½u ainda
      A - Em Andamento
      E - Encerrado
      I - Invalido
     */

    /**
     * 
     * @param mysqli_connect	$sock	instância de conexão do mysql
     * @param int $cod_curso Número de identificação primaria do curso da tabela Cursos
     * @return string  N - Não começou ainda,  A - Em Andamento,  E - Encerrado, I - Invalido
     */
    static function RetornaStatusCurso($sock, $cod_curso) {
        $query = "select curso_inicio,curso_fim from Cursos where cod_curso=" . $cod_curso;
        $res = AcessoSQL::Enviar($sock, $query);
        if (AcessoSQL::RetornaNumLinhas($res) > 0) {
            $linha = AcessoSQL::RetornaLinha($res);
            $hoje = time();
            $ontem = $hoje - 86400;
            if ($linha[0] > $hoje)
                return ("N");
            else if ($linha[1] >= $ontem)
                return ("A");
            else
                return ("E");
        }
        else {
            return ("I");
        }
    }

    /*     * ********************************************************************
      NomeCurso - Retorna o nome do curso
      Entrada: $sock - SOCK Externo
      Saida: string contendo o nome do curso
     */

    /**
     * 
     * @param mysqli_connect	$sock	instância de conexão do mysql
     * @param int $cod_curso Número de identificação primaria do usuario da tabela Cursos
     * @return string Retorna uma string contendo o nome do curso
     */
    static function NomeCurso($sock, $cod_curso) {
        $query = "select nome_curso from Cursos where cod_curso = " . $cod_curso;
        $res = AcessoSQL::Enviar($sock, $query);
        $linha = AcessoSQL::RetornaLinha($res);
        return $linha['nome_curso'];
    }

    /**
     * Gera html que prepara menu de ajuda,
     * com com varios <li>, sem os <ul>. Nestes <li> há imagens e links que abrem em novas janelas.
     * 
     * @param mysqli_connect	$sock	instância de conexão do mysql
     * @param int $cod_curso Número de identificação primaria do curso da tabela Cursos
     * @param int $cod_ferramenta Número de identificação primaria da Ferramenta da tabela Ferramenta
     * @param int $cod_pagina Parametro Opcional, Número de identificação primaria, a verificar, mas parece estar relacionado a cod_texto da tabela linguas_textos
     * @param int $cod_usuario Número de identificação primaria do usuario da tabela Usuário
     * @return string Retorna uma lista com varios <li></li>, sem o <ul> antes. Dentro desta lista há links e imagens relacionadas a cada ajuda.
     */
    static function PreparaAjuda($sock, $cod_curso, $cod_ferramenta, $cod_pagina = null, $cod_usuario) {

        //   global $cod_lingua_s,$sock,$cod_usuario,$bibliotecas;
        $bibliotecas = "../views/";
        $ajuda_nao_encontrada = true;

        if (isset($cod_pagina)) {
            $eformador = Usuarios::EFormador($sock, $cod_curso, $cod_usuario);
            $sock = AcessoSQL::MudarDB($sock, "");
            $query = "select nome_pagina,texto from Ajuda where cod_ferramenta=" . ConversorTexto::VerificaNumeroQuery($cod_ferramenta) . " and cod_pagina=" . ConversorTexto::VerificaNumeroQuery($cod_pagina) . " and cod_lingua=" . ConversorTexto::VerificaNumeroQuery($_SESSION['cod_lingua_s']) . " and tipo_usuario='" . ($eformador ? 'F' : 'A' ) . "'";
            $res = AcessoSQL::Enviar($sock, $query);
            if (AcessoSQL::RetornaNumLinhas($res) > 0) {
                $ajuda_nao_encontrada = false;
                $ajuda_link = $bibliotecas . "../ajuda/ajuda.php?cod_curso=" . $cod_curso . "&amp;cod_ferramenta=" . $cod_ferramenta . "&amp;cod_pagina=" . $cod_pagina . "&amp;tipo_usuario=" . ($eformador ? 'F' : 'A' );
                $html_link = "<li><img src=\"../../../web-content/imgs/icAjuda.gif\" border=\"0\" alt=\"Ajuda\" />&nbsp;</li>";
                $html_link.="<li><a href=\"" . $ajuda_link . "\" onclick=\"window.open('" . $ajuda_link . "','AjudaDisplay','width=600,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');return false;\" target=\"blank\">Ajuda</a></li>";
                $html_link.="<li>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;</li>";
            }
            $sock = AcessoSQL::MudarDB($sock, $cod_curso);
        }

        // Esse ficarï¿½provisï¿½iamente, atï¿½que todas as pï¿½inas estiverem listadas
        if ($ajuda_nao_encontrada) {
            //     // Pegando o subtitulo da ferramenta para usar para linkar a ajuda
            //     $tmp=explode("> - ",$cabecalho);
            //     $tmp1=explode("</b>",$tmp[1]);
            //     $aname=RetiraEspacoEAcentos($tmp1[0]);
            // Checando se existe o diretï¿½io com a Ajuda da ferramenta
            if (Usuarios::EFormador($sock, $cod_curso, $cod_usuario))
                $arquivo = $cod_lingua_s . "_" . $cod_ferramenta . "_f.html";
            else
                $arquivo = $cod_lingua_s . "_" . $cod_ferramenta . "_a.html";

            if (file_exists($bibliotecas . "../ajuda/" . $arquivo)) {
                $ajuda_link = $bibliotecas . "../ajuda/" . $arquivo;
                // ?? - Ajuda
                $html_link = "<li><img src=\"../imgs/icAjuda.gif\" border=\"0\" alt=\"Ajuda\" />&nbsp;</li>";
                $html_link.="<li><a href=\"" . $ajuda_link . "\" onClick=\"window.open('" . $ajuda_link . "','AjudaDisplay','width=600,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');return false;\" target=\"blank\">Ajuda</a></li>";
                $html_link.="<li>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;</li>";
            }
        }
        return $html_link;
    }

    /*     * ********************************************************************
      CompletarDadosCurso - Retorna True se faltam dados do curso para completar
      Entradas: $sock - Socket da base de dados (retorno do Conectar)
      $cod_curso - Curso em que ele estï¿½
      Saida: true se precisa completar dados
     */
    
    /**
     * CompletarDadosCurso - Retorna True se faltam dados do curso para completar
     * 
     * @param mysqli_connect	$sock	instância de conexão do mysql
     * @param int $cod_curso Número de identificação primaria do curso da tabela Cursos
     * @return boolean  Retorna True se faltam dados do curso para completar
     */
    static function CompletarDadosCurso($sock, $cod_curso) {
        $query = "select inscricao_inicio,inscricao_fim,curso_inicio,curso_fim,informacoes,publico_alvo,tipo_inscricao from Cursos where cod_curso=" . $cod_curso;
        $res = AcessoSQL::Enviar($sock, $query);
        $linha = AcessoSQL::RetornaLinha($res);
        if ($linha['inscricao_inicio'] == "" || $linha['inscricao_fim'] == "" || $linha['curso_inicio'] == "" || $linha['curso_fim'] == "" || $linha['informacoes'] == "" || $linha['publico_alvo'] == "" || $linha['tipo_inscricao'] == "")
            return true;
        return false;
    }

}
