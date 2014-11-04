<?php

/**
 * Classe Arquivos do modulo geral
 * @author     TelEduc
 * @copyright  20014 TelEduc
 * @license    http://teleduc.org.br/
 */
class Arquivos {
    /*     * ***********************************************************************
      RetornaArrayDiretorio - Retorna um array com os arquivos do diretorio
      Entrada: $diretorio - diretorio a ser varrido.
      Saida: Array multidimensional com:
      $lista[<num>]['Caminho'] - caminho completo.
      $lista[<num>]['Diretorio'] - Diretorio do arquivo
      $lista[<num>]['Arquivo'] - Nome do arquivo
      $lista[<num>]['Status'] - Condiï¿½ï¿½o especial (true ou false);
      $lista[<num>]['Tamanho'] - tamanho do arquivo
      $lista[<num>]['Data'] - data da ï¿½ltima modificaï¿½ï¿½o
     */

    /**
     * Este metodo estático retorna um array dos arquivos dentro do diretorio que é passado pelos parametros.
     * 
     * 
     * @param String $diretorio Caminho do diretorio a ser consultado
     * @return array Retorna array multidimensional com os arquivos
     */
    static function RetornaArrayDiretorio($diretorio) {

        if (Arquivos::ExisteArquivo($diretorio) == false)
            return (array());

        $diretorio = rtrim($diretorio, DIRECTORY_SEPARATOR);

        // Inicializamos o contador de arquivos encontrados
        $cont = 0;

        // Inicializamos a fila
        $dirs = array();
        // Inserimos o primeiro elemento na fila
        array_push($dirs, $diretorio);

        // Enquanto houver diretï¿½rios na fila procure
        // pelos arquivos
        while (count($dirs) > 0) {
            // Remove o primeiro elemento da fila
            $dir_cur = array_shift($dirs);

            // Lista o padrï¿½o procurado
            if (($e1 = glob($dir_cur . DIRECTORY_SEPARATOR . '*')) === false)
                return (array());

            if (($e2 = glob($dir_cur . DIRECTORY_SEPARATOR . '.*')) === false)
                return (array());

            $e2 = array_filter($e2, "FiltraDiretorioRelativos");
            $encontrados = array_merge($e1, $e2);

            if (count($encontrados)) {
                foreach ($encontrados as $idx => $nome) {
                    clearstatcache();
                    $tipo_arquivo = filetype($nome);

                    if ($tipo_arquivo == 'dir') {
                        $lista[$cont]['Caminho'] = $nome;
                        $lista[$cont]['Diretorio'] = trim(substr($nome, strlen($diretorio) + 1), DIRECTORY_SEPARATOR);
                        $lista[$cont]['Arquivo'] = "";
                        $lista[$cont]['Tamanho'] = 0;
                        clearstatcache();
                        $lista[$cont]['Data'] = filemtime($nome);
                        ;
                        $lista[$cont]['Status'] = Arquivos::RetornaStatusArquivo($nome);
                    } else if ($tipo_arquivo == 'file') {
                        $lista[$cont]['Caminho'] = $nome;
                        $dir_pai = dirname($nome);
                        $lista[$cont]['Diretorio'] = trim(substr($dir_pai, strlen($diretorio) + 1), DIRECTORY_SEPARATOR);
                        // correÃ§Ã£o feita para exibicao de todos os acentos - a funÃ§Ã£o basename nÃ£o Ã© "not utf-8 safe"
                        $lista[$cont]['Arquivo'] = ltrim(basename(preg_replace("/\//", "/ ", $nome)));
                        clearstatcache();
                        $lista[$cont]['Tamanho'] = filesize($nome);
                        clearstatcache();
                        $lista[$cont]['Data'] = filemtime($nome);
                        ;
                        $lista[$cont]['Status'] = Arquivos::RetornaStatusArquivo($nome);
                    }
                    $cont++;
                }
            }
            // Lista os subdiretï¿½rios da pasta corrente e
            // adiciona-os na fila
            // Lista o padrï¿½o procurado
            if (($c1 = glob($dir_cur . DIRECTORY_SEPARATOR . '*')) === false)
                return (array());

            if (($c2 = glob($dir_cur . DIRECTORY_SEPARATOR . '.*')) === false)
                return (array());
            $c2 = array_filter($c2, "FiltraDiretorioRelativos");

            $conteudo = array_merge($c1, $c2);

            if (count($conteudo) > 0) {
                foreach ($conteudo as $idx => $nome) {
                    clearstatcache();
                    if (filetype($nome) == "dir") {
                        array_push($dirs, $nome);
                    }
                }
            }
        }
        if (count($lista) > 0)
            uasort($lista, 'CompareRetornaArrayDiretorio');
        return ($lista);
    }

    /*     * ***********************************************************************
      ExisteArquivo - verifica a existï¿½ncia de um arquivo
      Entrada: $arquivo - nome do arquivo
      Saida: true se existe, do contrï¿½rio, false
     */

    /**
     * Este metodo estático retorna se existe um arquivo baseado em um caminho passado pelos parametros.
     * 
     * @param string $arquivo caminho do arquivo, incluindo as informacoes do proprio arquivo, Ex.: /mnt/pasta1/arq.html
     * @return boolean Se true arquivo exista, caso contrario arquivo nao existe
     */
    static function ExisteArquivo($arquivo) {
        clearstatcache();
        if (is_link($arquivo))
            return (true);
        else {
            clearstatcache();
            return(file_exists($arquivo));
        }
    }

    /*     * ***********************************************************************
      RetornaStatusArquivo - Retorna 1 se perms do arquivo = ????????x
      Entrada: $arquivo - nome passado
      Saida: true - Se tudo ok, false - se ocorreu algum erro
     */

    /**
     * Metódo estático que retorna se no momento você tem permissao de acesso ao arquivo.
     * 
     * @param String $arquivo caminho do arquivo, incluindo as informacoes do proprio arquivo, Ex.: /mnt/pasta1/arq.html
     * @return boolean Retorna true se haver permissoes suficientes no arquivo e false caso contrário.
     */
    static function RetornaStatusArquivo($arquivo) {
        clearstatcache();
        $perms = fileperms($arquivo);
        if (($perms & 0020) > 0)
            return (true);
        else
            return (false);
    }

    /*     * ***********************************************************************
      FiltraDiretorioRelativos - Funï¿½ï¿½o utilizada pela funï¿½ï¿½o 'array_filter'.
      Remove de um array contendo caminhos, o diretï¿½rio corrente ('.') e o
      diretï¿½rio pai ('..').
      Entrada: $var - caminho para arquivo
      Saida: true - se for diferente de '.' e '..'
     */

    /**
     * Dado uma string contendo um caminho para um arquivo, 
     * esse metodo ira retornar true caso existe um arquivo arquivo no final do caminho enviado.
     * 
     * @param string $var Caminho a ser verificado 
     * @return boolean Se true existe um arquivo no final desde caminho.
     */
    static function FiltraDiretorioRelativos($var) {
        $v = basename($var);
        return(($v != '.') && ($v != '..'));
    }

    /*     * ***********************************************************************
      CompareRetornaArrayDiretorio - usado internamente (Diogo)
      em RetornaArrayDiretorio
      Compara o conteï¿½do de uma sequencia
     */

    /**
     * Metódo estático auxiliar de uso interno para ordenacao,
     * 
     * Utilizado em um uasort como  funcao de comparação definida pelo usuário. 
     * 
     * @param array $ar1 Deve possuir o indexador Diretorio ou Arquivo $ar1['Diretorio'] ou $ar1['Arquivo']
     * @param array $ar2 Deve possuir o indexador Diretorio ou Arquivo $ar2['Diretorio'] ou $ar2['Arquivo']
     * @return int Retorna 0 se nao houve a comparacao, -1 caso o ar1 seja menor que ar2 e 1 caso o ar1 seja maior que ar2
     */
    static function CompareRetornaArrayDiretorio($ar1, $ar2) {
        if ($ar1['Diretorio'] < $ar2['Diretorio'])
            return -1;
        else if ($ar1['Diretorio'] > $ar2['Diretorio'])
            return 1;

        if ($ar1['Arquivo'] < $ar2['Arquivo'])
            return -1;
        else if ($ar1['Arquivo'] > $ar2['Arquivo'])
            return 1;
        return 0;
    }

    /*     * ***********************************************************************
      Remove arquivo - Apaga arquivo e link simbolico
      Entrada: $arquivo - nome do arquivo
      Saida: false - se houver algum erro
     */
    /**
     * Apaga um arquivo baseado no caminho enviado.
     * 
     * @param String $arquivo caminho do arquivo, incluindo as informacoes do proprio arquivo, Ex.: /mnt/pasta1/arq.html
     * @return boolean Retorna true caso consiga apagar o arquivo e false caso nao.
     */
    static function RemoveArquivo($arquivo) {
        //  if (!file_exists($arquivo))
        //    return false;
        //  return(unlink($arquivo));
        return(unlink($arquivo));
    }

    /*     * ***********************************************************************
      CriaLinkSimbolico - Cria link simbolico
      Entrada: $origem - diretï¿½rio de origem
      $destino - caminho e nome do link
      Saida: true - Se tudo ok, false - se ocorreu algum erro
     */
    
    
    /**
     * Cria um link simbolico de sistema.
     * Nao é um link HTML.
     * 
     * @param String $origem Alvo do link
     * @param String $destino O nome do link
     * @return boolean Retorna TRUE em caso de sucesso ou FALSE em caso de falha.
     */
    static function CriaLinkSimbolico($origem, $destino) {
        return(symlink($origem, $destino));
    }

    /*     * ***********************************************************************
      CriaDiretorio - cria diretorio com modo 755
      Entrada: $diretorio - nome do diretorio
      Saida: true - Se tudo ok, false - se ocorreu algum erro
     */
    
    
    /**
     * Cria pasta a partir do caminho enviado com permissão 755 
     * 
     * @param String $diretorio 
     * @return boolean
     */
    static function CriaDiretorio($diretorio) {

        if (Arquivos::ExisteArquivo($diretorio)) {
            clearstatcache();
            if (filetype($diretorio) == "dir")
                return true;
        }

        $dir_tmp = dirname($diretorio);
        if (!Arquivos::CriaDiretorio($dir_tmp))
            return false;

        $r1 = mkdir($diretorio, 0755);
        $r2 = chmod($diretorio, 0755);
        return ($r1 && $r2);
    }

}
