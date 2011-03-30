<div class="footercolumn">
  <?php /*<h2 class="footer"> Quick<span class="footergray">Links</span></h2>*/ ?>
	<p></p>
  <ul class="footer">
    <li><?php echo anchor('', 'Help') ?></li>
	<?php if(get_active_controller() !== 'login'): ?>
		<li><?php echo anchor('welcome/index', 'Dashboard') ?></li>
		<li><?php echo anchor('login/logout', '[Logout]') ?></li>
	<?php endif; ?>
  </ul>
</div>
<?php /**
<?php if(get_active_controller() !== 'login'): ?>
<div class="footercolumn">
  <h2 class="footer">Transaction</h2>
  <ul class="footer">
	<li><?php echo anchor('transaction/display', 'Transaction Manager') ?></li>
    <li><?php echo anchor('transaction/add', 'Add Transaction') ?></li>
	<li><?php echo anchor('transaction/display_trash', 'Trash') ?></li>
	<li><?php echo anchor('report/display', 'Report') ?></li>
  </ul>
</div>
<?php endif; ?>

<?php if(get_active_controller() !== 'login'): ?>
<div class="footercolumn">
  <h2 class="footer">Reservation</h2>
  <ul class="footer">
	<li><?php echo anchor('reservation/display', 'Reservation Manager') ?></li>
    <li><?php echo anchor('reservation/add', 'Add Reservation') ?></li>
	<li><?php echo anchor('reservation/display_trash', 'Trash') ?></li>
  </ul>
</div>
<?php endif; ?>

<?php if(get_active_controller() !== 'login'): ?>
<div class="footercolumn">
  <h2 class="footer">Client</h2>
  <ul class="footer">
	<li><?php echo anchor('client/display', 'Client Manager') ?></li>
    <li><?php echo anchor('client/add', 'Add Client') ?></li>
	<li><?php echo anchor('client/display_trash', 'Trash') ?></li>
	<li><?php echo anchor('client_type/display', 'Client Type') ?></li>
  </ul>
</div>
<?php endif; ?>

<?php if(get_active_controller() !== 'login'): ?>
<div class="footercolumn">
  <h2 class="footer">Product</h2>
  <ul class="footer">
	<li><?php echo anchor('product/display', 'Product Manager') ?></li>
    <li><?php echo anchor('product/add', 'Add Product') ?></li>
	<li><?php echo anchor('product/display_trash', 'Trash') ?></li>
	<li><?php echo anchor('product_type/display', 'Product Type') ?></li>
  </ul>
</div>
<?php endif; ?>
 *
 */
 ?>