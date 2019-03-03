Changelog
=========

Version 3.2.0 (2019-03-03)
--------------------------

Feature Release

- Hinzufügen eines Erfolgs über das Frontend möglich
- Mehr Filter und Optionen in der Erfolge-Verwaltung im Backend
- Updateserver hinzugefügt

Version 3.1.0 (2019-02-15)
--------------------------

Feature Release

- Neues Cli-Script zur automatischen Geburtstagserinnerung wurde erstellt. Info
  unter :doc:`optionen`.
- Neues Build-System für das Packet. Mittel der "build.xml" kann nun das Packet
  sowie alle Erweiterungen erstellt werden. Die anderen build-Dateien wurden
  entfernt.

Version 3.0.1 (2019-02-09)
--------------------------

Bugfix Release

- Durch einen Fehler wurden die Benutzer der ausgewählten Gruppe bei einer
  Neuanmeldung zu einer Veranstaltung nicht mehr per E-Mail informiert.
- Der Fehler wurde behoben und zusätzlich die Möglichkeit geschaffen, mehrere
  Benutzergruppen mit einer E-Mail über die Anmeldung zu informieren.

Version 3.0.0 (2019-02-03)
----------------------------

Neue Version des TKDClub Packages. Der Code wurde komplett neu gestaltet und
entrümpelt. Folgende Features wurden hinzugefügt bzw. verbessert:

**Allgemeine Verbesserungen**

- Checkout- Anzeige für Datensätze mit Checkin- Möglichkeit
- für viele Listen gibt es nun Mini-Statistiken wenn in der Toolbar der Button
  "Statistik" verfügbar ist
- für alle Listen gibt es nun eine csv-Export Funktion
- Für jeden Bereich kann unter "Hilfe" die Onlinehilfe unter readthedocs.io
  aufgerufen werden

**Mitglieder**

- Statusanzeige für Funktionen im Verein in der Listenansicht (Sternchen)
- Foto des Mitglieds anhängen
- Lizenzen für ein Mitglied eingeben
- IBAN für ein Mitglied speichern

**Trainings**

- Ministatistik für Trainings
- Datensätze können in dieser Ministatistik als bezahlt markiert werden

**Erfolge**

- neuer Medaillenlook
- Turnierarten hinzugefügt

**Prüfungen**

- Unterscheidung in Kup- und Danprüfungen
- Unterschiedliche Prüfungskosten für Kup- und Danprüfungen in der
  Konfiguration einstellbar

**Veranstaltungen/Teilnehmer**

- Bei Anmeldeformularen im Frontend muss nun den Datenschutzbestimmungen
  des Vereins zugestimmt werden. Außerdem kann der User der Speicherung seiner
  eingegeben Daten über den Zeitraum hinaus wie in der Datenschutzerklärung des
  Vereins angegeben, zustimmmen. Dies entspricht der Eintragung in den
  Newsletter des Vereins
- Gesammlte Daten für Veranstaltungen können DSGVO-konform im Backend gelöscht
  werden

**E-Mail**

- alle Mails werden grundsätzlich als Bcc versendet (DSGVO)
- Es sind Anhänge (bis 3 Stück) möglich
- Auch Newsletter- Abonnenten sowie Teilnehmer für eine Veranstaltung können
  nun als Empfänger ausgewählt werden

**Newsletter Abos**

- neue Datenbank für Newsletteranmeldung angelegt. In diese werden der Vor-,
  Nachname und E-Mailadresse von Teilnehmern gespeichert, die dieser
  Speicherung zugestimmt haben

**Statistiken**

- aktuelle Version der GoogleCharts verwendet
- Übersicht über alle Trainings für jeden Trainer
- Möglichkeit in dieser Sicht Trainings für jeden Trainer separat als bezahlt
  zu speichern

**Geburtstagserinnerung**

- mittels eines cli-Scipts durch einen Cron-Job werden Benutzer über
  Geburtstage von Mitgliedern informiert
