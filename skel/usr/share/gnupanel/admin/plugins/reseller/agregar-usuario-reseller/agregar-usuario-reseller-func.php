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

function agrega_record_en_pdns($conexion,$usa_nat,$id_usuario,$name,$contenido,$tipo,$ttl,$prio)
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

function agregar_usuario_en_apache($conexion,$id_reseller,$reseller,$dominio,$id_ip,$primer_reseller)
   {
   global $dir_base_web;
   $checkeo = NULL;

    $consulta = "SELECT admin FROM gnupanel_admin WHERE id_admin=(SELECT cliente_de FROM gnupanel_reseller WHERE id_reseller=$id_reseller) ";
    $res_consulta = pg_query($conexion,$consulta);
    $admin = pg_fetch_result ($res_consulta,0,0);
    $checkeo = $res_consulta;

    $consulta = "SELECT usa_nat FROM gnupanel_ips_servidor WHERE id_ip=$id_ip ";
    $res_consulta = pg_query($conexion,$consulta);
    $usa_nat = pg_fetch_result ($res_consulta,0,0);
    $checkeo = $checkeo && $res_consulta;

    $document_root = $dir_base_web.$admin."/".$reseller."@".$dominio."/".$dominio."/subdominios/";
    $ip = "";
    if($usa_nat==1)
    {
    $ip = "(SELECT ip_privada FROM gnupanel_ips_servidor WHERE id_ip=$id_ip)";
    }
    else
    {
    $ip = "(SELECT ip_publica FROM gnupanel_ips_servidor WHERE id_ip=$id_ip)";
    }

    $server_admin = $reseller."@".$dominio;
    $subdom_def = "gnupanel.".$dominio;
    $document_root_def = "/usr/share/gnupanel/gnupanel";
    $consulta = "INSERT INTO gnupanel_apacheconf(id_subdominio,id_dominio,subdominio,ip,documentroot,serveradmin) VALUES((SELECT id FROM gnupanel_pdns_records WHERE name='$subdom_def' AND type='A'),(SELECT id_usuario FROM gnupanel_usuario WHERE dominio='$dominio'),'gnupanel',$ip,'$document_root_def','$server_admin') ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "UPDATE gnupanel_usuario SET ip = (SELECT ip_publica FROM gnupanel_ips_servidor WHERE id_ip=$id_ip) WHERE dominio = '$dominio' ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "UPDATE gnupanel_apacheconf SET es_ssl = 1, puerto = 443 WHERE id_dominio = (SELECT id_usuario FROM gnupanel_usuario WHERE dominio = '$dominio')";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $subdom_def = "gnupanel.".$dominio;
    $document_root_def = "/usr/share/gnupanel/gnupanel";
    $redirect_def = "https://".$subdom_def ;
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

function agregar_serv_backup_en_dns($conexion,$usuario,$dominio,$id_ip,$servidor)
    {
    $retorno = NULL;
    $checkeo = NULL;
    $consulta = "SELECT id_usuario,usuario,dominio FROM gnupanel_usuario WHERE usuario='$usuario' AND dominio='$dominio' ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_assoc ($res_consulta,0);
    $id_usuario = $retorno['id_usuario'];
    $checkeo = $res_consulta;

    $consulta = "SELECT usa_nat FROM gnupanel_ips_servidor WHERE id_ip = $id_ip ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_assoc ($res_consulta,0);
    $usa_nat = $retorno['usa_nat'];
    $checkeo = $res_consulta && $res_consulta;

    $consulta = "SELECT ip,es_dns,es_mx,subdominio_ns,subdominio_mx FROM gnupanel_servidores_secundarios WHERE id_servidor=(SELECT id_servidor FROM gnupanel_servidores WHERE servidor='$servidor')";
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
		$contenido = $arreglo['subdominio_ns'].".".$dominio;
		$tipo = "NS";
		$ttl = "86400";
		$prio = "NULL";
		$change_date = time();
		$consulta = "INSERT INTO gnupanel_pdns_records(domain_id,name,content,type,ttl,prio,change_date) VALUES($id_usuario,'$dominio','$contenido','$tipo',$ttl,$prio,$change_date) ";
		$res_consulta = pg_query($conexion,$consulta);
		$checkeo = $checkeo && $res_consulta;

		$contenido = $arreglo['subdominio_ns'].".".$dominio;
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

function agregar_dominio_en_dns($conexion,$usuario,$dominio,$id_ip,$primer_reseller)
    {
    $retorno = NULL;
    $checkeo = NULL;

    $consulta = "SELECT id_usuario,usuario,dominio FROM gnupanel_usuario WHERE usuario='$usuario' AND dominio='$dominio' ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_assoc ($res_consulta,0);
    $id_usuario = $retorno['id_usuario'];
    $checkeo = $res_consulta;

    $consulta = "SELECT ip_publica,usa_nat FROM gnupanel_ips_servidor WHERE id_ip = $id_ip ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_assoc ($res_consulta,0);
    $ip_publica = $retorno['ip_publica'];
    $usa_nat = $retorno['usa_nat'];

    $consulta = "SELECT id_servidor FROM gnupanel_ips_servidor WHERE id_ip = $id_ip ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_assoc ($res_consulta,0);
    $id_servidor = $retorno['id_servidor'];
    $checkeo = $checkeo && $res_consulta;
    $ip_default = dame_ip_default($id_servidor);

    $checkeo = $checkeo && $res_consulta;
    $checkeo = $checkeo && agrega_dominio_en_pdns($conexion,$id_usuario,$dominio);

    $contenido = "ns1.".$dominio." ".$usuario."@".$dominio." ".date(Ymd)."00";
    $tipo = "SOA";
    $ttl = "86400";
    $prio = "NULL";
    $checkeo = $checkeo && agrega_record_en_pdns($conexion,$usa_nat,$id_usuario,$dominio,$contenido,$tipo,$ttl,$prio);

    $contenido = "ns1.".$dominio;
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

    $name = "mail.".$dominio;
    $checkeo = $checkeo && agrega_record_en_pdns($conexion,$usa_nat,$id_usuario,$name,$contenido,$tipo,$ttl,$prio);

    $name = "ftp.".$dominio;
    $checkeo = $checkeo && agrega_record_en_pdns($conexion,$usa_nat,$id_usuario,$name,$contenido,$tipo,$ttl,$prio);

    $name = "gnupanel.".$dominio;
    $checkeo = $checkeo && agrega_record_en_pdns($conexion,$usa_nat,$id_usuario,$name,$contenido,$tipo,$ttl,$prio);

    return $checkeo;
    }

function agregar_usuario_en_postfix($conexion,$usuario,$dominio,$pasaporte_crypt,$id_reseller)
    {
    global $_SESSION;
    global $gid_postfix;
    global $mailquota;

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

function agregar_usuario_en_proftpd($conexion,$usuario,$dominio,$pasaporte_crypt,$id_reseller,$espacio,$transferencia,$primer_reseller)
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

	if($primer_reseller)
	{
		$maildir = $dir_base_web.$admin."/";
	}
	else
	{
		$maildir = $dir_base_web.$admin."/".$reseller."@".$dominio_reseller;
	}

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

function verifica_agregar_usuario_reseller_0($reseller,$dominio,$correo_contacto,$pasaporte_0,$pasaporte_1,$id_ip,$plan,$vigencia,$primer_nombre,$segundo_nombre,$apellido,$compania,$pais,$provincia,$ciudad,$calle,$numero,$piso,$departamento,$codpostal,$telefono,$telefono_celular,$fax)
{
    global $escribir;
	$retorno = NULL;
	if(!verifica_dato($reseller,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($dominio,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_correo($correo_contacto)) $retorno = $escribir['correo_inv']." ";
	if(!verifica_dato($pasaporte_0,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($pasaporte_1,NULL)) $retorno = $escribir['carac_inv']." ";
	if($pasaporte_0 != $pasaporte_1) $retorno = $escribir['distintos']." ";
	if(!$id_ip) $retorno = $escribir['no_id_ip']." ";
	if(existe_dominio_reseller($dominio)) $retorno = $escribir['existe']." ";
	if(!hay_espacio($plan,$vigencia)) $retorno = $escribir['sin_espacio']." ";
	if(!hay_transferencia($plan,$vigencia)) $retorno = $escribir['sin_transferencia']." ";
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
	return $retorno;
}

function hay_espacio($plan,$vigencia)
{

	global $servidor_db;
	global $puerto_db;
	global $database;
	global $usuario_db;
	global $passwd_db;
	global $_SESSION;
	global $espacio_servidor;

	$id_admin = $_SESSION['id_admin'];
	$result = NULL;
	$suma = 0;
	$conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
	$conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
	$consulta = "SELECT sum(espacio) FROM gnupanel_reseller_plan WHERE EXISTS (SELECT id_reseller FROM gnupanel_reseller WHERE cliente_de = $id_admin AND gnupanel_reseller.id_reseller = gnupanel_reseller_plan.id_reseller) ";
	$res_consulta = pg_query($conexion,$consulta);
	$suma = pg_fetch_result($res_consulta,0,0);
	pg_free_result($res_consulta);
	$retorno = NULL;
	$consulta = "SELECT espacio FROM gnupanel_reseller_planes WHERE id_dueno = $id_admin AND plan = '$plan' AND vigencia = $vigencia ";
	$res_consulta = pg_query($conexion,$consulta);
	$espacio_plan = pg_fetch_result($res_consulta,0,0);
	
	pg_free_result($res_consulta);
	if(($espacio_servidor - $suma)>=$espacio_plan) $result = true;
	pg_close($conexion);
	return $result;
}

function hay_transferencia($plan,$vigencia)
{
	global $servidor_db;
	global $puerto_db;
	global $database;
	global $usuario_db;
	global $passwd_db;
	global $_SESSION;
	global $transferencia_servidor;

	$id_admin = $_SESSION['id_admin'];
	$result = NULL;
	$suma = 0;
	$conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
	$conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
	$consulta = "SELECT sum(transferencia) FROM gnupanel_reseller_plan WHERE EXISTS (SELECT id_reseller FROM gnupanel_reseller WHERE cliente_de = $id_admin AND gnupanel_reseller.id_reseller = gnupanel_reseller_plan.id_reseller) ";
	$res_consulta = pg_query($conexion,$consulta);
	$suma = pg_fetch_result($res_consulta,0,0);
	pg_free_result($res_consulta);
	$retorno = NULL;
	$consulta = "SELECT transferencia FROM gnupanel_reseller_planes WHERE id_dueno = $id_admin AND plan = '$plan' AND vigencia = $vigencia ";
	$res_consulta = pg_query($conexion,$consulta);
	$transferencia_plan = pg_fetch_result($res_consulta,0,0);
	pg_free_result($res_consulta);
	if(($transferencia_servidor - $suma)>=$transferencia_plan) $result = true;
	pg_close($conexion);
	return $result;
}

function agregar_usuario_reseller($reseller,$dominio,$correo_contacto,$pasaporte,$id_ip,$plan,$vigencia,$idioma,$servidor,$primer_nombre,$segundo_nombre,$apellido,$compania,$pais,$provincia,$ciudad,$calle,$numero,$piso,$departamento,$codpostal,$telefono,$telefono_celular,$fax)
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
	$id_admin = $_SESSION['id_admin'];
	$pasaporte_crypt = gnupanel_crypt($pasaporte);
	$primer_reseller = !existe_algun_reseller();

	$conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
	$conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
	$consulta = "BEGIN";
	$res_consulta = pg_query($conexion,$consulta);
	$consulta = "INSERT INTO gnupanel_reseller(reseller,dominio,correo_contacto,id_ip,password,cliente_de) values('$reseller','$dominio','$correo_contacto',$id_ip,'$pasaporte_crypt',$id_admin) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $res_consulta;
	$consulta = "SELECT id_reseller FROM gnupanel_reseller WHERE reseller = '$reseller' AND dominio = '$dominio' ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;
	$id_reseller = pg_fetch_result ($res_consulta,0,0);
	$consulta = "SELECT * FROM gnupanel_reseller_planes WHERE plan = '$plan' AND vigencia = $vigencia AND id_dueno = $id_admin ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;
	$retorno = pg_fetch_assoc($res_consulta,0);
	$id_plan = $retorno['id_plan'];
	$dominios = $retorno['dominios'];
	$subdominios = $retorno['subdominios'];
	$dominios_parking = $retorno['dominios_parking'];
	$espacio = $retorno['espacio'];
	$transferencia = $retorno['transferencia'];
	$bases_postgres = $retorno['bases_postgres'];
	$bases_mysql = $retorno['bases_mysql'];
	$cuentas_correo = $retorno['cuentas_correo'];
	$listas_correo = $retorno['listas_correo'];
	$cuentas_ftp = $retorno['cuentas_ftp'];

	$consulta = "UPDATE gnupanel_ips_servidor SET esta_usada = 1, es_de_id_reseller = $id_reseller, es_ip_principal = 1 WHERE id_ip = $id_ip ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;
	
	$consulta = "INSERT INTO gnupanel_reseller_plan(id_reseller,id_plan,vencimiento_plan,vigencia_plan,dominios,subdominios,dominios_parking,espacio,transferencia,bases_postgres,bases_mysql,cuentas_correo,listas_correo,cuentas_ftp) VALUES($id_reseller,$id_plan,now(),$vigencia,$dominios,$subdominios,$dominios_parking,$espacio,$transferencia,$bases_postgres,$bases_mysql,$cuentas_correo,$listas_correo,$cuentas_ftp) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;
	$consulta = "INSERT INTO gnupanel_reseller_data(id_reseller) VALUES($id_reseller) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;
	
	$consulta = "INSERT INTO gnupanel_reseller_estado(id_reseller,vencimiento_plan,vigencia_plan) VALUES($id_reseller,(SELECT vencimiento_plan FROM gnupanel_reseller_plan WHERE id_reseller = $id_reseller),$vigencia) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;
	
	$consulta = "INSERT INTO gnupanel_reseller_sets(id_reseller,id_tema) VALUES($id_reseller,(SELECT id_tema FROM gnupanel_temas WHERE tema = '$tema_default_config')) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;
	
	$consulta = "INSERT INTO gnupanel_reseller_lang(id_reseller,idioma) VALUES($id_reseller,'$idioma') ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;

	$consulta = "INSERT INTO gnupanel_divisas_reseller(id_reseller) VALUES($id_reseller) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;

	$consulta = "INSERT INTO gnupanel_usuario(usuario,dominio,correo_contacto,password,cliente_de) VALUES('$reseller','$dominio','$correo_contacto','*',$id_reseller) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;

	$consulta = "SELECT id_usuario FROM gnupanel_usuario WHERE usuario='$reseller' AND dominio='$dominio' ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;
	$retorno = pg_fetch_assoc($res_consulta,0);
	$id_usuario = $retorno['id_usuario'];

	$consulta = "INSERT INTO gnupanel_usuario_plan(id_usuario,id_plan,vencimiento_plan,vigencia_plan,subdominios,dominios_parking,espacio,transferencia,bases_postgres,bases_mysql,cuentas_correo,listas_correo,cuentas_ftp) VALUES($id_usuario,0,now()+interval '$vigencia month',$vigencia,-1,-1,-1,-1,-1,-1,-1,-1,-1) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;

	$consulta = "INSERT INTO gnupanel_usuario_estado(id_usuario,vencimiento_plan,vigencia_plan) VALUES($id_usuario,now()+interval '$vigencia month',$vigencia) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;

	$consulta = "INSERT INTO gnupanel_usuario_data(id_usuario) VALUES($id_usuario) ";
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

	$checkeo = $checkeo && agregar_dominio_en_dns($conexion,$reseller,$dominio,$id_ip,$primer_reseller);
	$checkeo = $checkeo && agregar_serv_backup_en_dns($conexion,$reseller,$dominio,$id_ip,$servidor);


	$checkeo = $checkeo && agregar_usuario_en_postfix($conexion,$reseller,$dominio,$pasaporte_crypt,$id_reseller);
	$checkeo = $checkeo && agregar_usuario_en_proftpd($conexion,$reseller,$dominio,$pasaporte_crypt,$id_reseller,$espacio,$transferencia,$primer_reseller);

	$checkeo = $checkeo && agregar_usuario_en_est($conexion,$id_reseller,$dominio,$espacio,$transferencia*1024*1024);

	$checkeo = $checkeo && agregar_usuario_en_apache($conexion,$id_reseller,$reseller,$dominio,$id_ip,$primer_reseller);

	$checkeo = $checkeo && agregar_data_reseller($conexion,$id_reseller,$primer_nombre,$segundo_nombre,$apellido,$compania,$pais,$provincia,$ciudad,$calle,$numero,$piso,$departamento,$codpostal,$telefono,$telefono_celular,$fax);

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

function agregar_data_reseller($conexion,$id_reseller,$primer_nombre,$segundo_nombre,$apellido,$compania,$pais,$provincia,$ciudad,$calle,$numero,$piso,$departamento,$codpostal,$telefono,$telefono_celular,$fax)
{
global $_SESSION;

$retorno = NULL;
$checkeo = NULL;
$id_usuario = NULL;
$consulta = "";
$res_consulta = NULL;
$id_admin = $_SESSION['id_admin'];

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
$consulta_ini = "UPDATE gnupanel_reseller_data SET ";
$consulta_fin = " WHERE id_reseller = $id_reseller";
$consulta_tot = $consulta_ini.$consulta.$consulta_fin;
$res_consulta = pg_query($conexion,$consulta_tot);
$checkeo = $res_consulta;

$consulta_aux = "SELECT reseller,dominio FROM gnupanel_reseller WHERE id_reseller=$id_reseller ";
$res_consulta = pg_query($conexion,$consulta_aux);
$retorno = pg_fetch_assoc($res_consulta,0);
$usuario = $retorno['reseller'];
$dominio = $retorno['dominio'];

$consulta_aux = "SELECT id_usuario FROM gnupanel_usuario WHERE usuario='$usuario' AND dominio='$dominio' ";
$res_consulta = pg_query($conexion,$consulta_aux);
$retorno = pg_fetch_assoc($res_consulta,0);
$id_usuario = $retorno['id_usuario'];

$consulta_ini = "UPDATE gnupanel_usuario_data SET ";
$consulta_fin = " WHERE id_usuario = $id_usuario";
$consulta_tot = $consulta_ini.$consulta.$consulta_fin;
$res_consulta = pg_query($conexion,$consulta_tot);
$checkeo = $checkeo && $res_consulta;
}
return $checkeo;
}

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

function agregar_usuario_reseller_0($procesador,$mensaje)
{
    global $idioma;
    global $escribir;
    global $plugin;
    global $plugins;
    global $seccion;
    global $_POST;
    global $_SESSION;

	$idiomas = dame_idiomas_disp();
	$idioma = trim($_POST['idioma']);
	$reseller = strtolower(trim($_POST['reseller']));
	$dominio = strtolower(trim($_POST['dominio']));
	$correo_contacto = strtolower(trim($_POST['correo_contacto']));
	$plan = trim($_POST['plan']);
	$vigencia = trim($_POST['vigencia']);

	$password = trim($_POST['password']);
	$password_r = trim($_POST['password_r']);
	
	$primer_nombre = trim($_POST['primer_nombre']);
	$segundo_nombre = trim($_POST['segundo_nombre']);
	$apellido = trim($_POST['apellido']);
	$compania = trim($_POST['compania']);
	$pais = "";
	if(isset($_POST['pais']))
		{
		$pais = trim($_POST['pais']);
		}
		else
		{
		$pais = "AR";
		}
	$provincia = trim($_POST['provincia']);
	$ciudad = trim($_POST['ciudad']);
	$calle = trim($_POST['calle']);
	$numero = trim($_POST['numero']);
	$piso = trim($_POST['piso']);
	$departamento = trim($_POST['departamento']);
	$codpostal = trim($_POST['codpostal']);
	$telefono = trim($_POST['telefono']);
	$telefono_celular = trim($_POST['telefono_celular']);
	$fax = trim($_POST['fax']);
	$ingresando = trim($_POST['ingresando']);
	$pr_reseller = "true";
	if(existe_algun_reseller()) $pr_reseller = "false";

	$planes = array();
	$servidores = array();


	$planes = dame_planes_reseller();


	$vigencias = NULL;
	$data_plan = NULL;

	if(count($planes)>0)
	{
		if(!isset($_POST['plan'])) $plan = $planes[0];
		$vigencias = dame_vigencias_plan($plan);
		if(!isset($_POST['vigencia'])) $vigencia = $vigencias[0];
		if(!corresponde_vigencia($plan,$vigencia)) $vigencia = $vigencias[0];
		$data_plan = dame_precio_plan($plan,$vigencia);
	}

	$precio = $data_plan['precio'];
	$moneda = $data_plan['moneda'];

	$servidores = dame_servidores();
	$paises = dame_paises();
	print "<div id=\"formulario\" > \n";
	print "\n";
	print "<SCRIPT language=\"JavaScript\">\n";
	print "function si_cambia_form() {\n";
	print "elementos = document.getElementsByTagName('input'); \n";
	print "largo = elementos.length; \n";
	print "for(i=0;i<largo;i++) {\n";
	print "if(elementos[i].name == 'ingresando') elementos[i].value = 0; \n";
	print "}\n";
	print "formularios = document.getElementsByTagName('form');\n";
	print "var formulario;\n";
	print "largo = formularios.length; \n";
	print "for(i=0;i<largo;i++) {\n";
	print "if(formularios[i].id == 'formar') formulario = formularios[i]; \n";
	print "}\n";
	print "formulario.submit();\n";
	print "}\n";
	print "</SCRIPT>\n";
	print "\n";

	if($mensaje) print "$mensaje <br> \n";

	if(count($planes)>0)
	{
	print "<ins> \n";
	print "<form id=\"formar\" method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table> \n";
	genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);
	genera_fila_formulario("idioma",$idiomas,"select",50,!$mensaje);
	genera_fila_formulario("reseller",$reseller,"text",50,!$mensaje);
	genera_fila_formulario("dominio",$dominio,"text",50,!$mensaje);
	genera_fila_formulario("correo_contacto",$correo_contacto,"text_correo",50,!$mensaje);
	genera_fila_formulario("plan",$planes,"select_ip_submit",$plan,'si_cambia_form();');
	genera_fila_formulario("vigencia",$vigencias,"select_ip_submit",$vigencia,'si_cambia_form();');
	genera_fila_formulario("precio",$precio,"text_blocked_int",8,NULL);
	genera_fila_formulario("moneda",$moneda,"text_blocked",8,NULL);

	genera_fila_formulario("servidor",$servidores,"select_ip",NULL,NULL);
	genera_fila_formulario("password",$password,"password",20,!$mensaje);
	genera_fila_formulario("password_r",$password_r,"password",20,!$mensaje);
	genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);
	genera_fila_formulario("primer_nombre",$primer_nombre,"text",20,!$mensaje,true,true);
	genera_fila_formulario("segundo_nombre",$segundo_nombre,"text",20,!$mensaje,NULL,true);
	genera_fila_formulario("apellido",$apellido,"text",20,!$mensaje,true,true);
	genera_fila_formulario("compania",$compania,"text",20,!$mensaje,true,true);
	genera_fila_formulario("pais",$paises,"select_pais",$pais,NULL);
	genera_fila_formulario("provincia",$provincia,"text",20,!$mensaje,true,true);
	genera_fila_formulario("ciudad",$ciudad,"text",20,!$mensaje,true,true);
	genera_fila_formulario("calle",$calle,"text",20,!$mensaje,NULL,true);
	genera_fila_formulario("numero",$numero,"text_int",20,!$mensaje,NULL);
	genera_fila_formulario("piso",$piso,"text_int",20,!$mensaje,NULL);
	genera_fila_formulario("departamento",$departamento,"text",20,!$mensaje,NULL,true);
	genera_fila_formulario("codpostal",$codpostal,"text",20,!$mensaje,NULL,true);
	genera_fila_formulario("telefono",$telefono,"text",20,!$mensaje,NULL,true);
	genera_fila_formulario("telefono_celular",$telefono_celular,"text",20,!$mensaje,NULL,true);
	genera_fila_formulario("fax",$fax,"text",20,!$mensaje,NULL,true);
	genera_fila_formulario("pr_reseller",$pr_reseller,'hidden',NULL,true);
	genera_fila_formulario("ingresando","1",'hidden',NULL,true);
	genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);
	genera_fila_formulario("resetea",NULL,'reset',NULL,true);
	genera_fila_formulario("agrega",NULL,'submit',NULL,true);
	print "</table> \n";
	print "</form> \n";
	print "</ins> \n";
	}
	else
	{
	$escriba = $escribir['no_planes'];
	print "<br><br>$escriba <br>";
	}

	print "</div> \n";
	print "<div id=\"botones\" > \n";
	print "</div> \n";
	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";
}

function agregar_usuario_reseller_1($procesador,$mensaje)
{
    global $idioma;
    global $escribir;
    global $plugin;
    global $plugins;
    global $seccion;
    global $_POST;
    global $_SESSION;
	$id_ip = NULL;
	$reseller = strtolower(trim($_POST['reseller']));
	$dominio = strtolower(trim($_POST['dominio']));
	$correo_contacto = strtolower(trim($_POST['correo_contacto']));
	$plan = trim($_POST['plan']);
	$vigencia = trim($_POST['vigencia']);
	$password = trim($_POST['password']);
	$password_r = trim($_POST['password_r']);
	$servidor = trim($_POST['servidor']);
	$idioma = trim($_POST['idioma']);
	$primer_nombre = trim($_POST['primer_nombre']);
	$segundo_nombre = trim($_POST['segundo_nombre']);
	$apellido = trim($_POST['apellido']);
	$compania = trim($_POST['compania']);
	$pais = trim($_POST['pais']);
	$provincia = trim($_POST['provincia']);
	$ciudad = trim($_POST['ciudad']);
	$calle = trim($_POST['calle']);
	$numero = trim($_POST['numero']);
	$piso = trim($_POST['piso']);
	$departamento = trim($_POST['departamento']);
	$codpostal = trim($_POST['codpostal']);
	$telefono = trim($_POST['telefono']);
	$telefono_celular = trim($_POST['telefono_celular']);
	$fax = trim($_POST['fax']);
	$pr_reseller = trim($_POST['pr_reseller']);
	$ingresando = trim($_POST['ingresando']);

	$id_ip = dame_ip_disponible();

	$checkea = verifica_agregar_usuario_reseller_0($reseller,$dominio,$correo_contacto,$password,$password_r,$id_ip,$plan,$vigencia,$primer_nombre,$segundo_nombre,$apellido,$compania,$pais,$provincia,$ciudad,$calle,$numero,$piso,$departamento,$codpostal,$telefono,$telefono_celular,$fax);

	if($checkea && $ingresando=="1")
	{
	agregar_usuario_reseller_0($procesador,$checkea);
	}
	else
	{
	if($ingresando == "1")
		{
		$id_reseller = agregar_usuario_reseller($reseller,$dominio,$correo_contacto,$password,$id_ip,$plan,$vigencia,$idioma,$servidor,$primer_nombre,$segundo_nombre,$apellido,$compania,$pais,$provincia,$ciudad,$calle,$numero,$piso,$departamento,$codpostal,$telefono,$telefono_celular,$fax);
		$escriba_00 = "";

		if($id_reseller)
		{
			$escriba = $escribir['exito'];
			$escriba_00 = $escribir['mando_correo'];
			$cabecera_mandar = $reseller."@".$dominio;
			$cabecera_00 = "From: ".$cabecera_mandar." \r\n";
			$cabecera_01 = "Reply-To: ".$cabecera_mandar." \r\n";
			$cabecera_02 = $cabecera_00.$cabecera_01;
			$subjeto = $escribir['correo_09'];
			$enviar = "";
			$enviar = $enviar.$escribir['correo_00']." ".$reseller."@".$dominio."\n";
			$enviar = $enviar.$escribir['correo_01']." ".$password."\n";
			$enviar = $enviar.$escribir['correo_02']." "."https://"."gnupanel.".$dominio."/reseller \n";
			$enviar = $enviar.$escribir['correo_03']." "."https://"."gnupanel.".$dominio."/users \n";
			if($pr_reseller=="true")
			{
			$enviar = $enviar.$escribir['correo_04']." "."https://"."gnupanel.".$dominio."/admin \n";
			}
			$enviar = $enviar.$escribir['correo_10']." "."https://"."gnupanel.".$dominio."/webmail \n";
			$enviar = $enviar.$escribir['correo_11']." "."https://"."gnupanel.".$dominio."/phpmyadmin \n";
			$enviar = $enviar.$escribir['correo_12']." "."https://"."gnupanel.".$dominio."/phppgadmin \n";

			$enviar = $enviar.$escribir['correo_05']." ".$cabecera_mandar." \n";
			$enviar = $enviar.$escribir['correo_06']." ".$cabecera_mandar." \n";
			$enviar = $enviar.$escribir['correo_07']." \n";
			$enviar = $enviar.$escribir['correo_08']." \n";

			$subjeto = html_entity_decode($subjeto,ENT_QUOTES,'UTF-8');
			$enviar = html_entity_decode($enviar,ENT_QUOTES,'UTF-8');

			mail("$correo_contacto","$subjeto","$enviar","$cabecera_02");
			mail("$cabecera_mandar","$subjeto","$enviar","$cabecera_02");
			/*
			print "$correo_contacto <br>\n";
			print "$cabecera_00 <br>\n";
			print "$cabecera_01<br>\n";
			print "$subjeto<br>\n";
			print "$enviar<br>\n";
			*/
		}
		else
		{
			$escriba = $escribir['fracaso'];
		}

		print "<div id=\"formulario\" > \n";
		print "<br><br>$escriba <br> \n";
		print "$escriba_00 <br> \n";
		print "</div> \n";
		print "<div id=\"botones\" > \n";
		print "</div> \n";
		print "<div id=\"ayuda\" > \n";
		$escriba = $escribir['help'];
		print "$escriba\n";
		print "</div> \n";
		}
	}
}

function agregar_usuario_reseller_init($nombre_script)
{
	global $_POST;
	$paso = trim($_POST['ingresando']);
	switch($paso)
	{
		case "1":
		agregar_usuario_reseller_1($nombre_script,NULL);
		break;
		default:
		agregar_usuario_reseller_0($nombre_script,NULL);
	}
}

?>
