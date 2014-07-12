<?php
class Accounts{
    
    private $auth;
    private $authResult;
    private $accountManager;
    private $allAccountsWithQuotas = array();
    private $mailAccountCount;
    private $domainUsedSpace = 0;
    private $domainSpaceLimit = 0;
    
    public function __construct($domainName, $getAllAccounts = true){
        
        $this->auth = new Zm_Auth(ZIMBRA_SERVER_HOSTNAME, ZIMBRA_ADMIN_USERNAME, ZIMBRA_ADMIN_PASSWORD);
        $this->authResult = $this->auth->login();
        
        $this->accountManager = new Zm_Account($this->auth);
        
        if($getAllAccounts)
        {
        
            $allAccounts = $this->accountManager->getAllAccounts($domainName);
            $quotas = $this->accountManager->getQuotaUsage($domainName);
            
            $this->mailAccountCount = $quotas['SEARCHTOTAL'];
            
            if($this->mailAccountCount==1)
            {
                $allAccounts[0] = $allAccounts;
                unset($allAccounts['ID'],$allAccounts['NAME'],$allAccounts['A']);
                //sadece 1 tane hesap var
                //birden fazla hesabın olduğu duruma getir
                $accountInfo = $quotas['ACCOUNT'];
                unset($quotas['ACCOUNT']);
                
                $quotas['ACCOUNT'][0] = $accountInfo;
                unset($accountInfo);
            }
            
            //collect account informatons
            foreach ($quotas['ACCOUNT'] as $quotaInfo)
            {
                $this->allAccountsWithQuotas[$quotaInfo['NAME']] = array(
                    'USED' => $quotaInfo['USED']/(1024*1024),
                    'LIMIT' => $quotaInfo['LIMIT']/(1024*1024)
                );
                $this->domainUsedSpace += $quotaInfo['USED'];
                $this->domainSpaceLimit += $quotaInfo['LIMIT'];
            }
            
            foreach ($allAccounts as $account)
            {
                $lastLoginIndex = null;
                for($i = 145; $i < 166; $i++)
                {
                    if($account['A'][$i]['N'] == 'zimbraLastLogonTimestamp')
                    {
                        $lastLoginIndex = $i;
                        break;
                    }
                }
                
                $this->allAccountsWithQuotas[$account['NAME']]['LASTLOGIN'] = $lastLoginIndex == 0 ? "Never logged in" : strftime('%d %B %Y %T', strtotime($value['A'][$lastLoginIndex]['DATA']));
            }
            
            unset($allAccounts);
        }
    }
    
    private function processCRUDResponse($response)
    {
        $result = null;
        
        if(gettype($response) == 'object' && get_class($response)=='SoapFault')
        {
            $result = $response->faultstring;
        }else{
            $result = true;
        }
        
        return $result;
    }
    
    public function createAccount($accountName, $password){
        $result = $this->accountManager->createAccount($accountName, $password);
        
        return $this->processCRUDResponse($result);
    }
    
    public function changeAccountPassword($accountName, $newPassword)
    {
        $result = $this->accountManager->setAccountPassword($accountName, $newPassword);
        
        return $this->processCRUDResponse($result);
    }
    
    public function deleteAccount($accountName)
    {
        if(strpos('postmaster',$accountName)!== false)
        {
            return 'Cannot delete postmaster account';
            exit();
        }
        
        $result = $this->accountManager->deleteAccount($accountName);
        
        return $this->processCRUDResponse($result);
    }
    
    public function getAllAccountsWithQuotas(){
        return $this->allAccountsWithQuotas;
    }
    
    public function getMailAccountCount(){
        return $this->mailAccountCount;
    }
    
    public function getDomainUsedSpace(){
        return $this->domainUsedSpace;
    }
    
    public function getDomainSpaceLimit(){
        return $this->domainSpaceLimit;
    }
    
    public function exportAccountsAsTableRows(){
        $result= '';
        
        foreach($this->allAccountsWithQuotas as $accountName => $accountOptions)
        {
            $result.='<tr>'.PHP_EOL;
            $result.='<td>'.$accountName.'</td>'.PHP_EOL;
            $result.='<td>'.sprintf('%.2f',$accountOptions['USED']).'</td>'.PHP_EOL;
            $result.='<td>'.sprintf('%.2f',$accountOptions['LIMIT']).'</td>'.PHP_EOL;
            $result.='<td>'.$accountOptions['LASTLOGIN'].'</td>'.PHP_EOL;
            $result.='<td><button class="ui-icon-key">Change Password</button></td>'.PHP_EOL;
            $result.='<td><button class="ui-icon-trash">Delete Account</button></td>'.PHP_EOL;
            $result.='</tr>'.PHP_EOL;
        }
        
        return $result;
    }
}
?>