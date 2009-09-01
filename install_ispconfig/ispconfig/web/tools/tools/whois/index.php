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
                                                            CHARSET => $go_info["theme"]["charset"],
                                    SERVERURL => $go_info["server"]["server_url"],
                                                            S => $s
                                    ) );

// BEGIN TOOL ###########################################################################

//$go_api->auth->check_admin(0);

$result = '';
$domain=($domainname."".$endfix."");
if (isset($submit)) {
// Switch von Domain-Endung
switch ($endfix) {
        case '.de':
        case '.se':
        case '.biz':
        case '.nl':
             $result=whois_cli($domain);
             break;
        case '.com':
        case '.net':
                                 $WhoIsServer="whois.crsnic.net";
                                 $result=whois_php ($WhoIsServer,$domain);
                                  break;
        case '.org':
                                 $WhoIsServer="whois.publicinterestregistry.net";
                                 $result=whois_php ($WhoIsServer,$domain);
                                  break;
        case '.lu':
                $WhoIsServer="whois.restena.lu";
                          $result=whois_php ($WhoIsServer,$domain);
                     break;
        case '.info':
                $WhoIsServer="whois.afilias.net";
                                 $result=whois_php ($WhoIsServer,$domain);
                                  break;
        case '.fr':
        case '.be':
        case '.at':
        case '.it':
        case '.no':
        case '.dk':
        case '.cz':
        case '.is':
        case '.sk':
        case '.li':
        case '.ch':
        case '.sk':
        case '.lt':
        case '.hu':
                $WhoIsServer="whois.ripe.net"; //$WhoIsServer zuweisen
                     $result=whois_php ($WhoIsServer,$domain);
                     break;
        case '.ac':
                $WhoIsServer="whois.nic.ac"; //$WhoIsServer zuweisen
                     $result=whois_php ($WhoIsServer,$domain);
                     break;
        case '.eu.org':
                $WhoIsServer="whois.eu.org"; //$WhoIsServer zuweisen
                          $result=whois_php ($WhoIsServer,$domain);
                     break;
        case '.pl':
                $WhoIsServer="dns.pl"; //$WhoIsServer zuweisen
                          $result=whois_php ($WhoIsServer,$domain);
                          break;
         case '.co.za':
                          $result=whois_http_get ("http://co.za/cgi-bin/whois.sh?Domain=".$domain."&Enter=Enter");
                          //Clean up of html to remove submit form
                            if (stristr($result,"No Matches")) {
                                        $result="Domain not found.";
                            }
                          break;
         case '.org.za':
                          $post = "domain=".$domain."&format=full";
                          $result=whois_http_post ("www.org.za/cgi-bin/rwhois",$post);
                                //Clean up of html response to remove submit form
                            if (stristr($result,"Domain not registered")) {
                                        $result="Domain not registered";
                            }
                            else{
                                        /*
                                        $doc = new DomDocument();
                                        $doc->loadHTML($result);
                                        $nodes=$doc->getElementsByTagName("pre");
                                        $result=$nodes->item(0)->textContent;
                                        */
                                        eregi('<pre>(.*)</pre>',$result,$matches);
                                        $result = $matches[1];
                            }
                          break;
         default:echo("Error!\n");break;
        }
}

function whois_php($WhoIsServer,$queryDomain){
        if($fp = @fsockopen ("$WhoIsServer", 43, $errnr, $errstr)) {
                $record="";
                                        stream_set_blocking($fp, 0);
                                         fputs($fp, "$queryDomain\n");
                        while (!feof($fp)) {
                       $record .= fgets($fp, 2048);
                                                }
                                        fclose($fp);
                                }
        return $record;
}

function whois_cli($queryDomain){
        $WhoIsServer = '-';
        $record ="No Results";
        $regex = '/^[a-zA-Z0-9\-\.]{0,63}$/';
        if(preg_match($regex,$queryDomain)) {
                $queryDomain = escapeshellcmd($queryDomain);
                $fp = popen("whois $queryDomain", 'r');
                $record = fread($fp, 4096);
                if (substr($result,0, 8) == "No match"){$result = "Domain not registered";}
                pclose($fp);
        }
        return $record;

}

function whois_http_get($WhoIsServerURL){
                          $record="No Results";
                        /*
                 $cUrl = curl_init($WhoIsServerURL);
                        curl_setopt($cUrl, CURLOPT_TIMEOUT, 180);
                        curl_setopt($cUrl, CURLOPT_HEADER, 0);
                        curl_setopt($cUrl, CURLOPT_RETURNTRANSFER, 1);
                        $record = trim(curl_exec($cUrl));
                        curl_close($cUrl);
                        */
                        $record = GetPage($WhoIsServerURL);
                        return $record;
}

function whois_http_post($WhoIsServerURL,$post){
                          $record="No Results";
                        /*
                 $cUrl = curl_init($WhoIsServerURL);
                        curl_setopt($cUrl, CURLOPT_TIMEOUT, 180);
                        curl_setopt($cUrl, CURLOPT_HEADER, 0);
                        curl_setopt($cUrl, CURLOPT_RETURNTRANSFER, 1);
                      curl_setopt($cUrl, CURLOPT_POST, 1);
                 curl_setopt($cUrl, CURLOPT_POSTFIELDS, $post);
                       $record = trim(curl_exec($cUrl));
                        curl_close($cUrl);
                        */
                        $record = PostPage($WhoIsServerURL,$post);
                        return $record;
}

function PostPage($host,$query,$others=''){
   $path=explode('/',$host);
   $host=$path[0];
   unset($path[0]);
   $path='/'.(implode('/',$path));
   $post="POST $path HTTP/1.1\r\nHost: $host\r\n";
   $post.="Content-type: application/x-www-form-";
   $post.="urlencoded\r\n${others}";
   $post.="User-Agent: Mozilla 4.0\r\nContent-length: ";
   $post.=strlen($query)."\r\nConnection: close\r\n\r\n$query";
   $h=fsockopen($host,80);
   fwrite($h,$post);
   for($a=0,$r='';!$a;){
       $b=fread($h,8192);
       $r.=$b;
       $a=(($b=='')?1:0);
   }
   fclose($h);
   return $r;
}

function GetPage ($WhoIsServerURL) {
        $handle = fopen($WhoIsServerURL, "rb");
        $contents = '';
        while (!feof($handle)) {
                  $contents .= fread($handle, 8192);
        }
        fclose($handle);
        return $contents;
}

$result .= "<table border='0' width='100%' align='center'><tr><td><PRE>$result</pre></td></tr></table>";
$html_pre = '&nbsp;<br><form name="form1" method="post" action="">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="center" class="t2">www.
                                                   <input type="text" name="domainname" size="17" value="'.$domainname.'">&nbsp;&nbsp;&nbsp;
                                                   <select name="endfix" size="1" style="font-family: Verdana; font-size: 10pt; color: #4E566B; font-weight: bold">


                                                                        <option name="com" value=".com">.com
                                                                        <option name="net" value=".net">.net
                                                                        <option name="org" value=".org">.org
                                                                        <option name="biz" value=".biz">.biz
                                                                        <option name="info" value=".info">.info
                                                                        <option name="ac" value=".ac">.ac
                                                                        <option name="at" value=".at">.at
                                                                        <option name="be" value=".be">.be
                                                                        <option name="ch" value=".ch">.ch
                                                                        <option name="cz" value=".cz">.cz
                                                                        <option name="de" value=".de">.de
                                                                        <option name="dk" value=".dk">.dk
                                                                        <option name="eu.org" value=".eu.org">.eu.org
                                                                        <option name="fr" value=".fr">.fr
                                                                        <option name="hu" value=".hu">.hu
                                                                        <option name="it" value=".it">.it
                                                                        <option name="is" value=".is">.is
                                                                        <option name="li" value=".li">.li
                                                                        <option name="lt" value=".lt">.lt
                                                                        <option name="lu" value=".lu">.lu
                                                                        <option name="nl" value=".nl">.nl
                                                                        <option name="no" value=".no">.no
                                                                        <option name="pl" value=".pl">.pl
                                                                        <option name="sk" value=".sk">.sk
                                                                        <option name="se" value=".se">.se
                                                                        <option name="co.za" value=".co.za">.co.za
                                                                        <option name="org.za" value=".org.za">.org.za
                                                        </select>&nbsp;
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