<style type="text/css">
#myformsettings{border:1px solid #C5C5C5;  background:-o-linear-gradient(#fff,#E4E4E4); background:-moz-linear-gradient(#fff,#E4E4E4); background:-webkit-linear-gradient(#fff,#E4E4E4);   width: 950px; overflow: hidden;padding: 10px;margin-top: -15px;}
   h1.title-setting{text-shadow: 1px 1px 1px #000;background-color: #3399CC;color: white;padding: 15px 10px;width: 50%;font-size: 20px;border: 1px solid #006799; border-bottom: 3px solid #277399;}
   .table-setting tr th label{color:#3399CC; font-weight: bold;}
   .image_formulario{color:#3399CC; font-weight: bold; position:relative; float: right; margin-top: -180px; margin-right: 150px; opacity: 0.7; }
   .image_formulario span{font-size: 340px;}
</style>


<div class="wpap form-setting">
            <?php 
                $default_rute = trailingslashit(get_home_path()).'wp-content/plugins/repair-statuses/ots';
                $root_route = trailingslashit(get_home_path());
            ?>
            <?php screen_icon()?>
            <h1 class='title-setting'><span class="dashicons dashicons-hammer"></span> Configuración Repair-Statuses</h1>
            <form id="myformsettings" method="post" action="<?php print(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
                <?php  wp_nonce_field('wprs-settings'); ?>
                <input type="hidden" name="action" value="wprs_import_options_setting">
                <input type="hidden" name="default_rute" value="<?php print($default_rute); ?>"> 
                 <table class="form-table table-setting">
                    <tr>
                        <tr valign="top">
                        <th scope="row">
                            <label><span class="dashicons dashicons-admin-page"></span> Directorio CVS Actual: </label>
                        </th>
                        <td>
                             <strong><?php print($check_options['wprs_file_cvs']); ?></strong>
                        </td>
                    </tr>

                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="wprs_opciones_titulo"><span class="dashicons dashicons-format-aside"></span> Archivo CVS: </label>
                        </th>
                        <td>
                            <input type="file" id="wprs_file_cvs" name="wprs_file_cvs" value="<?php print($check_options['wprs_file_cvs']); ?>" />
                            <br />
                            <small></small>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="wprs_rute_cvs"><span class="dashicons dashicons-category"></span> Insertar Ruta CVS:</label>
                        </th>
                        <td>
                            <input type="hidden" id="ruta_actual" value="<?php print($check_options['wprs_rute_cvs']); ?>">
                           <strong><?php print($root_route); ?></strong><input type="text" id="wprs_rute_cvs" name="wprs_rute_cvs" placeholder="ruta" value="<?php print($check_options['wprs_rute_cvs']); ?>">
                            <p class='readme_setting'>Ingresar una ruta existente en el cual se agregaran por defecto la carpeta <b>/ots/</b> <br>
                                <span style="font-weight: bold;color:red;">NOTA: Para guardar en carpeta RAIZ ingresar en ruta solo 'ots'</span>
                            </p>
                        </td>
                
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="wprs_character_separator"><span class="dashicons dashicons-image-filter"></span> Caracter Separador:</label>
                        </th>
                        <td>
                            <input maxlength="1" style="width:60px;" type="text" name="wprs_character_separator" value="<?php print($check_options['wprs_character_separator']); ?>">
                            <p class='readme_setting'>Separador del archivo CVS <br></p>
                            
                        </td>
                    </tr>
                 </table>
                 <p class="submit">
                 <input type="submit"  name="submit_settings" id="submit_settings" class="button button-primary" value="Guardar cambios">
                 </p>
                 <!--imagen de formulario-->
                 <h3 class="image_formulario"><span class="dashicons dashicons-admin-generic"></span></h3>
        </form>
</div>
<script type="text/javascript">
    jQuery(document).ready(function($){
        //obtener ruta actual
        var ruta_actual = $("#ruta_actual").val();
        var ruta_cambio = '';
        var file = '';

        jQuery(document).on('click','#submit_settings',function(e){
            //ya tenemos ambos datos esta pequeña validacion es para saber que vamos a cambiar de directorio
            file = $("#wprs_file_cvs").val();
            ruta_cambio = $("#wprs_rute_cvs").val();
            if(ruta_actual!=ruta_cambio && file==''){
                alert('Debe seleccionar archivo CVS si desea cambiar de directorio');
                e.preventDefault();
            }
            //no enviar nada
        });
    });
</script>