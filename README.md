# Embed Bokun WordPress plugin

Allows you to add Bokun products to your WordPress site via blocks.

You need to have at least WP 5.0 and classic editor not activated.

## Documentation

Plugin is extremely developer friendly. See the documentation below.

### Filters

`embed_bokun_support_post_types` ==> Define the post types where Bokun ID meta box is visible

```
add_filter('embed_bokun_support_post_types', function () {
    return [ 'your_post_type' ];
});
```

`embed_bokun_cron_date_dormat` ==> define how to show date and time in Bokun ID meta box

```
add_filter('embed_bokun_cron_date_dormat', function() {
    return 'd.m.Y H:i:s'; //define whatever you need...
});
```

`embed_bokun_post_update_api_path` ==> In case you need to modify api request when post update (example in case of Polang translations)

```
add_filter('embed_bokun_post_update_api_path', function( $string, $post_id, $bokun_id ) {
    $string = '/activity.json/' . $bokun_id . '?lang=' . pll_get_post_language( $post_id ); //get polylang current post lang as parameter
    return $string;
}, 10, 3);
```

More filters coming the future....

### Actions

Actions are used when building a custom widget.

You can remove the actions and build your own actions in your theme.

Custom widget has these actions hooked.

```
add_action( 'embed_bokun_custom_product', 'embed_bokun_create_images_carousel', 5, 2 );
add_action( 'embed_bokun_custom_product', 'embed_bokun_create_title', 10, 2 );
add_action( 'embed_bokun_custom_product', 'embed_bokun_create_excerpt', 15, 2 );
add_action( 'embed_bokun_custom_product', 'embed_bokun_create_duration', 20, 2 );
add_action( 'embed_bokun_custom_product', 'embed_bokun_create_content_columns', 30, 2 );
```

You can also add actions before and after the custom widget content with these two actions, plugin it self will not hook into these:
`embed_bokun_before_custom_product`
`embed_bokun_after_custom_product`

### Data fetch from Bokun to build customm widget

You need to define Bokun product ID for posts where you want to use custom style widget.

System will fetch data from Bokun automatically hourly.

Response is saved as post meta data `_embed_bokun_product_api_response`. You can use that data to build your custom style widget.

NOTE that you need to define api key and secret key in settings => bokun settings.

### Building your own css-styles

Do not overwrite the styles in the plugin folder. In a case of an update, your changes are gone.

Instead, override the styles in your theme code.

## Project setup for local development

This project is bootstrapped with [Create Guten Block](https://github.com/ahmadawais/create-guten-block).

Below you will find information on how to run scripts.

>You can find the most recent version of this guide [here](https://github.com/ahmadawais/create-guten-block).

## 👉  `npm start`
- Use to compile and run the block in development mode.
- Watches for any changes and reports back any errors in your code.

## 👉  `npm run build`
- Use to build production code for your block inside `dist` folder.
- Runs once and reports back the gzip file sizes of the produced code.

## 👉  `npm run eject`
- Use to eject your plugin out of `create-guten-block`.
- Provides all the configurations so you can customize the project as you want.
- It's a one-way street, `eject` and you have to maintain everything yourself.
- You don't normally have to `eject` a project because by ejecting you lose the connection with `create-guten-block` and from there onwards you have to update and maintain all the dependencies on your own.
