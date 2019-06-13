-- MYSQL
-- Esta tabela serve para funcionar o exemplo
drop database if exists DB_EXEMPLO_LB;
create database if not exists DB_EXEMPLO_LB;

use DB_EXEMPLO_LB;

drop table if exists contatos;

CREATE TABLE IF NOT EXISTS contatos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(128) NOT NULL,
    telefone VARCHAR(20) DEFAULT NULL,
    email VARCHAR(128) DEFAULT NULL
);