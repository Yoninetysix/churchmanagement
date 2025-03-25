<!-- member_register.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Church Member Registration</title>
    <link rel="stylesheet" href="user.css">
</head>
<body>
    <div class="container">
        <h2>Church Member Registration</h2>
        <form action="member_register_action.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="first_name" placeholder="First Name" required><br>
            <input type="text" name="last_name" placeholder="Last Name" required><br>
            <input type="text" name="gender" placeholder="Gender" required><br>
            <input type="date" name="dob" placeholder="Date of Birth" required><br>
            <input type="text" name="ministry" placeholder="Ministry" required><br>
            <input type="text" name="address" placeholder="Address" required><br>
            <input type="text" name="mobile_number" placeholder="Mobile Number" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="text" name="user_name" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="file" name="profile_image" accept="image/*"><br>
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
