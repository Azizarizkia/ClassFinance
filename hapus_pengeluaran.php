<?php 
	require 'connection.php';
	$id_pengeluaran = $_GET['id_pengeluaran'];
	if (isset($id_pengeluaran)) {
		if (deletePengeluaran($id_pengeluaran) > 0) {
			setAlert("Pengeluaran Telah dihapus", "Berhasil dihapus", "Berhasil");
		    header("Location: pengeluaran.php");
	    }
	} else {
	   header("Location: pengeluaran.php");
	}