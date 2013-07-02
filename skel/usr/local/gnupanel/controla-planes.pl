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

sub mide_directorio
    {
    my $directorio = $_[0];
    my $logueo = $_[1];
    my $comando = "/usr/bin/du -m -s ";
    open(MENSDIR,">> $logueo");
    
    my $comandar = $comando.$directorio;
    my $directorios = `$comandar`;
    my $mensaje;
    my @datos = NULL;
    @datos = split(" ",$directorios);
    my $tamano = $datos[0];
    close MENSDIR;
    $tamano = $tamano;
    }

sub dame_dominio_usuario
    {
    my $conexion = $_[0];
    my $logueo = $_[1];
    my $id_usuario = $_[2];

    my $sql = "SELECT dominio FROM gnupanel_usuario WHERE id_usuario = $id_usuario ";
    my $result = $conexion->exec($sql);
    my $dominio = $result->getvalue(0,0);

    $dominio = $dominio;
    }

sub dame_dominio_reseller
    {
    my $conexion = $_[0];
    my $logueo = $_[1];
    my $id_reseller = $_[2];

    my $sql = "SELECT dominio FROM gnupanel_reseller WHERE id_reseller = $id_reseller ";
    my $result = $conexion->exec($sql);
    my $dominio = $result->getvalue(0,0);

    $dominio = $dominio;
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
    $result[0] = $result[0]." - ".dame_dominio_reseller($conexion,$logueo,$id_reseller);

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
    $result[0] = $result[0]." - ".dame_dominio_usuario($conexion,$logueo,$id_usuario);

    @result = @result;    
    }

sub suspender_usuario
    {
    my $conexion = $_[0];
    my $id_usuario = $_[1];
    my $result = NULL;
    my $control = 0;
    my $sql = "BEGIN ";
    $result = $conexion->exec($sql);


    if($result->resultStatus == PGRES_COMMAND_OK)
	{
	$control = 0;
	}
    else
	{
	$control = 1;
	}

    $sql = "UPDATE gnupanel_usuario SET active = 0 WHERE id_usuario = $id_usuario AND active = 1 ";
    $result = $conexion->exec($sql);

    if($result->resultStatus == PGRES_COMMAND_OK)
	{
	}
    else
	{
	$control = $control + 1;
	}

    $sql = "UPDATE gnupanel_apacheconf SET active = 0,estado=4 WHERE id_dominio = $id_usuario AND active = 1 ";
    $result = $conexion->exec($sql);

    if($result->resultStatus == PGRES_COMMAND_OK)
	{
	}
    else
	{
	$control = $control + 1;
	}

    $sql = "UPDATE gnupanel_postfix_mailuser SET active = 0, passwd = ('!' || passwd) WHERE id_dominio = $id_usuario AND active = 1 ";
    $result = $conexion->exec($sql);

    if($result->resultStatus == PGRES_COMMAND_OK)
	{
	}
    else
	{
	$control = $control + 1;
	}

    $sql = "UPDATE gnupanel_proftpd_ftpuser SET active = 0, passwd = ('!' || passwd) WHERE id_dominio = $id_usuario AND active = 1 ";
    $result = $conexion->exec($sql);

    if($result->resultStatus == PGRES_COMMAND_OK)
	{
	}
    else
	{
	$control = $control + 1;
	}

    if($control == 0)
	{
	$sql = "END";
	$result = $conexion->exec($sql);
	}
    else
	{
	$sql = "ROLLBACK";
	$result = $conexion->exec($sql);
	}
    }

sub habilitar_usuario
    {
    my $conexion = $_[0];
    my $id_usuario = $_[1];
    my $result = NULL;
    my $control = 0;
    my $sql = "BEGIN ";
    $result = $conexion->exec($sql);

    if($result->resultStatus == PGRES_COMMAND_OK)
	{
	$control = 0;
	}
    else
	{
	$control = 1;
	}

    $sql = "UPDATE gnupanel_usuario SET active = 1, password = ltrim(password,'!') WHERE id_usuario = $id_usuario AND active = 0 ";
    $result = $conexion->exec($sql);

    if($result->resultStatus == PGRES_COMMAND_OK)
	{
	}
    else
	{
	$control = $control + 1;
	}

    $sql = "UPDATE gnupanel_apacheconf SET active = 1, estado = 1 WHERE id_dominio = $id_usuario AND active = 0 ";
    $result = $conexion->exec($sql);

    if($result->resultStatus == PGRES_COMMAND_OK)
	{
	}
    else
	{
	$control = $control + 1;
	}

    $sql = "UPDATE gnupanel_postfix_mailuser SET active = 1, passwd = ltrim(passwd,'!') WHERE id_dominio = $id_usuario AND active = 0 ";
    $result = $conexion->exec($sql);

    if($result->resultStatus == PGRES_COMMAND_OK)
	{
	}
    else
	{
	$control = $control + 1;
	}

    $sql = "UPDATE gnupanel_proftpd_ftpuser SET active = 1, passwd = ltrim(passwd,'!') WHERE id_dominio = $id_usuario AND active = 0 ";
    $result = $conexion->exec($sql);

    if($result->resultStatus == PGRES_COMMAND_OK)
	{
	}
    else
	{
	$control = $control + 1;
	}

    if($control == 0)
	{
	$sql = "END";
	$result = $conexion->exec($sql);
	}
    else
	{
	$sql = "ROLLBACK";
	$result = $conexion->exec($sql);
	}
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
	$destinatario = $fila[3];;
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

sub controla_resellers
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my @fila;

    open(MENSDIR,">> $logueo");
    my $result = NULL;
    
    my $sql = "SELECT id_reseller,reseller,dominio FROM gnupanel_reseller ORDER BY id_reseller LIMIT 1 ";
    $result = $conexion->exec($sql);
    @fila = $result->fetchrow;
    my $reseller_principal = $fila[0];
    my $correo_admin = $fila[1]."\@".$fila[2];

    $sql = "SELECT id_admin,politica_de_suspencion FROM gnupanel_admin_sets WHERE id_admin = (SELECT id_admin FROM gnupanel_admin WHERE admin = 'admin' ) ";
    $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
    {
    @fila = $result->fetchrow;
    my $id_admin = $fila[0];
    my $politica_de_suspencion = $fila[1];
    
    if($politica_de_suspencion >= 0)
	{
	$sql = "SELECT id_reseller,correo_contacto FROM gnupanel_reseller WHERE id_reseller <> $reseller_principal ";
	$result = $conexion->exec($sql);
	if($result->resultStatus == PGRES_TUPLES_OK)
	    {
	    my @fila_1;
	    while(@fila_1 = $result->fetchrow)
		{
		my $id_reseller = $fila_1[0];
		my $correo_contacto = $fila_1[1];
		my $result_1;
		$sql = "SELECT espacio,transferencia FROM gnupanel_reseller_plan WHERE id_reseller = $id_reseller ";
		$result_1 = $conexion->exec($sql);
		if($result_1->resultStatus == PGRES_TUPLES_OK)
		    {
		    my @fila_2;
		    my $result_2;
		    @fila_2 = $result_1->fetchrow;
		    my $espacio_disponible = $fila_2[0] * (1+($politica_de_suspencion/100));
		    my $transferencia_disponible = $fila_2[1] * 1048576 * (1+($politica_de_suspencion/100));
		    my $espacio_usado = 0;
		    my $transferencia_asada = 0;
		    my $espacio_disponible_alerta = $espacio_disponible * 9/10;
		    my $transferencia_disponible_alerta = $transferencia_disponible * 9/10;
		    $sql = "SELECT sum(total) FROM gnupanel_espacio WHERE dueno = $id_reseller ";
		    $result_2 = $conexion->exec($sql);
		    if($result_2->resultStatus == PGRES_TUPLES_OK)
			{
			@fila_2 = $result_2->fetchrow;
			$espacio_usado = $fila_2[0];
			}

		    $sql = "SELECT sum(total) FROM gnupanel_transferencias WHERE dueno = $id_reseller ";
		    $result_2 = $conexion->exec($sql);
		    if($result_2->resultStatus == PGRES_TUPLES_OK)
			{
			@fila_2 = $result_2->fetchrow;
			$transferencia_usada = $fila_2[0];
			}

		    if(($espacio_disponible_alerta<$espacio_usado) || ($transferencia_disponible_alerta<$transferencia_usada))
			{
			my $asunto = "";
			my $mensaje = "";
			if(($espacio_disponible<$espacio_usado) || ($transferencia_disponible<$transferencia_usada))
			    {
			    my @correo_e = dame_mensaje_reseller($conexion,$logueo,$id_reseller,"reseller-exedido-reseller.txt");
			    $asunto = $correo_e[0];
			    $mensaje = $correo_e[1];
			    envia_mensaje($correo_admin,$correo_contacto,$asunto,$mensaje);

			    @correo_e = dame_mensaje_admin($conexion,$logueo,$id_admin,"reseller-exedido-admin.txt");
			    $asunto = $correo_e[0];
			    $mensaje = $correo_e[1];
			    envia_mensaje($correo_admin,$correo_admin,$asunto,$mensaje);
			    }
			else
			    {
			    my @correo_e = dame_mensaje_reseller($conexion,$logueo,$id_reseller,"reseller-cercalimite-reseller.txt");
			    $asunto = $correo_e[0];
			    $mensaje = $correo_e[1];
			    envia_mensaje($correo_admin,$correo_contacto,$asunto,$mensaje);
			    }    
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
    }
    close MENSDIR;
}

sub controla_usuarios
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my @fila;

    open(MENSDIR,">> $logueo");
    my $result = NULL;
    
    my $sql = "SELECT id_reseller,reseller,dominio FROM gnupanel_reseller ";
    $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
	{

	while(@fila = $result->fetchrow)
	    {
	    my $id_reseller = $fila[0];
	    my $correo_reseller = $fila[1]."\@".$fila[2];
	    my $result_1 = NULL;
	    my @fila_1;
	    $sql = "SELECT politica_de_suspencion FROM gnupanel_reseller_sets WHERE id_reseller = $id_reseller ";
	    $result_1 = $conexion->exec($sql);
	    @fila_1 = $result_1->fetchrow;
	    my $politica_de_suspencion = $fila_1[0];
	    
	    $sql = "SELECT id_usuario FROM gnupanel_usuario WHERE cliente_de = $id_reseller ORDER BY id_usuario LIMIT 1 ";
	    $result_1 = $conexion->exec($sql);
	    @fila_1 = $result_1->fetchrow;
	    my $id_usuario_principal = $fila_1[0];

	    if($politica_de_suspencion >= 0)
		{
		$sql = "SELECT id_usuario,correo_contacto FROM gnupanel_usuario WHERE cliente_de = $id_reseller AND id_usuario <> $id_usuario_principal AND active = 1 ";
		my $result_2 = $conexion->exec($sql);
		my @fila_2;
		
		if($result_2->resultStatus == PGRES_TUPLES_OK)
		    {
		    while(@fila_2 = $result_2->fetchrow)
			{
			my $id_usuario = $fila_2[0];
			my $correo_usuario = $fila_2[1];
			my $coeficiente = 1 + ($politica_de_suspencion/100);

			$sql = "SELECT total,tope FROM gnupanel_espacio WHERE id_dominio = $id_usuario ";
			my $result_3 = $conexion->exec($sql);
			my @fila_3 = $result_3->fetchrow;

			my $espacio_usado = $fila_3[0];
			my $espacio_disponible = $fila_3[1] * $coeficiente;
			my $espacio_disponible_alerta = $espacio_disponible * 9/10;

			if($espacio_usado > $espacio_disponible)
			    {
			    
			    my @correo_e = dame_mensaje_usuario($conexion,$logueo,$id_usuario,"usuario-exedido-usuario.txt");
			    my $asunto = $correo_e[0];
			    my $mensaje = $correo_e[1];
			    suspender_usuario($conexion,$id_usuario);
			    envia_mensaje($correo_reseller,$correo_usuario,$asunto,$mensaje);
			    
			    @correo_e = dame_mensaje_reseller($conexion,$logueo,$id_reseller,"usuario-exedido-reseller.txt");
			    $asunto = $correo_e[0];
			    $mensaje = $correo_e[1];
			    envia_mensaje($correo_reseller,$correo_reseller,$asunto,$mensaje);
			    
			    }
			else
			    {
			    if($espacio_usado > $espacio_disponible_alerta)
				{
				my @correo_e = dame_mensaje_usuario($conexion,$logueo,$id_usuario,"usuario-cercalimite-usuario.txt");
				my $asunto = $correo_e[0];
				my $mensaje = $correo_e[1];
				envia_mensaje($correo_reseller,$correo_usuario,$asunto,$mensaje);
				}
			    }

			$sql = "SELECT total,tope FROM gnupanel_transferencias WHERE id_dominio = $id_usuario ";
			$result_3 = $conexion->exec($sql);
			@fila_3 = $result_3->fetchrow;

			my $transferencia_usado = $fila_3[0];
			my $transferencia_disponible = $fila_3[1] * $coeficiente;
			my $transferencia_disponible_alerta = $transferencia_disponible * 9/10;
			
			if($transferencia_usado > $transferencia_disponible)
			    {

			    my @correo_e = dame_mensaje_usuario($conexion,$logueo,$id_usuario,"usuario-exedidotransf-usuario.txt");
			    my $asunto = $correo_e[0];
			    my $mensaje = $correo_e[1];
			    suspender_usuario($conexion,$id_usuario);
			    envia_mensaje($correo_reseller,$correo_usuario,$asunto,$mensaje);
			
			    @correo_e = dame_mensaje_reseller($conexion,$logueo,$id_reseller,"usuario-exedidotransf-reseller.txt");
			    $asunto = $correo_e[0];
			    $mensaje = $correo_e[1];
			    envia_mensaje($correo_reseller,$correo_reseller,$asunto,$mensaje);
			    }
			else
			    {
			    if($transferencia_usado > $transferencia_disponible_alerta)
				{
				my @correo_e = dame_mensaje_usuario($conexion,$logueo,$id_usuario,"usuario-cercalimitetransf-usuario.txt");
				my $asunto = $correo_e[0];
				my $mensaje = $correo_e[1];
				envia_mensaje($correo_reseller,$correo_usuario,$asunto,$mensaje);
				}
			    }
			}
		    }
		}
		
	    $sql = "SELECT id_usuario,correo_contacto FROM gnupanel_usuario WHERE cliente_de = $id_reseller AND id_usuario <> $id_usuario_principal AND active = 0 ";
	    $result_2 = $conexion->exec($sql);

	    if($result_2->resultStatus == PGRES_TUPLES_OK)
		{
		while(@fila_2 = $result_2->fetchrow)
		    {
		    my $id_usuario = $fila_2[0];
		    my $correo_usuario = $fila_2[1];
		    my $coeficiente = 1 + ($politica_de_suspencion/100);

		    $sql = "SELECT total,tope FROM gnupanel_espacio WHERE id_dominio = $id_usuario ";
		    my $result_3 = $conexion->exec($sql);
		    my @fila_3 = $result_3->fetchrow;

		    my $espacio_usado = $fila_3[0];
		    my $espacio_disponible = $fila_3[1] * $coeficiente;
		    my $espacio_disponible_alerta = $espacio_disponible * 9/10;

		    $sql = "SELECT total,tope FROM gnupanel_transferencias WHERE id_dominio = $id_usuario ";
		    $result_3 = $conexion->exec($sql);
		    @fila_3 = $result_3->fetchrow;

		    my $transferencia_usado = $fila_3[0];
		    my $transferencia_disponible = $fila_3[1] * $coeficiente;
		    my $transferencia_disponible_alerta = $transferencia_disponible * 9/10;

		    if(($espacio_usado < $espacio_disponible) && ($transferencia_usado < $transferencia_disponible))
		        {
			my @correo_e = dame_mensaje_usuario($conexion,$logueo,$id_usuario,"usuario-habilitado-usuario.txt");
			my $asunto = $correo_e[0];
			my $mensaje = $correo_e[1];
		        habilitar_usuario($conexion,$id_usuario);
		        envia_mensaje($correo_reseller,$correo_usuario,$asunto,$mensaje);
		        }
		    }
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

sub controla_correos
{
    my $conexion = $_[0];
    my $logueo = $_[1];
    my @fila;

    open(MENSDIR,">> $logueo");
    my $result = NULL;
    
    my $sql = "SELECT address,id_dominio,homedir,maildir,mailquota,active FROM gnupanel_postfix_mailuser WHERE id_dominio < 10000000 ";
    $result = $conexion->exec($sql);
    if($result->resultStatus == PGRES_TUPLES_OK)
	{

	while(@fila = $result->fetchrow)
	    {
	    my $correo_usuario = $fila[0];
	    my $id_usuario = $fila[1];
	    my $directorio = $fila[2]."/".$fila[3];
	    my $quota = $fila[4];
	    my $activo = $fila[5];
	    my $porcentaje = 9/10;
	    my $quota_alerta = $quota * $porcentaje;
	    my $quota_usada = mide_directorio($directorio,$logueo);
	    

	    my $result_1 = NULL;
	    my @fila_1;
	    $sql = "SELECT correo_contacto FROM gnupanel_usuario WHERE id_usuario = $id_usuario ";
	    $result_1 = $conexion->exec($sql);
	    @fila_1 = $result_1->fetchrow;
	    my $correo_contacto = $fila_1[0];
	    if($quota_usada > $quota)
	    {
	    my @correo_e = dame_mensaje_usuario($conexion,$logueo,$id_usuario,"usuario-correoexedido-usuario.txt");
	    my $asunto = $correo_e[0];
	    my $mensaje = $correo_e[1];
	    $mensaje =~ s/_CORREO_USUARIO_/$correo_usuario/ ;
	    envia_mensaje($correo_contacto,$correo_usuario,$asunto,$mensaje);
	    envia_mensaje($correo_contacto,$correo_contacto,$asunto,$mensaje);
	    }
	    elsif($quota_usada > $quota_alerta)
	    {
	    my @correo_e = dame_mensaje_usuario($conexion,$logueo,$id_usuario,"usuario-correocasiexedido-usuario.txt");
	    my $asunto = $correo_e[0];
	    my $mensaje = $correo_e[1];
	    $mensaje =~ s/_CORREO_USUARIO_/$correo_usuario/ ;
	    envia_mensaje($correo_contacto,$correo_usuario,$asunto,$mensaje);
	    envia_mensaje($correo_contacto,$correo_contacto,$asunto,$mensaje);
	    }
	    else
	    {
	    my $mensaje = "";
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

sub suspende_usuarios_deudores
{
    my $conexion = $_[0];
    my $logueo = $_[1];

    my @fila_0;
    open(MENSDIR,">> $logueo");
    my $result_0 = NULL;
    my $sql_0 = "SELECT gnupanel_divisas_usuario.id_usuario,gnupanel_divisas_usuario.credito FROM gnupanel_divisas_usuario,gnupanel_usuario WHERE gnupanel_usuario.active = 1 AND credito < 0 AND gnupanel_divisas_usuario.id_usuario = gnupanel_usuario.id_usuario AND substring(trim(both ' ' from text(age(vencimiento + interval '120 hour',now()))) from 1 for 1) = '-' ";
    my $control = 0;
    $result_0 = $conexion->exec($sql_0);
    if($result_0->resultStatus == PGRES_TUPLES_OK)
    {
    while(@fila_0 = $result_0->fetchrow)
	{
	my $id_usuario = $fila_0[0];
	my $deuda = $fila_0[1];
	suspender_usuario($conexion,$id_usuario);
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




#######################################################################################################################
## Put ourselves in background if not in debug mode. 
require "/etc/gnupanel/gnupanel.conf.pl";

$nombre = $0;
$nombre = substr($nombre,rindex($nombre,"/")+1,length($nombre)-1);

$logueo = "/var/log/".$nombre.".log";
$pidfile = "/var/run/".$nombre.".pid";
$conexion = NULL;
$reloaded = 0;

system("/usr/local/gnupanel/calcula-deudas.pl");
system("/usr/local/gnupanel/checkea_pagos.php");

#open(STDERR, ">> $logueo");

#Inicio del programa
#	    open(STDOUT, ">> $logueo");

	    $conexion = NULL;
	    $conexion = Pg::connectdb("dbname=$database host=localhost user=$userdb password=$pasaportedb");
	    
		$estado = $conexion->status;

		if($estado==PGRES_CONNECTION_OK)
		    {
		    suspende_usuarios_deudores($conexion,$logueo);
		    controla_resellers($conexion,$logueo);
		    controla_usuarios($conexion,$logueo);
		    controla_correos($conexion,$logueo);
		    suspende_usuarios_deudores($conexion,$logueo);
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




##################################################################
##################################################################



##################################################################
##################################################################

