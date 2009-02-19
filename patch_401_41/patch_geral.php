<?php
	include("arquivos/cursos/aplic/bibliotecas/geral.inc");

	// Seleciona todos os cursos da base, para fazer a varredura
	$sock = Conectar("");
	Enviar($sock,"REPLACE INTO Ferramentas VALUES (10,21,22,'batepapo'),(19,35,36,'intermap'),(22,52,53,'avaliacoes'),(6,13,14,'perguntas')");
	

        $a  = Enviar($sock,"SHOW KEYS FROM Menu WHERE Key_name='PRIMARY'");
        if(RetornaNumLinhas($a)>0)
          Enviar($sock,"ALTER TABLE Menu DROP PRIMARY KEY");
        Enviar($sock,"ALTER TABLE Menu ADD PRIMARY KEY(posicao)");
        Enviar($sock,"REPLACE INTO Menu VALUES (22,4),(10,16),(19,25),(6,9)");
        Enviar($sock,"UPDATE Config SET valor='4.1' WHERE item='versao'");


	
	$query = "SELECT cod_curso FROM Cursos ORDER BY curso_inicio";
	$res = Enviar($sock, $query);
	$cursos = RetornaArrayLinhas($res);
	Desconectar($sock);

	if (is_array($cursos))
		foreach($cursos as $curso){
			$sock = Conectar($curso[0]);
#Criando tabelas da ferramenta Avaliação para cada curso


                        Enviar($sock,"CREATE TABLE IF NOT EXISTS `Pergunta_assuntos` (
                                        `cod_assunto` int(11) NOT NULL auto_increment,
                                        `cod_assunto_pai` int(11) default NULL,
                                        `nome` varchar(150) NOT NULL default '',
                                        `descricao` text,
                                        `data` int(11) NOT NULL default '0',
                                        PRIMARY KEY  (`cod_assunto`)
                                      ) ENGINE=MyISAM  DEFAULT CHARSET=latin1;");


                        Enviar($sock,"CREATE TABLE IF NOT EXISTS `Pergunta_itens` (
                                        `cod_pergunta` int(11) NOT NULL auto_increment,
                                        `cod_assunto` int(11) NOT NULL default '0',
                                        `pergunta` mediumtext NOT NULL,
                                        `resposta` mediumtext NOT NULL,
                                        `data` int(11) NOT NULL default '0',
                                        PRIMARY KEY  (`cod_pergunta`)
                                      ) ENGINE=MyISAM DEFAULT CHARSET=latin1;");

			Enviar($sock,"CREATE TABLE IF NOT EXISTS `Avaliacao` (
                                        `cod_avaliacao` int(11) NOT NULL default '0',
                                        `cod_atividade` int(11) NOT NULL default '0',
                                        `cod_usuario` int(11) NOT NULL default '0',
                                        `ferramenta` char(1) NOT NULL default '',
                                        `tipo` char(1) NOT NULL,
                                        `valor` float NOT NULL default '0',
                                        `status` char(1) NOT NULL default '',
                                        `data` int(11) NOT NULL default '0',
                                        `data_inicio` int(11) NOT NULL default '0',
                                        `data_termino` int(11) NOT NULL default '0',
                                        `inicio_edicao` int(11) default NULL,
                                        `criterios` mediumtext,
                                        `objetivos` mediumtext,
                                        PRIMARY KEY  (`cod_avaliacao`)
                                      ) ENGINE=MyISAM DEFAULT CHARSET=latin1;");

			Enviar($sock,"CREATE TABLE IF NOT EXISTS `Avaliacao_expressao` (
                                        `cod_expressao` int(11) NOT NULL auto_increment,
                                        `expressao` mediumtext NOT NULL,
                                        `norma` text,
                                        `tipo_compartilhamento` char(1) NOT NULL default 'A',
                                        `data` int(11) NOT NULL default '0',
                                        PRIMARY KEY  (`cod_expressao`)
                                      ) ENGINE=MyISAM DEFAULT CHARSET=latin1;");

			Enviar($sock,"CREATE TABLE IF NOT EXISTS `Avaliacao_externa` (
                                        `codigo` int(11) NOT NULL auto_increment,
                                        `titulo` varchar(200) NOT NULL,
                                        `objetivo` mediumtext NOT NULL,
                                        `dt_inicio` int(11) NOT NULL,
                                        `dt_termino` int(11) NOT NULL,
                                        `valor` decimal(4,2) NOT NULL,
                                        `status` varchar(1) NOT NULL,
                                        PRIMARY KEY  (`codigo`)
                                      ) ENGINE=MyISAM  DEFAULT CHARSET=latin1;");

			Enviar($sock,"CREATE TABLE IF NOT EXISTS `Avaliacao_historicos` (
                                        `cod_avaliacao` int(11) NOT NULL default '0',
                                        `cod_usuario` int(11) NOT NULL default '0',
                                        `data` int(11) NOT NULL default '0',
                                        `acao` char(1) NOT NULL
                                      ) ENGINE=MyISAM DEFAULT CHARSET=latin1;");

			Enviar($sock,"CREATE TABLE IF NOT EXISTS `Avaliacao_notas` (
                                        `cod_nota` int(11) NOT NULL auto_increment,
                                        `cod_aluno` int(11) NOT NULL default '0',
                                        `cod_grupo` int(11) default '0',
                                        `cod_avaliacao` int(11) NOT NULL default '0',
                                        `cod_formador` int(11) NOT NULL default '0',
                                        `comentario` mediumtext,
                                        `status` char(1) NOT NULL,
                                        `data` int(11) NOT NULL default '0',
                                        `tipo_compartilhamento` char(1) NOT NULL,
                                        `nota` float NOT NULL default '0',
                                        `data_alteracao` int(11) NOT NULL default '0',
                                        PRIMARY KEY  (`cod_nota`,`cod_aluno`,`cod_avaliacao`)
                                      ) ENGINE=MyISAM  DEFAULT CHARSET=latin1;");

			Enviar($sock,"CREATE TABLE IF NOT EXISTS `Avaliacao_notas_sequencia` (
                                        `cod` int(11) NOT NULL auto_increment,
                                        `cod_usuario` int(11) default NULL,
                                        `data` int(11) default NULL,
                                        PRIMARY KEY  (`cod`)
                                      ) ENGINE=MyISAM  DEFAULT CHARSET=latin1;");

			Enviar($sock,"CREATE TABLE IF NOT EXISTS `Avaliacao_sequencia` (
                                        `cod` int(11) NOT NULL auto_increment,
                                        `cod_usuario` int(11) default NULL,
                                        `data` int(11) default NULL,
                                        PRIMARY KEY  (`cod`)
                                      ) ENGINE=MyISAM  DEFAULT CHARSET=latin1;");
										
#Criando tabelas da ferramenta Batepapo para cada curso

			Enviar($sock,"CREATE TABLE IF NOT EXISTS `Batepapo_apelido` (
                                        `cod_sessao` int(11) NOT NULL default '0',
                                        `cod_usuario` int(11) NOT NULL default '0',
                                        `apelido` char(15) NOT NULL default '',
                                        PRIMARY KEY  (`cod_sessao`,`cod_usuario`)
                                      ) ENGINE=MyISAM DEFAULT CHARSET=latin1;");

			Enviar($sock,"CREATE TABLE IF NOT EXISTS `Batepapo_assuntos` (
                                        `cod_assunto` int(11) NOT NULL auto_increment,
                                        `assunto` char(255) NOT NULL default '',
                                        `data_inicio` int(11) NOT NULL default '0',
                                        `data_fim` int(11) default NULL,
                                        PRIMARY KEY  (`cod_assunto`)
                                      ) ENGINE=MyISAM  DEFAULT CHARSET=latin1;");

			Enviar($sock,"CREATE TABLE IF NOT EXISTS `Batepapo_conversa` (
                                        `cod_sessao` int(11) NOT NULL default '0',
                                        `cod_usuario` int(11) NOT NULL default '0',
                                        `cod_usuario_r` int(11) default NULL,
                                        `cod_fala` varchar(20) NOT NULL default '',
                                        `mensagem` text,
                                        `data` int(11) NOT NULL default '0'
                                      ) ENGINE=MyISAM DEFAULT CHARSET=latin1;");

			Enviar($sock,"CREATE TABLE IF NOT EXISTS `Batepapo_fala` (
                                        `cod_fala` int(11) NOT NULL default '0',
                                        `cod_texto_fala` int(11) NOT NULL default '0',
                                        PRIMARY KEY  (`cod_fala`)
                                      ) ENGINE=MyISAM DEFAULT CHARSET=latin1;");

			Enviar($sock,"CREATE TABLE IF NOT EXISTS `Batepapo_online` (
                                        `cod_usuario` int(11) NOT NULL default '0',
                                        `data` int(11) NOT NULL default '0',
                                        PRIMARY KEY  (`cod_usuario`)
                                      ) ENGINE=MyISAM DEFAULT CHARSET=latin1;");

			Enviar($sock,"CREATE TABLE IF NOT EXISTS `Batepapo_sessoes` (
                                        `cod_sessao` int(11) NOT NULL auto_increment,
                                        `data_inicio` int(11) NOT NULL default '0',
                                        `data_fim` int(11) NOT NULL default '0',
                                        `status` char(1) NOT NULL default '',
                                        PRIMARY KEY  (`cod_sessao`)
                                      ) ENGINE=MyISAM  DEFAULT CHARSET=latin1;");
			Desconectar($sock);
		}
?>
