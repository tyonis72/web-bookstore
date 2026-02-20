<?php
date_default_timezone_set("Asia/Jakarta");

define("APP_NAME", "BookStore");
define("BASE_URL", "http://localhost/bookstore");

function redirect($path)
{
    header("Location: " . BASE_URL . $path);
    exit;
}
