<?php
/**
* Returns language variables from $lang array (files in the "database/" directory)
* @return string
* @param string $sLang
*/
function listLangVariables( $sLang ){
  global $config;

  // here you can define the list of keys of the table, that will not be displayed and will not be available for translation
  $aDontDisplay = Array( 'script_footer' => true );

  if( is_file( $config['dir_database'].'lang_'.$sLang.'.php' ) ){
    if( $sLang != $config['admin_lang'] ){
      include $config['dir_database'].'lang_'.$config['admin_lang'].'.php';
      $aLangRaw = $lang;
    }
    include $config['dir_database'].'lang_'.$sLang.'.php';
    $content = null;
    $iKey = 0;

    foreach( $lang as $sKey => $sValue ){
      if( $sKey == 'Failed_login_wait_time' )
        $iKey = 1;
      if( !isset( $aReturn[$iKey] ) )
        $aReturn[$iKey] = null;
      if( !isset( $aDontDisplay[$sKey] ) )
        $aReturn[$iKey] .= '<li><label for="'.$sKey.'">'.$sKey.'</label><input type="text" name="'.$sKey.'" value="'.str_replace( '|n|', '\n', changeTxt( $sValue, '' ) ).'" id="'.$sKey.'" size="80" />'.( isset( $aLangRaw[$sKey] ) ? ' = '.htmlspecialchars( $aLangRaw[$sKey] ) : null ).'</li>';
    } // end foreach

    if( isset( $aReturn ) ){
      return $aReturn;
    }
  }
} // end function listLangVariables

/**
* Returns array of all available languages
* @return array
*/
function throwLanguages( ){
  global $config;
  foreach( new DirectoryIterator( $config['dir_database'] ) as $oFileDir ) {
    if( $oFileDir->isFile( ) && preg_match( '/^lang_[a-z]{2}\.php$/', $oFileDir->getFilename( ) ) ){
      preg_match( '/_[a-z]{2}/', $oFileDir->getFilename( ), $aMatches );
      $aLanguages[substr( $aMatches[0], 1 )] = $oFileDir->getFilename( );
    }
  } // end foreach

  if( isset( $aLanguages ) )
    return $aLanguages;
} // end function throwLanguages

/**
* Lists all language files
* @return string
*/
function listLanguages( ){
  global $lang, $config;
  $content = null;
  $aLanguages = throwLanguages( );
  if( isset( $aLanguages ) && is_array( $aLanguages ) ){
    $i = 0;
    foreach( $aLanguages as $sLang => $sFile ){
      $content .= '<tr class="l'.( ( ++$i % 2 ) ? 0: 1 ).'"><td><a href="?p=languages&amp;sLangEdit='.$sLang.'">'.$sLang.'</a></td><td class="options"><a href="?p=languages&amp;sLangEdit='.$sLang.'" class="edit">'.$lang['Edit'].'</a>'.( ( $config['default_language'] == $sLang || isset( $config['disable_language_delete'] ) ) ? null : '<a href="?p=languages&amp;sItemDelete='.$sLang.'" onclick="return del( )" class="delete">'.$lang['Delete'].'</a>' ).'</td></tr>';
    } // end foreach

    if( isset( $content ) )
      return $content;
  }
} // end function listLanguages

/**
* Lists all language files to menu
* @return string
*/
function listLanguagesMenu( ){
  global $lang, $config;
  $content = null;
  $aLanguages = throwLanguages( );
  if( isset( $aLanguages ) && is_array( $aLanguages ) && count( $aLanguages ) > 1 ){
    $i = 0;
    foreach( $aLanguages as $sLang => $sFile ){
      $content .= '<li'.( $config['language'] == $sLang ? ' class="selected"' : null ).'><a href="?sLanguage='.$sLang.'">'.$sLang.'</a></li>';
    } // end foreach

    if( isset( $content ) )
      return '<ul id="languages-select" class="menu">'.$content.'</ul>';
  }
} // end function listLanguagesMenu

/**
* Retursn language files selection
* @return string
* @param string $sLang
*/
function listLangSelect( $sLangSelect = null ){
  $content = null;
  $aLanguages = throwLanguages( );
  if( isset( $aLanguages ) && is_array( $aLanguages ) ){
    foreach( $aLanguages as $sLang => $sFile ){
      $sSelected = ( isset( $sLangSelect ) && $sLangSelect == $sLang ) ? ' selected="selected"' : null;
      $content .= '<option value="'.$sLang.'"'.$sSelected.'>'.$sLang.'</option>';
    } // end foreach
  }
  return $content;
} // end function listLangSelect

/**
* Adds language files
* @return void
* @param array $aForm
*/
function addLanguage( $aForm ){
  global $config;
  if( is_file( $config['dir_database'].'lang_'.$aForm['sName'].'.php' ) || !is_file( $config['dir_database'].'lang_'.$aForm['sLangFrom'].'.php' ) )
    return null;

  copy( $config['dir_database'].'config_'.$aForm['sLangFrom'].'.php', $config['dir_database'].'config_'.$aForm['sName'].'.php' );
  copy( $config['dir_database'].'lang_'.$aForm['sLangFrom'].'.php', $config['dir_database'].'lang_'.$aForm['sName'].'.php' );
  saveVariables( Array( 'start_page' => '' ), $config['dir_database'].'config_'.$aForm['sName'].'.php' );

  if( isset( $_FILES['aFile']['name'] ) && is_uploaded_file( $_FILES['aFile']['tmp_name'] ) ){
    include $config['dir_database'].'lang_'.$aForm['sLangFrom'].'.php';
    $aFile = file( $_FILES['aFile']['tmp_name'] );
    $iCount = count( $aFile );
    for( $i = 0; $i < $iCount; $i++ ){
      foreach( $lang as $sKey => $sValue ){
        if( preg_match( '/lang'."\['".$sKey."'\]".' /', $aFile[$i] ) && strstr( $aFile[$i], '=' ) && strstr( $aFile[$i], ';' ) ){
          $lang[$sKey] = str_replace( '";', '', substr( strstr( rtrim( $aFile[$i] ), '"' ), 1 ) );
          $bFound = true;
        }
      } // end foreach
    } // end for
    if( isset( $bFound ) )
      saveVariables( $lang, $config['dir_database'].'lang_'.$aForm['sName'].'.php', 'lang' );  
  }

  if( isset( $aForm['iClone'] ) ){
    $oSql = Sql::getInstance( );
    
    cloneDataInDatabase( 'sliders', $aForm['sLangFrom'], $aForm['sName'], 'iSlider' );
    cloneDataInDatabase( 'widgets', $aForm['sLangFrom'], $aForm['sName'], 'iWidget' );
    cloneDataInDatabase( 'tags', $aForm['sLangFrom'], $aForm['sName'], 'iTag' );
    $oSql->query( 'UPDATE tags SET 	sUrl = 	sUrl ||  "-'.$aForm['sName'].'" WHERE sLang = "'.$aForm['sName'].'"' );

    $oQuery = $oSql->getQuery( 'SELECT * FROM pages WHERE sLang = "'.$aForm['sLangFrom'].'" ORDER BY iPageParent ASC' );
    unset( $sFields, $aFields );
    $i = 0;
    while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
      $iPageOld = $aData['iPage'];
      unset( $aData['iPage'] );
      $aData['sLang'] = $aForm['sName'];
      
      if( isset( $aPagesIds[$aData['iPageParent']] ) ){
        $aData['iPageParent'] = $aPagesIds[$aData['iPageParent']];
      }

      foreach( $aData as $sKey => $sValue ){
        $aData[$sKey] = $oSql->quote( $sValue );
        if( !isset( $aFields[$sKey] ) ){
          $aFields[$sKey] = $sKey;
        }
      } // end foreach
      if( isset( $aFields ) && is_array( $aFields ) && !isset( $sFields ) )
        $sFields = implode( ', ', $aFields );

      $oSql->query( 'INSERT INTO pages ( '.$sFields.' ) VALUES ( '.implode( ', ', $aData ).' )' );
      $iNewId = isset( $iNewId ) ? ( $iNewId + 1 ) : $oSql->lastInsertId( );
      $aPagesIds[$iPageOld] = $iNewId;
      $i++;
      /*if( $i > 2000 ) break; */
    } // end while

    $oQuery = $oSql->getQuery( 'SELECT files.* FROM files, pages WHERE pages.iPage=files.iPage AND pages.sLang = "'.$aForm['sLangFrom'].'"' );
    unset( $sFields, $aFields );
    $i = 0;
    while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
      unset( $aData['iFile'] );
      $iPage = $aData['iPage'];
      foreach( $aData as $sKey => $sValue ){
        $aData[$sKey] = $oSql->quote( $sValue );
        if( !isset( $aFields[$sKey] ) ){
          $aFields[$sKey] = $sKey;
        }
      } // end foreach
      if( isset( $aFields ) && is_array( $aFields ) )
        $sFields = implode( ', ', $aFields );

      if( isset( $aPagesIds[$iPage] ) ){
        $aData['iPage'] = $aPagesIds[$iPage];
        $oSql->query( 'INSERT INTO files ( '.$sFields.' ) VALUES ( '.implode( ', ', $aData ).' )' );
        $i++;
        /*if( $i > 4000 ) break; */
      }
    } // end while
    // clone data functions
  }

  clearCache( );  
} // end function addLanguage

/**
* Deletes language files
* @return void
* @param string $sLanguage
*/
function deleteLanguage( $sLanguage ){
  global $config;

  if( is_file( $config['dir_database'].'lang_'.$sLanguage.'.php' ) )
    unlink( $config['dir_database'].'lang_'.$sLanguage.'.php' );
  if( is_file( $config['dir_database'].'config_'.$sLanguage.'.php' ) )
    unlink( $config['dir_database'].'config_'.$sLanguage.'.php' );

  $oSql = Sql::getInstance( );
  $oFile = FilesAdmin::getInstance( );

  $oQuery = $oSql->getQuery( 'SELECT iPage FROM pages WHERE sLang = "'.$sLanguage.'"' );
  while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
    $aDeletePages[$aData['iPage']] = $aData['iPage'];
  } // end while
  if( isset( $aDeletePages ) ){
    $oSql->query( 'DELETE FROM pages WHERE sLang = "'.$sLanguage.'"' );
    $oFile->deleteFiles( $aDeletePages );
  }

  $oQuery = $oSql->getQuery( 'SELECT sFileName FROM sliders WHERE sLang = "'.$sLanguage.'"' );
  while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
    if( !empty( $aData['sFileName'] ) && is_file( 'files/'.$aData['sFileName'] ) ){
      $aFilesDelete[] = $aData['sFileName'];
    }
  } // end while
  $oSql->query( 'DELETE FROM sliders WHERE sLang = "'.$sLanguage.'"' );
  $oSql->query( 'DELETE FROM widgets WHERE sLang = "'.$sLanguage.'"' );

  if( isset( $aFilesDelete ) ){
    foreach( $aFilesDelete as $sFileName ){
      $oFile->deleteFilesFromDirs( $sFileName );    
    } // end foreach
  }
  clearCache( );
} // end function deleteLanguage

/**
* Clone data in database
* @return void
* @param string $sTable
* @param string $sLangFrom
* @param string $sLangTo
* @param string $sPrimaryKey
*/
function cloneDataInDatabase( $sTable, $sLangFrom, $sLangTo, $sPrimaryKey = null ){
  $oSql = Sql::getInstance( );
  $oQuery = $oSql->getQuery( 'SELECT * FROM '.$sTable.' WHERE sLang = "'.$sLangFrom.'"' );
  unset( $sFields, $aFields );
  while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
    if( isset( $sPrimaryKey ) && isset( $aData[$sPrimaryKey] ) )
      unset( $aData[$sPrimaryKey] );
    $aData['sLang'] = $sLangTo;
    foreach( $aData as $sKey => $sValue ){
      $aData[$sKey] = $oSql->quote( $sValue );
      if( !isset( $aFields[$sKey] ) ){
        $aFields[$sKey] = $sKey;
      }
    } // end foreach
    if( isset( $aFields ) && is_array( $aFields ) && !isset( $sFields ) )
      $sFields = implode( ', ', $aFields );
    $oSql->query( 'INSERT INTO '.$sTable.' ( '.$sFields.' ) VALUES ( '.implode( ', ', $aData ).' )' );
  } // end while
} // end function cloneDataInDatabase
?>