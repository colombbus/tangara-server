Translate bundles
==================================

Strings have to be translated in XLIFF format to respect Symfony2's framework 
coding style and good pratices.

then run the following command:
``` bash
    app/console translation:update TangaraBundle --force
```
... will translate these lines in Twig templates:
``` twig
  {{ 'Symfony2 is great2'|trans }}
  {% trans %}Symfony2 is great2{% endtrans %}
```
  