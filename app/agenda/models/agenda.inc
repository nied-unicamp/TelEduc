<?php

/**
 * Classe Model do modulo Agenda
 * @author     TelEduc
 * @copyright  20014 TelEduc
 * @license    http://teleduc.org.br/
 */
class Agenda {

    /**
     * Este met�do est�tico faz uma consulta ao banco a tabela  Diret�rio e retorna uma string
     * com um caminho.
     *
     * @param mysqli_connect	$sock	Inst�ncia de conex�o do mysql
     * @param string $item Item pode ser comparado a um nome de uma variavel � uma coluna da tabela Diret�rio 
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
     * Retorna agenda Ativa baseado na conex�o passada, ou seja, o curso escolhido e busca tais informa��es na tabela agenda_itens. 
     *
     * @param mysqli_connect	$sock	Inst�ncia de conex�o do mysql
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
     * Este metodo est�tico faz uma consulta ao banco a tabela  Diretorio e retorna uma string
     * com um caminho.
     *
     * @param mysqli_connect	$sock	Inst�ncia de conex�o do mysql
     * @param string $dirname Nome do diretorio da ferramenta
     * @param int $cod_curso N�mero de identifica��o prim�ria do curso da tabela Cursos
     * @param int $cod_usuario N�mero de identifica��o prim�ria do usuario da tabela Usu�rios
     * @param int $cod_item N�mero de identifica��o prim�ria do item da tabela Item Agenda
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

        // Cria link simb�lico (apaga antigo, se houver)
        if ($cod_usuario < 0)
            $cod_usuario = 0;
        $dirlink = $diretorio_temp . "/" . $dirname . "_" . $cod_curso . "_" . $cod_usuario;
        $dirlinkpath = "../../../diretorio/" . $dirname . "_" . $cod_curso . "_" . $cod_usuario;

        if (Arquivos::ExisteArquivo($dirlink)) {
            Arquivos::RemoveArquivo($dirlink);
        }
        Arquivos::CriaLinkSimbolico($dir, $dirlink);

        $retorno['diretorio'] = $dirlink . "/";
        $retorno['link'] = $dirlinkpath . "/";

        return ($retorno);
    }

    /**
     * Este met�do est�tico faz uma consulta ao banco a tabela  Diret�rio e retorna uma string
     * com um caminho.
     *
     * @param int $cod_curso N�mero de identifica��o primaria do curso da tabela Cursos
     * @param string $dirlink Nome do diret�rio da ferramenta
     * @return string Retorna um texto com o caminho do diretorio.
     * @access public
     * */
    static function RetornaArquivosAgendaVer($cod_curso, $dirlink) {
        return (Arquivos::RetornaArrayDiretorio($dirlink));
    }

    /**
     * Este metodo � inicio da criacao de um item na agenda relacionando-se com a tabela Agenda_itens e  Agenda_itens_historicos
     *
     * @param mysqli_connect	$sock	Inst�ncia de conex�o do mysql
     * @param int $cod_usuario N�mero de identifica��o prim�ria do usuario da tabela Usu�rios
     * @param int $cod_curso N�mero de identifica��o prim�ria do curso da tabela Cursos
     * @param string $diretorio_temp Caminho tempor�rio do diret�rio proveniente da tabela Diretorios
     * @param string $titulo T�tulo para o novo item na agenda 
     * @param string $texto Texto para descri��o do novo item na agenda
     * @return string Codigo do novo item criado na agenda
     * @access public	
     * */
    static function IniciaCriacao($sock, $cod_usuario, $cod_curso, $diretorio_temp, $titulo, $texto) {
        if (trim($diretorio_temp) == "" || $cod_curso == "")
            return -1; //tah certo isso?!
	
        $ferramenta_agenda = 'agenda';
        $view_agenda = '../../../'.$ferramenta_agenda.'/views/';

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

    /*     * ********************************************************************
      AbreEdicao - Marca no banco de dados o início da edição do item dado, dinâmicamente
      Entrada: $cod_curso - código do curso, para conectar ao banco de dados
      $cod_item - item ao qual o endereco estará associado
      $cod_usuario - código do usuário que está utilizando
      $origem - página de onde originou-se a chamada à essa função

      Saida: XML da função Ajax
     */

    /**
     * Marca no banco de dados o in�cio da edi��o do item dado, dinamicamente
     * Saida: echo do XML da fun��o Ajax
     * 
     * @param int $cod_curso C�digo do curso, para conectar ao banco de dados
     * @param int $cod_item Item ao qual o endere�o estar� associado
     * @param int $cod_usuario C�digo do usu�rio que est� utilizando
     * @param type $origem P�gina de onde originou-se a chamada � essa fun��o
     * 
     * 
     */
    static function AbreEdicao($cod_curso, $cod_item, $cod_usuario, $origem) {
        // como vou precisar atualizar campos de data, preciso saber a data em UnixTime

        $data = time();
        $sock = AcessoSQL::Conectar($cod_curso);
        //Retorna os dados da agenda - Array: cod_item,cod_usuario,  titulo, texto, situacao,
        //data, data_ativo, data_inativo, status, inicio_edicao.
        $linha = Agenda::RetornaAgenda($sock, $cod_item);
        //Retorna a ultima ocorrencia do historico de um item dado - cod_item, cod_usuario, data, acao
        $linha_historico = Agenda::RetornaUltimaPosicaoHistorico($sock, $cod_item);

        if (($linha['status'] == "E") && ($cod_usuario != $linha_historico['cod_usuario'])) {
            window . open($view_agenda.'em_edicao.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=ver', 'EmEdicao', 'width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');

            //$objResponse->script("document.location='".$origem.".php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."cod_ferramenta=1'");
        } else {
            if ($linha['status'] == "E") {
                $consulta = "insert into Agenda_itens_historicos values (" . $cod_item . ", " . $cod_usuario . ", " . time() . ", 'D')";
                $res = AcessoSQL::Enviar($sock, $consulta);
            }
            $consulta = "update Agenda_itens set status='E', cod_usuario=" . $cod_usuario . ", inicio_edicao=" . time() . " where cod_item=" . $cod_item;
            $res = AcessoSQL::Enviar($sock, $consulta);
            $consulta = "insert into Agenda_itens_historicos values (" . $cod_item . ", " . $cod_usuario . ", " . time() . ", 'E')";
            $res = AcessoSQL::Enviar($sock, $consulta);
        }

        AcessoSQL::Desconectar($sock);

        //Para evitar erros no histÃ³rico
        sleep(1);
    }

    /*     * ********************************************************************
      AcabaEdicaoDinamic - Marca no banco de dados o fim da ediÃ§Ã£o do item dado, dinÃ¢micamente
      Entrada: $cod_curso - cÃ³digo do curso, para conectar ao banco de dados
      $cod_item - item ao qual o endereco estarÃ¡ associado
      $cod_usuario - cÃ³digo do usuÃ¡rio que estÃ¡ utilizando
      $acao - 0 se for cancelar a ediÃ§Ã£o,
      1 se for finalizar a ediÃ§Ã£o.

      Saida: XML da funÃ§Ã£o Ajax
     */

    /**
     * Marca no banco de dados o fim da edi��o do item dado, dinamicamente
     * 
     * @param int $cod_curso  C�digo do curso, para conectar ao banco de dados
     * @param int $cod_item item ao qual o endere�o estar� associado
     * @param int $cod_usuario  C�digo do usu�rio que est� utilizando
     * @param int $acao se for cancelar a edi��o, 1 se for finalizar a edi��o.
     * @return xajaxResponse XML da fun��o Ajax
     */
    static function AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, $acao) {

        $objResponse = new xajaxResponse();

        $sock = Conectar($cod_curso);

        // como vou precisar atualizar campos de data, preciso saber a data em UnixTime

        $data = time();

        $consulta = "update Agenda_itens set status='L', data=" . VerificaNumeroQuery($data) . " where cod_item=" . VerificaNumeroQuery($cod_item);
        $res = Enviar($sock, $consulta);

        if ($acao) {
            $consulta = "insert into Agenda_itens_historicos values ('" . VerificaStringQuery($cod_item) . "', '" . VerificaStringQuery($cod_usuario) . "', '" . VerificaStringQuery($data) . "', 'F')";
        } else {
            $consulta = "insert into Agenda_itens_historicos values ('" . VerificaNumeroQuery($cod_item) . "', '" . VerificaStringQuery($cod_usuario) . "', '" . VerificaStringQuery($data) . "', 'D')";
        }

        $res = Enviar($sock, $consulta);

        Desconectar($sock);

        return $objResponse;
    }

    /*     * ********************************************************************
      ExcluirArquivo - Exclui o arquivo, dinamicamente
      Entrada: $numero - numero do arquivo, no item
      $arq - endereco do arquivo no servidor
      $cod_curso - codigo do curso
      $cod_item - codigo do item
      $cod_usuario - codigo do usuario
      $origem - variÃ¡vel que preserva o valor da origem

      Saida: XML da funÃ§Ã£o Ajax
     */

    /**
     * Exclui o arquivo, dinamicamente
     *  
     * @param int $numero numero do arquivo, no item
     * @param string $arq endereco do arquivo no servidor
     * @param int $cod_curso codigo do curso
     * @param int $cod_item codigo do item
     * @param int $cod_usuario codigo do usuario
     * @param string $origem Vari�vel que preserva o valor da origem
     * @return xajaxResponse XML da fun��o Ajax
     */
    static function ExcluirArquivo($numero, $arq, $cod_curso, $cod_item, $cod_usuario, $origem) {

        AbreEdicao($cod_curso, $cod_item, $cod_usuario, $origem);

        $objResponse = new xajaxResponse();

        $arq = htmlspecialchars_decode($arq);

        RemoveDiretorio($arq);

        $objResponse->remove('arq_' . $numero);

        AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

        return $objResponse;
    }

    /*     * ********************************************************************
      SelecionarEntradaDinamic - Oculta os arquivos passados num array, dinamicamente
      Entrada: $nomes_arquivos - array de nomes dos arquivos
      $cod_curso - codigo do curso
      $cod_item - codigo do item
      $cod_usuario - codigo do usuario
      $origem - variÃ¡vel que preserva o valor da origem

      Saida: XML da funÃ§Ã£o Ajax
     */

    /**
     * Oculta os arquivos passados num array, dinamicamente
     * 
     * @param type $nomes_arquivos  Array de nomes dos arquivos
     * @param type $cod_curso C�digo do curso
     * @param type $cod_item C�digo do item
     * @param type $cod_usuario  C�digo do usu�rio
     * @param type $origem Vari�vel que preserva o valor da origem
     * @return xajaxResponse XML da fun��o Ajax
     */
    static function SelecionarEntradaDinamic($nomes_arquivos, $cod_curso, $cod_item, $cod_usuario, $origem) {

        AbreEdicao($cod_curso, $cod_item, $cod_usuario, $origem);

        $objResponse = new xajaxResponse();

        foreach ($nomes_arquivos as $cod => $linha) {
            $nome_arquivo = implode("/", explode("//", $linha[0]));
            if ($linha[1] == 1)
                AlteraStatusArquivo($nome_arquivo, true);
            else
                AlteraStatusArquivo($nome_arquivo, false);
        }

        AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

        $objResponse->script('window.location = "ver_linha.php?cod_curso=' . $cod_curso . '&cod_usuario=' . $cod_usuario . '&cod_ferramenta=1&cod_item=' . $cod_item . '&cod_usuario=' . $cod_usuario . '&origem=' . $origem . '&acao=selecionar_entrada&atualizacao=true"');

        return $objResponse;
    }

    /*     * ********************************************************************
      RetirarEntradaDinamic - Desoculta os arquivos passados num array, dinamicamente
      Entrada: $nomes_arquivos - array de nomes dos arquivos
      $cod_curso - codigo do curso
      $cod_item - codigo do item
      $cod_usuario - codigo do usuario
      $origem - variÃ¡vel que preserva o valor da origem

      Saida: XML da funÃ§Ã£o Ajax
     */

    /**
     * Desoculta os arquivos passados num array, dinamicamente
     * 
     * @param Array $nome_arquivo Array de nomes dos arquivos
     * @param type $cod_curso C�digo do curso
     * @param type $cod_item C�digo do item
     * @param type $cod_usuario C�digo do usu�rio
     * @param type $origem Vari�vel que preserva o valor da origem
     * @return xajaxResponse XML da fun��o Ajax
     */
    static function RetirarEntradaDinamic($nome_arquivo, $cod_curso, $cod_item, $cod_usuario, $origem) {

        AbreEdicao($cod_curso, $cod_item, $cod_usuario, $origem);

        $objResponse = new xajaxResponse();

        AlteraStatusArquivo($nome_arquivo, false);

        AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

        $objResponse->script('window.location = "ver_linha.php?&cod_curso=' . $cod_curso . '&cod_usuario=' . $cod_usuario . '&cod_ferramenta=1&cod_item=' . $cod_item . '&cod_usuario=' . $cod_usuario . '&origem=' . $origem . '&acao=retirar_entrada&atualizacao=true"');

        return $objResponse;
    }
    
    /*     * ********************************************************************
      RetornaUltimaPosicaoHistorico - Retorna a ultima ocorrï¿½ncia do historico de um item dado
      Entrada: $sock - BASE DO CURSO
      $cod_item - item
      Saida: array ['cod_item']
      ['cod_usuario']
      ['data']
      ['acao']
     */

    /**
     * Retorna a ultima ocorr�ncia do hist�rico de um item dado
     * 
     * @param mysqli_connect $sock	Inst�ncia de conex�o do mysql
     * @param int  $cod_item C�digo do Item
     * @return array  Array do tipo: array ['cod_item'], ['cod_usuario'], ['data'], ['acao']
     */
    static function RetornaUltimaPosicaoHistorico($sock, $cod_item) {
        $consulta = "select * from Agenda_itens_historicos where cod_item=" . $cod_item . " order by data desc limit 1";
        $res = AcessoSQL::Enviar($sock, $consulta);
        $linha = AcessoSQL::RetornaLinha($res);
        return($linha);
    }

    /*     * ********************************************************************
      RetornaAgenda - Retorna a agenda dada
      Entrada: $sock - BASE Interna
      $cod_item - codigo do item
      Saida: array ['cod_item'] - codigo do agenda
      ['cod_usuario'] - codigo do usuario
      ['titulo'] - Titulo da agenda
      ['texto'] - Texto
      ['situacao'] - situacao A,N,H
      ['status'] - status E,D,F,H,A,C
     */

    /**
     *  Retorna a agenda dada
     * Saida: array ['cod_item'] - codigo do agenda
     * ['cod_usuario'] - codigo do usuario
     * ['titulo'] - Titulo da agenda
     * ['texto'] - Texto
     * ['situacao'] - situacao A,N,H
     * ['status'] - status E,D,F,H,A,C
     * 
     * @param mysqli_connect $sock	Inst�ncia de conex�o do mysql
     * @param int  $cod_item C�digo do Item
     * @return Array Retorna array indexada 
     */
    static function RetornaAgenda($sock, $cod_item) {
        $query = "select cod_item,cod_usuario,titulo,texto,situacao,status from Agenda_itens where cod_item=" . $cod_item;
        $res = AcessoSQL::Enviar($sock, $query);
        $linha = AcessoSQL::RetornaLinha($res);
        return $linha;
    }

    /*     * ********************************************************************
      AcabaEdicao - Marca no banco de dados o fim da ediÃ§Ã£o do item dado
      Entrada: $sock - conexÃ£o com o BD
      $cod_curso - cÃ³digo do curso, para conectar ao banco de dados
      $cod_item - item ao qual o endereco estarÃ¡ associado
      $cod_usuario - cÃ³digo do usuÃ¡rio que estÃ¡ utilizando
      $acao - 0 se for cancelar a ediÃ§Ã£o,
      1 se for finalizar a ediÃ§Ã£o.

      Saida: sem saÃ­da
     */

    /**
     * Marca no banco de dados o fim da ediÃ§Ã£o do item dado
     * 
     * @param  mysqli_connect	$sock	Inst�ncia de conex�o do mysql
     * @param int $cod_curso - cÃ³digo do curso, para conectar ao banco de dados
     * @param int $cod_item item ao qual o endereco estarÃ¡ associado
     * @param int $cod_usuario cÃ³digo do usuÃ¡rio que estÃ¡ utilizando
     * @param int $acao0 se for cancelar a ediÃ§Ã£o, 1 se for finalizar a ediÃ§Ã£o.
     */
    static function AcabaEdicao($sock, $cod_curso, $cod_item, $cod_usuario, $acao) {

        // como vou precisar atualizar campos de data, preciso saber a data em UnixTime
        $data = time() + 1;

        $consulta = "update Agenda_itens set status='L', data=" . $data . " where cod_item=" . $cod_item;
        $res = AcessoSQL::Enviar($sock, $consulta);

        if ($acao) {
            $consulta = "insert into Agenda_itens_historicos values (" . $cod_item . ", " . $cod_usuario . ", " . $data . ", 'F')";
        } else {
            $consulta = "insert into Agenda_itens_historicos values (" . $cod_item . ", " . $cod_usuario . ", " . $data . ", 'D')";
        }
        $res = AcessoSQL::Enviar($sock, $consulta);
    }

    /*     * ********************************************************************
      CancelaEdicao - Cancela a edicao de um item (termina os diretï¿½rios tb).
      Entrada: $sock - BASE DO CURSO
      $cod_item - codigo do item
      $cod_usuario - usuario cancelando a edicao
      $cod_curso - codigo do curso
      $diretorio_arquivos - diretorio dos arquivos definitivos
      $diretorio_temp - diretorio temporario da edicao
      Saida: true se a ediï¿½ï¿½o foi cancelada corretamente
     */
    
    /**
     * 
     * @param mysqli_connect	$sock	Inst�ncia de conex�o do mysql
     * @param int $cod_item codigo do item
     * @param int $cod_usuario usuario cancelando a edicao
     * @param int $cod_curso codigo do curso
     * @param String $diretorio_arquivos diretorio dos arquivos definitivos
     * @param String $diretorio_temp diretorio temporario da edicao
     * @return boolean true se a ediï¿½ï¿½o foi cancelada corretamente
     */
    static function CancelaEdicao($sock, $cod_item, $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp) {
        if (trim($diretorio_temp) == "" || $cod_item == "" || $cod_curso == "")
            return false;

        $dirtemp = $diretorio_temp . "/tmp/" . $cod_curso . "/agenda/" . $cod_item . "/";
        if (file_exists($dirtemp)) {
            if (!Arquivos::RemoveDiretorio($dirtemp))
                return false;
        }
        $linha = Agenda::RetornaDadosDoItem($sock, $cod_item);
        if ($linha['status'] == "C") {

            $consulta = "delete from Agenda_itens_historicos where cod_item=" . $cod_item;
            $res = AcessoSQL::Enviar($sock, $consulta);

            $consulta = "delete from Agenda_itens where cod_item=" . $cod_item;
            $res = AcessoSQL::Enviar($sock, $consulta);

            return $res;
        }
        if ($linha['status'] == "E") {
            $consulta = "insert into Agenda_itens_historicos values (" . $cod_item . ", " . $cod_usuario . ", " . time() . ", 'D')";
            $res = AcessoSQL::Enviar($sock, $consulta);

            $consulta = "update Agenda_itens set status='L' where cod_item=" . $cod_item;
            $res = AcessoSQL::Enviar($sock, $consulta);

            return $res;
        }
    }

    /*     * ********************************************************************
      RetornaDadosDoItem - Retorna um array com os dados do item
      Entrada: $sock - BASE DO CURSO
      $cod_topico - tï¿½pico
      Saida: array com ['cod_item']
      ['cod_topico']
      ['cod_usuario']
      ['titulo']
      ['texto']
      ['tipo_compartilhamento']
      ['data']
      ['data_ativo']
      ['data_inativo']
      ['posicao_item']
      ['status']
      ['inicio_edicao']
     */

    static function RetornaDadosDoItem($sock, $cod_item) {
        $consulta = "select * from Agenda_itens where cod_item=" . $cod_item;
        $res = AcessoSQL::Enviar($sock, $consulta);
        $linha = AcessoSQL::RetornaLinha($res);
        return($linha);
    }

    /**
     * Usa html_entity_decode numa string e a exibi.
     * 
     * @param String $texto
     */
    static function DecodificaString($texto) {

        $string = html_entity_decode($texto);

        echo json_encode($string);
    }

    /*     * ********************************************************************
      VerificaAnexo - verifica se nome do anexo aprenseta acento ou chars ruins.
      Entrada: $string - com o nome do anexo a ser verificado
      Saida: 1 - nome do anexo esta ok!
      0 - nome do anexo apresenta acento ou chars ruins.
     */

    static function VerificaAnexo($str_anexo) {
        $string = utf8_decode($str_anexo);
        // Palavras acentuadas
        $bad_chars = "����������";
        $bad_chars.= "����";
        $bad_chars.= "����������";
        $bad_chars.= "����������";
        $bad_chars.= "����������";

        // Caracteres estranhos para nome de um arquivo anexo
        $bad_chars.= "�`!@#$%*()+={}[]\\\"';:?/,<>";
        $str_res = strpbrk($string, $bad_chars);

        // Se apresentar algum char de $bad_chars, str_res != ""
        if (($str_res == "") || ($str_res == false))
            return 1;
        else
            return 0;
    }

    /*     * ********************************************************************
      VerificaTexto -Verifica se o texto é composto apenas por caracteres "sem valor" (" ","<br>" ...).
      Entrada: $texto - texto a ser verificado

      Saida: -  O próprio texto ($texto) caso o mesmo contenha caracteres "com valor";
      -  "" caso contrário
     */

    static function VerificaTexto($texto) {

        $text1 = ConversorTexto::Enter2BR($texto);

        $text2 = explode("<br />", $text1);
        foreach ($text2 as $string) {
            $string1 = explode("&nbsp;", $string);
            foreach ($string1 as $string2) {
                $string3 = explode("<br>", $string2);
                foreach ($string3 as $pal) {
                    if ((trim($pal) != "") && (trim($pal) != "<P>") && (trim($pal) != "</P>")) {
                        return $texto;
                    }
                }
            }
        }

        return "";
    }

    /*     * ********************************************************************
      RetornaItensListaAgendas - Retorna array com os itens do t�pico dado
      Entrada: $sock - BASE DO CURSO
      Saida: array com []['cod_item']
      []['cod_usuario']
      []['titulo']
      []['texto']
      []['situacao']
      []['data']
      []['data_ativo']
      []['data_inativo']
      []['status']
      []['inicio_edicao']
     */

    /**
     * Retorna array com os itens do t�pico dado
     * 
     * @param type $sock
     * @return type
     */
    static function RetornaItensListaAgendas($sock) {
        $consulta = "select * from Agenda_itens where situacao='N' OR situacao='A' order by situacao,data";
        $res = AcessoSQL::Enviar($sock, $consulta);
        $lista = AcessoSQL::RetornaArrayLinhas($res);
        return($lista);
    }

    /*     * ********************************************************************
      AtivarAgenda - Ativa a agenda, jogando a agenda anterior no historico
      Entrada: $sock - BASE DO CURSO
      $cod_item - codigo do item
      $cod_usuario - codigo do usuario que est� removendo, para o hist�rico
      Saida: nenhuma
     */
    /**
     * Ativa a agenda, jogando a agenda anterior no historico
     * 
     * @param mysqli_connect	$sock	Inst�ncia de conex�o do mysql
     * @param type $cod_item Codigo do item
     * @param type $cod_usuario Codigo do usuario que est� removendo, para o hist�rico
     */
    static function AtivarAgenda($sock, $cod_item, $cod_usuario) {
        $linha = Agenda::RetornaAgendaAtiva($sock, $cod_item);
        if (isset($linha['cod_item']) && $linha['cod_item'] != "") {
            $consulta = "update Agenda_itens set situacao='H' where cod_item=" . $linha['cod_item'];
            $res = AcessoSQL::Enviar($sock, $consulta);
            $consulta = "insert into Agenda_itens_historicos values (" . $linha['cod_item'] . "," . $cod_usuario . "," . time() . ",'H')";
            $res = AcessoSQL::Enviar($sock, $consulta);
        }
        $consulta = "update Agenda_itens set situacao='A' where cod_item=" . $cod_item;
        $res = AcessoSQL::Enviar($sock, $consulta);
        $consulta = "insert into Agenda_itens_historicos values (" . $cod_item . "," . $cod_usuario . "," . time() . ",'A')";
        $res = AcessoSQL::Enviar($sock, $consulta);

        Usuarios::AtualizaFerramentasNova($sock, 1, 'T');
    }

    /*     * ********************************************************************
      RetornaItensHistorico - Retorna array com os itens no historico
      Entrada: $sock - BASE DO CURSO
      Saida: array com []['cod_item']
      []['cod_usuario']
      []['titulo']
      []['texto']
      []['situacao']
      []['data']
      []['data_ativo']
      []['data_inativo']
      []['status']
      []['inicio_edicao']
     */

    /**
     * Retornas Itens do Hist�rico da Agenda
     * @param mysqli_connect	$sock	Inst�ncia de conex�o do mysql
     * @return Array
     */
    static function RetornaItensHistorico($sock) {
        $consulta = "select * from Agenda_itens where situacao='H' order by data desc";
        $res = AcessoSQL::Enviar($sock, $consulta);
        $lista = AcessoSQL::RetornaArrayLinhas($res);
        return($lista);
    }

    /*     * ********************************************************************
      ApagarItem - Apaga o item selecionado (envia para a lixeira)
      Entrada: $sock - BASE DO CURSO
      $cod_item - codigo do item
      $cod_curso - codigo do curso
      $cod_usuario - codigo do usuario que est� removendo, para o hist�rico
      $diretorio_arquivos - diretorio dos arquivos do TelEduc
      $diretorio_temp - diretorio dos arquivos do TelEduc
      Saida: nenhuma
     */

    /**
     * Apaga o item selecionado (envia para a lixeira)
     * 
     * @param mysqli_connect	$sock	Inst�ncia de conex�o do mysql
     * @param int $cod_item codigo do item
     * @param int $cod_curso codigo do curso
     * @param int $cod_usuario  codigo do usuario que est� removendo, para o hist�rico
     * @param String $diretorio_arquivos  diretorio dos arquivos do TelEduc
     * @param String $diretorio_temp diretorio dos arquivos do TelEduc
     */
    static function ApagarItem($sock, $cod_item, $cod_curso, $cod_usuario, $diretorio_arquivos, $diretorio_temp) {
        /* Exclui registro da agenda do banco de dados */
        $linha = Agenda::RetornaDadosDoItem($sock, $cod_item);
        $consulta = "delete from Agenda_itens where cod_item=" . $cod_item;
        $res = AcessoSQL::Enviar($sock, $consulta);
        $consulta = "delete from Agenda_itens_historicos where cod_item=" . $cod_item;
        $res = AcessoSQL::Enviar($sock, $consulta);

        /* Exclui diretório temporário e diretório de arquivos criados,caso existam */
        if (file_exists($diretorio_temp . "/tmp/" . $cod_curso . "/agenda/" . $cod_item))
            Arquivos::RemoveDiretorio($diretorio_temp . "/tmp/" . $cod_curso . "/agenda/" . $cod_item);
        if (file_exists($diretorio_arquivos . "/" . $cod_curso . "/agenda/" . $cod_item))
            Arquivos::RemoveDiretorio($diretorio_arquivos . "/" . $cod_curso . "/agenda/" . $cod_item);
    }

    /*     * ********************************************************************
      RetornaResHistoricoDoItem - Retorna todas as ocorr�ncias do historico de um item dado
      Entrada: $sock - BASE DO CURSO
      $cod_item - item
      Saida: array []['cod_item']
      []['cod_usuario']
      []['data']
      []['acao']
     */

    static function RetornaResHistoricoDoItem($sock, $cod_item) {
        $consulta = "select * from Agenda_itens_historicos where cod_item=" . $cod_item . " order by data desc,acao desc";
        $res = AcessoSQL::Enviar($sock, $consulta);
        return($res);
    }
    
    /*verificar para q serve cada item desses e arrumar os comentarios.
     /* *********************************************************************
    RetornaAgenda - Retorna a agenda dada
    Entrada: $sock - BASE Interna
    Saida: array ['cod_item'] - codigo do agenda
    ['cod_usuario'] - codigo do usuario
    ['titulo'] - Titulo da agenda
    ['texto'] - Texto
    ['situacao'] - situacao A,N,H
    ['data']
    ['data_ativo']
    ['data_inativo']
    ['status']
    ['inicio_edicao']
    */
    
    static function RetornaAgendaCurso($sock)
    {
    	$query="select * from Agenda_itens";
    	$res=AcessoSQL::Enviar($sock,$query);
    	$linha=AcessoSQL::RetornaArrayLinhas($res);
    	return $linha;
    }
    
    /* *********************************************************************
     ImportarAgenda - Retorna a agenda dada
    Entradas: $cod_usuario
    $cod_curso_destino
    $cod_itens
    $cod_curso_origem
    $diretorio_origem
    $diretorio_destino
    $diretorio_temp
    Saida: booleano - true caso a importacao tenha sido um sucesso
    false caso tenha ocorrido algum erro
    */
    static function ImportarAgenda($cod_usuario, $cod_curso_destino, $cod_itens, $cod_curso_origem, $diretorio_origem, $diretorio_destino, $diretorio_temp)
    {
    	if(trim($diretorio_origem) == "" || trim($diretorio_destino) == "" || trim($diretorio_temp) == "" ||
    	$cod_curso_origem == "" || $cod_itens == "" || $cod_curso_destino == "" )
    		return false;
    
    	foreach($cod_itens as $cod => $cod_item)
    	{
    		$ret = true;
    		/*pega item a ser importado!*/
    		$sock = AcessoSQL::Conectar($cod_curso_origem);
    		$query = "select * from Agenda_itens where cod_item =".ConversorTexto::VerificaNumeroQuery($cod_item);
    		$id = AcessoSQL::Enviar($sock, $query);
    		$item_import = AcessoSQL::RetornaLinha($id);
    		AcessoSQL::Desconectar($sock);
    
    		/*insere item na base de dados do curso destino*/
    		$sock = AcessoSQL::Conectar($cod_curso_destino);
    		/*cria um item vazio na agenda do curso destino!*/
    		$cod_item_dest = Agenda::IniciaCriacao($sock, $cod_usuario, $cod_curso_destino, $diretorio_temp, $item_import['titulo'], $item_import['texto']);
    		AcessoSQL::MudarDB($sock,$cod_curso_origem,$opt);
    
    		/*Copia arquivo do diretorio de origem para o temp do curso destino!*/
    		/* Checa se existem arquivos no diretorio */
    		/*e necessario futuramente fazer uma verificao melhor dos erros, com a utilizacao de codigos de erro. usando
    		 o modelo atual e impossivel determinar qual foi o erro no termino da funcao*/
    		$array_dest=Arquivos::RetornaArrayDiretorio($diretorio_origem."/".$cod_curso_origem."/agenda/".$cod_item);
    		if ($ret && count($array_dest)!=0)
    		{
    			$ret = Arquivos::CriaDiretorio($diretorio_destino."/".$cod_curso_destino."/agenda/".$cod_item_dest."/");
    			if($ret)
    				$ret = Arquivos::CopiaArquivosDiretorio($diretorio_origem."/".$cod_curso_origem."/agenda/".$cod_item."/", $diretorio_destino."/".$cod_curso_destino."/agenda/".$cod_item_dest."/");
    		}
    		/*
    		 if($ret)
    			{
    		MudarDB($sock,$cod_curso_destino);
    		//faz a copia(Importacao) de fato!
    		$ret = AtualizaAgenda($sock, $cod_curso_destino, $cod_item_dest, $item_import['titulo'], $item_import['texto'],
    				'N', $diretorio_destino, $diretorio_temp, $cod_usuario);
    		}
    		else
    			return $ret;
    		*/
    		AcessoSQL::Desconectar($sock);
    	}
    	return $ret;
    }
    
    /**************************************************************************************
     RetornaNumArquivosVisiveis - Retorna o n\FAmero de arquivos visiveis ao usuario
    Entrada: $lista_arq - lista ed arquivos
    Saida: array com lista de arquivos
    ***************************************************************************************/
    static function RetornaNumArquivosVisiveis($lista_arq)
    {
    	$conta_arq=0;
    
    	if (count($lista_arq)>0)
    	{
    		foreach($lista_arq as $cod => $linha){
    			if((!$linha['Status']) &&  ($linha['Arquivo']!="")) {
    				$conta_arq++;
    			}
    		}
    
    	}
    	return($conta_arq);
    }
    
    /************************************************************************************************************
     haArquivosVisiveisDir - verifica se ha arquivo visivel dentro da pasta
    Entrada: $lista_arq_vis - vetor retornado da funcao RetornaArquivosVisiveis
    Saida: Retorna true se existe algum arquivo nao oculto dentro da pasta ou das subpastas, false caso               contrario
    *************************************************************************************************************/
    static function haArquivosVisiveisDir($dir, $lista_arq_vis){
    	foreach($lista_arq_vis as $cod => $linha)
    	{
    		if(!($linha['Status'])){
    			if (($linha['Arquivo']!="") && (strncmp($linha['Diretorio'], $dir, strlen($dir)) == 0)){
    				return true;
    			}
    		}
    	}
    
    	return false;
    }

}

?>