<?php
	
	function isValidSubDomain($subDomain)
	{	// check only [aA-zZ][0-9] character      
		if (ctype_alnum($subDomain))
			return 1;
		else
			return 0;   
	}
    
	function isSubDomainFreeDB($subDomain)
	{	// return 1 = free subdomain
		$link = mysqli_connect("localhost", "user", "password", "dbname");
		if (mysqli_connect_errno()) 
		{
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}
		$query = "SELECT `subdomain` FROM `subdomains` WHERE `subdomain`='" . $subDomain . "'";
		if ($result = mysqli_query($link, $query))
		{
			$row = mysqli_fetch_assoc($result);
			mysqli_free_result($result);
			if (empty($row))
				return 1;
			else
				return 0;
		}
		mysqli_close($link);
	}
    
    function isFreeDirectory($subDomain) 
	{   // return 1 = free directory	
        if (file_exists("/path/to/www/" . $subDomain)) 
            return 0;
        else
            return 1;  
    }

    function isNotMysqlUser($subDomain)
	{    // return 1 = free user
        $link = mysqli_connect("localhost", "user", "password");
        if (mysqli_connect_errno()) 
		{
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }
        $query = "SELECT User FROM mysql.user WHERE `User`='" . $subDomain . "'";
        if ($result = mysqli_query($link, $query))
		{
            $row = mysqli_fetch_assoc($result);
            mysqli_free_result($result);
           if (empty($row))
                return 1;
            else
                return 0;
        }
        mysqli_close($link);    
    }
    
    function isNotMysqlDB($subDomain)
	{    // return 1 = free db	 
        $link = mysqli_connect("localhost", "user", "password");
        if (mysqli_connect_errno()) 
		{
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }
        if (mysqli_select_db($link, $subDomain))
		{
            mysqli_close($link); 
            return 0;
        }
        else
            return 1;
    }
    
    function isFreeCNAME($subDomain)
	{	// return 1 = free CNAME			
		try 
		{
			$soap = new SoapClient("https://www.ovh.com/soapi/soapi-re-1.61.wsdl");
			$session = $soap->login("ovh-login", "ovh-password","fr", false);
			$result = $soap->zoneEntryList($session, "domain.com");			
			$soap->logout($session);
			$i = 0;
			$j = count($result);
			while ($i < $j)
			{
				if (strcmp($subDomain, $result[$i]->subdomain) == 0)
					return 0;						
				$i++;
			}
		} 
		catch(SoapFault $fault) 
		{
			 echo $fault;
		}
		return 1;
    }
	
	function isVirtualHost($subDomain)
	{	// return 1 = free virtualhost			
        if (file_exists("/etc/apache2/sites-available/" . $subDomain . ".domain.com"))        
            return 0;
        else
            return 1; 
	}
	
	function addDirectory($subDomain) 
	{
        $valide = mkdir("/path/to/www/" . $subDomain, 0755, true);
        if ($valide)
            copy("/path/to/files/index.html", "/path/to/www/" . $subDomain . "/index.html");
    }
	
	function addCNAME($subDomain)
	{
		try 
		{
			$soap = new SoapClient("https://www.ovh.com/soapi/soapi-re-1.61.wsdl");
			$session = $soap->login("ovh-login", "ovh-password","fr", false);
			$soap->zoneEntryAdd($session, "domain.com", "'" . $subDomain. "'", "CNAME", "domain.com", false);
			$soap->logout($session);
		} catch(SoapFault $fault) 
		{
			echo $fault;
		}
	}
     
    function addDatabase($subdomain)
	{
        $query = "CREATE USER 'user'@'localhost' IDENTIFIED BY '***';";
        $query = "GRANT USAGE ON * . * TO 'user'@'localhost' IDENTIFIED BY '***' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;";
        $query = "CREATE DATABASE IF NOT EXISTS `user` ;";
        $query = "GRANT ALL PRIVILEGES ON `user` . * TO 'user'@'localhost';";
    }
    
	function addVirtualHost($subDomain)
	{
		exec('/path/to/addVirtualHost /etc/apache2/sites-available/' . $subDomain . '.domain.com ' . $subDomain . '.domain.com ' . $subDomain);
	}
	
    function validationSubDomain($subDomain)
	{
        $valide = array();
        $valide[0] = isValidSubDomain($subDomain);
		if ($valide[0] == 1)
		{
			$valide[1] = isSubDomainFreeDB($subDomain);
			$valide[2] = isFreeDirectory($subDomain);
			$valide[3] = isNotMysqlUser($subDomain);
			$valide[4] = isNotMysqlDB($subDomain);
			$valide[5] = isFreeCNAME($subDomain);
			$valide[6] = isVirtualHost($subDomain);
		}
		return $valide;
    }
    
    function installationSubDomain($subDomain)
	{
		$validation = 1;
		$subDomain = strtolower($subDomain);
		$valide = validationSubDomain($subDomain); 
		$i = 0;
		$j = count($valide);
		while ($i < $j)
		{
			if ($valide[$i] == 0)
			{
				$validation = 0;
				break;
			}
			$i++;
		}
		if ($validation == 1)
		{
			addDirectory($subDomain);
			return 1;
		}			
		else
			return 0;
    }
	
?>
