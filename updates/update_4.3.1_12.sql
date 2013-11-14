/* Base Geral*/

INSERT IGNORE INTO `Lingua_textos` (`cod_texto`, `cod_lingua`, `cod_ferramenta`, `texto`) VALUES
(106, 1, 14, 'Seu comentário está vazio. Para não enviá-lo, pressione o botão Cancelar.'),
(106, 2, 14, 'Su comentario está vacío. Para no enviarlo, presione el botón Cancelar.'),
(106, 3, 14, 'Your comment is empty. To not send it, press the Cancel button.'),
(106, 4, 14, 'O teu comentário está vazio. Para não o enviar, pressiones o botão Cancelar.');

UPDATE `Lingua_textos`  set  `texto` = 
'Seu comentário está vazio. Para não enviá-lo, pressione o botão Cancelar.'
where `cod_texto`      = 106 and
      `cod_lingua`     = 1   and
      `cod_ferramenta` = 15;

UPDATE `Lingua_textos`  set  `texto` = 
'O teu comentário está vazio. Para não o enviar, pressiones o botão Cancelar.'
where `cod_texto`      = 106 and
      `cod_lingua`     = 4   and
      `cod_ferramenta` = 15;
