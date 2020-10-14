<?php
require_once("cnx/swgc-mysql.php");
require_once("cls/cls-sistema.php");


$clSistema = new clSis();
session_start();

$bAll = $_SESSION['bAll'];
$bDelete = $_SESSION['bDelete'];

$select = "SELECT bp.* FROM BitSlider bp WHERE bp.eCodSlider = ".($_GET['v1'] ? $_GET['v1'] : 1);
$rsPublicacion = mysql_query($select);
$rPublicacion = mysql_fetch_array($rsPublicacion);


?>



	<script type="text/javascript">
		function readURL(input,destino) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				reader.onload = function (e) {
					$('#falseinput').attr('src', e.target.result);
					$('#base').val(e.target.result);
          document.getElementById(destino).value=e.target.result;
          //llenar();
				};
				reader.readAsDataURL(input.files[0]);
			}
		}
        
        function guardarImagen() {
  var preview = document.getElementById('imgArchivo');
  var file    = document.getElementById('tArchivo').files[0];
  var reader  = new FileReader();

  reader.onloadend = function () {
    preview.value = reader.result;

  if (file) {
    reader.readAsDataURL(file);
  } else {
    preview.value = "";
  }
}
}
</script>
    <form id="datos" name="datos" action="<?=$_SERVER['REQUEST_URI']?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="eCodSlider" id="eCodSlider" value="<?=($_GET['v1'] ? $_GET['v1'] : 1)?>">
        <input type="hidden" name="nvaFecha" id="nvaFecha">
        <input type="hidden" name="eAccion" id="eAccion">
       <div class="row">
            <div class="col-md-12">
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title"></h5>
                        <!--campos-->
                        
          
                                        
           <div class="position-relative form-group">    
              <label>Contenido</label>
               <textarea class="form-control" name="tContenido" id="trumbowyg-demo"  placeholder="Contenido" rows="10" style="resize:none;"><?=$rPublicacion{'tContenido'} ? utf8_encode($rPublicacion{'tContenido'}) : ""?></textarea>
            </div>
                       
            
                        <!--campos-->
                    </div>
                </div>
            </div>
        </div>
    </form>
   
<script>
    
    //autocompletes
   
   
    

		</script>