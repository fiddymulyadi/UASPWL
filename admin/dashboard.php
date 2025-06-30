<?php
session_start();
include '../include/koneksi.php';


// Cek apakah user sudah login, jika belum redirect ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}


// Logout jika tombol logout diklik
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: ../login.php");
    exit();
}

// Ambil data profil sekolah
$result = $koneksi->query("SELECT logo FROM school_profile WHERE id=1");
$logo = 'default_logo.png'; // logo default
if ($result && $result->num_rows > 0) {
    $data = $result->fetch_assoc();
    if (!empty($data['logo'])) {
        $logo = 'uploads/' . $data['logo'];
    }
}

// Ambil data profil sekolah
$query = "SELECT * FROM school_profile LIMIT 1";
$result = $koneksi->query($query);
$profile = $result->fetch_assoc();


// Handle form submission untuk simpan/edit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $school_name = $conn->real_escape_string($_POST['school_name']);
    $npsn = $conn->real_escape_string($_POST['npsn']);
    $address = $conn->real_escape_string($_POST['address']);

    // Handle upload logo
    $logo_name = null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $logo_name = time() . "_" . basename($_FILES['logo']['name']);
        $target_file = $target_dir . $logo_name;

        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg','jpeg','png','gif'];

        if (in_array($imageFileType, $allowed_types)) {
            if (!move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
                $logo_name = null; // gagal upload
            }
        } else {
            $logo_name = null; // file bukan gambar
        }
    }

    // Check apakah record sudah ada
    $result = $conn->query("SELECT * FROM school_profile WHERE id=1");
    if ($result->num_rows > 0) {
        // Update data
        if ($logo_name) {
            $sql = "UPDATE school_profile SET 
                school_name='$school_name', npsn='$npsn', address='$address', logo='$logo_name'
                WHERE id=1";
        } else {
            $sql = "UPDATE school_profile SET 
                school_name='$school_name', npsn='$npsn', address='$address'
                WHERE id=1";
        }
    } else {
        // Insert data baru
        $sql = "INSERT INTO school_profile (id, school_name, npsn, address, logo) VALUES 
            (1, '$school_name', '$npsn', '$address', '$logo_name')";
    }
    $conn->query($sql);
}

// Load data saat ini
$data = $koneksi->query("SELECT * FROM school_profile WHERE id=1")->fetch_assoc();

$koneksi->close();
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
    <title>Dashboard</title>

    <!-- Fontfaces CSS-->
    <link href="assets/css/font-face.css" rel="stylesheet" media="all">
    <link href="assets/vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="assets/vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
    <link href="assets/vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="assets/vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="assets/vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="assets/vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
    <link href="assets/vendor/wow/animate.css" rel="stylesheet" media="all">
    <link href="assets/vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="assets/vendor/slick/slick.css" rel="stylesheet" media="all">
    <link href="assets/vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="assets/vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="assets/css/theme.css" rel="stylesheet" media="all">

</head>

<body class="animsition">
    <div class="page-wrapper">
        <!-- HEADER MOBILE-->
        <header class="header-mobile d-block d-lg-none">
            <div class="header-mobile__bar">
                <div class="container-fluid">
                    <div class="header-mobile-inner">
                        <a class="logo" href="/">
                             <img src="<?php echo htmlspecialchars($logo); ?>" alt="Logo Sekolah" style="max-height: 100px;">
                        </a>
                        <button class="hamburger hamburger--slider" type="button">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <nav class="navbar-mobile">
                <div class="container-fluid">
                    <ul class="navbar-mobile__list list-unstyled">
                        <li class="has-sub">
                            <a class="js-arrow" href="#">
                                <i class="fas fa-tachometer-alt"></i>Dashboard</a>
                        </li>
  
                    </ul>
                </div>
            </nav>
        </header>
        <!-- END HEADER MOBILE-->

        <!-- MENU SIDEBAR-->
        <aside class="menu-sidebar d-none d-lg-block">
            <div class="logo">
            <a href="dashboard.php">
                <?php if (!empty($data['logo']) && file_exists('uploads/' . $data['logo'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($data['logo']); ?>" alt="Logo Sekolah" style="max-height: 100px;" />
                <?php else: ?>
                    <img src="assets/images/icon/logo-default.png" alt="Logo Default" style="max-height: 100px;" />
                <?php endif; ?>
            </a>
            </div>
            <div class="menu-sidebar__content js-scrollbar1">
                <nav class="navbar-sidebar">
                    <ul class="list-unstyled navbar__list">
                        <li  class="active has-sub">
                            <a class="js-arrow" href="dashboard.php">
                                <i class="fas fa-tachometer-alt"></i>Dashboard</a>
                        </li>
                        <li>
                            <a class="js-arrow" href="#">
                                 <i class="fas fa-gears"></i>Pengaturan<i class="fas fa-angle-down float-right"></i></a>
                            <ul class="list-unstyled navbar__sub-list js-sub-list">
                                <li>
                                    <a href="profilsekolah.php">Profil Madrasah</a>
                                </li>
                                <li>
                                    <a href="profilkepala.php">Profil Kepala</a>
                                </li>
                                <li>
                                    <a href="visidanmisi.php">Visi dan Misi</a>
                                </li>
                                <li>
                                    <a href="kontak.php">Kontak</a>
                                </li>
                                <li>
                                    <a href="sosmed.php">Sosial Media</a>
                                </li>
                                <li>
                                    <a href="slider.php">Slider</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
                <!-- Tombol logout di bagian paling bawah sidebar -->
                <div class="mt-4 p-3 border-top">
                    <a href="dashboard.php?action=logout" class="btn btn-danger btn-block">
                        <i class="zmdi zmdi-power"></i> Logout
                    </a>
                </div>
            </div>
        </aside>
        <!-- END MENU SIDEBAR-->

        <!-- PAGE CONTAINER-->
        <div class="page-container">
            <!-- HEADER DESKTOP-->
           
            <!-- HEADER DESKTOP-->

            <!-- MAIN CONTENT-->
            <div class="main-content p-t-50">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="overview-wrap">
                                    <h2 class="title-1">Selamat Datang,  <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row m-t-25">
                            <div class="col-sm-6 col-lg-6">
                                <div class="overview-item overview-item--c1">
                                    <div class="overview__inner">
                                        <div class="overview-box p-b-25">
                                            <div class="icon">
                                                <i class="zmdi zmdi-home"></i>
                                            </div>
                                            <div class="text">
                                                 <span>Nama Lembaga:</span>
                                                <h2><?= htmlspecialchars($profile['school_name']); ?></h2>
                                               
                                            </div>
                                        </div>
                                       
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-6">
                                <div class="overview-item overview-item--c2">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix p-b-25">
                                            <div class="icon">
                                                <i class="zmdi zmdi-card w-100"></i>
                                            </div>
                                            <div class="text">
                                                <span>Nomor Statistik Madrasah</span>
                                                <h2><?= htmlspecialchars($profile['nsm']); ?></h4> 
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-6">
                                <div class="overview-item overview-item--c4">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix p-b-25">
                                            <div class="icon">
                                                <i class="zmdi zmdi-card"></i>
                                            </div>
                                            <div class="text">
                                                <span>NPSN</span>
                                                <h2><?= htmlspecialchars($profile['npsn']); ?></h4> 
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-6">
                                <div class="overview-item overview-item--c3">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix p-b-25">
                                            <div class="icon">
                                                <i class="zmdi zmdi-bookmark"></i>
                                            </div>
                                            <div class="text">
                                                <span class="text-bold">Status Lembaga</span>
                                                <h2> <?= htmlspecialchars($profile['status_sekolah']); ?></h2> 
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-6">
                                <div class="overview-item overview-item--c2">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix p-b-25">
                                            <div class="icon">
                                                <i class="zmdi zmdi-trending-up"></i>
                                            </div>
                                            <div class="text">
                                                 <span>Jenjang Pendidikan</span>
                                                <h2><?= htmlspecialchars($profile['jenjang']); ?></h2>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-6">
                                <div class="overview-item overview-item--c4">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix p-b-25">
                                            <div class="icon">
                                                <i class="zmdi zmdi-check"></i>
                                            </div>
                                            <div class="text">
                                                 <span>Status Akreditasi</span>
                                                <h2><?= htmlspecialchars($profile['akreditasi']); ?></h2>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                     
                        
                       
                        <div class="row">
                            <div class="col-md-12">
                                <div class="copyright">
                                    <p>Copyright © 2018 Colorlib. All rights reserved. Template by <a href="https://colorlib.com">Colorlib</a>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END MAIN CONTENT-->
            <!-- END PAGE CONTAINER-->
        </div>

    </div>

    <!-- Jquery JS-->
    <script src="assets/vendor/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap JS-->
    <script src="assets/vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="assets/vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <!-- Vendor JS       -->
    <script src="assets/vendor/slick/slick.min.js">
    </script>
    <script src="assets/vendor/wow/wow.min.js"></script>
    <script src="assets/vendor/animsition/animsition.min.js"></script>
    <script src="assets/vendor/bootstrap-progressbar/bootstrap-progressbar.min.js">
    </script>
    <script src="assets/vendor/counter-up/jquery.waypoints.min.js"></script>
    <script src="assets/vendor/counter-up/jquery.counterup.min.js">
    </script>
    <script src="assets/vendor/circle-progress/circle-progress.min.js"></script>
    <script src="assets/vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="assets/vendor/chartjs/Chart.bundle.min.js"></script>
    <script src="assets/vendor/select2/select2.min.js">
    </script>

    <!-- Main JS-->
    <script src="assets/js/main.js"></script>

</body>
</html>
<!-- end document-->