# WP jSlabify
Create beautiful Slabbed typography effects, right from the WordPress editor.

## Examples
![Sample Slabs created with WP jSlabify](https://i.imgur.com/pLF2FnR.png "Examples")

### Usage
#### Use Case 1: Auto Slabbing
Auto slabbing is the easiest way to use the plugin. Simply set a target ratio for the slab (width/height), and the algorithm does the rest. However, it is the hardest to customize
```
[slab element="h3" ratio="3"]
The curious incident of the dog in the night
[/slab]
```
#### Use Case 2: Manual Slabbing
Manual Slabbing gives you the most control over the slab's layout and styling, but can be time-consuming.
```
[slab element="blockquote"]
[slabline style="color: red;"]The curious[/slabline]
[slabline]incident of the dog[/slabline]
[slabline class="lastline"]in the night[/slabline]
[/slab]
```

### Use Case 3: Mixed Slabbing
Mixed Slabbing can provide the best of both worlds, allowing the algorithm to map most rows, but still allowing specific lines to be emphasized, as needed.
```
[slab element="a" ratio="2" href="https://www.google.com"]
[slabline style="color: red;"]The curious incident[/slabline]
of the dog ]in the night
[/slab]
```

## Options

### [slab] options
* **element** - The HTML element that will be used to wrap the slabbed text
* **id** - the id attribute to apply to the slab
* **class** - a set of classes to apply to the slab
* **style** - inline CSS to apply to the slab
* **ratio** - the target ratio (width/height) of the slab (normally only applies to manual or mixed slabbing)
* **href** - (only available when element is set to "a") the url to direct users to, on click.
* **title** - (only available when element is set to "a") the title of the link element.
* **target** - (only available when element is set to "a") the target of the link element.

### [slabline] options
* **id** - the id attribute to apply to the line
* **class** - a set of classes to apply to the line
* **style** - inline CSS to apply to the line
