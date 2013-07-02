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

$sessionLifetime = get_cfg_var("session.gc_maxlifetime");
$conexion_sesion = NULL;

function pgsql_session_open ($save_path, $session_name)
{
	global $servidor_db;
	global $puerto_db;
	global $database;
	global $usuario_db;
	global $passwd_db;
	global $conexion_sesion;
	$conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
	$conexion_sesion = pg_pconnect($conectar) OR die("No es posible conectarse con la base de datos");
	pgsql_session_gc(NULL);
	return $conexion_sesion;
} 

function pgsql_session_close()
{ 
	global $conexion_sesion;
	pgsql_session_gc(NULL);
	return pg_close($conexion_sesion);
}

function pgsql_session_read ($SessionID_in)
{
	global $SessionTableName; 
	global $conexion_sesion;
	global $sessionLifetime;
	global $tiempo_max_sesion;
	pgsql_session_gc(NULL);

        $SessionID = addslashes($SessionID_in);
        $session_data = pg_query($conexion_sesion,"SELECT * FROM $SessionTableName WHERE id_sesion = '$SessionID'") or die("No es posible conectarse con la base de datos");
        if (pg_num_rows($session_data) > 0)
	{
        	$row = pg_fetch_assoc($session_data);
		pg_free_result($session_data);
		if(time()>($row['last_active']+$tiempo_max_sesion))
		{
			return "";
		}
		else
		{
			return stripslashes($row['data']);
		}
        }
	else
	{
	    pg_free_result($session_data);
	    pgsql_session_write($SessionID_in,"");
	    return "";
        }

}

function pgsql_session_write ($SessionID, $val)
{ 
        global $SessionTableName; 
	global $conexion_sesion;
	global $sessionLifetime;
	pgsql_session_gc(NULL);

        $SessionID = addslashes($SessionID); 
        $val = addslashes($val); 

	$consulta = pg_query($conexion_sesion,"select * from $SessionTableName where id_sesion = '$SessionID'") or die ("No es posible conectarse con la base de datos");
	$row = pg_num_rows($consulta);
	$tiempo = time();
	pg_free_result($consulta);

	if($row==0)
	{
		$consulta = pg_query($conexion_sesion,"insert into $SessionTableName (id_sesion,last_active,data) values('$SessionID',$tiempo,'$val')") or die ("No es posible conectarse con la base de datos");
	}
	else
	{
		$consulta = pg_query($conexion_sesion,"update $SessionTableName set data = '$val', last_active = $tiempo where id_sesion = '$SessionID'") or die ("No es posible conectarse con la base de datos");
	}

	$result = ($consulta !== false);
	pg_free_result($consulta);

	return $result;
}

function pgsql_session_destroy ($SessionID) 
{
        global $SessionTableName;
	global $conexion_sesion;

        $SessionID = addslashes($SessionID);

	$consulta = pg_query($conexion_sesion,"delete from $SessionTableName where id_sesion = '$SessionID'") or die ("No es posible conectarse con la base de datos");
	$result = ($consulta !== false);
	pg_free_result($consulta);
	pgsql_session_gc (NULL);
	return $result;
}

function pgsql_session_gc ($entrada) 
{ 
        global $SessionTableName; 
	global $conexion_sesion;
	global $tiempo_max_sesion;

	$tiempo = time();
        $consulta = pg_query($conexion_sesion,"DELETE FROM $SessionTableName WHERE ($tiempo - last_active) > $tiempo_max_sesion") or die("No es posible conectarse con la base de datos");
	$result = ($consulta !== false);
	pg_free_result($consulta);
        return $result;
}

session_set_save_handler ('pgsql_session_open','pgsql_session_close','pgsql_session_read','pgsql_session_write','pgsql_session_destroy','pgsql_session_gc');
register_shutdown_function('session_write_close');


?>