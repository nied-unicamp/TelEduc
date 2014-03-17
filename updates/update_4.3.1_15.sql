/* Base Geral*/

ALTER TABLE Diretorio add PRIMARY KEY(item);
ALTER TABLE Extracao  add PRIMARY KEY(item);

INSERT IGNORE INTO Diretorio VALUES ('Extracao', '');
INSERT IGNORE INTO Diretorio VALUES ('tar', '');
INSERT IGNORE INTO Diretorio VALUES ('mysqldump', '');
INSERT IGNORE INTO Diretorio VALUES ('mimetypes', '');
