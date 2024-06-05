<!DOCTYPE html>
<html>
<head>
	<title>Login Form</title>
</head>

<body style="background-color:#99240b; ">
	<h1 style="background-color:#251282; color: White; text-align:center;">Login Page</h1>

	<form action="LoginCheckForm.php" method="post">
    	<table>
        	<tr>
            	<td style="color:White; font-size: 20px;">Employee Username :</td>
            	<td>
                	<input style="font-size: 20px; padding: 5px;" type="text" name="employee_user" placeholder="Enter username">
            	</td>
        	</tr>
        	<tr>
            	<td style="color:White; font-size: 20px;">Employee Password :</td>
            	<td>
                	<input style="font-size: 20px; padding: 5px;" type="password" name="employee_password" placeholder="Enter password">
            	</td>
        	</tr>
        	<tr>
            	<td style="color:White; text-align:left; font-size: 20px;">Employee Login :</td>
            	<td>
                	<input style="font-size: 20px; padding: 10px 20px;" type="submit" name="employee_login" value="Employee Login">
            	</td>
        	</tr>
    	</table>

    	<br>

    	<table style="float:middle; margin-left: 15px;">
        	<tr>
            	<td style="color:White; font-size: 20px;">Client Username :</td>
            	<td>
                	<input style="font-size: 20px; padding: 5px;" type="text" name="client_user" placeholder="Enter username">
            	</td>
        	</tr>
        	<tr>
            	<td style="color:White; font-size: 20px;">Client Password :</td>
            	<td>
                	<input style="font-size: 20px; padding: 5px;" type="password" name="client_password" placeholder="Enter password">
            	</td>
        	</tr>
        	<tr>
            	<td style="color:White; text-align:left; font-size: 20px;">Client Login :</td>
            	<td>
                	<input style="font-size: 20px; padding: 10px 20px;" type="submit" name="client_login" value="Client Login">
            	</td>
            	<td style="color:#321ba6; text-align:right; font-size: 30px;">
                	<b>Don't have an account? Register any time you want</b><br>
                	&nbsp;<b>Click Here Now!</b>
                	<a style="color:#321ba6; font-size: 20px;" href="ClientRegistration.php"><b>Register</b></a>
            	</td>
        	</tr>
    	</table>
	</form>
</body>
</html>