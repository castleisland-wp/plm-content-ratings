#Paul's Content Rating Shortcodes.

This plug-in lets you insert boxes with bar graphs giving ratings for anything you can think
of. You can include ratings into product reviews, movie reviews, take your pick.

There are three basic short codes you can use.

##Rating Stack

!(/images/rating-stack.png)

This shortcode outputs the ratings as a stack of labels over ratings.

```
[rating_stack max="%n" sprite="&#x2589;" no_zero="false"]Label1:Rating1,Label2:Rating2[/rating_stack]
```

##Rating Table

!(/images/rating_table.png)

This shortcode outputs content between the shortcode tags as a table with labels as row labels.

```
[rating_table max="5" sprite="&#x2589;" no_zero="false"]Label1:4,Label2:3[/rating_table]
```

###Contents

The standard syntax is a comma-delimited series of label and rating pairs, each with a label and a 
number, separated by a colon.


###Attributes

Both shortcodes have the same attributes.

max:		The top rating value, default: 5. If a rating exceeds this maximum, the final output will 
			use the maximum.

sprite:		The html entity for the character used in the grid. The default will be the 7/8 box 
			character.

no_zero:	If set to "true" or "yes," then the shortcode will not output any row. Allows you to use
			the shortcode with the output of another plugin.



##Simple Star Rating

!(/images/star-rating.png)

This shortcode outputs a graphical star rating based on the contents.

```
[simple_star_rating sprite="&#x2605;" max="5"]4[/simple_star_rating]
```

max:		The top rating value, default: 5. If a rating exceeds this maximum, the final output will 
			use the maximum.

sprite:		The html entity for the character used in the grid. The default will be the black star
			character.

You can also do it this way:

```
[simple_star_rating sprite="&#x2605;"]4/5[/simple_star_rating]
```

The short code will use the number after the slash as the maximum.  If you use this and the attribute, the 
number after the slash will override the attribute.
