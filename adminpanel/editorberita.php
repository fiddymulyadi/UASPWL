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


// Handle Create
if (isset($_POST['create'])) {
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul_berita']);
    $isi = mysqli_real_escape_string($koneksi, $_POST['isi_berita']);
    $tanggal_posting = date('Y-m-d H:i:s');

    // Upload gambar
    $gambar = null;
    if ($_FILES['gambar_unggulan']['name']) {
        $target_dir = "uploads/";
        $gambar = basename($_FILES["gambar_unggulan"]["name"]);
        $target_file = $target_dir . $gambar;
        move_uploaded_file($_FILES["gambar_unggulan"]["tmp_name"], $target_file);
    }

    $query = "INSERT INTO berita (judul_berita, isi_berita, gambar_unggulan, tanggal_posting, tanggal_update) 
              VALUES ('$judul', '$isi', '$gambar', '$tanggal_posting', '$tanggal_posting')";
    mysqli_query($koneksi, $query);
    header("Location: editorberita.php");
    exit;
}

// Handle Update
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul_berita']);
    $isi = mysqli_real_escape_string($koneksi, $_POST['isi_berita']);

    // Cek upload gambar baru
    $gambar = null;
    if ($_FILES['gambar_unggulan']['name']) {
        $target_dir = "uploads/";
        $gambar = basename($_FILES["gambar_unggulan"]["name"]);
        $target_file = $target_dir . $gambar;
        move_uploaded_file($_FILES["gambar_unggulan"]["tmp_name"], $target_file);
        $query = "UPDATE berita SET judul_berita='$judul', isi_berita='$isi', gambar_unggulan='$gambar', tanggal_update=NOW() WHERE id=$id";
    } else {
        $query = "UPDATE berita SET judul_berita='$judul', isi_berita='$isi', tanggal_update=NOW() WHERE id=$id";
    }
    mysqli_query($koneksi, $query);
    header("Location: editorberita.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($koneksi, "DELETE FROM berita WHERE id=$id");
    header("Location: editorberita.php");
    exit;
}

// Ambil data berita
$result = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal_posting DESC");
?>

<!DOCTYPE html>
<html lang="id">
<meta charset="UTF-8">

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
            <div class="menu-item">
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

            <div class="menu-item has-submenu active">
                <span><i class="fas fa-cog icon-left"></i>Konten</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="submenu">
                <a href="editorberita.php" class="active">Berita/Artikel</a>
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
            <h1>Manajemen Berita</h1>
            <div class="school-formcr">
                <button class="add-btn" id="openCreateModal"><i class="fa fa-plus"></i> Tambah</button>

                <table class="table-berita">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Judul Berita</th>
                            <th>Isi Berita</th>
                            <th>Gambar</th>
                            <th>Tgl Posting</th>
                            <th>Tgl Update</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['judul_berita']) ?></td>
                                <td><?= nl2br(htmlspecialchars(substr($row['isi_berita'], 0, 100))) . '...' ?></td>
                                <td>
                                    <?php if ($row['gambar_unggulan']) : ?>
                                        <img src="uploads/<?= $row['gambar_unggulan'] ?>" alt="Gambar Berita" class="thumbnail" />
                                    <?php else : ?>
                                        <span>Tidak ada gambar</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date("Y-m-d", strtotime($row['tanggal_posting'])) ?></td>
                                <td><?= date("Y-m-d", strtotime($row['tanggal_update'])) ?></td>
                                <td>
                                    <button class="action-btn editBtn"
                                        data-id="<?= $row['id'] ?>"
                                        data-judul="<?= htmlspecialchars($row['judul_berita'], ENT_QUOTES) ?>"
                                        data-isi="<?= htmlspecialchars($row['isi_berita'], ENT_QUOTES) ?>"
                                        data-gambar="<?= $row['gambar_unggulan'] ?>"
                                        title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <a href="?delete=<?= $row['id'] ?>" class="action-btn" title="Hapus"
                                        onclick="return confirm('Yakin ingin menghapus berita ini?')">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- Modal Form Create -->
                <div id="createModal" class="modal">
                    <div class="modal-content">
                        <span class="close" id="closeCreateModal">&times;</span>
                        <div class="modal-header">
                            <h2>Tambah Berita Baru</h2>
                        </div>
                        <form method="post" enctype="multipart/form-data" action="editorberita.php">
                            <label for="judul_berita">Judul Berita</label>
                            <input type="text" name="judul_berita" id="judul_berita" required />

                            <label for="isi_berita">Isi Berita</label>
                            <textarea name="isi_berita" id="isi_berita" required></textarea>

                            <label for="gambar_unggulan">Gambar Unggulan</label>
                            <input type="file" name="gambar_unggulan" id="gambar_unggulan" accept="image/*" />

                            <button type="submit" name="create">Simpan</button>
                        </form>
                    </div>
                </div>

                <!-- Modal Form Edit -->
                <div id="editModal" class="modal">
                    <div class="modal-content">
                        <span class="close" id="closeEditModal">&times;</span>
                        <div class="modal-header">
                            <h2>Edit Berita</h2>
                        </div>
                        <form method="post" enctype="multipart/form-data" action="editorberita.php">
                            <input type="hidden" name="id" id="edit_id" />
                            <label for="edit_judul_berita">Judul Berita</label>
                            <input type="text" name="judul_berita" id="edit_judul_berita" required />

                            <label for="edit_isi_berita">Isi Berita</label>
                            <textarea name="isi_berita" id="edit_isi_berita" required></textarea>

                            <label for="edit_gambar_unggulan">Gambar Unggulan (kosongkan jika tidak ingin ganti)</label>
                            <input type="file" name="gambar_unggulan" id="edit_gambar_unggulan" accept="image/*" />

                            <div id="currentImagePreview" style="margin-top: 10px;"></div>

                            <button type="submit" name="update">Update</button>
                        </form>
                    </div>
                </div>

                <script>
                    // Modal Create
                    const createModal = document.getElementById("createModal");
                    const openCreateBtn = document.getElementById("openCreateModal");
                    const closeCreateBtn = document.getElementById("closeCreateModal");

                    openCreateBtn.onclick = () => createModal.style.display = "block";
                    closeCreateBtn.onclick = () => createModal.style.display = "none";

                    // Modal Edit
                    const editModal = document.getElementById("editModal");
                    const closeEditBtn = document.getElementById("closeEditModal");

                    // Open edit modal and fill form with data
                    document.querySelectorAll(".editBtn").forEach(button => {
                        button.onclick = () => {
                            const id = button.getAttribute("data-id");
                            const judul = button.getAttribute("data-judul");
                            const isi = button.getAttribute("data-isi");
                            const gambar = button.getAttribute("data-gambar");

                            document.getElementById("edit_id").value = id;
                            document.getElementById("edit_judul_berita").value = judul;
                            document.getElementById("edit_isi_berita").value = isi;

                            const preview = document.getElementById("currentImagePreview");
                            if (gambar) {
                                preview.innerHTML = `<strong>Gambar saat ini:</strong><br><img src="uploads/${gambar}" alt="Gambar Berita" class="thumbnail" />`;
                            } else {
                                preview.innerHTML = `<em>Tidak ada gambar unggulan.</em>`;
                            }

                            editModal.style.display = "block";
                        };
                    });

                    closeEditBtn.onclick = () => editModal.style.display = "none";

                    // Tutup modal jika klik di luar konten modal
                    window.onclick = function(event) {
                        if (event.target == createModal) {
                            createModal.style.display = "none";
                        }
                        if (event.target == editModal) {
                            editModal.style.display = "none";
                        }
                    };
                </script>
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
            </div>
        </div>
    </main>
</body>

</html>