# TaxRatesAdjustment / Anpassung der MwSt.-Sätze

## eingestellte Modulpflege

Dieses Modul wurde für die Steueranpassung in Deutschland im Jahr 2020 erstellt. Da diese Ereignisse vergangen sind, wird dieses Modul nicht mehr weiterentwickelt und auch nicht mehr unterstützt. 

## Funktionsumfang

### Was kann das Modul?

Dieses Modul stellt 2 Aufrufe bereit, die in Standardkonstellationen die MwSt.-Sätze anpasst, die in Deutschland zum 01.07.20 und zum 01.01.2021 geändert werden (Bestandteil des beschlossenen Corona-Konjunkturpaketes). 
Weiterhin können über 2 weitere Aufrufe die Artikelpreise passend reduziert bzw. erhöht werden.

Die Anpassung kann über entsprechende Cronjobs zeitgesteuert zum Stichtermin ausgeführt werden, ohne dass hierfür Ihre Anwesenheit erforderlich ist.

Die Steuerscripte ändern:
- den im Shop eingestellten allgemeinen Steuersatz
- an den Artikeln hinterlegte spezielle Steuersätze

- von 19% zu 16% und
- von 7% zu 5%
- sowie später auch zurück

Die Steuerscripte ändern:
- den Standardpreis der Artikel
- den UVP-Preis der Artikel
- den Varminpreis an Elternartikeln (der Variantenpreis selbst wird schon mit dem Standardpreis geändert)
- den Varmaxpreis an Elternartikeln (der Variantenpreis selbst wird schon mit dem Standardpreis geändert)

- von 19% zu 16% und
- von 7% zu 5%
- sowie später auch zurück

Berücksichtigt werden artikelabhängige Steuersätze sowie auch der generelle Steuersatz des Shops. Bei den Varianten-MinPreisen und Max-Preisen wird der Steuersatz des Elternartikels zugrunde gelegt. 
Weicht der Steuersatz der Varianten vom Elternartikel ab, muss dies manuell nachgearbeitet werden.

Bei Multishopinstallationen (Enterprise Edition) können die zu aktualisierenden Subshops definiert werden.

Die Scripte prüfen anhand der Systemzeit mit kleinen Toleranzen (+/-3 Tage um das jeweilige Umstellungsdatum), ob die Veränderung ausgeführt werden darf. Damit wird verhindert, dass ein versehentliches Auslösen zur falschen Shopkonfiguration führt.

### Was kann das Modul nicht?

Sind im Shop noch an anderen Stellen Steuersätze hinterlegt, werden diese nicht angepasst.
Weiterhin werden auch die absoluten Artikelpreise und Berechnungswege nicht angepasst.

- Werden Artikelpreise brutto gepflegt und angezeigt, werden danach weiterhin die bisherigen Preise verwendet, jedoch mit geändertem Steuersatz.
- Werden Artikelpreise netto gepflegt und brutto angezeigt, ändern sich die daraus errechneten Bruttopreise.

Für die Preisanpassungen stehen Ihnen die entsprechenden Scripte im Modul zur Verfügung.

Beachten Sie bei der Preisanpassung speziell die Artikel, die einer Preisbindung unterliegen.

Artikel, die von der Steuersenkung ausgenommen sind (z.B. Tabakwaren) können hierbei nicht berücksichtigt werden und erfordern eine manuelle Nachbearbeitung.

Die Preise werden immer auf dem im Shop vorliegenden Preis angewandt. Hierbei kann es durchaus zu Rundungsungenauigkeiten kommen.

Gibt es in Ihrem Shop reguläre Steuersätze mit 16% oder 5%, werden diese beim Zurücksetzen ebenfalls auf 19% bzw. 7% angehoben. Eine Unterscheidung, welcher Steuersatz vorab reduziert wurde, gibt es nicht. Diese Anpassung muss dann manuell durchgeführt werden. 

## Systemanforderung

- installierter OXID eShop in Version 6 und dessen Anforderungen

## Installation / Update

Während der Installation werden noch keine Shopeinstellungen geändert. Führen Sie diesen Befehl im Shophauptverzeichnis aus:

```
composer require d3/taxratesadjustment:"dev-rel_2.x_articlePrices" --update-no-dev
```

## Ausführung

- Bitte führen Sie die Umstellung rechtzeitig vorab in einer Testinstallation durch und prüfen Ihren Shop, um Fehler im Livebetrieb zu vermeiden. Zum Übergehen der Datumsprüfung können Sie den folgenden Befehlen einfach den Parameter `-d` anhängen: z.B. `[ Shoppfad ]/vendor/bin/reduceTaxRate -d`. Für den Livebetrieb soll der Parameter nicht verwendet werden.
- Legen Sie sich unbedingt vor jeder Ausführung eine Datensicherung an. Die Software wird nach bestem Wissen erstellt. Durch die Vielzahl an möglichen Shopkonstellationen können  wir jedoch keine Gewährleistung für die richtige Ausführung und eventuelle Folgen übernehmen.

Richten Sie einen ersten Cronjob ein, der idealerweise am 01.07.2020 um 00:00 folgendes Script startet, um die Steuersätze zu senken. Alternativ führen Sie dieses Script zum passenden Zeitpunkt auf der Serverkonsole manuell aus:

```
[ Shoppfad ]/vendor/bin/reduceTaxRate
```

Richten Sie einen zweiten Cronjob ein, der idealerweise am 01.01.2021 um 00:00 folgendes Script startet, um die Steuersätze zurückzusetzen. Alternativ führen Sie dieses Script zum passenden Zeitpunkt auf der Serverkonsole manuell aus:

```
[ Shoppfad ]/vendor/bin/raiseTaxRate
```

Nutzen Sie für die Preisanpassungen die folgenden Scripte als Cronjob zum passenden Moment:

um die Artikelpreise zu senken::

```
[ Shoppfad ]/vendor/bin/reduceArticlePrices
```

um die Artikelpreise zurückzusetzen:

```
[ Shoppfad ]/vendor/bin/raiseArtikelPrices
```

Führen Sie die Preisanpassungsscripte nur ein einziges Mal aus, da die Preise sonst mehrfach gesenkt / erhöht werden.

Bei Fragen zur Einrichtung der Cronjobs kontaktieren Sie bitte Ihren Hostingprovider.

Prüfen Sie nach Ausführung der Scripte Ihren Shop bitte zeitnah auf richtige Funktion.

Zur Definition, welche Subshops bearbeitet werden sollen, kann der Parameter `-s 1,3,4` verwendet werden. Setzen Sie statt der `1,3,4` eine kommagetrennte Liste Ihrer gewünschten Shop-IDs ein. Ohne Angabe des Filters werden alle vorhandenen Subshops bearbeitet.

Sollen die Scripte über eine PHP-Version gestartet werden, die nicht als Standard am Server definiert ist, ändern Sie den Pfad zur passenden PHP-Version in den beiden bin-Scripten.

## Deinstallation

Entfernen Sie die eingerichteten Cronjobs nach den beiden Ausführungszeitpunkten, um versehentliche spätere Auslösungen zu vermeiden.

Nach heutigem Stand werden die Scripte nach dem Zurücksetzen der Steuersätze nicht mehr benötigt. Dann kann dieses Paket mit folgendem Befehl wieder aus der Installation entfernt werden:

```
composer remove d3/taxratesadjustment --update-no-dev
```

## Änderungshistorie

- 2.0.0: 
  - scriptgesteuertes Ändern der Steuersätze (generell und artikelspezifisch) reduzierend und erhöhend für jeden Subshop
  - per Argument übersteuerbare Ausführungsbeschränkung
- 2.1.0
  - Subshopfilter eingefügt
- 2.1.1
  - Composer Command korrigiert
- 2.1.2
  - PHP-Versionshinweis angepasst
- unreleased
  - Preisanpassungsscripte eingefügt
  
## Support

- D3 Data Development (Inh. Thomas Dartsch)
- Home: [www.d3data.de](https://www.d3data.de)
- E-Mail: support@shopmodule.com
