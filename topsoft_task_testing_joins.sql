USE topsoft_task;
SET foreign_key_checks = 1;
SET sql_mode = 'ANSI_QUOTES';

SELECT c.nome, c.cognome, c.email, t.numero_telefono 
FROM clienti c 
INNER JOIN telefoni_clienti t ON c.id_cliente = t.id_cliente ;

SELECT c.nome, c.cognome, p.numero_piva, t.numero_telefono, ca.codice AS codice_ateco, ca.descrizione FROM clienti c 
INNER JOIN telefoni_clienti t ON c.id_cliente = t.id_cliente
INNER JOIN partite_iva p ON c.id_cliente=p.id_cliente 
INNER JOIN piva_ateco pa ON p.id_piva=pa.id_piva 
INNER JOIN codici_ateco ca ON pa.id_ateco=ca.id_ateco 
ORDER BY c.nome;

SELECT c.nome, c.cognome, c.email, c.telefono, 
                 p.numero_piva, ca.codice AS codice_ateco, ca.descrizione
          FROM clienti c
          LEFT JOIN partite_iva p ON c.id_cliente = p.id_cliente
          LEFT JOIN piva_ateco pa ON p.id_piva = pa.id_piva
          LEFT JOIN codici_ateco ca ON pa.id_ateco = ca.id_ateco

SELECT c.nome, c.cognome, c.email, t.telefono, 
                 p.numero_piva, ca.codice AS codice_ateco, ca.descrizione
          FROM clienti c
          INNER JOIN telefoni_clienti t ON c.id_cliente = t.id_cliente
          LEFT JOIN partite_iva p ON c.id_cliente = p.id_cliente
          LEFT JOIN piva_ateco pa ON p.id_piva = pa.id_piva
          LEFT JOIN codici_ateco ca ON pa.id_ateco = ca.id_ateco
 
SELECT c.nome, c.cognome, p.numero_piva, t.numero_telefono, ca.codice AS codice_ateco, ca.descrizione FROM clienti c 
          INNER JOIN telefoni_clienti t ON c.id_cliente = t.id_cliente
          INNER JOIN partite_iva p ON c.id_cliente=p.id_cliente 
          INNER JOIN piva_ateco pa ON p.id_piva=pa.id_piva 
          INNER JOIN codici_ateco ca ON pa.id_ateco=ca.id_ateco 
          WHERE c.nome LIKE ? ORDER BY c.nome

SELECT c.nome, c.cognome, p.numero_piva, t.numero_telefono, ca.codice AS codice_ateco, ca.descrizione FROM clienti c 
          INNER JOIN telefoni_clienti t ON c.id_cliente = t.id_cliente
          INNER JOIN partite_iva p ON c.id_cliente=p.id_cliente 
          INNER JOIN piva_ateco pa ON p.id_piva=pa.id_piva 
          INNER JOIN codici_ateco ca ON pa.id_ateco=ca.id_ateco 
           WHERE c.nome LIKE 'Luca' ORDER BY c.nome
          
ALTER TABLE partite_iva
ADD CONSTRAINT numero_piva NOT NULL;

ALTER TABLE partite_iva COLUMN numero_piva NOT NULL;

ALTER TABLE partite_iva MODIFY numero_piva NOT NULL;

ALTER TABLE partite_iva MODIFY COLUMN numero_piva VARCHAR(15) UNIQUE NOT NULL;

ALTER TABLE partite_iva DROP INDEX numero_piva_2;

ALTER TABLE partite_iva
DROP FOREIGN KEY partite_iva_ibfk_1,
ADD CONSTRAINT partiteiva_clienti
FOREIGN KEY (id_cliente) REFERENCES clienti(id_cliente)
ON DELETE CASCADE;


ALTER TABLE telefoni_clienti
DROP FOREIGN KEY telefoni_clienti_ibfk_1,
ADD CONSTRAINT telefoniclienti_clienti
FOREIGN KEY (id_cliente) REFERENCES clienti(id_cliente)
ON DELETE CASCADE;

INSERT INTO clienti (nome, cognome, email, data_inserimento) VALUES
('Marco', 'Blu', 'marco.blu@example.com', '2024-08-22'),
('Chiara', 'Rossi', 'chiara.rossi@example.com', '2024-09-15');

INSERT INTO telefoni_clienti (id_cliente, numero_telefono) VALUES
(4, '+393491112233'),
(5, '+393482223344');

INSERT INTO partite_iva (id_cliente, numero_piva, denominazione, nome_azienda, data_attivazione) VALUES
(4, 'IT22233344455', 'IT', 'BluSoftware_SRL', '2022-02-15'),
(5, 'IT33344455566', 'IT', 'RossiConsulting_SRL', '2021-07-10');

DELETE FROM partite_iva WHERE id_piva= 5;

SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    IS_NULLABLE,
    COLUMN_TYPE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'topsoft_task'
  AND TABLE_NAME = 'clienti'
  AND IS_NULLABLE = 'NO';

SELECT TABLE_NAME, COLUMN_NAME, IS_NULLABLE, COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'topsoft_task' AND TABLE_NAME = 'clienti' AND IS_NULLABLE = 'NO';


SELECT 
    c.id_cliente,
    c.nome,
    c.cognome,
    c.email,
    t.numero_telefono,
    p.numero_piva,
    p.nome_azienda,
    p.denominazione,
    p.data_attivazione,
    ca.codice AS codice_ateco,
    ca.descrizione AS descrizione_ateco
FROM clienti c
LEFT JOIN telefoni_clienti t ON c.id_cliente = t.id_cliente
LEFT JOIN partite_iva p ON c.id_cliente = p.id_cliente
LEFT JOIN piva_ateco pa ON p.id_piva = pa.id_piva
LEFT JOIN codici_ateco ca ON pa.id_ateco = ca.id_ateco
WHERE c.email = 'alessandro.conti@example.com';



ALTER TABLE clienti
ADD CONSTRAINT check_surname_min_length CHECK (CHAR_LENGTH (cognome) >= 2);



