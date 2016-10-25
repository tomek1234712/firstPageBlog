<?php
/**
* Gets the fixes file and applies fixes to the script
* @return string
*/
function listBugFixes( ){
  global $config, $lang;

  if( !isset( $_SESSION['mBugFixes'] ) ){
    $_SESSION['mBugFixes'] = getContentFromUrl( 'http://opensolution.org/bugfixes.html' );
  }

  if( !empty( $_SESSION['mBugFixes'] ) ){
    if( $_SESSION['mBugFixes'] == 'no-bugs' )
      return true;

    $aBugs = unserialize( $_SESSION['mBugFixes'] );
    $i = 0;
    $content = null;
    foreach( $aBugs as $iBug => $aData ){
      $aData['sStatus'] = $lang['Cant_check'];
      if( isset( $aData['aSteps'] ) || isset( $aData['bDontVerifySteps'] ) ){
        if( isset( $aData['sPluginVerify'] ) && verifyCodeInFile( $aData['sPluginVerify'] ) === false ){
          $aData['sName'] = null;
        }
        if( isset( $aData['sName'] ) && isset( $aData['aSteps'] ) ){
          $iCount = count( $aData['aSteps'] );
          $iOk = 0;
          foreach( $aData['aSteps'] as $aSteps ){
            $mReturn = checkFileToUpgrade( $aSteps );
            if( isset( $mReturn ) && $mReturn === true ){
              $iOk++;
            }
            else
              break;
          } // end foreach
          $aData['sStatus'] = ( $iOk == $iCount ) ? $lang['Fixed'] : ( ( $iOk > 0 ) ? $lang['Uncompleted'] : '<strong>'.$lang['Fix_it'].'</strong>' );
        }
      }

      if( isset( $aData['sName'] ) ){
        $content .= '<tr class="level-'.$aData['iLevel'].'"><td class="name">'.$aData['sName'].'</td><td class="status">'.( isset( $aData['sStatus'] ) ? $aData['sStatus'] : null ).'</td><td class="options"><a href="'.$config['bugfixes_link'].'?sBug='.base64_encode( $config['version'].'-'.$iBug ).'" target="_blank" class="manual" title="'.$lang['More'].'">'.$lang['More'].'</a></td></tr>';
      }
    } // end foreach

    if( isset( $content ) )
      return $content;
  }
} // end function listBugFixes

/**
* Checks if a fiven bug is fixed
* @return mixed
*/
function checkFileToUpgrade( $aSteps ){
  if( $aSteps[0] == 'admin.php' )
    $aSteps[0] = $GLOBALS['config']['admin_file'];

  if( is_file( $aSteps[0] ) ){
    $sContent = file_get_contents( $aSteps[0] );
    if( !empty( $aSteps[2] ) ){
      return ( strstr( $sContent, $aSteps[2] ) || strstr( $sContent, str_replace( "\r", "", $aSteps[2] ) ) ) ? true : false;
    }
    else{
      return !strstr( $sContent, $aSteps[1] ) && !strstr( $sContent, str_replace( "\r", "", $aSteps[1] ) ) ? true : false;
    }
  }

  return null;
} // end function checkFileToUpgrade
?>