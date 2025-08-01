<?php
// ====================================================================
// PHP-Logik (Backend)
// ====================================================================

// Hier würden Sie die Datenbankverbindung herstellen.
// Für SQLite ist dies sehr einfach.
// In einer echten Anwendung würden Sie die Datenbankverbindung
// und die Authentifizierungslogik in separate Dateien auslagern.

// Platzhalter für die SQLite-Datenbankverbindung
$db = null;
try {
    // Erstellt die Datenbankdatei, falls sie nicht existiert
    $db = new PDO('sqlite:travel_app.db');
    // Aktiviert die Fehlerausgabe für PDO
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Beispiel: Eine einfache Tabelle erstellen, falls sie noch nicht existiert.
    // Dies sollte nur einmalig beim Start der Anwendung ausgeführt werden.
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL
    )");

} catch (PDOException $e) {
    // Bei einem Verbindungsfehler die Fehlermeldung ausgeben
    die("Datenbankverbindung fehlgeschlagen: " . $e->getMessage());
}

// Session-Management starten, um den Benutzerstatus zu verfolgen
session_start();

// Einfache Logik, um zu prüfen, ob ein Benutzer eingeloggt ist
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : 'Gast';

// ====================================================================
// HTML-Struktur (Frontend)
// ====================================================================
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Japan Travel App - Startseite</title>
    <!-- Bootstrap CSS über CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* ==================================================================== */
        /* CSS-Styling (für Bootstrap-Anpassungen und eigene Styles) */
        /* ==================================================================== */
        body {
            background-color: #f8f9fa; /* Leichter grauer Hintergrund */
            font-family: 'Inter', sans-serif;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://placehold.co/1200x600/34495e/ffffff?text=Japan-Reise');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 5rem 0;
            margin-top: 56px; /* Offset für die feste Navbar */
            text-align: center;
        }
        .feature-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-top: -3rem; /* Visueller Effekt, dass die Karte in den Hero-Bereich ragt */
        }
        .btn-primary {
            background-color: #34495e;
            border-color: #34495e;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #2c3e50;
            border-color: #2c3e50;
        }
    </style>
</head>
<body>
    <!-- ==================================================================== -->
    <!-- Bootstrap Navbar -->
    <!-- ==================================================================== -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-geo-alt-fill me-2" viewBox="0 0 16 16">
                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                </svg>
                Japan Travel App
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Startseite</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Registrieren</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ==================================================================== -->
    <!-- Hauptinhalt der Seite -->
    <!-- ==================================================================== -->
    <main>
        <!-- Hero-Sektion -->
        <header class="hero-section">
            <div class="container">
                <h1 class="display-3">Willkommen, <?php echo htmlspecialchars($username); ?>!</h1>
                <p class="lead">Plane deine unvergessliche Reise nach Japan mit deinen Freunden.</p>
                <a href="#" class="btn btn-primary btn-lg mt-3" onclick="showWelcomeMessage()">Starte deine Reise</a>
            </div>
        </header>

        <!-- Feature-Sektion (wird dynamisch mit PHP gefüllt) -->
        <section class="container mt-5 mb-5">
            <div class="row">
                <div class="col-md-8 mx-auto feature-card">
                    <?php if ($isLoggedIn): ?>
                        <h2>Hallo <?php echo htmlspecialchars($username); ?>!</h2>
                        <p>Hier siehst du bald eine Übersicht deiner Reisen und anstehenden Aufgaben.</p>
                        <button class="btn btn-outline-secondary">Neue Reise planen</button>
                    <?php else: ?>
                        <h2>Dein persönlicher Reiseplaner</h2>
                        <p>Melde dich an, um mit der Planung deiner Japan-Reise zu beginnen. Verwalte Flüge, Hotels, Finanzen und Packlisten in einer App.</p>
                        <a href="#" class="btn btn-primary">Jetzt anmelden</a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <!-- ==================================================================== -->
    <!-- JavaScript (JS) -->
    <!-- ==================================================================== -->
    <!-- Bootstrap JS über CDN (mit Popper.js für Dropdowns etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Eine einfache JavaScript-Funktion, die aufgerufen wird, wenn der Button geklickt wird
        function showWelcomeMessage() {
            alert('Willkommen bei deiner Japan Travel App! Bald kannst du hier alles planen.');
        }

        // Alternative, modernere Methode, die Popups vermeidet
        // function showWelcomeMessage() {
        //     const mainContent = document.querySelector('main');
        //     const messageDiv = document.createElement('div');
        //     messageDiv.className = 'alert alert-info alert-dismissible fade show fixed-top w-50 mx-auto mt-2';
        //     messageDiv.role = 'alert';
        //     messageDiv.innerHTML = `
        //         Willkommen bei deiner Japan Travel App!
        //         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        //     `;
        //     mainContent.prepend(messageDiv);
        // }
    </script>
</body>
</html>
