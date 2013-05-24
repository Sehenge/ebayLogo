<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Alex
 * Date: 5/9/13
 * Time: 2:23 PM
 * To change this template use File | Settings | File Templates.
 */

$data = $_POST['data'];
$dir = scandir($data);
$result = array();

foreach ($dir as $image) {
    if (preg_match("/\.jpg/", $image)) {
        $result[] = $image;
    }
}

echo json_encode($result);