<?php
include './include/koneksi.php';

$stmt1 = $koneksi->prepare("SELECT * FROM profil_sekolah LIMIT 1");
$stmt1->execute();
$result1 = $stmt1->get_result();
$profil = $result1->fetch_assoc();

$stmt2 = $koneksi->prepare("SELECT * FROM profil_kamad LIMIT 1");
$stmt2->execute();
$result2 = $stmt2->get_result();
$profil2 = $result2->fetch_assoc();

$stmt3 = $koneksi->prepare("SELECT * FROM kontak LIMIT 1");
$stmt3->execute();
$result3 = $stmt3->get_result();
$kontak = $result3->fetch_assoc();

$query = "SELECT * FROM slider ORDER BY id ASC";
$result4 = mysqli_query($koneksi, $query);

$stmt5 = $koneksi->prepare("SELECT * FROM statistik_madrasah LIMIT 1");
$stmt5->execute();
$result5 = $stmt5->get_result();
$stats = $result5->fetch_assoc();

$result6 = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal_posting DESC LIMIT 4");
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>MAN 1 Pontianak</title>
    <link rel="stylesheet" href="css/style.css" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        crossorigin="anonymous" />
</head>

<body>
    <header>


    </header>
    <nav class="navbar">
        <div class="logo">
            <div class="logo">
                <?php if (!empty($profil['logo']) && file_exists('./adminpanel/uploads/' . $profil['logo'])): ?>
                    <img src="adminpanel/uploads/<?php echo htmlspecialchars($profil['logo']); ?>" alt="Logo Sekolah" style="max-height: 100px;" />
                <?php else: ?>
                    <img src="adminpanel/uploads/1751705992_logo.png" alt="Logo Default" style="max-height: 100px;" />
                <?php endif; ?>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="#">BERANDA</a></li>
            <li><a href="#">PROFIL</a></li>
            <li><a href="#">BERITA</a></li>
            <li><a href="#">KONTAK</a></li>
        </ul>
    </nav>
    <div class="slider-container">
        <?php
        $first = true;
        while ($row = mysqli_fetch_assoc($result4)) {
        ?>
            <div
                class="slider <?php echo $first ? 'active' : ''; ?>"
                style="background-image: url('adminpanel/<?php echo htmlspecialchars($row['gambar']); ?>');">
                <div class="slider-text">
                    <h2><?php echo htmlspecialchars($row['judul']); ?></h2>
                    <p><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                </div>
            </div>
        <?php
            $first = false;
        }
        ?>
    </div>

    <section class="content">
        <h2 class="section-title">Statistik Madrasah</h2>
        <div class="stats">
            <div class="stat-item">
                <i class="fas fa-user"></i>
                <p class="angka-stats"><strong><?php echo htmlspecialchars($stats['jml_siswa'] ?? 'Belum ada data'); ?></strong>
                <p class="title-stats">Siswa</p>
            </div>
            <div class="stat-item">
                <i class="fas fa-graduation-cap"></i>
                <p class="angka-stats"><strong>± <?php echo htmlspecialchars($stats['jml_alumni'] ?? 'Belum ada data'); ?></strong>
                <p class="title-stats">Alumni</p>
            </div>
            <div class="stat-item">
                <i class="fas fa-chalkboard-teacher"></i>
                <p class="angka-stats"><strong><?php echo htmlspecialchars($stats['jml_guru'] ?? 'Belum ada data'); ?></strong>
                <p class="title-stats">Guru</p>
            </div>
            <div class="stat-item">
                <i class="fas fa-briefcase"></i>
                <p class="angka-stats"><strong><?php echo htmlspecialchars($stats['jml_tu'] ?? 'Belum ada data'); ?></strong>
                <p class="title-stats">Tata Usaha</p>
            </div>
            <div class="stat-item">
                <i class="fas fa-school"></i>
                <p class="angka-stats"><strong><?php echo htmlspecialchars($stats['jml_kelas'] ?? 'Belum ada data'); ?></strong>
                <p class="title-stats">Kelas</p>
            </div>
    </section>

    <section class="message-principal">
        <h2 class="section-title">Sambutan Kepala Madrasah</h2>
        <div class="principal-container">
            <div class="principal-photo">
                <?php if (!empty($profil2['foto']) && file_exists('adminpanel/uploads/' . $profil2['foto'])): ?>
                    <img src="adminpanel/uploads/<?php echo htmlspecialchars($profil2['foto']); ?>" alt="Kepala Madrasah" />
                <?php else: ?>
                    <div class="no-photo">Belum ada data foto</div>
                <?php endif; ?>
                <div class="principal-name">
                    <?= htmlspecialchars($profil2['nama_kepala'] ?? 'Belum ada data'); ?><br />
                    <small>Kepala Madrasah</small>
                </div>
            </div>
            <div class="principal-text">
                <p><em>Assalamu’alaikum Warohmatullahi Wabarokatuh</em></p>
                <p><?= nl2br(htmlspecialchars($profil2['kata_sambutan'] ?? 'Belum ada data')); ?></p>
                <p><em>Wassalamu’alaikum Warohmatullahi Wabarakatuh</em></p>
            </div>
        </div>
    </section>


    <div class="container-berita">
        <h2 class="section-title">Berita Terbaru</h2>

        <section class="news-list" aria-label="Daftar berita terbaru">
            <?php while ($row = mysqli_fetch_assoc($result6)) : ?>
                <article class="news-item" tabindex="0" aria-label="Berita: <?php echo htmlspecialchars($row['judul_berita']); ?>">
                    <?php if ($row['gambar_unggulan']) : ?>
                        <img src="adminpanel/uploads/<?php echo htmlspecialchars($row['gambar_unggulan']); ?>" alt="<?php echo htmlspecialchars($row['judul_berita']); ?>" class="news-image" />
                    <?php else : ?>
                        <img src="https://via.placeholder.com/300x160?text=No+Image" alt="No Image" class="news-image" />
                    <?php endif; ?>
                    <div class="news-content">
                        <a href="detail_berita.php?id=<?php echo $row['id']; ?>" class="news-title">
                            <?php echo htmlspecialchars($row['judul_berita']); ?>
                        </a>

                        <div class="news-date"><?php echo date('d M Y', strtotime($row['tanggal_posting'])); ?></div>
                    </div>
                </article>
            <?php endwhile; ?>
        </section>

        <section class="see-all-section" aria-label="Tombol untuk melihat semua berita">
            <a href="berita.php" class="btn-see-all">Lihat Semua Berita</a>
        </section>
    </div>


    <section class="content info-madrasah">
        <h2>Informasi Madrasah</h2>
        <div class="info-container">
            <div class="info-box pengumuman">
                <h3><i class="fas fa-bullhorn"></i> Pengumuman</h3>
                <ul>
                    <li><i class="fas fa-chevron-circle-right"></i> Penerimaan Peserta Didik Baru (PPDB) TA. 2023/2024</li>
                    <li><i class="fas fa-chevron-circle-right"></i> Jadwal Libur & Cuti Bersama Hari Raya Idul Fitri 1444 H</li>
                    <li><i class="fas fa-chevron-circle-right"></i> Jadwal Ujian Akhir Sekolah Th. 2023</li>
                    <li><i class="fas fa-chevron-circle-right"></i> Kerja bakti di lingkungan Madrasah</li>
                </ul>
            </div>
            <div class="info-box agenda">
                <h3><i class="fas fa-calendar-alt"></i> Agenda</h3>
                <ul>
                    <li><i class="fas fa-chevron-circle-right"></i> Rapat Rutin Guru & Staff TU Bulan November 2023</li>
                    <li><i class="fas fa-chevron-circle-right"></i> Upacara Peringatan Hari Santri Nasional</li>
                    <li><i class="fas fa-chevron-circle-right"></i> Upacara Peringatan Hari Kemerdekaan Indonesia</li>
                    <li><i class="fas fa-chevron-circle-right"></i> Donor Darah Gratis yg diselenggarakan PUSKESMAS Kom Yos Sudarso</li>
                </ul>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-container">
            <div>
                <?php if (!empty($profil['logo']) && file_exists('adminpanel/uploads/' . $profil['logo'])): ?>
                    <img src="adminpanel/uploads/<?php echo htmlspecialchars($profil['logo']); ?>" alt="Logo Sekolah" style="max-height: 300px;" class="footer-logo" />
                <?php else: ?>
                    <img src="assets/images/icon/logo.png" alt="Logo Default" style="max-height: 100px;" />
                <?php endif; ?>
                <div class="footer-title">MAN 1 PONTIANAK</div>
                <div class="footer-subtitle">"Madrasah hebat, bermartabat"</div>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-youtube"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                </div>
            </div>
            <div>
                <h3>Link Internal</h3>
                <ul>
                    <li><i class="fas fa-chevron-circle-right"></i> <a href="#">PPDB Online</a></li>
                    <li><i class="fas fa-chevron-circle-right"></i> <a href="#">E-Learning</a></li>
                    <li><i class="fas fa-chevron-circle-right"></i> <a href="#">Raport Digital Madrasah</a></li>
                </ul>
            </div>
            <div>
                <h3>Hubungi Kami</h3>
                <ul>
                    <li><i class="fas fa-map-marker-alt"></i><?= htmlspecialchars($kontak['alamat'] ?? 'Belum ada data'); ?></li>
                    <li><i class="fas fa-phone"></i><?= htmlspecialchars($kontak['telepon'] ?? 'Belum ada data'); ?></li>
                    <li><i class="fas fa-envelope"></i><?= htmlspecialchars($kontak['surel'] ?? 'Belum ada data'); ?></li>
                    <li><i class="fas fa-clock"></i><?= htmlspecialchars($kontak['jam_pelayanan'] ?? 'Belum ada data'); ?></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">Copyright 2023 MANSA</div>
    </footer>

    <script>
        const slides = document.querySelectorAll('.slider');
        let index = 0;

        function showSlide(i) {
            slides.forEach((slide, idx) => {
                if (idx === i) {
                    slide.style.opacity = 1;
                    slide.style.zIndex = 2;
                    slide.classList.add('active');
                } else {
                    slide.style.opacity = 0;
                    slide.style.zIndex = 1;
                    slide.classList.remove('active');
                }
            });
        }

        showSlide(index);

        setInterval(() => {
            index++;
            if (index >= slides.length) index = 0;
            showSlide(index);
        }, 6000);
    </script>
</body>

</html>