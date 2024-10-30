<?php
/*
Plugin Name: Content Attacher
Plugin URI: https://github.com/mostafa272/Content-Attacher
Description: The Content Attacher appends something to Wordpress posts or pages.
Version: 1.0
Author: Mostafa Shahiri<mostafa2134@gmail.com>
Author URI: https://github.com/mostafa272
*/
/*  Copyright 2009  Mostafa Shahiri(email : mostafa2134@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
register_activation_hook( __FILE__, 'coat_content_attacher_activation' );
register_uninstall_hook( __FILE__, 'coat_content_attacher_uninstall' );
add_action('admin_menu', 'coat_content_attacher_setup_menu');
add_action( 'admin_enqueue_scripts', 'coat_content_attacher_scripts' );
add_action( 'wp_head', 'coat_content_attacher_custom_scripts' );
function coat_content_attacher_activation(){
global $wpdb;
$wpdb->query("CREATE TABLE IF NOT EXISTS `content_attacher` (
  `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int(1) UNSIGNED NOT NULL,
  `show_on_fulltext` int(1) UNSIGNED NOT NULL,
  `status` int(1) UNSIGNED NOT NULL,
  `posts` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `pages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cats` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `authors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `title` (`title`(100))
)");
}
function coat_content_attacher_uninstall(){
global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS `content_attacher`");
}
function coat_content_attacher_setup_menu(){
        add_menu_page( 'Content Attacher Plugin', 'Content Attacher', 'manage_options', 'content-attacher', 'coat_content_attacher_init' );
}
/*
function image_label_maker_register_settings(){

    register_setting('image-label-maker-settings', 'access_imglbl');
     register_setting('image-label-maker-settings', 'filesize_imglbl');
     register_setting('image-label-maker-settings', 'question_imglbl');
     register_setting('image-label-maker-settings', 'deletetime_imglbl');
} */
function coat_content_attacher_init(){
global $wpdb;
if(current_user_can('manage_options'))
{
  $item_id=(isset($_GET['itemid']) && !empty($_GET['itemid']))?intval(sanitize_text_field($_GET['itemid'])):'';

  if(empty($item_id))
  {
  $title=(isset($_POST['att-title']) && !empty($_POST['att-title']))?sanitize_text_field($_POST['att-title']):'';
  $desc=(isset($_POST['att-editor']) && !empty($_POST['att-editor']))?wp_kses_post($_POST['att-editor']):'';
  $pos=(isset($_POST['att-position']) && !empty($_POST['att-position']))?intval(sanitize_text_field($_POST['att-position'])):1;
  $showfulltext=(isset($_POST['att-fulltext']) && !empty($_POST['att-fulltext']))?intval(sanitize_text_field($_POST['att-fulltext'])):1;
  $status=(isset($_POST['att-status']) && !empty($_POST['att-status']))?intval(sanitize_text_field($_POST['att-status'])):1;
  $allposts=(isset($_POST['att-posts']) && !empty($_POST['att-posts']))?(array)$_POST['att-posts']:array();
  $allpages=(isset($_POST['att-pages']) && !empty($_POST['att-pages']))?(array)$_POST['att-pages']:array();
  $allcats=(isset($_POST['att-cats']) && !empty($_POST['att-cats']))?(array)$_POST['att-cats']:array();
  $allauthors=(isset($_POST['att-authors']) && !empty($_POST['att-authors']))?(array)$_POST['att-authors']:array();
  }
  else
  {
  $item_obj = $wpdb->get_row( $wpdb->prepare("SELECT * FROM content_attacher WHERE ID = %d",$item_id));
  $title=(isset($_POST['att-title']) && !empty($_POST['att-title']))?sanitize_text_field($_POST['att-title']):$item_obj->title;
  $desc=(isset($_POST['att-editor']) && !empty($_POST['att-editor']))?wp_kses_post($_POST['att-editor']):$item_obj->description;
  $pos=(isset($_POST['att-position']) && !empty($_POST['att-position']))?intval(sanitize_text_field($_POST['att-position'])):$item_obj->position;
  $showfulltext=(isset($_POST['att-fulltext']) && !empty($_POST['att-fulltext']))?intval(sanitize_text_field($_POST['att-fulltext'])):$item_obj->show_on_fulltext;
  $status=(isset($_POST['att-status']) && !empty($_POST['att-status']))?intval(sanitize_text_field($_POST['att-status'])):$item_obj->status;
  $allposts=(isset($_POST['att-posts']) && !empty($_POST['att-posts']))?(array)$_POST['att-posts']:explode(',',$item_obj->posts);
  $allpages=(isset($_POST['att-pages']) && !empty($_POST['att-pages']))?(array)$_POST['att-pages']:explode(',',$item_obj->pages);
  $allcats=(isset($_POST['att-cats']) && !empty($_POST['att-cats']))?(array)$_POST['att-cats']:explode(',',$item_obj->cats);
  $allauthors=(isset($_POST['att-authors']) && !empty($_POST['att-authors']))?(array)$_POST['att-authors']:explode(',',$item_obj->authors);
  }

  if(!empty($allposts))
  {
   if (($key1 = array_search("0", $allposts)) !== false) {
    unset($allposts[$key1]);
    $allposts=array_values($allposts);
   }
 }
 if(!empty($allpages))
 {
   if (($key2 = array_search("0", $allpages)) !== false) {
    unset($allpages[$key2]);
    $allpages=array_values($allpages);
   }
 }
 if(!empty($allcats))
 {
   if (($key3 = array_search("0", $allcats)) !== false) {
    unset($allcats[$key3]);
    $allcats=array_values($allcats);
   }
 }
 if(!empty($allauthors))
 {
   if (($key4 = array_search("0", $allauthors)) !== false) {
    unset($allauthors[$key4]);
    $allauthors=array_values($allauthors);
   }
 }

     if ( isset($_POST['additem']) && isset($_POST['content_attacher_add_nonce']) && wp_verify_nonce($_POST['content_attacher_add_nonce'], 'content_attacher_add'))
     {
      $alposts=implode(',',$allposts);
      $alpages=implode(',',$allpages);
      $alcats=implode(',',$allcats);
      $alauthors=implode(',',$allauthors);

       $item_count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(ID) FROM content_attacher WHERE title = %s",$title));
       if(($item_count==0) && empty($item_id))
       {  $wpdb->insert('content_attacher',array('title'=>$title,'description'=>$desc,'position'=>$pos,'show_on_fulltext'=>$showfulltext,'status'=>$status,'posts'=>$alposts,'pages'=>$alpages,'cats'=>$alcats,'authors'=>$alauthors),array('%s','%s','%d','%d','%d','%s','%s','%s','%s'));
       }
       else
       {
          $wpdb->update('content_attacher',array('title'=>$title,'description'=>$desc,'position'=>$pos,'show_on_fulltext'=>$showfulltext,'status'=>$status,'posts'=>$alposts,'pages'=>$alpages,'cats'=>$alcats,'authors'=>$alauthors),array('ID'=>$item_id),array('%s','%s','%d','%d','%d','%s','%s','%s','%s'),array('%d'));
       }
     }
     if ( isset($_POST['delbotton']) && isset($_POST['content_attacher_form_nonce']) && wp_verify_nonce($_POST['content_attacher_form_nonce'], 'content_attacher_form'))
     {
       $delete_items = $_POST['checkitems'];
       if(!empty($delete_items) && (count($delete_items)>0))
       {
           foreach ($delete_items as $item)
           $wpdb->delete('content_attacher',array('ID'=>$item),array('%d'));
       }

     }
     if ((isset($_POST['newbotton']) && isset($_POST['content_attacher_form_nonce']) && wp_verify_nonce($_POST['content_attacher_form_nonce'], 'content_attacher_form'))||(!empty($item_id) && wp_verify_nonce($_REQUEST['coat_nonce'], 'coat_edit_nonce') ))
     {
      echo (empty($item_id))?'<h1>New Item</h1><br/>':'<h1>Edit Item</h1><br/>';
      echo '<form class="coat_form" action="'.esc_url($_SERVER['REQUEST_URI']).'" method="post">';
      echo '<p><label>Title <small>(required)</small>:</label><br/>';
      echo '<input type="text" name="att-title" value="'.$title.'" required/></p>';
      echo '<p><label></label></p>';
      $editor_id = 'att-editor';
      $editor_settings= array('editor_height'=>100);
       wp_editor( $desc, $editor_id,$editor_settings );
       echo '<br/>';
      echo '<p><label>Position:</label><br/>';
      echo '<select name="att-position" >';
      echo '<option value="1" '.selected(1,$pos).'>Before posts|pages</option>';
      echo '<option value="2" '.selected(2,$pos).'>After posts|pages</option>';
      echo '</select></p>';
      //Show on fulltext
      echo '<p><label>Show only in the page|post view:</label><br/>';
      echo '<select name="att-fulltext" >';
      echo '<option value="1" '.selected(1,$showfulltext).'>No</option>';
      echo '<option value="2" '.selected(2,$showfulltext).'>Yes</option>';
      echo '</select></p>';
      //status
      echo '<p><label>Status:</label><br/>';
      echo '<select name="att-status" >';
      echo '<option value="1" '.selected(1,$status).'>Unpublished</option>';
      echo '<option value="2" '.selected(2,$status).'>Published</option>';
      echo '</select></p>';
     $args = array( 'numberposts' => -1,'post_type'=>'post' ,'post_status'=>'publish');
   $posts = get_posts($args);
   echo '<p><label>Include Posts:</label><br>';
   echo '<select name="att-posts[]" multiple="true">';
   echo '<option value="0" '.selected('true',in_array('0' , $allposts)?'true':'false' ).'>-- Select posts --</option>';
   foreach ($posts as $p){
   echo '<option value="'.$p->ID.'" '.selected( 'true' , in_array($p->ID,$allposts)?'true':'false' ).'>'.$p->post_title.'</option>';
   }
   echo '</select></p>';
      //get pages
   $pages = get_pages();
   echo '<p><label>Include Pages:</label><br>';
  echo '<select name="att-pages[]" multiple="true">';
  echo '<option value="0" '.selected('true',in_array('0' , $allpages)?'true':'false' ).'>-- Select pages --</option>';
   foreach ($pages as $page){
   echo '<option value="'.$page->ID.'" '.selected( 'true' , in_array($page->ID,$allpages)?'true':'false' ).'>'.$page->post_title.'</option>';
   }
   echo '</select></p>';
  //get categories
   $cats = get_categories();
   echo '<p><label>Include Categories:</label><br>';
  echo '<select name="att-cats[]" multiple="true">';
  echo '<option value="0" '.selected('true',in_array('0' , $allcats)?'true':'false' ).'>-- Select categories --</option>';
   foreach ($cats as $cat){
   echo '<option value="'.$cat->term_id.'" '.selected( 'true' , in_array($cat->term_id,$allcats)?'true':'false' ).'>'.$cat->cat_name.'</option>';
   }
   echo '</select></p>';
   //get authors
   $authors = get_users();
   echo '<p><label>Include Authors:</label><br>';
  echo '<select name="att-authors[]" multiple="true">';
  echo '<option value="0" '.selected('true',in_array('0' , $allauthors)?'true':'false' ).'>-- Select authors --</option>';
   foreach ($authors as $author){
   echo '<option value="'.$author->ID.'" '.selected( 'true' , in_array($author->ID,$allauthors)?'true':'false' ).'>'.$author->display_name.'['.$author->user_login.']</option>';
   }
   echo '</select></p>';
   wp_nonce_field( 'content_attacher_add', 'content_attacher_add_nonce' );
   echo '<p><input class="coat_btn" type="submit" name="additem" value="Save" /><a class="backlink" href="'.admin_url('admin.php?page=content-attacher').'">Back</a></p></form>';
     }
     else
     {
    echo '<h1>Content Attacher Management</h1>';
   echo '<form name="coatform" action="'.esc_url($_SERVER['REQUEST_URI']).'" method="post">';
   wp_nonce_field( 'content_attacher_form', 'content_attacher_form_nonce' );
   $editnonce = wp_create_nonce('coat_edit_nonce');
   echo '<input class="coat_btn" type="submit" name="newbotton" value="+ New Item" />';
   echo '<input class="coat_btn" type="submit" name="delbotton" value="Delete Item" />';
   $results=$wpdb->get_results("SELECT ID, title, description,position,status FROM content_attacher");
    echo '<input type="text" id="coat_search" onkeyup="lookuptable(10)" placeholder="Search for ..."><br/>';
   echo '<table id="content_attacher" class="widefat fixed" cellspacing="0"><thead><tr><th id="coat_id">ID</th><th id="coat_title" >Title</th><th id="coat_content">Content</th><th id="coat_position">Position</th><th id="coat_status">Status</th><th id="coat_delete">Delete?</th></tr></thead>';
    if($results)
     { echo '<tbody>';
     foreach($results as $k=>$res)
     {  $tmp_key=$k+1;
     echo '<tr>';
     echo '<td>'.$tmp_key.'</td>';
     echo '<td><a href="'.admin_url('admin.php?page=content-attacher').'&itemid='.$res->ID.'&coat_nonce='.$editnonce.'" onclick="document.coatform.submit();">'.$res->title.'</a></td>';
     echo '<td>'.wp_trim_words(esc_html($res->description),15,'...').'</td>';
     echo ($res->position==1)?'<td>Before</td>':'<td>After</td>';
     echo ($res->status==1)?'<td class="coat_red">Unpublished</td>':'<td class="coat_green">Published</td>';
     echo '<td class="coat_checkbox">'.'<input name="checkitems[]" type="checkbox" id="checkbox[]" value="'.$res->ID.'"></td>';
     echo '</tr>';
     }
     echo '</tbody>';
     }
     echo '</table><div id="coat_pagination" class="coat_pagination"></div></form>';
   }
}
      else {
      echo "You don't have enough permission";
      }
}
/**
 * Add our JS and CSS files
 */
function coat_content_attacher_scripts() {

    if ($_GET['page'] != 'content-attacher') {
        return;
    }
      wp_enqueue_script( 'content-attacher-script', plugins_url( 'js/script.js', __FILE__ ),array('jquery'),'1.0',false );
        wp_enqueue_style( 'content-attacher-style', plugins_url( 'css/style.css', __FILE__ ) );
}
function coat_content_attacher_custom_scripts(){
if(is_admin() && $_GET['page']=='content-attacher')
{
$jscript="jQuery('document').ready(function(){
	pagination(10);
});";
    echo '<script type="text/javascript">'.$jscript.'</script>';
    }
}
//frontend
function coat_content_attacher_apply($content)
{  global $wpdb;
  $results=$wpdb->get_results("SELECT * FROM content_attacher WHERE status=2");
  $postid=get_the_ID();
  $catsid=get_the_category($postid);
  foreach($catsid as $k=>$cat)
  $catid[$k]=$cat->term_id;
  $authorid=get_the_author_meta('ID');
  if($results)
  {
  foreach ($results as $res)
  {
     $attach=0;
     $posts=explode(',',$res->posts);
     $pages=explode(',',$res->pages);
     $cats=explode(',',$res->cats);
     $authors=explode(',',$res->authors);
     if(in_array($postid,$posts) || in_array($postid,$pages))
     {
      $attach=1;
     }
     if(!empty($catid))
     {
     foreach ($catid as $c)
     {
     if(in_array($c,$cats))
      {
       $attach=1;
      }
     }
     }
     if(in_array($authorid,$authors))
     {
      $attach=1;
     }
   if($attach==1)
   {
     if($res->show_on_fulltext==2)
     {
     if(is_single() || is_page())
     {
      if($res->position==1)
       {
        $content='<div>'.$res->description.'</div>'.$content;
       }
       else
       {
        $content= $content.'<div>'.$res->description.'</div>';
       }
      }
     }
     else
     {
     if($res->position==1)
       {
        $content='<div>'.$res->description.'</div>'.$content;
       }
       else
       {
        $content= $content.'<div>'.$res->description.'</div>';
       }
     }

   }

  }
  }
  return $content;
}

add_filter('the_content','coat_content_attacher_apply');