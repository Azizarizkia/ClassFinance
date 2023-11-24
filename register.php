<?php
require 'connection.php';
checkLoginAtLogin();

if (isset($_POST['btnRegist'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = ($_POST['password']);
    $jabatan = $_POST['jabatan'];

    $password=password_hash($password, PASSWORD_DEFAULT);

    $checkNama = mysqli_query($conn, "SELECT * FROM user WHERE nama_lengkap = '$nama'");

    if (mysqli_num_rows($checkNama) > 0) {
        setAlert($nama, "Sudah memiliki akun", "Pergi ke halaman login");
        header("Location: login.php");
    } else {
        $id_jabatan = ($jabatan == "bendahara") ? 2 : 3; // Jika jabatan bendahara, id_jabatan = 2, jika siswa, id_jabatan = 3

        $insertUser = mysqli_query($conn, "INSERT INTO user (nama_lengkap, username, password, id_jabatan) VALUES ('$nama', '$username', '$password', $id_jabatan)");

        if ($insertUser) {
            setAlert("Berhasil Registrasi!", "Anda bisa login sekarang.", "Berhasil");
            header("Location: login.php");
        } else {
            setAlert("Gagal Registrasi!", "Coba Lagi.", "error");
            header("Location: register.php");
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
  <?php include 'include/css.php'; ?>
    <title>Login</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
  
      body {
        min-height: 100vh;
        background-size: cover;
        background-repeat: no-repeat;
        background-image: url(assets/img/img_properties/bg.jpg);
    }
    
      .container {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -55%);
    }
  </style>
  </head>
  <body>
    <div class="">
	    <img style="width:250px;margin-right:30px;"src="assets/img/img_properties/logo.png"align="right"/>
    </div>
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-4 mx-5 py-4 px-5 text-dark rounded border border-dark" style="background-color: rgba(180,190,196,.6);">
          <h3 class="text-center"style="color:white;">ClassFinance</h3>
          <h5 class="text-center"style="color:white;">Registrasi</h5>
          <form method="post" autocomplete="off">
            <div class="form-group">
              <label for="nama lengkap"style="color:white;"><i class="fas fa-fw fa-user"style="color:white;"></i> Nama lengkap</label>
              <input required class="form-control rounded-pill" type="text" name="nama" id="nama">
            </div>
            <div class="form-group">
              <label for="username"style="color:white;"><i class="fas fa-fw fa-user"style="color:white;"></i> Username</label>
              <input required class="form-control rounded-pill" type="text" name="username" id="username">
            </div>
            <div class="form-group">
              <label for="password"style="color:white;"><i class="fas fa-fw fa-lock"style="color:white;"></i> Password</label>
              <input required class="form-control rounded-pill" type="password" name="password" id="password">
            </div>
            <div class="form-group">
            <label for="peran"style="color:white;">Jabatan :</label>
              <select id="jabatan" name="jabatan" style="background:none; color:white;">
                  <option value="bendahara"style="color:black;">Bendahara</option>
                  <option value="siswa"style="color:black;">Siswa</option>
              </select>
            </div>
            <div class="form-group text-right">
            <button class="btn btn-success rounded-pill" name="btnRegister"><i class="fas fa-fw fa-sign-in-alt"></i><a href="/uangAziza/ClassFinance/login.php" style="color:white;"> login</a> </button>
              <button class="btn btn-success rounded-pill" type="submit" name="btnRegist"><i class="fas fa-fw fa-sign-in-alt"></i> Register</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  
    <footer style="position: absolute; bottom: 0; width: 100%; text-align: center;">
      <div style="background-color: transparent;" class="container-fluid mt-5">
        <div class="row justify-content-center">
          <div class="col-lg text-center text-white pt-4 pb-2">
            <p>&copy; Copyright 2023. By Aziza Rizkia Rahmashani. All Right Reserved.</p>
          </div>
        </div>
      </div>
    </footer>
  </body>
</html>