<div>
	<h2><?php echo $form->title; ?></h2>
	<p><?php echo $form->intro; ?></p>
	<form method="post" class="contactform" action="<?php echo $form->action; ?>">
		<div class="row">
			<div class="seven columns">
				<label for="name"><?php echo $form->name_label; ?></label>
				<input type="text" name="name" value="<?php echo $form->name; ?>" class="">
				<div class="error"><?php echo $errors->name; ?></div>
			</div>
			<div class="seven columns">
				<label for="eadr"><?php echo $form->email_label; ?></label>
				<input type="text" name="eadr" value="<?php echo $form->eadr; ?>" class="">
				<div class="error"><?php echo $errors->eadr; ?></div>
			</div>
		</div>
		<div>
			<label for="message"><?php echo $form->message_label; ?></label>
			<textarea name="message" rows="7"><?php echo $form->message; ?></textarea>
			<div class="error"><?php echo $errors->message; ?></div>
		</div>
		<input type="hidden" name="token" value="">
		<div>
			<label><?php echo $form->recaptcha_label; ?></label>
			<?php echo $form->captcha; ?>
			<div class="error"><?php echo $errors->recaptcha; ?></div>
		</div>
		<input type="submit" value="Send">
	</form>
</div>

