<?php
require_once "BaseRegister.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the submitted data
    $data = [
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'gender' => $_POST['gender'],
        'dob' => $_POST['dob'],
        'ministry' => $_POST['ministry'],
        'address' => $_POST['address'],
        'mobile_number' => $_POST['mobile_number'],
        'email' => $_POST['email'],
        'user_name' => $_POST['user_name'],
        'password' => $_POST['password'],
        'profile_image' => $_FILES['profile_image']['name'],
        'join_date' => date("Y-m-d"),
        'baptism_date' => $_POST['baptism_date'],
        'status' => 'pending'
    ];

    // Check if user already exists
    $member = new BaseRegister('members');
    if ($member->userExists($data['user_name'], $data['email'])) {
        echo "User already exists!";
    } else {
        // Save the image to the server
        move_uploaded_file($_FILES['profile_image']['tmp_name'], "profile_pics/" . $_FILES['profile_image']['name']);

        // Register the member
        if ($member->registerUser($data)) {
            echo "Registration successful! Please wait for admin approval.";
        } else {
            echo "Registration failed.";
        }
    }
}
?>
