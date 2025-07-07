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


// Handle tambah data slider
if (isset($_POST['tambah_slider'])) {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];

    // Upload gambar
    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];
    $folder = "uploads/";

    if (move_uploaded_file($tmp, $folder . $gambar)) {
        $sql = "INSERT INTO slider (gambar, judul, deskripsi) VALUES ('$folder$gambar', '$judul', '$deskripsi')";
        mysqli_query($koneksi, $sql);
    }
    header("Location: readslider.php");
    exit;
}

// Handle edit data slider
if (isset($_POST['edit_slider'])) {
    $id = $_POST['id'];
    $judul = $_POST['judul_edit'];
    $deskripsi = $_POST['deskripsi_edit'];

    if (!empty($_FILES['gambar_edit']['name'])) {
        $gambar = $_FILES['gambar_edit']['name'];
        $tmp = $_FILES['gambar_edit']['tmp_name'];
        $folder = "uploads/";
        move_uploaded_file($tmp, $folder . $gambar);
        $sql = "UPDATE slider SET judul='$judul', deskripsi='$deskripsi', gambar='$folder$gambar' WHERE id=$id";
    } else {
        $sql = "UPDATE slider SET judul='$judul', deskripsi='$deskripsi' WHERE id=$id";
    }
    mysqli_query($koneksi, $sql);
    header("Location: readslider.php");
    exit;
}

// Handle hapus data slider
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $sql = "DELETE FROM slider WHERE id=$id";
    mysqli_query($koneksi, $sql);
    header("Location: readslider.php");
    exit;
}

// Ambil data slider
$result = mysqli_query($koneksi, "SELECT * FROM slider ORDER BY id DESC");
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
            <div class="menu-item has-submenu active">
                <span><i class="fas fa-users icon-left"></i>Pengaturan Profil</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="submenu">
                <a href="profilsekolah.php">Profil Madrasah</a>
                <a href="profilkepala.php">Profil Kepala</a>
                <a href="visimisi.php">Visi dan Misi</a>
                <a href="kontak.php">Kontak</a>
                <a href="statistik.php">Statistik</a>
                <a href="readslider.php" class="active">Home Slider</a>
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
            <h1>Pengaturan Slider Header</h1>
            <div class="school-formcr">
                <button class="add-btn" id="openAddModalBtn"><i class="fa fa-plus"></i> Tambah Slider</button>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Gambar</th>
                            <th>Judul</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><img src="<?= htmlspecialchars($row['gambar']) ?>" alt="gambar slider"></td>
                                <td><?= htmlspecialchars($row['judul']) ?></td>
                                <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                                <td>
                                    <button class="edit-btn"
                                        data-id="<?= $row['id'] ?>"
                                        data-judul="<?= htmlspecialchars($row['judul'], ENT_QUOTES) ?>"
                                        data-deskripsi="<?= htmlspecialchars($row['deskripsi'], ENT_QUOTES) ?>"
                                        data-gambar="<?= htmlspecialchars($row['gambar'], ENT_QUOTES) ?>">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <a href="readslider.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus slider ini?')">
                                        <button class="del-btn"><i class="fa fa-trash"></i>
                                        </button>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- Modal Tambah -->
                <div id="addModal" class="modal">
                    <div class="modal-content">
                        <span class="close" id="closeAddModal">&times;</span>
                        <h2>Tambah Slider Baru</h2>
                        <form method="POST" enctype="multipart/form-data">
                            <label>Judul</label>
                            <input type="text" name="judul" required />
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" required></textarea>
                            <label>Gambar</label>
                            <input type="file" name="gambar" accept="image/*" required />
                            <button type="submit" name="tambah_slider">Simpan</button>
                        </form>
                    </div>
                </div>

                <!-- Modal Edit -->
                <div id="editModal" class="modal">
                    <div class="modal-content">
                        <span class="close" id="closeEditModal">&times;</span>
                        <h2>Edit Slider</h2>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" id="edit-id" />
                            <label>Judul</label>
                            <input type="text" name="judul_edit" id="edit-judul" required />
                            <label>Deskripsi</label>
                            <textarea name="deskripsi_edit" id="edit-deskripsi" required></textarea>
                            <label>Gambar (biarkan kosong jika tidak diubah)</label>
                            <input type="file" name="gambar_edit" accept="image/*" />
                            <br />
                            <img id="edit-img-preview" src="" alt="Preview Gambar" style="max-width: 100%; border-radius: 6px; margin-top: 10px;">
                            <button type="submit" name="edit_slider">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
            <script>
                // Modal Add
                const addModal = document.getElementById('addModal');
                const openAddModalBtn = document.getElementById('openAddModalBtn');
                const closeAddModalBtn = document.getElementById('closeAddModal');
                openAddModalBtn.onclick = () => addModal.style.display = 'block';
                closeAddModalBtn.onclick = () => addModal.style.display = 'none';

                // Modal Edit
                const editModal = document.getElementById('editModal');
                const closeEditModalBtn = document.getElementById('closeEditModal');

                const editBtns = document.querySelectorAll('.edit-btn');
                editBtns.forEach(btn => {
                    btn.onclick = () => {
                        editModal.style.display = 'block';
                        document.getElementById('edit-id').value = btn.getAttribute('data-id');
                        document.getElementById('edit-judul').value = btn.getAttribute('data-judul');
                        document.getElementById('edit-deskripsi').value = btn.getAttribute('data-deskripsi');
                        document.getElementById('edit-img-preview').src = btn.getAttribute('data-gambar');
                    }
                });
                closeEditModalBtn.onclick = () => editModal.style.display = 'none';

                // Close modal if outside click
                window.onclick = function(event) {
                    if (event.target == addModal) addModal.style.display = 'none';
                    if (event.target == editModal) editModal.style.display = 'none';
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
    </main>





</body>

</html>