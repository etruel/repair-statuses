

     <div class="wpap">
            <?php 
                $default_rute = trailingslashit(get_home_path()).'wp-content/plugins/repair-statuses/ots';
                $root_route = trailingslashit(get_home_path());
            ?>
            <?php screen_icon()?>
            <h1>Subir CVS</h1>
            <form id="myformsettings" method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="default_rute" value="<?php print($default_rute); ?>">
                 <?php settings_fields("wprs-group")?>
                 <table class="form-table">
                    <tr>
                        <tr valign="top">
                        <th scope="row">
                            <label>Directorio Actual => </label>
                        </th>
                        <td>
                             <strong><?php print(get_option('wprs_file_cvs')); ?></strong>
                        </td>
                    </tr>

                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="wprs_opciones_titulo">Archivo CVS: </label>
                        </th>
                        <td>
                            <input type="file" id="wprs_file_cvs" name="wprs_file_cvs" value="<?php echo get_option("wprs_opciones_titulo"); ?>" />
                            <br />
                            <small></small>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="wprs_rute_cvs">Intertar Ruta:</label>
                        </th>
                        <td>
                           <strong><?php print($root_route); ?></strong><input type="text" name="wprs_rute_cvs" value="<?php print(get_option('wprs_rute_cvs')); ?>"> <strong>/ots/</strong>
                        </td>
                
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="wprs_character_separator">Caracter Separador:</label>
                        </th>
                        <td>
                            <input <?php checked(get_option('wprs_character_separator'),'|'); ?>  type="radio" name="wprs_character_separator" value="|"> <strong>(|) </strong> 
                            <br>
                            <input <?php checked(get_option('wprs_character_separator'),','); ?>  type="radio" name="wprs_character_separator" value=","> <strong>(,) </strong> 
                        </td>
                    </tr>
                 </table>
                 <p class="submit">
                 <input type="submit" name="submit_settings" id="submit_settings" class="button button-primary" value="Guardar cambios">
                 </p>
            </form>
</div>
