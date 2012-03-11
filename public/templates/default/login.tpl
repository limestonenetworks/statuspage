	<div class="wrapper">
		<div id="content">
			<form action="" method="post" id="login">
				<fieldset>
					<legend>Login</legend>

					{if $error}<p class="error">{$error}</p>{/if}

					<p>
						<label for="username">Username:</label>
						<input type="text" name="username" id="username" />
					</p>
					<p>
						<label for="password">Password:</label>
						<input type="password" name="password" id="password" />
					</p>
					<p>
						<input type="submit" value="Login Now" />
					</p>
				</fieldset>
			</form>
		</div>
	</div>
