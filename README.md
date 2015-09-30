# wp-feed-aggregator

The *WordPress Feed Aggregator Plugin* enables users to have posts from multiple (public) Facebook pages added to their existing WordPress blog as a native WordPress post. These generated posts will be automatically added to the same category as other posts that are retrieved from the same Facebook page. The plugin runs on a schedule; a check for new post’s is done every 5 minutes.

## Current post support
- Images
- Text based status updates
- Status updates with images
- Albums
- Shared Links
- Videos

## To get started:
1. Firstly, enable the plugin in the WordPress admin console.

2. A tab called ‘Feed Aggregator Options’ will appear to the left menu side pane of the WordPress console. Click on it.

3. Here you will find the fields in which you are to add the Facebook page IDs. The page IDs are a unique number which you can find out by clicking the “Don’t understand” link which will take you to a 3rd party website where you can paste in the URL of the Facebook page and get it’s ID.

4. Once done, click save. The plugin will generate an initial number of posts, no more than ten, that are retrieved from the respective Facebook pages. This will take time, please be patient.

  * **Other notes:**
    The plugin will add categories according to the Facebook page names, you may need to account for this if you wish to have this appear as a link somewhere in your page according to the theme you have. Settings for this will be in the theme settings of your selected theme; which is independent of our plugin.

## Editing and Deleting Posts:
Remembering that the post’s added are of native WordPress form, this means that the editing or deleting of posts is done in standard WordPress manner. This can either be done by selecting edit on a specific post (must be logged in with sufficient permissions) or under the ‘Posts’ section in the admin console.
