<?php
if( !defined( 'CUSTOMER_PAGE' ) )
  exit;

$config['contact_form_id'] = isset( $config['contact_form_id'] ) ? ( $config['contact_form_id'] + 1 ) : 1;

require_once 'core/libraries/forms-validate.php';
if( isset( $_POST['sContactForm'] ) ){
  echo '<div class="contact-panel send">'.sendContactForm( $_POST ).'</div>';
}
else{ // contact form
  $aCaptcha = throwCaptchaText( );
  echo displayJavaScripts( 'core/libraries/quick.form.js' ); ?>
  <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="contact-panel form form-full" id="contact-form-<?php echo $config['contact_form_id']; ?>">
    <fieldset>
      <legend class="wai"><?php echo $lang['Contact_form']; ?></legend>
      <input type="hidden" name="sMd5" value="<?php echo $aCaptcha['sMd5']; ?>" />
      <dl>
        <dt><label for="contact-name"><?php echo $lang['Name_and_surname']; ?><span>(<?php echo $lang['required']; ?>)</span></label></dt>
        <dd><input type="text" name="sName" value="" id="contact-name" maxlength="100" data-form-check="required" /></dd>
        <dt><label for="contact-email"><?php echo $lang['Your_email']; ?><span>(<?php echo $lang['required']; ?>)</span></label></dt>
        <dd><input type="email" name="sEmailFrom" value="" id="contact-email" maxlength="100" data-form-check="email" /></dd>
        <dt><label for="contact-phone"><?php echo $lang['Telephone']; ?></label></dt>
        <dd><input type="text" name="sPhone" value="" id="contact-phone" maxlength="50" /></dd>
        <dt><label for="contact-topic"><?php echo $lang['Topic']; ?><span>(<?php echo $lang['required']; ?>)</span></label></dt>
        <dd><input type="text" name="sTopic" id="contact-topic" maxlength="100" data-form-check="required" /></dd>
        <dt><label for="contact-content"><?php echo $lang['Content_mail']; ?><span>(<?php echo $lang['required']; ?>)</span></label></dt>
        <dd><textarea cols="25" rows="8" name="sContent" id="contact-content" maxlength="<?php echo MAX_TEXTAREA_CHARS; ?>" data-form-check="required"></textarea></dd>
        <dd class="captcha"><label for="captcha-contact"><?php echo $aCaptcha['sText']; ?> = </label><input type="text" name="sCaptcha" id="captcha-contact" maxlength="5" data-form-check="int" /></dd>
      </dl>
      <div class="save"><input type="submit" name="sContactForm" value="<?php echo $lang['send']; ?>" /></div>
    </fieldset>
  </form>
  <script>
  $( "#contact-form-<?php echo $config['contact_form_id']; ?>" ).quickform();
  </script>
  <?php  
}
?>