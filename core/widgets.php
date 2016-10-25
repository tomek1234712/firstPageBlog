<?php
class Widgets
{

  public $aWidgets;
  public $aWidgetsTypes;
  private static $oInstance = null;

  public static function getInstance( ){  
    if( !isset( self::$oInstance ) ){  
      self::$oInstance = new Widgets( );  
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

    if( isset( $config['enable_cache'] ) && is_file( $config['dir_database'].'cache/'.$config['language'].'_widgets' ) ){
      $this->aWidgets = unserialize( file_get_contents( $config['dir_database'].'cache/'.$config['language'].'_widgets' ) );
      $this->aWidgetsTypes = unserialize( file_get_contents( $config['dir_database'].'cache/'.$config['language'].'_widgets_types' ) );
      if( !is_array( $this->aWidgets ) ){
        $config['enabled_widgets'] = null;
      }
      return true;
    }

    $this->aWidgets = null;
    $oSql = Sql::getInstance( );
    $oQuery = $oSql->getQuery( 'SELECT * FROM widgets WHERE iStatus >= '.getStatus( ).' AND sLang = "'.$config['language'].'" ORDER BY iPosition ASC' );
    while( $aValue = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
      if( !isset( $config['enabled_sliders'] ) && $aValue['iContentType'] == 3 ){
        if( defined( 'DEVELOPER_MODE' ) )
          echo '<p class="dev">ENABLE SLIDERS IN config.php FILE. SET TO true TO VARIABLE $config[\'enabled_sliders\']</p>';
      }
      else{
        $this->aWidgets[$aValue['iWidget']] = $aValue;
        $this->aWidgetsTypes[$aValue['iType']][$aValue['iWidget']] = $aValue['iWidget'];
      }
    } // end while

    if( isset( $config['enable_cache'] ) ){
      if( !is_array( $this->aWidgets ) ){
        $config['enabled_widgets'] = null;
      }
      file_put_contents( $config['dir_database'].'cache/'.$config['language'].'_widgets', serialize( $this->aWidgets ) );
      file_put_contents( $config['dir_database'].'cache/'.$config['language'].'_widgets_types', serialize( $this->aWidgetsTypes ) );
    }
  } // end function generateCache

  /**
  * Displays slider
  * @return string
  * @param array $aParametersExt
  * Default options: sClassName, sFunctionView, bNoLinks, iType, sConfig, aParametersFunctions, bDontDisplayErrors, bDontDisplayList
  */
  public function listWidgets( $aParametersExt = null ){
    global $lang, $config;

    if( isset( $aParametersExt['iType'] ) && isset( $this->aWidgetsTypes[$aParametersExt['iType']] ) ){
      $content = null;
      $i = 1;
      $sFunctionViewTemp = getFunctionName( $aParametersExt, __FUNCTION__ );
      $aParametersExtRaw = $aParametersExt;
        
      foreach( $this->aWidgetsTypes[$aParametersExt['iType']] as $iWidget ){
        if( !isset( $config['display_widgets_on_page'] ) || ( is_numeric( $config['current_page_id'] ) && isset( $config['display_widgets_on_page'][$config['current_page_id']] ) && $config['display_widgets_on_page'][$config['current_page_id']] === true ) || ( isset( $config['display_widgets_on_page'] ) && isset( $config['display_widgets_on_page'][$config['current_page_id']][$iWidget] ) ) ){
          $aParametersExt['iElement'] = $i;
          $sFunctionView = ( ( isset( $aParametersExt['sFunctionView'] ) && function_exists( $aParametersExt['sFunctionView'].$this->aWidgets[$iWidget]['iContentType'] ) ) ? $aParametersExt['sFunctionView'].$this->aWidgets[$iWidget]['iContentType'] : $sFunctionViewTemp.$this->aWidgets[$iWidget]['iContentType'] );

          if( !isset( $aFunctionsParametersSet[$this->aWidgets[$iWidget]['iContentType']] ) ){
            if( isset( $config['widgets_functions_parameteres_content_type_'.$this->aWidgets[$iWidget]['iContentType']] ) ){
              $aParametersExt['aFunctionParameters'][$this->aWidgets[$iWidget]['iContentType']] = $config['widgets_functions_parameteres_content_type_'.$this->aWidgets[$iWidget]['iContentType']];
            }
            if( isset( $config['widgets_functions_parameteres_type_'.$aParametersExt['iType']][$this->aWidgets[$iWidget]['iContentType']] ) ){
              $aParametersExt['aFunctionParameters'][$this->aWidgets[$iWidget]['iContentType']] = isset( $aParametersExt['aFunctionParameters'][$this->aWidgets[$iWidget]['iContentType']] ) ? array_merge( $aParametersExt['aFunctionParameters'][$this->aWidgets[$iWidget]['iContentType']], $config['widgets_functions_parameteres_type_'.$aParametersExt['iType']][$this->aWidgets[$iWidget]['iContentType']] ) : $config['widgets_functions_parameteres_type_'.$aParametersExt['iType']][$this->aWidgets[$iWidget]['iContentType']];
            }
            if( isset( $aParametersExtRaw['aFunctionParameters'][$this->aWidgets[$iWidget]['iContentType']] ) ){
              $aParametersExt['aFunctionParameters'][$this->aWidgets[$iWidget]['iContentType']] = isset( $aParametersExt['aFunctionParameters'][$this->aWidgets[$iWidget]['iContentType']] ) ? array_merge( $aParametersExt['aFunctionParameters'][$this->aWidgets[$iWidget]['iContentType']], $aParametersExtRaw['aFunctionParameters'][$this->aWidgets[$iWidget]['iContentType']] ) : $aParametersExtRaw['aFunctionParameters'][$this->aWidgets[$iWidget]['iContentType']];
            }
            $aFunctionsParametersSet[$this->aWidgets[$iWidget]['iContentType']] = true;
          }

          $sReturn = $sFunctionView( $this->aWidgets[$iWidget], $aParametersExt );
          if( !empty( $sReturn ) ){
            $content .= ( !isset( $aParametersExt['bDontDisplayList'] ) ? '<li id="widget-'.$iWidget.'" class="widget type-'.$this->aWidgets[$iWidget]['iContentType']./*( ( $aParametersExt['iElement'] % 4 ) == 1 ? ' row' : null ).*/'">' : null ).$sReturn.( !isset( $aParametersExt['bDontDisplayList'] ) ? '</li>' : null );
            $i++;        
          }
        }
      } // end foreach

      if( isset( $content ) )
        return '<div class="'.( isset( $aParametersExt['sClassName'] ) ? $aParametersExt['sClassName'] : 'widgets-list widgets-'.$aParametersExt['iType'] ).'">'.( !isset( $aParametersExt['bDontDisplayList'] ) ? '<ul>' : null ).$content.( !isset( $aParametersExt['bDontDisplayList'] ) ? '</ul>' : null ).'</div>';

    }
    else{
      return ( ( defined( 'DEVELOPER_MODE' ) && isset( $aParametersExt['iType'] ) && !isset( $aParametersExt['bDontDisplayErrors'] ) ) ? '<p class="dev">THERE IS NO WIDGET WITH TYPE - '.$aParametersExt['iType'].'</p>' : null );
    }
  } // end function listWidgets
};
?>