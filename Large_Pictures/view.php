<script type="text/javascript" src="../js/jquery.js"></script>
<?php
if ($handle = opendir('.')) {
    while (false !== ($entry = readdir($handle))) {
        if (is_dir($entry) && ($entry != "." && $entry != "..")) {
            echo '<div class="dir">', $entry, '</div>';
        }
    }

closedir($handle);
}

?>
<div class="container"></div>
<script type="text/javascript" src="../js/custom.js"></script>
<link rel="stylesheet" type="text/css" href="../css/style.css" />