# TMWP

## Big mess

### Folder structure

This plugin is a big mess you might say.
Well it follows only a few of simple rules
besides those that WordPress gave us:

- "admin/" contains partials/templates/js/css for the backend.
- "includes/" mostly contains code.
  - "includes/*.php" will always be loaded.
  - "includes/admin/*.php" will be loaded when `is_admin()` is true.
  - "includes/tmwp" contains all classes (loaded via class loader).
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
Within your module you might be interested in the following.

### Translation data

`Tmwp\Translation_Data` is a DTO object that is used to encapsulate that data that is being sent and received to and
from API.

There are accessor methods for values to be translated and metadata (that will be returned unchanged).

The accessor methods facilitate the usage  of _namespaces_ for values and metadata, allowing modules to easily separate
their data from other modules without risk of conflicts.

There are two main hooks: `tmwp_outgoing_data` (saved in the constant `TMWP_OUTGOING_DATA`) and `tmwp_incoming_data`
(saved in the constant `TMWP_INCOMING_DATA`) that pass this object to listeners, allowing for read and  write access.

### Actions: `TMWP_OUTGOING_DATA`
 
The `tmwp_outgoing_data` action fires before data is sent to API.
Use the `TMWP_OUTGOING_DATA` const to be forward compatible.
Only passed argument is an instance `Tmwp\Translation_Data`, that allow write access, so modules can add custom values
(and metadata) to be sent for translation. Metadata can be used for arbitrary values that will be returned unchanged
from the API.


### Actions: `TMWP_INCOMING_DATA`

The `tmwp_incoming_data` action fires after some translation data have been received from API.
Use the `TMWP_INCOMING_DATA` const to be forward compatible.

Only passed argument is an instance `Tmwp\Translation_Data`, that modules can use in read access to do operations
based on received data before a translation post is updated, or for in just-in-time modification.

The "raw" data received form API looks like this (JSON):

    {
      "__meta": {
        "id": 1, (ID as given by REST-API)
        "source": { id: 5, language: "de-DE", label: "Deutsch" }, (see below for `tmwp_get_current_language` filter)
        "target": { id: 6, language: 'fr-FR', label: "Francais" }, (see below for `tmwp_get_languages` filter)
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

and the `Tmwp\Translation_Data` provides separate accessor for "values" (anything that is not "meta") and for "meta".

Note that the method `Tmwp\Translation_Data::to_array()` convert the object in the array in everything similar to
the original raw data.

### Filter: `TMWP_POST_UPDATER`

Translation Manager can work with different translation plugins. This is why it does not create / update posts not do any
other write operation when data is received from API.

What is  does it to fire the filter "tmwp_post_updater" (stored in the constant `TMWP_POST_UPDATER`) where translation
plugins can hook to return a callback that will be used to update the translation post. The callback will be called with 
an instance of `Tmwp\Translation_Data` that encapsulate data received from API and it must return the translated post
object.

### Action: `TMWP_UPDATED_POST`

After a translation post have been updated by translation plugin, Translation Manager fires the hook "" (stored in 
the constant `Translation_Data`) passing the post object itself and the `Tmwp\Translation_Data` instance build from
API response.

### Setting languages

Available languages can be set via `tmwp_get_languages` filter like this:

    [
        'lang_id' => 'Lang label',
        5 => [ 'lang_code' => 'de-DE', 'label' => Deutsch' ],
        'en-GB' => 'English',
    ]

The ID can be whatever you need in your translation plugin.
You also may want to give us the current language via the `tmwp_get_current_language`,
so that he translation agency and you afterwards know the origin language.

### Module example

Explanatory usages example for all main plugin hooks can be found in MultilingualPress connection class, 
found in `/includes/tmwp/module/class-mlp-connect.php`