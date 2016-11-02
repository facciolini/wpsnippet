<?php /*INSERISCE MODIFICA CSS SU PAGINA WORDPRESS*/
add_action('admin_menu', 'custom_css_hooks');
 add_action('save_post', 'save_custom_css');
 add_action('wp_head','insert_custom_css');
 function custom_css_hooks() {
 add_meta_box('custom_css', 'Custom CSS', 'custom_css_input', 'post', 'normal', 'high');
 add_meta_box('custom_css', 'Custom CSS', 'custom_css_input', 'page', 'normal', 'high');
 add_meta_box('custom_css', 'Custom CSS', 'custom_css_input', 'product', 'normal', 'high');  /*inserimento su pag. product di Woocommerce*/
 }
 function custom_css_input() {
 global $post;
 echo '<input type="hidden" name="custom_css_noncename" id="custom_css_noncename" value="'.wp_create_nonce('custom-css').'" />';
 echo '<textarea name="custom_css" id="custom_css" rows="5" cols="30" style="width:100%;">'.get_post_meta($post->ID,'_custom_css',true).'</textarea>';
 }
 function save_custom_css($post_id) {
 if (!wp_verify_nonce($_POST['custom_css_noncename'], 'custom-css')) return $post_id;
 if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
 $custom_css = $_POST['custom_css'];
 update_post_meta($post_id, '_custom_css', $custom_css);
 }
 function insert_custom_css() {
 if (is_page() || is_single()) {
 if (have_posts()) : while (have_posts()) : the_post();
 echo '<style type="text/css">'.get_post_meta(get_the_ID(), '_custom_css', true).'</style>';
 endwhile; endif;
 rewind_posts();
 }
 }





/*INSERISCE ANNO IN CORSO */
/* 
<?php echo date('Y'); ?> 
*/





/*AMMINISTRA UTENTI - NASCONDE PULSANTE DIVI BUILDER*/
add_action( 'admin_head', 'custom_admin_css' );
function custom_admin_css(){
              
                $current_user = wp_get_current_user();
                $user_id = $current_user->ID;
                $isUserAdmin  = isset($current_user->caps['administrator']);
                $isUserEditor = isset($current_user->caps['editor']);
                $isUserAuthor = isset($current_user->caps['author']);
                $isUserAdminCFR = isset($current_user->caps['admin_cfr']);
 
                $hide_pagebuilder= !($isUserAdmin) ;
 
                if ($hide_pagebuilder){
                               echo ' <style>#et_pb_toggle_builder {display:none;}</style> ';
                }
}





/*INSERISCE LEGGI TUTTO IN ARTICOLI*/
function new_excerpt_more( $more ) {
	return ' ...<a class="read-more" href="'. get_permalink( get_the_ID() ) . '">Leggi Tutto</a>';
}
add_filter( 'excerpt_more', 'new_excerpt_more' );





/*LUNGHEZZA ESTRATTO ARTICOLI*/
function custom_excerpt_length( $length ) {
	return 120;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

/* LUNGHEZZA ESTRATTO ARTICOLI - ALTERNATIVA*/
function ri_new_excerpt_more($more) {
    return ' ...';
}
add_filter('excerpt_more', 'ri_new_excerpt_more');





/*IMPOSTARE LA LUNGHEZZA MINIMA DEGLI ARTICOLI*/
function minWord($content){
    global $post;
        $num = 100; //imposta il numero minimo di parole
    $content = $post->post_content;
    if (str_word_count($content) <  $num)
        wp_die( __('Errore: l'articolo è troppo breve.') );
}
add_action('publish_post', 'minWord');



/*SHORTCODE IFRAME*/
/*SHORTCODE: [iframe url="http://URBAN-ENERGY.WU" width="100" height="100" scrolling="yes" frameborder="1" marginheight="2"] */

add_shortcode('iframe', array('iframe_shortcode', 'shortcode'));
class iframe_shortcode {
    function shortcode($atts, $content=null) {
          extract(shortcode_atts(array(
               'url'      => '',
               'scrolling'      => 'no',
               'width'      => '100%',
               'height'      => '300',
               'frameborder'      => '0',
               'marginheight'      => '0',
          ), $atts));
          if (empty($url)) return '<!-- Iframe: Non hai inserito un URL valido -->';
     return '<iframe src="'.$url.'" title="" width="'.$width.'" height="'.$height.'" scrolling="'.$scrolling.'" frameborder="'.$frameborder.'" marginheight="'.$marginheight.'"><a href="'.$url.'" target="_blank">'.$url.'</a></iframe>';
    }
}





/*SHORTCODE MAPPA GOOGLE */
/*SHORTCODE: [googlemap width="200" height="200" src="[url]"] */
function add_google_maps($atts, $content = null) {
       extract(shortcode_atts(array(
                    "width" => '640',
                    "height" => '480',
                    "src" => ''
                    ), $atts));
      return '<iframe width="'.$width.'" height="'.$height.'" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'.$src.'"></iframe>';
}
add_shortcode("googlemap", "add_google_maps");





/* ETCIHETTE COLORATE POST*/
/* Per inserire un etichetta seleziona la voce nel menu a tendina che trovi nel widget Pubblica (stato/visibilità/pubblicato il: etc) a destra nell’editor*/
add_filter( 'display_post_states','custom_post_state');
function custom_post_state( $states ) {
	global $post;
	$show_custom_state = get_post_meta( $post->ID, '_status' );
	   if ( $show_custom_state ) {
		$states[] = __( '<span class="custom_state '.strtolower($show_custom_state[0]).'">'.$show_custom_state[0].'</span>' );
		}
	return $states;
}
add_action( 'post_submitbox_misc_actions', 'custom_status_metabox' );
function custom_status_metabox(){
	global $post;
	$custom  = get_post_custom($post->ID);
	$status  = $custom["_status"][0];
	$i   = 0;
	/* ----------------------------------- */
	/*   modifica qui i nomi delle etichette             */
	/* ----------------------------------- */
	$custom_status = array(
			'Bozza',
			'Errori',
			'Finale',
			'Corretto',
			'Importante',
		);
	echo '<div class="misc-pub-section custom">';
	echo '<label>Etichetta Post: </label><select name="status">';
	echo '<option class="default">Nessuna Etichetta</option>';
	for($i=0;$i<count($custom_status);$i++){
		if($status == $custom_status[$i]){
		    echo '<option value="'.$custom_status[$i].'" selected="true">'.$custom_status[$i].'</option>';
		  }else{
		    echo '<option value="'.$custom_status[$i].'">'.$custom_status[$i].'</option>';
		  }
		}
	echo '</select>';
	echo '<br /></div>';
}
add_action('save_post', 'save_status');
function save_status(){
	global $post;
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){ return $post->ID; }
	update_post_meta($post->ID, "_status", $_POST["status"]);
}
add_action( 'admin_head', 'status_css' );
function status_css() {
	echo '<style type="text/css">
	.default{font-weight:bold;}
	.custom{border-top:solid 1px #e5e5e5;}
	.custom_state{
		font-size:10px;
		color:#666;
		background:#e5e5e5;
		padding:3px 6px 3px 6px;
		-moz-border-radius:3px;
		border-radius:3px;
		}
		/* ----------------------------------- */
		/*   modifica qui i colori delle etichette            */
		/* ----------------------------------- */
		.bozza{background:#4D4D4D;color:#fff;}
		.errori{background:#FF0000;color:#fff;}
		.finale{background:#00BD00;color:#333;}
		.corretto{background:#1E90FF;color:#fff;}
		.importante{background:#FFA500;color:#fff;}
		</style>';
}



/* REDIRECT DEGLI UTENTI AD UN URL SPECIFICO DOPO LA REGISTRAZIONE */
function __my_registration_redirect(){
    return home_url( '/my-page' );
}
add_filter( 'registration_redirect', '__my_registration_redirect' );



/*ESCLUDERE I POST DI UNA O PIÙ CATEGORIE DALLA HOMEPAGE*/

function exclude_category_home( $query ) {
    if ( $query->is_home ) {
        $query->set( 'cat', '-5, -34'); // change category IDs
    }
    return $query;
}
 
add_filter( 'pre_get_posts', 'exclude_category_home' );





/*AGGIUNGERE/RIMUOVERE INFORMAZIONI UTENTE */
function extra_contact_info($contactmethods) {
unset($contactmethods['aim']);
unset($contactmethods['yim']);
unset($contactmethods['jabber']);
$contactmethods['facebook'] = 'Facebook';
$contactmethods['twitter'] = 'Twitter';
$contactmethods['linkedin'] = 'LinkedIn';
 
return $contactmethods;
}
add_filter('user_contactmethods', 'extra_contact_info');
