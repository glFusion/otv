# Secret-Sharing Plugin for glFusion
This plugin allows for secure sharing of secret data between parties.
Secrets are encrypted using a public and private key, and the private key is only
provided to the submitter in the form of a custom URL.

Using that URL the secret value may be viewed once only and is then deleted.
The secret is also initially hidden from view and accessed by clicking a link
to ensure that it is safe to view the secret on-screen.

## Installation
Use the glFusion automated installation.

## Usage
### Saving a Secret
1. Visit /keyshare/index.php on your site.
1. Enter your secret text in the field and click "Submit".
1. Copy the displayed URL and send it via some method (more secure the better).
### Viewing a Secret
1. Visit the provided URL.
1. Click the "Display Secret" link when it is safe to do so.
1. Copy the secret data for later use.

## Notes
* Secrets are encrypted by a combination of public and private keys.
* The public key is saved in the database, the private key is used to
  create the custom URL.
* The URL is an encrypted value containing the secret record ID and private key.
  The plain-text private key is not actually provided to anyone.

## Configuration
* Hide from Plugin menu: Select whether the plugin should be included in the "Extras" menu.
* Expire secrets after X days: Enter a number of days after which un-viewed secrets will be deleted.
  Enter "0" to disable purging.
* Delete after Viewing: Normally set to "Yes" to delete secrets immediately after the view
  is created. This can be set to "No" temporarily to assist debugging.

## Access Control
Access is controlled via glFusion "features". Assign the appropriate feature to one or
more glFusion groups to limit access. By default "All Users" has access to create and
view secrets.
* `keyshare.view` - Users and groups with this feature can view secrets.
* `keyshare.create` - Users and groups with this feature can create secrets.

## License
This program is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 2 of the License, or (at your option) any later
version.
