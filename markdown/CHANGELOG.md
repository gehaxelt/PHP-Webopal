Changelog WebOpal
==================================

##2012-12-19: WebOpal v0.4 ##
- **ACHTUNG**: Standardmäßig werden jetzt die `*.sign` und nicht mehr die `*.impl` Dateien fokussiert. Dies soll einfach simulieren, dass man von Außen auf die Struktur zugreift. Mit der Checkbox "Debugmodus" kann jedoch der Fokus wieder auf die `*.impl` gelegt werden, wodurch Hilfsfunktionen debuggt werden können.
- **ACHTUNG**: AutoFokus: Strukturen müssen nicht mehr fokussiert werden. WebOpal fokussiert automatisch. Ist eine Funktion mehrfach definiert, kann sie über `[struc]=>fun()` fokussiert werden.
- **NEW FEATURE**: Es kann jetzt auch `IMPLEMENTATION` und `SIGNATURE` direkt in den Eingabefeldern definiert werden. Falls dies nicht geschieht, wird das automatisch gemacht.
- **NEW FEATURE**: Error Jumping: Fehler werden jetzt automatisch rötlich im Editor hinterlegt und man kann direkt zu ihnen springen.
- **NEW FEATURE**: Die Editorbereiche sind jetzt beliebig vergrößerbar (untere Rechte Ecke)
- Minor: Javascript wurde mit JS-Lint überprüft
- Minor: FEATURES.md endlich ausgefüllt
- Minor: Nach einem Versionsupdate wird der neuste Teil des Changelog angezeigt (Cookie)
- Minor: Bugreport verlangt nun E-Mail-Adresse
- Minor: Dynamisches Hinzufügen von Strukturen debuggt
- Minor: Cursor jetzt nur noch im fokussierten ACE-Editor sichtbar
- Minor: Hilfe Update

##2012-12-13: WebOpal v0.3 ##
- **NEW FEATURE**: Bugreport / Featurevorschläge
- **NEW FEATURE**: Optimierung der Ladezeiten
- **NEW FEATURE**: Dynamisches Hinzufügen und Löschen von Strukturen
- **NEW FEATURE**: Hin- und Herspringen von Editor zu Editor mit Strg-Alt-NUMPAD[2,4,6,8]
- Minor: Drücken von Enter in der Befehlszeile wird den Code ausführen
- Minor: Drücken von Strg+Enter führt den Code von überall aus.
- Minor: Logo hinzugefügt
- Minor: Letzte Ausführung wird angezeigt
- Backend: Code aufgeräumt/sortiert

##2012-12-05: WebOpal v0.2 ##

- **NEW FEATURE**: ACE Editor anstatt Editarea (mit OPAL Syntax Highlighting)
- **NEW FEATURE**: Multiple Funktionen ausführen (z.B. "func(x,y);foo(x)")
- **NEW FEATURE**: Code-Completion (Strg-Space)
- **NEW FEATURE**: Umstellung des Absendens auf AJAX
- **NEW FEATURE**: Uploaden von *.impl und *.sign Dateien
- Minor: Link zu GitHub, Contributer-Liste

##2012-11-28: WebOpal v0.1a ##

- **NEW FEATURE**: Multiple Dateien (default 3) möglich (Akkordeonstruktur von JQUERY UI)
- **NEW FEATURE**: Benennung der Strukturen möglich (wenn nicht benannt, wird zufällig ein Name gewählt)
- **NEW FEATURE**: Eine der Strukturen kann fokussiert werden
- **NEW FEATURE**: Timeout, wenn Programm zu lange läuft (default 10s)
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
