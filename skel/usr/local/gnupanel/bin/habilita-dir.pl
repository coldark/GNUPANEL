#!/usr/bin/perl

#DIRECTORIO_RAIZ=$1
#DIRECTORIO_DESTINO=$2

#chmod 0770 ${DIRECTORIO_RAIZ}
#mkdir -m 0770 -p ${DIRECTORIO_DESTINO}
#chown -R ftpuser:ftpgroup ${DIRECTORIO_RAIZ}
#chmod -R 0770 ${DIRECTORIO_DESTINO}

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

sub copiar_sitios
{
$directorio = $_[0];
$dir_backup = $_[1];
$comando = "/bin/cp -f -r ".$directorio." ".$dir_backup;
system($comando);
}

sub crea_directorio
    {
    my $directorio_in = $_[0];
    my $comando = "/bin/mkdir -p -m 0500 ";
    
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

sub checkea_directorio
{
    my $directorio_raiz = $_[0];
    my $directorio_destino = $_[1];
    
    my $result = 0;
    my $largo = length($directorio_raiz_sitios);
    my $dir_comp = substr($directorio_raiz,0,$largo);
    my $cantidad_barras = split("/",$directorio_raiz);
    $largo = length($directorio_raiz);
    my $dir_comp_1 = substr($directorio_destino,0,$largo);
    
    if(($dir_comp eq $directorio_raiz_sitios) && ($dir_comp_1 eq $directorio_raiz) && ($cantidad_barras >= 6))
    {
	$result = 1;
    }

    $result = $result;
}

sub checkea_directorio_c
{
    my $directorio_c = $_[0];
    
    my $result = 0;
    my $largo = length($directorio_raiz_correo);
    my $dir_comp = substr($directorio_c,0,$largo);
    my $cantidad_barras = split("/",$directorio_c);
    
    if(($dir_comp eq $directorio_raiz_correo) && ($cantidad_barras >= 6))
    {
	$result = 1;
    }

    $result = $result;
}

sub inicializa_permisos
{
    my $comando = "/bin/chmod 600 /usr/share/gnupanel/aplicaciones/";
    my $archivo = "joomla/configuration.php";
    my $comandar = $comando.$archivo;
    system($comandar);

    $archivo = "oscommerce/includes/configure.php";
    $comandar = $comando.$archivo;
    system($comandar);

    $archivo = "oscommerce/admin/includes/configure.php";
    $comandar = $comando.$archivo;
    system($comandar);

    $archivo = "phpbb/config.php";
    $comandar = $comando.$archivo;
    system($comandar);

    $archivo = "wordpress/wp-config.php";
    $comandar = $comando.$archivo;
    system($comandar);
}

#########################################################################################################################

require "/etc/gnupanel/gnupanel.conf.pl";

my $directorio_raiz = $ARGV[0];
my $directorio_destino = $ARGV[1];
my $checkeo = $ARGV[2];
my $directorio_correos = $ARGV[3];
my $directorio_backup_correos = $ARGV[4];
my $control = checkea_directorio($directorio_raiz,$directorio_destino);
my $control_c = checkea_directorio_c($directorio_correos);

$com_perm = "/usr/bin/find $directorio_backup -type d -exec /bin/chmod 0700 {} \\\; ";
system($com_perm);
$com_perm = "/usr/bin/find $directorio_backup -type f -exec /bin/chmod 0600 {} \\\; ";
system($com_perm);

if($checkeo == 1)
    {
    print "$control\n";
    }
elsif($checkeo == 2)
    {
    if($control_c == 1)
	{
	copiar_sitios($directorio_correos,$directorio_backup_correos."/mails");
	my $comando = "chown -R ".$usuario_dir_apache.":".$grupo_dir_apache." ".$directorio_backup_correos;
	system($comando); 
	}
    }
else
    {
    if($control == 1)
	{
	#inicializa_permisos();
	my $comando = "chmod 0700 $directorio_raiz ";
	system($comando); 
	$comando = "mkdir -m 0700 -p $directorio_destino ";
	system($comando); 
	$comando = "chown -R ".$usuario_dir_apache.":".$grupo_dir_apache." ".$directorio_raiz ;
	system($comando); 
	$comando = "chmod -R 0700 $directorio_destino ";
	system($comando); 
	}
    }

#########################################################################################################################


