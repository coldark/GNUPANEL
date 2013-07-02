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

function agrega_record_en_pdns($conexion,$usa_nat,$id_usuario,$name,$contenido,$tipo,$ttl,$prio)
{
$change_date = time();
$checkeo = NULL;
if($usa_nat==1 && $tipo == "A")
	{
	$consulta = "INSERT INTO gnupanel_pdns_records(domain_id,name,content,type,ttl,prio,change_date) VALUES($id_usuario,'$name','$contenido','$tipo',$ttl,$prio,$change_date) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $res_consulta;

	$contenido_nat = "(SELECT ip_privada FROM gnupanel_ips_servidor WHERE ip_publica = '$contenido')";
	$consulta = "INSERT INTO gnupanel_pdns_records_nat(domain_id,name,content,type,ttl,prio,change_date) VALUES($id_usuario,'$name',$contenido_nat,'$tipo',$ttl,$prio,$change_date) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;
	}
else
	{
	$consulta = "INSERT INTO gnupanel_pdns_records(domain_id,name,content,type,ttl,prio,change_date) VALUES($id_usuario,'$name','$contenido','$tipo',$ttl,$prio,$change_date) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $res_consulta;

	$consulta = "INSERT INTO gnupanel_pdns_records_nat(domain_id,name,content,type,ttl,prio,change_date) VALUES($id_usuario,'$name','$contenido','$tipo',$ttl,$prio,$change_date) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;
	}
return $checkeo;
}

function existe_en_dns($subdominio)
{
	global $servidor_db;
	global $puerto_db;
	global $database;
	global $usuario_db;
	global $passwd_db;
	$result = NULL;
	$conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
	$conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
	$consulta = "SELECT id FROM gnupanel_pdns_records WHERE name = '$subdominio' ";
	$res_consulta = pg_query($conexion,$consulta);
	$cantidad = pg_num_rows($res_consulta);
	if($cantidad > 0) $result = true;
	$checkeo = $res_consulta;
	pg_close($conexion);
	return $result;
}

function tiene_ip($id_usuario)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT ip FROM gnupanel_usuario WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $ip = pg_fetch_result ($res_consulta,0,0);
    if(strlen($ip)>7) $retorno = true; 
    pg_free_result($res_consulta);
    pg_close($conexion);
    return $retorno;
}

function dame_dominio($id_usuario)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT dominio FROM gnupanel_usuario WHERE id_usuario= $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $dominio = pg_fetch_result ($res_consulta,0,0);
    pg_free_result($res_consulta);
    pg_close($conexion);
    return $dominio;
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

function quedan_subdominios()
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

    $id_usuario = $_SESSION['id_usuario'];
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT subdominios FROM gnupanel_usuario_plan WHERE id_usuario= $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $cuantas_tengo = pg_fetch_result ($res_consulta,0,0);

    $consulta = "SELECT subdominios FROM gnupanel_usuario_estado WHERE id_usuario= $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $cuantas_use = pg_fetch_result ($res_consulta,0,0);

    $result = ($cuantas_tengo == -1) || ($cuantas_use < $cuantas_tengo);
    pg_close($conexion);
    return $result;
}

function subdominio_prohibido($subdominio)
{
global $escribir;
$retorno = NULL;
$dominios_prohibidos = NULL;

$dominios_prohibidos[0] = "gnupanel";
$dominios_prohibidos[1] = "ftp";
$dominios_prohibidos[2] = "pop";
$dominios_prohibidos[3] = "smtp";
$dominios_prohibidos[4] = "mx";
$dominios_prohibidos[5] = "ns0";
$dominios_prohibidos[6] = "ns1";
$dominios_prohibidos[7] = "ns2";
$dominios_prohibidos[8] = "ns3";
$dominios_prohibidos[9] = "ns4";
$dominios_prohibidos[10] = "ns5";
$dominios_prohibidos[11] = "ns6";
$dominios_prohibidos[12] = "ns7";
$dominios_prohibidos[13] = "ns8";
$dominios_prohibidos[14] = "ns9";
$dominios_prohibidos[15] = "mx0";
$dominios_prohibidos[16] = "mx1";
$dominios_prohibidos[17] = "mx2";
$dominios_prohibidos[18] = "mx3";
$dominios_prohibidos[19] = "mx4";
$dominios_prohibidos[20] = "mx5";
$dominios_prohibidos[21] = "mx6";
$dominios_prohibidos[22] = "mx7";
$dominios_prohibidos[23] = "mx8";
$dominios_prohibidos[24] = "mx9";
$dominios_prohibidos[25] = "ns";
$dominios_prohibidos[26] = "tmp";
$dominios_prohibidos[27] = "moodledata";

if(is_array($dominios_prohibidos))
{
foreach($dominios_prohibidos as $prohibido)
	{
	if($prohibido == $subdominio) $retorno = $escribir['reservado_0']." ".$subdominio." ".$escribir['reservado_1'];
	}
}
return $retorno;
}

function verifica_agregar_subdominio($subdominio,$es_ssl)
{
	global $escribir;
	$retorno = NULL;
	if(!verifica_dato($subdominio,NULL)) $retorno = $escribir['carac_inv']." ";
	if(existe_subdominio($subdominio,$es_ssl)) $retorno = $escribir['existe']." ";
	if(strlen($subdominio)==0) $retorno = $escribir['sin_subdominio']." ";
	$prohibido = subdominio_prohibido($subdominio,$es_ssl);
	if($prohibido) $retorno = $prohibido." ";
	return $retorno;
}

function existe_subdominio($subdominio,$es_ssl_in)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $result = NULL;
    $es_ssl = 0;
    if($es_ssl_in=="true") $es_ssl = 1;
    $id_usuario = $_SESSION['id_usuario'];
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT subdominio FROM gnupanel_apacheconf WHERE subdominio = '$subdominio' AND id_dominio = $id_usuario AND es_ssl = $es_ssl ";
    $res_consulta = pg_query($conexion,$consulta);
    $cantidad = pg_num_rows($res_consulta);
    if($cantidad > 0) $result = true;
    pg_close($conexion);
    return $result;
}

function agregar_subdominio($id_usuario,$subdominio,$es_ssl_in)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $dir_base_web;

    $es_ssl = 0;
    if($es_ssl_in=="true") $es_ssl = 1;
    $puerto = 80;
    if($es_ssl==1) $puerto = 443;

    $checkeo = NULL;
    $consulta = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");

    $consulta = "SELECT usuario,dominio,ip FROM gnupanel_usuario WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $usuario_data = pg_fetch_assoc($res_consulta,0);
    $usuario = $usuario_data['usuario'];
    $dominio = $usuario_data['dominio'];
    $ip_publica = $usuario_data['ip'];
    $ip_privada = NULL;
    $consulta = "SELECT id_reseller,reseller,dominio,id_ip FROM gnupanel_reseller WHERE id_reseller = (SELECT cliente_de FROM gnupanel_usuario WHERE id_usuario = $id_usuario)";
    $res_consulta = pg_query($conexion,$consulta);
    $reseller_data = pg_fetch_assoc($res_consulta,0);
    $id_reseller = $reseller_data['id_reseller'];
    $reseller = $reseller_data['reseller'];
    $dominio_reseller = $reseller_data['dominio'];
    $id_ip = $reseller_data['id_ip'];
    $usa_nat = NULL;
    if(!isset($ip_publica))
	{
	$consulta = "SELECT ip_publica,ip_privada,usa_nat FROM gnupanel_ips_servidor WHERE id_ip = $id_ip ";
	$res_consulta = pg_query($conexion,$consulta);
	$ip_publica = pg_fetch_result($res_consulta,0,0);
	$ip_privada = pg_fetch_result($res_consulta,0,1);
	$usa_nat = pg_fetch_result($res_consulta,0,2);
	}
    else
	{
	$consulta = "SELECT ip_publica,ip_privada,usa_nat FROM gnupanel_ips_servidor WHERE ip_publica = '$ip_publica' ";
	$res_consulta = pg_query($conexion,$consulta);
	$ip_publica = pg_fetch_result($res_consulta,0,0);
	$ip_privada = pg_fetch_result($res_consulta,0,1);
	$usa_nat = pg_fetch_result($res_consulta,0,2);
	}

    $ip = NULL;
    if($usa_nat == 1)
	{
	$ip = $ip_privada;
	}
    else
	{
	$ip = $ip_publica;
	}



    $consulta = "SELECT admin FROM gnupanel_admin WHERE id_admin = (SELECT cliente_de FROM gnupanel_reseller WHERE id_reseller = $id_reseller)";
    $res_consulta = pg_query($conexion,$consulta);
    $admin = pg_fetch_result($res_consulta,0,0);
    
    $directorio = $dir_base_web.$admin."/".$reseller."@".$dominio_reseller."/".$dominio."/subdominios/".$subdominio;
    if($es_ssl==1) $directorio = $dir_base_web.$admin."/".$reseller."@".$dominio_reseller."/".$dominio."/subdominios-ssl/".$subdominio;
    $administrador = $usuario."@".$dominio;

    $res_consulta = pg_query($conexion,"BEGIN");
    $checkeo = $res_consulta;
    
    $subdominio_completo = $subdominio.".".$dominio;
    if(!existe_en_dns($subdominio_completo)) $checkeo = $checkeo && agrega_record_en_pdns($conexion,$usa_nat,$id_usuario,$subdominio_completo,$ip_publica,"A",86400,'NULL');

    $id_subdominio = "(SELECT id FROM gnupanel_pdns_records WHERE name = '$subdominio_completo')";
    $consulta = "INSERT INTO gnupanel_apacheconf(id_subdominio,id_dominio,subdominio,ip,puerto,documentroot,serveradmin,es_ssl,estado) VALUES ($id_subdominio,$id_usuario,'$subdominio','$ip',$puerto,'$directorio','$administrador',$es_ssl,0) ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "UPDATE gnupanel_usuario_estado SET subdominios = subdominios + 1 WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "SELECT content FROM gnupanel_pdns_records WHERE name = '$dominio_reseller' AND type = 'SOA' ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $checkeo = $checkeo && actualiza_soa($conexion,pg_fetch_result($res_consulta,0,0));

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

function agregar_subdominio_0($procesador,$mensaje)
{
    global $idioma;
    global $escribir;
    global $plugin;
    global $plugins;
    global $seccion;
    global $_POST;
    global $_SESSION;

	$id_usuario = $_SESSION['id_usuario'];
	$dominio = dame_dominio($id_usuario);
	$subdominio = strtolower(trim($_POST['subdominio']));
	$es_ssl = trim($_POST['es_ssl']);
	print "<div id=\"formulario\" > \n";
	if($mensaje) print "$mensaje <br/> \n";
	if(quedan_subdominios())
	{
	print "<ins> \n";
	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table> \n";
	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
	genera_fila_formulario("subdominio",$subdominio,"text_con_text",8,!$mensaje,true,NULL,254,".".$dominio);

	if(tiene_ip($id_usuario))
	{
	genera_fila_formulario("es_ssl",$es_ssl,"check_box",20,!$mensaje);
	}
	else
	{
	genera_fila_formulario("es_ssl",$es_ssl,"check_box_lock",20,!$mensaje);
	}

	genera_fila_formulario("ingresando","1",'hidden',NULL,true);
	genera_fila_formulario("resetea",NULL,'reset',NULL,true);
	genera_fila_formulario("agrega",NULL,'submit',NULL,true);
	print "</table> \n";
	print "</form> \n";
	print "</ins> \n";
	}
	else
	{
	$escriba = $escribir['no_subdominio'];
	print "$escriba <br>";
	}

	print "</div> \n";
	print "<div id=\"botones\" > \n";
	print "</div> \n";
	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";
}

function agregar_subdominio_1($procesador,$mensaje)
{
    global $idioma;
    global $escribir;
    global $plugin;
    global $plugins;
    global $seccion;
    global $_POST;
    global $_SESSION;

	$id_usuario = $_SESSION['id_usuario'];
	$dominio = dame_dominio($id_usuario);
	$subdominio = strtolower(trim($_POST['subdominio']));
	$es_ssl = trim($_POST['es_ssl']);
	$checkea = verifica_agregar_subdominio($subdominio,$es_ssl);

	if($checkea)
	{
	agregar_subdominio_0($procesador,$checkea);
	}
	else
	{

	print "<div id=\"formulario\" > \n";
	$chequeo = agregar_subdominio($id_usuario,$subdominio,$es_ssl);
	if($chequeo)
		{
		$escriba = $escribir['exito'];
		print "<br><br>$escriba<br><br> \n";
		}
	else
		{
		$escriba = $escribir['fracaso'];
		print "$escriba <br/> \n";
		}
		print "</div> \n";
		print "<div id=\"botones\" > \n";
		print "</div> \n";
		print "<div id=\"ayuda\" > \n";
		$escriba = $escribir['help'];
		print "$escriba\n";
		print "</div> \n";
	}
}

function agregar_subdominio_init($nombre_script)
{
	global $_POST;
	$paso = trim($_POST['ingresando']);
	switch($paso)
	{
		case "1":
		agregar_subdominio_1($nombre_script,NULL);
		break;
		default:
		agregar_subdominio_0($nombre_script,NULL);
	}
}

?>
