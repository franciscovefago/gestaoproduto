-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           10.4.21-MariaDB - mariadb.org binary distribution
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              11.3.0.6295
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Copiando estrutura do banco de dados para gerencia_produto
CREATE DATABASE IF NOT EXISTS `gerencia_produto` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `gerencia_produto`;

-- Copiando estrutura para tabela gerencia_produto.categoria_produto
CREATE TABLE IF NOT EXISTS `categoria_produto` (
  `categoria_produto_id` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `categoria_produto_nome` varchar(90) DEFAULT NULL,
  `categoria_produto_imposto` double DEFAULT NULL,
  PRIMARY KEY (`categoria_produto_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela gerencia_produto.categoria_produto: ~3 rows (aproximadamente)
/*!40000 ALTER TABLE `categoria_produto` DISABLE KEYS */;
INSERT INTO `categoria_produto` (`categoria_produto_id`, `categoria_produto_nome`, `categoria_produto_imposto`) VALUES
	(0000000001, 'P', 5),
	(0000000002, 'M', 5.2),
	(0000000003, 'G', 5.6);
/*!40000 ALTER TABLE `categoria_produto` ENABLE KEYS */;

-- Copiando estrutura para tabela gerencia_produto.produto
CREATE TABLE IF NOT EXISTS `produto` (
  `produto_id` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `categoria_produto_id` int(10) unsigned zerofill NOT NULL,
  `produto_nome` varchar(90) DEFAULT NULL,
  `produto_valor` varchar(45) DEFAULT NULL,
  `produto_status` enum('Ativo','Desativado') DEFAULT NULL,
  PRIMARY KEY (`produto_id`),
  KEY `fk_produto_categoria_produto_idx` (`categoria_produto_id`),
  CONSTRAINT `fk_produto_categoria_produto` FOREIGN KEY (`categoria_produto_id`) REFERENCES `categoria_produto` (`categoria_produto_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela gerencia_produto.produto: ~6 rows (aproximadamente)
/*!40000 ALTER TABLE `produto` DISABLE KEYS */;
INSERT INTO `produto` (`produto_id`, `categoria_produto_id`, `produto_nome`, `produto_valor`, `produto_status`) VALUES
	(0000000001, 0000000001, 'Camiseta Preta', '5000', 'Ativo'),
	(0000000002, 0000000002, 'Camiseta Branca', '5000', 'Ativo'),
	(0000000003, 0000000002, 'Camiseta Preta', '5000', 'Ativo'),
	(0000000004, 0000000003, 'Camiseta Preta', '5000', 'Ativo'),
	(0000000005, 0000000001, 'Camiseta Branca', '5000', 'Ativo'),
	(0000000006, 0000000003, 'Camiseta Branca', '5000', 'Ativo');
/*!40000 ALTER TABLE `produto` ENABLE KEYS */;

-- Copiando estrutura para tabela gerencia_produto.usuario
CREATE TABLE IF NOT EXISTS `usuario` (
  `usuario_id` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `usuario_nome` varchar(90) DEFAULT NULL,
  `usuario_email` varchar(100) DEFAULT NULL,
  `usuario_senha` varchar(90) DEFAULT NULL,
  `usuario_ativo` enum('Ativo','Desativado') DEFAULT NULL,
  PRIMARY KEY (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela gerencia_produto.usuario: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` (`usuario_id`, `usuario_nome`, `usuario_email`, `usuario_senha`, `usuario_ativo`) VALUES
	(0000000001, 'padrao', 'adm@gmail.com', 'e044c2d67098492ad1b565adeb790227', 'Ativo');
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;

-- Copiando estrutura para tabela gerencia_produto.venda
CREATE TABLE IF NOT EXISTS `venda` (
  `venda_id` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `venda_data_emitida` datetime DEFAULT NULL,
  `venda_obs` text DEFAULT NULL,
  PRIMARY KEY (`venda_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela gerencia_produto.venda: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `venda` DISABLE KEYS */;
INSERT INTO `venda` (`venda_id`, `venda_data_emitida`, `venda_obs`) VALUES
	(0000000003, '2022-10-02 17:23:55', 'Venda feita no Pix'),
	(0000000004, '2022-10-02 17:25:24', 'Feito entrega a domicilio ');
/*!40000 ALTER TABLE `venda` ENABLE KEYS */;

-- Copiando estrutura para tabela gerencia_produto.venda_produto
CREATE TABLE IF NOT EXISTS `venda_produto` (
  `venda_id` int(10) unsigned zerofill NOT NULL,
  `produto_id` int(10) unsigned zerofill NOT NULL,
  `venda_produto_qtd` int(11) DEFAULT NULL,
  `venda_produto_valor` varchar(45) DEFAULT NULL,
  `venda_produto_percentual` double DEFAULT NULL,
  PRIMARY KEY (`venda_id`,`produto_id`),
  KEY `fk_venda_has_produto_produto1_idx` (`produto_id`),
  KEY `fk_venda_has_produto_venda1_idx` (`venda_id`),
  CONSTRAINT `fk_venda_has_produto_produto1` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`produto_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_venda_has_produto_venda1` FOREIGN KEY (`venda_id`) REFERENCES `venda` (`venda_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela gerencia_produto.venda_produto: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `venda_produto` DISABLE KEYS */;
INSERT INTO `venda_produto` (`venda_id`, `produto_id`, `venda_produto_qtd`, `venda_produto_valor`, `venda_produto_percentual`) VALUES
	(0000000003, 0000000002, 2, '5000', 5.2),
	(0000000003, 0000000003, 3, '5000', 5.2),
	(0000000004, 0000000001, 2, '5000', 5),
	(0000000004, 0000000006, 1, '5000', 5.6);
/*!40000 ALTER TABLE `venda_produto` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
