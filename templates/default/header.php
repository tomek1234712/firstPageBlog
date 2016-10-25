<?php


// More about design modifications - www.opensolution.org/docs/
if( !defined( 'CUSTOMER_PAGE' ) )
  exit;
?>
<!DOCTYPE HTML>
<html lang="<?php echo $config['language']; ?>">
<head>
  <title><?php echo $config['title']; ?></title>
  <meta name="description" content="<?php echo $config['description']; ?>" />
  <meta name="generator" content="Quick.Cms.Ext v<?php echo $config['version']; ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <?php
  if( isset( $aData['iMetaRobots'] ) && $aData['iMetaRobots'] > 0 && isset( $config['meta_robots_options'][$aData['iMetaRobots']] ) ){?>
  <meta name="robots" content="<?php echo $config['meta_robots_options'][$aData['iMetaRobots']][1]; ?>" />
  <?php } ?>
  <!-- <link rel="stylesheet" href="templates/<?php echo $config['skin']; ?>/style.css" /> -->
  <link rel="stylesheet" href="templates/<?php echo $config['skin']; ?>/style-sliders.css" />
  <!-- <link rel="stylesheet" href="templates/<?php echo $config['skin']; ?>/style-widgets.css" /> -->


  <link rel="stylesheet" href="templates/<?php echo $config['skin']; ?>/main.css" />

<!-- IE HACKS -->
<!-- pozycja strzalek slidera, pozycja preloadera, pozycja podmenu wysuwanego -->
            <link rel="stylesheet" type="text/css" href="templates/<?php echo $config['skin']; ?>/style/ie/wszystkie-ie.css" />

            <link rel="stylesheet" href="templates/<?php echo $config['skin']; ?>/style/ie/ie10-i-mniej.css" />
            <script src="plugins/html5shiv.js"></script>

            <!--[if lt IE 10]>
            <link rel="stylesheet" href="templates/<?php echo $config['skin']; ?>/style/ie/ie9-i-mniej.css" />
            <script src="plugins/html5shiv.js"></script>
            <![endif]-->

            <!--[if lt IE 9]>
            <link rel="stylesheet" href="templates/<?php echo $config['skin']; ?>/style/ie/ie8-i-mniej.css" />
            <script src="plugins/html5shiv.js"></script>
            <![endif]-->





<!-- ignoruj plugin skype -->
<meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />


<!--favikona-->
<link rel="icon" type="image/png" href="templates/<?php echo $config['skin']; ?>/img/fav.png">


<!-- biblioteki -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.js"></script>  -->

<!-- mmenu -->

<!-- 
   <script src="plugins/jQuery.mmenu-master/dist/core/js/jquery.mmenu.min.all.js" type="text/javascript"></script>
   <link href="plugins/jQuery.mmenu-master/dist/core/css/jquery.mmenu.all.css" type="text/css" rel="stylesheet" /> -->

<!-- hammer eventy touchowe -->


<!--    <script src="plugins/hammer.min.js" type="text/javascript"></script> -->



<script src="plugins/imagesloaded.js" type="text/javascript"></script>
<script src="plugins/jquery.matchHeight-min.js" type="text/javascript"></script>
<script src="plugins/jquery.form.js" type="text/javascript"></script>


<!-- zmienne php do js -->

<script>
<?php

echo 'var news_ok = "'.$lang['news_ok'].'";';
echo 'var news_empty = "'.$lang['news_empty'].'";';
echo 'var news_error = "'.$lang['news_error'].'";';

echo 'var news_start = "'.$lang['Newsletter_add'].'";';



echo 'var news_alt_error = "'.$lang['news_alt_error'].'";';
echo 'var news_alt_ok = "'.$lang['news_alt_ok'].'";';
echo 'var news_alt_empty = "'.$lang['news_alt_empty'].'";';
echo 'var news_alt_start = "'.$lang['news_alt_start'].'";';
echo 'var news_alt_progress = "'.$lang['news_alt_progress'].'";';



?>
</script>

<!-- moje skrypty -->

<script src="plugins/skrypty.js" type="text/javascript"></script>


  <script src="core/common.js"></script>
  <?php if( isset( $config['enabled_sliders'] ) ){ ?><script src="core/libraries/quick.slider.js"></script><?php } ?>
  <script src="core/libraries/quick.box.js"></script>
  <script>$(function(){ backToTopInit(); });</script>


  
</head>

<!-- //klasa usun - fix na błąd z tagami -->

<body<?php if( isset( $aData['iPage'] ) && is_numeric( $aData['iPage'] ) ) echo ' id="page'.$aData['iPage'].'"'; ?> class="<?php echo $config['language'];?> 

<?php if( $aData['sName'] == '#personal development' ){echo 'usun';}?>

">


<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/pl_PL/sdk.js#xfbml=1&version=v2.5";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>


<nav id="skiplinks">
  <ul>
    <li><a href="#head2"><?php echo $lang['Skip_to_main_menu']; ?></a></li>
    <li><a href="#content"><?php echo $lang['Skip_to_content']; ?></a></li>
  <?php 
    if( isset( $config['page_search'] ) && is_numeric( $config['page_search'] ) && isset( $oPage->aPages[$config['page_search']] ) ){ ?>
  <li><a href="#search"><?php echo $lang['Skip_to_search']; ?></a></li>
  <?php 
    }
    if( isset( $config['page_sitemap'] ) && is_numeric( $config['page_sitemap'] ) && isset( $oPage->aPages[$config['page_sitemap']] ) ){ ?>
  <li><a href="<?php echo $oPage->aPages[$config['page_sitemap']]['sLinkName']; ?>#page"><?php echo $lang['Skip_to_sitemap']; ?></a></li>
  <?php } ?>
  </ul>
</nav>
<?php if( isset( $config['enabled_widgets'] ) ) echo $oWidget->listWidgets( Array( 'iType' => 0, 'bDontDisplayErrors' => true ) ); ?>



<div id="fb-likebox">


<div class="fb-page" data-href="https://www.facebook.com/TomekSzczepaniak.pl.555/" data-width="280" data-small-header="false" data-adapt-container-width="false" data-hide-cover="false" data-show-facepile="true" data-show-posts="false"><div class="fb-xfbml-parse-ignore"><blockquote cite="https://www.facebook.com/TomekSzczepaniak.pl.555/"><a href="https://www.facebook.com/TomekSzczepaniak.pl.555/">TomekSzczepaniak.pl</a></blockquote></div></div>

</div>



<!-- menu mobilne -->

<nav id="my-menu" class="ukryj">
   <ul>
      <li><a href="/">Home</a></li>
      <li><a href="/about/">About us</a></li>
      <li><a href="/contact/">Contact</a></li>
   </ul>
</nav>


<div id="container">
  <section id="header">
    <div class="wrapper">
    <div class="zawartosc">
        <div id="header_boki">
              <div id="header_lewo">

                  <div id="logo">
                  <!-- logosik -->
                  <a href="./"><?php echo $config['logo']; ?></a>
                  </div>

              </div>

              <div id="header_prawo">


  <?php
          if( isset( $config['page_search'] ) && is_numeric( $config['page_search'] ) && isset( $oPage->aPages[$config['page_search']] ) ){
        ?>
<!--         <div id="szukajka">
        <a id="search" tabindex="-1"></a>
        <form method="post" action="<?php echo $oPage->aPages[$config['page_search']]['sLinkName']; ?>" id="search-form">
          <fieldset>
            <input type="text" name="sSearch" id="sSearch" value="<?php if( isset( $_GET['sSearch'] ) ) echo $_GET['sSearch']; ?>" maxlength="100" placeholder="<?php echo $config['szukajka']; ?>"/>


            <button type="submit" title="<?php echo $lang['search']; ?>"><img src="templates/default/img/search.png" alt="szukaj"></button>


          </fieldset>
        </form>
        </div> -->
        <?php
          }
        ?>



<div id="top_menu">


<!-- nowa szukajka -->
        <form method="post" action="<?php echo $oPage->aPages[$config['page_search']]['sLinkName']; ?>" id="wysuwana_szukajka">
         
            <input type="text" name="sSearch" id="sSearch" value="<?php if( isset( $_GET['sSearch'] ) ) echo $_GET['sSearch']; ?>" maxlength="100" placeholder="<?php echo $config['szukajka']; ?>"/>

        </form>

<!-- nowa szukajka koniec -->

<div id="spolecznosciowe">
               <?php if( isset( $config['enabled_widgets'] ) ) echo $oWidget->listWidgets( Array( 'iType' => 1, 'bDontDisplayErrors' => true ) ); ?>


               </div>
        <?php echo $oPage->listPagesMenu( 1, Array( 'iDepthLimit' => 0 ) ); // content of top menu ?>
</div>
 
              </div>
        </div>
<div id="header_calosc">
<!-- menu cala szerokosc -->
 <?php if( isset( $config['enabled_widgets'] ) ) echo $oWidget->listWidgets( Array( 'iType' => 2, 'bDontDisplayErrors' => true ) ); ?>
              

</div>

  
</div>
</div>
</section>

<section id="slider">
   <div class="wrapper">
    <div class="zawartosc">

<?php 
// print_r($aData);
// if(  (isset($config['current_tag_id'])&&$config['current_tag_id']=='1')  || strpos(listPageTags( $aData['iPage'] ),'sport') !== false ){
if(  strpos(listPageTags( $aData['iPage'] ),'sport') !== false ){
  echo '<img src="templates/default/img/slider1.jpg">';
}
else if(  strpos(listPageTags( $aData['iPage'] ),'podróże') !== false ){
  echo '<img src="templates/default/img/slider2.jpg">';
}
else if(  strpos(listPageTags( $aData['iPage'] ),'rozwój') !== false ){
  echo '<img src="templates/default/img/slider3.jpg">';
}


else{




if( isset( $config['enabled_sliders'] ) ) echo $oSlider->listSliders( Array( 'iType' => 1 ) ); 


}
?>

<?php 
// if( isset( $config['enabled_widgets'] ) ) echo $oWidget->listWidgets( Array( 'iType' => 7, 'bDontDisplayErrors' => true ) ); 

?>

<div class="menu-2">
<?php 

echo '<ul>
<li id="sport"><a href="sport.html"><img src="templates/default/img/sport.png"><span>'.$lang['menu_sport'].'</span></a></li>
<li id="podroze"><a href="podroze.html"><img src="templates/default/img/podroze.png"><span>'.$lang['menu_travel'].'</span></a></li>
<li id="rozwoj"><a href="rozwoj.html"><img src="templates/default/img/rozwoj.png"><span>'.$lang['menu_personal'].'</span></a></li>
</ul>';


?>

</div>




    </div>
    </div>
</section>
