<?php
require_once('./vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

// Set the page title
$pageTitle = "UZOCA | Contact Us";

// Render the view
view("contact", ["title" => $pageTitle]);
