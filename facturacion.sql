-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-06-2021 a las 04:55:07
-- Versión del servidor: 10.3.15-MariaDB
-- Versión de PHP: 7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `facturacion`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizar_precio_producto` (IN `n_cantidad` INT, IN `n_precio` DECIMAL(10,2), IN `codigo` INT)  BEGIN
    	DECLARE nueva_existencia int;
        DECLARE nuevo_total  decimal(10,2);
        DECLARE nuevo_precio decimal(10,2);
        
        DECLARE cant_actual int;
        DECLARE pre_actual decimal(10,2);
        
        DECLARE actual_existencia int;
        DECLARE actual_precio decimal(10,2);
                
        SELECT precio_product, cant_product INTO actual_precio,actual_existencia FROM producto WHERE cod_product = codigo;

        SET nueva_existencia = actual_existencia + n_cantidad;
        SET nuevo_total = (actual_existencia * actual_precio) + (n_cantidad * n_precio);
        SET nuevo_precio = nuevo_total / nueva_existencia;
        
        UPDATE producto SET cant_product  = nueva_existencia, precio_product = nuevo_precio WHERE cod_product = codigo;
        
        SELECT nueva_existencia,nuevo_precio;
        
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `add_detalle_temp` (IN `codigo` INT, IN `cantidad` INT, IN `token_user` VARCHAR(50))  BEGIN 
    
     DECLARE precio_actual decimal(10,2);
     SELECT precio_product INTO precio_actual FROM producto WHERE cod_product = codigo;
     
     INSERT INTO detalle_temp(token_user, cod_product, cantidad, precio_venta) VALUES(token_user, codigo, cantidad, precio_actual);
     
     SELECT tmp.correlativo, tmp.cod_product, p.nombre, tmp.cantidad, tmp.precio_venta FROM detalle_temp tmp
     INNER JOIN producto P
     ON tmp.cod_product = p.cod_product
     WHERE tmp.token_user = token_user;
     
     END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `anular_factura` (`no_factura` INT)  BEGIN
    	DECLARE existe_factura int;
        DECLARE registros int;
        DECLARE a int;
        
        DECLARE cod_producto int;
        DECLARE cant_producto int;
        DECLARE existencia_actual int;
        DECLARE nueva_existencia int;
        
        SET existe_factura = (SELECT COUNT(*) FROM factura WHERE nofactura = no_factura and estado = 1);
        
        if existe_factura > 0 THEN
        	CREATE TEMPORARY TABLE tbl_tmp (
                id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                cod_prod BIGINT,
                cant_prod int);
                
                SET a = 1;
                
                SET registros = (SELECT COUNT(*) FROM detallefactura WHERE nofactura = no_factura);
                
                IF registros > 0 THEN
                	INSERT INTO tbl_tmp(cod_prod, cant_prod) SELECT codproducto, cantidad FROM detallefactura WHERE nofactura = no_factura;
                  WHILE a <= registros DO
                  		SELECT cod_prod, cant_prod INTO cod_producto, cant_producto FROM tbl_tmp WHERE id = a;
                        SELECT cant_product INTO existencia_actual FROM producto WHERE cod_product = cod_producto;
                        SET nueva_existencia = existencia_actual + cant_producto;
                        UPDATE producto SET cant_product = nueva_existencia WHERE cod_product = cod_producto;
                        
                        SET a=a+1;
                  END WHILE;
                  
                  UPDATE factura SET estado = 2 WHERE nofactura = no_factura;
                  DROP TABLE tbl_tmp;
                  SELECT * FROM factura WHERE nofactura = no_factura;
                  
                END IF;
                
                ELSE 
                	SELECT 0 factura;
                END IF;
                

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `del_detalle_temp` (`id_detalle` INT, `token` VARCHAR(50))  BEGIN
    	DELETE FROM detalle_temp WHERE correlativo = id_detalle;
        
        SELECT tmp.correlativo, tmp.cod_product, p.nombre, tmp.cantidad, tmp.precio_venta FROM detalle_temp tmp
        INNER JOIN producto p
        ON tmp.cod_product = p.cod_product
        WHERE tmp.token_user = token;
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `procesar_venta` (IN `cod_usuario` INT, IN `token` VARCHAR(50))  BEGIN
    	DECLARE factura INT;
        
        DECLARE registros INT;
        DECLARE total DECIMAL(10,2);
        
        DECLARE nueva_existencia int;
        DECLARE existencia_actual int;
        
        DECLARE tmp_cod_producto int;
        DECLARE tmp_cant_producto int;
        DECLARE a INT;
        SET a = 1;
        
        CREATE TEMPORARY TABLE tbl_tmp_tokenuser(
                id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                cod_prod BIGINT,
                cant_prod int);
                
        SET registros = (SELECT COUNT(*) FROM detalle_temp WHERE token_user = token);
                
        IF registros > 0 THEN
           INSERT INTO tbl_tmp_tokenuser(cod_prod, cant_prod) SELECT cod_product, cantidad FROM detalle_temp WHERE token_user = token;
           
           INSERT INTO factura(usuario) VALUES(cod_usuario);
           SET factura = LAST_INSERT_ID();
                         
           
           INSERT INTO detallefactura(nofactura, codproducto, cantidad, precio_venta) SELECT (factura) as nofactura, cod_product, cantidad, precio_venta FROM detalle_temp WHERE token_user = token;
                         
                    
           WHILE a <= registros DO 
           		SELECT cod_prod, cant_prod INTO tmp_cod_producto, tmp_cant_producto FROM tbl_tmp_tokenuser WHERE id = a;
                SELECT cant_product INTO existencia_actual FROM producto WHERE cod_product = tmp_cod_producto;
                         
                    
                SET nueva_existencia = existencia_actual - tmp_cant_producto;
                UPDATE producto SET cant_product = nueva_existencia WHERE cod_product = tmp_cod_producto;
                         
                SET a=a+1;
                         
           END WHILE;
                        
               SET total = (SELECT SUM(cantidad * precio_venta) FROM detalle_temp WHERE token_user = token);
               UPDATE factura SET totalfactura = total WHERE nofactura = factura;
               DELETE FROM detalle_temp WHERE token_user = token;
               TRUNCATE TABLE tbl_tmp_tokenuser;
               SELECT * FROM factura WHERE nofactura = factura;                   
                         
        ELSE
              SELECT 0;
                         
        END IF;
             

    END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` bigint(20) NOT NULL,
  `nit` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `razon_social` varchar(100) NOT NULL,
  `telefono` bigint(20) NOT NULL,
  `email` varchar(200) NOT NULL,
  `direccion` text NOT NULL,
  `iva` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `nit`, `nombre`, `razon_social`, `telefono`, `email`, `direccion`, `iva`) VALUES
(1, '931243120', 'Frutas y Verduras My Tierra S.A', '', 3163882108, 'tytierra@servicio.com', 'edificio puerto verde piso 1 barrio centro', '19.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallefactura`
--

CREATE TABLE `detallefactura` (
  `correlativo` bigint(11) NOT NULL,
  `nofactura` bigint(11) NOT NULL,
  `codproducto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `detallefactura`
--

INSERT INTO `detallefactura` (`correlativo`, `nofactura`, `codproducto`, `cantidad`, `precio_venta`) VALUES
(1, 1, 101, 1, '1465.01'),
(2, 1, 110, 2, '2000.00'),
(3, 1, 113, 1, '1560.00'),
(4, 2, 101, 1, '1465.01'),
(5, 2, 110, 2, '2000.00'),
(6, 2, 113, 1, '1560.00'),
(7, 3, 101, 1, '1465.01'),
(8, 3, 110, 2, '2000.00'),
(9, 3, 113, 1, '1560.00'),
(10, 4, 112, 2, '1200.00'),
(11, 4, 111, 3, '400.00'),
(13, 5, 104, 2, '1000.00'),
(14, 5, 108, 3, '500.00'),
(15, 5, 109, 3, '1000.00'),
(16, 5, 114, 2, '300.00'),
(20, 6, 111, 1, '400.00'),
(21, 6, 114, 2, '300.00'),
(23, 7, 103, 1, '1294.49'),
(24, 7, 112, 3, '1200.00'),
(26, 8, 113, 2, '1560.00'),
(27, 8, 104, 1, '1000.00'),
(28, 9, 111, 2, '400.00'),
(29, 9, 109, 1, '1000.00'),
(31, 10, 101, 1, '1465.01'),
(32, 11, 112, 1, '1200.00'),
(33, 12, 107, 2, '3000.00'),
(34, 12, 111, 4, '400.00'),
(36, 13, 108, 2, '500.00'),
(37, 14, 113, 2, '1560.00'),
(38, 14, 108, 5, '500.00'),
(40, 15, 102, 3, '1485.72'),
(41, 15, 110, 2, '2000.00'),
(42, 16, 112, 2, '1200.00'),
(43, 16, 101, 1, '1465.01'),
(44, 16, 103, 3, '1294.49'),
(45, 16, 110, 3, '2000.00'),
(46, 17, 101, 2, '1465.01'),
(47, 17, 111, 3, '400.00'),
(49, 18, 110, 2, '2000.00'),
(50, 18, 101, 1, '1465.01'),
(51, 18, 113, 2, '1548.80'),
(52, 19, 112, 1, '1200.00'),
(53, 20, 110, 2, '2000.00'),
(54, 20, 114, 3, '300.00'),
(55, 21, 101, 1, '1465.01'),
(56, 21, 110, 1, '2000.00'),
(58, 22, 112, 1, '1200.00'),
(59, 22, 102, 1, '1485.72');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_temp`
--

CREATE TABLE `detalle_temp` (
  `correlativo` int(11) NOT NULL,
  `token_user` varchar(50) NOT NULL,
  `cod_product` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entrada`
--

CREATE TABLE `entrada` (
  `id_entrada` int(11) NOT NULL,
  `cod_product` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `cant_product` int(11) NOT NULL,
  `precio_product` decimal(10,2) NOT NULL,
  `id_usuario` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `entrada`
--

INSERT INTO `entrada` (`id_entrada`, `cod_product`, `fecha`, `cant_product`, `precio_product`, `id_usuario`) VALUES
(8, 101, '2021-06-07 18:14:06', 100, '2000.00', 1),
(9, 102, '2021-06-07 18:15:03', 20, '3000.00', 1),
(10, 103, '2021-06-07 18:15:55', 70, '1500.00', 1),
(11, 101, '2021-06-07 18:16:40', 50, '3000.00', 1),
(12, 103, '2021-06-07 18:33:21', 20, '2000.00', 1),
(13, 102, '2021-06-07 19:29:44', 10, '1900.00', 1),
(14, 102, '2021-06-07 19:30:40', 10, '1900.00', 1),
(15, 101, '2021-06-07 19:32:46', 20, '1000.00', 1),
(16, 103, '2021-06-07 19:37:07', 10, '1000.00', 1),
(17, 103, '2021-06-07 19:37:52', 10, '1000.00', 1),
(18, 102, '2021-06-07 19:42:14', 10, '1000.00', 1),
(19, 102, '2021-06-07 19:43:07', 10, '2000.00', 1),
(20, 103, '2021-06-07 19:44:09', 10, '1000.00', 1),
(21, 101, '2021-06-08 19:01:53', 10, '1000.00', 1),
(22, 101, '2021-06-08 19:21:33', 10, '1000.00', 1),
(23, 102, '2021-06-08 19:31:01', 70, '1000.00', 1),
(24, 101, '2021-06-08 19:31:28', 10, '1000.00', 1),
(25, 103, '2021-06-08 19:32:15', 10, '1300.00', 1),
(26, 101, '2021-06-08 19:35:52', 10, '1000.00', 1),
(27, 101, '2021-06-08 19:39:48', 10, '1200.00', 1),
(28, 101, '2021-06-08 19:40:21', 10, '100.00', 1),
(29, 103, '2021-06-08 19:40:49', 20, '1200.00', 1),
(30, 103, '2021-06-08 19:42:31', 10, '1000.00', 1),
(31, 101, '2021-06-08 19:43:04', 10, '1000.00', 1),
(32, 102, '2021-06-08 20:06:54', 10, '1000.00', 1),
(33, 103, '2021-06-09 23:08:42', 10, '1000.00', 1),
(34, 101, '2021-06-09 23:08:59', 10, '1000.00', 1),
(35, 103, '2021-06-09 23:09:09', 10, '100.00', 1),
(36, 104, '2021-06-10 21:13:46', 100, '1000.00', 1),
(37, 104, '2021-06-10 21:14:45', 90, '2000.00', 1),
(38, 106, '2021-06-10 21:15:30', 30, '2500.00', 1),
(39, 106, '2021-06-10 21:15:59', 200, '3000.00', 1),
(40, 108, '2021-06-10 21:22:52', 100, '500.00', 1),
(41, 109, '2021-06-10 21:23:49', 20, '1000.00', 1),
(42, 110, '2021-06-10 21:24:31', 90, '2000.00', 1),
(43, 111, '2021-06-10 21:24:53', 90, '400.00', 1),
(44, 112, '2021-06-10 21:25:24', 80, '1200.00', 1),
(45, 113, '2021-06-10 21:25:51', 100, '2000.00', 1),
(46, 114, '2021-06-10 21:26:32', 200, '300.00', 1),
(47, 115, '2021-06-10 21:27:43', 900, '300.00', 1),
(48, 113, '2021-06-13 09:31:22', 100, '1200.00', 1),
(49, 113, '2021-06-13 09:31:42', 50, '1400.00', 1),
(50, 113, '2021-06-14 15:06:33', 5, '1000.00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura`
--

CREATE TABLE `factura` (
  `nofactura` bigint(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario` int(11) DEFAULT NULL,
  `totalfactura` decimal(10,2) DEFAULT NULL,
  `estado` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `factura`
--

INSERT INTO `factura` (`nofactura`, `fecha`, `usuario`, `totalfactura`, `estado`) VALUES
(3, '2021-06-13 17:50:09', 3, '7025.01', 1),
(4, '2021-06-13 17:58:00', 1, '3600.00', 1),
(5, '2021-06-13 18:07:45', 26, '7100.00', 2),
(6, '2021-06-13 18:55:19', 1, '1000.00', 2),
(7, '2021-06-13 19:03:40', 1, '4894.49', 2),
(8, '2021-06-13 19:07:08', 1, '4120.00', 2),
(9, '2021-06-13 22:43:29', 1, '1800.00', 1),
(10, '2021-06-13 22:44:39', 1, '1465.01', 1),
(11, '2021-06-13 22:47:56', 1, '1200.00', 1),
(12, '2021-06-13 22:49:49', 1, '7600.00', 1),
(13, '2021-06-13 22:50:24', 1, '1000.00', 1),
(14, '2021-06-13 22:50:52', 1, '5620.00', 1),
(15, '2021-06-13 22:55:39', 1, '8457.16', 1),
(16, '2021-06-14 08:24:58', 1, '13748.48', 1),
(17, '2021-06-14 17:35:39', 1, '4130.02', 1),
(18, '2021-06-14 17:38:37', 1, '8562.61', 2),
(19, '2021-06-14 17:45:50', 1, '1200.00', 2),
(20, '2021-06-14 21:17:44', 1, '4900.00', 2),
(21, '2021-06-15 21:42:45', 1, '3465.01', 1),
(22, '2021-06-15 21:52:33', 13, '2685.72', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id_producto` int(11) NOT NULL,
  `cod_product` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `cant_product` int(11) NOT NULL,
  `precio_product` decimal(10,2) NOT NULL,
  `unidad_com` varchar(105) NOT NULL,
  `iva` int(11) NOT NULL DEFAULT 19,
  `fecha_add` datetime NOT NULL DEFAULT current_timestamp(),
  `id_usuario` int(11) NOT NULL,
  `estado` varchar(45) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id_producto`, `cod_product`, `nombre`, `descripcion`, `cant_product`, `precio_product`, `unidad_com`, `iva`, `fecha_add`, `id_usuario`, `estado`) VALUES
(12, 101, 'fresa', 'frescas', 194, '1465.01', 'kilo', 19, '2021-06-07 18:14:06', 1, '1'),
(13, 102, 'mora', 'frescas', 136, '1485.72', 'kilo', 19, '2021-06-07 18:15:03', 1, '1'),
(14, 103, 'Peras', 'fresca', 177, '1294.49', 'libras', 19, '2021-06-07 18:15:55', 1, '1'),
(15, 104, 'manzana', 'frescas', 98, '1000.00', 'kilo', 19, '2021-06-10 21:13:46', 1, '1'),
(16, 105, 'Cerezas', 'frescas', 90, '2000.00', 'kilo', 19, '2021-06-10 21:14:45', 1, '1'),
(17, 106, 'Uvas pasas', 'frescas', 30, '2500.00', 'kilo', 19, '2021-06-10 21:15:30', 1, '1'),
(18, 107, 'Sandia', 'frescas', 198, '3000.00', 'kilo', 19, '2021-06-10 21:15:59', 1, '1'),
(19, 108, 'Mango', 'fresco', 90, '500.00', 'kilo', 19, '2021-06-10 21:22:52', 1, '1'),
(20, 109, 'Melocoton', 'fresco', 16, '1000.00', 'kilo', 19, '2021-06-10 21:23:49', 1, '1'),
(21, 110, 'remolacha', 'frescas', 82, '2000.00', 'kilo', 19, '2021-06-10 21:24:31', 1, '1'),
(22, 111, 'Zanahoria', 'fresca', 78, '400.00', 'kilo', 19, '2021-06-10 21:24:53', 1, '1'),
(23, 112, 'franbuesas', 'fresca', 74, '1200.00', 'kilo', 19, '2021-06-10 21:25:24', 1, '1'),
(24, 113, 'yuca', 'frescas', 252, '1548.80', 'kilo', 19, '2021-06-10 21:25:51', 1, '1'),
(25, 114, 'papa', 'frescas', 198, '300.00', 'kilo', 19, '2021-06-10 21:26:32', 1, '1'),
(26, 115, 'hierba', 'fresca', 900, '300.00', 'kilo', 19, '2021-06-10 21:27:43', 1, '1');

--
-- Disparadores `producto`
--
DELIMITER $$
CREATE TRIGGER `entrada_A_I` AFTER INSERT ON `producto` FOR EACH ROW BEGIN
         INSERT INTO entrada(cod_product, cant_product, precio_product, id_usuario)
         VALUES (new.cod_product, new.cant_product, new.precio_product, new.id_usuario);
     END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL,
  `cod_rol` varchar(45) NOT NULL,
  `nombre_rol` varchar(45) NOT NULL,
  `descripcion` varchar(1005) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `cod_rol`, `nombre_rol`, `descripcion`) VALUES
(3, 'ad', 'Administrador', 'administra la empresa'),
(1, 'ca', 'Cajero', 'realizar ventas'),
(2, 'su', 'Supervisor', 'Administra el inventario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `identificacion` int(50) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `apellido` varchar(45) NOT NULL,
  `telefono` varchar(10) NOT NULL,
  `direccion` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contrasenia` varchar(45) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `identificacion`, `nombre`, `apellido`, `telefono`, `direccion`, `email`, `contrasenia`, `id_rol`, `estado`) VALUES
(1, 111, 'Emir', 'Renteria Alegria', '3153882108', 'calle 8 sur 56-18', 'emir11r@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 3, 1),
(4, 2233443, 'Yudy', 'Perea Martinez', '3125426272', 'gadagaajja', 'yudy@hotmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 2, 1),
(6, 101010101, 'Katerin', 'Torres Alegrias', '318000000', 'cll 5 sr b-23', 'katerin@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 1, 1),
(7, 9982821, 'Cristhin', 'Delgado Grueso', '3651227200', 'colon quuinto', 'cristhyy@hotmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 1, 1),
(10, 191919191, 'Marco', 'Perea OrdoÃ±ez', '3123212113', 'calle 98 sr de ca', 'Marco@hotmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 2, 1),
(13, 918272, 'Karla', 'Fernandez', '3157158445', 'calle 100 de ca', 'karla@hotmail', '827ccb0eea8a706c4c34a16891f84e7b', 1, 1),
(14, 33222, 'Marcela', 'Delgado Cuero', '322987161', 'calle 9 sur b 222', 'marce@hotmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 3, 1),
(15, 12212, 'Sindyy', 'Cuero Amu', '3125426272', 'calle 100 de ca', 'sindy@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 1, 1),
(16, 72252, 'Carlos', 'Bustamante Gonzales', '315715844', 'calle 9 sur b 222', 'Carlos@hotmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 1, 1),
(17, 524242, 'Hernan', 'Cuero Amu', '315715844', 'calle 9 sur b 222', 'Hernan@hotmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 1, 1),
(18, 11776635, 'Ivan', 'Martinez Perea', '3125426272', 'calle 98 sr de ca', 'ivan@hotmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 2, 1),
(19, 231122, 'Graciela', 'hernandez Ricaute', '3157158445', 'calle 98 sr de ca', 'Graciela@hotmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 1, 1),
(20, 903938, 'Daniel', 'Martinez Perea', '3123212113', 'calle 9 sur b 222', 'dani@hotmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 1, 1),
(21, 32321, 'Sofia', 'Delgado Cuero', '3157158445', 'calle 9 sur b 123', 'sofi@hotmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 1, 1),
(22, 87363, 'Miguel', 'Fernandez Castro', '3123212113', 'calle 100 de ca', 'miguel@hotmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 2, 1),
(23, 5373762, 'Carolina', 'Ortiz Bonilla', '3125426272', 'calle 9 sur b 123', 'caro@hotmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 1, 1),
(24, 34343, 'Francisco', 'd', 'f', 'calle 9 sur b 123', 'francisco@hotmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 1, 1),
(25, 92019, 'Carmen', 'Sinisterra Ramirez', '315715844', 'calle 9 sur b 222', 'carmen@hotmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 2, 1),
(26, 111000, 'javier', 'martinez perea', '3126252551', 'calle 8 sur 56-18', 'javier@hotmail.com', '01cfcd4f6b8770febfb40cb906715822', 1, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  ADD PRIMARY KEY (`correlativo`);

--
-- Indices de la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  ADD PRIMARY KEY (`correlativo`);

--
-- Indices de la tabla `entrada`
--
ALTER TABLE `entrada`
  ADD PRIMARY KEY (`id_entrada`),
  ADD KEY `cod_product` (`cod_product`);

--
-- Indices de la tabla `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`nofactura`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id_producto`),
  ADD UNIQUE KEY `id_producto` (`id_producto`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`cod_rol`),
  ADD UNIQUE KEY `id_rol` (`id_rol`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  MODIFY `correlativo` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  MODIFY `correlativo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de la tabla `entrada`
--
ALTER TABLE `entrada`
  MODIFY `id_entrada` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de la tabla `factura`
--
ALTER TABLE `factura`
  MODIFY `nofactura` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
