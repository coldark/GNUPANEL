
###############################################################################################################################################################
###############################################################################################################################################################
###############################################################################################################################################################
###############################################################################################################################################################
###############################################################################################################################################################
###############################################################################################################################################################
###############################################################################################################################################################
###############################################################################################################################################################
###############################################################################################################################################################


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


sub analiza_pop3
    {
    my $renglon = $_[0];
    my $conexion = $_[1];
    my $logueo = $_[2];
    open(MENSPOP3,">> $logueo");
    my $pop3 = index($renglon,"retr");
    my @datos = NULL;
    my @dominios = NULL;
    my @transfer = NULL;
    my $mensaje;
    if($pop3>=0)
	{
	@datos = split(',',$renglon);
	@dominios = split('@',$datos[1]);
	my $dominio = $dominios[1];
	@transfer = split('=',$datos[4]);
	my $transferencia = $transfer[1];
	my $result = NULL;
        $result = $conexion->exec("BEGIN");
	my $sql = "SELECT dominio FROM gnupanel_transferencias WHERE dominio='$dominio' ";
        my $result = $conexion->exec($sql);
	$result = $conexion->getResult;
	if($result)
	    {
	    $sql = "UPDATE gnupanel_transferencias SET pop3 = pop3 + $transferencia WHERE dominio = '$dominio' ";
    	    $result = $conexion->exec($sql);
	    $estado = $result->resultStatus;

	    $sql = "UPDATE gnupanel_transferencias SET total = http + ftp + smtp + pop3 WHERE dominio = '$dominio' ";
    	    $result = $conexion->exec($sql);
	    my $estado = $estado && $result->resultStatus;

	    if($estado == PGRES_COMMAND_OK)
		{
        	$result = $conexion->exec("END");
		}
	    else
		{
		$mensaje = $conexion->errorMessage;
		print MENSPOP3 $mensaje;
        	$result = $conexion->exec("ROLLBACK");
		}
	    }
	else
	    {
	    $mensaje = "El dominio $dominio no es local \n";
	    print MENSPOP3 $mensaje;
    	    $result = $conexion->exec("END");
	    }
	}
    close MENSPOP3;
    }
    
sub analiza_smtp
    {
    my $renglon = $_[0];
    my $conexion = $_[1];
    my $logueo = $_[2];
    open(MENSSMTP,">> $logueo");

    my $smtp = index($renglon,"from");
    if($smtp>=0)
	{
	$smtp = index($renglon,"size");
	}
    
    my @datos = NULL;
    my @dominios = NULL;
    my @transfer = NULL;
    my $mensaje;
    if($smtp>=0)
	{
	@datos = split(',',$renglon);
	@dominios = split('@',$datos[0]);
	my $dominio = $dominios[1];
        chop($dominio);
	@transfer = split('=',$datos[1]);
	my $transferencia = $transfer[1];
	$transferencia = 0 + $transferencia;
	my $result = NULL;
        $result = $conexion->exec("BEGIN");
        $result = $conexion->exec("SELECT dominio FROM gnupanel_transferencias WHERE dominio='$dominio'");
	$result = $conexion->getResult;
	if($result)
	    {
	    my $sql = "UPDATE gnupanel_transferencias SET smtp=smtp+$transferencia WHERE dominio = '$dominio'";
    	    $result = $conexion->exec($sql);
	    my $estado = $result->resultStatus;

	    $sql = "UPDATE gnupanel_transferencias SET total = http + ftp + smtp + pop3 WHERE dominio = '$dominio' ";
    	    $result = $conexion->exec($sql);
	    $estado = $estado && $result->resultStatus;

	    if($estado == PGRES_COMMAND_OK)
		{
        	$result = $conexion->exec("END");
		}
	    else
		{
		$mensaje = $conexion->errorMessage;
		print MENSSMTP $mensaje;
        	$result = $conexion->exec("ROLLBACK");
		}
	    }
	else
	    {
	    $mensaje = "El dominio $dominio no es local \n";
	    print MENSSMTP $mensaje;
    	    $result = $conexion->exec("END");
	    }
	}
    close MENSSMTP;	
    }

sub analiza_ftp
    {
    my $renglon = $_[0];
    my $conexion = $_[1];
    my $logueo = $_[2];
    open(MENSFTP,">> $logueo");
    my @datos = NULL;
    my @dominios = NULL;
    my @transfer = NULL;
    @datos = split(' ',$renglon);
    @dominios = split('@',$datos[13]);
    my $dominio = $dominios[1];
    @transfer = split('=',$datos[4]);
    my $transferencia = $datos[7];
    my $ftp = $datos[11];
    my $result = NULL;
    $result = $conexion->exec("BEGIN");
    my $sql = "SELECT dominio FROM gnupanel_transferencias WHERE dominio='$dominio' ";
    $result = $conexion->exec($sql);
    $result = $conexion->getResult;
    my $estado;
    my $mensaje;
    
    if($result && (($ftp eq "i") || ($ftp eq "o" )))
	{
	$sql = "UPDATE gnupanel_transferencias SET ftp = ftp + $transferencia WHERE dominio = '$dominio' ";
    	$result = $conexion->exec($sql);
	$estado = $result->resultStatus;

	$sql = "UPDATE gnupanel_transferencias SET total = http + ftp + smtp + pop3 WHERE dominio = '$dominio' ";
    	$result = $conexion->exec($sql);
	$estado = $estado && $result->resultStatus;

	if($estado == PGRES_COMMAND_OK)
	    {
    	    $result = $conexion->exec("END");
	    }
	else
	    {
	    $mensaje = $conexion->errorMessage;
	    print MENSFTP $mensaje;
    	    $result = $conexion->exec("ROLLBACK");
	    }
	}
    else
	{
	$mensaje = "No se hace nada \n";
	print MENSFTP $mensaje;
    	$result = $conexion->exec("END");
	}
	
    close MENSFTP;
    }


#######################################################################################################################

	    
		
		if($estado==PGRES_CONNECTION_OK)
		    {
		    while(defined($linea=$file->read))
			{
			    chop($linea);
			    $pop3 = index($linea,"pop3");
			    $smtp = index($linea,"postfix/qmgr");

			    if($smtp>=0)
				{
		    		analiza_smtp($linea,$conexion,$logueo);
				}

			    if($pop3>=0)
				{
				analiza_pop3($linea,$conexion,$logueo);
				}
			}
		    }
		



###############################################################################################################################################################
	    

	    		
		if($estado==PGRES_CONNECTION_OK)
		    {
		    while(defined($linea=$file->read))
			{
			    chop($linea);
	    		    analiza_ftp($linea,$conexion,$logueo);
			}
		    }
		else
		    {
		    $mensaje = $conexion->errorMessage;
		    open(MENSAGES,">> $logueo");
		    print MENSAGES $mensaje;
		    close MENSAGES;
		    $conexion->reset;
		    }



###############################################################################################################################################################




