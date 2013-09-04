/* Base Geral*/

UPDATE `Lingua_textos`  set  `texto` = 
'Editar Questão Objetiva'
where `cod_texto`      = 99 and
      `cod_lingua`     = 1  and
      `cod_ferramenta` = 23;

UPDATE `Lingua_textos`  set  `texto` = 
'Salvar Todas'
where `cod_texto`      = 181 and
      `cod_lingua`     = 1   and
      `cod_ferramenta` = 23;

UPDATE `Lingua_textos`  set  `texto` = 
'Salvar Resposta'
where `cod_texto`      = 182 and
      `cod_lingua`     = 1   and
      `cod_ferramenta` = 23;

DELETE FROM `Lingua_textos` 
where `cod_texto`      > 502 and
      `cod_lingua`     = 1   and
      `cod_ferramenta` = 23;

INSERT IGNORE INTO `Lingua_textos` (`cod_texto`, `cod_lingua`, `cod_ferramenta`, `texto`) VALUES
(24,  1, 23, 'Editar resposta'),
(232, 1, 23, 'Criação de Alternativa'),
(233, 1, 23, 'Não Corrigida');


UPDATE `Lingua_textos`  set  `texto` = 
'Edit Answer'
where `cod_texto`      = 24 and
      `cod_lingua`     = 3  and
      `cod_ferramenta` = 23;

UPDATE `Lingua_textos`  set  `texto` = 
'Save All'
where `cod_texto`      = 181 and
      `cod_lingua`     = 3   and
      `cod_ferramenta` = 23;

UPDATE `Lingua_textos`  set  `texto` = 
'Save Answer'
where `cod_texto`      = 182 and
      `cod_lingua`     = 3   and
      `cod_ferramenta` = 23;

DELETE FROM `Lingua_textos` 
where `cod_texto`      > 231 and
      `cod_lingua`     = 3   and
      `cod_ferramenta` = 23;

INSERT IGNORE INTO `Lingua_textos` (`cod_texto`, `cod_lingua`, `cod_ferramenta`, `texto`) VALUES
(232, 3, 23, 'Creation of Alternative'),
(233, 3, 23, 'Not corrected');

DELETE FROM `Lingua_textos` 
where `cod_texto`      > 1 and
      `cod_lingua`     = 4 and
      `cod_ferramenta` = 23;

INSERT IGNORE INTO `Lingua_textos` (`cod_texto`, `cod_lingua`, `cod_ferramenta`, `texto`) VALUES
(2, 4, 23, 'Existem questoes não corrigidas'),
(3, 4, 23, 'Comentário feito com sucesso!'),
(4, 4, 23, 'Corrigir Exercício'),
(5, 4, 23, 'Voltar'),
(6, 4, 23, 'Compartilhado com Formadores'),
(7, 4, 23, 'Totalmente compartilhado'),
(8, 4, 23, 'Não compartilhado'),
(9, 4, 23, 'Resposta Certa'),
(10, 4, 23, 'Resposta Errada'),
(11, 4, 23, 'Alternativa Certa'),
(12, 4, 23, 'Arquivos'),
(13, 4, 23, 'Título'),
(14, 4, 23, 'Nota'),
(15, 4, 23, 'Valor'),
(16, 4, 23, 'Status'),
(17, 4, 23, 'Enunciado'),
(18, 4, 23, 'Alternativas'),
(19, 4, 23, 'Corrigida'),
(20, 4, 23, 'Resposta'),
(21, 4, 23, 'Ok'),
(22, 4, 23, 'Cancelar'),
(23, 4, 23, 'Comentário do Avaliador'),
(24, 4, 23, 'Editar resposta'),
(25, 4, 23, 'Editar Nota'),
(26, 4, 23, 'Editar Comentário'),
(27, 4, 23, 'Fechar'),
(28, 4, 23, 'Entregar Correção'),
(29, 4, 23, 'Aplicação cancelada com sucesso!'),
(30, 4, 23, 'Exercício aplicado com sucesso'),
(31, 4, 23, 'Exercício reaplicado com sucesso'),
(32, 4, 23, 'Questões incluídas com sucesso'),
(33, 4, 23, 'Título alterado com sucesso.'),
(34, 4, 23, 'O título não pode ser vazio.'),
(35, 4, 23, 'Não há nenhuma questão'),
(36, 4, 23, 'Questões apagadas com sucesso.'),
(37, 4, 23, 'Valores atribuidos com sucesso'),
(38, 4, 23, 'O valor deve ser numérico!'),
(39, 4, 23, 'Realmente deseja apagar o ficheiro?'),
(40, 4, 23, 'Tem a certeza que deseja descompactar este ficheiro?'),
(41, 4, 23, 'Realmente deseja ocultar o ficheiro? Ele não sera visível para alunos.'),
(42, 4, 23, 'Ficheiro(s) ocultado(s) com sucesso.'),
(43, 4, 23, 'Ficheiro apagado com sucesso.'),
(44, 4, 23, 'Ficheiro anexado com sucesso'),
(45, 4, 23, 'Hora de disponibilização inválida. Por favor volte e corrija.'),
(46, 4, 23, 'Hora de limite de entrega inválida. Por favor volte e corrija.'),
(47, 4, 23, 'A disponibilização do exercício deve ser posterior a data atual.'),
(48, 4, 23, 'O limite de entrega deve ser posterior a disponibilização do exercício.'),
(49, 4, 23, 'Editar Exercício'),
(50, 4, 23, 'Renomear título'),
(51, 4, 23, 'Editar texto'),
(52, 4, 23, 'Limpar texto'),
(53, 4, 23, 'Aplicar'),
(54, 4, 23, 'Reaplicar'),
(55, 4, 23, 'Cancelar aplicação'),
(56, 4, 23, 'Histórico'),
(57, 4, 23, 'Compartilhamento'),
(58, 4, 23, 'Texto'),
(59, 4, 23, 'Questões'),
(60, 4, 23, 'Tipo'),
(61, 4, 23, 'Tópico'),
(62, 4, 23, 'Dificuldade'),
(63, 4, 23, 'Total'),
(64, 4, 23, 'Apagar selecionadas'),
(65, 4, 23, 'Atribuir valor'),
(66, 4, 23, 'Adicionar questões'),
(67, 4, 23, 'Nome'),
(68, 4, 23, 'Tamanho'),
(69, 4, 23, 'Data'),
(70, 4, 23, 'Oculto'),
(71, 4, 23, 'Apagar'),
(72, 4, 23, 'Descompactar'),
(73, 4, 23, 'Ocultar'),
(74, 4, 23, 'Área restrita ao formador'),
(75, 4, 23, 'Associar a avaliação'),
(76, 4, 23, 'Sim'),
(77, 4, 23, 'Não'),
(78, 4, 23, 'Disponibilizar gabarito com a correcção'),
(79, 4, 23, 'Tipo de aplicação'),
(80, 4, 23, 'Individual'),
(81, 4, 23, 'Em Grupo'),
(82, 4, 23, 'Disponibilização'),
(83, 4, 23, 'Imediata'),
(84, 4, 23, 'Agendar'),
(85, 4, 23, 'Horário'),
(86, 4, 23, 'Limite de entrega'),
(87, 4, 23, 'Realmente deseja limpar o gabarito? O conteúdo será perdido.'),
(88, 4, 23, 'O título não pode ser vazio.'),
(89, 4, 23, 'Tópico criado com sucesso'),
(90, 4, 23, 'Realmente deseja apagar o(s) item(s) selecionado(s)?'),
(91, 4, 23, 'Dificuldade atualizada com sucesso.'),
(92, 4, 23, 'Tópico atualizado com sucesso.'),
(93, 4, 23, 'Tem a certeza que deseja excluir definitivamente as questões?'),
(94, 4, 23, 'Editar enunciado'),
(95, 4, 23, 'Novo topico'),
(96, 4, 23, 'Limpar enunciado'),
(97, 4, 23, 'Editar gabarito'),
(98, 4, 23, 'Limpar gabarito'),
(99, 4, 23, 'Editar Questão Objetiva'),
(100, 4, 23, 'Difícil'),
(101, 4, 23, 'Médio'),
(102, 4, 23, 'Fácil'),
(103, 4, 23, 'Gabarito'),
(104, 4, 23, 'Editar'),
(105, 4, 23, 'Adicionar Alternativa'),
(106, 4, 23, 'Nome do tópico'),
(107, 4, 23, 'Exercícios Individuais Disponíveis'),
(108, 4, 23, 'Exercícios em Grupo Disponíveis'),
(109, 4, 23, 'Exercícios Individuais'),
(110, 4, 23, 'Exercícios em Grupo'),
(111, 4, 23, 'Biblioteca de Exercícios'),
(112, 4, 23, 'Biblioteca de Questões'),
(113, 4, 23, 'Agrupar'),
(114, 4, 23, 'de'),
(115, 4, 23, 'do grupo'),
(116, 4, 23, 'Exercícios não entregues'),
(117, 4, 23, 'Exercícios não corrigidos'),
(118, 4, 23, 'Não há nenhum exercício'),
(119, 4, 23, 'Agrupar por:'),
(120, 4, 23, 'O titulo deve conter apenas números, letras e espacos'),
(121, 4, 23, 'Aplicação cancelada com sucesso.'),
(122, 4, 23, 'Tem a certeza que deseja excluir definitivamente o(s) exercício(s) selecionado(s)?'),
(123, 4, 23, 'Tem a certeza que deseja recuperar os exercícios selecionados?'),
(124, 4, 23, 'Tem a certeza que deseja enviar para lixeira os exercícios selecionados?'),
(125, 4, 23, 'Exercício(s) excluído(s) da lixeira.'),
(126, 4, 23, 'Exercício(s) recuperado(s).'),
(127, 4, 23, 'Exercício(s) enviado(s) para lixeira.'),
(128, 4, 23, 'Lixeira'),
(129, 4, 23, 'Novo exercício'),
(130, 4, 23, 'Situação'),
(131, 4, 23, 'Apagar selecionados'),
(132, 4, 23, 'Cancelar aplicação dos selecionados'),
(133, 4, 23, 'Recuperar selecionados'),
(134, 4, 23, 'Ação'),
(135, 4, 23, 'Usuário'),
(136, 4, 23, 'Criação'),
(137, 4, 23, 'Aplicada'),
(138, 4, 23, 'Cancelada aplicação'),
(139, 4, 23, 'Desconhecida'),
(140, 4, 23, 'Edição Cancelada'),
(141, 4, 23, 'Em Edição'),
(142, 4, 23, 'Edição Finalizada'),
(143, 4, 23, 'Questoes ordenadas.'),
(144, 4, 23, 'Tem a certeza que deseja excluir definitivamente as questões selecionadas?'),
(145, 4, 23, 'Tem a certeza que deseja recuperar as questões selecionadas?'),
(146, 4, 23, 'Tem a certeza que deseja enviar para lixeira as questões selecionadas?'),
(147, 4, 23, 'Questões excluídas da lixeira.'),
(148, 4, 23, 'Questões recuperadas.'),
(149, 4, 23, 'Questões enviadas para a lixeira.'),
(150, 4, 23, 'Voltar à edição do exercício'),
(151, 4, 23, 'Nova questão'),
(152, 4, 23, 'Filtrar'),
(153, 4, 23, 'clique no cabeçalho para ordenar as questões'),
(154, 4, 23, 'Incluir selecionadas em um exercício'),
(155, 4, 23, 'Incluir selecionadas no exercício'),
(156, 4, 23, 'Recuperar selecionadas'),
(157, 4, 23, 'Escolha um exercício:'),
(158, 4, 23, 'Tipo da questão');
INSERT INTO `Lingua_textos` (`cod_texto`, `cod_lingua`, `cod_ferramenta`, `texto`) VALUES
(159, 4, 23, 'Objetiva'),
(160, 4, 23, 'Dissertativa'),
(161, 4, 23, 'Todos'),
(162, 4, 23, 'Todas'),
(163, 4, 23, 'Realmente deseja entregar? Questões não salvas não serão enviadas.'),
(164, 4, 23, 'Todas as respostas foram salvas com sucesso.'),
(165, 4, 23, 'Resolver exercício'),
(166, 4, 23, 'Não respondida'),
(167, 4, 23, 'Respondida'),
(168, 4, 23, 'Comentário'),
(169, 4, 23, 'Ver resolução'),
(170, 4, 23, 'Avaliação'),
(171, 4, 23, 'Resposta gravada'),
(172, 4, 23, 'Desejas excluir os ficheiro(s) selecionado(s) ?'),
(173, 4, 23, 'Escolha um tópico'),
(174, 4, 23, 'Desejas entregar a correcção?'),
(175, 4, 23, 'Texto editado com sucesso!'),
(176, 4, 23, 'Nota final'),
(177, 4, 23, 'questão'),
(178, 4, 23, 'Título do Exercício'),
(179, 4, 23, 'Grupo'),
(180, 4, 23, 'Aluno'),
(181, 4, 23, 'Editar gabarito'),
(182, 4, 23, 'Limpar gabarito'),
(183, 4, 23, 'Esconder'),
(184, 4, 23, 'Uma questão pode conter no máximo 10 alternativas.'),
(185, 4, 23, 'Ficheiro anexado com sucesso.'),
(186, 4, 23, ' (oculto)'),
(187, 4, 23, 'Diretório está vazio.'),
(188, 4, 23, 'Existem questões com valores iguais a 0, Deseja continuar?'),
(189, 4, 23, 'Entregar'),
(190, 4, 23, 'Corrigir Exercício'),
(191, 4, 23, 'Exercício entregue com sucesso!'),
(192, 4, 23, 'Compartilhamento alterado com sucesso.'),
(193, 4, 23, 'Não é possível aplicar um exercício vazio. Adicione ao menos uma questão.'),
(194, 4, 23, 'Não há comentários disponíveis'),
(195, 4, 23, 'Pressione o botão abaixo para selecionar o ficheiro a ser anexado.(ficheiros .ZIP podem ser enviados e descompactados posteriormente)'),
(196, 4, 23, 'Tem a certeza que deseja enviar para a lixeira esse exercício?'),
(197, 4, 23, 'Desejas limpar o texto? O conteúdo será perdido.'),
(198, 4, 23, 'Texto excluído com sucesso!'),
(199, 4, 23, 'Realmente deseja cancelar aplicação dos selecionados?'),
(200, 4, 23, 'Enunciado editado com sucesso'),
(201, 4, 23, 'Exercício criado com sucesso!'),
(202, 4, 23, 'Alternativa adicionada com sucesso!'),
(203, 4, 23, 'Alternativa editada com sucesso!'),
(204, 4, 23, 'Questão criada com sucesso!'),
(205, 4, 23, 'Enunciado excluído com sucesso.'),
(206, 4, 23, 'Nome do anexo com acentos ou caracteres inválidos! Renomeie o ficheiro e tente novamente.'),
(207, 4, 23, 'ja existe(m) alternativa(s) correta(s). Deseja continuar?'),
(208, 4, 23, 'Pressione o botão abaixo para selecionar o ficheiro a ser anexado.(ficheiros .ZIP podem ser enviados e descompactados posteriormente)'),
(209, 4, 23, 'Erro ao apagar ficheiro.'),
(210, 4, 23, 'Ficheiro descompactado com sucesso.'),
(211, 4, 23, 'Erro ao descompactar ficheiro.'),
(212, 4, 23, 'Multipla escolha'),
(213, 4, 23, 'Digite um nome para o tópico'),
(214, 4, 23, 'Editar Questao Multipla Escolha'),
(215, 4, 23, 'Erro ao anexar ficheiro'),
(216, 4, 23, 'Questões filtradas'),
(217, 4, 23, 'ja existe'),
(218, 4, 23, 'Deseja sobrescrevê-lo?'),
(219, 4, 23, 'Ficheiro'),
(220, 4, 23, 'Abrindo a pasta requisitada,aguarde...'),
(221, 4, 23, 'a'),
(222, 4, 23, 'de'),
(223, 4, 23, 'Comentário de Aluno'),
(224, 4, 23, 'Comentário de Formador'),
(225, 4, 23, 'Comentários postados por mim'),
(226, 4, 23, 'Corrigido'),
(227, 4, 23, 'Entregue'),
(228, 4, 23, 'A questão foi aplicada em um exercício, portanto não pode ser editada.'),
(229, 4, 23, 'Disponibilizado'),
(230, 4, 23, 'Agendado'),
(231, 4, 23, 'Em criação'),
(232, 4, 23, 'Criação de Alternativa'),
(233, 4, 23, 'Não Corrigida'),
(234, 4, 23, 'Salvar Todas'),
(235, 4, 23, 'Salvar Resposta');