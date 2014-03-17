/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE=NO_AUTO_VALUE_ON_ZERO */;
/*!40101 SET NAMES latin1 */;

--
-- Banco de Dados: `TelEduc4`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `Ajuda`
--

CREATE TABLE IF NOT EXISTS `Ajuda` (
  `cod_ferramenta` int(11) NOT NULL DEFAULT '0',
  `cod_pagina` int(11) NOT NULL DEFAULT '0',
  `cod_lingua` int(11) NOT NULL DEFAULT '0',
  `tipo_usuario` char(1) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `texto` text,
  `nome_pagina` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`cod_ferramenta`,`cod_pagina`,`cod_lingua`,`tipo_usuario`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Batepapo_sessoes_correntes`
--

CREATE TABLE IF NOT EXISTS `Batepapo_sessoes_correntes` (
  `cod_curso` int(11) NOT NULL DEFAULT '0',
  `cod_sessao` int(11) NOT NULL DEFAULT '0',
  `cod_usuario` int(11) NOT NULL DEFAULT '0',
  `apelido` varchar(15) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `cod_usuario_r` int(11) DEFAULT NULL,
  `apelido_r` varchar(15) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `cod_fala` int(11) NOT NULL DEFAULT '0',
  `mensagem` text,
  `data` int(11) NOT NULL DEFAULT '0',
  `fala` varchar(25) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Config`
--

CREATE TABLE IF NOT EXISTS `Config` (
  `item` varchar(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `valor` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Contatos`
--

CREATE TABLE IF NOT EXISTS `Contatos` (
  `nome` varchar(100) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `email` text,
  `status` char(1) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Cursos`
--

CREATE TABLE IF NOT EXISTS `Cursos` (
  `cod_curso` int(11) NOT NULL DEFAULT '0',
  `nome_curso` varchar(128) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `inscricao_inicio` int(11) DEFAULT NULL,
  `inscricao_fim` int(11) DEFAULT NULL,
  `curso_inicio` int(11) DEFAULT NULL,
  `curso_fim` int(11) DEFAULT NULL,
  `informacoes` text,
  `publico_alvo` text,
  `tipo_inscricao` text,
  `num_alunos` int(11) DEFAULT NULL,
  `cod_coordenador` int(11) NOT NULL DEFAULT '0',
  `acesso_visitante` char(1) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `cod_pasta` int(11) DEFAULT NULL,
  `_timestamp` int(11) DEFAULT NULL,
  `cod_lingua` int(11) DEFAULT '1',
  PRIMARY KEY (`cod_curso`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Cursos_compart`
--

CREATE TABLE IF NOT EXISTS `Cursos_compart` (
  `cod_curso` int(11) NOT NULL DEFAULT '0',
  `cod_ferramenta` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cod_curso`,`cod_ferramenta`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Cursos_extraidos`
--

CREATE TABLE IF NOT EXISTS `Cursos_extraidos` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(128) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `categoria` varchar(127) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `curso_inicio` int(11) NOT NULL DEFAULT '0',
  `curso_fim` int(11) NOT NULL DEFAULT '0',
  `versao_teleduc` varchar(10) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `caminho` text NOT NULL,
  `montado` enum('nao','desmontando','montando','sim') NOT NULL DEFAULT 'nao',
  `data_ultimo_acesso` int(11) DEFAULT '0',
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Cursos_extraidos_compart`
--

CREATE TABLE IF NOT EXISTS `Cursos_extraidos_compart` (
  `codigo` int(11) NOT NULL DEFAULT '0',
  `cod_ferramenta` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`codigo`,`cod_ferramenta`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Cursos_extraidos_sequencia`
--

CREATE TABLE IF NOT EXISTS `Cursos_extraidos_sequencia` (
  `cod` int(11) NOT NULL AUTO_INCREMENT,
  `cod_usuario` int(11) DEFAULT NULL,
  `data` int(11) DEFAULT NULL,
  PRIMARY KEY (`cod`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Cursos_pastas`
--

CREATE TABLE IF NOT EXISTS `Cursos_pastas` (
  `cod_pasta` int(11) NOT NULL AUTO_INCREMENT,
  `pasta` char(127) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`cod_pasta`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Cursos_requisicao`
--

CREATE TABLE IF NOT EXISTS `Cursos_requisicao` (
  `cod_curso` int(11) NOT NULL DEFAULT '0',
  `nome_curso` varchar(128) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `duracao` varchar(128) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `informacoes` text,
  `publico_alvo` text,
  `tipo_inscricao` text,
  `num_alunos` int(11) DEFAULT NULL,
  `cod_pasta` int(11) DEFAULT NULL,
  `data` int(11) NOT NULL DEFAULT '0',
  `nome_contato` varchar(128) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `login_contato` varchar(20) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `email_contato` varchar(128) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `instituicao` varchar(128) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  `avaliado` char(1) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'N',
  `nova_categoria` varchar(128) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`cod_curso`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Diretorio`
--

CREATE TABLE IF NOT EXISTS `Diretorio` (
  `item` varchar(20) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `diretorio` text,
  PRIMARY KEY (`item`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Escolaridade`
--

CREATE TABLE IF NOT EXISTS `Escolaridade` (
  `cod_escolaridade` int(11) NOT NULL DEFAULT '0',
  `cod_texto_escolaridade` int(11) DEFAULT NULL,
  PRIMARY KEY (`cod_escolaridade`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `Escolaridade`
--

INSERT INTO `Escolaridade` (`cod_escolaridade`, `cod_texto_escolaridade`) VALUES
(0, 39),
(1, 40),
(2, 41),
(3, 42),
(4, 43),
(5, 44),
(6, 45);

-- --------------------------------------------------------

--
-- Estrutura da tabela `Extracao`
--

CREATE TABLE IF NOT EXISTS `Extracao` (
  `item` varchar(20) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `valor` varchar(200) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`item`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Ferramentas`
--

CREATE TABLE IF NOT EXISTS `Ferramentas` (
  `cod_ferramenta` int(11) NOT NULL DEFAULT '0',
  `cod_texto_nome` int(11) NOT NULL DEFAULT '0',
  `cod_texto_descricao` int(11) NOT NULL DEFAULT '0',
  `diretorio` char(100) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`cod_ferramenta`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `Ferramentas`
--

INSERT INTO `Ferramentas` (`cod_ferramenta`, `cod_texto_nome`, `cod_texto_descricao`, `diretorio`) VALUES
(1, 5, 6, 'agenda'),
(3, 7, 8, 'material'),
(4, 9, 10, 'material'),
(5, 11, 12, 'material'),
(6, 13, 14, 'perguntas'),
(7, 15, 16, 'material'),
(8, 17, 18, 'mural'),
(9, 19, 20, 'forum'),
(10, 21, 22, 'batepapo'),
(11, 23, 24, 'correio'),
(12, 25, 26, 'grupos'),
(13, 27, 28, 'perfil'),
(14, 29, 30, 'diario'),
(15, 31, 32, 'portfolio'),
(16, 3, 4, 'dinamica'),
(17, 1, 2, 'estrutura'),
(18, 33, 34, 'acessos'),
(19, 35, 36, 'intermap'),
(22, 52, 53, 'avaliacoes'),
(23, 48, 49, 'exercicios'),
(24, 54, 55, 'enquete'),
(25, 56, 57, 'autenticacao'),
(30, 58, 59, 'busca');

-- --------------------------------------------------------

--
-- Estrutura da tabela `Instituicao`
--

CREATE TABLE IF NOT EXISTS `Instituicao` (
  `nome` varchar(100) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `informacoes` text,
  `link` varchar(200) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Lingua_textos`
--

CREATE TABLE IF NOT EXISTS `Lingua_textos` (
  `cod_texto` int(11) NOT NULL DEFAULT '0',
  `cod_lingua` int(11) NOT NULL DEFAULT '0',
  `cod_ferramenta` int(11) NOT NULL DEFAULT '0',
  `texto` text,
  PRIMARY KEY (`cod_texto`,`cod_lingua`,`cod_ferramenta`),
  KEY `idx_Lingua_Textos` (`cod_texto`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Menu`
--

CREATE TABLE IF NOT EXISTS `Menu` (
  `cod_ferramenta` int(11) NOT NULL DEFAULT '0',
  `posicao` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`posicao`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `Menu`
--

INSERT INTO `Menu` (`cod_ferramenta`, `posicao`) VALUES
(17, 0),
(22, 4),
(-1, 1),
(16, 2),
(1, 3),
(-1, 5),
(3, 6),
(4, 7),
(5, 8),
(7, 12),
(24, 11),
(-1, 13),
(8, 14),
(9, 15),
(6, 9),
(11, 17),
(-1, 18),
(12, 19),
(13, 20),
(14, 21),
(15, 22),
(-1, 23),
(18, 24),
(30, 26),
(10, 16),
(19, 25),
(23, 10);

-- --------------------------------------------------------

--
-- Estrutura da tabela `Patchs`
--

CREATE TABLE IF NOT EXISTS `Patchs` (
  `cod_patch` int(11) NOT NULL AUTO_INCREMENT,
  `patch` char(128) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`cod_patch`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Usuario`
--

CREATE TABLE IF NOT EXISTS `Usuario` (
  `cod_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(128) NOT NULL DEFAULT '',
  `senha` varchar(20) DEFAULT NULL,
  `nome` varchar(128) NOT NULL DEFAULT '',
  `rg` varchar(11) DEFAULT NULL,
  `email` varchar(128) NOT NULL DEFAULT '',
  `telefone` varchar(20) DEFAULT NULL,
  `endereco` varchar(100) DEFAULT NULL,
  `cidade` varchar(50) DEFAULT NULL,
  `estado` char(2) DEFAULT NULL,
  `pais` varchar(30) DEFAULT NULL,
  `data_nasc` int(11) DEFAULT NULL,
  `sexo` char(1) DEFAULT NULL,
  `local_trab` varchar(40) DEFAULT NULL,
  `profissao` varchar(40) DEFAULT NULL,
  `cod_escolaridade` int(11) DEFAULT NULL,
  `informacoes` text,
  `data_inscricao` int(11) DEFAULT NULL,
  `cod_lingua` int(11) DEFAULT NULL,
  `confirmacao` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`cod_usuario`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Usuario_config`
--

CREATE TABLE IF NOT EXISTS `Usuario_config` (
  `cod_usuario` int(11) NOT NULL,
  `cod_curso` int(11) NOT NULL,
  `notificar_email` char(1) NOT NULL DEFAULT '0',
  `notificar_data` int(11) DEFAULT '0',
  PRIMARY KEY (`cod_usuario`,`cod_curso`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Usuario_curso`
--

CREATE TABLE IF NOT EXISTS `Usuario_curso` (
  `cod_usuario_global` int(11) NOT NULL,
  `cod_usuario` int(11) NOT NULL,
  `cod_curso` int(11) NOT NULL,
  `tipo_usuario` varchar(20) DEFAULT NULL,
  `portfolio` varchar(10) NOT NULL DEFAULT 'ativado',
  `data_inscricao` int(11) DEFAULT NULL,
  PRIMARY KEY (`cod_usuario`,`cod_curso`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Usuario_sequencia`
--

CREATE TABLE IF NOT EXISTS `Usuario_sequencia` (
  `cod` int(11) NOT NULL AUTO_INCREMENT,
  `cod_usuario` int(11) NOT NULL,
  `data` int(11) DEFAULT NULL,
  PRIMARY KEY (`cod`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;