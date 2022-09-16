# Stripecon EU Lightning Talk 2022: CMS Preview - beyond the documentation

Please take this code if any of it will be useful to you - the whole point of this is to help people start using the preview panel to help their content authors get a clear indication of what their content will look like (beyond just pages themselves).

Check out [the official documentation](https://docs.silverstripe.org/en/4/developer_guides/customising_the_admin_interface/preview/#php) about the preview panel for additional context.

Basically, if your data has a visual representation you _absolutely_ should be providing a CMS preview for it. It's dead easy and gives a lot of value.

## Do you want to have play?

This is a fully functional demonstration as shown in my lightning talk at Stripecon EU 2022.
It includes fixtures for all of the content I used in that demonstration.

1. clone the repo
1. `composer install`
1. Perform a dev/build - this will pull in all of the fixtures. _DO NOT_ do a second dev/build at any point.

## Check out the companion module
I have created a module which goes hand-in-hand with this talk - it provides a preview of data objects from within a gridfield - no need to click through to the edit form for each record.

https://github.com/GuySartorelli/silverstripe-gridfield-preview

## What this codebase includes examples of

- Basic examples from documentation
  - On a page (Books Page)
  - In a ModelAdmin (Product Admin)
  - Model as a Page (Region Admin)
- Example that can be previewed from a modeladmin OR on a page (Property Admin & Property Page)
- Preview a PDF which is generated on-the-fly (Pdf Admin -> PDF Templates)
- Preview objects in a ModelAdmin directly from the gridfield (Pdf Admin)
- Preview relations of an object from a js-injected dropdown (Pdf Admin -> Clients)
