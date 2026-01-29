In diesem Projekt habe ich ein Chat-System umgesetzt, das es Benutzern ermöglicht, ohne Neuladen der Seite miteinander zu kommunizieren.
Die Anwendung bietet zwei verschiedene Arten der Kommunikation:

Private Nachrichten zwischen zwei Benutzern

Einen globalen Chat, der für alle angemeldeten Benutzer sichtbar ist

Automatische Aktualisierung der Nachrichten durch regelmäßige AJAX-Anfragen

Das Hauptziel des Projekts war es, das Client-Server-Prinzip, die AJAX-Kommunikation sowie die Speicherung und Verwaltung von Nachrichten in einer Datenbank besser zu verstehen.

🎯 Umgesetzte Funktionen
✅ Private Nachrichten (1-to-1)

Senden und Empfangen von Nachrichten zwischen zwei Benutzern

Vollständiger Verlauf der Konversationen

Anzeige von ungelesenen Nachrichten

Automatische Aktualisierung alle 2 Sekunden

✅ Globaler Chat

Nachrichten sind für alle Benutzer sichtbar

Anzeige des Benutzernamens des Absenders

Regelmäßige Aktualisierung der Inhalte

Gemeinsame Unterhaltung für alle

✅ Benutzeroberfläche

Seitenleiste mit allen Konversationen

Chatbereich mit automatischem Scrollen

Visuelle Unterscheidung zwischen gesendeten und empfangenen Nachrichten

Anzeige der Uhrzeit der Nachrichten

🔄 Funktionsweise: Private Nachricht zwischen zwei Benutzern
Schritt 1: Benutzeroberfläche (sendMsg.php)

Der Benutzer schreibt eine Nachricht in ein Textfeld und kann sie entweder:

über den Senden-Button abschicken

oder mit der Enter-Taste senden

JavaScript erkennt diese Aktion und ruft die Funktion sendMessage() auf.

Schritt 2: Versand der Nachricht mit AJAX

Die Funktion liest den Nachrichtentext aus und sendet ihn per AJAX an den Server.
Vor dem Versand wird geprüft, ob die Nachricht nicht leer ist.

Schritt 3: Verarbeitung auf dem Server (sendMessage.php)

Das PHP-Skript:

liest den aktuell angemeldeten Benutzer aus der Session

überprüft die empfangenen Daten

speichert die Nachricht mithilfe einer vorbereiteten SQL-Anfrage in der Datenbank

Dadurch werden SQL-Injections verhindert.

Schritt 4: Speicherung in der Datenbank

Die Nachrichten werden in der Tabelle messages gespeichert mit:

Absender

Empfänger

Inhalt der Nachricht

Versanddatum

Lesestatus

So bleibt jede Nachricht dauerhaft gespeichert.

Schritt 5: Abrufen der Nachrichten (getMessages.php)

Alle 2 Sekunden ruft das Frontend dieses Skript auf, um:

gesendete

empfangene
Nachrichten zwischen den beiden Benutzern abzurufen.

Die Daten werden im JSON-Format zurückgegeben.

Schritt 6: Anzeige der Nachrichten

Die Nachrichten werden dynamisch dargestellt:

rechts für gesendete Nachrichten

links für empfangene Nachrichten

Die Seite wird dabei nicht neu geladen, was einen Echtzeit-Effekt erzeugt.

🌐 Funktionsweise des globalen Chats
Allgemeines Prinzip

Der globale Chat funktioniert ähnlich wie der private Chat,
jedoch ohne einen festen Empfänger.

Eine Nachricht im globalen Chat ist für alle Benutzer sichtbar.

Senden einer globalen Nachricht

Wenn der Benutzer den globalen Chat auswählt:

wird ein Status (isGlobalChat) aktiviert

die Nachricht wird an sendGlobalChat.php gesendet

es wird kein receiverId verwendet

Speicherung in der Datenbank

Die Nachrichten des globalen Chats werden in der Tabelle global_chat gespeichert mit:

ID des Absenders

Nachrichtentext

Zeitstempel

Abrufen und Anzeigen

Das Skript getGlobalChat.php:

lädt die letzten Nachrichten

verbindet die Benutzertabelle, um den Namen des Absenders anzuzeigen

gibt die Daten im JSON-Format zurück

Im Frontend wird jede Nachricht zusammen mit dem Namen des Absenders angezeigt.
