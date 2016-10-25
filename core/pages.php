<?php
if( !defined( 'CUSTOMER_PAGE' ) && !defined( 'ADMIN_PAGE' ) )
  exit( 'Quick.Cms.Ext by OpenSolution.org' );

class Pages
{

  public $aPagesParentsMenus = null;
  public $aPages = null;
  public $sLanguageBackEndChoosed = null;
  public $aPagesChildrens = null;
  public $aPagesAllChildrens = null;
  public $aPagesParents = null;
  private static $oInstance = null;

  public static function getInstance( ){
    if( !isset( self::$oInstance ) ){  
      self::$oInstance = new Pages( );  
    }  
    return self::$oInstance;  
  } // end function getInstance

  /**
  * Constructor
  * @return void
  */
  private function __construct( ){
    $this->generateCache( );
  } // end function __construct

  /**
  * Generates cache variables
  * @return void
  */
  public function generateCache( ){
    global $config;

    if( !is_file( $config['dir_database'].'cache/links_ids' ) || !is_file( $config['dir_database'].'cache/links' ) )
      $this->generateLinks( );

    $iStatus = getStatus( );
    if( $iStatus == 0 )
      $config['enable_cache'] = null;

    if( !isset( $config['pages_links'] ) )
      $config['pages_links'] = unserialize( file_get_contents( $config['dir_database'].'cache/links' ) );
    if( isset( $config['enable_cache'] ) && is_file( $config['dir_database'].'cache/'.$config['language'].'_pages' ) ){
      $this->aPages = unserialize( file_get_contents( $config['dir_database'].'cache/'.$config['language'].'_pages' ) );
      $this->aPagesChildrens = unserialize( file_get_contents( $config['dir_database'].'cache/'.$config['language'].'_pages_childrens' ) );
      $this->aPagesParents = unserialize( file_get_contents( $config['dir_database'].'cache/'.$config['language'].'_pages_parents' ) );
      $this->aPagesParentsMenus = unserialize( file_get_contents( $config['dir_database'].'cache/'.$config['language'].'_pages_parents_menus' ) );
      $this->aPagesAllChildrens = unserialize( file_get_contents( $config['dir_database'].'cache/'.$config['language'].'_pages_all_childrens' ) );
      return true;
    }

    $aLinksIds = unserialize( file_get_contents( $config['dir_database'].'cache/links_ids' ) );
    $oSql = Sql::getInstance( );
    $oQuery = $oSql->getQuery( 'SELECT iPage, iPageParent, iTime, iMenu, iSubpages, sName, sNameMenu, sTitle, sUrl, iTheme, sRedirect, sDescriptionMeta, sDescriptionShort FROM pages WHERE iStatus >= '.$iStatus.' AND sLang = "'.$config['language'].'" ORDER BY iPosition ASC, sName COLLATE NOCASE ASC' );
    while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
      if( isset( $aData['sDescriptionShort'] ) ){
        $aData['sDescriptionShort'] = stripslashes( $aData['sDescriptionShort'] );
      }

      $this->aPages[$aData['iPage']] = $aData;

      $this->aPages[$aData['iPage']]['sLinkName'] = $aLinksIds[$aData['iPage']];
      if( $config['start_page'] == $aData['iPage'] && $config['language'] == $config['default_language'] ){
        $this->aPages[$aData['iPage']]['sLinkRaw'] = $this->aPages[$aData['iPage']]['sLinkName'];
        $this->aPages[$aData['iPage']]['sLinkName'] = './';
      }

      if( $aData['iPageParent'] > 0 ){
        $this->aPagesChildrens[$aData['iPageParent']][] = $aData['iPage'];
        $this->aPagesParents[$aData['iPage']] = $aData['iPageParent'];
      }
      else{
        if( isset( $aData['iMenu'] ) )
          $this->aPagesParentsMenus[$aData['iMenu']][] = $aData['iPage'];
      }
    } // end while

    $this->generateAllChildrens( );

    if( isset( $config['enable_cache'] ) ){
      file_put_contents( $config['dir_database'].'cache/'.$config['language'].'_pages', serialize( $this->aPages ) );
      file_put_contents( $config['dir_database'].'cache/'.$config['language'].'_pages_childrens', serialize( isset( $this->aPagesChildrens ) ? $this->aPagesChildrens : null ) );
      file_put_contents( $config['dir_database'].'cache/'.$config['language'].'_pages_parents', serialize( isset( $this->aPagesParents ) ? $this->aPagesParents : null ) );
      file_put_contents( $config['dir_database'].'cache/'.$config['language'].'_pages_parents_menus', serialize( isset( $this->aPagesParentsMenus ) ? $this->aPagesParentsMenus : null ) );
      file_put_contents( $config['dir_database'].'cache/'.$config['language'].'_pages_all_childrens', serialize( isset( $this->aPagesAllChildrens ) ? $this->aPagesAllChildrens : null ) );
    }
  } // end function generateCache

  /**
  * Generates links
  * @return void
  */
  public function generateLinks( ){
    global $config;

    $oSql = Sql::getInstance( );
    $oQuery = $oSql->getQuery( 'SELECT sUrl, sName, sLang, iPage, iIdInLink FROM pages ORDER BY iPosition ASC, iPage ASC' );

    while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
      $aData['iPage'] = (int) $aData['iPage'];
      $sUrl = ( isset( $config['language_separator'] ) ? $aData['sLang'].$config['language_separator'] : null ).change2Url( !empty( $aData['sUrl'] ) ? $aData['sUrl'] : $aData['sName'] );

      if( isset( $aLinks[$sUrl] ) || $aData['iIdInLink'] == 1 ){
        $aLinksIds[$aData['iPage']] = $sUrl.','.$aData['iPage'].'.html';
        $aLinks[$sUrl.','.$aData['iPage']] = Array( $aData['iPage'], $aData['sLang'] );
      }
      else{
        $aLinksIds[$aData['iPage']] = $sUrl.'.html';
        $aLinks[$sUrl] = Array( $aData['iPage'], $aData['sLang'] );
      }
    } // end while

    if( isset( $aLinks ) ){
      file_put_contents( $config['dir_database'].'cache/links', serialize( $aLinks ) );
      file_put_contents( $config['dir_database'].'cache/links_ids', serialize( $aLinksIds ) );
      require_once 'core/tags-admin.php';
      generateTagsLinks( );
    }
  } // end function generateLinks

  /**
  * Returns page data
  * @return array
  * @param int  $iPage
  */
  public function throwPage( $iPage ){
    global $config;

    if( isset( $this->aPages[$iPage] ) ){
      $oSql = Sql::getInstance( );
      $aData = array_merge( $this->aPages[$iPage], $oSql->throwAll( 'SELECT sDescriptionFull, iMetaRobots, sListFunction FROM pages WHERE iPage = '.$iPage ) );
      if( !empty( $aData['sDescriptionFull'] ) ){
        $aData['sDescriptionFull'] = stripslashes( $aData['sDescriptionFull'] );
        if( defined( 'CUSTOMER_PAGE' ) && !empty( $aData['sDescriptionFull'] ) ){
          preg_match_all( '/\[IMAGES=[0-9]+\]/', $aData['sDescriptionFull'], $aMatchesImages );
          if( isset( $aMatchesImages[0] ) ){
            $oFile = Files::getInstance( );
            foreach( $aMatchesImages[0] as $sValue ){
              preg_match( '/[0-9]+/', $sValue, $aType );
              if( isset( $aType[0] ) && isset( $config['images_locations'][$aType[0]] ) ){
                $aData['sDescriptionFull'] = str_replace( Array( '<p>'.$sValue.'</p>', '<div class="mrk">'.$sValue.'</div>' ), $sValue, $aData['sDescriptionFull'] );
                $sReplace = $oFile->listImages( $iPage, Array( 'iType' => $aType[0] ) );
                $aData['sDescriptionFull'] = str_replace( $sValue, ( !empty( $sReplace ) ? $sReplace : ( defined( 'DEVELOPER_MODE' ) ? '<p class="dev">THIS PAGE HAS NO IMAGES OF TYPE - '.$aType[0].'</p>' : null ) ), $aData['sDescriptionFull'] );
              }
              else{
                $aData['sDescriptionFull'] = str_replace( $sValue, ( defined( 'DEVELOPER_MODE' ) ? '<p class="dev">THERE IS NO SUCH IMAGES TYPE - '.$aType[0].'</p>' : null ), $aData['sDescriptionFull'] );
              }
            } // end foreach
          }

          preg_match_all( '/\[WIDGET=[0-9]+\]/', $aData['sDescriptionFull'], $aMatchesWidgets );
          if( isset( $aMatchesWidgets[0] ) ){
            $oFile = Files::getInstance( );
            foreach( $aMatchesWidgets[0] as $sValue ){
              preg_match( '/[0-9]+/', $sValue, $aType );
              if( isset( $aType[0] ) && isset( $config['enabled_widgets'] ) ){
                $aData['sDescriptionFull'] = str_replace( Array( '<p>'.$sValue.'</p>', '<div class="mrk">'.$sValue.'</div>' ), $sValue, $aData['sDescriptionFull'] );
                $sReplace = displayWidget( $aType[0] );
                $aData['sDescriptionFull'] = str_replace( $sValue, ( !empty( $sReplace ) ? $sReplace : ( defined( 'DEVELOPER_MODE' ) ? '<p class="dev">THERE IS NO WIDGET - '.$aType[0].'</p>' : null ) ), $aData['sDescriptionFull'] );
              }
              else{
                $aData['sDescriptionFull'] = str_replace( $sValue, ( defined( 'DEVELOPER_MODE' ) ? '<p class="dev">WIDGETS ARE DISABLED</p>' : null ), $aData['sDescriptionFull'] );
              }
            } // end foreach
          }

          if( strstr( $aData['sDescriptionFull'], '[BREAK]' ) ){
            $aData['sDescriptionFull'] = str_replace( Array( '<p>[BREAK]</p>', '<div class="mrk">[BREAK]</div>' ), '[BREAK]', $aData['sDescriptionFull'] );
            $aExp = explode( '[BREAK]', $aData['sDescriptionFull'] );
            $iPageContent = ( isset( $_GET['iPageContent'] ) && is_numeric( $_GET['iPageContent'] ) && $_GET['iPageContent'] > 0 ) ? $_GET['iPageContent'] : 1;
            if( isset( $aExp[$iPageContent - 1] ) ){
              $aData['sDescriptionFull'] = $aExp[$iPageContent - 1];
              $aData['sPages'] = countPages( count( $aExp ), 1, $iPageContent, Array( 'sUrlName' => 'iPageContent', 'sAddress' => ( isset( $aData['sLinkRaw'] ) ? $aData['sLinkRaw'] : $aData['sLinkName'] ), 'sAddressFirstPage' => ( isset( $aData['sLinkRaw'] ) ? $aData['sLinkName'] : null ) ) );
            }
          }
        }
      }
      return $aData;
    }
    else
      return null;
  } // end function throwPage

  /**
  * Lists all pages containing the searched phrase
  * @return string
  * @param string $sSearch
  * @param array $aParametersExt
  * Default options: iType, sClassName, sFunctionView, iLimitPerPage, bNoLinks, bNoFollow
  */
  public function listPagesSearch( $sSearch, $aParametersExt = null ){
    global $config;

    $oSql = Sql::getInstance( );
    $aWords = getWordsFromPhrase( $sSearch );
    if( isset( $aWords ) ){
      if( isset( $config['disable_pages_in_search_results'] ) && is_array( $config['disable_pages_in_search_results'] ) ){
        $aWhere[] = 'iPage IS NOT '.implode( ' AND iPage IS NOT ', $config['disable_pages_in_search_results'] );
      }

      foreach( $aWords as $sWord ){
        $aWhere[] = 'sSearchField LIKE \'%'.str_replace( '\'', '\'\'', $sWord ).'%\'';
      } // end foreach

      $oQuery = $oSql->getQuery( 'SELECT iPage, ( sName || sTitle || sUrl || sDescriptionMeta || sDescriptionShort || sDescriptionFull || sKeywords ) AS sSearchField FROM pages WHERE iStatus >= '.getStatus( ).' AND sLang = "'.$config['language'].'" AND '.implode( ' AND ', $aWhere ).' ORDER BY iPosition ASC, sName COLLATE NOCASE ASC' );
      while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
        if( isset( $this->aPages[$aData['iPage']] ) ){
          $aPages[] = $aData['iPage'];
        }
      } // end while
      if( isset( $aPages ) ){
        $aParametersExt['bAssingFromRequestUri'] = true;
        return $this->listPages( $aPages, $aParametersExt );
      }
    }
  } // end function listPagesSearch

  /**
  * Returns a list of pages
  * @return string
  * @param mixed $mData
  * @param array $aParametersExt
  * Default options: iType, sClassName, sFunctionView, iLimitPerPage, bNoLinks, bPagination, sUrlName, bAssingFromRequestUri
  */
  public function listPages( $mData, $aParametersExt = null ){
    global $config, $lang;

    if( is_array( $mData ) ){
      $aPages = $mData;
    }
    else{
      if( isset( $this->aPagesChildrens[$mData] ) )
        $aPages = $this->aPagesChildrens[$mData];
    }

    if( isset( $aPages ) ){
      $iCount = count( $aPages );
      if( isset( $aParametersExt['sFunctionView'] ) ){
        if( defined( 'DEVELOPER_MODE' ) && !function_exists( $aParametersExt['sFunctionView'] ) ){
          echo '<div class="msg error"><h1>There is no function <u>'.$aParametersExt['sFunctionView'].'</u></h1></div>';
        }
      }
      elseif( isset( $aParametersExt['iType'] ) ){
        $aParametersExt['sFunctionView'] = __FUNCTION__.'View'.$aParametersExt['iType'];
      }

      if( isset( $aParametersExt['iType'] ) && $aParametersExt['iType'] == 4 ){
        $aPages = $this->sortPages( $aPages, Array( 'sKey1' => 'iTime', 'sFunctionSort' => 'arsort' ) );
      }

      $sFunctionView = getFunctionName( $aParametersExt, __FUNCTION__ );
      $iLimitPerPage = isset( $aParametersExt['iLimitPerPage'] ) && is_numeric( $aParametersExt['iLimitPerPage'] ) ? $aParametersExt['iLimitPerPage'] : ( ( isset( $aParametersExt['iType'] ) && isset( $config['pages_list_'.$aParametersExt['iType']] ) ) ? $config['pages_list_'.$aParametersExt['iType']] : $config['pages_list_all'] );
      if( !isset( $aParametersExt['sUrlName'] ) )
        $aParametersExt['sUrlName'] = 'iPage';
      $aKeys = countPageNumber( $iCount, ( isset( $_GET[$aParametersExt['sUrlName']] ) ? $_GET[$aParametersExt['sUrlName']] : null ), $iLimitPerPage );
      $content = null;
      $i2 = 1;

      for( $i = $aKeys['iStart']; $i < $aKeys['iEnd']; $i++ ){
        $aParametersExt['iElement'] = $i2;
        $content .= $sFunctionView( $this->aPages[$aPages[$i]], $aParametersExt );
        $i2++;
      } // end for

      if( isset( $content ) ){
        if( $iCount > $iLimitPerPage && isset( $aParametersExt['bPagination'] ) ){
          $iPage = is_numeric( $mData ) ? $mData : $config['current_page_id'];
          if( !isset( $aParametersExt['bAssingFromRequestUri'] ) ){
            $aParametersExt['sAddress'] = ( isset( $this->aPages[$iPage]['sLinkRaw'] ) ? $this->aPages[$iPage]['sLinkRaw'] : $this->aPages[$iPage]['sLinkName'] );
            if( isset( $this->aPages[$iPage]['sLinkRaw'] ) ){
              $aParametersExt['sAddressFirstPage'] = $this->aPages[$iPage]['sLinkName'];
            }
          }
          $sPages = '<nav class="pages">'.$lang['Pages'].': <ul>'.countPages( $iCount, $iLimitPerPage, $aKeys['iPageNumber'], $aParametersExt ).'</ul></nav>';
        }
        return '<ul class="'.( isset( $aParametersExt['sClassName'] ) ? $aParametersExt['sClassName'] : ( isset( $aParametersExt['iType'] ) ? ( $aParametersExt['iType'] < 5 ? 'pages-list ':'').'pages-'.$aParametersExt['iType'] : 'pages-list no-type' ) ).'">'.$content.'</ul>'.( isset( $sPages ) ? $sPages : null );
      }
    }  
  } // end function listPages

  /**
  * Returns a sitemap
  * @return string
  * @param array $aParametersExt
  * Default options: iPageParent, iDepth
  */
  public function listSiteMap( $aParametersExt = null ){
    global $config;
    if( !isset( $aParametersExt['iPageParent'] ) ){
      foreach( $this->aPages as $iPage => $aData ){
        if( !isset( $this->aPagesParents[$iPage] ) && $aData['iSubpages'] != 0 && !isset( $config['disable_pages_in_sitemap'][$iPage] ) )
          $aPages[] = $iPage;
      }
    }
    else
      $aPages = isset( $this->aPagesChildrens[$aParametersExt['iPageParent']] ) ? $this->aPagesChildrens[$aParametersExt['iPageParent']] : null;

    if( !isset( $aParametersExt['iDepth'] ) )
      $aParametersExt['iDepth'] = 0;
    
    if( isset( $aPages ) ){
      $content = null;
      foreach( $aPages as $iPage ){
        if( !isset( $config['disable_pages_in_sitemap'][$iPage] ) ){
          $content .= '<li><a href="'.$this->aPages[$iPage]['sLinkName'].'">'.$this->aPages[$iPage]['sName'].'</a>'.( ( isset( $this->aPagesChildrens[$iPage] ) && $this->aPages[$iPage]['iSubpages'] != 4 ) ? $this->listSiteMap( Array( 'iPageParent' => $iPage, 'iDepth' => ( $aParametersExt['iDepth'] + 1 ) ) ) : null ).'</li>';
        }
      } // end foreach

      if( isset( $content ) ){
        return isset( $aParametersExt['iPageParent'] ) ? '<ul>'.$content.'</ul>' : '<ul id="site-map">'.$content.'</ul>';
      }
    }
  } // end function listSiteMap

  /**
  * Lists all pages and generates a xml file
  * @return string
  */
  public function listPagesSiteMap2Xml( ){
    global $config;

    $aLinksIds = unserialize( file_get_contents( $config['dir_database'].'cache/links_ids' ) );
    $content = null;
    $oSql = Sql::getInstance( );
    $oQuery = $oSql->getQuery( 'SELECT iPage FROM pages WHERE iStatus > 0 ORDER BY iPosition ASC, sName COLLATE NOCASE ASC' );
    while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
      if( isset( $aLinksIds[$aData['iPage']] ) && !isset( $config['disable_pages_in_sitemap_xml'][$aData['iPage']] ) ){
        if( $config['start_page'] == $aData['iPage'] )
          $aLinksIds[$aData['iPage']] = null;
        $content .= '<url><loc>'.$config['url_domain'].$aLinksIds[$aData['iPage']].'</loc></url>'."\n";
      }
    } // end while

    if( isset( $content ) ){
      return '<?xml version="1.0" encoding="utf-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n".$content.'</urlset>';
    }
  } // end function listPagesSiteMap2Xml

  /**
  * Sorts pages
  * @return array
  * @param array $aPages
  * @param string $aParametersExt
  * Default options: sKey1, sKey2, sFunctionSort
  */
  protected function sortPages( $aPages, $aParametersExt = null ){
    foreach( $aPages as $iPage ){
      $aSort[$iPage][0] = ( !empty( $this->aPages[$iPage][$aParametersExt['sKey1']] ) ? $this->aPages[$iPage][$aParametersExt['sKey1']] : ( ( $aParametersExt['sKey1'] == 'iTime' ) ? 9999999999 : null ) );
      if( isset( $aParametersExt['sKey2'] ) )
        $aSort[$iPage][1] = ( !empty( $this->aPages[$iPage][$aParametersExt['sKey2']] ) ? $this->aPages[$iPage][$aParametersExt['sKey2']] : null );
    } // end foreach

    if( function_exists( $aParametersExt['sFunctionSort'] ) ){
      $aParametersExt['sFunctionSort']( $aSort );
    }

    foreach( $aSort as $iPage => $aValue ){
      $aReturn[] = $iPage;
    } // end foreach
    return $aReturn;
  } // end function sortPages

  /**
  * Generates and displays a menu
  * @return string
  * @param int $iMenu
  * @param array $aParametersExt
  * Default options: sClassName, sFunctionView, iDepthLimit, bExpanded, bDisplayTitles
  */
  public function listPagesMenu( $iMenu, $aParametersExt = null ){
    global $lang, $config;

    if( !isset( $this->aPagesParentsMenus[$iMenu] ) )
      return null;

    $this->aMenuParams['sFunctionView'] = getFunctionName( $aParametersExt, __FUNCTION__ );
    $this->aMenuParams['iDepthLimit'] = isset( $aParametersExt['iDepthLimit'] ) ? $aParametersExt['iDepthLimit'] : 1;
    $this->aMenuParams['bExpanded'] = isset( $aParametersExt['bExpanded'] ) ? true : null;

    $content = null;
    foreach( $this->aPagesParentsMenus[$iMenu] as $iElement => $iPage ){
      $aParametersExt['sSubMenu'] = ( isset( $this->aPagesChildrens[$iPage] ) && ( isset( $this->aMenuParams['bExpanded'] ) || ( isset( $config['current_page_id'] ) && ( $iPage == $config['current_page_id'] || isset( $this->aPagesAllChildrens[$iPage][$config['current_page_id']] ) ) ) ) && $this->aMenuParams['iDepthLimit'] > 0 && $this->aPages[$iPage]['iSubpages'] != 4 ) ? $this->listPagesSubMenu( $iPage, 1 ) : null;
      $aParametersExt['bSelected'] = ( isset( $config['current_page_id'] ) && $config['current_page_id'] == $iPage ) ? true : null;
      $aParametersExt['iElement'] = $iElement;
      $aParametersExt['bSelectMain'] = ( isset( $config['current_page_id'] ) && isset( $this->aPagesAllChildrens[$iPage][$config['current_page_id']] ) ) ? true : null;

      $content .= $this->aMenuParams['sFunctionView']( $this->aPages[$iPage], $aParametersExt );
    } // end foreach
    unset( $this->aMenuParams );

    if( isset( $content ) ){
      return '<nav class="'.( isset( $aParametersExt['sClassName'] ) ? $aParametersExt['sClassName'] : 'menu-'.$iMenu ).'">'.( ( isset( $aParametersExt['bDisplayTitles'] ) && isset( $lang['Menu_'.$iMenu] ) ) ? '<strong>'.$lang['Menu_'.$iMenu].'</strong>' : null ).'<ul>'.$content.'</ul></nav>';
    }
  } // end function listPagesMenu

  /**
  * Displays a sub menu
  * @return string
  * @param int $iPageParent
  * @param int $iDepth
  */
  public function listPagesSubMenu( $iPageParent, $iDepth = 1 ){
    global $config;

    if( isset( $this->aPagesChildrens[$iPageParent] ) ){

      $content = null;
      foreach( $this->aPagesChildrens[$iPageParent] as $iElement => $iPage ){
        $aParametersExt['sSubMenu'] = ( isset( $this->aPagesChildrens[$iPage] ) && ( ( isset( $this->aMenuParams['bExpanded'] ) || ( isset( $config['current_page_id'] ) && ( $iPage == $config['current_page_id'] || isset( $this->aPagesAllChildrens[$iPage][$config['current_page_id']] ) ) ) ) && $this->aMenuParams['iDepthLimit'] > $iDepth && $this->aPages[$iPage]['iSubpages'] != 4 ) ? $this->listPagesSubMenu( $iPage, $iDepth + 1 ) : null );
        $aParametersExt['bSelected'] = ( isset( $config['current_page_id'] ) && $config['current_page_id'] == $iPage ) ? true : null;
        $aParametersExt['iElement'] = $iElement;
        $content .= $this->aMenuParams['sFunctionView']( $this->aPages[$iPage], $aParametersExt );
      } // end foreach

      if( isset( $content ) ){
        return '<ul>'.$content.'</ul>';
      }
    }
  } // end function listPagesSubMenu

  /**
  * Returns all main pages childrens
  * @return null
  */
  protected function generateAllChildrens( $iPageParentMain = null, $iPageParent = null ){
    if( isset( $this->aPagesChildrens ) ){
      if( isset( $iPageParent ) ){
        foreach( $this->aPagesChildrens[$iPageParent] as $iSubPage ){
          $this->aPagesAllChildrens[$iPageParentMain][$iSubPage] = true;
          $this->aPagesAllChildrens[$iPageParent][$iSubPage] = true;
          if( isset( $this->aPagesChildrens[$iSubPage] ) ){
            $this->generateAllChildrens( $iPageParentMain, $iSubPage );
          }
        } // end foreach      
      }
      else{
        foreach( $this->aPagesChildrens as $iPageParent => $aChildrens ){
          if( !isset( $this->aPagesParents[$iPageParent] ) && isset( $this->aPages[$iPageParent] ) && $this->aPages[$iPageParent]['iMenu'] != 0 ){
            foreach( $aChildrens as $iSubPage ){
              $this->aPagesAllChildrens[$iPageParent][$iSubPage] = true;
              if( isset( $this->aPagesChildrens[$iSubPage] ) ){
                $this->generateAllChildrens( $iPageParent, $iSubPage );
              }
            } // end foreach
          }
        } // end foreach
      }
    }
  } // end function generateAllChildrens

  /**
  * Returns a page tree
  * @return string
  * @param int  $iPage
  * @param int  $iPageCurrent
  */
  public function getPagesTree( $iPage, $iPageCurrent = null ){
    if( !isset( $iPageCurrent ) ){
      $iPageCurrent = $iPage;
      $this->mData = null;
    }

    if( isset( $this->aPagesParents[$iPage] ) && isset( $this->aPages[$this->aPagesParents[$iPage]] ) ){
      $this->mData[] = '<a href="'.$this->aPages[$this->aPagesParents[$iPage]]['sLinkName'].'">'.$this->aPages[$this->aPagesParents[$iPage]]['sName'].'</a>';
      return $this->getPagesTree( $this->aPagesParents[$iPage], $iPageCurrent );
    }
    else{
      if( isset( $this->mData ) ){
        array_unshift( $this->mData, ( isset( $GLOBALS['config']['page_link_in_navigation_path'] ) ) ? '<a href="'.$this->aPages[$iPageCurrent]['sLinkName'].'">'.$this->aPages[$iPageCurrent]['sName'].'</a>' : '<span>'.$this->aPages[$iPageCurrent]['sName'].'</span>' );
        $aReturn = array_reverse( $this->mData );
        $this->mData = null;
        return implode( '&nbsp;&raquo;&nbsp;', $aReturn );
      }
    }
  } // end function getPagesTree

  /**
  * Lists all pages containing tag
  * @return string
  * @param mixed $mData
  * @param array $aParametersExt
  * Default options: iType, sClassName, sFunctionView, iLimitPerPage, bNoLinks, bNoFollow
  */
  public function listTagsPages( $iTag, $aParametersExt = null ){
    global $config;

    $oSql = Sql::getInstance( );
    $oQuery = $oSql->getQuery( 'SELECT pages_tags.iPage FROM pages_tags INNER JOIN pages ON pages_tags.iPage = pages.iPage WHERE pages_tags.iTag = '.$iTag.' AND pages.iStatus >= '.getStatus( ).' AND pages.sLang = "'.$config['language'].'" ORDER BY pages.iPosition ASC, pages.sName COLLATE NOCASE ASC' );
    while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
      if( isset( $this->aPages[$aData['iPage']] ) ){
        $aPages[] = $aData['iPage'];
      }
    } // end while
    if( !isset( $aParametersExt['sAddress'] ) ){
      $aParametersExt['sAddress'] = $config['current_tag_url'].'.html';
    }
    if( isset( $aPages ) ){
      $aParametersExt['bAssingFromRequestUri'] = true;
      return $this->listPages( $aPages, $aParametersExt );
    }
  } // end function listTagsPages

};
?>