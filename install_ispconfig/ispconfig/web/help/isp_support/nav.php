<?

?>
	<!--
	// Definition der Menüstruktur:

	// Ordner Eintrag:
	// menuDaten.neu(new VerzEintrag('[Name]','[Name des Ordners]','[Titel]','[Icon zu]','[Icon offen]','[Link]'));
	//          [Icon zu] , [Icon offen] und [Link] müssen nicht gesetzt werden. In diesem Fall werden die Standardwerte verwendet.
	// Soll der Ordner in der untersten Ebene erscheinen, so ist als Name des Ordners "root" anzugeben.
	
	// Beispiel:
	// menuDaten.neu(new VerzEintrag('ISPConfig','root','ISPConfig Support','','',''));

	// Dokument Eintrag:
	// menuDaten.neu(new LinkEintrag('[Name des Ordners]','[Titel]','[Link zum Dokument]','[Target]','[Icon]',[Status Text]',[Intern1],[Intern2]));
	// Hinweis: [Target], [Icon] und [Statustext] müssen nicht gesetzt werden. [Intern1] und [Intern2] sollten immer leer bleiben.

	// Beispiel:
	// menuDaten.neu(new LinkEintrag('ISPConfig','Knowledgebase','http://www.ispconfig.org/de/support/knowledgebase.php','_blank','doc.gif','Link zur ISPConfig Knowledgebase','',''));
	
	
	// Nach Ende des Kommentars können die Einträge eingefügt werden:
	//-->

menuDaten.neu(new VerzEintrag('ISPConfig','root','ISPConfig Support','','',''));
menuDaten.neu(new LinkEintrag('ISPConfig','Knowledgebase (de)','http://www.ispconfig.org/','_blank','doc.gif','ISPConfig Knowledgebase','',''));
menuDaten.neu(new LinkEintrag('ISPConfig','Knowledgebase (en)','http://www.ispconfig.org/','_blank','doc.gif','ISPConfig Knowledgebase','',''));