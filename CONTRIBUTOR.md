# TM4MLP

## Big mess

### Folder structure

This plugin is a big mess you might say.
Well it follows only a few of simple rules
besides those that WordPress gave us:

- "admin/" contains partials/templates/js/css for the backend.
- "includes/" mostly contains code.
  - "includes/*.php" will always be loaded.
  - "includes/admin/*.php" will be loaded when `is_admin()` is true.
  - "includes/tm4mlp" contains all classes (loaded via class loader).
- "public/" contains partials/templates/js/css for the frontend.

### Procedural copy of classes

Furthermore you'll find classes/methods (like `...\Admin\Setup::plugin_activate`)
and (proxy-)functions just calling them (like `..._admin_plugin_activate()`) which seems odd.
This is intended as function names can be easily removed from hooks
and filters compared to objects (with some arbitrary SPL object hash).


## Writing modules

Sometimes modules for 3rd party plugins are needed.
Either if they are used for additional data to translate
or if they shall be informed about new translations.
Writing modules is easy (here: for the plugin "akismet"):

1. Create a file with the plugin slug like "modules/akismet.php"
2. Write code as usual for WordPress using hooks and filter.
3. This file will only be loaded if the other plugin ("akismet")is activated.

Simple as that.
It is okay to create sub-folders and more beyond the "modules" directory.
Within your module you might be interested in those filter/actions:

### Filter
 
The `tm4mlp_sanitize_post` sanitizes data before it is send in for translation.
Use the `TM4MLP_SANITIZE_POST` const to be forward compatible.
This filter is documented in the method
`\Tm4mlp\Pages\Add_Translation::handle_post()`.

The `tm4mlp_api_translation_update` receives data like this (JSON):

    {
      "__meta": {
        "id": 1, (ID as given by REST-API)
        "source": { id: 5, language: "de-DE", label: "Deutsch" }, (see below for `tm4mlp_get_current_language` filter)
        "target": { id: 6, language: 'fr-FR', label: "Francais" }, (see below for `tm4mlp_get_languages` filter)
      },
      "0": {
        "__meta": {
          "id": 1,
          "entity": "post",
          "type": "post"
        },
        "post_title": "Le titre.",
        "post_content": "Le contenu."
      },
      "1": {
        "__meta": {
          "id": 1,
          "entity": "post",
          "type": "page"
        },
        "post_title": "La page",
        "post_content": "Le contenu de page."
      }
    }

An example for development can be found at the API on the path "/api/stub/translation.json".
More documentation about this action can be found in the `tm4mlp_api_fetch` function.

Set languages via `tm4mlp_get_languages` filter like this:

    [
        'lang_id' => 'Lang label',
        5 => [ 'lang_code' => 'de-DE', 'label' => Deutsch' ],
        'en-GB' => 'English',
    ]

The ID can be whatever you need in your translation plugin.
You also may want to give us the current language via the `tm4mlp_get_current_language`,
so that he translation agency and you afterwards know the origin language.