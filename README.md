# WP jSlabify
Create beautiful Slabbed typography effects, right from the WordPress editor.

## Examples
![Sample Slabs created with WP jSlabify](https://i.imgur.com/VWx382O.png "Examples")

## Usage
### Meet The Shortcodes

#### `[slab]insert content here[/slab]`
The *[slab]* shortcode is the basis of all slabbed typography. In fact, The other shortcodes will only function when wrapped in a *[slab]* shortcode. the slab shortcode offers the following optional attributes:

* **theme** - apply templated styles. default options are *league* or *ultra*
* **ratio** - the target ratio (width/height) of the resulting slab (this will effect how line lengths are selected, when slabbing text automatically). All values must be greater than zero. values greater than 1 will attempt to create slabs that are wider than they are tall. values less than 1 will attempt to create slabs that are taller than they are wide. Defaults to *1*.
* **force** - when creating a slab, force the container's dimensions to match the proportion set in the "ratio" attribute, even if the slab content will not completely fill the container.
* **hcenter** - when force is set to true, setting *hcenter* to true will center the slab horizontally within the container. defaults to *true*.
* **vcenter** - when force is set to true, setting *vcenter* to true will center the slab vertically within the container. defaults to *true*.
* **element** - The HTML element that will be used to wrap the slabbed text. defaults to *div*.
* **id** - the id attribute to apply to the slab
* **class** - a set of classes to apply to the slab
* **style** - inline CSS to apply to the slab
* **href** - (only available when element is set to "a") the url to direct users to, on click.
* **title** - (only available when element is set to "a") the title of the link element.
* **target** - (only available when element is set to "a") the target of the link element.

#### `[slabline]insert content here[/slabline]`
The *[slabline]* shortcode is used to manually group a set of words in a slab into a single row. it offers the following optional attributes:

* **id** - the id attribute to apply to the line
* **class** - a set of classes to apply to the line
* **style** - inline CSS to apply to the line

#### `[slabbreak]`
The *[slabbreak]* shortcode is used to insert a separator between two lines in a slab. it offers the following optional attributes:

* **type** - one of several predefined types of separators, including arrows and banners
* **id** - the id attribute to apply to the separator
* **class** - a set of classes to apply to the separator
* **style** - inline CSS to apply to the separator

## Examples
### Auto Slabbing
Auto slabbing is the easiest way to use the plugin. Simply set a target ratio for the slab (width/height), and the algorithm does the rest. However, it is the hardest to customize
```
[slab element="h3" ratio="3" theme="ultra"]
The curious incident of the dog in the night
[/slab]
```
### Manual Slabbing
Manual Slabbing gives you the most control over the slab's layout and styling, but can be time-consuming.
```
[slab element="blockquote" theme="league"]
[slabline style="color: red;"]The curious[/slabline]
[slabline]incident of the dog[/slabline]
[slabbreak type="banner"]
[slabline class="lastline"]in the night[/slabline]
[/slab]
```

### Mixed Slabbing
Mixed Slabbing can provide the best of both worlds, allowing the algorithm to map most rows, but still allowing specific lines to be emphasized, as needed.
```
[slab element="a" ratio="2" href="https://www.google.com"]
[slabline style="color: red;"]The curious incident[/slabline]
of the dog in the night
[/slab]
```
