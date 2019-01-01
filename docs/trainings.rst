Trainings
=========

.. image:: img/trainings_list.jpg
   :align: right

Mittels des Punktes "Trainings" erhalten Sie eine Liste aller im Verein
gehaltener Trainings.

Mit den Filtern kann die Trainingsliste durchsucht und gefiltert werden. Die
Volltextsuche ist nach Trainings-ID und Datum möglich. Alle weiteren
Filtermöglichkeiten finden Sie unter den Suchwerkzeugen.

.. image:: img/trainings_filter.jpg

Beim Status gibt es 3 verschiende Stati zur Auswahl, diese werden auch
durch ein Symbol in der Listansicht angezeigt:

.. image:: img/trainings_icon_unpaid.jpg
    :align: right

"unbezahlt"
    Training, bei dem weder die Trainingsleitung noch die Assistenz bezahlt
    wurde.

.. image:: img/trainings_icon_paid.jpg
    :align: right

"bezahlt"
    Training, bei dem sowohl die Trainingsleitung als auch die Assistenz
    bezahlt wurde

.. image:: img/trainings_icon_part_paid.jpg
    :align: right

"teilweise bezahlt"
    Training bei dem entweder die Trainingsleitung oder die Assistenz bezahlt
    wurde

Training hinzufügen
-------------------

.. image:: img/new.jpg
    :align: right

Nach Aufruf des Menüpunkts "Trainings" klicken Sie in der folgenden Listensicht
oben in der Toolbar auf "Neu".

Im erscheinenden Formular füllen Sie die Felder aus und klicken "Speichern &
Schließen". Um ein neues Training hinzuzufügen, müssen mindestens die Felder
Datum und Trainer ausgefüllt werden. Das neue Training wird automatisch als
"unbezahlt" zur Datenbank hinzugfügt.

.. hint::
    Jedes Mitglied kann als Trainer markiert werden. Dazu in der "Mitglieder"-
    Liste die entsprechende Person bearbeiten und unter "Funktionen" die
    Funktion als Trainer hinzufügen.

    Um einen Trainingstyp auswählen zu können, sind die gewünschten Typen in
    der Konfiguration hinzuzufügen. Sie bekommen einen Hinweis, falls diese
    beiden Parameter nicht gepflegt sind.

Für jedes Training können außerdem noch bis zu 3 weitere Assistenten/-innen
hinzufügt werden. Damit die Berechnung der Trainerentschädigung funktioniert,
müssen die Trainer-/ Assistentenentschädigung und das Kilometergeld in der
Konfiguration der Komponente eingestellt werden.

.. _ref-set-trainings-as-paid:

Trainings als bezahlt setzen
----------------------------

Neu erstellte Trainings werden als "unbezahlt" markiert. Der Bezahlstatus ist
für die Trainingsleitung und die Assistenz separat ausgewiesen.

.. image:: img/trainings_paid_unpaid.jpg

.. image:: img/statistics_on.jpg
    :align: right

Die noch zu zahlenden Aufwandsentschädigungen werden in der Statistik
ausgewiesen. Dafür in der Listansicht oben über den Button die Statistiken
einschalten.

.. image:: img/trainings_stats_mini.jpg

Diese Gesamtstatistik zeigt aber nicht die einzelnen Beträge der jeweiligen
Summen pro Person. Um für jede Person die entsprechende Summe angezeigt zu
bekommen und die Trainings als bezahlt zu setzen, wie folgt vorgehen:
(es gibt 2 Möglichkeiten)

Möglichkeit 1:
    - bei aktivierter Statistik die Filter unter "Suchwerkzeuge" verwenden und
      nach unbezahlten Trainings einer Person filtern
    - es erscheint im Statistikfeld der Eintrag mit der Summe der Person,
      ebenso ein Link ("alle Trainings als bezahlt speichern"). Mit diesem
      werden in der Datenbank alle Trainings der ausgewählten Person als
      bezahlt markiert.

    .. image:: img/trainings_stats_of_trainer.jpg

.. _ref-possibility2-for-status-setting:

Möglichkeit 2:
    - In der großen Statistik Sicht (Link in der Sidebar oder über das Menü)
      findet sich eine Übersicht über alle Trainingsleitungen und Assistenzen,
      hier ist die noch ausstehende Summe pro Person zu sehen
    - Mittels der Links "als bezahlt speichern" werden auch hier alle Training
      in der Datenbank als bezahlt markiert

    .. image:: img/training_stats_maxi.jpg

.. danger::
    - **Vor** dem Klicken auf einen der Links zum Markieren der Trainings als
      als bezahlt in der Datenbank, ist **unbedingt** die entsprechende Summe
      zu notieren. Nach dem der Datenbankvorgang abgeschlossen ist, gibt es
      keine Möglichkeit mehr, herauszufinden wie hoch die Summe für die Person
      war!

Training bearbeiten und löschen
-------------------------------

.. image:: img/delete.jpg
    :align: right

.. image:: img/edit.jpg
    :align: right

Jedes Training kann durch Aktivieren der Tick-Box und klicken der Buttons
"Löschen" und "Bearbeiten" gelöscht bzw. editiert werden. Ein alternativer Weg
der Bearbeitung besteht im Anklicken des Trainingsdatums in der Listansicht.
Auch hier öffnet sich das Formular (Detailansicht) zum Bearbeiten des
Trainings. Gelöscht können mehrere Mitglieder zur gleichen Zeit werden,
editiert kann immer nur ein Datensatz zur selben Zeit.

.. hint::
    Grundsätzlich ist es möglich, im der Detailanicht auch die Bezahlung der
    Trainingsleitung und der Assistenz zu markieren. Von diesem Vorgehen wird
    aber abgeraten. Es sollte immer wie unter :ref:`ref-set-trainings-as-paid`
    beschrieben vorgegangen werden, um Trainings als bezahlt zu markieren!

Statistiken aktivieren
----------------------

.. image:: img/statistics_on.jpg
    :align: right

Mit einem Klick auf den Button können die Trainings-Statistiken aktiviert
werden. Da das Erstellen der Statistiken auf die Performance Auswirkungen hat,
sind diese nicht von Anfang an eingeschaltet.

Die **Besonderheit** in den Trainingsstatistiken liegt an der Verknüpfung mit
den Filtern der Listansicht. Immer wenn bei aktivierten Statistiken ein Filter
gesetzt wird, ändert sich die Statistik entsprechend. So sind schnell
interessante Auswertungen möglich.

Im Menüpunkt ":doc:`statistik`" werden die Statistiken zusätzlich mittels
Diagrammen visualisiert. Hier findet sich auch die unter
:ref:`Trainings als bezahlt setzen - Möglichkeit 2
<ref-possibility2-for-status-setting>`
beschriebene Übersicht.

.. image:: img/statistics_off.jpg
    :align: right

Nach einem erneuten Klick auf den Statistik-Button werden die Statistiken
wieder ausgeblendet. Die Listenansicht sollte daraufhin wieder schneller
aufgebaut werden.

Export als .cvs Datei
----------------------

.. image:: img/export.jpg
    :align: right

In der Listansicht können alle per Tick-Box markierten Trainings in eine
csv-Datei exportiert werden.
