<?php

namespace app\src;

use app\assets\DB;

class ContactForm {
    private $con;
    private $name;
    private $email;
    private $subject;
    private $message;

    public function __construct() {
        $this->con = DB::getInstance();
    }

    public function setName() {
        return $this->name = isset($_POST['name']) ? ucwords(trim(strip_tags($_POST['name']))) : "";
    }

    public function setEmail() {
        return $this->email = isset($_POST['email']) ? strtolower(trim(strip_tags($_POST['email']))) : "";
    }

    public function setSubject() {
        return $this->subject = isset($_POST['subject']) ? ucwords(strtolower(trim(strip_tags($_POST['subject'])))) : "";
    }

    public function setMessage() {
        return $this->message = isset($_POST['messageContent']) ? ucfirst(trim($_POST['messageContent'])) : "";
    }

    public function sendContactMail() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send-message'])) {
            // Validate inputs
            if (empty($this->setName())) {
                return "<p class='text-rose-500 dark:text-rose-400'>Name field is required.</p>";
            }
            if (empty($this->setEmail())) {
                return "<p class='text-rose-500 dark:text-rose-400'>Email field is required.</p>";
            }
                if (!filter_var($this->setEmail(), FILTER_VALIDATE_EMAIL)) {
                return "<p class='text-rose-500 dark:text-rose-400'>Invalid email format.</p>";
            }
            if (empty($this->setSubject())) {
                return "<p class='text-rose-500 dark:text-rose-400'>Subject field is required.</p>";
            }
            if (empty($this->setMessage())) {
                return "<p class='text-rose-500 dark:text-rose-400'>Message field is required.</p>";
            }

            try {
                $sql = "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
                $stmt = $this->con->prepare($sql);
                $stmt->execute([$this->name, $this->email, $this->subject, $this->message]);
                
                return "<p class='text-green-500 dark:text-green-400'>Message sent successfully! We'll get back to you soon.</p>";
            } catch (\Exception $e) {
                error_log("Error submitting contact form: " . $e->getMessage());
                return "<p class='text-rose-500 dark:text-rose-400'>Failed to send message. Please try again later.</p>";
            }
        }
        return "";
    }
} 