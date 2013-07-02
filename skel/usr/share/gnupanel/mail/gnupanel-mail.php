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
error_reporting(0);
require_once("config/gnupanel-mail-ini.php");
session_cache_limiter('nocache');
$session_ant = session_name('mail');

require_once("funciones/funciones.php");

session_start();

if(isset($_SESSION['id_usuario'])) $tiempo_max_sesion = dame_tiempo_max_sesion_usuario($_SESSION['id_usuario']);

session_start();

$idioma_default = "en";
if(isset($_SESSION['id_usuario'])) $idioma_default = dame_idioma_usuario($_SESSION['id_usuario']);

$idioma = setea_lenguaje($idioma_default);

mensajes_botones_load();
$nombre_script = substr(strrchr($_SERVER['SCRIPT_NAME'],"/"),1);
mensajes_login_load();


$incluir = "login/login-func.php";
require_once("$incluir");
if($_GET['desloguear']=="1") terminar_sesion();
$logueado = NULL;
$logueado = $_SESSION['logueado'];
$interfaz = "mail";
$seccion = $_GET['seccion'];
$plugin = $_GET['plugin'];
$incluye_seccion = "1";
$incluye_plugin = "1";
//$estilo_ini = dame_tema_interfaz_usuarios();
$estilo_per = dame_tema_interfaz_usuarios_per();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7"/>
<title><?php echo $titulo ?></title>
<?php

$estilo_poner = NULL;
$estilo_js = NULL;

if($estilo_per)
{
	$estilo_poner = "<link rel=\"stylesheet\" type=\"text/css\" href=\"$estilo_per\" /> \n";
	$estilo_js = basename(dirname($estilo_per));
}

print "$estilo_poner \n";

if($logueado != "1") $estilo_js = $estilo_js."_login";
?>
<script language="JavaScript" src="estilos/niftycube.js" type="text/javascript" ></script>
<script language="JavaScript" src="estilos/estilos.js" type="text/javascript" ></script>
</head>
<body onload="redondeo('<?php echo $estilo_js; ?>');" >
<div id="cabecera" >

</div>

<div id="cuerpo" >

<div id="botonera" >

<?php
if($logueado == "1")
{
	print "<h4><a href=\"gnupanel-mail.php\" > \n";
	print $escribir['inicio'];
	print "</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
	print "<a href=\"gnupanel-mail.php&#063;desloguear=1\" > \n";
	print $escribir['deslogueo'];
	print "</a></h4>\n";

	$incluir = "botonera.php";
	require_once("$incluir");
}
?>
</div>

<?php
if($logueado != "1")
{
	print "<div id=\"contenedor\" > \n";
	print "<div id=\"titulo\" > \n";
	print "</div> \n";
	login_init($nombre_script);
}
else
{
	print "<div id=\"contenedor\" > \n";
	$cant_max_result = dame_cant_max_result_usuario($_SESSION['id_usuario']);
	require_once("plugins.php");
}

?>

</div>

</div>
</body>

</html>
