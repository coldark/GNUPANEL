
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

