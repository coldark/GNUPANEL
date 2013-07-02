<?php
/***********************************************************************************************************

GNUPanel es un programa para el control de hospedaje WEB 
Copyright (C) 2006  Ricardo Marcelo Alvarez rmalvarezkai@gmail.com

------------------------------------------------------------------------------------------------------------

Este archivo es parte de GNUPanel.

	GNUPanel es Software Libre; Usted puede redistribuirlo y/o modificarlo
	bajo los t�rminos de la GNU Licencia P�blica General (GPL) tal y como ha sido
	p�blicada por la Free Software Foundation; o bien la versi�n 2 de la Licencia,
	o (a su opci�n) cualquier versi�n posterior.

	GNUPanel se distribuye con la esperanza de que sea �til, pero SIN NINGUNA
	GARANT�A; tampoco las impl�citas garant�as de MERCANTILIDAD o ADECUACI�N A UN
	PROP�SITO PARTICULAR. Consulte la GNU General Public License (GPL) para m�s
	detalles.

	Usted debe recibir una copia de la GNU General Public License (GPL)
	junto con GNUPanel; si no, escriba a la Free Software Foundation Inc.
	51 Franklin Street, 5� Piso, Boston, MA 02110-1301, USA.

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


if(is_array($plugins))
{
foreach($plugins as $llave => $valor)
{

	$icono = "";
	if(strlen(dame_icono($llave))>0) $icono = "<td valign=\"middle\" height=\"100%\" ><a href=\"$nombre_script&#063;seccion&#061;".$llave."\" >".dame_icono($llave)."</a></td>";
	$escritura = "<td valign=\"middle\" height=\"100%\" ><a href=\"$nombre_script&#063;seccion&#061;".$llave."\" >".$plugins_lang[$llave][$llave]."</a></td>";
	print "<h1><table height=\"100%\" ><tr height=\"100%\" >$icono $escritura</h1></tr></table> \n";

	if($seccion) $incluye_seccion = $seccion;
	if($llave == $seccion)
	{
		if(is_array($plugins[$llave]))
		{
		foreach($plugins[$llave] as $llavero => $valore)
		{
		$escritura = $plugins_lang[$llave][$llavero];

		if($valore=="agregar-usuario-apache" || $valore=="listar-usuario-apache" || $valore=="cambia-usuario-apache" || $valore=="borrar-usuario-apache" )
			{
				if($plugin=="agregar_usuario_apache" || $plugin=="listar_usuario_apache" || $plugin=="cambia_usuario_apache" || $plugin=="borrar_usuario_apache" || $plugin=="agregar_dir_prot" )
					{
					print "<h3><a href=\"$nombre_script&#063;seccion&#061;".$llave."&#038;plugin&#061;".$llavero."\" >$escritura</a></h3>\n";
					if($plugin) $incluye_plugin = $plugin;
					}
			}
		else
			{
			print "<h2><a href=\"$nombre_script&#063;seccion&#061;".$llave."&#038;plugin&#061;".$llavero."\" >$escritura</a></h2>\n";
			if($plugin) $incluye_plugin = $plugin;
			}

		}
		}
		
	}
}
}
?>