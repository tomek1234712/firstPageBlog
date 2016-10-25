<?php
final class FilesAdmin extends Files
{

  private $aDirs;
  private $aFilesAll = null;
  private static $oInstance = null;

  public static function getInstance( $mValue = null ){  
    if( !isset( self::$oInstance ) ){  
      self::$oInstance = new FilesAdmin( );  
    }  
    return self::$oInstance;  
  } // end function getInstance

  /**
  * Constructor
  * @return void
  */
  private function __construct( ){
    $this->generateThumbDirs( );
  } // end function __construct

  /**
  * Returns thumbs directory names
  * @return array
  */
  private function generateThumbDirs( ){
    foreach( new DirectoryIterator( 'files/' ) as $oFileDir ) {
      if( is_numeric( $oFileDir->getFilename( ) ) && $oFileDir->isDir( ) ){
        $this->aDirs[$oFileDir->getFilename( )] = $oFileDir->getFilename( );
      }
    } // end foreach
  } // end function generateThumbDirs

  /**
  * Returns list of files in a directory
  * @return string
  * @param array $aParametersExt
  * Default options: sSort, bDontDisplayAllOption, bDisplayAll
  */
  public function listFilesInDir( $aParametersExt = null ){
    global $lang, $config;
    $content = null;
    $iTimeFile = 1;
    foreach( new DirectoryIterator( 'files/' ) as $oFileDir ) {
      $sFileName = $oFileDir->getFilename( );
      if( $oFileDir->isFile( ) && $sFileName != '.htaccess' ){
        if( isset( $aParametersExt['sSort'] ) && $aParametersExt['sSort'] == 'time' )
          $aFiles[$sFileName] = Array( filemtime( 'files/'.$sFileName ) );
        else
          $aFiles[$sFileName] = Array( $sFileName, filemtime( 'files/'.$sFileName ) );
      }
    } // end foreach

    if( isset( $aFiles ) ){
      $oIJ = ImageJobs::getInstance( );

      if( isset( $aParametersExt['sSort'] ) && $aParametersExt['sSort'] == 'time' ){
        arsort( $aFiles );
        $iTimeFile = 0;
      }
      else{
        asort( $aFiles );
      }

      $iTime = time( );
      $i = 0;
      $iCount = count( $aFiles );
      foreach( $aFiles as $sFileName => $aValue ){
        $content .= '<tr class="l'.( ( $i % 2 ) ? 0: 1 ).( ( $iTime - $aValue[$iTimeFile] < 1200 ) ? ' time' : null ).'" id="fileTr'.$i.'"><td class="select custom"><input type="checkbox" name="aDirFiles['.$i.']" value="'.$sFileName.'" data-i="'.$i.'" data-img="'.( ( $oIJ->checkCorrectFile( $sFileName, $config['allowed_image_extensions'] ) == true ) ? 1 : 0 ).'" '.( isset( $_SESSION['aUploadedFiles'][$sFileName] ) ? 'checked="checked"' : null ).' id="oDF-'.$i.'" /><label for="oDF-'.$i.'">'.$lang['Delete'].'</label></td><td class="file"><a href="files/'.$sFileName.'" target="_blank">'.$sFileName.'</a></td><td class="position">&nbsp;</td><td class="description">&nbsp;</td><td class="location">&nbsp;</td><td class="thumb">&nbsp;</td><td class="crop">&nbsp;</td></tr>';
        $i++;
        if( !isset( $aParametersExt['bDisplayAll'] ) && $i >= $config['dir_files_list_limit'] ){
          if( !isset( $aParametersExt['bDontDisplayAllOption'] ) )
            $bDisplayAllOption = true;
          break;
        }
      } // end foreach

      if( isset( $_SESSION['aUploadedFiles'] ) ){
        unset( $_SESSION['aUploadedFiles'] );
        $bDisplayAllOption = null;
      }

      return '<li class="files-dir-head"><table><caption>'.$lang['Files_on_server'].'</caption><tbody><tr><th class="select">'.$lang['Select'].'</th><th class="file">'.$lang['File'].'</th><th class="position hidden">'.$lang['Position'].'</th><th class="description hidden">'.$lang['Description'].'</th><th class="location hidden">'.$lang['Location'].'</th><th class="thumb hidden">'.$lang['Thumbnail'].'</th><th class="crop hidden">'.$lang['Crop'].'</th></tr><tr><th>&nbsp;</th><th class="file"><input type="text" name="sFilesInDirPhrase" value="" size="50" class="search" onkeyup="listSearch( this, \'files-dir-table\' )" placeholder="'.$lang['search'].'" /></th><th colspan="5">&nbsp;</th></tr></tbody></table></li><li class="files-dir-body"><table id="files-dir-table">'.( isset( $bDisplayAllOption ) ? '<thead><tr><th colspan="7" class="display-files-all">'.$lang['Last_added_files_only'].' <a href="#">'.$lang['See_all_files'].'</a></th></tr></thead><tfoot><tr><th colspan="7" class="display-files-all"><a href="#">'.$lang['See_all_files'].' &ddarr;</a></th></tr></tfoot>' : null ).'<tbody>'.$content.'</tbody></table><div class="space">&nbsp;</div></li>';
    }
  } // end function listFilesInDir

  /**
  * Uploads file to a server
  * @return string
  * @param string $sFileName
  */
  public function uploadFile( $sFileName ){
    global $config;
    $oIJ = ImageJobs::getInstance( );
    if( $oIJ->checkCorrectFile( $sFileName, $config['allowed_not_image_extensions'] ) || $oIJ->checkCorrectFile( $sFileName, $config['allowed_image_extensions'] ) ){
      $sFileNameNew = $oIJ->checkIsFile( $sFileName, 'files/' );
      if( ( isset( $_FILES['sFileName']['tmp_name'] ) && move_uploaded_file( $_FILES['sFileName']['tmp_name'], 'files/'.$sFileNameNew ) ) || file_put_contents( 'files/'.$sFileNameNew, file_get_contents( "php://input" ) ) ){
        $_SESSION['aUploadedFiles'][$sFileNameNew] = true;
        return '{"success":true'.( ( $oIJ->checkCorrectFile( $sFileName, $config['allowed_image_extensions'] ) && $oIJ->checkImgMaxDimension( 'files/'.$sFileNameNew ) !== true ) ? ', "size_info":true' : null ).'}';
      }
      else{
        return '{"success":false}';
      }
    }
    else{
      return '{error:"Incorrect extension"}';
    }
  } // end function uploadFile

  /**
  * Lists all files on selected page
  * @return string
  * @param int $iPage
  */
  public function listAllFiles( $iPage ){
    global $config, $lang;

    $content = null;
    $oSql = Sql::getInstance( );
    $oQuery = $oSql->getQuery( 'SELECT * FROM files WHERE iPage = "'.$iPage.'" ORDER BY iType DESC, iPosition ASC, sFileName ASC' );
    $i = 0;
    while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
      $content .= '<tr class="l'.( ( $i % 2 ) ? 0: 1 ).'"><td class="custom"><input type="checkbox" name="aFilesDelete['.$aData['iFile'].']" id="oFD-'.$aData['iFile'].'" class="delete" value="'.$aData['iFile'].'" data-img="'.$aData['iSizeDetails'].'" /><label for="oFD-'.$aData['iFile'].'">'.$lang['Delete'].'</label></td><td class="name"><a href="files/'.$aData['sFileName'].'"'.( $aData['iSizeDetails'] > 0 ? ' title="'.$aData['sDescription'].'" class="quickbox[images]"' : null ).' target="_blank">'.$aData['sFileName'].'</a></td><td class="position"><input type="text" name="aFilesPositions['.$aData['iFile'].']" value="'.$aData['iPosition'].'" size="2" maxlength="4" class="numeric" /></td><td class="description"'.( ( $aData['iSizeDetails'] == 0 ) ? ' colspan="3"' : null ).'><input type="text" name="aFilesDescription['.$aData['iFile'].']" value="'.$aData['sDescription'].'" size="20" class="input description"  /></td>'.( ( $aData['iSizeDetails'] > 0 ) ? '<td class="default adv"><input type="radio" name="iDefaultImage" value="'.$aData['iFile'].'"'.( $aData['iDefault'] == 1 ? ' checked="checked"' : null ).'/></td><td class="location"><select name="aFilesTypes['.$aData['iFile'].']">'.getSelectFromArray( $config['images_locations'], $aData['iType'] ).'</select></td><td class="thumb adv-toogle"><select name="aFilesSizes['.$aData['iFile'].']">'.getThumbnailsSelect( ( ( $aData['iSizeDetails'] == $aData['iSizeLists'] && $aData['iSizeDetails'] == $aData['iSizeOther'] ) ? $aData['iSizeOther'] : null ), true ).'</select></td><td class="thumb adv"><select name="aFilesSizesDetails['.$aData['iFile'].']">'.getThumbnailsSelect( $aData['iSizeDetails'] ).'</select></td><td class="thumb adv"><select name="aFilesSizesLists['.$aData['iFile'].']">'.getThumbnailsSelect( $aData['iSizeLists'] ).'</select></td><td class="thumb adv"><select name="aFilesSizesOther['.$aData['iFile'].']">'.getThumbnailsSelect( $aData['iSizeOther'] ).'</select></td><td class="crop adv"><select name="aFilesCrops['.$aData['iFile'].']">'.getSelectFromArray( $config['crop_options'], $aData['iCrop'] ).'</select></td>' : '<td colspan="4" class="adv">&nbsp;</td>' ).'</tr>';
      $i++;
    } // end while

    if( isset( $content ) ){
      return '<input type="hidden" name="iChangedFiles" id="iChangedFiles" value="1" /><table id="files-list">'.( !isset( $config['disable_page_files_advanced_options'] ) ? '<caption><em>'.$lang['Advanced_options'].'</em> <a href="#" class="expand"><span class="display">'.$lang['Display'].'</span><span class="hide">'.$lang['Hide'].'</span></a></caption>' : null ).'<thead><tr><th class="delete">'.$lang['Delete'].'<div class="custom"><input type="checkbox" name="delete-all" id="delete-all" /><label for="delete-all">'.$lang['Delete'].'</label></div></th><th class="name">'.$lang['File'].'</th><th class="position">'.$lang['Position'].'</th><th class="description">'.$lang['Description'].'</th><th class="default adv">'.$lang['Default'].'</th><th class="location">'.$lang['Location'].'</th><th class="thumb adv-toogle">'.$lang['Thumbnail'].'</th><th class="thumb adv">'.$lang['Thumbnail_details'].'</th><th class="thumb adv">'.$lang['Thumbnail_lists'].'</th><th class="thumb adv">'.$lang['Thumbnail_other'].'</th><th class="crop adv">'.$lang['Crop'].'</th></tr></thead><tbody>'.$content.'</tbody></table>';
    }
  } // end function listAllFiles

  /**
  * Adds files from a server
  * @param array  $aForm
  * @param int    $iPage
  */
  public function addFilesFromServer( $aForm, $iPage ){
    global $config;

    if( isset( $aForm['aDirFiles'] ) ){
      $oIJ = ImageJobs::getInstance( );
      $oSql = Sql::getInstance( );

      foreach( $aForm['aDirFiles'] as $iKey => $sFile ){
        if( is_file( 'files/'.$sFile ) ){
          $sFileRaw = null;
          if( isset( $config['change_files_names'] ) ){
            if( isset( $aForm['sName'] ) && !empty( $aForm['sName'] ) ){
              $sFileRaw = $sFile;
              $sFile = $oIJ->checkIsFile( $aForm['sName'].'.'.$oIJ->getExtOfFile( $sFile ), 'files/' );
            }
          }
          else{
            if( $oIJ->changeFileName( $oIJ->getNameOfFile( $sFile ) ).'.'.$oIJ->changeFileName( $oIJ->getExtOfFile( $sFile ) ) != $sFile ){
              $sFileRaw = $sFile;
              $sFile = $oIJ->checkIsFile( $sFile, 'files/' );
            }
          }

          if( isset( $sFileRaw ) && !is_file( 'files/'.$sFile ) )
            copy( 'files/'.$sFileRaw, 'files/'.$sFile );
          $iSize = ( isset( $aForm['aDirFilesSizes'][$iKey] ) && $oIJ->checkCorrectFile( $sFile, $config['allowed_image_extensions'] ) ) ? $aForm['aDirFilesSizes'][$iKey] : 0;
          $oSql->query( 'INSERT INTO files ( sFileName, iSizeDetails, iSizeLists, iSizeOther, iType, iPosition, sDescription, iDefault, iPage, iCrop ) VALUES ( "'.$sFile.'", "'.$iSize.'", "'.$iSize.'", "'.$iSize.'", "'.( ( isset( $aForm['aDirFilesTypes'][$iKey] ) && is_numeric( $aForm['aDirFilesTypes'][$iKey] ) ) ? $aForm['aDirFilesTypes'][$iKey] : 1 ).'", "'.( ( isset( $aForm['aDirFilesPositions'][$iKey] ) && is_numeric( $aForm['aDirFilesPositions'][$iKey] ) ) ? $aForm['aDirFilesPositions'][$iKey] : 0 ).'", "'.changeTxt( trim( $aForm['aDirFilesDescriptions'][$iKey] ), 'ndnl' ).'", 0, "'.$iPage.'", "'.( ( isset( $aForm['aDirFilesCrop'][$iKey] ) && is_numeric( $aForm['aDirFilesCrop'][$iKey] ) ) ? $aForm['aDirFilesCrop'][$iKey] : 0 ).'" )' );

          if( $iSize > 0 ){
            $this->generateThumbs( $sFile, $iSize, ( isset( $aForm['aDirFilesCrop'][$iKey] ) ? $aForm['aDirFilesCrop'][$iKey] : null ) );
          }

          if( isset( $sFileRaw ) ){
            $this->deleteFilesFromDirs( $sFileRaw, $iSize );
          }
        }
      }
    }
  } // end function addFilesFromServer

  /**
  * Saves data of files and images (description, position etc.) to flat files database
  * @return void
  * @param array $aForm
  * @param int $iPage
  */
  public function saveFiles( $aForm, $iPage = null ){
    global $config;

    if( isset( $aForm['aFilesDescription'] ) && is_array( $aForm['aFilesDescription'] ) ){
      if( isset( $aForm['aFilesDelete'] ) ){
        $this->deleteSelectedFiles( $aForm['aFilesDelete'] );
      }
      $oSql = Sql::getInstance( );
      $oQuery = $oSql->getQuery( 'SELECT * FROM files WHERE iPage = "'.$iPage.'" ORDER BY iPosition ASC' );
      while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
        if( !isset( $aForm['aFilesDelete'][$aData['iFile']] ) && isset( $aForm['aFilesDescription'][$aData['iFile']] ) ){
          
          if( isset( $aForm['aFilesCrops'][$aData['iFile']] ) && $aForm['aFilesCrops'][$aData['iFile']] != $aData['iCrop'] ){
            $aUpdate[$aData['iFile']][] = 'iCrop = "'.$aForm['aFilesCrops'][$aData['iFile']].'"';
            $aVerifyFilesCrop[$aData['sFileName']] = Array( $aForm['aFilesCrops'][$aData['iFile']], $aData['iCrop'] );
          }

          if( isset( $aForm['aFilesSizes'][$aData['iFile']] ) && is_numeric( $aForm['aFilesSizes'][$aData['iFile']] ) ){
            $aForm['aFilesSizesDetails'][$aData['iFile']] = $aForm['aFilesSizesLists'][$aData['iFile']] = $aForm['aFilesSizesOther'][$aData['iFile']] = $aForm['aFilesSizes'][$aData['iFile']];
          }

          if( isset( $aForm['aFilesSizesDetails'][$aData['iFile']] ) && ( ( $aForm['aFilesSizesDetails'][$aData['iFile']] != $aData['iSizeDetails'] && $aData['iSizeDetails'] > 0 ) || ( isset( $aVerifyFilesCrop[$aData['sFileName']] ) ) ) ){
            $aUpdate[$aData['iFile']][] = 'iSizeDetails = "'.$aForm['aFilesSizesDetails'][$aData['iFile']].'"';
            $aUpdateSizeDetails[$aData['iFile']][] = $aData['sFileName'];
            $aUpdateSizeDetails[$aData['iFile']][] = $aForm['aFilesSizesDetails'][$aData['iFile']];
          }

          if( isset( $aForm['aFilesSizesLists'][$aData['iFile']] ) && ( ( $aForm['aFilesSizesLists'][$aData['iFile']] != $aData['iSizeLists'] && $aData['iSizeLists'] > 0 ) || ( isset( $aVerifyFilesCrop[$aData['sFileName']] ) ) ) ){
            $aUpdate[$aData['iFile']][] = 'iSizeLists = "'.$aForm['aFilesSizesLists'][$aData['iFile']].'"';
            $aUpdateSizeLists[$aData['iFile']][] = $aData['sFileName'];
            $aUpdateSizeLists[$aData['iFile']][] = $aForm['aFilesSizesLists'][$aData['iFile']];
          }

          if( isset( $aForm['aFilesSizesOther'][$aData['iFile']] ) && ( ( $aForm['aFilesSizesOther'][$aData['iFile']] != $aData['iSizeOther'] && $aData['iSizeOther'] > 0 ) || ( isset( $aVerifyFilesCrop[$aData['sFileName']] ) ) ) ){
            $aUpdate[$aData['iFile']][] = 'iSizeOther = "'.$aForm['aFilesSizesOther'][$aData['iFile']].'"';
            $aUpdateSizeOther[$aData['iFile']][] = $aData['sFileName'];
            $aUpdateSizeOther[$aData['iFile']][] = $aForm['aFilesSizesOther'][$aData['iFile']];
          }

          if( isset( $aForm['aFilesTypes'][$aData['iFile']] ) && $aForm['aFilesTypes'][$aData['iFile']] != $aData['iType'] ){
            $aUpdate[$aData['iFile']][] = 'iType = "'.$aForm['aFilesTypes'][$aData['iFile']].'"';
          }

          if( $aForm['aFilesPositions'][$aData['iFile']] != $aData['iPosition'] ){
            $aUpdate[$aData['iFile']][] = 'iPosition = "'.$aForm['aFilesPositions'][$aData['iFile']].'"';
          }

          $aForm['aFilesDescription'][$aData['iFile']] = changeTxt( trim( $aForm['aFilesDescription'][$aData['iFile']] ), 'ndnl' );
          if( $aForm['aFilesDescription'][$aData['iFile']] != $aData['sDescription'] ){
            $aUpdate[$aData['iFile']][] = 'sDescription = '.$oSql->quote( $aForm['aFilesDescription'][$aData['iFile']] );
          }
        }
      } // end while

      if( isset( $aUpdate ) ){
        foreach( $aUpdate as $iFile => $aFields ){
          $oSql->query( 'UPDATE files SET '.implode( ', ', $aFields ).' WHERE iFile = "'.$iFile.'"' );
        } // end foreach
        
        if( isset( $aUpdateSizeDetails ) ){
          foreach( $aUpdateSizeDetails as $iFile => $aValue ){
            $this->generateThumbs( $aValue[0], $aValue[1], ( isset( $aForm['aFilesCrops'][$iFile] ) ? $aForm['aFilesCrops'][$iFile] : null ) );
          } // end foreach
        }
        
        if( isset( $aUpdateSizeLists ) ){
          foreach( $aUpdateSizeLists as $iFile => $aValue ){
            $this->generateThumbs( $aValue[0], $aValue[1], ( isset( $aForm['aFilesCrops'][$iFile] ) ? $aForm['aFilesCrops'][$iFile] : null ) );
          } // end foreach
        }

        if( isset( $aUpdateSizeOther ) ){
          foreach( $aUpdateSizeOther as $iFile => $aValue ){
            $this->generateThumbs( $aValue[0], $aValue[1], ( isset( $aForm['aFilesCrops'][$iFile] ) ? $aForm['aFilesCrops'][$iFile] : null ) );
          } // end foreach
        }

        if( isset( $aVerifyFilesCrop ) ){
          $oIJ = ImageJobs::getInstance( );
          foreach( $aVerifyFilesCrop as $sFileName => $aValue ){
            $iFile = $oSql->getColumn( 'SELECT iFile FROM files WHERE sFileName = '.$oSql->quote( $sFileName ).' AND iCrop != '.$aValue[0].' LIMIT 1' );
            if( empty( $iFile ) && isset( $this->aDirs ) ){
              if( $aValue[1] > 0 && isset( $config['crop_options'][$aValue[1]][1] ) )
                $sFileName = $oIJ->getNameOfFile( $sFileName ).$config['crop_options'][$aValue[1]][1].'.'.$oIJ->getExtOfFile( $sFileName );
              foreach( $this->aDirs as $iDir ){
                if( $aValue[1] > 0 ){
                  if( is_file( 'files/'.$iDir.'/'.$sFileName ) )
                    unlink( 'files/'.$iDir.'/'.$sFileName );
                }
                else{
                  if( is_file( 'files/'.$iDir.'/'.$sFileName ) )
                    unlink( 'files/'.$iDir.'/'.$sFileName );                
                }
              } // end foreach
            }
          } // end foreach
        }
      }
    }
  } // end function saveFiles

  /**
  * Generates thumbnails
  * @return void
  * @param string $sFileName
  * @param int $iSize
  */
  private function generateThumbs( $sFileName, $iSize, $iCrop = null ){
    global $config;

    $sThumbsDir = 'files/'.$iSize.'/';
    $oIJ = ImageJobs::getInstance( );
    $sFileNameThumb = ( isset( $iCrop ) && $iCrop > 0 && isset( $config['crop_options'][$iCrop][1] ) ) ? $oIJ->getNameOfFile( $sFileName ).$config['crop_options'][$iCrop][1].'.'.$oIJ->getExtOfFile( $sFileName ) : $sFileName;
    if( !is_file( $sThumbsDir.$sFileNameThumb ) ){
      $aImgSize = $oIJ->throwImgSize( 'files/'.$sFileName );
      if( isset( $config['max_dimension_of_image'] ) && is_numeric( $config['max_dimension_of_image'] ) && ( $aImgSize['width'] > $config['max_dimension_of_image'] || $aImgSize['height'] > $config['max_dimension_of_image'] ) && ( $aImgSize['width'] < MAX_IMAGE_SIZE && $aImgSize['height'] < MAX_IMAGE_SIZE ) ){
        $oIJ->setThumbSize( $config['max_dimension_of_image'] );
        $oIJ->createThumb( 'files/'.$sFileName, 'files/', $sFileName );
      }

      if( !is_dir( $sThumbsDir ) ){
        mkdir( $sThumbsDir );
        chmod( $sThumbsDir, FILES_CHMOD );
      }

      if( !is_file( $sThumbsDir.$sFileNameThumb ) )
        $oIJ->createCustomThumb( 'files/'.$sFileName, $sThumbsDir, $iSize, $sFileNameThumb, true, $iCrop );
    }
  } // end function generateThumbs

  /**
  * Set page default image
  * @return void
  * @param int $iPage
  * @param int $iFile
  */
  public function setDefaultImage( $iPage, $iFile = null ){
    $oSql = Sql::getInstance( );
    $oSql->query( 'UPDATE files SET iDefault = 0 WHERE iPage = "'.$iPage.'" AND iSizeDetails > 0'.( isset( $iFile ) ? ' AND iFile != "'.$iFile.'"' : null ) );
    if( isset( $iFile ) ){
      $oSql->query( 'UPDATE files SET iDefault = 1 WHERE iFile = "'.$iFile.'"' );
    }
    else{
      $oSql->query( 'UPDATE files SET iDefault = 1 WHERE iFile = "'.$oSql->getColumn( 'SELECT iFile FROM files WHERE iPage = "'.$iPage.'" AND iSizeDetails > 0 ORDER BY iPosition ASC, sFileName ASC LIMIT 1' ).'"' );
    }
  } // end function setDefaultImage

  /**
  * Deletes all files attached to pages that are being deleted
  * @return void
  * @param array  $aPages
  */
  public function deleteFiles( $aPages ){
    global $config;
    if( isset( $aPages ) && is_array( $aPages ) ){
      $oSql = Sql::getInstance( );
      $sWhere = implode( ' OR iPage = ', $aPages );
      if( isset( $config['delete_unused_files'] ) ){
        $oQuery = $oSql->getQuery( 'SELECT sFileName, iSizeDetails FROM files WHERE iPage = '.$sWhere );
        while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
          $aDelete[$aData['sFileName']] = $aData['iSizeDetails'];
        } // end while
        if( isset( $aDelete ) ){
          $oSql->query( 'DELETE FROM files WHERE iPage = '.$sWhere );
          foreach( $aDelete as $sFileName => $iSize ){
            $this->deleteFilesFromDirs( $sFileName, $iSize );
          } // end foreach
        }
      }
      else{
        $oSql->query( 'DELETE FROM files WHERE iPage = '.$sWhere );
      }
    }
  } // end function deleteFiles

  /**
  * Deletes all files selected for deletion
  * @return void
  * @param array  $aFiles
  */
  public function deleteSelectedFiles( $aFiles ){
    global $config;
    if( isset( $aFiles ) && is_array( $aFiles ) ){
      $oSql = Sql::getInstance( );
      $sWhere = implode( ' OR iFile = ', $aFiles );
      if( isset( $config['delete_unused_files'] ) ){
        $oQuery = $oSql->getQuery( 'SELECT sFileName, iSizeDetails FROM files WHERE iFile = '.$sWhere );
        while( $aData = $oQuery->fetch( PDO::FETCH_ASSOC ) ){
          $aDelete[$aData['sFileName']] = $aData['iSizeDetails'];
        } // end while
        if( isset( $aDelete ) ){
          $oSql->query( 'DELETE FROM files WHERE iFile = '.$sWhere );
          foreach( $aDelete as $sFileName => $iSize ){
            $this->deleteFilesFromDirs( $sFileName, $iSize );
          } // end foreach
        }
      }
      else{
        $oSql->query( 'DELETE FROM files WHERE iFile = '.$sWhere );
      }
    }
  } // end function deleteSelectedFiles

  /**
  * Deletes files and images from the "files/" directory
  * @return void
  * @param string $sFileName
  * @param int $iSize
  */
  public function deleteFilesFromDirs( $sFileName, $iSize = null ){
    global $config;
    $oSql = Sql::getInstance( );
    $iData = $oSql->getColumn( 'SELECT iFile FROM files WHERE sFileName = "'.$sFileName.'"' );
    if( empty( $iData ) ){
      $iData = $oSql->getColumn( 'SELECT iSlider FROM sliders WHERE sFileName = "'.$sFileName.'"' );
    }
    if( empty( $iData ) ){
      if( isset( $iSize ) && $iSize > 0 && isset( $this->aDirs ) ){
        if( isset( $config['crop_options'] ) ){
          $oIJ = ImageJobs::getInstance( );
          $aFileNameExt = $oIJ->throwNameExtOfFile( $sFileName );
          foreach( $config['crop_options'] as $iCrop => $aValue ){
            if( !empty( $aValue[1] ) )
              $aFilesCropNames[] = $aFileNameExt[0].$aValue[1].'.'.$aFileNameExt[1];
          } // end foreach
        }
        foreach( $this->aDirs as $iDir ){
          if( is_file( 'files/'.$iDir.'/'.$sFileName ) )
            unlink( 'files/'.$iDir.'/'.$sFileName );
          if( isset( $aFilesCropNames ) ){
            foreach( $aFilesCropNames as $sName ){
              if( is_file( 'files/'.$iDir.'/'.$sName ) )
                unlink( 'files/'.$iDir.'/'.$sName );
            } // end foreach
          }
        } // ennd foreach
      }
      if( is_file( 'files/'.$sFileName ) )
        unlink( 'files/'.$sFileName );
    }
  } // end function deleteFilesFromDirs

  /**
  * Display image thumbnail
  * @return string
  * @param string $sFileName
  * @param int $iSize
  */
  public function getImageThumb( $sFileName, $iSize = null ){
    global $config;

    if( !isset( $iSize ) || $iSize > 399 ){
      $oSql = Sql::getInstance( );
      $iSize = $oSql->getColumn( 'SELECT iSizeDetails FROM files WHERE sFileName = '.$oSql->quote( $sFileName ).' AND iCrop = 0 ORDER BY iSizeDetails ASC LIMIT 1' );
    }

    if( isset( $iSize ) && !empty( $iSize ) && is_file( 'files/'.$iSize.'/'.$sFileName ) ){
      return '<img src="files/'.$iSize.'/'.$sFileName.'" />';
    }
    else{
      $iSize = null; // delete this line to enable displaying crop images in image preview if normal thumbnail is unavailable
      if( isset( $iSize ) && !empty( $iSize ) ){
        if( !isset( $oSql ) )
          $oSql = Sql::getInstance( );
        $iCrop = $oSql->getColumn( 'SELECT iCrop FROM files WHERE sFileName = '.$oSql->quote( $sFileName ).' AND iCrop > 0 ORDER BY iSizeDetails ASC LIMIT 1' );
        if( !empty( $iCrop ) && isset( $config['crop_options'][$iCrop][1] ) ){
          $oIJ = ImageJobs::getInstance( );
          $sFileNameThumb = $oIJ->getNameOfFile( $sFileName ).$config['crop_options'][$iCrop][1].'.'.$oIJ->getExtOfFile( $sFileName );
          if( is_file( 'files/'.$iSize.'/'.$sFileNameThumb ) ){
            $sFileName = $iSize.'/'.$sFileNameThumb;
          }
        }
      }
      return '<img src="files/'.$sFileName.'" width="'.$config['default_image_size'].'" />';
    }
  } // end function getImageThumb

  /**
  * Function list all images types to JS code
  * @return string
  */
  public function listImagesTypeEditor( ){
    global $config;
    $content = null;

    if( isset( $config['images_locations'] ) && is_array( $config['images_locations'] ) && count( $config['images_locations'] ) > 0 ){
      foreach( $config['images_locations'] as $iKey => $sValue )
        $content .= '{text: "'.$sValue.'", value: "[IMAGES='.$iKey.']"}, ';  
    }
    
    if( isset( $content ) )
      return 'aQuick.imageLocations = [ '.$content.' ];';
    else
      return null;
  } // end function listImagesTypeEditor

};
?>