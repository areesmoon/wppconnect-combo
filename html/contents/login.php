<div class="jumbotron text-center">
  <h1>Login Whatsapp BOT</h1> 
</div>

<?php
if(isset($_SESSION['userlogin_message'])):
?>
<div class="container">
	<div class="row">
		<div class="col-sm-2"></div>
		<div class="col-sm-8">
      <div class="alert alert-warning text-center">
        <strong><?=$_SESSION['userlogin_message'];?></strong>
      </div>
		</div>
		<div class="col-sm-2"></div>
	</div>
</div>
<?php endif; ?>

<div class="container">
	<div class="row">
		<div class="col-sm-2"></div>
		<div class="col-sm-8">
      <form enctype="multipart/form-data" action="userlogin" method="post">
        <div class="form-group">
          <label for="usr">Username</label>
          <input type="text" class="form-control" id="username" name="username">
        </div>
        <div class="form-group">
          <label for="pwd">Password:</label>
          <input type="password" class="form-control" id="password" name="password">
        </div>
        <div class="form-group">
          <button type="submit" class="btn">Login</button>
        </div>
      </form>
		</div>
		<div class="col-sm-2"></div>
	</div>
</div>