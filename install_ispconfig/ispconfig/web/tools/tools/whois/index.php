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
include("../../../../lib/config.inc.php");
include("../../../../lib/session.inc.php");

$go_api->content->define(   array(
                                    main    => "main.htm",
                                    table   => "tools_standard.htm",
                                    stylesheet => "style.css"));

$go_api->content->assign( array(    TITLE => "$session_site Startseite",
                                                            SESSION => $session,
                                                            BACKGROUND_GIF => "",
                                                            COPYRIGHT => "",
                                                            FGCOLOR => "$session_nav_hcolour",
                                                            TABLE_H_COLOR => "$session_page_hcolour",
                                                            BOXSIZE => "450",
                                                            WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#FFFFFF\">&nbsp; Whois</font>",
                                                            SITENAME => "$session_site",
                                                            DESIGNPATH => $session_design_path,
                                    SERVERURL => $go_info["server"]["server_url"],
                                                            S => $s
                                    ) );

// BEGIN TOOL ###########################################################################

//$go_api->auth->check_admin(0);

if (isset($submit)) {


// Switch von Domain-Endung
switch ($endfix) {
        case '.de':$id=1;break;
        case '.com':$id=2;break;
        case '.net':$id=3;break;
        case '.org':$id=4;break;
        case '.lu':$id=5;break;
        case '.fr':$id=6;break;
        case '.be':$id=7;break;
        case '.at':$id=8;break;
        case '.it':$id=9;break;
        case '.no':$id=10;break;
        case '.ac':$id=11;break;
        case '.dk':$id=12;break;
        case '.cz':$id=13;break;
        case '.is':$id=14;break;
        case '.sk':$id=15;break;
        case '.li':$id=16;break;
        case '.ch':$id=17;break;
        case '.eu.org':$id=18;break;
        case '.sk':$id=19;break;
        case '.lt':$id=20;break;
        case '.hu':$id=21;break;
		case '.se':$id=22;break;
		case '.nl':$id=23;break;

        default:echo("Error!\n");break;
        }

//Domain zusammensetzen
$domain=($domainname."".$endfix."");
//Richtigen Server abfragen
$antwort = '';

if ($id==1) {
	$WhoIsServer = '';
	$regex = '/^[a-zA-Z0-9\-\.]{0,63}$/';
	
	if(preg_match($regex,$domain)) {
		
		$domain = escapeshellcmd($domain);
		//$result = system("whois $domain");
		$fp = popen("whois $domain", 'r');
		$result = fread($fp, 4096);
		pclose($fp);
		$record = $result;
		
              if (substr("$result" ,2, 16) == "No entries found") { //Antwort kontrollieren:frei
                $antwort = 0;
                }

        else { //Antwort kontrollieren:nicht frei
                $antwort = 1;
                } // Wenn nciht merh frei Daten ansehen?
      }
}

else if ($id==5) {


        $WhoIsServer="whois.restena.lu";
        $fp = fsockopen ("$WhoIsServer", 43, $errnr, $errstr);
    set_socket_blocking($fp, 0);
           fputs($fp, "$domain\n");
                 while (!feof($fp)) {
                        $result = fgets($fp, 2048);

                if (substr("$result" ,2, 16) == "No entries found") {
                        $antwort = 0;
                        }

        else if (substr("$result" ,0, 11) == "domainname:") {
                        $antwort = 1;
              }
                }
    fclose($fp);
}



// s.o.



else if ($id==6) {
        $WhoIsServer="whois.ripe.net"; //$WhoIsServer zuweisen
        $fp = fsockopen ("$WhoIsServer", 43, $errnr, $errstr); // Verbindung aufmachen
    set_socket_blocking($fp,0);
           fputs($fp, "$domain\n"); //Domain uebermittlen
                 while (!feof($fp)) { // Antwort einlesen
                        $result = fgets($fp, 2048);
        if (substr("$result" ,2, 16) == "No entries found") { //Antwort kontrollieren:frei
                        $antwort = 0;
                }

        else if (substr("$result" ,0, 7) == "domain:") { //Antwort kontrollieren:nicht frei
                        $antwort = 1;
                } // Wenn nciht merh frei Daten ansehen?
      }
    fclose($fp); // Verbindung schliessen
}

//s .o.

else if ($id==7) {
        $WhoIsServer="whois.ripe.net"; //$WhoIsServer zuweisen
        $fp = fsockopen ("$WhoIsServer", 43, $errnr, $errstr); // Verbindung aufmachen
    set_socket_blocking($fp,0);
           fputs($fp, "$domain\n"); //Domain uebermittlen
                 while (!feof($fp)) { // Antwort einlesen
                        $result = fgets($fp, 2048);
        if (substr("$result" ,2, 16) == "No entries found") { //Antwort kontrollieren:frei
                $antwort = 0;
                }

        else if (substr("$result" ,0, 7) == "domain:") { //Antwort kontrollieren:nicht frei
                $antwort = 1;
                           } // Wenn nciht merh frei Daten ansehen?

      }

    fclose($fp); // Verbindung schliessen

}

else if ($id==8) {

        $WhoIsServer="whois.ripe.net"; //$WhoIsServer zuweisen

        $fp = fsockopen ("$WhoIsServer", 43, $errnr, $errstr); // Verbindung aufmachen
    set_socket_blocking($fp,0);
           fputs($fp, "$domain\n"); //Domain uebermittlen
                 while (!feof($fp)) { // Antwort einlesen
                        $result = fgets($fp, 2048);
        if (substr("$result" ,2, 16) == "No entries found") { //Antwort kontrollieren:frei
                $antwort = 0;
                }

        else if (substr("$result" ,0, 7) == "domain:") { //Antwort kontrollieren:nicht frei
                $antwort = 1;
                           } // Wenn nciht merh frei Daten ansehen?
      }
    fclose($fp); // Verbindung schliessen
}

//s .o.

else if ($id==9) {
        $WhoIsServer="whois.ripe.net"; //$WhoIsServer zuweisen
        $fp = fsockopen ("$WhoIsServer", 43, $errnr, $errstr); // Verbindung aufmachen
    set_socket_blocking($fp,0);
           fputs($fp, "$domain\n"); //Domain uebermittlen
                 while (!feof($fp)) { // Antwort einlesen
                        $result = fgets($fp, 2048);
        if (substr("$result" ,2, 16) == "No entries found") { //Antwort kontrollieren:frei
                $antwort = 0;
                }
        else if (substr("$result" ,0, 7) == "domain:") { //Antwort kontrollieren:nicht frei
                $antwort = 1;
                           } // Wenn nciht merh frei Daten ansehen?
      }
    fclose($fp); // Verbindung schliessen
}



//s .o.



else if ($id==10) {

        $WhoIsServer="whois.ripe.net"; //$WhoIsServer zuweisen
        $fp = fsockopen ("$WhoIsServer", 43, $errnr, $errstr); // Verbindung aufmachen
    set_socket_blocking($fp,0);
           fputs($fp, "$domain\n"); //Domain uebermittlen
                 while (!feof($fp)) { // Antwort einlesen
                        $result = fgets($fp, 2048);
        if (substr("$result" ,2, 16) == "No entries found") { //Antwort kontrollieren:frei
                $antwort = 0;
                }

        else if (substr("$result" ,0, 7) == "domain:") { //Antwort kontrollieren:nicht frei
                $antwort = 1;
                           } // Wenn nciht merh frei Daten ansehen?
      }
    fclose($fp); // Verbindung schliessen
}



//s .o.



else if ($id==11) {

        $WhoIsServer="whois.nic.ac"; //$WhoIsServer zuweisen
        $fp = fsockopen ("$WhoIsServer", 43, $errnr, $errstr); // Verbindung aufmachen
    set_socket_blocking($fp,0);
           fputs($fp, "$domain\n"); //Domain uebermittlen
                 while (!feof($fp)) { // Antwort einlesen
                        $result = fgets($fp, 2048);
        if (substr("$result" ,0, 12) == "No match for") { //Antwort kontrollieren:frei
                $antwort = 0;
                }

        else if (substr("$result" ,0, 6) == "Domain") { //Antwort kontrollieren:nicht frei
                $antwort = 1;
                            } // Wenn nciht merh frei Daten ansehen?
      }
    fclose($fp); // Verbindung schliessen
}



//s .o.

else if ($id==12) {

        $WhoIsServer="whois.ripe.net"; //$WhoIsServer zuweisen
        $fp = fsockopen ("$WhoIsServer", 43, $errnr, $errstr); // Verbindung aufmachen
    set_socket_blocking($fp,0);
           fputs($fp, "$domain\n"); //Domain uebermittlen
                 while (!feof($fp)) { // Antwort einlesen
                        $result = fgets($fp, 2048);
        if (substr("$result" ,2, 16) == "No entries found") { //Antwort kontrollieren:frei
                $antwort = 0;
                }

        else if (substr("$result" ,0, 7) == "domain:") { //Antwort kontrollieren:nicht frei
                $antwort = 1;
                           } // Wenn nciht merh frei Daten ansehen?
      }
    fclose($fp); // Verbindung schliessen
}



//s .o.

else if ($id==13) {

        $WhoIsServer="whois.ripe.net"; //$WhoIsServer zuweisen
        $fp = fsockopen ("$WhoIsServer", 43, $errnr, $errstr); // Verbindung aufmachen
    set_socket_blocking($fp,0);
           fputs($fp, "$domain\n"); //Domain uebermittlen
                 while (!feof($fp)) { // Antwort einlesen
                        $result = fgets($fp, 2048);
        if (substr("$result" ,2, 16) == "No entries found") { //Antwort kontrollieren:frei
                $antwort = 0;
                }

        else if (substr("$result" ,0, 7) == "domain:") { //Antwort kontrollieren:nicht frei
                $antwort = 1;
                           } // Wenn nciht merh frei Daten ansehen?
      }
    fclose($fp); // Verbindung schliessen
}



//s .o.

else if ($id==14) {

        $WhoIsServer="whois.ripe.net"; //$WhoIsServer zuweisen
        $fp = fsockopen ("$WhoIsServer", 43, $errnr, $errstr); // Verbindung aufmachen
    set_socket_blocking($fp,0);
           fputs($fp, "$domain\n"); //Domain uebermittlen
                 while (!feof($fp)) { // Antwort einlesen
                        $result = fgets($fp, 2048);
        if (substr("$result" ,2, 16) == "No entries found") { //Antwort kontrollieren:frei
                $antwort = 0;
                }

        else if (substr("$result" ,0, 7) == "domain:") { //Antwort kontrollieren:nicht frei
                $antwort = 1;
                           } // Wenn nciht merh frei Daten ansehen?

      }
    fclose($fp); // Verbindung schliessen
}



//s .o.

else if ($id==15) {

        $WhoIsServer="whois.ripe.net"; //$WhoIsServer zuweisen
        $fp = fsockopen ("$WhoIsServer", 43, $errnr, $errstr); // Verbindung aufmachen
    set_socket_blocking($fp,0);
           fputs($fp, "$domain\n"); //Domain uebermittlen
                 while (!feof($fp)) { // Antwort einlesen
                        $result = fgets($fp, 2048);
        if (substr("$result" ,2, 16) == "No entries found") { //Antwort kontrollieren:frei
                $antwort = 0;
                }

        else if (substr("$result" ,0, 7) == "domain:") { //Antwort kontrollieren:nicht frei
                $antwort = 1;
                           } // Wenn nciht merh frei Daten ansehen?
      }
    fclose($fp); // Verbindung schliessen
}



//s .o.



else if ($id==16) {

        $WhoIsServer="whois.ripe.net"; //$WhoIsServer zuweisen
        $fp = fsockopen ("$WhoIsServer", 43, $errnr, $errstr); // Verbindung aufmachen
    set_socket_blocking($fp,0);
           fputs($fp, "$domain\n"); //Domain uebermittlen
                 while (!feof($fp)) { // Antwort einlesen
                        $result = fgets($fp, 2048);
        if (substr("$result" ,0, 16) == "No entries found") { //Antwort kontrollieren:frei
                $antwort = 0;
                }

        else if (substr("$result" ,0, 7) == "Domain ") { //Antwort kontrollieren:nicht frei
                $antwort = 1;
                           } // Wenn nciht merh frei Daten ansehen?
      }
    fclose($fp); // Verbindung schliessen
}



//s .o.

else if ($id==17) {

        $WhoIsServer="whois.ripe.net"; //$WhoIsServer zuweisen
        $fp = fsockopen ("$WhoIsServer", 43, $errnr, $errstr); // Verbindung aufmachen
    set_socket_blocking($fp,0);
           fputs($fp, "$domain\n"); //Domain uebermittlen
                 while (!feof($fp)) { // Antwort einlesen
                        $result = fgets($fp, 2048);
        if (substr("$result" ,0, 16) == "No entries found") { //Antwort kontrollieren:frei
                $antwort = 0;
                }

        else if (substr("$result" ,0, 7) == "Domain ") { //Antwort kontrollieren:nicht frei
                $antwort = 1;
                           } // Wenn nciht merh frei Daten ansehen?
      }
    fclose($fp); // Verbindung schliessen
}



//s .o.

else if ($id==18) {

        $WhoIsServer="whois.eu.org"; //$WhoIsServer zuweisen
        $fp = fsockopen ("$WhoIsServer", 43, $errnr, $errstr); // Verbindung aufmachen
    set_socket_blocking($fp,0);
           fputs($fp, "$domain\n"); //Domain uebermittlen
                 while (!feof($fp)) { // Antwort einlesen
                        $result = fgets($fp, 2048);
        if (substr("$result" ,0, 16) == "No entries found") { //Antwort kontrollieren:frei
                $antwort = 0;
                }

        else if (substr("$result" ,0, 7) == "domain:") { //Antwort kontrollieren:nicht frei
                $antwort = 1;
                           } // Wenn nciht merh frei Daten ansehen?
      }
    fclose($fp); // Verbindung schliessen
}



//s .o.

else if ($id==19) {

        $WhoIsServer="whois.ripe.net"; //$WhoIsServer zuweisen
        $fp = fsockopen ("$WhoIsServer", 43, $errnr, $errstr); // Verbindung aufmachen
    set_socket_blocking($fp,0);
           fputs($fp, "$domain\n"); //Domain uebermittlen
                 while (!feof($fp)) { // Antwort einlesen
                        $result = fgets($fp, 2048);
        if (substr("$result" ,2, 16) == "No entries found") { //Antwort kontrollieren:frei
                $antwort = 0;
                }

        else if (substr("$result" ,0, 7) == "domain:") { //Antwort kontrollieren:nicht frei
                $antwort = 1;
                           } // Wenn nciht merh frei Daten ansehen?
      }
    fclose($fp); // Verbindung schliessen
}



//s .o.

else if ($id==20) {

        $WhoIsServer="whois.ripe.net"; //$WhoIsServer zuweisen
        $fp = fsockopen ("$WhoIsServer", 43, $errnr, $errstr); // Verbindung aufmachen
    set_socket_blocking($fp,0);
           fputs($fp, "$domain\n"); //Domain uebermittlen
                 while (!feof($fp)) { // Antwort einlesen
                        $result = fgets($fp, 2048);
        if (substr("$result" ,2, 16) == "No entries found") { //Antwort kontrollieren:frei
                $antwort = 0;
                }

        else if (substr("$result" ,0, 7) == "domain:") { //Antwort kontrollieren:nicht frei
                $antwort = 1;
                           } // Wenn nciht merh frei Daten ansehen?
      }
    fclose($fp); // Verbindung schliessen
}



//s .o.



else if ($id==21) {
        $WhoIsServer="whois.ripe.net"; //$WhoIsServer zuweisen
        $fp = fsockopen ("$WhoIsServer", 43, $errnr, $errstr); // Verbindung aufmachen
    set_socket_blocking($fp,0);
           fputs($fp, "$domain\n"); //Domain uebermittlen
                 while (!feof($fp)) { // Antwort einlesen
                        $result = fgets($fp, 2048);
        if (substr("$result" ,2, 16) == "No entries found") { //Antwort kontrollieren:frei
                $antwort = 0;
                }

        else if (substr("$result" ,0, 7) == "domain:") { //Antwort kontrollieren:nicht frei
                $antwort = 1;
                           } // Wenn nciht merh frei Daten ansehen?
      }
    fclose($fp); // Verbindung schliessen
}

else if ($id==22) {
	
	// Linux whois für SE
	$WhoIsServer = '-';
	$regex = '/^[a-zA-Z0-9\-\.]{0,63}$/';
	
	if(preg_match($regex,$domain)) {
		
		$domain = escapeshellcmd($domain);
		//$result = system("whois $domain");
		$fp = popen("whois $domain", 'r');
		$result = fread($fp, 4096);
		pclose($fp);
		$record = $result;
		
        if (stristr("$result", "domainname.name:")) { //Antwort kontrollieren:nicht frei
		$antwort = 2;
		} else {
			$antwort = 0;
		}
	}
} 

else if ($id==23) {
	// Linux whois für NL
	$WhoIsServer = '-';
	$regex = '/^[a-zA-Z0-9\-\.]{0,63}$/';
	
	if(preg_match($regex,$domain)) {
		
		$domain = escapeshellcmd($domain);
		//$result = system("whois $domain");
		$fp = popen("whois $domain", 'r');
		$result = fread($fp, 4096);
		pclose($fp);
		$record = $result;
		
        if (stristr("$result", "Domain name:")) { //Antwort kontrollieren:nicht frei
		$antwort = 2;
		} else {
			$antwort = 0;
		}
	}
} 

//s .o.

else if ($id==2||3||4) {

        $fp = fsockopen("whois.crsnic.net", 43, $errno, $errstr);
   set_socket_blocking($fp, 0);
   fputs($fp, "$domain\n");
   while (!feof($fp)) {
                   $result = trim(fgets($fp, 2048));
                   if (substr($result,0, 8) == "No match"){
                $antwort = 0;
                }
        else if (substr($result, 0, 13) == "Whois Server:"){
                $WhoIsServer = trim(ereg_replace("Whois Server:"," ",$result));
                $antwort = 1;
              }
      }
   fclose($fp);
   }

//}

//DomainDaten abfragen
$result = '';
function showtime($domain, $WhoIsServer) {
global $domain,$WhoIsServer,$result;
if ((empty($domain) == false) && (empty($WhoIsServer) == false)) {
                $fps = fsockopen ("$WhoIsServer", 43, $errno, $errstr)
                or die(printf("Error while connecting to whois server.\n"));
                set_socket_blocking($fps, 0);
                fputs($fps, "$domain\n");
            $result .= "<table border='0' width='100%' align='center'><tr><td><PRE>";
                        while (!feof($fps)) {
                                $result .= fgets($fps, 2048);
                                //echo "reihe";
            }
        $result .= "</pre>
    </td></tr></table>";
        fclose($fps);
    } else {
                  $result .= ("Could not gather all necessary data.\n");
        }
}

//Sollen DomainDaten angezeigt werden?
//die($antwort);
if($antwort == 1) {
    showtime($domain, $WhoIsServer);
	
} elseif($antwort == 2) {
	
	$result .= "<table border='0' width='100%' align='center'><tr><td><PRE>$record</pre>
    </td></tr></table>";
} else {
        if ((empty($domain) == false) && (empty($WhoIsServer) == false)) {
                $result .= "<center>The domain has not been registered yet.<center><p>";
        }
}

}


$html_pre = '&nbsp;<br><form name="form1" method="post" action="">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="center" class="t2">www.
                                  <input type="text" name="domainname" size="17">&nbsp;&nbsp;&nbsp;<select name="endfix" size="1" style="font-family: Verdana; font-size: 10pt; color: #4E566B; font-weight: bold">
        <option name="de" value=".de">.de
        <option name="com" value=".com">.com
        <option name="net" value=".net">.net
        <option name="org" value=".org">.org
        <option name="ac" value=".ac">.ac
        <option name="at" value=".at">.at
        <option name="be" value=".be">.be
        <option name="ch" value=".ch">.ch
        <option name="cz" value=".cz">.cz
        <option name="dk" value=".dk">.dk
        <option name="eu.org" value=".eu.org">.eu.org
        <option name="fr" value=".fr">.fr
        <option name="it" value=".it">.it
        <option name="is" value=".is">.is
        <option name="hu" value=".hu">.hu
        <option name="li" value=".li">.li
        <option name="lt" value=".lt">.lt
        <option name="lu" value=".lu">.lu
        <option name="no" value=".no">.no
        <option name="sk" value=".sk">.sk
		<option name="se" value=".se">.se
		<option name="nl" value=".nl">.nl
</select>
                    &nbsp;
                    <input type="submit" name="submit" value="Search &gt;&gt;" class="button"> </td>
                </tr>
              </table>
            </form><br>&nbsp;<br>';

$html_show = $html_pre . $result;

// END TOOL #############################################################################

$go_api->content->assign( array( TOOL => $html_show));
$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;
?>