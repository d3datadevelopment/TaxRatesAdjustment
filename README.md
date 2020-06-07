# TaxRatesAdjustment

## Inhalte

### Was kann das Modul?

Dieses Modul stellt 2 Aufrufe bereit, die in Standardkonstellationen die MwSt.-Sätze anpasst, die in Deutschland zum 01.07.20 und zum 01.01.2021 geändert werden (Bestandteil des beschlossenen Corona-Konjunkturpaketes). 

Die Anpassung kann über entsprechende Cronjobs zeitgesteuert zum Stichtermin ausgeführt werden, ohne dass hierfür Ihre Anwesenheit erforderlich ist.

Die Scripte ändern:
- den im Shop eingestellten allgemeinen Steuersatz
- an den Artikeln hinterlegten speziellen Steuersätzen

- von 19% zu 16% und
- von 7% zu 5%
- sowie später auch zurück

Bei Multishopinstallationen (Enterprise Edition) können die zu aktualisierenden Subshops definiert werden.

Die Scripte prüfen anhand der Systemzeit mit kleinen Toleranzen (+/-3 Tage um das jeweilige Umstellungsdatum), ob die Veränderung ausgeführt werden darf. Damit wird verhindert, dass ein versehentliches Auslösen zur falschen Shopkonfiguration führt.

### Was kann das Modul nicht?

Sind im Shop noch an anderen Stellen Steuersätze hinterlegt, werden diese nicht angepasst.
Weiterhin werden auch die absoluten Artikelpreise und Berechnungswege nicht angepasst.

- Werden Artikelpreise brutto gepflegt und angezeigt, werden danach weiterhin die bisherigen Preise verwendet, jedoch mit geändertem Steuersatz.
- Werden Artikelpreise netto gepflegt und brutto angezeigt, ändern sich die daraus errechneten Bruttopreise.

Passen Sie die Artikelpreise danach ggf. an.

Gibt es in Ihrem Shop reguläre Steuersätze mit 16% oder 5%, werden diese beim Zurücksetzen ebenfalls auf 19% bzw. 7% angehoben. Eine Unterscheidung, welcher Steuersatz vorab reduziert wurde, gibt es nicht. Diese Anpassung muss dann manuell durchgeführt werden. 

## Systemanforderung

- installierter OXID eShop in Version 6 und dessen Anforderungen

## Installation

Zur Installation werden noch keine Einstellungen geändert. Führen Sie diesen Befehl im Shophauptverzeichnis aus:


```
composer require d3/taxratesadjustment:"^2.0" --no-dev
```

## Ausführung

- Bitte führen Sie die Umstellung rechtzeitig vorab in einer Testinstallation durch und prüfen Ihren Shop, um Fehler im Livebetrieb zu vermeiden. Zum Übergehen der Datumsprüfung können Sie den folgenden Befehlen einfach den Parameter `-d` anhängen: z.B. `[ Shoppfad ]/vendor/bin/reduceTaxRate -d`. Für den Livebetrieb soll der Parameter nicht verwendet werden.
- Legen Sie sich unbedingt vor jeder Ausführung eine Datensicherung an. Die Software wird nach bestem Wissen erstellt. Durch die Vielzahl an möglichen Shopkonstellationen können  wir jedoch keine Gewährleistung für die richtige Ausführung und eventuelle Folgen übernehmen.

Richten Sie einen ersten Cronjob ein, der idealerweise am 01.07.2020 um 00:00 folgendes Script startet, um die Steuersätze zu senken. Alternativ führen Sie dieses Script zum passenden Zeitpunkt manuell aus:

```
[ Shoppfad ]/vendor/bin/reduceTaxRate
```

Richten Sie einen zweiten Cronjob ein, der idealerweise am 01.01.2021 um 00:00 folgendes Script startet, um die Steuersätze zurückzusetzen. Alternativ führen Sie dieses Script zum passenden Zeitpunkt manuell aus:


```
[ Shoppfad ]/vendor/bin/raiseTaxRate
```

Für die Einrichtung der Cronjobs kontakten Sie bei Fragen bitte Ihren Hostingprovider.

Püfen Sie nach Ausführung der Scripte bitte zeitnah Ihren Shop auf richtige Funktion.

Zu Definition, welche Subshops bearbeitet werden sollen, kann der Parameter `-s 1,3,4` verwendet werden. Setzen Sie statt der `1,3,4` eine kommagetrennte Liste Ihrer gewünschten Shop-IDs ein. Ohne Angabe des Filters werden alle vorhandenen Subshops bearbeitet.

## Deinstallation

Entfernen Sie die eingerichteten Cronjobs nach den beiden Ausführungszeitpunkten, um versehentliche Auslösungen zu vermeiden.

Nach heutigem Stand werden die Scripte nach dem Zurücksetzen der Steuersätze nicht mehr benötigt. Dann kann dieses Paket mit folgendem Befehl wieder aus der Installation entfernt werden:

```
composer remove d3/taxratesadjustment --no-dev
```

## Support

D3 Data Development (Inh. Thomas Dartsch)

E-Mail: support@shopmodule.com
