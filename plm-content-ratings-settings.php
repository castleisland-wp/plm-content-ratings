<?php 

function plm_content_admin() {
		add_options_page(
		"Content Ratings Configuration",
		"Content Rating Config",
		'administrator',
		'plm_content_menu',
		'plm_content_menu_display'
		);

}

add_action('admin_menu', 'plm_content_admin');

function plm_content_options() {
		add_settings_section(
		'plm_content_section',
		'Options',
		'plm_content_section_display',
		'plm_content_menu'
		);

	add_settings_field(
		'plm_default_rating_sprite',
		'Default Sprite for Rating Boxes',
		'plm_default_rating_sprite_disp',
		'plm_content_menu',
		'plm_content_section',
		array('Select the character to use as the sprite for ratings boxes. Default is &amp;#x2589; (&#x2589;).' )
		);

	add_settings_field(
		'plm_default_star_sprite',
		'Default Sprite for Star Ratings',
		'plm_default_star_sprite_disp',
		'plm_content_menu',
		'plm_content_section',
		array('Select the character to use as the sprite for star ratings. Default is &amp;#x2605; (&#x2605;).')
		);

	add_settings_field(
		'plm_default_maximum',
		'Default Maximum Rating',
		'plm_default_maximum_disp',
		'plm_content_menu',
		'plm_content_section',
		array('Select the maximum for ratings. Default is 5.')
		);

	register_setting(
		'plm_content_menu', 
		'plm_default_rating_sprite');

	register_setting(
		'plm_content_menu', 
		'plm_default_star_sprite');

	register_setting(
		'plm_content_menu', 
		'plm_default_maximum');
}
add_action('admin_init', 'plm_content_options');

// Display Menus 
function plm_content_menu_display()
{
?>
<div class="wrap">
	<h2>Content Rating Settings</h2>
	<?php settings_errors(); ?>
	<form method="post" action="options.php">
		<?php settings_fields('plm_content_menu'); ?>
		<?php do_settings_sections('plm_content_menu'); ?>
		<?php submit_button(); ?>
	</form>
</div>
<?php
} 

function plm_content_section_display()
{ 
	echo "<p>Set the default output settings for the Content Rating shortcodes here. You can always override these settings using the shortcode parameters.</p>";

 }

function plm_default_rating_sprite_disp($args)
{
?>
<select name="plm_default_rating_sprite" id="plm_default_rating_sprite">
	<option value="x2714" <?php selected(get_option('plm_default_rating_sprite'), 'x2714', TRUE) ?>>Check &#x2714;</option>
	<option value="x2611" <?php selected(get_option('plm_default_rating_sprite'), 'x2611', TRUE) ?>>Check Box &#x2611;</option>
	<option value="x2611" <?php selected(get_option('plm_default_rating_sprite'), 'x2612', TRUE) ?>>Ballot Box &#x2612;</option>
	<option value="x2589" <?php selected(get_option('plm_default_rating_sprite'), 'x2589', TRUE) ?>>Block &#x2589;</option>
	<option value="x2605" <?php selected(get_option('plm_default_rating_sprite'), 'x2605', TRUE) ?>>Star &#x2605;</option>
</select>
<label for="plm_default_rating_sprite">&nbsp;<?php echo $args[0];?></label>
<?php
}


function plm_default_star_sprite_disp($args)
{
?>
<select name="plm_default_star_sprite" id="plm_default_star_sprite">
	<option value="x2714" <?php selected(get_option('plm_default_star_sprite'), 'x2714', TRUE) ?>>Check &#x2714;</option>
	<option value="x2611" <?php selected(get_option('plm_default_star_sprite'), 'x2611', TRUE) ?>>Check Box &#x2611;</option>
	<option value="x2612" <?php selected(get_option('plm_default_star_sprite'), 'x2612', TRUE) ?>>Ballot Box &#x2612;</option>
	<option value="x2589" <?php selected(get_option('plm_default_star_sprite'), 'x2589', TRUE) ?>>Block &#x2589;</option>
	<option value="x2605" <?php selected(get_option('plm_default_star_sprite'), 'x2605', TRUE) ?>>Star &#x2605;</option>
	<option value="x263A" <?php selected(get_option('plm_default_star_sprite'), 'x263A', TRUE) ?>>Smiley &#x263A;</option>
</select>
<label for="plm_default_star_sprite">&nbsp;<?php echo $args[0];?></label>
<?php
}

function plm_default_maximum_disp($args)
{
?>
<select name="plm_default_maximum" id="plm_default_maximum">
<?php
	for ($i=3; $i <= 20 ; $i++) { 
		echo '<option value="' . $i . '" ' . selected(get_option('plm_default_maximum'), $i, FALSE) . '>' . $i .'</option>';
	}
?>
</select>
<label for="plm_default_maximum">&nbsp;<?php echo $args[0];?></label>
<?php
}

function temptest() {
	$x = get_option('plm_calendar_medium', 'motherfucker');

	return '<p>' . $x . '</p>';
}
add_shortcode('temp', 'temptest');
?>