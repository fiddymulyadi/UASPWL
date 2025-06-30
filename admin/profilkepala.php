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
    'nama' => '',
    'sambutan' => '',
    'foto' => ''
];

// Ambil data dari database
$sql = "SELECT * FROM kepala_sekolah LIMIT 1";
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
    $nama = $koneksi->real_escape_string($_POST['nama'] ?? '');
    $sambutan = $koneksi->real_escape_string($_POST['sambutan'] ?? '');
    $foto = $data['foto']; // keep old logo by default

    // Handle upload Foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $foto_name = time() . "_" . basename($_FILES['foto']['name']);
        $target_file = $target_dir . $foto_name;

        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                // Hapus file logo lama jika ada dan berbeda
                if (!empty($data['foto']) && file_exists($target_dir . $data['foto'])) {
                    unlink($target_dir . $data['foto']);
                }
                $foto = $foto_name;
            }
        }
    }

    if ($result && $result->num_rows > 0) {
        // Update existing record
        $update_sql = "UPDATE kepala_sekolah SET 
            nama='$nama',
            sambutan='$sambutan',
            foto='$foto'
            WHERE id=1";
        if ($koneksi->query($update_sql) === TRUE) {
            $message = "Data berhasil diperbarui.";
        } else {
            $message = "Error update data: " . $koneksi->error;
        }
    } else {
        // Insert new record
        $insert_sql = "INSERT INTO kepala_sekolah
            (nama, sambutan, foto) VALUES 
            ('$nama', '$sambutan', '$foto')";
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

$profil = $koneksi->query("SELECT * FROM school_profile WHERE id=1")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Pengaturan Kepala Madrasah</title>
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

        .foto-preview {
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
                <?php if (!empty($profil['logo']) && file_exists('uploads/' . $profil['logo'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($profil['logo']); ?>" alt="Logo Sekolah" style="max-height: 100px;" />
                <?php else: ?>
                    <img src="assets/images/icon/logo.png" alt="Logo Default" style="max-height: 100px;" />
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
                        <li>
                            <a class="js-arrow" href="#">
                                 <i class="fas fa-gears"></i>Pengaturan<i class="fas fa-angle-down float-right"></i></a>
                            <ul class="list-unstyled navbar__sub-list js-sub-list">
                                <li>
                                    <a href="profilsekolah.php">Profil Madrasah</a>
                                </li>
                                <li class="active has-sub">
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
    <!-- HEADER DESKTOP -->
   <!-- PAGE CONTAINER-->
        <div class="page-container">

            <!-- MAIN CONTENT-->
            <div class="main-content p-t-50">
                <div class="section__content section__content--p30">
                      <div class="login-wrap p-4" style="max-width: 1000px; margin: auto; background: #fff; border-radius: 6px;">
                    <div class="container-fluid">
                        <h2 class="title-1 mb-3">Pengaturan Kepala Madrasah</h2>
                        <?php if($message): ?>
                          <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
                        <?php endif; ?>
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="school_name">Nama Kepala</label>
                                    <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($data['nama']); ?>" required>
                                </div>
                                <div class="form-group col-md-12">
                                <label for="address">Sambutan</label>
                                <textarea class="form-control" id="sambutan" name="sambutan" rows="10"><?php echo htmlspecialchars($data['sambutan']); ?></textarea>
                            </div>
                            </div>
                            <div class="form-group">
                                <label for="foto">Foto Profil</label>
                                <?php if (!empty($data['foto']) && file_exists('uploads/' . $data['foto'])): ?>
                                    <div>
                                        <img src="uploads/<?php echo htmlspecialchars($data['foto']); ?>" alt="Foto Kepala" class="foto-preview img-thumbnail" />
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control-file" id="foto" name="foto" accept="image/*">
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
