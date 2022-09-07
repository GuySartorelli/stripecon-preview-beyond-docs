# Stripecon Lightning Talk 2022: CMS Preview - beyond the documentation

## Done

- Basic examples from documentation
  - On a page (Books Page)
  - In a ModelAdmin (Product Admin)
  - Model as a Page (Region Admin)
- Example that can be previewed from a modeladmin OR on a page (Property Admin & Property Page)
- Preview a PDF which is generated on-the-fly (Pdf Admin -> PDF Templates)
- Preview objects in a ModelAdmin directly from the gridfield (Pdf Admin)
- Preview relations of an object from a js-injected dropdown (Pdf Admin -> Clients)

## TODO

- Remove extraneous admin sections
- Make it easier to tell what admin section I need to go to for which part of the demo
- Add some content to actually demo
- have some miniature set of slides (or even just an opening presentation page) inside the app
  - Who I am
  - Demo time
  - ??

## Stretch goals

- Add lots of nice documentation in the code (phpdocs mostly) to help people navigate the examples
- Make the demonstrations look nice (theming)
- set up fixtures so others can easily spin up the demonstrations if they want
  - Maybe use https://github.com/chrispenny/silverstripe-data-object-to-fixture

## LIMITATIONS

- Can't make vendor DataObject classes CMSPreviewable - have to subclass them and replace via Injector instead
- Can be a pain to create CMSEditLink implementations
- Controlling the preview panel via javascript isn't always straight forward and isn't well documented
