<?php
/*
Plugin Name: SidePost
Plugin URI: http://zoranmaric.com/plg/srp.zip
Description: Sidebar recent posts with thumbnails plugin
Version: 1.0.0
Author: Zoran Maric
Author URI: http://zoranmaric.com
License: GPL2
*/
?>
<?php
function srp_stil(){
wp_enqueue_style( 'srp', plugins_url( '/css/style.css', __FILE__ ) );
}
add_action('wp_enqueue_scripts', 'srp_stil');function srp_admin()
{   
	wp_enqueue_style( 'srpadmin', plugins_url( '/css/srpadmin.css', __FILE__ ) );    
	/* Enqueue Style */  
	wp_enqueue_style( 'zmenadmin' );}add_action( 'admin_print_styles', 'srp_admin', 20 );
	// Custom excerpt
function custom_excerpt($new_length = 20, $new_more = '...') {
add_filter('excerpt_length', function () use ($new_length) {
return $new_length;
}, 999);
add_filter('excerpt_more', function () use ($new_more) {
return $new_more;
});
$output = get_the_excerpt();
echo $output;
}// Post thumbnails
if(!current_theme_supports('post-thumbnails')) {
add_theme_support('post-thumbnails');
}
class srp_plugin extends WP_Widget {
// constructor
function srp_plugin()     {
parent::WP_Widget(false, $name = __('SIDEPOST', 'wp_widget_plugin') );
}
// widget form creation
function form($instance) {
// Check values
if( $instance) {
	$title = esc_attr($instance['title']);
	$srp_ct = esc_attr($instance['srp_ct']);
	$srp_pp = esc_attr($instance['srp_pp']);
	$srp_lv = esc_attr($instance['srp_lv']);
	$srp_br = esc_attr($instance['srp_br']);
	$srp_dv = esc_attr($instance['srp_dv']);
} else {
	$title = '';
	$srp_ct = '';
	$srp_pp = '';
	$srp_lv = '';
	$srp_br = '';
	$srp_dv = '';
}
?>
<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'wp_widget_plugin'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
<p><input class="upisatidva" id="<?php echo $this->get_field_id('srp_ct'); ?>" name="<?php echo $this->get_field_name('srp_ct'); ?>" type="text" value="<?php echo $srp_ct; ?>" />
<label for="<?php echo $this->get_field_id('srp_ct'); ?>"><?php _e('Category ID number*. <br> To get posts from more than one categorie, just write more than one ID number and use commas to separate.', 'wp_widget_plugin'); ?></label></p>
<p><input class="upisati" id="<?php echo $this->get_field_id('srp_pp'); ?>" name="<?php echo $this->get_field_name('srp_pp'); ?>" type="number" value="<?php echo $srp_pp; ?>" />
<label for="<?php echo $this->get_field_id('srp_pp'); ?>"><?php _e('How many post to show?', 'wp_widget_plugin'); ?></label></p>
<p><input class="upisati" id="<?php echo $this->get_field_id('srp_lv'); ?>" name="<?php echo $this->get_field_name('srp_lv'); ?>" type="number" value="<?php echo $srp_lv; ?>" />
<label for="<?php echo $this->get_field_id('srp_lv'); ?>"><?php _e('Thumbnails width and height.', 'wp_widget_plugin'); ?></label></p>
<p><input class="upisati" id="<?php echo $this->get_field_id('srp_br'); ?>" name="<?php echo $this->get_field_name('srp_br'); ?>" type="number" value="<?php echo $srp_br; ?>" /><label for="<?php echo $this->get_field_id('srp_br'); ?>"><?php _e(' Number of words in excerpt.', 'wp_widget_plugin'); ?></label></p>
<p><input id="<?php echo $this->get_field_id('srp_dv'); ?>" name="<?php echo $this->get_field_name('srp_dv'); ?>" type="checkbox" value="1" <?php checked( '1', $srp_dv ); ?> />
<label for="<?php echo $this->get_field_id('srp_dv'); ?>"><?php _e('Show date after title.', 'wp_widget_plugin'); ?></label></p><p>*Here is the list of categories and ID numbers:
 <?php //category list
  $categories = get_categories(); 
  foreach ($categories as $category) {
  	$option = '<option value="/category/archives/'.$category->category_nicename.'">';
	$option .= $category->cat_name;
	$option .= ' ID = '.$category->term_id.'.';
	$option .= '</option>';
	echo $option;
  }
 ?> </p>
<p>Hope that you will find this plugin useful...</p>
<p align="right"><a href="http://www.zoranmaric.com"> Zoran Maric</a></p>
<br />
<?php
}
// widget update
function update($new_instance, $old_instance) {
$instance = $old_instance;
// Fields
	$instance['title'] = strip_tags($new_instance['title']);
	$instance['srp_ct'] = strip_tags($new_instance['srp_ct']);
	$instance['srp_pp'] = strip_tags($new_instance['srp_pp']);
	$instance['srp_lv'] = strip_tags($new_instance['srp_lv']);
	$instance['srp_dv'] = strip_tags($new_instance['srp_dv']);
	$instance['srp_br'] = strip_tags($new_instance['srp_br']);
return $instance;
}
// widget display
function widget($args, $instance) {
extract( $args );
// these are the widget options
	$title = apply_filters('widget_title', $instance['title']);
	$srp_ct = $instance['srp_ct'];
	$srp_pp = $instance['srp_pp'];
	$srp_lv = $instance['srp_lv'];
	$srp_br = $instance['srp_br'];
	$srp_dv = $instance['srp_dv'];
echo $before_widget;
// Display the widget
echo '<div class="zmspr">';
// Check if title is set
if ( $title ) {
echo $before_title . $title . $after_title;
}
?>
<?php
$kat = new WP_Query( 'cat='.$srp_ct.'&posts_per_page='.$srp_pp );
while($kat->have_posts()) : $kat->the_post(); ?>
<ul><li><div class="naslov"> <a href="<?php the_permalink()?>" rel="bookmark"><?php the_title()?></a></div><?php if ($srp_dv){	echo "<div class='datum'>";	$dta = the_time(get_option('date_format'));	echo $dta;	echo "</div>";}?>
<div class="okvir">
<?php if ( has_post_thumbnail() ) {
the_post_thumbnail( array($srp_lv,$srp_lv), array('class' => 'slika') );
}
custom_excerpt($srp_br, '...');
?>
</div>
</li></ul>
<?php endwhile;wp_reset_query();
echo '</div>';
echo $after_widget;
}
}
// register widget
add_action('widgets_init', create_function('', 'return register_widget("srp_plugin");'));?>