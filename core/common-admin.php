<?php
/**
* Log in and out actions to back-end
* @return void
*/
function loginActions( ){
  global $config, $lang;
  $content = null;

  if( verifyAdminSession( ) === true && ( !isset( $config['login_pass'] ) || !isset( $config['developer_login_email'] ) ) ){
    if( $_SESSION[$config['session_key_name']] == -1 && !isset( $config['developer_login_email'] ) )
      unset( $_SESSION[$config['session_key_name']] );
    elseif( !isset( $config['login_pass'] ) )
      unset( $_SESSION[$config['session_key_name']] );
  }

  if( !verifyAdminSession( ) ){
    $oSql = Sql::getInstance( );
    $bFirstLog = null;
    if( empty( $config['login_email'] ) || !checkEmail( $config['login_email'] ) || ( isset( $config['login_pass'] ) && empty( $config['login_pass'] ) ) ){
      $bFirstLog = true;
    }

    if( isset( $config['failed_logs'] ) && isset( $config['failed_login_time'] ) && $config['failed_logs'] > 2 && time( ) - $config['failed_login_time'] <= 900 ){
      $bLoginExceed = true;
      $content = '<div class="msg error"><strong>'.$lang['Failed_login_wait_time'].'</strong></div>';
    }
    else{
      if( $_GET['p'] == 'login' && isset( $_POST['sEmail'] ) && checkEmail( $_POST['sEmail'] ) ){
        if( isset( $_POST['sPass'] ) ){
          if( isset( $bFirstLog ) )
            saveVariables( Array( 'login_email' => $_POST['sEmail'], 'login_pass' => $_POST['sPass'] ), $config['dir_database'].'config.php' );

          if( !empty( $_POST['sPass'] ) && checkLogin( $_POST['sEmail'], $_POST['sPass'], $bFirstLog ) === true ){
            if( $_SESSION[$config['session_key_name']] >= 0 ){
              if( !isset( $_COOKIE['sEmail'] ) || $_COOKIE['sEmail'] != $_POST['sEmail'] )
                @setCookie( 'sEmail', $_POST['sEmail'], time( ) + 2592000 );
              if( isset( $config['last_login'] ) )
                updateBin( 'before_last_login', $config['last_login'] );
              updateBin( 'last_login', time( ) );
              updateBin( 'failed_logs', 0 );
            }

            header( 'Location: '.( !empty( $_SESSION['sLoginNextPage'] ) ? $_SESSION['sLoginNextPage'] : $config['admin_file'] ) );
            exit;
          }
          else{
            $sLoginPage = $config['admin_file'];
            $content = '<div class="msg error"><strong>'.$lang['Wrong_email_or_pass'].'</strong><a href="javascript:history.back()">&laquo; '.$lang['back'].'</a></div>';
          }
        }
        else{
          if( isset( $config['enabled_main_admin_password_remind'] ) && ( !isset( $config['failed_email_password_remind'] ) || $config['failed_email_password_remind'] <= 1 ) ){
            if( changeSpecialChars( $_POST['sEmail'] ) == $config['login_email'] ){
              if( isset( $config['last_password_remind'] ) && time( ) - $config['last_password_remind'] <= 900 ){
                $content = '<div class="msg error"><strong>'.$lang['Failed_password_sent_wait_time'].'</strong><a href="javascript:history.back()">&laquo; '.$lang['back'].'</a></div>';
              }
              else{
                sendEmail( $lang['Password_remind_email_topic'], parseContent( $lang['Password_remind_email_content'], Array( '[PASSWORD]' => $config['login_pass'] ) ), $config['login_email'], $config['login_email'] );
                $content = '<div class="msg error"><strong>'.$lang['Password_sent'].'</strong><a href="?p=">&laquo; '.$lang['back'].'</a></div>';
              }
              updateBin( 'last_password_remind', time( ) );
            }
            else{
              $content = '<div class="msg error"><strong>'.$lang['Wrong_email'].'</strong><a href="javascript:history.back()">&laquo; '.$lang['back'].'</a></div>';
              updateBin( 'failed_email_password_remind', 1 );
              updateBin( 'failed_email_password_remind_time', time( ) );
            }
          }
          else{
            $content = '<div class="msg error"><strong>'.$lang['Wrong_email'].'</strong><a href="javascript:history.back()">&laquo; '.$lang['back'].'</a></div>';
          }
        }
      }
      else{
        $_SESSION['sLoginNextPage'] = str_replace( '&amp;', '&', $_SERVER['REQUEST_URI'] );
        $content = '<form method="post" action="?p=login" id="login-form">
          <fieldset>
            <legend >'.( isset( $bFirstLog ) ? $lang['Type_login_password'] : $lang['log_in'] ).'</legend>
            <ul class="forms full">
              <li><label>'.$lang['Email'].':<input type="email" name="sEmail" class="input" value="'.( isset( $_COOKIE['sEmail'] ) ? strip_tags( $_COOKIE['sEmail'] ) : null ).'" data-form-check="email" /></label></li>
              '.( isset( $config['enabled_main_admin_password_remind'] ) && isset( $_GET['bPasswordRemind'] ) ? '<li><input type="submit" class="main" value="'.$lang['send_password'].' &raquo;" /></li>' : '<li><label>'.$lang['Password'].':<input type="'.( isset( $bFirstLog ) ? 'text' : 'password' ).'" name="sPass" class="input" value="" data-form-check="required" /></label></li><li><input type="submit" class="main" value="'.$lang['log_in'].' &raquo;" /></li>' ).'
            </ul>
          </fieldset>
        </form>
        <script>
          $( function(){
            focusCursor( ["sEmail", "sPass"] );
            $( "#login-form" ).quickform();
          } );
        </script>';
      }
    }

    if( isset( $config['login_pass'] ) || isset( $config['developer_login_email'] ) ){
      require_once 'templates/admin/_header.php';
      echo '<section id="login-panel"'.( isset( $bFirstLog ) ? ' class="init"' : null ).'>
        <header>
          <nav>
            <ul>
              <li><a href="http://opensolution.org/" target="_blank"><img src="templates/admin/img/logo_os_dark.png" alt="Logo OpenSolution" /></a></li>
              <li><a href="http://opensolution.org/" target="_blank">Quick.Cms.Ext v'.$config['version'].'</a></li>
            </ul>
          </nav>
        </header>
        '.$content.'
        <footer>
          <nav>
            <ul>
              <li><a href="./">'.$lang['homepage'].'</a></li>'.
              ( isset( $config['enabled_main_admin_password_remind'] ) ? ( isset( $_GET['bPasswordRemind'] ) ? '<li><a href="?p=login">'.$lang['log_in'].'</a></li>' : '<li><a href="?p=login&amp;bPasswordRemind">'.$lang['forgot_your_password'].'</a></li>' ) : null ).'
            </ul>
          </nav>
        </footer>
      </section><style>#foot{display:none;}</style>';
      require_once 'templates/admin/_footer.php';
    }
    else{
      header( 'Location: ./' );
    }
    exit;
  }
  else{
    if( $_GET['p'] == 'logout' ){
      foreach( $_SESSION as $sKey => $mValue ){
        unset( $_SESSION[$sKey] );
      } // end foreach
      header( 'Location: '.$config['admin_file'] );
      exit;
    }
    elseif( $_GET['p'] != 'dashboard' && !strstr( $_GET['p'], 'ajax-' ) && ( ( !isset( $_COOKIE['bLicense'.str_replace( '.', '', $config['version'] )] ) && $config['display_admin_license_info'] === true ) || ( is_file( $config['dir_database'].'cache/verify-time' ) && ( time( ) - file_get_contents( $config['dir_database'].'cache/verify-time' ) ) > 2592000 ) ) ){
      header( 'Location: '.$config['admin_file'].'?p=dashboard' );
      exit;
    }
  }
} // end function loginActions

/**
* Checks login and password saved in database/config.php
* @return bool
* @param string $sEmailRaw
* @param string $sPassRaw
* @param bool $bFirstLog
*/
function checkLogin( $sEmailRaw, $sPassRaw, $bFirstLog = null ){
  global $config;
  $sEmail = changeSpecialChars( $sEmailRaw );
  $sPass = changeSpecialChars( str_replace( '"', '&quot;', $sPassRaw ) );

  if( ( isset( $config['login_pass'] ) && $config['login_email'] == $sEmail && $config['login_pass'] == $sPass ) || isset( $bFirstLog ) ){
    $_SESSION[$config['session_key_name']] = 0;
    return true;
  }
  elseif( isset( $config['developer_login_email'] ) && isset( $config['developer_login_pass'] ) && $config['developer_login_email'] == md5( $sEmailRaw ) && $config['developer_login_pass'] == md5( $sPassRaw ) && ( ( isset( $config['developer_login_ip'] ) && $config['developer_login_ip'] == md5( $_SERVER['REMOTE_ADDR'] ) ) || !isset( $config['developer_login_ip'] ) ) ){
    $_SESSION[$config['session_key_name']] = -1;
    $_SESSION[$config['developer_login_email']] = $config['session_key_name'];
    return true;
  }
  else{
    updateBin( 'failed_logs', ( isset( $config['failed_logs'] ) ? ( $config['failed_logs'] + 1 ) : 1 ) );
    updateBin( 'failed_login_time', time( ) );
    return false;
  }
} // end function checkLogin

/**
* Update data in bin table
* @return void
* @param string $sKey
* @param mixed $mValue
* @param bool $bValueRaw
*/
function updateBin( $sKey, $mValue, $bValueRaw = null ){
  global $config;
  $oSql = Sql::getInstance( );

  if( isset( $config[$sKey] ) )
    $oSql->query( 'UPDATE bin SET sValue = '.( isset( $bValueRaw ) ? $mValue : addslashes( $mValue ) ).' WHERE sKey = "'.addslashes( $sKey ).'"' );
  else
    $oSql->query( 'INSERT INTO bin ( "sKey", "sValue" ) VALUES( "'.addslashes( $sKey ).'", '.( isset( $bValueRaw ) ? $mValue : addslashes( $mValue ) ).' )' );
} // end function updateBin

/**
* Saves variables to config
* @return void
* @param array  $aForm
* @param string $sFile
* @param string $sVariable
*/
function saveVariables( $aForm, $sFile, $sVariable = 'config' ){
  if( is_file( $sFile ) && strstr( $sFile, '.php' ) ){
    $aFile = file( $sFile );
    $iCount = count( $aFile );
    $rFile = fopen( $sFile, 'w' );

    if( isset( $aForm['page_search'] ) && isset( $aForm['start_page'] ) && $aForm['start_page'] == $aForm['page_search'] ){
      $aForm['page_search'] = '';
    }

    for( $i = 0; $i < $iCount; $i++ ){
      foreach( $aForm as $sKey => $sValue ){
        if( preg_match( '/'.$sVariable."\['".$sKey."'\]".' = /', $aFile[$i] ) && strstr( $aFile[$i], '=' ) ){
          $mEndOfLine = strstr( $aFile[$i], '; //' );
          if( empty( $mEndOfLine ) ){
            $mEndOfLine = ';';
          }
          $sValue = changeSpecialChars( trim( str_replace( '"', '&quot;', $sValue ) ) );
          if( preg_match( '/^(true|false|null)$/', $sValue ) ){
            $aFile[$i] = "\$".$sVariable."['".$sKey."'] = ".$sValue.$mEndOfLine;
          }
          else
            $aFile[$i] = "\$".$sVariable."['".$sKey."'] = \"".$sValue."\"".$mEndOfLine;
        }
      } // end foreach

      fwrite( $rFile, rtrim( $aFile[$i] ).( $iCount == ( $i + 1 ) ? null : "\r\n" ) );

    } // end for
    fclose( $rFile );
  }
} // end function saveVariables

/**
* Return themes select
* @return string
* @param int $iThemeSelect
*/
function getThemesSelect( $iThemeSelect ){
  global $config;

  $content = null;
  foreach( $config['themes'] as $iTheme => $aData ){
    $content .= '<option value="'.$iTheme.'"'.( ( $iTheme == $iThemeSelect ) ? ' selected="selected"' : null ).'>'.$aData[3].'</option>';
  } // end foreach
  return $content;
} // end function getThemesSelect

/**
* Return image thumbnails sizes select
* @return string
* @param int $iSizeSelect
* @param bool $bCustom
*/
function getThumbnailsSelect( $iSizeSelect, $bCustom = null ){
  global $config, $lang;

  $content = null;
  foreach( $config['images_thumbnails'] as $iSize ){
    $content .= '<option value="'.$iSize.'"'.( ( $iSize == $iSizeSelect ) ? ' selected="selected"' : null ).'>'.$iSize.'</option>';
  } // end foreach
  if( isset( $bCustom ) && !is_numeric( $iSizeSelect ) )
    $content .= '<option value="" selected="selected" disabled="disabled">'.$lang['Custom'].'</option>';
  return $content;
} // end function getThumbnailsSelect

/**
* Clears cache from database/cache/
* @return void
* @param string $sName
*/
function clearCache( $sName = null ){
  global $config;

  foreach( new DirectoryIterator( $config['dir_database'].'cache/' ) as $oFileDir ){
    if( $oFileDir->isFile( ) && ( !isset( $sName ) || ( isset( $sName ) && strstr( $oFileDir->getFilename( ), $sName ) ) ) ){
      unlink( $config['dir_database'].'cache/'.$oFileDir->getFilename( ) );
    }
  } // end foreach
} // end function clearCache

/**
* List news from OpenSolution
* @return void
*/
function listMessagesNews( ){
  global $config;
  if( isset( $_COOKIE['iMessagesNewsTime'] ) && ( !isset( $_SESSION['iMessagesNewsTime'] ) || $_SESSION['iMessagesNewsTime'] != $_COOKIE['iMessagesNewsTime'] ) ){
    $_SESSION['iMessagesNewsTime'] = $_COOKIE['iMessagesNewsTime'];
    $_SESSION['iMessagesNewsNumber'] = 0;
  }
  if( isset( $_COOKIE['bMessagesNewsClear'] ) && isset( $_SESSION['sMessagesNews'] ) ){
    $_SESSION['sMessagesNews'] = str_replace( ' class="unread"', '', $_SESSION['sMessagesNews'] );
  }

  if( !isset( $_SESSION['sMessagesNews'] ) ){
    $aContent = getContentFromUrl( 'http://opensolution.org/list-messages.html' );
    if( isset( $aContent['aNews'] ) && is_array( $aContent['aNews'] ) ){
      $iTimeLast = isset( $_COOKIE['iMessagesNewsTime'] ) && is_numeric( $_COOKIE['iMessagesNewsTime'] ) ? $_COOKIE['iMessagesNewsTime'] : 0;
      $i = $_SESSION['iMessagesNewsNumber'] = 0;
      $content = null;
      
      foreach( $aContent['aNews'] as $aData ){
        if( $iTimeLast < $aData['iTime'] )
          $_SESSION['iMessagesNewsNumber']++;
        $content .= '<li'.( $iTimeLast < $aData['iTime'] ? ' class="unread"' : null ).'>'.'<a href="'.$aData['sLinkName'].'" target="_blank" class="head">'.( isset( $aData['sIcon'] ) ? $aData['sIcon'] : null ).$aData['sName'].'</a><a href="'.$aData['sLinkName'].'" target="_blank">'.changeTxt( $aData['sDescription'] ).'</a></li>';
        $i++;
        if( $i > 4 )
          break;
      } // end foreach
      $_SESSION['sMessagesNews'] = ( !empty( $aContent['sHead'] ) ? $aContent['sHead'] : null ).'<ul>'.$content.'</ul>'.( !empty( $aContent['sFoot'] ) ? $aContent['sFoot'] : null );
    }
  }
} // end function listMessagesNews

/**
* Lists notifications and alerts
* @return string
*/
function listMessagesNotices( ){
  global $lang, $config;

  if( !isset( $_SESSION['sMessagesNotices'] ) ){
    $iLocalhost = preg_match( '/localhost|192\.168\.|127\.0\.0\.1/', $_SERVER['HTTP_HOST'].$_SERVER['SERVER_ADDR'] );

    if( $config['failed_logs'] > 0 ){
      $aNotices[] = '<li>'.$lang['Failed_logs'].' <strong>'.displayDate( $config['failed_login_time'], $config['date_format_admin_default'] ).'</strong></li>';
    }
    
    if( isset( $config['failed_email_password_remind'] ) && $config['failed_email_password_remind'] > 0 ){
      $aNotices[] = '<li>'.$lang['Failed_password_remind'].' <strong>'.displayDate( $config['failed_email_password_remind_time'], $config['date_format_admin_default'] ).'</strong></li>';
    }

    if( is_dir( 'files/backup/' ) ){
      $iNewest = 0;
      foreach( new DirectoryIterator( 'files/backup/' ) as $oFileDir ){
        if( $oFileDir->isFile( ) && strstr( $oFileDir->getFilename( ), '.zip' ) && filemtime( 'files/backup/'.$oFileDir->getFilename( ) ) > $iNewest ){
          $iNewest = filemtime( 'files/backup/'.$oFileDir->getFilename( ) );
        }
      } // end foreach
      if( time( ) - $iNewest > 1209600 ){
        $aNotices[] = '<li>'.$lang['Backup_old'].' <a href="?p=backup&sOption=create" target="_blank">'.$lang['Backup_create'].' &raquo;</a></li>';
      }
    }

    if( !defined( 'LICENSE_NO_LINK' ) && is_dir( 'templates/'.$config['skin'].'/' ) ){
      foreach( new DirectoryIterator( 'templates/'.$config['skin'].'/' ) as $oFileDir ) {
        if( strstr( $oFileDir->getFilename( ), '.php' ) && preg_match( '/http:\/\/opensolution\.org|http:\/\/www\.opensolution\.org/i', file_get_contents( 'templates/'.$config['skin'].'/'.$oFileDir->getFilename( ) ) ) ){
          define( 'LICENSE_LINK_OK', true );
          break;
        }
      } // end foreach

      if( !defined( 'LICENSE_LINK_OK' ) )
        $aNotices[] = '<li>Restore link <strong>http://opensolution.org/</strong> located in the footer on your website <a href="http://opensolution.org/license.html" target="_blank">'.$lang['More'].' &raquo;</a></li>';
    }

    $aContent = getContentFromUrl( 'http://opensolution.org/list-messages.html' );
    if( is_array( $aContent ) ){
      if( !empty( $aContent['sErrors'] ) ){
        $aNotices[] = $aContent['sErrors'];
        if( !is_file( $config['dir_database'].'cache/verify-time' ) )
          file_put_contents( $config['dir_database'].'cache/verify-time', time( ) );
      }
      elseif( is_file( $config['dir_database'].'cache/verify-time' ) )
        unlink( $config['dir_database'].'cache/verify-time' );
    }

    if( empty( $iLocalhost ) && strstr( $_SERVER['REQUEST_URI'], 'admin.php' ) ){
      $aNotices[] = '<li>'.$lang['Increase_security'].' <a href="'.$config['manual_link'].'information#security" target="_blank">'.$lang['More'].' &raquo;</a></li>';
    } 

    if( defined( 'DEVELOPER_MODE' ) && !empty( $iLocalhost ) ){
      $aNotices[] = '<li>'.$lang['Localhost_recommend'].'</li>';
    }

    if( is_file( 'index.php' ) && ( time( ) - filemtime( 'index.php' ) > 6480000 ) && ( isset( $aNotices ) || rand( 1, 3 ) == 2 ) ){
      $aNotices[] = '<li>'.$lang['Check_for_bug_fixes'].' <a href="?p=bugfixes" target="_blank">'.$lang['More'].' &raquo;</a></li>';
    }

    if( isset( $aNotices ) ){
      $_SESSION['sMessagesNotices'] = '<ul>'.implode( '', $aNotices ).'</ul>';
      $_SESSION['iMessagesNoticesNumber'] = count( $aNotices );
    }
  }
} // end function listMessagesNotices

/**
* Displays the lists of backup files
* @return string
*/
function listBackupFiles( ){
  global $lang, $config;

  if( !is_dir( 'files/backup/' ) )
    return null;

  foreach( new DirectoryIterator( 'files/backup/' ) as $oFileDir ) {
    if( $oFileDir->isFile( ) && strstr( $oFileDir->getFilename( ), '.zip' ) ){
      $aFiles[] = $oFileDir->getFilename( );
    }
  } // end foreach

  $content = null;
  if( isset( $aFiles ) ){
    rsort( $aFiles );
    $iCount = count( $aFiles );
    for( $i = 0; $i < $iCount; $i++ ){
      $content .= '<tr class="l'.( ( $i % 2 ) ? 0: 1 ).'"><th class="name">'.$aFiles[$i].'</th><td class="date">'.substr( $aFiles[$i], 7, 10 ).' '.str_replace( '-', ':', substr( $aFiles[$i], 18, 5 ) ).'</td><td class="size">'.sprintf( '%01.2f', ( ( filesize( 'files/backup/'.$aFiles[$i] ) / 1024 ) ) ).' KB</td><td class="options">'.( !isset( $config['disable_backup_restore'] ) ? '<a href="?p=backup&amp;sOption=restore&amp;sFile='.$aFiles[$i].'" class="restore" onclick="return confirm( aQuick.sConfirmShure );">'.$lang['Backup_restore'].'</a> ' : null ).'<a href="?p=backup&amp;sItemDelete='.$aFiles[$i].'" class="delete" onclick="return del( this )">'.$lang['Delete'].'</a></td></tr>';
    } // end for
  }

  if( isset( $content ) )
    return $content;
} // end function listBackupFiles

/**
* Restores backup
* @return void
* @param string $sFile
*/
function restoreBackup( $sFile ){
  global $config;
  if( !isset( $config['restore_backup'] ) )
    $config['restore_backup'] = 0;

  if( $config['restore_backup'] == 0 ){
    $oIJ = ImageJobs::getInstance( );
    $oIJ->truncateDir( $config['dir_database'] );
    $oBackup = new PclZip( 'files/backup/'.$sFile );
    $oBackup->extract( );
  }
  elseif( $config['restore_backup'] == 1 ){
    $oBackup = new PclZip( 'files/backup/'.$sFile );
    $oBackup->extract( PCLZIP_OPT_BY_PREG, '/config|lang/', PCLZIP_OPT_REPLACE_NEWER );
  }
  elseif( $config['restore_backup'] == 2 ){
    clearCache( );
    $oBackup = new PclZip( 'files/backup/'.$sFile );
    $oBackup->extract( PCLZIP_OPT_BY_PREG, '/'.preg_quote( basename( $config['database'] ) ).'/', PCLZIP_OPT_REPLACE_NEWER );
  }
} // end function restoreBackup

/**
* Lists functions names from templates/SKIN_NAME/_lists.php
* @return string
* @param string $sFunctionSelect
* @param mixed $mFilterFunctions
*/
function listListFunctions( $sFunctionSelect = null, $mFilterFunctions = null ){
  global $config;
  if( is_file( 'templates/'.$config['skin'].'/_lists.php' ) ){
    $aFile = file( 'templates/'.$config['skin'].'/_lists.php' );
    foreach( $aFile as $sLine ){
      if( strstr( $sLine, 'function ' ) && strstr( $sLine, '(' ) ){
        $aExp = explode( '(', $sLine );
        $sFunctionName = trim( str_replace( 'function', '', $aExp[0] ) );
        if( ( isset( $mFilterFunctions ) && preg_match( '/'.$mFilterFunctions.'/', $sFunctionName ) ) || !isset( $mFilterFunctions ) ){
          $aFunctions[$sFunctionName] = $sFunctionName;
        }
      }
    } // end foreach

    if( isset( $aFunctions ) ){
      $content = null;
      foreach( $aFunctions as $sFunctionName ){
        $content .= '<option value="'.$sFunctionName.'"'.( ( isset( $sFunctionSelect ) && $sFunctionSelect == $sFunctionName ) ? ' selected="selected"' : null ).'>'.$sFunctionName.'</option>';
      } // end foreach
      return $content;
    }
  }
} // end function listListFunctions

/**
* Gets content from url
* @return midex
* @param string $sUrl
* @param bool $bReturnUrl
*/
function getContentFromUrl( $sUrl, $bReturnUrl = null ){
  global $config;
  if( isset( $config[$sUrl] ) )
    return $config[$sUrl];
  throwSiteUrls( );
  if( ( isset( $_SESSION['iGetDataFromUrlError'] ) && $_SESSION['iGetDataFromUrlError'] <= 2 ) || !isset( $_SESSION['iGetDataFromUrlError'] ) ){
    $sUrlComplete = $sUrl.( strstr( $sUrl, '/opensolution.org' ) ? ( strstr( $sUrl, '?' ) ? '&' : '?' ).'sLang='.$config['admin_lang'].'&sUrl='.$config['url_domain'].'&sScript=Quick.Cms.Ext&sVersion='.$config['version'].'&mParam='.$config['url_param'].( defined( 'DEVELOPER_MODE' ) ? '&amp;bDeveloper=' : null ) : null );
    if( isset( $bReturnUrl ) )
      return $sUrlComplete;
    else
      $mContent = @file_get_contents( $sUrlComplete );
  }
  else
    return null;

  if( $mContent === false ){
    if( !isset( $config['checked_downloading_content'] ) ){
      $config['checked_downloading_content'] = true;
      if( !isset( $_SESSION['iGetDataFromUrlError'] ) )
        $_SESSION['iGetDataFromUrlError'] = 0;

      $_SESSION['iGetDataFromUrlError']++;
    }
    return false;
  }
  else{
    $config['checked_downloading_content'] = true;
    if( $sUrl == 'http://opensolution.org/list-messages.html' ){
      $config[$sUrl] = @unserialize( $mContent );
      if( is_array( $config[$sUrl] ) )
        return $config[$sUrl];
    }
    else{
      return $mContent;
    }
  }
} // end function getContentFromUrl

/**
* Checks if there is a code in the file or the file itself
* @return bool
* @param string $sInstruction
*/
function verifyCodeInFile( $sInstruction ){
  if( !empty( $sInstruction ) ){
    $aExp = explode( ',', $sInstruction );
    $aExp[0] = trim( $aExp[0] );
    if( $aExp[0] == 'admin.php' )
      $aExp[0] = $GLOBALS['config']['admin_file'];
    $aExp[1] = isset( $aExp[1] ) ? trim( $aExp[1] ) : null;
    if( !empty( $aExp[1] ) )
      return ( is_file( $aExp[0] ) && preg_match( $aExp[1], file_get_contents( $aExp[0] ) ) ) ? true : false;
    else
      return is_file( $aExp[0] ) ? true : false;
  }
  return null;
} // end function verifyCodeInFile
?>