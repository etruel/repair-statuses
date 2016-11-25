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
			return get_option('wprs_options', array());
		}

		//opcion para guardar los datos
		public static function save_file_cvs($myrute,$myfile,$default_rute){
			$cvs_name   = $myfile['name'];
	  		$tmp_cvs_name  = 	$myfile['tmp_name'];
	  		//obtenemos la ruta del archivo
	  		$path = trailingslashit(get_home_path()).$myrute;
	  		$result_file = ''; //variable a retornar segun el resultado

	  		if($myrute!=""){
		  		if(!is_dir($path)){
		  			$result_file = false;
		  			echo "<script> alert('No existe el directorio especificado'); </script>";
				}else{
					//preguntamos si ya existe la ots. Si no existe la creamos con permisos
					if(!is_dir($path."/ots")) mkdir($path."/ots",'777');
					 //movemos la imagen a la ruta especificada
			  		if(!empty($cvs_name)){
			  			move_uploaded_file($tmp_cvs_name,$path."/ots"."/".$cvs_name);
			  			$result_file =  $path."/ots"."/".$cvs_name;
			  			chmod($result_file,"0777");
					}else{
						$result_file = false;
					}
				}//cierre del else principal de mkdir
			}else{
				//colocar path desde el plugin por defecto
				move_uploaded_file($tmp_cvs_name,$default_rute."/".$cvs_name);
			  	$result_file =  $default_rute."/".$cvs_name;
			  	chmod($result_file,"0777");

			  	//dando permisos para usuarios
			}

			@ini_set('safe_mode','Off'); //disable safe mode
			@ini_set('ignore_user_abort','Off'); //Set PHP ini setting
			@ini_set('memory_limit', "512M");

			return $result_file;
		}


		//funcion para recoger los datos en el controlador de settings
		public static function import_options_setting()
		{
			
			check_admin_referer('wprs-settings');
			//uploadfile	
			$rute_cvs =  self::save_file_cvs($_POST['wprs_rute_cvs'],$_FILES['wprs_file_cvs'],$_POST['default_rute']);
			if($rute_cvs!=false){
				//variables de opcion
				$check_options['wprs_rute_cvs'] = $_POST['wprs_rute_cvs'];
				$check_options['wprs_character_separator'] = $_POST['wprs_character_separator'];
				$check_options['wprs_file_cvs'] = $rute_cvs;
					
				//guardamos todas las opciones
				$check_options = update_option('wprs_options',$check_options);
				//volvemos a llamar los campos en settings
				wp_redirect(admin_url('options-general.php?page=wprs_file_cvs'));
			}
		}

		//create template html
		public static function options_page()
		{
			//obtendremos todas las opciones
			$check_options = get_option('wprs_options', array());
			include_once("wprs_settings.php");
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
						alert("le diste click");
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
			$file = trailingslashit( get_home_path()) . 'ots/ordenesdetrabajo.txt';
			//obtenemos el directorio actual donde buscaremos el archivo que vamos a utilizar
			//$file = trailingslashit(get_option('wprs_file_cvs'));
		//	clearstatcache();
			//$file = 'ordenesdetrabajo.txt';
			$dev = '';
			if (file_exists($file)) {
				//aqui colocaremos el separador del archivo por medio de la variable opcion que creamos
				$estados = self::reparaciones_csv_2_array($file,'|');
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