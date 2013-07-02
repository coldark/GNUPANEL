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

    sub senal_de_fin
	{
	    my $signal = shift;
	    $SIG{$signal} = \&senal_de_fin;
	    close MENSAGES;
	    exit(0);
	}

    sub senal_de_kill
	{
	    my $signal = shift;
	    $SIG{$signal} = \&senal_de_kill;
	    close MENSAGES;
	    exit(0);
	}

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

sub formato_numero
    {
    my $numero = $_[0];
    my $result = "";

    if($numero < 10)
	{
	$result = "0000".trim($numero);
	}

    if($numero >= 10 && $numero < 100)
	{
	$result = "000".trim($numero);
	}

    if($numero >= 100 && $numero < 1000)
	{
	$result = "00".trim($numero);
	}

    if($numero >= 1000 && $numero < 10000)
	{
	$result = "0".trim($numero);
	}

    if($numero >= 10000)
	{
	$result = trim($numero);
	}
    
    $result = "".$result;
    }


sub hay_algo_que_configurar
    {
    my $conexion = $_[0];
    my $sql = "SELECT configurar FROM apache_dominios_conf WHERE id = 2 AND configurar = 1";
    my $result = $conexion->exec($sql);
    my $retorno = 0;

    if($result->ntuples > 0)
	{
	$retorno = 1;
	$sql = "UPDATE apache_dominios_conf SET configurar = 0 WHERE id = 2 ";
	$result = $conexion->exec($sql);
	}
    $retorno = $retorno;
    }

sub desconfigura_mailman
    {
    my $nombre_lista = $_[0];
    my $conexion = $_[1];

    open(MENSDIR,">> $logueo");
    my $comando = "/usr/sbin/rmlist -a ";
    my $comandar = "";
    my $checkeo = NULL;
    $comandar = $comando.$nombre_lista;
    system($comandar);
    close MENSDIR;
    }

sub configura_mailman
    {
    my $nombre_lista = $_[0];
    my $dominio = $_[1];
    my $administrador = $_[2];
    my $pasaporte = $_[3];
    my $dominio_reseller = $_[4];
    open(MENSDIR,">> $logueo");
    my $comando = "/usr/sbin/newlist -q -l en ";
    $comandar = $comando."-u gnupanel.".$dominio_reseller."/lists -e ".$dominio." ".$nombre_lista." ".$administrador." ".$pasaporte;
    system($comandar);
    
    $comando = "/usr/lib/mailman/bin/change_pw  ";
    $comandar = $comando."-l ".$nombre_lista." -p ".$pasaporte;
    system($comandar);
    
    close MENSDIR;
    }

sub dame_dominio_reseller
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my $dominio = $_[2];

    open(MENSDIR,">> $logueo");
    my $result = NULL;
    my $dominio_reseller;
    my $sql = "SELECT dominio FROM gnupanel_reseller WHERE id_reseller = (SELECT gnupanel_usuario.cliente_de FROM gnupanel_usuario WHERE gnupanel_usuario.dominio = '$dominio') ";
    $result = $conexion->exec($sql);
    $dominio_reseller = $result->getvalue(0,0);
    close MENSDIR;
    $dominio_reseller = $dominio_reseller;
}

sub cuales_hay_que_configurar
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my @fila;

    open(MENSDIR,">> $logueo");
    my $result = NULL;
    
    my $sql = "SELECT * FROM gnupanel_postfix_listas WHERE estado <> 9";
    $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
    {
    while(@fila = $result->fetchrow)
	{
	my $nombre_lista = $fila[1];
	my $correo_admin_lista = $fila[2];
	my $password = $fila[3];
	my $dominio = $fila[5];
	my $dominio_reseller = dame_dominio_reseller($conexion,$logueo,$dominio);
	configura_mailman($nombre_lista,$dominio,$correo_admin_lista,$password,$dominio_reseller);
	$sql = "UPDATE gnupanel_postfix_listas SET estado = 9, password = '*' WHERE nombre_lista = '$nombre_lista' ";
	my $result_0 = $conexion->exec($sql);
	}
    }
    else
    {
	$mensaje = $conexion->errorMessage;
	print MENSDIR $mensaje;
    }
    close MENSDIR;
}

sub cuales_hay_que_desconfigurar
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my @fila;
    my @fila_0;
    open(MENSDIR,">> $logueo");
    my $result = NULL;
    my $result_0 = NULL;
    
    my $sql = "SELECT * FROM gnupanel_postfix_listas_remover";
    $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
    {
    while(@fila = $result->fetchrow)
	{
	my $nombre_lista = $fila[1];
	$sql = "SELECT id_lista FROM gnupanel_postfix_listas WHERE nombre_lista = '$nombre_lista' ";
	$result_0 = $conexion->exec($sql);
	if($result_0->resultStatus == PGRES_TUPLES_OK)
	    {
    
	    if($result_0->ntuples==0)
	    {
	    desconfigura_mailman($nombre_lista);
	    }

	    $sql = "DELETE FROM gnupanel_postfix_listas_remover WHERE nombre_lista = '$nombre_lista' ";
	    $result_0 = $conexion->exec($sql);
	    }
	}
    }
    else
    {
	$mensaje = $conexion->errorMessage;
	print MENSDIR $mensaje;
    }

    close MENSDIR;
}

#######################################################################################################################
## Put ourselves in background if not in debug mode. 
require "/etc/gnupanel/gnupanel.conf.pl";

$nombre = $0;
$nombre = substr($nombre,rindex($nombre,"/")+1,length($nombre)-1);

$logueo = "/var/log/".$nombre.".log";
$pidfile = "/var/run/".$nombre.".pid";
$conexion = NULL;
$reloaded = 0;

open(STDERR, ">> $logueo");

$SIG{'TERM'} = \&senal_de_fin;
$SIG{'KILL'} = \&senal_de_kill;

    if (open(TTY, "/dev/tty"))
	{
	    ioctl(TTY, $TIOCNOTTY, 0);
	    close(TTY);
	}

    $definida = setpgrp(0, 0);

    $proceso = getppid;

    $child_pid = fork;

    if ( $child_pid != 0)
	{
	    print "Inicializando el demonio de configuracion de mailman \n";
	    exit(0);
	}
    else
	{

#Inicio del programa
	    open(STDOUT, ">> $logueo");
	    open(PIDO,"> $pidfile");
	    print PIDO "$$ \n";
	    close PIDO;

	    $conexion = NULL;
	    $conexion = Pg::connectdb("dbname=$database host=localhost user=$userdb password=$pasaportedb");
	    
	    while(1)
		{
		$estado = $conexion->status;

		if($estado != PGRES_CONNECTION_OK)
		    {
		    $conexion->reset;
		    }

		while(hay_algo_que_configurar($conexion)==0)
		    {
		    sleep($tiempo_dir);
		    }

		if($estado==PGRES_CONNECTION_OK)
		    {
		    cuales_hay_que_configurar($conexion,$logueo);
		    cuales_hay_que_desconfigurar($conexion,$logueo);
		    }
		else
		    {
		    $mensaje = $conexion->errorMessage;
		    open(MENSAGES,">> $logueo");
		    print MENSAGES $mensaje;
		    close MENSAGES;
		    $conexion->reset;
		    }
		}

#fin del programa
	}
    
##$end = 0;

###############################################################################################################################################################




