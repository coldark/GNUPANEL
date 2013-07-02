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

sub pone_barra
    {
    my $directorio_in = $_[0];
    my $directorio = trim($directorio_in);
    my $caracter = chop($directorio);
    if($caracter eq "/")
	{
	$directorio = $directorio.$caracter;
	}
    else
	{
	$directorio = $directorio.$caracter."/";
	}	
    $directorio = $directorio;    
    }

sub mide_directorios_webftp
    {
    my $directorio = $_[0];
    my $conexion = $_[1];
    my $logueo = $_[2];
    my $comando = "/usr/bin/du -m -s ";
    open(MENSDIR,">> $logueo");
    my $estado;
    my $mensaje;
    my $comandar = $comando.$directorio;

    my $directorios = `$comandar`;

    my @datos = NULL;
    @datos = split("\n",$directorios);
    my $k;
    foreach $k(@datos)
	{
	my @divide = NULL;
	@divide = split(" ",$k);
	my $tamano = $divide[0];
	my $dominio = substr($divide[1],rindex($divide[1],"/")+1,length($divide[1])-1);
	my $result = NULL;
	$result = $conexion->exec("BEGIN");
	my $sql = "SELECT dominio FROM gnupanel_espacio WHERE dominio='$dominio' ";
	$result = $conexion->exec($sql);
	$result = $conexion->getResult;

	if($result)
	    {
	    $sql = "UPDATE gnupanel_espacio SET ftpweb = $tamano WHERE dominio = '$dominio' ";
    	    $result = $conexion->exec($sql);
	    $estado = $result->resultStatus;

	    $sql = "UPDATE gnupanel_espacio SET total = ftpweb + correo + postgres + mysql WHERE dominio = '$dominio' ";
    	    $result = $conexion->exec($sql);
	    $estado = $estado && $result->resultStatus;

	    if($estado == PGRES_COMMAND_OK)
		{
    		$result = $conexion->exec("END");
		}
	    else
		{
		$mensaje = $conexion->errorMessage;
		print MENSDIR $mensaje;
    		$result = $conexion->exec("ROLLBACK");
		}
	    }
	else
	    {
	    $mensaje = "No se hace nada \n";
	    print MENSDIR $mensaje;
    	    $result = $conexion->exec("END");
	    }

	}
    close MENSDIR;
    }
    
sub mide_directorios_correo
    {
    my $directorio = $_[0];
    my $conexion = $_[1];
    my $logueo = $_[2];
    my $comando = "/usr/bin/du -m -s ";
    open(MENSDIR,">> $logueo");
    
    my $comandar = $comando.$directorio;
    my $directorios = `$comandar`;
    my $mensaje;
    my @datos = NULL;
    @datos = split("\n",$directorios);
    my $j;
    foreach $j(@datos)
	{
	my @divide = NULL;
	@divide = split(" ",$j);
	my $tamano = $divide[0];
	my $dominio = substr($divide[1],rindex($divide[1],"/")+1,length($divide[1])-1);
	my $result = NULL;
	$result = $conexion->exec("BEGIN");
	my $sql = "SELECT dominio FROM gnupanel_espacio WHERE dominio='$dominio' ";
	$result = $conexion->exec($sql);
	$result = $conexion->getResult;

	if($result)
	    {
	    $sql = "UPDATE gnupanel_espacio SET correo = $tamano WHERE dominio = '$dominio' ";
    	    $result = $conexion->exec($sql);
	    $estado = $result->resultStatus;

	    $sql = "UPDATE gnupanel_espacio SET total = ftpweb + correo + postgres + mysql WHERE dominio = '$dominio' ";
    	    $result = $conexion->exec($sql);
	    my $estado = $estado && $result->resultStatus;

	    if($estado == PGRES_COMMAND_OK)
		{
    		$result = $conexion->exec("END");
		}
	    else
		{
		$mensaje = $conexion->errorMessage;
		print MENSDIR $mensaje;
    		$result = $conexion->exec("ROLLBACK");
		}
	    }
	else
	    {
	    $mensaje = "No se hace nada \n";
	    print MENSDIR $mensaje;
    	    $result = $conexion->exec("END");
	    }

	}
    close MENSDIR;
    }


sub checkea_directorios
    {
    my $base = $_[0];
    my $base_correo = $_[1];
    my $conexion = $_[2];
    my $logueo = $_[3];
    my $comando = "/usr/bin/du -m -s ";

    open(MENSDIR_A,">> $logueo");
    my $directorio = $base;
    my $barra = chop($directorio);
    if($barra eq '/')
	{
	$directorio = $directorio.$barra;
	}
    else
	{
	$directorio = $directorio.$barra."/";
	}

    my $directorio_correo = $base_correo;
    $barra = chop($directorio_correo);
    if($barra eq '/')
	{
	$directorio_correo = $directorio_correo.$barra;
	}
    else
	{
	$directorio_correo = $directorio_correo.$barra."/";
	}    

    my $sql = "SELECT id_admin,admin FROM gnupanel_admin ";
    my $result = $conexion->exec($sql);
    my $estado = $result->resultStatus;
    if($estado != PGRES_TUPLES_OK)
	{
	my $mensaje = $conexion->errorMessage;
	print MENSDIR_A $mensaje;
	}
    else
	{
	my @filas_0 ;        
	my @filas_1;
	my @directorios_webftp;
	my @directorios_correo;
	my $fila;
    
	while(@fila = $result->fetchrow)
	    {
	    if(@fila)
		{
		push(@filas_0,$fila[0]);
		push(@filas_1,$fila[1]);
		}
	    }

	my @id_admins = @filas_0;
	my @admins = @filas_1;

	my $largo = @admins;
	my $admin;
	my $id_admin;
	my $director;
	my $i; 
	for($i=0;$i<$largo;$i++)
	    {
	    $admin = $admins[$i];
	    $id_admin = $id_admins[$i];
	    $sql = "SELECT reseller,dominio FROM gnupanel_reseller WHERE cliente_de = $id_admin ";
	    $result = $conexion->exec($sql);
	    while(@fila = $result->fetchrow)
		{
		$director = $directorio.$admin."/".join("@",@fila)."/*";
		push(@directorios_webftp,$director);
		$director = $directorio_correo.$admin."/".join("@",@fila)."/*";
		push(@directorios_correo,$director);
		}
	    }

	$largo = @directorios_webftp;
	for($i=0;$i<$largo;$i++)
	    {
	    mide_directorios_webftp($directorios_webftp[$i],$conexion,$logueo);
	    mide_directorios_correo($directorios_correo[$i],$conexion,$logueo);
	    }
	}
    close MENSDIR_A;
    }

sub mide_maildir
    {
    my $directorio = $_[0];
    my $conexion = $_[1];
    my $logueo = $_[2];
    open(MENSDIR,">> $logueo");
    my $archivo_quota = "mail-quota.mx";
    my $comando = "/bin/ls -d -1 ".$directorio."/admin/*/*/*";
    my $dir_temp = `$comando`;
    my @directorios_correos = split(/\n/,$dir_temp);
    my $largo = @directorios_correos;
    my $archivo = "";

    for($i=0;$i<$largo;$i++)
    {
	$archivo = $directorios_correos[$i]."/".$archivo_quota;
	$comando = "/usr/bin/du -s -m ".$directorios_correos[$i]." | /usr/bin/mawk '{print \$1;}' > $archivo";
	system($comando);
	$comando = "/bin/chown correos:mail $archivo";
	system($comando);
	$comando = "/bin/chmod 0644 $archivo";
	system($comando);
    }

    close MENSDIR;
    }

sub mide_espacio_mysql
    {
    my $directorio = $_[0];
    my $conexion = $_[1];
    my $logueo = $_[2];
    open(MENSDIR,">> $logueo");
    my $estado;
    my $mensaje;

    my $sql = "SELECT id_dominio FROM gnupanel_espacio ";
    my $result = $conexion->exec($sql);

    my $estado = $result->resultStatus;
    if($estado != PGRES_TUPLES_OK)
	{
	my $mensaje = $conexion->errorMessage;
	print MENSDIR $mensaje;
	}
    else
	{
	my $fila;

	while(@fila = $result->fetchrow)
	    {
	    my $id_usuario = $fila[0];
	    my $espacio = mide_espacio_mysql_aux($directorio,$id_usuario,$conexion,$logueo);
	    
	    $sql = "UPDATE gnupanel_espacio SET mysql = $espacio WHERE id_dominio = $id_usuario ";
	    my $result_0 = $conexion->exec($sql);

	    $sql = "UPDATE gnupanel_espacio SET total = ftpweb + correo + postgres + mysql WHERE id_dominio = $id_usuario ";
	    $result_0 = $conexion->exec($sql);
	    }
	}

    close MENSDIR;
    }


sub mide_espacio_mysql_aux
    {
    my $directorio = $_[0];
    my $id_usuario = $_[1];
    my $conexion = $_[2];
    my $logueo = $_[3];
    my $comando = "/usr/bin/du -m -s ";
    open(MENSDIR,">> $logueo");
    my $estado;
    my $mensaje;
    my $comandar = $comando.pone_barra($directorio);
    my $espacio = 0;
    my $sql = "SELECT nombre_base FROM gnupanel_bases_de_datos WHERE id_tipo_base = 1 AND id_dueno = $id_usuario ";
    my $result = $conexion->exec($sql);

    my $estado = $result->resultStatus;
    if($estado != PGRES_TUPLES_OK)
	{
	my $mensaje = $conexion->errorMessage;
	print MENSDIR $mensaje;
	}
    else
	{
	my $fila;
	while(@fila = $result->fetchrow)
	    {
	    my $nombre_base = $fila[0];
	    my $comandito = $comandar.$nombre_base;
	    my $tamanos = `$comandito`;
	    my @datos = NULL;
    	    @datos = split(" ",$tamanos);
	    $espacio = $espacio + $datos[0];
	    }
	}

    close MENSDIR;
    $espacio = $espacio;
    }

sub mide_espacio_postgres
    {
    my $conexion = $_[0];
    my $logueo = $_[1];
    open(MENSDIR,">> $logueo");
    my $estado;
    my $mensaje;

    my $sql = "SELECT id_dominio FROM gnupanel_espacio ";
    my $result = $conexion->exec($sql);

    my $estado = $result->resultStatus;
    if($estado != PGRES_TUPLES_OK)
	{
	my $mensaje = $conexion->errorMessage;
	print MENSDIR $mensaje;
	}
    else
	{
	my $fila;

	while(@fila = $result->fetchrow)
	    {
	    my $id_usuario = $fila[0];
	    
#	    $sql = "SELECT (sum(database_size(nombre_base))/1048576) FROM gnupanel_bases_de_datos WHERE id_dueno = $id_usuario AND id_tipo_base = 0 ";
	    $sql = "SELECT (sum(pg_database_size(nombre_base))/1048576) FROM gnupanel_bases_de_datos WHERE id_dueno = $id_usuario AND id_tipo_base = 0 ";
	    my $result_0 = $conexion->exec($sql);
	    my $espacio_postgres = 0;
	    if($result_0->getvalue(0,0)) 
		{
		$espacio_postgres = $result_0->getvalue(0,0);
		}
	    
	    $sql = "UPDATE gnupanel_espacio SET postgres = $espacio_postgres WHERE id_dominio = $id_usuario ";
	    $result_0 = $conexion->exec($sql);

	    $sql = "UPDATE gnupanel_espacio SET total = ftpweb + correo + postgres + mysql WHERE id_dominio = $id_usuario ";
	    $result_0 = $conexion->exec($sql);
	    }
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
	    print "Inicializando el medidor de espacio en disco \n";
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
		    checkea_directorios($directorio_raiz_sitios,$directorio_raiz_correo,$conexion,$logueo);
		    mide_espacio_mysql($directorio_raiz_mysql,$conexion,$logueo);
		    mide_espacio_postgres($conexion,$logueo);
		    mide_maildir($directorio_raiz_correo,$conexion,$logueo);
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
