# 🍞 Toast Panic - Brot-Klicker Spiel

Ein spaßiges Clicker-Spiel, bei dem du Brot fängst, bevor es verbrennt! Sammle Punkte, steige im Level auf und tritt gegen andere Spieler an.

## 🎮 Spielbeschreibung

In Toast Panic musst du so schnell wie möglich auf das Brot klicken, bevor die Zeit abläuft und es verbrennt. Mit jedem Level wird das Spiel schwieriger und die Zeit kürzer. Sammle Highscores und teile deine Kommentare mit anderen Spielern!

### Features
- **Mehrere Brot-Skins**: Wähle zwischen verschiedenen Brotsorten (🍞🥖🥐🥨🥯)
- **Level-System**: Jedes Level wird schwieriger
- **Highscore-Tabelle**: Konkurriere mit anderen Spielern
- **Kommentarbereich**: Teile deine Gedanken mit der Community
- **Soundeffekte**: Optionale Soundeffekte für Interaktionen

## 🚀 Installation & Setup

### Voraussetzungen
- Webserver mit PHP 7.4+ (z.B. Apache, Nginx)
- MySQL/MariaDB Datenbank
- Moderner Webbrowser mit JavaScript-Unterstützung

### Schritte

1. **Repository klonen**
   ```bash
   git clone https://github.com/yourusername/brot-klicker.git
   cd brot-klicker
   ```

2. **Datenbank einrichten**
   - Erstelle eine neue MySQL-Datenbank
   - Führe das `database_schema.sql` Script aus, um die Tabellen zu erstellen

3. **Konfiguration**
   - Kopiere `config.example.php` zu `config.php`
   - Bearbeite `config.php` und füge deine Datenbank-Zugangsdaten ein:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'dein_db_benutzer');
     define('DB_PASS', 'dein_db_passwort');
     define('DB_NAME', 'deine_datenbank');
     ```

4. **Dateien auf den Webserver hochladen**
   - Lade alle Dateien in das Web-Verzeichnis deines Servers
   - Stelle sicher, dass PHP-Dateien ausgeführt werden können

5. **Browser öffnen**
   - Navigiere zu `http://deine-domain/brot-klicker/`
   - Das Spiel sollte nun funktionieren!

## 📁 Projekt-Struktur

```
brot-klicker/
├── index.php          # Hauptspiel-Seite
├── game.js            # Spiel-Logik und Interaktionen
├── style.css          # CSS-Stile
├── api.php            # Backend-API für Highscores und Kommentare
├── config.php         # Datenbank-Konfiguration (nicht im Repo)
├── config.example.php # Beispiel-Konfiguration
├── database_schema.sql # Datenbank-Schema
├── .gitignore         # Git-Ignore-Dateien
└── README.md          # Diese Datei
```

## 🎯 Spielsteuerung

- **Linksklick**: Klicke auf das Brot, um Punkte zu sammeln
- **Spiel starten**: Klicke auf "🎮 Spiel starten"
- **Skin wählen**: Wähle dein bevorzugtes Brot aus
- **Sound umschalten**: Aktiviere/Deaktiviere Soundeffekte
- **Score speichern**: Gib deinen Namen ein und speichere deinen Highscore

## 🔧 API-Endpunkte

Das Spiel verwendet eine einfache REST-API:

### GET /api.php?action=getHighscores
Gibt die Top 10 Highscores zurück.

### GET /api.php?action=getComments
Gibt die letzten Kommentare zurück.

### POST /api.php
- `action=saveScore`: Speichert einen neuen Highscore
- `action=saveComment`: Speichert einen neuen Kommentar

## 🤝 Mitwirken

Beiträge sind willkommen! Bitte erstelle ein Issue oder Pull Request.

## 📄 Lizenz

Dieses Projekt steht unter der MIT-Lizenz. Siehe [LICENSE](LICENSE) für Details.

## 🎨 Credits

Entwickelt mit ❤️ für Brot-Liebhaber überall.