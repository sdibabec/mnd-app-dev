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

session_start();

$errores = array();

$data = json_decode( file_get_contents('php://input') );

/*Preparacion de variables*/
$bAplicar = false;

$codigo = $data->eCodAccion ? $data->eCodAccion : $data->eCodPublicacion;
$accion = $data->tCodAccion ? $data->tCodAccion : $data->tAccion;

$eCodPublicacion = $data->eCodPublicacion ? $data->eCodPublicacion : false;
$eCodTipoPublicacion = $data->eCodTipoPublicacion ? $data->eCodTipoPublicacion : false;
$tCodEstatus = $data->tCodEstatus ? "'".$data->tCodEstatus."'" : false;


    $terms = explode(" ",$data->tNombre);
    
    $termino = "";
    
    for($i=0;$i<sizeof($terms);$i++)
    {
        $termino .= " AND tNombre like '%".$terms[$i]."%' ";
    }

$fhFecha = $data->fhFechaInicio ? explode("/",$data->fhFechaInicio) : false;
$fhFecha2 = $data->fhFechaTermino ? explode("/",$data->fhFechaTermino) : false;

$fhFechaInicio = "'".$fhFecha[2]."-".$fhFecha[1]."-".$fhFecha[0]."'";
$fhFechaTermino = $fhFecha2 ? "'".$fhFecha2[2]."-".$fhFecha2[1]."-".$fhFecha2[0]."'" : "'".$fhFechaInicio."'";

$eLimit = 100;
$bOrden = $data->rOrden;
$rdOrden = $data->rdOrden ? $data->rdOrden : 'eCodPublicacion';

$eInicio = $data->eInicio ? (($data->eInicio * 15)-15) : 0;
$eTermino = ($eInicio>0 ? $eInicio : 1) * 15;

$bAll = $_SESSION['bAll'];
$bDelete = $_SESSION['bDelete'];


switch($accion)
{
    case 'D':
        
        $insert = "UPDATE BitPublicaciones SET tCodEstatus = 'EL' WHERE eCodPublicacion = ".$codigo;
        
        break;
    case 'F':
        $insert = "UPDATE BitPublicaciones SET tCodEstatus = 'FI' WHERE eCodPublicacion = ".$codigo;
        
        break;
    case 'C':
        $tHTML =  '<table class="table table-hover" width="100%">'.
        '<thead>'.
        '<tr>'.
        '<th>C&oacute;digo</th>'.
        '<th>T&iacute;tulo</th>'.
        '</tr>'.
        '</thead>'.
        '<tbody>';
        /* hacemos select */
        $select1 =   " SELECT bp.* ".
            " FROM BitPaginas bp ".
            //" INNER JOIN CatEstatus ce ON ce.tCodEstatus = bp.tCodEstatus ".
            " WHERE 1=1 ".
            
            ($tCodEstatus ? " AND bp.tCodEstatus = $tCodEstatus ": "").
            " LIMIT 0, $eLimit ";
        
        $eFilas = mysql_num_rows(mysql_query($select1));
        
        $ePaginas = round($eFilas / 15);
        
        $select = "SELECT * FROM ($select1) N0 ORDER BY $rdOrden $bOrden LIMIT $eInicio, $eTermino";
        
        $rsConsulta = mysql_query($select);
        while($rConsulta=mysql_fetch_array($rsConsulta)){
         /* validamos si est√° cargado */
           
            
            //imprimimos
       $tHTML .=    '<tr>'.
                    '<td>'.menuEmergenteJSON($rConsulta{'eCodPagina'},'cata-pag-con').'</td>'.
                    '<td>'.base64_decode($rConsulta{'tTitulo'}).'</td>'.
                    
                    '</tr>';
            //imprimimos
        }
        /* hacemos select */
        if($ePaginas>1)
        {
        $tHTML .=   '<tr>'.
                    '<td colspan="4" align="right">';
        $tHTML .=   '<nav aria-label="Page navigation example">';
        $tHTML .=   '<ul class="pagination">';
        for($i=1;$i<=$ePaginas;$i++)
        {
            $activo = ($i==$data->eInicio ? 'active' : '');
            $tHTML .= '<li class="page-item '.$activo.'"><a class="page-link" href="#" onclick="asignarPagina(\''.$i.'\')">'.$i.'</a></li>';   
        }
        $tHTML .=   '</ul>';
        $tHTML .=   '</nav>';
        $tHTML .=   '</td>';
        $tHTML .=   '</tr>';
        }
        $tHTML .= '</tbody>'.
            '</table>';
        
        
        
        break;
}
        
 if(!sizeof($errores) && ($accion=="D" || $accion=="F"))
{       
        $rs = mysql_query($insert);

        if(!$rs)
        {
            $errores[] = 'Error al efectuar la operacion '.mysql_error();
        }
        

     if(!sizeof($errores))
     {
         $tDescripcion = "Se ha ".(($accion=="D") ? 'Eliminado' : 'Finalizado')." el paquete c®Ædigo ".sprintf("%07d",$codigo);
         $tDescripcion = "'".utf8_encode($tDescripcion)."'";
         $fecha = "'".date('Y-m-d H:i:s')."'";
         $eCodUsuario = $_SESSION['sessionAdmin']['eCodUsuario'];
         mysql_query("INSERT INTO SisLogs (eCodUsuario, fhFecha, tDescripcion) VALUES ($eCodUsuario, $fecha, $tDescripcion)");
     }
}

echo json_encode(array("exito"=>((!sizeof($errores)) ? 1 : 0), 'errores'=>$errores,'registros'=>(int)mysql_num_rows($rsConsulta),"consulta"=>$tHTML,"query"=>$select));

?>