<?php
final class PagesAdmin extends Pages
{
  private static $oInstance = null;
  private $aPageChildrens = null;
  private $aSelects = null;
  public $aLinksIds = null;

  public static function getInstance( ){  
    if( !isset( self::$oInstance ) ){  
      self::$oInstance = new PagesAdmin( );  
    }  
    return self::$oInstance;  
  } // end function getInstance

  /**
  * Constructor
  * @return void
  */
  private function __construct( ){
    global $config;
    if( !is_file( $config['dir_database'].'cache/links_ids' ) || !is_file( $config['dir_database'].'cache/links' ) )
      $this->generateLinks( );
    if( is_file( $config['dir_database'].'cache/links_ids' ) )
      $this->aLinksIds = unserialize( file_get_contents( $config['dir_database'].'cache/links_ids' ) );
  } // end function __construct

  /**
  * Returns a list of pages in form of a HTML select
  * @return string
  * @param int $iPageSelected
  * @param bool $bMainOnly
  */
  public function listPagesSelectAdmin( $iPageSelected, $bMainOnly = true, $bDisableHiddenPages = true ){
    global $config;

    if( isset( $iPageSelected ) && $iPageSelected > 0 && isset( $bMainOnly ) ){
      $oSql = Sql::getInstance( );
      if( $oSql->getColumn( 'SELECT iPage FROM pages WHERE iPage = "'.$iPageSelected.'" AND iPageParent > 0 AND sLang = "'.$config['language'].'"' ) == $iPageSelected )
        $bMainOnly = null;
    }

    $sType = isset( $bMainOnly ) ? 'main' : 'all';
    $content = null;
    if( isset( $this->aSelects[$sType] ) ){
      $content = $this->aSelects[$sType];
    }
    else{
      $oSql = Sql::getInstance( );
      foreach( $config['pages_menus'] as $iMenu => $sMenu ){
        $oQuery = $oSql->getQuery( 'SELECT iStatus, iPage, iSubpages, sName FROM pages WHERE iPageParent = 0 AND sLang = "'.$config['language'].'" AND iMenu = "'.$iMenu.'" ORDER BY iPosition ASC' );
        $i = 0;
        while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
          if( $i == 0 )
            $content .= '<option value="0" disabled="disabled" style="color:#999;">- '.$config['pages_menus'][$iMenu].' -</option>';
          $content .= '<option'.( $aData['iStatus'] == 0 ? ' style="color:#bbb;"' : null ).' value="'.$aData['iPage'].'">'.$aData['sName'].'</option>';
          if( !isset( $bMainOnly ) )
            $content .= $this->listSubPagesSelectAdmin( $iPageSelected, $aData['iPage'], $aData['iSubpages'] );
          $i++;
        } // end while
      } // end foreach
      $this->aSelects[$sType] = $content;
    }
    
    if( isset( $content ) ){
      $config['main_only'] = isset( $bMainOnly ) ? true : null;
      if( isset( $iPageSelected ) && $iPageSelected > 0 )
        return str_replace( 'value="'.$iPageSelected.'"', 'value="'.$iPageSelected.'" selected="selected"', $content );
      else
        return $content;
    }
  } // end function listPagesSelectAdmin

  /**
  * Returns a list of subpages in form of a HTML select
  * @return string
  * @param int $iPageSelected
  * @param int $iPageParent
  * @param int $iSubpagesType
  * @param int $iDepth
  */
  public function listSubPagesSelectAdmin( $iPageSelected, $iPageParent, $iSubpagesType, $iDepth = 1 ){
    $oSql = Sql::getInstance( );
    $content = null;
    $oQuery = $oSql->getQuery( 'SELECT iStatus, iPage, iSubpages, sName FROM pages WHERE iPageParent = "'.$iPageParent.'" ORDER BY '.( $iSubpagesType == 4 ? 'iTime DESC' : 'iPosition ASC, sName COLLATE NOCASE ASC' ) );
    $sSeparator = str_repeat( '&nbsp;&nbsp;', $iDepth );
    while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
      $content .= '<option'.( $aData['iStatus'] == 0 ? ' style="color:#bbb;"' : null ).' value="'.$aData['iPage'].'">'.$sSeparator.$aData['sName'].'</option>'.$this->listSubPagesSelectAdmin( $iPageSelected, $aData['iPage'], $aData['iSubpages'], $iDepth + 1 );
    } // end while
    return $content;
  } // end function listSubPagesSelectAdmin

  /**
  * Returns a list of pages in form of a HTML select multi
  * @return string
  * @param string $sSqlQuery
  * @param int $iPage
  * @param bool $bMainOnly
  */
  public function listPagesSelectMultiAdmin( $sSqlQuery, $sPageKey = 'iPage', $bMainOnly = true ){
    if( isset( $sSqlQuery ) ){
      $oSql = Sql::getInstance( );
      $oQuery = $oSql->getQuery( $sSqlQuery );
      while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
        if( $aData['iPageParent'] > 0 )
          $bMainOnly = null;
        $aPages[] = $aData[$sPageKey];
      } // end while
    }
    $sPagesSelect = $this->listPagesSelectAdmin( null, $bMainOnly );
    if( isset( $aPages ) ){
      foreach( $aPages as $iPage ){
        $sPagesSelect = str_replace( 'value="'.$iPage.'"', 'value="'.$iPage.'" selected="selected"', $sPagesSelect );  
      } // end foreach
    }
    return $sPagesSelect;  
  } // end function listPagesSelectMultiAdmin

  /**
  * Returns the list of pages
  * @return string
  * @param array $aParametersExt
  * Default options: iDepth, iMenu, iPageParent
  */
  public function listPagesAdmin( $aParametersExt = null ){
    global $lang, $config;

    $content = null;
    $sWhere = null;
    $oSql = Sql::getInstance( );
    if( !isset( $aParametersExt['iDepth'] ) )
      $aParametersExt['iDepth'] = 0;
    
    if( isset( $aParametersExt['iMenu'] ) && isset( $config['pages_menus'][$aParametersExt['iMenu']] ) )
      $sWhere = ' AND iMenu = "'.$aParametersExt['iMenu'].'" AND iPageParent = 0 ';
    elseif( isset( $aParametersExt['iPageParent'] ) && is_numeric( $aParametersExt['iPageParent'] ) )
      $sWhere = ' AND iPageParent = "'.$aParametersExt['iPageParent'].'" ';
    elseif( isset( $aParametersExt['iPageNews'] ) && is_numeric( $aParametersExt['iPageNews'] ) ){
      $sWhere = ' AND iPageParent = "'.$aParametersExt['iPageNews'].'" ';
      if( !isset( $_GET['sSort'] ) )
        $sSort = 'iTime DESC';
    }

    if( !isset( $sSort ) && isset( $aParametersExt['iSubpages'] ) && $aParametersExt['iSubpages'] == 4 ){
      $sSort = 'iTime DESC';
    }
    elseif( !isset( $sSort ) && isset( $_GET['sSort'] ) ){
      if( $_GET['sSort'] == 'id' )
        $sSort = 'iPage ASC';
      elseif( $_GET['sSort'] == 'name' )
        $sSort = 'sName COLLATE NOCASE ASC, iPosition ASC';
    }
    if( !isset( $sSort ) )
      $sSort = 'iPosition ASC, sName COLLATE NOCASE ASC';

    $oQuery = $oSql->getQuery( 'SELECT * FROM pages WHERE sLang = "'.$config['language'].'"'.$sWhere.' ORDER BY '.$sSort );
    while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
      $content .= '<tr class="l'.$aParametersExt['iDepth'].'"><td class="id">'.$aData['iPage'].'</td><th class="name">
            <a href="?p=pages-form&amp;iPage='.$aData['iPage'].'">'.$aData['sName'].'</a>'.( ( $aData['iHideSubpagesList'] == 1 ) ? ' <a href="?p=pages&amp;iPageNews='.$aData['iPage'].'" class="expand" title="'.$lang['More'].'">'.$lang['More'].'</a>' : null ).' <a href="./'.( ( $config['start_page'] == $aData['iPage'] ) ? null : $this->aLinksIds[$aData['iPage']] ).'" target="_blank" class="preview" title="'.$lang['Preview'].'">'.$lang['Preview'].'</a>
            <ul>
              <li class="status custom"><input type="checkbox" class="status" name="aStatus['.$aData['iPage'].']" id="aStatus['.$aData['iPage'].']" value="1"'.( ( $aData['iStatus'] == 1 ) ? ' checked="checked"' : null ).' /><label for="aStatus['.$aData['iPage'].']">'.$lang['Status'].'</label></li>
            </ul>
          </th><td class="position">
            <input type="text" name="aPositions['.$aData['iPage'].']" value="'.$aData['iPosition'].'" class="numeric" size="3" maxlength="4" />
          </td><td class="options">
            <a href="?p=pages-form&amp;iPage='.$aData['iPage'].'" class="edit" title="'.$lang['Edit'].'">'.$lang['Edit'].'</a>
            '.( ( !isset( $config['disable_main_page_delete'] ) || $aData['iPageParent'] > 0 ) ? '<a href="?p=pages&amp;iItemDelete='.$aData['iPage'].'" class="delete" title="'.$lang['Delete'].'" onclick="return del( this )">'.$lang['Delete'].'</a> ' : null ).'
             
          </td>
        </tr>';
      if( $aData['iHideSubpagesList'] == 0 )
        $content .= $this->listPagesAdmin( Array( 'iPageParent' => $aData['iPage'], 'iDepth' => ( $aParametersExt['iDepth'] + 1 ), 'iSubpages' => $aData['iSubpages'] ) );
    } // end while

    if( isset( $content ) ){
      if( isset( $aParametersExt['iMenu'] ) && isset( $config['pages_menus'][$aParametersExt['iMenu']] ) )
        $content = '<tr class="type"><td colspan="4">'.$config['pages_menus'][$aParametersExt['iMenu']].'</td></tr>'.$content;
      return $content;
    }
  } // end function listPagesAdmin

  /**
  * Returns the list of pages
  * @return string
  * @param string $sSearch
  * @param array $aParametersExt
  * Default options: iDepth, iMenu, iPageParent
  */
  public function listPagesAdminSearch( $sSearch, $aParametersExt = null ){
    global $lang, $config;

    $aWords = getWordsFromPhrase( $sSearch );
    if( isset( $aWords ) ){
      foreach( $aWords as $sWord ){
        $aWhere[] = 'sSearchField LIKE \'%'.str_replace( '\'', '\'\'', $sWord ).'%\'';
      } // end foreach

      $content = null;
      $oSql = Sql::getInstance( );
      $oQuery = $oSql->getQuery( 'SELECT *, ( sName || sTitle || sUrl || sDescriptionMeta || sDescriptionShort || sDescriptionFull || sKeywords ) AS sSearchField FROM pages WHERE sLang = "'.$config['language'].'" AND '.implode( ' AND ', $aWhere ).' ORDER BY '.( isset( $_GET['sSort'] ) ? ( ( $_GET['sSort'] == 'id' ) ? 'iPage ASC' : 'sName COLLATE NOCASE ASC, iPosition ASC' ) : 'iPosition ASC, sName COLLATE NOCASE ASC' ) );
      while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
        $content .= '<tr class="l0"><td class="id">'.$aData['iPage'].'</td><th class="name">
              <a href="?p=pages-form&amp;iPage='.$aData['iPage'].'">'.$aData['sName'].'</a> <a href="./'.( ( $config['start_page'] == $aData['iPage'] ) ? null : $this->aLinksIds[$aData['iPage']] ).'" target="_blank" class="preview" title="'.$lang['Preview'].'">'.$lang['Preview'].'</a>
              <ul>
                <li class="status custom"><input type="checkbox" class="status" name="aStatus['.$aData['iPage'].']" id="aStatus['.$aData['iPage'].']" value="1"'.( ( $aData['iStatus'] == 1 ) ? ' checked="checked"' : null ).' /><label for="aStatus['.$aData['iPage'].']">'.$lang['Status'].'</label></li>
              </ul>
            </th><td class="position">
              <input type="text" name="aPositions['.$aData['iPage'].']" value="'.$aData['iPosition'].'" class="numeric" size="3" maxlength="4" />
            </td><td class="options">
              <a href="?p=pages-form&amp;iPage='.$aData['iPage'].'" class="edit" title="'.$lang['Edit'].'">'.$lang['Edit'].'</a>
              '.( ( !isset( $config['disable_main_page_delete'] ) || $aData['iPageParent'] > 0 ) ? '<a href="?p=pages&amp;iItemDelete='.$aData['iPage'].'" class="delete" title="'.$lang['Delete'].'" onclick="return del( this )">'.$lang['Delete'].'</a> ' : null ).'
            </td>
          </tr>';
      } // end while

      if( isset( $content ) ){
        return $content;
      }
    }
  } // end function listPagesAdminSearch

  /**
  * Saves page's position and status
  * @return void
  * @param array $aForm
  */
  public function savePages( $aForm ){
    global $config;

    if( isset( $aForm['aPositions'] ) && is_array( $aForm['aPositions'] ) ){
      
      clearCache( );

      $oSql = Sql::getInstance( );
      $oQuery = $oSql->getQuery( 'SELECT iPage, iPosition, iStatus FROM pages WHERE sLang = "'.$config['language'].'"' );
      while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
        if( isset( $aForm['aPositions'][$aData['iPage']] ) && !isset( $aChanged[$aData['iPage']] ) ){
          $iStatus = isset( $aForm['aStatus'][$aData['iPage']] ) ? 1 : 0;
          if( $iStatus != $aData['iStatus'] ){
            $aChanged[$aData['iPage']] = true;
            if( $iStatus == 0 ){
              $this->generatePageAllChildrens( $aData['iPage'] );
              if( isset( $this->aPageChildrens ) ){
                foreach( $this->aPageChildrens as $iPage ){
                  if( isset( $aForm['aStatus'][$iPage] ) ){
                    unset( $aForm['aStatus'][$iPage] );
                    $aChanged[$iPage] = true;
                  }
                } // end foreach
              }
            }
          }

          if( !isset( $aChanged[$aData['iPage']] ) && $aForm['aPositions'][$aData['iPage']] != $aData['iPosition'] ){
            $aChanged[$aData['iPage']] = true;
          }
        }
      } // end while

      if( isset( $aChanged ) ){
        foreach( $aChanged as $iPage => $bValue ){
          $oSql->query( 'UPDATE pages SET iPosition = "'.( (int) $aForm['aPositions'][$iPage] ).'", iStatus = '.( isset( $aForm['aStatus'][$iPage] ) ? 1 : 0 ).' WHERE iPage = '.$iPage );
        } // end foreach
      }
    }
  } // end function savePages


  /**
  * Returns id's of all subpages of a given page
  * @return void
  * @param int  $iPage
  */
  private function throwSubpagesIdAdmin( $iPage ){
    $iCount = count( $this->aPagesChildrens[$iPage] );
    for( $i = 0; $i < $iCount; $i++ ){
      $this->mData[$this->aPagesChildrens[$iPage][$i]] = true;
      if( isset( $this->aPagesChildrens[$this->aPagesChildrens[$iPage][$i]] ) ){
        $this->throwSubpagesIdAdmin( $this->aPagesChildrens[$iPage][$i] );
      }
    } // end for
  } // end function throwSubpagesIdAdmin

  /**
  * Saves page data including data of all attached images and files
  * @return int
  * @param array  $aForm
  */
  public function savePage( $aForm ){
    global $config;

    clearCache( );

    $oFile = FilesAdmin::getInstance( );
    $oSql = Sql::getInstance( );
    $aForm = changeMassTxt( $aForm, 'ndnl', Array( 'sDescriptionShort', 'nds ndnl' ), Array( 'sDescriptionFull', 'nds ndnl' ), Array( 'sDescriptionMeta', 'Nds' ) );
    $aForm['iStatus'] = isset( $aForm['iStatus'] ) ? 1 : 0;
    $aForm['iIdInLink'] = isset( $aForm['iIdInLink'] ) ? 1 : 0;
    $aForm['iHideSubpagesList'] = isset( $aForm['iHideSubpagesList'] ) ? 1 : 0;

    $aForm['iTime'] = '';
    if( !empty( $aForm['sDate'] ) )
      $aForm['iTime'] = (int) abs( strtotime( ( strstr( $aForm['sDate'], ':' ) ? $aForm['sDate'].':00' : $aForm['sDate'].' 00:00:00' ) ) );
    
    if( isset( $aForm['iPageParent'] )&& is_numeric( $aForm['iPageParent'] ) && $aForm['iPageParent'] != $aForm['iPage'] ){
      $aDataParent = $oSql->throwAll( 'SELECT iMenu, iStatus FROM pages WHERE iPage = '.$aForm['iPageParent'] );
      $aForm['iMenu'] = $aDataParent['iMenu'];
      if( $aForm['iStatus'] == 1 && $aDataParent['iStatus'] == 0 )
        $aForm['iStatus'] = 0;
    }
    else
      $aForm['iPageParent'] = 0;

    if( !empty( $aForm['iRedirect'] ) && is_numeric( $aForm['iRedirect'] ) ){
      $aForm['sRedirect'] = $aForm['iRedirect'];
    }

    if( isset( $aForm['iPosition'] ) && !is_numeric( $aForm['iPosition'] ) )
      $aForm['iPosition'] = 0;

    if( isset( $aForm['iPage'] ) && is_numeric( $aForm['iPage'] ) ){
      if( $aForm['iStatus'] == 0 && $aForm['iStatus'] != $oSql->getColumn( 'SELECT iStatus FROM pages WHERE iPage = '.$aForm['iPage'] ) ){
        $this->generatePageAllChildrens( $aForm['iPage'] );
        if( isset( $this->aPageChildrens ) ){
          foreach( $this->aPageChildrens as $iPage ){
            $oSql->query( 'UPDATE pages SET iStatus = 0 WHERE iPage = '.$iPage );
          } // end foreach
        }
      }
      $oSql->update( 'pages', $aForm, Array( 'iPage' => $aForm['iPage'] ), true );
      $oSql->query( 'DELETE FROM pages_tags WHERE iPage = '.$aForm['iPage'] );
    }
    else{
      $aForm['sLang'] = $config['language'];
      unset( $aForm['iPage'] );
      $aForm['iPage'] = $oSql->insert( 'pages', $aForm, true );
    }

    if( ( isset( $aForm['iChangedFiles'] ) && $aForm['iChangedFiles'] == 1 ) || isset( $aForm['aDirFiles'] ) || isset( $aForm['aFilesDelete'] ) ){
      if( isset( $aForm['aDirFiles'] ) )
        $oFile->addFilesFromServer( $aForm, $aForm['iPage'] );

      if( isset( $aForm['iChangedFiles'] ) && $aForm['iChangedFiles'] == 1 || isset( $aForm['aFilesDelete'] ) ){
        if( isset( $aForm['aFilesDescription'] ) )
          $oFile->saveFiles( $aForm, $aForm['iPage'] );
      }

      $oFile->setDefaultImage( $aForm['iPage'], isset( $aForm['iDefaultImage'] ) ? $aForm['iDefaultImage'] : null );
    }

    if( isset( $aForm['aTags'] ) ){
      foreach( $aForm['aTags'] as $iTag ){
        if( is_numeric( $iTag ) )
          $oSql->query( 'INSERT INTO pages_tags ( iPage, iTag ) VALUES ( "'.$aForm['iPage'].'", "'.$iTag.'" )' );
      } // end foreach
    }

    return $aForm['iPage'];
  } // end function savePage 

  /**
  * Returns all main pages childrens
  * @return null
  * @param int $iPageParent
  * @param bool $bUnset
  */
  private function generatePageAllChildrens( $iPageParent = null, $bUnset = true ){
    if( isset( $bUnset ) )
      unset( $this->aPageChildrens );
    $oSql = Sql::getInstance( );
    $oQuery = $oSql->getQuery( 'SELECT iPage FROM pages WHERE iPageParent = "'.$iPageParent.'"' );
    while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
      $this->aPageChildrens[$aData['iPage']] = $aData['iPage'];
      $this->generatePageAllChildrens( $aData['iPage'], null );
    }
  } // end function generatePageAllChildrens

  /**
  * Deletes a page and its subpages
  * @return void
  * @param int $iPage
  */
  public function deletePage( $iPage ){
    $oSql = Sql::getInstance( );
    $oFile = FilesAdmin::getInstance( );

    clearCache( );
    $this->generatePageAllChildrens( $iPage );
    $this->aPageChildrens[$iPage] = $iPage;
    foreach( $this->aPageChildrens as $iPage ){
      $oSql->query( 'DELETE FROM pages WHERE iPage = '.$iPage );
      $oSql->query( 'DELETE FROM pages_tags WHERE iPage = '.$iPage );
    } // end foreach
    $oFile->deleteFiles( $this->aPageChildrens );
  } // end function deletePage

  /**
  * Returns page data
  * @return array
  * @param int  $iPage
  */
  public function throwPageAdmin( $iPage ){
    global $config;

    $oSql = Sql::getInstance( );
    $aData = $oSql->throwAll( 'SELECT * FROM pages WHERE iPage = '.$iPage );
    if( isset( $aData ) && is_array( $aData ) ){
      return $aData;
    }
  } // end function throwPageAdmin

  /**
  * Clone pages data
  * @return int
  * @param int $iPage
  */
  function clonePage( $iPage ){
    $oSql = Sql::getInstance( );

    $aData = $oSql->throwAll( 'SELECT * FROM pages WHERE iPage = '.$iPage.' LIMIT 1' );
    if( isset( $aData ) && is_array( $aData ) ){
      unset( $aData['iPage'] );
      $aData['iStatus'] = 0;

      foreach( $aData as $sKey => $sValue ){
        $aData[$sKey] = $oSql->quote( $sValue );
        if( !isset( $aFields[$sKey] ) ){
          $aFields[$sKey] = $sKey;
        }
      } // end foreach

      if( isset( $aFields ) && is_array( $aFields ) && !isset( $sFields ) )
        $sFields = implode( ', ', $aFields );

      $oSql->query( 'INSERT INTO pages ( '.$sFields.' ) VALUES ( '.implode( ', ', $aData ).' )' );
      $iPageNew = $oSql->lastInsertId( );
      unset( $aFields, $sFields );

      $oQuery = $oSql->getQuery( 'SELECT * FROM files WHERE iPage = '.$iPage );
      while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
        unset( $aData['iFile'] );
        foreach( $aData as $sKey => $sValue ){
          $aData[$sKey] = $oSql->quote( $sValue );
          if( !isset( $aFields[$sKey] ) ){
            $aFields[$sKey] = $sKey;
          }
        } // end foreach
        if( isset( $aFields ) && is_array( $aFields ) && !isset( $sFields ) )
          $sFields = implode( ', ', $aFields );
        $aData['iPage'] = $iPageNew;
        $oSql->query( 'INSERT INTO files ( '.$sFields.' ) VALUES ( '.implode( ', ', $aData ).' )' );
      } // end while

      return $iPageNew;
    }
  } // end function clonePage

};
?>