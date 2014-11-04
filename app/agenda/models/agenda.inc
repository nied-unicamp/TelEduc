<?php

/**
 * Classe Model do modulo Agenda
 * @author     TelEduc
 * @copyright  20014 TelEduc
 * @license    http://teleduc.org.br/
 */
class Agenda {

    /**
     * Este metódo estático faz uma consulta ao banco a tabela  Diretório e retorna uma string
     * com um caminho.
     *
     * @param mysqli_connect	$sock	Instância de conexão do mysql
     * @param string $item Item pode ser comparado a um nome de uma variavel é uma coluna da tabela Diretório 
     * @return string Retorna um texto com o caminho do diretorio 
     * @access public
     * */
    static function RetornaDiretorio($sock, $item) {
        $query = "select diretorio from Diretorio where item='" . ConversorTexto::VerificaStringQuery($item) . "'";
        $res = AcessoSQL::Enviar($sock, $query);
        $linha = AcessoSQL::RetornaLinha($res);
        return ($linha[0]);
    }

    /**
     * Retorna agenda Ativa baseado na conexão passada, ou seja, o curso escolhido e busca tais informações na tabela agenda_itens. 
     *
     * @param mysqli_connect	$sock	Instância de conexão do mysql
     * @return array Array bidimensional indexado pelas colunas da tabela Agenda_itens
     * @access public
     * */
    static function RetornaAgendaAtiva($sock) {
        $query = "select cod_item,titulo,texto,situacao from Agenda_itens where situacao='A'";
        $res = AcessoSQL::Enviar($sock, $query);
        $linha = AcessoSQL::RetornaLinha($res);
        return $linha;
    }

  

    /**
     * Este metodo estático faz uma consulta ao banco a tabela  Diretorio e retorna uma string
     * com um caminho.
     *
     * @param mysqli_connect	$sock	Instância de conexão do mysql
     * @param string $dirname Nome do diretorio da ferramenta
     * @param int $cod_curso Número de identificação primária do curso da tabela Cursos
     * @param int $cod_usuario Número de identificação primária do usuario da tabela Usuários
     * @param int $cod_item Número de identificação primária do item da tabela Item Agenda
     * @param string $diretorio_arquivos Diretorio dos arquivos do TelEduc - Caminho final do diretorio proveniente da tabela Diretorios
     * @param string $diretorio_temp Diretorio dos arquivos do TelEduc -  Caminho tempoario do diretorio proveniente da tabela Diretorios
     * @return array Array bidimensional indexada por diretorio e link (caminho relativo)
     * @access public
     * */
    static function CriaLinkVisualizar($sock, $dirname, $cod_curso, $cod_usuario, $cod_item, $diretorio_arquivos, $diretorio_temp) {
        if (trim($diretorio_arquivos) == "" || trim($diretorio_temp) == "" || trim($dirname) == "" ||
                $cod_curso == "" || $cod_item == "")
            return false;

        /* Busca Arquivo a ser mostrado */
        $dir = $diretorio_arquivos . "/" . $cod_curso . "/" . $dirname . "/" . $cod_item . "/";

        // Cria link simbï¿½lico (apaga antigo, se houver)
        if ($cod_usuario < 0)
            $cod_usuario = 0;
        $dirlink = $diretorio_temp . "/" . $dirname . "_" . $cod_curso . "_" . $cod_usuario;
        $dirlinkpath = "../../diretorio/" . $dirname . "_" . $cod_curso . "_" . $cod_usuario;

        if (Arquivos::ExisteArquivo($dirlink)) {
            Arquivos::RemoveArquivo($dirlink);
        }
        Arquivos::CriaLinkSimbolico($dir, $dirlink);

        $retorno['diretorio'] = $dirlink . "/";
        $retorno['link'] = $dirlinkpath . "/";

        return ($retorno);
    }

    /*     * ********************************************************************
      RetornaArquivosAgendaVer - Retorna lista de arquivos da Agenda
      Entrada: $dirlink - nome do diretï¿½rio da ferramenta
      $cod_curso - cï¿½digo do item
      Saida: Array multidimensional com:
      $lista[<num>]['Caminho'] - caminho completo.
      $lista[<num>]['Diretorio'] - Diretorio do arquivo
      $lista[<num>]['Arquivo'] - Nome do arquivo
      $lista[<num>]['Status'] - Condiï¿½ï¿½o especial (true ou false);
      $lista[<num>]['Tamanho'] - tamanho do arquivo
      $lista[<num>]['Data'] - data da ï¿½ltima modificaï¿½ï¿½o
     */

    /**
     * Este metódo estático faz uma consulta ao banco a tabela  Diretório e retorna uma string
     * com um caminho.
     *
     * @param int $cod_curso Número de identificação primaria do curso da tabela Cursos
     * @param string $dirlink Nome do diretório da ferramenta
     * @return string Retorna um texto com o caminho do diretorio.
     * @access public
     * */
    static function RetornaArquivosAgendaVer($cod_curso, $dirlink) {
        return (Arquivos::RetornaArrayDiretorio($dirlink));
    }

    /*     * ********************************************************************
      IniciaCriacao - Inicia a criaï¿½ï¿½o de um novo item
      Entrada: $sock - BASE DO CURSO
      $cod_usuario - usuario atual
      $cod_curso - codigo do curso
      $diretorio_temp - nome do diretï¿½rio temporario, usado durante a ediï¿½ï¿½o
      $titulo - titulo do item a ser criado
      Saida: codigo do item criado
     */

    /**
     * Este metodo é inicio da criacao de um item na agenda relacionando-se com a tabela Agenda_itens e  Agenda_itens_historicos
     *
     * @param mysqli_connect	$sock	Instância de conexão do mysql
     * @param int $cod_usuario Número de identificação primária do usuario da tabela Usuários
     * @param int $cod_curso Número de identificação primária do curso da tabela Cursos
     * @param string $diretorio_temp Caminho temporário do diretório proveniente da tabela Diretorios
     * @param string $titulo Título para o novo item na agenda 
     * @param string $texto Texto para descrição do novo item na agenda
     * @return string Codigo do novo item criado na agenda
     * @access public	
     * */
    static function IniciaCriacao($sock, $cod_usuario, $cod_curso, $diretorio_temp, $titulo, $texto) {
        if (trim($diretorio_temp) == "" || $cod_curso == "")
            return -1; //tah certo isso?!


        $cod_item = AcessoSQL::RetornaProximoCodigo($sock, "Agenda_itens");

        $consulta = "insert into Agenda_itens (cod_item, cod_usuario, titulo, texto, situacao, data, status, inicio_edicao) values (" . $cod_item . ", " . $cod_usuario . ", '" . ConversorTexto::VerificaStringQuery(htmlentities($titulo)) . "','" . ConversorTexto::VerificaStringQuery(htmlentities($texto)) . "', 'N', " . time() . ", 'L', " . time() . ")";

        $res = AcessoSQL::Enviar($sock, $consulta);
        $consulta = "insert into Agenda_itens_historicos values (" . $cod_item . ", " . $cod_usuario . ", " . time() . ", 'C')";

        $res = AcessoSQL::Enviar($sock, $consulta);

        if (!file_exists($diretorio_temp . "/tmp/" . $cod_curso . "/"))
            Arquivos::CriaDiretorio($diretorio_temp . "/tmp/" . $cod_curso . "/");
        if (!file_exists($diretorio_temp . "/tmp/" . $cod_curso . "/agenda/"))
            Arquivos::CriaDiretorio($diretorio_temp . "/tmp/" . $cod_curso . "/agenda/");

        $dir = $diretorio_temp . "/tmp/" . $cod_curso . "/agenda/" . $cod_item . "/";
        Arquivos::CriaDiretorio($dir);


        return($cod_item);
    }

}
