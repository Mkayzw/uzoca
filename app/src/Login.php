<?php

namespace app\src;

use app\assets\DB;

class Login
{
    private $con;
    private $phoneEmail;
    private $password;
    private $userType;

    public function __construct()
    {
        $this->con = DB::getInstance();
    }

    // Sets the phone number or email field of the form
    public function setPhoneEmail(): string
    {
        return $this->phoneEmail = isset($_POST['phoneEmail']) ? strtolower(trim(strip_tags($_POST['phoneEmail']))) : "";
    }

    // Sets the password field of a form
    public function setPassword(): string
    {
        return $this->password = isset($_POST['password']) ? $_POST['password'] : "";
    }

    // Sets the user type field of the form
    public function setUserType(): string
    {
        return $this->userType = isset($_POST['userType']) ? strtolower(trim(strip_tags($_POST['userType']))) : "";
    }

    public function loginUser()
    {
        if (isset($_POST['login-submit'])) {
            // Check if user type was selected
            if (is_empty($this->setUserType())) {
                displayMessage("<span class='font-bold'>User Type</span> is required.", "text-rose-500");
                return;
            }

            // Check if a email or phone number was entered and displays the appropriate feedback
            if (is_empty($this->setPhoneEmail())) {
                displayMessage("<span class='font-bold'>Email or Phone Number</span> field is required.", "text-rose-500");
                return;
            }

            // Check if a password was entered and displays the appropriate feedback
            if (is_empty($this->setPassword())) {
                displayMessage("<span class='font-bold'>Password</span> field is required.", "text-rose-500");
                return;
            }

            $params = [
                $this->setPhoneEmail(),
                $this->setPassword(),
            ];

            // Params to check if the chosen phone number or email exists and give appropriate feedback
            $userCheckParams = [
                $this->setPhoneEmail(),
                $this->setPhoneEmail(),
            ];

            // Check user type and query appropriate table
            $table = 'users'; // All users are in the users table
            $role = $this->setUserType();

            $checkIfUserExists = $this->con->select("password", $table, "WHERE (phone = ? OR email = ?) AND role = ?", ...[$this->setPhoneEmail(), $this->setPhoneEmail(), $role]);

            if ($checkIfUserExists->num_rows < 1) {
                displayMessage("No account found with that email and user type.", "text-rose-500");
                return;
            }

            $userExists = $checkIfUserExists->fetch_object();

            if (password_verify($this->setPassword(), $userExists->password)) {
                $setUserSession = $this->con->select("name, id, role", $table, "WHERE (phone = ? OR email = ?) AND role = ?", ...[$this->setPhoneEmail(), $this->setPhoneEmail(), $role])->fetch_object();

                $_SESSION['user'] = $setUserSession->name;
                $_SESSION['id'] = $setUserSession->id;
                $_SESSION['role'] = $setUserSession->role;
                $_SESSION['loggedUser'] = strtolower($setUserSession->name . $setUserSession->id);

                displayMessage("Login successful. You would be redirected to your dashboard shortly.", "text-green-500");

                // Redirect based on user type
                $redirectPath = match($this->setUserType()) {
                    'user' => '/uzoca/user/dashboard',
                    'admin' => '/uzoca/admin',
                    'agent' => '/uzoca/agent',
                    'landlord' => '/uzoca/landlord/dashboard',
                    default => '/uzoca/login'
                };

                header("Refresh: 3, $redirectPath", true, 301);
                    return;
                } else {
                    displayMessage("Incorrect <span class='font-bold'>Password</span>.", "text-rose-500");
                    return;
            }
        } else {
            displayMessage("You need to sign in to access your dashboard");
        }
    }
} 