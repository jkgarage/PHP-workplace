This is a fun project to integrate with Box.net, it pulls out photos from a predefiend folder in Box.net and play as a slideshow on a web page.

Slideshow code is taken from http://www.lateralcode.com/simple-slideshow/

## Notable features
1. Integrate with Box.net so you don't need a local storage to host any of the photos
2. Slideshow plays on any web browser, so it's cross platfom
   => works well with all mobile devices, which have precious storage space
3. Photo collection can be updated anytime, just update the content in your Box.net
folder, and the slideshow will be refreshed


## Dependencies
 Box_Rest_Client.v2 is required for interfacing with Box.net
 KLogger is optional, capture log message for trace/debug purpose.

- Box_Rest_Client.v2 : https://github.com/jkgarage/PHP_workplace/tree/master/box-php-sdk
- KLogger : https://github.com/katzgrau/KLogger

## Structure
- `BoxShowHome.php`   : the homepage which 
    1. sets up connection with Box.net, 
	2. renders 'simpleSlideshow_index.html'
    3. spawns a file download in back ground every interval (event triggered by `ajax_photo_download.js`)
- `config.php`        : keep all configurations
- `photodownload.php` : this page runs in background to download photo from Box.net to the buffer. The refresh in turn triggers `photodownload.php` running in background
- `simpleSlideshow_index.html` : page design, to play the slide show

For suggestion/feedback, please feel free to drop me a note.