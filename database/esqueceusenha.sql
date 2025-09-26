-- Cria a base de dados se ela não existir, com o conjunto de caracteres recomendado.
CREATE DATABASE IF NOT EXISTS cadastro_site CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Seleciona a base de dados para usar nos comandos seguintes.
USE cadastro_site;

-- Apaga as tabelas na ordem inversa de dependência para evitar erros de chave estrangeira.
-- 'feedbacks' é apagada primeiro porque depende de 'usuario'.
DROP TABLE IF EXISTS feedbacks;
DROP TABLE IF EXISTS prestadores;
DROP TABLE IF EXISTS usuario;

-- --------------------------------------------------------
-- ESTRUTURA DA TABELA: usuario
-- Esta tabela armazena os dados dos utilizadores que se registam para usar o site (clientes).
-- --------------------------------------------------------
CREATE TABLE usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cpf VARCHAR(14) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Adiciona as colunas necessárias para a funcionalidade de "Esqueceu a Senha".
ALTER TABLE `usuario`
ADD COLUMN `reset_token` VARCHAR(255) NULL DEFAULT NULL AFTER `senha`,
ADD COLUMN `reset_token_expires_at` DATETIME NULL DEFAULT NULL AFTER `reset_token`;

-- --------------------------------------------------------
-- ESTRUTURA DA TABELA: feedbacks
-- Armazena as avaliações que os 'usuarios' fazem sobre os 'prestadores'.
-- --------------------------------------------------------
CREATE TABLE feedbacks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nome_prestador VARCHAR(100) NOT NULL,
    profissao VARCHAR(100),
    tipo VARCHAR(50),
    nota INT,
    comentario TEXT,
    data_feedback TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE
);

