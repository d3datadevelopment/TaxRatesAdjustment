# TaxRatesAdjustment

## Inhalte

### Was kann das Modul?

Dieses Modul stellt 2 Aufrufe bereit, die in Standardkonstellationen die MwSt.-Sätze anpasst, die in Deutschland zum 01.07.20 und zum 01.01.2021 geändert werden (Bestandteil des beschlossenen Corona-Konjunkturpaketes). 

Die Anpassung kann über entsprechende Cronjobs zeitgesteuert zum Stichtermin ausgeführt werden, ohne dass hierfür Ihre Anwesenheit erforderlich ist.

Die Scripte ändern:
- den im Shop eingestellten allgemeinen Steuersatz
- an den Artikeln hinterlegten speziellen Steuersätze

- von 19% zu 16% und
- von 7% zu 5%
- sowie später auch zurück

In Multishopinstallationen (Enterprise Edition) werden die Angaben für jeden Subshop geändert.

Die Scripte prüfen anhand der Systemzeit mit kleinen Toleranzen (+/-3 Tage um das jeweilige Umstellungsdatum), ob die Veränderung ausgeführt werden darf. Damit wird verhindert, dass ein versehentliches Auslösen zur falschen Shopkonfiguration führt.

### Was kann das Modul nicht?

Sind im Shop noch an anderen Stellen Steuersätze hinterlegt, werden diese nicht angepasst.
Weiterhin werden auch die absoluten Artikelpreise und Berechnungswege nicht angepasst.

- Werden Artikelpreise brutto gepflegt, werden danach weiterhin die bisherigen Preise verwendet, jedoch mit geändertem Steuersatz.
- Werden Artikelpreise netto gepflegt, ändern sich die daraus errechneten Bruttopreise.

Passen Sie die Artikelpreise danach ggf. an.

In Multishopinstallationen (Enterprise Edition) können einzelne Subshops nicht von Änderungen ausgenommen werden.

Gibt es in Ihrem Shop reguläre Steuersätze mit 16% oder 5%, werden diese beim Zurücksetzen ebenfalls auf 19% bzw. 7% angehoben. Eine Unterscheidung, welcher Steuersatz vorab reduziert wurde, gibt es nicht. Diese Anpassung muss dann manuell durchgeführt werden. 

## Systemanforderung

- installierter OXID eShop in Version 6 und dessen Anforderungen

## Installation

Zur Innstallation werden noch keine Einstellungen geändert. Führen Sie diesen Befehl im Shophauptverzeichnis aus:


```
composer require d3/taxratesadjustment:"^2.0" --no-dev
```

## Ausführung

- Bitte führen Sie die Umstellung rechtzeitig vorab in einer Testinstallation durch und prüfen Ihren Shop, um Fehler im Livebetrieb zu vermeiden. Ändern Sie dazu in "./Models/reduceTaxRate.php sowie raiseTaxRate.php" die erlaubten Zeitfenster auf Ihren Testzeitpunkt.
- Legen Sie sich unbedingt vorab eine Datensicherung an. Die Software wird nach bestem Wissen erstellt. Durch die Vielzahl an möglichen Shopkonstellationen können  wir jedoch keine Gewährleistung für die richtige Ausführung und eventuelle Folgen übernehmen.

Richten Sie einen ersten Cronjob ein, der idealerweise am 01.07.2020 um 00:00 folgendes Script startet, um die Steuersätze zu senken. Alternativ führen Sie dieses Script zum passenden Zeitpunkt manuell aus:

```
[ Shoppfad ]/vendor/bin/reduceTaxRate
```

Richten Sie einen zweiten Cronjob ein, der idealerweise am 01.01.2021 um 00:00 folgendes Script startet, um die Steuersätze zurückzusetzen. Alternativ führen Sie dieses Script zum passenden Zeitpunkt manuell aus:


```
[ Shoppfad ]/vendor/bin/raiseTaxRate
```

Für die Einrichtung der Cronjobs kontakten Sie bei Fragen bitte Ihren Hostingprovider.

## Deinstallation

Entfernen Sie die eingerichteten Cronjobs nach den beiden Ausführungszeitpunkten, um versehentliche Auslösungen zu vermeiden.

Nach heutigem Stand werden die Scripte nach dem Zurücksetzen der Steuersätze nicht mehr benötigt. Dann kann es mit folgendem Befehl wieder aus der Installation entfernt werden:

```
composer remove d3/taxratesadjustment --no-dev
```

## Support

D3 Data Development (Inh. Thomas Dartsch)
E-Mail: support@shopmodule.com
