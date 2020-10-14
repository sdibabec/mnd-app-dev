<? header('Access-Control-Allow-Origin: *');  ?>
<? header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method"); ?>
<? header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE"); ?>
<? header("Allow: GET, POST, OPTIONS, PUT, DELETE"); ?>
<? header('Content-Type: application/json'); ?>
<?

if (isset($_SERVER{'HTTP_ORIGIN'})) {
        header("Access-Control-Allow-Origin: {$_SERVER{'HTTP_ORIGIN'}}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

require_once("../cnx/swgc-mysql.php");
include("../inc/fun-ini.php");

date_default_timezone_set('America/Mexico_City');

function generarContenido($datos)
{
    $tHTML .= '<li onclick="verPublicacion('.$datos{'eCodPublicacion'}.')"><img style="border-radius:50%; width:75px; height:75px;" src="'.$img.'">';
        $tHTML .= '     <table width="100%"><tr><td colspan="2"><h2>'.substr(base64_decode($datos{'tTitulo'}),0,30).'</h2></td></tr>';
        $tHTML .= '    <tr><td>'.date('d/m/Y',strtotime($datos{'fhFechaActualizacion'})).'</td><td align="right">';
        $tHTML .= '        <a href="#" class="btn-leer"><i class="fas fa-eye btn-leer"></i></a>';
        $tHTML .= '    </td></tr>';
        $tHTML .= '    </table>';
        $tHTML .= '    </li>';
}

$fhFecha = "'".date('Y-m-d H:i:s')."'";

function base64imagen($datos)
    {
        $nombre = "../cla/fot/".uniqid().'.jpg';
        $datos1 = explode(',', ($datos));
        $content = base64_decode($datos1[1]);
        
        $pf = fopen($nombre,"w");
        fwrite($pf,$content);
        fclose($pf);
        
        return str_replace("../cla/fot","/fot",$nombre);
    }

$errores = array();

$data = json_decode( file_get_contents('php://input') );

$pf = fopen("log.txt","w");
        fwrite($pf,json_encode($data));
        fclose($pf);

//$timg = 'data:image/jpeg;base64,'.base64_encode(file_get_contents("../cni/".$rPublicacion{'tImagen'}));


$tAccion = $data->accion;

$resultados = array();

switch($tAccion)
{
    case 'inse-reg':
        $errores = array();
            $tCorreo = $data->correo ? "'".$data->correo."'" : false;
            $tPasswd = $data->password ? "'".base64_encode($data->password)."'" : false;
        
        if(!$tCorreo){ $errores[] = "El correo es obligatorio"; }
        if(!$tPasswd){ $errores[] = "El password es obligatorio"; }
        
        if(!sizeof($errores))
        {
            $select = "SELECT tNombre usuario, tCorreo correo, tPasswordAcceso password, eCodUsuario codigousuario, tApellidos apellidos, tTelefono telefono FROM SisUsuarios WHERE eCodEstatus = 3 AND tCorreo = $tCorreo AND tPasswordAcceso = $tPasswd";
            $rs = mysql_query($select);
            $r = mysql_fetch_array($rs);
            if(!mysql_num_rows($rs)){ $errores[] = "Error en las credenciales"; }
        }
            
            $resultados = array('exito'=>(mysql_num_rows($rs) ? 1 : 0),'codigousuario'=>$r{'codigousuario'},'usuario'=>$r{'usuario'},'apellidos'=>$r{'apellidos'},'telefono'=>$r{'telefono'},'correo'=>$r{'correo'},'password'=>base64_decode($r{'password'}),'errores'=>implode("\n",$errores));
        break;
    case 'not-con':
        $tHTML = '';
        $select = "SELECT bp.*, su.tNombre tUsuario FROM BitPublicaciones bp INNER JOIN SisUsuarios su ON su.eCodUsuario=bp.eCodUsuario WHERE bp.tCodEstatus IN ('AC','FI') AND bp.eCodTipoPublicacion IN (1,2,4) ORDER BY bp.eCodPublicacion DESC LIMIT 0,32";
    $rsPublicaciones = mysql_query($select);
    while($rPublicacion = mysql_fetch_array($rsPublicaciones))
    { 
        $agotado = ($rPublicacion{'tCodEstatus'}=="FI") ? '<i> [AGOTADO]</i>' : '';
       $img = 'data:image/jpeg;base64,'.base64_encode(file_get_contents("../cni/".$rPublicacion{'tImagen'}));
        $tHTML .= '<li onclick="verPublicacion('.$rPublicacion{'eCodPublicacion'}.')"><a href="#" ><img style="border-radius:50%; width:75px; height:75px;" src="'.$img.'"><h2>'.substr(base64_decode($rPublicacion{'tTitulo'}),0,30).$agotado.'</h2><p>'.date('d/m/Y',strtotime($rPublicacion{'fhFechaActualizacion'})).'</p></a></li>';
    }
        $resultados = array('exito'=>(mysql_num_rows($rsPublicaciones) ? 1 : 0),'tHTML'=>$tHTML);
        break;
    case 'blg-con':
        $tHTML = '';
        $select = "SELECT bp.*, su.tNombre tUsuario FROM BitPublicaciones bp INNER JOIN SisUsuarios su ON su.eCodUsuario=bp.eCodUsuario WHERE bp.tCodEstatus IN ('AC','FI') ORDER BY bp.eCodPublicacion DESC LIMIT 0,32";
    $rsPublicaciones = mysql_query($select);
    while($rPublicacion = mysql_fetch_array($rsPublicaciones))
    { 
        $agotado = ($rPublicacion{'tCodEstatus'}=="FI") ? '<i> [AGOTADO]</i>' : '';
       $img = 'data:image/jpeg;base64,'.base64_encode(file_get_contents("../cni/".$rPublicacion{'tImagen'}));
        $tHTML .= '<li onclick="verPublicacion('.$rPublicacion{'eCodPublicacion'}.')"><a href="#" ><img style="border-radius:50%; width:75px; height:75px;" src="'.$img.'"><h2>'.substr(base64_decode($rPublicacion{'tTitulo'}),0,30).$agotado.'</h2><p>'.date('d/m/Y',strtotime($rPublicacion{'fhFechaActualizacion'})).'</p></a></li>';
    }
        $resultados = array('exito'=>(mysql_num_rows($rsPublicaciones) ? 1 : 0),'tHTML'=>$tHTML);
        break;
    case 'ini-con':
        $tHTML = '';
        $select = "SELECT bp.*, su.tNombre tUsuario FROM BitPublicaciones bp INNER JOIN SisUsuarios su ON su.eCodUsuario=bp.eCodUsuario WHERE bp.tCodEstatus IN ('AC','FI') ORDER BY bp.eCodPublicacion DESC LIMIT 0,10";
    $rsPublicaciones = mysql_query($select);
    while($rPublicacion = mysql_fetch_array($rsPublicaciones))
    { 
        $agotado = ($rPublicacion{'tCodEstatus'}=="FI") ? '<i> [AGOTADO]</i>' : '';
        $img = 'data:image/jpeg;base64,'.base64_encode(file_get_contents("../cni/".$rPublicacion{'tImagen'}));
        $tHTML .= '<li onclick="verPublicacion('.$rPublicacion{'eCodPublicacion'}.')"><a href="#" ><img style="border-radius:50%; width:75px; height:75px;" src="'.$img.'"><h2>'.substr(base64_decode($rPublicacion{'tTitulo'}),0,30).$agotado.'</h2><p>'.date('d/m/Y',strtotime($rPublicacion{'fhFechaActualizacion'})).'</p></a></li>';
    }
        $resultados = array('exito'=>(mysql_num_rows($rsPublicaciones) ? 1 : 0),'tHTML'=>$tHTML);
        break;
    case 'not-det':
        
        //preparamos variables
        
        $codigo = $data->codigo ? $data->codigo : false;
        
        $select = "SELECT bp.*, su.tNombre tUsuario FROM BitPublicaciones bp INNER JOIN SisUsuarios su ON su.eCodUsuario=bp.eCodUsuario WHERE bp.eCodPublicacion = $codigo";
    $rsPublicaciones = mysql_query($select);
    $rPublicacion = mysql_fetch_array($rsPublicaciones);
    
    $tTitulo = base64_decode($rPublicacion{'tTitulo'});
    $tContenido = nl2br(base64_decode($rPublicacion{'tContenido'}));
    $tImagen = 'data:image/jpeg;base64,'.base64_encode(file_get_contents("../cni/".$rPublicacion{'tImagen'}));
    $tUsuario = $rPublicacion{'tUsuario'};
    $fhFecha = date('d/m/Y',strtotime($rPublicacion{'fhFechaActualizacion'}));
        
        $tHTML = '<li><center><h3  style="white-space: normal">'.$tTitulo.'</h3></center></li>';
        $tHTML .= '<li><center><img src="'.$tImagen.'" style="max-width:95%"></center></li>';
        $tHTML .= '<li><hp  style="white-space: normal">'.$tContenido.'</h3></li>';
        
        $resultados = array('tHTML'=>$tHTML);
        break;
    case 'men-con':
        
        //preparamos variables
        
        $eCodUsuario = $data->codigousuario ? $data->codigousuario : false;
        
        $select = "SELECT ".
          " bm.eCodMensaje, bm.tTitulo, bm.tMensaje, bs.tCodEstatus, bm.fhFecha ".
          " FROM BitMensajes bm ".
          " LEFT JOIN BitPublicaciones bp ON bp.eCodPublicacion = bm.eCodPublicacion ".
          " LEFT JOIN BitSolicitudes bs ON bm.eCodSolicitud=bs.eCodSolicitud ".
          " AND bs.eCodPublicacion=bp.eCodPublicacion ".
          //" INNER JOIN CatEstatus ce ON ce.tCodEstatus = bs.tCodEstatus ".
          " WHERE bm.eCodAfiliado = ".$eCodUsuario;
    $rsMensajes = mysql_query($select);
        if(mysql_num_rows($rsMensajes)){
    while($rMensaje = mysql_fetch_array($rsMensajes))
    {
        $tHTML = '<li><a href="#" onclick="readMSG(\''.$rMensaje{'eCodMensaje'}.'\')">'.base64_decode($rMensaje{'tTitulo'}).'</a></li>';
    }
        }
        else
        {
            $tHTML = '<li>Sin mensajes por el momento</li>';
        }
        
        $resultados = array('tHTML'=>$tHTML);
        break;
    case 'afi-reg':
        $eCodUsuario = $data->codigousuario ? $data->codigousuario : false;
        $tNombre    = trim($data->nombre)      ? "'".$data->nombre."'"                 : false;
        $tApellidos = trim($data->apellidos)   ? "'".$data->apellidos."'"              : false;
        $tCorreo    = trim($data->correo)      ? "'".$data->correo."'"                 : false;
        $tTelefono  = trim($data->telefono)    ? "'".$data->telefono."'"               : false;
        $password   = trim($data->password)     ? "'".base64_encode($data->password)."'" :            false;
        
        if(!$eCodUsuario)
        {
        $query = "INSERT INTO SisUsuarios (tNombre,tApellidos,tCorreo,tTelefono,tPasswordAcceso,eCodEstatus,eCodPerfil) VALUES ($tNombre,$tApellidos,$tCorreo,$tTelefono,$password,3,4)";
        }
        else
        {
            $query = "UPDATE SisUsuarios SET tNombre=$tNombre,tApellidos=$tApellidos,tCorreo=$tCorreo,tTelefono=$tTelefono,tPasswordAcceso=$password WHERE eCodUsuario = $eCodUsuario";
        }
        $rs = mysql_query($query);
        
        $pf = fopen("log.txt","w");
        fwrite($pf,$query);
        fclose($pf);
        
        $resultados = array('exito'=>(($rs) ? 1 : 0));
        break;
    case 'men-det':
        
        //preparamos variables
        
        $codigo = $data->codigomensaje ? $data->codigomensaje : false;
        
        $select = "SELECT * FROM BitMensajes WHERE eCodMensaje = $codigo";
    $rsMensaje = mysql_query($select);
    $rMensaje = mysql_fetch_array($rsMensaje);
    
    $tTitulo = base64_decode($rMensaje{'tTitulo'});
    $tContenido = nl2br(base64_decode($rMensaje{'tMensaje'}));
    
        
        $tHTML = '<p align="justify"><b>'.$tTitulo.'</b><br><br>'.$tContenido.'</p>';
        
        $resultados = array('tHTML'=>$tHTML);
        break;
    
}

echo json_encode($resultados);
?>