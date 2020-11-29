;(function ($) {
    function initialize_field($el) {
        var $input = $el.find('input[type="text"]')
        var $hidden = $el.find('input[type="hidden"]')

        var change_hidden = function () {
            // timeout is required to ensure the $input val is correct
            setTimeout(function () {
                var wpcp_val = $input.val()
                var acf_val = wpcp_val
                var val = acf_val || wpcp_val

                acf.val($hidden, val)

                $input.val(get_user_value(val))
            }, 1)
        }

        /**
         * Returns the user's color instead of the `wp-color-picker-alpha`'s one.'
         *
         * @param {string} val - The wp-color-picker-alpha value
         * @returns {string} - The user's value
         */
        var get_user_value = function (val) {
            var userValue = null
            $.each(args.palettes, function (index, paletteValue) {
                if (
                    userValue == null &&
                    get_rgba_notation(paletteValue) === get_rgba_notation(val)
                ) {
                    userValue = paletteValue
                    return false
                }
            })

            return userValue
        }

        var args = {
            defaultColor: false,
            palettes: false,
            hide: true,
            change: change_hidden,
            clear: change_hidden,
        }

        try {
            args.palettes = JSON.parse($input.attr('swp-acf-cp-palettes'))
        } catch (error) {
            console.error(error)
        }

        args = acf.apply_filters('swp_acf_color_palette_args', args, $el)

        $input.val(get_user_value($input.val()))

        // Iris initialisation
        $input.wpColorPicker(args)
    }

    /**
     * Converts any colors (or almost) to their rgba equivalents.
     *
     * Many thanks to Oria for his brillant code :D
     * https://stackoverflow.com/a/44655529/3356679
     */
    function get_rgba_notation(color) {
        if (!color) {
            return
        }

        if (color.toLowerCase() === 'transparent') {
            return 'rgba(0, 0, 0, 0)'
        }

        if (color[0] === '#') {
            if (color.length < 7) {
                // convert #RGB and #RGBA to #RRGGBB and #RRGGBBAA
                color =
                    '#' +
                    color[1] +
                    color[1] +
                    color[2] +
                    color[2] +
                    color[3] +
                    color[3] +
                    (color.length > 4 ? color[4] + color[4] : '')
            }

            return (
                'rgba(' +
                parseInt(color.substr(1, 2), 16) +
                ', ' +
                parseInt(color.substr(3, 2), 16) +
                ', ' +
                parseInt(color.substr(5, 2), 16) +
                ', ' +
                (color.length > 7
                    ? parseInt(color.substr(7, 2), 16) / 255
                    : 1) +
                ')'
            )
        }

        if (color.indexOf('rgb') === -1) {
            // convert named colors
            var tmp_elem = document.body.appendChild(
                document.createElement('fictum'),
            ) // intentionally use unknown tag to lower chances of css rule override with !important
            var flag = 'rgb(1, 2, 3)' // this flag tested on chrome 59, ff 53, ie9, ie10, ie11, edge 14

            tmp_elem.style.color = flag
            if (tmp_elem.style.color !== flag) {
                return // color set failed - some monstrous css rule is probably taking over the color of our object
            }

            tmp_elem.style.color = color

            if (tmp_elem.style.color === flag || tmp_elem.style.color === '') {
                return // color parse failed
            }

            color = getComputedStyle(tmp_elem).color

            document.body.removeChild(tmp_elem)
        }

        if (color.indexOf('rgb') === 0) {
            if (color.indexOf('rgba') === -1) {
                color += ',1' // convert 'rgb(R,G,B)' to 'rgb(R,G,B)A' which looks awful but will pass the regxep below
            }

            return (
                'rgba(' +
                color
                    .match(/[\.\d]+/g)
                    .map(function (e) {
                        return e.replace(/^0\./, '.')
                    })
                    .join(', ') +
                ')'
            )
        }
    }

    if (typeof acf.add_action !== 'undefined') {
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
        acf.add_action('ready append', function ($el) {
            // search $el for fields of type 'color_palette'
            acf.get_fields({ type: 'color_palette' }, $el).each(function () {
                initialize_field($(this))
            })
        })
    }
})(jQuery)
