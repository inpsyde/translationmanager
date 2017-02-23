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
