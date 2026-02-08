<?php
require_once 'config/config.php';

echo "Setting up database...\n";

// Check if email column exists
$check_email = "SHOW COLUMNS FROM useraccounts LIKE 'Email'";
$result = $conn->query($check_email);

if ($result->num_rows == 0) {
    echo "Adding Email column to useraccounts table...\n";
    $alter_sql = "ALTER TABLE useraccounts ADD COLUMN Email VARCHAR(150) NULL AFTER Username";
    if ($conn->query($alter_sql)) {
        echo "Email column added successfully.\n";
    } else {
        echo "Error adding Email column: " . $conn->error . "\n";
    }
} else {
    echo "Email column already exists.\n";
}

// Check if admin user exists
$check_admin = "SELECT * FROM useraccounts WHERE Username = 'admin'";
$result = $conn->query($check_admin);

if ($result->num_rows == 0) {
    echo "Creating admin user...\n";
    $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
    $insert_admin = "INSERT INTO useraccounts (AccountID, EmployeeID, Username, PasswordHash, Email, IsVerified, AccountStatus) VALUES (1, 1, 'admin', '$password_hash', 'suruizandrie@gmail.com', 1, 'Active')";
    
    if ($conn->query($insert_admin)) {
        echo "Admin user created successfully.\n";
    } else {
        echo "Error creating admin user: " . $conn->error . "\n";
    }
} else {
    echo "Admin user already exists. Updating email...\n";
    $update_email = "UPDATE useraccounts SET Email = 'suruizandrie@gmail.com' WHERE Username = 'admin'";
    if ($conn->query($update_email)) {
        echo "Admin email updated.\n";
    } else {
        echo "Error updating admin email: " . $conn->error . "\n";
    }
}

// Check if admin role exists
$check_role = "SELECT * FROM roles WHERE RoleName = 'admin'";
$result = $conn->query($check_role);

if ($result->num_rows == 0) {
    echo "Creating admin role...\n";
    $insert_role = "INSERT INTO roles (RoleID, RoleName, Description) VALUES (1, 'admin', 'System Administrator with full access')";
    if ($conn->query($insert_role)) {
        echo "Admin role created successfully.\n";
    } else {
        echo "Error creating admin role: " . $conn->error . "\n";
    }
} else {
    echo "Admin role already exists.\n";
}

// Check if user has admin role
$check_user_role = "SELECT * FROM useraccountroles WHERE AccountID = 1 AND RoleID = 1";
$result = $conn->query($check_user_role);

if ($result->num_rows == 0) {
    echo "Assigning admin role to admin user...\n";
    $insert_user_role = "INSERT INTO useraccountroles (AccountID, RoleID) VALUES (1, 1)";
    if ($conn->query($insert_user_role)) {
        echo "Admin role assigned successfully.\n";
    } else {
        echo "Error assigning admin role: " . $conn->error . "\n";
    }
} else {
    echo "Admin role already assigned.\n";
}

// Test the admin user
echo "\nTesting admin user...\n";
$test_query = "SELECT * FROM useraccounts WHERE Username = 'admin'";
$result = $conn->query($test_query);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "Admin user found:\n";
    echo "Username: " . $user['Username'] . "\n";
    echo "Email: " . $user['Email'] . "\n";
    echo "Password Hash: " . $user['PasswordHash'] . "\n";
    echo "Account Status: " . $user['AccountStatus'] . "\n";
    
    // Test password verification
    $test_password = 'admin123';
    if (password_verify($test_password, $user['PasswordHash'])) {
        echo "Password verification: SUCCESS\n";
    } else {
        echo "Password verification: FAILED\n";
    }
} else {
    echo "Admin user not found!\n";
}

$conn->close();
echo "\nDatabase setup complete!\n";
?>
