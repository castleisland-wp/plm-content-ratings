<?php 
/*
Plugin Name: Content Ratings
Plugin URI: http://www.paulmcelligott.com
Description: Generates star ratings and other graphics based on shortcodes.
Version: 0.1
Author: Paul McElligott
Text Domain: plm-content-ratings
Author URI: http://www.paulmcelligott.com
License: GPL2
*/

function plm_parse_array($the_text) 
{
	$pattern = '%([\w\s&\-\.]+)[:,|/]{1}(\d+)([,|/:]?)%';
	$returnArray = array();

	if (preg_match_all($pattern, $the_text, $match_array, PREG_SET_ORDER)) 
	{
		foreach ($match_array as $match_pair) {
			$returnArray[$match_pair[1]] = $match_pair[2];
		}

		return $returnArray;
	} else {
		return FALSE;
	}
}

function plm_array_to_rows ($theArray, $exclude = FALSE, $sprite='&#x2589;') 
{
	$returnRows = '';

	foreach ($theArray as $label => $rating) {
		$returnRows .= plm_output_row($label, $rating, $exclude, $sprite);
	}

	return $returnRows;
}

function plm_array_to_stack($theArray, $exclude = FALSE, $sprite='&#x2589;')
{
	$returnStack = '';

	foreach ($theArray as $label => $rating) {
		$returnStack .= plm_output_stack($label, $rating, $exclude, $sprite);
	}

	return $returnStack;
}

function plm_output_row($label, $value, $exclude = FALSE, $sprite='&#x2589;', $max=5) 
{

	$returnThis = '<tr class="r_row" ><td class="r_row_label">' . $label . ': </td><td>';

	$returnThis .= plm_output_rating($sprite, $value, $max).  '</td></tr>';

	if (!($exclude AND ($value==0))) {
		return $returnThis;
	} else {
		return '';
	}
} 

function plm_output_stack($label, $value, $exclude = FALSE, $sprite = '&#x2589;', $max=5)
{	
	$returnThis = '<div class="plm_stack_label">' . $label . '</div><div class="plm_stack_rating">';

	$returnThis .= plm_output_rating($sprite, $value, $max) . '</div>';	

	if (!($exclude AND ($value == 0))) {
		return $returnThis;
	} else {
		return '';
	}
}

function plm_output_rating( $sprite, $value, $max = 5 ) 
{
	$returnThis = '';

	$rated = '<span class="plm_rated">' . $sprite . '</span>';
	$filler = '<span class="plm_filler">' . $sprite . '</span>';

	if (intval($value) > intval($max))
	{
		$value = intval($max);
	}

	$returnThis .= str_repeat($rated, $value);

	$plm_remainder = $max - $value;

	if ($plm_remainder > 0) $returnThis .= str_repeat($filler, $plm_remainder);

	return $returnThis;
}

function plm_stack($atts, $content)
{
	extract(shortcode_atts(array(
		'sprite' => '&#x2589;',
		'no_zero' => 'no',
		'max' => '5',
		),
		 $atts, 
		 'rating_stack' 
		 )
	);
	//if max is not a number, reset it to 5.
	if (!is_numeric($max)) {
		$max = 5;
	} else {
		$max = intval($max);
	}

	//test possible values of $no_zero.  If true exclude zero values from stack.
	switch ( strtolower( $no_zero )  ) {
			case 'yes':
			case 'true':
			case 'y':
				$exclude = TRUE;
				break;
			
			default:
				$exclude = FALSE;
				break;
	}
	//Past $content to array parser.
	$theRatings = plm_parse_array(html_entity_decode($content));

	// if return value is not false, output the rating stack, otherwise
	// return the original $content.
	if(!$theRatings) {
		return '<p>'. html_entity_decode($content) . '</p>';
	} else {
		return '<div class="plm_rating_stack_container">' . plm_array_to_stack($theRatings, $exclude, $sprite) . '</div>';
	}
}

add_shortcode('rating_stack', 'plm_stack');

function plm_rtable($atts, $content)
{
	extract(shortcode_atts(array(
		'sprite' => '&#x2589;',
		'no_zero' => 'no',
		'max' => '5',
		),
		 $atts, 
		 'rating_table' 
		 )
	);
	
	//if max is not a number, reset it to 5.
	if (!is_numeric($max)) {
		$max = 5;
	} else {
		$max = intval($max);
	}

	//test possible values of $no_zero.  If true exclude zero values from stack.
	switch ( strtolower( $no_zero )  ) {
			case 'yes':
			case 'true':
			case 'y':
				$exclude = TRUE;
				break;
			
			default:
				$exclude = FALSE;
				break;
	}
		
	//Past $content to array parser.
	$theRatings = plm_parse_array(html_entity_decode($content));

	// if return value is not false, output the rating stack, otherwise
	// return the original $content.

	if(!$theRatings) {
		return '<p>'. $content . '</p>';
	} else {
		return '<div class="plm_rating_table_container"><table class="plm_rating_table"><tbody>' . plm_array_to_rows($theRatings, $exclude, $sprite) . '</tbody></table></div>';
	}
}

add_shortcode('rating_table', 'plm_rtable');

function plm_star_rating($atts, $content) {

	extract(shortcode_atts(array(
		'sprite' => '&#x2605;',
		'max' => '5'
		),
		 $atts, 
		 'simple_star_rating' 
		 )
	);

	$content = preg_replace('%(\d+)([:,\|\-\*#]+)(\d+)%', '$1/$3', $content); // Replace nonstandard separator characters with "/"

	if (preg_match('*\d\/\d*', $content)) 
	{
		$tempArray = explode('/', $content);
		$rating = intval($tempArray[0]);
		$max = intval($tempArray[1]);

	} elseif (preg_match('%[1-5]{1}%', $content)) {

		$rating = $content;
	} else {
		$rating = 0;
	}

	return '<div class="plm_star_rating">' . plm_output_rating($sprite, $rating, $max) . '</div>';
}

add_shortcode('simple_star_rating', 'plm_star_rating');

function plm_content_rating_styles() {
		wp_enqueue_style('plm_content_rating', plugins_url('styles/style.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'plm_content_rating_styles');

// Add Quicktags
function plm_custom_quicktags() {

	if ( wp_script_is( 'quicktags' ) ) {
	?>
	<script type="text/javascript">
	QTags.addButton( 'plm_rstack', 'Rating Stack', '[rating_stack]', '[/rating_stack', '', 'Rating Stack', 141 );
	QTags.addButton( 'plm_rtable', 'Rating Table', '[rating_table]', '[/rating_table]', '', 'Rating Table', 142 );
	QTags.addButton( 'plm_star', 'Star Rating', '[simple_star_rating]', '[/simple_star_rating]', '', 'Star Rating', 143 );
	</script>
	<?php
	}

}

// Hook into the 'admin_print_footer_scripts' action
add_action( 'admin_print_footer_scripts', 'plm_custom_quicktags' );
?>