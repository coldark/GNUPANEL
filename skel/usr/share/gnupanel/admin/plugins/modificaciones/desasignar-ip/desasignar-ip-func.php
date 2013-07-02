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

function cantidad_usuarios_reseller()
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $id_admin = $_SESSION['id_admin'];
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT id_reseller,reseller,dominio FROM gnupanel_reseller WHERE cliente_de = $id_admin AND EXISTS (SELECT id_ip FROM gnupanel_ips_servidor WHERE es_ip_principal = 0 AND gnupanel_ips_servidor.es_de_id_reseller = gnupanel_reseller.id_reseller)";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
	$retorno = count(pg_fetch_all($res_consulta));
	}

pg_close($conexion);
return $retorno;    
}

function lista_usuarios_reseller($comienzo)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $cant_max_result;

    $id_admin = $_SESSION['id_admin'];
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT id_reseller,reseller,dominio FROM gnupanel_reseller WHERE cliente_de = $id_admin AND EXISTS (SELECT id_ip FROM gnupanel_ips_servidor WHERE es_ip_principal = 0 AND gnupanel_ips_servidor.es_de_id_reseller = gnupanel_reseller.id_reseller) ORDER BY dominio LIMIT $cant_max_result OFFSET $comienzo";
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

function dame_ips_asignadas($id_reseller)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $cant_max_result;
    global $escribir;

    $id_admin = $_SESSION['id_admin'];
    $result = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");

    $consulta = "SELECT id_ip,ip_publica,esta_usada FROM gnupanel_ips_servidor WHERE es_de_id_reseller = $id_reseller AND es_ip_principal = 0 ";
    $res_consulta = pg_query($conexion,$consulta);
    $result = pg_fetch_all($res_consulta);

    pg_close($conexion);
    return $result;    
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

function desasignar_ip($id_reseller,$id_ip,$esta_usada)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $cant_max_result;
    global $escribir;

    $id_admin = $_SESSION['id_admin'];
    $checkeo = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");

    $res_consulta = pg_query($conexion,"BEGIN");
    $checkeo = $res_consulta;

    if($esta_usada == 1)
	{
	$checkeo = $checkeo && desasigna_ip_de_subdominios($id_reseller,$id_ip,$esta_usada,$conexion);
	}

    $consulta = "UPDATE gnupanel_ips_servidor SET es_de_id_reseller = NULL, esta_usada = 0 WHERE id_ip = $id_ip ";
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

function desasigna_ip_de_subdominios($id_reseller,$id_ip,$esta_usada,$conexion)
{
    $chequeo = NULL;
    $consulta = "SELECT ip_publica,ip_privada,usa_nat FROM gnupanel_ips_servidor WHERE id_ip = $id_ip ";
    $res_consulta = pg_query($conexion,$consulta);
    $chequeo = $res_consulta;
    $ip_publica = pg_fetch_result($res_consulta,0,0);
    $ip_privada = pg_fetch_result($res_consulta,0,1);
    $usa_nat = pg_fetch_result($res_consulta,0,2);

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

    $consulta = "SELECT content FROM gnupanel_pdns_records WHERE type = 'SOA' AND name = (SELECT dominio FROM gnupanel_reseller WHERE id_reseller = $id_reseller) ";
    $res_consulta = pg_query($conexion,$consulta);
    $chequeo = $chequeo && $res_consulta;
    $soa_record_ant = pg_fetch_result($res_consulta,0,0);

    $consulta = "DELETE FROM gnupanel_pdns_records WHERE content = '$ip_publica' AND NOT EXISTS (SELECT id_subdominio FROM gnupanel_apacheconf WHERE gnupanel_apacheconf.id_subdominio = gnupanel_pdns_records.id AND es_ssl = 0 ) AND EXISTS (SELECT id_subdominio FROM gnupanel_apacheconf WHERE gnupanel_apacheconf.id_subdominio = gnupanel_pdns_records.id AND es_ssl = 1 ) ";

    $res_consulta = pg_query($conexion,$consulta);
    $chequeo = $chequeo && $res_consulta;

    $consulta = "DELETE FROM gnupanel_apacheconf WHERE ip = '$ip_apache' AND es_ssl = 1 ";
    $res_consulta = pg_query($conexion,$consulta);
    $chequeo = $chequeo && $res_consulta;

    $consulta = "UPDATE gnupanel_pdns_records_nat SET content = '$ip_apache_nueva' WHERE content = '$ip_apache' ";
    $res_consulta = pg_query($conexion,$consulta);
    $chequeo = $chequeo && $res_consulta;

    $consulta = "UPDATE gnupanel_pdns_records SET content = '$ip_publica_nueva' WHERE content = '$ip_publica' ";
    $res_consulta = pg_query($conexion,$consulta);
    $chequeo = $chequeo && $res_consulta;

    $chequeo = $chequeo && actualiza_soa($conexion,$soa_record_ant);

    $consulta = "UPDATE gnupanel_apacheconf SET ip = '$ip_apache_nueva' WHERE ip = '$ip_apache' ";
    $res_consulta = pg_query($conexion,$consulta);
    $chequeo = $chequeo && $res_consulta;

    $consulta = "UPDATE gnupanel_usuario SET ip = NULL WHERE ip = '$ip_publica' ";
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
	$cantidad = cantidad_usuarios_reseller();
	if(!isset($comienzo)) $comienzo = 0;
	$resellers = lista_usuarios_reseller($comienzo);
	print "<div id=\"formulario\" > \n";
	print "<ins> \n";

	print "<table width=\"80%\" > \n";

	print "<tr> \n";
	print "<td width=\"40%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"40%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"20%\" > \n";
	print "</td> \n";
	print "</tr> \n";

	print "<tr> \n";

	print "<td width=\"40%\" > \n";
	$escriba = $escribir['reseller'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"40%\" > \n";
	$escriba = $escribir['dominio'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	print "</td> \n";

	print "</tr> \n";

	print "<tr> \n";
	print "<td width=\"40%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"40%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"20%\" > \n";
	print "</td> \n";
	print "</tr> \n";

	if(is_array($resellers))
	{
	foreach($resellers as $arreglo)
	{
	print "<tr> \n";
	print "<td width=\"40%\" > \n";
	$escriba = $arreglo['reseller'];
	print "$escriba \n";
	print "</td> \n";
	print "<td width=\"40%\" > \n";
	$escriba = $arreglo['dominio'];
	print "$escriba \n";
	print "</td> \n";
	print "<td width=\"20%\" > \n";
	$escriba = $escribir['desasignar'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
	$variables = array();
	$variables['id_reseller'] = $arreglo['id_reseller'];
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
	$id_reseller = $_POST['id_reseller'];	
	$comienzo = $_POST['comienzo'];
	$ips = $_POST['ips'];
	$ips_asignadas = dame_ips_asignadas($id_reseller);
	print "<div id=\"formulario\" > \n";
	$escriba = $escribir['advertencia'];
	print "$escriba<br>\n";
	print "<ins> \n";
	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table width=\"80%\" > \n";

	print "<tr> \n";
	print "<td width=\"40%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"20%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"40%\" > \n";
	print "</td> \n";
	print "</tr> \n";

	print "<tr> \n";
	print "<td width=\"40%\" > \n";
	$escriba = $escribir['ip'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	$escriba = $escribir['esta_usada'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"40%\" > \n";
	print "</td> \n";

	print "</tr> \n";

	print "<tr> \n";
	print "<td width=\"40%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"20%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"40%\" > \n";
	print "</td> \n";
	print "</tr> \n";

	if(is_array($ips_asignadas))
	{
	foreach($ips_asignadas as $valor)
	{
	print "<tr> \n";

	print "<td width=\"40%\" > \n";
	$escriba = $valor['ip_publica'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	$escriba = $escribir['no'];
	if($valor['esta_usada']==1) $escriba = $escribir['si'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"40%\" > \n";
	$escriba = $escribir['desasignar'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
	$variables = array();
	$variables['ingresando'] = "2";
	$variables['comienzo'] = $_POST['comienzo'];
	$variables['id_ip'] = $valor['id_ip'];
	$variables['esta_usada'] = $valor['esta_usada'];
	$variables['id_reseller'] = $id_reseller;
	boton_con_formulario($procesador_inc,$escriba,$variables,NULL);
	print "</td> \n";

	print "</tr> \n";
	}
	}

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
//	print "$escriba\n";
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
	$id_reseller = $_POST['id_reseller'];	
	$comienzo = $_POST['comienzo'];
	$id_ip = $_POST['id_ip'];
	$esta_usada = $_POST['esta_usada'];

	print "<div id=\"formulario\" > \n";

	if(desasignar_ip($id_reseller,$id_ip,$esta_usada))
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
