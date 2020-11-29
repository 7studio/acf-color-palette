(function ($) {
    function initialize_field($el) {
        var $input = $el.find('input[type="text"]');
        var $hidden = $el.find('input[type="hidden"]');

        var change_hidden = function () {
            // timeout is required to ensure the $input val is correct
            setTimeout(function () {
                var wpcp_val = $input.val();
                var acf_val = wpcp_val;
                var val = acf_val || wpcp_val;

                acf.val($hidden, val);

                $input.val(get_user_value(val));
            }, 1);
        };

        /**
         * Returns the user's color instead of the `wp-color-picker-alpha`'s one.'
         *
         * @param {string} val - The wp-color-picker-alpha value
         * @returns {string} - The user's value
         */
        var get_user_value = function (val) {
            // Iterate over an array of strings, select the first elements that
            // equalsIgnoreCase the 'matchString' value
            var matchString = val.toLowerCase();
            var userValue = null;
            $.each(args.palettes, function (index, value) {
                if (userValue == null && value.toLowerCase() === matchString) {
                    userValue = value;
                    return false;
                }
            });

            return userValue;
        };

        var args = {
            defaultColor: false,
            palettes: false,
            hide: true,
            change: change_hidden,
            clear: change_hidden,
        };

        try {
            args.palettes = JSON.parse($input.attr("swp-acf-cp-palettes"));
        } catch (error) {
            console.error(error);
        }

        args = acf.apply_filters("swp_acf_color_palette_args", args, $el);

        $input.val(get_user_value($input.val()));

        // Iris initialisation
        $input.wpColorPicker(args);
    }

    if (typeof acf.add_action !== "undefined") {
        /**
         * ready append (ACF5)
         *
         * These are 2 events which are fired during the page load
         * ready = on page load similar to $(document).ready()
         * append = on new DOM elements appended via repeater field
         *
         * @type    event
         * @date    20/07/13
         *
         * @param   $el (jQuery selection) the jQuery element which contains the ACF fields
         * @return  n/a
         */
        acf.add_action("ready append", function ($el) {
            // search $el for fields of type 'color_palette'
            acf.get_fields({ type: "color_palette" }, $el).each(function () {
                initialize_field($(this));
            });
        });
    }
})(jQuery);
