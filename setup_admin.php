
<?php
require 'db.php';

$email = 'admin@gmail.com';
$password = 'Admin@12345';
$name = 'Salon Administrator';
$phone = '09123456789';

$check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
mysqli_stmt_bind_param($check, "s", $email);
mysqli_stmt_execute($check);
$result = mysqli_stmt_get_result($check);

if (mysqli_num_rows($result) > 0) {
    $stmt = mysqli_prepare($conn,
        "UPDATE users SET role='admin' WHERE email=?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    echo "Admin account already existed. Admin role verified.";
} else {
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($conn,
        "INSERT INTO users (name, phone, email, password, role)
         VALUES (?, ?, ?, ?, 'admin')");
    mysqli_stmt_bind_param($stmt, "ssss", $name, $phone, $email, $hash);

    if (mysqli_stmt_execute($stmt)) {
        echo "Admin account created successfully.";
    } else {
        echo "Unable to create the admin account.";
    }
}
?>
