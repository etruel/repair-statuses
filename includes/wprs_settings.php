

     <div class="wpap">
            <?php 
                $default_rute = trailingslashit(get_home_path()).'wp-content/plugins/repair-statuses/ots/';
                $root_route = trailingslashit( get_home_path()).'ots/';
            ?>
            <?php screen_icon()?>
            <h1>Settings</h1>
            <div class="message-ajax-setting">ENVIANDO</div>
            <form method="post" action="options.php" enctype="multipart/form-data">
                 <?php settings_fields("wprs-group")?>
                 <?php @do_settings_fields("wprs-group")?>
                 <table class="form-table">
                    <tr valign="top">
                        <th scope="row">
                            <label for="wprs_opciones_titulo">File CVS:</label>
                        </th>
                        <td>
                            <input type="file" id="wprs_file_cvs" name="wprs_file_cvs" value="<?php echo get_option("wprs_opciones_titulo")?>" />
                            <br />
                            <small></small>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="wprs_rute_cvs">Cvs file path:</label>
                        </th>
                        <td>
                           <!--<textarea name="wprs_opciones_description"><?php echo get_option("wprs_opciones_description")?></textarea>
                            <br />
                            <small></small>-->
                            <input type="radio" class="radio_setting" <?php checked(get_option('wprs_rute_cvs'),$default_rute); ?>  name="wprs_rute_cvs" value="<?php print($default_rute);?>"> 
                            <strong>Default:</strong>
                            <br>
                            <input type="radio" class="radio_setting" <?php checked(get_option('wprs_rute_cvs'),$root_route); ?> name="wprs_rute_cvs" value="<?php print($root_route);  ?>"/> 
                            <strong>Root Route(<?php print($root_route); ?>)</strong>
                        </td>
                    </tr>
                 </table>
                 <p class="submit">
                 <input type="submit" name="submit" id="submit_settings" class="button button-primary" value="Guardar cambios">
                 </p>
            </form>
</div>
<style type="text/css">
    .message-ajax-setting{
        background-color: white;
        padding: 10px;
        border-left: 4px solid #D6840E;
        font-weight: bold;
        width: 50%;
        box-shadow: 1px 1px 10px #ccc;
        display: none;
    }
</style>
