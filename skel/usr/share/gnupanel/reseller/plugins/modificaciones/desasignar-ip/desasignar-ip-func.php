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

function cantidad_usuarios_usuario()
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $id_reseller = $_SESSION['id_reseller'];
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_usuario WHERE cliente_de = $id_reseller AND ip IS NOT NULL ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
	$retorno = count(pg_fetch_all($res_consulta)) - 1;
	}
pg_close($conexion);
return $retorno;    
}

function dame_usuario_principal()
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $id_reseller = $_SESSION['id_reseller'];
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT id_usuario from gnupanel_usuario_plan WHERE id_plan = 0 AND EXISTS (SELECT * FROM gnupanel_usuario WHERE gnupanel_usuario.cliente_de = $id_reseller AND gnupanel_usuario.id_usuario = gnupanel_usuario_plan.id_usuario) ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_result($res_consulta,0,0);
    pg_close($conexion);
    return $retorno;
}

function lista_usuarios_usuario($comienzo)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $cant_max_result;

    $id_reseller = $_SESSION['id_reseller'];
    $id_principal = dame_usuario_principal();
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT id_usuario,usuario,dominio,ip FROM gnupanel_usuario WHERE cliente_de = $id_reseller AND id_usuario <> $id_principal AND ip IS NOT NULL ORDER BY dominio LIMIT $cant_max_result OFFSET $comienzo";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
	$retorno = pg_fetch_all($res_consulta);
	}
pg_close($conexion);
return $retorno;    
}

function actualiza_soa($conexion,$soa_record_ant)
{
$checkeo = NULL;
$soa_recordman = explode (" ",$soa_record_ant);

$soa_recordman[2] = trim($soa_recordman[2]);
$parte_fecha = substr($soa_recordman[2],0,8);
$parte_numero = substr($soa_recordman[2],-1,2);
$fecha = trim(date(Ymd));
if($fecha == $parte_fecha)
	{
	$parte_numero = $parte_numero + 1;
	if($parte_numero < 10)
		{
		$soa_recordman[2] = $fecha."0".$parte_numero;
		}
	else
		{
		$soa_recordman[2] = $fecha.$parte_numero;
		}
	}
else
	{
	$soa_recordman[2] = $fecha."00";
	}

$soa_record_act = implode(" ", $soa_recordman);
$soa_record_act = trim($soa_record_act);

$consulta = "UPDATE gnupanel_pdns_records SET content = '$soa_record_act' WHERE content = '$soa_record_ant' AND type = 'SOA' ";
$res_consulta = pg_query($conexion,$consulta);
$checkeo = $res_consulta;

$consulta = "UPDATE gnupanel_pdns_records_nat SET content = '$soa_record_act' WHERE content = '$soa_record_ant' AND type = 'SOA' ";
$res_consulta = pg_query($conexion,$consulta);
$checkeo = $checkeo && $res_consulta;
if($checkeo) $checkeo = $soa_record_act;
return $checkeo;
}

function desasignar_ip($id_usuario,$ip)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $cant_max_result;
    global $escribir;

    $id_reseller = $_SESSION['id_reseller'];
    $checkeo = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");

    $res_consulta = pg_query($conexion,"BEGIN");
    $checkeo = $res_consulta;

    $checkeo = $checkeo && desasigna_ip_de_subdominios($id_usuario,$ip,$conexion);

    $consulta = "UPDATE gnupanel_ips_servidor SET esta_usada = 0 WHERE ip_publica = '$ip' ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "SELECT dominio FROM gnupanel_reseller WHERE id_reseller = $id_reseller ";
    $res_consulta = pg_query($conexion,$consulta);
    $dominio_reseller = pg_fetch_result($res_consulta,0,0);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "SELECT content FROM gnupanel_pdns_records WHERE name = '$dominio_reseller' AND type = 'SOA' ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $soa_record = actualiza_soa($conexion,pg_fetch_result($res_consulta,0,0));
    $checkeo = $checkeo && $soa_record;

    $consulta = "UPDATE gnupanel_usuario SET ip = NULL WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    if($checkeo)
	{
	$res_consulta = pg_query($conexion,"END");
	}
    else
	{
	$res_consulta = pg_query($conexion,"ROLLBACK");
	}

    pg_close($conexion);
    return $checkeo;
}

function desasigna_ip_de_subdominios($id_usuario,$ip,$conexion)
{
    global $_SESSION;

    $id_reseller = $_SESSION['id_reseller'];

    $chequeo = NULL;
    $consulta = "SELECT ip_privada,usa_nat FROM gnupanel_ips_servidor WHERE ip_publica = '$ip' ";
    $res_consulta = pg_query($conexion,$consulta);
    $chequeo = $res_consulta;
    $ip_publica = $ip;
    $ip_privada = pg_fetch_result($res_consulta,0,0);
    $usa_nat = pg_fetch_result($res_consulta,0,1);

    $consulta = "SELECT ip_publica,ip_privada,usa_nat FROM gnupanel_ips_servidor WHERE es_de_id_reseller = $id_reseller AND es_ip_principal = 1 ";
    $res_consulta = pg_query($conexion,$consulta);
    $chequeo = $chequeo && $res_consulta;
    $ip_publica_nueva = pg_fetch_result($res_consulta,0,0);
    $ip_privada_nueva = pg_fetch_result($res_consulta,0,1);
    $usa_nat_nueva = pg_fetch_result($res_consulta,0,2);

    $ip_apache = $ip_publica;
    if($usa_nat==1) $ip_apache = $ip_privada;

    $ip_apache_nueva = $ip_publica_nueva;
    if($usa_nat_nueva==1) $ip_apache_nueva = $ip_privada_nueva;

    $consulta = "DELETE FROM gnupanel_pdns_records WHERE content = '$ip_publica' AND NOT EXISTS (SELECT id_subdominio FROM gnupanel_apacheconf WHERE gnupanel_apacheconf.id_subdominio = gnupanel_pdns_records.id AND es_ssl = 0 ) AND EXISTS (SELECT id_subdominio FROM gnupanel_apacheconf WHERE gnupanel_apacheconf.id_subdominio = gnupanel_pdns_records.id AND es_ssl = 1 ) ";

    $res_consulta = pg_query($conexion,$consulta);
    $chequeo = $chequeo && $res_consulta;

    $consulta = "DELETE FROM gnupanel_apacheconf WHERE ip = '$ip_apache' AND es_ssl = 1 ";
    $res_consulta = pg_query($conexion,$consulta);
    $chequeo = $chequeo && $res_consulta;

    $consulta = "UPDATE gnupanel_pdns_records_nat SET content = '$ip_apache_nueva' WHERE content = '$ip_apache' AND prio IS NULL ";
    $res_consulta = pg_query($conexion,$consulta);
    $chequeo = $chequeo && $res_consulta;

    $consulta = "UPDATE gnupanel_pdns_records SET content = '$ip_publica_nueva' WHERE content = '$ip_publica' AND prio IS NULL ";
    $res_consulta = pg_query($conexion,$consulta);
    $chequeo = $chequeo && $res_consulta;

    $consulta = "UPDATE gnupanel_apacheconf SET ip = '$ip_apache_nueva' WHERE ip = '$ip_apache' ";
    $res_consulta = pg_query($conexion,$consulta);
    $chequeo = $chequeo && $res_consulta;

    return $chequeo;
}

function desasignar_ip_0($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	global $cant_max_result;
	$comienzo = $_POST['comienzo'];
	$cantidad = cantidad_usuarios_usuario();
	if(!isset($comienzo)) $comienzo = 0;
	$usuarios = lista_usuarios_usuario($comienzo);
	print "<div id=\"formulario\" > \n";
	print "<ins> \n";

	print "<table width=\"80%\" > \n";

	print "<tr> \n";

	print "<td width=\"30%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"30%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	print "</td> \n";

	print "</tr> \n";

	print "<tr> \n";

	print "<td width=\"30%\" > \n";
	$escriba = $escribir['usuario'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"30%\" > \n";
	$escriba = $escribir['dominio'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	$escriba = $escribir['ip'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	print "</td> \n";

	print "</tr> \n";

	if(is_array($usuarios))
	{
	foreach($usuarios as $arreglo)
	{
	print "<tr> \n";
	print "<td width=\"30%\" > \n";
	$escriba = $arreglo['usuario'];
	print "$escriba \n";
	print "</td> \n";
	print "<td width=\"30%\" > \n";
	$escriba = $arreglo['dominio'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	$escriba = $arreglo['ip'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	$escriba = $escribir['desasignar'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
	$variables = array();
	$variables['id_usuario'] = $arreglo['id_usuario'];
	$variables['ip'] = $arreglo['ip'];
	$variables['usuario'] = $arreglo['usuario'];
	$variables['dominio'] = $arreglo['dominio'];
	$variables['ingresando'] = "1";
	$variables['comienzo'] = $comienzo;
	boton_con_formulario($procesador_inc,$escriba,$variables,NULL);
	print "</td> \n";

	print "</tr> \n";
	}
	}


	print "</table> \n";
	print "</ins> \n";
	print "</div> \n";
	print "<div id=\"botones\" > \n";
	print "<ins> \n";

	if($cant_max_result < $cantidad)
	{
	print "<table width=\"80%\" > \n";
	print "<tr> \n";
	print "<td width=\"35%\" > \n";
	if($comienzo > 0)
	{
	$escriba = $escribir['anterior'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
	$variables = array();
	$desde = $comienzo - $cant_max_result;
	$variables['comienzo'] = $desde;
	$variables['ingresando'] = "0";
	boton_con_formulario($procesador_inc,$escriba,$variables,NULL);
	}
	print "</td> \n";
	print "<td width=\"30%\" > \n";
	$num_pag = ceil($cantidad/$cant_max_result);
	$esta_pagina = ceil($comienzo/$cant_max_result)+1;
	print $escribir['pagina']." $esta_pagina/$num_pag "."\n";
	print "</td> \n";
	print "<td width=\"35%\" > \n";

	if($cantidad > ($comienzo+$cant_max_result))
	{
	$escriba = $escribir['siguiente'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
	$variables = array();
	$desde = $comienzo + $cant_max_result;
	$variables['comienzo'] = $desde;
	$variables['ingresando'] = "0";
	boton_con_formulario($procesador_inc,$escriba,$variables,NULL);
	}
	print "</td> \n";
	print "</tr> \n";
	print "</table> \n";
	}

	print "</ins> \n";
	print "</div> \n";
	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";
}

function desasignar_ip_1($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;

	$id_usuario = $_POST['id_usuario'];
	$usuario = $_POST['usuario'];
	$dominio = $_POST['dominio'];
	
	$comienzo = $_POST['comienzo'];
	$ip = $_POST['ip'];
	print "<div id=\"formulario\" > \n";
	print "<ins> \n";
	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table width=\"80%\" > \n";

	print "<tr> \n";

	print "<td width=\"40%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"60%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "</tr> \n";

	print "<tr> \n";

	print "<td width=\"40%\" > \n";
	$escriba = $escribir['usuario'].":";
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"60%\" > \n";
	$escriba = $usuario;
	print "$escriba \n";
	print "</td> \n";

	print "</tr> \n";

	print "<tr> \n";

	print "<td width=\"40%\" > \n";
	$escriba = $escribir['dominio'].":";
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"60%\" > \n";
	$escriba = $dominio;
	print "$escriba \n";
	print "</td> \n";

	print "</tr> \n";

	print "<tr> \n";

	print "<td width=\"40%\" > \n";
	$escriba = $escribir['ip'].":";
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"60%\" > \n";
	$escriba = $ip;
	print "$escriba \n";
	print "</td> \n";

	print "</tr> \n";


	print "<tr> \n";

	print "<td width=\"40%\" > \n";
	$escriba = $valor['ip_publica'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"60%\" > \n";
	$escriba = $escribir['desasignar'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
	$variables = array();
	$variables['ingresando'] = "2";
	$variables['comienzo'] = $_POST['comienzo'];
	$variables['ip'] = $ip;
	$variables['id_usuario'] = $id_usuario;
	boton_con_formulario($procesador_inc,$escriba,$variables,NULL);
	print "</td> \n";

	print "</tr> \n";

	print "</table> \n";
	print "</form> \n";
	print "</ins> \n";
	print "</div> \n";
	print "<div id=\"botones\" > \n";
	print "<ins> \n";

	$escriba = $escribir['volver'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
	$variables = array();
	$variables['ingresando'] = "0";
	$variables['comienzo'] = $_POST['comienzo'];
	boton_con_formulario($procesador_inc,$escriba,$variables,NULL);

	print "</ins> \n";
	print "</div> \n";


	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['advertencia'];
	print "$escriba\n";
	print "</div> \n";
}


function desasignar_ip_2($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$id_usuario = $_POST['id_usuario'];	
	$comienzo = $_POST['comienzo'];
	$ip = $_POST['ip'];

	print "<div id=\"formulario\" > \n";

	if(desasignar_ip($id_usuario,$ip))
	{
	$escriba = $escribir['exito'];
	print "<br><br>$escriba<br>";
	}
	else
	{
	$escriba = $escribir['fracaso'];
	print "<br><br>$escriba<br>";
	}

	print "</div> \n";
	
	print "<div id=\"botones\" > \n";
	print "<ins> \n";

	$escriba = $escribir['volver'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
	$variables = array();
	$variables['ingresando'] = "0";
	$variables['comienzo'] = $_POST['comienzo'];
	boton_con_formulario($procesador_inc,$escriba,$variables,NULL);

	print "</ins> \n";
	print "</div> \n";


	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";
}

function desasignar_ip_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];

	switch($paso)
	{
		case "1":
		desasignar_ip_1($nombre_script,NULL);
		break;

		case "2":
		desasignar_ip_2($nombre_script,NULL);
		break;

		default:
		desasignar_ip_0($nombre_script,NULL);
	}
}



?>
