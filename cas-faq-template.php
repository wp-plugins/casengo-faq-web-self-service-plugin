<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

  function casengo_faq_head() {
    // todo: load from external CSS file instead of inline css block
    include(dirname( __FILE__ ) . "/cas-faq-defaultcss.php");
    // output base css
                                                
    echo '<style>' . $cas_faq_base_css . '</style>';
    
    if(get_option('cas_faq_searchbar') != 'yes') {
        // hide the search bar
        echo '<style>' . $cas_faq_searchbar . '</style>';
    }

    if(get_option('cas_faq_toparticlesection') == 'yes') {
        echo '<style>' . $cas_faq_toparticlesection_show . '</style>';
    } else {
        echo '<style>' . $cas_faq_toparticlesection_hide . '</style>';    
    }

    if(get_option('cas_faq_compatibility') == 'yes') {
        echo '<style>' . $cas_faq_compatibility_mode . '</style>';
    }

    if(get_option('cas_faq_show_article_bullets') == 'yes') {
        echo '<style>' . $cas_faq_show_article_bullets . '</style>';
    } else {
        echo '<style>' . $cas_faq_hide_article_bullets . '</style>';
    }
    
    if(get_option('cas_faq_style') == 'default' ) {
        // default          
        echo '<style>';        
        echo $cas_faq_default_css;
        echo '</style>';
    } else if(get_option('cas_faq_style') == 'simple-editor' ) {
        echo '<style>';
        echo $cas_faq_default_css;
        echo $cas_faq_simple_css;
        
        $arr = get_option('casengo-faq-simple-style-editor');
        
        $output  = '.category h1 a, .searchresult .searchtitle { ';
        if ($arr['main_cat_size'] != 'inherit') $output .= 'font-size: ' . $arr['main_cat_size'] . ';';
        if ($arr['main_cat_color'] != 'inherit') $output .= 'color: ' . $arr['main_cat_color'] . ';';
        if ($arr['main_cat_deco'] != 'inherit') $output .= $arr['main_cat_deco'] . ';';
        $output .= '}';

        $output .= '.boxitem-inner h2, .articleitem h2 a, .searchresult a { ';
        if ($arr['sub_cat_size'] != 'inherit') $output .= 'font-size: ' . $arr['sub_cat_size'] . ';';
        if ($arr['sub_cat_color'] != 'inherit') $output .= 'color: ' . $arr['sub_cat_color'] . ';';
        if ($arr['sub_cat_deco'] != 'inherit') $output .= $arr['sub_cat_deco'] . ';';
        $output .= '}';

        $output .= '.articleitem a, .boxitem-inner p, .boxitem-inner span, .boxitem-inner a { ';
        if ($arr['article_link_size'] != 'inherit') $output .= 'font-size: ' . $arr['article_link_size'] . ';';
        if ($arr['article_link_color'] != 'inherit') $output .= 'color: ' . $arr['article_link_color'] . ';';
        if ($arr['article_link_deco'] != 'inherit') $output .= $arr['article_link_deco'] . ';';
        $output .= '}';

        $output .= '.articletitle { ';
        if ($arr['article_title_size'] != 'inherit') $output .= 'font-size: ' . $arr['article_title_size'] . ';';
        if ($arr['article_title_color'] != 'inherit') $output .= 'color: ' . $arr['article_title_color'] . ';';
        if ($arr['article_title_deco'] != 'inherit') $output .= $arr['article_title_deco'] . ';';
        $output .= '}';

        $output .= '.articlebody { ';
        if ($arr['article_body_size'] != 'inherit') $output .= 'font-size: ' . $arr['article_body_size'] . '; line-height: ' . $arr['article_body_size'] . ';';
        if ($arr['article_body_color'] != 'inherit') $output .= 'color: ' . $arr['article_body_color'] . ';';
        if ($arr['article_body_deco'] != 'inherit') $output .= $arr['article_body_deco'] . ';';
        $output .= '}';

        $output .= '.breadcrumb { ';
        if ($arr['breadcrumb_size'] != 'inherit') $output .= 'font-size: ' . $arr['breadcrumb_size'] . ';';
        if ($arr['breadcrumb_color'] != 'inherit') $output .= 'color: ' . $arr['breadcrumb_color'] . ';';
        if ($arr['breadcrumb_deco'] != 'inherit') $output .= $arr['breadcrumb_deco'] . ';';
        $output .= '}';

        $output .= '#search-form #keyword, .searchbox #keyword-mini { ';
        if ($arr['search_keyword_size'] != 'inherit') $output .= 'font-size: ' . $arr['search_keyword_size'] . ';';
        if ($arr['search_keyword_color'] != 'inherit') $output .= 'color: ' . $arr['search_keyword_color'] . ';';
        if ($arr['search_keyword_deco'] != 'inherit') $output .= $arr['search_keyword_deco'] . ';';
        $output .= '}';

        $output .= '.boxitem-inner .lbl { ';
        if ($arr['top_articles_header_size'] != 'inherit') $output .= 'font-size: ' . $arr['top_articles_header_size'] . ';';
        if ($arr['top_articles_header_color'] != 'inherit') $output .= 'color: ' . $arr['top_articles_header_color'] . ';';
        if ($arr['top_articles_header_deco'] != 'inherit') $output .= $arr['top_articles_header_deco'] . ';';
        $output .= '}';

        echo $output . '</style>';
    } else {
        // custom css
        echo '<style>';
        echo get_option('cas_faq_custom_css');                
        echo '</style>';

    }
}

add_action('wp_head', 'casengo_faq_head');

get_header(); ?>

		<div id="primary" class="site-content row clearfix">
	
			<div id="content" role="main">
			<div class="container">
			<div class="entry">

<?php
    // curl needed to load
    // if url contains id, page variables (article/category pages), add querystring to fetch url
           
        // first determine if default permalink is used for faq page (?page_id=123)
    if(isset($_GET['page_id'])) {
        $permalink_default = true;        
    } else {
        $permalink_default = false;
    }
    
    $querystring = "?embed=wp-v1&";

    if(isset($_GET['page'])) {
        $querystring .= "page=" . $_GET['page'];    
    }

    if(isset($_GET['id'])) {
        $querystring .= "&id=" . $_GET['id'];
    }
    
    // if url contains keyword search, add it to query string as well
    if(isset($_GET['keyword'])) {
        $querystring .= "&keyword=" . urlencode($_GET['keyword']);    
    }
    
    $url = "http://" . get_option('cas_widget_domain'). ".casengo.com/support" . $querystring;

    // load support portal via cURL (= deprecated
    // $curl = curl_init($url);
    // curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    // $response = curl_exec($curl);

    // load support portal via PHP's built-in command: file_get_contents (removes dependency on cURL and is faster too)
    $response = file_get_contents($url);

     // process output
     // get the body content only
     preg_match("~<body[^>]*>(.*?)</body>~si", $response, $output);
     
     // remove reference to load external script
     $output[1] = str_replace("<script src=\"support/tpl.js\"></script>","",$output[1]);
     
     if($permalink_default) {
        // if url is using WP's default permalink setting: ?page_id=123
        // add page_id to the querystring of every link 
        $output[1] = str_replace("href=\"?","href=\"?page_id=" . $_GET['page_id'] . "&",$output[1]);
        // transform search form to include page_id as a hidden form object
        $output[1] = str_replace("<input type=\"hidden\" id=\"page\" name=\"page\" value=\"search\">","<input type=\"hidden\" id=\"page\" name=\"page\" value=\"search\"><input type=\"hidden\" id=\"page_id\" name=\"page_id\" value=\"" . $_GET['page_id'] . "\">",$output[1]);
     }
     
     echo $output[1] . '<br/>';
    //curl_close($curl);
?>
			</div>
            </div><!-- #content -->
			
		  
		</div><!-- #primary -->

<?php get_footer(); ?>