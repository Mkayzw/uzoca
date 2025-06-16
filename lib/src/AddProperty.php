<?php

namespace app\src;

use app\assets\DB;

class AddProperty
{
    private $con;
    private $bannerImage;
    private $detailsOnePic;
    private $detailsTwoPic;
    private $detailsThreePic;
    private $detailsFourPic;
    private $detailsFivePic;
    private $propertyName;
    private $propertyLocation;
    private $propertyPrice;
    private $propertyCategory;
    private $propertySummary;
    private $propertyDescription;

    public function __construct()
    {
        $this->con = DB::getInstance();
    }

    // Sets the banner image field of the form
    public function setBannerImage(): array
    {
        return $this->bannerImage = isset($_FILES['pic-1']) ? $_FILES['pic-1'] : "";
    }

    // Sets the pic 2 field of a form
    public function setDetailsOnePic(): array
    {
        return $this->detailsOnePic = isset($_FILES['pic-2']) ? $_FILES['pic-2'] : "";
    }

    // Sets the pic 2 field of a form
    public function setDetailsTwoPic(): array
    {
        return $this->detailsTwoPic = isset($_FILES['pic-3']) ? $_FILES['pic-3'] : "";
    }

    // Sets the pic 3 field of a form
    public function setDetailsThreePic(): array
    {
        return $this->detailsThreePic = isset($_FILES['pic-4']) ? $_FILES['pic-4'] : "";
    }

    // Sets the pic 4 field of a form
    public function setDetailsFourPic(): array
    {
        return $this->detailsFourPic = isset($_FILES['pic-5']) ? $_FILES['pic-5'] : "";
    }

    // Sets the pic 5 field of a form
    public function setDetailsFivePic(): array
    {
        return $this->detailsFivePic = isset($_FILES['pic-6']) ? $_FILES['pic-6'] : "";
    }

    // Sets the property name field of a form
    public function setPropertyName(): string
    {
        return $this->propertyName = isset($_POST['property-name']) ? ucwords(strtolower(trim(strip_tags($_POST['property-name'])))) : "";
    }

    // Sets the property location field of a form
    public function setPropertyLocation(): string
    {
        return $this->propertyLocation = isset($_POST['property-location']) ? ucwords(strtolower(trim(strip_tags($_POST['property-location'])))) : "";
    }

    // Sets the property price field of a form
    public function setPropertyPrice(): string
    {
        return $this->propertyPrice = isset($_POST['property-price']) ? strtolower(trim(strip_tags($_POST['property-price']))) : "";
    }

    // Sets the property category field of a form
    public function setPropertyCategory(): string
    {
        return $this->propertyCategory = isset($_POST['property-category']) ? ucwords(strtolower(trim(strip_tags($_POST['property-category'])))) : "";
    }

    // Sets the property summary field of a form
    public function setPropertySummary(): string
    {
        return $this->propertySummary = isset($_POST['property-summary']) ? ucfirst(trim($_POST['property-summary'])) : "";
    }

    // Sets the property description field of a form
    public function setPropertyDescription(): string
    {
        return $this->propertyDescription = isset($_POST['property-description']) ? ucfirst(trim($_POST['property-description'])) : "";
    }

    /**
     * Adds a new property
     * @return void
     */
    public function addNewProperty()
    {
        if (isset($_POST['add-property'])) {
            $uploadFolder = "../assets/img/";
            $renamedImages = [];
            $allowedExtensions = [
                "png",
                "jpeg",
                "jpg",
                "webp",
                "jfif",
                "gif"
            ];

            // Get all fields and check if they are empty
            $fields = [
                $this->setPropertyName(),
                $this->setPropertyLocation(),
                $this->setPropertyPrice(),
                $this->setPropertyCategory(),
                $this->setPropertySummary(),
                $this->setPropertyDescription(),
            ];
            foreach ($fields as $field) {
                if (is_empty($field)) {
                    displayMessage("All fields are required.", "text-rose-500");
                    return;
                }
            }

            // Get all images names
            $imagesNames = [
                $this->setBannerImage()['name'],
                $this->setDetailsOnePic()['name'],
                $this->setDetailsTwoPic()['name'],
                $this->setDetailsThreePic()['name'],
                $this->setDetailsFourPic()['name'],
                $this->setDetailsFivePic()['name'],
            ];

            // Get the temp name of the images
            $imagesTempNames = [
                $this->setBannerImage()['tmp_name'],
                $this->setDetailsOnePic()['tmp_name'],
                $this->setDetailsTwoPic()['tmp_name'],
                $this->setDetailsThreePic()['tmp_name'],
                $this->setDetailsFourPic()['tmp_name'],
                $this->setDetailsFivePic()['tmp_name'],
            ];

            // Check if all the images have been selected and display an appropriate message
            foreach ($imagesNames as $images) {
                if (is_empty($images)) {
                    displayMessage("All images are required.", "text-rose-500");
                    return;
                }
            }

            // Check if the file extension is a valid image extension
            foreach ($imagesNames as $image) {
                if (!in_array(pathinfo($image, PATHINFO_EXTENSION), $allowedExtensions)) {
                    displayMessage("Invalid image extension. Please select a valid image with either a png, jpg, jpeg, or webp extension.", "text-rose-500");
                    return;
                }
            }

            // Rename the images and prepare for upload
            $propertyName = strtolower(str_replace(' ', '-', $this->setPropertyName()));
            $timestamp = time();

            foreach ($imagesNames as $key => $images) {
                $imageName = $propertyName . '-' . $timestamp . '-' . ($key + 1) . '.jpg';
                array_push($renamedImages, $imageName);
            }

            // Upload images
            foreach ($imagesTempNames as $key => $tempName) {
                $imageName = $renamedImages[$key];
                $imageFullPath = $uploadFolder . $imageName;

                if (!move_uploaded_file($tempName, $imageFullPath)) {
                    displayMessage("Failed to upload image " . ($key + 1) . ". Please try again.", "text-rose-500");
                    return;
                }
            }

            // Create property link
            $propertyLink = strtolower(str_replace(' ', '-', $this->propertyName));

            try {
                // Insert into properties table
                $propertyColumns = [
                    "owner_id",
                    "title",
                    "location",
                    "price",
                    "type",
                    "summary",
                    "description",
                    "index_img",
                    "img_1",
                    "img_2",
                    "img_3",
                    "img_4",
                    "img_5",
                    "status"
                ];

                $propertyValues = [
                    $_SESSION['user_id'],
                    $fields[0],
                    $fields[1],
                    $fields[2],
                    $fields[3],
                    $fields[4],
                    $fields[5],
                    $renamedImages[0],
                    $renamedImages[1],
                    $renamedImages[2],
                    $renamedImages[3],
                    $renamedImages[4],
                    $renamedImages[5],
                    "available"
                ];

                // Create placeholders for the SQL query
                $placeholders = str_repeat('?,', count($propertyColumns) - 1) . '?';
                $columns = implode(',', $propertyColumns);
                
                // Prepare and execute the property insertion
                $sql = "INSERT INTO properties ($columns) VALUES ($placeholders)";
                $paramTypes = 'i' . str_repeat('s', count($propertyValues) - 1); // 'i' for owner_id (integer), 's' for other fields (string)
                
                $result = $this->con->prepare($sql, $paramTypes, ...$propertyValues);
                
                if (!$result) {
                    throw new \Exception("Failed to insert property");
                }

                $propertyId = $this->con->lastID();

                // Link property to admin
                $linkSql = "INSERT INTO property_landlords (property_id, user_id) VALUES (?, ?)";
                $linkResult = $this->con->prepare($linkSql, 'ii', $propertyId, $_SESSION['user_id']);

                if (!$linkResult) {
                    throw new \Exception("Failed to link property to admin");
                }

                displayMessage("Property added successfully.", "text-emerald-500");
                header("Refresh: 2, /uzoca/admin/properties", true, 301);
            } catch (\Exception $e) {
                // Delete uploaded images
                foreach ($renamedImages as $image) {
                    @unlink($uploadFolder . $image);
                }
                
                displayMessage("Failed to add property: " . $e->getMessage(), "text-rose-500");
            }
        } else {
            displayMessage("Property Details");
        }
    }
}
