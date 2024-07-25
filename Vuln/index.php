<?php
// SQL bağlantısının açılışı
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "emre";

// Veritabanına bağlantı kurma
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol etme
if ($conn->connect_error) {
    die("Veritabanına bağlanılamadı: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ZAFİYET DENEME</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
  <h2>ZAFİYET DENEME EKRANI</h2>
  <form id="testForm" method="post">
    <input type="text" id="input1" name="input1" placeholder="XSS">
    <input type="text" id="input2" name="input2" placeholder="OS COMMAND (geçerli bir IP adresi)">
    <input type="text" id="input3" name="input3" placeholder="SQL INJECTION (kayıtlı bir personel adı girin)">
    <button type="submit" name="submit">GÖNDER</button>
  </form>

  <div class="result" id="result">
    <?php
    if (isset($_POST['submit'])) {
        // Input alanlarını sırasıyla al
        $input1 = !empty($_POST['input1']) ? $_POST['input1'] : null;
        $input2 = !empty($_POST['input2']) ? $_POST['input2'] : null;
        $input3 = !empty($_POST['input3']) ? $_POST['input3'] : null;

        // OS Command Injection denemesi için ikinci input alanı
        if ($input2 !== null) {
            // Ping işlemi için
            $pingResult = shell_exec("ping -c 4 " . escapeshellarg($input2));
            if ($pingResult !== null) {
                echo "OS COMMAND (Ping Sonuçları): <br><pre>" . htmlspecialchars($pingResult) . "</pre>";
            } else {
                echo "Ping komutu çalıştırılamadı veya çıktı üretmedi.";
            }
        }

        // XSS için ilk input alanı (doğrudan kullanıcı girişi)
        if ($input1 !== null) {
            // XSS testi için HTML karakterlerini kaçırmadan doğrudan ekleme
            echo "Input 1 (XSS): " . $input1; // HTML karakterlerini kaçırmadan
        }

        // SQL Injection denemesi için üçüncü input alanı
        if ($input3 !== null) {
            // SQL Injection riski için doğrudan kullanıcı girdisini sorguya dahil etme
            $query = "SELECT * FROM emreninPersonelleri WHERE Ad = '$input3'";
            $result = $conn->query($query);

            if ($result) {
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "ID: " . htmlspecialchars($row["id"]) . "<br><br>" .
                             "Ad: " . htmlspecialchars($row["Ad"]) . "<br><br>" .
                             "Soyad: " . htmlspecialchars($row["Soyad"]) . "<br><br>" .
                             "Eposta: " . htmlspecialchars($row["Eposta"]) . "<br><br>" .
                             "Sifre: " . htmlspecialchars($row["sifre"]) . "<br><br>";
                    }
                } else {
                    echo "Bu isimde bir personel bulunamadı.";
                }
            } else {
                echo "Sorgu hatası: " . $conn->error;
            }
        }
    }
    ?>
  </div>
</div>
</body>
</html>

<?php
$conn->close(); // SQL bağlantısının kapanışı
?>
