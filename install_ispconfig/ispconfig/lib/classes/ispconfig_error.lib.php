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

if(CONFIG_LOADED != 1) die('Direct access not permitted.');

class error
{
    // This Class is deprecated
    
      function message($message,$stop)
      {
      global $go_api, $go_info, $s;
      
      $msg = "<html>

		<head>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1252\">
		</head>

		<body>
		<p align=\"center\">&nbsp;</p>
		<p align=\"center\">&nbsp;</p>
		<div align=\"center\">
		  <center>
		  <table border=\"0\" width=\"414\" bgcolor=\"#000000\" cellspacing=\"1\" cellpadding=\"2\">
		    <tr>
		      <td width=\"404\" bgcolor=\"" . $go_info["theme"]["page"]["nav_color"] . "\">
		        <p align=\"left\"><font size=\"2\" face=\"Verdana\" color=\"#333333\"><b>&nbsp; Es ist folgender Fehler aufgetreten:</b></font></td>
 		   </tr>
 		 </center>
 		 <tr>
  		  <td width=\"404\" bgcolor=\"#FFFFFF\">
   		     <p align=\"center\"><font size=\"2\" face=\"Verdana\"><br>
   		     <br>
   		     &nbsp;<br>
   		     $message<br>
   		     &nbsp;</font>
		<p>    </td>
    </tr>
  </table>
</div>
</body>

</html>";

    if($stop == 1) {
        die($msg);
    } else {
        echo($msg);
    }
		
      }


}
?>