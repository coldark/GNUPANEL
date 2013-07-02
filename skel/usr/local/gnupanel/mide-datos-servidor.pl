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
			  


sub mide_uptime
    {
    my $comando = "/bin/cat /proc/uptime | /usr/bin/mawk '{print \$1}'";
    my $dias = 0;
    my $horas = 0;
    my $minutos = 0;
    my $segundos = 0;
    my $uptime;
    my $proc_uptime = `$comando`;
    my @datos = NULL;
    @datos = split("\n",$proc_uptime);
    my $tiempo_enc = int($datos[0]);
    $segundos = $tiempo_enc % 60;
    $minutos = int($tiempo_enc / 60) % 60;
    $horas = int($tiempo_enc / 3600) % 24;
    $dias = int($tiempo_enc / 86400) ;
    $uptime = "$dias $horas:";
    if($minutos<10)
    {
	$uptime = $uptime."0".$minutos.":";
    }
    else
    {
	$uptime = $uptime.$minutos.":";
    }    
    
    if($segundos<10)
    {
	$uptime = $uptime."0".$segundos;
    }
    else
    {
	$uptime = $uptime.$segundos;
    }    
    $uptime = $uptime;
    }

sub datos_procesador
    {
    my $comando = "/bin/cat /proc/cpuinfo | /usr/bin/mawk -F \":\" '{print \$2}'";
    my $result;
    
    my $proc_info = `$comando`;
    my @datos = NULL;
    @datos = split("\n",$proc_info);

    my $result = $datos[1].$datos[4].$datos[6]." Mhz"." Cache".$datos[7];
    $result = trim($result);
    }

sub uso_procesador
    {
    my $comando = "/bin/cat /proc/loadavg";# | /usr/bin/mawk -F \":\" '{print \$2}'";
    my $result;
    my $carga_proc = `$comando`;
    my @datos = NULL;
    @datos = split(" ",$carga_proc);
    $result = (int((($datos[0]+$datos[1]+$datos[2])/3)*100))/100;
    $result = int($result);
    }

sub memoria_total
    {
    my $comando = "/bin/cat /proc/meminfo | /usr/bin/mawk -F \":\" '{print \$2}' | /usr/bin/mawk '{print \$1}'";
    my $result;
    my $memoria_total = `$comando`;
    my @datos = NULL;
    @datos = split("\n",$memoria_total);
    $result = trim($datos[0]);
    $result = int($result/1024);
    }

sub memoria_usada
    {
    my $comando = "/bin/cat /proc/meminfo | /usr/bin/mawk -F \":\" '{print \$2}' | /usr/bin/mawk '{print \$1}'";
    my $result;
    my $memoria_usada = `$comando`;
    my @datos = NULL;
    @datos = split("\n",$memoria_usada);
    $result = trim($datos[0]-$datos[1]);
    $result = int($result/1024);
    }

sub swap_total
    {
    my $comando = "/usr/bin/env -i /bin/cat /proc/meminfo | /bin/grep -i swaptotal | /usr/bin/mawk -F \":\" '{print \$2}' | /usr/bin/mawk '{print \$1}'";
    my $result = 0;
    my $swap_total = `$comando`;
    my @datos = NULL;
    @datos = split("\n",$swap_total);
    
    $comando = "/bin/uname -m";
    my $maquina = `$comando`;
    $maquina = trim($maquina);
    my $i = 0;

    if($maquina eq "x86_64")
    {
	$i = 0;
    }

    $result = trim($datos[$i]);
    $result = int($result/1024);
    }

sub swap_usada
    {
    my $comando = "/usr/bin/env -i /bin/cat /proc/meminfo | /bin/grep -i swapfree | /usr/bin/mawk -F \":\" '{print \$2}' | /usr/bin/mawk '{print \$1}'";
    my $result = 0;
    my $swap_usada = `$comando`;
    my @datos = NULL;
    @datos = split("\n",$swap_usada);

    $comando = "/bin/uname -m";
    my $maquina = `$comando`;
    $maquina = trim($maquina);
    my $i = 0;

    if($maquina eq "x86_64")
    {
	$i = 0;
    }

    $result = trim($datos[$i]);
    $result = swap_total() - int($result/1024);
    $result = int($result);
    }

sub disco_total
    {
    my $comando_0 = "/bin/df -m | /bin/grep -v tmpfs | /bin/grep dev | /usr/bin/mawk '{print \$3}'";
    my $comando_1 = "/bin/df -m | /bin/grep -v tmpfs | /bin/grep dev | /usr/bin/mawk '{print \$4}'";
    my $result;
    $result = 0;
    my $disco_usado = `$comando_0`;
    my $disco_disp = `$comando_1`;
    my @datos_usado = NULL;
    my @datos_disp = NULL;
    @datos_usado = split("\n",$disco_usado);
    @datos_disp = split("\n",$disco_disp);
    my $largo = @datos_usado;
    my $i;
    for($i=0;$i<$largo;$i++)
	{
        $result = $result + $datos_usado[$i] + $datos_disp[$i];
	}
    $result = trim($result);
    }

sub disco_usado
    {
    my $comando = "/bin/df -m | /bin/grep -v tmpfs | /bin/grep dev | /usr/bin/mawk '{print \$3}'";
    my $result;
    $result = 0;
    my $disco_usado = `$comando`;
    my @datos_usado = NULL;
    @datos_usado = split("\n",$disco_usado);
    my $largo = @datos_usado;
    my $i;
    for($i=0;$i<$largo;$i++)
	{
        $result = $result + $datos_usado[$i];
	}
    $result = trim($result);
    }

sub ingresa_en_base
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my $uptime = $_[2];
    my $procesador_data = $_[3];
    my $procesador_uso = $_[4];
    my $memoria_total = $_[5];
    my $memoria_usada = $_[6];
    my $swap_total = $_[7];
    my $swap_usada = $_[8];
    my $disco_total = $_[9];
    my $disco_usado = $_[10];

    open(MENSDIR,">> $logueo");
    my $result = NULL;
    my $sql = "UPDATE gnupanel_server_data SET uptime='$uptime',procesador_data='$procesador_data',procesador_uso=$procesador_uso,memoria_total=$memoria_total,memoria_usada=$memoria_usada,swap_total=$swap_total,swap_usada=$swap_usada,disco_total=$disco_total,disco_usado=$disco_usado WHERE id_servidor=(SELECT id_servidor FROM gnupanel_servidores WHERE servidor='$nombre_servidor') ";
    $result = $conexion->exec($sql);
    my $estado = $result->resultStatus;
    my $mensaje;
    if($estado == PGRES_COMMAND_OK)
    {
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
	    print "Inicializando el medidor de datos del servidor \n";
	    exit(0);
	}
    else
	{

#Inicio del programa
	    open(STDOUT, ">> $logueo");
	    open(PIDO,"> $pidfile");
	    print PIDO "$$ \n";
	    close PIDO;

#	    my ($login,$pass,$uid,$gid) = getpwnam($usuario);
#	    $( = $gid;
#	    $) = $gid;
#	    $< = $uid;
#	    $> = $uid;
#
#	    if (  ((split(/ /,$)))[0] ne $gid) || ((split(/ /,$())[0] ne $gid)  ) 
#		{
#		open(MENSAGES,">> $logueo");
#		print MENSAGES "No se pudo cambiar el GID \n";
#		close MENSAGES;
#		}
# 
#	    if (  ($> ne $uid) || ($< ne $uid)  )
#		{
#		open(MENSAGES,">> $logueo");
#		print MENSAGES "No se pudo cambiar el UID \n";
#		close MENSAGES;
#		}
#
#	    undef($login);
#	    undef($pass);
#	    undef($uid);
#	    undef($gid);

	    $conexion = NULL;
	    $conexion = Pg::connectdb("dbname=$database host=localhost user=$userdb password=$pasaportedb");
	    
	    while(1)
		{
		$estado = $conexion->status;

		if($estado != PGRES_CONNECTION_OK)
		    {
		    $conexion->reset;
		    }
		
		if($estado==PGRES_CONNECTION_OK)
		    {
		    ingresa_en_base($conexion,$logueo,mide_uptime(),datos_procesador(),uso_procesador(),memoria_total(),memoria_usada(),swap_total(),swap_usada(),disco_total(),disco_usado());
		    }
		else
		    {
		    $mensaje = $conexion->errorMessage;
		    open(MENSAGES,">> $logueo");
		    print MENSAGES $mensaje;
		    close MENSAGES;
		    $conexion->reset;
		    }
		sleep($tiempo_dir*120);
		}

#fin del programa
	}
    
$end = 0;

###############################################################################################################################################################





