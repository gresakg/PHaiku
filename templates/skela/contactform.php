<div>
	<h2><?php echo $form_title; ?></h2>
	<p><?php echo $form_intro; ?></p>
	<form method="post" class="contactform" action="<?php echo $action; ?>">
		<div class="row">
			<div class="seven columns">
				<label for="name"><?php echo $name_label; ?></label>
				<input type="text" name="name" value="" class="">
			</div>
			<div class="seven columns">
				<label for="eadr"><?php echo $email_label; ?></label>
				<input type="text" name="eadr" value="" class="">
			</div>
		</div>
		<div>
			<label for="message"><?php echo $message_label; ?></label>
			<textarea name="message" rows="7"></textarea>
		</div>
		<input type="hidden" name="token" value="">
		<input type="submit" value="Send">
	</form>
</div>

