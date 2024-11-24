<?php
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class ProfileController extends Controller
{
    private $model;

    public function __construct($f3)
    {
        parent::__construct($f3);
        $this->model = new User();
    }

public function render()
{
    // Setup the CSS and pass data to the view
    $this->set("css", ["css/login.css"]);
    $this->setPageTitle("Update Profile");
    $this->set("form", "includes/profile-update.html");
    $this->set("container", "profile-container");
    $this->set("username", isset($_SESSION["username"]) ? $_SESSION["username"] : "user");

    // Use regular S3 URL for default avatar
    $this->set("avatar", isset($_SESSION["avatar"]) ? $_SESSION["avatar"] : "https://filmfinder-uploads.s3.us-east-1.amazonaws.com/default-avatar.png");

    // Handle session messages
    $this->set("successMessage", $this->get("SESSION.successMessage") ?? NULL);
    $this->clear("SESSION.successMessage");
    $this->set("deleteSuccessMessage", $this->get("SESSION.deleteSuccessMessage") ?? NULL);
    $this->clear("SESSION.deleteSuccessMessage");

    echo $this->template->render("index.html");
}


    // Clear session messages
    private function clear($key)
    {
        $this->set($key, NULL);
    }

    public function update()
    {
        // Trim and sanitize input values
        $this->set("POST", [
            "username" => trim($this->get("POST.username")),
            "password" => trim($this->get("POST.password")),
            "password-confirm" => trim($this->get("POST.password-confirm")),
        ]);

        $avatar = null;
        // Check if an avatar file is uploaded and handle the upload
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            error_log('File uploaded successfully: ' . print_r($_FILES['avatar'], true));  // Log file upload details
            $avatar = $this->uploadAvatar($_FILES['avatar']);
        } else {
            error_log('File upload error: ' . $_FILES['avatar']['error']);  // Log upload error
        }

        // Validate form input
        if ($this->isFormValid()) {
            // Retrieve sanitized and validated input data
            $username = $this->get("POST.username");
            $password = $this->get("POST.password");
            $userId = $_SESSION["userId"];

            // Update user details in the database
            $updateSuccess = $this->model->updateUser($userId, $username, $password, $avatar);

            if ($updateSuccess) {
                // Update session variables if the database update is successful
                if ($username) {
                    $_SESSION['username'] = $username; 
                }
                if ($password) {
                    $_SESSION['password'] = $password; 
                }
                if ($avatar) {
                    $_SESSION['avatar'] = $avatar;
                }
                // Set a success message and redirect to the profile page
                $this->set("SESSION.successMessage", "User updated successfully.");
                $this->f3->reroute("@profile");
            }

        } else {
            // Set the username back to the view if validation fails
            $this->set("username", $this->get("POST.username"));
        }

        // Render the view again
        $this->render();
    }

    // Handle avatar upload
private function uploadAvatar($file, $currentAvatarUrl = null)
{
    // Initialize the S3 client with credentials from environment variables
    $s3Client = new S3Client([
        'version' => 'latest',
        'region'  => getenv('AWS_REGION'),
        'credentials' => [
            'key'    => getenv('AWS_ACCESS_KEY_ID'),
            'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
        ],
        'debug' => true,  // Enable debugging for AWS SDK
    ]);

    // Log the S3 configuration
    error_log('AWS Region: ' . getenv('AWS_REGION'));
    error_log('AWS Access Key: ' . getenv('AWS_ACCESS_KEY_ID'));
    error_log('AWS Secret Key: ' . getenv('AWS_SECRET_ACCESS_KEY'));

    // Bucket name from environment variable
    $bucket = getenv('AWS_BUCKET_NAME');

    // If a file is provided (i.e., user uploaded a new avatar)
    if ($file && $file['error'] == 0) {
        // Generate a unique name for the file (to avoid name conflicts)
        $fileName = uniqid() . '.' . strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Path to the file in the bucket
        $key = "avatars/{$fileName}";  // This creates the 'avatars/' folder in the bucket automatically

        // Log the file path and MIME type before upload
        error_log('File path: ' . $file['tmp_name']);
        error_log('File MIME type: ' . $file['type']);

        try {
            // Upload the file to S3
            $result = $s3Client->putObject([
                'Bucket'     => $bucket,
                'Key'        => $key,
                'SourceFile' => $file['tmp_name'],
                'ACL'        => 'public-read',  // Make the file publicly readable
                'ContentType' => $file['type'], // Set the correct MIME type
            ]);

            // Log the full result to see if there is an issue
            error_log('S3 Upload Result: ' . print_r($result, true));

            // Return the S3 URL of the uploaded image
            return $result['ObjectURL'];
        } catch (AwsException $e) {
            // Log the error and return null if the upload fails
            error_log("S3 upload error: " . $e->getMessage());
            return null;
        }
    } else {
        // If no file is uploaded, use the current avatar or fallback to default if none exists
        return $currentAvatarUrl ?: "https://filmfinder-uploads.s3.us-east-1.amazonaws.com/default-avatar.png";
    }
}



    // Delete user account
    public function delete()
    {
        $userId = $_SESSION["userId"];
        $this->model->deleteUser($userId);
        $this->f3->reroute("@logout");
    }

    // Validate form data
    private function isFormValid()
    {
        $errors = [];
        // Retrieve sanitized and validated input data
        $username = $this->get("POST.username");
        $pass = $this->get("POST.password");
        $passConfirm = $this->get("POST.password-confirm");

        // Check if username exists
        if ($username) {
            $existingUser = $this->model->getUserByUsername($username);
            if (!empty($existingUser)) {
                array_push($errors, "Username already exists.");
            }
        } 

        // Validate password confirmation
        if ($pass && $passConfirm == "") {
            array_push($errors, "Please confirm the password.");
        } else if (strcmp($passConfirm, $pass) != 0) {
            array_push($errors, "Password doesn't match.");
        }

        return $this->validateForm($errors);
    }
}
?>
