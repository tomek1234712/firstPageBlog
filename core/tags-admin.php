<?php
/**
* List tags
* @return string
*/
function listTagsAdmin( ){
  global $lang, $config;

  if( !is_file( $config['dir_database'].'cache/tags_links_ids' ) )
    generateTagsLinks( );

  $aTagsLinks = is_file( $config['dir_database'].'cache/tags_links_ids' ) ? unserialize( file_get_contents( $config['dir_database'].'cache/tags_links_ids' ) ) : null;

  $oSql = Sql::getInstance( );
  $oQuery = $oSql->getQuery( 'SELECT tags.*, count( pages_tags.iPage ) AS iPages FROM tags LEFT OUTER JOIN pages_tags ON pages_tags.iTag = tags.iTag WHERE sLang = "'.$config['language'].'" GROUP BY tags.iTag ORDER BY tags.iPosition ASC, tags.sName COLLATE NOCASE ASC' );
  $i = 0;
  $content = null;
  while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
    $content .= '<tr class="l'.( ( ++$i % 2 ) ? 0: 1 ).'"><td>'.$aData['iTag'].'</td><td><a href="?p=tags-form&amp;iTag='.$aData['iTag'].'">'.$aData['sName'].'</a></td><td><a href="'.$aTagsLinks[$aData['iTag']].'" target="_blank">'.$aData['sUrl'].'</a></td><td>'.$aData['iPosition'].'</td><td>'.$aData['iPages'].'</td><td class="options"><a href="?p=tags-form&amp;iTag='.$aData['iTag'].'" class="edit">'.$lang['Edit'].'</a><a href="?p=tags&amp;iItemDelete='.$aData['iTag'].'" onclick="return del( )" class="delete">'.$lang['Delete'].'</a></td></tr>';
  } // end while

  return $content;
} // end function listTagsAdmin

/**
* Returns tags data
* @return array
* @param int $iTag
*/
function throwTag( $iTag ){
  $oSql = Sql::getInstance( );
  return $oSql->throwAll( 'SELECT * FROM tags WHERE iTag = "'.$iTag.'"' );
} // end function throwTag

/**
* Deletes tag
* @return void
* @param int $iTag
*/
function deleteTag( $iTag ){
  $oSql = Sql::getInstance( );
  clearCache( 'tags' );
  $oSql->query( 'DELETE FROM tags WHERE iTag = "'.$iTag.'" ' );
  $oSql->query( 'DELETE FROM pages_tags WHERE iTag = "'.$iTag.'" ' );

  generateTagsLinks( );
} // end function deleteTag

/**
* Save tag data
* @return int
* @param array $aForm
*/
function saveTag( $aForm ){
  global $config;
  $oSql = Sql::getInstance( );
  clearCache( 'tags' );

  $aForm = changeMassTxt( $aForm, 'ndnl' );
  if( empty( $aForm['sUrl'] ) )
    $aForm['sUrl'] = $aForm['sName'];
  $aForm['sUrl'] = trim( $aForm['sUrl'] );
  
  if( isset( $aForm['iPosition'] ) && !is_numeric( $aForm['iPosition'] ) )
    $aForm['iPosition'] = 0;

  if( is_numeric( $oSql->getColumn( 'SELECT iTag FROM tags WHERE sUrl = "'.$aForm['sUrl'].'"'.( ( isset( $aForm['iTag'] ) && is_numeric( $aForm['iTag'] ) ) ? 'AND iTag != '.$aForm['iTag'] : null ) ) ) ){
    return false;
  }
  
  if( isset( $aForm['iTag'] ) && is_numeric( $aForm['iTag'] ) ){
    $oSql->update( 'tags', $aForm, Array( 'iTag' => $aForm['iTag'] ), true );
  }
  else{
    $aForm['sLang'] = $config['language'];
    unset( $aForm['iTag'] );
    $aForm['iTag'] = $oSql->insert( 'tags', $aForm, true );
  }

  generateTagsLinks( );
  return $aForm['iTag'];
} // end function saveTag

/**
* Returns a list of tags in form of a HTML select
* @return string
* @param int $mData
*/
function listTagsSelect( $mData ){
  global $config;

  $oSql = Sql::getInstance( );
  $content = null;
  if( is_numeric( $mData ) ){
    $iPage = $mData;
  }
  elseif( is_array( $mData ) ){
    $aTags = array_flip( $mData );
  }

  $oQuery = $oSql->getQuery( 'SELECT tags.sName, tags.iTag'.( isset( $iPage ) ? ', pages_tags.iPage' : ( isset( $aTags ) ? ', count( pages_tags.iPage ) AS iPages' : null ) ).' FROM tags'.( isset( $iPage ) ? ' LEFT JOIN pages_tags ON pages_tags.iTag=tags.iTag AND pages_tags.iPage = "'.$iPage.'"' : ( isset( $aTags ) ? ' LEFT OUTER JOIN pages_tags ON pages_tags.iTag = tags.iTag' : null ) ).' WHERE sLang = "'.$config['language'].'"'.( isset( $aTags ) ? ' GROUP BY tags.iTag' : null ).' ORDER BY tags.iPosition ASC, tags.sName ASC' );
  while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
    $content .= '<option value="'.$aData['iTag'].'"'.( ( !empty( $aData['iPage'] ) || ( isset( $aTags ) && isset( $aTags[$aData['iTag']] ) ) ) ? ' selected="selected"' : null ).'>'.$aData['sName'].( !empty( $aData['iPages'] ) ? ' ('.$aData['iPages'].')' : null ).'</option>';
  } // end while

  return $content;
} // end function listTagsSelect

/**
* Generates tags links
* @return void
*/
function generateTagsLinks( ){
  global $config;
  
  $oSql = Sql::getInstance( );
  $oQuery = $oSql->getQuery( 'SELECT sUrl, iTag, sLang FROM tags ORDER BY iPosition ASC' );
  while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
    $sUrl = $config['tags_url_prefix'].change2Url( $aData['sUrl'] );
    $aLinksIds[$aData['iTag']] = $sUrl.'.html';
    $aLinks[$sUrl] = Array( $aData['iTag'], $aData['sLang'] );
  } // end while

  file_put_contents( $config['dir_database'].'cache/tags_links', ( isset( $aLinks ) ? serialize( $aLinks ) : null ) );
  file_put_contents( $config['dir_database'].'cache/tags_links_ids', ( isset( $aLinksIds ) ? serialize( $aLinksIds ) : null ) );
} // end function generateTagsLinks
?>