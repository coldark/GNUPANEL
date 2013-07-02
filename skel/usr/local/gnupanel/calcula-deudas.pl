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

sub es_usuario_nuevo
    {
    my $conexion = $_[0];
    my $logueo = $_[1];
    my $id_usuario = $_[2];
    my $resultado = 0;    
    my $sql = "SELECT date(date_trunc('day',now())) - date(date_trunc('day',usuario_desde)) FROM gnupanel_usuario_data WHERE id_usuario = $id_usuario ";
    my $result = $conexion->exec($sql);
    my $antiguedad = int($result->getvalue(0,0));
    if($antiguedad==0)
	{
	$resultado = 1;
	}
    $resultado = $resultado;
    }

sub calcula_deudas_reseller
{
    my $conexion = $_[0];
    my $logueo = $_[1];

    my @fila_0;
    open(MENSDIR,">> $logueo");
    my $result_0 = NULL;
    my $deuda = 0;
    my $sql_0 = "SELECT id_reseller,id_plan,vigencia_plan FROM gnupanel_reseller_plan WHERE substring(trim(both ' ' from text(age(vencimiento_plan,now()))) from 1 for 1) = '-' ";
    my $control = 0;
    $result_0 = $conexion->exec($sql_0);
    if($result_0->resultStatus == PGRES_TUPLES_OK)
    {
    while(@fila_0 = $result_0->fetchrow)
	{
	my $id_reseller = $fila_0[0];
	my $id_plan = $fila_0[1];
	my $vigencia = $fila_0[2];
	my $credito = 0;
	my $sql_1 = "SELECT precio FROM gnupanel_reseller_planes WHERE id_plan = $id_plan ";
	$result_1 = $conexion->exec($sql_1);
	$control = 0;
	$deuda = 0;
	
	if(PGRES_TUPLES_OK == $result_1->resultStatus)
	    {
	    }
	else
	    {
	    $control = $control + 1;
	    }    
	    
	my @fila_1;
	if(@fila_1 = $result_1->fetchrow)
	    {
	    $deuda = $deuda + $fila_1[0];
	    }

	$sql_1 = "SELECT dias_de_gracia FROM gnupanel_admin_sets WHERE id_admin = (SELECT cliente_de FROM gnupanel_reseller WHERE id_reseller = $id_reseller) ";
	$result_1 = $conexion->exec($sql_1);
	$control = 0;

	if(PGRES_TUPLES_OK == $result_1->resultStatus)
	    {
	    }
	else
	    {
	    $control = $control + 1;
	    }    

	if(@fila_1 = $result_1->fetchrow)
	    {
	    $dias_de_gracia = $fila_1[0];
	    }

	$sql_2 = "SELECT credito FROM gnupanel_divisas_reseller WHERE id_reseller = $id_reseller ";
	$result_2 = $conexion->exec($sql_2);

	if(PGRES_TUPLES_OK == $result_2->resultStatus)
	    {
	    }
	else
	    {
	    $control = $control + 1;
	    }    

	
	if(@fila_2 = $result_2->fetchrow)
	    {
	    $credito = $fila_2[0];
	    }

	my $result_3 = $conexion->exec("BEGIN");
	if(PGRES_COMMAND_OK == $result_3->resultStatus)
	    {
	    }
	else
	    {
	    $control = $control + 1;
	    }    
	

	my $sql_3 = "UPDATE gnupanel_divisas_reseller SET credito = credito - $deuda, vencimiento = now() + interval '$dias_de_gracia days' WHERE id_reseller = $id_reseller ";

	if($credito<0)
	    {
	    $sql_3 = "UPDATE gnupanel_divisas_reseller SET credito = credito - $deuda WHERE id_reseller = $id_reseller ";
	    }
	$result_3 = $conexion->exec($sql_3);

	if(PGRES_COMMAND_OK == $result_3->resultStatus)
	    {
	    }
	else
	    {
	    $control = $control + 1;
	    }    


	$sql_3 = "UPDATE gnupanel_reseller_plan SET vencimiento_plan = vencimiento_plan + interval '$vigencia month' WHERE id_reseller = $id_reseller ";
	$result_3 = $conexion->exec($sql_3);

	if(PGRES_COMMAND_OK == $result_3->resultStatus)
	    {
	    }
	else
	    {
	    $control = $control + 1;
	    }


	$sql_3 = "UPDATE gnupanel_reseller_estado SET vencimiento_plan = (SELECT vencimiento_plan FROM gnupanel_reseller_plan WHERE id_reseller = $id_reseller ) WHERE id_reseller = $id_reseller ";
	$result_3 = $conexion->exec($sql_3);

	if(PGRES_COMMAND_OK == $result_3->resultStatus)
	    {
	    }
	else
	    {
	    $control = $control + 1;
	    }

	if($control == 0)
	    {
	    $result_3 = $conexion->exec("END");
	    }
	else
	    {
	    $result_3 = $conexion->exec("ROLLBACK");
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

sub calcula_deudas_extras_reseller
{
    my $conexion = $_[0];
    my $logueo = $_[1];

    my @fila_0;
    open(MENSDIR,">> $logueo");
    my $result_0 = NULL;
    my $deuda = 0;
    my $control = 0;

    my $sql_0 = "SELECT id_reseller,id_extra FROM gnupanel_reseller_extras WHERE substring(trim(both ' ' from text(age(vencimiento_extra,now()))) from 1 for 1) = '-' ";
    $result_0 = $conexion->exec($sql_0);

    if($result_0->resultStatus == PGRES_TUPLES_OK)
    {
    while(@fila_0 = $result_0->fetchrow)
	{
	my $id_reseller = $fila_0[0];
	my $id_extra = $fila_0[1];
	my $sql_1 = "SELECT periodo,precio FROM gnupanel_reseller_precios_extras WHERE id_extra = $id_extra ";
	my $result_1 = $conexion->exec($sql_1);
	my $periodo = 0;
	my @fila_1;

	if(PGRES_TUPLES_OK == $result_1->resultStatus)
	    {
	    }
	else
	    {
	    $control = $control + 1;
	    }

	if(@fila_1 = $result_1->fetchrow)
	    {
	    $periodo = $fila_1[0];
	    $deuda = $fila_1[1];
	    }

	$sql_1 = "SELECT dias_de_gracia FROM gnupanel_admin_sets WHERE id_admin = (SELECT cliente_de FROM gnupanel_reseller WHERE id_reseller = $id_reseller) ";
	$result_1 = $conexion->exec($sql_1);

	if(PGRES_TUPLES_OK == $result_1->resultStatus)
	    {
	    }
	else
	    {
	    $control = $control + 1;
	    }    

	if(@fila_1 = $result_1->fetchrow)
	    {
	    $dias_de_gracia = $fila_1[0];
	    }

	$sql_1 = "SELECT credito FROM gnupanel_divisas_reseller WHERE id_reseller = $id_reseller ";
	$result_1 = $conexion->exec($sql_1);

	if(PGRES_TUPLES_OK == $result_1->resultStatus)
	    {
	    }
	else
	    {
	    $control = $control + 1;
	    }    
	
	if(@fila_1 = $result_1->fetchrow)
	    {
	    $credito = $fila_1[0];
	    }

	if(@fila_1 = $result_1->fetchrow)
	    {
	    $periodo = $fila_1[0];
	    $deuda = $fila_1[1];
	    }

	$result_1 = $conexion->exec("BEGIN");
	if(PGRES_COMMAND_OK == $result_1->resultStatus)
	    {
	    }
	else
	    {
	    $control = $control + 1;
	    }    

	$sql_1 = "UPDATE gnupanel_divisas_reseller SET credito = credito - $deuda, vencimiento = now() + interval '$dias_de_gracia days' WHERE id_reseller = $id_reseller ";
	if($credito<0)
	    {
	    $sql_1 = "UPDATE gnupanel_divisas_reseller SET credito = credito - $deuda WHERE id_reseller = $id_reseller ";
	    }

	$result_1 = $conexion->exec($sql_1);


	$sql_1 = "UPDATE gnupanel_reseller_extras SET vencimiento_extra = vencimiento_extra + interval '$periodo month' WHERE id_reseller = $id_reseller AND id_extra = $id_extra ";
	$result_1 = $conexion->exec($sql_1);

	if(PGRES_COMMAND_OK == $result_1->resultStatus)
	    {
	    }
	else
	    {
	    $control = $control + 1;
	    }

	if($control == 0)
	    {
	    $result_1 = $conexion->exec("END");
	    }
	else
	    {
	    $result_1 = $conexion->exec("ROLLBACK");
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

#######################################################################################################################
#######################################################################################################################
#######################################################################################################################
#######################################################################################################################

sub calcula_deudas_usuarios
{
    my $conexion = $_[0];
    my $logueo = $_[1];

    my @fila_0;
    open(MENSDIR,">> $logueo");
    my $result_0 = NULL;
    my $deuda = 0;
    my $sql_0 = "SELECT id_usuario,id_plan,vigencia_plan FROM gnupanel_usuario_plan WHERE substring(trim(both ' ' from text(age(vencimiento_plan,now()))) from 1 for 1) = '-' ";
    my $control = 0;
    my $dias_de_gracia = 0;
    
    $result_0 = $conexion->exec($sql_0);
    if($result_0->resultStatus == PGRES_TUPLES_OK)
    {
    while(@fila_0 = $result_0->fetchrow)
	{
	my $id_usuario = $fila_0[0];
	my $id_plan = $fila_0[1];
	my $vigencia = $fila_0[2];
	my $credito = 0;
	my $sql_1 = "SELECT precio FROM gnupanel_usuarios_planes WHERE id_plan = $id_plan ";
	$result_1 = $conexion->exec($sql_1);
	$control = 0;
	$deuda = 0;
	
	if(PGRES_TUPLES_OK == $result_1->resultStatus)
	    {
	    }
	else
	    {
	    $control = $control + 1;
	    }    
	    
	my @fila_1;
	if(@fila_1 = $result_1->fetchrow)
	    {
	    $deuda = $deuda + $fila_1[0];
	    }

	$sql_1 = "SELECT dias_de_gracia FROM gnupanel_reseller_sets WHERE id_reseller = (SELECT cliente_de FROM gnupanel_usuario WHERE id_usuario = $id_usuario) ";
	$result_1 = $conexion->exec($sql_1);
	$control = 0;

	if(PGRES_TUPLES_OK == $result_1->resultStatus)
	    {
	    }
	else
	    {
	    $control = $control + 1;
	    }    

	if(@fila_1 = $result_1->fetchrow)
	    {
	    $dias_de_gracia = $fila_1[0];
	    }

	$sql_2 = "SELECT credito FROM gnupanel_divisas_usuario WHERE id_usuario = $id_usuario ";
	$result_2 = $conexion->exec($sql_2);

	if(PGRES_TUPLES_OK == $result_2->resultStatus)
	    {
	    }
	else
	    {
	    $control = $control + 1;
	    }    

	
	if(@fila_2 = $result_2->fetchrow)
	    {
	    $credito = $fila_2[0];
	    }

	my $result_3 = $conexion->exec("BEGIN");
	if(PGRES_COMMAND_OK == $result_3->resultStatus)
	    {
	    }
	else
	    {
	    $control = $control + 1;
	    }    
	

	my $sql_3 = "UPDATE gnupanel_divisas_usuario SET credito = credito - $deuda, vencimiento = now() + interval '$dias_de_gracia days' WHERE id_usuario = $id_usuario ";

	if($credito<0)
	    {
	    $sql_3 = "UPDATE gnupanel_divisas_usuario SET credito = credito - $deuda WHERE id_usuario = $id_usuario ";
	    }
	$result_3 = $conexion->exec($sql_3);

	if(PGRES_COMMAND_OK == $result_3->resultStatus)
	    {
	    }
	else
	    {
	    $control = $control + 1;
	    }    


	$sql_3 = "UPDATE gnupanel_usuario_plan SET vencimiento_plan = vencimiento_plan + interval '$vigencia month' WHERE id_usuario = $id_usuario ";
	$result_3 = $conexion->exec($sql_3);

	if(PGRES_COMMAND_OK == $result_3->resultStatus)
	    {
	    }
	else
	    {
	    $control = $control + 1;
	    }


	$sql_3 = "UPDATE gnupanel_usuario_estado SET vencimiento_plan = (SELECT vencimiento_plan FROM gnupanel_usuario_plan WHERE id_usuario = $id_usuario ) WHERE id_usuario = $id_usuario ";
	$result_3 = $conexion->exec($sql_3);

	if(PGRES_COMMAND_OK == $result_3->resultStatus)
	    {
	    }
	else
	    {
	    $control = $control + 1;
	    }

	if($control == 0)
	    {
	    $result_3 = $conexion->exec("END");
	    }
	else
	    {
	    $result_3 = $conexion->exec("ROLLBACK");
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

sub calcula_deudas_extras_usuario
{
    my $conexion = $_[0];
    my $logueo = $_[1];

    my @fila_0;
    open(MENSDIR,">> $logueo");
    my $result_0 = NULL;
    my $deuda = 0;
    my $control = 0;

    my $sql_0 = "SELECT id_usuario,id_extra FROM gnupanel_usuario_extras WHERE substring(trim(both ' ' from text(age(vencimiento_extra,now()))) from 1 for 1) = '-' ";
    $result_0 = $conexion->exec($sql_0);

    if($result_0->resultStatus == PGRES_TUPLES_OK)
    {
    while(@fila_0 = $result_0->fetchrow)
	{
	my $id_usuario = $fila_0[0];
	my $id_extra = $fila_0[1];
	my $sql_1 = "SELECT periodo,precio FROM gnupanel_usuarios_precios_extras WHERE id_extra = $id_extra ";
	my $result_1 = $conexion->exec($sql_1);
	my $periodo = 0;
	my @fila_1;

	if(PGRES_TUPLES_OK == $result_1->resultStatus)
	    {
	    }
	else
	    {
	    $control = $control + 1;
	    }

	if(@fila_1 = $result_1->fetchrow)
	    {
	    $periodo = $fila_1[0];
	    $deuda = $fila_1[1];
	    }

	$sql_1 = "SELECT dias_de_gracia FROM gnupanel_reseller_sets WHERE id_reseller = (SELECT cliente_de FROM gnupanel_usuario WHERE id_usuario = $id_usuario) ";
	$result_1 = $conexion->exec($sql_1);

	if(PGRES_TUPLES_OK == $result_1->resultStatus)
	    {
	    }
	else
	    {
	    $control = $control + 1;
	    }    

	if(@fila_1 = $result_1->fetchrow)
	    {
	    $dias_de_gracia = $fila_1[0];
	    }

	$sql_1 = "SELECT credito FROM gnupanel_divisas_usuario WHERE id_usuario = $id_usuario ";
	$result_1 = $conexion->exec($sql_1);

	if(PGRES_TUPLES_OK == $result_1->resultStatus)
	    {
	    }
	else
	    {
	    $control = $control + 1;
	    }    
	
	if(@fila_1 = $result_1->fetchrow)
	    {
	    $credito = $fila_1[0];
	    }

	if(@fila_1 = $result_1->fetchrow)
	    {
	    $periodo = $fila_1[0];
	    $deuda = $fila_1[1];
	    }

	$result_1 = $conexion->exec("BEGIN");
	if(PGRES_COMMAND_OK == $result_1->resultStatus)
	    {
	    }
	else
	    {
	    $control = $control + 1;
	    }    

	$sql_1 = "UPDATE gnupanel_divisas_usuario SET credito = credito - $deuda, vencimiento = now() + interval '$dias_de_gracia days' WHERE id_usuario = $id_usuario ";
	if($credito<0)
	    {
	    $sql_1 = "UPDATE gnupanel_divisas_usuario SET credito = credito - $deuda WHERE id_usuario = $id_usuario ";
	    }

	$result_1 = $conexion->exec($sql_1);

	$sql_1 = "UPDATE gnupanel_usuario_extras SET vencimiento_extra = vencimiento_extra + interval '$periodo month' WHERE id_usuario = $id_usuario AND id_extra = $id_extra ";
	$result_1 = $conexion->exec($sql_1);

	if(PGRES_COMMAND_OK == $result_1->resultStatus)
	    {
	    }
	else
	    {
	    $control = $control + 1;
	    }

	if($control == 0)
	    {
	    $result_1 = $conexion->exec("END");
	    }
	else
	    {
	    $result_1 = $conexion->exec("ROLLBACK");
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

sub envia_mensajes_a_deudores_usuario
{
    my $conexion = $_[0];
    my $logueo = $_[1];

    my @fila_0;
    open(MENSDIR,">> $logueo");
    my $result_0 = NULL;
    my $sql_0 = "SELECT gnupanel_divisas_usuario.id_usuario,gnupanel_divisas_usuario.credito FROM gnupanel_divisas_usuario,gnupanel_usuario WHERE gnupanel_usuario.active = 1 AND credito < 0 AND gnupanel_divisas_usuario.id_usuario = gnupanel_usuario.id_usuario AND substring(trim(both ' ' from text(age(vencimiento,now()))) from 1 for 1) = '-' ";
    my $control = 0;
    $result_0 = $conexion->exec($sql_0);
    if($result_0->resultStatus == PGRES_TUPLES_OK)
    {
    while(@fila_0 = $result_0->fetchrow)
	{
	my $id_usuario = $fila_0[0];
	my $deuda = $fila_0[1];
	my $id_reseller = 0;
	my $correo_usuario = "";
	my $correo_reseller = "";
	my $dominio_reseller = "";
	my $dominio_usuario = "";
	my $active = 0;
	
	my $sql_1 = "SELECT correo_contacto,cliente_de,dominio,active FROM gnupanel_usuario WHERE id_usuario = $id_usuario ";
	$result_1 = $conexion->exec($sql_1);
	my @fila_1;

	if(@fila_1 = $result_1->fetchrow)
	    {
	    $correo_usuario = $fila_1[0];
	    $id_reseller = $fila_1[1];
	    $dominio_usuario = $fila_1[2];
	    $active = $fila_1[3];
	    }

	if($active == 1)
	    {
	    my $sql_1 = "SELECT reseller,dominio FROM gnupanel_reseller WHERE id_reseller = $id_reseller ";
	    $result_1 = $conexion->exec($sql_1);

	    if(@fila_1 = $result_1->fetchrow)
		{
		$correo_reseller = $fila_1[0]."\@".$fila_1[1];
		$dominio_reseller = "http://gnupanel.".$fila_1[1]."/users";
		}

	    my @correo_e = dame_mensaje_usuario($conexion,$logueo,$id_usuario,"usuario-deudor-usuario.txt");
	    my $asunto = $correo_e[0];
	    my $mensaje = $correo_e[1];

	    $asunto =~ s/_DOMINIO_USUARIO_/$dominio_usuario/ ;
	    $mensaje =~ s/_ENLACE_/$dominio_reseller/ ;
	    envia_mensaje($correo_reseller,$correo_usuario,$asunto,$mensaje);

	    @correo_e = dame_mensaje_reseller($conexion,$logueo,$id_reseller,"usuario-deudor-reseller.txt");
	    $asunto = $correo_e[0];
	    $mensaje = $correo_e[1];
	    $asunto =~ s/_DOMINIO_USUARIO_/$dominio_usuario/ ;
	    envia_mensaje($correo_reseller,$correo_reseller,$asunto,$mensaje);
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

sub envia_aviso_de_vencimiento_usuario
{
    my $conexion = $_[0];
    my $logueo = $_[1];

    my @fila_0;
    open(MENSDIR,">> $logueo");
    my $result_0 = NULL;

    my $sql_0 = "SELECT gnupanel_divisas_usuario.id_usuario,gnupanel_divisas_usuario.credito,date(gnupanel_divisas_usuario.vencimiento),date(gnupanel_divisas_usuario.vencimiento)-date(now()),gnupanel_reseller_sets.dias_de_gracia FROM gnupanel_divisas_usuario,gnupanel_reseller_sets,gnupanel_usuario WHERE gnupanel_divisas_usuario.credito < 0 AND gnupanel_usuario.active = 1 AND gnupanel_divisas_usuario.id_usuario = gnupanel_usuario.id_usuario ";

    my $control = 0;
    $result_0 = $conexion->exec($sql_0);
    if($result_0->resultStatus == PGRES_TUPLES_OK)
    {
    while(@fila_0 = $result_0->fetchrow)
	{
	my $id_usuario = $fila_0[0];
	my $deuda = $fila_0[1];
	my $fecha_vencimiento = $fila_0[2];
	my $dias_para_vencimiento = int($fila_0[3]);
	my $dias_de_gracia = int($fila_0[4]);
	my $id_reseller = 0;
	my $correo_usuario = "";
	my $correo_reseller = "";
	my $dominio_reseller = "";
	
	my $sql_1 = "SELECT correo_contacto,cliente_de FROM gnupanel_usuario WHERE id_usuario = $id_usuario ";
	$result_1 = $conexion->exec($sql_1);
	my @fila_1;

	if(@fila_1 = $result_1->fetchrow)
	    {
	    $correo_usuario = $fila_1[0];
	    $id_reseller = $fila_1[1];
	    }

	my $sql_1 = "SELECT reseller,dominio FROM gnupanel_reseller WHERE id_reseller = $id_reseller ";
	$result_1 = $conexion->exec($sql_1);
	if(@fila_1 = $result_1->fetchrow)
	    {
	    $correo_reseller = $fila_1[0]."\@".$fila_1[1];
	    $dominio_reseller = "http://gnupanel.".$fila_1[1]."/users";
	    }

	if($dias_para_vencimiento==$dias_de_gracia)
	    {
	    if(es_usuario_nuevo($conexion,$logueo,$id_usuario)!=1)
		{
		my @correo_e = dame_mensaje_usuario($conexion,$logueo,$id_usuario,"usuario-avisopago-usuario.txt");
		my $asunto = $correo_e[0];
		my $mensaje = $correo_e[1];
		$mensaje =~ s/_DIAS_DE_GRACIA_/$dias_de_gracia/ ;
		$mensaje =~ s/_FECHA_/$fecha_vencimiento/ ;
		$mensaje =~ s/_ENLACE_/$dominio_reseller/ ;
		decode_entities($mensaje);
		envia_mensaje($correo_reseller,$correo_usuario,$asunto,$mensaje);
		}
	    else
		{
		my @correo_e = dame_mensaje_usuario($conexion,$logueo,$id_usuario,"usuario-nuevo-usuario.txt");
		my $asunto = $correo_e[0];
		my $mensaje = $correo_e[1];
		$mensaje =~ s/_DIAS_DE_GRACIA_/$dias_de_gracia/ ;
	        envia_mensaje($correo_reseller,$correo_usuario,$asunto,$mensaje);
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
    calcula_deudas_reseller($conexion,$logueo);
    calcula_deudas_extras_reseller($conexion,$logueo);
    calcula_deudas_usuarios($conexion,$logueo);
    calcula_deudas_extras_usuario($conexion,$logueo);
    envia_mensajes_a_deudores_usuario($conexion,$logueo);
    envia_aviso_de_vencimiento_usuario($conexion,$logueo);
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
###############################################################################################################################################################
###############################################################################################################################################################
###############################################################################################################################################################



