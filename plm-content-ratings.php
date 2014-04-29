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

function plm_output_row($label, $value, $exclude = FALSE, $sprite='&#x2589;') 
{

	$returnThis = '<tr class="r_row" ><td class="r_row_label">' . $label . ': </td><td>';

	$returnThis .= plm_output_rating($sprite, $value, 5).  '</td></tr>';

	if (!($exclude AND ($value==0))) {
		return $returnThis;
	} else {
		return '';
	}
} 

function plm_output_stack($label, $value, $exclude = FALSE, $sprite = '&#x2589;')
{	
	$returnThis = '<div class="plm_stack_label">' . $label . '</div><div class="plm_stack_rating">';

	$returnThis .= plm_output_rating($sprite, $value, 5) . '</div>';	

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
		'no_zero' => 'no'
		),
		 $atts, 
		 'rating_stack' 
		 )
	);

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
	$theRatings = plm_parse_array(html_entity_decode($content));

	if(!$theRatings) {
		return '<p>'. $content . '</p>';
	} else {
		return '<div class="plm_rating_stack_container">' . plm_array_to_stack($theRatings, $exclude, $sprite) . '</div>';
	}
}

add_shortcode('rating_stack', 'plm_stack');

function plm_rtable($atts, $content)
{
	extract(shortcode_atts(array(
		'sprite' => '&#x2589;',
		'no_zero' => 'no'
		),
		 $atts, 
		 'rating_table' 
		 )
	);
	
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

	$theRatings = plm_parse_array(html_entity_decode($content));

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

function plm_phone_parse($atts, $content) 
{
	extract(shortcode_atts(array(
		'format' => 'parens',
		),
		 $atts 
		 )
	);

	$phoneNum = html_entity_decode($content);

	$pattern = '/\(?([1-9][0-9]{2})[\)\-\s]?([1-9][0-9]{2})[\-\s]?([0-9]{4})/';

	switch ($format) 
	{
		case 'dashes':
		case 'd':
			$replace = '$1-$2-$3';
			break;
		default;
			$replace = '($1) $2-$3';
	}

	$small_replace = '$1$2$3';

	if ( preg_match($pattern, $phoneNum) ) {
		$ret = '<span class="phone_number" data-phone="'. preg_replace($pattern, $small_replace, $phoneNum) . '">';
		$ret .= preg_replace($pattern, $replace, $phoneNum) . '</span>';
	} else {
		$ret = 'Not a match!';
	}

	return $ret;
}

add_shortcode('phone_format', 'plm_phone_parse');

function plm_email_encode($atts, $content) 
{
	$pattern = '/\b([\w\d.+#$%_\(\)]+)@([A-Za-z0-9.]+)\.([A-Za-z0-9.]{2,})\b/';

	$emailAdd = html_entity_decode($content);

	$mailboxRep = '$1';
	$domainRep = '$2';
	$tldRep = '$3';
	$anchorRep = '$1 at $2 dot $3';

	if (preg_match($pattern, $emailAdd)) {
		$mailBox = preg_replace($pattern, $mailboxRep, $emailAdd);
		$domain = preg_replace($pattern, $domainRep, $emailAdd);
		$tld = preg_replace($pattern, $tldRep, $emailAdd);

		$anchor = preg_replace($pattern, $anchorRep, $emailAdd);

		$ret = '<a href="' . $anchor . '" rel="nofollow" onClick="';

		$ret .= 'this.href=\'mailto:\' + \'' . $mailBox . '\' + \'@\' + \'' . $domain . '.' . $tld . '\'">' . $anchor . '</a>';

		return $ret;
	}
}

add_shortcode('emailprotect', 'plm_email_encode');
?>