<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();

/**
 * Requires a specified page
 * @param string $fileName
 */
function view(string $fileName)
{
    $fileFullPath = __DIR__ . "/pages/{$fileName}.php";

    if (file_exists($fileFullPath)) {
        require_once($fileFullPath);
    } else {
        header("Location: /uzoca/404");
        exit;
    }
}

/**
 * Checks if a variable is empty and set
 * @param mixed $field
 * @return bool
 */
function is_empty($field): bool
{
    if (!isset($field) || $field === "") {
        return true;
    } else {
        return false;
    }
}

/**
 * Displays a message with optional styling
 * @param string $message
 * @param string $class
 * @param string $tag
 */
function displayMessage(string $message, string $class = "", string $tag = "p"): void
{
    echo "<{$tag} class='{$class}'>{$message}</{$tag}>";
}
