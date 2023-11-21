<?php
define('PATH_DIR', 'https://e2295558.webdev.cmaisonneuve.qc.ca/TP3_CinemaWebDev/'); 

require_once('app/controleurs/Frontend.Class.php');
require_once __DIR__.'app/vues/vendor/autoload.php';

require 'app/includes/config.php';
require 'app/includes/chargementClasses.inc.php';

$url = isset($_GET["url"]) ? explode ('/', ltrim($_GET["url"], '/')) : '/';


session_start();

(new Routeur)->router();


