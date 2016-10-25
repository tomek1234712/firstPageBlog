<?php
/**
* Returns status limit
* @return int
*/
function getStatus( ){
  if( verifyAdminSession( ) === true ){
    if( defined( 'CUSTOMER_PAGE' ) )
      return isset( $GLOBALS['config']['display_hidden_pages'] ) ? 0 : 1;
    else
      return 0;
  }
  else
    return 1;
} // end function getStatus

/**
* Verifies admin session
* @return bool
*/
function verifyAdminSession( ){
  if( isset( $GLOBALS['config']['session_key_name'] ) && isset( $_SESSION[$GLOBALS['config']['session_key_name']] ) && is_int( $_SESSION[$GLOBALS['config']['session_key_name']] ) )
    return true;
  else
    return false;
} // end function verifyAdminSession

/**
* Returns a function name based on sent parameters
* @return string
* @param array $aParams
* @param string $sFunctionName
*/
function getFunctionName( $aParams, $sFunctionName ){
  return ( isset( $aParams['sFunctionView'] ) && function_exists( $aParams['sFunctionView'] ) ) ? $aParams['sFunctionView'] : $sFunctionName.'View';
} // end function getFunctionName

/**
* Returns a theme files name
* @return array
* @param int $iTheme
*/
function throwThemeFiles( $iTheme ){
  global $config;
  if( isset( $config['themes'][$iTheme] ) && isset( $config['themes'][$iTheme][0] ) && isset( $config['themes'][$iTheme][1] ) && isset( $config['themes'][$iTheme][2] ) ){}
  else
    $iTheme = 1;

  return Array( 'sHeader' => $config['themes'][$iTheme][0], 'sMain' => $config['themes'][$iTheme][1], 'sFooter' => $config['themes'][$iTheme][2] );
} // end function throwThemeFiles

/**
* Displays date changed by $config['time_diff']
* @return string
* @param int $iTime
* @param string $sFormat
*/
function displayDate( $iTime = null, $sFormat = 'Y-m-d H:i' ){
  global $config;
  return isset( $iTime ) ? date( $sFormat, $iTime + ( $config['time_diff'] * 60 ) ) : date( $sFormat, time( )  + ( $config['time_diff'] * 60 ) );
} // end function displayDate

/**
* Return configuration from table bin
* @return void
* @param bool $bInsert
*/
function getBinValues( $bInsert = null ){
  global $config;
  $oSql = Sql::getInstance( );
  $oQuery = $oSql->getQuery( 'SELECT sKey, sValue FROM bin' );
  while( $aValue = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
    if( !isset( $config[$aValue['sKey']] ) )
      $config[$aValue['sKey']] = $aValue['sValue'];
  } // end while

  if( isset( $bInsert ) ){
    if( isset( $config['session_key_name'] ) ){
      if( time( ) - substr( $config['session_key_name'], 1, 10 ) > 86400 ){
        $oSql->query( 'DELETE FROM bin WHERE sKey = "session_key_name"' );
        $config['session_key_name'] = null;
      }
    }

    if( !isset( $config['session_key_name'] ) ){
      $config['session_key_name'] = 's'.time( ).rand( 1000, 9999 );
      $oSql->query( 'INSERT INTO bin ( "sKey", "sValue" ) VALUES( "session_key_name", "'.$config['session_key_name'].'" )' );
    }
  }
} // end function getBinValues

/**
* Function returns textarea field
* @return string
* @param  string  $sName
* @param  string  $sContent
* @param array $aParametersExt
* Default options: iTab, mWysiwyg, sToolbar, sPlugins, sClassName, sFunctionName
*/
function getTextarea( $sName = 'sContent', $sContent = '', $aParametersExt = null ){
  global $config, $lang;
  $content = null;
  if( !isset( $aParametersExt['mWysiwyg'] ) )
    $aParametersExt['mWysiwyg'] = $config['wysiwyg'];
  if( !isset( $aParametersExt['sFunctionName'] ) && isset( $aParametersExt['mWysiwyg'] ) && $aParametersExt['mWysiwyg'] !== false )
    $aParametersExt['sFunctionName'] = 'getWysiwyg'.$aParametersExt['mWysiwyg'];

  if( isset( $aParametersExt['sFunctionName'] ) && !empty( $aParametersExt['sFunctionName'] ) ){
    if( function_exists( $aParametersExt['sFunctionName'] ) ){
      $content .= $aParametersExt['sFunctionName']( $sName, $aParametersExt );
    }
    else{
      return defined( 'DEVELOPER_MODE' ) ? '<p class="dev">THERE IS NO SUCH FUNCTION - '.$aParametersExt['sFunctionName'].'</p>' : null;
    }
  }
  $content .= '<textarea name="'.$sName.'" id="'.$sName.'" rows="20" cols="60" class="'.( isset( $aParametersExt['sClassName'] ) ? $aParametersExt['sClassName'] : 'text-editor' ).'" '.( isset( $aParametersExt['iTab'] ) ? ' tabindex="'.$aParametersExt['iTab'].'"' : null ).'>'.$sContent.'</textarea>';

  return $content;
} // end function getTextarea

/**
* Throws an array with URL information
* @return array
*/
function throwSiteUrls( ){
  global $config;
  if( !isset( $config['url_scheme'] ) ){
    $aData = parse_url( $_SERVER['REQUEST_URI'] );
    $aData['query'] = isset( $aData['query'] ) ? '?'.$aData['query'] : null;
    $config['url_scheme'] = 'http://';
    $config['url_domain'] = $config['url_scheme'].( isset( $config['domain'] ) ? $config['domain'] : $_SERVER['HTTP_HOST'] ).( isset( $aData['host'] ) ? $aData['host'] : null ).( isset( $aData['path'] ) ? substr( $aData['path'], 0, strrpos( $aData['path'], '/' ) + 1 ) : null );
    $config['url_complete'] = $config['url_scheme'].( isset( $config['domain'] ) ? $config['domain'] : $_SERVER['HTTP_HOST'] ).( isset( $aData['host'] ) ? $aData['host'] : null ).( isset( $aData['path'] ) ? $aData['path'] : null ).$aData['query'];
    $config['url_complete_encoded'] = urlencode( $config['url_complete'] );
  }
} // end function throwSiteUrls

/**
* Returns captcha text
* @return array
*/
function throwCaptchaText(  ){
  $i1 = rand( 1, 20 );
  $i2 = rand( 1, 60 );
  return Array( 'sText' => '<strong>'.changeTxtToCode( $i1 ).'</strong><ins>+</ins><em>'.changeTxtToCode( $i2 ).'</em>', 'sMd5' => md5( $i1 + $i2 ), 'aValues' => Array( $i1, $i2 ) );
} // end function throwCaptchaText

/**
* Generates dynamical description for the meta description
* @return string
* @param array $aData
*/
function generateDynamicMetaDescription( $aData ){
  global $config;
  if( !empty( $aData['sDescriptionShort'] ) )
    $sDescription = $aData['sDescriptionShort'];
  elseif( !empty( $aData['sDescriptionFull'] ) )
    $sDescription = $aData['sDescriptionFull'];

  if( !empty( $sDescription ) )
    $sDescription = trim( preg_replace( '/\s+/', ' ', preg_replace( '/\x{00A0}/u', ' ', str_replace( Array( "\n", '|n|', '"' ), Array( ' ', ' ', '\'' ), strip_tags( $sDescription ) ) ) ) );
  if( !empty( $sDescription ) ){
    if( strlen( $sDescription ) > 160  )
      $sDescription = cutText( $sDescription, 156 ).' ...';
    return $sDescription;
  }
  else{
    return $config['description'];
  }
} // end function generateDynamicMetaDescription

/**
* Display widget
* @return string
* @param int $iWidget
* @param array $aParametersExt
*/
function displayWidget( $iWidget, $aParametersExt = null ){
  global $config;
  if( isset( $config['enabled_widgets'] ) ){
    $oWidget = Widgets::getInstance( );

    if( isset( $oWidget->aWidgets[$iWidget] ) ){
      $aParametersExtRaw = $aParametersExt;

      if( isset( $config['widgets_functions_parameteres_content_type_'.$oWidget->aWidgets[$iWidget]['iContentType']] ) ){
        $aParametersExt['aFunctionParameters'][$oWidget->aWidgets[$iWidget]['iContentType']] = $config['widgets_functions_parameteres_content_type_'.$oWidget->aWidgets[$iWidget]['iContentType']];
      }
      if( isset( $config['widgets_functions_parameteres_type_'.$oWidget->aWidgets[$iWidget]['iType']][$oWidget->aWidgets[$iWidget]['iContentType']] ) ){
        $aParametersExt['aFunctionParameters'][$oWidget->aWidgets[$iWidget]['iContentType']] = isset( $aParametersExt['aFunctionParameters'][$oWidget->aWidgets[$iWidget]['iContentType']] ) ? array_merge( $aParametersExt['aFunctionParameters'][$oWidget->aWidgets[$iWidget]['iContentType']], $config['widgets_functions_parameteres_type_'.$oWidget->aWidgets[$iWidget]['iType']][$oWidget->aWidgets[$iWidget]['iContentType']] ) : $config['widgets_functions_parameteres_type_'.$oWidget->aWidgets[$iWidget]['iType']][$oWidget->aWidgets[$iWidget]['iContentType']];
      }
      if( isset( $aParametersExtRaw['aFunctionParameters'] ) ){
        $aParametersExt['aFunctionParameters'][$oWidget->aWidgets[$iWidget]['iContentType']] = isset( $aParametersExt['aFunctionParameters'][$oWidget->aWidgets[$iWidget]['iContentType']] ) ? array_merge( $aParametersExt['aFunctionParameters'][$oWidget->aWidgets[$iWidget]['iContentType']], $aParametersExtRaw['aFunctionParameters'] ) : $aParametersExtRaw['aFunctionParameters'];
      }
      if( !isset( $aParametersExt['sFunctionView'] ) )
        $aParametersExt['sFunctionView'] = 'listWidgetsView'.$oWidget->aWidgets[$iWidget]['iContentType'];

      $sFunctionView = getFunctionName( $aParametersExt, 'listWidgets' );
      return '<div class="widget widget-id-'.$iWidget.' type-'.$oWidget->aWidgets[$iWidget]['iContentType'].'">'.$sFunctionView( $oWidget->aWidgets[$iWidget], $aParametersExt ).'</div>';
    }
  }
  else{
    return ( defined( 'DEVELOPER_MODE' ) ? '<p><strong>SET TRUE VARIABLE: $config[\'enabled_widgets\'] TO DISPLAY WIDGETS</strong></p>' : null );
  }
} // end function displayWidget

/**
* Sends an e-mail
* @return mixed
* @param string $sTopic
* @param string $sContent
* @param string $sEmailTo
* @param string $sEmailFrom
* @param array $aParametersExt
* Default options: bDisplayMsg
*/
function sendEmail( $sTopic, $sContent, $sEmailTo, $sEmailFrom = null, $aParametersExt = null ){
  global $lang, $config;
  if( !isset( $sEmailFrom ) )
    $sEmailFrom = $config['contact_email'];
  if( !empty( $sTopic ) && !empty( $sContent ) && !empty( $sEmailFrom ) && !empty( $sEmailTo ) && checkEmail( $sEmailTo ) && checkEmail( $sEmailFrom ) ){
    if( @mail( $sEmailTo, '=?UTF-8?B?'.base64_encode( $sTopic ).'?=', $sContent, 'MIME-Version: 1.0'."\r\n".'Content-type: text/plain; charset=UTF-8'."\r\n".( ( $config['emails_from_header_option'] == 2 ) ? 'Reply-to: '.$sEmailFrom : ( ( $config['emails_from_header_option'] == 3 && $config['contact_email'] != $sEmailFrom ) ? 'Reply-to: '.$sEmailFrom."\r\n".'From: '.$config['contact_email'] : 'From: '.$sEmailFrom ) ) ) ){
      return ( isset( $aParametersExt['bDisplayMsg'] ) ? '<div class="msg done"><h2>'.$lang['Mail_send_correct'].'</h2></div>' : true );
    }
  }
  return ( isset( $aParametersExt['bDisplayMsg'] ) ? '<div class="msg error"><h2>'.$lang['Mail_send_error'].'</h2></div>' : false );
} // end function sendEmail

/**
* Function converts the contents special chars to other data
* @return string
* @param string $sContent
* @param array $aParametersExt
*/
function parseContent( $sContent, $aParametersExt = null ){
  preg_match_all( '/\[[A-Z]+[A-Z0-9_]+\]/', $sContent, $aMatches );
  if( isset( $aMatches[0] ) && count( $aMatches[0] ) > 0 ){
    foreach( $aMatches[0] as $iKey => $sValue ){
      $sContent = str_replace( $sValue, ( isset( $aParametersExt[$sValue] ) ? $aParametersExt[$sValue] : '' ), $sContent ); 
    } // end foreach
  }
  return $sContent;
} // end function parseContent

/**
* Sends an e-mail from contact form
* @return string
* @param $aForm
*/
function sendContactForm( $aForm ){
  global $config, $lang;

  if( !empty( $aForm['sMd5'] ) && strlen( $aForm['sMd5'] ) == 32 && !empty( $aForm['sCaptcha'] ) && md5( trim( $aForm['sCaptcha'] ) ) == $aForm['sMd5'] && checkFormFields( $aForm, Array( 'sTopic' => true, 'sEmailFrom' => Array( 'email' ), 'sContent' => Array( 'textarea' ) ) ) ){
    return sendEmail( $aForm['sTopic'], parseContent( $lang['Contact_form_content'], Array( '[NAME]' => $aForm['sName'], '[PHONE]' => $aForm['sPhone'], '[EMAIL]' => $aForm['sEmailFrom'], '[CONTENT]' => $aForm['sContent'] ) ), $config['contact_email'], $aForm['sEmailFrom'], Array( 'bDisplayMsg' => true ) );
  }
  else{
    return '<div class="msg error panel">'.$lang['Fill_required_fields'].'<br /><a href="javascript:history.back();" class="action">&laquo; '.$lang['back'].'</a></div>';
  }
} // end function sendContactForm
?>