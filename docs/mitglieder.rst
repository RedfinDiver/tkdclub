Mitglieder
==========

Eine der wichtigsten Funktionen der TKDClub- Komponente ist es,
Vereinsmitglieder zu verwalten. Mittels des mitgelieferten Plugins ist es
möglich, das Joomla! Registrierungsformular so zu erweitern, dass sich
Personen beim Verein als Mitglied und gleichzeitig als Nutzer der Website
registrieren können.

Das Mitglied kann seine gespeicherten Daten einsehen und ausgewählte Daten
(z.B. Adresse) jederzeit selbst ändern.

Dem Administrator ist möglich, bereits auf der Website registrierte Nutzer
mit einem Mitglied aus der Mitgliederliste zu verknüpfen.

---------------------
Verwaltung im Backend
---------------------

.. image:: img/members_list.jpg
   :align: right

Mittels des Punktes "Mitglieder" erhalten Sie eine Liste aller Mitglieder. Beim
Öffnen werden standardmässig nur die aktiven Mitglieder angezeigt.

Mit den Filtern kann die Mitgliederliste durchsucht und gefiltert werden. Die
Volltextsuche ist nach Mitglieds-ID, Vorname, Nachname, Strasse, PLZ, Stadt und
Ausweisnummer möglich.

.. image:: img/members_filter.jpg

Einstellungen
-------------

.. image:: img/options.jpg
   :align: right

In der Konfiguration der Komponente können für die Mitgliederverwaltung Liste
folgende Einstellungen getroffen werden:

.. image:: img/members_options.jpg

Nationen
    Hier können Nationen eingeben werden. Die Einträge sind mit einem Komma
    getrennt einzugeben (z.B. AUT,GER,ITA) und erscheinen dann als Liste im
    Mitgliedsformular. Als Standard erscheinen zur Auswahl:
    AUT,GER,SUI,ITA,CRO,BIH,SRB.

Funktionen im Verein
    Hier können Funktionen definiert werden, die dann im Mitgliedsformular
    einem Mitglied zugeordnet werden können. Die als Standard vorhandenen
    Funktionen sind: Präsident, Vizepräsident, Kassier, Schriftführer und
    Trainer.

Lizenzen
    Hier können eigene Lizentypen (mit Komma getrennt) eingeben werden. Einige
    Lizenzen stehen standardmässig zur Verfügung, so z.B. Trainerlizenzen und
    auch Refereelizenzen. Diese Lizenzen können einem Mitglied in der
    Detailansicht zugeordnet werden.

Mitglied hinzufügen
-------------------

.. image:: img/new.jpg
   :align: right

Nach Aufruf des Menüpunkts "Mitglieder" klicken Sie in der folgenden
Listensicht oben in der Toolbar auf "Neu".

Im erscheinenden Formular füllen Sie die Felder aus und klicken "Speichern &
Schließen". Um ein neues Mitglied hinzuzufügen, müssen mindestens die Felder
*Nachname, Vorname, Geschlecht und Eintrittsdatum* ausgefüllt werden. Das neue
Mitglied wird automatisch als "aktiv" zur Datenbank hinzugfügt.

Mitglieder bearbeiten und löschen
---------------------------------

.. image:: img/delete.jpg
    :align: right

.. image:: img/edit.jpg
    :align: right

Jedes Mitglied kann durch Aktivieren der Tick-Box und klicken der Buttons
"Löschen" und "Bearbeiten" gelöscht bzw. editiert werden. Ein alternativer Weg
der Bearbeitung besteht im Anklicken des Nachnamens in der Listansicht. Auch
hier öffnet sich das Formular zum Bearbeiten des Teilnehmers. Gelöscht können
mehrere Mitglieder zur gleichen Zeit werden, editieren kann man immer nur einen
Datensatz zur gleichen Zeit.

Mitglied mit Website-Benutzer verknüpfen
----------------------------------------

Ein Mitglied kann mit einem bestehenden Website-Benutzer verknüpft werden.
Dazu einfach in der Bearbeitungssicht des Mitglieds oben rechts den
entsprechenden Benutzer nach Klick auf das User-Symbol auswählen und danach
speichern:

.. image:: img/members_link.jpg
    :align: center

Wenn das entsprechende Plugin aktiviert ist, wird bei der Registrierung eines
neuen Benutzers auch ein neues Mitglied in der Liste angelegt. Benutzer und
Mitlied sind dann automatisch verknüpft.

Dateien anhängen
----------------

Nach dem Speichern des Mitglieds ist es im Tab "Anhänge" möglich .pdf, .jpg und
.png Dateien anzuhängen. Die maximale Dateigröße ist auf 500 Kb begrenzt. Hier
können Anmeldeformulare, Urkunden usw. angehängt werden.

Es ist außerdem möglich für jedes Mitglied ein Foto zu hinterlegen. Dieses
wird dann in Detailansicht angezeigt.

Statistiken aktivieren
----------------------

.. image:: img/statistics_on.jpg
    :align: right

Mit einem Klick auf den Button können die Mitglieder-Statistiken aktiviert
werden. Da das Erstellen der Statistiken auf die Performance Auswirkungen hat,
sind diese nicht von Anfang an eingeschaltet.

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

In der Listansicht können alle per Tick-Box markierten Mitglieder in eine
csv-Datei exportiert werden.

----------------------
Verwaltung im Frontend
----------------------

Zur Zeit beschränkt sich die Verwaltung im Frontend die Möglichkeit, als
auf der Website registrierter Nutzer mit bestehender Verknüpfung zum
entsprechenden Mitglied aus der Mitgliederliste, die gespeicherten Daten im
Frontend einzusehen und ausgewählte Daten selbst ändern zu können.

Damit bei der Benutzerregistrierung die entprechenden Daten zur
Mitgliedsanmeldung angezeigt werden, muss das Plugin "tkdclubmember" durch den
Administrator aktiviert sein:

.. image:: img/members_plugin.jpg
    :align: center
