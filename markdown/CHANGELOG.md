Changelog WebOpal
==================================

##???: WebOpal v0.3 ##
- NEW FEATURE: Optimierung der Ladezeiten
- NEW FEATURE: Dynamisches Hinzufügen und Löschen von Strukturen
- Minor: Drücken von Enter in der Befehlszeile wird den Code ausführen
- Minor: Dr�cken von Strg+Enter f�hrt den Code von �berall aus.
- Minor: Logo hinzugefügt
- Minor: Letzte Ausf�hrung wird angezeigt
- Backend: Code aufgeräumt/sortiert

##2012-12-05: WebOpal v0.2 ##

- NEW FEATURE: ACE Editor anstatt Editarea (mit OPAL Syntax Highlighting)
- NEW FEATURE: Multiple Funktionen ausführen (z.B. "func(x,y);foo(x)")
- NEW FEATURE: Code-Completion (Strg-Space)
- NEW FEATURE: Umstellung des Absendens auf AJAX
- NEW FEATURE: Uploaden von *.impl und *.sign Dateien
- Minor: Link zu GitHub, Contributer-Liste

##2012-11-2: WebOpal v0.1a 8##

- NEW FEATURE: Multiple Dateien (default 3) möglich (Akkordeonstruktur von JQUERY UI)
- NEW FEATURE: Benennung der Strukturen möglich (wenn nicht benannt, wird zufällig ein Name gewählt)
- NEW FEATURE: Eine der Strukturen kann fokussiert werden
- NEW FEATURE: Timeout, wenn Programm zu lange läuft (default 10s)
- Bugfix: Typos berichtigt
- Bugfix: CHANGELOG.md erstellt
- Bugfix: WebOpal W3C verfiziert

##2012-11-24: WebOpal v0.1 ##

- Eingabe vom Implementation-, bzw. Signatureteil
- Eingabe des auszuführenden Befehls
- Ausgabe der Opalausgabe
- Plattformunabhängig - Es wird nur ein Browser benötigt
- Custom Search hinzugefügt
- Syntaxhighlighting hinzugefügt
- Download hinzugefügt (Log des Funktionsaufrufes bereits inkludiert!)
- Alle Importe des SubSystem System verboten. Man könnte sonst auf meinem Server Dateien erstellen, lesen, ... was nicht meinen Sicherheitsvorstellungen entspricht.
- https://projects.uebb.tu-berlin.de/opal/dosfop/2.4/bibopalicaman/Subsystem_System.html#SEC822
