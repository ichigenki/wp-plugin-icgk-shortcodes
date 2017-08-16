<?php
/*
Plugin Name: ICGK Shortcodes
Plugin URI: 
Description: ウィジェットでも使えるショートコード集 [home] [sitename] [link id="" slug="" (blog="1")][/link_id] [title id="" slug="" (blog="1")] [content id="" slug="" (type="page" blog="1")] [excerpt id="" slug="" (type="page" blog="1")] [list_child id="" slug="" (type="page" blog="1")] [list_childpage] [bloglist_id (title="リストタイトル" type="post/page" odr="ASC" odrby="date/menu_order" blog="1")] [temp_file file=''] [widget_childpage id='' slug=""]
Version: 2.0.1
Author: ICHIGENKI
Author URI: 
License: GPL2



[home]：サイトのホームURLを表示する
[sitename]：サイトのタイトルを表示する
[link id="" slug="" (blog="1")][/link_id]：IDまたはスラッグを指定してページ（ポスト）にリンク（パーマリンク）する
[title id="" slug="" (blog="1")]：IDまたはスラッグを指定してタイトルを表示する
[content id="" slug="" (type="page" blog="1")]：IDまたはスラッグを指定して本文を表示する
[excerpt id="" slug="" (type="page" blog="1")]：IDまたはスラッグを指定して抜粋を表示する
[list_child id="" slug="" (type="page" blog="1")]：指定IDまたはスラッグの子ページのタイトルをリストアップ
[list_childpage]：現在の固定ページの子固定ページのタイトルをリストアップ
[linkid (title="リストタイトル" type="post/page" odr="ASC" odrby="date/menu_order" blog="1")]：「リンクＩＤ」をリスト表示
[temp_file file='']：テンプレートを呼び出す
[widget_childpage id='' slug=""]：ウィジェット用 IDまたはスラッグを指定してその子ページへのリンク表示
*/

// 【ショートコード】： ウィジェットでショートコードを使えるようにする
add_filter('widget_text', 'do_shortcode' );

/* **** [home]：サイトのホームURLを表示する **** */
function my_site_url() {
ob_start();
echo home_url();
$td .= ob_get_clean();
return $td;
}
add_shortcode('home', 'my_site_url');

/* **** [sitename]：サイトのタイトルを表示する **** */
function get_my_sitename() {
ob_start();
echo bloginfo('sitename');
$td .= ob_get_clean();
return $td;
}
add_shortcode('sitename', 'get_my_sitename');

/* **** [link id="" slug="" (blog="1")][/link_id]：IDまたはスラッグを指定してページ（ポスト）にリンク（パーマリンク）する **** */
function link_by_id_or_slug($atts, $content=null) {
  ob_start();
  extract(shortcode_atts(array(
    'id' => '',
    'slug' => '',
    'type' => 'page',
    'blog' => '1',
  ), $atts));
  if ( is_multisite() ) switch_to_blog(esc_attr($blog)); 
  if ( esc_attr($id) == '' ) {
    global $wpdb;
    $post_name = esc_attr($slug);
    $post_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '".$post_name."'");
    $post_id = esc_html($post_id);
  } else {
    $post_id = esc_attr($id);
  }
  echo '<a href="' . get_permalink($post_id) . '" class="link-post';
  if ( esc_attr($id) == '' ) {
    echo' link-slug-' . esc_attr($slug);
  } else {
    echo' link-id-' . esc_attr($id);
  }
  echo '" title="「' . get_the_title($post_id) . '」ページへ">' . do_shortcode($content) . '</a>';
  if ( is_multisite() ) restore_current_blog();
  $output = ob_get_clean();
  return $output;
}
add_shortcode('link', 'link_by_id_or_slug');

/* **** [title id="" slug="" (blog="1")]：IDまたはスラッグを指定してタイトルを表示する **** */
function title_by_id_or_slug($atts) {
  ob_start();
  extract(shortcode_atts(array(
    'id' => '',
    'slug' => '',
    'blog' => '1',
  ), $atts));
  if ( is_multisite() ) switch_to_blog(esc_attr($blog));
  if ( esc_attr($id) == '' ) {
    global $wpdb;
    $post_name = esc_attr($slug);
    $post_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '".$post_name."'");
    $post_id = esc_html($post_id);
  } else {
    $post_id = esc_attr($id);
  }
  echo get_the_title($post_id);
  if ( is_multisite() ) restore_current_blog();
  $output = ob_get_clean();
  return $output;
}
add_shortcode('title', 'title_by_id_or_slug');

/* **** [content id="" slug="" (type="page" blog="1")]：IDまたはスラッグを指定して本文を表示する **** */
function content_by_id_or_slug($atts) {
  ob_start();
  extract(shortcode_atts(array(
    'id' => '',
    'blog' => '1',
  ), $atts));
  if ( is_multisite() ) switch_to_blog(esc_attr($blog));
  if ( esc_attr($id) == '' ) {
    global $wpdb;
    $post_name = esc_attr($slug);
    $post_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '".$post_name."'");
    $post_id = esc_html($post_id);
  } else {
    $post_id = esc_attr($id);
  }
  echo nl2br(get_post($post_id)->post_content) . "\n";
  if ( is_multisite() ) restore_current_blog();
  $output = ob_get_clean();
  return $output;
}
add_shortcode('content', 'content_by_id_or_slug');

/* **** [excerpt id="" slug="" (type="page" blog="1")]：IDまたはスラッグを指定して抜粋を表示する **** */
function excerpt_by_id_or_slug($atts) {
  ob_start();
  extract(shortcode_atts(array(
    'id' => '',
    'slug' => '',
    'blog' => '1',
  ), $atts));
  if ( is_multisite() ) switch_to_blog(esc_attr($blog));
  if ( esc_attr($id) == '' ) {
    global $wpdb;
    $post_name = esc_attr($slug);
    $post_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '".$post_name."'");
    $post_id = esc_html($post_id);
  } else {
    $post_id = esc_attr($id);
  }
  nl2br(get_post($post_id)->post_excerpt) . "\n";
  if ( is_multisite() ) restore_current_blog();
  $output = ob_get_clean();
  return $output;
}
add_shortcode('excerpt', 'excerpt_by_id_or_slug');

/* **** [list_child id="" slug="" (type="page" blog="1")]：指定IDまたはスラッグの子ページのタイトルをリストアップ **** */
function child_list($atts) {
  ob_start();
  extract(shortcode_atts(array(
    'id' => '',
    'slug' => '',
    'type' => 'page',
    'blog' => '1',
  ), $atts));
  if ( is_multisite() ) switch_to_blog(esc_attr($blog));
  if ( esc_attr($id) == '' ) {
    global $wpdb;
    $post_name = esc_attr($slug);
    $post_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '".$post_name."'");
    $post_id = esc_html($post_id);
  } else {
    $post_id = esc_attr($id);
  }
  $def = array(
    'post_type' => esc_attr($type),
    'orderby' => 'menu_order',
    'order' => 'asc',
    'post_parent' => $post_id,
    'posts_per_page' => -1,
  );
  $my_query = new WP_Query($def);
  $pid = $def['post_parent']; 
  $title = $pid->post_title;
  $slug = $pid->post_name;
  echo '<ul class="childlist-list parent-id-' . $pid . ' ' . $slug . '">' . "\n";
  if ($my_query -> have_posts()) {
    while ($my_query -> have_posts()) {
      $my_query -> the_post();
      echo '<li class="item-id' . get_the_ID() . '"><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>' . "\n";
    }
  }
  wp_reset_postdata();
  echo '</ul>';
  if ( is_multisite() ) restore_current_blog();
  $output = ob_get_clean();
  return $output;
}
add_shortcode('list_child_id', 'child_list');

/* **** [list_childpage]：現在の固定ページの子固定ページのタイトルをリストアップ **** */
function childpage_list($args) {
  $def = array(
    'post_type' => 'page',
    'orderby' => 'menu_order',
    'order' => 'asc',
    'post_parent' => '',
    'posts_per_page' => -1,
  );
  $args = shortcode_atts($def, $args);
  ob_start();
  $child_query = new WP_Query($args);
  $pid = get_post($post_id); 
  $title = $pid->post_title;
  $slug = $pid->post_name;
  echo '<div class="content" id="content-id-' . get_the_ID() . '">';
  echo '<ul class="childlist-list">' . "\n";
  if ($child_query -> have_posts()) {
    while ($child_query -> have_posts()) {
      $child_query -> the_post();
      echo '<li class="item-id-' . get_the_ID() . '"><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>' . "\n";
    }
  }
  wp_reset_postdata();
  echo '</ul>';
  echo '</div>' . "\n";
  $output = ob_get_clean();
  return $output;
}
add_shortcode('list_childpage', 'childpage_list');

/* **** [linkid (title="リストタイトル" type="post/page" odr="ASC" odrby="date/menu_order" blog="1")]：「リンクＩＤ」をリスト表示 **** */
function linkid_list($atts) { /* 固定ページ */
  ob_start();
  extract(shortcode_atts(array(
    'title' => 'リストタイトル',
    'type' => 'page',
    'odr' => 'ASC',
    'odrby' => 'menu_order',
    'blog' => '1',
  ), $atts));
  if ( is_multisite() ) switch_to_blog(esc_attr($blog));
  get_linkid_pagelist(esc_attr($title), esc_attr($type), esc_attr($odr), esc_attr($odrby));
  if ( is_multisite() ) restore_current_blog();
  $output = ob_get_clean();
  return $output;
}
add_shortcode('pagelist_id', 'pagelist_by_id');
function bloglist_by_id($atts) { /* 個別投稿（ブログ） */
  ob_start();
  extract(shortcode_atts(array(
    'title' => 'リストタイトル',
    'type' => 'post',
    'odr' => 'ASC',
    'odrby' => 'date',
    'blog' => '1',
  ), $atts));
  if ( is_multisite() ) switch_to_blog(esc_attr($blog));
  get_linkid_bloglist(esc_attr($title), esc_attr($type), esc_attr($odr), esc_attr($odrby));
  if ( is_multisite() ) restore_current_blog();
  $output = ob_get_clean();
  return $output;
}
add_shortcode('linkid', 'linkid_list');

/* **** [temp_file file='']：テンプレートを呼び出す **** */
function template_by_name($atts) {
  ob_start();
  extract(shortcode_atts(array(
    'file' => '',
  ), $atts));
  get_template_part(esc_attr($file));
  $output = ob_get_clean();
  return $output;
}
add_shortcode('temp_file', 'template_by_name');

/* **** [widget_childpage id='' slug=""]：ウィジェット用 IDまたはスラッグを指定してその子ページへのリンク表示 **** */
function pegetree_by_name($atts) {
  ob_start();
  extract(shortcode_atts(array(
    'id' => '',
    'slug' => '',
    'type' => 'page',
    'blog' => '1',
  ), $atts));
  if ( is_multisite() ) switch_to_blog(esc_attr($blog));
  if ( esc_attr($id) == '' ) {
    global $wpdb;
    $post_name = esc_attr($slug);
    $post_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '".$post_name."'");
    $post_id = esc_html($post_id);
  } else {
    $post_id = esc_attr($id);
  }
  echo '<div id="widget-pagetree">'."\n";
  echo '<ul class="pagetree-list parent">'."\n";
  wp_list_pages( array(
    'child_of'=> $post_id,
    'sort_column'  => 'menu_order',
    'title_li' => __(''),
  ) );
  echo '</ul>'."\n";
  echo '</div><!-- /#widget-pagetree -->'."\n";
  if ( is_multisite() ) restore_current_blog();
  $output = ob_get_clean();
  return $output;
}
add_shortcode('widget_childpage', 'pegetree_by_name');
