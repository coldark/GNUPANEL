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

sub crea_directorio
    {
    my $directorio_in = $_[0];
    my $comando = "/bin/mkdir -p -m 0550 ";
    my $comandar = "";
    my $directorio = $directorio_in;
    my $directorio_raiz_sitios_in = trim($directorio_raiz_sitios);
    my $caracter = chop($directorio_raiz_sitios_in);
    if(!($caracter eq "/"))
	{
	$directorio_raiz_sitios_in = $directorio_raiz_sitios_in.$caracter;
	}
    
    $directorio =~ s/$directorio_raiz_sitios_in//;
    @data = split("/",$directorio);

    my $raiz = $directorio_raiz_sitios_in;
    chomp($raiz);
    foreach $partes(@data)
	{
	if(length($partes)>0)
	    {
	    $raiz = $raiz."/".$partes;
	    $comandar = $comando.$raiz;
	    if (!(-e $raiz))
		{
    		my $comandar = $comando.$raiz;
		system($comandar);
		chown(scalar(getpwnam($usuario_dir_apache)),scalar(getgrnam($grupo_dir_apache)),$raiz);
		}    
	    }
	}
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

sub dame_idioma_usuario
    {
    my $conexion = $_[0];
    my $logueo = $_[1];
    my $id_usuario = $_[2];
    
    my $sql = "SELECT idioma FROM gnupanel_usuario_lang WHERE id_usuario = $id_usuario ";
    my $result = $conexion->exec($sql);
    my $idioma = $result->getvalue(0,0);

    if(length($idioma)<2)
	{
	$idioma = "en";
	}
    
    $idioma = trim($idioma);
    $idioma = $idioma;
    }

sub genera_estadisticas
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my @fila;
    my @fila_0;
    open(MENSDIR,">> $logueo");
    my $result = NULL;
    my $result_0 = NULL;
    
    my $sql = "SELECT * FROM gnupanel_apacheconf WHERE estado = 9";
    $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
    {
    while(@fila = $result->fetchrow)
	{
	my $directorio = $fila[6];
	my $server_admin = $fila[7];
	my $ip = $fila[4];
	my $subdominio = $fila[3];
	my $id_subdominio = $fila[1];
	my $id_dominio = $fila[2];
	my $tiene_ssl = $fila[10];
	my $estado = $fila[14];
	my $redirigir = $fila[8];
	my $dominio_destino = $fila[9];
	my $id_apache = $fila[0];
	my $php_register_globals = $fila[11];
	my $php_safe_mode = $fila[12];
	my $indexar = $fila[13];
	my $activo = $fila[15];
	
	my $sql_sub_0 = "(SELECT id_servidor FROM gnupanel_servidores WHERE servidor = '$nombre_servidor')";
	my $sql_sub_1 = "(SELECT ip_publica FROM gnupanel_ips_servidor WHERE CASE WHEN id_servidor = $sql_sub_0 THEN ip_publica = '$ip' OR ip_privada = '$ip' ELSE false END)";
	$sql = "SELECT dominio FROM gnupanel_usuario WHERE id_usuario = $id_dominio AND EXISTS $sql_sub_1 ";

	$result_0 = $conexion->exec($sql);
	if($result_0->resultStatus == PGRES_TUPLES_OK)
	    {
	    @fila_0 = $result_0->fetchrow;
	    my $dominio = $fila_0[0];
	    if($estado == 9)
		{

	        my $dir_admin = "/usr/share/gnupanel/admin/";
		my $dir_reseller = "/usr/share/gnupanel/reseller/";
		my $dir_usuarios = "/usr/share/gnupanel/usuarios/";
		my $dir_add_ons = "/usr/share/gnupanel/add-ons/";
    		my $dir_mail = "/usr/share/gnupanel/mail/";
    		my $dir_gnupanel = "/usr/share/gnupanel/gnupanel/";
    		my $dir_default = "/usr/share/gnupanel/gnupanel/";

		if(($dir_admin eq pone_barra($directorio)) || ($dir_reseller eq pone_barra($directorio)) || ($dir_usuarios eq pone_barra($directorio)) || ($dir_add_ons eq pone_barra($directorio)) || ($dir_mail eq pone_barra($directorio)) || ($dir_gnupanel eq pone_barra($directorio)) || ($dir_default eq pone_barra($directorio)))
		    {
		    }
		else
		    {
		    if(!($subdominio eq "gnupanel"))
			{
			my $idioma = dame_idioma_usuario($conexion,$logueo,$id_dominio);
			crea_directorio($directorio."/gnupanel");
			crea_directorio($directorio."/webmail");
			if($tiene_ssl==1)
			    {
			        $directorio =~ s/\/subdominios-ssl\//\/gnupanel\//; 
			    }
			else
			    {
			        $directorio =~ s/\/subdominios\//\/gnupanel\//; 
			    }
			crea_directorio($directorio);
			genera_webalizer($subdominio,$dominio,$directorio,$tiene_ssl,$idioma);
			genera_awstats($subdominio,$dominio,$directorio,$tiene_ssl,$idioma);
			genera_index($subdominio,$dominio,$directorio,$tiene_ssl,$conexion);
			}
		    }
		}
	    }
	}
    }
    else
    {
	$mensaje = $conexion->errorMessage;
	print MENSDIR $mensaje;
	print $mensaje;
    }
    close MENSDIR;
}

sub dame_archivo_log_webalizer
{
    my $subdominio = $_[0];
    my $dominio = $_[1];
    my $tiene_ssl = $_[2];

    my $archivo_log = $dominio;
    if(length($subdominio)>0)
	{
	$archivo_log = $subdominio.".".$archivo_log;	
	}

    if($tiene_ssl == 1)
	{
	$archivo_log = "ssl.".$archivo_log;
	}

    $archivo_log = "/var/log/apache2/webalizer/".$archivo_log.".log";
    $archivo_log = $archivo_log;
}

sub dame_archivo_log_awstats
{
    my $subdominio = $_[0];
    my $dominio = $_[1];
    my $tiene_ssl = $_[2];

    my $archivo_log = $dominio;
    if(length($subdominio)>0)
	{
	$archivo_log = $subdominio.".".$archivo_log;	
	}

    if($tiene_ssl == 1)
	{
	$archivo_log = "ssl.".$archivo_log;	
	}

    $archivo_log = "/var/log/apache2/awstats/".$archivo_log.".log";
    $archivo_log = $archivo_log;
}

sub genera_awstats
{
    my $subdominio = $_[0];
    my $dominio = $_[1];
    my $directorio = $_[2];
    my $tiene_ssl = $_[3];
    my $idioma = $_[4];
    my $archivo_log = dame_archivo_log_awstats($subdominio,$dominio,$tiene_ssl);
    $directorio = pone_barra($directorio)."awstats/";
    crea_directorio($directorio);
    my $archivo_awstats = "/etc/awstats/awstats.".$dominio.".conf";
    my $dominio_aw = $dominio;

    if(length($subdominio)>0)
	{
	$archivo_awstats = "/etc/awstats/awstats.".$subdominio.".".$dominio.".conf";
	$dominio_aw = $subdominio.".".$dominio;
	}

    my $borrar = "rm -f ".$archivo_awstats;
    my $comando = "/usr/local/gnupanel/genera_awstats $dominio_aw $directorio";
    
    open(AWSTAT,"> $archivo_awstats");
    print AWSTAT "LogType=W \n";
    print AWSTAT "LogFile=\"$archivo_log\" \n";
    print AWSTAT "LogFormat=1 \n";
    print AWSTAT "SiteDomain=\"$dominio_aw\" \n";
    print AWSTAT "DirIcons=\"icon\" \n";
    print AWSTAT "DirData=\"/var/lib/awstats\" \n";
    print AWSTAT "Lang=\"$idioma\" \n";
    print AWSTAT "DNSLookup=1 \n";
    print AWSTAT "PurgeLogFile=1 \n";
    print AWSTAT "KeepBackupOfHistoricFiles=1 \n";
    print AWSTAT "Lang=\"$idioma\" \n";
    print AWSTAT "HostAliases=\"$dominio_aw\" \n";
    close AWSTAT;
    system($comando);
    $comando = "/bin/chown -R ".$usuario_dir_apache.":".$grupo_dir_apache." ".$directorio;
    system($comando);
    $comando = "/bin/chmod 440 ".$directorio."*";
    system($comando);
    system($borrar);
}

sub genera_webalizer
{
    my $subdominio = $_[0];
    my $dominio = $_[1];
    my $directorio = $_[2];
    my $tiene_ssl = $_[3];
    my $idioma = $_[4];
    my $archivo_log = dame_archivo_log_webalizer($subdominio,$dominio,$tiene_ssl);
    $directorio = pone_barra($directorio)."webalizer/";
    crea_directorio($directorio);
    
    my $dominio_web = $dominio;

    if(length($subdominio)>0)
	{
	$dominio_web = $subdominio.".".$dominio;
	}

    my $comando = "/bin/cat `/bin/ls -1 ".$archivo_log."* | /usr/bin/sort -r` | /usr/bin/env LANG=en_US LC_ALL=en_US LC_CTYPE=en_US.ISO8859-1 LC_MESSAGES=en_US /usr/bin/webalizer -p -x php -n ".$dominio_web." -o ".$directorio." - " ;

    if($idioma eq "es")
	{
	$comando = "/bin/cat `/bin/ls -1 ".$archivo_log."* | /usr/bin/sort -r` | /usr/bin/env LANG=es_ES LC_ALL=es_ES LC_CTYPE=es_ES.ISO8859-1 LC_MESSAGES=es_ES /usr/bin/webalizer -p -x php -n ".$dominio_web." -o ".$directorio." - " ;
	}
    elsif($idioma eq "fr")
	{
	$comando = "/bin/cat `/bin/ls -1 ".$archivo_log."* | /usr/bin/sort -r` | /usr/bin/env LANG=fr_FR LC_ALL=fr_FR LC_CTYPE=fr_FR.ISO8859-1 LC_MESSAGES=fr_FR /usr/bin/webalizer -p -x php -n ".$dominio_web." -o ".$directorio." - " ;
	}
    	
    system($comando);
    $comando = "/bin/chown -R ".$usuario_dir_apache.":".$grupo_dir_apache." ".$directorio;
    system($comando);
    $comando = "/bin/chmod 440 ".$directorio."*";
    system($comando);
}

sub genera_index
{
    my $subdominio = $_[0];
    my $dominio = $_[1];
    my $directorio = $_[2];
    my $tiene_ssl = $_[3];
    my $conexion = $_[4];
    #my $archivo_log = dame_archivo_log($subdominio,$dominio,$tiene_ssl);
    my $subdominio_E = "";
    my $renglon;
    my $sql = "SELECT idioma FROM gnupanel_usuario_lang WHERE id_usuario = (SELECT id_usuario FROM gnupanel_usuario WHERE dominio = '$dominio') ";
    my $result = $conexion->exec($sql);
    my $idioma = $result->getvalue(0,0);

    $archivo_index = pone_barra($directorio)."index.php";
    $archivo_index_stats = "/usr/local/gnupanel/lang/".$idioma."/index-stats.txt";
    $archivo_htaccess = pone_barra($directorio).".htaccess";
    
    if(length($subdominio)>0)
	{
	$subdominio_E = $subdominio.".".$dominio;
	}
    else
	{
	$subdominio_E = $dominio;
	}
    
    open(INDICE,"> $archivo_index");
    open(ENTRADA,$archivo_index_stats);

    while(eof(ENTRADA)==0)
	{
	$renglon = <ENTRADA>;
	$renglon =~ s/_SUBDOMINIO_/$subdominio_E/ ;
	print INDICE $renglon;
	}

    close ENTRADA;    
    close INDICE;

    open(HTACCESS,"> $archivo_htaccess");
    print HTACCESS "AddDefaultCharset ISO-8859-1 \n";
    close HTACCESS;

    $comando = "/bin/chown ".$usuario_dir_apache.":".$grupo_dir_apache." ".$archivo_index;
    system($comando);
    $comando = "/bin/chmod 440 ".$archivo_index;
    system($comando);

    $comando = "/bin/chown ".$usuario_dir_apache.":".$grupo_dir_apache." ".$archivo_htaccess;
    system($comando);

    $comando = "/bin/chmod 440 ".$archivo_htaccess;
    system($comando);

    $comando = "/bin/chmod 550 ".pone_barra($directorio);
    system($comando);

    $comando = "/bin/chmod 550 ".pone_barra($directorio)."awstats";
    system($comando);

    $comando = "/bin/chmod 550 ".pone_barra($directorio)."webalizer";
    system($comando);
    
    $comando = "/bin/chmod 550 ".pone_barra($directorio)."awstats/icon";
    system($comando);

}

#######################################################################################################################
## Put ourselves in background if not in debug mode. 
require "/etc/gnupanel/gnupanel.conf.pl";

$nombre = $0;
$nombre = substr($nombre,rindex($nombre,"/")+1,length($nombre)-1);

$logueo = "/var/log/".$nombre.".log";
$conexion = NULL;
$reloaded = 0;

open(STDERR, ">> $logueo");

#Inicio del programa
open(STDOUT, ">> $logueo");

$conexion = NULL;
$conexion = Pg::connectdb("dbname=$database host=localhost user=$userdb password=$pasaportedb");
	    
$estado = $conexion->status;

if($estado==PGRES_CONNECTION_OK)
    {
    genera_estadisticas($conexion,$logueo);
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





    



