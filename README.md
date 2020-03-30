
# Wp Webhooks & WP Webhooks Pro Extension
This plugin demonstrates how you can extend both of our plugins WP Webhooks and WP Webbhooks Pro.

It shows you the following things:
- How to setup a custom action (Recieve Data)
- How to setup a custom trigger (Send Data)
- How to create a custom menu item for WP Webhooks/Pro

  

### How is the plugin structured
In the main file **wp-webhooks-pro-extension.php** incudes all the necessary definitions about the plugin extensions, as well as some code to safely register the functionality in combination with WP Webhooks/Pro.
Within this file, there are certain **require_once** calls, which include each of the functionalities (We separated them into custom files to make it easier to understand). 

The code is well documented with all the information you need. In case there are still questions or you found a bug, feel free to reach out to us at any time! 