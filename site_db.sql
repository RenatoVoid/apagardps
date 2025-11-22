-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 22/11/2025 às 20:36
-- Versão do servidor: 9.1.0
-- Versão do PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `site_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `dispositivos_confiaveis`
--

DROP TABLE IF EXISTS `dispositivos_confiaveis`;
CREATE TABLE IF NOT EXISTS `dispositivos_confiaveis` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expira_em` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `dispositivos_confiaveis`
--

INSERT INTO `dispositivos_confiaveis` (`id`, `usuario_id`, `token_hash`, `expira_em`) VALUES
(1, 1, '7d413327fbd2b8475afeaefbf2a09e6af8be43bcfa344d19138c035666d28fcb', '2025-11-29 17:40:52'),
(2, 2, 'b69e9de77cf67cb9a7ab8e37188471ce2390d6e23162861d1a049edb1fe0b66f', '2025-11-29 17:59:55'),
(3, 6, 'c06ecda6567061567c9788b7d8b74da2c9714b1ba96d02a70f8bf9391f568018', '2025-11-29 18:22:35'),
(4, 7, 'f027e9d23be2b5e5abb077eb7eb3892bf1504349b19572fffe01ff26fc2bcba8', '2025-11-29 19:00:13'),
(5, 8, '2ecdf62fb2a5177bd8a9fe01513458632836c04eb60dc04081e0aa5c94033ed9', '2025-11-29 19:09:04'),
(6, 9, '7e9bba42997e9c5b73c00540a436e867d2fd90f6eeddf278a35390e0ca722e2e', '2025-11-29 16:12:58'),
(7, 10, '1253be06d585e9490fc25ed2c3e98d77c26063c0f21756ad043f6f57e7853980', '2025-11-29 17:15:01');

-- --------------------------------------------------------

--
-- Estrutura para tabela `persistent_logins`
--

DROP TABLE IF EXISTS `persistent_logins`;
CREATE TABLE IF NOT EXISTS `persistent_logins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `selector` char(12) NOT NULL,
  `token_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `selector` (`selector`),
  UNIQUE KEY `selector_unique` (`selector`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tokens_2fa`
--

DROP TABLE IF EXISTS `tokens_2fa`;
CREATE TABLE IF NOT EXISTS `tokens_2fa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `codigo` varchar(4) NOT NULL,
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expira_em` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome_completo` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `senha` text,
  `senha_backup` text,
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_admin` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome_completo`, `email`, `senha`, `senha_backup`, `data_criacao`, `is_admin`) VALUES
(10, NULL, 'renatooliveira10482@gmail.com', '$2y$10$xCeU2JsKZc9ME78UJfs33OCKIG9ToqIwiyl1.w2aBr2zDdsImFHb2', 'fmMaYwEgSgQCdXdITueBsMfSkEHFp0yLfQ3qgTQgj3Ri5Ip9e+x4z4+zSb2/J5ol0j+keVw4AhF9Zd1GWNZLiUlNoTAHfEELqr2+laI2UfIx+gp0Gxt47zTdNABAZHQoVyfoZpmjGXusnfhPA4cdDlZkUPta71bYRJ4Fdfu2dNCjgdBH7aHz4PFKs1SdeR/oXVfatDTYi/c+8BdmE2R/eH8wrAzxijxVidLXalCkA6wk5NTb2OQmDEE5XEUvhfTXAKw3uKEfAFq/t2+qDVukDNZoAMgXpteLAVr+G8No6so435i1Hxa6RplLrbd1N/qONP+CqB1xiNHKq98UIxPpZw==', '2025-11-22 17:14:33', 1);

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `persistent_logins`
--
ALTER TABLE `persistent_logins`
  ADD CONSTRAINT `persistent_logins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `tokens_2fa`
--
ALTER TABLE `tokens_2fa`
  ADD CONSTRAINT `tokens_2fa_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
