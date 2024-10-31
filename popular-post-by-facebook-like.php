<?php
/*
Plugin Name: Popular Post By Facebook Like
Plugin URI: http://wp24h.com/top-popular-posts-by-facebook-like-count.html
Description: Make a widget to show Popular Post By Facebook Like.
Version: 1.0
Author: WPclever
Author URI: http://wp24h.com/author/wp24h
License: GPL2
*/

add_action('the_content', 'get_facebook_like');
function get_facebook_like($content) {
    global $post, $wp_query;
    $post_id = $post->ID;
    $vurl = get_permalink($post_id);
    $a = file_get_contents('https://graph.facebook.com/?ids='.$vurl);
    $b = json_decode($a,true);
    $key = array_keys($b);
    $key = $key[0];
    if(isset($b[$key]['shares'])) {
        $share_id = $b[$key]['shares'];
    } else {
        $share_id = 0;
    };
    update_post_meta($post_id, '_vfblike', $share_id);
    return $content;
}

function vfblike_widgets() {
    register_widget('ppfbWidget'); }
class ppfbWidget extends WP_Widget
{
  function ppfbWidget()
  {
    $widget_ops = array('classname' => 'ppfbWidget', 'description' => 'Display Popular Post By Facebook Like count' );
    $this->WP_Widget('ppfbWidget', 'Popular Post By Facebook Like', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'vfbtitle' => 'Popular Post By Facebook Like', 'vfbpost' => '10' ) );
    $vfbtitle = $instance['vfbtitle'];
    $vfbpost = $instance['vfbpost'];
?>
  <p><label for="<?php echo $this->get_field_id('vfbtitle'); ?>">Title: <br /><input class="widefat" id="<?php echo $this->get_field_id('vfbtitle'); ?>" name="<?php echo $this->get_field_name('vfbtitle'); ?>" type="text" value="<?php echo esc_attr($vfbtitle); ?>" /></label></p>
  <p><label for="<?php echo $this->get_field_id('vfbpost'); ?>">Number of posts to show: <br /><input class="widefat" id="<?php echo $this->get_field_id('vfbpost'); ?>" name="<?php echo $this->get_field_name('vfbpost'); ?>" type="text" value="<?php echo esc_attr($vfbpost); ?>" /></label></p>
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['vfbtitle'] = $new_instance['vfbtitle'];
    $instance['vfbpost'] = $new_instance['vfbpost'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $vfbtitle = empty($instance['vfbtitle']) ? ' ' : apply_filters('widget_title', $instance['vfbtitle']);
    $vfbpost = $instance['vfbpost'];
 
    if (!empty($vfbtitle))
      echo $before_title . $vfbtitle . $after_title;
 
    // WIDGET CODE GOES HERE
    $vfbargs = array(
        'posts_per_page' => $vfbpost,
        'meta_key' => '_vfblike',
        'orderby' => 'meta_value_num',
        'order' => 'DESC'
    );
    query_posts( $vfbargs );
    while ( have_posts() ) : the_post();
        echo '<li><a href="';
        the_permalink();
        echo '">';
        the_title();
        echo '</a></li> ('.get_post_meta(get_the_ID(), '_vfblike', true).' likes)';
    endwhile;
    wp_reset_query();

    echo $after_widget;
  }
 
}
add_action( 'widgets_init', 'vfblike_widgets' );
?>