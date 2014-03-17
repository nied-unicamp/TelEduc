/* Base Geral*/

ALTER TABLE Diretorio add PRIMARY KEY(item);
ALTER TABLE Extracao  add PRIMARY KEY(item);

INSERT IGNORE INTO Extracao VALUES ('diretorio', '{Cursos_extraidos.codigo}');
INSERT IGNORE INTO Extracao VALUES ('select_query','select codigo from Cursos_extraidos where codigo = \']codigo[\'');
INSERT IGNORE INTO Extracao VALUES ('compactar','S');
