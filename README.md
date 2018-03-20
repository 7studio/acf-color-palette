# ACF Color Palette Field

Welcome to the Advanced Custom Fields Color Palette repository on Github.

## Description

Add a new ACF field type: "Color Palette" which allows you to use the color picker with a defined color palette only.

This field enjoys [kallookoo/wp-color-picker-alpha](https://github.com/kallookoo/wp-color-picker-alpha)
which overwrites [Automattic Iris](http://automattic.github.io/Iris/) to enable Alpha Channel in `wpColorPicker`
and so let you choose more color variations.

You are able to define your palette into the field's settings with different notations like below:
```
#f00 : Red
rgb(0,255,0) : Green
rgba(0, 0, 255, 1) : Blue
rgba(80, 180, 255, .4) : Light Blue
rgba(255, 180, 80, .7) : Light Orange
grey : Grey
#800080 : Purple
```

![ACF Color Palette Field](http://www.7studio.fr/github/acf-color-palette/screenshot-1.png)

**This plugin works only with the [ACF PRO](https://www.advancedcustomfields.com/pro/) (version 5.5.0 or higher).**

## Changelog

### 1.0.0 (March 20, 2018)
* Initial Release.
