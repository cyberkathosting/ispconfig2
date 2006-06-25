<?
include("/root/ispconfig/scripts/lib/config.inc.php");
include("/root/ispconfig/scripts/lib/server.inc.php");

$interfaces = "/etc/network/interfaces";
$interfaces_action = "/etc/init.d/networking restart &> /dev/null";
$interfaces_tpl = '# /etc/network/interfaces -- configuration file for ifup(8), ifdown(8)

# The loopback interface
auto lo
iface lo inet loopback

# The first network card - this entry was created during the Debian installation
# (network, broadcast and gateway are optional)
auto eth0
iface eth0 inet static
        address {IP}
        netmask {NETMASK}
        network {NETWORK}
        broadcast {BROADCAST}
        gateway {GATEWAY}';

class fn{

function getInput($length = 255) {
  $fr = fopen("php://stdin", "r");
  $input = fgets($fr, $length);
  $input = rtrim($input);
  fclose($fr);
  return $input;
}

function mkdirs($strPath, $mode = '0755'){
  // Verzeichnisse rekursiv erzeugen
  if(is_dir($strPath)) return true;
  $pStrPath = dirname($strPath);
  if(!$this->mkdirs($pStrPath, $mode)) return false;
  $old_umask = umask(0);
  $ret_val = mkdir($strPath, octdec($mode));
  umask($old_umask);
  return $ret_val;
}

function wf($file, $content){
  $this->mkdirs(dirname($file));
  if(!$fp = fopen ($file, "wb")){
    return false;
  } else {
    fwrite($fp,$content);
    fclose($fp);
    return true;
  }
}

function binary_netmask($netmask){
  list($f1,$f2,$f3,$f4) = explode(".", trim($netmask));
  $bin = str_pad(decbin($f1),8,"0",STR_PAD_LEFT).str_pad(decbin($f2),8,"0",STR_PAD_LEFT).str_pad(decbin($f3),8,"0",STR_PAD_LEFT).str_pad(decbin($f4),8,"0",STR_PAD_LEFT);
  $parts = explode("0", $bin);
  return substr_count($parts[0], "1");
}

function netmask($netmask){
  list($f1,$f2,$f3,$f4) = explode(".", trim($netmask));
  $bin = str_pad(decbin($f1),8,"0",STR_PAD_LEFT).str_pad(decbin($f2),8,"0",STR_PAD_LEFT).str_pad(decbin($f3),8,"0",STR_PAD_LEFT).str_pad(decbin($f4),8,"0",STR_PAD_LEFT);
  $parts = explode("0", $bin);
  $bin = str_pad($parts[0], 32, "0", STR_PAD_RIGHT);
  $bin = wordwrap($bin, 8, ".", 1);
  list($f1,$f2,$f3,$f4) = explode(".", trim($bin));
  return bindec($f1).".".bindec($f2).".".bindec($f3).".".bindec($f4);
}

function network($ip, $netmask){
  $netmask = $this->netmask($netmask);
  list($f1,$f2,$f3,$f4) = explode(".", $netmask);
  $netmask_bin = str_pad(decbin($f1),8,"0",STR_PAD_LEFT).str_pad(decbin($f2),8,"0",STR_PAD_LEFT).str_pad(decbin($f3),8,"0",STR_PAD_LEFT).str_pad(decbin($f4),8,"0",STR_PAD_LEFT);
  list($f1,$f2,$f3,$f4) = explode(".", $ip);
  $ip_bin = str_pad(decbin($f1),8,"0",STR_PAD_LEFT).str_pad(decbin($f2),8,"0",STR_PAD_LEFT).str_pad(decbin($f3),8,"0",STR_PAD_LEFT).str_pad(decbin($f4),8,"0",STR_PAD_LEFT);
  for($i=0;$i<32;$i++){
    $network_bin .= substr($netmask_bin,$i,1) * substr($ip_bin,$i,1);
  }
  $network_bin = wordwrap($network_bin, 8, ".", 1);
  list($f1,$f2,$f3,$f4) = explode(".", trim($network_bin));
  return bindec($f1).".".bindec($f2).".".bindec($f3).".".bindec($f4);
}

function broadcast($ip, $netmask){
  $netmask = $this->netmask($netmask);
  $binary_netmask = $this->binary_netmask($netmask);
  list($f1,$f2,$f3,$f4) = explode(".", $ip);
  $ip_bin = str_pad(decbin($f1),8,"0",STR_PAD_LEFT).str_pad(decbin($f2),8,"0",STR_PAD_LEFT).str_pad(decbin($f3),8,"0",STR_PAD_LEFT).str_pad(decbin($f4),8,"0",STR_PAD_LEFT);
  $broadcast_bin = str_pad(substr($ip_bin, 0, $binary_netmask),32,"1",STR_PAD_RIGHT);
  $broadcast_bin = wordwrap($broadcast_bin, 8, ".", 1);
  list($f1,$f2,$f3,$f4) = explode(".", trim($broadcast_bin));
  return bindec($f1).".".bindec($f2).".".bindec($f3).".".bindec($f4);
}

}

$fn = new fn;

echo "Please select your language [de/en] / Bitte wählen Sie Ihre Sprache aus [de/en]: ";
$lang = $fn->getInput();
if($lang != "de" && $lang != "en") $lang = "en";

switch ($lang) {
      case "de":

echo "Geben Sie die gewünschte IP-Adresse ein (z.B. 192.168.0.100): ";
$ip = $fn->getInput();

echo "Geben Sie die Netzmaske ein (z.B. 255.255.255.0): ";
$netmask = $fn->netmask($fn->getInput());

echo "Geben Sie das Gateway ein (z.B. 192.168.0.1): ";
$gateway = $fn->getInput();

$network = $fn->network($ip, $netmask);
$broadcast = $fn->broadcast($ip, $netmask);

echo "\nIP-Adresse: ".$ip."\n";
echo "Netzmaske: ".$netmask."\n";
echo "Gateway: ".$gateway."\n";
echo "Netzwerk: ".$network."\n";
echo "Broadcast-Adresse: ".$broadcast."\n\n";

echo "Die Netzwerk-Konfiguration wird jetzt geändert. \nBitte haben Sie einen Moment Geduld. \nSie können danach das ISPConfig Administrationsinterface per Browser unter \n\nhttps://".$ip.":81/\n\nerreichen.\n\n";

      break;
      case "en":

echo "Please enter your desired IP address (e.g. 192.168.0.100): ";
$ip = $fn->getInput();

echo "Please enter your netmask (e.g. 255.255.255.0): ";
$netmask = $fn->netmask($fn->getInput());

echo "Please enter your gateway (e.g. 192.168.0.1): ";
$gateway = $fn->getInput();

$network = $fn->network($ip, $netmask);
$broadcast = $fn->broadcast($ip, $netmask);

echo "\nIP Address: ".$ip."\n";
echo "Netmask: ".$netmask."\n";
echo "Gateway: ".$gateway."\n";
echo "Network: ".$network."\n";
echo "Broadcast Address: ".$broadcast."\n\n";

echo "The network configuration is going to be changed now. \nPlease wait a moment. \nAfterwards you can access the ISPConfig administration interface in your browser under \n\nhttps://".$ip.":81\n\n";

      break;
}

$content = str_replace("{IP}", $ip, $interfaces_tpl);
$content = str_replace("{NETMASK}", $netmask, $content);
$content = str_replace("{NETWORK}", $network, $content);
$content = str_replace("{BROADCAST}", $broadcast, $content);
$content = str_replace("{GATEWAY}", $gateway, $content);

$fn->wf($interfaces, $content);
$mod->db->query("UPDATE isp_server SET server_ip = '".$ip."', server_netzmaske = '".$netmask."' WHERE 1");

exec($interfaces_action);
?>