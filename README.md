# TaxRatesAdjustment / Anpassung der MwSt.-Sätze

## Funktionsumfang

### Was kann das Modul?

Dieses Modul stellt 2 Aufrufe bereit, die in Standardkonstellationen die MwSt.-Sätze anpasst, die in Deutschland zum 01.07.20 und zum 01.01.2021 geändert werden (Bestandteil des beschlossenen Corona-Konjunkturpaketes). 

Die Anpassung kann über entsprechende Cronjobs zeitgesteuert zum Stichtermin ausgeführt werden, ohne dass hierfür Ihre Anwesenheit erforderlich ist.

Die Scripte ändern:
- den im Shop eingestellten allgemeinen Steuersatz
- an den Artikeln hinterlegte spezielle Steuersätze

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

- installierter OXID eShop in Version 4.10 (CE, PE) oder 5.3 (EE) und dessen Anforderungen

Ein Einsatz in älteren Shopversionen ist vor dem Livebetrieb zwingend auf Verwendbarkeit zu testen.

## Installation

Kopieren Sie den Inhalt des `copy_this`-Ordners in Ihren Shopordner. Achten Sie darauf, auch die verborgene .htaccess mitzukopieren, dass die Scripte nicht über den Browser von außen erreichbar sind.

Vergeben Sie den beiden Scripten im Ordner `_taxRates/bin` Ausführungsrechte.

## Ausführung

- Bitte führen Sie die Umstellung rechtzeitig vorab in einer Testinstallation durch und prüfen Ihren Shop, um Fehler im Livebetrieb zu vermeiden. Zum Übergehen der Datumsprüfung können Sie den folgenden Befehlen einfach den Parameter `-d` anhängen: z.B. `[ Shoppfad ]/_taxRates/bin/reduceTaxRate -d`. Für den Livebetrieb soll der Parameter nicht verwendet werden.
- Legen Sie sich unbedingt vor jeder Ausführung eine Datensicherung an. Die Software wird nach bestem Wissen erstellt. Durch die Vielzahl an möglichen Shopkonstellationen können  wir jedoch keine Gewährleistung für die richtige Ausführung und eventuelle Folgen übernehmen.

Richten Sie einen ersten Cronjob ein, der idealerweise am 01.07.2020 um 00:00 folgendes Script startet, um die Steuersätze zu senken. Alternativ führen Sie dieses Script zum passenden Zeitpunkt manuell auf der Serverkonsole aus:

```
[ Shoppfad ]/_taxRates/bin/reduceTaxRate
```

Richten Sie einen zweiten Cronjob ein, der idealerweise am 01.01.2021 um 00:00 folgendes Script startet, um die Steuersätze zurückzusetzen. Alternativ führen Sie dieses Script zum passenden Zeitpunkt manuell auf der Serverkonsole aus:

```
[ Shoppfad ]/_taxRates/bin/raiseTaxRate
```

Bei Fragen zur Einrichtung der Cronjobs kontaktieren Sie bitte Ihren Hostingprovider.

Prüfen Sie nach Ausführung der Scripte Ihren Shop bitte zeitnah auf richtige Funktion.

Zur Definition, welche Subshops bearbeitet werden sollen, kann der Parameter `-s 1,3,4` verwendet werden. Setzen Sie statt der `1,3,4` eine kommagetrennte Liste Ihrer gewünschten Shop-IDs ein. Ohne Angabe des Filters werden alle vorhandenen Subshops bearbeitet.

Sollen die Scripte über eine PHP-Version gestartet werden, die nicht als Standard am Server definiert ist, setzen Sie den Pfad zur passenden PHP-Version vor den Scriptaufruf:

```
/usr/local/php5.6/bin/php [ Shoppfad ]...
```

## Deinstallation

Entfernen Sie die eingerichteten Cronjobs nach den beiden Ausführungszeitpunkten, um versehentliche spätere Auslösungen zu vermeiden.

Nach heutigem Stand werden die Scripte nach dem Zurücksetzen der Steuersätze nicht mehr benötigt. Dann kann der Ordner `_taxRates` wieder komplett aus der Installation entfernt werden.

## Änderungshistorie

- 1.0.0: 
  - scriptgesteuertes Ändern der Steuersätze (generell und artikelspezifisch) reduzierend und erhöhend für jeden Subshop
  - per Argument übersteuerbare Ausführungsbeschränkung
  - Subshopfilter eingefügt
  
- 1.0.1
  - falsche Konvertierung der ShopId entfernt
  
## Support

- D3 Data Development (Inh. Thomas Dartsch)
- Home: [www.d3data.de](https://www.d3data.de)
- E-Mail: support@shopmodule.com
