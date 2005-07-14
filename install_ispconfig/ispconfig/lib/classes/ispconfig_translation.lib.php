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

// Implements Message Catalog like utility to PHP

class msgcat {
  
  // PUBLIC VARIABLES
  
  VAR $mclistcount ;   // Counter for the mclist array
  
  // PRIVATE VARIABLES
  
  VAR $mclocale ;      // Identifies current locale
  VAR $mcloaddir ;     // full path to .msg files
  VAR $mclist ;        // Array for the translations for mclocale
  
  VAR $debuglvl ;      // 0 - NONE || 1 - ECHO || 2 - FILE
  VAR $fpdebug ;       // Degub file pointer if needed
  VAR $mccrlf;
  
  // PUBLIC INTERFACE
  
  function msgcat($localstr=de,$loaddir="./",$debug=0)
    {
    global $go_info;
    // Constructor: sets values for initial workout
    $this->mclocale = $localstr ;
    $this->mcloaddir = $loaddir ;
    $this->debug_init($debug) ;
    $this->mclistcount = 0 ;
    $this->mclist_create() ;
    }
    
  function mc($srcstr)
    {
    // Returns translated string for STRING $srcstr
    $retstr = $this->mclist[$srcstr] ;
    if (!$retstr) 
      {
      $retstr = $this->mcunknown($srcstr);
      }
    return $retstr ;
    }
    
  function destroy()
    {
    // Ending file operations
    if ($this->fpdebug)
      {
      $this->debug("===== Ending operations =====") ;
      fclose($this->fpdebug) ;
      }
    }
  // PRIVATE METHODS
  
  function mcunknown($srcstr)
    {
    // Rebuild this function to suit your needs... called when no translation was found
    $this->debug("ERROR: " . $srcstr . " not found in " . $this->mclocale . ".lng file") ;
    return $srcstr ;
    }
    
  function mcset($localestr,$srcstr,$trstr)
    {
    // Sets mc on $localestr locale for $srcstr as $trstr
    if ($localestr==$this->mclocale) {
      $this->mclist += Array($srcstr => $trstr) ;
      $this->debug("O.K.: Added '" . $trstr . "' as '" . $srcstr . "' for locale " . $localestr) ;
      $this->mclistcount++ ;
    } else {
      $this->debug("ERROR: Invalid locale setting on file " . $this->mclocale . ".lng for " . $srcstr) ;
      }
    }
    
  function mclist_create()
    {
    global $go_info;
    // Generates message catalog from file
    	if($go_info["server"]["os"] == "windows"){
		// unix = 1, Win = 2
    		$mccrlf = 2;
    	} else {
    		$mccrlf = 2;
    	}
    $this->mclist = Array() ;
    $fp = fopen($this->mcloaddir . $this->mclocale . ".lng","r") ;
    if ($fp) {
      $this->debug("PROCESSING: File " . $this->mclocale . ".lng in folder " . $this->mcloaddir) ;
      while (!feof($fp))
        {
        $str = fgets($fp,4096) ;
        if (substr($str,0,2)!="//") 
          {
          // Note on UNIX/Linux CRLF is length 2 not 1, change -1 to -2
          $data = explode("#:#",substr($str,0,strlen($str)-$mccrlf)) ;
          $this->mcset($data[0],$data[1],$data[2]) ;
          }
        } 
      fclose($fp) ;
    } else {
      $this->debug("ERROR: File " . $this->mclocale . ".lng does not exist in folder " . $this->loaddir) ;
      }
    }
    
  function debug($msg)
    {
    // Debug management
    switch ($this->debuglvl)
      {
      case 0:
        break ;
      case 1: 
        echo "<!-- " . $msg . " -->\n" ;
        break ;
      case 2: 
        $msg = date("d/m/Y - H:i:s",getdate())." - " . $msg . "\n";
        fputs($this->fpdebug,$msg) ;
        break ;
      }
    }
    
  function debug_init($debug)
    {
    // Debug initialization
    $this->debuglvl = $debug ;
    switch ($this->debuglvl)
      {
      case 0: 
        // Do Nothing
        break ;
      case 1: 
        $this->debug("MSGCAT CLASS v 1.0 INITIALIZATION FOR LOCALE " . $this->mclocale . " ON DIR " . $this->mcloaddir) ;
        break ;
      case 2: 
        $this->fpdebug = fopen($this->loaddir . "debug","a") ;
        if (!$this->fpdebug)
          {
          $this->debuglvl = 1 ;
          $this->debug("Could not open debug file, debuglvl set to 1") ;                
        } else {
          $this->debug("===== " . $GLOBALS["PHP_SELF"] . ": MSGCAT CLASS v 1.0 INITIALIZATION FOR LOCALE " . $this->mclocale . " ON DIR " . $this->mcloaddir . " =====");
          }
        break ;
      }
    }
  }
?>