    <?php
    session_start();
    require 'db.php'; // your DB connection

    $action = $_POST['action'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($action === 'signup') {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO admins (email, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $hashed);
        if ($stmt->execute()) {
            $_SESSION['admin_id'] = $stmt->insert_id;
            header("Location: admin_dashboard.php");
        } else {
            echo "Signup failed!";
        }
        $stmt->close();
    }

    if ($action === 'login') {
        $stmt = $conn->prepare("SELECT id, password FROM admins WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['admin_id'] = $row['id'];
                header("Location: admin_dashboard.php");
            } else {
                echo "Invalid password";
            }
        } else {
            echo "No admin found!";
        }
        $stmt->close();
    }
    $conn->close();
    ?>
