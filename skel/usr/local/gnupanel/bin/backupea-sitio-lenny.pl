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

sub fin_backup
{
    my $conexion = $_[0];
    my $id_usuario = $_[1];
    
    my @fila;

    my $result = NULL;
    
    my $sql = "SELECT reseller,dominio FROM gnupanel_reseller WHERE id_reseller = (SELECT cliente_de FROM gnupanel_usuario WHERE id_usuario = $id_usuario ) ";
    $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
	{
	@fila = $result->fetchrow;
	my $correo_reseller = $fila[0]."\@".$fila[1];
	my @fila_1;
	my $result_1;
	$sql = "SELECT correo_contacto FROM gnupanel_usuario WHERE id_usuario = $id_usuario ";
	$result_1 = $conexion->exec($sql);
	@fila_1 = $result_1->fetchrow;
	my $correo_usuario = $fila_1[0];
	my @correo_e = dame_mensaje_usuario($conexion,$logueo,$id_usuario,"fin-backup-usuario.txt");
	my $asunto = $correo_e[0];
	my $mensaje = $correo_e[1];
	envia_mensaje($correo_reseller,$correo_usuario,$asunto,$mensaje);
	}    
}

sub dame_directorio_correos
{
    my $conexion = $_[0];
    my $id_usuario = $_[1];
    my $dominios_in = $_[2];
    
    my @fila;

    my $result = NULL;
    
    my $sql = "SELECT reseller,dominio,cliente_de FROM gnupanel_reseller WHERE id_reseller = (SELECT cliente_de FROM gnupanel_usuario WHERE id_usuario = $id_usuario ) ";
    $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
	{
	@fila = $result->fetchrow;
	my $correo_reseller = $fila[0]."\@".$fila[1];
	my $id_admin = $fila[2];
	my @fila_1;
	my $result_1;
	$sql = "SELECT admin FROM gnupanel_admin WHERE id_admin = $id_admin ";
	$result_1 = $conexion->exec($sql);
	@fila_1 = $result_1->fetchrow;
	my $admin = $fila_1[0];
	$result = pone_barra($directorio_raiz_correo).$admin."/".$correo_reseller."/".$dominios_in;
	}
    $result = $result;
}

sub dame_base_datos_pg
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my $id_usuario = $_[2];
    my @fila;
    my $result;
    my @retorno;
    my $retornar;
    my $sql = "SELECT nombre_base FROM gnupanel_bases_de_datos WHERE id_dueno = $id_usuario AND id_tipo_base = 0";
    my $i = 0;
    $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
    {
	while(@fila = $result->fetchrow)
	{
	    $retorno[$i] = $fila[0];
	    #print $retorno[$i]."\n";
	    $i = $i + 1;
	}
    }
    $retornar = join(",",@retorno) ;
}

sub dame_base_datos_my
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my $id_usuario = $_[2];
    my @fila;
    my $result;
    my @retorno;
    my $retornar;
    my $sql = "SELECT nombre_base FROM gnupanel_bases_de_datos WHERE id_dueno = $id_usuario AND id_tipo_base = 1";
    my $i = 0;
    $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
    {
	while(@fila = $result->fetchrow)
	{
	    $retorno[$i] = $fila[0];
	    $i = $i + 1;
	}
    }
    $retornar = join(",",@retorno) ;
}

sub crea_directorio
    {
    my $directorio_in = $_[0];
    my $comando = "/bin/mkdir -p -m 0750 ";
    
    my $comandar = "";
    
    my $borrar = "/bin/rm -f -r ".$directorio_in;
    system($borrar);

    $comandar = $comando.pone_barra($directorio_in)."files";
    system($comandar);

    $comandar = $comando.pone_barra($directorio_in)."databases/postgres";
    system($comandar);

    $comandar = $comando.pone_barra($directorio_in)."databases/mysql";
    system($comandar);
    }

sub copiar_sitios
{
$directorio = $_[0];
$dir_backup = $_[1];
$comando = "/bin/cp -f -r ".$directorio." ".$dir_backup;
system($comando);
}


#######################################################################################################################
## Put ourselves in background if not in debug mode. 
require "/etc/gnupanel/gnupanel.conf.pl";

$dominio = $ARGV[0];
$id_usuario = $ARGV[1];
$directorio = $ARGV[2];
$por_ftp = $ARGV[3];
$nombre_archivo = $ARGV[4];

$control = 0;
$conexion = NULL;
$conexion = Pg::connectdb("dbname=$database host=localhost user=$userdb password=$pasaportedb");
	    
$estado = $conexion->status;
my $dir_backup = pone_barra($directorio_backup).$dominio;
my $dir_ftp = pone_barra($directorio)."backup";

$directorio_de_correos = dame_directorio_correos($conexion,$id_usuario,$dominio);

$comando = "/usr/bin/sudo /usr/local/gnupanel/bin/habilita-dir.pl $directorio $dir_ftp 9999 $directorio_de_correos $dir_backup ";
$control = $control + system($comando);

if($por_ftp == 1)
    {
    $comando = "/bin/rm -f ".pone_barra($dir_ftp).$dominio."-*-backup.tar.gz";
    $control = $control + system($comando);
    }

crea_directorio($dir_backup);
copiar_sitios($directorio,$dir_backup."/files");

$comando = "/usr/bin/sudo /usr/local/gnupanel/bin/habilita-dir.pl $directorio $dir_ftp 2 $directorio_de_correos $dir_backup ";
$control = $control + system($comando);

if($estado==PGRES_CONNECTION_OK)
    {
    
    @mysql = split(",",dame_base_datos_my($conexion,$logueo,$id_usuario));
    my $largo = @mysql;
    for($i=0;$i<$largo;$i++)
	{
	my $base_mysql = trim($mysql[$i]);
	my $fila = $dir_backup."/databases/mysql/".$base_mysql.".sql";
	my $comandar = "/usr/bin/mysqldump -c -u root --password=$pasaportedbmysql -r $fila $base_mysql ";
	$control = $control + system($comandar);
	}

    @postgres = split(",",dame_base_datos_pg($conexion,$logueo,$id_usuario));
    my $largo = @postgres;
    for($i=0;$i<$largo;$i++)
	{
	$ENV{'PGUSER'} = $userdb;
	$ENV{'PGPASSWORD'} = $pasaportedb;
	my $base_postgres = trim($postgres[$i]);
	my $fila = $dir_backup."/databases/postgres/".$base_postgres.".sql";
	my $comandar = "/usr/bin/pg_dump -Fc -O -f $fila $base_postgres ";
	$control = $control + system($comandar);
	}
    }

$comandar = "/bin/tar --remove-files -c -O -z -C $directorio_backup $dominio";

$archivo = `$comandar`;

if($por_ftp == 1)
    {
    $comando = "/bin/mkdir -p ".$dir_ftp;
    $control = $control + system($comando);
    
    my $destino = pone_barra($dir_ftp).$nombre_archivo ;
    
    open(RESPALDO,">$destino");
    print RESPALDO $archivo;
    close RESPALDO;
    $comando = "chmod 640 $destino ";
    $control = $control + system($comando);
    fin_backup($conexion,$id_usuario);
    }
    else
    {
    print $archivo;
    }

$comando = "/usr/bin/sudo /usr/local/gnupanel/bin/deshabilita-dir.sh $directorio $dir_ftp backup ";
$control = $control + system($comando);

#fin del programa

##$end = 0;

###############################################################################################################################################################
