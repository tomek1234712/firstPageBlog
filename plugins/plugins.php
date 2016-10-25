<?php
/**
* Displays javascript plugins files to load in head and for demand
* @param mFileScript
* @return string
*/
function displayJavaScripts( $mFileScript ){
  global $config, $lang, $aData;

  if( !isset( $config['loaded_scripts'][$mFileScript] ) ){
    if( is_file( $mFileScript ) ){
      $content = '<script src="'.$mFileScript.'"></script>';
    }
    if( strstr( $mFileScript, 'quick.form' ) ){
      $content .= '<script>if( typeof aCF === "undefined" ) aCF = {};';
      $content .= 'aCF["sWarning"] = "'.$lang['Fill_required_fields'].'";';
      $content .= 'aCF["sInt"] = "'.$lang['Wrong_value'].'";';
      $content .= 'aCF["sEmail"] = "'.$lang['Type_correct_email'].'";</script>';
    }
    if( strstr( $mFileScript, 'galleria' ) ){
      $content .= '<script>
        Galleria.loadTheme("plugins/galleria/themes/classic/galleria.classic.js");
        Galleria.run(".images-7", {
          _toggleInfo: false,
          autoplay: 4000,
        });
      </script>';
    }
    // plugins
    $config['loaded_scripts'][$mFileScript] = true;
  }

  if( isset( $content ) )
    return $content;
} // end function displayJavaScripts

?>