<?php
/***********************************************************************************************************

GNUPanel es un programa para el control de hospedaje WEB 
Copyright (C) 2006  Ricardo Marcelo Alvarez rmalvarezkai@gmail.com

------------------------------------------------------------------------------------------------------------

Este archivo es parte de GNUPanel.

	GNUPanel es Software Libre; Usted puede redistribuirlo y/o modificarlo
	bajo los términos de la GNU Licencia Pública General (GPL) tal y como ha sido
	públicada por la Free Software Foundation; o bien la versión 2 de la Licencia,
	o (a su opción) cualquier versión posterior.

	GNUPanel se distribuye con la esperanza de que sea útil, pero SIN NINGUNA
	GARANTÍA; tampoco las implícitas garantías de MERCANTILIDAD o ADECUACIÓN A UN
	PROPÓSITO PARTICULAR. Consulte la GNU General Public License (GPL) para más
	detalles.

	Usted debe recibir una copia de la GNU General Public License (GPL)
	junto con GNUPanel; si no, escriba a la Free Software Foundation Inc.
	51 Franklin Street, 5º Piso, Boston, MA 02110-1301, USA.

------------------------------------------------------------------------------------------------------------

This file is part of GNUPanel.

	GNUPanel is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	GNUPanel is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with GNUPanel; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

------------------------------------------------------------------------------------------------------------

***********************************************************************************************************/

$servidor_db = "localhost";
$puerto_db = "5432";
$database = "gnupanel";
$usuario_db = "gnupanel";
$passwd_db = "PASAPORTE";
$SessionTableName = "gnupanel_sesiones";
$tiempo_max_sesion = 900;
$conexion_sesion = $conexion_sesion;
$cant_max_result = 10;
$idiomas = "en";
$idioma_default = "en";
$transferencia_servidor = TRANSFERENCIA_SERVIDOR;
$interfaz = "usuarios";
$espacio_servidor = ESPACIO_SERVIDOR;
$gid_postfix = GID_MAIL;
$dir_base_web = "/var/www/sitios/";
$mailquota = 20;
$nombre_servidor = "NOMBRE_SERVIDOR";
$ip_db_servidor[$nombre_servidor] = "localhost";
$tema_default_config = "gnutransfer";

require("config/gnupanel-plugins-mail-ini.php");

require("config/session-pg.php");

?>