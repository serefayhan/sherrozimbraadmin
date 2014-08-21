<?php
    session_start();
    if(!isset($_SESSION['STATUS']) || $_SESSION['STATUS'] !== 'loggedin')
    {
        header('location:index.php');
        die();
    }
    
    require_once('helpers/config.php');
    require_once('helpers/utils.php');
    require_once('helpers/zmservice/Auth.php');
    require_once('helpers/zmservice/Account.php');
    require_once('helpers/accounthelper/accounts.php');
    require_once('helpers/distributionlisthelper/distributionlists.php');
    $accounts = new Accounts($_SESSION['USER'][1]);
    $ditributionlists = new DistributionLists($_SESSION['USER'][1]);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Zimbraadmin - New!</title>
<link rel="stylesheet" type="text/css" href="css/clean.css" />
<link rel="stylesheet" type="text/css" href="css/unsemantic-grid-responsive-tablet.css" />
<link rel="stylesheet" type="text/css" href="css/manage.css" />
<link rel="stylesheet" type="text/css" href="css/jqui/jquery-ui-1.10.4.custom.min.css" />
<link rel="stylesheet" type="text/css" href="css/datatables/jquery.dataTables.min.css" />
<link rel="stylesheet" type="text/css" href="css/datatables/jquery.dataTables_themeroller.min.css" />
</head>
<body>
    <div class="grid-container">
        <div class="grid-100 error">
        </div>
        <div id="tabs" class="grid-100 grid-parent">
            <ul>
                <li><a href="#emailaccounts">E-Mail Accounts</a></li>
                <li><a href="#distributionlists">Distribution Lists</a></li>
                <li style="float: right;"><a href="javascript:;" onclick="location='logout.php'">Logout</a></li>
                <li style="float: right;"><a href="#statistics">Statistics</a></li>
            </ul>
            <div id="emailaccounts" class="grid-100">
                <h2>E-Mail accounts of <?=$_SESSION['USER'][1]?></h2>
                <hr />
                <div id="createnewaccount" class="grid-100 left-align" style="margin-bottom: 10px;">
                    <button>Create Account</button>
                </div>
                <table class="datatable">
                    <thead>
                        <tr>
                            <th>E-Mail Account</th>
                            <th>Usage (Mb)</th>
                            <th>Quota (Mb)</th>
                            <th>Last Login</th>
                            <th>Change Password</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?=$accounts->exportAccountsAsTableRows()?>
                    </tbody>
                </table>
            </div>
            <div id="distributionlists" class="grid-100">
                <h2>Distribution lists of <?=$_SESSION['USER'][1]?></h2>
                <hr />
                <fieldset class="ui-state-hover ui-corner-all">
                    <legend>Create New Distribution List</legend>
                    <form id="adddistributionlistform" method="post" onsubmit="return false;">
                        <label for="txtnewdistrbutionlistname">Distribution List Name</label>
                        <input type="text" name="txtnewdistrbutionlistname" id="txtnewdistrbutionlistname" />
                        <label>&nbsp;</label>
                        <button type="button">Create List</button>
                        <label>&nbsp;</label>
                        <input type="hidden" name="method" id="method" value="createNewDistributionList" />
                    </form>
                </fieldset>
                <table class="datatable">
                    <thead>
                        <tr>
                            <th>Distribution List Name</th>
                            <th>Modify Subscribers</th>
                            <th>Delete Distribution List</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?=$ditributionlists->getAllDistributionListsAsTableRows()?>
                    </tbody>
                </table>
            </div>
            <div id="statistics" class="grid-100">
                <h2>Statistics for <?=$_SESSION['USER'][1]?></h2>
                <hr />
                <div class="grid-33 push-33">
                    <table style="width: 100%;">
                        <tr>
                            <td>Number of Accounts</td>
                            <td id="numberofaccounts"><?=$accounts->getMailAccountCount()?></td>
                        </tr>
                        <tr>
                            <td>Limit / Used</td>
                            <td id="quotausagebyaccounts"><?=$accounts->getDomainSpaceLimit()?> / <?=$accounts->getDomainUsedSpace()?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div id="addnewaccountdialog" style="display: none;">
        <form name="addaccountform" id="addaccountform">
            <label for="txtnewaccount">New Account Name</label>
            <input type="text" name="txtnewaccount" id="txtnewaccount" size="35"/>
            <label for="txtnewaccountpassword">New Password</label>
            <input type="password" name="txtnewaccountpassword" id="txtnewaccountpassword"/>
            <label for="txtnewaccountpasswordagain">Confirm New Password</label>
            <input type="password" name="txtnewaccountpasswordagain" id="txtnewaccountpasswordagain"/>
            <input type="hidden" name="method" id="method" value="createNewAccount" />
        </form>
    </div>
    <div id="deleteaccountdialog" style="display: none;">
        <p>
            Are you sure to delete this account:<span id="accountnametodelete"></span>
        </p>
    </div>
    <div id="changepassworddialog" style="display: none;">
        <form name="changepasswordform" id="changepasswordform">
            <label for="txtpwmailaccount">Account</label>
            <input type="email" name="txtpwmailaccount" id="txtpwmailaccount" size="35" readonly/>
            <label for="txtpwmailaccountpassword">New Password</label>
            <input type="password" name="txtpwmailaccountpassword" id="txtpwmailaccountpassword"/>
            <label for="txtpwmailaccountpasswordagain">Confirm New Password</label>
            <input type="password" name="txtpwmailaccountpasswordagain" id="txtpwmailaccountpasswordagain"/>
            <input type="hidden" name="method" id="method" value="changeAccountPassword" />
        </form>
    </div>
    <div id="deletedistributionlistdialog">
        <p>
            Are you sure to delete this distribution list:<span id="distributionlistnametodelete"></span>
        </p>
    </div>
    <div id="showdistributionlistmembersdialog" style="display: none";>
        <fieldset class="ui-state-hover ui-corner-all">
            <legend>Add New Member</legend>
            <form id="addmembertodistributionlistform" name="addmembertodistributionlistform" onsubmit="return false;">
                <label for="txtaddnewmembertodistributionlist">Member Email</label>
                <input type="text" name="txtaddnewmembertodistributionlist" id="txtaddnewmembertodistributionlist" />
                <label>&nbsp;</label>
                <button type="button">Add to List</button>
                <label>&nbsp;</label>
                <input type="hidden" name="txtdistrbutionlistidtoadd" id="txtdistrbutionlistidtoadd" />
                <input type="hidden" name="method" id="method" value="addMemberToDistributionList" />
            </form>
        </fieldset>
        <table class="ajaxdatatable">
            <thead>
                <tr>
                    <th>Member Email</th>
                    <th>Delete Member</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
    <div id="deletedistributionlistmemberdialog">
        <p>
            Are you sure to delete this member from distribution list:<span id="distributionlistmembernametodelete"></span>
        </p>
    </div>
    <div id="loadingoverlay" class="ui-widget-overlay ui-front"></div>
    <div id="loadingmessage" class="ui-widget-shadow"></div>
    <div id="notification" class="ui-tooltip ui-widget ui-corner-all ui-widget-content" title="">
        <div class="ui-tooltip-content"></div>
    </div>
    <script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui-1.10.4.custom.min.js"></script>
    <script type="text/javascript" src="js/validate/jquery.validate.min.js"></script>
    <script type="text/javascript" src="js/datatables/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        $(function(){
            
            $("#loadingmessage").position({
                of: "#tabs"
            });
            
            $("#loadingoverlay, #loadingmessage").hide();
            
            $(document)
            .ajaxStart(function(){
                $("#loadingoverlay").show();
                $("#loadingmessage")
                .show()
                .position({
                    of: "#tabs"
                });
            })
            .ajaxStop(function(){
                $("#loadingoverlay, #loadingmessage").hide();
            });
            
            $("#changepasswordform").validate({
                rules: {
                    txtpwmailaccount: {
                        required: true,
                        email: true
                    },
                    txtpwmailaccountpassword: "required",
                    txtpwmailaccountpasswordagain:{
                        required: true,
                        equalTo: "#txtpwmailaccountpassword"
                    }
                },
                messages: {
                    txtpwmailaccount: "Please enter valid e-mail address",
                    txtpwmailaccountpassword: "Please enter new password",
                    txtpwmailaccountpasswordagain: {
                        required: "Please confirm new password",
                        equalTo: "Please enter the same password as above"
                    }
                }
            });
            
            $("#addaccountform").validate({
                rules: {
                    txtnewaccount: "required",
                    txtnewaccountpassword: "required",
                    txtnewaccountpasswordagain:{
                        required: true,
                        equalTo: "#txtnewaccountpassword"
                    }
                },
                messages: {
                    txtnewaccount: "Please enter account name",
                    txtnewaccountpassword: "Please enter new password",
                    txtnewaccountpasswordagain: {
                        required: "Please confirm new password",
                        equalTo: "Please enter the same password as above"
                    }
                }
            });
            
            $("#adddistributionlistform").validate({
                rules: {
                    txtnewdistrbutionlistname: "required"
                },
                messages: {
                    txtnewdistrbutionlistname: "Please enter list name"
                }
            });
            
            $("#addmembertodistributionlistform").validate({
                rules: {
                    txtaddnewmembertodistributionlist: {
                        required: true,
                        email: true
                    }
                },
                messages: {
                    txtaddnewmembertodistributionlist: "Please enter valid e-mail address"
                }
            })
            
            $(".datatable").DataTable({
                "jQueryUI": true
            });
            
            $('.datatable tbody, .ajaxdatatable tbody').on( 'click', 'tr', function () {
                if ( !$(this).hasClass('selected') )
                {
                    var parentTable = $(this).parentsUntil('div').last();
                    $(parentTable).DataTable().$('tr.selected').removeClass('selected');
                    $(this).addClass('selected');
                }
            });
            
            $("#tabs").tabs();
            
            $("#txtaddnewmembertodistributionlist").autocomplete({
                appendTo: "#showdistributionlistmembersdialog",
                source: function(request, callback){
                    callback(
                        $("#emailaccounts .datatable")
                        .DataTable()
                        .column(0)
                        .data()
                        .filter(
                            function(v,i){
                                return v.indexOf(request.term)>=0
                            }
                        )
                    )
                }
            });
            
            initAccountsGridButtons();
            initAccountsGridPagingEvent();
            initDistributionListsGridButtons();
            initDistributionListsGridPagingEvent();
            $("#emailaccounts #createnewaccount button")
                .button({
                    icons: {primary: "ui-icon-plus"}
                })
                .on('click', function(){
                    $("#addnewaccountdialog").dialog("open");
                });
            $("fieldset button")
                .button({
                    icons: {primary: "ui-icon-plus"}
                });
            $("#adddistributionlistform button")
                .on('click', function(){
                    processCreateNewDistributionList();
                });
            $("#addmembertodistributionlistform button")
                .on('click', function(){
                    processAddMemberToDistributionList();
                });
            
            $("#addnewaccountdialog").dialog({
                dialogClass: "no-close",
                autoOpen: false,
                closeOnEscape: false,
                modal: true,
                title: "Create New Account",
                buttons:[
                    {
                        text: "Create",
                        icons: {primary: "ui-icon-plus"},
                        click: function(){ processCreateNewAccount(); }
                    },
                    {
                        text: "Cancel",
                        icons: {primary: "ui-icon-circle-close"},
                        click: function(){ $(this).dialog("close"); }
                    }
                ]
            });    
            $("#changepassworddialog").dialog({
                dialogClass: "no-close",
                autoOpen: false,
                closeOnEscape: false,
                modal: true,
                title: "Change Account's Password",
                buttons:[
                    {
                        text: "Change",
                        icons: {primary: "ui-icon-key"},
                        click: function(){ processChangeAccountPassword(); }
                    },
                    {
                        text: "Cancel",
                        icons: {primary: "ui-icon-circle-close"},
                        click: function(){ $(this).dialog("close"); }
                    }
                ]
            });
            $("#deleteaccountdialog").dialog({
                dialogClass: "no-close",
                autoOpen: false,
                closeOnEscape: false,
                modal: true,
                title: "Delete E-mail Account",
                buttons:[
                    {
                        text: "Delete",
                        icons: {primary: "ui-icon-trash"},
                        click: function(){ processDeleteEmailAccount(); }
                    },
                    {
                        text: "Cancel",
                        icons: {primary: "ui-icon-circle-close"},
                        click: function(){ $(this).dialog("close"); }
                    }
                ]
            });
            $("#showdistributionlistmembersdialog").dialog({
                dialogClass: "no-close",
                autoOpen: false,
                closeOnEscape: false,
                modal: true,
                title: "Distribution List Members",
                width: 600,
                buttons:[
                    {
                        text: "Close",
                        icons: {primary: "ui-icon-close"},
                        click: function(){ $(this).dialog("close"); $(".ajaxdatatable").DataTable().destroy(); $(".ajaxdatatable tbody").html(''); }
                    }
                ]
            });
            $("#deletedistributionlistdialog").dialog({
                dialogClass: "no-close",
                autoOpen: false,
                closeOnEscape: false,
                modal: true,
                title: "Delete Distribution list",
                buttons:[
                    {
                        text: "Delete",
                        icons: {primary: "ui-icon-trash"},
                        click: function(){ processDeleteDistributionList(); }
                    },
                    {
                        text: "Cancel",
                        icons: {primary: "ui-icon-circle-close"},
                        click: function(){ $(this).dialog("close"); }
                    }
                ]
            });
            $("#deletedistributionlistmemberdialog").dialog({
                dialogClass: "no-close",
                autoOpen: false,
                closeOnEscape: false,
                modal: true,
                title: "Delete Distribution list Member",
                buttons:[
                    {
                        text: "Delete",
                        icons: {primary: "ui-icon-trash"},
                        click: function(){ processDeleteDistributionListMember(); }
                    },
                    {
                        text: "Cancel",
                        icons: {primary: "ui-icon-circle-close"},
                        click: function(){ $(this).dialog("close"); }
                    }
                ]
            });
        });
        
        function showNotification(text)
        {            
            $("#notification div").html(text);
            $("#notification").show();
            $("#notification").position({
                my: "center top+25",
                at: "center top",
                of: ".grid-container"
            });
            setTimeout(function(){ $("#notification").hide("drop", { direction: "up" }, "slow"); },3000);
        }
        
        function initAccountsGridPagingEvent()
        {
            $("#emailaccounts .datatable").on("draw.dt", function(){
                initAccountsGridButtons();
            });
        }
        
        function initDistributionListsGridPagingEvent()
        {
            $("#distributionlists .datatable").on("draw.dt", function(){
                initDistributionListsGridButtons();
            });
        }
        
        function initAccountsGridButtons() {
            $("#emailaccounts table td button.ui-icon-key:not(ui-widget)")
                .button({
                    icons: {primary: "ui-icon-key"}
                })
                .on('click', function(){
                    $("#txtpwmailaccount").val($(this).parentsUntil("tbody").last().find("td:first").html());
                    $("#changepassworddialog").dialog("open");
                });
            $("#emailaccounts table td button.ui-icon-trash:not(ui-widget)")
                .button({
                    icons: {primary: "ui-icon-trash"}
                })
                .on('click', function(){
                    $("#accountnametodelete").html($(this).parentsUntil("tbody").last().find("td:first").html());
                    $("#deleteaccountdialog").dialog("open");
                });
        }
        
        function initDistributionListsGridButtons()
        {
            $("#distributionlists table td button.ui-icon-person:not(ui-widget)")
                .button({
                    icons: {primary: "ui-icon-person"}
                })
                .on('click', function(){
                    loadDistributionListMembers(this);
                });
            $("#distributionlists table td button.ui-icon-trash:not(ui-widget)")
                .button({
                    icons: {primary: "ui-icon-trash"}
                })
                .on('click', function(){
                    $("#distributionlistnametodelete").html($(this).parentsUntil("tbody").last().find("td:first").html());
                    $("#deletedistributionlistdialog").dialog("open");
                });
        }
        
        function updateStatistics()
        {
            $.ajax({
                method: 'POST',
                url: 'xhr.php',
                data: {method: 'getAccountsStatistics'}
            })
            .done(function(data){
                data = eval('('+data+')');
                $("#numberofaccounts").html(data.n);
                $("#quotausagebyaccounts").html(data.q);
            });
        }
        
        function reloadAccountsGrid()
        {
            $("#emailaccounts .datatable").DataTable().destroy();
            $("#emailaccounts .datatable tbody").html('');
            
            $.ajax({
                method: 'POST',
                url: 'xhr.php',
                data: {method: 'getAccountsAsTableRow'}
            })
            .done(function(data){
                $("#emailaccounts .datatable tbody").html(data);
                $("#emailaccounts .datatable").DataTable({
                    "jQueryUI": true
                });
            });
        }
        
        function reloadDistributionListsGridGrid()
        {
            $("#distributionlists .datatable").DataTable().destroy();
            $("#distributionlists .datatable tbody").html('');
            
            $.ajax({
                method: 'POST',
                url: 'xhr.php',
                data: {method: 'getDistributionListsAsTableRow'}
            })
            .done(function(data){
                $("#distributionlists .datatable tbody").html(data);
                $("#distributionlists .datatable").DataTable({
                    "jQueryUI": true
                });
            });
        }
        
        function processCreateNewAccount(){
            if($("#addaccountform").valid())
            {
                $.ajax({
                    method: 'POST',
                    url: 'xhr.php',
                    data: $("#addaccountform").serialize()
                })
                .done(function(data){
                    if (data==1)
                    {
                        $("#addnewaccountdialog").dialog("close");
                        reloadAccountsGrid();
                        updateStatistics();
                    }
                    else
                    {
                        alert(data);
                    }
                });
            }
        }
        
        function processChangeAccountPassword(){
            if($("#changepasswordform").valid())
            {
                $.ajax({
                    method: 'POST',
                    url: 'xhr.php',
                    data: $("#changepasswordform").serialize()
                })
                .done(function(data){
                    if (data==1)
                    {
                        $("#changepassworddialog").dialog("close");
                        showNotification("Password changed successfully");
                    }
                    else
                    {
                        alert(data);
                    }
                });
            }
        }
        
        function processDeleteEmailAccount(){
            $.ajax({
                method: 'POST',
                url: 'xhr.php',
                data: {
                    txtaccountname: $("#accountnametodelete").text(),
                    method: 'deleteAccount'
                }
            })
            .done(function(data){
                if (data==1)
                {
                    $("#deleteaccountdialog").dialog("close");
                    reloadAccountsGrid();
                    updateStatistics();
                }
                else
                {
                    alert(data);
                }
            });
        }
        
        function processCreateNewDistributionList(){
            if ($("#txtnewdistrbutionlistname").valid())
            {
                $.ajax({
                    method:'POST',
                    url:'xhr.php',
                    data: $("#adddistributionlistform").serialize()
                })
                .done(function(data){
                    if (data==1)
                    {
                        reloadDistributionListsGridGrid();
                    }
                    else
                    {
                        alert(data);
                    }
                });
            }
        }
        
        function getSelectedDistributionListID()
        {
            return $("#distributionlists .datatable").DataTable().$("tr.selected").data("list-id");
        }
        
        function getSelectedDistributionListsListMemberButton()
        {
            return $("#distributionlists .datatable").DataTable().$("tr.selected").find("button.ui-icon-person").get(0);
        }
        
        function processDeleteDistributionList(){
            $.ajax({
                method:'POST',
                url:'xhr.php',
                data: {
                    method: 'deleteDistributionList',
                    txtdistributionlistid: getSelectedDistributionListID()
                }
            })
            .done(function(data){
                if (data==1)
                {
                    $("#deletedistributionlistdialog").dialog("close");
                    reloadDistributionListsGridGrid();
                }
                else
                {
                    alert(data);
                }
            });
        }
        
        function loadDistributionListMembers(sender){
            $.ajax({
                method: 'POST',
                url: 'xhr.php',
                data: {
                    method: 'getDistributionListMembersAsJSArray',
                    distributionListID: $(sender).parentsUntil("tbody").last().data("list-id")
                }
            })
            .done(function(data){
                $(".ajaxdatatable").DataTable({
                    "jQueryUI": true,
                    "data": eval(data),
                    "columns": [
                        { "title": "Member E-Mail" },
                        { "title": "Delete Member" }
                    ],
                    "paging": false,
                    "ordering": false
                    //"scrollY": "200px",
                    //"scrollCollapse": true
                });
                $(".ajaxdatatable button.ui-icon-trash")
                .button({
                    icons: {primary: "ui-icon-trash"}
                })
                .on('click', function(){
                    $("#distributionlistmembernametodelete").html($(this).parentsUntil("tbody").last().find("td:first").html());
                    $("#deletedistributionlistmemberdialog").dialog("open");
                });
                $("#showdistributionlistmembersdialog").dialog("open");
                $("#txtdistrbutionlistidtoadd").val(getSelectedDistributionListID());
            });
        }
        
        function processAddMemberToDistributionList()
        {
            if ($("#addmembertodistributionlistform").valid()) {
                $.ajax({
                    method:'POST',
                    url:'xhr.php',
                    data: $("#addmembertodistributionlistform").serialize()
                })
                .done(function(data){
                    if (data==1)
                    {
                        $(".ajaxdatatable").DataTable().destroy();
                        $(".ajaxdatatable tbody").html('');
                        loadDistributionListMembers(getSelectedDistributionListsListMemberButton());
                    }
                    else
                    {
                        alert(data);
                    }
                });
            }
        }
        
        function processDeleteDistributionListMember()
        {
            $.ajax({
                method:'POST',
                url:'xhr.php',
                data: {
                    method: 'deleteDistributionListMember',
                    txtdistrbutionlistidtodelete: getSelectedDistributionListID(),
                    txtmembertodelete: $("#distributionlistmembernametodelete").text()
                }
            })
            .done(function(data){
                if (data==1)
                {
                    $(".ajaxdatatable").DataTable().destroy();
                    $(".ajaxdatatable tbody").html('');
                    $("#deletedistributionlistmemberdialog").dialog("close");
                    loadDistributionListMembers(getSelectedDistributionListsListMemberButton());
                }
                else
                {
                    alert(data);
                }
            });
        }
    </script>
</body>
</html>