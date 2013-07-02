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

function dame_dominio($id_usuario)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT dominio from gnupanel_usuario WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
	$retorno = pg_fetch_result($res_consulta,0,0);
	}

    pg_close($conexion);
    return $retorno;    
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

if(is_array($dominios_prohibidos))
{
foreach($dominios_prohibidos as $prohibido)
	{
	if($prohibido == $subdominio) $retorno = $escribir['reservado_0']." ".$subdominio." ".$escribir['reservado_1'];
	}
}
return $retorno;
}

function dame_lista_subdominios()
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $retorno = NULL;
    $result = NULL;
    $id_usuario = $_SESSION['id_usuario'];
    $dominio = dame_dominio($id_usuario);

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT subdominio,es_ssl from gnupanel_apacheconf WHERE id_dominio = $id_usuario ORDER BY subdominio DESC";
    $res_consulta = pg_query($conexion,$consulta);

    if(!$res_consulta)
	{
	$result = NULL;
	}
    else
	{
	$result = pg_fetch_all($res_consulta);
	}

    if(is_array($result))
	{
	$retorno = array();
	foreach($result as $arreglo)
		{
		$devolver = NULL;
		if(strlen($arreglo['subdominio'])>0)
		{
		$devolver = $arreglo['subdominio'].".".$dominio;
		}
		else
		{
		$devolver = $dominio;
		}

		if($arreglo['es_ssl']==1)
		{
		$devolver = "https://".$devolver;
		}
		else
		{
		$devolver = "http://".$devolver;
		}
		if(!subdominio_prohibido($arreglo['subdominio'])) $retorno[$devolver] = $devolver;
		}
	}

    pg_close($conexion);
    return $retorno;    
    }

function quedan_dominios_parking()
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $id_usuario = $_SESSION['id_usuario'];
    $usados = 0;
    $disponibles = 0;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");

    $consulta = "SELECT dominios_parking from gnupanel_usuario_plan WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $disponibles = pg_fetch_result($res_consulta,0,0);

    $consulta = "SELECT dominios_parking from gnupanel_usuario_estado WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $usados = pg_fetch_result($res_consulta,0,0);

    if($usados<$disponibles) $retorno = true;
    if($disponibles==-1) $retorno = true;
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

function verifica_parkear($dominio)
{
	global $escribir;
	$retorno = NULL;
	if(!verifica_dato_idn($dominio,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!quedan_dominios_parking()) $retorno = $escribir['sin_parking']." ";
	if(existe_dominio_reseller($dominio)) $retorno = $escribir['existe']." ";
	return $retorno;
}

function agrega_dominio_en_pdns($conexion,$id_dominio,$dominio)
    {
    $consulta = "INSERT INTO gnupanel_pdns_domains(id,name,type) VALUES($id_dominio,'$dominio','MASTER') ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $res_consulta;

    $consulta = "INSERT INTO gnupanel_pdns_domains_nat(id,name,type) VALUES($id_dominio,'$dominio','MASTER') ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    return $checkeo;
}

function agrega_record_en_pdns($conexion,$usa_nat,$id_dominio,$name,$contenido,$tipo,$ttl,$prio)
{
/*
$consulta = "INSERT INTO gnupanel_pdns_records(domain_id,name,content,type,ttl,prio,change_date) VALUES($id_usuario,'$dominio','$contenido','$tipo',$ttl,$prio,$change_date) ";
//print "$consulta <br> \n";
$res_consulta = pg_query($conexion,$consulta);
$checkeo = $res_consulta;
*/
$change_date = time();
$checkeo = NULL;
if($usa_nat==1 && $tipo == "A")
	{
	$consulta = "INSERT INTO gnupanel_pdns_records(domain_id,name,content,type,ttl,prio,change_date) VALUES($id_dominio,'$name','$contenido','$tipo',$ttl,$prio,$change_date) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $res_consulta;

	$contenido_nat = "(SELECT ip_privada FROM gnupanel_ips_servidor WHERE ip_publica = '$contenido')";
	$consulta = "INSERT INTO gnupanel_pdns_records_nat(domain_id,name,content,type,ttl,prio,change_date) VALUES($id_dominio,'$name',$contenido_nat,'$tipo',$ttl,$prio,$change_date) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;
	}
else
	{
	$consulta = "INSERT INTO gnupanel_pdns_records(domain_id,name,content,type,ttl,prio,change_date) VALUES($id_dominio,'$name','$contenido','$tipo',$ttl,$prio,$change_date) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $res_consulta;

	$consulta = "INSERT INTO gnupanel_pdns_records_nat(domain_id,name,content,type,ttl,prio,change_date) VALUES($id_dominio,'$name','$contenido','$tipo',$ttl,$prio,$change_date) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;
	}
return $checkeo;
}


function agregar_serv_backup_en_dns($conexion,$id_dominio,$dominio)
    {
    global $_SESSION;
    $id_usuario = $_SESSION['id_usuario'];
    $id_reseller = "(SELECT cliente_de FROM gnupanel_usuario WHERE id_usuario = $id_usuario)";
    $retorno = NULL;
    $checkeo = NULL;

    $id_ip = "(SELECT id_ip FROM gnupanel_reseller WHERE id_reseller = $id_reseller)";
    $consulta = "SELECT usa_nat,id_servidor FROM gnupanel_ips_servidor WHERE id_ip = $id_ip ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_assoc ($res_consulta,0);
    $usa_nat = $retorno['usa_nat'];
    $id_servidor = $retorno['id_servidor'];
    $checkeo = $res_consulta;

    $consulta = "SELECT dominio FROM gnupanel_reseller WHERE id_reseller = $id_reseller ";
    $res_consulta = pg_query($conexion,$consulta);
    $dominio_reseller = pg_fetch_result($res_consulta,0,0);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "SELECT ip,es_dns,es_mx,subdominio_ns,subdominio_mx FROM gnupanel_servidores_secundarios WHERE id_servidor = $id_servidor";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_all ($res_consulta);
    $checkeo = $checkeo && $res_consulta;
	$prio_s = 20;


	if(is_array($retorno))
	{
	foreach($retorno as $arreglo)
	{
		if($arreglo['es_dns']==1)
		{
		$contenido = $arreglo['subdominio_ns'].".".$dominio_reseller;
		$tipo = "NS";
		$ttl = "86400";
		$prio = "NULL";
		$change_date = time();
		$consulta = "INSERT INTO gnupanel_pdns_records(domain_id,name,content,type,ttl,prio,change_date) VALUES($id_dominio,'$dominio','$contenido','$tipo',$ttl,$prio,$change_date) ";
		$res_consulta = pg_query($conexion,$consulta);
		$checkeo = $checkeo && $res_consulta;

		$contenido = $arreglo['subdominio_ns'].".".$dominio_reseller;
		$tipo = "NS";
		$ttl = "86400";
		$prio = "NULL";
		$change_date = time();
		$consulta = "INSERT INTO gnupanel_pdns_records_nat(domain_id,name,content,type,ttl,prio,change_date) VALUES($id_dominio,'$dominio','$contenido','$tipo',$ttl,$prio,$change_date) ";
		$res_consulta = pg_query($conexion,$consulta);
		$checkeo = $checkeo && $res_consulta;

		$name = $arreglo['subdominio_ns'].".".$dominio;
		$contenido = $arreglo['ip'];
		$tipo = "A";
		$ttl = "86400";
		$prio = "NULL";
		$change_date = time();
		$consulta = "INSERT INTO gnupanel_pdns_records(domain_id,name,content,type,ttl,prio,change_date) VALUES($id_dominio,'$name','$contenido','$tipo',$ttl,$prio,$change_date) ";
		$res_consulta = pg_query($conexion,$consulta);
		$checkeo = $checkeo && $res_consulta;

		$name = $arreglo['subdominio_ns'].".".$dominio;
		$contenido = $arreglo['ip'];
		$tipo = "A";
		$ttl = "86400";
		$prio = "NULL";
		$change_date = time();
		$consulta = "INSERT INTO gnupanel_pdns_records_nat(domain_id,name,content,type,ttl,prio,change_date) VALUES($id_dominio,'$name','$contenido','$tipo',$ttl,$prio,$change_date) ";
		$res_consulta = pg_query($conexion,$consulta);
		$checkeo = $checkeo && $res_consulta;
		}

		if($arreglo['es_mx']==1)
		{
		$contenido = $arreglo['subdominio_mx'].".".$dominio;
		$tipo = "MX";
		$ttl = "86400";
		$prio = $prio_s;
		$change_date = time();
		$consulta = "INSERT INTO gnupanel_pdns_records(domain_id,name,content,type,ttl,prio,change_date) VALUES($id_dominio,'$dominio','$contenido','$tipo',$ttl,$prio,$change_date) ";
		$res_consulta = pg_query($conexion,$consulta);
		$checkeo = $checkeo && $res_consulta;

		$contenido = $arreglo['subdominio_mx'].".".$dominio;
		$tipo = "MX";
		$ttl = "86400";
		$prio = $prio_s;
		$change_date = time();
		$consulta = "INSERT INTO gnupanel_pdns_records_nat(domain_id,name,content,type,ttl,prio,change_date) VALUES($id_dominio,'$dominio','$contenido','$tipo',$ttl,$prio,$change_date) ";
		$res_consulta = pg_query($conexion,$consulta);
		$checkeo = $checkeo && $res_consulta;

		$name = $arreglo['subdominio_mx'].".".$dominio;
		$contenido = $arreglo['ip'];
		$tipo = "A";
		$ttl = "86400";
		$prio = "NULL";
		$change_date = time();
		$consulta = "INSERT INTO gnupanel_pdns_records(domain_id,name,content,type,ttl,prio,change_date) VALUES($id_dominio,'$name','$contenido','$tipo',$ttl,$prio,$change_date) ";
		$res_consulta = pg_query($conexion,$consulta);
		$checkeo = $checkeo && $res_consulta;

		$name = $arreglo['subdominio_mx'].".".$dominio;
		$contenido = $arreglo['ip'];
		$tipo = "A";
		$ttl = "86400";
		$prio = "NULL";
		$change_date = time();
		$consulta = "INSERT INTO gnupanel_pdns_records_nat(domain_id,name,content,type,ttl,prio,change_date) VALUES($id_dominio,'$name','$contenido','$tipo',$ttl,$prio,$change_date) ";
		$res_consulta = pg_query($conexion,$consulta);
		$checkeo = $checkeo && $res_consulta;
		$prio_s = $prio_s + 10;
		}
	}
	}
    return $checkeo;
    }

function redirigir_en_apache($conexion,$id_dominio,$destino)
{
    global $_SESSION;

	//$destino = pone_barra($destino_in);
	$id_usuario = $_SESSION['id_usuario'];

	$consulta = "SELECT usuario || '@' || dominio FROM gnupanel_usuario WHERE id_usuario = $id_usuario ";
	$res_consulta = pg_query($conexion,$consulta);
	$serveradmin = pg_fetch_result($res_consulta,0,0);
	$checkeo = $res_consulta;
	$entrada = NULL;

	
	$consulta = "SELECT name FROM gnupanel_pdns_domains WHERE id = $id_dominio ";
	$res_consulta = pg_query($conexion,$consulta);
	$dominio = pg_fetch_result($res_consulta,0,0);
	$checkeo = $checkeo && $res_consulta;

	$dominio_www = "www.".$dominio;

	$consulta = "SELECT id,content FROM gnupanel_pdns_records_nat WHERE domain_id = $id_dominio AND name = '$dominio' AND type = 'A' ";
	$res_consulta = pg_query($conexion,$consulta);
	$id_subdominio = pg_fetch_result($res_consulta,0,0);
	$ip = pg_fetch_result($res_consulta,0,1);
	$checkeo = $checkeo && $res_consulta;

	$subdominio = "";
	$consulta = "INSERT INTO gnupanel_apacheconf(id_subdominio,id_dominio,subdominio,ip,puerto,serveradmin,redirigir,dominio_destino,es_ssl,estado,active) VALUES ($id_subdominio,$id_dominio,'$subdominio','$ip',80,'$serveradmin',1,'$destino',0,0,1) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;

	$consulta = "SELECT id,content FROM gnupanel_pdns_records_nat WHERE domain_id = $id_dominio AND name = '$dominio_www' ";
	$res_consulta = pg_query($conexion,$consulta);
	$id_subdominio = pg_fetch_result($res_consulta,0,0);
	$ip = pg_fetch_result($res_consulta,0,1);
	$checkeo = $checkeo && $res_consulta;

	$subdominio = "www";
	$consulta = "INSERT INTO gnupanel_apacheconf(id_subdominio,id_dominio,subdominio,ip,puerto,serveradmin,redirigir,dominio_destino,es_ssl,estado,active) VALUES ($id_subdominio,$id_dominio,'$subdominio','$ip',80,'$serveradmin',1,'$destino',0,0,1) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;

return $checkeo;
}

function parkear($dominio_in,$destino)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

	$id_usuario = $_SESSION['id_usuario'];
	$conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
	$conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
	$dominio = mb_strtolower($dominio_in);
	$dominio = idn_to_ascii($dominio);
	$change_date = time();
	$res_consulta = pg_query($conexion,"BEGIN");
	$checkeo = $res_consulta;

	$consulta = "INSERT INTO gnupanel_usuarios_dominios(id,id_usuario) VALUES (currval('gnupanel_usuarios_dominios_secuencia_seq'),$id_usuario)";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;

	$id_dominio = "(SELECT max(id) FROM gnupanel_usuarios_dominios WHERE id_usuario = $id_usuario) ";


	$id_reseller = "(SELECT cliente_de FROM gnupanel_usuario WHERE id_usuario = $id_usuario)";

	$consulta = "SELECT ip FROM gnupanel_usuario WHERE id_usuario = $id_usuario ";
	$res_consulta = pg_query($conexion,$consulta);
	$ip_publica = pg_fetch_result ($res_consulta,0,0);
	$checkeo = $res_consulta; 
	$usa_nat = NULL;

	$consulta = "SELECT usuario,dominio FROM gnupanel_usuario WHERE id_usuario = $id_usuario ";
	$res_consulta = pg_query($conexion,$consulta);
	$usuario = pg_fetch_result ($res_consulta,0,0);
	$dominio_usuario = pg_fetch_result ($res_consulta,0,1);
	$checkeo = $checkeo && $res_consulta; 

	$consulta = "SELECT dominio FROM gnupanel_reseller WHERE id_reseller = $id_reseller ";
	$res_consulta = pg_query($conexion,$consulta);
	$dominio_reseller = pg_fetch_result($res_consulta,0,0);
	$checkeo = $checkeo && $res_consulta;

	$ip_privada = $ip_publica;
    if(strlen($ip_publica)<2)
	{
	$consulta = "SELECT ip_publica,usa_nat,ip_privada FROM gnupanel_ips_servidor WHERE id_ip = (SELECT id_ip from gnupanel_reseller WHERE id_reseller = $id_reseller) ";
	$res_consulta = pg_query($conexion,$consulta);
	$retorno = pg_fetch_assoc ($res_consulta,0);
	$ip_publica = $retorno['ip_publica'];
	$ip_privada = $retorno['ip_privada'];
	$usa_nat = $retorno['usa_nat'];
	$checkeo = $checkeo && $res_consulta;
	}
    else
	{
	$consulta = "SELECT ip_publica,usa_nat,ip_privada FROM gnupanel_ips_servidor WHERE ip_publica = '$ip_publica' ";
	$res_consulta = pg_query($conexion,$consulta);
	$retorno = pg_fetch_assoc ($res_consulta,0);
	$ip_publica = $retorno['ip_publica'];
	$ip_privada = $retorno['ip_privada'];
	$usa_nat = $retorno['usa_nat'];
	$checkeo = $checkeo && $res_consulta;
	}


	$checkeo = $checkeo && agrega_dominio_en_pdns($conexion,$id_dominio,$dominio);

	$consulta = "SELECT reseller,dominio FROM gnupanel_reseller WHERE id_reseller = $id_reseller ";
	$res_consulta = pg_query($conexion,$consulta);
	$usuario_reseller = pg_fetch_result($res_consulta,0,0);
	$dominio_reseller = pg_fetch_result($res_consulta,0,1);
	$checkeo = $checkeo && $res_consulta;

    $consulta = "SELECT content FROM gnupanel_pdns_records WHERE name = '$dominio_reseller' AND type = 'SOA' ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $soa_record = actualiza_soa($conexion,pg_fetch_result($res_consulta,0,0));
    $checkeo = $checkeo && $soa_record;

    $contenido = $soa_record;
    $tipo = "SOA";
    $ttl = "86400";
    $prio = "NULL";
    $checkeo = $checkeo && agrega_record_en_pdns($conexion,$usa_nat,$id_dominio,$dominio,$contenido,$tipo,$ttl,$prio);

    $contenido = "ns1.".$dominio_reseller;
    $tipo = "NS";
    $checkeo = $checkeo && agrega_record_en_pdns($conexion,$usa_nat,$id_dominio,$dominio,$contenido,$tipo,$ttl,$prio);

    $contenido = "mx1.".$dominio;
    $tipo = "MX";
    $prio = 10;
    $checkeo = $checkeo && agrega_record_en_pdns($conexion,$usa_nat,$id_dominio,$dominio,$contenido,$tipo,$ttl,$prio);

    $consulta = "SELECT id,content FROM gnupanel_pdns_records WHERE name = '$dominio_usuario' AND type = 'TXT' ORDER BY id";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $contenido = pg_fetch_result($res_consulta,0,1);
    $tipo = "TXT";
    $prio = "NULL";
    $checkeo = $checkeo && agrega_record_en_pdns($conexion,$usa_nat,$id_dominio,$dominio,$contenido,$tipo,$ttl,$prio);

    $contenido = $ip_publica;
    $name = $dominio;
    $tipo = "A";
    $prio = "NULL";
    $checkeo = $checkeo && agrega_record_en_pdns($conexion,$usa_nat,$id_dominio,$name,$contenido,$tipo,$ttl,$prio);

    $name = "ns1.".$dominio;
    $checkeo = $checkeo && agrega_record_en_pdns($conexion,$usa_nat,$id_dominio,$name,$contenido,$tipo,$ttl,$prio);

    $name = "mx1.".$dominio;
    $checkeo = $checkeo && agrega_record_en_pdns($conexion,$usa_nat,$id_dominio,$name,$contenido,$tipo,$ttl,$prio);

    $name = "www.".$dominio;
    $checkeo = $checkeo && agrega_record_en_pdns($conexion,$usa_nat,$id_dominio,$name,$contenido,$tipo,$ttl,$prio);

    $checkeo = $checkeo && agregar_serv_backup_en_dns($conexion,$id_dominio,$dominio);

    $checkeo = $checkeo && redirigir_en_apache($conexion,$id_dominio,$destino);

    $consulta = "INSERT INTO gnupanel_postfix_transport(id_dominio,dominio) VALUES($id_dominio,'$dominio') ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "UPDATE gnupanel_usuario_estado SET dominios_parking = dominios_parking + 1 WHERE id_usuario = $id_usuario ";
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

return $checkeo;
}

function parkear_0($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	global $cant_max_result;
	$id_usuario = $_SESSION['id_usuario'];

	$subdominios = dame_lista_subdominios();
	if(isset($_POST['destino'])) $destino = $_POST['destino'];

	print "<div id=\"formulario\" > \n";
	print "<ins> \n";
	if($mensaje) print "$mensaje <br> \n";
	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table width=\"80%\" > \n";


	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
	genera_fila_formulario("dominio",$dominio,"text_idn",20,!$mensaje);
	genera_fila_formulario("destino",$subdominios,"select_ip",20,!$mensaje);
	genera_fila_formulario("ingresando","1",'hidden',NULL,true);
	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
	genera_fila_formulario("resetea",NULL,'reset',NULL,true);
	genera_fila_formulario("parker",NULL,'submit',NULL,true);

	print "</table> \n";
	print "</form> \n";
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

function parkear_1($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	global $cant_max_result;
	$id_usuario = $_SESSION['id_usuario'];

	$dominio = $_POST['dominio'];
	$destino = $_POST['destino'];
	$escriba = NULL;


	$checkea = verifica_parkear($dominio,$destino);
	if($checkea)
	{
	parkear_0($procesador,$checkea);
	}
	else
	{
	if(parkear($dominio,$destino))
	{
		$escriba = $escribir['exito'];
	}
	else
	{
		$escriba = $escribir['fracaso'];
	}
	
	print "<div id=\"formulario\" > \n";
	print "<br> \n";
	print "<br><br>$escriba<br><br>";
	print "</div> \n";
	print "<div id=\"botones\" > \n";
	print "<ins> \n";

	$escriba = $escribir['volver'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
	$variables = array();
	$variables['ingresando'] = "0";
	$variables['comienzo'] = 0;
	boton_con_formulario($procesador_inc,$escriba,$variables,NULL);

	print "</ins> \n";
	print "</div> \n";
	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";
	}


}

function parkear_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];
	switch($paso)
	{
		case "1":
		parkear_1($nombre_script,NULL);
		break;

		default:
		parkear_0($nombre_script,NULL);
	}
}



?>
