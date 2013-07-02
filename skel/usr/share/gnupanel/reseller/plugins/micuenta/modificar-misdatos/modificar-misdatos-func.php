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

function dame_data_reseller($id_reseller)
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
    $result = NULL;
    $result = array();

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * FROM gnupanel_reseller_data WHERE id_reseller = $id_reseller";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	return NULL;
	}
    else
	{
	$retorno = pg_fetch_assoc($res_consulta);
	}

    $consulta = "SELECT correo_contacto FROM gnupanel_reseller WHERE id_reseller = $id_reseller";
    $res_consulta = pg_query($conexion,$consulta);
    $correo_contacto = pg_fetch_result($res_consulta,0,0);

    if(is_array($retorno))
    {
    foreach($retorno as $llave => $arreglo)
	{
		if($llave!='id_reseller' && $llave!='usuario_desde')
		{
		$result[$llave] = $arreglo;
		if($llave == 'apellido') $result['correo_contacto'] = $correo_contacto;
		}
	}
    }

pg_close($conexion);
return $result;
}

function verifica_modificar_misdatos($primer_nombre,$segundo_nombre,$apellido,$correo_contacto,$compania,$pais,$provincia,$ciudad,$calle,$numero,$piso,$departamento,$codpostal,$telefono,$telefono_celular,$fax)
{
    global $escribir;
	$retorno = NULL;
	if(!verifica_dato($primer_nombre,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($segundo_nombre,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($apellido,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_correo($correo_contacto)) $retorno = $escribir['correo_inv']." ";
	if(!verifica_dato($compania,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($pais,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($provincia,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($ciudad,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
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

function modificar_misdatos($id_reseller,$primer_nombre,$segundo_nombre,$apellido,$correo_contacto,$compania,$pais,$provincia,$ciudad,$calle,$numero,$piso,$departamento,$codpostal,$telefono,$telefono_celular,$fax)
{
global $servidor_db;
global $puerto_db;
global $database;
global $usuario_db;
global $passwd_db;
global $_SESSION;
$retorno = NULL;
$consulta = "";
$res_consulta = NULL;
$conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
$conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
$checkeo = NULL;
if(strlen($primer_nombre)>0) $consulta = $consulta."primer_nombre = '$primer_nombre',";
if(strlen($segundo_nombre)>=0) $consulta = $consulta."segundo_nombre = '$segundo_nombre',";
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
$res_consulta = pg_query($conexion,"BEGIN");
$checkeo = $res_consulta;
$consulta_ini = "UPDATE gnupanel_reseller_data SET ";
$consulta_fin = " WHERE id_reseller = $id_reseller";
$consulta_tot = $consulta_ini.$consulta.$consulta_fin;
$res_consulta = pg_query($conexion,$consulta_tot);
$checkeo = $checkeo && $res_consulta;
$consulta_ini = "UPDATE gnupanel_usuario_data SET ";
$consulta_fin = " WHERE id_usuario = (SELECT id_usuario FROM gnupanel_usuario WHERE cliente_de = $id_reseller ORDER BY id_usuario LIMIT 1)";
$consulta_tot = $consulta_ini.$consulta.$consulta_fin;

$consulta_2 = "UPDATE gnupanel_reseller SET correo_contacto = '$correo_contacto' WHERE id_reseller = $id_reseller ";
$consulta_3 = "UPDATE gnupanel_usuario SET correo_contacto = '$correo_contacto'".$consulta_fin;


$res_consulta = pg_query($conexion,$consulta_tot);
$checkeo = $checkeo && $res_consulta;

$res_consulta = pg_query($conexion,$consulta_2);
$checkeo = $checkeo && $res_consulta;

$res_consulta = pg_query($conexion,$consulta_3);
$checkeo = $checkeo && $res_consulta;

if($checkeo)
	{
	$res_consulta = pg_query($conexion,"END");
	}
else
	{
	$res_consulta = pg_query($conexion,"ROLLBACK");
	}
}

pg_close($conexion);
return $checkeo;
}

function modificar_misdatos_0($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$reseller_data = NULL;
	$id_reseller = $_SESSION['id_reseller'];	

	$primer_nombre = trim($_POST['primer_nombre']);
	$segundo_nombre = trim($_POST['segundo_nombre']);
	$apellido = trim($_POST['apellido']);
	$correo_contacto = trim($_POST['correo_contacto']);
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
	$paises = dame_paises();

	print "<div id=\"formulario\" > \n";
	if($mensaje) print "$mensaje <br/> \n";
	print "<ins> \n";
	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table width=\"80%\" > \n";
	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
	if(!isset($_POST['apellido']))
	{
	$reseller_data = dame_data_reseller($id_reseller);
	
	if(is_array($reseller_data))
	{
	foreach($reseller_data as $llave => $arreglo)
	{
		$tipo_form = "text";
		$largor = 40;
		switch($llave)
		{
		case "numero":
		$tipo_form = "text_int";
		break;

		case "piso":
		$tipo_form = "text_int";
		break;

		case "correo_contacto":
		$tipo_form = "text_correo";
		break;

		case "pais":
		$tipo_form = "select_pais";
		$largor = $arreglo;
		$arreglo = $paises;
		break;

		default:
		$tipo_form = "text";
		}
		genera_fila_formulario($llave,$arreglo,$tipo_form,$largor,NULL,NULL,true);
	}
	}
	}
	else
	{
	genera_fila_formulario("primer_nombre",$primer_nombre,"text",40,!$mensaje,NULL,true);
	genera_fila_formulario("segundo_nombre",$segundo_nombre,"text",40,!$mensaje,NULL,true);
	genera_fila_formulario("apellido",$apellido,"text",40,!$mensaje,NULL,true);
	genera_fila_formulario("correo_contacto",$correo_contacto,"text_correo",40,!$mensaje,NULL,true);
	genera_fila_formulario("compania",$compania,"text",40,!$mensaje,NULL,true);
	genera_fila_formulario("pais",$paises,"select_pais",$pais,NULL);
	genera_fila_formulario("provincia",$provincia,"text",40,!$mensaje,NULL,true);
	genera_fila_formulario("ciudad",$ciudad,"text",40,!$mensaje,NULL,true);
	genera_fila_formulario("calle",$calle,"text",40,!$mensaje,NULL,true);
	genera_fila_formulario("numero",$numero,"text_int",40,!$mensaje,NULL);
	genera_fila_formulario("piso",$piso,"text_int",40,!$mensaje,NULL);
	genera_fila_formulario("departamento",$departamento,"text",40,!$mensaje,NULL,true);
	genera_fila_formulario("codpostal",$codpostal,"text",40,!$mensaje,NULL,true);
	genera_fila_formulario("telefono",$telefono,"text",40,!$mensaje,NULL,true);
	genera_fila_formulario("telefono_celular",$telefono_celular,"text",40,!$mensaje,NULL,true);
	genera_fila_formulario("fax",$fax,"text",40,!$mensaje,NULL,true);
	}

	genera_fila_formulario("comienzo",$comienzo,'hidden',NULL,true,NULL);
	genera_fila_formulario("ingresando","1",'hidden',NULL,true,NULL);
	genera_fila_formulario("resetea",NULL,'reset',NULL,true,NULL);
	genera_fila_formulario("modifica",NULL,'submit',NULL,true,NULL);

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

function modificar_misdatos_1($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;

	$id_reseller = $_SESSION['id_reseller'];	
	
	$primer_nombre = trim($_POST['primer_nombre']);
	$segundo_nombre = trim($_POST['segundo_nombre']);
	$apellido = trim($_POST['apellido']);
	$correo_contacto = trim($_POST['correo_contacto']);
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

	$checkeo = NULL;
	$checkeo = verifica_modificar_misdatos($primer_nombre,$segundo_nombre,$apellido,$correo_contacto,$compania,$pais,$provincia,$ciudad,$calle,$numero,$piso,$departamento,$codpostal,$telefono,$telefono_celular,$fax);

	if($checkeo)
	{
	modificar_misdatos_0($nombre_script,$checkeo);
	}
	else
	{
	$escriba = NULL;
	if(modificar_misdatos($id_reseller,$primer_nombre,$segundo_nombre,$apellido,$correo_contacto,$compania,$pais,$provincia,$ciudad,$calle,$numero,$piso,$departamento,$codpostal,$telefono,$telefono_celular,$fax))
	{
	$escriba = $escribir['exito'];
	}
	else
	{
	$escriba = $escribir['fracaso'];
	}	

	print "<div id=\"formulario\" > \n";
	print "<ins> \n";
	print "<br><br>$escriba <br><br>\n";
	print "</ins> \n";
	print "</div> \n";
	print "<div id=\"botones\" > \n";
	print "</div> \n";


	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";
	}
}

function modificar_misdatos_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];

	switch($paso)
	{
		case "1":
		modificar_misdatos_1($nombre_script,NULL);
		break;
		default:
		modificar_misdatos_0($nombre_script,NULL);
	}
}



?>
