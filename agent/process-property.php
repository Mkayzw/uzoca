<?php
require_once("../includes/init.php");

use app\src\AgentDashboard;

// Check if user is logged in and is an agent
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agent') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $agentDashboard = new AgentDashboard();
    
    // Handle file uploads
    $uploadDir = '../assets/images/properties/';
    $mainImage = '';
    $additionalImages = [];
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Process main image
    if (isset($_FILES['main-image']) && $_FILES['main-image']['error'] === UPLOAD_ERR_OK) {
        $mainImageName = uniqid() . '_' . basename($_FILES['main-image']['name']);
        $mainImagePath = $uploadDir . $mainImageName;
        
        if (move_uploaded_file($_FILES['main-image']['tmp_name'], $mainImagePath)) {
            $mainImage = '/uzoca/assets/images/properties/' . $mainImageName;
        }
    }
    
    // Process additional images
    for ($i = 1; $i <= 5; $i++) {
        $fieldName = 'pic-' . $i;
        if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
            $imageName = uniqid() . '_' . basename($_FILES[$fieldName]['name']);
            $imagePath = $uploadDir . $imageName;
            
            if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $imagePath)) {
                $additionalImages[] = '/uzoca/assets/images/properties/' . $imageName;
            }
        }
    }
    
    // Prepare property data
    $propertyData = [
        'agent_id' => $_SESSION['user_id'],
        'title' => $_POST['title'],
        'description' => $_POST['description'],
        'summary' => $_POST['summary'],
        'location' => $_POST['location'],
        'price' => $_POST['price'],
        'category' => $_POST['category'],
        'status' => $_POST['status'],
        'main_image' => $mainImage,
        'additional_images' => json_encode($additionalImages)
    ];
    
    // Add property to database
    if ($agentDashboard->addProperty($propertyData)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Property added successfully']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to add property']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
} 