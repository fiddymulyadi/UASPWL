<?php
session_start();
include './include/koneksi.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $koneksi->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $result = $koneksi->query("SELECT * FROM users WHERE username='$username'");
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: adminpanel/dashboard.php");
            exit;
        } else {
            $message = "Password salah.";
        }
    } else {
        $message = "Username tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login</title>
    <style>
        /* Reset dasar */
        * {
            box-sizing: border-box;
        }

        body,
        html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;

            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: #2c3e50;
            padding: 30px 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            width: 320px;
            text-align: center;
        }

        .logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 20px;
        }

        h2 {
            margin-bottom: 20px;
            color: #FFF;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0 20px 0;
            border: 1.8px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 8px rgba(102, 126, 234, 0.5);
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #667eea;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            letter-spacing: 1px;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #5a67d8;
        }

        .message {
            color: #e74c3c;
            margin-bottom: 15px;
            font-weight: 600;
        }

        p {
            margin-top: 18px;
            font-size: 14px;
            color: #bdc3c7;
            ;
        }

        a {
            color: #FFF;
            text-decoration: none;
            font-weight: 600;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <img src="logo.png" alt="Logo" class="logo" />
        <h2>Login</h2>
        <?php if ($message) {
            echo "<div class='message'>$message</div>";
        } ?>
        <form method="post" action="">
            <input type="text" name="username" placeholder="Username" required autofocus />
            <input type="password" name="password" placeholder="Password" required />
            <input type="submit" value="Login" />
        </form>
        <p>Belum punya akun? <a href="register.php">Register di sini</a></p>
    </div>
</body>

</html>