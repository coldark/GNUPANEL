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

sub existe_reseller
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my $reseller_dominio = $_[2];
    my @fila;
    open(MENSDIR,">> $logueo");
    my $result;
    my $retorno = 1;
    my @separador = split("\@",$reseller_dominio);
    my $reseller = $separador[0];
    my $dominio = $separador[1];
    my $sql = "SELECT count(dominio) FROM gnupanel_reseller WHERE reseller = '$reseller' AND dominio = '$dominio' ";
    $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
    {
	my $existe = $result->getvalue(0,0);
	if($existe == 0)
	    {
	    $retorno = 0;
	    }
    }
    else
    {
	$mensaje = $conexion->errorMessage;
	print MENSDIR $mensaje;
	print $mensaje;
	$retorno = 1;
    }
    close MENSDIR;
    $retorno = $retorno;
}

sub existe_usuario
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my $dominio = $_[2];
    my @fila;
    open(MENSDIR,">> $logueo");
    my $result;
    my $retorno = 1;
    my $sql = "SELECT count(dominio) FROM gnupanel_usuario WHERE dominio = '$dominio' ";
    $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
    {
	my $existe = $result->getvalue(0,0);
	if($existe == 0)
	    {
	    $retorno = 0;
	    }
    }
    else
    {
	$mensaje = $conexion->errorMessage;
	print MENSDIR $mensaje;
	print $mensaje;
	$retorno = 1;
    }
    close MENSDIR;
    $retorno = $retorno;
}

sub existe_base_datos
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my $basedatos = $_[2];
    my @fila;
    open(MENSDIR,">> $logueo");
    my $result;
    my $retorno = 1;
    my $sql = "SELECT count(nombre_base) FROM gnupanel_bases_de_datos WHERE nombre_base = '$basedatos' ";
    $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
    {
	my $existe = $result->getvalue(0,0);
	if($existe == 0)
	    {
	    $retorno = 0;
	    }
    }
    else
    {
	$mensaje = $conexion->errorMessage;
	print MENSDIR $mensaje;
	print $mensaje;
	$retorno = 1;
    }
    close MENSDIR;
    $retorno = $retorno;
}

sub existe_usuario_basedatos
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my $usuario = $_[2];
    my @fila;
    open(MENSDIR,">> $logueo");
    my $result;
    my $retorno = 1;
    my $sql = "SELECT count(base_user) FROM gnupanel_usuarios_base WHERE base_user = '$usuario' ";
    $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
    {
	my $existe = $result->getvalue(0,0);
	if($existe == 0)
	    {
	    $retorno = 0;
	    }
    }
    else
    {
	$mensaje = $conexion->errorMessage;
	print MENSDIR $mensaje;
	print $mensaje;
	$retorno = 1;
    }
    close MENSDIR;
    $retorno = $retorno;
}




sub borra_reseller_no_usados
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my @fila;
    open(MENSDIR,">> $logueo");
    
    my $sql = "SELECT admin FROM gnupanel_admin ";
    my $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
    {
    while(@fila = $result->fetchrow)
	{
    	my $admin = $fila[0];
	my $directorio_sitios = pone_barra($directorio_raiz_sitios).$admin."/*";
	my $directorio_correos = pone_barra($directorio_raiz_correo).$admin."/*";
	my @directorios ;
	my $comando = "/bin/ls -1 -d ";
	my $comandar = "";
	
	$comandar = $comando.$directorio_sitios;
	my $directorios_temp = `$comandar`;
	
	@directorios = split("\n",trim($directorios_temp));
	
	my $largo = @directorios;
	
	for($i=0;$i<$largo;$i++)
	    {
	    my @directorio = split("/",trim($directorios[$i]));
	    my $reseller_dominio = trim($directorio[@directorio-1]);
	    my $existe_reseller = existe_reseller($conexion,$logueo,$reseller_dominio);
	    
	    if($existe_reseller == 0)
		{
		$comandar = "/bin/rm -f -r ".$directorios[$i];
		system($comandar);
		}
	    }
	
	$comandar = $comando.$directorio_correos;
	$directorios_temp = `$comandar`;
	
	@directorios = split("\n",trim($directorios_temp));
	
	$largo = @directorios;
	
	for($i=0;$i<$largo;$i++)
	    {
	    my @directorio = split("/",trim($directorios[$i]));
	    my $reseller_dominio = trim($directorio[@directorio-1]);
	    my $existe_reseller = existe_reseller($conexion,$logueo,$reseller_dominio);
	    
	    if($existe_reseller == 0)
		{
		$comandar = "/bin/rm -f -r ".$directorios[$i];
		system($comandar);
		}
	    }
	}
    }
    
    close MENSDIR;
}

sub borra_dominios_no_usados
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my @fila;
    open(MENSDIR,">> $logueo");
    
    my $sql = "SELECT id_admin,admin FROM gnupanel_admin ";
    my $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
    {
	while(@fila = $result->fetchrow)
	{
    	my $id_admin = $fila[0];
    	my $admin = $fila[1];
	my @fila_0;
	$sql = "SELECT reseller,dominio FROM gnupanel_reseller WHERE cliente_de = $id_admin ";
	my $result_0 = $conexion->exec($sql);
	if($result_0->resultStatus == PGRES_TUPLES_OK)
	    {
	    while(@fila_0 = $result_0->fetchrow)
		{	
		my $directorio_sitios = pone_barra($directorio_raiz_sitios).$admin."/".$fila_0[0]."\@".$fila_0[1]."/*";
		my $directorio_correos = pone_barra($directorio_raiz_correo).$admin."/".$fila_0[0]."\@".$fila_0[1]."/*";
		my @directorios ;
    		my $comando = "/bin/ls -1 -d ";
		my $comandar = "";
	
		$comandar = $comando.$directorio_sitios;
		my $directorios_temp = `$comandar`;

		@directorios = split("\n",trim($directorios_temp));
	
		my $largo = @directorios;
	
		for($i=0;$i<$largo;$i++)
		    {
		    my @directorio = split("/",trim($directorios[$i]));
		    my $usuario_dominio = trim($directorio[@directorio-1]);
		    my $existe_dominio = existe_usuario($conexion,$logueo,$usuario_dominio);
			if($existe_dominio == 0)
			{
			$comandar = "/bin/rm -f -r ".$directorios[$i];
			system($comandar);
			}
		    }
	
		$comandar = $comando.$directorio_correos;
		$directorios_temp = `$comandar`;
	
		@directorios = split("\n",trim($directorios_temp));
	
		$largo = @directorios;
	
		for($i=0;$i<$largo;$i++)
		    {
		    my @directorio = split("/",trim($directorios[$i]));
		    my $usuario_dominio = trim($directorio[@directorio-1]);
		    my $existe_dominio = existe_usuario($conexion,$logueo,$usuario_dominio);
			if($existe_dominio == 0)
			{
			$comandar = "/bin/rm -f -r ".$directorios[$i];
			system($comandar);
			}
		    }
		}
	    }
	}
    }
    
    close MENSDIR;
}

sub borra_bases_postgres_no_usados
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my @fila;
    open(MENSDIR,">> $logueo");
    
    my $sql = "SELECT datname from pg_catalog.pg_database WHERE datname != 'postgres' AND datname != 'template0' AND datname != 'template1' AND datname != '$database' AND NOT EXISTS (SELECT * FROM gnupanel_bases_de_datos WHERE gnupanel_bases_de_datos.nombre_base = pg_catalog.pg_database.datname AND id_tipo_base = 0) ";
    my $comando = "/bin/su postgres -c \"/usr/lib/postgresql/9.1/bin/dropdb ";
    
    my $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
    {
	while(@fila = $result->fetchrow)
	{
    	my $base_postgres = trim($fila[0]);
	my $comandar = $comando.$base_postgres."\"";
	system($comandar);	
	}
    }

    $sql = "SELECT usename from pg_catalog.pg_user WHERE usename != 'postgres' AND usename != 'gnupanel' AND usename != 'apache' AND usename != 'postfix' AND usename != 'pdns' AND usename != 'proftpd' AND usename != 'sdns' AND NOT EXISTS (SELECT * FROM gnupanel_usuarios_base WHERE gnupanel_usuarios_base.base_user = pg_catalog.pg_user.usename ) ";
    $comando = "/bin/su postgres -c \"/usr/lib/postgresql/9.1/bin/dropuser ";
    $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
    {
	while(@fila = $result->fetchrow)
	{
    	my $usuario_postgres = trim($fila[0]);
	my $comandar = $comando.$usuario_postgres."\"";
	system($comandar);	
	}
    }

    close MENSDIR;
}

sub borra_bases_mysql_no_usados
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my @fila;
    open(MENSDIR,">> $logueo");
    
    my $sql = "/usr/bin/mysql -u root --password=$pasaportedbmysql -e \"SHOW DATABASES; \" | /bin/grep -v information_schema | /bin/grep -v Database | /bin/grep -v mysql | /bin/grep -v phpmyadmin | /bin/grep -v roundcube | /bin/grep -v geeklab ";
    my $basedato = `$sql`;
    my @basedatos = split("\n",$basedato);
    my $largo = @basedatos;
    for($i=0;$i<$largo;$i++)
	{
	$base_borrar = trim($basedatos[$i]);
	$existe = existe_base_datos($conexion,$logueo,$base_borrar);
	if($existe == 0)
	    {
	    $sql = "/usr/bin/mysql -u root --password=$pasaportedbmysql -e \"DROP DATABASE IF EXISTS $base_borrar;\" ";
	    system($sql);
	    }
	}

    $sql = "/usr/bin/mysql -u root --password=$pasaportedbmysql -e \"SELECT User FROM mysql.user WHERE User != 'mysql' AND User != 'root' AND User != 'debian-sys-maint' AND User != 'phpmyadmin' AND User != 'roundcube' AND User != 'geeklab' AND User != 'geeklab_en' ; \" | /bin/grep -v User ";
    $usuariobase = `$sql`;
    my @usuariosbase = split("\n",$usuariobase);
    $largo = @usuariosbase;
    for($i=0;$i<$largo;$i++)
	{
	$usuario_borrar = trim($usuariosbase[$i]);
	$existe = existe_usuario_basedatos($conexion,$logueo,$usuario_borrar);
	if($existe == 0)
	    {
	    $sql = "/usr/bin/mysql -u root --password=$pasaportedbmysql -e \"DELETE FROM mysql.user WHERE User = '$usuario_borrar'; \" ";
	    system($sql);
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
    borra_reseller_no_usados($conexion,$logueo);
    borra_dominios_no_usados($conexion,$logueo);
    borra_bases_postgres_no_usados($conexion,$logueo);
    borra_bases_mysql_no_usados($conexion,$logueo);
    }
else
    {
    $mensaje = $conexion->errorMessage;
    open(MENSAGES,">> $logueo");
    print MENSAGES $mensaje;
    close MENSAGES;
    $conexion->reset;
    }

my $comandar = "/usr/local/gnupanel/clean-stats-logs.sh";
system($comandar);

#fin del programa

##$end = 0;

###############################################################################################################################################################


