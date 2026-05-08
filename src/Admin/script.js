(function ($) {
  $(window).on("load", function () {
    $('#eimad_menu_override').change(function() {
        if ($(this).is(':checked')) {
            const menuName = $('#eimad_new_menu_name').val();
            if (! confirm("All data related to " + menuName + " menu will be lost. Do you still want to proceed?")) {
                $('#eimad_menu_override').prop("checked", false);
            }
        }
    });
  });
})(jQuery);