<?php
/*
 * File: image.php
 * Author: Simon Jarvis (SimpleImage)
 * Modified by: Safet Hočkić <q7eb2a@gmail.com>
 * Based in: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details:
 * http://www.gnu.org/licenses/gpl.html
 */

class Image
{

    public $image;
    public $image_type;

    public function __construct($filename = null)
    {
        if (!empty($filename)) {
            $this->load($filename);
        }
        $this->top = 30;
        $this->image_width = 600;
        $this->border_width = "650";
        $this->text_width = 150;
    }

    public function load($filename)
    {
        $image_info = getimagesize($filename);
        $this->image_type = $image_info[2];
        if ($this->image_type == IMAGETYPE_JPEG) {
            $this->image = imagecreatefromjpeg($filename);
        } elseif ($this->image_type == IMAGETYPE_GIF) {
            $this->image = imagecreatefromgif($filename);
        } elseif ($this->image_type == IMAGETYPE_PNG) {
            $this->image = imagecreatefrompng($filename);
        } else {
            throw new Exception("Samo jpg,gif i png!");
        }

    }

    public function save($filename, $compression = 100, $permissions = null)
    {
        $image_type = $this->image_type;

        if ($image_type == IMAGETYPE_JPEG) {
            imagejpeg($this->image, $filename, $compression);
        } elseif ($image_type == IMAGETYPE_GIF) {
            imagegif($this->image, $filename);
        } elseif ($image_type == IMAGETYPE_PNG) {
            imagealphablending($this->image, false);
            imagesavealpha($this->image, true);
            $transparent = imagecolorallocatealpha($this->image, 255, 255, 255, 127);
            imagefilledrectangle($this->image, 0, 0, $this->getWidth(), $this->getHeight(), $transparent);
            imagepng($this->image, $filename);
        }

        if ($permissions !== null) {
            chmod($filename, $permissions);
        }
    }

    public function output($image_type = IMAGETYPE_JPEG, $quality = 100)
    {
        if ($image_type == IMAGETYPE_JPEG) {
            header("Content-type: image/jpeg");
            imagejpeg($this->image, null, $quality);
        } elseif ($image_type == IMAGETYPE_GIF) {
            header("Content-type: image/gif");
            imagegif($this->image);
        } elseif ($image_type == IMAGETYPE_PNG) {
            header("Content-type: image/png");
            imagepng($this->image);
        }
    }

    public function getWidth()
    {
        return imagesx($this->image);
    }

    public function getHeight()
    {
        return imagesy($this->image);
    }

    public function resizeToHeight($height)
    {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width, $height);
    }

    public function resizeToWidth($width)
    {
        $ratio = $width / $this->getWidth();
        $height = $this->getHeight() * $ratio;
        $this->resize($width, $height);
    }

    public function square($size)
    {
        $new_image = imagecreatetruecolor($size, $size);

        if ($this->getWidth() > $this->getHeight()) {
            $this->resizeToHeight($size);

            imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
            imagecopy($new_image, $this->image, 0, 0, ($this->getWidth() - $size) / 2, 0, $size, $size);
        } else {
            $this->resizeToWidth($size);

            imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
            imagecopy($new_image, $this->image, 0, 0, 0, ($this->getHeight() - $size) / 2, $size, $size);
        }

        $this->image = $new_image;
    }

    public function scale($scale)
    {
        $width = $this->getWidth() * $scale / 100;
        $height = $this->getHeight() * $scale / 100;
        $this->resize($width, $height);
    }

    public function resize($width, $height, $force = FALSE)
    {
        // Only 250 x 250
        if ($this->getWidth() < 250) {
            $width = 250;
            $height = 250;
            $force = TRUE;
        }
        // if file is smaller, do not resize (optional)
        if ($force == FALSE) {
            if ($width > $this->getWidth() AND $height > $this->getHeight()) {
                $width = $this->getWidth();
                $height = $this->getHeight();
            }
        }

        $new_image = imagecreatetruecolor($width, $height);

        // check if this image is PNG or GIF, then set if transparent
        if (($this->image_type == IMAGETYPE_GIF) OR ($this->image_type == IMAGETYPE_PNG)) {
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
            $transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
            imagefilledrectangle($new_image, 0, 0, $width, $height, $transparent);
        }

        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
    }

    public function cut($x, $y, $width, $height)
    {
        $new_image = imagecreatetruecolor($width, $height);
        imagecopy($new_image, $this->image, 0, 0, $x, $y, $width, $height);
        $this->image = $new_image;
    }

    public function maxarea($width, $height = null)
    {
        $height = $height ? $height : $width;

        if ($this->getWidth() > $width) {
            $this->resizeToWidth($width);
        }
        if ($this->getHeight() > $height) {
            $this->resizeToheight($height);
        }
    }

    public function cutFromCenter($width, $height)
    {
        if ($width < $this->getWidth() && $width > $height) {
            $this->resizeToWidth($width);
        }
        if ($height < $this->getHeight() && $width < $height) {
            $this->resizeToHeight($height);
        }

        $x = ($this->getWidth() / 2) - ($width / 2);
        $y = ($this->getHeight() / 2) - ($height / 2);

        $this->cut($x, $y, $width, $height);
    }

    public function createPoster($width, $tmp_folder, $title, $description)
    {
        $height = $this->getHeight() + $this->text_width;

        $new_image = imagecreatetruecolor($width, $height);
        // Resize original image
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $this->image_width, $height, $this->getWidth(), $this->getHeight());

        $this->save($tmp_folder);

        $im = imagecreatetruecolor($this->image_width, $this->getHeight());

        // Allocate colors 255,250,250
        $text_color = imagecolorallocate($im, 255, 250, 250);
        $bordercolors = imagecolorallocate($im, 255, 250, 250);
        $backgroundcolor = imagecolorallocate($im, 0, 0, 0);
        $watermark_color = imagecolorallocate($im, 189, 189, 189);

        // Fill Im with the background color.
        imagefill($im, 0, 0, $backgroundcolor);

        // Draw a border on the background image
        $size = getimagesize($tmp_folder);
        $x = ($width / 2) - ($size[0] / 2) - 2;
        $y = $this->top + 3;
        $w = $this->image_width + 3;
        $h = $height + 3;

        // Merge the original image resource with the background image resource
        imagecopy($im, imagecreatefromjpeg($tmp_folder), $x + 3, $this->top + 6, 2, 2, $this->image_width, $this->getHeight());

        // Linije

        /* Ljeva linija*/
        imageline($im, $x, $y, $x, $y + $h, $bordercolors);
        /* Top linija */
        imageline($im, $x, $y, $x + $w, $y, $bordercolors);
        /* Desno linija */
        imageline($im, $x + $w, $y, $x + $w, $y + $h, $bordercolors);
        /* Donja linija */
        imageline($im, $x, $y + $h, $x + $w, $y + $h, $bordercolors);

        // Write out the text
        $this->CenterImageString($im, $this->image_width, ucfirst($title), 40, ($this->top + $this->getHeight() + 60), $text_color);
        $this->CenterImageString($im, $this->image_width, ucfirst($description), 16, ($this->top + $this->getHeight() + 94), $text_color);
        // idemo sad autor datum itd...
        imagettftext($im, 12, 0, 23, 30, $text_color, 'arial.ttf', "Autor: Nermin");

        imagettftext($im, 12, 0, $this->image_width - 157, 30, $text_color, 'arial.ttf', "Datum: 25.08.2012.");

        // Watermark. Be a sport and dont remove this.
        ImageString($im, 2, $this->image_width - 120, $this->getHeight() - 15, "Lajkamo.net posteri", $watermark_color);

        imagepng($im, $tmp_folder);
        imagedestroy($im);
    }

    function CenterImageString($image, $image_width, $string, $font_size, $y, $color)
    {
        $text_width = imagefontwidth($font_size) * strlen($string);
        $center = ceil($image_width / 2);
        $x = $center - (ceil($text_width / 2));

        ### Get exact dimensions of text string
        $box = @imageTTFBbox($font_size, 0, 'calibri.ttf', $string);
        ### Get width of text from dimensions
        $textwidth = abs($box[4] - $box[0]);
        ### Get x-coordinate of centered text horizontally using length of the image and length of the text
        $xcord = ($image_width / 2) - ($textwidth / 2) - 2;

        imagettftext($image, $font_size, 0, $xcord, $y, $color, 'calibri.ttf', $string);
    }

}

$image = new Image('./uploads/media/user_picture/0001/01/012b240a9ad965479310cd680a972f0762a2a834.jpeg');
$image->createPoster(650, "./slikaneka.png", "testiram nesto ovo", "deskripcija lol");