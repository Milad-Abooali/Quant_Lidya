<?php
######################################################################
#  M | 12:48 PM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

    require_once "config.php";

    /**
     * Escape User Input Values POST & GET
     */
    GF::escapeReq();

    $user_id = $_POST["uid"] ?? $_SESSION["id"];
    $cat = $_POST["cat"];
    $date = date('Y-m-d H:i:s');
    
    /* Getting file name */
    $filename = $_FILES['file']['name'];
    $filename = strtolower($_FILES['file']['name']);
    
    /* Location */
    $location = "media/".$filename;
    $uploadOk = 1;
    $imageFileType = pathinfo($location,PATHINFO_EXTENSION);
    
    /* Valid Extensions */
    $valid_extensions = array("jpg","jpeg","png");
    /* Check file extension */
    if( !in_array(strtolower($imageFileType),$valid_extensions) ) {
        $uploadOk = 0;
    }
    
    if($uploadOk == 0){
        echo 0;
    }else{
       /* Upload file */
        if(move_uploaded_file($_FILES['file']['tmp_name'],$location)){
            $media = 'SELECT media,id FROM media WHERE user_id = '.$user_id.' AND type = "'.$cat.'"';
            $result2 = $DB_admin->query($media);
            if(mysqli_num_rows($result2) > 0){
                $mysql_update = 'UPDATE media SET media="'.$filename.'", updated_at="'.$date.'", updated_by="'.$user_id.'" WHERE user_id="'.$user_id.'" AND type = "'.$cat.'"';
                mysqli_query($DB_admin, $mysql_update) or die("database error:". mysqli_error($DB_admin));

                // Add actLog
                global $actLog; $actLog->add('Media', null,1,'{"CAT":"'.$cat.'","Action":"Update"}');
                echo 1;

            } else {
                $mysql_insert = 'INSERT INTO media (media, user_id, type, created_at, created_by, updated_at, updated_by) VALUES("'.$filename.'", "'.$user_id.'", "'.$cat.'", "'.$date.'", "'.$_SESSION["id"].'", "'.$date.'", "'.$_SESSION["id"].'")';
                mysqli_query($DB_admin, $mysql_insert) or die("database error:". mysqli_error($DB_admin));
                $inserted_id = mysqli_insert_id($DB_admin);

                // Add actLog
                global $actLog; $actLog->add('Media', null,1,'{"CAT":"'.$cat.'","Action":"Insert '.$inserted_id.'"}');
                echo 1;
            }
            //echo $location;
            if($cat == "avatar"){
                createThumb($location, $location, $desired_width = 96, $desired_height = 96);
            }
        } else {
            echo 0;
        }
    }
    function createThumb($src, $dest, $desired_width = false, $desired_height = false) {
    
        /* If no dimenstion for thumbnail given, return false */    
        if (!$desired_height && !$desired_width)
            return false;
        
        $fparts = pathinfo($src);
        $ext = strtolower($fparts['extension']);
        echo $ext;
        
        /* if its not an image return false */
        if (!in_array($ext, array(
                'gif',
                'jpg',
                'png',
                'jpeg'
            )))
            return false;
    
        /* read the source image */
        if ($ext == 'gif')
            $resource = imagecreatefromgif($src);
        else if ($ext == 'png')
            $resource = imagecreatefrompng($src);
        else if ($ext == 'jpg' || $ext == 'jpeg')
            $resource = imagecreatefromjpeg($src);
    
        $width = imagesx($resource);
        $height = imagesy($resource);
        
        /* find the “desired height” or “desired width” of this thumbnail, relative
         * to each other, if one of them is not given */
        if (!$desired_height)
            $desired_height = floor($height * ($desired_width / $width));
        
        if (!$desired_width)
            $desired_width = floor($width * ($desired_height / $height));
    
        /* create a new, “virtual” image */
        $virtual_image = imagecreatetruecolor($desired_width, $desired_height);
        
        switch ($ext)
        {
        case "png":
            // integer representation of the color black (rgb: 0,0,0)
            $background = imagecolorallocate($virtual_image, 0, 0, 0);
            
            // removing the black from the placeholder
            imagecolortransparent($virtual_image, $background);
    
            // turning off alpha blending (to ensure alpha channel information 
            // is preserved, rather than removed (blending with the rest of the 
            // image in the form of black))
            imagealphablending($virtual_image, false);
    
            // turning on alpha channel information saving (to ensure the full range 
            // of transparency is preserved)
            imagesavealpha($virtual_image, true);
    
            break;
        case "gif":
            // integer representation of the color black (rgb: 0,0,0)
            $background = imagecolorallocate($virtual_image, 0, 0, 0);
            
            // removing the black from the placeholder
            imagecolortransparent($virtual_image, $background);
    
            break;
        }
    
        /* copy source image at a resized size */
        imagecopyresampled($virtual_image, $resource, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
    
        /* create the physical thumbnail image to its destination */
        /* Use correct function based on the desired image type from $dest thumbnail
         * source */
        $fparts = pathinfo($dest);
        $ext = strtolower($fparts['extension']);
        /* if dest is not an image type, default to jpg */
        if (!in_array($ext, array(
                'gif',
                'jpg',
                'png',
                'jpeg'
            )))
            $ext = 'jpg';
        $dest = $fparts['dirname'] . '/' . $fparts['filename'] . '.' . $ext;
    
        if ($ext == 'gif')
            imagegif($virtual_image, $dest);
        else if ($ext == 'png')
            imagepng($virtual_image, $dest, 1);
        else if ($ext == 'jpg' || $ext == 'jpeg')
            imagejpeg($virtual_image, $dest, 100);
    
        return array(
            'width' => $width,
            'height' => $height,
            'new_width' => $desired_width,
            'new_height' => $desired_height,
            'dest' => $dest
        );
    }
?>