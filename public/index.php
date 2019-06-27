<?php
include "../config/config.php";

if(isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 'index';
}

// подготовка 
$params = prepareVariables($page);

echo render($page, $params);
