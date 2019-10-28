<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <!-- my CSS -->
    <link rel="stylesheet" href="style.css">

    <!-- fonts Google -->
    <link href="https://fonts.googleapis.com/css?family=Quicksand&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet"> 
		<link href="https://fonts.googleapis.com/css?family=Noto+Sans|Noto+Sans+JP&display=swap" rel="stylesheet">


    <title>Enkripsi & Dekripsi</title>
  </head>
  <body>
  
    
      <form action="index.php" method="post">
      <article class="coba">
        <div class="container">
          <div class="row">
            <div class="col text-center">
              <img src="img/logo.png" alt="logo">
            </div>
          </div>
          <div class="row">
            <div class="col">
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-10 col-12">
										<!-- Pesan  yaang akan di ubah -->
                    <input name="pesan" type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Pesan" autofocus Required="required" autocomplete="off">
                  </div>
                  <div class="col-lg-2 col">
										<!-- tombol penentu -->
                    <button name="submit" type="submit" class="btn">Hasil</button>
                  </div>
                </div>

                <div class="row key">
                  <div class="col-6">
										<!-- Kunci -->
                    <input name="kunci" type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Kunci" Required="required">
                  </div>
                  <div class="col-6">
									<!-- proses E / D -->
                  <select name="proses">
                    <option value="E">Enkripsi</option>
                    <option value="D">Dekripsi</option>
                  </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </article>
      </form>



  <?php 

	if(isset($_POST["submit"])) {

		// memasukan nilai ke variable
		$pesan = $_POST["pesan"];
		$kunci = $_POST["kunci"];
		$proses = $_POST["proses"];

		// membuat objek
		$obj = new KripRC4;

		// mengatur kunci
		$obj->setKunci($kunci);
		
		if($proses == "E") {
			// proeses Enkript
			$obj->EDkripsi($pesan,$proses);

		} else {
			$pesan = $obj->ubahPesan($pesan);
			$obj->pesanASCI(explode(" ",$pesan));

			// proses Dekript
			$obj->EDkripsi($pesan,$proses);
		}
	}


class KripRC4 {
	private $kunci;
	private $S;
	private $K;
	private $H;

	// menyimpan ascii asli untuk proses dekript
	private $pesanAsli;
	public function pesanASCI($n) {
		$this->pesanAsli = $n;
	}

	// menset kunci yang akan digunakan untuk E/D
	public function setKunci($n) {
		$this->kunci = $n;
	}

	//  mendapatkan nilai kunci
	public function getKunci() {
		return $this->kunci;
	}

	// mengubah nilai ascii menjadi karakter
	public function ubahPesan($n) {
		// membagi tiap ascii berdasarkan sepasi " "
		$h = explode(" ",$n);

		$hasil = "";

		for($i = 0 ; $i < count($h) ; $i++) {
			$hasil .= chr($h[$i]);
		}

		// membalikan nilai hasil mengubah ascii menjadi karakter (sudah digabung menjadi string)
		return $hasil;
	}

	// pembuatan arrayS yang berisi $S[0] = 0, $S[1] = 1, dll
	public function iniArrayS() {
		for($i = 0 ; $i < 255 ; $i++) {
			$S[$i] = $i;
		}

		$this->S = $S;
  }
  
	public function iniArrayK() {		
		$key = $this->getKunci();
		$flag = 1;
		for($i = 0 ; $i < 255 ; $i++) {
			$K[$i] = ord($key[$i % strlen($key)]);
		}

		$this->K = $K;
	}

	public function acakSBox() {
		$i = 0 ;
		$j = 0 ;

		$S = $this->S;
		$K = $this->K;
		for($i = 0 ; $i < 255 ; $i++) {
			$j = ($j + $S[$i] + $K[$i]) % 255;
			$n = $S[$i];
			$S[$i] = $S[$j];
			$S[$j] = $n;
		}

		$this->S = $S;
	}

	public function pseudoRandomByte($pesan) {
		$S = $this->S;
		$K = $this->K;

		$i = 0 ; 
		$j = 0 ;

		$Key = array();
		
		for($p = 0 ; $p < strlen($pesan) ; $p++) {

			$i = ($i + 1) % 255;

			$j = ($j + $S[$i]) % 255;

			$n = $S[$i];
			$S[$i] = $S[$j];
			$S[$j] = $n;

			$t = ($S[$i] + $S[$j]) % 255;

			$Key[] =  $S[$t];
		}
		// mendapatkan key hasil pseudoRandomByte
		return $Key;
	}

	// mengubah ascii menjadi karakter
	public function getHasil($n) {
		$arrHasil = array();

		for($i = 0 ; $i < count($n) ; $i++) {
			$arrHasil[$i] = chr($n[$i]);
		}

		return $arrHasil;
	}

	// mengubah karater menjadi binner
	public function ubahBinner($n) {
		$n = decbin($n);

		if(strlen($n) > 8) {
			// bila nilainya lebih dari 8, maka hapus nilai depannya
			$jum = strlen($n) - 8;
			$n = substr($n, $jum, strlen($n));
		} else {
			// bila nilainya kurang dari 8, maka tambah dengan 0 di depannya
			while(strlen($n) % 8 != 0) {
				$n = "0" . $n;
			}	
		}

		// mengembalikan binner dengan jumlah sampai 8 bit
		return $n;
	}

	// menghasilkan hasil dari xor binner karakter dengan key
	public function hasilXorBinner($p,$k) {
		$arrHasil = array();
		for($i = 0 ; $i < strlen($p) ; $i++) {
			if($p[$i] == $k[$i]) {
				$arrHasil[] = "0";
			} else {
				$arrHasil[] = "1";
			}
		}

		// mengubah binner menjadi desimal
		$hasil = bindec(implode($arrHasil));

		// mengembalikan nilai desimal
		return $hasil;
	}

	// proses XOR dengan memasukan pesan, kunci dan status ("E" atau "D")
	public function prosesXOR($pesan,$kunci,$status) {

		$arrPesan = array();
		$arrHasil = array();

		if($status == "E") {
			// Bila enkripsi
			for($i = 0 ; $i < strlen($pesan) ; $i++) {
				$arrPesan[$i] = ord($pesan[$i]);
			}	
		} else {
			// Bila dekript langsung pakai saja nilai yang sudah di simpan di $this->pesanAsli
			$arrPesan = $this->pesanAsli;
		}

		for($i = 0 ; $i < count($arrPesan) ; $i++) {

			// mengubah pesan karakter yang ke $i menjadi binner
			$p = $this->ubahBinner($arrPesan[$i]);

			// mengubah key karakter yang ke $i menjadi binner
			$k = $this->ubahBinner($kunci[$i]);
		
			// melakukan proses xor
			$h = $this->hasilXorBinner($p,$k);

			// hasil di simpan ke array
			$arrHasil[$i] = $h;
		}

		// mengubah ascii menjadi karakter
		$hasil = $this->getHasil($arrHasil);

		// menyimpan dalam bentuk ascii
		$this->H = $arrHasil;
		return $hasil;
	}

	public function cetakHasil($hasil) {
		// untuk yang karakter
		$hasil = implode(" ",$hasil);

		// untuk yang ascii
		$n = implode(" ",$this->H);
    ?>
    
    <article class="hasil">
      <div class="container">
        <div class="row">
          <div class="col">
            <h6>Hasil enkripsi atau  dekripsi dari tulisan yang anda masukkan...</h6>
						<p><?= $hasil ?> </p>
          </div>
        </div>
				<div class="row">
          <div class="col">
            <h6>Cetak ASCII...</h6>
						<p><?= $n ?> </p>
          </div>
        </div>
      </div>
    </div>

		<?php
	}

	public function EDkripsi($n,$status) {

		// memanggil semua fungsi yang ada di kelas
		$this->iniArrayS();
		$this->iniArrayK();
		$this->acakSBox();

		// mendapatkan key hasil pseudoRandomByte
		$key_prb = $this->pseudoRandomByte($n);

		// Proses xor key dan pesan berdasarkan status (E atau D)
		$hasil = $this->prosesXOR($n,$key_prb,$status);

		// Mencetak hasil (baik berupa ascii maupun karakter)
		$this->cetakHasil($hasil);
	}
}
?>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>