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

sub existe_registro_transferencias
{
my $conexion = $_[0];
my $ano = $_[1];
my $mes = $_[2];
my $id_dominio = $_[3];

my $existe = 0;
my $sql = "SELECT * FROM gnupanel_transferencias_historico WHERE ano = $ano AND mes = $mes AND id_dominio = $id_dominio ";
my $result = $conexion->exec($sql);

if($result->ntuples > 0)
    {
    $existe = 1;
    }

$existe = $existe;
}

sub existe_registro_espacio
{
my $conexion = $_[0];
my $ano = $_[1];
my $mes = $_[2];
my $id_dominio = $_[3];
my $existe = 0;
my $sql = "SELECT * FROM gnupanel_espacio_historico WHERE ano = $ano AND mes = $mes AND id_dominio = $id_dominio ";
my $result = $conexion->exec($sql);

if($result->ntuples > 0)
    {
    $existe = 1;
    }

$existe = $existe;
}


sub resetea_transferencia
{
    my $conexion = $_[0];
    my $logueo = $_[1];

    my $result_0 = 0;
    my $control = 0;

    $result_0 = $conexion->exec("BEGIN");

    if(PGRES_COMMAND_OK == $result_0->resultStatus)
	{
	}
    else
	{
	$control = $control + 1;
	}    

    my $mes = `/bin/date +%m`;
    my $ano = `/bin/date +%Y`;
    my $dia = `/bin/date +%d`;
    
    chop($mes);
    chop($ano);
    chop($dia);

    $mes = int($mes);
    $ano = int($ano);
    $dia = int($dia);
    

    if($dia == 1)
    {    
	if($mes == 1)
	    {
	    $mes = 12;
	    $ano = $ano - 1;
	    }
	else
	    {
	    $mes = $mes - 1;
	    }
    }
        
    my $sql = "SELECT * FROM gnupanel_transferencias ORDER BY id_dominio ";
    my $result = $conexion->exec($sql);

    if(PGRES_TUPLES_OK == $result->resultStatus)
	{
	my @fila;
	while(@fila = $result->fetchrow)
	    {
	    my $id_dominio = $fila[0];
	    my $dominio = $fila[1];
	    my $dueno = $fila[2];
	    my $http = $fila[3];
	    my $ftp = $fila[4];
	    my $smtp = $fila[5];
	    my $pop3 = $fila[6];
	    my $total = $fila[7];
	    my $tope = $fila[8];
	    
	    my $existe = existe_registro_transferencias($conexion,$ano,$mes,$id_dominio);
	    
	    if($existe == 1)
		{
		my $sql_0 = "UPDATE gnupanel_transferencias_historico SET fecha=now(), dueno=$dueno, http = http + $http, ftp = ftp + $ftp, smtp = smtp + $smtp, pop3 = pop3 + $pop3, total = http + ftp + smtp + pop3, tope = $tope WHERE ano = $ano AND mes = $mes AND id_dominio = $id_dominio";
		my $result_0 = $conexion->exec($sql_0);
		if(PGRES_COMMAND_OK == $result_0->resultStatus)
		    {
		    }
		else
		    {
		    $control = $control + 1;
		    }    
		}
	    else
		{
		my $sql_0 = "INSERT INTO gnupanel_transferencias_historico (ano,mes,fecha,id_dominio,dominio,dueno,http,ftp,smtp,pop3,total,tope) VALUES ($ano,$mes,now(),$id_dominio,'$dominio',$dueno,$http,$ftp,$smtp,$pop3,$total,$tope) ";
		my $result_0 = $conexion->exec($sql_0);
		
		if(PGRES_COMMAND_OK == $result_0->resultStatus)
		    {
		    }
		else
		    {
		    $control = $control + 1;
		    }    
		}
	    }	
	}
    else
	{
	$control = $control + 1;
	}    

    if($dia == 1)
    {
    $sql = "UPDATE gnupanel_transferencias SET http = 0, ftp = 0, smtp = 0, pop3 = 0, total = 0 ";
    $result = $conexion->exec($sql);

    if(PGRES_COMMAND_OK == $result->resultStatus)
	{
	}
    else
	{
	$control = $control + 1;
	}    

    $sql = "UPDATE gnupanel_proftpd_ftpquotatallies SET bytes_xfer_used = 0 ";
    $result = $conexion->exec($sql);


    if(PGRES_COMMAND_OK == $result->resultStatus)
	{
	}
    else
	{
	$control = $control + 1;
	}    
    }
    
    $sql = "SELECT * FROM gnupanel_espacio ORDER BY id_dominio ";
    $result = $conexion->exec($sql);

    if(PGRES_TUPLES_OK == $result->resultStatus)
	{
	my @fila;
	while(@fila = $result->fetchrow)
	    {
	    my $id_dominio = $fila[0];
	    my $dominio = $fila[1];
	    my $dueno = $fila[2];
	    my $ftpweb = $fila[3];
	    my $correo = $fila[4];
	    my $postgres = $fila[5];
	    my $mysql = $fila[6];
	    my $total = $fila[7];
	    my $tope = $fila[8];
	    
	    my $existe = existe_registro_espacio($conexion,$ano,$mes,$id_dominio);
	    
	    if($existe == 1)
		{
		my $sql_0 = "UPDATE gnupanel_espacio_historico SET fecha=now(), dueno=$dueno, ftpweb = $ftpweb, correo = $correo, postgres = $postgres, mysql = $mysql, total = ftpweb + correo + postgres + mysql, tope = $tope WHERE ano = $ano AND mes = $mes AND id_dominio = $id_dominio";
		my $result_0 = $conexion->exec($sql_0);
		
		if(PGRES_COMMAND_OK == $result_0->resultStatus)
		    {
		    }
		else
		    {
		    $control = $control + 1;
		    }    
		}
	    else
		{
		my $sql_0 = "INSERT INTO gnupanel_espacio_historico (ano,mes,fecha,id_dominio,dominio,dueno,ftpweb,correo,postgres,mysql,total,tope) VALUES ($ano,$mes,now(),$id_dominio,'$dominio',$dueno,$ftpweb,$correo,$postgres,$mysql,$total,$tope) ";
		my $result_0 = $conexion->exec($sql_0);
		
		if(PGRES_COMMAND_OK == $result_0->resultStatus)
		    {
		    }
		else
		    {
		    $control = $control + 1;
		    }    
		}
	    }	
	}
    else
	{
	$control = $control + 1;
	}    
    
    if($control == 0)
	{
	$result = $conexion->exec("END");
	}
    else
	{
	$result = $conexion->exec("ROLLBACK");
	}

}

###############################################################################################################################################################
## Put ourselves in background if not in debug mode. 
require "/etc/gnupanel/gnupanel.conf.pl";

$nombre = $0;
$nombre = substr($nombre,rindex($nombre,"/")+1,length($nombre)-1);

$logueo = "/var/log/".$nombre.".log";
$conexion = NULL;
$reloaded = 0;

#open(STDERR, ">> $logueo");

#Inicio del programa
#open(STDOUT, ">> $logueo");

$conexion = NULL;
$conexion = Pg::connectdb("dbname=$database host=localhost user=$userdb password=$pasaportedb");
	    
$estado = $conexion->status;

if($estado==PGRES_CONNECTION_OK)
    {
    resetea_transferencia($conexion,$logueo);
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





    

