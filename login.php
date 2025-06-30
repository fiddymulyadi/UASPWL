<?php
session_start();
include 'include/koneksi.php';

$message = '';

$result = $koneksi->query("SELECT logo FROM school_profile WHERE id=1");
$logo = 'default_logo.png'; // logo default
if ($result && $result->num_rows > 0) {
    $data = $result->fetch_assoc();
    if (!empty($data['logo'])) {
        $logo = 'admin/uploads/' . $data['logo'];
    }
}



if (isset($_POST['login'])) {
    $username = $koneksi->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $result = $koneksi->query("SELECT * FROM users WHERE username = '$username'");

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Login berhasil, simpan session dan redirect
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            header("Location: admin/dashboard.php"); // ganti dengan halaman dashboard Anda
            exit();
        } else {
            $message = "Password salah.";
        }
    } else {
        $message = "Username tidak ditemukan.";
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
    <title>Login</title>

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
                            <a class="logo" href="/">
                             <img src="<?php echo htmlspecialchars($logo); ?>" alt="Logo Sekolah" style="max-height: 100px;">
                        </a>
                        </div>
                        <div class="login-form">

                            <?php if ($message): ?>
                                <div class="alert alert-danger"><?php echo $message; ?></div>
                            <?php endif; ?>

                            <form method="post" action="">
                                <div class="form-group">
                                    <label>Username</label>
                                    <input class="au-input au-input--full" type="text" name="username" placeholder="Masukkan Username" required/>
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input class="au-input au-input--full" type="password" name="password" placeholder="Masukkan Password" required/>
                                </div>
                                <button class="au-btn au-btn--block au-btn--green m-b-20" type="submit" name="login">Login</button>
                            </form>

                            <div class="register-link">
                                <p>
                                    Belum punya akun?
                                    <a href="register.php">Daftar di sini</a>
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
    <script src="admin/assets/vendor/slick/slick.min.js"></script>
    <script src="admin/assets/vendor/wow/wow.min.js"></script>
    <script src="admin/assets/vendor/animsition/animsition.min.js"></script>
    <script src="admin/assets/vendor/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <script src="admin/assets/vendor/counter-up/jquery.waypoints.min.js"></script>
    <script src="admin/assets/vendor/counter-up/jquery.counterup.min.js"></script>
    <script src="admin/assets/vendor/circle-progress/circle-progress.min.js"></script>
    <script src="admin/assets/vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="admin/assets/vendor/chartjs/Chart.bundle.min.js"></script>
    <script src="admin/assets/vendor/select2/select2.min.js"></script>

    <!-- Main JS-->
    <script src="admin/assets/js/main.js"></script>

</body>
</html>
<!-- end document-->