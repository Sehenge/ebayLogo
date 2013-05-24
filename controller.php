<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Alex
 * Date: 5/9/13
 * Time: 1:38 PM
 * To change this template use File | Settings | File Templates.
 */

define('IMPORT_IMAGE_ROW_POSITION', 18);
define('LOGO_FILENAME', 'logo.png');
define('IMPORT_IMAGES_ROOT', 'Large_Pictures/');

class ImportElement {

    var $raw;
    var $data;

    var $images_raw;
    var $images;

    /**
     * Image
     **/
    function getImageURLs(&$data){
        $tmp = explode(',', $data);
        $ret = array();

        foreach($tmp as $imgelem) {
            $pos = strpos($imgelem, "=");
            $ret[] = substr($imgelem, $pos+1);
        }
        return $ret;
    }

    function loadImages($prefix, &$logo, $process_prefix=false, $break_on_first=false) {
        $this->images = array();
        $i = 0;
        $ret = true;

        foreach($this->images_raw as $image) {

            $content = @file_get_contents($image);
            if($content && stristr($content, "404 Not Found") == false) {
                $a = parse_url($image);
                if (strpos($a['path'] , '..') !== false) $a['path'] = str_replace('..', '', $a['path']);
                $path = rtrim(DIR_FS_DOCUMENT_ROOT, '/').$a['path'];
                $path = preg_replace("/Large_Pictures/", "Pictures", $path);
                $dir = dirname($path);
                $origname = basename($path);
                if (! file_exists($dir)) mkdir($dir, 0755, true);
                $src = $a['scheme'] . '://' . $a['host'] . $a['path'];
                $this->resizeImage($src, $path, 500, 360, $rgb=0xFFFFFF, $quality=100);
                //file_put_contents($path, $content);
                chmod($path, 0666);

                $img_name = $process_prefix.$prefix.'_'.$i.'.jpg';
                $this->resizeImage($src, IMPORT_IMAGES_ROOT.$img_name, 500, 260, $rgb=0xFFFFFF, $quality=100);
                //file_put_contents(IMPORT_IMAGES_ROOT.$img_name, $content);
                chmod(IMPORT_IMAGES_ROOT.$img_name, 0666);

                $imobj = new ImageElement();
                $imobj->name = $img_name;
                $imobj->name_orig = $a['path'];
                $imobj->index = $i;
                $imobj->stampLogo($logo);

                if($i==0) {
                    $imobj->makeThumb(120);
                } else {
                    $imobj->makeThumb(85);
                }

                $this->images[] = $imobj;
            } else {

                if($i==0 && $break_on_first)
                {
                    $this->image_error = $image;
                    return false;
                }
            }
            $i++;
        }
        return $ret;
    }
}
class ImageLogo {

    var $width;
    var $height;
    var $img;

    function getLogo() {
        $tmp = imagecreatefrompng(LOGO_FILENAME);
        if(!$tmp) echo "CANNOT LOAD LOGO!";

        $ret = new ImageLogo();
        $ret->img = $tmp;

        list($ret->width, $ret->height) = getimagesize(LOGO_FILENAME);

        return $ret;
    }
}

class ImageElement {
    var $name;
    var $name_orig;

    public static $width;
    public static $height;

    var $thumb_name;
    var $thumb_width;
    var $thumb_height;
    var $index;

    public static function stampLogo(&$logo) {
        //die(self::$name);
        $brands = scandir(IMPORT_IMAGES_ROOT);
        foreach ($brands as $brand) {
            if (is_dir(IMPORT_IMAGES_ROOT.$brand) && ($brand != "." && $brand != "..") && ($brand != 'ADIDAS')) {
                $models = scandir(IMPORT_IMAGES_ROOT.$brand);
                foreach ($models as $model) {
                    if ($model != "." && $model != "..") {
                        $rmodel = IMPORT_IMAGES_ROOT.$brand.'/'.$model;
                        echo $model, "\n";
                        //var_dump(getimagesize($rmodel));
                        list(self::$width, self::$height) = getimagesize($rmodel);

                        $image_tmp = imagecreatefromjpeg ($rmodel);
                        $dst_x = $dst_y = 0;

                            $dst_x = self::$width - 497;
                            $dst_y = self::$height - 99;

                        ImageElement::imagecopymerge_alpha($image_tmp, $logo->img, $dst_x, $dst_y, 0, 0, $logo->width, $logo->height, 50);

                        imagejpeg($image_tmp, $rmodel, 100);
                        chmod($rmodel, 0666);

                        imagedestroy($image_tmp);
                    }
                }
            }
        }
    }

    function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){
        $opacity=$pct;
        // getting the watermark width
        $w = imagesx($src_im);
        // getting the watermark height
        $h = imagesy($src_im);

        // creating a cut resource
        $cut = imagecreatetruecolor($src_w, $src_h);
        // copying that section of the background to the cut
        imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);
        // inverting the opacity
        $opacity = 100 - $opacity;

        // placing the watermark now
        imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
        imagecopymerge($dst_im, $cut, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $opacity);
    }
}