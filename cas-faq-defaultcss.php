<?php
$cas_faq_base_css='
/*********************/
/*** BASE CSS V1.0 ***/
/*********************/

.header img { display:none }
.searchbox { display:block }
.askbutton { display:none }
.article { overflow:hidden }
.entry .footer { display:none }
.viewall { display:none }
.articleescalate { display:none }
.header h1 { display:none }
';

$cas_faq_searchbar='
/******************/
/*** SEARCH BAR ***/
/******************/

#casengofaq-toparticle-article .boxitem:nth-child(2) { display:none }
.searchbox { display: none }
';

$cas_faq_toparticlesection_show='
#casengofaq-toparticle-main .boxitem { display: block; width: 49%;float: left; margin-right: 5px }
#casengofaq-toparticle-article .boxitem { margin-left: 18px }
';

$cas_faq_toparticlesection_hide='
.boxitem { display: none }
';

$cas_faq_show_article_bullets='
.article ul, .boxitem ul { list-style: square outside; }
';

$cas_faq_hide_article_bullets='
.article ul, .boxitem ul { list-style: none; }
';

$cas_faq_compatibility_mode='
/**************************/
/*** COMPATIBILITY MODE ***/
/**************************/

#primary { width: auto; margin: 0 }

';

$cas_faq_default_css='
/**********************/
/*** GENERIC STYLES ***/
/**********************/

#content { width: auto }
#main { margin-top: 20px !important }
.wrapper { margin:12px auto }
.overview { margin-left: 16px; margin-right: 16px; margin-top: 10px }
.articlefooter a, .articledate { display:none }
.articleitem { float:left; width:49%; margin-right:5px }
.category { margin-bottom: 10px }
.breadcrumb { margin-left: 0px; background-color: inherit; margin-right: 16px; margin-bottom: 28px }
.boxitem ul li span { margin-right: 8px; text-transform: uppercase; font-size: 75% }
#casengofaq-toparticle-main .topbox { margin-left: 16px; }
    
/***************************************/
/******** HOME / CATEGORY VIEWS ********/
/***************************************/

.category h1 { font-weight: bold; font-size: 36px; margin-bottom:20px; }
.article { margin-bottom: 20px }
.articleitem h2 a, .boxitem-inner .lbl { font-weight: bold }
.articleitem h2 { margin-bottom: 20px }

/***************************/
/*** ARTICLE DETAIL PAGE ***/
/***************************/

.articledetail { margin-left: 18px }
/* .articledate { color: #aaa; font-size: 11px; padding-bottom: 10px } */
.articletitle { font-weight: bold; margin-bottom:20px }
.articlefooter { font-size: 11px; margin: 25px 0px 30px }
.articlebody { line-height: 22px }

/******************/
/*** SEARCH BAR ***/
/******************/

.searchbox { margin-left: 0px; margin-right: 0px; margin-top: 10px; margin-bottom: 20px }
.searchtitle { font-size: 16px; margin-left: 0px; font-weight: bold }
.searchresult { font-size: 12px }
.searchresult h2 { font-size: 22px; margin-bottom: 2px !important }
.searchresult .meta { font-size: 11px; color: #bbb }
.searchresult p { font-size: 13px }
.searchbox-inner input { background: transparent; color: #888; border: 0; padding: 5px; margin: 0; width: 70%; font-size: 26px }
.searchbox-inner button {  display: none }
';

$cas_faq_simple_css='
/***************************/
/*** SIMPLE STYLE EDITOR ***/
/***************************/
';



?>
