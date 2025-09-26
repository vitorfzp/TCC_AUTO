-- Cria a base de dados se ela não existir, com o conjunto de caracteres recomendado.
CREATE DATABASE IF NOT EXISTS cadastro_site CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Seleciona a base de dados para usar nos comandos seguintes.
USE cadastro_site;

-- --- ESTRUTURA DAS TABELAS ---
-- Apaga as tabelas existentes na ordem inversa de dependência para evitar erros.
DROP TABLE IF EXISTS feedbacks;
DROP TABLE IF EXISTS prestadores;
DROP TABLE IF EXISTS usuario;


-- --------------------------------------------------------
-- ESTRUTURA DA TABELA: usuario
-- Armazena os dados dos clientes que se cadastram para usar o site.
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
-- Armazena os dados dos profissionais que oferecem serviços.
-- --------------------------------------------------------
CREATE TABLE prestadores (
    cpf VARCHAR(14) PRIMARY KEY NOT NULL,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefone VARCHAR(20) NULL DEFAULT NULL, -- Campo para o WhatsApp
    profissao VARCHAR(100) NOT NULL,
    arquivo VARCHAR(255),
    mensagem TEXT,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- --------------------------------------------------------
-- ESTRUTURA DA TABELA: feedbacks
-- Armazena as avaliações que os 'usuarios' fazem sobre os 'prestadores'.
-- --------------------------------------------------------
CREATE TABLE feedbacks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nome_prestador VARCHAR(100) NOT NULL,
    profissao VARCHAR(100),
    nota INT,
    comentario TEXT,
    data_feedback TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE
);