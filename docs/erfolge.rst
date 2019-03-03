=======
Erfolge
=======

Die TKDClub - Komponente erlaubt es Erfolge eigener Sportler aufzuzeichnen.
Dafür gibt es Listen im Backend und im Frontend.

---------------------
Verwaltung im Backend
---------------------

Einstellungen
-------------

.. image:: img/options.jpg
   :align: right

In der Konfiguration der Komponente können für die Erfolgsverwaltung folgende
Einstellungen getroffen werden:

.. image:: img/medals_options.jpg

Turniertypen
    Hier können Turniertypen eingeben werden. Die Einträge sind mit einem Komma
    getrennt einzugeben und erscheinen dann als Auswahlliste im Formular zum
    Hinzufügen eines Erfolgs. Als Standard erscheinen zur Auswahl:
    Kyorugi und Poomsae

Benachrichtigung
    Hier kann eingestellt werden, ob eine E-Mail Benachrichtigung beim
    Hinzufügen eines neuen Erfolgs über das Frontend an eine Benutzergruppe
    gesendet werden soll. 

E-Mail an Gruppe
    Dieses Feld ist nur sichtbar wenn das "Benachrichtigung" Feld auf "Ja"
    gestellt wird. Hier kann die Benutzergruppe ausgewählt werden, die eine
    E-Mail erhält. Wird nichts ausgewählt, so werden standardmäßig die
    Super User benachrichtigt.

Liste im Backend
----------------

.. image:: img/medals_list.jpg
   :align: right

Mittels des Punktes "Erfolge" im Backend erhalten Sie eine Liste mit Erfolgen
des Vereins.

Mit den Filtern kann die Erfolgsliste durchsucht und gefiltert werden.
Die Volltextsuche ist nach Eintrags-ID, Datum, Bewerb und Klasse möglich.

.. image:: img/medals_filter.jpg


Erfolg hinzufügen
-----------------

.. image:: img/new.jpg
   :align: right

Nach Aufruf des Menüpunkts "Erfolge" klicken Sie in der folgenden Listensicht
oben in der Toolbar auf "Neu".

Im erscheinenden Formular füllen Sie die Felder aus und klicken "Speichern &
Schließen". Um ein neuen Erfolg hinzuzufügen, müssen mindestens die Felder
Datum, Platzierung und Sportler ausgefüllt werden. Alle aktive Mitglieder
stehen als Sportler zur Auswahl zur Verfügung. Bei Teambewerben können mehrere
Sportler hinzugefügt werden.

Bei den Turniertypen stehen immer Kyorugie und Poomsae zur Verfügung. In den
Einstellungen der Komponente können weitere Turniertypen hinzugefügt werden.

Erfolge bearbeiten und löschen
------------------------------

.. image:: img/delete.jpg
    :align: right

.. image:: img/edit.jpg
    :align: right

Jeder Erfolg kann durch Aktivieren der Tick-Box und klicken der Buttons
"Löschen" und "Bearbeiten" gelöscht bzw. editiert werden. Ein alternativer Weg
der Bearbeitung besteht im Anklicken des Datums in der Listansicht. Auch hier
öffnet sich das Formular zum Bearbeiten des Erfolgs. Gelöscht können mehrere
Erfolge zur gleichen Zeit werden, editiert kann immer nur ein Datensatz zur
gleichen Zeit werden.

Statistiken aktivieren
----------------------

.. image:: img/statistics_on.jpg
    :align: right

Mit einem Klick auf den Button können die Erfolgs-Statistiken aktiviert werden.
Da das Erstellen der Statistiken auf die Performance Auswirkungen hat, sind
diese nicht von Anfang an eingeschaltet.

Die Statistiken geben einen schnellen Überblick über die Mitglieder-Datensätze.
Im Menüpunkt ":doc:`statistik`" werden die Statistiken zusätzlich mittels
Diagrammen visualisiert.

.. image:: img/statistics_off.jpg
    :align: right

Nach einem erneuten Klick auf den Statistik-Button werden die Statistiken
wieder ausgeblendet. Die Listenansicht sollte daraufhin wieder schneller
aufgebaut werden.

Export als .cvs Datei
----------------------

.. image:: img/export.jpg
    :align: right

In der Listansicht können alle per Tick-Box markierten Erfolge in eine
csv-Datei exportiert werden.

----------------------
Verwaltung im Frontend
----------------------

.. image:: img/menu_types.jpg

Für das Frontend stehen 2 verschieden Menütypen für Erfolge zur Verfügung:

- Erfolg hinzufügen
- Tabelle der Erfolge anzeigen

Erfolg hinzuzufügen
-------------------

Legen Sie einen Menüpunkt mit diesem Menütyp an und veröffentlichen Sie diesen.

Im Frontend erscheint unter diesem Link ein Formular zum Hinzufügen eines neuen
Erfolgs. Nur ein im Frontend eingeloggter Benutzer mit den erfordelichen
Rechten kann einen Erfolg hinzufügen.

Wird ein neuer Erfolg über das Frontend hinzugefügt, so erhalten alle Benutzer
der in der Konfiguration eingestellten Benutzergruppe per E-Mail eine
Information über den neuen Eintrag.

Ein über das Frontend hinzugefügter Erfolg wird grundsätzlich mit den Status
"Versteckt" abgespeichert. Erst wenn ein Benutzer mit der entsprechenden
Berechtigung veröffentlicht, erscheint dieser in der Erfolgsliste im Frontend.

Tabelle der Erfolge hinzufügen
------------------------------

Legen Sie einen Menüpunkt mit diesem Menütyp an und veröffentlichen Sie diesen.

Es erscheint im Frontend eine Tabelle mit den Erfolgen des Vereins und einer
Medaillendarstellung.

.. image:: img/medals_list_frontend.jpg

.. hint::
    In dieser Liste werden nur Erfolge mit dem Status "Veröffentlicht"
    angezeigt!
