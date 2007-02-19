<?
/*
Copyright (c) 2005, projektfarm Gmbh, Till Brehm, Falko Timme
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, 
are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, 
      this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, 
      this list of conditions and the following disclaimer in the documentation 
      and/or other materials provided with the distribution.
    * Neither the name of ISPConfig nor the names of its contributors 
      may be used to endorse or promote products derived from this software without 
      specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND 
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. 
IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, 
INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, 
BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, 
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY 
OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING 
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, 
EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
include("../lib/config.inc.php");
include("../lib/session.inc.php");

######################################################
#
#  Frameaufteilung
#
######################################################

$frameaufteilung = "*,72%";
if ($session_modul == "termine") $frameaufteilung = "208,*";

if($_POST["web_id"] > 0) $HTTP_GET_VARS["web_id"] = $_POST["web_id"];

$geststring = "";
foreach($HTTP_GET_VARS as $key => $val) {
    $getstring .= "$key=$val&";
}
$getstring = substr($getstring,0,-1);

$seiten_navigation = "inhalt.php";
if($go_info["modul"]["sidenav"] == "tree") $seiten_navigation = "inhalt_tree.php";
if($go_info["modul"]["sidenav"] == "flat") $seiten_navigation = "inhalt_flat.php";


?>
<html>

<head>

<title>ISPConfig</title>
<STYLE>
        BODY  { scrollbar-arrow-color:blue;
                          scrollbar-base-color:blue;
                          scrollbar-darkshadow-color:blue;
                          scrollbar-face-color:red
                          scrollber-highlight-color:blue;
                          scrollbar-shadow-color:blue   }
</STYLE>

<script language= "JavaScript">
  <!--Break out of frames
    if (top.frames.length > 0)
    top.location=self.document.location;
  //-->
</script>

<noscript>
<meta http-equiv="REFRESH" content="1; URL=nojsindx.htm">
</noscript>

</head>
  <frameset rows="102,99%,10" framespacing="0" border="0" frameborder="0">
    <frame name="Hauptframe" src="<? echo $go_info["modul"]["path"]?>/navigation.php?<? echo $getstring?>" scrolling="no" noresize marginwidth="0" marginheight="0" target="_self">
    <frameset cols="<? echo $frameaufteilung?>">
      <frame name="menuFrame" src="<? echo $go_info["modul"]["path"].'/'.$seiten_navigation.'?'.$getstring?>" scrolling="auto" marginwidth="10" marginheight="10" target="_self">
      <frame name="seiteFrame" src="<? echo $go_info["modul"]["path"]?>/frame_start.php?<? echo $getstring?>" scrolling="auto" target="_self" marginwidth="22" marginheight="26">
        </frameset>
    <frame name="seiteFrame2" src="<? echo $go_info["modul"]["path"]?>/legende.php?<? echo $session?>" target="_self" scrolling="no" noresize>
    <noframes>
  <body>
  <p>Diese Seite verwendet Frames. Frames werden von Ihrem Browser aber nicht
  unterstützt.</p>
  </body>
    </noframes>
  </frameset>

</html>