Allgemeines
===========

Strukturierung der Daten
------------------------

Wie in Joomla üblich, erfolgt die Strukturierung der Daten in TKDClub mittels
2 verschiedenen Ansichten:

1. in **Listansichten**, in denen die gesammelten Datensätze angezeigt werden.
   Diese Listen können nach Stichworten durchsucht, gefiltert und auch in der
   Sortierung verändert werden. In diesen Listansichten gibt es Buttons mit
   denen neue Datensätze angelegt, gelöscht oder anderweitig verändert werden
   können.

2. in der **Detailansicht** die man durch Klicken eines als Link erkennbaren
   Titels in der Listansicht oder durch anwählen des "Tick"-Feldes vor einem
   Datensatz und nachfolgenden Klick des "Bearbeiten" Buttons in der
   Buttonleiste, erreicht.

Datenintegrität - Einchecken / Auschecken
-----------------------------------------

Damit die Datenintigrität gewahrt bleibt, wird immer, wenn ein Benutzer einen
Datensatz in der Detailsicht zur Bearbeitung öffnet, dieser Datensatz in der
Datenbank als in Bearbeitung markiert.

In Joomla! wird dies als "auschecken" bezeichnet. Damit der Datensatz wieder
zur Bearbeitung für andere Benutzer zur Verfügung steht, muss die Detailsicht
mittels der Button "Abbrechen" oder "Speichern und Schließen" verlassen werden.

.. important::
    Ein Schließen des Browserfensters oder ein Klick auf den "Zurück"-Button
    des Browsers lässt den Datensatz ausgecheckt. Erkennbar ist das in der
    Listansicht an dem kleinen Schlosssymbol, dass an einer Stelle des
    Datensatzes angezeigt wird.

.. image:: img/checkin.jpg
    :align: right

In diesem kleinen Schlosssymbol liegt auch schon die Lösung des Problems: Legt
man den Mauszeiger sieht man durch welchen Benutzer der Eintrag ausgecheckt
wurde. Nach dem man sich überzeugt hat, dass dieser Benutzer nur vergessen hat,
den Eintrag einzuchecken, kann man das Einchecken mit einem Klick auf das
Symbol nachholen.

Der Datensatz kann dann wieder regulär von einem anderen Benutzer bearbeitet
werden.
