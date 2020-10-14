<?php
include("cnx/swgc-mysql.php");
session_start();
date_default_timezone_set('America/Mexico_City');

ini_set(session.cookie_lifetime, 108000);
ini_set(session.gc_maxlifetime, 108000);

/* establecer el limitador de caché a 'private' */

session_cache_limiter('private');
$cache_limiter = session_cache_limiter();

/* establecer la caducidad de la caché a 30 minutos */
session_cache_expire(30);
$cache_expire = session_cache_expire();

class clSis
{
	public function __construct()
	{
		$select = "SELECT tValor FROM SisVariables WHERE tNombre = 'tURL'";
        $rCFG = mysql_fetch_array(mysql_query($select));
        $this->url = $rCFG{'tValor'};
	}
	public function iniciarSesion()
	{
		$tCorreo = "'".$_POST['tCorreo']."'";
		$tPasswordAcceso = "'".base64_encode($_POST['tPasswordAcceso'])."'";
		
		$select = "SELECT * FROM SisUsuarios WHERE eCodEstatus=3 AND tCorreo = $tCorreo AND tPasswordAcceso = $tPasswordAcceso";
		$rsUsuario = mysql_query($select);
		$rUsuario = mysql_fetch_array($rsUsuario);
        
		if($rsUsuario)
		{
			$_SESSION['sessionAdmin'] = $rUsuario;
            $rInicio = mysql_fetch_array(mysql_query("SELECT * FROM SisSeccionesPerfilesInicio WHERE eCodPerfil = ".$rUsuario{'eCodPerfil'}));
            $url = base64_encode($this->generarUrl($rInicio{'tCodSeccion'}));
            
            mysql_query("INSERT INTO SisUsuariosAccesos (eCodUsuario, fhFecha) VALUES (".$rUsuario{'eCodUsuario'}.",'".date('Y-m-d H:i:s')."')");
            
            if($rUsuario{'eCodPerfil'}==4)
            { $this->consultarPromotoria(); }
            
			return array('exito'=>1,'seccion'=>$url);
		}
		else
		{
			return array('exito'=>0);
		}
	}
	
	public function cargarSeccion($seccion)
	{
		//$res->validarSeccion($_GET['tCodSeccion']);
		//$res = $this->validarSeccion($_GET['tCodSeccion']);
	
        
        //generar url de archivo
        
        //incluimos
			$fichero = 'mod/'.$_GET['tDirectorio'].'/'.$_GET['tCodSeccion'].'.php';
            
            mysql_query("INSERT INTO SisUsuariosSeccionesAccesos (eCodUsuario, tCodSeccion,  fhFecha) VALUES (".$_SESSION['sessionAdmin']['eCodUsuario'].",'".$_GET['tCodSeccion']."','".date('Y-m-d H:i:s')."')");
			//echo ($fichero);
			return include($fichero);
		
        

	}
   
	
	public function generarMenu()
	{
		$tMenu = '';
        $select = "SELECT * FROM CatTiposSecciones ORDER BY ePosicion ASC";
        $rsTiposSecciones = mysql_query($select);
        while($rTipoSeccion = mysql_fetch_array($rsTiposSecciones))
        {
            /* ****** Cargamos las secciones ******** */
            $select = "	SELECT DISTINCT
						ss.tCodSeccion,
						ss.tTitulo,
						ss.tIcono,
                        ss.ePosicion
					FROM SisSecciones ss".
					($_SESSION['sessionAdmin']['bAll'] ? "" : " INNER JOIN SisSeccionesPerfiles ssp ON ssp.tCodSeccion = ss.tCodSeccion").
					" WHERE
					ss.eCodEstatus = 3 ".
					//" AND ss.tCodPadre = 'sis-dash-con' ".
                    " AND ss.tCodTipoSeccion='".$rTipoSeccion{'tCodTipoSeccion'}."' ".
					($_SESSION['sessionAdmin']['bAll'] ? "" :
					" AND
					ssp.eCodPerfil = ".$_SESSION['sessionAdmin']['eCodPerfil']).
                    " ORDER BY ss.ePosicion ASC";
            

		      $rsMenus = mysql_query($select);
            
                if(mysql_num_rows($rsMenus))
                { $tMenu .= '<li class="app-sidebar__heading" style="position: relative; z-index: 9999;">'.$rTipoSeccion{'tNombre'}.'</li>'; }
            
                  
		          while($rMenu = mysql_fetch_array($rsMenus))
		          {
                        $url = $this->generarUrl($rMenu{'tCodSeccion'});
		          	    $activo = ($_GET['tCodSeccion']==$rMenu{'tCodSeccion'}) ? 'class="mm-active"' : '';
		          	    $bArchivo = $url;
                      
                        $bOcultar = (
                            ($rMenu{'bSesion'} && 
                             ($_SESSION['sessionAdmin']['eCodPerfil']!=3 && $_SESSION['sessionAdmin']['eCodPerfil']!=4)) ? 'display:none;' : '');
                      
		          	    $tMenu .= '<li  style="position: relative; z-index: 9999; '.$bOcultar.'" '.$activo.'>
                                      <a href="'.$this->url.$bArchivo.'"><i class="'.($rTipoSeccion{'tIcono'}).'"></i> '.utf8_encode($rMenu{'tTitulo'}).'</a>
                                  </li>';
		          }
                 
              
            /* ****** Cargamos las secciones ******** */
        }
		
		return $tMenu;
	}
	
	public function validarSeccion($seccion)
	{
		$select = 	"SELECT * FROM SisSeccionesPerfiles ".
					($_SESSION['sessionAdmin']['bAll'] ? "" : " WHERE eCodPerfil = ".$_SESSION['sessionAdmin']['eCodPerfil']." AND tCodSeccion = '".$seccion."'");
		
		$rsSeccion = mysql_query($select);
		$rSeccion = mysql_fetch_array($rsSeccion);
		return $rSeccion{'tCodSeccion'} ? $rSeccion{'tCodSeccion'} : false;
	}
	
	public function tituloSeccion($seccion)
	{
		$select = 	"SELECT tTitulo FROM SisSecciones ".
					" WHERE tCodSeccion = '".$seccion."'";
		
		$rsSeccion = mysql_query($select);
		$rSeccion = mysql_fetch_array($rsSeccion);
		return $rSeccion{'tTitulo'} ? utf8_encode($rSeccion{'tTitulo'}) : false;
	}
	
	public function validarEnlace($seccion)
	{
		$select = 	"SELECT * FROM SisSeccionesPerfiles ".
					($_SESSION['sessionAdmin']['bAll'] ? "" : " WHERE eCodPerfil = ".$_SESSION['sessionAdmin']['eCodPerfil']." AND tCodSeccion = '".$seccion."'");
		
		$rsSeccion = mysql_query($select);
		if(mysql_num_rows($rsSeccion)<1)
		{
			return false;
		}
        else
        {
            return true;
        }
	}
	
	public function registrarUsuario()
    {
        $eCodUsuario = $_POST['eCodUsuario'] ? $_POST['eCodUsuario'] : false;
        $eCodPerfil = $_POST['eCodPerfil'] ? $_POST['eCodPerfil'] : false;
        $tNombre = $_POST['tNombre'] ? "'".utf8_encode($_POST['tNombre'])."'" : false;
        $tApellidos = $_POST['tApellidos'] ? "'".utf8_encode($_POST['tApellidos'])."'" : false;
        $tPasswordAcceso = $_POST['tPasswordAcceso'] ? "'".base64_encode($_POST['tPasswordAcceso'])."'" : false;
        $tPasswordOperaciones = $_POST['tPasswordOperaciones'] ? "'".base64_encode($_POST['tPasswordOperaciones'])."'" : false;
        $tCorreo = $_POST['tCorreo'] ? "'".$_POST['tCorreo']."'" : false;
        $bAll = $_POST['bAll'] ? 1 : 0;
        
        $fhFechaCreacion = "'".date('Y-m-d H:i:s')."'";
        
        if(!$eCodUsuario)
        {
            $insert = "INSERT INTO SisUsuarios (tNombre, tApellidos, tCorreo, tPasswordAcceso, tPasswordOperaciones,  eCodEstatus, eCodPerfil, fhFechaCreacion,bAll) VALUES ($tNombre, $tApellidos, $tCorreo, $tPasswordAcceso, $tPasswordOperaciones, 3, $eCodPerfil, $fhFechaCreacion,$bAll)";
        }
        else
        {
            $insert = "UPDATE SisUsuarios SET
            tPasswordAcceso = $tPasswordAcceso,
            tPasswordOperaciones = $tPasswordOperaciones,
            eCodPerfil = $eCodPerfil,
            bAll = $bAll
            WHERE
            eCodUsuario = $eCodUsuario";
        }
        
        $rsUsuario = mysql_query($insert);
        
        return $rsUsuario ? true : false;
    }
    
    public function actualizarPerfil()
    {
        $eCodUsuario = $_POST['eCodUsuario'] ? $_POST['eCodUsuario'] : false;
        $tPasswordAcceso = $_POST['tPasswordAcceso'] ? "'".base64_encode($_POST['tPasswordAcceso'])."'" : false;
        $tPasswordOperaciones = $_POST['tPasswordOperaciones'] ? "'".base64_encode($_POST['tPasswordOperaciones'])."'" : false;
        $tCorreo = $_POST['tCorreo'] ? "'".$_POST['tCorreo']."'" : false;
        
        $fhFechaCreacion = "'".date('Y-m-d H:i:s')."'";
        
            $insert = "UPDATE SisUsuarios SET
            tPasswordAcceso = $tPasswordAcceso,
            tPasswordOperaciones = $tPasswordOperaciones
            WHERE
            eCodUsuario = $eCodUsuario";
        
        
        $rsUsuario = mysql_query($insert);
        
        $this->cerrarSesion();
        
        return $rsUsuario ? true : false;
    }
	
	public function cerrarSesion()
	{
		$_SESSION = array();
		$_SESSION['sessionAdmin'] = NULL;
		session_destroy();
	}
    
    public function consultarPromotoria()
    {
        $select =   " SELECT cc.tNombres tCliente, bp.eCodPromotoria, ".
            " bp.fhFechaPromotoria, ce.tIcono estatus, ".
            " pp.eCodTienda, ct.tNombre tTienda ".
            " FROM BitPromotoria bp ".
            " INNER JOIN CatClientes cc ON cc.eCodCliente = bp.eCodCliente ".
            " INNER JOIN CatEstatus ce ON ce.eCodEstatus = bp.eCodEstatus ".
            " INNER JOIN RelPromotoriasPromotores pp ON pp.eCodPromotoria = bp.eCodPromotoria ".
            " INNER JOIN RelPromotoriasSupervisores ps ON ps.eCodPromotoria = bp.eCodPromotoria ".
            " INNER JOIN CatTiendas ct ON ct.eCodTienda = pp.eCodTienda ".
            " WHERE DATE(bp.fhFechaPromotoria) = '".date('Y-m-d')."'".
            ($_SESSION['sessionAdmin']['eCodPerfil']==3 ? " AND ps.eCodSupervisor = ".$_SESSION['sessionAdmin']['eCodUsuario'] : "").
            ($_SESSION['sessionAdmin']['eCodPerfil']==4 ? " AND pp.eCodPromotor = ".$_SESSION['sessionAdmin']['eCodUsuario'] : "").
            " LIMIT 0,1 ";
        $rsConsulta = mysql_query($select);
        
        if(mysql_num_rows($rsConsulta)==1)
        {
            $r = mysql_fetch_array($rsConsulta);
            $_SESSION['sesionPromotoria'] = $r;
        }
        else
        {
            $_SESSION['sesionPromotoria'] = false;
        }
    }
	
	//Secciones
	public function validarPermiso($seccion)
	{
        unset($_SESSION['bAll']);
        
		$bAll = $_SESSION['sessionAdmin']['bAll'];
		$select = 	"SELECT * FROM SisSeccionesPerfiles ".
					($bAll ? "" : " WHERE eCodPerfil = ".$_SESSION['sessionAdmin']['eCodPerfil']." AND tCodSeccion = '".$seccion."'");
		
		$rsSeccion = mysql_query($select);
		$rSeccion = mysql_fetch_array($rsSeccion);
		if($rSeccion{'bAll'} || $bAll)
		{
            $_SESSION['bAll'] = 1;
			return true;
		}
        else
        {
            $_SESSION['bAll'] = 0;
            return false;
        }
	}
    
    public function validarEliminacion($seccion)
	{
        unset($_SESSION['bDelete']);
		$bAll = $_SESSION['sessionAdmin']['bAll'];
		$select = 	"SELECT * FROM SisSeccionesPerfiles ".
					($bAll ? "" : " WHERE eCodPerfil = ".$_SESSION['sessionAdmin']['eCodPerfil']." AND tCodSeccion = '".$seccion."'");
		
		$rsSeccion = mysql_query($select);
		$rSeccion = mysql_fetch_array($rsSeccion);
		if($rSeccion{'bDelete'} || $bAll)
		{
            $_SESSION['bDelete'] = 1;
			return true;
		}
        else
        {
            $_SESSION['bDelete'] = 0;
            return false;
        }
	}
	
    private function base64toImage($data)
    {
        $fname = "inv/".uniqid().'.jpg';
        $data1 = explode(',', base64_decode($data));
        $content = base64_decode($data1[1]);
        //$img = filter_input(INPUT_POST, "image");
        //$img = str_replace(array('data:image/png;base64,','data:image/jpg;base64,'), '', base64_decode($data));
        //$img = str_replace(' ', '+', $img);
        //$img = base64_decode($img);
        
        //file_put_contents($fname, $img);
        
        $pf = fopen($fname,"w");
        fwrite($pf,$content);
        fclose($pf);
        
        return $fname;
    }
    
    private function generarUrl($seccion)
    {
        $base = explode('-',$seccion);
        $tAccion = $base[2];
        $tTipo = $base[0];
        $tSeccion = $base[1];
        
        $select = "SELECT tNombre FROM SisSeccionesReemplazos WHERE tBase = '".$tAccion."'";
        $rAccion = mysql_fetch_array(mysql_query($select));
        
        $select = "SELECT tNombre FROM SisSeccionesReemplazos WHERE tBase = '".$tTipo."'";
        $rTipo = mysql_fetch_array(mysql_query($select));
        
        $select = "SELECT tNombre FROM SisSeccionesReemplazos WHERE tBase = '".$tSeccion."'";
        $rSeccion = mysql_fetch_array(mysql_query($select));
        
        $select = "SELECT * FROM SisSecciones WHERE tCodSeccion = '$seccion'";
        $rsUrlSeccion = mysql_query($select);
        $rUrlSeccion = mysql_fetch_array($rsUrlSeccion);
        
        $url = $rUrlSeccion{'tDirectorio'}.'/'.$seccion.'/'.$rAccion{'tNombre'}.'-'.$rTipo{'tNombre'}.'-'.$rSeccion{'tNombre'}.'/';
        
        return $url;
    }
    
    public function seccionPadre($seccion)
    {
        $select = "SELECT tCodPadre FROM SisSecciones WHERE tCodSeccion = '$seccion'";
        $rsSeccion = mysql_query($select);
        $rSeccion = mysql_fetch_array($rsSeccion);
        
        return generarURL($rSeccion{'tCodPadre'});
    }
}



?>