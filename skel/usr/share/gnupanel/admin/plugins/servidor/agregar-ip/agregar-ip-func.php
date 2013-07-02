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

function ya_existe_ip_principal($id_servidor)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_ips_servidor WHERE id_servidor = $id_servidor AND es_ip_principal = 1 " ;
    $res_consulta = pg_query($conexion,$consulta);
    $num = pg_num_rows($res_consulta);
    if($num>0) $retorno = true;
    pg_close($conexion);
    return $retorno;
}

function agrega_ip($servidor,$ip_publica_in,$ip_privada_in,$usa_nat,$es_ip_principal,$es_dns)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;

    global $plugin;
    global $plugins;
    global $seccion;

    $retorno = NULL;
    $consulta = NULL;
    $ip_publica = implode(".",$ip_publica_in);
    $ip_privada = implode(".",$ip_privada_in);

    if($es_ip_principal == "true")
    {
    	$es_ip_principal = 1;
    }
    else
    {
    	$es_ip_principal = 0;
    }

    if($es_dns == "true")
    {
    	$es_dns = 1;
    }
    else
    {
    	$es_dns = 0;
    }

    if($es_de_id_reseller == "true")
    {
    	$es_de_id_reseller = 1;
    }
    else
    {
    	$es_de_id_reseller = 0;
    }

    if($usa_nat == "true")
    {
    	$usa_nat = 1;
    }
    else
    {
    	$usa_nat = 0;
    }

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $id_servidor = dame_id_servidor($servidor);

    if($usa_nat==1)
	{
    	$consulta = "INSERT INTO gnupanel_ips_servidor(ip_publica,usa_nat,ip_privada,id_servidor,es_ip_principal,es_dns,esta_usada) values('$ip_publica',$usa_nat,'$ip_privada',$id_servidor,$es_ip_principal,$es_dns,0) ";
	}
    else
	{
    	$consulta = "INSERT INTO gnupanel_ips_servidor(ip_publica,usa_nat,id_servidor,es_ip_principal,es_dns,esta_usada) values('$ip_publica',$usa_nat,$id_servidor,$es_ip_principal,$es_dns,0) ";
	}

    $res_consulta = pg_query($conexion,$consulta);

    if($res_consulta)
	{
	$escriba = $escribir['exito'];
	print "$escriba <br/> \n";
	}

    $retorno = $res_consulta;
    return $retorno;
}

function existe_ip_publica($ip_in)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    //$ip = $ip_in[0].".".$ip_in[1].".".$ip_in[2].".".$ip_in[3];
    $ip = implode(".",$ip_in);
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_ips_servidor WHERE ip_publica = '$ip' ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = "ERROR";
	}
    else
	{
	$row = pg_num_rows($res_consulta);
	if($row == 0)
	    {
	    $retorno = NULL;
	    }
	else
	    {
	    $retorno = 1;
	    }
	}

    pg_close($conexion);
    return $retorno;
    }

function existe_ip_privada($ip_in)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    //$ip = $ip_in[0].".".$ip_in[1].".".$ip_in[2].".".$ip_in[3];
    $ip = implode(".",$ip_in);
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_ips_servidor WHERE ip_privada = '$ip' ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = "ERROR";
	}
    else
	{
	$row = pg_num_rows($res_consulta);
	if($row == 0)
	    {
	    $retorno = NULL;
	    }
	else
	    {
	    $retorno = 1;
	    }
	}

    pg_close($conexion);
    return $retorno;
    }

function agregar_ip_3($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_POST;
	global $_SESSION;

	$servidor = trim($_POST['servidor']);
	$usa_nat = trim($_POST['usa_nat']);
        $es_ip_principal = trim($_POST['es_ip_principal']);
	$es_dns = trim($_POST['es_dns']);

	$ip_publica = array();
	$ip_publica[0] = trim($_POST['ip_publica_0']);
	$ip_publica[1] = trim($_POST['ip_publica_1']);
	$ip_publica[2] = trim($_POST['ip_publica_2']);
	$ip_publica[3] = trim($_POST['ip_publica_3']);

	$ip_privada = array();
	$ip_privada[0] = trim($_POST['ip_privada_0']);
	$ip_privada[1] = trim($_POST['ip_privada_1']);
	$ip_privada[2] = trim($_POST['ip_privada_2']);
	$ip_privada[3] = trim($_POST['ip_privada_3']);
	
	$verif_publica = verifica_ip($ip_publica);
	$verif_privada = verifica_ip($ip_privada);

	$no_existe_publica = !existe_ip_publica($ip_publica);
	$no_existe_privada = !existe_ip_privada($ip_privada);
	
	if($no_existe_publica && $no_existe_privada && $verif_publica && ($verif_privada || !($usa_nat=="true")))
	{
	
	print "<div id=\"formulario\" > \n";
	print "<ins> \n";

	if(agrega_ip($servidor,$ip_publica,$ip_privada,$usa_nat,$es_ip_principal,$es_dns))
	{
	$escriba = $escribir['exito'];
	print "<br><br>$escriba <br/> \n";
	}
	else
	{
	$escriba = $escribir['fracaso'];
	print "<br><br>$escriba <br/> \n";
	}

	print "</ins> \n";
	print "</div> \n";
	print "<div id=\"botones\" > \n";
	print "</div> \n";
	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";
	}
	else
	{
		if(!$no_existe_publica)
		{
		$escriba = $escribir['existe_ip_publica']." ";
		agregar_ip_2($procesador,$escriba);
		}
		else
		{
			if(!$no_existe_privada)
			{
			$escriba = $escribir['existe_ip_privada']." ";
			agregar_ip_2($procesador,$escriba);
			}
			else
			{
			$escriba = $escribir['mal_ips']." ";
			agregar_ip_2($procesador,$escriba);
			}

		}

	}

}

function agregar_ip_2($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_POST;
	global $_SESSION;

	$servidor = trim($_POST['servidor']);
	$usa_nat = trim($_POST['usa_nat']);
        $es_ip_principal = trim($_POST['es_ip_principal']);
	$es_dns = trim($_POST['es_dns']);

	$ip_publica = array();
	$ip_publica[0] = trim($_POST['ip_publica_0']);
	$ip_publica[1] = trim($_POST['ip_publica_1']);
	$ip_publica[2] = trim($_POST['ip_publica_2']);
	$ip_publica[3] = trim($_POST['ip_publica_3']);

	$ip_privada = array();
	$ip_privada[0] = trim($_POST['ip_privada_0']);
	$ip_privada[1] = trim($_POST['ip_privada_1']);
	$ip_privada[2] = trim($_POST['ip_privada_2']);
	$ip_privada[3] = trim($_POST['ip_privada_3']);

	print "<div id=\"formulario\" > \n";
	if($mensaje) print "$mensaje <br/> \n";
	print "<ins> \n";
	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table> \n";
	genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);
	genera_fila_formulario("servidor",$servidor,'text_blocked',NULL,!$mensaje);
	genera_fila_formulario("usa_nat",$usa_nat,'check_box',NULL,!$mensaje);

	if(!ya_existe_ip_principal(dame_id_servidor($servidor))) genera_fila_formulario("es_ip_principal",$es_ip_principal,'check_box',NULL,!$mensaje);
	genera_fila_formulario("es_dns",$es_dns,'check_box',NULL,!$mensaje);

	genera_fila_formulario("ip_publica",$ip_publica,'ip',NULL,!$mensaje);

	if($usa_nat == "true") genera_fila_formulario("ip_privada",$ip_privada,'ip',NULL,!$mensaje);

	genera_fila_formulario("ingresando","3",'hidden',NULL,true);
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

function agregar_ip_1($procesador,$mensaje)
{

	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_POST;
	global $_SESSION;

	$servidor = trim($_POST['servidor']);
	$usa_nat = trim($_POST['usa_nat']);
        $es_ip_principal = trim($_POST['es_ip_principal']);
	$es_dns = trim($_POST['es_dns']);

	print "<div id=\"formulario\" > \n";
	if($mensaje) print "$mensaje <br/> \n";

	print "<ins> \n";
	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table> \n";
	genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);
	genera_fila_formulario("servidor",$servidor,'text_blocked',NULL,!$mensaje);
	genera_fila_formulario("usa_nat",$usa_nat,'check_box',NULL,!$mensaje);

	if(!ya_existe_ip_principal(dame_id_servidor($servidor))) genera_fila_formulario("es_ip_principal",$es_ip_principal,'check_box',NULL,!$mensaje);
	genera_fila_formulario("es_dns",$es_dns,'check_box',NULL,!$mensaje);
	genera_fila_formulario("ingresando","2",'hidden',NULL,true);
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

function agregar_ip_0($procesador,$mensaje)
{
    global $idioma;
    global $escribir;
    global $plugin;
    global $plugins;
    global $seccion;
    global $_POST;
    global $_SESSION;
    $servidores = dame_servidores();
	print "<div id=\"formulario\" > \n";
	if($mensaje) print "$mensaje <br/> \n";
	print "<ins> \n";
	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table> \n";
	genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);
	genera_fila_formulario("servidor",$servidores,"select_ip",NULL,NULL);
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

function agregar_ip_init($nombre_script)
{
	global $_POST;
	global $escribir;
	$paso = $_POST['ingresando'];
	switch($paso)
	{
		case "1":
		agregar_ip_1($nombre_script,NULL);
		break;

		case "2":
		agregar_ip_2($nombre_script,NULL);
		break;

		case "3":
		agregar_ip_3($nombre_script,NULL);
		break;

		default:
		$mensaje = $escribir['sel_serv'];
		agregar_ip_0($nombre_script,$mensaje);
	}
}


?>
