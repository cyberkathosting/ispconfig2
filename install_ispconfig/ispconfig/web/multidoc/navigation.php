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
include("../../lib/config.inc.php");
include("../../lib/session.inc.php");
$navigation = "../" . $session_design_path . "/nav.inc.php";?>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta name="GENERATOR" content="Microsoft FrontPage 4.0">
<meta name="ProgId" content="FrontPage.Editor.Document">
<title>nav frame</title>

<STYLE type='text/css'>
<!--
<?include("../$session_style_path/style.css.php")?>
// -->
</STYLE>
<?if($go_info["server"]["ort"] != "local"){?>
<script language="JavaScript">
<!--
var bannerid;

bannerid = Math.round(Math.random()*1000) + 1;

function bannershow() 

{
document.banner.src = "http://kansas.valueclick.com/cycle?host=hs0195481&size=468x60&b=index&noscript=1";
setTimeout('bannershow()', 50000);
}

function bannerclick() 

{
open("http://kansas.valueclick.com/redirect?host=hs0195481&size=468x60&b=index&v=0");
}
-->
</script>
<?}?>
</head>

<body topmargin="0" leftmargin="0" bgcolor="<?echo $session_nav_hcolour?>" <?if($go_info["server"]["ort"] != "local") echo"onload=\"bannershow()\""?>>

<?include("$navigation")?>

</body>

</html>