<?php


	
//creamos la clase para el settings
class wprs_to_repair_settings {

		
		function __construct() {	

			//pasando los datos via post
			add_action( 'admin_post_wprs_import_options_setting', array(__CLASS__, 'import_options_setting'));
			/* Add the entry views AJAX actions to the appropriate hooks. */
			add_action( 'wp_ajax_estadorep', array(__CLASS__,'reparaciones_ajax' ));
			add_action( 'wp_ajax_nopriv_estadorep', array(__CLASS__,'reparaciones_ajax' ));
			/* Add the [entry-views] shortcode. */
			add_shortcode( 'reparaciones_form', array(__CLASS__,'print_reparaciones_form' ));
			add_action('admin_menu',  array(__CLASS__, 'options_submenu_page'));


		}


		public static function options_submenu_page() {
			add_submenu_page(
				'options-general.php',          // admin page slug
				  __( 'Ajustes Repair Statuses', '' ), // page title
				  __( 'Ajustes Repair Statuses', '' ), // menu title
				  'manage_options',               // capability required to see the page
				  'wprs_file_cvs',                // admin page slug, e.g. options-general.php?page=wpars_options
				  array(__CLASS__,'options_page' )           // callback function to display the options page
			);
		}


		public static function check_options(){
			$options_settings = get_option('wprs_options', array());
			//definimos las variables
   			$options_settings['wprs_rute_cvs'] = (isset($options_settings['wprs_rute_cvs'])) ? $options_settings['wprs_rute_cvs']  : '';    
   			$options_settings['wprs_character_separator'] = (isset($options_settings['wprs_character_separator'])) ? $options_settings['wprs_character_separator']  : '';    
   			$options_settings['wprs_file_cvs'] = (isset($options_settings['wprs_file_cvs'])) ? $options_settings['wprs_file_cvs']  : '';    
			
			//devolvemos variables declaradas
   			return $options_settings;
		}

		//opcion para guardar los datos al escoger un archivo
		public static function save_file_cvs($myrute,$myfile,$default_rute){
			$cvs_name   = $myfile['name'];
	  		$tmp_cvs_name  = 	$myfile['tmp_name'];
	  		$msj_temp = '';

	  		//obtenemos la ruta del archivo
	  		$path = trailingslashit(get_home_path()).$myrute;
	  		$result_file = ''; //variable a retornar segun el resultado

	  		if($myrute!=""){
	  			//al tener ots de ruta se colocara en carpeta raiz
	  			if($myrute=='ots'){
	  				//si no existe la carpeta en raiz creamos el path ots
					if(!is_dir($path)) mkdir($path,'777');
					if(!empty($cvs_name)){
				  		//copiamos nuestro archivo
				  		copy($tmp_cvs_name, $path."/".$cvs_name);
				  		//copiamos nuestro htaccess
				  		copy($default_rute.'/.htaccess', $path."/.htaccess");
				  		//guardamos la ruta actual
				  		$result_file =  $path."/".$cvs_name;
						
					}else{

						$result_file = false;
						$msj_temp = 'Error al intentar guardar el archivo';
					}

				//EN ESTA CONDICION PREGUNTAMOS SI NO EXISTE OTRA CARPETA
	  			}else if(!is_dir($path)){
	  				$msj_temp = 'No existe el directorio especificado';
		  			$result_file = false;
				}else{
					//preguntamos si ya existe la ots. Si no existe la creamos con permisos
					if(!is_dir($path."/ots")) mkdir($path."/ots",'777');
					 //movemos la imagen a la ruta especificada
			  		if(!empty($cvs_name)){
				  		//copiamos nuestro archivo
				  		copy($tmp_cvs_name, $path."/ots"."/".$cvs_name);
				  		//copiamos nuestro htaccess
				  		copy($default_rute.'/.htaccess', $path."/ots"."/.htaccess");
				  		//guardamos la ruta actual
				  		$result_file =  $path."/ots"."/".$cvs_name;
						
					}else{
						$result_file = false;
						$msj_temp = 'Error al intentar guardar el archivo';

					}
				}//cierre del else principal de mkdir
			}else{
				copy($tmp_cvs_name,$default_rute."/".$cvs_name);
			  	copy($default_rute.'/.htaccess', $path."/ots"."/.htaccess");
				$result_file =  $default_rute."/".$cvs_name;
			}

			@ini_set('safe_mode','Off'); //disable safe mode
			@ini_set('ignore_user_abort','Off'); //Set PHP ini setting
			@ini_set('memory_limit', "512M");

			return array($result_file,$msj_temp);
		}


		//funcion para recoger los datos en el controlador de settings
		public static function import_options_setting()
		{
		
			check_admin_referer('wprs-settings');
			$wprs_options_setting = self::check_options();
			//uploadfile
			if(!empty($_FILES['wprs_file_cvs']['name'])){	
				//vamos a mostrar los errores y todo
				list($rute_cvs,$msj_setting) = self::save_file_cvs($_POST['wprs_rute_cvs'],$_FILES['wprs_file_cvs'],$_POST['default_rute']);
			}else{
				//seguiria siendo la misma rut
				$rute_cvs = $wprs_options_setting['wprs_file_cvs'];
			}
			if($rute_cvs!=false){
				//variables de opcion
				$check_options['wprs_rute_cvs'] = $_POST['wprs_rute_cvs'];
				$check_options['wprs_character_separator'] = $_POST['wprs_character_separator'];
				$check_options['wprs_file_cvs'] = $rute_cvs;
						
				//guardamos todas las opciones
				$check_options = update_option('wprs_options',$check_options);
				//volvemos a llamar los campos en settings
			}
			//vamos a acomodar los mensajes
			$msj_setting= str_replace(' ','+',$msj_setting);  
			//redireccion
			wp_redirect(admin_url('options-general.php?page=wprs_file_cvs&msj='.$msj_setting));

		}

		//create template html
		public static function options_page()
		{
			//obtendremos todas las opciones
			//$check_options = get_option('wprs_options', array());
			$check_options = self::check_options();

			//mostramos el template
			include_once("wprs_form_settings.php");
			//desps de esto mostramos el mensaje de alerta si existe
			if(isset($_GET['msj']) && !empty($_GET['msj'])){echo "<script> alert('".$_GET['msj']."'); </script>";}
		}


				/*print reparaciones*/
		public static function print_reparaciones_form($attr = '') {
			global $post;
			/* Load the reparaciones JavaScript in the footer. */
			add_action( 'wp_footer', array(__CLASS__,'reparaciones_load_scripts' ));

			/* Merge the defaults and the given attributes. */
			$attr = shortcode_atts( array( 'before' => '', 'after' => '' ), $attr );

			$form  = '<ul class="orderfield"><li>Nro: <input type="number" min=0 name="order_number" id="order_number" value=""></li>';
			$form .= '<li><button id="btn_consultar">Consultar</button></li></ul>';
			$form .= '<table id="order_number_result"></table>';

			/* Returns the formatted number of views. */
			return $attr['before'] . ( $form ) . $attr['after'];
		}

		/**
		 * Displays a small script that sends an AJAX request for the page.  It passes the $order_n to the AJAX 
		 * callback function for updating the meta.
		 *
		 * @since 0.1
		 */
		public static function reparaciones_load_scripts() {
			/* Create a nonce for the AJAX request. */
			$nonce = wp_create_nonce('reparaciones_ajax' );
			/* Display the JavaScript needed. */
			?>
		<style type="text/css">
			input[type=number] {
				-moz-appearance: textfield;
				text-align: right;
				padding-right: 5px;
			}
			input[type=number]::-webkit-inner-spin-button, 
			input[type=number]::-webkit-outer-spin-button { 
				-webkit-appearance: none; 
				margin: 0; 
			}	
		</style>
			<script type="text/javascript">/* <![CDATA[ */
				jQuery(document).ready( function($) {
					$(':input[type=number]').on('mousewheel', function(e){
						e.preventDefault();
					});	
					$("#btn_consultar").click( function() {
						$('#order_number_result').html('<img src="<?php echo includes_url('images/spinner.gif'); ?>">');
						$.post( 
							"<?php echo admin_url( 'admin-ajax.php' ); ?>", 
							{
							action : "estadorep",
							_ajax_nonce : "<?php echo $nonce; ?>",
							order_n : $( "#order_number").val() 
							},
							function( result ) {
								if(result.success) {
									$('#order_number_result').html(result.data);
								}else {
									$('#order_number_result').html('No Encontrado');
								}
									
								//var result = $.parseJSON(data);
								
								console.log(result);
						});
					});
				});
				/* ]]> */
			</script>
			<?php
		}
		/**
		 * Callback function hooked to 'wp_ajax_reparaciones' and 'wp_ajax_nopriv_reparaciones'.  It checks the
		 * AJAX nonce and passes the given $order_n to the entry views update function.
		 *
		 */
		public static function reparaciones_ajax() {

			/* Check the AJAX nonce to make sure this is a valid request. */
			check_ajax_referer( 'reparaciones_ajax' );
			$estado = '';
			
			/* If the post ID is set, set it to the $order_n variable and make sure it's an integer. */
			if ( isset( $_POST['order_n'] ) )
				$order_n = absint( $_POST['order_n'] );
			
			/* If $order_n isn't empty, pass it to the reparaciones_update() function to update the view count. */
			if ( !empty( $order_n ) ){
				$estado = self::reparaciones_get_update( $order_n );
			}
			
			if(!empty($estado)){
				wp_send_json_success( $estado );
			}else {
				wp_send_json_error( );
			}
		}

		/**
		 * lee el archivo y busca el  $order_n para devolver el estado dela orden
		 *
		 * @since 0.1
		 */
		public static function reparaciones_get_update( $order_n = 0 ) {
			$wprs_options_setting = self::check_options();
			//$file = trailingslashit( get_home_path()) . 'ots/ordenesdetrabajo.txt';
			//	clearstatcache();
			//obtendremoos el directorio del archivo para mostrarlo en la tabla fronts
			$file = $wprs_options_setting['wprs_file_cvs'];
			$dev = '';
			if (file_exists($file)) {
				//aqui colocaremos el separador del archivo por medio de la variable opcion que creamos
				$estados = self::reparaciones_csv_2_array($file,$wprs_options_setting['wprs_character_separator']);
				foreach($estados as $key => $estado) {
					if($estado[0]==$order_n) {
						// 0       1        2                    3                 4           5                6                 7  8                          9 
						//000002|ENTREGADO|OBS ESTADO ENTREGADO|22/02/2013 13:59|08/03/2013|SILVANA CHITARO|SEC.GAMA MYSTERE 4000||CAMBIO DE CARCAZA DELANTERA|31.00

						$dev.= "<tr><td>Estado:</td><td>$estado[1]</td></tr>";
						$dev.= "<tr><td>Descripción:</td><td>$estado[2]</td></tr>";
						$dev.= "<tr><td>Recibido:</td><td>$estado[3]</td></tr>";
						$dev.= "<tr><td>Entregado:</td><td>$estado[4]</td></tr>";
						$dev.= "<tr><td>A Nombre de:</td><td>$estado[5]</td></tr>";
						$dev.= "<tr><td>Dirección:</td><td>$estado[6]</td></tr>";
						$dev.= "<tr><td>Reparación:</td><td>$estado[8]</td></tr>";
						$dev.= "<tr><td>Precio:</td><td>$estado[9]</td></tr>";
					}
				}
			}
			return $dev;				
		}

		/**
		 * 
		 * @param type $url		the file with CSV data (url / string)
		 * @param type $delm	colum delimiter (e.g: ; or | or , ...)
		 * @param type $encl	values enclosed by (e.g: ' or " or ^ or ...)
		 * @param type $head	with or without 1st row = head (true/false) 
		 * @return type array 
		 */
		public static function reparaciones_csv_2_array($url,$delm=";",$encl="\"",$head=false) {
		   
		    $csvxrow = file($url);   // ---- csv rows to array ----
		   
		    $csvxrow[0] = chop($csvxrow[0]);
		    $csvxrow[0] = str_replace($encl,'',$csvxrow[0]);
		    $keydata = explode($delm,$csvxrow[0]);
		    $keynumb = count($keydata);
		   
		    if ($head === true) {
		    $anzdata = count($csvxrow);
		    $z=0;
		    for($x=1; $x<$anzdata; $x++) {
		        $csvxrow[$x] = chop($csvxrow[$x]);
		        $csvxrow[$x] = str_replace($encl,'',$csvxrow[$x]);
		        $csv_data[$x] = explode($delm,$csvxrow[$x]);
		        $i=0;
		        foreach($keydata as $key) {
		            $out[$z][$key] = $csv_data[$x][$i];
		            $i++;
		            }   
		        $z++;
		        }
		    }
		    else {
		        $i=0;
		        foreach($csvxrow as $item) {
		            $item = chop($item);
		            $item = str_replace($encl,'',$item);
		            $csv_data = explode($delm,$item);
		            for ($y=0; $y<$keynumb; $y++) {
		               $out[$i][$y] = $csv_data[$y];
		            }
		        $i++;
		        }
		    }

		return $out;
		}




}//cierre de la clase
$wprs_to_repair_settings = new wprs_to_repair_settings();

?>