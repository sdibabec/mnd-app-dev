<?
include("swgc-mysql.php");


$sql = array();
$sql[] = "CREATE TABLE `SisMaximosRegistros` (`eCodMaximo` int(11) NOT NULL, `eRegistros` int(11) NOT NULL ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

$sql[] = "INSERT INTO `SisMaximosRegistros` (`eCodMaximo`, `eRegistros`) VALUES (1, 10)";
$sql[] = "INSERT INTO `SisMaximosRegistros` (`eCodMaximo`, `eRegistros`) VALUES (2, 25)";
$sql[] = "INSERT INTO `SisMaximosRegistros` (`eCodMaximo`, `eRegistros`) VALUES (3, 50)";
$sql[] = "INSERT INTO `SisMaximosRegistros` (`eCodMaximo`, `eRegistros`) VALUES (4, 100)";
$sql[] = "INSERT INTO `SisMaximosRegistros` (`eCodMaximo`, `eRegistros`) VALUES (5, 250)";

$sql[] = "DELETE FROM SisSeccionesMenusEmergentes WHERE tTitulo = 'Marcar como agotado' AND tCodSeccion = 'cata-pub-con'";


foreach($sql as $insert)
{
    $rs = mysql_query($insert);
    echo $rs ? 'Exito' : 'Error: '.$insert.' '.mysql_error();
    echo '<br>';
}
?>