<?php

include '../include/koneksi.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: ../login.php");
    exit;
}

$message = "";


// Cek apakah data profil ada
$data = [];
$stmt = $koneksi->prepare("SELECT * FROM profil_sekolah LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $id = $data['id'];
} else {
    $id = null; // tanda belum ada data
}

// Fungsi ambil data profil sekolah, ambil 1 data saja
function getDataProfil($koneksi)
{
    $stmt = $koneksi->prepare("SELECT * FROM profil_sekolah LIMIT 1");
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lembaga = $_POST['nama_lembaga'] ?? '';
    $npsn = $_POST['npsn'] ?? '';
    $nsm = $_POST['nsm'] ?? '';
    $jenjang_pendidikan = $_POST['jenjang_pendidikan'] ?? '';
    $status = $_POST['status'] ?? '';
    $akreditasi = $_POST['akreditasi'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $sejarah = $_POST['sejarah'] ?? '';

    // Ambil data lama dulu untuk ambil id dan logo lama
    $dataLama = getDataProfil($koneksi);
    if ($dataLama !== null) {
        $id = $dataLama['id'];
        $logo = $dataLama['logo'];
    } else {
        $id = null;
        $logo = '';
    }

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

    if ($id) {
        // Update data jika sudah ada
        $stmtUpdate = $koneksi->prepare("UPDATE profil_sekolah SET nama_lembaga=?, npsn=?, nsm=?, jenjang_pendidikan=?, status=?, akreditasi=?, alamat=?, sejarah=?, logo=? WHERE id=?");
        $stmtUpdate->bind_param("sssssssssi", $nama_lembaga, $npsn, $nsm, $jenjang_pendidikan, $status, $akreditasi, $alamat, $sejarah, $logo, $id);
        if ($stmtUpdate->execute()) {
            $message = "Data profil berhasil diperbarui.";
        } else {
            $message = "Gagal memperbarui data: " . $stmtUpdate->error;
        }
    } else {
        // Insert data baru jika belum ada
        $stmtInsert = $koneksi->prepare("INSERT INTO profil_sekolah (nama_lembaga, npsn, nsm, jenjang_pendidikan, status, akreditasi, alamat, sejarah, logo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmtInsert->bind_param("sssssssss", $nama_lembaga, $npsn, $nsm, $jenjang_pendidikan, $status, $akreditasi, $alamat, $sejarah, $logo);
        if ($stmtInsert->execute()) {
            $message = "Data profil berhasil disimpan.";
        } else {
            $message = "Gagal menyimpan data: " . $stmtInsert->error;
        }
    }
}

// Ambil ulang data terbaru untuk ditampilkan di form

$data = getDataProfil($koneksi);
if (!$data) {
    $data = [
        'nama_lembaga' => '',
        'npsn' => '',
        'nsm' => '',
        'jenjang_pendidikan' => '',
        'status' => '',
        'akreditasi' => '',
        'alamat' => '',
        'sejarah' => '',
        'logo' => ''
    ];
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <aside class="sidebar">
        <div class="logo">
            <?php if (!empty($data['logo']) && file_exists('uploads/' . $data['logo'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($data['logo']); ?>" alt="Logo Sekolah" style="max-height: 100px;" />
            <?php else: ?>
                <img src="assets/images/icon/logo.png" alt="Logo Default" style="max-height: 100px;" />
            <?php endif; ?>
        </div>
        <nav>
            <div class="menu-item">
                <span><a href="dashboard.php"><i class="fas fa-tachometer-alt icon-left"></i>Dashboard</a></span>
            </div>
            <div class="menu-item has-submenu active">
                <span><i class="fas fa-users icon-left"></i>Pengaturan Profil</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="submenu">
                <a href="profilsekolah.php" class="active">Profil Madrasah</a>
                <a href="profilkepala.php">Profil Kepala</a>
                <a href="visimisi.php">Visi dan Misi</a>
                <a href="kontak.php">Kontak</a>
                <a href="statistik.php">Statistik</a>
                <a href="readslider.php">Home Slider</a>
            </div>

            <div class="menu-item has-submenu">
                <span><i class="fas fa-cog icon-left"></i>Konten</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="submenu">
                <a href="editorberita.php">Berita/Artikel</a>
                <a href="editorpengumuman.php">Pengumuman</a>
                <a href="editoragenda.php">Agend</a>
            </div>
        </nav>

        <div class="logout">
            <form action="../logout.php" method="post">
                <button type="submit" class="logout-button" title="Logout">
                    <i class="fas fa-power-off"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>
    <main class="content">
        <div class="content-header">
            <h1>Profil Madrasah</h1>

        </div>
        <form action="" method="post" enctype="multipart/form-data" class="school-form">
            <?php if ($message): ?>
                <span class="closebtn">&times;</span>
                <div class="alert alert-info">
                    <span class="closebtn">&times;</span>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            <div class="form-grid">
                <div class="form-group">
                    <label for="nama_lembaga">Nama Lembaga</label>
                    <input type="text" id="nama_lembaga" name="nama_lembaga" value="<?php echo htmlspecialchars($data['nama_lembaga']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="npsn">Nomor Pokok Sekolah Nasional</label>
                    <input type="text" name="npsn" id="npsn" value="<?php echo htmlspecialchars($data['npsn']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="jenjang_pendidikan">Jenjang Pendidikan</label>
                    <input type="text" id="jenjang_pendidikan" name="jenjang_pendidikan" value="<?php echo htmlspecialchars($data['jenjang_pendidikan']); ?>">
                </div>
                <div class="form-group">
                    <label for="akreditasi">Akreditasi</label>
                    <input type="text" id="akreditasi" name="akreditasi" value="<?php echo htmlspecialchars($data['akreditasi']); ?>">
                </div>
                <div class="form-group">
                    <label for="nsm">Nomor Statistik Madrasah</label>
                    <input type="text" id="nsm" name="nsm" value="<?php echo htmlspecialchars($data['nsm']); ?>">
                </div>
                <div class="form-group">
                    <label for="status">Status Madrasah</label>
                    <select name="status">
                        <option value="Negeri" <?php echo ($data['status'] == 'Negeri') ? 'selected' : ''; ?>>Negeri</option>
                        <option value="Swasta" <?php echo ($data['status'] == 'Swasta') ? 'selected' : ''; ?>>Swasta</option>
                    </select>
                </div>
                <div class="form-group full">
                    <label for="alamat">Alamat</label>
                    <textarea name="alamat" id="alamat" name="alamat" required><?php echo htmlspecialchars($data['alamat']); ?></textarea>
                </div>
                <div class="form-group full">
                    <label for="sejarah">Sejarah</label>
                    <textarea class="form-control" id="sejarah" name="sejarah" rows="6"><?php echo htmlspecialchars($data['sejarah']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="logo">Logo Madrasah</label>
                    <?php if (!empty($data['logo']) && file_exists('uploads/' . $data['logo'])): ?>
                        <div>
                            <img src="uploads/<?php echo htmlspecialchars($data['logo']); ?>" alt="Logo Sekolah" class="logo-preview img-thumbnail" />
                        </div>
                    <?php endif; ?>
                    <input type="file" id="logo" name="logo" accept="image/*">
                </div>
            </div>
            <div>
                <button type="submit" id=" saveBtn" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
            </div>
        </form>


    </main>
    <script>
        document.querySelectorAll('nav .menu-item.has-submenu').forEach(menuItem => {
            menuItem.addEventListener('click', () => {
                // Cek apakah menuItem sudah aktif atau belum
                const isActive = menuItem.classList.contains('active');

                if (isActive) {
                    // Jika sudah aktif, hapus class active (sembunyikan submenu)
                    menuItem.classList.remove('active');
                } else {
                    // Jika belum aktif, tutup semua submenu aktif lain terlebih dahulu
                    document.querySelectorAll('nav .menu-item.has-submenu.active').forEach(item => {
                        item.classList.remove('active');
                    });
                    // Kemudian buka submenu yang diklik
                    menuItem.classList.add('active');
                }
            });
        });
    </script>
</body>

</html>