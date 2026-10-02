<h1>Contact Us</h1><div class=feat_prod_box_details>
<p>Whatsapp /Call +254720401869</p>
<p class=details><b>We respond to emails promptly. Please use the form below </b> for more information about our domain registration, web design, PHP development and web hosting in Kenya.</p><p class=details><a href="/blog/web-hosting-and-domain-registration-the-difference.htm">The difference between domain registration and web hosting(Read here.)</a>.</p></div>
<div class=contact_form>
<div class=form_subtitle>all fields are required</div>
<form name="demo" method="post" onsubmit="return validateFormOnSubmit(this)" action="enquiry.php">
<div class=form_row>
<label class=contact><b>Name:</b></label><input id=FName name=username type=text class=contact_input /></div>
<div class=form_row>
<label class=contact><b>Email:</b></label><input id=Email name=email type=text class=contact_input /></div>
<div class=form_row>
<label class=contact><b>Phone:</b></label><input id=Phone name=phone type=text class=contact_input /></div>
<div class=form_row>
<label class=contact><b>Message:</b></label>
<textarea id=Message name=from class="contact_textarea" ></textarea>
</div>
<?php 
$num1 = rand(1, 10); $num2 = rand(1, 10);
$_SESSION['answer'] = $num1 + $num2; ?>

<div class=form_row>
<label class=contact><b>What is <?php echo $num1; ?> + <?php echo $num2; ?> </b></label><input name=math type=number max="20" class=contact_input /></div>
<div class=contact align=center>
<input type=reset name=reset value=Reset> 
<input id=Submit name=Submit value=Submit type=submit><br />
</div>
</form>
</div>
<div class=clear></div>