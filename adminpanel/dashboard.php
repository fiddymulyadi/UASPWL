<?php

include '../include/koneksi.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: ../login.php");
    exit;
}


$stmt1 = $koneksi->prepare("SELECT * FROM profil_sekolah LIMIT 1");
$stmt1->execute();
$result1 = $stmt1->get_result();
$profil = $result1->fetch_assoc();

$stmt2 = $koneksi->prepare("SELECT * FROM profil_kamad LIMIT 1");
$stmt2->execute();
$result2 = $stmt2->get_result();
$profilkamad = $result2->fetch_assoc();



?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard</title>
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        rel="stylesheet" />
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
            <div class="menu-item active">
                <span><a href="dashboard.php"><i class="fas fa-tachometer-alt icon-left"></i>Dashboard</a></span>
            </div>
            <div class="menu-item has-submenu">
                <span><i class="fas fa-users icon-left"></i>Pengaturan Profil</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="submenu">
                <a href="profilsekolah.php">Profil Madrasah</a>
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
            <h1>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?> !</h1>
        </div>
        <div class="cards-grid">
            <div class="card">
                <div class="icon"><i class="fa-solid fa-house white"></i></div>
                <div class="text">
                    <div class="title">Nama Lembaga</div>

                    <div class="description"><?= htmlspecialchars($profil['nama_lembaga'] ?? 'Belum ada data') ?></div>

                </div>
            </div>
            <div class="card">
                <div class="icon"><i class="fa-solid fa-bookmark"></i></div>
                <div class="text ">
                    <div class="title">Kepala Madrasah</div>
                    <div class="description"><?= htmlspecialchars($profilkamad['nama_kepala'] ?? 'Belum ada data'); ?></div>
                </div>
            </div>

            <div class="card">
                <div class="icon"><i class="fa-solid fa-bookmark"></i></div>
                <div class="text ">
                    <div class="title">NSM</div>
                    <div class="description"><?= htmlspecialchars($profil['nsm'] ?? 'Belum ada data'); ?></div>
                </div>
            </div>
            <div class="card">

                <div class="icon"><i class="fa-solid fa-bookmark"></i></div>
                <div class="text">
                    <div class="title">NPSN</div>
                    <div class="description"><?= htmlspecialchars($profil['npsn'] ?? 'Belum ada data'); ?></div>
                </div>
            </div>

            <div class="card">
                <div class="icon"><i class="fa-solid fa-shield"></i></div>
                <div class="text">
                    <div class="title">Status Lembaga</div>
                    <div class="description"><?= htmlspecialchars($profil['status'] ?? 'Belum ada data'); ?></div>
                </div>
            </div>
            <div class="card">
                <div class="icon"><i class="fa-solid fa-arrow-trend-up"></i></div>
                <div class="text">
                    <div class="title">Jenjang Pendidikan</div>
                    <div class="description"><?= htmlspecialchars($profil['jenjang_pendidikan'] ?? 'Belum ada data'); ?></div>
                </div>
            </div>
            <div class="card">
                <div class="icon"><i class="fa-solid fa-check-double"></i></div>
                <div class="text">
                    <div class="title">Akreditasi</div>
                    <div class="description"> <?= htmlspecialchars($profil['akreditasi'] ?? 'Belum ada data'); ?></div>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.querySelectorAll('nav .menu-item.has-submenu').forEach(menuItem => {
            menuItem.addEventListener('click', () => {
                // Toggle class active pada menu-item yang diklik
                const isActive = menuItem.classList.contains('active');

                // Hapus semua active dan sembunyikan submenu lain
                document.querySelectorAll('nav .menu-item.has-submenu').forEach(item => item.classList.remove('active'));

                if (!isActive) {
                    menuItem.classList.add('active');
                }
            });
        });
    </script>




</body>

</html>