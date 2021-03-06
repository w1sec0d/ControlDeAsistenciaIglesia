SET FOREIGN_KEY_CHECKS = 0;
CREATE TABLE CULTO(
	ID INT AUTO_INCREMENT,
    PRIMARY KEY(ID),
    SEDE VARCHAR(40) NOT NULL,
	INICIO DATETIME NOT NULL,
    FIN DATETIME NOT NULL,
    CUPOS INT NOT NULL
);
CREATE TABLE USUARIO(
	ID INT,
    PRIMARY KEY(ID),
    NOMBRE VARCHAR(50) NOT NULL,
	TELEFONO VARCHAR(20),
    ROL VARCHAR(20),
    CONTRASENA VARCHAR(25),
    SEDE VARCHAR(50)
);
CREATE TABLE CULTO_USUARIO(
	ID INT AUTO_INCREMENT,
    PRIMARY KEY(ID),
    IDCULTOFK INT NOT NULL,
    FOREIGN KEY(IDCULTOFK) REFERENCES CULTO(ID),
    IDUSUARIOFK INT NOT NULL,
    FOREIGN KEY(IDUSUARIOFK) REFERENCES USUARIO(ID),
    CUPOS INT NOT NULL,
    ASISTENCIA BOOLEAN DEFAULT false
);

INSERT INTO CULTO (`ID`,`SEDE`,`INICIO`,`FIN`,`CUPOS`) VALUES (1,'BRITALIA','2020-10-03 17:00:00','2020-10-03 18:00:00',45);
INSERT INTO CULTO (`ID`,`SEDE`,`INICIO`,`FIN`,`CUPOS`) VALUES (2,'BRITALIA','2020-10-03 18:00:00','2020-10-03 19:00:00',45);
INSERT INTO CULTO (`ID`,`SEDE`,`INICIO`,`FIN`,`CUPOS`) VALUES (3,'BRITALIA','2020-10-04 08:00:00','2020-10-04 09:30:00',45);
INSERT INTO CULTO (`ID`,`SEDE`,`INICIO`,`FIN`,`CUPOS`) VALUES (4,'BRITALIA','2020-10-04 10:30:00','2020-10-04 12:00:00',45);
INSERT INTO CULTO (`ID`,`SEDE`,`INICIO`,`FIN`,`CUPOS`) VALUES (5,'BRITALIA','2020-10-04 17:00:00','2020-10-04 18:00:00',45);

CREATE VIEW CULTOS_CUPOS AS
SELECT ID,SEDE,DAYOFWEEK(INICIO) AS DIA,HOUR(INICIO) AS HORA_INICIO, HOUR(FIN) AS HORA_FIN, CUPOS from CULTO;
SELECT * FROM CULTOS_CUPOS;

DELIMITER //
CREATE PROCEDURE RESTAR_CUPOS(IN CUPOS_RESERVADOS INT,IN ID_CULTO INT, IN SEDE_IGLESIA VARCHAR(40))
BEGIN
	UPDATE CULTO SET CUPOS = CUPOS - CUPOS_RESERVADOS WHERE ID = ID_CULTO AND SEDE = SEDE_IGLESIA;
END //
DELIMITER ;

CREATE VIEW USUARIO_CULTO AS
SELECT ID_CULTO_USUARIO,ID_CULTO,SEDE,HORA_INICIO,HORA_FIN,ID_USUARIO,NOMBRE_USUARIO, TELEFONO_USUARIO,CUPOS,ASISTENCIA,CUPOS_TOTALES FROM (SELECT CU.ID AS ID_CULTO_USUARIO,CU.ASISTENCIA, CU.IDCULTOFK AS ID_CULTO, USUARIO.ID AS ID_USUARIO, USUARIO.NOMBRE AS NOMBRE_USUARIO, USUARIO.TELEFONO AS TELEFONO_USUARIO, CU.CUPOS as CUPOS FROM CULTO_USUARIO as CU INNER JOIN USUARIO ON CU.IDUSUARIOFK = USUARIO.ID) AS SUBCONSULTA INNER JOIN (SELECT ID, SEDE, HOUR(INICIO) AS HORA_INICIO, HOUR(FIN) AS HORA_FIN, CUPOS AS CUPOS_TOTALES FROM CULTO) AS SUBCONSULTA2 ON SUBCONSULTA.ID_CULTO = SUBCONSULTA2.ID;