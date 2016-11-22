<?php
/* Add the entry views AJAX actions to the appropriate hooks. */
add_action( 'wp_ajax_estadorep', 'reparaciones_ajax' );
add_action( 'wp_ajax_nopriv_estadorep', 'reparaciones_ajax' );
/* Add the [entry-views] shortcode. */
add_shortcode( 'reparaciones_form', 'print_reparaciones_form' );

function print_reparaciones_form($attr = '') {
	global $post;
	/* Load the reparaciones JavaScript in the footer. */
	add_action( 'wp_footer', 'reparaciones_load_scripts' );

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
function reparaciones_load_scripts() {
	/* Create a nonce for the AJAX request. */
	$nonce = wp_create_nonce( 'reparaciones_ajax' );
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
function reparaciones_ajax() {

	/* Check the AJAX nonce to make sure this is a valid request. */
	check_ajax_referer( 'reparaciones_ajax' );
	$estado = '';
	
	/* If the post ID is set, set it to the $order_n variable and make sure it's an integer. */
	if ( isset( $_POST['order_n'] ) )
		$order_n = absint( $_POST['order_n'] );
	
	/* If $order_n isn't empty, pass it to the reparaciones_update() function to update the view count. */
	if ( !empty( $order_n ) ){
		$estado = reparaciones_get_update( $order_n );
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
function reparaciones_get_update( $order_n = 0 ) {
	$file = trailingslashit( get_home_path() ) . 'ots/ordenesdetrabajo.txt';
//	clearstatcache();
	//$file = 'ordenesdetrabajo.txt';
	$dev = '';
	if (is_file($file)) {
		$estados = reparaciones_csv_2_array($file,'|');
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
function reparaciones_csv_2_array($url,$delm=";",$encl="\"",$head=false) {
   
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
