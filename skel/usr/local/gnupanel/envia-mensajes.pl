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
use HTML::Entities;

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


sub hay_mensajes_para_enviar
    {
    my $conexion = $_[0];
    my $sql = "SELECT configurar FROM apache_dominios_conf WHERE (id = 3 OR id = 4) AND configurar = 1";
    my $result = $conexion->exec($sql);
    my $retorno = 0;

    if($result->ntuples > 0)
	{
	$retorno = 1;
	$sql = "UPDATE apache_dominios_conf SET configurar = 0 WHERE id = 3 ";
	$result = $conexion->exec($sql);
	}
    $retorno = $retorno;
    }

sub envia_mensaje
    {
    my $remitente = $_[0];    
    my $destinatario = $_[1];    
    my $asunto = $_[2];    
    my $mensaje = $_[3];    
    decode_entities($asunto);
    decode_entities($mensaje);

    my $replyto = "Reply-To: ".$remitente."\n";
    $remitente = "From: ".$remitente."\n";
    $asunto = "Subject: ".$asunto."\n";
    $destinatario = "To: ".$destinatario."\n";
    my $sender = "|/usr/sbin/sendmail -t";
    open (MAIL,"$sender");
    print MAIL "$remitente";
    print MAIL "$replyto";
    print MAIL "$destinatario";
    print MAIL "$asunto";
    print MAIL "\n";
    print MAIL "$mensaje\n";
    close(MAIL);
    }

sub envia_mensaje_admin
    {
    my $conexion = $_[0];
    my $logueo = $_[1];
    my $id_admin = $_[2];
    my $asunto = $_[3];
    my $mensaje = $_[4];

    my @fila;

    open(MENSDIR,">> $logueo");
    my $result = NULL;
    
    my $sql = "SELECT id_reseller,reseller,dominio,correo_contacto FROM gnupanel_reseller WHERE cliente_de = $id_admin AND active = 1 ORDER BY id_reseller ";
    $result = $conexion->exec($sql);


    if($result->resultStatus == PGRES_TUPLES_OK)
    {
    @fila = $result->fetchrow;
    my $id_reseller = $fila[0];
    my $reseller = $fila[1];
    my $dominio = $fila[2];
    my $correo_admin = $reseller."\@".$dominio;

    while(@fila = $result->fetchrow)
	{
	$id_reseller = $fila[0];
	$destinatario = $fila[3];
	envia_mensaje($correo_admin,$destinatario,$asunto,$mensaje);
	}
    }
    else
    {
	$mensaje = $conexion->errorMessage;
	print MENSDIR $mensaje;
    }
    close MENSDIR;
    }

sub envia_mensaje_reseller
    {
    my $conexion = $_[0];
    my $logueo = $_[1];
    my $id_reseller = $_[2];
    my $asunto = $_[3];
    my $mensaje = $_[4];

    my @fila;

    open(MENSDIR,">> $logueo");
    my $result = NULL;
    
    my $sql = "SELECT reseller,dominio FROM gnupanel_reseller WHERE id_reseller = $id_reseller ";
    $result = $conexion->exec($sql);

    @fila = $result->fetchrow;
    my $reseller = $fila[0];
    my $dominio = $fila[1];
    my $correo_reseller = $reseller."\@".$dominio;

    $sql = "SELECT correo_contacto FROM gnupanel_usuario WHERE cliente_de = $id_reseller AND active = 1 ";
    $result = $conexion->exec($sql);

    if($result->resultStatus == PGRES_TUPLES_OK)
    {

    while(@fila = $result->fetchrow)
	{
	$destinatario = $fila[0];
	envia_mensaje($correo_reseller,$destinatario,$asunto,$mensaje);
	}
    }
    else
    {
	$mensaje = $conexion->errorMessage;
	print MENSDIR $mensaje;
    }
    close MENSDIR;
    }

sub cuales_hay_que_enviar_admin
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my @fila;

    open(MENSDIR,">> $logueo");
    my $result = NULL;
    
    my $sql = "SELECT * FROM gnupanel_informes_admin WHERE enviado = 0 ";
    $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
    {
    while(@fila = $result->fetchrow)
	{
	my $id = $fila[0];
	my $id_admin = $fila[1];
	my $asunto = $fila[3];
	my $informe = $fila[4];
	envia_mensaje_admin($conexion,$logueo,$id_admin,$asunto,$informe);
	$sql = "UPDATE gnupanel_informes_admin SET enviado = 1 WHERE id = $id ";
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

sub cuales_hay_que_enviar_reseller
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my @fila;

    open(MENSDIR,">> $logueo");
    my $result = NULL;
    
    my $sql = "SELECT * FROM gnupanel_informes_reseller WHERE enviado = 0 ";
    $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
    {
    while(@fila = $result->fetchrow)
	{
	my $id = $fila[0];
	my $id_reseller = $fila[1];
	my $asunto = $fila[3];
	my $informe = $fila[4];
	envia_mensaje_reseller($conexion,$logueo,$id_reseller,$asunto,$informe);
	$sql = "UPDATE gnupanel_informes_reseller SET enviado = 1 WHERE id = $id ";
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

sub cuales_hay_que_enviar_autoreply
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my @fila;

    open(MENSDIR,">> $logueo");
    my $result = NULL;
    my $checkeo = 0;
    my $sql = "SELECT * FROM gnupanel_postfix_autoreply_cola ";
    $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
    {
    while(@fila = $result->fetchrow)
	{
	my $id = $fila[0];
	my $origen = $fila[1];
	my $destino = $fila[2];
	$checkeo = envia_mensaje_autoreply($conexion,$origen,$destino);
	if($checkeo == 1)
	    {
	    $sql = "DELETE FROM gnupanel_postfix_autoreply_cola WHERE id = $id ";
	    my $result_0 = $conexion->exec($sql);
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

sub envia_mensaje_autoreply
    {
    my $conexion = $_[0];
    my $origen = $_[1];
    my $destino = $_[2];
    my $asunto = "";
    my $mensaje = "";
    my $checkeo = 0;
    my @fila;

    open(MENSDIR,">> $logueo");
    my $result = NULL;
    
    my $sql = "SELECT subject,mensaje,active FROM gnupanel_postfix_autoreply WHERE address = '$origen' ";
    $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
    {
    @fila = $result->fetchrow;
    $asunto = $fila[0];
    $mensaje = $fila[1];
    if($fila[2] == 1)
	{
	envia_mensaje($origen,$destino,$asunto,$mensaje);
	}
    $checkeo = 1;    
    }
    else
    {
	$mensaje = $conexion->errorMessage;
	print MENSDIR $mensaje;
    }
    close MENSDIR;
    $checkeo = $checkeo;
    }

sub hay_que_restartear_postfix
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my @fila;

    open(MENSDIR,">> $logueo");
    my $result = NULL;
    my $checkeo = 0;
    my $sql = "SELECT configurar FROM apache_dominios_conf WHERE id = 4 ";
    $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
    {
    while(@fila = $result->fetchrow)
	{
	my $configurar = $fila[0];

	if($configurar == 1)
	    {
	    $sql = "UPDATE apache_dominios_conf SET configurar = 0 WHERE id = 4 ";
	    my $result_0 = $conexion->exec($sql);
	    my $comando = "/etc/init.d/postfix reload ";
	    system($comando);	    
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
	    print "Inicializando el demonio de envio de mensajes a clientes \n";
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
	    
		while(hay_mensajes_para_enviar($conexion)==0)
		    {
		    sleep($tiempo_dir);
		    }

		if($estado==PGRES_CONNECTION_OK)
		    {
		    hay_que_restartear_postfix($conexion,$logueo);
		    cuales_hay_que_enviar_admin($conexion,$logueo);
		    cuales_hay_que_enviar_reseller($conexion,$logueo);
		    cuales_hay_que_enviar_autoreply($conexion,$logueo);
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




