#!/usr/bin/perl

#############################################################################################################
#
#GNUPanel es un programa para el control de hospedaje WEB 
#Copyright (C) 2006  Ricardo Marcelo Alvarez rmalvarezkai@gmail.com
#
#------------------------------------------------------------------------------------------------------------
#
#Este archivo es parte de GNUPanel.
#
#	GNUPanel es Software Libre; Usted puede redistribuirlo y/o modificarlo
#	bajo los términos de la GNU Licencia Pública General (GPL) tal y como ha sido
#	públicada por la Free Software Foundation; o bien la versión 2 de la Licencia,
#	o (a su opción) cualquier versión posterior.
#
#	GNUPanel se distribuye con la esperanza de que sea útil, pero SIN NINGUNA
#	GARANTÍA; tampoco las implícitas garantías de MERCANTILIDAD o ADECUACIÓN A UN
#	PROPÓSITO PARTICULAR. Consulte la GNU General Public License (GPL) para más
#	detalles.
#
#	Usted debe recibir una copia de la GNU General Public License (GPL)
#	junto con GNUPanel; si no, escriba a la Free Software Foundation Inc.
#	51 Franklin Street, 5º Piso, Boston, MA 02110-1301, USA.
#
#------------------------------------------------------------------------------------------------------------
#
#This file is part of GNUPanel.
#
#	GNUPanel is free software; you can redistribute it and/or modify
#	it under the terms of the GNU General Public License as published by
#	the Free Software Foundation; either version 2 of the License, or
#	(at your option) any later version.
#
#	GNUPanel is distributed in the hope that it will be useful,
#	but WITHOUT ANY WARRANTY; without even the implied warranty of
#	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#	GNU General Public License for more details.
#
#	You should have received a copy of the GNU General Public License
#	along with GNUPanel; if not, write to the Free Software
#	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
#
#------------------------------------------------------------------------------------------------------------
#
#############################################################################################################

use Pg;

sub trim 
{
my($string)=@_;
for ($string)
    {
    s/^\s+//;
    s/\s+$//;
    }
return $string;
}

sub encola_autorespuesta
{
    my $conexion = $_[0];
    my $origen = $_[1];
    my $destino = $_[2];
    my @fila;
    my $sql = "INSERT INTO gnupanel_postfix_autoreply_cola(address,destino) VALUES ('$origen','$destino') ";
    $result = $conexion->exec($sql);
}

###############################################################################################################################################################
## Put ourselves in background if not in debug mode. 
require "/etc/gnupanel/gnupanel.conf.pl";

my $nombre = $0;
my $origen = $ARGV[0];
my $destino = $ARGV[1];
$nombre = substr($nombre,rindex($nombre,"/")+1,length($nombre)-1);

my $logueo = "/var/log/".$nombre.".log";
my $conexion = NULL;

#Inicio del programa

$conexion = NULL;
$conexion = Pg::connectdb("dbname=$database host=localhost user=$userdb password=$pasaportedb");
	    
my $estado = $conexion->status;

if($estado == PGRES_CONNECTION_OK)
    {
    if($origen ne $destino)
	{
	encola_autorespuesta($conexion,$origen,$destino);
	}
    }
else
    {
    my $errormensaje = $conexion->errorMessage;
    }

#fin del programa

open (SALIDA,">/tmp/otrasalida.txt")
print SALIDA "ENTRE: \n";
close SALIDA;

print "ENTRE:\n";    
$end = 0;

###############################################################################################################################################################





    





