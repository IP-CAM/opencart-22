<form action="<?php echo $action; ?>" method="get">
  <input type="hidden" name="gh" value="<?php echo $encrypted_string; ?>" />
  <div class="buttons">
    <div class="right">
      <input type="submit" value="<?php echo $button_confirm; ?>" class="button" />
    </div>
  </div>
</form>
