<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
        <meta http-equiv="Content-Type" content="text/html;
        charset=iso-8859-1" />
        <title>OOP in PHP</title>
        <?php include("class_lib.php"); ?>
</head>
<body>
        <?php  
                $stefan = new person("Stefan Mischook");   
                echo "Stefan's full name: " .  $stefan->get_name() ;  

                /*  
                Since $pinn_number was declared private, this line of code 
                will generate an error. Try it out!   
                */  
                 
                echo "<P>Tell me private stuff: ".$stefan->pinn_number;  

                
                $james = new employee("Johnny Fingers");
                echo "<P>---> " . $james->get_name();
        ?>  

</body>
</html>