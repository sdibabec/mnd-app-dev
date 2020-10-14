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
date_default_timezone_set('America/Mexico_City');

function base64imagen($datos)
    {
        $uuid = uniqid();
        $nombre = "../cni/".$uuid.'.jpg';
        $datos1 = explode(',', ($datos));
        $content = base64_decode($datos1[1]);
        
        $pf = fopen($nombre,"w");
        fwrite($pf,$content);
        fclose($pf);
        
        return str_replace("../cni/","",$nombre);
    }

$fhFecha = "'".date('Y-m-d H:i:s')."'";
$eCodUsuario = $_SESSION['sessionAdmin']['eCodUsuario'];


session_start();

$errores = array();

$data = json_decode( file_get_contents('php://input') );

/*Preparacion de variables*/
$eCodPagina        = $data->eCodSlider                    ? $data->eCodSlider                     : false;


$tContenido             = trim($data->tContenido )                  ? "'".utf8_decode($data->tContenido)."'"   : false;


$eCodUsuario = $_SESSION['sessionAdmin']['eCodUsuario'];



if(!$tContenido){ $errores[] = "El contenido es obligatorio"; }



if(!sizeof($errores))
{
    if(!$eCodPagina)
    {
        $query = "INSERT INTO BitSlider
                    (
                        tContenido
                        
                    ) VALUES(
                        $tContenido
                    )";
        $bTipo = 1;
    }
    else
    {
        $query = "UPDATE BitSlider
                    SET
                        tContenido              =   $tContenido
                    WHERE eCodSlider       =   $eCodPagina
                    ";
        $bTipo = 2;
    }
    $rs = mysql_query($query);
    
    $eCodPagina = $eCodPagina ? $eCodPagina : mysql_insert_id();
    
    if(!$rs){ $errores[] = "Error al insertar el dato"; }
    
    
}

if(!sizeof($errores))
{
    $tDescripcion = "Se ha ".(($bTipo==1) ? 'insertado' : 'actualizado')." el slider ".sprintf("%07d",$eCodPagina);
    $tDescripcion = "'".$tDescripcion."'";
    $fecha = "'".date('Y-m-d H:i:s')."'";
    $eCodUsuario = $_SESSION['sessionAdmin']['eCodUsuario'];
    mysql_query("INSERT INTO SisLogs (eCodUsuario, fhFecha, tDescripcion) VALUES ($eCodUsuario, $fecha, $tDescripcion)");
}

echo json_encode(array("exito"=>((!sizeof($errores)) ? 1 : 0), 'errores'=>$errores));

?>