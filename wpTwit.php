<?php
/*
Plugin Name: wpTwit
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A twitter plugin for Wordpress using lineMyTwit
Version: 1.0
Author: Christopher Shennan
Author URI: http://www.chrisshennan.com
*/
require_once(dirname(__FILE__) . '/lib/myTwit/lineMyTwit.class.php');
require_once(dirname(__FILE__) . '/wpTwitWidget.php');

class wpTwit
{
  public function wpTwit()
  {
    if(is_admin()) {
      add_action('admin_menu', array($this, 'admin_menu'));
    }

    add_action('widgets_init', create_function('', 'register_widget("wpTwitWidget");'));
  }

  // ADD THE MENU ITEMS
  public function admin_menu()
  {
    add_options_page('wpTwit', 'wpTwit', 'administrator', 'wpTwit-general', array($this,'admin_options'));
  }

  public function admin_options() {
    if (is_admin ()) {
      $subpage = $_GET['subpage'];
      if (!$subpage || !file_exists(dirname(__FILE__) . '/view/' . $_GET['subpage'] . '.php')) {
        $subpage = 'general';
      }

      $func_name = 'execute' . str_replace(' ', '', ucwords(str_replace('_', ' ', $subpage)));
      if (is_callable(array($this, $func_name))) {
        call_user_func(array($this, $func_name));
      } else {
        die('Function ' . $func_name . ' not defined');
      }
    }
  }

  public function executeGeneral()
  {
    include(dirname(__FILE__) . '/view/general.php');
  }
}

$wpTwit = new wpTwit();