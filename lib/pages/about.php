<?php
require_once('./vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

// Set the page title
$pageTitle = "UZOCA | About Us";

// Render the view
view("about", ["title" => $pageTitle]); 