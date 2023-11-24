<?php 
  require 'connection.php';
  checkLogin();

  $pengeluaran_query = "SELECT * FROM pengeluaran INNER JOIN user ON pengeluaran.id_user = user.id_user";
  $pengeluaran_result = mysqli_query($conn, $pengeluaran_query);
  
  if (!$pengeluaran_result) {
      die("Error in pengeluaran query: " . mysqli_error($conn));
  }

  $jml_pengeluaran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT sum(jumlah_pengeluaran) as jml_pengeluaran FROM pengeluaran"));
  $jml_pengeluaran = $jml_pengeluaran['jml_pengeluaran'];
  
  $jml_uang_kas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT sum(minggu_ke_1 + minggu_ke_2 + minggu_ke_3 + minggu_ke_4) as jml_uang_kas FROM uang_kas"));
  $jml_uang_kas = $jml_uang_kas['jml_uang_kas'];
  
  $jml_uang_kas = is_numeric($jml_uang_kas) ? $jml_uang_kas : 0;
  
  if (isset($_POST['btnAddPengeluaran'])) {
    $pengeluaran = isset($_POST['jumlah_pengeluaran']) ? $_POST['jumlah_pengeluaran'] : 0;

    $pengeluaran = is_numeric($pengeluaran) ? $pengeluaran : 0;

    $sisa = $jml_uang_kas - $jml_pengeluaran - $pengeluaran;
    echo "$sisa";
      if ($sisa < 0) {
          setAlert("Gagal Menambah Pengeluaran, Jumlah pengeluaran lebih besar dari jumlah uang", "Gagal", "Gagal");
          header("Location: pengeluaran.php");
          exit();
      } else {
          if (addPengeluaran($_POST) > 0) {
              setAlert("Pengeluaran telah ditambahkan", "Berhasil ditambahkan", "Berhasil");
              header("Location: pengeluaran.php");
              exit();
          }
      }
  }
  
  if (isset($_POST['btnEditPengeluaran'])) {
      if (editPengeluaran($_POST) > 0) {
          setAlert("Pengeluaran telah diubah", "Berhasil diubah", "Berhasil");
          header("Location: pengeluaran.php");
          exit();
      }
  }   
      // setAlert("Jumlah pengeluaran lebih besar dari jumlah uang", "Gagal menambahkan", "Gagal");
?>

<?php if (!empty($_SESSION['alert'])): ?>
  <div class="alert alert-<?php echo $_SESSION['alert']['type']; ?> alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-ban"></i> <?php echo $_SESSION['alert']['title']; ?></h5>
        <?php echo $_SESSION['alert']['message']; ?>
      </div>
      <?php unset($_SESSION['alert']); ?>
<?php endif; ?>

<!DOCTYPE html>
<html>
<head>
  <?php include 'include/css.php'; ?>
  <title>Pengeluaran</title>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  
  <?php include 'include/navbar.php'; ?>

  <?php include 'include/sidebar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm">
            <h1 class="m-0 text-dark">Pengeluaran</h1>
          </div><!-- /.col -->
          <div class="col-sm text-right">
            <?php if ($_SESSION['id_jabatan'] !== '3'): ?>
              <button class="btn btn-primary" data-toggle="modal" data-target="#tambahPengeluaranModal"><i class="fas fa-fw fa-plus"></i> Tambah Pengeluaran</button>
              <!-- Modal -->
              <div class="modal fade text-left" id="tambahPengeluaranModal" tabindex="-1" role="dialog" aria-labelledby="tambahPengeluaranModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <form method="post">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="tambahPengeluaranModalLabel">Tambah Pengeluaran</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <div class="form-group">
                          <label for="jumlah_pengeluaran">Jumlah Pengeluaran</label>
                          <input type="number" name="jumlah_pengeluaran" id="jumlah_pengeluaran" required class="form-control" placeholder="Rp.">
                        </div>
                        <div class="form-group">
                          <label for="keterangan">Keterangan</label>
                          <textarea name="keterangan" id="keterangan" required class="form-control"></textarea>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-fw fa-times"></i> Close</button>
                        <button type="submit" name="btnAddPengeluaran" class="btn btn-primary"><i class="fas fa-fw fa-save"></i> Save</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            <?php endif ?>
          </div>
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg">
            <div class="table-responsive">
              <table class="table table-bordered table-hover table-striped" id="table_id">
                <thead>
                  <tr>
                    <th>No.</th>
                    <th>Tanggal Pengeluaran</th>
                    <th>Username</th>
                    <th>Jumlah Pengeluaran</th>
                    <th>Keterangan</th>
                    <?php if ($_SESSION['id_jabatan'] !== '3'): ?>
                      <th>Aksi</th>
                    <?php endif ?>
                  </tr>
                </thead>
                <tbody>
                  <?php $i = 1; ?>
                  <?php foreach ($pengeluaran_result as $dp): ?>
                    <tr>
                      <td><?= $i++; ?></td>
                      <td><?= date("d-m-Y, H:i:s", $dp['tanggal_pengeluaran']); ?></td>
                      <td><?= $dp['username']; ?></td>
                      <td>Rp. <?= number_format($dp['jumlah_pengeluaran']); ?></td>
                      <td><?= $dp['keterangan']; ?></td>
                      <?php if ($_SESSION['id_jabatan'] !== '3'): ?>
                        <td>
                          <a href="" class="badge badge-success" data-toggle="modal" data-target="#editPengeluaranModal<?= $dp['id_pengeluaran']; ?>"><i class="fas fa-fw fa-edit"></i> Ubah</a>
                          <div class="modal fade text-left" id="editPengeluaranModal<?= $dp['id_pengeluaran']; ?>" tabindex="-1" role="dialog" aria-labelledby="editPengeluaranModalLabel<?= $dp['id_pengeluaran']; ?>" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                              <form method="post">
                                <input type="hidden" name="id_pengeluaran" value="<?= $dp['id_pengeluaran']; ?>">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="editPengeluaranModalLabel<?= $dp['id_pengeluaran']; ?>">Ubah Pengeluaran</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <div class="modal-body">
                                    <div class="form-group">
                                      <label for="jumlah_pengeluaran<?= $dp['id_pengeluaran']; ?>">Jumlah Pengeluaran</label>
                                      <input type="number" name="jumlah_pengeluaran" id="jumlah_pengeluaran<?= $dp['id_pengeluaran']; ?>" required class="form-control" placeholder="Rp." value="<?= $dp['jumlah_pengeluaran']; ?>">
                                    </div>
                                    <div class="form-group">
                                      <label for="keterangan<?= $dp['id_pengeluaran']; ?>">Keterangan</label>
                                      <textarea name="keterangan" id="keterangan<?= $dp['id_pengeluaran']; ?>" required class="form-control"><?= $dp['keterangan']; ?></textarea>
                                    </div>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-fw fa-times"></i> Close</button>
                                    <button type="submit" name="btnEditPengeluaran" class="btn btn-primary"><i class="fas fa-fw fa-save"></i> Save</button>
                                  </div>
                                </div>
                              </form>
                            </div>
                          </div>
                          <?php if ($_SESSION['id_jabatan'] == '1'): ?>
                            <a href="hapus_pengeluaran.php?id_pengeluaran=<?= $dp['id_pengeluaran']; ?>" class="badge badge-danger btn-delete" data-nama="Pengeluaran : Rp. <?= number_format($dp['jumlah_pengeluaran']); ?> | <?= $dp['keterangan']; ?>"><i class="fas fa-fw fa-trash"></i> Hapus</a>
                          <?php endif ?>
                        </td>
                      <?php endif ?>
                    </tr>
                  <?php endforeach ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <strong>Copyright &copy; 2023 By Aziza Rizkia Rahmashani.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
    </div>
  </footer>

</div>
</body>
</html>
