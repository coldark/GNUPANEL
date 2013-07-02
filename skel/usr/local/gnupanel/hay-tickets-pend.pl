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

sub dame_mensaje_admin
    {
    my $conexion = $_[0];
    my $logueo = $_[1];
    my $id_admin = $_[2];
    my $mensaje_txt = $_[3];
    my @result;
    my $comando = "";
    
    my $sql = "SELECT idioma FROM gnupanel_admin_lang WHERE id_admin = $id_admin ";
    my $result = $conexion->exec($sql);
    my $idioma = $result->getvalue(0,0);
    
    my $archivo_txt = "/usr/local/gnupanel/lang/".$idioma."/".$mensaje_txt;
    
    open(LECTURA,$archivo_txt);
    
    $result[0] = <LECTURA>;
    $result[1] = "";
    
    while(eof(LECTURA)==0)
	{
	my $renglon = <LECTURA>;
	$result[1] = $result[1].$renglon;
	}

    close(LECTURA);
    
    @result = @result;    
    }

sub dame_mensaje_reseller
    {
    my $conexion = $_[0];
    my $logueo = $_[1];
    my $id_reseller = $_[2];
    my $mensaje_txt = $_[3];
    my @result;
    my $comando = "";
    
    my $sql = "SELECT idioma FROM gnupanel_reseller_lang WHERE id_reseller = $id_reseller ";
    my $result = $conexion->exec($sql);
    my $idioma = $result->getvalue(0,0);
    
    my $archivo_txt = "/usr/local/gnupanel/lang/".$idioma."/".$mensaje_txt;
    
    open(LECTURA,$archivo_txt);
    
    $result[0] = <LECTURA>;
    $result[1] = "";
    
    while(eof(LECTURA)==0)
	{
	my $renglon = <LECTURA>;
	$result[1] = $result[1].$renglon;
	}

    close(LECTURA);
    
    @result = @result;    
    }

sub dame_mensaje_usuario
    {
    my $conexion = $_[0];
    my $logueo = $_[1];
    my $id_usuario = $_[2];
    my $mensaje_txt = $_[3];
    my @result;
    my $comando = "";
    
    my $sql = "SELECT idioma FROM gnupanel_usuario_lang WHERE id_usuario = $id_usuario ";
    my $result = $conexion->exec($sql);
    my $idioma = $result->getvalue(0,0);
    
    my $archivo_txt = "/usr/local/gnupanel/lang/".$idioma."/".$mensaje_txt;
    
    open(LECTURA,$archivo_txt);
    
    $result[0] = <LECTURA>;
    $result[1] = "";
    
    while(eof(LECTURA)==0)
	{
	my $renglon = <LECTURA>;
	$result[1] = $result[1].$renglon;
	}

    close(LECTURA);
    
    @result = @result;    
    }

sub envia_mensaje_admin
    {
    my $conexion = $_[0];
    my $logueo = $_[1];
    my $id_admin = $_[2];
    my $asunto = "";
    my $mensaje = "";
    my $origen;
    my $destino;
    my $checkeo = 1;    
    my @fila;

    my @correo_e = dame_mensaje_admin($conexion,$logueo,$id_admin,"ticket-pend-admin.txt");
    $asunto = $correo_e[0];
    $mensaje = $correo_e[1];

    open(MENSDIR,">> $logueo");
    my $result = NULL;
    
    my $sql = "SELECT correo FROM gnupanel_admin WHERE id_admin = $id_admin ";
    $result = $conexion->exec($sql);

    if($result->resultStatus == PGRES_TUPLES_OK)
    {
    $destino = $result->getvalue(0,0);
    }
    else
    {
	$mensaje = $conexion->errorMessage;
	print MENSDIR $mensaje;
	$checkeo = 0;
    }

    $sql = "SELECT id_reseller,reseller,dominio FROM gnupanel_reseller ORDER BY id_reseller LIMIT 1";
    $result = $conexion->exec($sql);

    if($result->resultStatus == PGRES_TUPLES_OK)
    {
    $origen = $result->getvalue(0,1)."\@".$result->getvalue(0,2);
    }
    else
    {
	$mensaje = $conexion->errorMessage;
	print MENSDIR $mensaje;
	$checkeo = 0;
    }

    if($checkeo == 1)
    {
    envia_mensaje($origen,$destino,$asunto,$mensaje);
    }

    close MENSDIR;
    }

sub envia_mensaje_reseller
    {
    my $conexion = $_[0];
    my $logueo = $_[1];
    my $id_reseller = $_[2];
    my $asunto = "";
    my $mensaje = "";
    my $origen;
    my $destino;
    my $checkeo = 1;    
    my @fila;

    my @correo_e = dame_mensaje_reseller($conexion,$logueo,$id_reseller,"ticket-pend-reseller.txt");
    $asunto = $correo_e[0];
    $mensaje = $correo_e[1];

    open(MENSDIR,">> $logueo");
    my $result = NULL;
    
    my $sql = "SELECT correo_contacto FROM gnupanel_reseller WHERE id_reseller = $id_reseller AND active = 1 ";
    $result = $conexion->exec($sql);

    if($result->resultStatus == PGRES_TUPLES_OK)
    {
    $destino = $result->getvalue(0,0);
    }
    else
    {
	$mensaje = $conexion->errorMessage;
	print MENSDIR $mensaje;
	$checkeo = 0;
    }

    $sql = "SELECT reseller,dominio FROM gnupanel_reseller WHERE id_reseller = $id_reseller ";
    $result = $conexion->exec($sql);

    if($result->resultStatus == PGRES_TUPLES_OK)
    {
    $origen = $result->getvalue(0,0)."\@".$result->getvalue(0,1);
    }
    else
    {
	$mensaje = $conexion->errorMessage;
	print MENSDIR $mensaje;
	$checkeo = 0;
    }

    if($checkeo == 1)
    {
    envia_mensaje($origen,$destino,$asunto,$mensaje);
    }

    close MENSDIR;
    }

sub hay_tickets_pend_admin
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my @fila;

    open(MENSDIR,">> $logueo");
    my $result = NULL;
    
    my $sql = "SELECT DISTINCT id_cliente_de FROM gnupanel_tickets_reseller WHERE atendido = 0 ";
    $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
    {
    while(@fila = $result->fetchrow)
	{
	my $id_admin = $fila[0];
	envia_mensaje_admin($conexion,$logueo,$id_admin);
	}
    }
    else
    {
	$mensaje = $conexion->errorMessage;
	print MENSDIR $mensaje;
    }
    close MENSDIR;
}

sub hay_tickets_pend_reseller
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my @fila;

    open(MENSDIR,">> $logueo");
    my $result = NULL;
    
    my $sql = "SELECT DISTINCT id_cliente_de FROM gnupanel_tickets_usuarios WHERE atendido = 0 ";
    $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
    {
    while(@fila = $result->fetchrow)
	{
	my $id_reseller = $fila[0];
	envia_mensaje_reseller($conexion,$logueo,$id_reseller);
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
$conexion = NULL;
$reloaded = 0;

##open(STDERR, ">> $logueo");

#Inicio del programa
##	    open(STDOUT, ">> $logueo");

	    $conexion = NULL;
	    $conexion = Pg::connectdb("dbname=$database host=localhost user=$userdb password=$pasaportedb");
	    
	    $estado = $conexion->status;

	    if($estado==PGRES_CONNECTION_OK)
		{
		hay_tickets_pend_admin($conexion,$logueo);
		hay_tickets_pend_reseller($conexion,$logueo);
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




