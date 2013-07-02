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




function inicia_logueo($procesador,$mensaje)
{
    global $idioma;
    global $escribir;
    $procesador_get = generar_link_con_gets($procesador);

    print "<div id=\"formulario\" > \n";
    print "<ins> \n";
    print "<br/> \n";
    print "<form method=\"post\" action=\"$procesador_get\" > \n";
    print "<table> \n";
    print "<tr> \n";
    print "<td> \n";
    print $escribir['ing_user'];
    print "</td> \n";
    print "<td> \n";
    print "<input id=\"id_admin_user\" name=\"admin_user\" size=\"20\" > \n";
    print "</td> \n";
    print "</tr> \n";
    print "<tr> \n";
    print "<td> \n";
    print $escribir['ing_contr'];
    print "</td> \n";
    print "<td> \n";
    print "<input id=\"id_admin_pasaporte\" type=\"password\" name=\"admin_pasaporte\" size=\"20\" > \n";
    print "</td> \n";
    print "</tr> \n";
    print "<tr> \n";
    print "<td> \n";
    print "</td> \n";
    print "<td> \n";
    print "<input id=\"id_ingresando\" type=\"hidden\" name=\"ingresando\" value=\"1\" > \n";
    print "<input type=\"submit\" name=\"Ingresar\" value=\"".$escribir['ingresar']."\" > \n";
    print "<input type=\"reset\" name=\"Borrar\" value=\"".$escribir['borrar']."\" > \n";
    print "</td> \n";
    print "</tr> \n";
    print "</table> \n";
    print "</form> \n";
    print "</ins> \n";
    if($mensaje) print "$mensaje <br/> \n";
    print "</div> \n";
    print "<div id=\"botones\" > \n";
    print "</div> \n";
    print "<div id=\"ayuda\" > \n";
    $escriba = $escribir['help'];
    print "$escriba\n";
    print "</div> \n";
}

function procesa_logueo($procesador)
{
    global $escribir;
    global $_SESSION;
    global $_POST;
    $procesador_get = generar_link_con_gets($procesador);

    $verifica = "WARN";
    if(cadena_valida($_POST['admin_user']) && cadena_valida($_POST['admin_pasaporte']))
    {
	$verifica = checkea_pasaporte_admin(pg_escape_string($_POST['admin_user']),pg_escape_string($_POST['admin_pasaporte']));
    }
    else
    {
	$verifica = "2";
    }

    if($verifica == "1")
    {
	$_SESSION['id_admin'] = dame_id_admin(pg_escape_string($_POST['admin_user']));
	$_SESSION['logueado'] = "1";
	require("login.php");
    }
    else
    {
	sleep(4);
	switch ($verifica)
	{
	    case "0":
	    inicia_logueo($procesador,$escribir['mal_contr']." ");
	    break;
	    case "2":
	    inicia_logueo($procesador,$escribir['carac_inv']." ");
	    break;
	    default:
	    inicia_logueo($procesador,$escribir['user_no_existe']." ");
	}    
    }
}

function terminar_sesion()
{
    global $_SESSION;
    $_SESSION = array();
    session_destroy();
}

function login_init($nombre_script)
{
	global $_POST;
	if($_POST['ingresando']=="1")
	{
		procesa_logueo($nombre_script);
	}
	else
	{
		inicia_logueo($nombre_script,NULL);
	}
}


?>
