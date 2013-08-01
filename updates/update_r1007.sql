/*Base Geral*/

INSERT INTO `Lingua_textos` (`cod_texto`, `cod_lingua`, `cod_ferramenta`, `texto`) VALUES
(153, 1, 3, 'A avaliação foi incluída com sucesso.'),
(148, 1, 5, 'Leitura(s) importada(s) com sucesso'),
(148, 1, 7, 'Parada(s) obrigatória(s) importada(s) com sucesso'),
(148, 2, 3, 'Actividad importó correctamente.'),
(153, 2, 3, 'La evaluación se ha agregado correctamente'),
(148, 2, 5, 'Lectura(s) importado(s) con éxito'),
(148, 2, 7, 'Parada(s) obligatoria(s) importado(s) con éxito'),
(153, 3, 3, 'Evaluation included successfully'),
(148, 3, 5, 'Reading(s) successfully imported'),
(148, 3, 7, 'Required Stop(s) successfully imported'),
(148, 4, 3, 'Actividade importada com sucesso'),
(153, 4, 3, 'A avaliação foi incluída com sucesso.'),
(148, 4, 5, 'Leitura(s) importada(s) com sucesso'),
(148, 4, 7, 'STOP(s) importado(s) com sucesso');

UPDATE `Lingua_textos`  set  `texto` = 
'Atividade importada com sucesso'
where `cod_texto`      = 148 and
      `cod_lingua`     = 1   and
      `cod_ferramenta` = 3;

UPDATE `Lingua_textos`  set  `texto` = 
'Activity successfully imported'
where `cod_texto`      = 148 and
      `cod_lingua`     = 3   and
      `cod_ferramenta` = 3;

UPDATE `Lingua_textos`  set  `texto` = 
'Esse aluno não fazia parte de nenhum grupo quando a avaliação foi aplicada.'
where `cod_texto`      = 183 and
      `cod_lingua`     = 4   and
      `cod_ferramenta` = 22;

UPDATE `Lingua_textos`  set  `texto` = 
'Criar Avaliação externa'
where `cod_texto`      = 184 and
      `cod_lingua`     = 4   and
      `cod_ferramenta` = 22;

UPDATE `Lingua_textos`  set  `texto` = 
'Avaliação externa individual'
where `cod_texto`      = 185 and
      `cod_lingua`     = 4   and
      `cod_ferramenta` = 22;

UPDATE `Lingua_textos`  set  `texto` = 
'Avaliação externa em grupo'
where `cod_texto`      = 186 and
      `cod_lingua`     = 4   and
      `cod_ferramenta` = 22;

UPDATE `Lingua_textos`  set  `texto` = 
'Avaliação externa'
where `cod_texto`      = 187 and
      `cod_lingua`     = 4   and
      `cod_ferramenta` = 22;

UPDATE `Lingua_textos`  set  `texto` = 
'Você não informou o título da avaliação.'
where `cod_texto`      = 188 and
      `cod_lingua`     = 4   and
      `cod_ferramenta` = 22;

UPDATE `Lingua_textos`  set  `texto` = 
'Não há participantes a serem avaliados no grupo'
where `cod_texto`      = 189 and
      `cod_lingua`     = 4   and
      `cod_ferramenta` = 22;

INSERT IGNORE INTO `Lingua_textos` (`cod_texto`, `cod_lingua`, `cod_ferramenta`, `texto`) VALUES
(232, 3, 22, 'Hide Legend');
