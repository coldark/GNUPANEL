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

function es_usuario_principal($id_usuario)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $cant_max_result;

    $retorno = NULL;

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT id_plan FROM gnupanel_usuario_plan WHERE id_usuario = $id_usuario";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	return NULL;
	}
    else
	{
	$retorno = (pg_fetch_result($res_consulta,0,0)==0);
	}


pg_close($conexion);
return $retorno;
}

function paypal_configurado()
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
    $consulta = "SELECT * FROM paypal_reseller WHERE id_reseller = (SELECT cliente_de FROM gnupanel_usuario WHERE id_usuario = $id_usuario) ";
    $res_consulta = pg_query($conexion,$consulta);

    if(pg_num_rows($res_consulta)>0) 
	{
	$result = pg_fetch_assoc($res_consulta,0);
		if($result['test']==1)
		{
			$result['link_paypal'] = "https://www.sandbox.paypal.com/cgi-bin/webscr";
		}
		else
		{
			$result['link_paypal'] = "https://www.paypal.com/cgi-bin/webscr";
		}
	$result['paypal_imagen'] = "http://images.paypal.com/images/x-click-but01.gif";
	}
if($result['active']==0) $result = NULL;
pg_close($conexion);
if(es_usuario_principal($id_usuario)) $result = NULL;
return $result;
}

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

function encripta_id_pago($id_pago)
{
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $key = md5("paypal");
    $result = urlsafe_b64encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256,$key,$id_pago,MCRYPT_MODE_CBC,$iv));
    $iv = urlsafe_b64encode($iv);
    $result = $result."&crcsum=".$iv;
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

function dame_deuda()
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
    $consulta = "SELECT credito FROM gnupanel_divisas_usuario WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $result = pg_fetch_result($res_consulta,0,0);
    if($result<0)
	{
	$result = (-1)*$result;
	}
    else
	{
	$result = 0;
	}

pg_close($conexion);
return $result;
}

function dame_usuario()
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
    $consulta = "SELECT usuario,dominio FROM gnupanel_usuario WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $usuario = pg_fetch_result($res_consulta,0,0);
    $dominio = pg_fetch_result($res_consulta,0,1);
    $result = $usuario."@".$dominio;

pg_close($conexion);
return $result;
}



function verifica_paypal($monto)
{
    global $escribir;
	$retorno = NULL;
	$divisa = dame_divisa();
	$monto_minimo_paypal = 5;
	if($monto < $monto_minimo_paypal) $retorno = $escribir['monto_chico']." ";
	if(!verifica_dato($monto,true)) $retorno = $escribir['carac_inv']." ";
	if(!(($divisa=="USD") || ($divisa=="EUR") || ($divisa=="AUD"))) $retorno = $escribir['divisa_inv']." ";
	return $retorno;
}

function paypal($monto)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

    $result = NULL;
    $test = 0;
    $active = 0;
    $id_pago = NULL;

    if($test_in=="true") $test = 1;
    if($active_in=="true") $active = 1;

    $id_usuario = $_SESSION['id_usuario'];
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $usuario = date('YmdHis')."_usuario_".dame_usuario();
    $codigo_seguimiento = crc32($usuario);
    if($codigo_seguimiento<0) $codigo_seguimiento = (-1)*$codigo_seguimiento;
    $consulta = "INSERT INTO pagos_usuario(id_usuario,importe,empresa_cobranza,codigo_seguimiento) VALUES($id_usuario,$monto,'paypal','$codigo_seguimiento') ";
    $res_consulta = pg_query($conexion,$consulta);

    if($res_consulta)
	{

		$consulta = "SELECT id FROM pagos_usuario WHERE id_usuario = $id_usuario AND importe = $monto AND empresa_cobranza = 'paypal' AND codigo_seguimiento = '$codigo_seguimiento' ";
		$res_consulta = pg_query($conexion,$consulta);
		$id_pago = pg_fetch_result($res_consulta,0,0);
	}

    if($res_consulta)
	{
	$result = array();
	$result['importe'] = $monto;
	$result['usuario'] = "U_".$usuario;
	$result['codigo_seguimiento'] = $codigo_seguimiento;
	$result['id_pago'] = $id_pago;
	}

pg_close($conexion);
return $result;
}

function paypal_0($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	global $cant_max_result;

	$monto = dame_deuda();

	if(isset($_POST['monto'])) $monto = $_POST['monto'];

	print "<div id=\"formulario\" > \n";
	if($mensaje) print "$mensaje <br/> \n";
	print "<ins> \n";

	if(paypal_configurado())
	{
	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";

	print "<table width=\"80%\" > \n";

	genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);
	genera_fila_formulario('monto',$monto,'text_int',8,!$mensaje,true,NULL,254,dame_divisa());
	genera_fila_formulario('ingresando',"1",'hidden',NULL,true);
	genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);
	genera_fila_formulario("resetea",NULL,'reset',NULL,true,NULL);
	genera_fila_formulario("configurar",NULL,'submit',NULL,true,NULL);

	print "</table> \n";
	print "</form> \n";
	}
	else
	{
	$escriba = $escribir['no_disponible'];
	print "<br><br>$escriba <br>\n";
	}

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

function paypal_1($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;

	$paypal_data = paypal_configurado();
	$monto = $_POST['monto'];
	$moneda = dame_divisa();
	$checkeo = NULL;
	$checkeo = verifica_paypal($monto);

	if($checkeo)
	{
	paypal_0($nombre_script,$checkeo);
	}
	else
	{
	$data_paypal = paypal($monto);

	print "<div id=\"formulario\" > \n";
	print "<ins> \n";

	if($data_paypal)
	{
	print "<table width=\"80%\" > \n";

	print "<tr> \n";
	print "<td> \n";
	print "<br>";
	print "</td> \n";
	print "<td> \n";
	print "</td> \n";
	print "</tr> \n";

	print "<tr> \n";
	print "<td> \n";
	$escriba = $escribir['monto'];
	print "$escriba";
	print "</td> \n";
	print "<td> \n";
	print "$monto";
	print "</td> \n";
	print "</tr> \n";

	print "<tr> \n";
	print "<td> \n";
	$escriba = $escribir['moneda'];
	print "$escriba";
	print "</td> \n";
	print "<td> \n";
	print "$moneda";
	print "</td> \n";
	print "</tr> \n";

	print "<tr> \n";
	print "<td> \n";
	print "<br>";
	print "</td> \n";
	print "<td> \n";
	print "</td> \n";
	print "</tr> \n";

	print "<tr> \n";

	print "<td> \n";
	print "</td> \n";

	print "<td> \n";
	
	$escriba = $escribir['pagar_paypal'];
	$procesador_inc = $paypal_data['link_paypal'];
	$variables = array();
	$variables['cmd'] = "_xclick";
	$variables['business'] = $paypal_data['correo_paypal'];
	$variables['item_name'] = "U_".dame_usuario();
	$variables['custom'] = $data_paypal['codigo_seguimiento'];
	$variables['quantity'] = "1";
	$notify_url = "https://".$_SERVER['SERVER_NAME']."/usuarios/plugins/centrodepagos/paypal/paypal-ipn.php?id_pago=".encripta_id_pago($data_paypal['id_pago']);
	$variables['notify_url'] = $notify_url;
	$variables['amount'] = $monto;
	$variables['no_shipping'] = "1";
	$variables['currency_code'] = "$moneda";
	boton_con_formulario_paypal($procesador_inc,$escriba,$variables,$paypal_data['paypal_imagen']);

	print "</td> \n";
	print "</tr> \n";

	print "</table> \n";
	}
	else
	{
	$escriba = $escribir['fracaso'];
	print "<br><br>$escriba <br>\n";
	}

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
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";
	}
}

function paypal_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];

	switch($paso)
	{
		case "1":
		paypal_1($nombre_script,NULL);
		break;
		default:
		paypal_0($nombre_script,NULL);
	}
}



?>
