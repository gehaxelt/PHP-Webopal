#Hilfe

##1. Was ist Opal?
Opal ist eine funktionale Programmiersprache, entwickelt und benutzt an der TU Berlin.

##2. Wie kann ich mehrere Funktionen hintereinander ausführen?
Einfach die Funktionsaufrufe mit einem Semicolon trennen. Webopal sucht aus den vorhandenen Strukturen die zu fokussierende automatisch aus.
Zum Beispiel **`hello;f(3);g("87"!,3)`**
Eine in mehreren Strukturen definierte Funktion kann wie folgt richtig fokussiert werden:
**`[struktur]=>funktion(3)`**

##3. Was ist der Debugmodus?
Standardmäßig fokussiert Webopal die `*.sign` Dateien. Dadurch könnt ihr Hilfsfunktionen, welche in der `*.impl` deklariert wurden, nicht ausführen. Falls ihr das jedoch tun wollt, dann könnt ihr mit dem Debugmodus die `*.impl` Dateien fokussieren.

##4. Automatische Codevervollständigung
Einfach in einem Editorfeld **`Strg+Leer`** drücken. Bestimmte lange Wörter wie `IMPLEMENTATION` oder `denotation` kennt WebOpal schon. Jedoch kann jedes in einem Editorfeld geschriebene Wort, länger als 4 Buchstaben, vervollständigt werden. Bitte beachtet jedoch, dass zur Zeit die Codevervollständigung eineindeutig sein muss:

- `IMP` **`Strg+Leer`** führt zu nichts
- `IMPL` **`Strg+Leer`** führt dagegen zu `IMPLEMENTATION`

Probiert es einfach aus und definiert eine Funktion wie `peterPepperIsCool`, schreibt noch einmal `peter` und drückt **`Strg+Leer`**.

##5. Wie kann ich mithelfen?
Man kann:

* unser Projekt auf Github forken und selber Code schreiben
* Bugs, Ideen oder Anregungen über den Bug- & Ideenreport auf dieser Seite an uns melden.
