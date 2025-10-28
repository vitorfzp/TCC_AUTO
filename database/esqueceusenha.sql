-- Cria a base de dados se ela não existir
CREATE DATABASE IF NOT EXISTS cadastro_site CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Seleciona a base de dados
USE cadastro_site;

-- --- ESTRUTURA DAS TABELAS ---
-- Apaga as tabelas existentes na ordem inversa de dependência
DROP TABLE IF EXISTS feedbacks;
DROP TABLE IF EXISTS prestadores;
DROP TABLE IF EXISTS usuario;


-- --------------------------------------------------------
-- ESTRUTURA DA TABELA: usuario
-- --------------------------------------------------------
CREATE TABLE usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cpf VARCHAR(14) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    reset_token VARCHAR(255) NULL DEFAULT NULL,
    reset_token_expires_at DATETIME NULL DEFAULT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- --------------------------------------------------------
-- ESTRUTURA DA TABELA: prestadores
-- --------------------------------------------------------
CREATE TABLE prestadores (
    cpf VARCHAR(14) PRIMARY KEY NOT NULL,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NULL DEFAULT NULL, 
    profissao VARCHAR(100) NOT NULL,
    arquivo VARCHAR(255),
    mensagem TEXT,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- --------------------------------------------------------
-- ESTRUTURA DA TABELA: feedbacks (***CORRIGIDA***)
-- --------------------------------------------------------
CREATE TABLE feedbacks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    prestador_cpf VARCHAR(14) NOT NULL,
    nota INT,
    comentario TEXT,
    data_feedback TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- Chave estrangeira ligando ao CLIENTE
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE,
    
    -- Chave estrangeira ligando ao PRESTADOR
    FOREIGN KEY (prestador_cpf) REFERENCES prestadores(cpf) ON DELETE CASCADE
);

SELECT * FROM prestadores;
DELETE FROM prestadores WHERE cpf= "";


SELECT * FROM usuario;
DELETE FROM usuario WHERE cpf= "";

SELECT * FROM feedbacks;
DELETE FROM usuario WHERE id= "";