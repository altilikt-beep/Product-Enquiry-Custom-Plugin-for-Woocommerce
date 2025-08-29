<div id="pid-modal" class="pid-modal" style="display:none;" aria-hidden="true">
  <div class="pid-modal-dialog" role="dialog" aria-modal="true">
    <button class="pid-modal-close" aria-label="Close">&times;</button>
    <div class="pid-modal-body">
      <div class="pid-left">
        <img id="pid-product-image" src="" alt="" style="display:none;max-width:100%;height:auto;">
        <h3 id="pid-product-name"></h3>
        <p>Product ID: <span id="pid-product-id"></span></p>
      </div>
      <div class="pid-right">
        <form id="pid-form">
          <input type="hidden" name="product_id" id="pid_input_product_id" value="">
          <input type="hidden" name="product_name" id="pid_input_product_name" value="">
          <input type="hidden" name="product_image" id="pid_input_product_image" value="">
          <p><label>Name<br><input type="text" name="name" required></label></p>
          <p><label>Email<br><input type="email" name="email" required></label></p>
          <p><label>Phone<br><input type="tel" id="pid-phone" name="phone" required></label></p>
          <p><button type="submit" class="pid-submit">Send Inquiry</button></p>
          <div id="pid-response" class="pid-response" role="status" aria-live="polite"></div>
        </form>
      </div>
    </div>
  </div>
</div>
