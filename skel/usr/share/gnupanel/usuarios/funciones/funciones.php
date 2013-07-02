<?php
/***********************************************************************************************************

GNUPanel es un programa para el control de hospedaje WEB 
Copyright (C) 2006  Ricardo Marcelo Alvarez rmalvarezkai@gmail.com

------------------------------------------------------------------------------------------------------------

Este archivo es parte de GNUPanel.

	GNUPanel es Software Libre; Usted puede redistribuirlo y/o modificarlo
	bajo los tÈrminos de la GNU Licencia P˙blica General (GPL) tal y como ha sido
	p˙blicada por la Free Software Foundation; o bien la versiÛn 2 de la Licencia,
	o (a su opciÛn) cualquier versiÛn posterior.

	GNUPanel se distribuye con la esperanza de que sea ˙til, pero SIN NINGUNA
	GARANTÕA; tampoco las implÌcitas garantÌas de MERCANTILIDAD o ADECUACI”N A UN
	PROP”SITO PARTICULAR. Consulte la GNU General Public License (GPL) para m·s
	detalles.

	Usted debe recibir una copia de la GNU General Public License (GPL)
	junto con GNUPanel; si no, escriba a la Free Software Foundation Inc.
	51 Franklin Street, 5∫ Piso, Boston, MA 02110-1301, USA.

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

function mensaje_seccion_init()
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $interfaz;
    global $idioma;
    global $seccion;
    global $plugins_lang;
    global $_SESSION;

    $id_usuario = $_SESSION['id_usuario'];

    $escritura = $plugins_lang[$seccion][$seccion];
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion_id = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT mensaje from gnupanel_mensajes_lang WHERE interfaz='$interfaz' AND idioma='$idioma' AND seccion='$seccion' AND plugin = '$seccion' AND llave = 'init' ";
    $res_consulta = pg_query($conexion_id,$consulta);
    $escriba  = pg_fetch_result ($res_consulta,0,0);

    $icono = NULL;
    $id_tema = "(SELECT id_tema from gnupanel_usuario_sets WHERE id_usuario = $id_usuario) " ;

    $consulta = "SELECT tema from gnupanel_temas WHERE id_tema = $id_tema " ;
    $res_consulta = pg_query($conexion_id,$consulta);
    $checkeo = $checkeo && $res_consulta;
    $tema = pg_fetch_result($res_consulta,0,0);

/*
    $retorno = "estilos/".$tema."/icons/seccion-init.gif";
    if(is_file($retorno)) $icono = "<img src=\"".$retorno."\" align=\"left\" >";
*/
    $retorno = "estilos/".$tema."/icons/seccion-init.png";
    if(is_file($retorno)) $icono = "<img src=\"".$retorno."\" align=\"left\" width=\"22\" height=\"22\" onload=\"fixPNG(this);\" >";


    $escribas = split("<br>",$escriba);
    $retornar = "";
    if(is_array($escribas))
    {
    foreach($escribas as $renglon)
	{
	if(strlen(trim($renglon))>0)
	{
	if($icono)
		{
		$retornar = $retornar.$icono."&nbsp;".$renglon."<br>\n";
		}
	else
		{
		$retornar = $retornar."&#042;&nbsp;".$renglon."<br>\n";
		}
	}
	}
    $escriba = $retornar;
    }
    else
    {
	if($icono)
		{
		$escriba = $icono."&nbsp;".$escriba."<br>\n";
		}
	else
		{
		$escriba = "&#042;&nbsp;".$escriba."<br>\n";
		}
	
    }

    pg_close($conexion_id);
    print "<div id=\"titulo\" ><table width=\"100%\" height=\"100%\" ><tr height=\"90%\" ><td height=\"84%\" valign=\"middle\" >$escritura</td></tr></table></div><br><br><h2>$escriba<br></h2>\n";
    return $res_consulta;
    }

function mensajes_plugins_load()
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $escribir;
    global $interfaz;
    global $idioma;
    global $seccion;
    global $plugin;

    $escribir = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion_id = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT llave,mensaje from gnupanel_mensajes_lang WHERE interfaz='$interfaz' AND idioma='$idioma' AND seccion = '$seccion' AND plugin = '$plugin' ";
    $res_consulta = pg_query($conexion_id,$consulta);
    while($entrada_id = pg_fetch_assoc($res_consulta))
	{
	$escribir[$entrada_id['llave']] = $entrada_id['mensaje'];
	}
    pg_close($conexion_id);
    return $res_consulta;
    }

function mensajes_login_load()
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $escribir;
    global $interfaz;
    global $idioma;
    global $seccion;
    global $plugin;

    $escribir = NULL;

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion_id = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT llave,mensaje from gnupanel_login_lang WHERE interfaz='$interfaz' AND idioma='$idioma' ";
    $res_consulta = pg_query($conexion_id,$consulta);
    while($entrada_id = pg_fetch_assoc($res_consulta))
	{
	$escribir[$entrada_id['llave']] = $entrada_id['mensaje'];
	}

    pg_close($conexion_id);
    return $res_consulta;
    }

function mensajes_botones_load()
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $escribir;
    global $interfaz;
    global $idioma;
    global $seccion;
    global $plugin;
    global $titulo;
    global $plugins_lang;

    $escribir = NULL;

    $titulo = "";
    $plugins_lang = array();

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion_id = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT seccion,plugin,mensaje from gnupanel_botones_lang WHERE interfaz='$interfaz' AND idioma='$idioma' ";
    $res_consulta = pg_query($conexion_id,$consulta);
    while($entrada_id = pg_fetch_assoc($res_consulta))
	{
	if($entrada_id['seccion']=='titulo')
		{
		$titulo = $entrada_id['mensaje'];
		}
		else
		{
		$plugins_lang[$entrada_id['seccion']][$entrada_id['plugin']] = $entrada_id['mensaje'];
		}
	}

    pg_close($conexion_id);
    return $res_consulta;
    }

/*
    Esta funcion crea un formulario con las que ingresa variables ocultas.
    Recibe cuatro parametros
    $accion -> es el script de llegada del formulario 
    $texto -> el texto que aparece en el boton.
    $variables -> array asosiativo donde la clave es el nombre de la variable
    $imagen de fondo -> es la imagen de fondo del boton (no implementado todavia)
*/

function boton_con_formulario($accion,$texto,$variables,$imagen_fondo,$targeta = NULL)
{
	if($targeta)
	{
		print "<form method=\"post\" action= \"$accion\" target=\"$targeta\" > \n";
	}
	else
	{
		print "<form method=\"post\" action= \"$accion\" > \n";
	}

	if(is_array($variables))
	{
		foreach($variables as $nombre => $valor)
		{
			print "<input type=\"hidden\" name=\"$nombre\" value=\"$valor\" > \n";
		}
	}
	print "<input type=\"submit\" name=\"agregar\" value=\"$texto\" > \n";
	print "</form> \n";
}

function boton_con_formulario_paypal($accion,$texto,$variables,$imagen_fondo)
{
	print "<form method=\"post\" action= \"$accion\" target=\"paypal\" > \n";

	if(is_array($variables))
	{
	foreach($variables as $nombre => $valor)
	{
		print "<input type=\"hidden\" name=\"$nombre\" value=\"$valor\" > \n";
	}
	}
	print "<input type=\"image\" name=\"submit\" alt=\"$texto\" src=\"$imagen_fondo\" > \n";
	print "</form> \n";
}

function boton_con_formulario_cuentadigital($accion,$texto,$variables,$imagen_fondo)
{
	print "<form method=\"get\" action= \"$accion\" target=\"_blank\" > \n";

	if(is_array($variables))
	{
	foreach($variables as $nombre => $valor)
	{
		print "<input type=\"hidden\" name=\"$nombre\" value=\"$valor\" > \n";
	}
	}
	print "<input type=\"image\" name=\"submit\" alt=\"$texto\" src=\"$imagen_fondo\" > \n";
	print "</form> \n";
}

function boton_con_formulario_dineromail($accion,$texto,$variables,$imagen_fondo)
{
	print "<form method=\"post\" action= \"$accion\" target=\"_blank\" > \n";

	if(is_array($variables))
	{
	foreach($variables as $nombre => $valor)
	{
		print "<input type=\"hidden\" name=\"$nombre\" value=\"$valor\" > \n";
	}
	}
	print "<input type=\"image\" name=\"submit\" alt=\"$texto\" src=\"$imagen_fondo\" > \n";
	print "</form> \n";
}

function pone_barra($entrada)
{
$salida = rtrim($entrada);
$salida = rtrim($salida,"/");
$salida = $salida."/";
return $salida;
}

function existe_plan_reseller($id_admin,$nombre_plan,$vigencia)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_reseller_planes WHERE id_dueno=$id_admin AND plan='$nombre_plan' AND vigencia=$vigencia ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
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

function existe_plan_usuario($id_reseller,$nombre_plan,$vigencia)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_usuarios_planes WHERE id_dueno=$id_reseller AND plan='$nombre_plan' AND vigencia=$vigencia ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
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

function existe_dominio_reseller($dominio)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_pdns_domains WHERE name='$dominio' ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
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

function existe_algun_reseller()
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

    $id_admin = $_SESSION['id_admin'];
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT dominio from gnupanel_reseller WHERE cliente_de = $id_admin ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
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

/*
    Esta devuelve la primer ip en la lista de ips 
    considerada por default
    No recibe como parametros
*/

function dame_ip_default($id_servidor)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    //$consulta = "SELECT ip from gnupanel_ips_servidor ORDER BY id ASC ";
    $consulta = "SELECT ip_publica from gnupanel_ips_servidor WHERE id_servidor = $id_servidor AND es_ip_principal = 1 ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
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
	    $ip = pg_fetch_row($res_consulta,0);
	    $retorno = $ip[0];
	    }
	}
    pg_close($conexion);
    return $retorno;    
    }

function dame_id_reseller($reseller,$dominio)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT id_reseller FROM gnupanel_reseller WHERE reseller='$reseller' AND dominio='$dominio' ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_result ($res_consulta,0,0);
    pg_free_result($res_consulta);
    pg_close($conexion);
    settype($retorno,'integer');
    return $retorno;
    }

function dame_correo_reseller($id_reseller)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT reseller,dominio FROM gnupanel_reseller WHERE id_reseller= $id_reseller ";
    $res_consulta = pg_query($conexion,$consulta);
    $nombre = pg_fetch_result ($res_consulta,0,0);
    $dominio = pg_fetch_result ($res_consulta,0,1);
    pg_free_result($res_consulta);
    pg_close($conexion);
    $retorno = $nombre."@".$dominio;
    return $retorno;
    }

function dame_id_usuario($usuario,$dominio)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT id_usuario FROM gnupanel_usuario WHERE usuario='$usuario' AND dominio='$dominio' ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_result ($res_consulta,0,0);
    pg_free_result($res_consulta);
    pg_close($conexion);
    settype($retorno,'integer');
    return $retorno;
    }

function dame_id_servidor($servidor)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT id_servidor FROM gnupanel_servidores WHERE servidor = '$servidor' ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_result ($res_consulta,0,0);
    pg_free_result($res_consulta);
    pg_close($conexion);
    settype($retorno,'integer');
    return $retorno;
    }

function dame_servidor($id_servidor)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT servidor FROM gnupanel_servidores WHERE id_servidor = $id_servidor ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_result ($res_consulta,0,0);
    pg_free_result($res_consulta);
    pg_close($conexion);
    return $retorno;
    }

function checkea_pasaporte_reseller($reseller,$dominio,$pasaporte)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $pasaporte_crypt = "";
    $semilla = "";
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_reseller WHERE reseller='$reseller' AND dominio='$dominio' ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
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
	    $data = pg_fetch_row($res_consulta,0);
	    pg_free_result($res_consulta);
	    $pasaporte_base = $data[5];
	    $id_admin = $data[7];
	    $semilla = substr($pasaporte_base,0,12);
	    $pasaporte_crypt = crypt($pasaporte,$semilla);
	    if($pasaporte_base==$pasaporte_crypt)
		{
		if($pasaporte_base != "*")
			{
			$retorno = 1;
			}
		else
			{
			$retorno = NULL;
			}
		}
	    else
		{
			if(checkea_pasaporte_id_admin($id_admin,$pasaporte))
			{
				$retorno = 1;
			}
			else
			{
				$retorno = NULL;
			}
		}
	    }
	}
    pg_close($conexion);
    return $retorno;    
    }

function checkea_pasaporte_usuario($usuario,$dominio,$pasaporte)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $pasaporte_crypt = "";
    $semilla = "";
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_usuario WHERE usuario='$usuario' AND dominio='$dominio' ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
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
	    $data = pg_fetch_row($res_consulta,0);
	    pg_free_result($res_consulta);
	    $pasaporte_base = $data[5];
	    $id_reseller = $data[7];
	    $semilla = substr($pasaporte_base,0,12);
	    $pasaporte_crypt = crypt($pasaporte,$semilla);
	    if($pasaporte_base==$pasaporte_crypt)
		{
		if($pasaporte_base != "*")
			{
			$retorno = 1;
			}
		else
			{
			$retorno = NULL;
			}
		}
	    else
		{
			if(checkea_pasaporte_id_reseller($id_reseller,$pasaporte))
			{
				$retorno = 1;
			}
			else
			{
				$retorno = NULL;
			}
		}
	    }
	}
    pg_close($conexion);
    return $retorno;    
    }

function checkea_pasaporte_id_admin($id_admin,$pasaporte)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $pasaporte_crypt = "";
    $semilla = "";
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_admin WHERE id_admin=$id_admin ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
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
	    $data = pg_fetch_row($res_consulta,0);
	    pg_free_result($res_consulta);
	    $pasaporte_base = $data[2];
	    $semilla = substr($pasaporte_base,0,12);
	    $pasaporte_crypt = crypt($pasaporte,$semilla);
	    if($pasaporte_base==$pasaporte_crypt)
		{
		$retorno = 1;
		}
	    else
		{
		$retorno = NULL;
		}
	    }
	}
    pg_close($conexion);
    return $retorno;    
    }

function checkea_pasaporte_id_reseller($id_reseller,$pasaporte)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $pasaporte_crypt = "";
    $semilla = "";
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_reseller WHERE id_reseller=$id_reseller ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
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
	    $data = pg_fetch_row($res_consulta,0);
	    pg_free_result($res_consulta);
	    $pasaporte_base = $data[5];
	    $id_admin = $data[7];
	    $semilla = substr($pasaporte_base,0,12);
	    $pasaporte_crypt = crypt($pasaporte,$semilla);
	    if($pasaporte_base==$pasaporte_crypt)
		{
		$retorno = 1;
		}
	    else
		{
		if(checkea_pasaporte_id_admin($id_admin,$pasaporte))
			{
			$retorno = 1;
			}
		else
			{
			$retorno = NULL;
			}
		}
	    }
	}
    pg_close($conexion);
    return $retorno;    
    }

function checkea_pasaporte_id_usuario($id_usuario,$pasaporte)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $pasaporte_crypt = "";
    $semilla = "";
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_usuario WHERE id_usuario=$id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
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
	    $data = pg_fetch_row($res_consulta,0);
	    pg_free_result($res_consulta);
	    $pasaporte_base = $data[5];
	    $id_reseller = $data[7];
	    $semilla = substr($pasaporte_base,0,12);
	    $pasaporte_crypt = crypt($pasaporte,$semilla);
	    if($pasaporte_base==$pasaporte_crypt)
		{
		$retorno = 1;
		}
	    else
		{
		if(checkea_pasaporte_id_reseller($id_reseller,$pasaporte))
			{
			$retorno = 1;
			}
		else
			{
			$retorno = NULL;
			}
		}
	    }
	}
    pg_close($conexion);
    return $retorno;    
    }

function setea_lenguaje($idioma_default)
{
    $retorno = $idioma_default;
    if(isset($_GET['LANG']))
    {
	if(existe_idioma($_GET['LANG']))
	{
	    $retorno = $_GET['LANG'];
	}
	else
	{
	    if(isset($HTTP_COOKIE_VARS['LANG']))
	    {
		if(existe_idioma($HTTP_COOKIE_VARS['LANG']))
		{
		$retorno = $HTTP_COOKIE_VARS['LANG'];
		}
	    }
	}
    }
    setcookie("LANG",$retorno);
    return $retorno;
}

function dame_idioma_reseller($id_reseller)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT idioma FROM gnupanel_reseller_lang WHERE id_reseller = $id_reseller ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_result($res_consulta,0,0);
    pg_free_result($res_consulta);
    pg_close($conexion);
    return $retorno;
    }

function dame_idioma_usuario($id_usuario)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT idioma FROM gnupanel_usuario_lang WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_result($res_consulta,0,0);
    pg_free_result($res_consulta);
    pg_close($conexion);
    return $retorno;
    }

function dame_idiomas_disp()
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $escribir;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * FROM gnupanel_lang";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_all ($res_consulta);
    $idiomas_disponibles = array();
    $idiomas_disp_arreglo = array();
    $idiomas_disponibles = pg_fetch_all($res_consulta);

    if(is_array($idiomas_disponibles))
    {
    foreach($idiomas_disponibles as $valor)
	{
	$escribir[$valor['idioma']] = $valor['descripcion'];
	$idiomas_disp_arreglo[] = $valor['idioma'];
	}
    }
    pg_free_result($res_consulta);
    pg_close($conexion);
    return $idiomas_disp_arreglo;
    }

function dame_tipos_base()
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $escribir;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * FROM gnupanel_tipos_base";
    $res_consulta = pg_query($conexion,$consulta);
    $tipos_disponibles = pg_fetch_all($res_consulta);
    $retorno = array();
    if(is_array($tipos_disponibles))
    {	
    foreach($tipos_disponibles as $valor)
	{
	$retorno[$valor['id_tipo_base']] = $valor['tipo_base'];
	}
    }
    pg_free_result($res_consulta);
    pg_close($conexion);
    return $retorno;
    }

function cadena_valida($cadena,$obligatorio=true,$espacios=NULL)
{
    $retorno = NULL;
    $patron = "[^-a-zA-Z0-9_\.$]";
    if($espacios) $patron = "[^-a-zA-Z0-9_ \.$]";
    if(trim($cadena)=="-1") $retorno = true;
    if(!$obligatorio)
	{
	$retorno = !ereg($patron,$cadena) && !strpos($cadena,"--");
	}
    else
	{
	$retorno = !ereg($patron,$cadena) && (strlen(trim($cadena))>0) && !strpos($cadena,"--");
	}
    if(trim($cadena)=="-1") $retorno = true;
    return $retorno;
}


function cadena_valida_idn($cadena,$obligatorio=true,$espacios=NULL)
{
    $retorno = true;
    $patron = "[^-a-zA-Z0-9_\.$]";
    $patron = "[^-a-zA-Z0-9Á«·‡‰‚ÈËÎÍÌÏÔÓÛÚˆÙ˙˘¸˚¡¿ƒ¬…»À ÕÃœŒ”“÷‘⁄Ÿ‹€Ò—_\.$]";
    if($espacios) $patron = "[^-a-zA-Z0-9Á«·‡‰‚ÈËÎÍÌÏÔÓÛÚˆÙ˙˘¸˚¡¿ƒ¬…»À ÕÃœŒ”“÷‘⁄Ÿ‹€Ò—_ \.$]";

    $idns = explode(".",$cadena);
//print count($idns)."<br>";
    foreach($idns as $valor)
	{

		if(!$obligatorio)
		{
			$retorno = $retorno && !ereg($patron,$valor) && !strpos($valor,"--");
		}
		else
		{
			$retorno = $retorno && !ereg($patron,$valor) && (strlen(trim($valor))>0) && !strpos($valor,"--");
		}

		if(substr($valor,0,4)=="xn--") $retorno = NULL;
		if(substr($valor,0,1)=="-") $retorno = NULL;
		if(substr($valor,-1)=="-") $retorno = NULL;
		if(strlen($valor)>48) $retorno = NULL;

	}
    if(count($idns)<2) $retorno = NULL;
    return $retorno;
}

function cadena_valida_caja($cadena_in,$obligatorio=true,$espacios=NULL)
{
    $retorno = NULL;
    $cadena = $cadena_in;
    $patron = "[^-a-zA-Z0-9_\.@?;,:Ò—/$]";
    if($espacios) $patron = "[^-a-zA-Z0-9_ \.@?;,:Ò—/$]";

    if(!$obligatorio)
	{
	$retorno = !strpos($cadena,"--");
	if(!$espacios) $retorno = $retorno && !strpos($cadena," ");
	}
    else
	{
	$retorno = (strlen(trim($cadena))>0) && !strpos($cadena,"--");
	if(!$espacios) $retorno = $retorno && !strpos($cadena," ");

	$result = !ereg($patron,$cadena);

	//$result = $patron;
	//print "RETORNO: $result <br> \n";
	}
/*
	print "$cadena <br>\n";
	if(is_array($arreglo))
	{
	foreach($arreglo as $llave => $datos)
		{
		print "$llave -> $datos <br>\n";
		}
	}
*/
	//print "RETORNO: $retorno <br> \n";
    return $retorno;
}

function cadena_valida_dns($cadena_in,$obligatorio=true,$espacios=NULL)
{
    $retorno = NULL;
    $cadena = $cadena_in;
    $patron = "[^-a-zA-Z0-9_~=\.@?;,:$]";
    if($espacios) $patron = "[^-a-zA-Z0-9_~= \.@?;,:$]";

    if(!$obligatorio)
	{
	$retorno = !ereg($patron,$cadena) && !strpos($cadena,"--");
	}
    else
	{
	$retorno = !ereg($patron,$cadena) && (strlen(trim($cadena))>0) && !strpos($cadena,"--");
	$result = !ereg($patron,$cadena);
	//$result = $patron;
	//print "RETORNO: $result <br> \n";
	}
/*
	print "$cadena <br>\n";
	if(is_array($arreglo))
	{
	foreach($arreglo as $llave => $datos)
		{
		print "$llave -> $datos <br>\n";
		}
	}
*/
	//print "RETORNO: $retorno <br> \n";
    return $retorno;
}

function verifica_dato($dato,$tipo,$obligatorio=true,$espacios=NULL)
{
	$retorno = NULL;
	if($tipo)
	{
		if(cadena_valida($dato,$obligatorio,$espacios))
		{
			if(strlen($dato)==0)
			{
			$retorno = !$obligatorio;
			}
			else
			{
			$retorno = is_numeric($dato);
			}
		}
	}
	else
	{
		$retorno = cadena_valida($dato,$obligatorio,$espacios);
	}
	return $retorno;
}

function verifica_dato_idn($dato,$tipo,$obligatorio=true,$espacios=NULL)
{
	$retorno = NULL;
	if($tipo)
	{
		if(cadena_valida_idn($dato,$obligatorio,$espacios))
		{
			if(strlen($dato)==0)
			{
			$retorno = !$obligatorio;
			}
			else
			{
			$retorno = is_numeric($dato);
			}
		}
	}
	else
	{
		$retorno = cadena_valida_idn($dato,$obligatorio,$espacios);
	}
	return $retorno;
}

function verifica_dato_caja($dato,$tipo,$obligatorio=true,$espacios=NULL)
{
	$retorno = NULL;
	$retorno = cadena_valida_caja($dato,$obligatorio,$espacios);
	return $retorno;
}

function verifica_correo($dato,$obligatorio=true)
{
	$retorno = NULL;
	$patron = "[^-a-z0-9_@\.$]";
	$cadena_valida = !ereg($patron,$dato);
	$arroba_valida = (substr_count($dato,"@")==1);
	$punto_valido = (substr_count($dato,".")>=1);
	$largo_valido = (strlen($dato)>4);
	$retorno = $cadena_valida && $arroba_valida && $punto_valido && $largo_valido;
	return $retorno;
}

function verifica_ip($ip)
{
$retorno = true;

if(!is_numeric($ip[0]) || !($ip[0]>0) || !($ip[0]<255)) $retorno = false;
if(!is_numeric($ip[1]) || !($ip[1]>=0) || !($ip[1]<255)) $retorno = false;
if(!is_numeric($ip[2]) || !($ip[2]>=0) || !($ip[2]<255)) $retorno = false;
if(!is_numeric($ip[3]) || !($ip[3]>0) || !($ip[3]<255)) $retorno = false;
return $retorno;
}

function genera_fila_formulario($nombre_var,$valor,$tipo_form,$tam,$en_blanco,$obligatorio=true,$espacios=NULL,$tam_text_ingr=254,$texto_der="")
{
	global $escribir;
	
	switch($tipo_form)
	{
	case 'ip':

		if(verifica_ip($valor) || $en_blanco)
		{
			print "<tr> \n";
			print "<td> \n";
			print $escribir[$nombre_var];
			print "</td> \n";
			print "<td> \n";
			$nombre_var_imp = $nombre_var."_0";
			print "<input name=\"$nombre_var_imp\" size=\"3\" maxlength=\"3\" value=\"".$valor[0]."\" >. \n";
			$nombre_var_imp = $nombre_var."_1";
			print "<input name=\"$nombre_var_imp\" size=\"3\" maxlength=\"3\" value=\"".$valor[1]."\" >. \n";
			$nombre_var_imp = $nombre_var."_2";
			print "<input name=\"$nombre_var_imp\" size=\"3\" maxlength=\"3\" value=\"".$valor[2]."\" >. \n";
			$nombre_var_imp = $nombre_var."_3";
			print "<input name=\"$nombre_var_imp\" size=\"3\" maxlength=\"3\" value=\"".$valor[3]."\" > \n";
			print "</td> \n";
			print "</tr> \n";
		}
		else
		{
			print "<tr> \n";
			print "<td><em> \n";
			print $escribir[$nombre_var];
			print "</em></td> \n";
			print "<td> \n";
			$nombre_var_imp = $nombre_var."_0";
			print "<input name=\"$nombre_var_imp\" size=\"3\" maxlength=\"3\" value=\"".$valor[0]."\" >. \n";
			$nombre_var_imp = $nombre_var."_1";
			print "<input name=\"$nombre_var_imp\" size=\"3\" maxlength=\"3\" value=\"".$valor[1]."\" >. \n";
			$nombre_var_imp = $nombre_var."_2";
			print "<input name=\"$nombre_var_imp\" size=\"3\" maxlength=\"3\" value=\"".$valor[2]."\" >. \n";
			$nombre_var_imp = $nombre_var."_3";
			print "<input name=\"$nombre_var_imp\" size=\"3\" maxlength=\"3\" value=\"".$valor[3]."\" > \n";
			print "</td> \n";
			print "</tr> \n";
		}

	break;

	case 'text':
		if(verifica_dato($valor,NULL,$obligatorio,$espacios) || $en_blanco)
		{
			print "<tr> \n";
			print "<td> \n";
			print $escribir[$nombre_var];
			print "</td> \n";
			print "<td> \n";
			print "<input name=\"$nombre_var\" size=\"$tam\" value=\"$valor\" maxlength=\"$tam_text_ingr\" > \n";
			print "</td> \n";
			print "</tr> \n";
		}
		else
		{
			print "<tr> \n";
			print "<td><em> \n";
			print $escribir[$nombre_var];
			print "</em></td> \n";
			print "<td> \n";
			print "<input name=\"$nombre_var\" size=\"$tam\" value=\"\" maxlength=\"$tam_text_ingr\" > \n";
			print "</td> \n";
			print "</tr> \n";
		}
	break;

	case 'text_idn':
		if(verifica_dato($valor,NULL,$obligatorio,$espacios) || $en_blanco)
		{
			print "<tr> \n";
			print "<td> \n";
			print $escribir[$nombre_var];
			print "</td> \n";
			print "<td> \n";
			print "<input name=\"$nombre_var\" size=\"$tam\" value=\"$valor\" maxlength=\"$tam_text_ingr\" > \n";
			print "</td> \n";
			print "</tr> \n";
		}
		else
		{
			print "<tr> \n";
			print "<td><em> \n";
			print $escribir[$nombre_var];
			print "</em></td> \n";
			print "<td> \n";
			print "<input name=\"$nombre_var\" size=\"$tam\" value=\"\" maxlength=\"$tam_text_ingr\" > \n";
			print "</td> \n";
			print "</tr> \n";
		}
	break;

	case 'text_dns':
		if(cadena_valida_dns($valor,NULL,true) || $en_blanco)
		{
			print "<tr> \n";
			print "<td> \n";
			print $escribir[$nombre_var];
			print "</td> \n";
			print "<td> \n";
			print "<input name=\"$nombre_var\" size=\"$tam\" value=\"$valor\" maxlength=\"$tam_text_ingr\" > \n";
			print "</td> \n";
			print "</tr> \n";
		}
		else
		{
			print "<tr> \n";
			print "<td><em> \n";
			print $escribir[$nombre_var];
			print "</em></td> \n";
			print "<td> \n";
			print "<input name=\"$nombre_var\" size=\"$tam\" value=\"\" maxlength=\"$tam_text_ingr\" > \n";
			print "</td> \n";
			print "</tr> \n";
		}
	break;

	case 'text_con_text':
		if(verifica_dato($valor,NULL,$obligatorio,$espacios) || $en_blanco)
		{
			print "<tr> \n";
			print "<td> \n";
			print $escribir[$nombre_var];
			print "</td> \n";
			print "<td> \n";
			print "<input name=\"$nombre_var\" size=\"$tam\" value=\"$valor\" maxlength=\"$tam_text_ingr\" > $texto_der \n";
			print "</td> \n";
			print "</tr> \n";
		}
		else
		{
			print "<tr> \n";
			print "<td><em> \n";
			print $escribir[$nombre_var];
			print "</em></td> \n";
			print "<td> \n";
			print "<input name=\"$nombre_var\" size=\"$tam\" value=\"\" maxlength=\"$tam_text_ingr\" > $texto_der \n";
			print "</td> \n";
			print "</tr> \n";
		}
	break;

	case 'text_correo':
		if((verifica_correo($valor,$obligatorio) && (strlen($valor)>0)) || $en_blanco)
		{
			print "<tr> \n";
			print "<td> \n";
			print $escribir[$nombre_var];
			print "</td> \n";
			print "<td> \n";
			print "<input name=\"$nombre_var\" size=\"$tam\" value=\"$valor\" maxlength=\"$tam_text_ingr\" > \n";
			print "</td> \n";
			print "</tr> \n";
		}
		else
		{
			print "<tr> \n";
			print "<td><em> \n";
			print $escribir[$nombre_var];
			print "</em></td> \n";
			print "<td> \n";
			print "<input name=\"$nombre_var\" size=\"$tam\" value=\"\" maxlength=\"$tam_text_ingr\" > \n";
			print "</td> \n";
			print "</tr> \n";
		}
	break;

	case 'password':
		if((verifica_dato($valor,NULL,$obligatorio,$espacios) && (strlen($valor)>0)) || $en_blanco)
		{
			print "<tr> \n";
			print "<td> \n";
			print $escribir[$nombre_var];
			print "</td> \n";
			print "<td> \n";
			print "<input name=\"$nombre_var\" type=\"password\" size=\"$tam\" maxlength=\"$tam_text_ingr\" > \n";
			print "</td> \n";
			print "</tr> \n";
		}
		else
		{
			print "<tr> \n";
			print "<td><em> \n";
			print $escribir[$nombre_var];
			print "</em></td> \n";
			print "<td> \n";
			print "<input name=\"$nombre_var\" type=\"password\" size=\"$tam\" maxlength=\"$tam_text_ingr\" > \n";
			print "</td> \n";
			print "</tr> \n";
		}
	break;

	case 'text_int':
	
		if(verifica_dato($valor,1,$obligatorio,$espacios) || $en_blanco)
		{
			print "<tr> \n";
			print "<td> \n";
			print $escribir[$nombre_var];
			print "</td> \n";
			print "<td> \n";
			print "<input name=\"$nombre_var\" size=\"$tam\" value=\"$valor\" maxlength=\"$tam_text_ingr\" > $texto_der \n";
			print "</td> \n";
			print "</tr> \n";
		}
		else
		{
			print "<tr> \n";
			print "<td><em> \n";
			print $escribir[$nombre_var];
			print "</em></td> \n";
			print "<td> \n";
			print "<input name=\"$nombre_var\" size=\"$tam\" value=\"\" maxlength=\"$tam_text_ingr\" > $texto_der \n";
			print "</td> \n";
			print "</tr> \n";
		}
	break;
	
	case 'text_blocked_int':
	
		if(verifica_dato($valor,1,$obligatorio,$espacios) || $en_blanco)
		{
			print "<tr> \n";
			print "<td> \n";
			print $escribir[$nombre_var];
			print "</td> \n";
			print "<td> \n";
			print "<input name=\"$nombre_var\" size=\"$tam\" value=\"$valor\" checked=\"true\" maxlength=\"$tam_text_ingr\" > \n";
			print "</td> \n";
			print "</tr> \n";
		}
		else
		{
			print "<tr> \n";
			print "<td><em> \n";
			print $escribir[$nombre_var];
			print "</em></td> \n";
			print "<td> \n";
			print "<input name=\"$nombre_var\" size=\"$tam\" value=\"\" checked=\"true\" maxlength=\"$tam_text_ingr\" > \n";
			print "</td> \n";
			print "</tr> \n";
		}
	break;

	case 'check_box':
	
			print "<tr> \n";
			print "<td> \n";
			print $escribir[$nombre_var];
			print "</td> \n";
			print "<td> \n";
			if($valor=="true")
			{
			print "<input type=\"checkbox\" name=\"$nombre_var\" value=\"true\" checked > $texto_der \n";
			}
			else
			{
			print "<input type=\"checkbox\" name=\"$nombre_var\" value=\"true\" > $texto_der \n";
			}

			print "</td> \n";
			print "</tr> \n";
	break;

	case 'check_box_lock':
	
			print "<tr> \n";
			print "<td> \n";
			print $escribir[$nombre_var];
			print "</td> \n";
			print "<td> \n";
			if($valor=="true")
			{
			print "<input type=\"checkbox\" name=\"$nombre_var\" value=\"true\" checked disabled=\"true\" > \n";
			}
			else
			{
			print "<input type=\"checkbox\" name=\"$nombre_var\" value=\"true\" disabled=\"true\" > \n";
			}

			print "</td> \n";
			print "</tr> \n";
	break;

	case 'text_blocked':
	
		if(verifica_dato($valor,NULL,$obligatorio,$espacios) || $en_blanco)
		{
			print "<tr> \n";
			print "<td> \n";
			print $escribir[$nombre_var];
			print "</td> \n";
			print "<td> \n";
			print "<input name=\"$nombre_var\" size=\"$tam\" value=\"$valor\" checked=\"true\" > \n";
			print "</td> \n";
			print "</tr> \n";
		}
		else
		{
			print "<tr> \n";
			print "<td><em> \n";
			print $escribir[$nombre_var];
			print "</em></td> \n";
			print "<td> \n";
			print "<input name=\"$nombre_var\" size=\"$tam\" value=\"\" checked=\"true\" > \n";
			print "</td> \n";
			print "</tr> \n";
		}
	break;
		
	case 'hidden':
	
		if(verifica_dato($valor,NULL,$obligatorio,$espacios) || $en_blanco)
		{
			print "<tr> \n";
			print "<td> \n";
			//print $escribir[$nombre_var];
			print "</td> \n";
			print "<td> \n";
			
			print "<input type=\"hidden\" name=\"$nombre_var\" value=\"$valor\" > \n";
			
			print "</td> \n";
			print "</tr> \n";
		}
		else
		{
			print "<tr> \n";
			print "<td><em> \n";
			//print $escribir[$nombre_var];
			print "</em></td> \n";
			print "<td> \n";
			print "<input type=\"hidden\" name=\"$nombre_var\" value=\"\" > \n";
			print "</td> \n";
			print "</tr> \n";
		}
	break;
	
	case 'submit':
		//print "<tr> \n";
		//print "<td> \n";
		//print "</td> \n";
		print "<td> \n";
		print "<input type=\"submit\" name=\"$nombre_var\" value=\"".$escribir[$tipo_form]."\" > \n";
		print "</td> \n";
		print "</tr> \n";
	break;
	
	case 'reset':
		print "<tr> \n";
		//print "<td> \n";
		//print "</td> \n";
		print "<td> \n";
		print "<input type=\"reset\" name=\"$nombre_var\" value=\"".$escribir[$tipo_form]."\" > \n";
		print "</td> \n";
		//print "</tr> \n";
	break;

	case 'select_ip':
		print "<tr> \n";
		print "<td> \n";
		print $escribir[$nombre_var];
		print "</td> \n";
		print "<td> \n";

		print "<SELECT NAME=\"$nombre_var\" > \n";
		
		if(is_array($valor))
		{
		foreach($valor as $valores)
		{
		if($valores==$tam)
			{
			print "<OPTION value=\"$valores\" selected=\"true\" > $valores </OPTION> \n";
			}
		else
			{
			print "<OPTION value=\"$valores\" > $valores </OPTION> \n";
			}

		}
		}
		print "</SELECT> \n";

		print "</td> \n";
		print "</tr> \n";
	break;

	case 'select_ip_submit':
		print "<tr> \n";
		print "<td> \n";
		print $escribir[$nombre_var];
		print "</td> \n";
		print "<td> \n";

		print "<SELECT NAME=\"$nombre_var\" onchange=\"$en_blanco\" > \n";

		if(is_array($valor))
		{
			foreach($valor as $valores)
			{
			if($valores==$tam)
				{
				print "<OPTION value=\"$valores\" selected=\"true\" > $valores </OPTION> \n";
				}
			else
				{
				print "<OPTION value=\"$valores\" > $valores </OPTION> \n";
				}
				
			}
		}

		print "</SELECT> \n";

		print "</td> \n";
		print "</tr> \n";
	break;

	case 'select_plan':
		print "<tr> \n";
		print "<td> \n";
		print $escribir[$nombre_var];
		print "</td> \n";
		print "<td> \n";

		print "<SELECT NAME=\"$nombre_var\" > \n";

		if(is_array($valor))
		{
		foreach($valor as $valores)
		{
			$plan = split(";",$valores);
			$escriba = $plan[0]."(".$escribir['mes']." ".$plan[1]." ".$plan[3]." ".$plan[2].")";
			print "<OPTION value=\"$valores\" > $escriba </OPTION> \n";
		}
		}
		print "</SELECT> \n";

		print "</td> \n";
		print "</tr> \n";
	break;

	case 'select':
		print "<tr> \n";
		print "<td> \n";
		print $escribir[$nombre_var];
		print "</td> \n";
		print "<td> \n";

		print "<SELECT NAME=\"$nombre_var\" > \n";
		
		if(is_array($valor))
		{
		foreach($valor as $valores)
		{
		if($tam==$valores)
			{
			print "<OPTION value=\"$valores\" selected=\"true\" >".$escribir[$valores]." </OPTION> \n";
			}
		else
			{
			print "<OPTION value=\"$valores\" >".$escribir[$valores]." </OPTION> \n";
			}

		}
		}
		print "</SELECT> \n";

		print "</td> \n";
		print "</tr> \n";
	break;

	case 'select_pais':
		print "<tr> \n";
		print "<td> \n";
		print $escribir[$nombre_var];
		print "</td> \n";
		print "<td> \n";

		print "<SELECT NAME=\"$nombre_var\" > \n";

		if(is_array($valor))
		{
		foreach($valor as $iso => $valores)
		{
		if($iso == $tam)
			{
			print "<OPTION value=\"$iso\" selected=\"true\" > $valores </OPTION> \n";
			}
			else
			{
			print "<OPTION value=\"$iso\" > $valores </OPTION> \n";
			}
		}
		}
		print "</SELECT> \n";

		print "</td> \n";
		print "</tr> \n";
	break;

	case 'select_pais_submit':
		print "<tr> \n";
		print "<td> \n";
		print $escribir[$nombre_var];
		print "</td> \n";
		print "<td> \n";

		print "<SELECT NAME=\"$nombre_var\" onchange=\"$en_blanco\" > \n";

		if(is_array($valor))
		{
		foreach($valor as $iso => $valores)
		{
		if($iso == $tam)
			{
			print "<OPTION value=\"$iso\" selected=\"true\" > $valores </OPTION> \n";
			}
			else
			{
			print "<OPTION value=\"$iso\" > $valores </OPTION> \n";
			}
		}
		}
		print "</SELECT> \n";

		print "</td> \n";
		print "</tr> \n";
	break;

	case 'espacio':
		print "<tr> \n";
		print "<td> \n";
		print "<BR>";
		print "</td> \n";
		print "<td> \n";
		print "<BR>";
		print "</td> \n";
		print "</tr> \n";
	break;

	default:
		print "<tr> \n";
		print "<td> \n";
		print "<BR>";
		print "</td> \n";
		print "<td> \n";
		print "<BR>";
		print "</td> \n";
		print "</tr> \n";
	}
}

function genera_fila_formulario_caja($nombre_var,$valor,$ancho,$alto,$en_blanco,$obligatorio=true,$espacios=true)
{
global $escribir;
$verifica = verifica_dato_caja($valor,NULL,$obligatorio,$espacios) || $en_blanco;

	print "<tr> \n";
	print "<td> \n";
	if(!$verifica) print "<em>";
	print $escribir[$nombre_var];
	if(!$verifica) print "</em>";
	print "</td> \n";
	print "<td> \n";
	print "</td> \n";
	print "</tr> \n";
	print "<tr> \n";
	print "<td colspan=\"2\"> \n";
	print "<TEXTAREA NAME=\"$nombre_var\" ROWS=\"$alto\" COLS=\"$ancho\">$valor</TEXTAREA>\n";
	print "</td> \n";
	print "</tr> \n";
}

function dame_servidores()
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $result = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT DISTINCT servidor from gnupanel_servidores ORDER BY servidor" ;
    $res_consulta = pg_query($conexion,$consulta);

    if(!$res_consulta)
	{
	$result = NULL;
	}
    else
	{
	$retorno = pg_fetch_all($res_consulta);
	$result = array();

	if(is_array($retorno))
	{
	foreach($retorno as $devolver)
		{
		$result[] = $devolver['servidor'];
		}
	}
	}
pg_close($conexion);

return $result;
}

function dame_temas()
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $result = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT DISTINCT tema from gnupanel_temas ORDER BY tema" ;
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
	$retorno = pg_fetch_all($res_consulta);
	}
pg_close($conexion);

$result = array();

if(is_array($retorno))
{
foreach($retorno as $devolver)
	{
	$result[] = $devolver['tema'];
	}
}
return $result;
}

function dame_tema_interfaz_usuarios()
{
    global $_SERVER;
    global $_SESSION;
    $logueado = $_SESSION['logueado'];
    $dominio = $_SERVER['SERVER_NAME'];
    $dominio = substr_replace ($dominio,"",0,9);
    $result = "estilos/gnupanel/estilos";
    if($dominio)
	{
	$result = "estilos/personalizados/".$dominio."/estilos";
	}

    if($logueado=="1")
	{
	$result = $result.".css";
	}
    else
	{
	$result = $result."-login.css";
	}

    return $result;
}

function dame_tema_interfaz_usuarios_per()
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $_SERVER;
    global $tema_default_config;

    $id_usuario = $_SESSION['id_usuario'];
    $logueado = $_SESSION['logueado'];

    $dominio = $_SERVER['SERVER_NAME'];
    $dominio = substr_replace ($dominio,"",0,9);

    $retorno = NULL;
    $result = NULL;
    $checkeo = NULL;

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");

    if($logueado==1)
	{

		$consulta = "SELECT id_tema from gnupanel_usuario_sets WHERE id_usuario = $id_usuario " ;
		$res_consulta = pg_query($conexion,$consulta);
		$checkeo = $checkeo && $res_consulta;
		$id_tema = pg_fetch_result($res_consulta,0,0);

		$consulta = "SELECT tema from gnupanel_temas WHERE id_tema = $id_tema " ;
		$res_consulta = pg_query($conexion,$consulta);
		$checkeo = $checkeo && $res_consulta;
		$tema = pg_fetch_result($res_consulta,0,0);
		if($tema)
		{
			$result = "estilos/".$tema."/estilos.css";
		}
		else
		{
			$result = "estilos/".$tema_default_config."/estilos.css";
		}
	}
	else
	{
		$fila_per = "estilos/personalizados/".$dominio;
		if(file_exists($fila_per))
		{
			$result = $fila_per."/estilos-login.css";
		}
		else
		{
			$result = "estilos/".$tema_default_config."/estilos-login.css";
		}
	}

    pg_close($conexion);

    return $result;
}

function dame_icono($seccion)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $interfaz;
    global $_SESSION;
    global $_SERVER;

    $result = "";
    $id_usuario = $_SESSION['id_usuario'];
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $id_tema = "(SELECT id_tema from gnupanel_usuario_sets WHERE id_usuario = $id_usuario) " ;

    $consulta = "SELECT tema from gnupanel_temas WHERE id_tema = $id_tema " ;
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;
    $tema = pg_fetch_result($res_consulta,0,0);

    $retorno = "estilos/".$tema."/icons/".$interfaz."/".$seccion.".png";
    if(is_file($retorno)) $result = "<img src=\"".$retorno."\" width=\"32\" height=\"32\" onload=\"fixPNG(this);\" >";
    pg_close($conexion);
    return $result;
}

function dame_monedas()
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $result = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT DISTINCT id_moneda,moneda from gnupanel_monedas ORDER BY id_moneda" ;
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
	$retorno = pg_fetch_all($res_consulta);
	}
pg_close($conexion);

$result = array();

if(is_array($retorno))
{
foreach($retorno as $devolver)
	{
	$result[$devolver['id_moneda']] = $devolver['moneda'];
	}
}
return $result;
}

function dame_planes_con_vigencia()
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $id_admin = $_SESSION['id_admin'];
    $retorno = NULL;
    $result = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT plan,vigencia,precio,moneda from gnupanel_reseller_planes WHERE id_dueno=$id_admin ORDER BY plan";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
	$retorno = pg_fetch_all($res_consulta);
	}

$result = array();

if(is_array($retorno))
{
foreach($retorno as $devolver)
	{
	$consulta = "SELECT moneda from gnupanel_monedas WHERE id_moneda = ".$devolver['moneda'];
	$res_consulta = pg_query($conexion,$consulta);
	$moneda = pg_fetch_result($res_consulta,0,0);
	$result[] = $devolver['plan'].";".$devolver['vigencia'].";".$devolver['precio'].";".$moneda;
	}
}

if(is_array($result))
{
	foreach($result as $devolver)
	{
	print "$devolver <br> \n";
	}
}

pg_close($conexion);
return $result;
}

function dame_planes_con_vigencia_usuario()
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $id_reseller = $_SESSION['id_reseller'];
    $retorno = NULL;
    $result = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT plan,vigencia,precio,moneda from gnupanel_usuarios_planes WHERE id_dueno=$id_reseller ORDER BY plan";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
	$retorno = pg_fetch_all($res_consulta);
	}

$result = array();

if(is_array($retorno))
{
foreach($retorno as $devolver)
	{
	$consulta = "SELECT moneda from gnupanel_monedas WHERE id_moneda = ".$devolver['moneda'];
	$res_consulta = pg_query($conexion,$consulta);
	$moneda = pg_fetch_result($res_consulta,0,0);
	$result[] = $devolver['plan'].";".$devolver['vigencia'].";".$devolver['precio'].";".$moneda;
	}
}
pg_close($conexion);
return $result;
}

function dame_cant_max_result_reseller($id_reseller)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT cant_max_result FROM gnupanel_reseller_sets WHERE id_reseller = $id_reseller ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_result ($res_consulta,0,0);
    pg_free_result($res_consulta);
    pg_close($conexion);
    settype($retorno,'integer');
    return $retorno;
    }

function dame_cant_max_result_usuario($id_usuario)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT cant_max_result FROM gnupanel_usuario_sets WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_result ($res_consulta,0,0);
    pg_free_result($res_consulta);
    pg_close($conexion);
    settype($retorno,'integer');
    return $retorno;
    }

function dame_tiempo_max_sesion_reseller($id_reseller)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT tiempo_max_sesion FROM gnupanel_reseller_sets WHERE id_reseller = $id_reseller ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_result ($res_consulta,0,0);
    pg_free_result($res_consulta);
    pg_close($conexion);
    settype($retorno,'integer');
    return $retorno;
    }

function dame_tiempo_max_sesion_usuario($id_usuario)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT tiempo_max_sesion FROM gnupanel_usuario_sets WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_result ($res_consulta,0,0);
    pg_free_result($res_consulta);
    pg_close($conexion);
    settype($retorno,'integer');
    return $retorno;
    }

function dame_politica_susp_admin($id_admin)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT politica_de_suspencion FROM gnupanel_admin_sets WHERE id_admin = $id_admin ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_result ($res_consulta,0,0);
    pg_free_result($res_consulta);
    pg_close($conexion);
    settype($retorno,'integer');
    return $retorno;
    }

function dame_politica_susp_reseller($id_reseller)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT politica_de_suspencion FROM gnupanel_reseller_sets WHERE id_reseller = $id_reseller ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_result ($res_consulta,0,0);
    pg_free_result($res_consulta);
    pg_close($conexion);
    settype($retorno,'integer');
    return $retorno;
    }

function dame_paises()
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $result = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT iso,descripcion FROM paises ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_all ($res_consulta);


    if(is_array($retorno))
    {
    foreach($retorno as $arreglo)
	{
	$result[$arreglo['iso']] = $arreglo['descripcion'];
	}
    }

    pg_free_result($res_consulta);
    pg_close($conexion);
    return $result;
    }

function dame_descripcion_pais($pais)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $result = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT descripcion FROM paises WHERE iso = '$pais' ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_assoc ($res_consulta,0);
    $result = $retorno['descripcion'];
    pg_free_result($res_consulta);
    pg_close($conexion);
    return $result;
    }

function generar_link_con_gets($enlace)
    {
	global $_GET;
	$link_fin = "";

	if(is_array($_GET))
	{
	foreach($_GET as $llave => $valor)
	{
		//if($llave!="desloguear") $link_fin = $link_fin.$llave."&#061;".$valor."&#038;";
		if($llave!="desloguear") $link_fin = $link_fin.$llave."=".$valor."&";
	}
	}

	$retorno = trim($link_fin);
	$retorno = rtrim($retorno,"&");
	if(strlen($retorno)>0)
		{
		$retorno = $enlace."?".$retorno;
		}
	else
		{
		$retorno = $enlace;
		}

	return $retorno;
    }

function gnupanel_crypt($cadena,$semilla=NULL)
    {
	$retorno = NULL;
	$salt = "";
	if($semilla)
		{
		$retorno = crypt($cadena,$semilla);
		}
	else
		{
		$salt = substr(md5(uniqid(rand(),true)),0,8);
		$salt = "$1$".$salt."$";
		$retorno = crypt($cadena,$salt);
		}
	return $retorno;
    }

function verifica_version_smf()
{
$result = NULL;
$resultado = NULL;
$archivo_verificador = "http://install.gnupanel.org/smf/VERSION";
$archivo = file_get_contents($archivo_verificador);
$version_smf = trim($archivo);
if($version_smf == "1.1.4") $result = true;
return $result;
}

function dame_version()
{
	$result = NULL;
	$archivo_version = "../VERSION";
	$result = file_get_contents($archivo_version);
	$result = trim($result," \n");
	return $result;
}

function estoy_habilitado()
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

    $id_usuario = $_SESSION['id_usuario'];
    $logueado = $_SESSION['logueado'];

    $retorno = NULL;
    $result = NULL;

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");

    if($logueado==1)
	{
		$consulta = "SELECT active from gnupanel_usuario WHERE id_usuario = $id_usuario " ;
		$res_consulta = pg_query($conexion,$consulta);
		$retorno = pg_fetch_result($res_consulta,0,0);
		if($retorno == 1)
		{
			$result = true;
		}
	}

    pg_close($conexion);

    return $result;
}

?>