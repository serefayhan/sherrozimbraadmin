<?php
    /*
     * ilk olarak ldap ile sunucuya verilen kullanıcı adı ve
     * sifreyle baglanmayi deniyoruz
     */
    $postmasterEmailParts = explode('@', $_POST['txtPostmasterEmail']);
    $postmasterEmailParts[] = explode('.', $postmasterEmailParts[1]);
    $postmasterPassword = $_POST["txtPostmasterPassword"];
    
    try
    {
        // Just in case your Zimbra server is setup in a format like "zimbra.domain.com"
        $LDAPConnection= ldap_connect(ZIMBRA_SERVER_HOSTNAME,389);
    }
    catch(Exception $e)
    {
        $error.= 'Can\'t connect to LDAP server.' . $e->getMessage();
    }
    
    if (!ldap_set_option($LDAPConnection,LDAP_OPT_PROTOCOL_VERSION,3))
    {
        $error.= 'LDAP Server protocol error.';
    }
    
    try
    {
        if($postmasterEmailParts[0] == 'postmaster')
        {
            $LDAPbind = ldap_bind($LDAPConnection, 'uid='.$postmasterEmailParts[0].',ou=people,dc='.implode(',dc=',$postmasterEmailParts[2]), $postmasterPassword);
        }
        
        if(!$LDAPbind)
        {
            $error.= 'Username and/or password not correct or not authorized.';
        }
    }
    catch(Exception $e)
    {
        $error.= 'Unable to bind: ' . $e->getMessage();
    }
    
    if(strlen($error)==0)
    {
        $_SESSION['STATUS']= 'loggedin';
        $_SESSION['USER']= $postmasterEmailParts;
    }
?>