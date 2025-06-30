<?php
session_start();
include '../include/koneksi.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: ../login.php");
    exit();
}

// Default data supaya tidak undefined index
$defaultData = [
    'school_name' => '',
    'npsn' => '',
    'address' => '',
    'sejarah' => '',
    'akreditasi' => '',
    'nsm' => '',
    'status_sekolah' => 'Negeri',
    'jenjang' => '',
    'logo' => ''
];

// Ambil data dari database
$sql = "SELECT * FROM school_profile LIMIT 1";
$result = $koneksi->query($sql);
if ($result && $result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $data = array_merge($defaultData, $data);
} else {
    $data = $defaultData;
}

// Handle form submission
$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Escape input
    $school_name = $koneksi->real_escape_string($_POST['school_name'] ?? '');
    $npsn = $koneksi->real_escape_string($_POST['npsn'] ?? '');
    $address = $koneksi->real_escape_string($_POST['address'] ?? '');
    $sejarah = $koneksi->real_escape_string($_POST['sejarah'] ?? '');
    $akreditasi = $koneksi->real_escape_string($_POST['akreditasi'] ?? '');
    $nsm = $koneksi->real_escape_string($_POST['nsm'] ?? '');
    $status_sekolah = $koneksi->real_escape_string($_POST['status_sekolah'] ?? 'Negeri');
    $jenjang = $koneksi->real_escape_string($_POST['jenjang'] ?? '');
    $logo = $data['logo']; // keep old logo by default

    // Handle upload logo
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $logo_name = time() . "_" . basename($_FILES['logo']['name']);
        $target_file = $target_dir . $logo_name;

        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
                // Hapus file logo lama jika ada dan berbeda
                if (!empty($data['logo']) && file_exists($target_dir . $data['logo'])) {
                    unlink($target_dir . $data['logo']);
                }
                $logo = $logo_name;
            }
        }
    }

    if ($result && $result->num_rows > 0) {
        // Update existing record
        $update_sql = "UPDATE school_profile SET 
            school_name='$school_name',
            npsn='$npsn',
            address='$address',
            sejarah='$sejarah',
            akreditasi='$akreditasi',
            nsm='$nsm',
            status_sekolah='$status_sekolah',
            jenjang='$jenjang',
            logo='$logo'
            WHERE id=1";
        if ($koneksi->query($update_sql) === TRUE) {
            $message = "Data berhasil diperbarui.";
        } else {
            $message = "Error update data: " . $koneksi->error;
        }
    } else {
        // Insert new record
        $insert_sql = "INSERT INTO school_profile 
            (school_name, npsn, address, sejarah, akreditasi, nsm, status_sekolah, jenjang, logo) VALUES 
            ('$school_name', '$npsn', '$address', '$sejarah', '$akreditasi', '$nsm', '$status_sekolah', '$jenjang', '$logo')";
        if ($koneksi->query($insert_sql) === TRUE) {
            $message = "Data berhasil disimpan.";
        } else {
            $message = "Error simpan data: " . $koneksi->error;
        }
    }

    // Reload data setelah simpan
    $result = $koneksi->query($sql);
    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $data = array_merge($defaultData, $data);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Pengaturan Profil Sekolah | CoolAdmin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    
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

    <style>
        /* Custom style for 2 columns form */
        .form-row > .col-md-6 {
            margin-bottom: 15px;
        }
        .logo-preview {
            max-height: 120px;
            margin-bottom: 10px;
        }
    </style>

</head>
<body class="animsition">
<div class="page-wrapper">
    <!-- MENU SIDEBAR-->
        <aside class="menu-sidebar d-none d-lg-block">
            <div class="logo">
            <a href="#">
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
                        <li>
                            <a class="js-arrow" href="dashboard.php">
                                <i class="fas fa-tachometer-alt"></i>Dashboard</a>
                        </li>
                        <li class="active has-sub">
                            <a href="pengaturan.php">
                                <i class="fas fa-cog"></i>Pengaturan
                            </a>
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
    <!-- HEADER DESKTOP -->
   <!-- PAGE CONTAINER-->
        <div class="page-container">

            <!-- MAIN CONTENT-->
            <div class="main-content p-t-50">
                <div class="section__content section__content--p30">
                      <div class="login-wrap p-4" style="max-width: 1000px; margin: auto; background: #fff; border-radius: 6px;">
                    <div class="container-fluid">
                        <h2 class="title-1 mb-3">Pengaturan Profil Sekolah</h2>
                        <?php if($message): ?>
                          <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
                        <?php endif; ?>
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="school_name">Nama Sekolah</label>
                                    <input type="text" class="form-control" id="school_name" name="school_name" value="<?php echo htmlspecialchars($data['school_name']); ?>" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="npsn">NPSN</label>
                                    <input type="text" class="form-control" id="npsn" name="npsn" value="<?php echo htmlspecialchars($data['npsn']); ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="address">Alamat</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($data['address']); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="address">Sejarah</label>
                                <textarea class="form-control" id="sejarah" name="sejarah" rows="10"><?php echo htmlspecialchars($data['sejarah']); ?></textarea>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="jenjang">Jenjang</label>
                                <input type="text" class="form-control" id="jenjang" name="jenjang" value="<?php echo htmlspecialchars($data['jenjang']); ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="akreditasi">Akreditasi</label>
                                    <input type="text" class="form-control" id="akreditasi" name="akreditasi" value="<?php echo htmlspecialchars($data['akreditasi']); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="nsm">NSM</label>
                                    <input type="text" class="form-control" id="nsm" name="nsm" value="<?php echo htmlspecialchars($data['nsm']); ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="status_sekolah">Status Sekolah</label>
                                    <select class="form-control" id="status_sekolah" name="status_sekolah">
                                        <option value="Negeri" <?php echo ($data['status_sekolah'] == 'Negeri') ? 'selected' : ''; ?>>Negeri</option>
                                        <option value="Swasta" <?php echo ($data['status_sekolah'] == 'Swasta') ? 'selected' : ''; ?>>Swasta</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="logo">Logo Sekolah</label>
                                <?php if (!empty($data['logo']) && file_exists('uploads/' . $data['logo'])): ?>
                                    <div>
                                        <img src="uploads/<?php echo htmlspecialchars($data['logo']); ?>" alt="Logo Sekolah" class="logo-preview img-thumbnail" />
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control-file" id="logo" name="logo" accept="image/*">
                            </div>

                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                      </div>
                </div>
            </div>
            <!-- END MAIN CONTENT -->

        </div>
        <!-- END PAGE CONTAINER-->
</div>

<!-- Jquery JS-->
<script src="assets/vendor/jquery-3.2.1.min.js"></script>
<!-- Bootstrap JS-->
<script src="assets/vendor/bootstrap-4.1/popper.min.js"></script>
<script src="assets/vendor/bootstrap-4.1/bootstrap.min.js"></script>
<!-- Vendor JS       -->
<script src="assets/vendor/slick/slick.min.js"></script>
<script src="assets/vendor/wow/wow.min.js"></script>
<script src="assets/vendor/animsition/animsition.min.js"></script>
<script src="assets/vendor/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
<script src="assets/vendor/counter-up/jquery.waypoints.min.js"></script>
<script src="assets/vendor/counter-up/jquery.counterup.min.js"></script>
<script src="assets/vendor/circle-progress/circle-progress.min.js"></script>
<script src="assets/vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="assets/vendor/chartjs/Chart.bundle.min.js"></script>
<script src="assets/vendor/select2/select2.min.js"></script>
<!-- Main JS-->
<script src="assets/js/main.js"></script>

</body>
</html>
