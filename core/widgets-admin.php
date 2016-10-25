<?php
/**
* Save widget data
* @return int
* @param array $aForm
*/
function saveWidget( $aForm ){
  global $config;
  
  clearCache( 'widgets' );
  $oSql = Sql::getInstance( );

  $aForm['iStatus'] = isset( $aForm['iStatus'] ) ? 1 : 0;
  $aForm['iDisplayName'] = isset( $aForm['iDisplayName'] ) ? 1 : 0;
  $aForm = changeMassTxt( $aForm, 'ndnl', Array( 'sDescription', 'nds ndnl' ) );

  if( $aForm['iContentType'] == 1 ){
    $aForm['sContent'] = null;
  }
  elseif( $aForm['iContentType'] == 2 && !empty( $aForm['sPageElements'] ) && is_numeric( $aForm['iPage'] ) ){
    $aForm['sContent'] = serialize( normalizeElements( $aForm['sPageElements'] ) );
    $aForm['iId'] = $aForm['iPage'];
  }
  elseif( $aForm['iContentType'] == 3 && is_numeric( $aForm['iSliderType'] ) ){
    $aForm['sContent'] = null;
    $aForm['iId'] = $aForm['iSliderType'];
  }
  elseif( $aForm['iContentType'] == 4 && is_numeric( $aForm['iMenu'] ) ){
    $aForm['sContent'] = null;
    $aForm['iId'] = $aForm['iMenu'];
  }
  elseif( ( $aForm['iContentType'] == 5 || $aForm['iContentType'] == 6 ) && is_numeric( $aForm['iSubpages'] ) ){
    $aForm['sContent'] = null;
    $aForm['iId'] = $aForm['iSubpages'];
  }
  elseif( $aForm['iContentType'] == 10 && is_numeric( $aForm['iNoticeType'] ) ){
    $aForm['iOnce'] = isset( $aForm['iOnce'] ) ? 1 : 0;
    $aForm['sContent'] = serialize( Array( 'sDescription' => $aForm['sDescription'], 'iOnce' => $aForm['iOnce'] ) );
    $aForm['iId'] = $aForm['iNoticeType'];
  }
  elseif( $aForm['iContentType'] == 23 && isset( $aForm['aTags'] ) ){
    $aForm['sContent'] = serialize( $aForm['aTags'] );
    $aForm['iId'] = null;
  }
  // widgets types - saving

  if( isset( $aForm['iWidget'] ) && is_numeric( $aForm['iWidget'] ) ){
    $oSql->update( 'widgets', $aForm, Array( 'iWidget' => $aForm['iWidget'] ), true );
  }
  else{
    $aForm['sLang'] = $config['language'];
    unset( $aForm['iWidget'] );
    $aForm['iWidget'] = $oSql->insert( 'widgets', $aForm, true );
  }

  return $aForm['iWidget'];
} // end function saveWidget

/**
* Returns widget data
* @return array
* @param int $iWidget
*/
function throwWidget( $iWidget ){
  $oSql = Sql::getInstance( );
  $aData = $oSql->throwAll( 'SELECT * FROM widgets WHERE iWidget = "'.$iWidget.'"' );

  if( $aData['iContentType'] == 1 ){
    $aData['sContent'] = null;
  }
  elseif( $aData['iContentType'] == 2 ){
    $aData['sPageElements'] = $aData['sContent'];
    $aData['iPage'] = $aData['iId'];
    $aData['sContent'] = null;
  }
  elseif( $aData['iContentType'] == 3 ){
    $aData['iSliderType'] = $aData['iId'];
  }
  elseif( $aData['iContentType'] == 4 ){
    $aData['iMenu'] = $aData['iId'];
  }
  elseif( $aData['iContentType'] == 5 || $aData['iContentType'] == 6 ){
    $aData['iSubpages'] = $aData['iId'];
  } 
  elseif( $aData['iContentType'] == 10 ){
    $aData['iNoticeType'] = $aData['iId'];
    $aData = array_merge( $aData, unserialize( $aData['sContent'] ) );
  }
  elseif( $aData['iContentType'] == 23 ){
    $aData['aTags'] = !empty( $aData['sContent'] ) ? unserialize( $aData['sContent'] ) : null;
  }
  // widgets types - displaying

  return $aData;
} // end function throwWidget

/**
* List widgets
* @return string
*/
function listWidgetsAdmin( ){
  global $lang, $config;

  if( isset( $_GET['sSort'] ) ){
    if( $_GET['sSort'] == 'id' )
      $sSort = 'iWidget ASC';
    elseif( $_GET['sSort'] == 'name' )
      $sSort = 'sName COLLATE NOCASE ASC, iPosition ASC';
    elseif( $_GET['sSort'] == 'position' )
      $sSort = 'iPosition ASC, iType ASC';
    elseif( $_GET['sSort'] == 'content-type' )
      $sSort = 'iContentType ASC, iType ASC, iPosition ASC';
  }
  if( !isset( $sSort ) )
    $sSort = 'iType ASC, iPosition ASC';

  $oSql = Sql::getInstance( );
  $oQuery = $oSql->getQuery( 'SELECT * FROM widgets WHERE sLang = "'.$config['language'].'" ORDER BY '.$sSort );
  $i = 0;
  $content = null;
  while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
    $content .= '<tr class="l'.( ( ++$i % 2 ) ? 0: 1 ).'"><td>'.$aData['iWidget'].'</td><td><a href="?p=widgets-form&amp;iWidget='.$aData['iWidget'].'">'.htmlspecialchars( $aData['sName'] ).'</a></td><td>'.getYesNoTxt( $aData['iStatus'] ).'</td><td>'.$aData['iPosition'].'</td><td>'.$config['widgets_types'][$aData['iType']].'</td><td>'.$config['widgets_contents'][$aData['iContentType']].'</td><td class="options"><a href="?p=widgets-form&amp;iWidget='.$aData['iWidget'].'" class="edit">'.$lang['Edit'].'</a>'.( !isset( $config['disable_widgets_delete'] ) ? '<a href="?p=widgets&amp;iItemDelete='.$aData['iWidget'].'" onclick="return del( )" class="delete">'.$lang['Delete'].'</a> ' : null ).'</td></tr>';
  } // end while

  return $content;
} // end function listWidgetsAdmin

/**
* Deletes widget
* @return void
* @param int $iWidget
*/
function deleteWidget( $iWidget ){
  clearCache( 'widgets' );
  $oSql = Sql::getInstance( );
  $oSql->query( 'DELETE FROM widgets WHERE iWidget = "'.$iWidget.'" ' );
} // end function deleteWidget

/**
* Functions normalize elements
* @return string
* @param string $sElements
*/
function normalizeElements( $sElements ){
  $aExp = explode( ',', $sElements );
  foreach( $aExp as $iKey => $sElement ){
    $sElement = trim( str_replace( "'", '', $sElement ) );
    if( !empty( $sElement ) )
      $aReturn[$sElement] = $sElement == 'image' ? 'sLink' : true;
  } // end foreach
  return $aReturn;
} // end function normalizeElements

/**
* Function list all widgets to JS code
* @return string
*/
function listWidgetsEditor( ){
  global $config;

  $content = null;
  $oSql = Sql::getInstance( );
  $oQuery = $oSql->getQuery( 'SELECT * FROM widgets WHERE sLang = "'.$config['language'].'" ORDER BY iType ASC, iPosition ASC' );
  while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
    $content .= '{text: "'.$aData['iWidget'].' - '.$aData['sName'].'", value: "[WIDGET='.$aData['iWidget'].']"}, ';  
  } // end while

  if( isset( $content ) )
    return 'aQuick.widgetsIds = [ '.$content.' ];';
  else
    return null;
} // end function listWidgetsEditor
?>