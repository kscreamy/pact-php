<?php
/*
    Mock Provider as an example

*/

if (isset($_GET["amount"])) {
    $objects = generate(intval($_GET["amount"]));
} elseif (isset($_GET["file"])) {
    $fileName = filter_var($_GET["file"], FILTER_SANITIZE_STRING);
    $currentDir = dirname(__FILE__);
    $relativeDir = $currentDir . DIRECTORY_SEPARATOR . $fileName;
    $objects = \json_decode(file_get_contents($relativeDir));
} elseif (!empty($_POST)) {
    $body = '{ "type": "some new type" }';
    $body = \json_encode(\json_decode($body));
    $objects = \json_decode($body);
} else if (!isset($_GET['xml'])) {
    $objects = generate();
}

if (isset($_GET['xml'])) {
    header('Content-Type: application/xml');
    $body =  '<?xml version="1.0" encoding="UTF-8"?><alligator name="Mary" feet="4"><favoriteColor>blue</favoriteColor></alligator>';
    echo $body;
}
else {
    header('Content-Type: application/json');
    echo json_encode($objects);
}



function generate($objCount = 3)
{
    $objects = array();
    
    for ($i=0;$i<$objCount;$i++) {
        $obj = new \stdClass();
        $obj->id = 100 + $i;
        $obj->name = sprintf("Type %d", $obj->id);
        
        $objects[] = $obj;
    }
    
    $ret = new \stdClass();
    $ret->types = $objects;

    if ($objCount == 4) {
        $ret->extra = "ignore me";
    }

    return $ret;
}
