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

sub analiza_pop3
    {
    my $dominio = $_[0];
    my $transferencia = $_[1];
    my $conexion = $_[2];
    my $logueo = $_[3];
    open(MENSPOP3,">> $logueo");

    my $mensaje;

    my $result = NULL;
    $result = $conexion->exec("BEGIN");
    my $sql = "SELECT dominio FROM gnupanel_transferencias WHERE dominio='$dominio' ";
    my $result = $conexion->exec($sql);
    $result = $conexion->getResult;
    if($result)
    {
	$sql = "UPDATE gnupanel_transferencias SET pop3 = pop3 + $transferencia WHERE dominio = '$dominio' ";
    	$result = $conexion->exec($sql);
	$estado = $result->resultStatus;

	$sql = "UPDATE gnupanel_transferencias SET total = http + ftp + smtp + pop3 WHERE dominio = '$dominio' ";
    	$result = $conexion->exec($sql);
	my $estado = $estado && $result->resultStatus;

	if($estado == PGRES_COMMAND_OK)
	{
    	    $result = $conexion->exec("END");
	}
	else
	{
	    $mensaje = $conexion->errorMessage;
	    print MENSPOP3 $mensaje;
    	    $result = $conexion->exec("ROLLBACK");
	}
    }
    else
    {
	$mensaje = "El dominio $dominio no es local \n";
	print MENSPOP3 $mensaje;
    	$result = $conexion->exec("END");
    }
	
    close MENSPOP3;
    }
    
sub analiza_smtp
    {
    my $dominio = $_[0];
    my $transferencia = $_[1];
    my $conexion = $_[2];
    my $logueo = $_[3];

    open(MENSSMTP,">> $logueo");

    my $mensaje;
    
    my $result = NULL;
    $result = $conexion->exec("BEGIN");
    $result = $conexion->exec("SELECT dominio FROM gnupanel_transferencias WHERE dominio='$dominio'");
    $result = $conexion->getResult;
    if($result)
    {
	my $sql = "UPDATE gnupanel_transferencias SET smtp=smtp+$transferencia WHERE dominio = '$dominio'";
    	$result = $conexion->exec($sql);
	my $estado = $result->resultStatus;

	$sql = "UPDATE gnupanel_transferencias SET total = http + ftp + smtp + pop3 WHERE dominio = '$dominio' ";
    	$result = $conexion->exec($sql);
	$estado = $estado && $result->resultStatus;

	if($estado == PGRES_COMMAND_OK)
	{
    	    $result = $conexion->exec("END");
	}
	else
	{
	    $mensaje = $conexion->errorMessage;
	    print MENSSMTP $mensaje;
    	    $result = $conexion->exec("ROLLBACK");
	}
    }
    else
    {
	$mensaje = "El dominio $dominio no es local \n";
	print MENSSMTP $mensaje;
    	$result = $conexion->exec("END");
    }
	
    close MENSSMTP;	
    }

sub analiza_ftp
    {
    my $dominio = $_[0];
    my $transferencia = $_[1];
    my $conexion = $_[2];
    my $logueo = $_[3];

    open(MENSFTP,">> $logueo");
    my $result = NULL;
    $result = $conexion->exec("BEGIN");
    my $sql = "SELECT dominio FROM gnupanel_transferencias WHERE dominio='$dominio' ";
    $result = $conexion->exec($sql);
    $result = $conexion->getResult;
    my $estado;
    my $mensaje;
    
    if($result)
	{
	$sql = "UPDATE gnupanel_transferencias SET ftp = ftp + $transferencia WHERE dominio = '$dominio' ";
    	$result = $conexion->exec($sql);
	$estado = $result->resultStatus;

	$sql = "UPDATE gnupanel_transferencias SET total = http + ftp + smtp + pop3 WHERE dominio = '$dominio' ";
    	$result = $conexion->exec($sql);
	$estado = $estado && $result->resultStatus;

	if($estado == PGRES_COMMAND_OK)
	    {
    	    $result = $conexion->exec("END");
	    }
	else
	    {
	    $mensaje = $conexion->errorMessage;
	    print MENSFTP $mensaje;
    	    $result = $conexion->exec("ROLLBACK");
	    }
	}
    else
	{
	$mensaje = "No se hace nada \n";
	print MENSFTP $mensaje;
    	$result = $conexion->exec("END");
	}
	
    close MENSFTP;
    }

sub analiza_http
    {
    my $id_dominio = $_[0];
    my $transferencia = $_[1];
    my $conexion = $_[2];
    my $logueo = $_[3];

    open(MENSHTTP,">> $logueo");
    my $result = NULL;

    my $sql = "BEGIN";
    $result = $conexion->exec($sql);
    my $estado;
    my $mensaje;
    
    if($result)
	{
	$sql = "UPDATE gnupanel_transferencias SET http = http + $transferencia WHERE id_dominio = $id_dominio ";
    	$result = $conexion->exec($sql);
	$estado = $result->resultStatus;

	$sql = "UPDATE gnupanel_transferencias SET total = http + ftp + smtp + pop3 WHERE id_dominio = $id_dominio ";
    	$result = $conexion->exec($sql);
	$estado = $estado && $result->resultStatus;

	if($estado == PGRES_COMMAND_OK)
	    {
    	    $result = $conexion->exec("END");
	    }
	else
	    {
	    $mensaje = $conexion->errorMessage;
	    print MENSHTTP $mensaje;
    	    $result = $conexion->exec("ROLLBACK");
	    }
	}
    else
	{
	$mensaje = "No se hace nada \n";
	print MENSHTTP $mensaje;
    	$result = $conexion->exec("END");
	}
	
    close MENSHTTP;
    }
    

#######################################################################################################################
## Put ourselves in background if not in debug mode. 
require "/etc/gnupanel/gnupanel.conf.pl";

$nombre = $0;
$nombre = substr($nombre,rindex($nombre,"/")+1,length($nombre)-1);

$logueo = "/var/log/".$nombre.".log";
$pidfile = "/var/run/".$nombre.".pid";
$conexion = NULL;
$tiempo = trim(time());
$archivo_recolector_http = "/var/lib/gnupanel/".$nombre."_http.".$tiempo.".log";

$archivo_recolector_mail = "/var/lib/gnupanel/".$nombre."_mail.".$tiempo.".log";
$archivo_recolector_ftp = "/var/lib/gnupanel/".$nombre."_ftp.".$tiempo.".log";

open(STDERR, ">> $logueo");

print "Inicializando el medidor de trafico de GNUPanel \n";

$conexion = NULL;
$conexion = Pg::connectdb("dbname=$database host=localhost user=$userdb password=$pasaportedb");
	    
$estado = $conexion->status;

if($estado != PGRES_CONNECTION_OK)
{
    $conexion->reset;
}
		
if($estado==PGRES_CONNECTION_OK)
{

$comando = "/bin/cat ".$archivo_log_http." > ".$archivo_recolector_http;
system($comando);
$comando = "/bin/cat ".$archivo_log_http." >> ".$archivo_log_http.".0";
system($comando);
$comando = "/bin/echo -n \"\" > ".$archivo_log_http;
system($comando);
$comando ="/etc/init.d/apache2 reload";
system($comando);

$comando = "/bin/cat ".$archivo_log_mail." > ".$archivo_recolector_mail;
system($comando);
$comando = "/bin/cat ".$archivo_log_mail." >> ".$archivo_log_mail.".0";
system($comando);
$comando = "/bin/echo -n \"\" > ".$archivo_log_mail;
system($comando);

$comando = "/bin/cat ".$archivo_log_ftp." > ".$archivo_recolector_ftp;
system($comando);
$comando = "/bin/cat ".$archivo_log_ftp." >> ".$archivo_log_ftp.".0";
system($comando);
$comando = "/bin/echo -n \"\" > ".$archivo_log_ftp;
system($comando);
$comando ="/etc/init.d/proftpd restart";
#system($comando);



open(ARCHIVO_HTTP,$archivo_recolector_http);

my %linea_http_out;

while(eof(ARCHIVO_HTTP)==0)
{
    $linea = <ARCHIVO_HTTP>;
    $linea = trim($linea);
    my @datos_in = split(' ',$linea);
    $linea_http_out{trim($datos_in[0])} = $linea_http_out{trim($datos_in[0])} + trim($datos_in[1]);
}
close($archivo_recolector_http);

my @linea_http_key;
my @linea_http_values;

@linea_http_key = keys(%linea_http_out);
@linea_http_values = values(%linea_http_out);

$largo_key = @linea_http_key;
$largo_values = @linea_http_values;

for($i=0;$i<$largo_key;$i++)
{
    analiza_http($linea_http_key[$i],$linea_http_values[$i],$conexion,$logueo);
}

$comando = "/bin/rm -f ".$archivo_recolector_http;
system($comando);

my %linea_pop3_out;
my %linea_smtp_out;

my @linea_pop3_key;
my @linea_pop3_values;

my @linea_smtp_key;
my @linea_smtp_values;

open(ARCHIVO_MAIL,$archivo_recolector_mail);
while(eof(ARCHIVO_MAIL)==0)
{
    $linea = <ARCHIVO_MAIL>;
    $linea = trim($linea);
    $pop3 = index($linea,"pop3");
    $smtp = index($linea,"postfix/qmgr");
    
    if(($smtp>=0)&&(index($linea,"from")>=0)&&(index($linea,"size")>=0))
    {
	my @datos = NULL;
	my @dominios = NULL;
	my @transfer = NULL;
	@datos = split(',',$linea);
	@dominios = split('@',$datos[0]);
	@transfer = split('=',$datos[1]);
	my $domi = trim($dominios[1]);
	chop($domi);
	$domi = lc($domi);
	$linea_smtp_out{$domi} = $linea_smtp_out{$domi} + trim($transfer[1]);
    }

    if($pop3>=0)
    {
	my @datos = NULL;
	my @dominios = NULL;
	my @transfer = NULL;
	@datos = split(',',$linea);
	@dominios = split('@',$datos[1]);
	@transfer = split('=',$datos[4]);
	my $domi = trim($dominios[1]);
	$domi = lc($domi);
	if(length($domi)>0)
	{
	    $linea_pop3_out{$domi} = $linea_pop3_out{$domi} + trim($transfer[1]);
	}
    }

}
close($archivo_recolector_mail);


@linea_pop3_key = keys(%linea_pop3_out);
@linea_pop3_values = values(%linea_pop3_out);


$largo_key = @linea_pop3_key;
$largo_values = @linea_pop3_values;

for($i=0;$i<$largo_key;$i++)
{
    analiza_pop3($linea_pop3_key[$i],$linea_pop3_values[$i],$conexion,$logueo);
}

@linea_smtp_key = keys(%linea_smtp_out);
@linea_smtp_values = values(%linea_smtp_out);


$largo_key = @linea_smtp_key;
$largo_values = @linea_smtp_values;

for($i=0;$i<$largo_key;$i++)
{
    analiza_smtp($linea_smtp_key[$i],$linea_smtp_values[$i],$conexion,$logueo);
}

$comando = "/bin/rm -f ".$archivo_recolector_mail;
system($comando);

open(ARCHIVO_FTP,$archivo_recolector_ftp);

my %linea_ftp_out;

while(eof(ARCHIVO_FTP)==0)
{
    $linea = <ARCHIVO_FTP>;
    $linea = trim($linea);
    
    my @datos = NULL;
    my @dominios = NULL;
    my @transfer = NULL;
    @datos = split(' ',$linea);
    @dominios = split('@',$datos[13]);
    my $dominio = lc(trim($dominios[1]));
    @transfer = split('=',$datos[4]);
    my $transferencia = trim($datos[7]);
    my $ftp = trim($datos[11]);
    if(($ftp eq "i") || ($ftp eq "o"))
    {
	$linea_ftp_out{$dominio} = $linea_ftp_out{$dominio} + $transferencia;
    }
}
close($archivo_recolector_http);

my @linea_ftp_out_key;
my @linea_ftp_out_values;

@linea_ftp_out_key = keys(%linea_ftp_out);
@linea_ftp_out_values = values(%linea_ftp_out);

$largo_key = @linea_ftp_out_key;
$largo_values = @linea_ftp_out_values;

for($i=0;$i<$largo_key;$i++)
{
    analiza_ftp($linea_ftp_out_key[$i],$linea_ftp_out_values[$i],$conexion,$logueo);
}

$comando = "/bin/rm -f ".$archivo_recolector_ftp;
system($comando);

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
    
$end = 0;

###############################################################################################################################################################





