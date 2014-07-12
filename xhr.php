<?php
    session_start();
    if(!isset($_SESSION['STATUS']) || $_SESSION['STATUS'] !== 'loggedin')
    {
        die();
    }
    
    require_once('helpers/config.php');
    require_once('helpers/utils.php');
    require_once('helpers/zmservice/Auth.php');
    require_once('helpers/zmservice/Account.php');
    require_once('helpers/accounthelper/accounts.php');
    require_once('helpers/distributionlisthelper/distributionlists.php');
    
    switch($_POST['method'])
    {
        case 'getAccountsAsTableRow':
            getAccountsAsTableRow();
            break;
        case 'getDistributionListsAsTableRow':
            getDistributionListsAsTableRow();
            break;
        case 'getAccountsStatistics':
            getAccountsStatistics();
            break;
        case 'getDistributionListMembersAsJSArray':
            if(isset($_POST['distributionListID']))
            {
                getDistributionListMembersAsJSArray();
            }
            else
            {
                returnEmptyList();
            }
            break;
        case 'createNewAccount':
            if(isset($_POST['txtnewaccount']))
            {
                createNewAccount();
            }
            break;
        case 'changeAccountPassword':
                changeAccountPassword();
            break;
        case 'deleteAccount':
                deleteAccount();
            break;
        case 'createNewDistributionList':
            if(isset($_POST['txtnewdistrbutionlistname']))
            {
                createNewDistributionList();
            }
            break;
        case 'deleteDistributionList':
            if(isset($_POST['txtdistributionlistid']))
            {
                deleteDistributionList();
            }
            break;
        case 'addMemberToDistributionList':
            if(isset($_POST['txtdistrbutionlistidtoadd']) && isset($_POST['txtaddnewmembertodistributionlist']))
            {
                addMemberToDistributionList();
            }
            break;
        case 'deleteDistributionListMember':
            if(isset($_POST['txtdistrbutionlistidtodelete']) && isset($_POST['txtmembertodelete']))
            {
                deleteMemberFromDistributionList();
            }
            break;
    }
    
    function getAccountsAsTableRow()
    {
        $accountManager = new Accounts($_SESSION['USER'][1]);
        
        echo $accountManager->exportAccountsAsTableRows();
    }
    
    function getDistributionListsAsTableRow()
    {
        $distributionLists = new DistributionLists($_SESSION['USER'][1]);
        
        echo $distributionLists->getAllDistributionListsAsTableRows();
    }
    
    function getAccountsStatistics()
    {
        $accountManager = new Accounts($_SESSION['USER'][1]);
        
        echo '{ n:'
            .$accountManager->getMailAccountCount()
            .', q:"'
            .$accountManager->getDomainSpaceLimit().' / '.$accountManager->getDomainUsedSpace()
            .'"}';
    }
    
    function createNewAccount()
    {
        $accountManager = new Accounts($_SESSION['USER'][1], false);
        $accountName= '';
        
        if( strpos($_POST['txtnewaccount'],'@') === false )
        {
            $accountName = $_POST['txtnewaccount'].'@'.$_SESSION['USER'][1];
        }
        
        $result = $accountManager->createAccount($accountName, $_POST['txtnewaccountpassword']);
        
        echo $result;
    }
    
    function changeAccountPassword()
    {
        $accountManager = new Accounts($_SESSION['USER'][1], false);
        $accountName= '';
        
        if( strpos($_POST['txtpwmailaccount'],'@') === false )
        {
            $accountName = $_POST['txtpwmailaccount'].'@'.$_SESSION['USER'][1];
        }
        else
        {
            $accountName = $_POST['txtpwmailaccount'];
        }
        
        $result = $accountManager->changeAccountPassword($accountName, $_POST['txtpwmailaccountpassword']);
        
        echo $result;
    }
    
    function deleteAccount()
    {
        $accountManager = new Accounts($_SESSION['USER'][1], false);
        $accountName= '';
        
        if( strpos($_POST['txtaccountname'],'@') === false )
        {
            $accountName = $_POST['txtaccountname'].'@'.$_SESSION['USER'][1];
        }
        else
        {
            $accountName = $_POST['txtaccountname'];
        }
        
        $result = $accountManager->deleteAccount($accountName);
        
        echo $result;
    }
    
    function createNewDistributionList(){
        $distributionListsManager = new DistributionLists($_SESSION['USER'][1], false);
        
        $distributonListName= '';
        
        if( strpos($_POST['txtnewdistrbutionlistname'],'@') === false )
        {
            $distributonListName = $_POST['txtnewdistrbutionlistname'].'@'.$_SESSION['USER'][1];
        }
        else
        {
            $distributonListName = $_POST['txtnewdistrbutionlistname'];
        }
        
        $result = $distributionListsManager->createDistributionList($distributonListName);
        
        echo $result;
    }
    
    function deleteDistributionList()
    {
        $distributionLists = new DistributionLists($_SESSION['USER'][1], false);
        $result = $distributionLists->deleteDistributionList($_POST['txtdistributionlistid']);
        
        echo $result;
    }
    
    function addMemberToDistributionList()
    {
        $distributionLists = new DistributionLists($_SESSION['USER'][1], false);
        $result = $distributionLists->addMemberToDistributionList($_POST['txtdistrbutionlistidtoadd'], $_POST['txtaddnewmembertodistributionlist']);
        
        echo $result;
    }
    
    function deleteMemberFromDistributionList(){
        $distributionLists = new DistributionLists($_SESSION['USER'][1], false);
        $result = $distributionLists->deleteMemberFromDistributionList($_POST['txtdistrbutionlistidtodelete'], $_POST['txtmembertodelete']);
        
        echo $result;
    }
    
    function getDistributionListMembersAsJSArray()
    {
        $distributionLists = new DistributionLists($_SESSION['USER'][1], false);
        if($distributionLists->getDistributionListMembers($_POST['distributionListID']))
        {
            $listMembers = $distributionLists->getSelectedDistributionListMembersAsJSArray();
            echo $listMembers;
        }
        else
        {
            returnEmptyList();
        }
    }
    
    function returnEmptyList()
    {
        echo '[]';
    }
?>