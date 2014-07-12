<?php
    session_start();
    if(isset($_SESSION["STATUS"]) && $_SESSION["STATUS"] === "loggedin")
    {
        header("location:manage.php");
        die();
    }
    
    $error = '';
    
    if(count($_POST) > 0 && isset($_POST["txtPostmasterEmail"]) && isset($_POST["txtPostmasterPassword"])){
        if(strlen($_POST["txtPostmasterEmail"])>0 && strlen($_POST["txtPostmasterPassword"])>0)
        {
            $LDAPConnection = null;
            
            require_once("helpers/config.php");
            require_once("helpers/loginhelper/login.php");
            
            if(isset($_SESSION["STATUS"]) && $_SESSION["STATUS"] === "loggedin")
            {
                header("location:manage.php");
                die();
            }
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Zimbraadmin - New!</title>
<link rel="stylesheet" type="text/css" href="css/clean.css" />
<link rel="stylesheet" type="text/css" href="css/unsemantic-grid-responsive-tablet.css" />
<link rel="stylesheet" type="text/css" href="css/jqui/jquery-ui-1.10.4.custom.min.css" />
<link rel="stylesheet" type="text/css" href="css/datatables/jquery.dataTables.min.css" />
<link rel="stylesheet" type="text/css" href="css/datatables/jquery.dataTables_themeroller.min.css" />
<style type="text/css">
    form label{
        display: block;
    }
    label.error{
        color: red;
        font-style: italic;
    }
    div.error{
        background-color: #c43c35;
        background-repeat: repeat-x;
        background-image: -khtml-gradient(linear, left top, left bottom, from(#ee5f5b), to(#c43c35));
        background-image: -moz-linear-gradient(top, #ee5f5b, #c43c35);
        background-image: -ms-linear-gradient(top, #ee5f5b, #c43c35);
        background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #ee5f5b), color-stop(100%, #c43c35));
        background-image: -webkit-linear-gradient(top, #ee5f5b, #c43c35);
        background-image: -o-linear-gradient(top, #ee5f5b, #c43c35);
        background-image: linear-gradient(top, #ee5f5b, #c43c35);
        border-color: #c43c35 #c43c35 #882a25;
        border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
        border-radius: 4px;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25);
        -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25);
        -moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25);
        border-style: solid;
        border-width: 1px;
        color: #fff;
        filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ee5f5b', endColorstr='#c43c35', GradientType=0);
        margin-bottom: 20px;
        padding: 5px 0;
        text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
    }
</style>
</head>
<body>
    <div class="grid-container">
        <div class="grid-100">
            <h1>Zimbra Admin</h1>
        </div>
        <?php
            if($error != '')
            {
        ?>
        <div class="grid-100 error">
            <?=$error?>
        </div>
        <?php
            }
        ?>
        <div class="grid-33 push-33">
            <form id="loginform" name="loginform" method="post" action="">
                <label for="txtPostmasterEmail">Postmaster E-Mail</label>
                <input type="email" name="txtPostmasterEmail" id="txtPostmasterEmail" required />
                <label for="txtPostmasterPassword">Postmaster Password</label>
                <input type="password" name="txtPostmasterPassword" id="txtPostmasterPassword" required />
                <label>&nbsp;</label>
                <button name="btnSubmit">Log in!</button>
            </form>
        </div>
    </div>
    <script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui-1.10.4.custom.min.js"></script>
    <script type="text/javascript" src="js/validate/jquery.validate.min.js"></script>
    <script type="text/javascript" src="js/datatables/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        $(function(){
           $("#loginform").validate();
           $("button").button({icons:{primary:"ui-icon-key"}});
        });
    </script>
</body>
</html>