<?php
require_once("cnx/swgc-mysql.php");
require_once("cls/cls-sistema.php");


$clSistema = new clSis();
session_start();
$bAll = $_SESSION['bAll'];
$bDelete = $_SESSION['bDelete'];


$select = "SELECT * FROM SisMaximosRegistros ORDER BY eRegistros ASC";
$rsMaximos = mysql_query($select);

$select = "SELECT * FROM CatClientes WHERE ecodCliente = ".$_SESSION['sessionAdmin']['eCodCliente'];
$rsCliente = mysql_query($select);
$rCliente = mysql_fetch_array($rsCliente);
        
$select = "SELECT DISTINCT
	ce.tNombre tEstatus,
	ce.tCodEstatus 
FROM
	CatEstatus ce
	INNER JOIN BitPublicaciones be ON be.tCodEstatus = ce.tCodEstatus
    WHERE 1=1 ".
    ($_SESSION['sessionAdmin']['bAll'] ? "" : " AND eCodEstatus<>4").
" ORDER BY
	ce.tNombre ASC";
$rsEstatus = mysql_query($select);

$select = "SELECT * FROM CatTiposPublicaciones WHERE tCodEstatus = 'AC'";
$rsTipos = mysql_query($select);

?>
<script>
function detalles(codigo)
    {
        window.location="?tCodSeccion=cata-eve-det&eCodEvento="+codigo;
    }
function cancelar(codigo)
    {
        window.location="?tCodSeccion=cata-eve-con&eAccion=4&eCodEvento="+codigo;
    }
function finalizar(codigo)
    {
        window.location="?tCodSeccion=cata-eve-con&eAccion=8&eCodEvento="+codigo;
    }
function ruta(codigo)
    {
        window.location="?tCodSeccion=cata-eve-det&eCodEvento="+codigo+"&bRuta=1";
    }
    
$(document).ready(function() {
             filtrar();
    
              //$('#fhFechaConsulta1, #fhFechaConsulta2').datepicker();
    
        $("#fhFechaInicio").datepicker({
            dateFormat: "dd/mm/yy",
    onSelect: function(selectedDate) {
        // Set the minDate of 'to' as the selectedDate of 'from'
        $("#fhFechaTermino").datepicker("option", "minDate", selectedDate);
    }
});
    
$("#fhFechaTermino").datepicker({
    dateFormat: "dd/mm/yy",
    onSelect: function(selectedDate) {
        // Set the minDate of 'to' as the selectedDate of 'from'
        $("#fhFechaInicio").datepicker("option", "maxDate", selectedDate);
    }
});
          
          });
    

</script>
<div class="row">
                            <div class="col-lg-12">
                                
                                
                                <!--filtros-->
                                <div class="col-md-12">
                                    <div id="eRegistros" style="font-size:16px; font-weight:bold;"></div>
                                    <div style="text-align:right;">
                                        <button class="btn btn-info" onclick="mostrarFiltros()" style="display:none">
                                            <i class="zmdi zmdi-filter-list"></i>Filtros</button>
                                        <button class="btn btn-info" onclick="filtrar()">
                                            <i class="zmdi zmdi-search"></i>Consultar</button>
                                    </div>
                                </div>
                                
                                <div class="clearfix"></div>
                                
                                <div id="divFiltros" style="display:none;" class="card card-body table-responsive">
                                <form id="Datos" name="Datos">
                                    <input type="hidden" name="tAccion" id="tAccion" value="">
                                    <input type="hidden" name="eAccion" id="eAccion" value="">
                                    <input type="hidden" name="tCodAccion" id="tCodAccion" value="">
                                    <input type="hidden" name="eCodAccion" id="eCodAccion" value="">
                                    <input type="hidden" name="eInicio" id="eInicio" value="">
                                    <table width="100%" cellpadding="10" class="table">
                                    <tr hidden>
                                        <td width="25%"></td>
                                        <td width="25%"></td>
                                        <td width="25%"></td>
                                        <td width="25%"></td>
                                    </tr>
<tr>
    <td><label><input type="radio" name="rdOrden" value="eCodPagina" checked="checked"> C&oacute;digo</label></td>
    <td>
        <input type="text" class="form-control" name="eCodPagina" id="eCodPagina">
    </td>
    <td></td>
    <td></td>
</tr>

<tr>
    <td>Mostrar</td>
    <td>
        <select id="eMaxRegistros" name="eMaxRegistros" >
        <? while($rRegistro = mysql_fetch_array($rsMaximos)) { ?>
            <option value="<?=$rRegistro{'eRegistros'};?>"><?=$rRegistro{'eRegistros'};?> registros</option>
        <? } ?>
        </select>
    </td>
    <td>Orden</td>
    <td>
        <select id="rOrden" name="rOrden" >
        <option value="DESC">Descendente</option>
        <option value="ASC">Ascendente</option>
        </select>
    </td>
</tr>
</table>
                                    </form>
                                </div>
                                
                                <div class="clearfix" style="padding:10px;"><img src="/images/separador.jpg" class="img-responsive" style="width:100%;"></div>
                                <!--filtros-->
                                
                                <!--tabla-->
                                <div class="card card-body table-responsive table-responsive-data2" id="divXHR" style="min-height:350px;">
                                </div>
                                <!--tabla-->
                                
                            </div>
                        </div>