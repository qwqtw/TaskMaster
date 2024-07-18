<?php

class ManageProfileController extends Controller {

    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get the form data
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Validate and sanitize inputs as necessary

            // Call model method to update the user details
            $userModel = new User();
            $updateResult = $userModel->updateProfile($_SESSION['user_id'], $username, $password);

            if ($updateResult) {
                echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
            }
        }
    }

    public function deleteAccount() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Call model method to delete the user account
            $userModel = new User();
            $deleteResult = $userModel->deleteAccount($_SESSION['user_id']);

            if ($deleteResult) {
                session_destroy(); // Destroy session after account deletion
                echo json_encode(['success' => true, 'message' => 'Account deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete account']);
            }
        }
    }
}
