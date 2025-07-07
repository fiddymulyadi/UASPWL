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

$upload_dir = 'uploads/';

// Handle Create (Tambah)
if (isset($_POST['create'])) {
    $judul = $_POST['judul_pengumuman'] ?? '';
    $deskripsi = $_POST['deskripsi_pengumuman'] ?? '';
    $tanggal = $_POST['tanggal_posting'] ?? '';
    $lampiran = null;

    if (isset($_FILES['lampiran_file']) && $_FILES['lampiran_file']['error'] == 0) {
        $file_name = basename($_FILES['lampiran_file']['name']);
        $file_tmp = $_FILES['lampiran_file']['tmp_name'];
        $target_file = $upload_dir . $file_name;
        if (move_uploaded_file($file_tmp, $target_file)) {
            $lampiran = $file_name;
        }
    }

    $stmt = $koneksi->prepare("INSERT INTO pengumuman (judul_pengumuman, deskripsi_pengumuman, lampiran_file, tanggal_posting) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $judul, $deskripsi, $lampiran, $tanggal);
    $stmt->execute();
    $stmt->close();

    $_SESSION['message'] = "Data berhasil ditambahkan";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle Update (Edit)
if (isset($_POST['update'])) {
    $id = $_POST['id'] ?? 0;
    $judul = $_POST['judul_pengumuman'] ?? '';
    $deskripsi = $_POST['deskripsi_pengumuman'] ?? '';
    $tanggal = $_POST['tanggal_posting'] ?? '';
    $lampiran = $_POST['lampiran_old'] ?? null;

    if (isset($_FILES['lampiran_file']) && $_FILES['lampiran_file']['error'] == 0) {
        if ($lampiran && file_exists($upload_dir . $lampiran)) {
            unlink($upload_dir . $lampiran);
        }
        $file_name = basename($_FILES['lampiran_file']['name']);
        $file_tmp = $_FILES['lampiran_file']['tmp_name'];
        $target_file = $upload_dir . $file_name;
        if (move_uploaded_file($file_tmp, $target_file)) {
            $lampiran = $file_name;
        }
    }

    $stmt = $koneksi->prepare("UPDATE pengumuman SET judul_pengumuman=?, deskripsi_pengumuman=?, lampiran_file=?, tanggal_posting=? WHERE id=?");
    $stmt->bind_param("ssssi", $judul, $deskripsi, $lampiran, $tanggal, $id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['message'] = "Data berhasil diperbarui";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($koneksi, "DELETE FROM pengumuman WHERE id=$id");
    header("Location: editorpengumuman.php");
    exit;
}

// Ambil data pengumuman
$result = $koneksi->query("SELECT * FROM pengumuman ORDER BY tanggal_posting DESC");

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
                <a href="editorberita.php">Berita/Artikel</a>
                <a href="editorpengumuman.php" class="active">Pengumuman</a>
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
        </div>
        <div class="school-formcr">
            <button id="btnTambah" class="add-btn"><i class="fas fa-plus"></i> Tambah</button>
            <table class="table-pengumuman">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul Pengumuman</th>
                        <th>Deskripsi</th>
                        <th>Lampiran</th>
                        <th>Tgl Posting</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr data-id="<?php echo $row['id']; ?>"
                            data-judul="<?php echo htmlspecialchars($row['judul_pengumuman'], ENT_QUOTES); ?>"
                            data-deskripsi="<?php echo htmlspecialchars($row['deskripsi_pengumuman'], ENT_QUOTES); ?>"
                            data-lampiran="<?php echo htmlspecialchars($row['lampiran_file'], ENT_QUOTES); ?>"
                            data-tanggal="<?php echo htmlspecialchars($row['tanggal_posting'], ENT_QUOTES); ?>">
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['judul_pengumuman']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($row['deskripsi_pengumuman'])); ?></td>
                            <td>
                                <?php if (!empty($row['lampiran_file'])): ?>
                                    <a href="uploads/<?php echo urlencode($row['lampiran_file']); ?>" target="_blank">Lihat File</a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['tanggal_posting']); ?></td>
                            <td>
                                <button class="action-btn edit-btn" title="Edit" onclick="openEditModal(this)">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <a href="?delete=<?= $row['id'] ?>" class="action-btn" title="Hapus"
                                    onclick="return confirm('Yakin ingin menghapus Pengumuman ini?')">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Modal Tambah -->
            <div id="modalTambah" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal('modalTambah')">&times;</span>
                    <h2>Tambah Pengumuman</h2>
                    <form method="POST" enctype="multipart/form-data" action="">
                        <label>Judul Pengumuman</label><br />
                        <input type="text" name="judul_pengumuman" required><br />

                        <label>Deskripsi</label><br />
                        <textarea name="deskripsi_pengumuman" required></textarea><br />

                        <label>Tanggal Posting</label><br />
                        <input type="date" name="tanggal_posting" required><br />

                        <label>Lampiran File</label><br />
                        <input type="file" name="lampiran_file" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif"><br /><br />

                        <button type="submit" name="create">Tambah</button>
                    </form>
                </div>
            </div>

            <!-- Modal Edit -->
            <div id="modalEdit" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal('modalEdit')">&times;</span>
                    <h2>Edit Pengumuman</h2>
                    <form method="POST" enctype="multipart/form-data" action="">
                        <input type="hidden" name="id" id="edit_id">

                        <label>Judul Pengumuman</label><br />
                        <input type="text" name="judul_pengumuman" id="edit_judul" required><br />

                        <label>Deskripsi</label><br />
                        <textarea name="deskripsi_pengumuman" id="edit_deskripsi" required></textarea><br />

                        <label>Tanggal Posting</label><br />
                        <input type="date" name="tanggal_posting" id="edit_tanggal" required><br />

                        <label>Lampiran File (Kosongkan jika tidak ingin mengganti)</label><br />
                        <input type="file" name="lampiran_file" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif"><br />
                        <small>File lama: <span id="edit_lampiran_old_display">-</span></small>
                        <input type="hidden" name="lampiran_old" id="edit_lampiran_old">

                        <br />
                        <button type="submit" name="update">Simpan</button>
                    </form>
                </div>
            </div>

            <!-- Modal Delete -->
            <div id="modalDelete" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal('modalDelete')">&times;</span>
                    <h2>Hapus Pengumuman</h2>
                    <p>Apakah Anda yakin ingin menghapus pengumuman ini?</p>
                    <form method="POST" action="">
                        <input type="hidden" name="id_delete" id="delete_id">
                        <button type="submit" name="delete">Ya, Hapus</button>
                        <button type="button" onclick="closeModal('modalDelete')">Batal</button>
                    </form>
                </div>
            </div>

    </main>

    <script>
        // Fungsi buka tutup modal
        function openModal(id) {
            document.getElementById(id).style.display = 'block';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        // Tombol Tambah Pengumuman
        document.getElementById('btnTambah').addEventListener('click', function() {
            openModal('modalTambah');
        });

        // Fungsi untuk membuka modal Edit dan mengisi data
        function openEditModal(btn) {
            const tr = btn.closest('tr');
            const id = tr.getAttribute('data-id');
            const judul = tr.getAttribute('data-judul');
            const deskripsi = tr.getAttribute('data-deskripsi');
            const lampiran = tr.getAttribute('data-lampiran');
            const tanggal = tr.getAttribute('data-tanggal');

            document.getElementById('edit_id').value = id;
            document.getElementById('edit_judul').value = judul;
            document.getElementById('edit_deskripsi').value = deskripsi;
            document.getElementById('edit_tanggal').value = tanggal;
            document.getElementById('edit_lampiran_old').value = lampiran;
            document.getElementById('edit_lampiran_old_display').textContent = lampiran ? lampiran : '-';

            openModal('modalEdit');
        }

        // Fungsi untuk membuka modal Hapus dan mengisi data id
        function openDeleteModal(btn) {
            const tr = btn.closest('tr');
            const id = tr.getAttribute('data-id');
            document.getElementById('delete_id').value = id;
            openModal('modalDelete');
        }

        // Close modal saat klik di luar modal-content
        window.onclick = function(event) {
            const modals = ['modalTambah', 'modalEdit', 'modalDelete'];
            modals.forEach(id => {
                let modal = document.getElementById(id);
                if (event.target === modal) {
                    modal.style.display = "none";
                }
            });
        }
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

</body>

</html>