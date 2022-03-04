# Development

This is a guide for software engineers who wish to take part in the development of this product.

## Writing modules

Sometimes modules for 3rd party plugins are needed.
Either if they are used for additional data to translate
or if they need to be informed about new translations.

Examples of modules can be found in [src/Module](src/Module).

Within your module you might be interested in the following.

### Translation data

`Translationmanager\Translation` is a data transfer object that is used to encapsulate the data that is being sent and received to and
from API.

There are accessor methods for values to be translated and metadata (that will be returned unchanged).

The accessor methods facilitate the usage  of _namespaces_ for values and metadata, allowing modules to easily separate
their data from other modules without risk of conflicts.

There are two main hooks: `translationmanager_outgoing_data` and `translationmanager_incoming_data` that pass this object to listeners, allowing for read and  write access.

### Action: `'translationmanager_outgoing_data'`

The `translationmanager_outgoing_data` action fires before data is sent to API.

The only passed argument is an instance of `Translationmanager\Translation`, that allows write access, so modules can add custom values
(and metadata) to be sent for translation. Metadata can be used for arbitrary values that will be returned unchanged
from the API.

### Action: `translationmanager_incoming_data`

The `translationmanager_incoming_data` action fires after some translation data have been received from API.

The only passed argument is an instance of `Translationmanager\Translation`, that modules can use in read access to do operations
based on received data before a translation post is updated, or for in just-in-time modification.

The "raw" data received form API looks like this (JSON):

    {
      "__meta": {
        "id": 1, (ID as given by REST-API)
        "source": { id: 5, language: "de-DE", label: "Deutsch" }, (see below for `translationmanager_current_language` filter)
        "target": { id: 6, language: 'fr-FR', label: "Francais" }, (see below for `translationmanager_languages` filter)
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

and `Translationmanager\Translation` provides separate accessor for "values" (anything that is not "meta") and for "meta".

The method `Translationmanager\Translation::to_array()` converts the object to an array similar to
the original raw data.

### Filter: `translationmanager_post_updater`

Translation Manager can work with different translation plugins. This is why it does not create / update posts nor does any
other write operation when data is received from API.

Instead it fires the `translationmanager_post_updater` filter where translation
plugins can hook to return a callback that will be used to update the translation post. The callback will be called with
an instance of `Translationmanager\Translation` that encapsulate data received from API and it must return the translated post
object.

### Action: `translationmanager_updated_post`

After the post have been updated by translation plugin, Translation Manager fires the `translationmanager_updated_post`
passing the `Translationmanager\Translation` instance.

### Setting languages

Available languages can be set via `translationmanager_languages` filter like this:

    [
        'lang_id' => 'Lang label',
        5 => [ 'lang_code' => 'de-DE', 'label' => Deutsch' ],
        'en-GB' => 'English',
    ]

The ID can be whatever you need in your translation plugin.
You also may want to give us the current language via the `translationmanager__current_language`,
so that the translation agency and you afterwards know the origin language.
