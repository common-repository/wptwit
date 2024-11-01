<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class wpTwitWidget extends WP_Widget
{
  function wpTwitWidget() {
    parent::WP_Widget(false, 'wpTwit Widget');
  }
  /** @see WP_Widget::widget */
    function widget($args, $instance) {
      extract( $args );
      $upload_dir = wp_upload_dir();

      $title = esc_attr($instance['title']);
      $twitter_username = esc_attr($instance['twitter_username']);
      $number_of_posts = esc_attr($instance['number_of_posts']);
      $show_header = esc_attr($instance['show_header']);
      $show_follow_us_as = esc_attr($instance['show_follow_us_as']);

      ?>
      <?php echo $before_widget; ?>
        <?php echo $before_title; ?>
        <?php echo $title ?>
        <?php echo $after_title; ?>
      <?php

      $twitter = new lineMyTwit(array(
          'user' => $twitter_username,
          'myTwitHeader' => ($show_header == 'on'),
          'show_follow_us_as' => $show_follow_us_as,
          'postLimit' => $number_of_posts,
          'email' => get_option('admin_email'),                       // USE ADMIN EMAIL FROM GENERAL SETTINGS
          'base_dir' => $upload_dir['basedir'] . '/twitter'
        ));
      echo $twitter->show();

      ?><?php echo $after_widget; ?><?php
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
	$instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
	$instance['twitter_username'] = strip_tags($new_instance['twitter_username']);
        $instance['number_of_posts'] = strip_tags($new_instance['number_of_posts']);
        $instance['show_header'] = strip_tags($new_instance['show_header']);
        $instance['show_follow_us_as'] = strip_tags($new_instance['show_follow_us_as']);

        return $instance;
    }

  function form($instance) {
    $title = esc_attr($instance['title']);
    $twitter_username = esc_attr($instance['twitter_username']);
    $number_of_posts = esc_attr($instance['number_of_posts']);
    $show_header = esc_attr($instance['show_header']);
    $show_follow_us_as = esc_attr($instance['show_follow_us_as']);

    $options = '';
    for($count_number_of_posts=1;$count_number_of_posts<=20;$count_number_of_posts++) {
      $selected = ($count_number_of_posts == $number_of_posts ? ' SELECTED' : '');
      $options .= '<option value="' . $count_number_of_posts . '"' . $selected . '>'. $count_number_of_posts . '</option>';
    }

    $follow_us_options_markup = '';
    $follow_us_options = array('' => 'Do not display', 'Follow us on Twitter' => 'Follow us on Twitter', 'Follow me on Twitter' => 'Follow me on Twitter', 'Follow @[USERNAME] on Twitter' => 'Follow @[USERNAME] on Twitter');
    foreach($follow_us_options as $value => $follow_us_option) {
      $selected = ($show_follow_us_as == $follow_us_option ? ' SELECTED' : '');
      $follow_us_options_markup .= '<option value="' . $value . '"' . $selected . '>'. $follow_us_option . '</option>';
    }
?>
  <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title:'); ?></label><input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
  <p><label for="<?php echo $this->get_field_id('twitter_username'); ?>"><?php _e('Twitter Username:'); ?></label><input class="widefat" id="<?php echo $this->get_field_id('twitter_username'); ?>" name="<?php echo $this->get_field_name('twitter_username'); ?>" type="text" value="<?php echo $twitter_username; ?>" /></p>
  <p><label for="<?php echo $this->get_field_id('number_of_posts'); ?>"><?php _e('Number of Posts:'); ?></label><select class="widefat" id="<?php echo $this->get_field_id('number_of_posts'); ?>" name="<?php echo $this->get_field_name('number_of_posts'); ?>"><?php echo $options; ?></select></p>
  <p><label for="<?php echo $this->get_field_id('show_follow_us_as'); ?>"><?php _e('Show Follow Us as:'); ?></label><select class="widefat" id="<?php echo $this->get_field_id('show_follow_us_as'); ?>" name="<?php echo $this->get_field_name('show_follow_us_as'); ?>"><?php echo $follow_us_options_markup; ?></select></p>
  <p><input class="checkbox" id="<?php echo $this->get_field_id('show_header'); ?>" name="<?php echo $this->get_field_name('show_header'); ?>" type="checkbox" <?php if($show_header) {?>CHECKED<?php } ?> />&nbsp;<label for="<?php echo $this->get_field_id('show_header'); ?>"><?php _e('Show Twitter Header:'); ?></label></p>
<?php
}
}
?>
