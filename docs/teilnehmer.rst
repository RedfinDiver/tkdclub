==========
Teilnehmer
==========

.. image:: img/participants_menu.jpg
    :align: right

Unter der Teilnehmer Liste erscheinen alle Sportler die sich zu einer
Veranstaltung angemeldet haben. Im Backend (also als Adminstrator) dient die
Liste der Übersicht. Sie kann gefiltert und sortiert werden.

.. important::
    Obwohl auch im Backend Anmeldungen durch den Adminstrator durchgeführt
    werden können, sollte dies nur in Ausnahmefällen erfolgen.

    Die Anmeldungen in dieser Liste sollten eigentlich nur aus Anmeldungen vom
    Frontend der Seite stammen. Dies ist umso wichtiger, da im Frontend die
    Zustimmung zu den Datenschutzbestimmungen sowie zum Abo des Newsletters
    eingeholt und gespeichert wird.

    Dies erlaubt einen DSGVO-konformen Umgang mit den eingeholten Daten!

---------------------
Verwaltung im Backend
---------------------

Wie bereits ausgeführt, dient der Menüpunkt im Backend nur der Übersicht.
Trotzdem sind auch hier Anmeldungen durch den Adminstrator möglich.

Einstellungen
-------------

.. image:: img/options.jpg
   :align: right

In der Konfiguration der Komponente haben die Einstellungen wie unter
":doc:`teilnehmer`" beschrieben, Auswirkungen auf das Anmeldeformular im
Frontend, sowie auf das DSGVO-konforme Löschen von Daten.

Neuen Teilnehmer hinzufügen
---------------------------

.. image:: img/new.jpg
   :align: right

Nach Aufruf des Menüpunkts "Teilnehmer" klicken Sie in der folgenden
Listensicht oben in der Toolbar auf "Neu".

Geben Sie im erscheinenden Formular die Daten des Teilnehmers ein und klicken
Sie auf "Speichern&Schließen".

.. important::
    Für über das Backend hinzugefügte Teilnehmer gibt es keine Zustimmung zu
    den Datenschutzbestimmungen oder für die Eintragung in den Newsletter.
    Hier hat der Adminstrator die Verantwortung für den DSGVO- konformen Umgang
    mit den Daten!

Das ein Sportler den o.g. Datenschutzbestimmungen und der Newslettereintragung
zugestimmt hat, erkennt man an den entprechenden Einträgen in der Listansicht:

.. image:: img/participants_dsgvo_agreed.jpg

Teilnehmer bearbeiten und löschen
---------------------------------

.. image:: img/delete.jpg
    :align: right

.. image:: img/edit.jpg
    :align: right

Jeder Teilnehmer kann durch Aktivieren der Tick-Box und klicken der Buttons
"Löschen" und "Bearbeiten" gelöscht bzw. editiert werden. Ein alternativer Weg
der Bearbeitung besteht im Anklicken des Datums oder der Veranstaltung in der
Listansicht. Auch hier öffnet sich das Formular zum Bearbeiten des Teilnehmers.
Gelöscht können mehrere Teilnehmer zur gleichen Zeit werden, editieren kann man
immer nur einen Datensatz zur gleichen Zeit.

Teilnehmer veröffentlichen und verstecken
-----------------------------------------

.. image:: img/events_publish_unpublish.jpg
    :align: right

Mit diesen Button können Teinehmer veröffentlicht und versteckt werden. Dies
kann eine Alternative zur Löschung sein.

DSGVO Konformität
-----------------

Um mit den per Anmeldeformular erhobenen Daten entsprechen der DSGVO
(Datenschutzgrundverordung) zu verfahren, sind folgende Dinge Veraussetzung:

1. Die Datenschutzerklärung des Vereins ist veröffentlicht. (Am besten als
   Artikel) Diese wird von den Teilnehmern bei der Übersendung der Daten
   akzeptiert.

2. Die in dieser Datenschutzerklärung genannte Speicherdauer für erhobene
   Daten wird in der Konfiguration entsprechend eingestellt.

.. image:: img/participants_button_gdpr.jpg
    :align: right

Durch Klick auf den Button "Löschung nach DSGVO" sucht die Software
Teilnehmerdaten, deren Speicherzeit laut den Einstellungen abgelaufen ist, und
löscht diese.

Für diejenigen Teilnehmer, die zugestimmt haben ihre E-Mail Adresse in den
Verein-Newsletter eintragen zu lassen, werden diese Daten in der Newsletter
Datenbank gespeichert.

Export als .cvs Datei
----------------------

.. image:: img/export.jpg
    :align: right

In der Listansicht können alle per Tick-Box markierten Teilnehmer in eine
csv-Datei exportiert werden.

--------
Frontend
--------

Menüpunkt hinzufügen
--------------------
