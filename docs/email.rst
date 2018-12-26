E-Mail senden
=============

Mit dieser Funktion kann an Mitglieder eine Email versendet werden. Dies ist
nur vom Backend aus möglich. Mit dem Button "Optionen" gelangen Sie in die
Einstellungen. (Falls der angemeldete User die entsprechenden Berechtigungen
besitzt).

.. image:: img/options.jpg
   :align: right

Konfiguration
-------------

.. image:: img/mail_config.jpg
   :align: center

In der Konfiguration sind folgende Einstellungen möglich:

**Betreffprefix**
    Wenn hier ein Text eingegeben wird, so wird dieser dem im Formular
    eingegebenen Betreff vorangestellt. Ein Beispiel kann sein "Info aus dem
    Verein -"

**Signatur**
    Der hier eingegebene Text wird an den Nachrichtentext angehängt. Als
    Beispiel wäre "Grüße aus dem Verein, bis später im Training!" als möglicher
    Anhang denkbar.

**E-Mail für Servertest**
    Möchte man auf dem Server den Emailversand testen, so ist es möglich hier
    eine E-Mailadresse zu hinterlegen. Dann wird beim Versand einer E-Mail jede
    weitere Einstellung ignoriert und das Mail nur an diese Adresse und an die
    des in der Website eingeloggten Users versendet. Das ist nützlich um nicht
    bei einem Test an zig-verschiedene Mitglieder eine Mail mit "Test" im
    Betreff schicken zu müssen.

E-Mail erstellen
----------------

.. image:: img/mail_form.jpg
   :align: center

Im Formular sind die folgenden Felder auszufüllen:

**Betreff**
    Hier geben Sie den Betreff der E-Mail an

**Nachricht**
    In dieses Feld wird der eigentliche Nachrichtentext geschrieben

**Empfänger**
    Hier kann entschieden werden, an wen die Mail gesendet wird. Zur Auswahl
    stehen aktive, unterstützende oder ausgetretene Mitglieder sowie Newletter-
    Abonnenten. Grundsätzlich werden alle Empfänger als Blindkopie hinzugefügt.
    Es muss davon mindestens 1 Auswahl markiert sein, ansonsten gibt es eine
    Fehlermeldung. Ausnahme ist, wenn wie in der Konfiguration beschrieben,
    eine Test-Email Addresse eingetragen ist. Dann werden diese Einstellungen
    ignoriert.

.. hint::

    Dass die Test-E-Mail aktiv ist, wird im Formular mit einem Hinweis und der
    hinterlegten E-Mail Adresse angezeigt. Der angemeldete User bekommt stehts
    eine Kopie der E-Mail auf seine im Joomla User-Pofil hinterlegte E-Mail
    Adresse.

.. image:: img/mail_testhint.jpg
   :align: center

E-Mail senden
-------------

.. image:: img/mail_send.jpg
   :align: right

Nach dem Ausfüllen des Formulars kann die E-Mail mit dem Button "Senden"
verschickt werden.

Falls beim Senden der E-Mail etwas falsch läuft, wird eine Fehlermeldung
ausgegeben.
