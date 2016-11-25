

     <div class="wpap">
            <?php 
                $default_rute = trailingslashit(get_home_path()).'wp-content/plugins/repair-statuses/ots';
                $root_route = trailingslashit(get_home_path());
            ?>
            <?php screen_icon()?>
            <h1>Configuración Repair-Statuses</h1>
            <form id="myformsettings" method="post" action="<?php print(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
                <?php  wp_nonce_field('wprs-settings'); ?>
                <input type="hidden" name="action" value="wprs_import_options_setting">
                <input type="hidden" name="default_rute" value="<?php print($default_rute); ?>"> 
                 <table class="form-table">
                    <tr>
                        <tr valign="top">
                        <th scope="row">
                            <label>Directorio CVS Actual => </label>
                        </th>
                        <td>
                             <strong><?php print($check_options['wprs_file_cvs']); ?></strong>
                        </td>
                    </tr>

                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="wprs_opciones_titulo">Archivo CVS: </label>
                        </th>
                        <td>
                            <input type="file" id="wprs_file_cvs" name="wprs_file_cvs" value="<?php print($check_options['wprs_file_cvs']); ?>" />
                            <br />
                            <small></small>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="wprs_rute_cvs">Insertar Ruta CVS:</label>
                        </th>
                        <td>
                           <strong><?php print($root_route); ?></strong><input type="text" name="wprs_rute_cvs" value="<?php print($check_options['wprs_rute_cvs']); ?>">
                            <p class='readme_setting'>Ingresar una ruta existente en el cual se agregaran por defecto la carpeta <b>/ots/</b> <br></p>
                        </td>
                
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="wprs_character_separator">Caracter Separador:</label>
                        </th>
                        <td>
                            <input maxlength="1" style="width:60px;" type="text" name="wprs_character_separator" value="<?php print($check_options['wprs_character_separator']); ?>">
                            <p class='readme_setting'>Separador del archivo CVS <br></p>
                            
                        </td>
                    </tr>
                 </table>
                 <p class="submit">
                 <input type="submit" name="submit_settings" id="submit_settings" class="button button-primary" value="Guardar cambios">
                 </p>
            </form>
</div>