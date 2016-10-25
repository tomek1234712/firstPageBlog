<?php
class Sliders
{

  public $aSliders;
  public $aSlidersTypes;
  private static $oInstance = null;

  public static function getInstance( ){  
    if( !isset( self::$oInstance ) ){  
      self::$oInstance = new Sliders( );  
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

    if( isset( $config['enable_cache'] ) && is_file( $config['dir_database'].'cache/'.$config['language'].'_sliders' ) ){
      $this->aSliders = unserialize( file_get_contents( $config['dir_database'].'cache/'.$config['language'].'_sliders' ) );
      $this->aSlidersTypes = unserialize( file_get_contents( $config['dir_database'].'cache/'.$config['language'].'_sliders_types' ) );
      if( !is_array( $this->aSliders ) ){
        $config['enabled_sliders'] = null;
      }
      return true;
    }
    
    $this->aSliders = null;
    $oSql = Sql::getInstance( );
    $oQuery = $oSql->getQuery( 'SELECT * FROM sliders WHERE iStatus >= '.getStatus( ).' AND sLang = "'.$config['language'].'" ORDER BY iPosition ASC' );
    while( $aValue = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
      $this->aSliders[$aValue['iSlider']] = $aValue;
      $this->aSlidersTypes[$aValue['iType']][$aValue['iSlider']] = $aValue['iSlider'];
    } // end while

    if( isset( $config['enable_cache'] ) ){
      if( !is_array( $this->aSliders ) ){
        $config['enabled_sliders'] = null;
      }
      file_put_contents( $config['dir_database'].'cache/'.$config['language'].'_sliders', serialize( $this->aSliders ) );
      file_put_contents( $config['dir_database'].'cache/'.$config['language'].'_sliders_types', serialize( $this->aSlidersTypes ) );
    }
  } // end function generateCache

  /**
  * Displays slider
  * @return string
  * @param array $aParametersExt
  * Default options: sClassName, sFunctionView, bNoLinks, iType, sConfig
  */
  public function listSliders( $aParametersExt = null ){
    global $lang, $config;

    if( isset( $aParametersExt['iType'] ) && isset( $this->aSlidersTypes[$aParametersExt['iType']] ) ){
      $content = null;
      if( !isset( $aParametersExt['sFunctionView'] ) ){
        $aParametersExt['sFunctionView'] = __FUNCTION__.'View'.$aParametersExt['iType'];
      }
      $sFunctionView = getFunctionName( $aParametersExt, __FUNCTION__ );
      $i = 1;
      foreach( $this->aSlidersTypes[$aParametersExt['iType']] as $iSlider ){
        $aParametersExt['iElement'] = $i;
        $content .= $sFunctionView( $this->aSliders[$iSlider], $aParametersExt );
        $i++;
      } // end foreach

      if( isset( $content ) )
        return '<div class="'.( isset( $aParametersExt['sClassName'] ) ? $aParametersExt['sClassName'] : 'slider-'.$aParametersExt['iType'] ).'" id="slider-'.$aParametersExt['iType'].'"><ul>'.$content.'</ul></div><script>$("#slider-'.$aParametersExt['iType'].'").quickslider({'.( isset( $aParametersExt['sConfig'] ) ? $aParametersExt['sConfig'] : $config['default_slider_config'] ).'});</script>';
    }
    else{
      return ( ( defined( 'DEVELOPER_MODE' ) && isset( $aParametersExt['iType'] ) ) ? '<p class="dev">THERE IS NO SLIDER WITH TYPE - '.$aParametersExt['iType'].'</p>' : null );
    }
  } // end function listSliders

};
?>