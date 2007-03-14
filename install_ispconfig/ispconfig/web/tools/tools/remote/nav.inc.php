<? if($go_api->auth->check_admin(0,1)) {
?>
menuDaten.neu(new VerzEintrag('remoting','root','<? echo $go_api->lng("Remoting")?>','','',''));
menuDaten.neu(new LinkEintrag('remoting','<? echo $go_api->lng("User")?>','<? echo $go_info["server"]["server_url"]?>/tools/tools/remote/userlist.php?<?echo $session?>','seiteFrame','server_client.gif','User','n','<?echo "1&amp;$session"?>'));
<?
}
?>