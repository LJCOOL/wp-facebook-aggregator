/* Put anything about your ideas, worries etc in here */

- The current plugin works by simply adding chunks of facebook posts into
posts or pages made by adding the tag "[custom-facebook-feed]".
  -> From what I see, we need to go step further by handling each post as one
    entity which will link to a separate page for comments (blogging bit).
    -> We can hack the existing code to do this, but how are we going to handle
      the page making. Dynamic? Page for each post? How?

- In my experimentation, I have found out how to add categories into Wordpress
which will in turn be the specific categories to be clicked on. Eg. RMIT Design
Hub, RMIT CSIT, etc. In these pages we can add "[custom-facebook-feed]", which
will essentially turn each page into something like separate Facebook pages.

// Comparing posts
There is a Metadata API that allows you add metadata to posts, comments etc. and
then later retrieve them. Depending on how Facebook data is received, if each
Facebook post has a unique ID we could add that to each post and reference it
when adding posts to a page. 
https://codex.wordpress.org/Metadata_API
