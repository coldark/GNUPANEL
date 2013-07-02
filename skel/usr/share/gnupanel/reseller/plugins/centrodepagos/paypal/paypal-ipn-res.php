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


function urlsafe_b64encode($string)
{
	$data = base64_encode($string);
	$data = str_replace(array('+','/','='),array('-','_','.'),$data);
	return $data;
}

function urlsafe_b64decode($string)
{
	$data = str_replace(array('-','_','.'),array('+','/','='),$string);
	$mod4 = strlen($data) % 4;

	if ($mod4)
	{
		$data .= substr('====', $mod4);
	}

	return base64_decode($data);
}

function desencripta_id_pago()
{
    global $_GET;
    $id_pago_b64 = $_GET['id_pago'];
    $iv = urlsafe_b64decode($_GET['crcsum']);
    $result = NULL;
    $key = md5("paypal");
    $id_pago = urlsafe_b64decode($id_pago_b64);
    $result = trim(rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256,$key,$id_pago,MCRYPT_MODE_CBC,$iv),"\0\4"));
    return $result;
}

function dame_divisa()
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

    $id_usuario = $_SESSION['id_usuario'];
    $result = NULL;

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT moneda FROM gnupanel_monedas WHERE id_moneda = (SELECT moneda FROM gnupanel_usuarios_planes WHERE id_plan = (SELECT id_plan FROM gnupanel_usuario_plan WHERE id_usuario = $id_usuario)) ";
    $res_consulta = pg_query($conexion,$consulta);
    $result = pg_fetch_result($res_consulta,0,0);
pg_close($conexion);
return $result;
}

function dame_link_paypal($id_reseller)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

    $result = NULL;

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");

    $consulta = "SELECT test FROM paypal_admin WHERE id_admin = (SELECT cliente_de FROM gnupanel_reseller WHERE id_reseller = $id_reseller) ";
    $res_consulta = pg_query($conexion,$consulta);

    if(pg_num_rows($res_consulta)>0) 
	{
	$test = pg_fetch_result($res_consulta,0,0);
		if($test == 1)
		{
			$result = "ssl://www.sandbox.paypal.com";
		}
		else
		{
			//$result = "ssl://www.paypal.com";
			$result = "ssl://ipnpb.paypal.com";
		}
	}

pg_close($conexion);
return $result;
}

function dame_id_pago_reseller($control,$empresa)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;

    $result = NULL;
    $checkeo = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
                                                    
    $consulta = "SELECT id,id_reseller FROM pagos_reseller WHERE codigo_seguimiento = '$control' AND empresa_cobranza = '$empresa' AND confirmado = 0 AND acreditado = 0";
    //$consulta = "SELECT id,id_reseller FROM pagos_reseller WHERE codigo_seguimiento = '$control' AND empresa_cobranza = '$empresa' ";
    
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $res_consulta;

    if($checkeo)
    {
        if(pg_num_rows($res_consulta)>0)
        {
            $result = array();
            $result = pg_fetch_assoc($res_consulta,0);
        }
    }
    pg_free_result($res_consulta);    
    pg_close($conexion);
    return $result;
}

function acreditar_fondos_reseller($id_reseller,$id_pago,$fondos)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;

    $checkeo = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $res_consulta = pg_query($conexion,"BEGIN");
    $checkeo = $res_consulta;
                                                    
    $consulta = "UPDATE gnupanel_divisas_reseller SET credito = credito + $fondos WHERE id_reseller = $id_reseller ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "UPDATE pagos_reseller SET importe = $fondos, confirmado = 1, acreditado = 1, acreditacion_pago = now() WHERE id_reseller = $id_reseller AND id = $id_pago ";
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

$dir_actual = getcwd();
chdir("../../..");
require_once("config/gnupanel-reseller-ini.php");
session_cache_limiter('nocache');
require_once("funciones/funciones.php");
chdir($dir_actual);

$session_act = "paypal";
$session_ant = session_name($session_act);
session_start();

global $_POST;
global $_GET;

$data_pago = NULL;
$data_pago = array();

$id_pago = desencripta_id_pago();

$data_pago['id_pago'] = $id_pago;

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';


foreach ($_POST as $key => $value) 
{
	if(trim($key)=="custom") $data_pago['codigo_seguimiento'] = trim($value);
	if(trim($key)=="mc_gross") $data_pago['importe'] = trim($value);
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}

// post back to PayPal system to validate
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

$data_reseller = dame_id_pago_reseller(trim($data_pago['codigo_seguimiento']),"paypal");
$link_paypal = dame_link_paypal($data_reseller['id_reseller']);
$fp = fsockopen($link_paypal,443,$errno,$errstr,60);

if (!$fp)
{
	// HTTP ERROR
}
else
{
	fputs($fp,$header.$req);
	while (!feof($fp))
	{
		$res = fgets($fp,1024);

		if(strcmp($res,"VERIFIED") == 0)
		{
			if($data_reseller['id']==$data_pago['id_pago'])
			{
				acreditar_fondos_reseller($data_reseller['id_reseller'],$data_pago['id_pago'],$data_pago['importe']);
			}
		}
		elseif(strcmp($res,"INVALID") == 0) 
		{
			// log for manual investigation
		}
	}
fclose ($fp);
}

?>