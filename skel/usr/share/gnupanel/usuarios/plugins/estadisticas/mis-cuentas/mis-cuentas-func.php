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
if($_SESSION['logueado']!="1") exit("Error");

function dame_usuario_principal()
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $id_usuario = $_SESSION['id_usuario'];
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");

    $consulta = "SELECT cliente_de from gnupanel_usuario WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $cliente_de = pg_fetch_result($res_consulta,0,0);

    $consulta = "SELECT id_usuario from gnupanel_usuario WHERE cliente_de = $cliente_de ORDER BY id_usuario LIMIT 1 ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_result($res_consulta,0,0);
    pg_close($conexion);
    return $retorno;
}

function dame_consumos_usuario($id_usuario)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

    $retorno = NULL;
    $result = NULL;
    $checkeo = NULL;
    $result = array();

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");

    $consulta = "SELECT subdominios,dominios_parking,bases_postgres,bases_mysql,cuentas_correo,listas_correo,cuentas_ftp FROM gnupanel_usuario_estado WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $res_consulta;
    $result['usado'] = pg_fetch_assoc($res_consulta,0);

    $consulta = "SELECT subdominios,dominios_parking,bases_postgres,bases_mysql,cuentas_correo,listas_correo,cuentas_ftp FROM gnupanel_usuario_plan WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;
    $result['disponible'] = pg_fetch_assoc($res_consulta,0);

    if($id_usuario == dame_usuario_principal())
	{
	$id_reseller = "(SELECT cliente_de FROM gnupanel_usuario WHERE id_usuario = $id_usuario)";
	$consulta = "SELECT sum(subdominios) AS subdominios,sum(dominios_parking) AS dominios_parking,sum(bases_postgres) AS bases_postgres,sum(bases_mysql) AS bases_mysql,sum(cuentas_correo) AS cuentas_correo,sum(listas_correo) AS listas_correo,sum(cuentas_ftp) AS cuentas_ftp FROM gnupanel_usuario_estado WHERE EXISTS (SELECT * FROM gnupanel_usuario WHERE cliente_de = $id_reseller AND gnupanel_usuario.id_usuario = gnupanel_usuario_estado.id_usuario) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;
	$result['usado'] = pg_fetch_assoc($res_consulta,0);

	$consulta = "SELECT subdominios,dominios_parking,bases_postgres,bases_mysql,cuentas_correo,listas_correo,cuentas_ftp FROM gnupanel_reseller_plan WHERE id_reseller = $id_reseller";

	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;
	$result['disponible'] = pg_fetch_assoc($res_consulta,0);
	}

pg_close($conexion);
return $result;
}

function dame_usuario_usuario($id_usuario)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

    $retorno = NULL;
    $result = NULL;
    $checkeo = NULL;
    $result = array();

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");

    $consulta = "SELECT usuario,dominio FROM gnupanel_usuario WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $result = pg_fetch_assoc($res_consulta,0);

pg_close($conexion);
return $result;


}


function mis_cuentas_0($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$id_usuario = $_SESSION['id_usuario'];	
	$usuario_data = dame_usuario_usuario($id_usuario);
	$usuario_consumos = dame_consumos_usuario($id_usuario);
	$usuario_disp = $usuario_consumos['disponible'];
	$usuario_usado = $usuario_consumos['usado'];


	print "<div id=\"formulario\" > \n";
	print "<ins> \n";

	print "<table width=\"80%\" align=\"center\" > \n";

		print "<tr> \n";
		print "<td width=\"40%\" colspan=\"1\" > \n";
		print "<br>";
		print "</td> \n";
		print "<td width=\"60%\" colspan=\"3\" > \n";
		print "<br>";
		print "</td> \n";
		print "</tr> \n";

		print "<tr> \n";
		print "<td width=\"40%\" colspan=\"1\" > \n";
		$escriba = $escribir['usuario'];
		print "$escriba";
		print "</td> \n";
		print "<td width=\"60%\" colspan=\"3\" > \n";
		$escriba = $usuario_data['usuario'];
		print "$escriba";
		print "</td> \n";
		print "</tr> \n";

		print "<tr> \n";
		print "<td width=\"40%\" colspan=\"1\" > \n";
		$escriba = $escribir['dominio'];
		print "$escriba";
		print "</td> \n";
		print "<td width=\"60%\" colspan=\"3\" > \n";
		$escriba = $usuario_data['dominio'];
		print "$escriba";
		print "</td> \n";
		print "</tr> \n";

		print "<tr> \n";
		print "<td width=\"40%\" colspan=\"1\" > \n";
		$escriba = "<br>";
		print "$escriba";
		print "</td> \n";
		print "<td width=\"60%\" colspan=\"3\" > \n";
		print "$escriba";
		print "</td> \n";
		print "</tr> \n";

	if(is_array($usuario_disp))
	{
	foreach($usuario_disp AS $llave => $valor)
		{
		print "<tr> \n";

		print "<td width=\"60%\" > \n";
		$escriba = $escribir[$llave];
		print "$escriba <br>\n";
		print "</td> \n";

		print "<td width=\"10%\" > \n";
		$escriba = 0;
		if(isset($usuario_usado[$llave])) $escriba = $usuario_usado[$llave];
		print "$escriba <br>\n";
		print "</td> \n";

		print "<td width=\"10%\" > \n";
		$escriba = $escribir['de'];
		print "$escriba <br>\n";
		print "</td> \n";

		print "<td width=\"20%\" > \n";
		$disponible = $valor;
		settype($disponible,'integer');
		if($disponible == -1) $disponible = $escribir['ilimitadas'];
		$escriba = $disponible;
		print "$escriba <br>\n";
		print "</td> \n";

		print "</tr> \n";
		}
	}	

	print "</table> \n";
	print "</ins> \n";

	print "</div> \n";

	print "<div id=\"botones\" > \n";
	print "<ins> \n";

	print "</ins> \n";
	print "</div> \n";

	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";
}

function mis_cuentas_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];

	switch($paso)
	{
		case "1":
		mis_cuentas_0($nombre_script,NULL);
		break;
		default:
		mis_cuentas_0($nombre_script,NULL);
	}
}



?>

