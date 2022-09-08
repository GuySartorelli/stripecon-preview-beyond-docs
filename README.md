# Stripecon EU Lightning Talk 2022: CMS Preview - beyond the documentation

## Do you want to have play?

This is a fully functional demonstration as shown in my lightning talk at Stripecon EU 2022.
It includes fixtures for all of the content I used in that demonstration.

1. clone the repo
1. `composer install`
1. Perform a dev/build - this will pull in all of the fixtures. _DO NOT_ do a second dev/build at any point.

## Done

- Basic examples from documentation
  - On a page (Books Page)
  - In a ModelAdmin (Product Admin)
  - Model as a Page (Region Admin)
- Example that can be previewed from a modeladmin OR on a page (Property Admin & Property Page)
- Preview a PDF which is generated on-the-fly (Pdf Admin -> PDF Templates)
- Preview objects in a ModelAdmin directly from the gridfield (Pdf Admin)
- Preview relations of an object from a js-injected dropdown (Pdf Admin -> Clients)

## Stretch goals

- Make the demonstrations look nice (theming)
- Add lots of nice documentation in the code (phpdocs mostly) to help people navigate the examples
