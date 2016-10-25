<?php
/**
* List of all available plugins
* @return string
* @param string $sVersion
*/
function listPlugins( ){
  global $lang, $config;

  if( !isset( $_SESSION['sPluginsList'] ) ){
    $sPlugins = getContentFromUrl( 'http://opensolution.org/plugins.html' );
    if( !empty( $sPlugins ) )
      $_SESSION['sPluginsList'] = $sPlugins;
  }

  if( isset( $_SESSION['sPluginsList'] ) ){
    $sInstalledPlugins = null;
    if( isset( $config['plugins_installed'] ) && !empty( $config['plugins_installed'] ) ){
      $aPluginsInstalled = unserialize( $config['plugins_installed'] );
      $sInstalledPlugins = null;
      foreach( $aPluginsInstalled as $sPluginName => $bValue ){
        $sInstalledPlugins .= '"'.$sPluginName.'", ';
      } // end foreach
      $sInstalledPlugins = '<script>var aPluginsInstalled = [ '.$sInstalledPlugins.' ];</script>';
    }
    return Array( $sInstalledPlugins, $_SESSION['sPluginsList'] );
  }
} // end function listPlugins

/**
* Install plugin
* @return string
* @param string $sPlugin
*/
function installPlugin( $sPlugin ){
  global $lang, $config;

  if( !function_exists( 'gzcompress' ) ){
    return $lang['Plugin_gzcompress_error'];
  }

  if( isset( $config['plugins_installed'] ) && !empty( $config['plugins_installed'] ) )
    $aPluginsInstalled = unserialize( $config['plugins_installed'] );
  if( isset( $aPluginsInstalled ) && isset( $aPluginsInstalled[$sPlugin] ) )
    return $lang['Plugin_already_installed_error'];

  $sInstruction = getContentFromUrl( 'http://opensolution.org/plugins-install.html?sPlugin='.$sPlugin );

  if( !empty( $sInstruction ) ){
    $mReturn = installPluginSteps( $sPlugin, unserialize( $sInstruction ) );
    if( $mReturn === true ){
      if( $sPlugin != 'pagesNewField' ){
        $aPluginsInstalled[$sPlugin] = true;
        updateBin( 'plugins_installed', '\''.serialize( $aPluginsInstalled ).'\'', true );
      }
      return 'success';
    }
    else{
      if( isset( $mReturn['aNotWriteableFiles'] ) ){
        return '<h2>'.$lang['Plugin_file_write_error'].'</h2><ul><li>'.implode( '</li><li>', $mReturn['aNotWriteableFiles'] ).'</li></ul>';
      }
      elseif( isset( $mReturn['aCodeSearchNotPassed'] ) ){
        $content = null;
        foreach( $mReturn['aCodeSearchNotPassed'] as $iStep => $sFile ){
          $content .= '<li>'.$sFile.'-'.$iStep.'</a></li>';
        }
        return '<h2>'.$lang['Plugin_install_error_header'].'</h2><ul>'.$content.'</li></ul>'.$lang['Plugin_install_error_footer'].'</div>';
      }
      else{
        return $mReturn;
      }
    }
  }
  else{
    return 1;  
  }

} // end function installPlugin

/**
* Modify original script code and install plugin
* @return mixed
* @param string $sPlugin
* @param array $aInstruction
*/
function installPluginSteps( $sPlugin, $aInstruction ){
  global $lang, $config;

  if( is_array( $aInstruction ) && isset( $aInstruction['steps'] ) ){
    if( isset( $aInstruction['file_download'] ) ){
      if( !is_dir( 'files/backup/' ) )
        mkdir( 'files/backup/' );
      if( !is_file( 'files/backup/'.$aInstruction['file_download'] ) && @copy( getContentFromUrl( 'http://opensolution.org/plugins-install.html?sPlugin='.$sPlugin.'&bDownloadFile', true ), 'files/backup/'.$aInstruction['file_download'] ) ){
        chmod( 'files/backup/'.$aInstruction['file_download'], FILES_CHMOD );
      }
      else{
        return $lang['Plugin_download_error'];  
      }
    }

    foreach( $aInstruction['steps'] as $iStep => $aValue ){
      if( isset( $aValue['file'] ) ){
        $aFilesWas[$aValue['file']] = true;
        if( $aValue['file'] == 'database/lang_en.php' ){
          if( !is_file( $aValue['file'] ) )
            unset( $aInstruction['steps'][$iStep] );

          foreach( new DirectoryIterator( 'database/' ) as $oFileDir ){
            if( $oFileDir->isFile( ) && $oFileDir->isWritable( ) && preg_match( '/lang_[a-z]{2}\.php/', $oFileDir->getFilename( ) ) && !isset( $aFilesWas['database/'.$oFileDir->getFilename( )] ) ){
              $aStepsAdd[] = array_merge( $aValue, Array( 'file' => 'database/'.$oFileDir->getFilename( ), 'step' => $iStep ) );
            }
          } // end foreach
        }
        elseif( $aValue['file'] == 'database/lang_pl.php' || $aValue['file'] == 'database/config_pl.php' ){
          if( !is_file( $aValue['file'] ) )
            unset( $aInstruction['steps'][$iStep] );
        }
        elseif( $aValue['file'] == 'admin.php' ){
          $aInstruction['steps'][$iStep]['file'] = $config['admin_file'];
        }
        elseif( $aValue['file'] == 'database/config_en.php' ){
          if( !is_file( $aValue['file'] ) )
            unset( $aInstruction['steps'][$iStep] );

          foreach( new DirectoryIterator( 'database/' ) as $oFileDir ){
            if( $oFileDir->isFile( ) && $oFileDir->isWritable( ) && preg_match( '/config_[a-z]{2}\.php/', $oFileDir->getFilename( ) ) && !isset( $aFilesWas['database/'.$oFileDir->getFilename( )] ) ){
              $aStepsAdd[] = array_merge( $aValue, Array( 'file' => 'database/'.$oFileDir->getFilename( ), 'step' => $iStep ) );
            }
          } // end foreach
        }
      }
    } // end foreach

    if( isset( $aStepsAdd ) ){
      $aInstruction['steps'] = array_merge( $aInstruction['steps'], $aStepsAdd );
    }

    foreach( $aInstruction['steps'] as $iStep => $aValue ){
      if( isset( $aValue['file'] ) ){
        if( is_file( $aValue['file'] ) && is_writeable( $aValue['file'] ) ){
          $aFilesPassed[$iStep] = $iStep;
        }
        else{
          $aFilesNotPassed[$aValue['file']] = $aValue['file'];
        }
      }
      elseif( isset( $aValue['SQL'] ) ){
        $aSqlQueries[] = $aValue['SQL'];
      }
    } // end foreach

    if( isset( $aFilesNotPassed ) ){
      if( isset( $aInstruction['file_download'] ) && is_file( 'files/backup/'.$aInstruction['file_download'] ) )
        unlink( 'files/backup/'.$aInstruction['file_download'] );
      return Array( 'aNotWriteableFiles' => $aFilesNotPassed );
    }
    else{
      foreach( $aFilesPassed as $iKey ){
        $aValue = $aInstruction['steps'][$iKey];

        $sFileContent = file_get_contents( $aValue['file'] );
        if( strstr( $sFileContent, $aValue['find'] ) ){
          $aCodeSearchPassed[$iKey] = $iKey;
        }
        elseif( isset( $aValue['find_else'] ) && strstr( $sFileContent, $aValue['find_else'] ) ){
          $aCodeSearchPassed[$iKey] = $iKey;
          $aCodeSearchPassedElse[$iKey] = $iKey;
        }
        else{
          if( !isset( $aValue['exception'] ) )
            $aCodeSearchNotPassed[( isset( $aValue['step'] ) && is_numeric( $aValue['step'] ) ? ++$aValue['step'] : ++$iKey )] = $aValue['file'];
        }
      } // end foreach

      if( isset( $aCodeSearchNotPassed ) ){
        if( isset( $aInstruction['file_download'] ) && is_file( 'files/backup/'.$aInstruction['file_download'] ) )
          unlink( 'files/backup/'.$aInstruction['file_download'] );
        return Array( 'aCodeSearchNotPassed' => $aCodeSearchNotPassed );
      }
      else{
        foreach( $aCodeSearchPassed as $iKey ){
          $aValue = $aInstruction['steps'][$iKey];
          if( !isset( $aValue['exception'] ) || ( isset( $aValue['if_not_exists'] ) && verifyCodeInFile( $aValue['if_not_exists'] ) === false ) ){
            file_put_contents( $aValue['file'], str_replace( ( isset( $aCodeSearchPassedElse[$iKey] ) ? $aValue['find_else'] : $aValue['find'] ), ( isset( $aCodeSearchPassedElse[$iKey] ) ? $aValue['replace_else'] : $aValue['replace'] ), file_get_contents( $aValue['file'] ) ) );
          }
        } // end foreach

        if( isset( $aSqlQueries ) ){
          $oSql = Sql::getInstance( );
          foreach( $aSqlQueries as $sQuery ){
            $oSql->query( trim( $sQuery ) );
          } // end foreach
        }

        if( isset( $aReturn ) )
          return $aReturn;
        else{
          if( isset( $aInstruction['file_download'] ) && is_file( 'files/backup/'.$aInstruction['file_download'] ) ){
            $oZip = new PclZip( 'files/backup/'.$aInstruction['file_download'] );
            $oZip->extract( );
            unlink( 'files/backup/'.$aInstruction['file_download'] );
          }
          return true;
        }
      }
    }
  }
} // end function installPluginSteps

/**
* Checks if the script was uploaded to a server in binary mode
* @return bool
*/
function checkBinaryMode( ){
  if( strstr( file_get_contents( 'core/plugins-admin.php' ), base64_decode( 'ZWxzZXsNCiAgICAkR0xPQkFMU1snY29uZmlnJ11bJ2Rpc2FibGVfcGx1Z2luc19pbnN0YWxsJ10gPSB0cnVlOw0KICAgIHJldHVybiBmYWxzZTsNCiAgfQ==' ) ) )
    return true;
  else{
    $GLOBALS['config']['disable_plugins_install'] = true;
    return false;
  }
} // end function checkBinaryMode
?>