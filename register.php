<?php
include 'include/koneksi.php';

$message = '';

if (isset($_POST['register'])) {
    $username = $koneksi->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $cek = $koneksi->query("SELECT id FROM users WHERE username='$username'");
    if ($cek->num_rows > 0) {
        $message = "Username sudah digunakan, silakan pilih username lain.";
    } else {
        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
        if ($koneksi->query($sql) === TRUE) {
            $message = "Registrasi berhasil. Anda dapat login sekarang.";
        } else {
            $message = "Error: " . $koneksi->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="au theme template">
    <meta name="author" content="Hau Nguyen">
    <meta name="keywords" content="au theme template">

    <!-- Title Page-->
    <title>Register</title>

    <!-- Fontfaces CSS-->
    <link href="admin/assets/css/font-face.css" rel="stylesheet" media="all">
    <link href="admin/assets/vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="admin/assets/vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
    <link href="admin/assets/vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="admin/assets/vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="admin/assets/vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="admin/assets/vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
    <link href="admin/assets/vendor/wow/animate.css" rel="stylesheet" media="all">
    <link href="admin/assets/vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="admin/assets/vendor/slick/slick.css" rel="stylesheet" media="all">
    <link href="admin/assets/vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="admin/assets/vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="admin/assets/css/theme.css" rel="stylesheet" media="all">

</head>

<body class="animsition">
    <div class="page-wrapper">
        <div class="page-content--bge5">
            <div class="container">
                <div class="login-wrap">
                    <div class="login-content">
                        <div class="login-logo">
                            <a href="#">
                                <img src="admin/assets/images/icon/logo.png" alt="CoolAdmin" />
                            </a>
                        </div>
                        <div class="login-form">

                            <?php if ($message): ?>
                                <div class="alert alert-info"><?php echo $message; ?></div>
                            <?php endif; ?>

                            <form method="post" action="">
                                <div class="form-group">
                                    <label>Username</label>
                                    <input class="au-input au-input--full" type="text" name="username" placeholder="Masukkan Username" required />
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input class="au-input au-input--full" type="password" name="password" placeholder="Masukkan Password" required />
                                </div>
                                <button class="au-btn au-btn--block au-btn--green m-b-20" type="submit" name="register">Register</button>
                            </form>

                            <div class="register-link">
                                <p>
                                    Sudah punya akun?
                                    <a href="login.php">Login di sini</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jquery JS-->
    <script src="admin/assets/vendor/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap JS-->
    <script src="admin/assets/vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="admin/assets/vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <!-- Vendor JS       -->
    <script src="admin/assets/vendor/slick/slick.min.js">
    </script>
    <script src="admin/assets/vendor/wow/wow.min.js"></script>
    <script src="admin/assets/vendor/animsition/animsition.min.js"></script>
    <script src="admin/assets/vendor/bootstrap-progressbar/bootstrap-progressbar.min.js">
    </script>
    <script src="admin/assets/vendor/counter-up/jquery.waypoints.min.js"></script>
    <script src="admin/assets/vendor/counter-up/jquery.counterup.min.js">
    </script>
    <script src="admin/assets/vendor/circle-progress/circle-progress.min.js"></script>
    <script src="admin/assets/vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="admin/assets/vendor/chartjs/Chart.bundle.min.js"></script>
    <script src="admin/assets/vendor/select2/select2.min.js">
    </script>

    <!-- Main JS-->
    <script src="admin/assets/js/main.js"></script>

</body>

</html>
<!-- end document-->