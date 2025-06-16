<?php

namespace app\src;

use app\assets\DB;

class EditProperty
{
    private $ownerID;
    private $propertyID;
    private $propertyName;
    private $bannerImage;
    private $detailsOnePic;
    private $detailsTwoPic;
    private $detailsThreePic;
    private $detailsFourPic;
    private $detailsFivePic;
    private $getPropertyName;
    private $propertyLocation;
    private $propertyPrice;
    private $propertyCategory;
    private $propertySummary;
    private $propertyDescription;
    private $con;

    public function __construct()
    {
        $this->ownerID = $_SESSION['id'];
        $this->propertyID = $_GET['propertyID'];
        $this->getPropertyName = $_GET['propertyName'];

        $this->con = DB::getInstance();
    }

    private function getHouseDetails()
    {
        return $this->con->select("id, index_img, img_1, img_2, img_3, img_4, img_5, title, price, description, location, type, summary", "properties JOIN property_landlords ON properties.id = property_landlords.property_id", "WHERE properties.id = ? AND link = ? AND property_landlords.user_id = ?", ...[$this->propertyID, $this->getPropertyName, $this->ownerID]);
    }

    // Sets the banner image field of the form
    public function setBannerImage()
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


    /**
     * Gets the details for a particular property
     */
    public function showProperty()
    {

        // Check if the required GET properties are set
        if (is_empty($this->propertyID) || is_empty($this->getPropertyName)) {
            header("Location: /404", true, 301);
        }

        $getHouse = $this->getHouseDetails();

        // Check if the specified apartment exists
        if ($getHouse->num_rows < 1) {
            header("Location: /admin/properties", true, 301);
        }

        while ($house = $getHouse->fetch_object()) : ?>
            <label class="h-[200px] lg:h-[400px] cursor-pointer rounded-xl relative" for="pic-1">
                <div class="grid gap-2 place-content-center justify-center text-center bg-violet-100 dark:text-slate-900 h-full rounded-xl p-3">
                    <i class="fr fi-rr-picture"></i>
                    <p>
                        Browse or drop images
                    </p>
                </div>

                <span class="sr-only">Choose profile photo</span>
                <input type="file" class="h-full cursor-pointer opacity-0 absolute top-0 left-0 w-full rounded-xl z-50 image-selector" id="pic-1" name="pic-1">

                <img class="col-span-12 rounded-xl lg:row-start-1 lg:row-end-5 lg:col-span-6 absolute top-0 left-0 w-full h-full" src="../assets/img/<?= $house->index_img ?>" alt="<?= $house->title ?>" />
            </label>

            <div class="grid gap-4 lg:grid-rows-4 grid-cols-12">
                <label class="h-[200px] lg:row-start-1 lg:row-end-5 lg:col-span-6 col-span-12 cursor-pointer rounded-xl relative lg:h-full" for="pic-2">
                    <div class="grid gap-2 place-content-center justify-center text-center bg-violet-100 dark:text-slate-900 h-full rounded-xl p-3">
                        <i class="fr fi-rr-picture"></i>
                        <p>
                            Browse or drop images
                        </p>
                    </div>

                    <span class="sr-only">Choose profile photo</span>
                    <input type="file" class="h-full cursor-pointer opacity-0 absolute top-0 left-0 w-full rounded-xl z-50 image-selector" id="pic-2" name="pic-2">

                    <img class="col-span-12 rounded-xl lg:row-start-1 lg:row-end-5 lg:col-span-6 absolute top-0 left-0 w-full h-full" src="../assets/img/<?= $house->img_1 ?>" alt="<?= $house->title ?>" />
                </label>

                <label class="h-[200px] col-span-12 lg:row-span-2 lg:col-span-3 cursor-pointer rounded-xl relative" for="pic-3">
                    <div class="grid gap-2 place-content-center justify-center text-center bg-violet-100 dark:text-slate-900 h-full rounded-xl p-3">
                        <i class="fr fi-rr-picture"></i>
                        <p>
                            Browse or drop images
                        </p>
                    </div>

                    <span class="sr-only">Choose profile photo</span>
                    <input type="file" class="h-full cursor-pointer opacity-0 absolute top-0 left-0 w-full rounded-xl image-selector" id="pic-3" name="pic-3">

                    <img class="col-span-12 rounded-xl lg:row-start-1 lg:row-end-5 lg:col-span-6 absolute top-0 left-0 w-full h-full" src="../assets/img/<?= $house->img_2 ?>" alt="<?= $house->title ?>" />
                </label>

                <label class="h-[200px] col-span-12 lg:row-span-2 lg:col-span-3 cursor-pointer rounded-xl relative" for="pic-4">
                    <div class="grid gap-2 place-content-center justify-center text-center bg-violet-100 dark:text-slate-900 h-full rounded-xl p-3">
                        <i class="fr fi-rr-picture"></i>
                        <p>
                            Browse or drop images
                        </p>
                    </div>

                    <span class="sr-only">Choose profile photo</span>
                    <input type="file" class="h-full cursor-pointer opacity-0 absolute top-0 left-0 w-full rounded-xl image-selector" id="pic-4" name="pic-4">

                    <img class="col-span-12 rounded-xl lg:row-start-1 lg:row-end-5 lg:col-span-6 absolute top-0 left-0 w-full h-full" src="../assets/img/<?= $house->img_3 ?>" alt="<?= $house->title ?>" />
                </label>

                <label class="h-[200px] col-span-12 lg:row-span-2 lg:col-span-3 cursor-pointer rounded-xl relative" for="pic-5">
                    <div class="grid gap-2 place-content-center justify-center text-center bg-violet-100 dark:text-slate-900 h-full rounded-xl p-3">
                        <i class="fr fi-rr-picture"></i>
                        <p>
                            Browse or drop images
                        </p>
                    </div>

                    <span class="sr-only">Choose profile photo</span>
                    <input type="file" class="h-full cursor-pointer opacity-0 absolute top-0 left-0 w-full rounded-xl image-selector" id="pic-5" name="pic-5">

                    <img class="col-span-12 rounded-xl lg:row-start-1 lg:row-end-5 lg:col-span-6 absolute top-0 left-0 w-full h-full" src="../assets/img/<?= $house->img_4 ?>" alt="<?= $house->title ?>" />
                </label>

                <label class="h-[200px] col-span-12 lg:row-span-2 lg:col-span-3 cursor-pointer rounded-xl relative" for="pic-6">
                    <div class="grid gap-2 place-content-center justify-center text-center bg-violet-100 dark:text-slate-900 h-full rounded-xl p-3">
                        <i class="fr fi-rr-picture"></i>
                        <p>
                            Browse or drop images
                        </p>
                    </div>

                    <span class="sr-only">Choose profile photo</span>
                    <input type="file" class="h-full cursor-pointer opacity-0 absolute top-0 left-0 w-full rounded-xl image-selector" id="pic-6" name="pic-6">

                    <img class="col-span-12 rounded-xl lg:row-start-1 lg:row-end-5 lg:col-span-6 absolute top-0 left-0 w-full h-full" src="../assets/img/<?= $house->img_5 ?>" alt="<?= $house->title ?>" />
                </label>
            </div>

            <div class="grid gap-4 lg:w-4/5 lg:mx-auto lg:gap-8 mt-8">
                <h3 class="header text-xl -mb-4">
                    <?php $this->editProperty(); ?>
                </h3>

                <div class="grid gap-4 lg:grid-cols-12">
                    <div class="lg:col-span-6">
                        <label class="block mb-1.5 ml-1" for="property-name">
                            Property Name
                        </label>

                        <input class="rounded-lg input" type="text" placeholder="Property Name" name="property-name" id="property-name" autocomplete="off" value="<?= $house->title ?>" />
                    </div>

                    <div class="lg:col-span-6">
                        <label class="block mb-1.5 ml-1" for="location">
                            Property Location
                        </label>

                        <input class="rounded-lg input" type="text" placeholder="Property Location" name="property-location" id="location" autocomplete="off" value="<?= $house->location ?>" />
                    </div>

                    <div class="lg:col-span-6">
                        <label class="block mb-1.5 ml-1" for="price">
                            Price
                        </label>

                        <input class="rounded-lg input" type="number" placeholder="Price" name="property-price" id="property-price" autocomplete="off" value="<?= $house->price ?>" />
                    </div>

                    <div class="lg:col-span-6">
                        <label class="block mb-1.5 ml-1" for="property-category">
                            Property Category
                        </label>

                        <select class="form-select input rounded-lg border-none focus:ring-transparent w-full" name="property-category" id="property-category">
                            <?php if ($house->type === "For Rent") : ?>
                                <option class="bg-white dark:bg-transparent" value="<?= $house->type ?>"><?= $house->type ?></option>
                                <option class="bg-white dark:bg-transparent" value="For Sale">For Sale</option>
                            <?php else : ?>
                                <option class="bg-white dark:bg-transparent" value="<?= $house->type ?>"><?= $house->type ?></option>
                                <option class="bg-white dark:bg-transparent" value="For Rent">For Rent</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="lg:col-span-6">
                        <label class="block mb-1.5 ml-1" for="property-summary">
                            Property Summary
                        </label>

                        <textarea class="input rounded-lg" name="property-summary" id="property-summary" rows="4" placeholder="Property Summary"><?= $house->summary ?></textarea>
                    </div>

                    <div class="lg:col-span-6">
                        <label class="block mb-1.5 ml-1" for="property-description">
                            Property Description
                        </label>

                        <textarea class="input rounded-lg" name="property-description" id="property-description" rows="4" placeholder="Property Description"><?= $house->description ?></textarea>
                    </div>

                    <button class="bg-sky-500 hover:bg-sky-600 focus:bg-sky-600 py-2 w-auto px-4 text-white rounded-lg lg:col-span-12 lg:mx-auto dark:bg-sky-600 dark:hover:bg-sky-700 dark:focus:bg-sky-700 hover:ring-1 hover:ring-sky-500 ring-offset-2 active:ring-1 active:ring-sky-500 dark:ring-offset-slate-800" type="submit" name="edit-property">
                        Edit Property
                    </button>
                </div>
            </div>
            </div>
<?php
        endwhile;
    }

    /**
     * Edits a particular property
     */
    public function editProperty()
    {
        $this->setPropertyName = isset($_POST['property-name']) ? strtolower(trim(strip_tags($_POST['property-name']))) : '';
        $this->propertyLocation = isset($_POST['property-location']) ? ucwords(trim(strip_tags($_POST['property-location']))) : '';
        $this->propertyPrice = isset($_POST['property-price']) ? (int) (trim(strip_tags($_POST['property-price']))) : '';
        $this->propertyCategory = isset($_POST['property-category']) ? trim(strip_tags($_POST['property-category'])) : '';
        $this->propertySummary = isset($_POST['property-summary']) ? ucfirst(trim(strip_tags($_POST['property-summary']))) : '';
        $this->propertyDescription = isset($_POST['property-description']) ? ucfirst(trim(strip_tags($_POST['property-description']))) : '';

        if (isset($_POST['edit-property'])) {
            if (is_empty($this->setPropertyName) || is_empty($this->setPropertyLocation) || is_empty($this->setPropertyPrice) || is_empty($this->setPropertyCategory) || is_empty($this->setPropertySummary) || is_empty($this->setPropertyDescription)) {
                displayMessage("All fields are required.");

                    return;
            } else {
            $updatedFields = [
                    "title" => $this->setPropertyName,
                    "location" => $this->setPropertyLocation,
                    "price" => $this->setPropertyPrice,
                    "type" => $this->setPropertyCategory,
                    "summary" => $this->setPropertySummary,
                    "description" => $this->setPropertyDescription
                ];

                $setClause = [];
                $params = [];
                foreach ($updatedFields as $column => $value) {
                    $setClause[] = "`{$column}` = ?";
                    $params[] = $value;
                }

                $sql = "UPDATE `properties` JOIN `property_landlords` ON properties.id = property_landlords.property_id SET " . implode(", ", $setClause) . " WHERE properties.id = ? AND link = ? AND property_landlords.user_id = ?";
                $params[] = $this->propertyID;
                $params[] = $this->getPropertyName;
                $params[] = $this->ownerID;

                if ($this->con->prepare($sql, str_repeat("s", count($params)), ...$params)) {
                    displayMessage("Property updated successfully!", "success");
                } else {
                    displayMessage("Failed to update property.");
                }
            }
        }
    }

    public function updatePropertyImage(string $image, string $imageColumnName) {
        $sql = "UPDATE `properties` JOIN `property_landlords` ON properties.id = property_landlords.property_id SET `{$imageColumnName}` = ? WHERE properties.id = ? AND link = ? AND property_landlords.user_id = ?";
        if ($this->con->prepare($sql, "ssss", $image, $this->propertyID, $this->getPropertyName, $this->ownerID)) {
            displayMessage("Image updated successfully!", "success");
        } else {
            displayMessage("Failed to update image.");
        }
    }
}
