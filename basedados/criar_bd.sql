-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 04-Jan-2025 às 02:12
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `felixbuslr24`
--
CREATE DATABASE IF NOT EXISTS `felixbuslr24` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `felixbuslr24`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `alertas`
--

CREATE TABLE IF NOT EXISTS `alertas` (
  `id_alerta` int(11) NOT NULL AUTO_INCREMENT,
  `texto_alerta` varchar(200) NOT NULL,
  `data_emissao` date NOT NULL,
  `id_remetentes` int(11) NOT NULL,
  `tipo` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_alerta`),
  KEY `fk_alertas` (`id_remetentes`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `bilhetes`
--

CREATE TABLE IF NOT EXISTS `bilhetes` (
  `id_bilhete` int(11) NOT NULL AUTO_INCREMENT,
  `preco` double NOT NULL,
  `data` date NOT NULL,
  `hora` time NOT NULL,
  `lugaresComprados` int(11) NOT NULL DEFAULT 0,
  `estado_bilhete` varchar(50) NOT NULL DEFAULT 'Ativo',
  `id_veiculo` int(11) NOT NULL,
  `id_rota` int(11) NOT NULL,
  PRIMARY KEY (`id_bilhete`),
  KEY `fk_veiculo` (`id_veiculo`),
  KEY `fk_rota` (`id_rota`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `bilhetescomprados`
--

CREATE TABLE IF NOT EXISTS `bilhetescomprados` (
  `id_bilheteComprado` int(11) NOT NULL AUTO_INCREMENT,
  `id_bilhete` int(11) NOT NULL,
  `id_utilizador_comprador` int(11) NOT NULL,
  PRIMARY KEY (`id_bilheteComprado`),
  KEY `fk_comprador` (`id_utilizador_comprador`),
  KEY `fk_bilhete` (`id_bilhete`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `rota`
--

CREATE TABLE IF NOT EXISTS `rota` (
  `id_rota` int(11) NOT NULL AUTO_INCREMENT,
  `nome_rota` varchar(100) NOT NULL,
  `origem` varchar(50) NOT NULL,
  `destino` varchar(50) NOT NULL,
  `distancia` int(11) NOT NULL COMMENT 'em kilometros',
  PRIMARY KEY (`id_rota`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `Id_User` int(11) NOT NULL AUTO_INCREMENT,
  `Nome` varchar(30) NOT NULL,
  `Email` varchar(64) NOT NULL,
  `Password` varchar(64) NOT NULL,
  `Autenticacao` varchar(45) NOT NULL DEFAULT 'Pendente',
  `TipoUser` int(11) NOT NULL DEFAULT 1,
  `Estado` varchar(15) NOT NULL DEFAULT 'Offline',
  `Saldo` double NOT NULL DEFAULT 0,
  PRIMARY KEY (`Id_User`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`Id_User`, `Nome`, `Email`, `Password`, `Autenticacao`, `TipoUser`, `Estado`, `Saldo`) VALUES
(1, 'Cliente', 'Cliente@gmail.com', '4983a0ab83ed86e0e7213c8783940193', 'Aceite', 1, 'Offline', 66),
(2, 'Funcionario', 'funcionario@gmail.com', 'cc7a84634199040d54376793842fe035', 'Aceite', 2, 'Offline', 36),
(3, 'admin', 'admin@gmail.com', '21232f297a57a5a743894a0e4a801fc3', 'Aceite', 3, 'Online', 10);

-- --------------------------------------------------------

--
-- Estrutura da tabela `veiculo`
--

CREATE TABLE IF NOT EXISTS `veiculo` (
  `id_veiculo` int(11) NOT NULL AUTO_INCREMENT,
  `capacidade_veiculo` int(11) NOT NULL,
  `nome_veiculo` varchar(40) NOT NULL,
  `matricula` varchar(10) NOT NULL,
  PRIMARY KEY (`id_veiculo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `alertas`
--
ALTER TABLE `alertas`
  ADD CONSTRAINT `fk_alertas` FOREIGN KEY (`id_remetentes`) REFERENCES `users` (`Id_User`);

--
-- Limitadores para a tabela `bilhetes`
--
ALTER TABLE `bilhetes`
  ADD CONSTRAINT `fk_rota` FOREIGN KEY (`id_rota`) REFERENCES `rota` (`id_rota`),
  ADD CONSTRAINT `fk_veiculo` FOREIGN KEY (`id_veiculo`) REFERENCES `veiculo` (`id_veiculo`);

--
-- Limitadores para a tabela `bilhetescomprados`
--
ALTER TABLE `bilhetescomprados`
  ADD CONSTRAINT `fk_bilhete` FOREIGN KEY (`id_bilhete`) REFERENCES `bilhetes` (`id_bilhete`),
  ADD CONSTRAINT `fk_comprador` FOREIGN KEY (`id_utilizador_comprador`) REFERENCES `users` (`Id_User`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
