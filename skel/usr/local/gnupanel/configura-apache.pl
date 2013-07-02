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
	$result = "00000000000".trim($numero);
	}

    if($numero >= 10 && $numero < 100)
	{
	$result = "0000000000".trim($numero);
	}

    if($numero >= 100 && $numero < 1000)
	{
	$result = "000000000".trim($numero);
	}

    if($numero >= 1000 && $numero < 10000)
	{
	$result = "00000000".trim($numero);
	}

    if($numero >= 10000)
	{
	$result = "0000000".trim($numero);
	}

    if($numero >= 100000)
	{
	$result = "000000".trim($numero);
	}


    if($numero >= 1000000)
	{
	$result = "00000".trim($numero);
	}

    if($numero >= 10000000)
	{
	$result = "0000".trim($numero);
	}

    if($numero >= 100000000)
	{
	$result = "000".trim($numero);
	}

    if($numero >= 1000000000)
	{
	$result = "00".trim($numero);
	}

    if($numero >= 10000000000)
	{
	$result = "0".trim($numero);
	}

    if($numero >= 100000000000)
	{
	$result = trim($numero);
	}
    
    $result = "".$result;
    }

sub checkea_directorio
{
    my $directorio = $_[0];
	    
    my $result = 0;
    my $largo = length($directorio_raiz_sitios);
    my $dir_comp = substr($directorio,0,$largo);
    my $cantidad_barras = split("/",$directorio);
    $largo = length($directorio_raiz);
		    
    if(($dir_comp eq $directorio_raiz_sitios) && ($cantidad_barras >= 8))
        {
        $result = 1;
	}
						    
    $result = $result;
}

sub dominio_por_dir_sub
    {
    my $directorio = $_[0];
    my @lista = split("/",$directorio);
    my $largo = @lista;
    $dominio = $lista[$largo-3];
    $result = $dominio;
    }

sub dominio_reseller_por_dir_sub
    {
    my $directorio = $_[0];
    my @lista = split("/",$directorio);
    my $largo = @lista;
    $dominio = $lista[$largo-4];
    @lista = split("@",$dominio);
    $dominio = $lista[1];
    $result = $dominio;
    }

sub crea_ssleay
    {
    my $pais = $_[0];
    my $provincia = $_[1];
    my $ciudad = $_[2];
    my $organizacion = $_[3];
    my $orga_red = $_[4];
    my $dominio = $_[5];
    my $correo = $_[6];
    my $archivo = $_[7];

    open(SSLEAY,">".$archivo);
    print SSLEAY "RANDFILE                = /tmp/gnupanel.rnd \n";
    print SSLEAY "[ req ] \n";
    print SSLEAY "default_bits            = 1024 \n";
    print SSLEAY "default_keyfile         = privkey.pem \n";
    print SSLEAY "distinguished_name      = req_distinguished_name \n";
    print SSLEAY "prompt                  = no \n";
    print SSLEAY "[ req_distinguished_name ] \n";
    print SSLEAY "countryName                     = $pais \n";
    print SSLEAY "stateOrProvinceName             = $provincia \n";
    print SSLEAY "localityName                    = $ciudad \n";
    print SSLEAY "organizationName                = $organizacion \n";
    print SSLEAY "organizationalUnitName          = $orga_red \n";
    print SSLEAY "commonName                      = $dominio \n";
    print SSLEAY "emailAddress                    = $correo \n";
    close SSLEAY;
    }

sub crea_cert_apache
    {

    my $dominio = $_[0];
    my $subdominio = $_[1];
    my $id_subdominio = $_[2];
    my $correo = $_[3];
    my $conexion = $_[4];

    my $pais = "";
    my $provincia = "";
    my $ciudad = "";
    my $organizacion = "";
    my $orga_red = "";
    my $result;
    my $estado;
    my @fila;
    
    my $sql = "SELECT * FROM gnupanel_usuario_data WHERE id_usuario = (SELECT id_usuario FROM gnupanel_usuario WHERE dominio = '$dominio') ";

    $result = $conexion->exec($sql);
    $estado = $result->resultStatus;
    if($estado == PGRES_TUPLES_OK)
    {
    @fila = $result->fetchrow;
    $pais = $fila[6];
    $provincia = $fila[7];
    $ciudad = $fila[8];
    $organizacion = $fila[5];
    $orga_red = lc($organizacion);
    }
    else
    {
    $pais = "AR";
    $provincia = "none";
    $ciudad = "none";
    $organizacion = "NONE";
    $orga_red = lc($organizacion);
    }
    
    my $borrar = "/bin/rm -f ";
    my $arch_cert = pone_barra(trim($dir_ssl_cert)).formato_numero($id_subdominio)."-".$subdominio.".pem";
    my $arch_cert_apache = pone_barra(trim($dir_ssl_cert)).formato_numero($id_subdominio)."-".$subdominio.".apache.pem";
    my $arch_ssl_eay = "/tmp/".$subdominio;

    my $comando_0 = "/usr/bin/openssl req -config $arch_ssl_eay -new -x509 -nodes -out $arch_cert_apache -keyout $arch_cert_apache -days 365";
    my $comando_1 = "/usr/bin/openssl req -config $arch_ssl_eay -new -x509 -nodes -out $arch_cert -keyout $arch_cert -days 365";

    crea_ssleay ($pais,$provincia,$ciudad,$organizacion,$orga_red,$subdominio,$correo,$arch_ssl_eay);
    system($comando_0);
    system($comando_1);

    chmod(0640,$arch_cert);
    chown(scalar(getpwnam($usuario_dir_apache)),scalar(getgrnam($grupo_dir_apache)),$arch_cert);
    chmod(0400,$arch_cert_apache);
    chown(scalar(getpwnam("root")),scalar(getgrnam($grupo_dir_apache)),$arch_cert_apache);
    $comandar = $borrar.$arch_ssl_eay;
    system($comandar);
    $comandar = $borrar."/tmp/gnupanel.rnd";
    system($comandar);
    }

sub crea_directorio
    {
    my $directorio_in = $_[0];
    my $comando = "/bin/mkdir -p -m 0700 ";
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

    $comando = "/bin/mkdir -p -m 0500 ";

    $dir_agreg = pone_barra($directorio_in)."gnupanel";
    $comandar = $comando.$dir_agreg;
    system($comandar);
    chown(scalar(getpwnam($usuario_dir_apache)),scalar(getgrnam($grupo_dir_apache)),pone_barra($directorio_in)."gnupanel");

    $dir_agreg = pone_barra($directorio_in)."gnupanel/awstats";
    $comandar = $comando.$dir_agreg;
    system($comandar);
    chown(scalar(getpwnam($usuario_dir_apache)),scalar(getgrnam($grupo_dir_apache)),pone_barra($directorio_in)."gnupanel/awstats");
    
    $dir_agreg = pone_barra($directorio_in)."gnupanel/webalizer";
    $comandar = $comando.$dir_agreg;
    system($comandar);
    chown(scalar(getpwnam($usuario_dir_apache)),scalar(getgrnam($grupo_dir_apache)),pone_barra($directorio_in)."gnupanel/webalizer");
    
    $dir_agreg = pone_barra($directorio_in)."webmail";
    $comandar = $comando.$dir_agreg;
    system($comandar);
    chown(scalar(getpwnam($usuario_dir_apache)),scalar(getgrnam($grupo_dir_apache)),pone_barra($directorio_in)."webmail");

    $comando = "/bin/mkdir -p -m 0700 ";
    $dir_agreg = pone_barra($directorio_in)."../tmp/uploads";
    $comandar = $comando.$dir_agreg;
    system($comandar);
    chown(scalar(getpwnam($usuario_dir_apache)),scalar(getgrnam($grupo_dir_apache)),pone_barra($directorio_in)."../tmp");
    chown(scalar(getpwnam($usuario_dir_apache)),scalar(getgrnam($grupo_dir_apache)),pone_barra($directorio_in)."../tmp/uploads");

    $dir_agreg = pone_barra($directorio_in)."../../backup";
    $comandar = $comando.$dir_agreg;
    system($comandar);
    chown(scalar(getpwnam($usuario_dir_apache)),scalar(getgrnam($grupo_dir_apache)),pone_barra($directorio_in)."../../backup");

    $comando = "/bin/chmod 0700 ";
    $dir_agreg = pone_barra($directorio_in)."../tmp";
    $comandar = $comando.$dir_agreg;
    system($comandar);

    my $public_html = pone_barra($directorio_in)."../../public_html";
    if(-l $public_html)
	{
	}
    else
	{
	$comandar = "/bin/ln -s subdominios ".$public_html;
	system($comandar);
	}
    }

sub dame_directorio_superior
    {
    my $directorio_in = $_[0];
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

    my $largo = @data;
    
    for($i=0;$i<$largo-1;$i++)
	{
	if(length($data[$i])>0)
	    {
	    $raiz = $raiz."/".$data[$i];
	    }
	}
    $directorio_in = $raiz;	
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

sub ingresa_en_namevirtualhost
    {
    my $ip_puerto = $_[0];
    my $archivo_nvhosts = $archivo_namevirtualhosts;
    $ip_puerto = trim($ip_puerto);
    open(VIRTHOSTS,">>".$archivo_nvhosts);
    print VIRTHOSTS "NameVirtualHost $ip_puerto \n";
    close VIRTHOSTS;
    }

sub existe_en_namevirtualhost
    {
    my $ip_puerto = $_[0];
    my $archivo_nvhosts = $archivo_namevirtualhosts;
    my $result = 0;
    my $renglon;
    $ip_puerto = "NameVirtualHost ".$ip_puerto;
    open(VIRTHOSTS,$archivo_nvhosts);
    
    while(eof(VIRTHOSTS)==0)
	{
	$renglon = <VIRTHOSTS>;
	if(trim($renglon) eq trim($ip_puerto))
	    {
	    $result = 1;
	    }
	}
    close VIRTHOSTS;
    $result = $result;    
    }

sub limpia_namevirtualhost
    {
    my $conexion = $_[0];
    my $archivo_nvhosts = $archivo_namevirtualhosts;
    my $archivo_nvhosts_temp = $archivo_namevirtualhosts.".tmp";
    open(NVHOSTTEMP,">".$archivo_nvhosts_temp);
    my $sql = "SELECT DISTINCT ip,puerto FROM gnupanel_apacheconf ORDER BY ip,puerto ";
    my $result = $conexion->exec($sql);
    my @fila;

    while(@fila = $result->fetchrow)
	{
	my $ip = $fila[0];
	my $puerto = $fila[1];
	print NVHOSTTEMP "NameVirtualHost $ip:$puerto \n";
	}

    my $comando = "/bin/cp -b -f ".$archivo_nvhosts_temp." ".$archivo_nvhosts;
    close NVHOSTTEMP;
    system($comando);
    }


sub dame_id_dominio_parking
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my $id_dominio = $_[2];

    my $sql = "SELECT DISTINCT id_usuario FROM gnupanel_usuarios_dominios WHERE id = $id_dominio LIMIT 1";
    my $result = $conexion->exec($sql);
    my $retorno = $result->getvalue(0,0);
    $retorno = $retorno;
}


sub dame_usuarios_apache
    {
    my $id_subdominio = $_[0];
    my $conexion = $_[1];
    my $sql = "SELECT userid FROM gnupanel_apache_user WHERE id_subdominio = $id_subdominio";
    my $result = $conexion->exec($sql);
    my $listado = "";
    
    if($result->resultStatus == PGRES_TUPLES_OK)
	{
	my @fila;
	while(@fila = $result->fetchrow)
	    {
	    $listado = $listado.$fila[0]." ";
	    }
	}
    $listado = $listado;
    }

sub configura_apache
    {
    my $documentroot = $_[0];
    my $serveradmin = $_[1];
    my $ip = $_[2];
    my $subdominio = $_[3];
    my $dominio = $_[4];
    my $id_subdominio = $_[5];
    my $tiene_ssl = $_[6];
    my $redirigir = $_[7];
    my $dominio_destino = $_[8];
    my $id_apache = $_[9];
    my $php_register_globals = $_[10];
    my $php_safe_mode = $_[11];
    my $indexar = $_[12];
    my $activo = $_[13];
    my $generar_cert_ssl = $_[14];
    my $conexion = $_[15];
    my $caracteres = $_[16];
    my $id_dominio = $_[17];
    my $estado_in = $_[18];

    $dominio_reseller = "http://suspended.".dominio_reseller_por_dir_sub($documentroot);
    if($estado_in==4)
	{
	    $redirigir = 1;
	    $dominio_destino = $dominio_reseller;
	}

    my $subdominio_conf_apache = "";
    my $subdominio_link_apache = "";
    my $subdominio_cert_apache = "";
    my $result;
    my $servername = "";

    if(length($subdominio)>0)
	{
	$servername = $subdominio.".".$dominio;
	}
    else
	{
	$servername = $dominio;
	}

    if($tiene_ssl==1)
	{
        $subdominio_conf_apache = pone_barra(trim($dir_conf_apache_ssl)).formato_numero($id_subdominio)."-".$servername;
	$subdominio_link_apache = pone_barra(trim($dir_link_apache_ssl)).formato_numero($id_subdominio)."-".$servername;
	$subdominio_cert_apache = pone_barra(trim($dir_ssl_cert)).formato_numero($id_subdominio)."-".$servername.".pem";
	if($generar_cert_ssl==1)
	    {
	    crea_cert_apache($dominio,$servername,$id_subdominio,$serveradmin,$conexion);
	    }
	}
	else
	{
        $subdominio_conf_apache = pone_barra(trim($dir_conf_apache)).formato_numero($id_subdominio)."-".$servername;
	$subdominio_link_apache = pone_barra(trim($dir_link_apache)).formato_numero($id_subdominio)."-".$servername;
	}
	
    open(SKEL_SUBDOMINIO,"$skel_apache_subdominios");
    open(SUBDOMINIO,">".$subdominio_conf_apache);

    my $directorio_stats = $documentroot;

    if($tiene_ssl==1)
	{
	    $directorio_stats =~ s/\/subdominios-ssl\//\/gnupanel\//; 
	}
    else
	{
	    $directorio_stats =~ s/\/subdominios\//\/gnupanel\//; 
	}

    if($tiene_ssl==1)
	{
	if(existe_en_namevirtualhost($ip.":443")==0)
	    {
	    ingresa_en_namevirtualhost($ip.":443");
	    }
	print SUBDOMINIO "<VirtualHost $ip:443>\n";
	print SUBDOMINIO "ServerAdmin $serveradmin \n";
	print SUBDOMINIO "ServerName $servername \n";
	print SUBDOMINIO "DocumentRoot $documentroot \n";
	print SUBDOMINIO "SSLEngine on \n";
	print SUBDOMINIO "SSLCertificateFile $subdominio_cert_apache \n";
	}
    else
	{
	if(existe_en_namevirtualhost($ip.":80")==0)
	    {
	    ingresa_en_namevirtualhost($ip.":80");
	    }
	print SUBDOMINIO "<VirtualHost $ip:80>\n";
	print SUBDOMINIO "ServerAdmin $serveradmin \n";
	print SUBDOMINIO "ServerName $servername \n";
	print SUBDOMINIO "DocumentRoot $documentroot \n";
	}

    if(!($subdominio eq "gnupanel"))
	{
	print SUBDOMINIO "Alias /gnupanel $directorio_stats \n";
	}

    if($indexar==1)
	{
	print SUBDOMINIO "Options +Indexes \n";
	}
    else
	{
	print SUBDOMINIO "Options -Indexes \n";
	}

    my $dir_gnupanel = "/usr/share/gnupanel/gnupanel/";

    print SUBDOMINIO "\n";
    print SUBDOMINIO "\t<Directory $documentroot> \n";
    print SUBDOMINIO "\tAddDefaultCharset $caracteres \n";

    if($dir_gnupanel eq pone_barra($documentroot))
	{
	}
    else
	{

	if($php_safe_mode==1)
	    {
    	    print SUBDOMINIO "\tphp_admin_value safe_mode 1 \n";
	    print SUBDOMINIO "\tphp_admin_value open_basedir ".dame_directorio_superior($documentroot)."/ \n";
	    print SUBDOMINIO "\tphp_admin_value upload_tmp_dir ".dame_directorio_superior($documentroot)."/tmp/uploads/ \n";
	    print SUBDOMINIO "\tphp_admin_value session.save_path ".dame_directorio_superior($documentroot)."/tmp/ \n";
	    print SUBDOMINIO "\tphp_admin_value suhosin.executor.func.blacklist ".$funciones_prohibidas." \n";
	    print SUBDOMINIO "\tSetEnv TMPDIR ".dame_directorio_superior($documentroot)."/tmp/ \n";
	    }	
	else
	    {
    	    print SUBDOMINIO "\tphp_admin_value safe_mode 0 \n";
	    print SUBDOMINIO "\tphp_admin_value open_basedir ".dame_directorio_superior($documentroot)."/ \n";
	    print SUBDOMINIO "\tphp_admin_value upload_tmp_dir ".dame_directorio_superior($documentroot)."/tmp/uploads/ \n";
	    print SUBDOMINIO "\tphp_admin_value session.save_path ".dame_directorio_superior($documentroot)."/tmp/ \n";
	    print SUBDOMINIO "\tphp_admin_value suhosin.executor.func.blacklist ".$funciones_prohibidas." \n";
	    print SUBDOMINIO "\tSetEnv TMPDIR ".dame_directorio_superior($documentroot)."/tmp/ \n";
	    }	

	if($php_register_globals==1)
	    {
	    print SUBDOMINIO "\tphp_flag register_globals 1 \n";
	    }
	else
	    {
	    print SUBDOMINIO "\tphp_flag register_globals 0 \n";
	    }    
	}
    print SUBDOMINIO "\t</Directory> \n";

    if($redirigir==1)
	{
	    print SUBDOMINIO "Redirect / $dominio_destino \n";
	}

    my $sql = "SELECT directorio,descripcion FROM gnupanel_apache_dir_prot WHERE id_subdominio = $id_apache";
    $result = $conexion->exec($sql);
    my $estado = $result->resultStatus;
    if($estado == PGRES_TUPLES_OK)
	{
	my @fila;
	while(@fila = $result->fetchrow)
	    {
	    my $directorio_prot = pone_barra($documentroot);
	    my $caracter = index($fila[0],"/");
	    if($caracter == 0)
		{
		$directorio_prot = $directorio_prot.substr($fila[0],1);
		}
	    else
		{
		$directorio_prot = $directorio_prot.$fila[0];
		}
	    
	    my $nombre_proteccion = $fila[1];
	    my $lista_usuarios = dame_usuarios_apache($id_apache,$conexion);

	    if($directorio_prot eq pone_barra($documentroot)."gnupanel")
		{
		print SUBDOMINIO "\n";
		print SUBDOMINIO "\t<Directory $directorio_stats> \n";
		print SUBDOMINIO "\tAuthName \"$nombre_proteccion\" \n";
		print SUBDOMINIO "\tAuthType Basic \n";
		print SUBDOMINIO "\trequire user $lista_usuarios \n";
		print SUBDOMINIO "\t</Directory> \n";
		print SUBDOMINIO "\n";
		}
	    else
		{
		print SUBDOMINIO "\n";
		print SUBDOMINIO "\t<Directory $directorio_prot> \n";
		print SUBDOMINIO "\tAuthName \"$nombre_proteccion\" \n";
		print SUBDOMINIO "\tAuthType Basic \n";
		print SUBDOMINIO "\trequire user $lista_usuarios \n";
		print SUBDOMINIO "\t</Directory> \n";
		print SUBDOMINIO "\n";
		}

	    #crea_directorio($directorio_prot);
	    }
	}

    $sql = "SELECT dir_indexes FROM gnupanel_apache_otras_conf WHERE id_subdominio = $id_apache";
    $result = $conexion->exec($sql);
    my $estado = $result->resultStatus;
    if($estado == PGRES_TUPLES_OK)
	{
	my @fila;
	while(@fila = $result->fetchrow)
	    {
	    my $directorio_indexes = pone_barra($documentroot);
	    my $caracter = index($fila[0],"/");
	    if($caracter == 0)
		{
		$directorio_indexes = $directorio_indexes.substr($fila[0],1);
		}
	    else
		{
		$directorio_indexes = $directorio_indexes.$fila[0];
		}
	    
	    print SUBDOMINIO "\n";
	    print SUBDOMINIO "\t<Directory $directorio_indexes> \n";
	    print SUBDOMINIO "\tOptions +Indexes \n";
	    print SUBDOMINIO "\t</Directory> \n";
	    print SUBDOMINIO "\n";
	    crea_directorio($directorio_prot);
	    }
	}

    if($subdominio eq "gnupanel")
	{
	}
    else
	{
	while(eof(SKEL_SUBDOMINIO)==0)
	    {
	    $renglon = <SKEL_SUBDOMINIO>;
	    print SUBDOMINIO "$renglon" ;
	    }
	}

    #if(($subdominio eq "gnupanel") && ($tiene_ssl==1))
    if($subdominio eq "gnupanel")
	{
	open(SKEL_GNUPANEL,$skel_apache_gnupanel);

	while(eof(SKEL_GNUPANEL)==0)
	    {
	    $renglon = <SKEL_GNUPANEL>;
	    print SUBDOMINIO "$renglon" ;
	    }
	
	close SKEL_GNUPANEL;
	}

    my $log_apache = $dominio;

    if(length($subdominio) > 0)
	{
	$log_apache = $subdominio.".".$log_apache;
	}

    if($tiene_ssl == 1)
	{
	$log_apache = "ssl.".$log_apache;
	}

    my $log_apache_dir_webalizer = "/var/log/apache2/webalizer/".$log_apache.".log";
    my $log_apache_dir_awstats = "/var/log/apache2/awstats/".$log_apache.".log";
    
    print SUBDOMINIO "LogFormat \"$id_dominio %B\" $log_apache \n";

    print SUBDOMINIO "CustomLog $log_apache_dir_webalizer combined \n";
    print SUBDOMINIO "CustomLog $log_apache_dir_awstats combined \n";
    
    print SUBDOMINIO "CustomLog $log_transferencia $log_apache \n";
    print SUBDOMINIO "</VirtualHost> \n";
    
    close SUBDOMINIO;
    close SKEL_SUBDOMINIO;

    if($dir_gnupanel eq pone_barra($documentroot))
	{
	}
    else
	{
	if(checkea_directorio($documentroot) == 1)
	    {
	    crea_directorio($documentroot);
	    }
	}
	
    if($activo==1)
	{
        symlink($subdominio_conf_apache,$subdominio_link_apache) ;
	}
    else
	{
	#my $comandito = "/bin/rm -f ".$subdominio_link_apache;
	#system($comandito);
        symlink($subdominio_conf_apache,$subdominio_link_apache) ;
	}

    genera_index($subdominio,$dominio,$documentroot,$conexion);
    $sql = "UPDATE gnupanel_apacheconf SET estado = 9 WHERE id_subdominio = $id_subdominio AND es_ssl = $tiene_ssl ";
    $result = $conexion->exec($sql);
    }

sub hay_algo_que_configurar
    {
    my $conexion = $_[0];
    my $sql = "SELECT configurar FROM apache_dominios_conf WHERE id = 1 AND configurar = 1";
    my $result = $conexion->exec($sql);
    my $retorno = 0;

    if($result->ntuples > 0)
	{
	$retorno = 1;
	$sql = "UPDATE apache_dominios_conf SET configurar = 0 WHERE id = 1 ";
	$result = $conexion->exec($sql);
	}
    $retorno = $retorno;
    }

sub desconfigura_apache
    {
    my $id_subdominio = $_[0];
    my $documentroot = $_[1];
    my $tiene_ssl = $_[2];
    my $conexion = $_[3];

    open(MENSDIR,">> $logueo");
    my $comando = "/bin/rm -f ";
    my $comandar = "";
    my $checkeo = NULL;

    my $servername = "";

    if(length($subdominio)>0)
	{
	$servername = $subdominio.".".$dominio;
	}
    else
	{
	$servername = $dominio;
	}

    my $subdominio_conf_apache = "";
    my $subdominio_link_apache = "";
    my $subdominio_cert_apache = "";

    my $arch_cert_apache = "";

    if($tiene_ssl==1)
	{
        $subdominio_conf_apache = pone_barra(trim($dir_conf_apache_ssl)).formato_numero($id_subdominio)."-*";
	$subdominio_link_apache = pone_barra(trim($dir_link_apache_ssl)).formato_numero($id_subdominio)."-*";
	$subdominio_cert_apache = pone_barra(trim($dir_ssl_cert)).formato_numero($id_subdominio)."-*";
	$arch_cert_apache = pone_barra(trim($dir_ssl_cert)).formato_numero($id_subdominio)."-*";
	$comandar = $comando.$subdominio_cert_apache;
	system($comandar);	
	$comandar = $comando.$arch_cert_apache;
	system($comandar);
	$comandar = $comando.$subdominio_link_apache;
	system($comandar);
	$comandar = $comando.$subdominio_conf_apache;
	system($comandar);
	}
	else
	{
        $subdominio_conf_apache = pone_barra(trim($dir_conf_apache)).formato_numero($id_subdominio)."-*";
	$subdominio_link_apache = pone_barra(trim($dir_link_apache)).formato_numero($id_subdominio)."-*";
	$comandar = $comando.$subdominio_link_apache;
	system($comandar);
	$comandar = $comando.$subdominio_conf_apache;
	system($comandar);
	}

    my $dir_gnupanel = "/usr/share/gnupanel/gnupanel/";

    if($dir_gnupanel eq pone_barra($documentroot))
	{
	}
    else
	{
	if (-e $documentroot)
	    {
	    my $comandar = $comando."-r ".$documentroot;
	    #system($comandar);
	    }    
	}
    close MENSDIR;
    }

sub cuales_hay_que_configurar
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my @fila;
    my @fila_0;
    open(MENSDIR,">> $logueo");
    my $result = NULL;
    my $result_0 = NULL;
    
    
    my $sql = "SELECT * FROM gnupanel_apacheconf WHERE estado <> 9";
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
	my $id_dominio = $fila[2]*1;
	my $tiene_ssl = $fila[10];
	my $estado = $fila[14];
	my $redirigir = $fila[8];
	my $dominio_destino = $fila[9];
	my $id_apache = $fila[0];
	my $php_register_globals = $fila[11];
	my $php_safe_mode = $fila[12];
	my $indexar = $fila[13];
	my $activo = $fila[15];
	my $caracteres = $fila[16];
	
	
	my $sql_sub_0 = "(SELECT id_servidor FROM gnupanel_servidores WHERE servidor = '$nombre_servidor')";
	my $sql_sub_1 = "(SELECT ip_publica FROM gnupanel_ips_servidor WHERE CASE WHEN id_servidor = $sql_sub_0 THEN ip_publica = '$ip' OR ip_privada = '$ip' ELSE false END)";

	#$sql = "SELECT dominio FROM gnupanel_usuario WHERE id_usuario = $id_dominio AND EXISTS $sql_sub_1 ";
	$sql = "SELECT name FROM gnupanel_pdns_domains WHERE id = $id_dominio ";
	$result_0 = $conexion->exec($sql);

	if($id_dominio >= 10000000)
	{
	    $id_dominio = dame_id_dominio_parking($conexion,$logueo,$id_dominio);
	}

	if($result_0->resultStatus == PGRES_TUPLES_OK)
	    {
	    @fila_0 = $result_0->fetchrow;
	    my $dominio = $fila_0[0];
	    if($estado == 0) {configura_apache($directorio,$server_admin,$ip,$subdominio,$dominio,$id_subdominio,$tiene_ssl,$redirigir,$dominio_destino,$id_apache,$php_register_globals,$php_safe_mode,$indexar,$activo,1,$conexion,$caracteres,$id_dominio,$estado);$reloaded=1;}
	    if($estado == 1) {configura_apache($directorio,$server_admin,$ip,$subdominio,$dominio,$id_subdominio,$tiene_ssl,$redirigir,$dominio_destino,$id_apache,$php_register_globals,$php_safe_mode,$indexar,$activo,0,$conexion,$caracteres,$id_dominio,$estado);$reloaded=1;}
	    if($estado == 2) {regenera_certificado_ssl($directorio,$server_admin,$ip,$subdominio,$dominio,$id_subdominio,$tiene_ssl,$conexion);$reloaded=1;}
	    if($estado == 4) {configura_apache($directorio,$server_admin,$ip,$subdominio,$dominio,$id_subdominio,$tiene_ssl,$redirigir,$dominio_destino,$id_apache,$php_register_globals,$php_safe_mode,$indexar,$activo,0,$conexion,$caracteres,$id_dominio,$estado);$reloaded=1;}
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

sub cuales_hay_que_desconfigurar
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my @fila;
    my @fila_0;
    open(MENSDIR,">> $logueo");
    my $result = NULL;
    my $result_0 = NULL;
    
    my $sql = "SELECT * FROM gnupanel_apachedesconf";
    $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
    {
    while(@fila = $result->fetchrow)
	{
	my $directorio = $fila[5];
	my $server_admin = $fila[6];
	my $ip = $fila[3];
	my $subdominio = $fila[2];
	my $id_subdominio = $fila[0];
	my $id_dominio = $fila[1];
	my $tiene_ssl = $fila[9];
	my $estado = $fila[10];
	$sql = "SELECT documentroot FROM gnupanel_apacheconf WHERE id_subdominio = $id_subdominio AND es_ssl = $tiene_ssl ";
	$result_0 = $conexion->exec($sql);
	if($result_0->resultStatus == PGRES_TUPLES_OK)
	    {
	    
	    if($result_0->ntuples==0)
	    {
	    desconfigura_apache($id_subdominio,$directorio,$tiene_ssl,$conexion);
	    }

	    $sql = "DELETE FROM gnupanel_apachedesconf WHERE id_subdominio = $id_subdominio ";
	    $result_0 = $conexion->exec($sql);
	    $reloaded = 1;	    
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

sub genera_index
{
    my $subdominio = $_[0];
    my $dominio = $_[1];
    my $directorio = $_[2];
    my $conexion = $_[3];
    $archivo_index = pone_barra($directorio)."index.html";
    $archivo_index_1 = pone_barra($directorio)."index.htm";
    $archivo_index_2 = pone_barra($directorio)."index.php";
    $archivo_index_3 = pone_barra($directorio)."index.xhtml";
    my $sql = "SELECT idioma FROM gnupanel_usuario_lang WHERE id_usuario = (SELECT id_usuario FROM gnupanel_usuario WHERE dominio = '$dominio') ";
    my $result = $conexion->exec($sql);
    my $idioma = $result->getvalue(0,0);
    
    $archivo_index_default = "/usr/local/gnupanel/lang/".$idioma."/index-default.txt";    

    my $renglon;
    my $subdominio_E;
    
    if(length($subdominio)>0)
	{
	$subdominio_E = $subdominio.".".$dominio;
	}
    else
	{
	$subdominio_E = $dominio;
	}


    if( (-e $archivo_index) || (-e $archivo_index_1) || (-e $archivo_index_2) || (-e $archivo_index_3) )
    {
    
    }    
    else
    {
    open(INDICE,"> $archivo_index");
    open(LECTURA,$archivo_index_default);

    while(eof(LECTURA)==0)
	{
	$renglon = <LECTURA>;
	$renglon =~ s/_SUBDOMINIO_/$subdominio_E/ ;
	print INDICE $renglon;
	}

    close LECTURA;
    close INDICE;

    $comando = "/bin/chown ".$usuario_dir_apache.":".$grupo_dir_apache." ".$archivo_index;
    system($comando);
    $comando = "/bin/chmod 600 ".$archivo_index;
    system($comando);
    }
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
	    print "Inicializando el demonio de configuracion de apache \n";
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
		    undef $conexion;
		    $conexion = NULL;
		    $conexion = Pg::connectdb("dbname=$database host=localhost user=$userdb password=$pasaportedb");
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
		    undef $conexion;
		    $conexion = NULL;
		    $conexion = Pg::connectdb("dbname=$database host=localhost user=$userdb password=$pasaportedb");
		    }

		if($reloaded == 1)
		    {
		    limpia_namevirtualhost($conexion);
		    my $comandar = "/etc/init.d/apache2 reload 1>/dev/null 2>/dev/null ";
		    system($comandar);
		    $reloaded = 0;
		    }
		}

#fin del programa
	}
    
##$end = 0;

###############################################################################################################################################################
