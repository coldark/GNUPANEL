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

function agregar_secundario($secundario,$servidor,$ip,$es_dns,$es_mx,$subdominio_ns,$subdominio_mx)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $_dns = 0;
    $_mx = 0;
    $_ip = implode(".",$ip);
    if($es_dns=="true") $_dns = 1;
    if($es_mx=="true") $_mx = 1;
    if($_dns==0) $subdominio_ns = "";
    if($_mx==0) $subdominio_mx = "";

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");

    $res_consulta = pg_query($conexion,"BEGIN");
    $retorno = $res_consulta;

    $consulta = "INSERT INTO gnupanel_servidores_secundarios(secundario,id_servidor,ip,es_dns,es_mx,subdominio_ns,subdominio_mx) values('$secundario',(SELECT id_servidor FROM gnupanel_servidores WHERE servidor='$servidor'),'$_ip',$_dns,$_mx,'$subdominio_ns','$subdominio_mx') ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = $retorno && $res_consulta;

    $id_servidor = "(SELECT id_servidor FROM gnupanel_servidores WHERE servidor='$servidor')" ;
    $consulta = "SELECT id_reseller,dominio from gnupanel_reseller WHERE EXISTS (SELECT * FROM gnupanel_ips_servidor WHERE gnupanel_ips_servidor.id_ip = gnupanel_reseller.id_ip AND gnupanel_ips_servidor.id_servidor = $id_servidor) ";
    $res_consulta = pg_query($conexion,$consulta);
    $dominios_reseller = pg_fetch_all($res_consulta);
    $retorno = $retorno && $res_consulta;

    if($_dns == 1)
	{
	if(is_array($dominios_reseller))
		{
		foreach($dominios_reseller as $arreglo_reseller)
			{
			$servidor_dns_reseller = $subdominio_ns.".".$arreglo_reseller['dominio'];
			$dominio_reseller = $arreglo_reseller['dominio'];
			$id_dominio_reseller = "(SELECT domain_id FROM gnupanel_pdns_records WHERE type = 'SOA' AND name = '$dominio_reseller')";

			$consulta = "INSERT INTO gnupanel_pdns_records(domain_id,name,type,content,ttl) VALUES($id_dominio_reseller ,'$dominio_reseller','NS','$servidor_dns_reseller',86400) ";
			$res_consulta = pg_query($conexion,$consulta);
			$retorno = $retorno && $res_consulta;

			$consulta = "INSERT INTO gnupanel_pdns_records_nat(domain_id,name,type,content,ttl) VALUES($id_dominio_reseller ,'$dominio_reseller','NS','$servidor_dns_reseller',86400) ";
			$res_consulta = pg_query($conexion,$consulta);
			$retorno = $retorno && $res_consulta;

			$consulta = "INSERT INTO gnupanel_pdns_records(domain_id,name,type,content,ttl) VALUES($id_dominio_reseller ,'$servidor_dns_reseller','A','$_ip',86400) ";
			$res_consulta = pg_query($conexion,$consulta);
			$retorno = $retorno && $res_consulta;

			$consulta = "INSERT INTO gnupanel_pdns_records_nat(domain_id,name,type,content,ttl) VALUES($id_dominio_reseller ,'$servidor_dns_reseller','A','$_ip',86400) ";
			$res_consulta = pg_query($conexion,$consulta);
			$retorno = $retorno && $res_consulta;

			$id_reseller = $arreglo_reseller['id_reseller'];
			$consulta = "SELECT dominio,cliente_de from gnupanel_usuario WHERE cliente_de = $id_reseller ";
			$res_consulta = pg_query($conexion,$consulta);
			$dominios_usuario = pg_fetch_all($res_consulta);
			$retorno = $retorno && $res_consulta;

			if(is_array($dominios_usuario))
				{
				foreach($dominios_usuario as $arreglo)
					{
					$dominio = $arreglo['dominio'];
					if(($dominio != $dominio_reseller))
						{
						$servidor_dns = $subdominio_ns.".".$arreglo['dominio'];
						$id_dominio = "(SELECT domain_id FROM gnupanel_pdns_records WHERE type = 'SOA' AND name = '$dominio')";
						$consulta = "INSERT INTO gnupanel_pdns_records(domain_id,name,type,content,ttl) VALUES($id_dominio ,'$dominio','NS','$servidor_dns_reseller',86400) ";
						$res_consulta = pg_query($conexion,$consulta);
						$retorno = $retorno && $res_consulta;

						$consulta = "INSERT INTO gnupanel_pdns_records_nat(domain_id,name,type,content,ttl) VALUES($id_dominio ,'$dominio','NS','$servidor_dns_reseller',86400) ";
						$res_consulta = pg_query($conexion,$consulta);
						$retorno = $retorno && $res_consulta;

						$consulta = "INSERT INTO gnupanel_pdns_records(domain_id,name,type,content,ttl) VALUES($id_dominio ,'$servidor_dns','A','$_ip',86400) ";
						$res_consulta = pg_query($conexion,$consulta);
						$retorno = $retorno && $res_consulta;

						$consulta = "INSERT INTO gnupanel_pdns_records_nat(domain_id,name,type,content,ttl) VALUES($id_dominio ,'$servidor_dns','A','$_ip',86400) ";
						$res_consulta = pg_query($conexion,$consulta);
						$retorno = $retorno && $res_consulta;
						}
					}
				}
			$consulta = "SELECT content FROM gnupanel_pdns_records WHERE type = 'SOA' AND name = '$dominio_reseller' ";
			$res_consulta = pg_query($conexion,$consulta);
			$retorno = $retorno && actualiza_soa($conexion,pg_fetch_result($res_consulta,0,0));
			}
		}
	}


    if($_mx == 1)
	{
	if(is_array($dominios_reseller))
		{
		foreach($dominios_reseller as $arreglo_reseller)
			{
			$prioridad = substr($subdominio_mx,-1,1)*10;

			$id_reseller = $arreglo_reseller['id_reseller'];
			$consulta = "SELECT dominio,cliente_de from gnupanel_usuario WHERE cliente_de = $id_reseller ";
			$res_consulta = pg_query($conexion,$consulta);
			$dominios_usuario = pg_fetch_all($res_consulta);
			$retorno = $retorno && $res_consulta;

			if(is_array($dominios_usuario))
				{
				foreach($dominios_usuario as $arreglo)
					{
					$dominio = $arreglo['dominio'];
					$servidor_mx = $subdominio_mx.".".$arreglo['dominio'];
					$id_dominio = "(SELECT domain_id FROM gnupanel_pdns_records WHERE type = 'SOA' AND name = '$dominio')";

					$consulta = "INSERT INTO gnupanel_pdns_records(domain_id,name,type,content,ttl,prio) VALUES($id_dominio ,'$dominio','MX','$servidor_mx',86400,$prioridad) ";
					$res_consulta = pg_query($conexion,$consulta);
					$retorno = $retorno && $res_consulta;

					$consulta = "INSERT INTO gnupanel_pdns_records_nat(domain_id,name,type,content,ttl,prio) VALUES($id_dominio ,'$dominio','MX','$servidor_mx',86400,$prioridad) ";
					$res_consulta = pg_query($conexion,$consulta);
					$retorno = $retorno && $res_consulta;

					$consulta = "INSERT INTO gnupanel_pdns_records(domain_id,name,type,content,ttl) VALUES($id_dominio ,'$servidor_mx','A','$_ip',86400) ";
					$res_consulta = pg_query($conexion,$consulta);
					$retorno = $retorno && $res_consulta;

					$consulta = "INSERT INTO gnupanel_pdns_records_nat(domain_id,name,type,content,ttl) VALUES($id_dominio ,'$servidor_mx','A','$_ip',86400) ";
					$res_consulta = pg_query($conexion,$consulta);
					$retorno = $retorno && $res_consulta;
					}
				}
			$dominio_reseller = $arreglo_reseller['dominio'];
			$consulta = "SELECT content FROM gnupanel_pdns_records WHERE type = 'SOA' AND name = '$dominio_reseller' ";
			$res_consulta = pg_query($conexion,$consulta);
			$retorno = $retorno && actualiza_soa($conexion,pg_fetch_result($res_consulta,0,0));
			}
		}
	}

    if($retorno)
	{
	$res_consulta = pg_query($conexion,"END");
	}
    else
	{
	$res_consulta = pg_query($conexion,"ROLLBACK");
	}

    pg_close($conexion);
    return $retorno;
}

function dame_ns_comienzo()
{
$subdominio_ns = NULL;
$subdominio_ns = array();
$subdominio_ns['ns2'] = "ns2";
$subdominio_ns['ns3'] = "ns3";
$subdominio_ns['ns4'] = "ns4";
$subdominio_ns['ns5'] = "ns5";
$subdominio_ns['ns6'] = "ns6";
$subdominio_ns['ns7'] = "ns7";
$subdominio_ns['ns8'] = "ns8";
$subdominio_ns['ns9'] = "ns9";
return $subdominio_ns;
}

function dame_mx_comienzo()
{
$subdominio_mx = NULL;
$subdominio_mx = array();
$subdominio_mx['mx2'] = "mx2";
$subdominio_mx['mx3'] = "mx3";
$subdominio_mx['mx4'] = "mx4";
$subdominio_mx['mx5'] = "mx5";
$subdominio_mx['mx6'] = "mx6";
$subdominio_mx['mx7'] = "mx7";
$subdominio_mx['mx8'] = "mx8";
$subdominio_mx['mx9'] = "mx9";
return $subdominio_mx;
}

function verifica_agregar_secundario($secundario,$servidor,$ip,$es_dns,$es_mx,$subdominio_ns,$subdominio_mx)
{
global $escribir;
$retorno = NULL;
$_dns = NULL;
$_mx = NULL;

if($es_dns=="true") $_dns = true;
if($es_mx=="true") $_mx = true;
if(!verifica_dato($secundario,NULL))
{
	$retorno = $escribir['carac_inv']." ";
}
else
{
	if(ya_es_secundario_de_este_primario($secundario,$servidor)) $retorno = $escribir['ya_esta_asignado']." ";
}
if(!verifica_dato($subdominio_ns,NULL,$_dns)) $retorno = $escribir['carac_inv']." ";
if(!verifica_dato($subdominio_mx,NULL,$_mx)) $retorno = $escribir['carac_inv']." ";
if(!verifica_ip($ip))
{
	$retorno = $escribir['ip_inv']." ";
}
else
{
	if(es_ip_de_otro_secundario($secundario,$ip)) $retorno = $escribir['ip_otro_secundario']." ";
}

if(existe_subdominio_ns_para_este_secundario($servidor,$subdominio_ns,$_dns)) $retorno = $escribir['existe_sub_ns']." ";

if(existe_subdominio_mx_para_este_secundario($servidor,$subdominio_mx,$_mx)) $retorno = $escribir['existe_sub_mx']." ";

return $retorno;
}

function ya_es_secundario_de_este_primario($secundario,$servidor)
{
global $servidor_db;
global $puerto_db;
global $database;
global $usuario_db;
global $passwd_db;
global $escribir;
$result = NULL;

$conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
$conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
$consulta = "SELECT * FROM gnupanel_servidores_secundarios WHERE id_servidor=(SELECT id_servidor FROM gnupanel_servidores WHERE servidor='$servidor') AND secundario='$secundario' ";
$res_consulta = pg_query($conexion,$consulta);
if(pg_num_rows($res_consulta)>0) $result = true;
pg_close($conexion);
return $result;
}

function es_ip_de_otro_secundario($secundario,$ip)
{
global $servidor_db;
global $puerto_db;
global $database;
global $usuario_db;
global $passwd_db;
global $escribir;
$result = NULL;
$_ip = implode(".",$ip);
$conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
$conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
$consulta = "SELECT * FROM gnupanel_servidores_secundarios WHERE ip='$_ip' AND NOT secundario='$secundario'";
$res_consulta = pg_query($conexion,$consulta);
if(pg_num_rows($res_consulta)>0) $result = true;
pg_close($conexion);
return $result;
}

function existe_subdominio_ns_para_este_secundario($servidor,$subdominio_ns,$_dns)
{
global $servidor_db;
global $puerto_db;
global $database;
global $usuario_db;
global $passwd_db;
global $escribir;
$result = NULL;
$conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
$conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
$consulta = "SELECT * FROM gnupanel_servidores_secundarios WHERE id_servidor=(SELECT id_servidor FROM gnupanel_servidores WHERE servidor='$servidor') AND subdominio_ns='$subdominio_ns' ";
$res_consulta = pg_query($conexion,$consulta);
if(pg_num_rows($res_consulta)>0) $result = true && $_dns;
pg_close($conexion);
return $result;
}

function existe_subdominio_mx_para_este_secundario($servidor,$subdominio_mx,$_mx)
{
global $servidor_db;
global $puerto_db;
global $database;
global $usuario_db;
global $passwd_db;
global $escribir;
$result = NULL;
$conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
$conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
$consulta = "SELECT * FROM gnupanel_servidores_secundarios WHERE id_servidor=(SELECT id_servidor FROM gnupanel_servidores WHERE servidor='$servidor') AND subdominio_mx='$subdominio_mx' ";
$res_consulta = pg_query($conexion,$consulta);
if(pg_num_rows($res_consulta)>0) $result = true && $_mx;
pg_close($conexion);
return $result;
}

function agregar_secundario_1($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_POST;
	global $_SESSION;
	$secundario = trim($_POST['secundario']);
	$servidor = trim($_POST['servidor']);
	$es_dns = trim($_POST['es_dns']);
	$es_mx = trim($_POST['es_mx']);
	$subdominio_ns = trim($_POST['subdominio_ns']);
	$subdominio_mx = trim($_POST['subdominio_mx']);
	$ip = array();
	$ip[0] = trim($_POST['ip_0']);
	$ip[1] = trim($_POST['ip_1']);
	$ip[2] = trim($_POST['ip_2']);
	$ip[3] = trim($_POST['ip_3']);
	$checkeo = verifica_agregar_secundario($secundario,$servidor,$ip,$es_dns,$es_mx,$subdominio_ns,$subdominio_mx);
	if($checkeo)
	{
	agregar_secundario_0($procesador,$checkeo);
	}
	else
	{
	$escriba = NULL;
		if(agregar_secundario($secundario,$servidor,$ip,$es_dns,$es_mx,$subdominio_ns,$subdominio_mx))
		{
		$escriba = $escribir['exito'];
		}
		else
		{
		$escriba = $escribir['fracaso'];
		}
		print "<div id=\"formulario\" > \n";
		print "<br><br>$escriba<br> \n";
		print "</div> \n";
		print "<div id=\"botones\" > \n";
		print "</div> \n";
		print "<div id=\"ayuda\" > \n";
		$escriba = $escribir['help'];
		print "$escriba\n";
		print "</div> \n";
	}
}

function agregar_secundario_0($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_POST;
	global $_SESSION;

	$secundario = trim($_POST['secundario']);
	$es_dns = trim($_POST['es_dns']);
	$es_mx = trim($_POST['es_mx']);
	$subdominio_ns = trim($_POST['subdominio_ns']);
	$subdominio_mx = trim($_POST['subdominio_mx']);

	if(!isset($_POST['subdominio_ns']))
	{
	$subdominio_ns = dame_ns_comienzo();
	}

	if(!isset($_POST['subdominio_mx']))
	{
	$subdominio_mx = dame_mx_comienzo();
	}

	$servidores = dame_servidores();
	$ip = array();
	$ip[0] = trim($_POST['ip_0']);
	$ip[1] = trim($_POST['ip_1']);
	$ip[2] = trim($_POST['ip_2']);
	$ip[3] = trim($_POST['ip_3']);

	print "<div id=\"formulario\" > \n";
	if($mensaje) print "$mensaje <br/> \n";
	print "<ins> \n";
	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table> \n";
	genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);
	genera_fila_formulario("secundario",$secundario,'text',28,!$mensaje);
	genera_fila_formulario("servidor",$servidores,"select_ip",NULL,NULL);
	genera_fila_formulario("ip",$ip,'ip',NULL,!$mensaje);
	genera_fila_formulario("es_dns",$es_dns,'check_box',NULL,!$mensaje);
	genera_fila_formulario("es_mx",$es_mx,'check_box',NULL,!$mensaje);
	genera_fila_formulario("subdominio_ns",$subdominio_ns,'select_ip',9,!$mensaje,NULL);
	genera_fila_formulario("subdominio_mx",$subdominio_mx,'select_ip',9,!$mensaje,NULL);
	genera_fila_formulario("ingresando","1",'hidden',NULL,true);
	genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);
	genera_fila_formulario("resetea",NULL,'reset',NULL,true);
	genera_fila_formulario("agrega",NULL,'submit',NULL,true);

	print "</table> \n";
	print "</form> \n";
	print "</ins> \n";
	print "</div> \n";
	print "<div id=\"botones\" > \n";
	print "</div> \n";
	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";
}


function agregar_secundario_init($nombre_script)
{
	global $_POST;

	$paso = $_POST['ingresando'];
	switch($paso)
	{
		case "1":
		agregar_secundario_1($nombre_script,NULL);
		break;

		default:
		agregar_secundario_0($nombre_script,NULL);
	}
}


?>
