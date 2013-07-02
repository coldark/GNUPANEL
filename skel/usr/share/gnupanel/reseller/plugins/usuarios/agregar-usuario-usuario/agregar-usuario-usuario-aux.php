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

function agrega_dominio_en_pdns($conexion,$id_usuario,$dominio)
{
$consulta = "INSERT INTO gnupanel_pdns_domains(id,name,type) VALUES($id_usuario,'$dominio','MASTER') ";
$res_consulta = pg_query($conexion,$consulta);
$checkeo = $res_consulta;

$consulta = "INSERT INTO gnupanel_pdns_domains_nat(id,name,type) VALUES($id_usuario,'$dominio','MASTER') ";
$res_consulta = pg_query($conexion,$consulta);
$checkeo = $checkeo && $res_consulta;

return $checkeo;
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

function agregar_usuario_en_apache($conexion,$id_reseller,$usuario,$dominio)
   {
   global $dir_base_web;
   $checkeo = NULL;

    $consulta = "SELECT admin FROM gnupanel_admin WHERE id_admin=(SELECT cliente_de FROM gnupanel_reseller WHERE id_reseller=$id_reseller) ";
    $res_consulta = pg_query($conexion,$consulta);
    $admin = pg_fetch_result ($res_consulta,0,0);
    $checkeo = $res_consulta;

    $consulta = "(SELECT reseller,dominio,id_ip FROM gnupanel_reseller WHERE id_reseller = $id_reseller)" ;
    $res_consulta = pg_query($conexion,$consulta);
    $reseller = pg_fetch_result ($res_consulta,0,0);
    $dominio_reseller = pg_fetch_result ($res_consulta,0,1);
    $id_ip = pg_fetch_result ($res_consulta,0,2);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "SELECT usa_nat FROM gnupanel_ips_servidor WHERE id_ip = $id_ip";
    $res_consulta = pg_query($conexion,$consulta);
    $usa_nat = pg_fetch_result ($res_consulta,0,0);
    $checkeo = $checkeo && $res_consulta;

    $document_root = $dir_base_web.$admin."/".$reseller."@".$dominio_reseller."/".$dominio."/subdominios/";
    $ip = "";
    if($usa_nat==1)
    {
    $ip = "(SELECT ip_privada FROM gnupanel_ips_servidor WHERE id_ip=$id_ip)";
    }
    else
    {
    $ip = "(SELECT ip_publica FROM gnupanel_ips_servidor WHERE id_ip=$id_ip)";
    }

    $server_admin = $usuario."@".$dominio;

    $subdom_def = "gnupanel.".$dominio;
    $document_root_def = "/usr/share/gnupanel/gnupanel";
    $redirect_def = "https://gnupanel.".$dominio_reseller ;
    $redirect_def = pone_barra($redirect_def);
    //$consulta = "INSERT INTO gnupanel_apacheconf(id_subdominio,id_dominio,subdominio,ip,documentroot,serveradmin,redirigir,dominio_destino) VALUES((SELECT id FROM gnupanel_pdns_records WHERE name='$subdom_def' AND type='A'),(SELECT id_usuario FROM gnupanel_usuario WHERE dominio='$dominio'),'gnupanel',$ip,'$document_root_def','$server_admin',1,'$redirect_def') ";
    $consulta = "INSERT INTO gnupanel_apacheconf(id_subdominio,id_dominio,subdominio,ip,documentroot,serveradmin) VALUES((SELECT id FROM gnupanel_pdns_records WHERE name='$subdom_def' AND type='A'),(SELECT id_usuario FROM gnupanel_usuario WHERE dominio='$dominio'),'gnupanel',$ip,'$document_root_def','$server_admin') ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $subdom_def = $dominio;
    $document_root_def = $document_root."_sin_subdominio";
    $consulta = "INSERT INTO gnupanel_apacheconf(id_subdominio,id_dominio,subdominio,ip,documentroot,serveradmin) VALUES((SELECT id FROM gnupanel_pdns_records WHERE name='$subdom_def' AND type='A'),(SELECT id_usuario FROM gnupanel_usuario WHERE dominio='$dominio'),'',$ip,'$document_root_def','$server_admin') ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $subdom_def = "www.".$dominio;
    $document_root_def = $document_root."www";
    $consulta = "INSERT INTO gnupanel_apacheconf(id_subdominio,id_dominio,subdominio,ip,documentroot,serveradmin) VALUES((SELECT id FROM gnupanel_pdns_records WHERE name='$subdom_def' AND type='A'),(SELECT id_usuario FROM gnupanel_usuario WHERE dominio='$dominio'),'www',$ip,'$document_root_def','$server_admin') ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "UPDATE apache_dominios_conf SET configurar = 1 WHERE id = 1 ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    return $checkeo;
   }

function agregar_usuario_en_est($conexion,$id_reseller,$dominio,$tope_espacio,$tope_transferencias)
   {
	$checkeo = NULL;
	$consulta = "INSERT INTO gnupanel_transferencias(id_dominio,dominio,dueno,tope) VALUES((SELECT id_usuario FROM gnupanel_usuario WHERE dominio='$dominio'),'$dominio',$id_reseller,$tope_transferencias) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $res_consulta;

	$consulta = "INSERT INTO gnupanel_espacio(id_dominio,dominio,dueno,tope) VALUES((SELECT id_usuario FROM gnupanel_usuario WHERE dominio='$dominio'),'$dominio',$id_reseller,$tope_espacio) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;
	return $checkeo;
   }

function agregar_serv_backup_en_dns($conexion,$id_usuario,$dominio)
    {
    global $_SESSION;
    $id_reseller = $_SESSION['id_reseller'];
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
		$consulta = "INSERT INTO gnupanel_pdns_records(domain_id,name,content,type,ttl,prio,change_date) VALUES($id_usuario,'$dominio','$contenido','$tipo',$ttl,$prio,$change_date) ";
		$res_consulta = pg_query($conexion,$consulta);
		$checkeo = $checkeo && $res_consulta;

		$contenido = $arreglo['subdominio_ns'].".".$dominio_reseller;
		$tipo = "NS";
		$ttl = "86400";
		$prio = "NULL";
		$change_date = time();
		$consulta = "INSERT INTO gnupanel_pdns_records_nat(domain_id,name,content,type,ttl,prio,change_date) VALUES($id_usuario,'$dominio','$contenido','$tipo',$ttl,$prio,$change_date) ";
		$res_consulta = pg_query($conexion,$consulta);
		$checkeo = $checkeo && $res_consulta;

		$name = $arreglo['subdominio_ns'].".".$dominio;
		$contenido = $arreglo['ip'];
		$tipo = "A";
		$ttl = "86400";
		$prio = "NULL";
		$change_date = time();
		$consulta = "INSERT INTO gnupanel_pdns_records(domain_id,name,content,type,ttl,prio,change_date) VALUES($id_usuario,'$name','$contenido','$tipo',$ttl,$prio,$change_date) ";
		$res_consulta = pg_query($conexion,$consulta);
		$checkeo = $checkeo && $res_consulta;

		$name = $arreglo['subdominio_ns'].".".$dominio;
		$contenido = $arreglo['ip'];
		$tipo = "A";
		$ttl = "86400";
		$prio = "NULL";
		$change_date = time();
		$consulta = "INSERT INTO gnupanel_pdns_records_nat(domain_id,name,content,type,ttl,prio,change_date) VALUES($id_usuario,'$name','$contenido','$tipo',$ttl,$prio,$change_date) ";
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
		$consulta = "INSERT INTO gnupanel_pdns_records(domain_id,name,content,type,ttl,prio,change_date) VALUES($id_usuario,'$dominio','$contenido','$tipo',$ttl,$prio,$change_date) ";
		$res_consulta = pg_query($conexion,$consulta);
		$checkeo = $checkeo && $res_consulta;

		$contenido = $arreglo['subdominio_mx'].".".$dominio;
		$tipo = "MX";
		$ttl = "86400";
		$prio = $prio_s;
		$change_date = time();
		$consulta = "INSERT INTO gnupanel_pdns_records_nat(domain_id,name,content,type,ttl,prio,change_date) VALUES($id_usuario,'$dominio','$contenido','$tipo',$ttl,$prio,$change_date) ";
		$res_consulta = pg_query($conexion,$consulta);
		$checkeo = $checkeo && $res_consulta;

		$name = $arreglo['subdominio_mx'].".".$dominio;
		$contenido = $arreglo['ip'];
		$tipo = "A";
		$ttl = "86400";
		$prio = "NULL";
		$change_date = time();
		$consulta = "INSERT INTO gnupanel_pdns_records(domain_id,name,content,type,ttl,prio,change_date) VALUES($id_usuario,'$name','$contenido','$tipo',$ttl,$prio,$change_date) ";
		$res_consulta = pg_query($conexion,$consulta);
		$checkeo = $checkeo && $res_consulta;

		$name = $arreglo['subdominio_mx'].".".$dominio;
		$contenido = $arreglo['ip'];
		$tipo = "A";
		$ttl = "86400";
		$prio = "NULL";
		$change_date = time();
		$consulta = "INSERT INTO gnupanel_pdns_records_nat(domain_id,name,content,type,ttl,prio,change_date) VALUES($id_usuario,'$name','$contenido','$tipo',$ttl,$prio,$change_date) ";
		$res_consulta = pg_query($conexion,$consulta);
		$checkeo = $checkeo && $res_consulta;
		$prio_s = $prio_s + 10;
		}
	}
	}
return $checkeo;
    }

function agregar_dominio_en_dns($conexion,$usuario,$dominio)
    {
    global $_SESSION;
    $retorno = NULL;
    $checkeo = NULL;
    $id_reseller = $_SESSION['id_reseller'];

    $consulta = "SELECT id_usuario FROM gnupanel_usuario WHERE usuario='$usuario' AND dominio='$dominio' ";
    $res_consulta = pg_query($conexion,$consulta);
    $id_usuario = pg_fetch_result($res_consulta,0,0);
    $checkeo = $res_consulta;

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

    $consulta = "SELECT ip_publica,usa_nat FROM gnupanel_ips_servidor WHERE id_ip = (SELECT id_ip from gnupanel_reseller WHERE id_reseller = $id_reseller) ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_assoc ($res_consulta,0);
    $ip_publica = $retorno['ip_publica'];
    $usa_nat = $retorno['usa_nat'];
    $checkeo = $checkeo && $res_consulta;

    $id_ip = "(SELECT id_ip FROM gnupanel_reseller WHERE id_reseller = $id_reseller)";
    $consulta = "SELECT id_servidor FROM gnupanel_ips_servidor WHERE id_ip = $id_ip ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_assoc ($res_consulta,0);
    $id_servidor = $retorno['id_servidor'];
    $checkeo = $checkeo && $res_consulta;
    $ip_default = dame_ip_default($id_servidor);
    $checkeo = $checkeo && agrega_dominio_en_pdns($conexion,$id_usuario,$dominio);

    $contenido = $soa_record;
    $tipo = "SOA";
    $ttl = "86400";
    $prio = "NULL";
    $checkeo = $checkeo && agrega_record_en_pdns($conexion,$usa_nat,$id_usuario,$dominio,$contenido,$tipo,$ttl,$prio);

    $contenido = "ns1.".$dominio_reseller;
    $tipo = "NS";
    $checkeo = $checkeo && agrega_record_en_pdns($conexion,$usa_nat,$id_usuario,$dominio,$contenido,$tipo,$ttl,$prio);

    $contenido = "mx1.".$dominio;
    $tipo = "MX";
    $prio = 10;
    $checkeo = $checkeo && agrega_record_en_pdns($conexion,$usa_nat,$id_usuario,$dominio,$contenido,$tipo,$ttl,$prio);

    $contenido = "v=spf1 ip4:".$ip_default." ip4:".$ip_publica." a mx -all";
    if($ip_default==$ip_publica) $contenido =  "v=spf1 ip4:".$ip_default." a mx -all";
    $tipo = "TXT";
    $prio = "NULL";
    $checkeo = $checkeo && agrega_record_en_pdns($conexion,$usa_nat,$id_usuario,$dominio,$contenido,$tipo,$ttl,$prio);

    $contenido = $ip_publica;
    $name = $dominio;
    $tipo = "A";
    $prio = "NULL";
    $checkeo = $checkeo && agrega_record_en_pdns($conexion,$usa_nat,$id_usuario,$name,$contenido,$tipo,$ttl,$prio);

    $name = "ns1.".$dominio;
    $checkeo = $checkeo && agrega_record_en_pdns($conexion,$usa_nat,$id_usuario,$name,$contenido,$tipo,$ttl,$prio);

    $name = "mx1.".$dominio;
    $checkeo = $checkeo && agrega_record_en_pdns($conexion,$usa_nat,$id_usuario,$name,$contenido,$tipo,$ttl,$prio);

    $name = "www.".$dominio;
    $checkeo = $checkeo && agrega_record_en_pdns($conexion,$usa_nat,$id_usuario,$name,$contenido,$tipo,$ttl,$prio);

    $name = "smtp.".$dominio;
    $checkeo = $checkeo && agrega_record_en_pdns($conexion,$usa_nat,$id_usuario,$name,$contenido,$tipo,$ttl,$prio);

    $name = "pop.".$dominio;
    $checkeo = $checkeo && agrega_record_en_pdns($conexion,$usa_nat,$id_usuario,$name,$contenido,$tipo,$ttl,$prio);

    $name = "gnupanel.".$dominio;
    $checkeo = $checkeo && agrega_record_en_pdns($conexion,$usa_nat,$id_usuario,$name,$contenido,$tipo,$ttl,$prio);

    $name = "mail.".$dominio;
    $checkeo = $checkeo && agrega_record_en_pdns($conexion,$usa_nat,$id_usuario,$name,$contenido,$tipo,$ttl,$prio);

    $name = "ftp.".$dominio;
    $checkeo = $checkeo && agrega_record_en_pdns($conexion,$usa_nat,$id_usuario,$name,$contenido,$tipo,$ttl,$prio);

    return $checkeo;
    }

function agregar_usuario_en_postfix($conexion,$usuario,$dominio,$pasaporte_crypt)
    {
    global $_SESSION;
    global $gid_postfix;
    global $mailquota;
    $id_reseller = $_SESSION['id_reseller'];
    $retorno = NULL;
    $checkeo = NULL;

    $consulta = "SELECT admin FROM gnupanel_admin WHERE id_admin=(SELECT cliente_de FROM gnupanel_reseller WHERE id_reseller=$id_reseller) ";
    $res_consulta = pg_query($conexion,$consulta);
    $admin = pg_fetch_result ($res_consulta,0,0);
	$checkeo = $res_consulta;

    $consulta = "SELECT reseller,dominio FROM gnupanel_reseller WHERE id_reseller=$id_reseller ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_assoc($res_consulta,0);
    $reseller = $retorno['reseller'];
    $dominio_reseller = $retorno['dominio'];

	$id_usuario = "(SELECT id_usuario FROM gnupanel_usuario WHERE usuario='$usuario' AND dominio='$dominio')";
	$consulta = "INSERT INTO gnupanel_postfix_transport(id_dominio,dominio) VALUES($id_usuario,'$dominio') ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $res_consulta;

	$address = $usuario."@".$dominio;
	$maildir = $admin."/".$reseller."@".$dominio_reseller."/".$dominio."/".$usuario."@".$dominio."/";
	$id_dominio = $id_usuario;
	$consulta = "INSERT INTO gnupanel_postfix_mailuser(address,dominio,passwd,maildir,mailquota,id_dominio,gid) VALUES('$address','$dominio','$pasaporte_crypt','$maildir',$mailquota,$id_dominio,$gid_postfix) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;

	$consulta = "INSERT INTO gnupanel_postfix_virtual VALUES ('$address','$address') " ;
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;

    return $checkeo;

    }



function agregar_usuario_en_proftpd($conexion,$usuario,$dominio,$pasaporte_crypt,$id_reseller,$espacio,$transferencia)
    {
    global $dir_base_web;
    $retorno = NULL;
    $checkeo = NULL;

    $consulta = "SELECT admin FROM gnupanel_admin WHERE id_admin=(SELECT cliente_de FROM gnupanel_reseller WHERE id_reseller=$id_reseller) ";
    $res_consulta = pg_query($conexion,$consulta);
    $admin = pg_fetch_result ($res_consulta,0,0);
	$checkeo = $res_consulta;


    $consulta = "SELECT reseller,dominio FROM gnupanel_reseller WHERE id_reseller=$id_reseller ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_assoc($res_consulta,0);
    $reseller = $retorno['reseller'];
    $dominio_reseller = $retorno['dominio'];
	$checkeo = $checkeo && $res_consulta;

	$address = $usuario."@".$dominio;

	$maildir = $dir_base_web.$admin."/".$reseller."@".$dominio_reseller."/".$dominio;

	$id_dominio = "(SELECT id_usuario FROM gnupanel_usuario WHERE usuario='$usuario' AND dominio='$dominio')";
	$consulta = "INSERT INTO gnupanel_proftpd_ftpuser (userid,passwd,homedir,id_dominio,dominio) VALUES('$address','$pasaporte_crypt','$maildir',$id_dominio,'$dominio') ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;


	$consulta = "INSERT INTO gnupanel_proftpd_ftpgroup (groupname,members) VALUES('$address','$address') ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;

	$espacio_p = $espacio * 1024 * 1024;
	$transferencia_p = $transferencia * 1024 * 1024;
	$consulta = "INSERT INTO gnupanel_proftpd_ftpquotalimits (name,quota_type,per_session,limit_type,bytes_in_avail,bytes_out_avail,bytes_xfer_avail,files_in_avail,files_out_avail,files_xfer_avail) VALUES('$address','user','false','soft',$espacio_p,0,$transferencia_p,0,0,0) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;


    return $checkeo;

    }

function verifica_agregar_usuario_usuario_0($usuario,$dominio,$correo_contacto,$pasaporte_0,$pasaporte_1,$plan,$vigencia,$moneda,$primer_nombre,$segundo_nombre,$apellido,$compania,$pais,$provincia,$ciudad,$calle,$numero,$piso,$departamento,$codpostal,$telefono,$telefono_celular,$fax)
{
    global $escribir;
	$retorno = NULL;
	if(!verifica_dato($usuario,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($dominio,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_correo($correo_contacto)) $retorno = $escribir['correo_inv']." ";
	if(!verifica_dato($pasaporte_0,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($pasaporte_1,NULL)) $retorno = $escribir['carac_inv']." ";
	if($pasaporte_0 != $pasaporte_1) $retorno = $escribir['distintos']." ";
	if(existe_dominio_reseller($dominio)) $retorno = $escribir['existe']." ";
	if(!hay_espacio($plan,$vigencia,$moneda)) $retorno = $escribir['sin_espacio']." ";
	if(!hay_transferencia($plan,$vigencia,$moneda)) $retorno = $escribir['sin_transferencia']." ";
	if(!verifica_dato($primer_nombre,NULL,true,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($segundo_nombre,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($apellido,NULL,true,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($compania,NULL,true,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($pais,NULL,true,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($provincia,NULL,true,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($ciudad,NULL,true,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($calle,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($numero,true,NULL,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($piso,true,NULL,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($departamento,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($codpostal,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($telefono,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($telefono_celular,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($fax,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
	if(strlen($pasaporte_0)<8) $retorno = $escribir['pocos_carac']." ";
	$hay_cuentas = hay_cuentas($plan,$vigencia,$moneda);
	if($hay_cuentas) $retorno = $hay_cuentas." "; 
	return $retorno;
}

function hay_cuentas($plan,$vigencia,$moneda)
{
	global $servidor_db;
	global $puerto_db;
	global $database;
	global $usuario_db;
	global $passwd_db;
	global $_SESSION;
	global $espacio_servidor;
	global $escribir;
	$result = NULL;
	$retorno = NULL;
	$id_reseller = $_SESSION['id_reseller'];
	
	$conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
	$conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
	$consulta = "SELECT dominios,subdominios,dominios_parking,bases_postgres,bases_mysql,cuentas_correo,listas_correo,cuentas_ftp FROM gnupanel_reseller_plan WHERE id_reseller = $id_reseller ";
	$res_consulta = pg_query($conexion,$consulta);
	$plan_reseller = pg_fetch_assoc($res_consulta,0);
	pg_free_result($res_consulta);

	$consulta = "SELECT subdominios,dominios_parking,bases_postgres,bases_mysql,cuentas_correo,listas_correo,cuentas_ftp FROM gnupanel_usuarios_planes WHERE plan = '$plan' AND vigencia = $vigencia AND moneda = $moneda AND id_dueno = $id_reseller ";
	$res_consulta = pg_query($conexion,$consulta);
	$plan_usuario = pg_fetch_assoc($res_consulta);
	pg_free_result($res_consulta);

	$consulta = "SELECT sum(subdominios) AS subdominios,sum(dominios_parking) AS dominios_parking,sum(bases_postgres) AS bases_postgres,sum(bases_mysql) AS bases_mysql,sum(cuentas_correo) AS cuentas_correo,sum(listas_correo) AS listas_correo,sum(cuentas_ftp) AS cuentas_ftp FROM gnupanel_usuario_estado WHERE EXISTS (SELECT * FROM gnupanel_usuario WHERE gnupanel_usuario.id_usuario = gnupanel_usuario_estado.id_usuario AND gnupanel_usuario.cliente_de = $id_reseller) ";
	$res_consulta = pg_query($conexion,$consulta);
	$consumo_total = pg_fetch_assoc($res_consulta);
	pg_free_result($res_consulta);

	$consulta = "SELECT count(id_usuario) FROM gnupanel_usuario WHERE cliente_de = $id_reseller ";
	$res_consulta = pg_query($conexion,$consulta);
	$cant_dominios = pg_fetch_result($res_consulta,0,0);
	pg_free_result($res_consulta);
	
	if(is_array($plan_reseller))
	{
	foreach($plan_reseller as $llave => $valor)
	{
	if($valor != -1)
		{
		if($llave == 'dominios')
			{
			if($cant_dominios >= $valor) $retorno = $escribir['no_dominios'];
			}
		else
			{
			if(($consumo_total[$llave] + $plan_usuario[$llave]) > $valor)
				{
				$clave = "no_".$llave;
				$retorno = $escribir[$clave];
				}
			}
		}
	}
	}

	pg_close($conexion);
	return $retorno;
}

function hay_espacio($plan,$vigencia,$moneda)
{
	global $servidor_db;
	global $puerto_db;
	global $database;
	global $usuario_db;
	global $passwd_db;
	global $_SESSION;
	global $espacio_servidor;
	$result = NULL;
	$id_reseller = $_SESSION['id_reseller'];
	$espacio_usado = 0;
	$espacio_disponible = 0;
	$espacio_plan = 0;
	$conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
	$conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
	$consulta = "SELECT sum(total) FROM gnupanel_espacio WHERE EXISTS (SELECT * FROM gnupanel_usuario WHERE gnupanel_usuario.id_usuario = gnupanel_espacio.id_dominio AND gnupanel_usuario.cliente_de = $id_reseller) ";
	$res_consulta = pg_query($conexion,$consulta);
	$espacio_usado = pg_fetch_result($res_consulta,0,0);
	
	pg_free_result($res_consulta);
	
	$retorno = NULL;
	$consulta = "SELECT espacio FROM gnupanel_usuarios_planes WHERE plan = '$plan' AND vigencia = $vigencia AND moneda = $moneda AND id_dueno = $id_reseller ";
	$res_consulta = pg_query($conexion,$consulta);
	$espacio_plan = pg_fetch_result($res_consulta,0,0);
	pg_free_result($res_consulta);

	$consulta = "SELECT espacio FROM gnupanel_reseller_plan WHERE id_reseller = $id_reseller ";
	$res_consulta = pg_query($conexion,$consulta);
	$espacio_disponible = pg_fetch_result($res_consulta,0,0);
	pg_free_result($res_consulta);

	$consulta = "SELECT cliente_de FROM gnupanel_reseller WHERE id_reseller = $id_reseller ";
	$res_consulta = pg_query($conexion,$consulta);
	$id_admin = pg_fetch_result($res_consulta,0,0);
	pg_free_result($res_consulta);
	
	if(($espacio_plan + $espacio_usado)<=$espacio_disponible) $result = true;
	pg_close($conexion);
	return $result;
}

function hay_transferencia($plan,$vigencia,$moneda)
{
	global $servidor_db;
	global $puerto_db;
	global $database;
	global $usuario_db;
	global $passwd_db;
	global $_SESSION;
	global $transferencia_servidor;
	$result = NULL;
	$id_reseller = $_SESSION['id_reseller'];
	$transferencia_usada = 0;
	$transferencia_disponible = 0;
	$transferencia_plan = 0;
	$conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
	$conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
	$consulta = "SELECT sum(total)/1048576 FROM gnupanel_transferencias WHERE EXISTS (SELECT * FROM gnupanel_usuario WHERE gnupanel_usuario.id_usuario = gnupanel_transferencias.id_dominio AND gnupanel_usuario.cliente_de = $id_reseller) ";
	$res_consulta = pg_query($conexion,$consulta);
	$transferencia_usada = pg_fetch_result($res_consulta,0,0);
	
	pg_free_result($res_consulta);
	
	$retorno = NULL;
	$consulta = "SELECT transferencia FROM gnupanel_usuarios_planes WHERE plan = '$plan' AND vigencia = $vigencia AND moneda = $moneda AND id_dueno = $id_reseller ";
	$res_consulta = pg_query($conexion,$consulta);
	$transferencia_plan = pg_fetch_result($res_consulta,0,0);
	pg_free_result($res_consulta);

	$consulta = "SELECT transferencia FROM gnupanel_reseller_plan WHERE id_reseller = $id_reseller ";
	$res_consulta = pg_query($conexion,$consulta);
	$transferencia_disponible = pg_fetch_result($res_consulta,0,0);
	pg_free_result($res_consulta);

	$consulta = "SELECT cliente_de FROM gnupanel_reseller WHERE id_reseller = $id_reseller ";
	$res_consulta = pg_query($conexion,$consulta);
	$id_admin = pg_fetch_result($res_consulta,0,0);
	pg_free_result($res_consulta);
	
	if(($transferencia_plan + $transferencia_usada)<=$transferencia_disponible) $result = true;
	pg_close($conexion);
	return $result;
}

function agregar_usuario_usuario($id_reseller,$usuario,$dominio,$correo_contacto,$pasaporte,$plan,$vigencia,$moneda,$idioma,$servidor,$primer_nombre,$segundo_nombre,$apellido,$compania,$pais,$provincia,$ciudad,$calle,$numero,$piso,$departamento,$codpostal,$telefono,$telefono_celular,$fax)
{
	global $servidor_db;
	global $puerto_db;
	global $database;
	global $usuario_db;
	global $passwd_db;
	global $_SESSION;
	global $tema_default_config;

	$retorno = NULL;
	$checkeo = NULL;
	$pasaporte_crypt = gnupanel_crypt($pasaporte);

	$conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
	$conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
	$consulta = "BEGIN";
	$res_consulta = pg_query($conexion,$consulta);
	$consulta = "INSERT INTO gnupanel_usuario(usuario,dominio,correo_contacto,password,cliente_de) values('$usuario','$dominio','$correo_contacto','$pasaporte_crypt',$id_reseller) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $res_consulta;
	$consulta = "SELECT id_usuario FROM gnupanel_usuario WHERE usuario = '$usuario' AND dominio = '$dominio' ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;
	$id_usuario = pg_fetch_result ($res_consulta,0,0);
	$consulta = "SELECT * FROM gnupanel_usuarios_planes WHERE plan = '$plan' AND vigencia = $vigencia AND moneda = $moneda AND id_dueno = $id_reseller ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;
	$retorno = pg_fetch_assoc($res_consulta,0);
	$id_plan = $retorno['id_plan'];
	$subdominios = $retorno['subdominios'];
	$dominios_parking = $retorno['dominios_parking'];
	$espacio = $retorno['espacio'];
	$transferencia = $retorno['transferencia'];
	$bases_postgres = $retorno['bases_postgres'];
	$bases_mysql = $retorno['bases_mysql'];
	$cuentas_correo = $retorno['cuentas_correo'];
	$listas_correo = $retorno['listas_correo'];
	$cuentas_ftp = $retorno['cuentas_ftp'];
	
	$consulta = "INSERT INTO gnupanel_usuario_plan(id_usuario,id_plan,vencimiento_plan,vigencia_plan,subdominios,dominios_parking,espacio,transferencia,bases_postgres,bases_mysql,cuentas_correo,listas_correo,cuentas_ftp) VALUES($id_usuario,$id_plan,now(),$vigencia,$subdominios,$dominios_parking,$espacio,$transferencia,$bases_postgres,$bases_mysql,$cuentas_correo,$listas_correo,$cuentas_ftp) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;
	$consulta = "INSERT INTO gnupanel_usuario_data(id_usuario) VALUES($id_usuario) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;
	
	$consulta = "INSERT INTO gnupanel_usuario_estado(id_usuario,vencimiento_plan,vigencia_plan,subdominios) VALUES($id_usuario,(SELECT vencimiento_plan FROM gnupanel_usuario_plan WHERE id_usuario = $id_usuario),$vigencia,1) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;
	
	$consulta = "INSERT INTO gnupanel_usuario_sets(id_usuario,id_tema) VALUES($id_usuario,(SELECT id_tema FROM gnupanel_temas WHERE tema = '$tema_default_config')) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;

	$consulta = "INSERT INTO gnupanel_usuario_lang(id_usuario,idioma) VALUES($id_usuario,'$idioma') ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;

	$consulta = "INSERT INTO gnupanel_divisas_usuario(id_usuario) VALUES($id_usuario) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;
	$checkeo = $checkeo && agregar_data_usuario($conexion,$id_usuario,$primer_nombre,$segundo_nombre,$apellido,$compania,$pais,$provincia,$ciudad,$calle,$numero,$piso,$departamento,$codpostal,$telefono,$telefono_celular,$fax);

	$checkeo = $checkeo && agregar_dominio_en_dns($conexion,$usuario,$dominio);

	$checkeo = $checkeo && agregar_serv_backup_en_dns($conexion,$id_usuario,$dominio);

	$checkeo = $checkeo && agregar_usuario_en_postfix($conexion,$usuario,$dominio,$pasaporte_crypt);

	$checkeo = $checkeo && agregar_usuario_en_proftpd($conexion,$usuario,$dominio,$pasaporte_crypt,$id_reseller,$espacio,$transferencia);

	$checkeo = $checkeo && agregar_usuario_en_est($conexion,$id_reseller,$dominio,$espacio,$transferencia*1024*1024);

	$checkeo = $checkeo && agregar_usuario_en_apache($conexion,$id_reseller,$usuario,$dominio);


	if($checkeo)
	{
	$consulta = "END";
	$res_consulta = pg_query($conexion,$consulta);
	$retorno = $id_reseller;
	}
	else
	{
	$consulta = "ROLLBACK";
	$res_consulta = pg_query($conexion,$consulta);
	$retorno = NULL;
	}
	pg_close($conexion);
	return $retorno;
}

function agregar_data_usuario($conexion,$id_usuario,$primer_nombre,$segundo_nombre,$apellido,$compania,$pais,$provincia,$ciudad,$calle,$numero,$piso,$departamento,$codpostal,$telefono,$telefono_celular,$fax)
{
global $_SESSION;

$retorno = NULL;
$checkeo = true;
$consulta = "";
$res_consulta = NULL;
$id_reseller = $_SESSION['id_reseller'];

$consulta = "";

if(strlen($primer_nombre)>0) $consulta = $consulta."primer_nombre = '$primer_nombre',";
if(strlen($segundo_nombre)>0) $consulta = $consulta."segundo_nombre = '$segundo_nombre',";
if(strlen($apellido)>0) $consulta = $consulta."apellido = '$apellido',";
if(strlen($compania)>0) $consulta = $consulta."compania = '$compania',";
if(strlen($pais)>0) $consulta = $consulta."pais = '$pais',";
if(strlen($provincia)>0) $consulta = $consulta."provincia = '$provincia',";
if(strlen($ciudad)>0) $consulta = $consulta."ciudad = '$ciudad',";
if(strlen($calle)>0) $consulta = $consulta."calle = '$calle',";
if(strlen($numero)>0) $consulta = $consulta."numero = $numero,";
if(strlen($piso)>0) $consulta = $consulta."piso = $piso,";
if(strlen($departamento)>0) $consulta = $consulta."departamento = '$departamento',";
if(strlen($codpostal)>0) $consulta = $consulta."codpostal = '$codpostal',";
if(strlen($telefono)>0) $consulta = $consulta."telefono = '$telefono',";
if(strlen($telefono_celular)>0) $consulta = $consulta."telefono_celular = '$telefono_celular',";
if(strlen($fax)>0) $consulta = $consulta."fax = '$fax',";
$consulta = rtrim($consulta,",");
if(strlen($consulta)>0)
{
$consulta_ini = "UPDATE gnupanel_usuario_data SET ";
$consulta_fin = " WHERE id_usuario = $id_usuario";
$consulta_tot = $consulta_ini.$consulta.$consulta_fin;
$res_consulta = pg_query($conexion,$consulta_tot);
$checkeo = $res_consulta;
}
return $checkeo;
}

/*
function dame_ip_disponible()
{
	global $servidor_db;
	global $puerto_db;
	global $database;
	global $usuario_db;
	global $passwd_db;
	$retorno = NULL;
	$result = NULL;
	$conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
	$conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
	$consulta = "SELECT id_ip from gnupanel_ips_servidor WHERE esta_usada <> 1 ORDER BY id_ip";
	$res_consulta = pg_query($conexion,$consulta);
	
	if(!$res_consulta)
	{
	$retorno = NULL;
	}
        else
	{
	if(pg_num_rows($res_consulta))
		{
		$retorno = pg_fetch_result($res_consulta,0,0);
		}
	else
		{
		$retorno = NULL;
		}
	}
	pg_close($conexion);


return $retorno;
}
*/
?>
