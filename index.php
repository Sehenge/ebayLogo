<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Alex
 * Date: 5/9/13
 * Time: 1:00 PM
 * To change this template use File | Settings | File Templates.
 */

require_once 'controller.php';
$logo = ImageLogo::getLogo();
//var_dump($logo);
ImageElement::stampLogo($logo);
echo 'Hello World!';