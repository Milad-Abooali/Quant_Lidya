<?php

/**
 * Upload
 * App - Main wrapper
 * By Milad [m.abooali@hotmail.com]
 */

require_once('../config.php');
require_once('config-over.php');
global $db;

$date       = date('Y-m-d H:i:s');
$type       = $_REQUEST['type'];
$filename   = strtolower($_FILES['file']['name']);

$result = new stdClass();

/* Location */
$location = "../media/".$filename;
$result->e = false;
$imageFileType = pathinfo($location,PATHINFO_EXTENSION);

/* Valid Extensions */
$valid_extensions = array("jpg","jpeg","png");
if( !in_array(strtolower($imageFileType), $valid_extensions) ) $result->e = "Not valid extensions!";

/* Check File Size - 2 MB */
if($_FILES['file']['size'] > 2097152) $result->e = "File size must be less than 2MB!";

/* Check user */
if(!$_SESSION['id']) $result->e = "You are not logged in!";

if(!$result->e){

    if(move_uploaded_file($_FILES['file']['tmp_name'], $location)){
        $where = "type='$type' AND user_id=".$_SESSION['id'];
        $is_exist = $db->exist('media', $where);
        $result->exist = $is_exist;
        if($is_exist){
            $update['media'] = $filename;
            $update['updated_at'] = $date;
            $update['updated_by'] = $_SESSION["id"];
            $result->res = $db->updateAny('media',$update, $where);
            // Add actLog
            global $actLog; $actLog->add('Media', null,$result->res, json_encode($update));
        } else {
            $insert['media'] = $filename;
            $insert['type'] = $type;
            $insert['user_id'] = $_SESSION["id"];
            $insert['created_at'] = $date;
            $insert['created_by'] = $_SESSION["id"];
            $insert['updated_at'] = $date;
            $insert['updated_by'] = $_SESSION["id"];
            $result->res = $db->insert('media', $insert);
            // Add actLog
            global $actLog; $actLog->add('Media', null,$result->res, json_encode($insert));
        }
        if($type == "avatar"){
            createThumb($location, $location, 96, 96);
            $result->avatar = "media/".$filename;
        }
    } else {
        $result->e = 'File upload Error!';
    }

}
header('Content-Type: application/json');
echo json_encode($result);

/**
 * Create Thumb
 * @TODOD Refactor
 */
function createThumb($src, $dest, $desired_width = false, $desired_height = false) {

    /* If no dimenstion for thumbnail given, return false */
    if (!$desired_height && !$desired_width)
        return false;

    $fparts = pathinfo($src);
    $ext = strtolower($fparts['extension']);
    //echo $ext;

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
