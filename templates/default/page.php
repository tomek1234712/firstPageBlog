<?php 
// More about design modifications - www.opensolution.org/docs/
if( !defined( 'CUSTOMER_PAGE' ) )
  exit;

require_once 'templates/'.$config['skin'].'/'.$aThemes['sHeader']; // include design of header
?>
<section id="page">
  <div class="wrapper">
   <div class="zawartosc">
<nav id="kolumna_lewa">

<?php if( isset( $config['enabled_widgets'] ) ) echo $oWidget->listWidgets( Array( 'iType' => 5, 'bDontDisplayErrors' => true ) ); ?>
</nav>




    <div id="kolumna_srodek">

<!-- ORYGINALNE -->
<?php

if( isset( $aData['sName'] ) ){ // displaying pages and subpages content
  echo '<h1>'.$aData['sName'].'</h1>'; // displaying page name

  if( isset( $config['display_editing_options'] ) && isset( $config['session_key_name'] ) && isset( $_SESSION[$config['session_key_name']] ) && is_int( $_SESSION[$config['session_key_name']] ) ){
    echo '<ul class="options"><li class="edit"><a href="'.$config['admin_file'].'?p=pages-form&amp;iPage='.$aData['iPage'].'&amp;sLanguage='.$config['language'].'" onclick="return confirm( \''.$lang['Operation_sure'].'\' )" title="'.$lang['Edit'].'">'.$lang['Edit'].'</a></li><li class="delete"><a href="'.$config['admin_file'].'?p=pages&amp;iItemDelete='.$aData['iPage'].'&amp;sLanguage='.$config['language'].'&amp;sVerify='.md5( $config['session_key_name'] ).'" onclick="return del( \''.$lang['Operation_sure_delete'].'\' )" title="'.$lang['Delete'].'">'.$lang['Delete'].'</a></li></ul>';
  }
  if( isset( $aData['sPagesTree'] ) )
    echo '<nav class="breadcrumb">'.$aData['sPagesTree'].'</nav>'; // displaying page tree (breadcrumb)

  if( !empty( $aData['iPageParent'] ) && !empty( $aData['iTime'] ) ){
    echo '<time>'.displayDate( $aData['iTime'], $config['date_format_customer_news'] ).'</time>';
  } 

  echo $oFile->listImages( $aData['iPage'], Array( 'iType' => 3 ) ); // displaying images with type: gallery 1
  echo $oFile->listImages( $aData['iPage'], Array( 'iType' => 1 ) ); // displaying images with type: left
  echo $oFile->listImages( $aData['iPage'], Array( 'iType' => 2 ) ); // displaying images with type: right
  
  if( isset( $aData['sDescriptionFull'] ) )
    echo '<div class="content">'.$aData['sDescriptionFull'].'</div>'; // full description

  echo $oFile->listImages( $aData['iPage'], Array( 'iType' => 4 ) ); // displaying images with type: gallery 2

  if( isset( $aData['sPages'] ) )
    echo '<nav class="pages">'.$lang['Pages'].': <ul>'.$aData['sPages'].'</ul></nav>'; // full description pagination


  if($aData['iPageParent'] == '1'){

    echo '<div class="fb-like" data-href="'.$_SERVER['REQUEST_URI'].'" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>';
}

  if( !empty( $config['page_sitemap'] ) && $aData['iPage'] == $config['page_sitemap'] )
    echo $oPage->listSiteMap( ); // displaying sitemap
  elseif( isset( $config['current_tag_id'] ) ){
    echo $oPage->listTagsPages( $config['current_tag_id'] ); // displaying tag subpages
  }

  echo $oFile->listFiles( $aData['iPage'] ); // display files included to the page
  echo listPageTags( $aData['iPage'] ); // display tags included to the page

  if( isset( $config['page_search'] ) && is_numeric( $config['page_search'] ) && $aData['iPage'] == $config['page_search'] ){ // search results
    $sSearchList = !empty( $_GET['sSearch'] ) ? $oPage->listPagesSearch( $_GET['sSearch'], Array( 'iType' => $aData['iSubpages'], 'bPagination' => true, 'sFunctionView' => ( !empty( $aData['sListFunction'] ) ? $aData['sListFunction'] : null ) ) ) : null;
    echo isset( $sSearchList ) ? $sSearchList : '<div class="msg error"><h1>'.$lang['Data_not_found'].'</h1></div>';
  }
  elseif( $aData['iSubpages'] > 0 )
    echo $oPage->listPages( $aData['iPage'], Array( 'iType' => $aData['iSubpages'], 'bPagination' => true, 'sFunctionView' => ( !empty( $aData['sListFunction'] ) ? $aData['sListFunction'] : null ) ) ); // displaying subpages
}
else{
  echo ( isset( $config['message'] ) ? $config['message'] : '<div class="msg error"><h1>'.$lang['Data_not_found'].'</h1></div>' ); // displaying 404 error or other message
}
// echo $config['current_tag_id'].'xxxxxxxxxxxxxxxxxx';
?>
<!-- ORYGINALNE -->





    </div>
    <nav id="kolumna_prawa">

<?php if( isset( $config['enabled_widgets'] ) ) echo $oWidget->listWidgets( Array( 'iType' => 6, 'bDontDisplayErrors' => true ) ); ?>
</nav>
  </div>
  </div>
</section>
<?php
require_once 'templates/'.$config['skin'].'/'.$aThemes['sFooter']; // include design of footer
?>
