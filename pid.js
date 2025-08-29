jQuery(document).ready(function($){
    // Open modal and populate product info
    $(document).on('click', '.pid-inquiry-btn', function(e){
        e.preventDefault();
        var $btn = $(this);
        var pid = $btn.attr('data-product-id') || '';
        var pname = $btn.attr('data-product-name') || '';
        var pimg = $btn.attr('data-product-image') || '';

        $('#pid-product-id').text(pid);
        $('#pid-product-name').text(pname);
        $('#pid-product-image').attr('src', pimg).toggle(!!pimg);

        $('#pid_input_product_id').val(pid);
        $('#pid_input_product_name').val(pname);
        $('#pid_input_product_image').val(pimg);

        $('#pid-response').html('');
        $('#pid-modal').fadeIn(150).attr('aria-hidden','false');

        // init intl-tel-input on phone field if available
        var phoneInput = document.querySelector('#pid-phone');
        if (phoneInput && !phoneInput.dataset.intlInit) {
            var iti = window.intlTelInput(phoneInput, {
                initialCountry: "pk", // ✅ Set Pakistan as default
                utilsScript: pid_ajax.utils_script || pid_ajax.utils_script
            });
            phoneInput.dataset.intlInit = '1';
        }
    });

    // Close modal
    $(document).on('click', '.pid-modal-close', function(e){
        e.preventDefault();
        $('#pid-modal').fadeOut(150).attr('aria-hidden','true');
    });

    // Click outside to close
    $(document).on('click', '#pid-modal', function(e){
        if ($(e.target).is('#pid-modal')) {
            $('#pid-modal').fadeOut(150).attr('aria-hidden','true');
        }
    });

    // Submit via AJAX
    $(document).on('submit', '#pid-form', function(e){
        e.preventDefault();
        var $form = $(this);
        var data = $form.serialize();
        data += '&nonce=' + encodeURIComponent(pid_ajax.nonce);
        data += '&action=pid_submit';

        $('.pid-submit').prop('disabled', true).text('Sending...');

        $.post(pid_ajax.ajax_url, data, function(resp){
            if ( resp.success ) {
                $('#pid-response').html('<div class="pid-success">'+resp.data+'</div>');
                $form[0].reset();
                setTimeout(function(){ $('#pid-modal').fadeOut(150).attr('aria-hidden','true'); }, 1200);
            } else {
                $('#pid-response').html('<div class="pid-error">'+(resp.data || 'Error')+'</div>');
            }
        }).fail(function(){
            $('#pid-response').html('<div class="pid-error">Request failed. Try again.</div>');
        }).always(function(){
            $('.pid-submit').prop('disabled', false).text('Send Inquiry');
        });
    });
});
