<?php echo $header; ?>
<script type="text/javascript">
	function changedIpayyItemDisplay() {
		if (document.getElementById("ipayy_item_display").value == "Custom")
			document.getElementById("ipayy_item_display_other").style.visibility="";
		else
			document.getElementById("ipayy_item_display_other").style.visibility="hidden";
	}
</script>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="<?php echo $ipayy_logo_url; ?>" height="25" alt="iPayy" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td>
            	<span class="required">*</span> <?php echo $entry_merchant_id; ?>
            	<br />
            	<span class="help"><?php echo $entry_merchant_help; ?></span>
        	</td>
            <td><input type="text" name="ipayy_merchant_id" value="<?php echo $ipayy_merchant_id; ?>" />
              <?php if (isset($error_merchant_id)) { ?>
              <span class="error"><?php echo $error_merchant_id; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td>
            	<span class="required">*</span> <?php echo $entry_application_id; ?>
            	<br />
            	<span class="help"><?php echo $entry_application_help; ?></span>
        	</td>
            <td><input type="text" name="ipayy_application_id" value="<?php echo $ipayy_application_id; ?>" />
              <?php if (isset($error_application_id)) { ?>
              <span class="error"><?php echo $error_application_id; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td>
            	<span class="required">*</span> <?php echo $entry_item_display; ?>
            	<br />
            	<span class="help"><?php echo $entry_item_help; ?></span>
        	</td>
            <td>
            	<select id="ipayy_item_display" name="ipayy_item_display" onchange="javascript:changedIpayyItemDisplay();">
            		<?php
            		foreach ($entry_item_options as $option) { 
            		?>
            		<option <?php if ($ipayy_item_display == $option) echo 'selected="selected"' ?>><?php echo $option; ?></option>
            		<?php } ?>
            	</select>
            	&nbsp;&nbsp;
            	<input type="text" id="ipayy_item_display_other" name="ipayy_item_display_other" value="<?php echo $ipayy_item_display_other; ?>" />
              	<?php if (isset($error_item_display)) { ?>
              	<span class="error"><?php echo $error_item_display; ?></span>
              	<?php } ?>
              	<script type="text/javascript">
					changedIpayyItemDisplay();
				</script>
            </td>
          </tr>
          <tr>
            <td><?php echo $entry_debug; ?></td>
            <td><select name="ipayy_debug">
                <?php if ($ipayy_debug) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_total; ?></td>
            <td><input type="text" name="ipayy_total" value="<?php echo $ipayy_total; ?>" /></td>
          </tr>          
          <tr>
            <td><?php echo $entry_completed_status; ?></td>
            <td><select name="ipayy_completed_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $ipayy_completed_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_denied_status; ?></td>
            <td><select name="ipayy_denied_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $ipayy_denied_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_failed_status; ?></td>
            <td><select name="ipayy_failed_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $ipayy_failed_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_status; ?></td>
            <td><select name="ipayy_status">
                <?php if ($ipayy_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_sort_order; ?></td>
            <td><input type="text" name="ipayy_sort_order" value="<?php echo $ipayy_sort_order; ?>" size="1" /></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?> 