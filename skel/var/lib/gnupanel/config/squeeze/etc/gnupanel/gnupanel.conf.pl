
#############################################################################################################
#
#GNUPanel es un programa para el control de hospedaje WEB 
#Copyright (C) 2006  Ricardo Marcelo Alvarez rmalvarezkai@gmail.com
#
#------------------------------------------------------------------------------------------------------------
#
#Este archivo es parte de GNUPanel.
#
#	GNUPanel es Software Libre; Usted puede redistribuirlo y/o modificarlo
#	bajo los términos de la GNU Licencia Pública General (GPL) tal y como ha sido
#	públicada por la Free Software Foundation; o bien la versión 2 de la Licencia,
#	o (a su opción) cualquier versión posterior.
#
#	GNUPanel se distribuye con la esperanza de que sea útil, pero SIN NINGUNA
#	GARANTÍA; tampoco las implícitas garantías de MERCANTILIDAD o ADECUACIÓN A UN
#	PROPÓSITO PARTICULAR. Consulte la GNU General Public License (GPL) para más
#	detalles.
#
#	Usted debe recibir una copia de la GNU General Public License (GPL)
#	junto con GNUPanel; si no, escriba a la Free Software Foundation Inc.
#	51 Franklin Street, 5º Piso, Boston, MA 02110-1301, USA.
#
#------------------------------------------------------------------------------------------------------------
#
#This file is part of GNUPanel.
#
#	GNUPanel is free software; you can redistribute it and/or modify
#	it under the terms of the GNU General Public License as published by
#	the Free Software Foundation; either version 2 of the License, or
#	(at your option) any later version.
#
#	GNUPanel is distributed in the hope that it will be useful,
#	but WITHOUT ANY WARRANTY; without even the implied warranty of
#	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#	GNU General Public License for more details.
#
#	You should have received a copy of the GNU General Public License
#	along with GNUPanel; if not, write to the Free Software
#	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
#
#------------------------------------------------------------------------------------------------------------
#
#############################################################################################################

$nombre_servidor = "NOMBRE_SERVIDOR";
$database = "gnupanel";
$userdb = "gnupanel";
$pasaportedb = "PASAPORTE";
$pasaportedbmysql = "MYSQL_PASSWD";
$pasaportemailman = "CONTRASENA_MAILMAN";

$archivo_log_mail = "/var/log/mail.log";
$archivo_log_ftp = "/var/log/proftpd/proftpd.log";
$archivo_log_http = "/var/log/apache2/transfer_pg.log";
$archivo_log_https = "/var/log/apache-ssl/transfer_pg.log";

$log_transferencia = "/var/log/apache2/transfer_pg.log";

$tiempo_dir = 15;
$directorio_raiz_sitios = "/var/www/sitios";
$directorio_raiz_correo = "/var/mail/correos";
$directorio_raiz_mysql = "/var/lib/mysql";
$directorio_backup = "/var/www/gnupanel-backups";

$funciones_prohibidas = "proc_open,popen,disk_free_space,diskfreespace,set_time_limit,leak,tmpfile,exec,system,shell_exec,passthru";

$usuario = "gnupanel";
$grupo = "gnupanel";

$usuario_dir_apache = "www-data";
$grupo_dir_apache = "www-data";

$skel_apache_subdominios = "/etc/gnupanel/SKEL_APACHE_SUBDOMINIOS";
$skel_apache_gnupanel = "/etc/gnupanel/SKEL_APACHE_GNUPANEL";

$dir_conf_apache = "/var/lib/gnupanel/etc/apache2/sites-available/";
$dir_link_apache = "/var/lib/gnupanel/etc/apache2/sites-enabled/";
$dir_postfix_secundario = "/var/lib/gnupanel/etc/postfix-secundario/";

$dir_ssl_cert = "/var/lib/gnupanel/etc/apache2/ssl/certs/";
$dir_conf_apache_ssl = "/var/lib/gnupanel/etc/apache2/ssl/sites-available/";
$dir_link_apache_ssl = "/var/lib/gnupanel/etc/apache2/ssl/sites-enabled/";
$randomfile = "/tmp/gnupanel.rnd";
$archivo_namevirtualhosts = "/var/lib/gnupanel/etc/apache2/namevirtualhost.conf";
$correo_administrador = "CORREO_ADMIN";
$dias_de_gracia = 15;

############################################################################################################









