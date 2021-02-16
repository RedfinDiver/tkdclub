Einstellungen
=============

Die TKDClub- Erweiterung besitzt zahlreiche Einstellmöglichkeiten. Durch diese
kann das Verhalten der Komponente bzw. verschiedene allgemeine Vorgaben
(Währungszeichen, Kilometergeld, Trainerentschädigungen etc.) eingestellt
werden.

.. image:: img/options.jpg
   :align: right

Um diese Einstellungen zu verändern sind Admin-Rechte erforderlich. Die
Optionen sind wie in Joomla! üblich, über entweder
*System->Konfiguration->Taekwondo Club* zu erreichen, oder in der Komponente
selbst über den Button "Optionen".

Die speziellen Einstellungen für jeden Menüpunkt bzw. jede Funktion werden dort
genau beschrieben, an dieser Stelle wird nur auf 3 allgemeine Einstellungen
eingegangen:

Allgemein
---------

.. image:: img/config.jpg

Hier kann der Vereinsname und das Währungszeichen einstellt werden. Bleiben die
Felder leer, so wird der Vereinsname mit "Taekwondo Club" angenommen und als
Währungszeichen das "€" - Symbol verwendet.

Geburtstag Erinnerungen
-----------------------

.. image:: img/bdreminder.jpg

Mit der Installation des TKDClub - Packets liegt im Wurzelverzeichnis der
Joomla! Installation im Ordner "Cli" die Datei "tkdclub_bd_reminder.php"
bereit. Diese Datei muss mittels eines Cron-Jobs ausgeführt werden, am besten
täglich am Morgen (Wenden Sie sich dafür an ihren Administrator!).

Haben an diesem Tag Mitglieder Geburtstag, wird an alle Mitglieder der
ausgewählten Benutzergruppen eine E-Mail zur Erinnerung an den Geburtstag des
Mitglieds/der Mitglieder geschickt.

Es kann darüber hinaus eingestellt werden, für welche Mitglieder (aktive,
ausgetretene oder unterstützende) die Erinnerung geschickt werden soll.

Das verschickte E-Mail enthält Namen, Alter und Kontaktdaten.

Berechtigungen
--------------

.. image:: img/rights.jpg

Hier sind die Einstellungen für das Hinzufügen, Ändern und Löschen von Inhalten
einzustellen. Hier kann z.b. eingestellt werden, das Inhalte von einer gewissen
Gruppe nur erstellt aber nicht gelöscht werden können.


