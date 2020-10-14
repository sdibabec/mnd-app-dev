<?
include("swgc-mysql.php");

//mysql_query("DELETE FROM SisSeccionesBotones WHERE tCodpadre = 'sis-dash-con' AND tFuncion like '%verFecha%'");
//mysql_query("ALTER TABLE SisUsuarios ADD tTelefono VARCHAR(20) NOT NULL AFTER tCorreo;");

//$insert = "ALTER TABLE SisUsuarios ADD tTelefono VARCHAR(20) NOT NULL AFTER tCorreo;";
//$insert = "CREATE TABLE BitEstudiosUsuarios (eCodUsuario INT,pregunta1 VARCHAR(30),pregunta2 VARCHAR(30),pregunta3 VARCHAR(30),pregunta4 VARCHAR(30),pregunta5 VARCHAR(30),pregunta6 VARCHAR(30),pregunta7 VARCHAR(30));";
//$insert = "CREATE TABLE BitMensajes(eCodMensaje INT PRIMARY KEY AUTO_INCREMENT,eCodPublicacion INT NOT NULL,eCodSolicitud INT NOT NULL,eCodUsuario INT NOT NULL,fhFecha DATETIME NOT NULL,tTitulo VARCHAR(100),tMensaje TEXT);";
//$rs = mysql_query($insert);
//echo $rs ? 'Exito' : 'Error';

//$insert = "CREATE TABLE BitSolicitudes(eCodSolicitud INT PRIMARY KEY AUTO_INCREMENT,eCodPublicacion INT,eCodUsuario INT,fhFecha DATETIME,tCodEstatus CHAR(2) DEFAULT 'NU');";
//$rs = mysql_query($insert);
//echo $rs ? 'Exito' : 'Error';

//$insert = "CREATE TABLE RelUsuariosDomicilios (  eCodUsuario int(11) DEFAULT NULL,  tCalle varchar(150) DEFAULT NULL,  tNumero varchar(20) DEFAULT NULL,  tColonia varchar(50) DEFAULT NULL,  tDelegacion varchar(50) DEFAULT NULL,  tMunicipio varchar(50) DEFAULT NULL,  tEstado varchar(50) DEFAULT NULL,  tCP varchar(10) DEFAULT NULL,  tReferencia varchar(200) NOT NULL,  tNombre varchar(150) DEFAULT NULL,  eEdad int(11) DEFAULT NULL,  tDiagnostico text,  tHospital text,  tArchivoID varchar(200) DEFAULT NULL,  tArchivoComprobante varchar(200) DEFAULT NULL,  tArchivoReceta varchar(200) DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;";


$sql = array();
//$sql[] = "ALTER TABLE BitMensajes CHANGE eCodPublicacion eCodPublicacion INT(11) NULL;";
//$sql[] = "ALTER TABLE BitMensajes CHANGE eCodSolicitud eCodSolicitud INT(11) NULL;";
//$sql[] = "ALTER TABLE BitMensajes ADD eCodAfiliado INT NULL AFTER eCodUsuario;";
//$sql[] = "INSERT INTO SisSecciones (tCodSeccion, tCodPadre, tCodTipoSeccion, tDirectorio, tTitulo, eCodEstatus, ePosicion, bFiltro, bPublico, tIcono) VALUES ('cata-men-usr', 'cata-afi-con', '', 'afi', '+ Mensaje', '3', '3', '0', NULL, 'fa fa-file-text-o');";
//$sql[] = "INSERT INTO SisSeccionesMenusEmergentes (eCodMenuEmergente, tCodPadre, tCodSeccion, tCodPermiso, tTitulo, tAccion, tFuncion, tValor, ePosicion) VALUES (NULL, 'cata-afi-con', 'cata-men-usr', 'A', 'Enviar mensaje', 'detalles', 'window.location=\'url\';', 'detalles', '3');";
//$sql[] = "INSERT INTO SisSeccionesBotones (eCodRegistro, tCodPadre, tCodSeccion, tCodBoton, tFuncion, tEtiqueta, ePosicion) VALUES (NULL, 'cata-men-usr', 'cata-men-usr', 'GU', 'guardar();', NULL, '1');";
//$sql[] = "INSERT INTO SisSeccionesReemplazos (eCodReemplazo, tBase, tNombre) VALUES (NULL, 'men', 'mensaje');";
//$sql[] = "DELETE FROM SisUsuarios WHERE eCodPerfil = 4;";
//$sql[] = "INSERT INTO SisSecciones (tCodSeccion, tCodPadre, tCodTipoSeccion, tDirectorio, tTitulo, eCodEstatus, ePosicion, bFiltro, bPublico, tIcono) VALUES ('cata-sol-con', 'sis-dash-con', 'con', 'sol', 'Afiliados', '3', '4', '1', NULL, 'fa fa-file-text-o'), ('cata-sol-det', 'cata-sol-con', '', 'sol', 'Detalles de Afiliado', '3', '2', '0', '1', 'fa fa-file-text-o');";
//$sql[] = "INSERT INTO SisSeccionesBotones (eCodRegistro, tCodPadre, tCodSeccion, tCodBoton, tFuncion, tEtiqueta, ePosicion) VALUES (NULL, 'cata-sol-det', 'cata-sol-det', 'GU', 'asignarEstatus(\'AC\');', 'Autorizar', '1'), (NULL, 'cata-sol-det', 'cata-sol-det', 'RE', 'asignarEstatus(\'RE\');', 'Rechazar', '2');";

//$sql[] = "UPDATE SisSecciones SET tTitulo = 'Solicitudes' WHERE tCodSeccion = 'cata-sol-con'";
//$sql[] = "UPDATE SisSecciones SET tTitulo = 'Detalles de Solicitud' WHERE tCodSeccion = 'cata-sol-det'";
//$sql[] = "INSERT INTO SisSeccionesReemplazos (eCodReemplazo, tBase, tNombre) VALUES (NULL, 'sol', 'solicitud');";
//$sql[] = "UPDATE SisSeccionesBotones SET tCodSeccion = 'cata-afi-reg', tFuncion = 'guardar();' WHERE tCodPadre = 'cata-afi-reg'; ";
//$sql[] = "UPDATE SisSeccionesBotones SET tCodSeccion = 'cata-afi-con' WHERE tCodPadre = 'cata-afi-reg';";
//$sql[] = "INSERT INTO SisSecciones (tCodSeccion, tCodPadre, tCodTipoSeccion, tDirectorio, tTitulo, eCodEstatus, ePosicion, bFiltro, bPublico, tIcono) VALUES ('cata-sol-arc', 'cata-sol-con', '', 'sol', 'Carga de Archivos', '3', '2', '0', '1', 'fa fa-file-text-o');";
//$sql[] = "INSERT INTO SisSeccionesBotones (eCodRegistro, tCodPadre, tCodSeccion, tCodBoton, tFuncion, tEtiqueta, ePosicion) VALUES (NULL, 'cata-sol-arc', 'cata-sol-arc', 'GU', 'guardar();', NULL, '1');";
//$sql[] = "INSERT INTO SisSeccionesReemplazos (eCodReemplazo, tBase, tNombre) VALUES (NULL, 'arc', 'archivo');";
//$sql[] = "INSERT INTO SisSeccionesMenusEmergentes (eCodMenuEmergente, tCodPadre, tCodSeccion, tCodPermiso, tTitulo, tAccion, tFuncion, tValor, ePosicion) VALUES (NULL, 'cata-sol-con', 'cata-sol-arc', 'A', 'Subir Archivos', 'editar', 'window.location=\'url\';', 'editar', '5');";

//$sql[] = "ALTER TABLE BitSolicitudes ADD tArchivoRecepcion VARCHAR(200) NULL AFTER tCodEstatus, ADD tArchivoPago VARCHAR(200) NULL AFTER tArchivoRecepcion;";
//$sql[] = "DELETE FROM SisSeccionesBotones WHERE tCodBoton = 'XL';";

//$sql[] = "UPDATE SisSeccionesBotones SET tCodSeccion = 'cata-sol-reg', tCodPadre = 'cata-sol-reg' WHERE tCodSeccion = 'cata-sol-det'";

//$sql[] = "INSERT INTO SisSecciones (tCodSeccion, tCodPadre, tCodTipoSeccion, tDirectorio, tTitulo, eCodEstatus, ePosicion, bFiltro, bPublico, tIcono) VALUES ('cata-sol-reg', 'cata-sol-con', '', 'sol', 'Autorizacion de Solicitud', '3', '2', '0', '1', 'fa fa-file-text-o');";

//$sql[] = "INSERT INTO SisSeccionesBotones (eCodRegistro, tCodPadre, tCodSeccion, tCodBoton, tFuncion, tEtiqueta, ePosicion) VALUES (NULL, 'cata-sol-det', 'cata-sol-con', 'CO', NULL, NULL, '1');";

//$sql[] = "INSERT INTO SisSeccionesMenusEmergentes (eCodMenuEmergente, tCodPadre, tCodSeccion, tCodPermiso, tTitulo, tAccion, tFuncion, tValor, ePosicion) VALUES (NULL, 'cata-sol-con', 'cata-sol-det', 'A', 'Detalle', 'editar', 'window.location=\'url\';', 'editar', '3');";

//$sql[] = "UPDATE SisSeccionesMenusEmergentes SET tCodSeccion = 'cata-sol-reg' WHERE tCodPadre = 'cata-sol-con' AND ePosicion = 1";

//$sql[] = "UPDATE SisSecciones SET tTitulo = 'Beneficiarios' WHERE tCodSeccion = 'cata-afi-con'; ";
//$sql[] = "UPDATE SisSecciones SET tTitulo = 'Detalles del Beneficiario' WHERE tCodSeccion = 'cata-afi-det';";
$sql[] = "UPDATE SisSecciones SET tTitulo = '+ Beneficiario' WHERE tCodSeccion = 'cata-afi-reg';";

//$sql[] = "DELETE FROM SisSeccionesBotones WHERE tCodBoton = 'CO';";

//$sql[] = "DELETE FROM SisSeccionesBotones WHERE tCodBoton = 'NU' AND tCodPadre = 'cata-afi-con';";

//$sql[] = "UPDATE SisUsuarios SET tCorreo = CONCAT(tCorreo,'-eliminado-') WHERE eCodEstatus = 7;";

foreach($sql as $insert)
{
    $rs = mysql_query($insert);
    echo $rs ? 'Exito' : 'Error: '.$insert.' '.mysql_error();
    echo '<br>';
}
?>