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
    <input type="text" id="input2" name="input2" placeholder="OS COMMAND">
    <input type="text" id="input3" name="input3" placeholder="SQL INJECTION">
    <button type="submit" name="submit">GÖNDER</button>
  </form>

  <div class="result" id="result">
    <?php
    if (isset($_POST['submit'])) {
        // Input alanlarını sırasıyla al
        $input1 = !empty($_POST['input1']) ? $_POST['input1'] : null;
        $input2 = !empty($_POST['input2']) ? $_POST['input2'] : null;
        $input3 = !empty($_POST['input3']) ? $_POST['input3'] : null;

        // Sonuçları saklamak için bir dizi oluştur
        $results = [];

        // OS Command Injection denemesi için ikinci input alanı
        if ($input2 !== null) {
            // Komut kalıbına uyan bir girdi olup olmadığını kontrol et
            if (preg_match('/^\s*(ls|ping|whoami|dir|ifconfig|ipconfig|pwd|uname)\b/', $input2)) {
                $output = shell_exec($input2 . " 2>&1");
                if ($output !== null) {
                    $results[] = "OS Command Injection sonucu: <pre>" . htmlspecialchars($output, ENT_QUOTES, 'UTF-8') . "</pre>";
                } else {
                    $results[] = "Komut çalıştırılamadı veya çıktı üretmedi.";
                }
            } else {
                $results[] = "OS Command Input: " . htmlspecialchars($input2, ENT_QUOTES, 'UTF-8');
            }
        }

        // XSS için ilk input alanı (doğrudan kullanıcı girişi)
        if ($input1 !== null) {
            $results[] = "Input 1 (XSS): " . $input1;
        }

        // SQL Injection denemesi için üçüncü input alanı
        if ($input3 !== null) {
            try {
                // Veritabanı bağlantısını oluştur
                $db = new SQLite3(':memory:');

                // Tabloyu oluştur
                $db->exec("CREATE TABLE users (
                    id INTEGER PRIMARY KEY,
                    username TEXT,
                    password TEXT,
                    kimlikno TEXT
                )");

                // Örnek verileri ekle
                $db->exec("INSERT INTO users (username, password, kimlikno) VALUES ('ali', 'ali123', '987645342')");
                $db->exec("INSERT INTO users (username, password, kimlikno) VALUES ('veli', 'veliveli', '123909393')");
                $db->exec("INSERT INTO users (username, password, kimlikno) VALUES ('root', 'root123', '234567898')");

                // SQL Injection denemesi
                $query = "SELECT * FROM users WHERE username = '$input3'";
                $result = $db->query($query);

                // Sonuçları kontrol et
                if ($result) {
                    $found = false;
                    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                        $found = true;
                        break; // İlk kullanıcıyı bulduktan sonra döngüden çık
                    }
                    if ($found) {
                        $results[] = "Doğrulama başarılı.";
                    } else {
                        $results[] = "Kayıt bulunamadı.";
                    }
                } else {
                    $results[] = "Kayıt bulunamadı.";
                }

                $db->close();
            } catch (Exception $e) {
                $results[] = "Error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
            }
        }

        // Sonuçları ekranda göster
        if (!empty($results)) {
            echo implode('<br>', $results);
        }
    }
    ?>
  </div>
</div>
</body>
</html>
