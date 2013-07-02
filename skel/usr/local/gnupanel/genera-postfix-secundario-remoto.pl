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

sub genera_relay_domains
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my $archivo = $_[2];

    my @fila;
    open(MENSDIR,">> $logueo");
    open(ARCHIVO,"> $archivo");
    
    my $result = NULL;
    my $sql = "SELECT dominio,transport FROM gnupanel_postfix_transport ";
    $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
    {
    while(@fila = $result->fetchrow)
	{
	my $dominio = $fila[0];
	my $transport = $fila[1];
	print ARCHIVO "$dominio $transport\n";
	print "$dominio $transport\n";
	}
    }
    else
    {
	$mensaje = $conexion->errorMessage;
	print MENSDIR $mensaje;
	print $mensaje;
    }

    close ARCHIVO;
    close MENSDIR;
}

sub genera_relay_recipients
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my $archivo = $_[2];

    my @fila;
    open(MENSDIR,">> $logueo");
    open(ARCHIVO,"> $archivo");
    
    my $result = NULL;
    my $sql = "SELECT address,goto FROM gnupanel_postfix_virtual ";
    $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
    {
    while(@fila = $result->fetchrow)
	{
	my $direccion = $fila[0];
	my $destino = $fila[1];
	print ARCHIVO "$direccion $destino\n";
	print "$direccion $destino\n";
	}
    }
    else
    {
	$mensaje = $conexion->errorMessage;
	print MENSDIR $mensaje;
	print $mensaje;
    }

    close ARCHIVO;
    close MENSDIR;
}





#######################################################################################################################
## Put ourselves in background if not in debug mode. 
require "/etc/gnupanel/sdns.conf.pl";

$nombre = $0;
$parametro = $ARGV[0];

$nombre = substr($nombre,rindex($nombre,"/")+1,length($nombre)-1);

$logueo = "/var/log/".$nombre.".log";
$conexion = NULL;
$reloaded = 0;

$archivo_domains = $dir_postfix_secundario."relay_domains" ;
$archivo_recipients = $dir_postfix_secundario."relay_recipients" ;

open(STDERR, ">> $logueo");

#Inicio del programa
#open(STDOUT, ">> $logueo");

$conexion = NULL;
$conexion = Pg::connectdb("dbname=$database host=localhost user=$userdb password=$pasaportedb");
	    
$estado = $conexion->status;

if($estado==PGRES_CONNECTION_OK)
    {
	if($parametro eq "domains")
	{
	    genera_relay_domains($conexion,$logueo,$archivo_domains);
	}
	elsif($parametro eq "recipients")
	{
	    genera_relay_recipients($conexion,$logueo,$archivo_recipients);
	}
	else
	{
	    genera_relay_domains($conexion,$logueo,$archivo_domains);
	}
    }
else
    {
    $mensaje = $conexion->errorMessage;
    open(MENSAGES,">> $logueo");
    print MENSAGES $mensaje;
    close MENSAGES;
    $conexion->reset;
    }

#fin del programa
	
    
##$end = 0;

###############################################################################################################################################################





    

