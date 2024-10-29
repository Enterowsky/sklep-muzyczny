<?php
// Ustawienia bazy danych
$host = 'localhost';
$dbname = 'sklep_muzyczny';
$username = 'root'; // Zmień na swoją nazwę użytkownika MySQL
$password = ''; // Zmień na swoje hasło MySQL

try {
    // Połączenie z bazą danych
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Odbierz dane z formularza
    $imie = $_POST['imie'];
    $email = $_POST['email'];
    $telefon = $_POST['telefon'];
    $adres = $_POST['adres'];
    $produkty = json_decode($_POST['produkty'], true);

    // Wstaw dane zamówienia do tabeli `zamowienia`
    $stmt = $pdo->prepare("INSERT INTO zamowienia (imie, email, telefon, adres) VALUES (?, ?, ?, ?)");
    $stmt->execute([$imie, $email, $telefon, $adres]);

    // Pobierz ID nowego zamówienia
    $zamowienie_id = $pdo->lastInsertId();

    // Wstaw produkty do tabeli `produkty_zamowienia`
    $stmt_produkt = $pdo->prepare("INSERT INTO produkty_zamowienia (zamowienie_id, nazwa_produktu, cena) VALUES (?, ?, ?)");

    foreach ($produkty as $produkt) {
        $stmt_produkt->execute([$zamowienie_id, $produkt['name'], $produkt['price']]);
    }

    // Pokaż potwierdzenie zamówienia
    echo "<h2>Dziękujemy za zakupy, $imie!</h2>";
    echo "<p>Zamówienie zostało złożone. Szczegóły wysłaliśmy na adres e-mail: $email.</p>";
    echo "<p><a href='main.html'>Wróć do strony głównej</a></p>";

    // Opcjonalnie: Możesz wyczyścić koszyk po złożeniu zamówienia
    echo "<script>localStorage.clear();</script>";

} catch (PDOException $e) {
    echo "<p>Błąd: " . $e->getMessage() . "</p>";
}
?>