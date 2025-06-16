<?php
session_start();

// Check if user is logged in and is a landlord
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'landlord') {
    header("Location: /uzoca/login.php");
    exit();
}

$pageTitle = "UZOCA | My Properties";
require_once("./includes/Header.php");

use app\src\ViewProperties;
use app\assets\DB;

$DB = DB::getInstance();
$viewProperties = new ViewProperties($DB);
?>

<div class="space-y-8">
    <div class="flex flex-wrap justify-between gap-y-2 gap-x-4 items-center">
        <h3 class="header text-2xl">
            My Properties
        </h3>

        <a class="inline-block rounded-lg py-1.5 px-3 text-white bg-sky-500 hover:bg-sky-600 hover:ring-1 hover:ring-sky-500 ring-offset-2 active:ring-1 active:ring-sky-500 dark:ring-offset-slate-800" href="/uzoca/landlord/properties/add.php">
                Add New Property
            </a>
        </div>

    <div class="bg-white p-4 lg:p-8 rounded-xl dark:bg-slate-900 dark:text-slate-100 space-y-8">
        <div class="overflow-x-auto">
            <?php $viewProperties->showProperties(); ?>
        </div>
    </div>
</div>

<?php require_once("./includes/Footer.php"); ?> 