<?php
if( !defined( 'CUSTOMER_PAGE' ) )
  exit;

/**
* Displays page in the menu - default settings
* @return string
* @param array $aData
* @param array $aParametersExt
*/
function listPagesMenuView( $aData, $aParametersExt ){
  $sClassName = null;
  if( isset( $aParametersExt['bSelected'] ) )
    $sClassName .= 'selected';
  if( isset( $aParametersExt['bSelectMain'] ) )
    $sClassName .= ' selected-parent';
  return '<li'.( isset( $sClassName ) ? ' class="'.$sClassName.'"' : null ).'><a href="'.$aData['sLinkName'].'">'.( !empty( $aData['sNameMenu'] ) ? $aData['sNameMenu'] : $aData['sName'] ).'</a>'.$aParametersExt['sSubMenu'].'</li>';
} // end function listPagesMenuView

/**
* Displays page in the list - default settings
* @return string
* @param array $aData
* @param array $aParametersExt
*/
function listPagesView( $aData, $aParametersExt ){
  global $config;
  if( !isset( $aParametersExt['iType'] ) || $aParametersExt['iType'] > 2 )
    $oFile = Files::getInstance( );
  //return '<li'.( ( $aParametersExt['iElement'] % 4 ) == 1 ? ' class="row"' : null ).'>'. // oldie
  return '<li>'.
    ( isset( $oFile ) ? $oFile->getDefaultImage( $aData['iPage'], Array( 'sLink' => ( !isset( $aParametersExt['bNoLinks'] ) ? $aData['sLinkName'] : null ), 'bNoLinks' => ( isset( $aParametersExt['bNoLinks'] ) ? true : null ), 'sKeySize' => ( isset( $aParametersExt['sKeySize'] ) ? $aParametersExt['sKeySize'] : null ) ) ) : null ). // image
    '<h2>'.( !isset( $aParametersExt['bNoLinks'] ) ? '<a href="'.$aData['sLinkName'].'">' : null ).$aData['sName'].( !isset( $aParametersExt['bNoLinks'] ) ? '</a>' : null ).'</h2>'. // name and link to page
    ( ( isset( $aParametersExt['iType'] ) && $aParametersExt['iType'] == 4 && !empty( $aData['iTime'] ) ) ? '<time>'.displayDate( $aData['iTime'], $config['date_format_customer_news'] ).'</time>' : null ). // date
    ( ( ( !isset( $aParametersExt['iType'] ) || $aParametersExt['iType'] > 1 ) && !empty( $aData['sDescriptionShort'] ) ) ? '<div class="description">'.$aData['sDescriptionShort'].'</div>' : null ). // short description here

'<a class="dalej" href="'.$aData['sLinkName'].'">'.$config['dalej'].'</a>'.

    '</li>';
} // end function listPagesView

/**
* Displays images
* @return string
* @param array $aData
* @param array $aParametersExt
*/
function listImagesView( $aData, $aParametersExt ){
  //return '<li'.( ( $aParametersExt['iElement'] % 4 ) == 1 ? ' class="row"' : null ).'>'. // oldie
  return '<li>'.
  ( !isset( $aParametersExt['bNoLinks'] ) ? '<a href="files/'.$aData['sFileName'].'" class="quickbox['.( isset( $aData['iPage'] ) ? $aData['iPage'] : 0 ).']" title="'.$aData['sDescription'].'">' : null ).'<img src="files/'.$aData['iSizeDetails'].'/'.$aData['sFileNameThumb'].'" alt="'.( !empty( $aData['sDescription'] ) ? $aData['sDescription'] : null ).'" />'.( !isset( $aParametersExt['bNoLinks'] ) ? '</a>' : null ).( !empty( $aData['sDescription'] ) ? '<p>'.$aData['sDescription'].'</p>' : null ).'</li>';
} // end function listImagesView

/**
* Displays files
* @return string
* @param array $aData
* @param array $aParametersExt
*/
function listFilesView( $aData, $aParametersExt ){
  return '<li class="'.$aData['sIconStyle'].'"><a href="files/'.$aData['sFileName'].'">'.$aData['sFileName'].'</a>'.( !empty( $aData['sDescription'] ) ? '<p>'.$aData['sDescription'].'</p>' : null ).'</li>';
} // end function listFilesView

/**
* Displays page details
* @return string
* @param int $iPage
* @param string $aDisplayOptions
* Default options: 
- name - options: true (with link), null (without link)
- date - options: true
- description - options: true
- more - options: true
- image - options: bNoLinks (without link), sLink (with link to page), null (with link to image)
* @param array $aParametersExt
* Default options: sHtmlTag, sClassName
*/
//echo displayPageDetails( 9, Array( 'image' => null, 'name' => true, 'description' => true ), Array( 'sHtmlTag' => 'div', 'sClassName' => 'page-details' ) );
function displayPageDetails( $iPage, $aDisplayOptions = Array( 'image' => 'sLink', 'name' => true, 'description' => true ), $aParametersExt = null ){
  if( isset( $aParametersExt['sClassName'] ) )
    $aParametersExt['sClassName'] = ' class="'.$aParametersExt['sClassName'].'"';
  if( !isset( $aParametersExt['sHtmlTag'] ) )
    $aParametersExt['sHtmlTag'] = 'aside';

  $oPage = Pages::getInstance( );
  if( isset( $oPage->aPages[$iPage] ) && isset( $aDisplayOptions ) && is_array( $aDisplayOptions ) ){
    $content = null;
    foreach( $aDisplayOptions as $sName => $mOption ){
      if( $sName == 'name' )
        $content .= '<div class="name">'.( isset( $mOption ) ? '<a href="'.$oPage->aPages[$iPage]['sLinkName'].'">' : null ).$oPage->aPages[$iPage]['sName'].( isset( $mOption ) ? '</a>' : null ).'</div>';
      elseif( $sName == 'date' && !empty( $oPage->aPages[$iPage]['iTime'] ) )
        $content .= '<time>'.displayDate( $oPage->aPages[$iPage]['iTime'], $GLOBALS['config']['date_format_customer_news'] ).'</time>';
      elseif( $sName == 'description' && !empty( $oPage->aPages[$iPage]['sDescriptionShort'] ) )
        $content .= '<div class="description">'.stripslashes( $oPage->aPages[$iPage]['sDescriptionShort'] ).'</div>';
      elseif( $sName == 'more' )
        $content .= '<span class="more"><a href="'.$oPage->aPages[$iPage]['sLinkName'].'">'.$GLOBALS['lang']['More'].'</a></span>';
      elseif( $sName == 'image' ){
        $oFile = Files::getInstance( );
        $aParametersExtImage['sKeySize'] = 'iSizeOther';
        if( !empty( $mOption ) )
          $aParametersExtImage[$mOption] = ( ( $mOption == 'sLink' ) ? $oPage->aPages[$iPage]['sLinkName'] : true );
        $content .= $oFile->getDefaultImage( $iPage, $aParametersExtImage );
      }
    } // end foreach
    return '<'.$aParametersExt['sHtmlTag'].( !empty( $aParametersExt['sClassName'] ) ? $aParametersExt['sClassName'] : null ).'>'.$content.'</'.$aParametersExt['sHtmlTag'].'>';
  }
} // end function displayPageDetails

/**
* Displays sliders
* @return string
* @param array $aData
* @param array $aParametersExt
*/
function listSlidersView( $aData, $aParametersExt ){
  if( !isset( $aParametersExt['bNoLinks'] ) && !empty( $aData['sRedirect'] ) ){
    if( is_numeric( $aData['sRedirect'] ) )
      $oPage = Pages::getInstance( );
    $sLink = '<a href="'.( ( isset( $oPage ) && isset( $oPage->aPages[$aData['sRedirect']] ) ) ? $oPage->aPages[$aData['sRedirect']]['sLinkName'] : $aData['sRedirect'] ).'">';
  }
  return '<li class="slide'.$aData['iSlider'].' '.( !empty( $aData['sFileName'] ) ? 'img' : 'no-img' ).'">'.( !empty( $aData['sFileName'] ) ? ( isset( $sLink ) ? $sLink : null ).'<img src="files/'.$aData['sFileName'].'" alt="Slider '.$aData['iSlider'].'" />'.( isset( $sLink ) ? '</a>' : null ) : null ).( !empty( $aData['sDescription'] ) ? '<div class="description">'.$aData['sDescription'].'</div>' : null ).'</li>';
} // end function listSlidersView

/**
* Displays widget type 1
* @return string
* @param array $aData
* @param array $aParametersExt
*/
function listWidgetsView1( $aData, $aParametersExt = null ){
  return ( $aData['iDisplayName'] == 1 ? '<div class="head">'.$aData['sName'].'</div>' : null ).'<div class="description">'.$aData['sDescription'].'</div>';
} // end function listWidgetsView1

/**
* Displays widget type 2 - page details
* @return string
* @param array $aData
* @param array $aParametersExt
*/
function listWidgetsView2( $aData, $aParametersExt = null ){
  if( is_numeric( $aData['iId'] ) ){
    $oPage = Pages::getInstance( );
    if( isset( $oPage->aPages[$aData['iId']] ) && !empty( $aData['sContent'] ) ){
      $sFunctionView = ( isset( $aParametersExt['aFunctionParameters'][$aData['iContentType']]['sFunctionView'] ) && function_exists( $aParametersExt['aFunctionParameters'][$aData['iContentType']]['sFunctionView'] ) ) ? $aParametersExt['aFunctionParameters'][$aData['iContentType']]['sFunctionView'] : 'displayPageDetails';
      $sDisplay = $sFunctionView( $aData['iId'], unserialize( $aData['sContent'] ), ( isset( $aParametersExt['aFunctionParameters'][$aData['iContentType']] ) ? $aParametersExt['aFunctionParameters'][$aData['iContentType']] : null ) );
      if( !empty( $sDisplay ) )
        return ( $aData['iDisplayName'] == 1 ? '<div class="head">'.$aData['sName'].'</div>' : null ).( !empty( $aData['sDescription'] ) ? '<div class="description">'.$aData['sDescription'].'</div>' : null ).$sDisplay;
    }
  }
  return ( ( defined( 'DEVELOPER_MODE' ) && isset( $aParametersExt['iType'] ) ) ? '<p class="dev">THERE IS NO PAGE WITH ID - '.$aData['iId'].'</p>' : null );
} // end function listWidgetsView2

/**
* Displays widget type 3 - slider
* @return string
* @param array $aData
* @param array $aParametersExt
*/
function listWidgetsView3( $aData, $aParametersExt = null ){
  global $config;

  if( is_numeric( $aData['iId'] ) && isset( $config['enabled_sliders'] ) ){
    $oSlider = Sliders::getInstance( );
    $aParams = Array( 'iType' => $aData['iId'], 'sClassName' => isset( $aParametersExt['aFunctionParameters'][$aData['iContentType']]['sClassName'] ) ? $aParametersExt['aFunctionParameters'][$aData['iContentType']]['sClassName'] : 'slider' );
    $sDisplay = $oSlider->listSliders( ( isset( $aParametersExt['aFunctionParameters'][$aData['iContentType']] ) ? array_merge( $aParams, $aParametersExt['aFunctionParameters'][$aData['iContentType']] ) : $aParams ) );
    if( !empty( $sDisplay ) )
      return ( $aData['iDisplayName'] == 1 ? '<div class="head">'.$aData['sName'].'</div>' : null ).( !empty( $aData['sDescription'] ) ? '<div class="description">'.$aData['sDescription'].'</div>' : null ).$sDisplay;  
  }
  return ( ( defined( 'DEVELOPER_MODE' ) && isset( $aParametersExt['iType'] ) ) ? '<p class="dev">THERE IS NO SLIDER TYPE - '.$aData['iId'].'</p>' : null );
} // end function listWidgetsView3

/**
* Displays widget type 4 - menu
* @return string
* @param array $aData
* @param array $aParametersExt
*/
function listWidgetsView4( $aData, $aParametersExt = null ){
  if( is_numeric( $aData['iId'] ) ){
    $oPage = Pages::getInstance( );
    $aParams = Array( 'iDepthLimit' => 0 );
    $sDisplay = $oPage->listPagesMenu( $aData['iId'], ( isset( $aParametersExt['aFunctionParameters'][$aData['iContentType']] ) ? array_merge( $aParams, $aParametersExt['aFunctionParameters'][$aData['iContentType']] ) : $aParams ) );
    if( !empty( $sDisplay ) )
      return ( $aData['iDisplayName'] == 1 ? '<div class="head">'.$aData['sName'].'</div>' : null ).( !empty( $aData['sDescription'] ) ? '<div class="description">'.$aData['sDescription'].'</div>' : null ).$sDisplay;  
  }
  return ( ( defined( 'DEVELOPER_MODE' ) && isset( $aParametersExt['iType'] ) ) ? '<p class="dev">THERE IS NO MENU TYPE - '.$aData['iId'].'</p>' : null );
} // end function listWidgetsView4

/**
* Displays widget type 5 - subpages
* @return string
* @param array $aData
* @param array $aParametersExt
*/
function listWidgetsView5( $aData, $aParametersExt = null ){
  if( is_numeric( $aData['iId'] ) ){
    $oPage = Pages::getInstance( );
    if( isset( $oPage->aPages[$aData['iId']] ) ){
      $aParams = Array( 'sKeySize' => 'iSizeOther', 'sUrlName' => 'iPageWidget'.$aData['iId'], 'bAssingFromRequestUri' => true );
      if( !isset( $aParametersExt['aFunctionParameters'][$aData['iContentType']]['iType'] ) )
        $aParams['iType'] = $oPage->aPages[$aData['iId']]['iSubpages'];
      $sDisplay = $oPage->listPages( $aData['iId'], ( isset( $aParametersExt['aFunctionParameters'][$aData['iContentType']] ) ? array_merge( $aParams, $aParametersExt['aFunctionParameters'][$aData['iContentType']] ) : $aParams ) );
      if( !empty( $sDisplay ) )
        return ( $aData['iDisplayName'] == 1 ? '<div class="head">'.$aData['sName'].'</div>' : null ).( !empty( $aData['sDescription'] ) ? '<div class="description">'.$aData['sDescription'].'</div>' : null ).$sDisplay;  
    }
  }
  return ( ( defined( 'DEVELOPER_MODE' ) && isset( $aParametersExt['iType'] ) ) ? '<p class="dev">THERE IS NO SUBPAGES FOR PAGE ID - '.$aData['iId'].'</p>' : null );
} // end function listWidgetsView5

/**
* Displays widget type 6 - subpages as slider
* @return string
* @param array $aData
* @param array $aParametersExt
*/
function listWidgetsView6( $aData, $aParametersExt = null ){
  global $config;
  if( is_numeric( $aData['iId'] ) ){
    $oPage = Pages::getInstance( );
    if( isset( $oPage->aPages[$aData['iId']] ) && isset( $config['enabled_sliders'] ) ){
      $aParams = Array( 'sKeySize' => 'iSizeOther', 'sFunctionView' => 'listPagesSliderView', 'sClassName' => 'pages-slider', 'sUrlName' => 'iPageWidget'.$aData['iId'], 'bAssingFromRequestUri' => true );
      if( !isset( $aParametersExt['aFunctionParameters'][$aData['iContentType']]['iType'] ) )
        $aParams['iType'] = $oPage->aPages[$aData['iId']]['iSubpages'];
      $sDisplay = $oPage->listPages( $aData['iId'], ( isset( $aParametersExt['aFunctionParameters'][$aData['iContentType']] ) ? array_merge( $aParams, $aParametersExt['aFunctionParameters'][$aData['iContentType']] ) : $aParams ) );
      if( !empty( $sDisplay ) ){
        return ( $aData['iDisplayName'] == 1 ? '<div class="head">'.$aData['sName'].'</div>' : null ).( !empty( $aData['sDescription'] ) ? '<div class="description">'.$aData['sDescription'].'</div>' : null ).'<div class="'.( isset( $aParametersExt['sClassName'] ) ? $aParametersExt['sClassName'] : 'slider' ).'" id="slider-'.( isset( $aParametersExt['iType'] ) ? $aParametersExt['iType'] : null ).'-'.$aData['iWidget'].'">'.$sDisplay.'</div><script>$("#slider-'.( isset( $aParametersExt['iType'] ) ? $aParametersExt['iType'] : null ).'-'.$aData['iWidget'].'").quickslider({'.( isset( $aParametersExt['aFunctionParameters'][$aData['iContentType']]['sConfig'] ) ? $aParametersExt['aFunctionParameters'][$aData['iContentType']]['sConfig'] : null ).'});</script>';
      }
    }
  }
  return ( ( defined( 'DEVELOPER_MODE' ) && isset( $aParametersExt['iType'] ) ) ? '<p class="dev">THERE IS NO SUBPAGES FOR PAGE ID - '.$aData['iId'].'</p>' : null );
} // end function listWidgetsView6

/**
* Displays page in the list - default settings
* @return string
* @param array $aData
* @param array $aParametersExt
*/
function listPagesSliderView( $aData, $aParametersExt ){
  global $config;
  $oFile = Files::getInstance( );
  $sImage = isset( $oFile ) ? $oFile->getDefaultImage( $aData['iPage'], Array( 'sLink' => ( !isset( $aParametersExt['bNoLinks'] ) ? $aData['sLinkName'] : null ), 'bNoLinks' => ( isset( $aParametersExt['bNoLinks'] ) ? true : null ), 'sKeySize' => ( isset( $aParametersExt['sKeySize'] ) ? $aParametersExt['sKeySize'] : null ) ) ) : null;
  return '<li class="slide'.$aData['iPage'].' '.( isset( $sImage ) ? 'img' : 'no-img' ).'">'.
    $sImage.
    '<div class="name">'.( !isset( $aParametersExt['bNoLinks'] ) ? '<a href="'.$aData['sLinkName'].'">' : null ).$aData['sName'].( !isset( $aParametersExt['bNoLinks'] ) ? '</a>' : null ).'</div>'. // name and link to page
    ( ( isset( $aParametersExt['iType'] ) && $aParametersExt['iType'] == 4 && !empty( $aData['iTime'] ) ) ? '<time>'.displayDate( $aData['iTime'], $config['date_format_customer_news'] ).'</time>' : null ). // date
    ( ( ( !isset( $aParametersExt['iType'] ) || $aParametersExt['iType'] > 1 ) && !empty( $aData['sDescriptionShort'] ) ) ? '<div class="slider-description">'.$aData['sDescriptionShort'].'</div>' : null ). // short description here
    '</li>';
} // end function listPagesSliderView

/* PLUGINS */

/**
* Displays widget type 12 - newsletter
* @return string
* @param array $aData
* @param array $aParametersExt
*/
function listWidgetsView12( $aData, $aParametersExt = null ){
  global $lang, $config;

  if( isset( $_POST['sNewsletterEmail'] ) ){
    require_once 'core/libraries/forms-validate.php';
    require_once 'core/newsletter.php';
    if( isset( $_POST['sNewsletterEmail'] ) && checkEmail( $_POST['sNewsletterEmail'] ) && strlen( $_POST['sNewsletterEmail'] ) < 100 ){
      addEmailToNewsletter( $_POST['sNewsletterEmail'] );
      return ( $aData['iDisplayName'] == 1 ? '<div class="head" id="newsletter">'.$aData['sName'].'</div>' : null ).( !empty( $aData['sDescription'] ) ? '<div class="description">'.$aData['sDescription'].'</div>' : null ).'<div class="msg done">'.$lang['Email_added'].'</div>';
    }
    else{
      return ( $aData['iDisplayName'] == 1 ? '<div class="head" id="newsletter">'.$aData['sName'].'</div>' : null ).( !empty( $aData['sDescription'] ) ? '<div class="description">'.$aData['sDescription'].'</div>' : null ).'<div class="msg error">'.$lang['Fill_required_fields'].'</div>';
    }
  }
  else{
  return '<form action="'.$_SERVER['REQUEST_URI'].'#newsletter" method="post" class="newsletter">
    <fieldset>
      <legend class="'.( $aData['iDisplayName'] == 1 ? 'head">'.$aData['sName'] : 'default">'.$lang['Newsletter'] ).'</legend>
      '.( !empty( $aData['sDescription'] ) ? '<div class="description">'.$aData['sDescription'].'</div>' : null ).'
      <div class="add">
        <label for="sNewsletterEmail" class="default">'.$lang['Newsletter_add'].'</label><input type="email" name="sNewsletterEmail" id="sNewsletterEmail" placeholder="'.$lang['Newsletter_add'].'" value="" class="input" maxlength="100" />
      </div>
      <div class="save">
        <button type="submit"><img src="templates/default/img/strzalka.png" alt="Zapisz mnie do newslettera"></button>
      </div>
    </fieldset>
  </form>';
  }
} // end function listWidgetsView12

/**
* Displays widget type 23 - tags
* @return string
* @param array $aData
* @param array $aParametersExt
*/
function listWidgetsView23( $aData, $aParametersExt = null ){
  global $config;
  if( !empty( $aData['sContent'] ) && strstr( $aData['sContent'], ':{' ) ){
    $sTags = listTagsWidget( unserialize( $aData['sContent'] ), ( isset( $aParametersExt['aFunctionParameters'][$aData['iContentType']] ) ? $aParametersExt['aFunctionParameters'][$aData['iContentType']] : null ) );
    if( !empty( $sTags ) ){
      return ( $aData['iDisplayName'] == 1 ? '<div class="head">'.$aData['sName'].'</div>' : null ).( !empty( $aData['sDescription'] ) ? '<div class="description">'.$aData['sDescription'].'</div>' : null ).'<ul class="tags">'.$sTags.'</ul>';
    }
  }
  return ( ( defined( 'DEVELOPER_MODE' ) && isset( $aParametersExt['iType'] ) ) ? '<p class="dev">THERE IS NO TAGS DEFINED IN WIDGET ID - '.$aData['iWidget'].'</p>' : null );
} // end function listWidgetsView23

/**
* Displays tags in the widget
* @return string
* @param array $aData
* @param array $aParametersExt
*/
function listTagsWidgetView( $aData, $aParametersExt ){
  global $config;
  return '<li><a href="'.$aData['sLink'].'" rel="tag">'.$aData['sName'].'</a></li>';
} // end function listTagsWidgetView

/**
* Displays page tags
* @return string
* @param array $aData
* @param array $aParametersExt
*/
function listPageTagsView( $aData, $aParametersExt ){
  global $config;
  return '<li><a href="'.$aData['sLink'].'" rel="tag">'.$aData['sName'].'</a></li>';
} // end function listPageTagsView

/**
* Displays widget type 10 - displaying notice
* @return string
* @param array $aData
* @param array $aParametersExt
*/
function listWidgetsView10( $aData, $aParametersExt = null ){
  if( !isset( $_COOKIE['iNoticeClosed-'.$aData['iWidget']] ) || $_COOKIE['iNoticeClosed-'.$aData['iWidget']] != 1 ){
    $aData = array_merge( $aData, unserialize( $aData['sContent'] ) );
    return '<script>$( function(){displayNotice( '.$aData['iWidget'].', '.$aData['iId'].(( isset( $aData['iOnce'] ) && $aData['iOnce'] == 1 ) ? ', true' : null).' );} );</script>
    <aside class="widget type-'.$aData['iContentType'].' location-'.$aData['iId'].' id-'.$aData['iWidget'].'">'.( $aData['iDisplayName'] == 1 ? '<div class="head">'.$aData['sName'].'</div>' : null ).( !empty( $aData['sDescription'] ) ? '<div class="description">'.$aData['sDescription'].'</div>' : null ).'<div class="content">'.$aData['sDescription'].'</div><div class="close"><a href="#">'.$GLOBALS['lang']['Close'].'</a></div></aside>';
  }
} // end function listWidgetsView10

/**
* Displays widget type 7 - contact form
* @return string
* @param array $aData
* @param array $aParametersExt
*/
function listWidgetsView7( $aData, $aParametersExt = null ){
  global $config, $lang;
  ob_start( );
  echo ( $aData['iDisplayName'] == 1 ? '<div class="head">'.$aData['sName'].'</div>' : null );
  echo ( !empty( $aData['sDescription'] ) ? '<div class="description">'.$aData['sDescription'].'</div>' : null );
  include 'templates/'.$config['skin'].'/_contact-form.php';
  $sReturn = ob_get_contents( );
  ob_end_clean( );
  return $sReturn;
} // end function listWidgetsView7

/**
* Displays widget type 20 - Back to top button
* @return string
* @param array $aData
* @param array $aParametersExt
*/
function listWidgetsView20( $aData, $aParametersExt = null ){
  return '<script>$(function(){ backToTopInit(); });</script>
  <div class="back-to-top-widget"><a href="#container">'.( $aData['iDisplayName'] == 1 ? '<div class="head">'.$aData['sName'].'</div>' : '&nbsp;' ).( !empty( $aData['sDescription'] ) ? '<div class="description">'.$aData['sDescription'].'</div>' : null ).'</a></div>
  ';
} // end function listWidgetsView20
?>