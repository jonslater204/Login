<!-- DOCTYPE html -->
<html>
<head>
<title>Login</title>
</head>
<body>
<?php
if (isset($_POST['submit'])) {
  require('db.inc.php');
  require('security.inc.php');
  if (security::valid($_POST['username'], $_POST['password'])) {
?>
  <h1>Success</h1>
<?php
  } else {
?>
  <h1>Failed</h1>
  <p>Check your username and password and <a href="login.php">try again</a>.</p>
<?php
  }
} else {
?>
<form method="post" action="login.php">
  <fieldset>
    <label for="username">Username</label>
    <input type="text" name="username" /><br/>
    <label for="password">Password</label>
    <input type="password" name="password" /><br />
    <input type="submit" name="submit" value="Login &raquo;" />
  </fieldset>
</form>
<?php
}
?>
</body>
</html>