<?php

/**
 * 
 */
require_once 'usuarios.inc';
require_once 'acesso_sql.inc';

/**
 * Classe Menu do modulo geral
 * @author     TelEduc
 * @copyright  2014 TelEduc
 * @license    http://teleduc.org.br/
 */
class Menu {

    /**
     * Baseado no código do curso, retorna qual é a lingua. 
     * 
     * @param mysqli_connect	$sock	instância de conexão do mysql
     * @param int $cod_curso Número de identificação primaria do curso da tabela Cursos
     * @return String Com o nome da lingua.
     */
    static function RetornaLinguaCurso($sock, $cod_curso) {
        $query = "select cod_lingua from Cursos where cod_curso='" . $cod_curso . "'";
        $res = AcessoSQL::Enviar($sock, $query);
        $linha = AcessoSQL::RetornaLinha($res);
        return $linha[0];
    }

    /*     * ********************************************************************
      RetornaOrdemFerramentas - Retorna a ordem das ferramentas no menu
      Entrada: $sock - BASE EXTERNA
      Saida: array []['cod_ferramenta']= codigo da ferramenta
      []['posicao'] = posicao da ferramenta. -1 se barra
     */

    /**
     * RetornaOrdemFerramentas - Retorna a ordem das ferramentas no menu
     * 
     * @param mysqli_connect	$sock	instância de conexão do mysql
     * @return array Retorna array desde modo: array []['cod_ferramenta']= codigo da ferramenta[]['posicao'] = posicao da ferramenta. -1 se barra
     */
    static function RetornaOrdemFerramentas($sock) {
        $query = "select * from Menu order by posicao";
        $res = AcessoSQL::Enviar($sock, $query);
        $lista = AcessoSQL::RetornaArrayLinhas($res);
        return ($lista);
    }

    /*     * ********************************************************************
      RetornaListaFerramentas - Retorna a lista de ferramentas
      Entrada: $sock - BASE EXTERNA
      Saida: array [$cod_ferramenta]['cod_texto_nome']
      [$cod_ferramenta]['cod_texto_descricao']
      [$cod_ferramenta]['diretorio']
     */

    /**
     * RetornaListaFerramentas - Retorna a lista de ferramentas
     * 
     * @param mysqli_connect	$sock	instância de conexão do mysql
     * @return Array Saida deste modo: array [$cod_ferramenta]['cod_texto_nome'], [$cod_ferramenta]['cod_texto_descricao'],[$cod_ferramenta]['diretorio']
     */
    static function RetornaListaFerramentas($sock) {
        $query = "select * from Ferramentas order by cod_ferramenta";
        $res = AcessoSQL::Enviar($sock, $query);
        $lista = AcessoSQL::RetornaArrayLinhas($res);
        $lista1 = "";
        unset($lista1);
        foreach ($lista as $cod => $linha) {
            $lista1[$linha['cod_ferramenta']]['cod_texto_nome'] = $linha['cod_texto_nome'];
            $lista1[$linha['cod_ferramenta']]['cod_texto_descricao'] = $linha['cod_texto_descricao'];
            $lista1[$linha['cod_ferramenta']]['diretorio'] = $linha['diretorio'];
        }
        return ($lista1);
    }
	
    static function RetornaNomeFerramentaCod($cod_ferramenta){
    	$sock = AcessoSQL::Conectar("");    	
    	$query = "select cod_texto_nome from Ferramentas where cod_ferramenta = " . $cod_ferramenta;
    	$res = AcessoSQL::Enviar($sock, $query);
    	
    	$nome = AcessoSQL::RetornaLinha($res);
    	
    	return $nome[0];
    }
    /*     * ********************************************************************
      RetornaListaTitulos - Retorna a lista de titulos
      Entrada: $sock - BASE EXTERNA
      $cod_lingua - idioma em que deve ser retornado o texto
      Saida: [$cod_ferramenta]

     */

    /**
     * RetornaListaTitulos - Retorna a lista de titulos
     * 
     * @param mysqli_connect	$sock	instância de conexão do mysql
     * @param int $cod_lingua Número de identificação primaria do lingua da tabela Linguas
     * @return Array Retorna array bidimensional indexada pelo codigo da ferramenta e como conteudo seu texto. 
     */
    static function RetornaListaTitulos($sock, $cod_lingua) {
        $query = "select * from Lingua_textos where (cod_lingua = " . $cod_lingua . ") and (cod_texto = 1) order by cod_ferramenta";
        $res = AcessoSQL::Enviar($sock, $query);
        $lista = AcessoSQL::RetornaArrayLinhas($res);
        $lista1 = "";
        unset($lista1);
        foreach ($lista as $cod => $linha) {

            $lista1[$linha['cod_ferramenta']] = $linha['texto'];
        }
        return ($lista1);
    }

    /*     * *********************************************************************
      RetornaConfiguracao - Retorna o conteï¿½do de um item da configuraï¿½ï¿½o
      Entrada: $sock - sock da base externa
      $item - item a ser buscado
      Saida: conteï¿½do
     */

    /**
     * Retorna o conteúdo de um item da configuração
     * 
     * @param mysqli_connect	$sock	instância de conexão do mysql
     * @param string $item Nome do item a ser buscado na tabela Config. 
     * @return string Conteúdo do item pedido.
     */
    static function RetornaConfiguracao($sock, $item) {
        $query = "select valor from Config where item='" . $item . "'";
        $res = AcessoSQL::Enviar($sock, $query);
        $linha = AcessoSQL::RetornaLinha($res);
        return ($linha['valor']);
    }

    /**
     * Retorna diretorio raiz
     * 
     * @param mysqli_connect	$sock	instância de conexão do mysql
     * @return sting  Diretorio raiz
     */
    static function RetornaDiretorio($sock) {
        $query = "select diretorio from Diretorio where item='raiz_www'";
        $res = AcessoSQL::Enviar($sock, $query);
        $linha = AcessoSQL::RetornaLinha($res);
        return $linha[0];
    }

    /*     * ********************************************************************
      RetornaCSSInline - Dado um array de arquivos css, retorna seu conteudo
      inline
     */

    /**
     * Dado um array de arquivos css, retorna seu conteudo inline
     * 
     * @param array $estilos_css  Um array de arquivos css
     * @return string Conteudo dos arquivos.
     */
    static function RetornaCSSInline($estilos_css) {
        $inline = "";

        foreach ($estilos_css as $css) {
            $inline .= file_get_contents($css);
        }

        return $inline;
    }

    /*     * ********************************************************************
      RetornaFerramentasCurso - Retorna a lista de ferramentas com status e
      acessibilidade
      Entrada: $sock - BASE DO CURSO
      Saida: array [$cod_ferramenta]['status'] - D, F, T
      [$cod_ferramenta]['acesso_visitante'] - A ou N
     */

    /**
     *  Retorna a lista de ferramentas com status e acessibilidade
     * @param mysqli_connect	$sock	instância de conexão do mysql
     * @return array Saida no formato: array [$cod_ferramenta]['status'] - D, F, T ; [$cod_ferramenta]['acesso_visitante'] - A ou N 
     */
    static function RetornaFerramentasCurso($sock) {
        $query = "select * from Curso_ferramentas order by cod_ferramenta";
        $res = AcessoSQL::Enviar($sock, $query);
        $lista1 = "";
        unset($lista1);
        if (AcessoSQL::RetornaNumLinhas($res) > 0) {
            $lista = AcessoSQL::RetornaArrayLinhas($res);
            foreach ($lista as $cod => $linha) {
                $lista1[$linha['cod_ferramenta']]['status'] = $linha['status'];
                $lista1[$linha['cod_ferramenta']]['acesso_visitante'] = $linha['acesso_visitante'];
            }
        }
        return ($lista1);
    }

    /*     * ********************************************************************
      RetornaNovidadeFerramentas - retorna um array com as datas das ï¿½ltimas
      atualizaï¿½ï¿½es para o usuï¿½rio dado.
      Entrada: $sock - BASE DO CURSO
      $cod_curso
      $cod_usuario
      Saida: array [$cod_ferramenta] - data ultimo acesso
     */

    /**
     * Retorna um array com as datas das últimas atualizações para o usuário dado.
     * 
     * @param mysqli_connect	$sock	instância de conexão do mysq
     * @param int $cod_curso Número de identificação primaria do curso da tabela Cursos
     * @param int $cod_usuario Número de identificação primaria do usuario da tabela usuario
     * @return Array Saida: array [$cod_ferramenta] - data ultimo acesso
     */
    static function RetornaNovidadeFerramentas($sock, $cod_curso, $cod_usuario) {
        $saida[0] = "0"; /* para ser usado como retorno padrï¿½o */

        /*         * ***************************************************
          Pega sigla que representa o ator
         * *************************************************** */
        $ator = Usuarios::RetornaStatusUsuario($sock, $cod_curso, $cod_usuario);

        if (Usuarios::ECoordenadorMesmo($sock, $cod_curso, $cod_usuario)) {
            /* R - coordenador */
            $ator = 'R';
        }

        $eformador = ($ator == 'F' || $ator == 'R');

        /*         * ***************************************************
          Pega todas as ferramentas ativas do curso
         * *************************************************** */
        $query = "select cod_ferramenta from Curso_ferramentas where status='A' ";
        $res = AcessoSQL::Enviar($sock, $query);
        $lista_ferramentas = AcessoSQL::RetornaArrayLinhas($res);
        /* inicia como 0 a data de atualizaï¿½ï¿½o de cada ferramenta ativa do curso */
        foreach ($lista_ferramentas as $cod => $ferramenta)
            $saida[$ferramenta['cod_ferramenta']] = 0;



        /*         * **********************************************************************************************
          Pega as ferramentas de tratamento comum.
          Ferramentas que sï¿½ tem compartilhamento para atores.
          Nï¿½o tem compartilhamento com usuarios especificos
          e/ou grupos
         * *********************************************************************************************** */
        $query = "select cod_ferramenta, data from Ferramentas_nova where tipo_compartilhamento='" . $ator . "' or tipo_compartilhamento='T' order by cod_ferramenta";
        $res = AcessoSQL::Enviar($sock, $query);
        $lista = AcessoSQL::RetornaArrayLinhas($res);
        /* Atualiza o vetor de saida para cada codigo de ferramenta */
        if (count($lista) > 0) {
            foreach ($lista as $cod => $linha) {

                $saida[$linha['cod_ferramenta']] = $linha['data'];
            }
        }

        /*         * ***********************************************************************************************
          Inicio do cï¿½digo para Ferramentas que possuem
          compartilhamento de baixa granularidade
          (usuarios e/ou grupos)
         * *********************************************************************************************** */
        /*
          9 - Fï¿½rum
         */

        /*         * ****************************************************
          Pega os grupos dos quais o usuï¿½rio faz parte
         * **************************************************** */
        $grupos_usuario = Menu::RetornaGruposDoUsuario($sock, $cod_usuario);

        /*         * ****************************************************
          Pega o maior data de atualizaï¿½ï¿½o para aquele usuï¿½rio ou
          para qualquer um dos grupos aos quais ele pertence
         * **************************************************** */
        $query = "select cod_ferramenta, max(data) as 'data' from Ferramentas_nova_usuarios where (tipo='U' and cod='" . $cod_usuario . "') ";
        /* Faz com que a query busque por todos os grupos dos quais ele faz parte */
        if ((count($grupos_usuario) > 0) && $grupos_usuario != "") {
            $tmp = "(";
            foreach ($grupos_usuario as $cod => $grupo) {
                if ($cod > 0)
                    $tmp .= ", ";
                $tmp .= $grupo['cod_grupo'];
            }
            $tmp .= ")";

            $query .= " or (tipo='G' and cod in " . $tmp . ")";
        }
        $query .= " group by cod_ferramenta";
        $res = AcessoSQL::Enviar($sock, $query);
        $lista = AcessoSQL::RetornaArrayLinhas($res);

        /* atualiza o vetor de saida se a data de atualizaï¿½ï¿½o
          encontrada para o usuario numa dada ferramenta ï¿½ maior
          do que o valor atual (vindo da data de atualizaï¿½ï¿½o pelo tipo do usuario) */
        if ((is_array($lista)) && (count($lista) > 0)) {
            foreach ($lista as $cod => $linha) {

                $saida[$linha['cod_ferramenta']] = max($saida[$linha['cod_ferramenta']], $linha['data']);
            }
        }

        /*         * ***************************************************
          Fim do cï¿½digo para Ferramentas que possuem
          compartilhamento de baixa granularidade
          (usuarios e/ou grupos)
         * **************************************************** */

        /* Correio (11) */
        /* Se tiver alguma mensagem com N, aparece a estrela */
        $query = "select count(*) from Correio_destinos where estado='N' and cod_usuario=" . $cod_usuario;
        $res = AcessoSQL::Enviar($sock, $query);
        $linha = AcessoSQL::RetornaLinha($res);
        if ($linha[0] > 0)
            $saida[11] = time();

        /* Diario (14) */
        if ((isset($eformador)) && ($eformador)) {
            /* Se formador, verifica se hï¿½ itens ou comentï¿½rios novos em qualquer usuï¿½rio */
            /* Itens novos */
            $query = "select max(data) from Diario_itens where status='A'";
            $res = AcessoSQL::Enviar($sock, $query);
            $linha = AcessoSQL::RetornaLinha($res);
            $data_itens = $linha[0];

            $query = "select max(data) from Diario_comentarios where status='A' and cod_comentarist<>" . $cod_usuario;
            $res = AcessoSQL::Enviar($sock, $query);
            $linha = AcessoSQL::RetornaLinha($res);
            $data_comentarios = $linha[0];

            $saida[14] = max($data_comentarios, $data_itens);
        } else {
            /* Se for aluno, checa se hï¿½ comentï¿½rio novo em seu diï¿½rio */
            $query = "select max(Dc.data) from Diario_itens Di, Diario_comentarios Dc where Di.cod_item=Dc.cod_item and Di.cod_usuario=" . $cod_usuario . " and Di.status='A' and Dc.status='A'";
            $res = AcessoSQL::Enviar($sock, $query);
            $linha = AcessoSQL::RetornaLinha($res);
            $saida[14] = $linha[0];
        }

        /* Portfolio e Portfolio de Grupos */

        return $saida;
    }

    /*     * ******************************************************************
      RetornaGruposDoUsuario - Retorna os cod_grupo de todos os grupos nï¿½ apagados
      a que o usuario pertence
      entrada: $sock - sock de conexão
      $cod_usuario - codigo do usuario do qual listar os grupos
     */

    /**
     *  Retorna os cod_grupo de todos os grupos não apagados a que o usuário pertence
     * 
     * @param mysqli_connect	$sock	instância de conexão do mysql
     * @param int $cod_usuario Número de identificação primaria do usuario da tabela usuarios
     * @return Array Retorna array com todos os códigos de grupos não apagados a qual o usuário pertence. 
     */
    static function RetornaGruposDoUsuario($sock, $cod_usuario) {
        $query = "select Grupos_usuario.cod_usuario, Grupos.* from Grupos_usuario right join Grupos ON Grupos_usuario.cod_grupo=Grupos.cod_grupo where Grupos_usuario.cod_usuario=" . $cod_usuario . " and (Grupos.status='A' or Grupos.status='T')";
        $res = AcessoSQL::Enviar($sock, $query);
        $lista_retorno = AcessoSQL::RetornaArrayLinhas($res);
        return ($lista_retorno);
    }

    /*     * ********************************************************************
      RetornaFerramentasMarcadas - Retorna a lista com os cï¿½digos das ferramentas
      marcadas.

      Entrada: $sock - BASE DO CURSO
      Saida: array contendo $cod_ferramenta
     */

    /**
     * Retorna a lista com os cï¿½digos das ferramentas marcadas.
     * 
     * @param mysqli_connect	$sock	instância de conexão do mysql
     * @return Array Saida: array contendo $cod_ferramenta
     */
    static function RetornaFerramentasMarcadas($sock) {
        $ferramentas_marcadas = array();

        $query = "select max(data) 'data' from Ferramentas_destaque";

        $res = AcessoSQL::Enviar($sock, $query);
        $linha = AcessoSQL::RetornaLinha($res);

        $maxdata = $linha['data'];
        if ($maxdata != NULL) {
            $query = "select cod_ferramenta from Ferramentas_destaque where data = " . $maxdata;
            $res = AcessoSQL::Enviar($sock, $query);

            $lista = AcessoSQL::RetornaArrayLinhas($res);
            if ($lista > 0)
                foreach ($lista as $cod => $linha)
                    $ferramentas_marcadas[$linha['cod_ferramenta']] = true;
        }

        return($ferramentas_marcadas);
    }

    /**
     * Exibi echo com link baseado nas informações.
     * 
     * As ferramentas do TelEduc foram agrupadas de acordo com
     * sua funcionalidade. As ferramentas que estao na divisa precisam
     * colocar um traco a mais para representar a divisao. Sao estas:
     * Ferramenta 8 - Mural
     * Ferramenta 11 - Correio
     * Ferramenta 15 - Portfolio
     * Ferramenta 17 - Estrutura do Ambiente
     * Ferramenta 19 - Intermap
     * Ferramenta 22 - Avaliacoes
     * 
     * @param int $cod_curso Número de identificação primaria do usuario da tabela usuarios
     * @param int $cod_ferr Ferramenta Atual - Número de identificação primaria da ferramenta da tabela Ferramentas
     * @param string $nome_ferramenta Nome da ferramenta 
     * @param string $diretorio Diretório
     * @param string $data Data atual
     * @param string $ultimo_acesso String com a data do ultimo acesso. 
     * @param string $style Baseada na descrição do metodo. 
     * @param int $cod_ferramenta Número de identificação primaria da ferramenta da tabela Ferramentas
     * @param int $cod_usuario Número de identificação primaria do usuário da tabela Usuários
     */
    static function ExibeLink($cod_curso, $cod_ferr, $diretorio, $data, $ultimo_acesso, $style, $cod_ferramenta, $cod_usuario) {

        /* As ferramentas do TelEduc foram agrupadas de acordo com
         * sua funcionalidade. As ferramentas que estao na divisa precisam
         * colocar um traco a mais para representar a divisao. Sao estas:
         * Ferramenta 8 - Mural
         * Ferramenta 11 - Correio
         * Ferramenta 15 - Portfolio
         * Ferramenta 17 - Estrutura do Ambiente
         * Ferramenta 19 - Intermap
         * Ferramenta 22 - Avaliacoes
         */
		
    	$diretorio_view = '/views';
    	$diretorio_imgs = '../../../web-content/imgs/';
    	
        /* Verifica se a ferramenta atual é a que esta selecionada */
        if ($cod_ferr == $cod_ferramenta)
            $style .= "Selecionada ";

        /* Verifica se a ferramenta atual esta na divisa */
        if ($cod_ferr == 8 || $cod_ferr == 11 || $cod_ferr == 15 || $cod_ferr == 17 || $cod_ferr == 19 || $cod_ferr == 22)
            $style .= "Divisa ";

        /* Verifica se ha novidades na ferramenta atual */
        $novidades = ($data > $ultimo_acesso);
        if ($novidades)
            $style .= "Novidade ";

        echo("            <li class=\"" . $style . "\">\n");
        echo("              <div>\n");

        if ($cod_ferr == 15)
            echo("                <a class=\"" . $style . "\" href=\"../../" . $diretorio . $diretorio_view ."/ver_" . $diretorio . ".php?cod_curso=" . $cod_curso . "&amp;cod_usuario=" . $cod_usuario . "&amp;cod_ferramenta=" . $cod_ferr . "&amp;exibir=myp\" >");
        else
            echo("                <a class=\"" . $style . "\" href=\"../../" . $diretorio . $diretorio_view. "/" . $diretorio . ".php?cod_curso=" . $cod_curso . "&amp;cod_usuario=" . $cod_usuario . "&amp;cod_ferramenta=" . $cod_ferr . "\">");

        /* Coloca estrela se houver novidade */
        if ($novidades)
            echo("<img src='".$diretorio_imgs."estrelinha.gif' border=\"0\" alt=\"\" />");
        
        $nome_ferramenta = Menu::RetornaNomeFerramentaCod($cod_ferr);

        echo _("msg".$nome_ferramenta."_-4");
        
        echo("                </a>\n");
        echo("              </div>\n");
        echo("            </li>\n");
    }
    
    static function ListaFerramentasMenuTrad(){
    	echo _("msg5_-4");
    	echo _("msg7_-4");
    	echo _("msg9_-4");
    	echo _("msg11_-4");
    	echo _("msg13_-4");
    	echo _("msg15_-4");
    	echo _("msg17_-4");
    	echo _("msg19_-4");
    	echo _("msg21_-4");
    	echo _("msg23_-4");
    	echo _("msg25_-4");
    	echo _("msg27_-4");
    	echo _("msg29_-4");
    	echo _("msg31_-4");
    	echo _("msg3_-4");
    	echo _("msg1_-4");
    	echo _("msg33_-4");
    	echo _("msg35_-4");
    	echo _("msg52_-4");
    	echo _("msg48_-4");
    	echo _("msg54_-4");
    	echo _("msg56_-4");
    	echo _("msg58_-4");
    }

}
