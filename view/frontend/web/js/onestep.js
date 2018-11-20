require(['jquery'], function ($) {
    $(document).ready(function () {
        $('body').on('click', '.payment-method-title label', function () {
            $('.payment-method-title').removeClass('active');
            $(this).parent().addClass('active');
            var dataCode = $(this).parent().attr('data-code');
            if (dataCode == 'paypal_express') {
                $('li.toolbar_order').hide();
            } else {
                $('li.toolbar_order').show();
            }

            $('.payment_title_block').removeClass('active');
            $('.payment_title_block').addClass('active').attr('data-code', dataCode);
        });
    });
});