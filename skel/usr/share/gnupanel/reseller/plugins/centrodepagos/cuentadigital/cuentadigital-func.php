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

function cuentadigital_configurado()
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $plugin;
    //global $plugins;
    global $seccion;

    $id_reseller = $_SESSION['id_reseller'];
    $result = NULL;

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * FROM cuentadigital_admin WHERE id_admin = (SELECT cliente_de FROM gnupanel_reseller WHERE id_reseller = $id_reseller) ";
    $res_consulta = pg_query($conexion,$consulta);

    if(pg_num_rows($res_consulta)>0) 
	{
	$result = pg_fetch_assoc($res_consulta,0);
	$result['link_cuentadigital'] = "https://www.cuentadigital.com/api.php";
	$result['cuentadigital_imagen'] = "plugins/".$seccion."/".$plugin."/logo-cuentadigital.jpg";
	}
if($result['active']==0) $result = NULL;
pg_close($conexion);
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

    $id_reseller = $_SESSION['id_reseller'];
    $result = NULL;

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT moneda FROM gnupanel_monedas WHERE id_moneda = (SELECT moneda FROM gnupanel_reseller_planes WHERE id_plan = (SELECT id_plan FROM gnupanel_reseller_plan WHERE id_reseller = $id_reseller)) ";
    $res_consulta = pg_query($conexion,$consulta);
    $result = pg_fetch_result($res_consulta,0,0);

pg_close($conexion);
return $result;
}

function dame_moneda_cuenta_digital()
{
	$result = NULL;
	$moneda = dame_divisa();

	switch($moneda)
	{
	case "\$AR":
		$result = "ARS";
	break;

	case "USD":
		$result = "USD";
	break;

	case "EUR":
		$result = "EUR";
	break;

	default:
		$result = "USD";
	}

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

    $id_reseller = $_SESSION['id_reseller'];
    $result = NULL;

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT credito FROM gnupanel_divisas_reseller WHERE id_reseller = $id_reseller ";
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

function dame_reseller()
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

    $id_reseller = $_SESSION['id_reseller'];
    $result = NULL;

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT reseller,dominio FROM gnupanel_reseller WHERE id_reseller = $id_reseller ";
    $res_consulta = pg_query($conexion,$consulta);
    $reseller = pg_fetch_result($res_consulta,0,0);
    $dominio = pg_fetch_result($res_consulta,0,1);
    $result = $reseller."@".$dominio;

pg_close($conexion);
return $result;
}

function dame_dominio_venta()
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

    $id_reseller = $_SESSION['id_reseller'];
    $result = NULL;

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT id_reseller,dominio FROM gnupanel_reseller WHERE cliente_de = (SELECT cliente_de FROM gnupanel_reseller WHERE id_reseller = $id_reseller) ORDER BY id_reseller LIMIT 1 ";
    $res_consulta = pg_query($conexion,$consulta);
    $dominio = pg_fetch_result($res_consulta,0,1);
    $result = $dominio;

pg_close($conexion);
return $result;
}



function verifica_cuentadigital($monto)
{
    global $escribir;
	$retorno = NULL;
	$divisa = dame_divisa();
	$monto_minimo_cuentadigital = 10;
	if($monto < $monto_minimo_cuentadigital) $retorno = $escribir['monto_chico']." ";
	if(!verifica_dato($monto,true)) $retorno = $escribir['carac_inv']." ";
	//if(!($divisa=="\$AR")) $retorno = $escribir['divisa_inv']." ";
	return $retorno;
}

function cuentadigital($monto)
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
    if($test_in=="true") $test = 1;
    if($active_in=="true") $active = 1;

    $id_reseller = $_SESSION['id_reseller'];

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $reseller = date('YmdHis')."_reseller_".dame_reseller();
    $codigo_seguimiento = crc32($reseller);
    if($codigo_seguimiento<0) $codigo_seguimiento = (-1)*$codigo_seguimiento;
    $consulta = "INSERT INTO pagos_reseller(id_reseller,importe,empresa_cobranza,codigo_seguimiento) VALUES($id_reseller,$monto,'cuentadigital','$codigo_seguimiento') ";
    $res_consulta = pg_query($conexion,$consulta);

    if($res_consulta)
	{
	$result = array();
	$result['importe'] = $monto;
	$result['reseller'] = $reseller;
	$result['codigo_seguimiento'] = $codigo_seguimiento;
	}

pg_close($conexion);
return $result;
}

function cuentadigital_0($procesador,$mensaje)
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

	if(cuentadigital_configurado())
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

function cuentadigital_1($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;

	$cuentadigital_data = cuentadigital_configurado();
	$monto = $_POST['monto'];
	$moneda = dame_divisa();
	$checkeo = NULL;
	$checkeo = verifica_cuentadigital($monto);

	if($checkeo)
	{
	cuentadigital_0($nombre_script,$checkeo);
	}
	else
	{
	$data_cuentadigital = cuentadigital($monto);

	print "<div id=\"formulario\" > \n";
	print "<ins> \n";

	if($data_cuentadigital)
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
	
	$escriba = $escribir['pagar_cuentadigital'];
	$procesador_inc = $cuentadigital_data['link_cuentadigital'];
	$variables = array();
	$variables['id'] = $cuentadigital_data['id_cuentadigital'];
	$variables['site'] = dame_dominio_venta();
	$variables['concepto'] = "R_".dame_reseller();
	$variables['codigo'] = $data_cuentadigital['codigo_seguimiento'];
	$variables['venc'] = "20";
	$variables['precio'] = $monto;
	$variables['moneda'] = dame_moneda_cuenta_digital();
	boton_con_formulario_cuentadigital($procesador_inc,$escriba,$variables,$cuentadigital_data['cuentadigital_imagen']);

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

function cuentadigital_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];

	switch($paso)
	{
		case "1":
		cuentadigital_1($nombre_script,NULL);
		break;
		default:
		cuentadigital_0($nombre_script,NULL);
	}
}



?>
