<?php
#$fStartTime = microtime( true );

/*
* Quick.Cms.Ext by OpenSolution.org
* www.OpenSolution.org
*/
define( 'CUSTOMER_PAGE', true );
require_once 'database/config.php';

$_SERVER['REQUEST_URI'] = htmlspecialchars( strip_tags( $_SERVER['REQUEST_URI'] ) );
if( isset( $_POST['sSearch'] ) ){
  header( 'Location: '.$_SERVER['REQUEST_URI'].'?sSearch='.urlencode( $_POST['sSearch'] ) );
  exit;
}
$_GET['sSearch'] = ( ( isset( $_GET['sSearch'] ) && !empty( $_GET['sSearch'] ) ) ? trim( htmlspecialchars( stripslashes( strip_tags( urldecode( $_GET['sSearch'] ) ) ) ) ) : null );

if( isset( $config['display_hidden_pages'] ) || isset( $config['display_editing_options'] ) || isset( $config['enabled_languages'] ) ){
  session_start( );
}

header( 'Content-Type: text/html; charset=utf-8' );
require_once 'core/libraries/file-jobs.php';
require_once 'core/libraries/trash.php';
require_once 'core/libraries/sql.php';
$oSql = Sql::getInstance( );

require_once 'core/common.php';
getBinValues( );

if( isset( $config['enabled_languages'] ) && !isset( $config['enabled_languages'][$config['language']] ) && !verifyAdminSession( ) ){
  header( 'Location: ./?sLanguage='.$config['default_language'] );
  exit;
}

require_once 'templates/'.$config['skin'].'/_lists.php';
require_once 'core/pages.php';
$oPage = Pages::getInstance( );

require_once 'core/files.php';
$oFile = Files::getInstance( );

if( isset( $config['enabled_widgets'] ) ){
  require_once 'core/widgets.php';
  $oWidget = Widgets::getInstance( );
}

if( isset( $config['enabled_sliders'] ) ){
  require_once 'core/sliders.php';
  $oSlider = Sliders::getInstance( );
}
require_once 'core/tags.php';
require_once 'plugins/plugins.php';

if( isset( $config['current_page_id'] ) && is_numeric( $config['current_page_id'] ) && isset( $oPage->aPages[$config['current_page_id']] ) ){
  $aData = $oPage->throwPage( $config['current_page_id'] );

  if( !empty( $aData['sRedirect'] ) ){
    if( is_numeric( $aData['sRedirect'] ) && isset( $oPage->aPages[$aData['sRedirect']] ) )
      $aData['sRedirect'] = $oPage->aPages[$aData['sRedirect']]['sLinkName'];

    header( 'Location: '.$aData['sRedirect'] );
    exit;
  }

  if( isset( $config['current_tag_id'] ) )
    overwriteDataByTag( $config['current_tag_id'] );

  $config['title'] = trim( !empty( $aData['sTitle'] ) ? $aData['sTitle'].' - '.$config['title'] : ( ( !isset( $config['display_homepage_name_title'] ) && $config['current_page_id'] == $config['start_page'] ) ? $config['title'] : strip_tags( $aData['sName'] ).' - '.$config['title'] ) ).( isset( $_GET['iPage'] ) && is_numeric( $_GET['iPage'] ) ? ' #'.$_GET['iPage'] : null );
  $config['description'] = !empty( $aData['sDescriptionMeta'] ) ? $aData['sDescriptionMeta'] : ( isset( $config['dynamic_meta_description'] ) ? generateDynamicMetaDescription( $aData ) : $config['description'] );
  $aData['sPagesTree'] = $oPage->getPagesTree( $aData['iPage'] );
  $aThemes = throwThemeFiles( $aData['iTheme'] );
  
  if( empty( $aData['sDescriptionFull'] ) && !empty( $aData['sDescriptionShort'] ) )
    $aData['sDescriptionFull'] = $aData['sDescriptionShort'];
}
elseif( isset( $_GET['p'] ) ){
  if( $_GET['p'] == 'test' ){
  }
  elseif( $_GET['p'] == 'xml-sitemap' ){
    throwSiteUrls( );
    header( 'Content-Type: text/xml' );
    echo $oPage->listPagesSiteMap2Xml( );
    exit;
  }
  // plugins actions
  elseif( $_GET['p'] == 'newsletter' && !empty( $_GET['sVerify'] ) ){
    require_once 'core/newsletter.php';
    $config['message'] = confirmEmailInNewsletter( $_GET['sVerify'] );
  }
  else{
    $bError404 = true;
  }
}
else{
  $bError404 = true;
}

if( isset( $bError404 ) ){
  header( "HTTP/1.0 404 Not Found\r\n" );
  $config['title'] = $lang['404_error'].' - ';
  //$aThemes['sMain'] = '404.php';
}

if( !isset( $aThemes ) )
  $aThemes = throwThemeFiles( 1 );

require_once 'templates/'.$config['skin'].'/'.$aThemes['sMain'];

#echo '<h2><center>'.sprintf( '%01.4f', ( memory_get_peak_usage( ) / 1024 ) / 1024 ).'MB</center></h2>';
#echo '<h2><center>'.sprintf( '%01.10f', microtime( true ) - $fStartTime ).'s</center></h2>';
?>