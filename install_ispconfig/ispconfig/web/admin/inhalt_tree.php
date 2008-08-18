<?
include("../../lib/config.inc.php");
include("../../lib/session.inc.php");
?>
<html>

<head>
<!--
Version 1.3 (C) 1999 Ansgar Federhen/Rheinbreitbach.net

Dieses Script darf unter folgenden Voraussetzungen frei verwendet und weitergegeben werden:
- Du schickst eine E-Mail an den Autor (js-menue@rheinbreitbach.net) mit der URL der Web-Site,
  auf der JS-Menü eingesetzt wird und
- Du übernimmst den Copyright-Vermerk von JS-Menü in Deine Datei.
Siehe auch die in der gepackten Datei enthaltene Liesmich.txt.   Danke.

e-mail: js-menue@rheinbreitbach.net    web: http://Rheinbreitbach.net/js-menue
//-->

<title></title>
<script LANGUAGE="JavaScript" TYPE="text/javascript">
<!-- Anfang des JavaScript-Codes, Code vor alten Browsern verstecken -->

<!-- start() - JAVASCRIPT-FUNKTION - diese Funktion wird beim Laden der HTML-Datei aufgerufen und ruft ihrerseits die Funktionen zum Laden der Menüdaten sowie zum Erstellen des Menüs und zum Erstellen des Frames 'klappFrame' auf. -->

function start() {
        ladeDaten();
        maleMenu()}

<!-- maleMenu() - JAVASCRIPT-FUNKTION - diese Funktion erstellt die Menüstruktur, beginnend mit dem Hauptverzeichnis und dann den darin enthaltenen Unterverzeichnissen. -->

function maleMenu() {
        var menuFrame = self.frames["menuFrame"];
        ausgabeFrame = menuFrame.window.document;
        ausgabeFrame.open("text/html");
        <!-- Bitte die folgende Zeile als Copyright-Hinweis nicht loeschen //-->
        ausgabeFrame.write("<HTML>\n<HEAD>\n\n");
        ausgabeFrame.write("<STYLE>\n<!--\nA {text-decoration: none}\nA {color: " + linkFarbe + "}\nA:hover {color: " + aLinkFarbe + "}\nBODY{ scrollbar-arrow-color: white\;\nscrollbar-base-color: <?echo $session_nav_hcolour?>   }\n-->\n</STYLE>\n");
        <?if( @is_file("tree.css")) { ?> ausgabeFrame.write("<link href='tree.css' rel='stylesheet' type='text/css'>"); <? } ?>
        ausgabeFrame.write("</HEAD>\n<BODY BGCOLOR=\"" + hintergrundFarbe + "\" BACKGROUND=\"" + hintergrundBild + "\" LINK=\"" + linkFarbe + "\" ALINK=\"" + aLinkFarbe + "\" VLINK=\"" + bLinkFarbe + "\" topmargin=\"10\" leftmargin=\"10\">\n");
        ausgabeFrame.write("<FONT FACE=\"" + MenuSchrift + "\" SIZE=" + MenuSchriftGroesse + " COLOR=\"" + textFarbe + "\">\n");
        ausgabeFrame.write(prefixHTML);
        if (MenuFett == "1") {
                ausgabeFrame.write("<NOBR><b>\n")}
        else {ausgabeFrame.write("<NOBR>\n")}
        if (menuDaten[1].ziel == "") {
                var zielFrame = standardZielFrame}
        else {var zielFrame = menuDaten[1].ziel}
        if (menuDaten[1].icon == "") {
                var bildBezeichnung = standardBildURL + 'globus-' + hintergrundStil + '.gif'}
        else {bildBezeichnung = standardBildURL + menuDaten[1].icon}
        ausgabeFrame.write("<A HREF=\"" + menuDaten[1].url + "\" TARGET=\"" + zielFrame + "\" onMouseOver=\"window.status='" + menuDaten[1].rootstat + "'; return true\"><IMG SRC=\"" + bildBezeichnung + "\" WIDTH=16 HEIGHT=16 ALIGN=TOP BORDER=0 ALT=\"" + menuDaten[1].rootstat + "\">&nbsp;<B>" + menuDaten[1].text + "</B></A><BR>\n");
        maleVerz("root","");
        if (MenuFett == "1") {
                ausgabeFrame.write("</b></NOBR>\n")}
        else {ausgabeFrame.write("</NOBR>\n")}
        ausgabeFrame.write(suffixHTML + "\n</FONT>\n</BODY>\n</HTML>");
        ausgabeFrame.close();
        window.status="ISPConfig" }

<!-- maleVerz() - JAVASCRIPT-FUNKTION - Diese Funktion wird von der Funktion 'maleMenu()' aufgerufen, um alle sichtbaren Einträge im Verzeichnis zu erstellen. -->

function maleVerz(startEintrag,zweigEintrag) {
        VerzAuf = '<? echo htmlspecialchars($go_api->lng("Verzeichnis öffnen"))?>'
    VerzZu = '<? echo htmlspecialchars($go_api->lng("Verzeichnis schliessen"))?>'
        var nachfolger = sucheNachfolgerVon(startEintrag);
        var aktuellerIndex = 1;
        while (aktuellerIndex <= nachfolger.laenge) {
                ausgabeFrame.write(zweigEintrag);
                if (nachfolger[aktuellerIndex].typ == 'link') {
                        if (nachfolger[aktuellerIndex].icon == "") {var bildBezeichnung = standardBildURL + standardLinkBild}
                        else {var bildBezeichnung = standardBildURL + nachfolger[aktuellerIndex].icon}
                        if (nachfolger[aktuellerIndex].ziel == "") {var zielFrame = standardZielFrame}
                        else {var zielFrame = nachfolger[aktuellerIndex].ziel}
                        if (aktuellerIndex != nachfolger.laenge) {
                                if (nachfolger[aktuellerIndex].icon == "leer.gif") {
                                ausgabeFrame.write("<IMG SRC=\"" + standardBildURL + "stamm-" + hintergrundStil + ".gif\" WIDTH=19 HEIGHT=16 ALIGN=TOP>")}
                                else {ausgabeFrame.write("<IMG SRC=\"" + standardBildURL + "zw" + hintergrundStil + ".gif\" WIDTH=19 HEIGHT=16 ALIGN=TOP>")}}
                        else {
                                if (nachfolger[aktuellerIndex].icon == "leer.gif") {
                                ausgabeFrame.write("<IMG SRC=\"" + standardBildURL + "end" + hintergrundStil + ".gif\" WIDTH=19 HEIGHT=16 ALIGN=TOP>")}
                                else {ausgabeFrame.write("<IMG SRC=\"" + standardBildURL + "zwe" + hintergrundStil + ".gif\" WIDTH=19 HEIGHT=16 ALIGN=TOP>")}}
                        if (nachfolger[aktuellerIndex].linkstat == "") {
                                var linkstat = nachfolger[aktuellerIndex].url}
                        else {var linkstat = nachfolger[aktuellerIndex].linkstat}

                        <!-- In der folgenden Zeile werden die Bilder gesetzt -->

                        ausgabeFrame.write("<A HREF=\"" + nachfolger[aktuellerIndex].url + "\" TARGET=\"" + zielFrame + "\" onMouseOver=\"window.status='" + linkstat + "'; return true\"><IMG SRC=\"" + bildBezeichnung + "\" WIDTH=16 HEIGHT=16 ALIGN=TOP BORDER=0 ALT=\"" + linkstat + "\">&nbsp;</A><A HREF=\"" + nachfolger[aktuellerIndex].url + "\" TARGET=\"" + zielFrame + "\" onMouseOver=\"window.status='" + linkstat + "'; return true\">&nbsp;" + nachfolger[aktuellerIndex].text + "</A><BR>\n")}
                else {var neuerZweig = zweigEintrag;
                        if (nachfolger[aktuellerIndex].iconZu == "") {
                                var iconZu = "vzzu-" + hintergrundStil + ".gif"}
                        else {var iconZu = nachfolger[aktuellerIndex].iconZu}
                        if (nachfolger[aktuellerIndex].iconOffen == "") {
                                var iconOffen = "vzauf-" + hintergrundStil + ".gif"}
                        else {var iconOffen = nachfolger[aktuellerIndex].iconOffen}
                        if (aktuellerIndex != nachfolger.laenge) {
                                if (nachfolger[aktuellerIndex].offen == 0) {
                                        ausgabeFrame.write("<A HREF=\"javascript:parent.aufzuVerz('" + nachfolger[aktuellerIndex].name + "',1)\" onMouseOver=\"window.status='" + VerzAuf + "'; return true\"><IMG SRC=\"" + standardBildURL + "zwauf-" + hintergrundStil + ".gif\" WIDTH=19 HEIGHT=16 ALT=\"" + VerzAuf + "\" ALIGN=TOP BORDER=0>")
                                        ausgabeFrame.write("<IMG SRC=\"" + standardBildURL + iconZu + "\" WIDTH=16 HEIGHT=16 ALT=\"" + VerzAuf + "\" ALIGN=TOP BORDER=0>&nbsp;</A>&nbsp;<A HREF=\"javascript:parent.aufzuVerz('" + nachfolger[aktuellerIndex].name + "',1)\" onMouseOver=\"window.status='" + VerzAuf + "'; return true\">&nbsp;" + nachfolger[aktuellerIndex].text + "</A><BR>\n")}
                                else {ausgabeFrame.write("<A HREF=\"javascript:parent.aufzuVerz('" + nachfolger[aktuellerIndex].name + "',0)\" onMouseOver=\"window.status='" + VerzZu + "'; return true\"><IMG SRC=\"" + standardBildURL + "zwzu-" + hintergrundStil + ".gif\" WIDTH=19 HEIGHT=16 ALT=\"" + VerzZu + "\" ALIGN=TOP BORDER=0>");
                                        ausgabeFrame.write("<IMG SRC=\"" + standardBildURL + iconOffen + "\" WIDTH=16 HEIGHT=16 ALT=\"" + VerzZu + "\" ALIGN=TOP BORDER=0>&nbsp;</A>&nbsp;<A HREF=\"javascript:parent.aufzuVerz('" + nachfolger[aktuellerIndex].name + "',0)\" onMouseOver=\"window.status='" + VerzZu + "'; return true\">&nbsp;" + nachfolger[aktuellerIndex].text + "</A><BR>\n");
                                        neuerZweig = neuerZweig + "<IMG SRC=\"" + standardBildURL + "stamm-" + hintergrundStil + ".gif\" WIDTH=19 HEIGHT=16 ALIGN=TOP>";
                                        maleVerz(nachfolger[aktuellerIndex].name,neuerZweig)}}
                        else {
                                if (nachfolger[aktuellerIndex].offen == 0) {
                                        ausgabeFrame.write("<A HREF=\"javascript:parent.aufzuVerz('" + nachfolger[aktuellerIndex].name + "',1)\" onMouseOver=\"window.status='" + VerzAuf + "'; return true\"><IMG SRC=\"" + standardBildURL + "zwauf-e" + hintergrundStil + ".gif\" WIDTH=19 HEIGHT=16 ALT=\"" + VerzAuf + "\" ALIGN=TOP BORDER=0>")
                                        ausgabeFrame.write("<IMG SRC=\"" + standardBildURL + iconZu + "\" WIDTH=16 HEIGHT=16 ALT=\"" + VerzAuf + "\" ALIGN=TOP BORDER=0>&nbsp;</A>&nbsp;<A HREF=\"javascript:parent.aufzuVerz('" + nachfolger[aktuellerIndex].name + "',1)\" onMouseOver=\"window.status='" + VerzAuf + "'; return true\">&nbsp;" + nachfolger[aktuellerIndex].text + "</A><BR>\n")}
                                else {ausgabeFrame.write("<A HREF=\"javascript:parent.aufzuVerz('" + nachfolger[aktuellerIndex].name + "',0)\" onMouseOver=\"window.status='" + VerzZu + "'; return true\"><IMG SRC=\"" + standardBildURL + "zwzu-e" + hintergrundStil + ".gif\" WIDTH=19 HEIGHT=16 ALT=\"" + VerzZu + "\" ALIGN=TOP BORDER=0>");
                                        ausgabeFrame.write("<IMG SRC=\"" + standardBildURL + iconOffen + "\" WIDTH=16 HEIGHT=16 ALT=\"" + VerzZu + "\" ALIGN=TOP BORDER=0>&nbsp;</A>&nbsp;<A HREF=\"javascript:parent.aufzuVerz('" + nachfolger[aktuellerIndex].name + "',0)\" onMouseOver=\"window.status='" + VerzZu + "'; return true\">&nbsp;" + nachfolger[aktuellerIndex].text + "</A><BR>\n");
                                        neuerZweig = neuerZweig + "<IMG SRC=\"" + standardBildURL + "leer.gif\" WIDTH=19 HEIGHT=16 ALIGN=TOP>";
                                        maleVerz(nachfolger[aktuellerIndex].name,neuerZweig)}}}
                aktuellerIndex++;}}


<!-- neumalen() - JAVASCRIPT-FUNKTION - diese Funktion wird in der Frameset-Definition bei Änderung der Fenstergröße aufgerufen (nur Netscape) und lädt das Menü neu -->

function neumalen() {
    maleMenu()}

<!-- aufzuVerz() - JAVASCRIPT-FUNKTION - diese Funktion öffnet bzw. schließt ein Verzeichnis. -->

function aufzuVerz(name,status) {
        var eintragsIndex = indexVonEintrag(name);
        menuDaten[eintragsIndex].offen = status;
        timeOutname = setTimeout("maleMenu()",100);
        if ((status == 1) && (menuDaten[eintragsIndex].url != '')) {frames["seiteFrame"].location.href=menuDaten[eintragsIndex].url}}

<!-- allesaufzu() - JAVASCRIPT-FUNKTION - diese Funktion öffnet/schließt alle Verzeichnisse. -->

function allesaufzu(was) {
        var aktuellerIndex = 1;
        while (aktuellerIndex <= menuDaten.laenge) {
                {menuDaten[aktuellerIndex].offen = was}
                aktuellerIndex++}
         maleMenu()}

<!-- indexVonEintrag() - JAVASCRIPT-FUNKTION - sucht einen Eintrag in 'menuDaten' anhand eines Namens -->

function indexVonEintrag(name) {
        var aktuellerIndex = 1;
        while (aktuellerIndex <= menuDaten.laenge) {
                if ((menuDaten[aktuellerIndex].typ == 'root') || (menuDaten[aktuellerIndex].typ == 'verzeichnis')) {
                        if (menuDaten[aktuellerIndex].name == name) {
                                return aktuellerIndex}}
                aktuellerIndex++}
        return -1}

<!-- sucheNachfolgerVon() - JAVASCRIPT-FUNKTION - erzeugt eine Sammlung aller Einträge, die unmittelbar unter dem aktuellen Eintrag liegen -->

function sucheNachfolgerVon(eintrag) {
        var nachfolger = new Sammlung();
        var aktuellerIndex = 1;
        while (aktuellerIndex <= menuDaten.laenge) {
                if ((menuDaten[aktuellerIndex].typ == 'verzeichnis') || (menuDaten[aktuellerIndex].typ == 'link')) {
                        if (menuDaten[aktuellerIndex].vorfahr == eintrag) {
                                nachfolger.neu(menuDaten[aktuellerIndex])}}
                aktuellerIndex++}
        return nachfolger}

<!-- Sammlung() - OBJEKT - ein dynamisches Objekt, welches Daten speichert, ähnlich einem Array. -->

function Sammlung() {
        this.laenge = 0;
        this.neu = neu;
        return this}

<!-- neu() - METHODE des Objekts Sammlung - fügt dem Objekt Sammlung neue Daten(sätze) hinzu. -->

function neu(object) {
        this.laenge++;
        this[this.laenge] = object}

<!-- HauptverzeichnisEintrag() - OBJEKT - steht für den Hauptverzeichnis-Eintrag im Menü. -->

function HauptverzeichnisEintrag(name,text,url,ziel,icon,rootstat) {
        this.name = name;
        this.text = text;
        this.url = url;
        this.ziel = ziel;
        this.icon = icon;
        this.typ = 'root';
        this.rootstat = rootstat;
        return this}

<!-- VerzEintrag() - OBJEKT - steht für einen Verzeichnis-Eintrag im Menü. -->

function VerzEintrag(name,vorfahr,text,iconZu,iconOffen,url) {
        this.name = name;
        this.vorfahr = vorfahr;
        this.text = text;
        this.iconZu = iconZu;
        this.iconOffen = iconOffen;
        this.typ = 'verzeichnis';
        this.offen = 0;
        this.url = url;
        return this}

<!-- LinkEintrag() - OBJEKT - steht für einen Link-Eintrag im Menü. -->

function LinkEintrag(vorfahr,text,url,ziel,icon,linkstat,art,name) {
        this.vorfahr = vorfahr;
        this.text = text;
        this.url = url;
        this.ziel = ziel;
        this.icon = icon;
        this.typ = 'link';
        this.art = art;
        this.name = name;
        this.linkstat = linkstat;
        return this}

<!-- ladeDaten() - JAVASCRIPT-FUNKTION - die in diesem Bereich vorhandenen benutzerdefinierten Daten und Variablen werden mit dieser Funktion eingelesen. -->

function ladeDaten() {
        menuDaten = new Sammlung();

<!--
// Menü-Struktur-Definitionen: hier wird die Menü-Struktur definiert
// Syntax/Schreibweise:

// Hauptverzeichnis-Eintrag:
// menuDaten.neu(new HauptverzeichnisEintrag('<NAME>','<TEXT>','<URL>','<ZIEL>','<ICON>','<STATUSTEXT>'));
// Hinweis: Es darf nur EIN Hauptverzeichniseintrag existieren und er MUSS an ERSTER Stelle stehen;
//          <STATUSTEXT>, <ZIEL> und <ICON> koennen leer bleiben, es werden dann Standards benutzt.

// Verzeichnis-Eintrag:
// menuDaten.neu(new VerzEintrag('<NAME>','<ÜBERGEORDNETER NAME>','<TEXT>','<ICON ZU>','<ICON OFFEN>','<URL>'));
// Hinweis: Verzeichnis-Einträge MÜSSEN einen gültigen Wert bei 'ÜBERGEORDNETER NAME' (=Name des übergeordneten
//          Verzeichnisses) haben, sie SOLLTEN untergeordnete Einträge haben (macht sonst keinen Sinn);
//          <ICON ZU> , <ICON OFFEN> und <URL> können leer bleiben, es werden dann Standards benutzt.

// Link-Eintrag:
// menuDaten.neu(new LinkEintrag('<ÜBERGEORDNETER NAME>','<TEXT>','<URL>','<ZIEL>','<ICON>',<STATUSTEXT>'));
// Hinweis: <ZIEL> und <ICON> können leer bleiben, es werden dann die im Bereich 'Benutzerdefinierte
//          Variablen' definierten Standards benutzt. <STATUSTEXT> kann leer bleiben, es wird dann der bei
//          <URL> eingesetzte Wert übernommen.

// Für weitere Informationen sieh in der JS-Menü-Dokumentation nach.
//-->

menuDaten.neu(new HauptverzeichnisEintrag('root','<? echo $go_api->lng("Administration")?>','frame_start.php?<? echo $session?>&tpl=start','','','Administration'));


<?


while (list($key, $val) = each($go_info["session"]["modules"])){
if($val["type"] == "p") {
$inhalt = SERVER_ROOT . DIR_TRENNER . "web" . DIR_TRENNER .$val["path"] . DIR_TRENNER.'lib'.DIR_TRENNER.'admin.inc.php';
}
if(@is_file($inhalt)) {
include($inhalt);
}
$inhalt = "";

}

echo "menuDaten.neu(new LinkEintrag('root','".$go_api->lng("Papierkorb")."','../multidoc/edit/papierkorb.php?gid=$gid&amp;$session','seiteFrame','papierkorb.gif','Papierkorb','i','$session'));\n";

?>



   hintergrundStil = 0;                     // 0 bei Verwendung eines hellen Hintergrunds, 1 bei Verwendung eines dunklen Hintergrunds (bestimmt, ob dunkle oder helle Icons verwendet werden)
  hintergrundFarbe = '<?echo $session_bgcolour?>';             // bestimmt die Hintergrundfarbe des Menüs, Farbtabelle siehe Dokumentation
         textFarbe = '<?echo $session_text_colour?>';             // bestimmt die Farbe von Text, der nicht mit einem Link hinterlegt ist
         linkFarbe = '#000000';             // bestimmt die Farbe von Text, der mit einem Link hinterlegt ist
        aLinkFarbe = '#FF0000';             // bestimmt die Farbe des gerade aktiven TextLinks (auch "hover-Effekt", allerdings nur IE 4.x und hoeher)
        bLinkFarbe = '#880088';             // bestimmt die Farbe der bereits besuchten TextLinks
   hintergrundBild = '';                    // hier ggf. den kompletten Pfad für eine gif- oder jpeg-Grafik als Hintergrundbild angeben, falls kein Hintergrundbild angegeben wird, 'leer.gif stehen lassen!
 standardZielFrame = 'seiteFrame';          // Name des Frames, in den standardmäßig alle Links geladen werden
   standardBildURL = '<?echo "../" . $session_design_path . "/icons/"?>';             // Pfad oder URL, wo sich die von JS-Menü benötigten Icons befinden
  standardLinkBild = 'globus.gif';          // Name des standardmäßig benutzten Icons für Links
       MenuSchrift = 'Arial,MS Sans Serif,Helvetica';  // die für das Menü benutzte(n) Schriftart(en)
MenuSchriftGroesse = '1';                   // die Schriftgröße - nicht zu groß (1 oder 2)!
          MenuFett = '0';                   // Falls das Menü in Fettschrift ausgegeben werden soll, 1 angeben, sonst 0

<!-- Zusätzlicher HTML-Code: -->

        prefixHTML = "";
        suffixHTML = "";
}
//-->
</script>
<noscript>
<meta http-equiv="REFRESH" content="1; URL=nojsindx.htm">
</noscript>

</head>

    <frameset cols="*,5" onload="start();allesaufzu(1)" framespacing="0" border="0" frameborder="0">
      <frame name="menuFrame" scrolling="auto" src="vorladen.php?<?echo $session?>" marginwidth="10" marginheight="10" target="_self">
      <frame name="sFrame" src="placeholder.php?<?echo $session?>" scrolling="no" target="_self" marginwidth="22" marginheight="26" noresize>
      </frameset>
      <noframes>
      <body>

      <p>Diese Seite verwendet Frames. Frames werden von Ihrem Browser aber nicht
      unterstützt.</p>

      </body>
      </noframes>


</html>


















































































