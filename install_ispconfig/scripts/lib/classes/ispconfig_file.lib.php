<?php
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

class file{

function rf($file){
  global $mod;
  clearstatcache();
  if(!$fp = fopen ($file, "rb")){
    $mod->log->msg("WARNING: could not open file ".$file, 2);
    return false;
  } else {
    if(filesize($file) > 0){
      $content = fread($fp, filesize($file));
    } else {
      $content = "";
    }
    fclose($fp);
    return $content;
  }
}

function wf($file, $content){
  global $mod;
  $this->mkdirs(dirname($file));
  if(!$fp = fopen ($file, "wb")){
    $mod->log->msg("WARNING: could not open file ".$file, 2);
    return false;
  } else {
    fwrite($fp,$content);
    fclose($fp);
    return true;
  }
}

function af($file, $content){
  global $mod;
  $this->mkdirs(dirname($file));
  if(!$fp = fopen ($file, "ab")){
    $mod->log->msg("WARNING: could not open file ".$file, 2);
    return false;
  } else {
    fwrite($fp,$content);
    fclose($fp);
    return true;
  }
}

function is_file_lfs($path){
    exec('[ -f "'.$path.'" ]', $tmp, $ret);
    return $ret == 0;
}

function no_comments($file, $comment = '#'){
  $content = $this->unix_nl($this->rf($file));
  $lines = explode("\n", $content);
  if(!empty($lines)){
    foreach($lines as $line){
      if(strstr($line, $comment)){
        $pos = strpos($line, $comment);
        if($pos != 0){
          $new_lines[] = substr($line,0,$pos);
        } else {
          $new_lines[] = "";
        }
      } else {
        $new_lines[] = $line;
      }
    }
  }
  if(is_array($new_lines)){
    $content_without_comments = implode("\n", $new_lines);
    $new_lines = NULL;
    return $content_without_comments;
  } else {
    return "";
  }
}

function manual_entries($file, $separator = '#### MAKE MANUAL ENTRIES BELOW THIS LINE! ####'){
  if(is_file($file)){
    $content = $this->rf($file);
    $parts = explode($separator, $content);
    $manual = "\n".trim($parts[1])."\n\n";
    return $manual;
  } else {
    return "";
  }
}

function remove_blank_lines($input, $file = 1){
  //Leerzeilen l�schen
  if($file){
    $content = $this->unix_nl($this->rf($input));
  } else {
    $content = $input;
  }
  $lines = explode("\n", $content);
  if(!empty($lines)){
    foreach($lines as $line){
      if(trim($line) != "") $new_lines[] = $line;
    }
  }
  if(is_array($new_lines)){
    $content = implode("\n", $new_lines);
  } else {
    $content = "";
  }
  if($file){
    $this->wf($input, $content);
  } else {
    return $content;
  }
}

function add_trailing_newline($input, $file = 1) {
  if($file){
    $content = $this->unix_nl($this->rf($input));
  } else {
    $content = $input;
  }
  $content .= "\n";
  if($file){
    $this->wf($input, $content);
  } else {
    return $content;
  }
}

function unix_nl($input){
  $output = str_replace("\r\n", "\n", $input);
  $output = str_replace("\r", "\n", $output);
  return $output;
}

function fileowner($file){
  $owner_id = fileowner($file);
  clearstatcache();
  return $owner_id;
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

function find_includes($file){
  ob_start();
  $httpd_root = system('httpd -V | awk -F"\"" \'$1==" -D HTTPD_ROOT="{print $2}\'');
  ob_end_clean();
  clearstatcache();
  if(is_file($file) && filesize($file) > 0){
    $includes[] = $file;
    $inhalt = $this->unix_nl($this->no_comments($file));
    $lines = explode("\n", $inhalt);
    if(!empty($lines)){
      foreach($lines as $line){
        if(stristr($line, "include ")){
          $include_file = str_replace("\n", "", trim(shell_exec("echo \"$line\" | awk '{print \$2}'")));
          if(substr($include_file,0,1) != "/"){
            $include_file = $httpd_root."/".$include_file;
          }
          if(is_file($include_file)){
            if($further_includes = $this->find_includes($include_file)){
              $includes = array_merge($includes, $further_includes);
            }
          } else {
            if(strstr($include_file, "*")){
              $more_files = explode("\n", shell_exec("ls -l $include_file | awk '{print \$9}'"));
              if(!empty($more_files)){
                foreach($more_files as $more_file){
                  if(is_file($more_file)){
                    if($further_includes = $this->find_includes($more_file)){
                      $includes = array_merge($includes, $further_includes);
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
  }
  if(is_array($includes)){
    $includes = array_unique($includes);
    return $includes;
  } else {
    return false;
  }
}

function edit_dist($var, $val){
  global $$var;
  $files = array("/root/ispconfig/dist.inc.php");
  foreach($files as $file){
    if(is_file($file)){
      $file_content = $this->unix_nl($this->rf($file));
      $lines = explode("\n", $file_content);
      for($i=0;$i<sizeof($lines);$i++){
        $parts = explode("=", $lines[$i]);
        if($parts[0] == $var || $parts[0] == '$'.$var.' '){
          $parts[1] = str_replace($$var, $val, $parts[1]);
        }
        $lines[$i] = implode("=", $parts);
      }
      $file_content = implode("\n", $lines);
      $this->wf($file, $file_content);
    }
  }
}

function getDirectoryListing($dirname, $sortorder = "a", $show_subdirs = 0, $show_subdirfiles = 0, $exts = "", $ext_save = 1){
// This function will return an array with filenames based on the criteria you can set in the variables
// @sortorder : a for ascending (the standard) or d for descending (you can use the "r" for reverse as well, works the same)
// @show_subdirs : 0 for NO, 1 for YES - meaning it will show the names of subdirectories if there are any
// Logically subdirnames will not be checked for the required extentions
// @show_subdirfiles : 0 for NO, 1 for YES - meaning it will show files from the subdirs
// Files from subdirs will be prefixed with the subdir name and checked for the required extentions.
// @exts can be either a string or an array, if not passed to the function, then the default will be a check for common image files
// If exts is set to "all" then all extentions are allowed
// @ext_save : 1 for YES, 0 for NO - meaning it will filter out system files or not (such as .htaccess)

   $dirname = realpath($dirname);
   if (!$exts || empty($exts) || $exts == "") {
       $exts = array("jpg", "gif", "jpeg", "png");
   }
   if ($handle = opendir($dirname)) {
       $filelist = array();
       while (false !== ($file = readdir($handle))) {

           // Filter out higher directory references
           if ($file != "." && $file != "..") {
               // Only look at directories or files, filter out symbolic links
               if ( filetype ($dirname."/".$file) != "link") {
                   // If it's a file, check against valid extentions and add to the list
                   if ( filetype ($dirname."/".$file) == "file" ) {
                       if ($this->checkFileExtension($file, $exts, $ext_save)) {
                                       $filelist[] = $file;
                       }
                   }
                   // If it's a directory and either subdirs should be listed or files from subdirs add relevant names to the list
                   else if ( filetype ($dirname."/".$file) == "dir" && ($show_subdirs == 1 || $show_subdirfiles == 1)) {
                       if ($show_subdirs == 1) {
                           $filelist[] = $file;
                       }
                       if ($show_subdirfiles == 1) {
                           $subdirname = $file;
                           $subdirfilelist = $this->getDirectoryListing($dirname."/".$subdirname."/", $sortorder, $show_subdirs, $show_subdirfiles, $exts, $ext_save);
                           for ($i = 0 ; $i < count($subdirfilelist) ; $i++) {
                               $subdirfilelist[$i] = $subdirname."/".$subdirfilelist[$i];
                           }
                           $filelist = array_merge($filelist, $subdirfilelist);
                       }

                   }

               }
           }
       }
       closedir($handle);

       // Sort the results
       if (count($filelist) > 1) {
           natcasesort($filelist);
           if ($sortorder == "d" || $sortorder == "r" ) {
               $filelist = array_reverse($filelist, TRUE);
           }
       }
       return $filelist;
   }
   else {
       return false;
   }
}

function checkFileExtension($filename, $exts, $ext_save = 1){
   $passed = FALSE;
   if ($ext_save == 1) {
       if (preg_match("/^\./", $filename)) {
           return $passed;
       }
   }
   if ($exts == "all") {
                   $passed = TRUE;
       return $passed;
   }
   if (is_string($exts)) {
       if (eregi("\.". $exts ."$", $filename)) {
                       $passed = TRUE;
           return $passed;
       }
   } else if (is_array($exts)) {
       foreach ($exts as $theExt) {
           if (eregi("\.". $theExt ."$", $filename)) {
               $passed = TRUE;
               return $passed;
           }
       }
   }
   return $passed;
}

/**
   * Delete a file, or a folder and its contents
   *
   * @author      Aidan Lister <aidan@php.net>
   * @version     1.0.3
   * @link        http://aidanlister.com/repos/v/function.rmdirr.php
   * @param       string   $dirname    Directory to delete
   * @return      bool     Returns TRUE on success, FALSE on failure
   */
  function rmdirr($dirname)
  {
    // Sanity check
    if (!file_exists($dirname)) {
      return false;
    }

    // Simple delete for a file
    if (is_file($dirname) || is_link($dirname)) {
      return unlink($dirname);
    }

    // Loop through the folder
    $dir = dir($dirname);
    while (false !== $entry = $dir->read()) {
      // Skip pointers
      if ($entry == '.' || $entry == '..') {
        continue;
      }

      // Recurse
      $this->rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
    }

    // Clean up
    $dir->close();
    return rmdir($dirname);
  }

}
?>