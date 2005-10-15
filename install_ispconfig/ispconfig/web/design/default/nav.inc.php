<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="22" bgcolor="#E0E0E0"><table width="100%" height="22" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" cellspacing="0" cellpadding="0" height="22">
          <tr>
            <td width="5" class="normal">&nbsp;</td>
			<?
      		while (list($key, $val) = each($go_info["session"]["modules"])) {
      			if($go_info["modul"]["name"] == $val["name"]) {
 		  		?><td bgcolor="#CCCCCC" class="navi">&nbsp; <a href="../capp.php?<? echo $session?>&amp;app=<? echo $val["name"]?>" class="navi" target="_top"><b><? echo $go_api->lng($val["title"])?></b></a> &nbsp;</td><?
 		  		} else {
 		  		?><td class="navi">&nbsp; <a href="../capp.php?<? echo $session?>&amp;app=<? echo $val["name"]?>" class="navi" target="_top"><? echo $go_api->lng($val["title"])?></a> &nbsp;</td><?
 		  		}
			}
      		?>
			<td class="navi">&nbsp; <a href="../logoff.php?s=<? echo $s?>" class="navi" target="_top"><? echo $go_api->lng('Logout')?></a> &nbsp;</td>
          </tr>
        </table></td>
        <td width="30" align="center"><a href="../logoff.php?s=<? echo $s?>"><img src="../<? echo $session_design_path?>/images/nav_oben_exit.gif" width="16" height="16" border="0" alt="Logout"></a></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="1" bgcolor="#CCCCCC"><img src="../<? echo $session_design_path?>/x.gif" width="1" height="1"></td>
  </tr>
  <tr>
    <td height="1" bgcolor="#FFFFFF"><img src="../<? echo $session_design_path?>/x.gif" width="1" height="1"></td>
  </tr>
  <tr>
    <td height="78" background="../<? echo $session_design_path?>/images/nav_oben_bg.gif">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
	  
        <td valign="top">
		<?php if(is_array($go_info["modul"]["menu"])) {?>
		<table border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="10" rowspan="3">&nbsp;</td>
            <td height="14"><img src="../<? echo $session_design_path?>/x.gif" width="1" height="1"></td>
          </tr>
		  <tr>
		  	<?php
		    while (list($key, $menu_item) = each($go_info["modul"]["menu"])) {
		    ?>
            <td width="10">&nbsp;</td>
			<td align="center"><a href="<? echo $menu_item[link] . "?s=$s"?><? if($menu_item[params] != '') echo '&'.$menu_item[params];?>" target="seiteFrame"><img src="../<? echo $session_design_path?>/images/<? echo $menu_item[icon]?>" border="0" alt="<? echo $go_api->lng($menu_item[title])?>"></a></td>
		  	<?php
			}
			reset($go_info["modul"]["menu"]);?>
		  </tr>
		  <tr>
			<?
		    while (list($key, $menu_item) = each($go_info["modul"]["menu"])) {
		    ?>
            <td width="10">&nbsp;</td>
			<td align="center" class="normal_kl"><a href="<? echo $menu_item[link] . "?s=$s"?><? if($menu_item[params] != '') echo '&'.$menu_item[params];?>" target="seiteFrame" class="normal_kl"><? echo $go_api->lng($menu_item[title])?></a></td>
		    <?php
		    }
		    ?>
		  </tr>
        </table>
		<?php } else { echo "&nbsp;";}?>
		</td>
        <td width="200"><img src="../<? 
		
		// bestimme Username des Resellers
		/*
		$groups = $go_api->groups->myGroups();
		if(is_array($groups)) {
			$reseller_sys_doc_id = $go_info["user"]["userid"];
		} else {
			$reseller = $go_api->db->queryOneRecord("SELECT doc_id FROM sys_user WHERE userid = ".$go_info["user"]["userid"]);
			$reseller_sys_doc_id = $reseller["doc_id"];
		}
		
		$reseller = $go_api->db->queryOneRecord("SELECT reseller_user FROM isp_isp_reseller WHERE reseller_userid = ".$reseller_sys_doc_id);
		
		
		$reseller_name = escapeshellcmd($reseller["reseller_user"]);
		$reseller_name = str_replace("/","",$reseller_name);
		*/
		
		session_start();
		$reseller_name = $_SESSION["reseller_image_id"];
		
		echo ($reseller_name != '' && @is_file('../design/reseller_images/nav_'.$reseller_name.'.png'))?'design/reseller_images/nav_'.$reseller_name.'.png':$session_design_path.'/images/nav_oben_logo.png';
		
		?>" width="200" height="78"></td>
      </tr>
    </table></td>
  </tr>
</table>
