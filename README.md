# Twig Image Utils Plugin

The **Twig Image Utils** Plugin is for [Grav CMS](http://github.com/getgrav/grav). It contains Twig functions for image handling.

## Installation

Installing the Twig Image Utils plugin can be done in one of two ways. The GPM (Grav Package Manager) installation method enables you to quickly and easily install the plugin with a simple terminal command, while the manual method enables you to do so via a zip file.

### GPM Installation (Preferred)

The simplest way to install this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's terminal (also called the command line).  From the root of your Grav install type:

    bin/gpm install twig-image-utils

This will install the Twig Image Utils plugin into your `/user/plugins` directory within Grav. Its files can be found under `/your/site/grav/user/plugins/twig-image-utils`.

### Manual Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `twig-image-utils`. You can find these files on [GitHub](https://github.com/tsnorri/grav-plugin-twig-image-utils) or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/twig-image-utils
	
> NOTE: This plugin is a modular component for Grav which requires [Grav](http://github.com/getgrav/grav) and the [Error](https://github.com/getgrav/grav-plugin-error) and [Problems](https://github.com/getgrav/grav-plugin-problems) to operate.

## Configuration

Before configuring this plugin, you should copy the `user/plugins/twig-image-utils/twig-image-utils.yaml` to `user/config/plugins/twig-image-utils.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: true
```

## Usage

The plugin adds the following filters:

*   `max_width_size_rule` returns a rule of the form `(min-width: Wpx) Wpx` for use with the `sizes` attribute of the `img` element where `W` is the pixel width of the image. It may be used to prevent the image from being scaled from its maximum size.
*   `image_with_default(max_size, axis)` returns an object that is capable of generating the HTML for an `<img>` tag. `maxSize` specifies the maximum size on the given axis for the default image. The value `0` for axis indicates width, `1` height.
*   `image_alternatives(max_size, axis)` returns a primary `Medium` and an array of alternative media: `[primary_medium, [[width_1, height_1, medium_1], …]]`. The primary medium is determined the same way as with `image_with_default`.
