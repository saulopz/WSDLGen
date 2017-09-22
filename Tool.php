<?php
$SERVER_NAME = $_SERVER['SERVER_NAME'];
$SERVER_PORT = $_SERVER['SERVER_PORT'];
$SCRIPT_FILENAME = $_SERVER['SCRIPT_NAME'];
$URI = "";

switch ($SERVER_PORT) {
    case 80 :
        $URI = "http://" . $SERVER_NAME . $SCRIPT_FILENAME;
        break;
    case 443 :
        $URI = "https://" . $SERVER_NAME . $SCRIPT_FILENAME;
        break;
    default :
        $URI = "http://" . $SERVER_NAME . ":" . $SERVER_PORT . $SCRIPT_FILENAME;
}

$FILE_NAME = getFileNameFromURI($SCRIPT_FILENAME);
$SERVICE_NAME = getServiceName($FILE_NAME);
$LOCAL = getLocal($SCRIPT_FILENAME);

function getFileNameFromURI($uri)
{
    $i = strlen($uri) - 1;
    $out = "";
    while (($uri[$i] != '/') && ($i > 0)) {
        $out = $uri[$i] . $out;
        $i--;
    }
    return $out;
}

function getServiceName($fileName)
{
    $out = "";
    $dotFound = false;
    for ($i = strlen($fileName) - 1; $i >= 0; $i--) {
        if ($dotFound) {
            $out = $fileName[$i] . $out;
        } else {
            $dotFound = $fileName[$i] == ".";
        }
    }
    return $out;
}

function getLocal($fileName)
{
    $out = "";
    $dotFound = false;
    for ($i = strlen($fileName) - 1; $i >= 0; $i--) {
        if ($dotFound) {
            $out = $fileName[$i] . $out;
        } else {
            $dotFound = $fileName[$i] == "/";
        }
    }
    return $out;
}