<?php 
    require_once (__DIR__  . "/Shared/General.php");
    require_once (__DIR__  . "/Request.php");
    require_once (__DIR__  . "/bin/Convert.php");

class HandleDataUpload extends General {
    private $file, $fileName, $fileTmp, $fileRamdomString, $userId, 
            $imageUse, $fullname, $describe, 
            $title, $tags,
            $dataImages, $namePowerpoint;
    private $data;
    private $access;
    private $arrayAllPhotos = [];

    public $response, $error;

    public $arrayConvertApi = [
        'secret_2Twrwqv8Vuzi4qGF',
        'secret_28LXYwoF7ydFRuHf',
        'secret_Ta2lieyXcF0ZbXNz',
        'secret_Ksc49M4bWoQyYuLM',
        'secret_lDD4vzd0bnPUB5rn',
    ];

    public function __construct()
    {
        $this->access = new General;
        $this->userId = $this->access->accessUserId();
    }

    /* Description: When the user uploads a photo, it will 
    query and retrieve only the 3 latest records from the table 'ug_save_old_avatars', */

    public function updateImages($inputName, $folder, $typeFile, $changeSize = null, $status = true) 
    {
        $this->file = $_FILES[ $inputName ];
        $this->fileName = $this->file['name'];
        $this->fileTmp =  $this->file['tmp_name'];

        $this->fileRamdomString = sha1( uniqid() );
        $fileExt = pathinfo( $this->fileName, PATHINFO_EXTENSION );
        $fileNameSeparator = pathinfo( $this->fileName, PATHINFO_FILENAME );
    
        // Result name File 
        $pathFullImage = 'usego_' . $fileNameSeparator . $this->fileRamdomString  .'.'. $fileExt;
        
        if ( $typeFile == 'file' ) 
        { 
            // Type equals file then assign namePpt equals -> nameImageUrl
            $this->namePowerpoint = $pathFullImage;
        } 

        $pathNavigation = _WEB_PATH_UPLOADS . $folder . '/' . $pathFullImage;

        // If it is an image, reduce the size and do not save directly
        if( $changeSize !== null )
        {
            $savedImageOnFolder = $this->resizeAndSaveImage($this->fileTmp, $pathNavigation, $changeSize, $changeSize);
            if( !$savedImageOnFolder )
            {
                $savedImageOnFolder = move_uploaded_file($this->fileTmp, $pathNavigation);
            }
        } else {
            $savedImageOnFolder = move_uploaded_file($this->fileTmp, $pathNavigation);
        }
        
        if( $status === true && $savedImageOnFolder ) 
        {
            $this->oldPhotoSavingActivity( $pathFullImage );
        }
        return $pathFullImage; 
    }    

    private function resizeAndSaveImage( $sourcePath, $destinationPath, $maxWidth, $maxHeight, $quality = 75) 
    {
        // Check if file file exists or not
        if (!file_exists( $sourcePath )) {
            return false;
        }
    
        // Get image size
        list( $sourceWidth, $sourceHeight, $sourceType) = getimagesize($sourcePath );
    
        // Check if there is a valid size
        if ( $sourceWidth <= 0 || $sourceHeight <= 0 ) {
            return false;
        }
    
        // Calculate new size based on scale and size constraints
        $aspectRatio = (int)$sourceWidth / (int)$sourceHeight;
        if ( (int)$maxWidth / (int)$maxHeight > $aspectRatio ) {
            $newWidth = $maxHeight * $aspectRatio;
            $newHeight = $maxHeight;
        } else {
            $newWidth = $maxWidth;
            $newHeight = $maxWidth / $aspectRatio;
        }
    
        // Create a new image with calculated dimensions
        $destinationImage = imagecreatetruecolor((int)$newWidth, (int)$newHeight);
        switch ( $sourceType ) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
            case IMAGETYPE_WEBP:
                $sourceImage = imagecreatefromwebp($sourcePath);
                break;
            // Add support for other image types if needed
            default:
                return false;
        }
    
        // Copy and shrink the image
        imagecopyresampled($destinationImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);
    
        // Save thumbnail image
        $success = imagejpeg($destinationImage, $destinationPath, $quality);
    
        // Release memory
        imagedestroy($sourceImage);
        imagedestroy($destinationImage);
    
        return $success;
    }    

    public function oldPhotoSavingActivity( $nameImage )
    {
        $this->data = [
            'avatar' => $nameImage,
            'createAt' => date('y-m-d H:i:s')
        ];

        // Execute update avatar  
        $this->fileUpdateAvatar( $this->data );

        $avatarAvailable_User = $this->fileCountAvatar();
        ( $avatarAvailable_User < 4 ) ? $this->fileSavedAvatar( $nameImage ) : $this->makeChangeAvatar( $nameImage );
        /* 
            If the number is less than 4, save immediately, 
            otherwise delete the oldest record and save
        */
    }

    public function useImageUpdate() 
    {
        // Get id 
        if ( $this->access->attributeEmpty( [ 'avatar' ] ) ) 
        {
            $this->imageUse = $_POST['avatar'];

            $this->data = [
                'avatar' => $this->imageUse,
            ];
            $this->fileUpdateAvatar( $this->data );
        } else 
        {
            $this->error = _on_error;
        }
    }

    /*  when users update fullname and description information */ 

    public function editInformation() 
    {
        // Get id 
        $this->data = [];      
        // Check exists fullname
        if (!empty( $_POST['fullname'] )) 
        {
            $this->fullname = $_POST['fullname'];
            $arrayFullname = explode(' ', $this->fullname);
            $this->data['firstname'] = $arrayFullname[0];
            $this->data['lastname'] = $arrayFullname[1];
        }
        // Check exists describe
        if (!empty( $_POST['describe'] )) 
        {
            $this->describe = $_POST['describe'];
            $this->data['describes'] = nl2br( $this->describe );
        }

        $this->updateInfoUser();
    }

    public function insertFilePowerpoint($auto, $nameFile = null, $images = null)
    {
        $imageString = ""; $fileString = "";
        if ($auto = 1) {
            $fileString = $nameFile;
            $imageString = $images;
        } else {
            $fileString = $this->namePowerpoint;
            $imageString = implode('||', $this->arrayAllPhotos);
        }
        $this->data = [
            'userId' => $this->userId,
            'title' => $this->title,
            'tags' => $this->tags,
            'images' => $imageString,
            'fileDownload' => $fileString,
            'createAt' => date('y-m-d H:i:s')
        ];

        $query = $this->access->MyModelsCrud->insert('ug_power_point', $this->data);
        if( $query )
        {
            $this->response = 'true';
        }
    }

    private function fileUpdateAvatar( $data ) 
    {
        $query = $this->access->MyModelsCrud->update('ug_users', $data, "id = $this->userId");
        $this->response = ( $query ) ? 'Ảnh đại diện đã được cập nhật.' : _on_error;
    }
    // Update avatar 

    private function makeChangeAvatar($image)
    {
        $this->removeOldAvatar();
        $this->fileSavedAvatar($image);
    }

    private function fileCountAvatar() 
    {
        return $this->access->MyModelsOther->getRows("
        SELECT 
            userId 
        FROM 
            ug_save_old_avatars 
        WHERE 
            userId = $this->userId");
    }
    // Check the number of avatars in the table

    private function fileSavedAvatar($image) 
    {
        $this->data = [
            'userId' => $this->userId,
            'avatar' => $image,
            'createAt' => date('y-m-d H:i:s')
        ];

        if ( $this->access->MyModelsCrud->insert('ug_save_old_avatars', $this->data) )
        $this->response = 'Cập nhật avatar thành công';
    }
    // Saved avatar on table 'ug_save_old_avatars'

    private function removeOldAvatar()
    {
        $idDeleteArray = $this->access->MyModelsOther->firstRaw("SELECT id, avatar FROM ug_save_old_avatars 
        WHERE userId = '$this->userId'
        ORDER BY createAt ASC LIMIT 1");

        // remove photos image > 3 on folder 
        if(!empty( $idDeleteArray )) 
        {
            $avartaOldImage = $idDeleteArray['avatar'];
            if($avartaOldImage && file_exists( _WEB_PATH_UPLOADS . 'avatar/' . $avartaOldImage )) 
            {
                unlink( _WEB_PATH_UPLOADS . 'avatar/' . $avartaOldImage );
            }

            // Remove on database 
            $idToDelete = $idDeleteArray['id'];
            // Id user to delete 
            $this->access->MyModelsCrud->remove('ug_save_old_avatars', "id = $idToDelete");   
        }
    }
    // Delete oldest photo

    private function updateInfoUser() 
    {
        $query = $this->access->MyModelsCrud->update('ug_users', $this->data, "id = $this->userId");
        $this->response = ( $query ) ? 'Thông tin đã được cập nhật.' : _on_error;
    }

    public function uploadPowerpoint()
    {
        if ($this->access->attributeEmpty(['title', 'tags'])) 
        {
            $this->title = $_POST['title'];
            $this->tags = $_POST['tags'];
        }
    }

    public function savedAllImagePowerpoint() 
    {
        if (!empty($_FILES['image-uploads']))
        {
            $this->dataImages = $_FILES['image-uploads'];
            
            if(is_array($this->dataImages) || is_object($this->dataImages))
            {
                foreach ($this->dataImages['name'] as $key => $imageName)
                {
                $this->fileName = $imageName;
                $this->fileTmp =  $this->dataImages['tmp_name'][$key];
                
                $this->fileRamdomString = sha1(uniqid());
                $fileExt = pathinfo($this->fileName, PATHINFO_EXTENSION);
                $fileNameSeparator = pathinfo($this->fileName, PATHINFO_FILENAME);

                // Result name File 
                $nameImageUrl = 'usego_' . $fileNameSeparator . $this->fileRamdomString  .'.'. $fileExt;

                $pathNavigation = _WEB_PATH_UPLOADS . 'powerpoint-images/' . $nameImageUrl;
                
                // Upload avatar 
                $savedImageOnFolder 
                = $this->resizeAndSaveImage($this->fileTmp, $pathNavigation, 1100, 1100);

                if(!$savedImageOnFolder) 
                {
                    $this->response = _on_error;
                } else {
                    $this->response = null;
                }

                array_push($this->arrayAllPhotos, $nameImageUrl);
                }
            }
        }
    }

    public function uploadImageProof() 
    {
        if ($this->access->attributeEmpty(['id'])) {
            try {
                $id = $_POST['id'];
                $uploadDir = _WEB_PATH_UPLOADS . 'proof-files/';
                $outputDir = _WEB_PATH_UPLOADS . 'proof-images/';
                $fontFile = _WEB_PATH_ROOT . '\views\cpanel\templates\fonts\static\Nunito-Medium.ttf';
    
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                if (!is_dir($outputDir)) {
                    mkdir($outputDir, 0777, true);
                }

                $images = null;
                $success = false;
    
                foreach ($this->arrayConvertApi as $apiSecret) {
                    try {
                        $fileHandler = new Convert($apiSecret, $uploadDir, $outputDir, $fontFile);
                        $arrayResults = $fileHandler->handleFileUpload();
                        $images = $arrayResults['images'];
                        $files = $arrayResults['files'];
                        $success = true;
                        break;
                    } catch (Exception $e) {
                        error_log("$apiSecret failed error: " . $e->getMessage());
                    }
                }
                if (!$success) {
                    throw new Exception("ConvertAPI failed.");
                }
    
                $data = [
                    'images' => $images,
                    'files' => $files
                ];
                $query = $this->access->MyModelsCrud->update(
                    "ug_service",
                    $data,
                    "id_trade = '$id' AND userId = '$this->userId'"
                );
    
                if ($query) {
                    $this->response = $images;
                }
    
            } catch (Exception $e) {
                error_log("Upload failed: " . $e->getMessage());
                echo "Error: " . $e->getMessage();
            }
        }
    }

    public function automaticUpload()
    {
        try {
            $uploadDir = _WEB_PATH_UPLOADS . 'powerpoint/';
            $outputDir = _WEB_PATH_UPLOADS . 'powerpoint-images/';
            $fontFile = _WEB_PATH_ROOT . '\views\cpanel\templates\fonts\static\Nunito-Medium.ttf';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0777, true);
            }

            $images = null;
            $success = false;

            foreach ($this->arrayConvertApi as $apiSecret) {
                try {
                    $fileHandler = new Convert($apiSecret, $uploadDir, $outputDir, $fontFile);
                    $arrayResults = $fileHandler->handleFileUpload();
                    $images = $arrayResults['images'];
                    $files = $arrayResults['files'];
                    $success = true;
                    break;
                } catch (Exception $e) {
                    error_log("$apiSecret failed error: " . $e->getMessage());
                }
            }
            if (!$success) {
                throw new Exception("ConvertAPI failed.");
            }

            $data = [
                'images' => $images,
                'files' => $files
            ];
            $this->response = $data;

        } catch (Exception $e) {
            error_log("Upload failed: " . $e->getMessage());
            echo "Error: " . $e->getMessage();
        }
    }
}

class definesUploadAction extends General {
    private $method;
    private $handle; 
    private $access;
    public function __construct()
    {
        $this->access = new General;
        $this->handle = new HandleDataUpload();
        $this->performDetermination();
    }

    public function identifyActionUpload()
    {
        if ($this->access->attributeEmpty(['identify'])) 
        {
            $identify = $_POST['identify'];
            $this->handle->uploadPowerpoint();

            if ( $identify === 'automatic' )
            {
                $fileDownload = $_POST['powerpoint'];
                $images = $_POST['images-uploads'];
                $this->handle->insertFilePowerpoint(1, $fileDownload, $images);
            } else {
                $this->handle->updateImages('powerpoint', 'powerpoint', 'file', null, false);
                $this->handle->savedAllImagePowerpoint();
                $this->handle->insertFilePowerpoint(0);
            }
        }
    }

    public function performDetermination() 
    {
        if ( $this->access->attributeEmpty( ['class'] ) )
        {
            $this->method = $_POST['class'];
        
            switch ( $this->method )
            {
                // When entering the authentication code
                case 'UpdateAvatar':
                    $this->handle->updateImages('avatar', 'avatar', 'image', 300, true); 
                    break;

                case 'UseAvatarOld':
                    $this->handle->useImageUpdate();
                    break;

                case 'EditInformation':
                    $this->handle->editInformation();
                    break;

                case 'UploadPowerpoint':
                    $this->identifyActionUpload();
                    break;

                case 'UploadImageProof':
                    $this->handle->uploadImageProof();
                    break;

                case 'AutomaticUpload':
                    $this->handle->automaticUpload();
                    break;
            }

            $this->handle->sendJsonResponse( $this->handle->response, $this->handle->error );
        }
    }
}

$handle = new definesUploadAction();