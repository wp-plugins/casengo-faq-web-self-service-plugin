<?php
   /*
   Plugin Name: Casengo FAQ - Selfservice Plugin
   Plugin URI: http://www.casengo.com/plugins/wordpress/v2
   Description: A plugin to embed the Casengo FAQ Selfservice page to your Wordpress site
   Version: 1.1
   Author: Thijs van der Veen
   Author URI: http://www.casengo.com
   License: GPL2
   */

/** WP OPTION VARIABLES **
    cas_widget_domain = Casengo subdomain name (example.casengo.com)
    cas_faq_enabled = FAQ page enabled (draft, private, public)
    cas_faq_searchbar = Search bar (ON/OFF)
    cas_faq_toparticlesection = Top Article Section
    cas_faq_pagetitle =  Page title of FAQ page
    
    cas_faq_style = VALUES { Default, CustomCSS, other.. }
    cas_faq_compatibility = Compatibility mode (ON/OFF)
    cas_faq_show_article_bullets =  Force (UL) bullets (ON/OFF)
    cas_faq_custom_css = Custom CSS script (if enabled)
    casengo-faq-simple-style-editor = Simple CSS editor valuse (if enabled) 
*/

/*
    // CLEAR WP OPTIONS (DEBUG ONLY) **
    
    update_option('cas_widget_domain','');
    update_option('cas_faq_enabled','');
    update_option('cas_faq_searchbar','');
    update_option('cas_faq_toparticlesection','');
    update_option('cas_faq_pagetitle','');
    
    update_option('cas_faq_style','');
    update_option('cas_faq_compatibility','');
    update_option('cas_faq_show_article_bullets','');
    update_option('cas_faq_custom_css','');
*/

function casengo_deactivate_plugin() {
    // remove FAQ page upon plugin deactivation                       
    $current_page_id = casengo_get_faq_page_id();
    if($current_page_id > 0) {
        wp_delete_post($current_page_id, 1);
    }    
}

function casengo_activate_plugin() {
    // work-around to redirect to admin plugin page after plugin activiation
    add_option('casengo_faq_do_activation_redirect', true);
}

function casengo_faq_redirect() {
    // redirect to plugin admin page after plugin activation
    if (get_option('casengo_faq_do_activation_redirect', false)) {
        delete_option('casengo_faq_do_activation_redirect');
        wp_redirect(admin_url('options-general.php?page=casengoFAQSelfservicePlugin&firstload=true'));
    }
}

register_activation_hook( __FILE__, 'casengo_activate_plugin' );
// on plugin initialization, call function to redirect to plugin admin page
add_action('admin_init', 'casengo_faq_redirect');

register_deactivation_hook( __FILE__, 'casengo_deactivate_plugin' );

function casengo_faq_isCurl(){
    // function to check if Curl is enabled (curl is mandatory)
    return function_exists('curl_version');
}

function casengo_add_js() {
    // Register the script like this for a plugin:
	wp_register_script( 'custom-script', plugins_url( '/cas-faq-tpl.js', __FILE__ ) );

	// For either a plugin or a theme, you can then enqueue the script:
	wp_enqueue_script( 'custom-script' );
}

function casengo_get_faq_page_id() {
    global $wpdb;
    $querystr = "
        SELECT $wpdb->posts.* 
        FROM $wpdb->posts WHERE 
        $wpdb->posts.post_excerpt = 'casengo-faq'
        AND ($wpdb->posts.post_status = 'publish') 
    ";
    // AND $wpdb->posts.post_status = 'publish'

    $pageposts = $wpdb->get_results($querystr, OBJECT);
    if($pageposts[0]->ID > 0) {
        return $pageposts[0]->ID; 
    } else {
        return 0;
    }
}

function casengo_faq_update_page($status,$menuname='') {
    
    $current_page_id = casengo_get_faq_page_id();

    if($menuname == '') {
        $menuname = get_option('casengo_faq_pagetitle');
    } else {
        update_option('cas_faq_pagetitle', $menuname);
    }

    if($current_page_id == 0) {
        // add page if its non-existing 
        $my_post = array(
        'post_title'    => $menuname,
        'post_content'    => '- This page is dynamically created by the Casengo FAQ Selfservice Page. Do not edit! -',
        'post_excerpt'    => 'casengo-faq',
        'post_type'    => 'page',
        'post_content'  => '',
        'post_status'   => 'publish',
        'post_author'   => 1
        );
        wp_insert_post( $my_post );
    } else {
        
        if($status == 'disabled') {
            wp_delete_post($current_page_id, 1);
        } else {
            $my_post = array();
            $my_post['ID'] = $current_page_id;
            $my_post['post_title'] = $menuname;
        }
        // Update the post into the database
        wp_update_post( $my_post );        
    }
    // Insert the post into the database
    update_option('cas_faq_enabled', $status);
}

function casengo_faq_template() {
    if(casengo_faq_isCurl) {
        // load js file & template when cURL is found
        
        // first, load the external js file
        add_action( 'wp_enqueue_scripts', 'casengo_add_js' );  
        
        // add the page template file
        add_filter( 'page_template', 'wpa_page_template');

        function wpa_page_template( $page_template ) {
            if ( is_page( get_option('cas_faq_pagetitle')) ) {
                $page_template = dirname( __FILE__ ) . '/cas-faq-template.php';
            }
            return $page_template;
        }
    }
}

function casengo_faq_populate_simple_config_table($values) {
    $textsizes = array("inherit" => "(Default font size)", "10px" => "10px", "12px"  => "12px", "13px" => "13px", "14px" => "14px", "15px" => "15px", "16px" => "16px", "18px" => "18px", "20px" => "20px", "22px" => "22px", "24px" => "24px", "26px" => "26px","28px" => "28px","30px" => "30px","32px" => "32px","34px" => "34px","36px" => "36px","--------" => "", "70%" => "70%", "80%" => "80%", "90%" => "90%", "100%" => "100%" );
    $colors = array( "(Default text color)" => "inherit","red" => "red", "green" => "green", "blue" => "blue", "grey" => "grey", "lightgrey" => "#ddd", "darkgrey" => "#666", "black" => "black", "orange" => "orange", "purple" => "purple", "yellow" => "yellow", "pink" => "pink" );
    $decos = array( "(Default font)" => "inherit","Bold" => "font-weight: bold", "Italic" => "font-style: italic", "Bold Italic" => "font-weight: bold; font-style: italic");
    
    $elements = array(  "main_cat" => array ( "title" => "Main Category", "font-size" => $post_values['main_cat_size'], "color" => $post_values['main_cat_color'], $post_values['main_cat_deco'] ),
                        "sub_cat" => array ( "title" => "Sub Category", "font-size" => $post_values['sub_cat_size'], "color" => $post_values['sub_cat_color'], $post_values['sub_cat_deco'] ),
                        "article_link" => array ( "title" => "Article Text Link", "font-size" => $post_values['article_link_size'], "color" => $post_values['article_link_color'], $post_values['article_link_deco'] ),
                        "article_title" => array ( "title" => "Article Title Header", "font-size" => $post_values['article_title_size'], "color" => $post_values['article_title_color'], $post_values['article_title_deco'] ),
                        "article_body" => array ( "title" => "Article Body Text", "font-size" => $post_values['article_body_size'], "color" => $post_values['article_body_color'], $post_values['article_body_deco'] ),
                        "breadcrumb" => array ( "title" => "Breadcrumb", "font-size" => $post_values['breadcrumb_size'], "color" => $post_values['breadcrumb_color'], $post_values['breadcrumb_deco'] ),
                        "search_keyword" => array ( "title" => "Search Keyword", "font-size" => $post_values['search_keyword_size'], "color" => $post_values['search_keyword_color'], $post_values['search_keyword_deco'] ),
                        "top_articles_header" => array ( "title" => "Top Articles Header", "font-size" => $post_values['top_articles_header_size'], "color" => $post_values['top_articles_header_color'], $post_values['top_articles_header_deco'] ) );
    echo '                                
                <table id="cas-faq-simple-editor-table">
    ';
    foreach ($elements as $key => $element) {
        echo '
                    <tr>
                        <td style="width:200px; text-decoration: underline">
                            ' . $element['title'] . '
                        </td>
                        <td>
                            <select name="' . $key . '_size" value="" style="width:150px">
            ';
        foreach($textsizes as $textsizekey => $textsizevalue) {
            
            $keyval=$key . '_size';
            if($values[$keyval] == $textsizevalue) {
                $selected='selected=true';
            } else {
                $selected="";
            }
            
            echo '<option ' . $selected . ' value="' . $textsizekey . '">' . $textsizevalue . '</option>';
        }
        
        echo '
                            </select>                                                        
                        </td>
                        <td>
                            <select name="' . $key . '_color" value="" style="width:160px; margin-left:16px">
            ';
        foreach($colors as $colorkey => $colorvalue) {
            
            $keyval=$key . '_color';
            if($values[$keyval] == $colorvalue) {
                $selected='selected=true';
            } else {
                $selected="";
            }
            
            echo '<option ' . $selected . ' value="' . $colorvalue . '">' . $colorkey . '</option>';
        }
        
        echo '
                            </select>                                                        
                        </td>
                        <td>
                            <select name="' . $key . '_deco" value="" style="width:160px; margin-left:16px">
           ';
        foreach($decos as $decokey => $decovalue) {
            
            $keyval=$key . '_deco';
            if($values[$keyval] == $decovalue) {
                $selected='selected=true';
            } else {
                $selected="";
            }
            
            echo '<option ' . $selected . ' value="' . $decovalue . '">' . $decokey . '</option>';
        }
        
        echo '
                            </select>                        
                    </tr>
        ';
    }
    echo '
                </table>
    ';                
}

function casengo_faq() {
    $cas_domain = get_option('cas_widget_domain');
 }

add_action( 'template_redirect', 'casengo_faq_template' );
add_action( 'admin_menu', 'casengo_faq_plugin_menu' );

function casengo_faq_plugin_menu() {
    add_options_page( 'Casengo Selfservice FAQ page', 'Casengo FAQ', 'manage_options', 'casengoFAQSelfservicePlugin', 'casengo_faq_settings' );
}

function casengo_faq_settings() {
  if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page!' ) );
	}

    if($_GET['firstload'] == 'true') {
        // upon plugin activation, do this..
        update_option('cas_faq_compatibility','yes');
        update_option('cas_faq_show_article_bullets','yes');
        update_option('cas_faq_searchbar','yes');
        update_option('cas_faq_toparticlesection','yes');
        update_option('cas_faq_pagetitle','Support');
        update_option('cas_faq_style','default');
    }

    // load css variables
    include(dirname( __FILE__ ) . "/cas-faq-defaultcss.php");

      // variables for the field and option names 
    $hidden_field_name = 'cas_submit_hidden';

    // if current_page_id > 0, page is installed
    $current_page_id = casengo_get_faq_page_id();

    //print_r($_POST);

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {

        // always update style (reset button or normal settings change)
        update_option( 'cas_faq_style', $_POST['cas_faq_style']);
        
        if($_POST['resetcss'] === 'yes') {
            // reset css button clicked
            update_option( 'cas_faq_custom_css', $cas_faq_default_css);
            
        } else {
            // submit button clicked
            if($_POST['cas_faq_style'] == 'custom-css') {
                update_option( 'cas_faq_custom_css', $_POST['cas_faq_custom_css']);
            }                          

            update_option('cas_widget_domain',$_POST['cas_widget_domain']);
            update_option('cas_faq_searchbar', $_POST['cas_faq_searchbar']);
            update_option('cas_faq_toparticlesection', $_POST['cas_faq_toparticlesection']);
            update_option('cas_faq_compatibility', $_POST['cas_faq_compatibility']);
            update_option('cas_faq_show_article_bullets', $_POST['cas_faq_show_article_bullets']);
            update_option('cas_faq_enabled', $_POST['cas_faq_enabled']);
            update_option('cas_faq_pagetitle', $_POST['cas_faq_pagetitle']);

            // If customer enters empty name, use default
            if($_POST['cas_faq_pagetitle'] == '') {
                $menuname='Support';
            } else {
                $menuname=stripslashes($_POST['cas_faq_pagetitle']);
            }

           if($_POST['cas_faq_enabled'] == 'disabled') {
                // enabled checkbox is not checked                       
                casengo_faq_update_page('disabled', $_POST['cas_faq_pagetitle']);
            } elseif ($_POST['cas_faq_enabled'] == 'private' ) {
                casengo_faq_update_page('private', $_POST['cas_faq_pagetitle']);
            } elseif ($_POST['cas_faq_enabled'] == 'public' ) {
            
                casengo_faq_update_page('public', $_POST['cas_faq_pagetitle']);
            }

            // store simple style editor values with update_option
            $my_array = Array(  'main_cat_size' => $_POST['main_cat_size'],
                                'main_cat_color' => $_POST['main_cat_color'],
                                'main_cat_deco' => $_POST['main_cat_deco'],
                                'sub_cat_size' => $_POST['sub_cat_size'],
                                'sub_cat_color' => $_POST['sub_cat_color'],
                                'sub_cat_deco' => $_POST['sub_cat_deco'],
                                'article_link_size' => $_POST['article_link_size'],
                                'article_link_color' => $_POST['article_link_color'],
                                'article_link_deco' => $_POST['article_link_deco'],
                                'article_title_size' => $_POST['article_title_size'],
                                'article_title_color' => $_POST['article_title_color'],
                                'article_title_deco' => $_POST['article_title_deco'],
                                'article_body_size' => $_POST['article_body_size'],
                                'article_body_color' => $_POST['article_body_color'],
                                'article_body_deco' => $_POST['article_body_deco'],
                                'breadcrumb_size' => $_POST['breadcrumb_size'],
                                'breadcrumb_color' => $_POST['breadcrumb_color'],
                                'breadcrumb_deco' => $_POST['breadcrumb_deco'],
                                'search_keyword_size' => $_POST['search_keyword_size'],
                                'search_keyword_color' => $_POST['search_keyword_color'],
                                'search_keyword_deco' => $_POST['search_keyword_deco'],
                                'top_articles_header_size' => $_POST['top_articles_header_size'],
                                'top_articles_header_color' => $_POST['top_articles_header_color'],
                                'top_articles_header_deco' => $_POST['top_articles_header_deco'] );

            
            update_option('casengo-faq-simple-style-editor', $my_array);
            // enabled is ON

            // value of enabled in the global variables is disabled
            // Create post object then
            // make sure it does not exist yet

            // if page not already exist with same name
            
            /*
            if($current_page_id == 0) {
                casengo_faq_update_page('publish');
            } else {
                $my_post = array();
                $my_post['ID'] = $current_page_id;
                $my_post['post_title'] = $menuname;

                // Update the post into the database
                wp_update_post( $my_post );
            }
            */
            
        }

        // Put an settings updated message on the screen
?>

<div class="updated"><p><?php _e('Settings saved. <strong><a href="' . get_site_url() . '">Visit your site</a></strong> to see the changes.', 'menu-general' ); ?></p></div>
<?php

    }

    // Now display the settings editing screen
    echo '<div class="wrap">';

    // header
    echo "<h2>" . __( 'Casengo FAQ Selfservice Plugin', 'menu-general' ) . "</h2>";

    // settings form
  
    ?>

    <?php
      // Read in existing option value from database
      $opt_val = get_option( 'cas_faq_style' );
      $cas_faq_enabled = get_option( 'cas_faq_enabled' );
    ?>

<script type="text/javascript">
    function cas_faq_validate_form() {
        var subdomainname=document.forms['form1'].cas_widget_domain.value;
        var menuname=document.forms['form1'].cas_faq_pagetitle.value;
    
        var re = /^[a-zA-Z0-9]+(-[a-zA-Z0-9]+)*$/;
        
        if(re.test(subdomainname) != true) {
        //if (subdomainname==null || subdomainname=="") {
            alert("Enter a valid casengo subdomain name.");
            document.forms[frm]['cas_widget_domain'].focus();
            return false;
        }
        if(menuname == null || menuname == '') {
            alert("Enter a page title.");
            document.forms['form1']['cas_faq_pagetitle'].focus();
            return false;
        }
  return true;
  }
</script>

<form id="form1" name="form1" method="post" action="" onsubmit="return cas_faq_validate_form();">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<script type="text/javascript">
    function cas_faq_styleChange(styleval) {
        if(styleval == 'custom-css') {
            document.getElementById('cas-faq-customcss').disabled = false;
            document.getElementById('cas-faq-customcss').style.color = '#000';
            document.getElementById('cas-faq-customcss').style.display = 'block';
            document.getElementById('cas-faq-resetcssbutton').disabled = false;
            document.getElementById('cas-faq-customcss-row').style.display = 'table-row';
            document.getElementById('cas-faq-simple-editor-table').style.display = 'none';
            
        } else if(styleval == 'simple-editor') {
            document.getElementById('cas-faq-customcss').disabled = true;
            document.getElementById('cas-faq-customcss').style.color = '#bbb';
            document.getElementById('cas-faq-customcss').style.display = 'none';
            document.getElementById('cas-faq-resetcssbutton').disabled = true;
            document.getElementById('cas-faq-customcss-row').style.display = 'none';
            document.getElementById('cas-faq-simple-editor-table').style.display = 'block';
            
        } else {
            document.getElementById('cas-faq-customcss').disabled = true;
            document.getElementById('cas-faq-customcss').style.color = '#bbb';
            document.getElementById('cas-faq-customcss').style.display = 'block';
            document.getElementById('cas-faq-customcss-row').style.display = 'table-row';
            document.getElementById('cas-faq-resetcssbutton').disabled = true;
            document.getElementById('cas-faq-simple-editor-table').style.display = 'none';
        }
    }
    
    function resetCss() {
        document.getElementById('resetcss').value = 'yes';
        document.getElementById('form1').submit();
    }
</script>

<?php
    if(!casengo_faq_isCurl) {
        // Curl is not installed
        echo '<p><div class="error"><strong>The plugin is deactivated!</strong><br><br>The cURL module is not installed in your PHP configuration.<br>For more information on how to to this, <a href="http://www.tomjepson.co.uk/enabling-curl-in-php-php-ini-wamp-xamp-ubuntu/">click here</a>.</div></p>';
    } else {
?>

    <!-- *** CASENGO ID SECTION *** -->

    <p>To add Casengo's FAQ /Selfservice/page to your WordPress site, you must have a Casengo account. Have an account already? Great! If not, <a href="http://get.casengo.com/signup/?ref=wordpress-plugin-faq-admin&amp;utm_source=WordPress&amp;utm_medium=Plugin&amp;utm_campaign=WordPress%2BPlugin%2BSignups" target="_blank" title="Sign up for a free Casengo account" rel="nofollow">sign up here</a>.</p>
    <p>
        <ul style="margin-left:40px; font-size:18px">
            <li><span style="font-size:18px">Follow the instructions below to install the FAQ page on your site:<br/></span>
            <li>--------------------------------------------------------------------------------------</li>
            <li>Step 1. <a href="http://get.casengo.com/signup/?ref=wordpress-plugin-faq-admin&amp;utm_source=WordPress&amp;utm_medium=Plugin&amp;utm_campaign=WordPress%2BPlugin%2BSignups" target="_blank" title="Sign up for a free Casengo account" rel="nofollow">Create a Casengo account for free</a></li>
            <li>Step 2. Fill in your unique Casengo Username below</li>
            <li>Step 3. Enter a page title</li>
            <li>Step 4. Press 'Save Changes' to commit new settings</li>
            <li>Step 5. <a href="<?php echo get_permalink($current_page_id); ?>" target="previewFAQpage">Preview the FAQ page</a> on your WordPress site </li>
            <li>Step 6. <a href="http://login.casengo.com/admin/#!/kb/categories">Click here</a> to add categories and articles via the Casengo admin site</li>
        </ul>
    </p>
    <p>
        We tried our best to make it look as good as possible in every WordPress theme. But if you encounter layout problems you can make changes in the advanced settings section.
    </p>
    <p>
        Have you tried the free <a href="http://wordpress.org/plugins/the-casengo-chat-widget/">Casengo Chat widget plugin</a>? It allows you to add chat/contact form functionality to your website.
    </p>
    <br>
    <hr>
    <br>
    <p><h3><strong><?php _e("General", 'menu-test' ); ?></h3></strong>
    Enter your unique Casengo username below. This field is mandatory. If it is not specified, the FAQ page will not be visible on your WordPress site.<br><br>
    <table style="margin-left:20px">
        <tr>
            <td style="width:220px">FAQ page status</td>
            <td>
                <select name="cas_faq_enabled" style="width:160px" value="">
                    <option <?php if ($cas_faq_enabled == 'disabled') echo 'selected="true"' ?> value="disabled">Disabled</option>
                    <option <?php if ($cas_faq_enabled == 'public') echo 'selected="true"' ?> value="public">Publicly visible</option>
                </select>
            </td>
        </tr>
        <tr>
            <td style="width:220px">Casengo Username (subdomain)</td>
            <td>
                http://<input type="text" name="cas_widget_domain" size="20" maxlength="64" style="font-weight: bold" value="<?php echo get_option('cas_widget_domain') ?>">.casengo.com
            </td>
        </tr>
        <tr><td><br/></td></tr>
    </table>

    <!-- *** SETTINGS *** -->
    
    <p><h3><strong><?php _e("Settings", 'menu-test' ); ?></strong></h3>
    <table style="margin-left:20px">
        <tr>
            <td style="width:220px">Page (menu) Title</td>
            <td>
                <input type="text" name="cas_faq_pagetitle" size="30" maxlength="64" value="<?php echo get_option('cas_faq_pagetitle') ?>">
            </td>
        </tr>
        <tr>
            <td style="width:220px">Show search bar<br></td>
            <td>
                <input <?php if(get_option('cas_faq_searchbar') == 'yes') echo 'CHECKED="true"'; ?> type="checkbox" id="cas_faq_searchbar" name="cas_faq_searchbar" size="30" value="yes">
            </td>
        </tr>
        <tr>
            <td style="width:220px">Show top article section<br></td>
            <td>
                <input <?php if(get_option('cas_faq_toparticlesection') == 'yes') echo 'CHECKED="true"'; ?> type="checkbox" id="cas_faq_toparticlesection" name="cas_faq_toparticlesection" size="30" value="yes">
            </td>
        </tr>
    </table>
    <br/>
    
    <!-- *** ADVANCED SETTINGS *** -->
    
    <p><h3><strong><?php _e("Advanced", 'menu-test' ); ?></strong></h3>

    <table style="margin-left:20px">
       <tr>
            <td style="width:220px">Style:</td>
            <td>
                <select name="<?php echo 'cas_faq_style'; ?>" style="width:200px;margin-bottom:12px" value="" onchange="cas_faq_styleChange(this.value)">
                    <option <?php if ($opt_val === 'default') echo 'selected="true"' ?> value="default">Default style</option>
                    <option <?php if ($opt_val === 'simple-editor') echo 'selected="true"' ?> value="simple-editor">Simple editor</option>
                    <option <?php if ($opt_val === 'custom-css') echo 'selected="true"' ?> value="custom-css">Customize CSS</option>
                </select>
            </td>
        </tr>
        <tr>
            <td style="width:220px"></td>
            <td>
                <?php casengo_faq_populate_simple_config_table(get_option('casengo-faq-simple-style-editor')); ?>
            </td>
        </tr>
        <tr id="cas-faq-customcss-row">
            <td style="width:220px;">Use Custom CSS</td>
            <td>
                <textarea id="cas-faq-customcss" name="cas_faq_custom_css" style="font-family: Courier, Arial;width:650px; height:200px; word-break: break-all;" value="">
                    <?php 
                        $cas_faq_custom_css = get_option('cas_faq_custom_css'); 
                        if($cas_faq_custom_css == '') {
                            // from include file (default-css.php)
                            echo $cas_faq_default_css;
                        } else {
                            echo $cas_faq_custom_css;
                        }
                    ?>
                </textarea>
                <br>
                <input id="cas-faq-resetcssbutton" type="button" class="button" onclick="resetCss()" value="Restore to Original CSS">
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td><td>&nbsp;</td>
        </tr>
        <tr>
            <td style="width:220px">Compatibility mode</td>
            <td>
                <input <?php if(get_option('cas_faq_compatibility') == 'yes') echo 'CHECKED="true"'; ?> type="checkbox" id="cas_faq_compatibility" name="cas_faq_compatibility" size="30" value="yes">&nbsp;Try unchecking the button if the page looks messed up. When enabled, the following style will be added: <span style="font-family:Courier">#primary { width: auto }</span>
            </td>
        </tr>
        <tr>
            <td style="width:220px">Display article Bullets</td>
            <td>
                <input <?php if(get_option('cas_faq_show_article_bullets') == 'yes') echo 'CHECKED="true"'; ?> type="checkbox" id="cas_faq_show_article_bullets" name="cas_faq_show_article_bullets" size="30" value="yes">
            </td>
        </tr>

    </table>

    <br>
    <hr>
    <p class="submit">
    
    <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
    <input id="cas-faq-viewfaqpage" type="button" class="button" onclick="window.open('<?php echo get_permalink($current_page_id); ?>','previewFAQpage');" value="View FAQ Page"> <a href="<?php echo get_permalink($current_page_id); ?>">
    </p>
    <input type="hidden" id="resetcss" name="resetcss">
    <script type="text/javascript">
        try {
            cas_faq_styleChange('<?php echo $opt_val; ?>');
            } catch (err) { }
    </script>

<?php } ?>
</form>

<?php	
	echo '</div>';
}
?>