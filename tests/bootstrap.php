<?php
require_once './includes/SplClassLoader.php';
$loader = new SplClassLoader('Geekpunks', './includes/');
$loader->register();