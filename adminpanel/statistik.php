<?php

include '../include/koneksi.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: ../login.php");
    exit;
}

$message = "";

$stmt1 = $koneksi->prepare("SELECT * FROM profil_sekolah LIMIT 1");
$stmt1->execute();
$result1 = $stmt1->get_result();
$profil = $result1->fetch_assoc();


// Cek apakah data kontak ada
$data = [];
$stmt = $koneksi->prepare("SELECT * FROM statistik_madrasah LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $id = $data['id'];
} else {
    $id = null; // tanda belum ada data
}

// Fungsi ambil data profil sekolah, ambil 1 data saja
function getDataStats($koneksi)
{
    $stmt = $koneksi->prepare("SELECT * FROM statistik_madrasah LIMIT 1");
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jml_siswa = $_POST['jml_siswa'] ?? '';
    $jml_alumni = $_POST['jml_alumni'] ?? '';
    $jml_guru = $_POST['jml_guru'] ?? '';
    $jml_tu = $_POST['jml_tu'] ?? '';
    $jml_kelas = $_POST['jml_kelas'] ?? '';


    if ($id) {
        // Update data jika sudah ada
        $stmtUpdate = $koneksi->prepare("UPDATE statistik_madrasah SET jml_siswa=?, jml_alumni=?, jml_guru=?, jml_tu=?, jml_kelas=? WHERE id=?");
        $stmtUpdate->bind_param("sssssi", $jml_siswa, $jml_alumni, $jml_guru, $jml_tu, $jml_kelas, $id);
        if ($stmtUpdate->execute()) {
            $message = "Data profil berhasil diperbarui.";
        } else {
            $message = "Gagal memperbarui data: " . $stmtUpdate->error;
        }
    } else {
        // Insert data baru jika belum ada
        $stmtInsert = $koneksi->prepare("INSERT INTO statistik_madrasah (jml_siswa, jml_alumni, jml_guru, jml_tu, jml_kelas) VALUES (?, ?, ?, ?, ?)");
        $stmtInsert->bind_param("sssss", $jml_siswa, $jml_alumni, $jml_guru, $jml_tu, $jml_kelas);
        if ($stmtInsert->execute()) {
            $message = "Data profil berhasil disimpan.";
        } else {
            $message = "Gagal menyimpan data: " . $stmtInsert->error;
        }
    }
}

// Ambil ulang data terbaru untuk ditampilkan di form

$data = getDataStats($koneksi);
if (!$data) {
    $data = [
        'jml_siswa' => '',
        'jml_alumni' => '',
        'jml_guru' => '',
        'jml_tu' => '',
        'jml_kelas' => ''
    ];
}

$koneksi->set_charset("utf8mb4");

?>

<!DOCTYPE html>
<html lang="id">
<meta charset="UTF-8">

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
            <?php if (!empty($profil['logo']) && file_exists('uploads/' . $profil['logo'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($profil['logo']); ?>" alt="Logo Sekolah" style="max-height: 100px;" />
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
                <a href="profilsekolah.php">Profil Madrasah</a>
                <a href="profilkepala.php">Profil Kepala</a>
                <a href="visimisi.php">Visi dan Misi</a>
                <a href="kontak.php">Kontak</a>
                <a href="statistik.php" class="active">Statistik</a>
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
        <form action="" method="post" enctype="multipart/form-data" class="school-form" style="max-width: 1000px">
            <?php if ($message): ?>
                <span class=" closebtn">&times;</span>
                <div class="alert alert-info">
                    <span class="closebtn">&times;</span>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            <div class="form-grid">
                <div class="form-group">
                    <label for="Siswa">Jml Siswa</label>
                    <input type="text" name="jml_siswa" id="jml_siswa" value="<?php echo htmlspecialchars($data['jml_siswa']); ?>" required />
                </div>
                <div class="form-group">
                    <label for="Aumni">Jml Alumni</label>
                    <input type="text" id="jml_alumni" name="jml_alumni" value="<?php echo htmlspecialchars($data['jml_alumni']); ?>" required />
                </div>
                <div class="form-group">
                    <label for="Guru">Jml Guru</label>
                    <input type="text" id="jml_guru" name="jml_guru" value="<?php echo htmlspecialchars($data['jml_guru']); ?>" required />
                </div>
                <div class="form-group">
                    <label for="tu">Jml TU</label>
                    <input type="text" id="jml_tu" name="jml_tu" value="<?php echo htmlspecialchars($data['jml_tu']); ?>">
                </div>
                <div class="form-group">
                    <label for="Kelas">Jml Kelas</label>
                    <input type="text" id="jml_kelas" name="jml_kelas" value="<?php echo htmlspecialchars($data['jml_kelas']); ?>">
                </div>
            </div>
            <div>
                <button type="submit" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
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