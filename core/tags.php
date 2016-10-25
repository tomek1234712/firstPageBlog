<?php
/**
* Displays tags
* @return string
* @param array $aTags
* @param array $aParametersExt
*/
function listTagsWidget( $aTags, $aParametersExt = null ){
  global $config;

  if( is_array( $aTags ) ){
    if( !isset( $config['tags_links'] ) )
      $config['tags_links'] = is_file( $config['dir_database'].'cache/tags_links_ids' ) ? unserialize( file_get_contents( $config['dir_database'].'cache/tags_links_ids' ) ) : null;
    if( isset( $config['tags_links'] ) ){
      $oSql = Sql::getInstance( );
      if( !isset( $aParametersExt['sFunctionView'] ) ){
        $aParametersExt['sFunctionView'] = __FUNCTION__.'View';
      }
      $sFunctionView = getFunctionName( $aParametersExt, __FUNCTION__ );
      $content = null;
      $oQuery = $oSql->getQuery( 'SELECT sName, iTag FROM tags WHERE iTag = '.implode( ' OR iTag = ', $aTags ).' ORDER BY iPosition ASC, sName COLLATE NOCASE ASC' );
      while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
        $aData['sLink'] = $config['tags_links'][$aData['iTag']];
        $content .= $sFunctionView( $aData, $aParametersExt );
      } // end while
      return $content;
    }
  }
} // end function listTagsWidget

/**
* Displays page tags
* @return string
* @param int $iPage
* @param array $aParametersExt
*/
function listPageTags( $iPage, $aParametersExt = null ){
  global $lang, $config;

  if( !isset( $config['tags_links'] ) )
    $config['tags_links'] = is_file( $config['dir_database'].'cache/tags_links_ids' ) ? unserialize( file_get_contents( $config['dir_database'].'cache/tags_links_ids' ) ) : null;
  if( isset( $config['tags_links'] ) ){
    $content = null;
    if( !isset( $aParametersExt['sFunctionView'] ) ){
      $aParametersExt['sFunctionView'] = __FUNCTION__.'View';
    }
    $oSql = Sql::getInstance( );
    $sFunctionView = getFunctionName( $aParametersExt, __FUNCTION__ );
    $oQuery = $oSql->getQuery( 'SELECT tags.iTag, tags.sName FROM pages_tags INNER JOIN tags ON pages_tags.iTag = tags.iTag WHERE pages_tags.iPage = '.$iPage.' AND tags.sLang = "'.$config['language'].'" ORDER BY tags.iPosition ASC, tags.sName COLLATE NOCASE ASC' );
    while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
      $aData['sLink'] = $config['tags_links'][$aData['iTag']];
      $content .= $sFunctionView( $aData, $aParametersExt );
    } // end while

    if( isset( $content ) )
      return '<ul id="tags"><li class="head">'.$lang['Tags'].':</li>'.$content.'</ul>';
  }
} // end function listPageTags

/**
* Overwrite $aData variable by tag data
* @return string 
* @param int $iTag
*/
function overwriteDataByTag( $iTag ){
  global $aData, $config;
  $oSql = Sql::getInstance( );
  $aTag = $oSql->throwAll( 'SELECT * FROM tags WHERE iTag = "'.$iTag.'"' );
  if( isset( $aTag ) ){
    $aData['sTitle'] = $aData['sName'] = '#'.$aTag['sName'];
    if( !empty( $aTag['sTitle'] ) )
      $aData['sTitle'] = $aTag['sTitle'];
    if( !empty( $aTag['sDescriptionMeta'] ) )
      $aData['sDescriptionMeta'] = $aTag['sDescriptionMeta'];
  }
} // end function overwriteDataByTag
?>