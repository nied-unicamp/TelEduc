<?php

/**
 * 
 */
require_once "teleduc.inc";

if (!defined("TMPDB"))
	define("TMPDB", "tmp");

/**
 * Classe AcessoSQL do modulo geral
 * @author     TelEduc
 * @copyright  2014 TelEduc
 * @license    http://teleduc.org.br/
 */
class AcessoSQL {

    /**
     * Neste metodo estático é criado uma conexão com o banco de dados baseado no curso selecionado
     * 
     * @param int $cod_curso Número de identificação primaria do usuario da tabela Cursos
     * @param constant $opt Variável opcional, caso esteja setada com um valor especifico presente na Constante TMPDB, o banco usado será um temporario salvo na sessão.
     * @return mysqli_connect Retorna uma instância de conexão do mysql
     * 
     */
    static function Conectar($cod_curso, $opt = NULL) {
        //   global $_SESSION['dbnamecurso'],
        //          $_SESSION['dbtmpnamecurso'];
        // 2Session
        //   $_SESSION['dbnamebase'] = $_SESSION['dbnamebase'];

        if (!isset($_SESSION['dbnamebase']) || !isset($_SESSION['dbtmpnamecurso']))
            AcessoSQL::LeConfig();
        if (!isset($cod_curso) || $cod_curso == "") {
            // Conecta-se ï¿½base temporï¿½ia sem selecionar base de dados.
            if (isset($opt) && ($opt == TMPDB))
                $base = "";
            else
                $base = $_SESSION['dbnamebase']; // Conecta-se ï¿½base do AMBIENTE
        }
        else {
            if (isset($opt) && ($opt == TMPDB))
                $base = $_SESSION['dbtmpnamecurso'] . $cod_curso;
            else
                $base = $_SESSION['dbnamecurso'] . $cod_curso;
        }
        return(AcessoSQL::ConectarDB($base, $opt));
    }
    /**
     * Este metodo estático fecha a conexão recebida.
     * 
     * @param mysqli_connect $sock uma instância de conexão do mysql
     */
    static function Desconectar($sock) {
        if (mysqli_ping($sock))
            if (!mysqli_close($sock))
                die("ERRO : mysqli_close = " . mysqli_errno($sock) . "<br />\n");
    }
    /**
     * Neste metodo estático é criado uma conexão com o banco de dados baseado no nome da base de dados mysql
     * 
     * @param string $base Nome da base de dados a ser usada. 
     * @param constant $opt Variável opcional, caso esteja setada com um valor especifico presente na Constante TMPDB, o banco usado será um temporario salvo na sessão.
     * @return mysqli_connect Retorna uma instância de conexão do mysql
     */
    static function ConectarDB($base, $opt = NULL) {
        //   global $_SESSION['dbhostname'],    $_SESSION['dbport'],    $_SESSION['dbuser'],    $_SESSION['dbpassword'],
        //          $_SESSION['dbtmphost'], $$_SESSION['dbtmpport'], $_SESSION['dbtmpuser'], $_SESSION['dbtmppassword'];

        if (!isset($_SESSION['dbuser']))
            AcessoSQL::LeConfig();

        // Conecta-se ï¿½base temporï¿½ia
        if (isset($opt) && ($opt == TMPDB)) {
            $host = $_SESSION['dbtmphost'];
            $port = $_SESSION['dbtmpport'];
            $user = $_SESSION['dbtmpuser'];
            $password = $_SESSION['dbtmppassword'];
        } else {
            $host = $_SESSION['dbhost'];
            $port = $_SESSION['dbport'];
            $user = $_SESSION['dbuser'];
            $password = $_SESSION['dbpassword'];
        }

        if (!($sock = mysqli_connect($host, $user, $password, $base)))
            die("Erro de SQL: mysqli_connect(): " . mysqli_connect_errno() . "<br />\n");

        return($sock);
    }

    /**
     * Metódo estático que baseado na conexão e em uma query 
     * 
     * @param mysqli_connect $sock instância de conexão do mysql
     * @param string $query Query do mysql
     * @return mysqli_result|boolean For successful SELECT, SHOW, DESCRIBE, or EXPLAIN queries it will return a mysqli_result object.  For other successful queries it will return TRUE. FALSE on failure
     */
    static function Enviar($sock, $query) {
        if (!($ms = mysqli_query($sock, $query))) {
            $enter = "%OD%0A";

            if (eregi("^SELECT", $query))
                $palavra = "pesquisa";
            else
                $palavra = "atualiza&ccedil;&atilde;o";

            $ERRMSG = ereg_replace("\"", "", $ERRMSG = mysql_error());
            $ERRMSG = ereg_replace("'", "", $ERRMSG);

            // Entra no if se for exibir sem a tela.php

            $msgerro = "                      Ocorreu um erro na " . $palavra . " solicitada\n";
            $msgerro .= "                      <br />\n";
            $msgerro .= "                      <b>Erro na consulta : </b>" . $ERRMSG . "<br />\n";
            $msgerro .= "                      <b>Consulta enviada : </b>" . $query . "<br />\n";


            AcessoSQL::ImprimeErroSemTela("de SQL", $msgerro, "S");
        } else
            return($ms);
        //return mysql_query($query,$sock);
    }
    
    /**
     * Neste metodo estático sao verificados se dois arquivos fisicos existem,
     * o .auth e o teleduc.conf, o .auth é quem fornece o caminho para o teleduc.conf, 
     * Qdo os arquivos existem, o teleduc.conf é lido e a partir dele sao criadas sessoes 
     * para serem usadas no futuro com os dados dos bancos. 
     * 
     */
    static function LeConfig() {

        /* Verifica se o arquivo de autorizacao existe, se ele pode ser aberto e se nao esta em branco */
        if (file_exists(".auth"))
            $fp = fopen(".auth", "r");
        else
            die("AcessoSQL::LeConfig(): Diret&oacute;rio sem arquivo de autoriza&ccedil;&atilde;o.");

        if (!$fp)
            die("AcessoSQL::LeConfig(): Diret&oacute;rio sem arquivo de autoriza&ccedil;&atilde;o.");

        $cam = fgets($fp, 256);
        if ($cam == "")
            die("AcessoSQL::LeConfig(): Arquivo de autoriza&ccedil;&atilde;o em branco. Se vocï¿½ estï¿½ vendo isso depois da instalaï¿½ï¿½o do ambiente, talvez tenha esquecido de rodar os scripts configure e configure_end. Reveja o manual.");
        $cam1 = explode("\n", $cam);
        $caminho = $cam1[0];
        fclose($fp);

        print_r($cam);

        /* Verifica se o teleduc.conf existe, e se ele pode ser aberto */
        if (file_exists($caminho . "teleduc.conf"))
            $fp = fopen($caminho . "teleduc.conf", "r");
        else
            die("AcessoSQL::LeConfig(): Arquivo de configura&ccedil;&atilde;o do TelEduc n&atilde;o encontrado.");


        if (!$fp)
            die("AcessoSQL::LeConfig(): Arquivo de configura&ccedil;&atilde;o do TelEduc n&atilde;o encontrado.");

        while (!feof($fp)) {

            $linha = fgets($fp, 256);

            $parte = explode("=", $linha, 2);
            @$conteudo = explode("\n", $parte[1]);

            /* Para os campos criticos, verifica se eles nao estao vazios */
            if ($parte[0] == "dbuser")
                ($conteudo[0] != "") ? $_SESSION['dbuser'] = trim($conteudo[0]) : die("AcessoSQL::LeConfig(): Falta informaï¿½ï¿½o no teleduc.conf");
            if ($parte[0] == "dbpassword")
                ($conteudo[0] != "") ? $_SESSION['dbpassword'] = trim($conteudo[0]) : die("AcessoSQL::LeConfig(): Falta informaï¿½ï¿½o no teleduc.conf");
            if ($parte[0] == "dbnamebase")
                ($conteudo[0] != "") ? $_SESSION['dbnamebase'] = trim($conteudo[0]) : die("AcessoSQL::LeConfig(): Falta informaï¿½ï¿½o no teleduc.conf");
            if ($parte[0] == "dbnamecurso")
                ($conteudo[0] != "") ? $_SESSION['dbnamecurso'] = trim($conteudo[0]) : die("AcessoSQL::LeConfig(): Falta informaï¿½ï¿½o no teleduc.conf");
            if ($parte[0] == "dbhost")
                ($conteudo[0] != "") ? $_SESSION['dbhost'] = trim($conteudo[0]) : die("AcessoSQL::LeConfig(): Falta informaï¿½ï¿½o no teleduc.conf");
            if ($parte[0] == "dbport")
                ($conteudo[0] != "") ? $_SESSION['dbport'] = trim($conteudo[0]) : die("AcessoSQL::LeConfig(): Falta informaï¿½ï¿½o no teleduc.conf");
            if ($parte[0] == "dbtmpuser")
                $_SESSION['dbtmpuser'] = trim($conteudo[0]);
            if ($parte[0] == "dbtmppassword")
                $_SESSION['dbtmppassword'] = trim($conteudo[0]);
            if ($parte[0] == "dbtmpnamecurso")
                $_SESSION['dbtmpnamecurso'] = trim($conteudo[0]);
            if ($parte[0] == "dbtmphost")
                $_SESSION['dbtmphost'] = trim($conteudo[0]);
            if ($parte[0] == "dbtmpport")
                $_SESSION['dbtmpport'] = trim($conteudo[0]);
        }
        fclose($fp);
    }
    /**
     * Metódo estático auxiliar que retorna um array com os dados do numero da linha especificada no parametro.
     * 
     * @param int $id Linha a ser retornada
     * @return mysqli_fetch_array Retorna array com os dados da linha escolhida
     */
    static function RetornaLinha($id) {
        return(mysqli_fetch_array($id));
    }

    /**
     * 
     * O metodo retorna o ID gerado pela consulta em uma tabela com uma coluna com o atributo AUTO_INCREMENT. Se a ultima consulta nao foi um INSERT ou UPDATE ou se a tabela modificada nao tem uma coluna com o atributo AUTO_INCREMENT, esta funcao ira retornar zero.
     * 
     * Executar uma consulta INSERT ou UPDATE usando a funcao LAST_INSERT_ID() irá também modificar o valor retornado pelo metodo.
     * 
     * @param mysqli_connect $sock instância de conexão do mysql
     * @return int Ultimo ID auto increment. 
     */
    static function RetornaUltimoAutoIncrement($sock) {
        return(mysqli_insert_id($sock));
    }

    /**
     * Retorna o numero de linhas do resultado
     * 
     * @param mysqli_result $result Retorno de uma consulta ao banco
     * @return int Número de linhas
     */
    static function RetornaNumLinhas($result) {
        return(mysqli_num_rows($result));
    }

    /**
     * 
     * @param mysqli_result $result Retorno de uma consulta ao banco
     * @param int $result_type O segundo arqumento, que é opcional, resulttype é uma constante indicando qual tipo de matriz deve ser produzido da linha atual do resultado. Os valores possiveis para este parametro sao as constantes MYSQLI_ASSOC, MYSQLI_NUM, ou MYSQLI_BOTH. Por padrao  ira assumir MYSQLI_BOTH para este parametro.
     * @return array Retorna matriz bidimensional indexada por numeros no seu primeiro nivel.
     */
    static function RetornaArrayLinhas($result, $result_type = MYSQLI_BOTH) {
        $num = AcessoSQL::RetornaNumLinhas($result);
        $cont = 0;
        $ar = array();
        while ($num > 0) {
            $num--;
            $ar[$cont] = mysqli_fetch_array($result, $result_type);
            $cont++;
        }
        return($ar);
    }

    /**
     * Metódo estático que retorna numero de campos de uma determinada consulta
     * 
     * @param mysqli_result $result Retorno de uma consulta ao banco
     * @return int numero de campos do resultado da consulta
     */
    static function RetornaNumCampos($result) {
        $num = mysqli_num_fields($result);
        return ($num);
    }

    /**
    * Este metodo estático retorna um vetor com o nome das colunas da consulta enviada como parametro. 
    * 
    * @param mysqli_result $result Retorno de uma consulta ao banco
    * @return array Retorna uma array indexada numericamente com o nome dos campos baseada na consulta do parametro
    */
    static function RetornaCampos($result) {

        $linha = "";
        unset($linha);


        $num = mysqli_num_fields($result);
        $c = 0;
        while ($c < $num) {
            $field = mysqli_fetch_field($result);
            $linha[$c] = $field->name;
            $c++;
        }
        return ($linha);
    }
    
    /**
     * Este metodo estático retorna um array bidimensional indexada por numeros e que
     * possue informacoes especificas de cada coluna da tabela.
     * 
     * Informacoes contidas no retorno a respeito de cada coluna:
     * table, max_length, not_null, primary_key, unique_key,multiple_key
     * numeric, blob, type, unsigned, zerofill
     * 
     * @param mysqli_connect $sock instância de conexão do mysql
     * @param string $tabela Nome da Tabela do banco de dados
     * @return array Retorna array bidimensional indexada por numeros.
     */
    static function RetornaListaCampos($sock, $tabela) {
        $ar = "";
        unset($ar);

        $query = "select * from " . $tabela . " limit 1";

        $id = Enviar($sock, $query);

        $num = mysqli_num_fields($id);
        $i = 0;
        while ($i < $num) {
            $field = mysqli_fetch_field($id);
            $ar[$i] = array("name" => $field->name,
                "table" => $field->table,
                "max_length" => $field->max_length,
                "not_null" => $field->not_null,
                "primary_key" => $field->primary_key,
                "unique_key" => $field->unique_key,
                "multiple_key" => $field->multiple_key,
                "numeric" => $field->numeric,
                "blob" => $field->blob,
                "type" => $field->type,
                "unsigned" => $field->unsigned,
                "zerofill" => $field->zerofill);
            $i++;
        }
        return ($ar);
    }

    /**
     * Este metodo estático altera o banco 
     * de dados da instância de conexão passada para o 
     * escolhido, baseado em um dos dois parametros opcionais.
     * 
     * @param mysqli_connect $sock instância de conexão do mysql
     * @param int $cod_curso Parametro opcional, Número de identificação primaria do usuario da tabela Cursos
     * @param constant $opt Variável opcional, caso esteja setada com um valor especifico presente na Constante TMPDB, o banco usado será um temporario salvo na sessão.
     * @return mysqli_connect Retorna a instância de conexão do mysql do banco relacionado ao curso ou opt
     */
    static function MudarDB($sock, $cod_curso = "", $opt = "") {
        //   global $_SESSION['dbnamecurso'], $_SESSION['dbtmpnamecurso'];
        // 2Session
        //   $_SESSION['dbnamebase'] = $_SESSION['dbnamebase'];
        if ($cod_curso != "") {
            // Conecta-se a base temporï¿½ia
            if (isset($opt) && ($opt == TMPDB))
                $base = $_SESSION['dbtmpnamecurso'] . $cod_curso;
            else
                $base = $_SESSION['dbnamecurso'] . $cod_curso;
        } else
            $base = $_SESSION['dbnamebase'];

        if (!mysqli_select_db($sock, $base))
            die("Erro de SQL: mysql_select_db = " . mysql_error() . "<br />\n");
        else
            return($sock);
    }

    /**
     * Retorna o proximo numero a ser usado nessa base,  baseando-se na tabela e o usuario logado ou passado no parametro opcional  
     * 
     * @param mysqli_connect $sock instância de conexão do mysql
     * @param string $tabela Nome da Tabela do banco de dados
     * @param int $cod_usuario_tmp Parametro Opcional, caso nao passado é utilizado o codigo do usuario logado e se passado é desconsiderado o usuario logado e utlizado o codigo que foi passado como indentificador primario do usuario 
     * @return int Retorna proximo numero da sequencia 
     */
    static function RetornaProximoCodigo($sock, $tabela, $cod_usuario_tmp = '') {
        //   global $cod_usuario;

        if ($cod_usuario_tmp == "") {
            if (isset($_SESSION['cod_usuario']) && !empty($_SESSION['cod_usuario']))
                $cod_usuario_tmp = $_SESSION['cod_usuario'];
            else
                $cod_usuario_tmp = 0;
        }

        $data = time();
        $query = "insert into " . $tabela . "_sequencia (cod_usuario,data) values (" . $cod_usuario_tmp . "," . $data . ")";

        if (!($res = mysqli_query($sock, $query))) {
            /* Criar tabela e repetir */
            $query = "CREATE TABLE " . $tabela . "_sequencia (cod int PRIMARY KEY AUTO_INCREMENT,cod_usuario INT,data INT)";
            $res = AcessoSQL::Enviar($sock, $query);
            $query = "insert into " . $tabela . "_sequencia (cod_usuario,data) values (" . $cod_usuario_tmp . "," . $data . ")";
            $res = AcessoSQL::Enviar($sock, $query);
            $query = "select cod from " . $tabela . "_sequencia where cod_usuario=" . $cod_usuario_tmp . " and data=" . $data;
            $res = AcessoSQL::Enviar($sock, $query);
            $linha = AcessoSQL::RetornaLinha($res);
            $query = "delete from " . $tabela . "_sequencia where cod_usuario=" . $cod_usuario_tmp . " and data=" . $data;
            $res = AcessoSQL::Enviar($sock, $query);
            return ($linha[0]);
        } else {
            $query = "select cod from " . $tabela . "_sequencia where cod_usuario=" . $cod_usuario_tmp . " and data=" . $data;
            $res = AcessoSQL::Enviar($sock, $query);
            $linha = AcessoSQL::RetornaLinha($res);
            $query = "delete from " . $tabela . "_sequencia where cod_usuario=" . $cod_usuario_tmp . " and data=" . $data;
            $res = AcessoSQL::Enviar($sock, $query);
            return ($linha[0]);
        }
    }
	
	/**************************************************************************
	 EnviaEmailComErro - envia um e-mail com os dados do erro para o e-mail do
		suporte
	*/
	static function EnviaEmailComErro($erro, $cod){
	
		$server_remetente = $_SERVER['SERVER_NAME'];
		$headers = "From: <teleduc@".$server_remetente.">\n";
		$headers.= "X-Mailer: PHP/".phpversion()."\n";
		$headers .= "MIME-Version: 1.0\n";
		$headers.= "Content-Type: text/html; charset=ISO-8859-1\n";
		$headers.= "Content-Transfer-Encoding: 7bit\n";
	
		$destinatario = "Suporte TelEduc <camila.kna@gmail.com>"; //TODO
	
		$assunto = "[TelEduc Error] ".$cod;
	
		$mensagem = "<html>\n";
		$mensagem.= "\t<head>\n";
		$mensagem.= "\t\t<title>".$assunto."</title>\n";
		$mensagem.= "\t</head>\n";
		$mensagem.= "\t<body>\n";
		$mensagem.= $erro;
		$mensagem.= "\t\t<strong>Referer:</strong> ".@$_SERVER['HTTP_REFERER']."<br />";
		$mensagem.= "\t\t<strong>Server:</strong> ".$_SERVER['SERVER_NAME']."<br />";
		$mensagem.= "\t\t<strong>URL:</strong> ".$_SERVER['REQUEST_URI']."<br />";
		$mensagem.= "\t</body>\n";
		$mensagem.= "</html>\n";
	
		return mail($destinatario, $assunto, $mensagem, $headers);
	
	}
	
	/* **********************************************************************
	 ImprimeErroSemTela - Para imprimir erros em que tela.php
	(menu, header, etc..) nÃ£o pode ser carregado.
	*/
        
        /**
         * Para imprimir erros em que tela.php
         * (menu, header, etc..) não pode ser carregado.
         * 
         * @param String $tipo_erro
         * @param String $erro
         * @param String $prefixo
         */
	function ImprimeErroSemTela($tipo_erro, $erro, $prefixo)
	{
		$cod = rand(0, 268435454);
		$cod_hex = dechex($cod);
		$cod_fim = strtoupper($prefixo.str_pad($cod_hex, 7, "0", STR_PAD_LEFT));
	
		/* Obtï¿½ a raiz_www */
		$sock = AcessoSQL::Conectar("");
		$query = "select diretorio from Diretorio where item = 'raiz_www'";
		$res = AcessoSQL::Enviar($sock,$query);
		$linha = AcessoSQL::RetornaLinha($res);
		$raiz_www = $linha[0];
		AcessoSQL::Desconectar($sock);
	
		$caminho_jscss = $raiz_www."/web-content/js-css";
	
		echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
		echo("<html lang=\"pt\">\n");
		echo("  <head>\n");
		echo("    <title>TelEduc - Erro</title>\n");
		echo("    <link href=\"".$caminho_jscss."/ambiente.css\" rel=\"stylesheet\" type=\"text/css\" />\n");
		echo("    <script type=\"text/javascript\">\n");
		echo("      function Iniciar(){\n");
		echo("        document.getElementById('detalhes').style.display='none';\n");
		echo("      }\n");
		echo("      var status='none';\n");
		echo("      function changeStatus(){\n");
		echo("        if(status=='') status = 'none';\n");
		echo("        else status = '';\n");
		echo("        document.getElementById('detalhes').style.display=status;\n");
		echo("      }\n");
		echo("    </script>\n");
		echo("  </head>\n");
		echo("  <body onload=\"Iniciar();\">\n");
		echo("    <br />\n");
		echo("    <table id=\"container\" style=\"width:700px;\">");
		echo("      <tr>\n");
		echo("        <td colspan=\"2\" id=\"conteudo\" valign=\"top\">\n");
		echo("          <h4>Erro ".$tipo_erro."</h4>\n");
		echo("          <br />\n");
		echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
		echo("            <tr>\n");
		echo("              <td>\n");
		echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
		echo("                  <tr class=\"head\">\n");
		echo("                    <td width=\"32%\" class=\"alLeft\">\n");
		echo("                      Ocorreu um Erro\n");
		echo("                    </td>\n");
		echo("                  </tr>\n");
		echo("                  <tr>\n");
		echo("                    <td>\n");
		echo("                      <p>Infelizmente o TelEduc comportou-se de forma inesperada. Houve um erro ".$tipo_erro." que impediu que a navega&ccedil;&atilde;o prosseguisse.</p>\n");
		echo("                      <p>As informa&ccedil;&otilde;es foram enviadas automaticamente &agrave; Equipe do TelEduc para que as provid&ecirc;ncias necess&aacute;rias sejam tomadas.</p>\n");
		echo("                      <p>Caso queira saber mais detalhes sobre o erro, entre em contato com a Equipe do TelEduc e informe o c&oacute;digo <strong>".$cod_fim."</strong>.</p>\n");
		echo("                    </td>\n");
		echo("                  </tr>\n");
		echo("                  <tr>\n");
		echo("                    <td style=\"text-align:right;\">\n");
		echo("                      <span class=\"link\" onclick=\"changeStatus();\">+ Detalhes do Erro</span>\n");
		echo("                    </td>\n");
		echo("                  </tr>\n");
		echo("                  <tr id=\"detalhes\">\n");
		echo("                    <td class=\"alLeft\" style=\"padding: 10px 50px 10px 50px;\">\n");
		echo("                      ".$erro."\n");
		echo("                    </td>\n");
		echo("                  </tr>\n");
		echo("                </table>\n");
		echo("              </td>\n");
		echo("            </tr>\n");
		echo("          </table>\n");
		echo("          <span class=\"btsNavBottom\">\n");
		echo("            <a href=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></a>\n");
		echo("          </span>\n");
		echo("        </td>\n");
		echo("      </tr>\n");
		echo("      <tr>\n");
		echo("        <td valign=\"bottom\" height=\"80\"><a href=\"http://www.nied.unicamp.br\"><img src=\"../imgs/logoNied.gif\" alt=\"nied\" border=\"0\" style=\"margin-right:8px;\" /></a><a href=\"http://www.ic.unicamp.br\"><img src=\"../imgs/logoInstComp.gif\" alt=\"Instituto de Computa&ccedil;&atilde;o\" border=\"0\" style=\"margin-right:6px;\" /></a><a href=\"http://www.unicamp.br\"><img src=\"../imgs/logoUnicamp.gif\" alt=\"UNICAMP\" border=\"0\" /></a></td>\n");
		echo("        <td valign=\"bottom\" id=\"rodape\">2006  - TelEduc - Todos os direitos reservados. All rights reserved - NIED - UNICAMP</td>\n");
		echo("      </tr>\n");
		echo("    </table>\n");
		echo("  </body>");
		echo("</html>");
	
		AcessoSQL::EnviaEmailComErro($erro, $cod_fim);
	
		exit();
	
	}
}
?>
