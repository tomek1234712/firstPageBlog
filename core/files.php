<?php
class Files
{

  public $aDefaultImages;
  public $aDontDisplayImagesTypes;
  private static $oInstance = null;

  public static function getInstance( ){  
    if( !isset( self::$oInstance ) ){  
      self::$oInstance = new Files( );  
    }  
    return self::$oInstance;  
  } // end function getInstance

  /**
  * Constructor
  * @return void
  * @param mixed $mValue
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

    if( isset( $config['enable_cache'] ) && is_file( $config['dir_database'].'cache/'.$config['language'].'_files' ) ){
      $this->aDefaultImages = unserialize( file_get_contents( $config['dir_database'].'cache/'.$config['language'].'_files' ) );
      return true;
    }
    
    $oFJ = new FileJobs( );
    $this->aDefaultImages = null;
    $oSql = Sql::getInstance( );
    $oQuery = $oSql->getQuery( 'SELECT files.iFile, files.iPage, files.iSizeLists, files.iSizeOther, files.sFileName, files.iCrop, files.sDescription FROM files, pages WHERE files.iDefault = 1 AND pages.iPage=files.iPage AND pages.sLang = "'.$config['language'].'"' );
    while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
      if( $aData['iCrop'] > 0 && isset( $config['crop_options'][$aData['iCrop']][1] ) )
        $aData['sFileNameCrop'] = $oFJ->getNameOfFile( $aData['sFileName'] ).$config['crop_options'][$aData['iCrop']][1].'.'.$oFJ->getExtOfFile( $aData['sFileName'] );
      $this->aDefaultImages[$aData['iPage']] = $aData;
    } // end while

    if( isset( $config['enable_cache'] ) ){
      file_put_contents( $config['dir_database'].'cache/'.$config['language'].'_files', serialize( $this->aDefaultImages ) );
    }
  } // end function generateCache

  /**
  * Displays default image
  * @return string
  * @param int $iPage
  * @param array $aParametersExt
  * Default options: sClassName, bNoLinks, sLink
  */
  public function getDefaultImage( $iPage, $aParametersExt = null ){
    if( isset( $this->aDefaultImages[$iPage] ) ){
      $sLink = null;
      if( !isset( $aParametersExt['bNoLinks'] ) || isset( $aParametersExt['sLink'] ) ){
        $sLink = isset( $aParametersExt['sLink'] ) ? '<a href="'.$aParametersExt['sLink'].'">' : '<a href="files/'.$this->aDefaultImages[$iPage]['sFileName'].'" class="quickbox['.$iPage.']">';
      }
      if( !isset( $aParametersExt['sKeySize'] ) )
        $aParametersExt['sKeySize'] = 'iSizeLists';
      return '<div class="'.( isset( $aParametersExt['sClassName'] ) ? $aParametersExt['sClassName'] : 'image' ).'">'.$sLink.'<img src="files/'.$this->aDefaultImages[$iPage][$aParametersExt['sKeySize']].'/'.( isset( $this->aDefaultImages[$iPage]['sFileNameCrop'] ) ? $this->aDefaultImages[$iPage]['sFileNameCrop'] : $this->aDefaultImages[$iPage]['sFileName'] ).'" alt="'.( !empty( $this->aDefaultImages[$iPage]['sDescription'] ) ? $this->aDefaultImages[$iPage]['sDescription'] : null ).'" />'.( isset( $sLink ) ? '</a>' : null ).'</div>';
    }
  } // end function getDefaultImage

  /**
  * Displays images list for Galleria
  * @return string
  * @param int $iPage
  * @param array $aParametersExt
  * Default options: sClassName, sFunctionView, bNoStyleId, bNoLinks, iType
  */
  public function listImagesGalleria( $iPage, $aParametersExt = null ){
    $content = $this->listImages( $iPage, $aParametersExt );
    if( !empty( $content ) ){
      return $content.displayJavaScripts( 'plugins/galleria/galleria-1.4.2.min.js' );
    }
  } // end function listImagesGalleria

  /**
  * Displays images
  * @return string
  * @param int $iPage
  * @param array $aParametersExt
  * Default options: sClassName, sFunctionView, bNoStyleId, bNoLinks, iType
  */
  public function listImages( $iPage, $aParametersExt = null ){
    global $config;

    if( isset( $aParametersExt['iType'] ) && !isset( $config['display_images_displayed_in_description'] ) && isset( $this->aDontDisplayImagesTypes[$aParametersExt['iType']] ) )
      return null;

    $content = null;
    $oFJ = new FileJobs( );
    $oSql = Sql::getInstance( );
    $oQuery = $oSql->getQuery( 'SELECT * FROM files WHERE iPage = "'.$iPage.'"'.( isset( $aParametersExt['iType'] ) ? ' AND iType = "'.$aParametersExt['iType'].'"' : null ).' AND iSizeDetails > 0 ORDER BY iPosition ASC, sFileName ASC' );
    if( !isset( $aParametersExt['sFunctionView'] ) && isset( $aParametersExt['iType'] ) ){
      $aParametersExt['sFunctionView'] = __FUNCTION__.'View'.$aParametersExt['iType'];
    }
    $sFunctionView = getFunctionName( $aParametersExt, __FUNCTION__ );
    $i = 1;
    while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
      $aData['sFileNameThumb'] = ( $aData['iCrop'] > 0 && isset( $config['crop_options'][$aData['iCrop']][1] ) ) ? $oFJ->getNameOfFile( $aData['sFileName'] ).$config['crop_options'][$aData['iCrop']][1].'.'.$oFJ->getExtOfFile( $aData['sFileName'] ) : $aData['sFileName'];
      $aParametersExt['iElement'] = $i;
      $content .= $sFunctionView( $aData, $aParametersExt );
      $i++;
    } // end while

    if( isset( $content ) ){
      if( isset( $aParametersExt['iType'] ) )
        $this->aDontDisplayImagesTypes[$aParametersExt['iType']] = true;

      return '<ul class="'.( isset( $aParametersExt['sClassName'] ) ? $aParametersExt['sClassName'] : ( isset( $aParametersExt['iType'] ) ? 'images-'.$aParametersExt['iType'] : 'images-list' ) ).'">'.$content.'</ul>';
    }
  } // end function listImages

  /**
  * Displays files
  * @return string
  * @param int $iPage
  * @param array $aParametersExt
  * Default options: sClassName, sFunctionView
  */
  public function listFiles( $iPage, $aParametersExt = null ){
    global $config;

    $content = null;
    $oSql = Sql::getInstance( );
    $oFJ = new FileJobs( );
    $oQuery = $oSql->getQuery( 'SELECT * FROM files WHERE iPage = "'.$iPage.'" AND iSizeDetails = 0 ORDER BY iPosition ASC, sFileName ASC' );
    $sFunctionView = getFunctionName( $aParametersExt, __FUNCTION__ );
    $i = 1;

    while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
      $aParametersExt['iElement'] = $i;
      $sExt = $oFJ->getExtOfFile( $aData['sFileName'] );
      if( !isset( $config['ext_icons'][$sExt] ) )
        $config['ext_icons'][$sExt] = 'nn';
      $aData['sIconStyle'] = $config['ext_icons'][$sExt];
      $content .= $sFunctionView( $aData, $aParametersExt );
      $i++;
    } // end while

    if( isset( $content ) )
      return '<ul class="'.( isset( $aParametersExt['sClassName'] ) ? $aParametersExt['sClassName'] : 'files-list' ).'">'.$content.'</ul>';
  } // end function listFiles
};
?>