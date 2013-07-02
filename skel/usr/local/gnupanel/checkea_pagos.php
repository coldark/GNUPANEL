#!/usr/bin/php5
<?php

error_reporting(0);

function do_post_request($url, $postdata, $files = null)
{
    $data = "";
    $boundary = "---------------------".substr(md5(rand(0,32000)), 0, 10);

    //Collect Postdata
    foreach($postdata as $key => $val)
    {
        $data .= "--$boundary\n";
        $data .= "Content-Disposition: form-data; name=\"".$key."\"\n\n".$val."\n";
    }

    $data .= "--$boundary\n";

    //Collect Filedata
    if(is_array($files))
    {
	foreach($files as $key => $file)
	{
	    $fileContents = file_get_contents($file['tmp_name']);
	    $data .= "Content-Disposition: form-data; name=\"{$key}\"; filename=\"{$file['name']}\"\n";
	    $data .= "Content-Type: image/jpeg\n";
	    $data .= "Content-Transfer-Encoding: binary\n\n";
	    $data .= $fileContents."\n";
	    $data .= "--$boundary--\n";
	}
    }

    $params = array('http' => array(
                                    'method' => 'POST',
                                    'header' => 'Content-Type: multipart/form-data; boundary='.$boundary,
                                    'content' => $data
                                    ));

    $ctx = stream_context_create($params);
    $fp = fopen($url, 'rb', false, $ctx);

    if (!$fp)
    {
        throw new Exception("Problem with $url, $php_errormsg");
    }

    $response = strip_tags(@stream_get_contents($fp));
    if ($response === false)
    {
        throw new Exception("Problem reading data from $url, $php_errormsg");
    }
    return $response;
}

function dame_dias($cant_dias)
{

$result = NULL;
$result = array();

    for($i=0;$i<=$cant_dias;$i++)
    {
	$fecha_cons = time() - ($i * 24 * 60 * 60);

	$fecha = getdate($fecha_cons);
        $ano = $fecha['year'];
	$mes = $fecha['mon'];
	$dia = $fecha['mday'];

	if($mes<10) $mes = "0".$mes;
	if($dia<10) $dia = "0".$dia;

	$result[] = $ano.$mes.$dia;
    }

$result = array_reverse($result);
return $result;
}

function give_me_dolar_midolar_com_ar()
{
    $result = NULL;
    $url = "http://www.midolar.com.ar/dolar.xml";
    $xml = simplexml_load_file($url);

    if($xml!==false)
    {
	if($xml)
	{
	    $dolar_compra_a = $xml->xpath('VALORCOMPRA');
	    $dolar_venta_a = $xml->xpath('VALORVENTA');

	    $dolar_compra = $dolar_compra_a[0];
	    $dolar_venta = $dolar_venta_a[0] ;
	
	    $dolar_compra = floatval(str_replace(",",".",$dolar_compra));
	    $dolar_venta = floatval(str_replace(",",".",$dolar_venta));

	    if($dolar_compra && $dolar_venta)
	    {
		$result = $dolar_compra;
		if($dolar_venta > $dolar_compra) $result = floatval($dolar_venta);
	    }
	}
    }
    return $result;
}

function give_me_dolar_netcomsatelital_com_ar()
{
    $result = NULL;
    $url = "http://www.netcomsatelital.com.ar/dolar.xml";
    $xml = simplexml_load_file($url);

    if($xml!==false)
    {
	if($xml)
	{
	    $dolar_compra_a = $xml->xpath('VALORCOMPRA');
	    $dolar_venta_a = $xml->xpath('VALORVENTA');

	    $dolar_compra = $dolar_compra_a[0];
	    $dolar_venta = $dolar_venta_a[0] ;
	
	    $dolar_compra = floatval(str_replace(",",".",$dolar_compra));
	    $dolar_venta = floatval(str_replace(",",".",$dolar_venta));

	    if($dolar_compra && $dolar_venta)
	    {
		$result = $dolar_compra;
		if($dolar_venta > $dolar_compra) $result = floatval($dolar_venta);
	    }
	}
    }
    return $result;
}

function give_me_dolar_cotizacion_dolar_com_ar()
{
    $result = NULL;
    $url = "http://www.cotizacion-dolar.com.ar/convertidor_on_line.php";
    $postdata = NULL;
    $postdata = array();

    $postdata['convertida'] = "ar";
    $postdata['cantidad'] = "us";
    $postdata['monto'] = "1";
    $postdata['tipo'] = "vendedor";

    $contenido = explode("\n",@do_post_request($url,$postdata));
    if(is_array($contenido))
    {
	foreach ($contenido as $key => $value)
	{
	    if(substr_count($value,"El resultado de la conversión es de:")>0)
	    {
		$renglones = explode(" ",$value);
		$value_0 = $renglones[17];
		$value_1 = array_pop($renglones);
		if(is_numeric($value_0) && is_numeric($value_1))
		{
		    if($value_0 == $value_1) $result = floatval($value_0);
		}
	    }
	}
    }
    return $result;
}

function give_me_dolar_dolarhot_com()
{
    $result = NULL;
    $diferencia_admitida = 0.25;
    $url = "http://www.dolarhot.com";
    $contenido = file_get_contents($url);
    $valores = NULL;
    $valores = array();

    $renglones = explode("\n",$contenido);
    foreach($renglones as $key => $value)
    {
	$valor = substr(trim($value),0,4);
	if(is_numeric($valor)) $valores[] = $valor;
    }

    if(count($valores == 2))
    {
	$diferencia = abs($valores[0] - $valores[1]);
	if($diferencia < $diferencia_admitida)
	{
	    $result = $valores[0];
	    if($valores[1]>$valores[0]) $result = floatval($valores[1]);
	}
    }

    return $result;
}

function give_me_dolar_yahoo_com()
{
    $result = NULL;
    $diferencia_admitida = 0.25;
    
    $from = 'USD'; //US Dollar
    $to = 'ARS'; // Pesos argentinos

    $url = 'http://finance.yahoo.com/d/quotes.csv?e=.csv&f=sl1d1t1&s='. $from . $to .'=X';

    $data_dolar = file_get_contents($url);

    
    if(strlen($data_dolar)>0)
    {
      $data_out = explode(',',$data_dolar);
      if(is_array($data_out))
      {
	if(count($data_out)>=2)
	{
	  if(is_numeric($data_out[1])) $result = floatval($data_out[1]);
	}
      }
    }

    return $result;
}

function give_me_dolar_google_com()
{
    $result = NULL;
    $diferencia_admitida = 0.25;
    
    $url = "http://www.google.com/ig/calculator?hl=en&q=1USD=?ARS";
    $data_dolar = file_get_contents($url);

    $data_dolar = str_replace('rhs:','"rhs":',$data_dolar);
    $data_dolar = str_replace('lhs:','"lhs":',$data_dolar);
    $data_dolar = str_replace('error:','"error":',$data_dolar);
    $data_dolar = str_replace('icc:','"icc":',$data_dolar);

    $data_dolar = json_decode($data_dolar,true);

    if(!$data_dolar['error'])
    {
      $data_in = explode(" ",$data_dolar['rhs']);
      if(is_array($data_in))
      {
	if(count($data_in)>0)
	{
	  if(is_numeric($data_in[0])) $result = floatval($data_in[0]);
	}
      }
    }

    return $result;
}

function give_me_dolar_eldolarblue_net()
{
    $result = NULL;
    $diferencia_admitida = 0.25;
    
    $url = "http://www.eldolarblue.net/getDolarLibre.php?as=json";

    $data_dolar = file_get_contents($url);

    $data_dolar = str_replace('url:','"url":',$data_dolar);
    $data_dolar = str_replace('datetime:','"datetime":',$data_dolar);
    $data_dolar = str_replace('exchangerate:','"exchangerate":',$data_dolar);
    $data_dolar = str_replace('buy:','"buy":',$data_dolar);
    $data_dolar = str_replace('sell:','"sell":',$data_dolar);
    $data_dolar = json_decode($data_dolar,true);
    if(is_array($data_dolar['exchangerate']))
    {
      if((is_numeric($data_dolar['exchangerate']['buy']))&&(is_numeric($data_dolar['exchangerate']['sell'])))
      {
	if($data_dolar['exchangerate']['buy']>$data_dolar['exchangerate']['sell'])
	{
	  $result = floatval($data_dolar['exchangerate']['buy']);
	}
	else
	{
	  $result = floatval($data_dolar['exchangerate']['sell']);
	}
      }
    }

    return $result;
}

function give_me_dolar($diferencia_admitida = 0.25,$debug=false)
{
    $result = NULL;
    $dolar = NULL;
    $dolares = NULL;
    $dolares = array();

    if($dolar = @give_me_dolar_midolar_com_ar())
    {
	$dolares['MIDOLAR_COM_AR'] = $dolar;
    }

    if($dolar = @give_me_dolar_netcomsatelital_com_ar())
    {
	$dolares['NETCOMSATELITAL_COM_AR'] = $dolar;
    }

    if($dolar = @give_me_dolar_cotizacion_dolar_com_ar())
    {
	$dolares['COTIZACION_DOLAR_COM_AR'] = $dolar;
    }

    if($dolar = @give_me_dolar_dolarhot_com())
    {
	$dolares['DOLARHOT_COM'] = $dolar;
    }

    if($dolar = @give_me_dolar_yahoo_com())
    {
	$dolares['YAHOO_COM'] = $dolar;
    }

    if($dolar = @give_me_dolar_google_com())
    {
	$dolares['GOOGLE_COM'] = $dolar;
    }

    if($dolar = @give_me_dolar_eldolarblue_net())
    {
	$dolares['ELDOLARBLUE_NET'] = $dolar;
    }

    if((is_array($dolares))&&(count($dolares)>0))
    {
	$i = 0;
	$dolar = 0;
	$lt_usd = current($dolares);
	$gt_usd = current($dolares);
	
	foreach($dolares as $key => $value)
	{
	    if($value<$lt_usd) $lt_usd = $value;
	    if($value>$gt_usd) $gt_usd = $value;
	    
	    if($debug) print "$key -> $value \n";
	    $dolar = $value + $dolar;
	    $i++;
	}
	
	$diff_usd = abs($lt_usd - $gt_usd);
	if($debug) print "DIFF: $diff_usd \n";

	$cantidad = $i;
	if($diff_usd<$diferencia_admitida) $result = number_format(($dolar/$cantidad),2);
	
    }

    return $result;
}

function dame_id_pago_usuario($control,$empresa)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;

    $result = NULL;
    $checkeo = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
						    
    $consulta = "SELECT id,id_usuario,importe FROM pagos_usuario WHERE codigo_seguimiento = '$control' AND empresa_cobranza = '$empresa' AND confirmado = 0 AND acreditado = 0";
    //$consulta = "SELECT id,id_usuario FROM pagos_usuario WHERE codigo_seguimiento = '$control' AND empresa_cobranza = '$empresa' ";
    
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $res_consulta;

    if($checkeo)
    {
	if(pg_num_rows($res_consulta)>0)
	{
	    $result = array();
	    $result = pg_fetch_assoc($res_consulta,0);
	}
    }

    pg_free_result($res_consulta);    			
    pg_close($conexion);
    return $result;
}

function acreditar_fondos_usuario($id_usuario,$id_pago,$fondos)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
		
    $checkeo = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $res_consulta = pg_query($conexion,"BEGIN");
    $checkeo = $res_consulta;
						    
    $consulta = "UPDATE gnupanel_divisas_usuario SET credito = credito + $fondos WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    //$consulta = "UPDATE pagos_usuario SET importe = $fondos, confirmado = 1, acreditado = 1, acreditacion_pago = now() WHERE id_usuario = $id_usuario AND id = $id_pago ";
    $consulta = "UPDATE pagos_usuario SET confirmado = 1, acreditado = 1, acreditacion_pago = now() WHERE id_usuario = $id_usuario AND id = $id_pago ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;
    			
    if($checkeo)
    {
	$res_consulta = pg_query($conexion,"END");
    }
    else
    {
	$res_consulta = pg_query($conexion,"ROLLBACK");
    }
													
    pg_close($conexion);
    return $checkeo;
}
												
function dame_id_pago_reseller($control,$empresa)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;

    $result = NULL;
    $checkeo = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
						    
    $consulta = "SELECT id,id_reseller,importe FROM pagos_reseller WHERE codigo_seguimiento = '$control' AND empresa_cobranza = '$empresa' AND confirmado = 0 AND acreditado = 0";
    //$consulta = "SELECT id,id_reseller FROM pagos_reseller WHERE codigo_seguimiento = '$control' AND empresa_cobranza = '$empresa' ";
    
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $res_consulta;

    if($checkeo)
    {
	if(pg_num_rows($res_consulta)>0)
	{
	    $result = array();
	    $result = pg_fetch_assoc($res_consulta,0);
	}
    }
    pg_free_result($res_consulta);    			
    pg_close($conexion);
    return $result;
}

function acreditar_fondos_reseller($id_reseller,$id_pago,$fondos)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
			
    $checkeo = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $res_consulta = pg_query($conexion,"BEGIN");
    $checkeo = $res_consulta;
						    
    $consulta = "UPDATE gnupanel_divisas_reseller SET credito = credito + $fondos WHERE id_reseller = $id_reseller ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    //$consulta = "UPDATE pagos_reseller SET importe = $fondos, confirmado = 1, acreditado = 1, acreditacion_pago = now() WHERE id_reseller = $id_reseller AND id = $id_pago ";
    $consulta = "UPDATE pagos_reseller SET confirmado = 1, acreditado = 1, acreditacion_pago = now() WHERE id_reseller = $id_reseller AND id = $id_pago ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;
									
    if($checkeo)
    {
	$res_consulta = pg_query($conexion,"END");
    }
    else
    {
	$res_consulta = pg_query($conexion,"ROLLBACK");
    }
													
    pg_close($conexion);
    return $checkeo;
}

function borrar_pagos_caducados($cant_dias)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;

    $result = NULL;
    $checkeo = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");

    $antiguedad = $cant_dias + 10;
					    
    $consulta = "DELETE FROM pagos_usuario WHERE age(inicio_pago) > interval '$antiguedad day' AND confirmado = 0 AND acreditado = 0";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $res_consulta;

    $consulta = "DELETE FROM pagos_reseller WHERE age(inicio_pago) > interval '$antiguedad day' AND confirmado = 0 AND acreditado = 0";
    
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    pg_close($conexion);
    return $checkeo;
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$requerir = "/etc/gnupanel/gnupanel-usuarios-ini.php";
require($requerir);
//error_reporting(E_ALL);

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$cuentadigital_control = "";
$cuentadigital_url = "https://www.cuentadigital.com/exportacion.php";

$dineromail_url = "https://argentina.dineromail.com/Vender/ConsultaPago.asp";
$dineromail_Email = "";
$dineromail_Acount = "";
$dineromail_Pin = "";

$dias_espera_pagos = 30;
$tope_diferencia_dolar = 4;
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$fechas = dame_dias($dias_espera_pagos);
$dolar = give_me_dolar($tope_diferencia_dolar);

if(!$dolar)
{
    sleep(300);
    $dolar = give_me_dolar($tope_diferencia_dolar);
}

if(!$dolar)
{
    exit("No se pudo obtener la cotizacion del dolar \n");
}

// CuentaDigital BEGIN

$arreglo_data = NULL;
$arreglo_data = array();

foreach($fechas as $dia_cons)
{
    $url = $cuentadigital_url."?control=".$cuentadigital_control."&fecha=".$dia_cons;
    $data_cuentadigital = file_get_contents($url);
    
    if($data_cuentadigital && (strlen($data_cuentadigital)>0))
    {
	$datos = explode ("\n",$data_cuentadigital);
	
	foreach($datos as $data_in)
	{
	    $data_out = explode("|",$data_in);
	    $data['fecha'] = $data_out['0'];
	    $data['cantidad'] = $data_out['2'];
	    $data['codigo'] = $data_out['6'];
	    $data['orden'] = $data_out['9'];
	    $arreglo_data[] = $data;
	}
    }
}

if(is_array($arreglo_data))
{
    foreach($arreglo_data as $valor)
    {
    
	if($data_usuario = dame_id_pago_usuario($valor['codigo'],"cuentadigital"))
	{
    	    //acreditar_fondos_usuario($data_usuario['id_usuario'],$data_usuario['id'],$valor['cantidad']);
    	    $diferencia = abs($data_usuario['importe'] - ($valor['cantidad']/$dolar));
    	    if($tope_diferencia_dolar>$diferencia)
    	    {
    		acreditar_fondos_usuario($data_usuario['id_usuario'],$data_usuario['id'],$data_usuario['importe']);
    	    }
    	    else
    	    {
    		print "CUENTADIGITAL revisar el id de pago usuario ".$data_usuario['id']." la diferencia de valores excede el limite establecido. \n" ;
    	    }
	}
	else
	{
	    if($data_reseller = dame_id_pago_reseller($valor['codigo'],"cuentadigital"))
	    {
    		$diferencia = abs($data_reseller['importe'] - ($valor['cantidad']/$dolar));
		//acreditar_fondos_reseller($data_reseller['id_reseller'],$data_reseller['id'],$valor['cantidad']);
    		if($tope_diferencia_dolar>$diferencia)
    		{
		    acreditar_fondos_reseller($data_reseller['id_reseller'],$data_reseller['id'],$data_reseller['importe']);
    		}
    		else
    		{
    		    print "CUENTADIGITAL revisar el id de pago reseller ".$data_reseller['id']." la diferencia de valores excede el limite establecido. \n" ;
    		}
	    }
	}
    }
}

// CuentaDigital END

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// DineroMail BEGIN

$delta = count($fechas) - 1;

if($delta > 30) $delta = 30;

$fin_fechas = count($fechas) - 1;

$ini_fechas = $fin_fechas - $delta;

$url = $dineromail_url."?"."Email=".$dineromail_Email."&Acount=".$dineromail_Acount."&Pin=".$dineromail_Pin."&StartDate=".$fechas[$ini_fechas]."&EndDate=".$fechas[$fin_fechas]."&XML=1";

$data_dineromail = file_get_contents($url);

if($data_dineromail && (strlen($data_dineromail)>0))
{

    $xml = simplexml_load_file($url);

    $esta_bien = $xml->State;

    if($esta_bien == 1)
    {
	foreach($xml->Pays->Pay as $valor)
	{
	    $cantidad = $valor->Trx_Payment;
	    $control = $valor->Items->Item['Item_Code'];

	    if($data_usuario = dame_id_pago_usuario($control,"dineromail"))
	    {
    		//acreditar_fondos_usuario($data_usuario['id_usuario'],$data_usuario['id'],$cantidad);
    		$diferencia = abs($data_usuario['importe'] - ($cantidad/$dolar));
    		if($tope_diferencia_dolar>$diferencia)
    		{
    		    acreditar_fondos_usuario($data_usuario['id_usuario'],$data_usuario['id'],$data_usuario['importe']);
    		}
    		else
    		{
    		    print "DINEROMAIL revisar el id de pago usuario ".$data_usuario['id']." la diferencia de valores excede el limite establecido. \n" ;
    		}
	    }
	    else
	    {
		if($data_reseller = dame_id_pago_reseller($control,"dineromail"))
		{
		    //acreditar_fondos_reseller($data_reseller['id_reseller'],$data_reseller['id'],$cantidad);
    		    $diferencia = abs($data_reseller['importe'] - ($cantidad/$dolar));
    		    if($tope_diferencia_dolar>$diferencia)
    		    {
    			acreditar_fondos_usuario($data_reseller['id_reseller'],$data_reseller['id'],$data_reseller['importe']);
    		    }
    		    else
    		    {
    			print "DINEROMAIL revisar el id de pago reseller ".$data_reseller['id']." la diferencia de valores excede el limite establecido. \n" ;
    		    }
		}
	    }
	}
    }
}

// DineroMail END
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Descartar Pagos Anteriores BEGIN

borrar_pagos_caducados($dias_espera_pagos);

// Descartar Pagos Anteriores END

?>