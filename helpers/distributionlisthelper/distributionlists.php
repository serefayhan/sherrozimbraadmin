<?php
class DistributionLists{
    
    private $auth;
    private $authResult;
    private $accountManager;
    private $allDitributionLists = array();
    private $distributionListsCount;
    private $selectedDistributionListMembers = array();
    
    public function __construct($domainName, $getAllLists = true){
        
        $this->auth = new Zm_Auth(ZIMBRA_SERVER_HOSTNAME, ZIMBRA_ADMIN_USERNAME, ZIMBRA_ADMIN_PASSWORD);
        $this->authResult = $this->auth->login();
        
        $this->accountManager = new Zm_Account($this->auth);
        
        if($getAllLists)
        {
            $distributionLists = $this->accountManager->getAllDistributionLists($domainName);
            if(isset($distributionLists['DL']))
            {
                if(isset($distributionLists['DL']['NAME']))
                {
                    $distributionLists['DL'][0] = $distributionLists['DL'];
                    unset($distributionLists['DL']['DYNAMIC'], $distributionLists['ID'], $distributionLists['NAME'], $distributionLists['A']);
                }
                
                $this->distributionListsCount = count($distributionLists['DL']);
                
                foreach($distributionLists['DL'] as $list){
                    $this->allDitributionLists[$list['NAME']] = array(
                        'ID' => $list['ID']
                    );
                }
                
                unset($distributionLists);
            }
            else
            {
                $this->allDitributionLists = array();
                $this->distributionListsCount = 0;
            }
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
    
    public function createDistributionList($distributionListName)
    {
        $result = $this->accountManager->createDistributionList($distributionListName);
        
        return $this->processCRUDResponse($result);
    }
    
    public function deleteDistributionList($distributionListID)
    {
        $result = $this->accountManager->deleteDistributionList($distributionListID);
        
        return $this->processCRUDResponse($result);
    }
    
    public function addMemberToDistributionList($distributionListID, $memberEMail)
    {
        $result = $this->accountManager->addDistributionListMember($distributionListID, $memberEMail);
        
        return $this->processCRUDResponse($result);
    }
    
    public function deleteMemberFromDistributionList($distributionListID, $memberEMail)
    {
        $result = $this->accountManager->removeDistributionListMember($distributionListID, $memberEMail);
        
        return $this->processCRUDResponse($result);
    }
    
    public function getAllDistributionLists()
    {
        return $this->allDitributionLists;
    }
    
    public function getDistributionListsCount()
    {
        return $this->distributionListsCount();
    }
    
    public function getSelectedDistributionListMembers()
    {
        return $this->selectedDistributionListMembers;
    }
    
    public function getSelectedDistributionListMembersCount()
    {
        return $this->selectedDistributionListMembersCount;
    }
    
    public function getAllDistributionListsAsTableRows()
    {
        $result = '';
        
        foreach($this->allDitributionLists as $listName => $listOptions)
        {
            $result.='<tr data-list-id="'.$listOptions['ID'].'">'.PHP_EOL;
            $result.='<td>'.$listName.'</td>'.PHP_EOL;
            $result.='<td><button class="ui-icon-person">List Members</button></td>'.PHP_EOL;
            $result.='<td><button class="ui-icon-trash">Delete List</button></td>'.PHP_EOL;
            $result.='</tr>'.PHP_EOL;
        }
        
        return $result;
    }
    
    public function getSelectedDistributionListMembersAsTableRows()
    {
        $result = '';
        
        foreach($this->selectedDistributionListMembers as $member)
        {
            $result.='<tr>'.PHP_EOL;
            $result.='<td>'.$member['MEMBER'].'</td>'.PHP_EOL;
            $result.='<td><button class="ui-icon-trash">Delete Member</button></td>'.PHP_EOL;
            $result.='</tr>'.PHP_EOL;
        }
        
        return $result;
    }
    
    public function getSelectedDistributionListMembersAsJSArray(){
        $result = array();
        
        foreach($this->selectedDistributionListMembers as $member)
        {
            $result[]='[\''.$member['MEMBER'].'\',\'<button class="ui-icon-trash">Delete Member</button>\']';
        }
        
        return '['.implode(',',$result).']';
    }
    
    public function getDistributionListMembers($distributionListID)
    {
        $result = false;
        
        $distributionListMembers = $this->accountManager->getDistributionList($distributionListID);
        
        if(isset($distributionListMembers['DL']['DLM']))
        {
            foreach($distributionListMembers['DL']['DLM'] as $member)
            {
                if(!is_array($member))
                {
                    $this->selectedDistributionListMembers[] = array(
                        'MEMBER' => $member
                    );
                }
                else
                {
                    $this->selectedDistributionListMembers[] = array(
                        'MEMBER' => $member['DATA']
                    );
                }
            }
            
            $this->selectedDistributionListMembersCount = count($this->selectedDistributionListMembers);
            
            $result= true;
        }
        else
        {
            $result = false;
        }
        
        return $result;
    }
}
?>